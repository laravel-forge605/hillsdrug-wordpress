<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once EASY_HTTPS_SSL_PATH . "/classes/ehssl-certificate-storage.php";

class EHSSL_SSL_Certificate
{

    public function __construct()
    {

    }

    public function handle_ssl_installation($email, $live_mode = false)
    {
        global $httpsrdrctn_options;

        EHSSL_Logger::log("Starting SSL certificate generation process...");
        $well_known_dir_path = ABSPATH . ".well-known";
        $acme_challenge_dir_path = $well_known_dir_path . "/acme-challenge";
        $challenge_files = array();
        $old_mask = umask(0077);
        try {
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_chmod -- Local ACME storage requires exact Unix permissions and checked failures without remote filesystem credentials.
            if (!is_dir($well_known_dir_path) && (!wp_mkdir_p($well_known_dir_path) || !chmod($well_known_dir_path, 0755))) {
                throw new RuntimeException('Could not create the .well-known directory.');
            }
            $storage = EHSSL_Certificate_Storage::prepare_storage();
            $certificate_dir_path = $storage . '/certificate';
            if (is_link($certificate_dir_path) || is_link($storage . '/account')) {
                throw new RuntimeException('Refusing to use symbolic links for certificate storage.');
            }
            $certificate_directories = $this->create_directories($acme_challenge_dir_path, $certificate_dir_path);
            if (is_wp_error($certificate_directories)) {
                return $certificate_directories;
            }
            $visibility = new League\Flysystem\UnixVisibility\PortableVisibilityConverter(0600, 0600, 0700, 0700);
            $adapter = new League\Flysystem\Local\LocalFilesystemAdapter($storage . '/account', $visibility);
            $filesystem = new League\Flysystem\Filesystem($adapter, array('visibility' => 'private', 'directory_visibility' => 'private'));

            $mode = $live_mode ? Afosto\Acme\Client::MODE_LIVE : Afosto\Acme\Client::MODE_STAGING;
            EHSSL_Logger::log("Initiating Certificate Request for " . strtoupper($mode) . " Mode.");

            $config = [
                'username' => $email,
                'fs'       => $filesystem,
                'mode'     => $mode,
            ];

            require_once EASY_HTTPS_SSL_PATH . '/classes/ehssl-acme-client.php';

            $client = new EHSSL_ACME_Client($config);

            $domains = array();
            $domain = EHSSL_Utils::get_domain();
            $domain_variant = EHSSL_Utils::get_domain_variant($domain);

            $domains[] = $domain;

            // Check if domain variant is accessible.
            if (EHSSL_Utils::is_domain_accessible($domain_variant)) {
                $domains[] = $domain_variant;
            }

            EHSSL_Logger::log("Domains to get certificate for: " . implode(",", $domains));

            $order = $client->createOrder($domains);
            EHSSL_Logger::log("Creating order for Lets Encrypt");

            // Prove ownership (HTTP or DNS validation).
            $authorizations = $client->authorize($order);
            EHSSL_Logger::log("Prove ownership (HTTP or DNS validation)");

            // Saving authorizations & performing Self tests.
            EHSSL_Logger::log("Saving authorizations & performing Self tests");
            foreach ($authorizations as $authorization) {
                $file = $authorization->getFile();
                if (!$file || !preg_match('/\A[A-Za-z0-9_-]+\z/', $file->getFilename())) {
                    throw new RuntimeException('Invalid HTTP challenge token.');
                }
                $challenge_path = $acme_challenge_dir_path . '/' . $file->getFilename();
                $challenge_files[] = $challenge_path;
                EHSSL_Certificate_Storage::write($challenge_path, $file->getContents(), 0644);

                // Self-test.
                // After exposing the challenges (made accessible through HTTP or DNS) we should perform a self test just to be sure it works before asking Let's Encrypt to validate ownership.
                if (!$client->selfTest($authorization, Afosto\Acme\Client::VALIDATION_HTTP)) {
                    EHSSL_Logger::log("Could not verify ownership via HTTP");
                    throw new \Exception(__('Could not verify ownership via HTTP', 'https-redirection'));
                }
            }

            // Request validation.
            EHSSL_Logger::log("Request validation");
            foreach ($authorizations as $authorization) {
                if (!$client->validate($authorization->getHttpChallenge(), 15)) {
                    throw new RuntimeException('ACME challenge validation failed.');
                }
            }

            if ($client->isReady($order)) {
                // The validation was successful.
                EHSSL_Logger::log("The validation was successful.");
                $certificate = $client->getCertificate($order);

                EHSSL_Logger::log("Saving certificates in certificate directory.");
                EHSSL_Certificate_Storage::write($certificate_dir_path . '/certificate.crt', $certificate->getCertificate(false));
                EHSSL_Certificate_Storage::write($certificate_dir_path . '/cabundle.crt', $certificate->getIntermediate());
                EHSSL_Certificate_Storage::write($certificate_dir_path . '/private.pem', $certificate->getPrivateKey());
                EHSSL_Certificate_Storage::write($certificate_dir_path . '/certificate_expiry.txt', $certificate->getExpiryDate()->format('Y-m-d H:i:s'));

                // Updating certificate expiry date.
                EHSSL_Logger::log("Updating certificate expirty date in db.");

                $httpsrdrctn_options['ehssl_expiry_ssl_certificate'] = $certificate->getExpiryDate()->format('Y-m-d H:i:s');
                update_option('httpsrdrctn_options', $httpsrdrctn_options);

                EHSSL_Logger::log("Certificate saved successfully");
                return 'SSL Certificate generated successfully! Download certificate now. Certificate will expire on: ' . $certificate->getExpiryDate()->format('Y-m-d H:i:s');
            }
            EHSSL_Logger::log("SSL Certificate installation failed.");
            return new WP_Error("1003", __("SSL Certificate installation failed. Check the logs for details.", 'https-redirection'));
        } catch (\Throwable $ex) {
            EHSSL_Logger::log("Exception Raised:" . $ex->getMessage());
            return new WP_Error("1004", $ex->getMessage());
        } finally {
            umask($old_mask);
            foreach ($challenge_files as $challenge_path) {
                if (file_exists($challenge_path)) {
                    wp_delete_file($challenge_path);
                    clearstatcache(true, $challenge_path);
                }
                if (file_exists($challenge_path)) {
                    EHSSL_Logger::log('Could not remove an ACME challenge file.');
                }
            }
        }
    }


