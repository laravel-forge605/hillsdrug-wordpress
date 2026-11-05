<?php

if (!defined('ABSPATH')) {
    exit;
}

class EHSSL_Certificate_Storage {
    
    public static function directory($create = false) {
        $name = get_option('ehssl_certificate_storage_name', '');
        if (!$name && $create) {
            // Persist once; concurrent requests must use the winning option value.
            add_option('ehssl_certificate_storage_name', 'ehssl-' . bin2hex(random_bytes(32)), '', false);
            $name = get_option('ehssl_certificate_storage_name', '');
        }
        if (!is_string($name) || !preg_match('/\Aehssl-[a-f0-9]{64}\z/', $name)) {
            throw new RuntimeException('Certificate storage is not initialized. Please generate a certificate first.');
        }
        return ABSPATH . '.well-known/' . $name;
    }

    public static function prepare_storage() {
        $directory = self::directory(true);
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_chmod -- Local ACME storage requires exact Unix permissions and checked failures without remote filesystem credentials.
        if (is_link(ABSPATH . '.well-known') || is_link($directory) || !wp_mkdir_p($directory) || !chmod($directory, 0700)) {
            throw new RuntimeException('Could not create certificate storage.');
        }
        // These rules must remain readable by the web server.
        $rules = "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nOrder allow,deny\nDeny from all\n</IfModule>\n";
        $iis = '<?xml version="1.0"?><configuration><system.webServer><security><authorization><clear/><add accessType="Deny" users="*" /></authorization></security></system.webServer></configuration>';
        foreach (array('.htaccess' => $rules, 'web.config' => $iis, 'index.php' => '<?php // Silence is golden.') as $name => $contents) {
            self::write($directory . '/' . $name, $contents, 0644);
        }

        // Test with harmless data before writing keys. Nginx ignores the files above.
        $probe_name = 'access-test-' . bin2hex(random_bytes(16)) . '.txt';
        $probe = $directory . '/' . $probe_name;
        try {
            self::write($probe, bin2hex(random_bytes(32)), 0644);
            $url = site_url('.well-known/' . basename($directory) . '/' . $probe_name);
            // HTTP is always public; also check HTTPS when configured for this site.
            foreach (array_unique(array('http', wp_parse_url($url, PHP_URL_SCHEME))) as $scheme) {
                $probe_url = set_url_scheme($url, $scheme);
                $checked_scheme = $scheme;
                $response = wp_remote_get($probe_url, array('timeout' => 15, 'redirection' => 0));
                // Permit only an HTTP-to-HTTPS upgrade of this exact probe URL.
                if (!is_wp_error($response) && $scheme === 'http'
                    && in_array(wp_remote_retrieve_response_code($response), array(301, 302, 307, 308), true)
                    && wp_remote_retrieve_header($response, 'location') === set_url_scheme($url, 'https')) {
                    $checked_scheme = 'https';
                    $response = wp_remote_get(set_url_scheme($url, 'https'), array('timeout' => 15, 'redirection' => 0));
                }
                // Some hosts conceal forbidden files with 404. First prove that the
                // .well-known URL actually serves files from our filesystem location.
                if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 404
                    && self::verify_public_mapping($checked_scheme)) {
                    continue;
                }
                if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 403) {
                    if (is_wp_error($response)) {
                        $reason = str_replace(
                            array(basename($directory), $probe_name),
                            array('[storage]', '[probe]'),
                            $response->get_error_message()
                        );
                        $details = strtoupper($checked_scheme) . ' request failed: ' . $reason;
                        $next_step = 'Check that the server can reach its own site URL and that HTTPS uses a trusted certificate. A staging certificate can cause HTTPS verification to fail.';
                    } else {
                        $status = wp_remote_retrieve_response_code($response);
                        $details = strtoupper($checked_scheme) . ' returned HTTP ' . $status . ' instead of 403.';
                        $next_step = 'Check the web server deny rules for .well-known/ehssl-* folders.';
                        if ($status === 404) {
                            $next_step .= ' The public control file could not be retrieved with its expected contents either. Ask your host whether .well-known is aliased to another directory or blocked by a proxy, and verify the WordPress site URL.';
                        } elseif ($status >= 300 && $status < 400) {
                            $next_step .= ' The request was redirected; only an upgrade to HTTPS at the same host and path is accepted.';
                        } elseif ($status === 200) {
                            $next_step .= ' The test URL is accessible or returns a page instead of an access denial.';
                        }
                    }
                    // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception text is data; the admin error renderer escapes it with esc_html().
                    throw new RuntimeException('Certificate storage access could not be verified as blocked. ' . $details . ' ' . $next_step);
                }
            }
        } finally {
            if (file_exists($probe)) {
                wp_delete_file($probe);
                clearstatcache(true, $probe);
            }
            if (file_exists($probe)) {
                throw new RuntimeException('Could not remove certificate storage access test.');
            }
        }
        return $directory;
    }

    private static function verify_public_mapping($scheme) {
        $name = 'ehssl-control-' . bin2hex(random_bytes(16)) . '.txt';
        $path = ABSPATH . '.well-known/' . $name;
        $contents = bin2hex(random_bytes(32));
        try {
            self::write($path, $contents, 0644);
            $url = set_url_scheme(site_url('.well-known/' . $name), $scheme);
            $response = wp_remote_get($url, array('timeout' => 15, 'redirection' => 0));
            return !is_wp_error($response)
                && wp_remote_retrieve_response_code($response) === 200
                && hash_equals($contents, (string) wp_remote_retrieve_body($response));
        } finally {
            if (file_exists($path)) {
                wp_delete_file($path);
                clearstatcache(true, $path);
            }
            if (file_exists($path)) {
                throw new RuntimeException('Could not remove certificate storage public control file.');
            }
        }
    }

    public static function write($path, $contents, $permissions = 0600) {
        if (is_link($path)) {
            throw new RuntimeException('Refusing to write through a symbolic link.');
        }
        $old_mask = umask(0077);
        try {
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_chmod -- Local ACME storage requires exact Unix permissions and checked failures without remote filesystem credentials.
            if (file_put_contents($path, $contents, LOCK_EX) !== strlen($contents) || !chmod($path, $permissions)) {
                throw new RuntimeException('Could not securely save certificate storage file.');
            }
        } finally {
            umask($old_mask);
        }
    }

    public static function download_url($name) {
        return wp_nonce_url(add_query_arg(array('action' => 'ehssl_download_cert', 'file' => $name), admin_url('admin-post.php')), 'ehssl_download_cert');
    }

    public static function download() {
        if (!current_user_can('manage_options')) {
            wp_die('You are not allowed to download certificate files.', '', array('response' => 403));
        }
        check_admin_referer('ehssl_download_cert');
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Strict allowlist below rejects altered filenames; sanitizing could turn invalid input into an allowed filename.
        $name = isset($_GET['file']) && is_string($_GET['file']) ? wp_unslash($_GET['file']) : '';
        if (!in_array($name, array('certificate.crt', 'cabundle.crt', 'private.pem'), true)) {
            wp_die('Invalid certificate filename.', '', array('response' => 400));
        }
        try {
            $directory = realpath(self::directory());
            $path = $directory ? realpath($directory . '/certificate/' . $name) : false;
            if (!$path || dirname($path) !== $directory . '/certificate' || !is_file($path) || !is_readable($path)) {
                throw new RuntimeException('Certificate file not found.');
            }
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Stream the allowlisted local file after capability, nonce, and path checks.
            $stream = fopen($path, 'rb');
            if ($stream === false) {
                throw new RuntimeException('Certificate file could not be opened.');
            }
        } catch (Throwable $error) {
            wp_die(esc_html($error->getMessage()), '', array('response' => 404));
        }
        nocache_headers();
        header('Cache-Control: no-store, private, max-age=0');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('X-Content-Type-Options: nosniff');
        fpassthru($stream);
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Close the authenticated download stream opened above.
        fclose($stream);
        exit;
    }
}
