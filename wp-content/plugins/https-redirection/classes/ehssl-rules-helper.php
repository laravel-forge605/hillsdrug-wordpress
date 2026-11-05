<?php

class EHSSL_Htaccess
{

    public function __construct()
    {

    }

    public function write_to_htaccess()
    {
        $rules = $this->getrules();
        if ( -1 === $rules || -1 === $this->delete_from_htaccess() ) {
            return -1;
        }
        $filesystem = EHSSL_Utils::get_filesystem();
        $htaccess = ABSPATH . '.htaccess';
        $contents = $filesystem->get_contents( $htaccess );
        if ( false === $contents ) {
            return -1;
        }
        return EHSSL_Utils::write_file( $htaccess, $rules . $contents ) ? 1 : -1;
    }

    public function getrules()
    {
        @ini_set('auto_detect_line_endings', true);

        //figure out what server they're using
        if (strstr(strtolower( sanitize_text_field( $_SERVER['SERVER_SOFTWARE'] ) ), 'apache')) {
            $server_type = 'apache';
        } else if (strstr(strtolower( sanitize_text_field( $_SERVER['SERVER_SOFTWARE'] ) ), 'nginx')) {
            $server_type = 'nginx';
        } else if (strstr(strtolower( sanitize_text_field( $_SERVER['SERVER_SOFTWARE'] ) ), 'litespeed')) {
            $server_type = 'litespeed';
        } else { //unsupported server
            return -1;
        }

        //check if some plugins are active to avoid incompatability issues
        // WP Fastest Cache
        if (isset($GLOBALS["wp_fastest_cache"])) {
            $wpfc = true;
            $wpfc_rules = '# WP Fastest Cache compatability' . PHP_EOL;
            $wpfc_rules .= 'RewriteCond %{REQUEST_URI} !wp-content\/cache\/(all|wpfc-mobile-cache)' . PHP_EOL;
        } else {
            $wpfc = false;
        }

        $rules = '';
        $httpsrdrctn_options = get_option('httpsrdrctn_options');
        $https_full_domain = $httpsrdrctn_options['https_domain'];
        $auto_redirect_enabled = $httpsrdrctn_options['https'];

        if ($auto_redirect_enabled != '1') {
            //HTTPS Redirection is NOT enabled
            return $rules;
        }

        if ($https_full_domain == '1') { //HTTPS Redirection on Full Site
            $rules .= '<IfModule mod_rewrite.c>' . PHP_EOL;
            $rules .= 'RewriteEngine On' . PHP_EOL;

            $rules .= 'RewriteCond %{HTTP:X-Forwarded-Proto} !https' . PHP_EOL; //Handle traffic connecting to your proxy or load balancer
            $rules .= 'RewriteCond %{HTTPS} off' . PHP_EOL; //Alternative is to use RewriteCond %{SERVER_PORT} !^443$
            if ($wpfc) {
                $rules .= $wpfc_rules;
            }
            $rules .= 'RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]' . PHP_EOL;

            $rules .= '</IfModule>' . PHP_EOL;

	        // Add HTTP Strict Transport Security rules if enabled.
			$rules .= $this->get_hsts_rules();

        } else { //HTTPS Redirection on a Few Pages ONLY
            if (empty($httpsrdrctn_options['https_pages_array'])) {
                //No specific page has been configured
                return '';
            }

            $rules .= '<IfModule mod_rewrite.c>' . PHP_EOL;
            $rules .= 'RewriteEngine On' . PHP_EOL;

            $rules .= 'RewriteCond %{HTTP:X-Forwarded-Proto} !https' . PHP_EOL; //Handle traffic connecting to your proxy or load balancer
            $rules .= 'RewriteCond %{HTTPS} off' . PHP_EOL; //Alternative is to use RewriteCond %{SERVER_PORT} !^443$
            if ($wpfc) {
                $rules .= $wpfc_rules;
            }
            $count = 0;
            $total_pages = count($httpsrdrctn_options['https_pages_array']);
            foreach ($httpsrdrctn_options['https_pages_array'] as $https_page) {
                //Add a RewriteCond line for each of the individual pages

                $count++;

                if (empty($https_page)) {
                    continue;
                }

                $rules .= 'RewriteCond %{REQUEST_URI} ' . trim($https_page);
                if ($total_pages != $count) { //This is not the last page so join them with an OR condition
                    $rules .= ' [OR]';
                }
                $rules .= PHP_EOL;
            }

            $rules .= 'RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]' . PHP_EOL;

            $rules .= '</IfModule>' . PHP_EOL;
        }

        //Add outer markers if we have rules
        if ($rules != '') {
            $rules = "# BEGIN HTTPS Redirection Plugin" . PHP_EOL . $rules . "# END HTTPS Redirection Plugin" . PHP_EOL;
        }

        return $rules;
    }

	public function get_hsts_rules(){
		$httpsrdrctn_options = get_option('httpsrdrctn_options', array());
		$enable_hsts = isset($httpsrdrctn_options['hsts_enabled']) && !empty($httpsrdrctn_options['hsts_enabled']) ? true : false;

		$hsts_rule = '';
		if ($enable_hsts) {
			$hsts_max_age = isset($httpsrdrctn_options['hsts_max_age']) && !empty($httpsrdrctn_options['hsts_max_age']) ? absint(sanitize_text_field($httpsrdrctn_options['hsts_max_age'])) : 31536000;
			$hsts_include_subdomains = isset($httpsrdrctn_options['hsts_include_sub_domains']) && !empty($httpsrdrctn_options['hsts_include_sub_domains']) ? true : false;
			$hsts_preload = isset($httpsrdrctn_options['hsts_preload']) && !empty($httpsrdrctn_options['hsts_preload']) ? true : false;

			// Example: Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
			$header = 'Header always set Strict-Transport-Security "%s" env=HTTPS';

			$hsts_flags = array();
			$hsts_flags[] = 'max-age='.$hsts_max_age;

			if (!empty($hsts_include_subdomains)){
				$hsts_flags[] = 'includeSubDomains';
			}

			if (!empty($hsts_preload)){
				$hsts_flags[] = 'preload';
			}

			$hsts_rule = '<IfModule mod_headers.c>' . PHP_EOL;
			$hsts_rule .= sprintf($header, implode('; ', $hsts_flags)) . PHP_EOL;
			$hsts_rule .= '</IfModule>' . PHP_EOL;
		}

		return $hsts_rule;
	}

    public function delete_from_htaccess($section = 'HTTPS Redirection Plugin')
    {
        $htaccess = ABSPATH . '.htaccess';
        $filesystem = EHSSL_Utils::get_filesystem();
        if ( ! $filesystem->exists( $htaccess ) ) {
            return EHSSL_Utils::write_file( $htaccess, '' ) ? 1 : -1;
        }
        $contents = $filesystem->get_contents( $htaccess );
        if ( false === $contents ) {
            return -1;
        }

        // Preserve other plugins' rules and their original whitespace.
        $lines = preg_split( '/(?<=\n)/', $contents );
        $inside_section = false;
        $remaining = '';
        foreach ( $lines as $line ) {
            if ( false !== strpos( $line, '# BEGIN ' . $section ) ) {
                $inside_section = true;
            }
            if ( ! $inside_section ) {
                $remaining .= $line;
            }
            if ( false !== strpos( $line, '# END ' . $section ) ) {
                $inside_section = false;
            }
        }
        if ( $inside_section ) {
            return -1; // Do not discard unrelated rules if the closing marker is missing.
        }
        return EHSSL_Utils::write_file( $htaccess, $remaining ) ? 1 : -1;
    }

}