    public static function get_certificate_urls()
    {
        try {
            $certificate_dir_path = EHSSL_Certificate_Storage::directory() . '/certificate';
        } catch (Throwable $error) {
            return new WP_Error('file_not_found', $error->getMessage());
        }

        $certificate_file = $certificate_dir_path . '/certificate.crt';
        $ca_bundle = $certificate_dir_path . '/cabundle.crt';
        $private_key_file = $certificate_dir_path . '/private.pem';
        $certificate_expiry_file = $certificate_dir_path . '/certificate_expiry.txt';

        // Check if the certificate and private key files exist.
        if (!file_exists($certificate_file) || !file_exists($private_key_file) || !file_exists($ca_bundle) || !file_exists($certificate_expiry_file)) {
            return new WP_Error('file_not_found', __('Certificate or private key file not found. Please generate a certificate first!', 'https-redirection'));
        }

        return array(
            "certificate.crt" => array(
                'path' => realpath($certificate_file),
            ),
            "cabundle.crt" => array(
                'path' => realpath($ca_bundle),
            ),
            "private.pem" => array(
                'path' => realpath($private_key_file),
            )
        );
    }


    private function create_directories($acme_challenge_dir_path, $certificate_dir_path)
    {
        // Check and create the acme-challenge directory if it doesn't exist.
        if (!is_dir($acme_challenge_dir_path)) {
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_chmod -- Local ACME storage requires exact Unix permissions and checked failures without remote filesystem credentials.
            if (!wp_mkdir_p($acme_challenge_dir_path) || !chmod($acme_challenge_dir_path, 0755)) {
                EHSSL_Logger::log("Failed to create the acme-challenge directory");
                return new WP_Error("1001", __("Failed to create the acme-challenge directory", 'https-redirection'));
            }
        }

        if (!is_dir($certificate_dir_path)) {
            if (!wp_mkdir_p($certificate_dir_path)) {
                EHSSL_Logger::log("Failed to create the certificate directory");
                return new WP_Error("1002", __("Failed to create the certificate directory", 'https-redirection'));
            }
        }

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_chmod -- Local ACME storage requires exact Unix permissions and checked failures without remote filesystem credentials.
        if (!chmod($certificate_dir_path, 0700)) {
            return new WP_Error('storage_permissions', 'Could not restrict certificate directory permissions.');
        }
        return true;
    }
}
