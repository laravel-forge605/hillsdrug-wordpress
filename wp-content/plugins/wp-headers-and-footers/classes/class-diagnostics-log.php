<?php

/**
 * Log file to know more about users website environment. Helps in debugging and providing support.
 *
 * @package WP Headers and Footers
 */

/**
 * Returns the plugin & system information.
 *
 * @param boolean $not_downloadable Check if the request is for downloading.
 * @package WP Headers and Footers
 *
 * @since 2.1.0
 * @version 2.1.1
 * @return string
 */
if ( ! class_exists( 'WPHeadersAndFooters_Diagnostics_Log' ) ) {

	/**
	 * The WPHeaderAndFooter Settings class
	 */
	class WPHeadersAndFooters_Diagnostics_Log {

		/**
		 * System information log.
		 *
		 * @param boolean $not_downloadable Check if the request is for download or not.
		 * @return string $html The log information string.
		 */
		public function wp_headers_and_footers_get_sysinfo( $not_downloadable = true ) {

			global $wpdb;

			$break = false === $not_downloadable ? "\n" : '<br>';
			$html  = '';

			if ( $not_downloadable ) {
				$html .= '<div class="wpheaderandfooter-diagnostic-wrapper">';
			}

			$html .= '### Begin System Info ###' . $break . $break;
			$html .= $this->wp_headers_and_footers_get_wordpress_config_info( $break, $wpdb );
			$html .= $this->wp_headers_and_footers_get_plugin_config_info( $break );
			$html .= $this->wp_headers_and_footers_get_server_config_info( $break, $wpdb );
			$html .= $this->wp_headers_and_footers_get_php_config_info( $break );
			$html .= $this->wp_headers_and_footers_get_theme_info( $break );
			$html .= $this->wp_headers_and_footers_get_plugins_list_info( $break );
			$html .= $break . '-- Scripts Being Used --' . $break;
			$html .= $this->wp_headers_and_footers_get_scripts( $not_downloadable, $break );
			$html .= $break . '### End System Info ###';
			$html .= '</div>';

			if ( $not_downloadable ) {
				$html .= $this->wp_headers_and_footers_get_download_ui_html();
			}

			return $html;
		}

		/**
		 * WordPress configuration section.
		 *
		 * @param string $break Line break.
		 * @param object $wpdb  WordPress database object.
		 * @return string
		 */
		private function wp_headers_and_footers_get_wordpress_config_info( $break, $wpdb ) {
			$wp_debug = 'Not set';
			if ( defined( 'WP_DEBUG' ) ) {
				$wp_debug = WP_DEBUG ? 'Enabled' : 'Disabled';
			}

			$html  = '-- WordPress Configuration --' . $break . $break;
			$html .= 'Site URL:                 ' . site_url() . $break;
			$html .= 'Home URL:                 ' . home_url() . $break;
			$html .= 'Multisite:                ' . ( is_multisite() ? 'Yes' : 'No' ) . $break;
			$html .= 'Version:                  ' . get_bloginfo( 'version' ) . $break;
			$html .= 'Language:                 ' . get_locale() . $break;
			$html .= 'Table Prefix:             ' . 'Length: ' . strlen( $wpdb->prefix ) . $break;
			$html .= 'WP_DEBUG:                 ' . $wp_debug . $break;
			$html .= 'Memory Limit:             ' . WP_MEMORY_LIMIT . $break;

			return $html;
		}

		/**
		 * Plugin configuration section.
		 *
		 * @param string $break Line break.
		 * @return string
		 */
		private function wp_headers_and_footers_get_plugin_config_info( $break ) {
			$html  = $break . '-- WP Headers and Footers Configuration --' . $break . $break;
			$html .= 'Plugin Version:           ' . WPHEADERANDFOOTER_VERSION . $break;

			return $html;
		}

		/**
		 * Server configuration section.
		 *
		 * @param string $break Line break.
		 * @param object $wpdb  WordPress database object.
		 * @return string
		 */
		private function wp_headers_and_footers_get_server_config_info( $break, $wpdb ) {
			$os_name = function_exists( 'php_uname' ) ? php_uname( 's' ) : PHP_OS;
			$software = 'Unknown';
			if ( isset( $_SERVER['SERVER_SOFTWARE'] ) && ! empty( $_SERVER['SERVER_SOFTWARE'] ) ) {
				$software = sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) );
			}

			$html  = $break . '-- Server Configuration --' . $break . $break;
			$html .= 'Operating System:         ' . $os_name . $break;
			$html .= 'PHP Version:              ' . PHP_VERSION . $break;
			$html .= 'MySQL Version:            ' . $wpdb->db_version() . $break;
			$html .= 'Server Software:          ' . $software;

			return $html;
		}

		/**
		 * PHP configuration section.
		 *
		 * @param string $break Line break.
		 * @return string
		 */
		private function wp_headers_and_footers_get_php_config_info( $break ) {
			$display_errors = ini_get( 'display_errors' );
			$display_label  = ( $display_errors && '0' !== (string) $display_errors ) ? $display_errors : 'N/A';

			$html  = $break . $break . '-- PHP Configuration --' . $break . $break;
			$html .= 'Memory Limit:             ' . ini_get( 'memory_limit' ) . $break;
			$html .= 'Post Max Size:            ' . ini_get( 'post_max_size' ) . $break;
			$html .= 'Upload Max Filesize:      ' . ini_get( 'upload_max_filesize' ) . $break;
			$html .= 'Time Limit:               ' . ini_get( 'max_execution_time' ) . $break;
			$html .= 'Max Input Vars:           ' . ini_get( 'max_input_vars' ) . $break;
			$html .= 'Display Errors:           ' . $display_label . $break;

			return $html;
		}

		/**
		 * Active theme section.
		 *
		 * @param string $break Line break.
		 * @return string
		 */
		private function wp_headers_and_footers_get_theme_info( $break ) {
			$my_theme = wp_get_theme();

			$html  = $break . '-- WordPress Active Theme --' . $break . $break;
			$html .= 'Name:                     ' . $my_theme->get( 'Name' ) . $break;
			$html .= 'URI:                      ' . $my_theme->get( 'ThemeURI' ) . $break;
			$html .= 'Author:                   ' . $my_theme->get( 'Author' ) . $break;
			$html .= 'Version:                  ' . $my_theme->get( 'Version' ) . $break;

			return $html;
		}

		/**
		 * Active / inactive / network plugins section.
		 *
		 * @param string $break Line break.
		 * @return string
		 */
		private function wp_headers_and_footers_get_plugins_list_info( $break ) {
			$plugins        = get_plugins();
			$active_plugins = get_option( 'active_plugins', array() );
			$html           = $break . '-- WordPress Active Plugins --' . $break . $break;

			foreach ( $plugins as $plugin_path => $plugin ) {
				if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
					continue;
				}
				$html .= $plugin['Name'] . ': v(' . $plugin['Version'] . ")$break";
			}

			$html .= $break . '-- WordPress Inactive Plugins --' . $break . $break;
			foreach ( $plugins as $plugin_path => $plugin ) {
				if ( in_array( $plugin_path, $active_plugins, true ) ) {
					continue;
				}
				$html .= $plugin['Name'] . ': v(' . $plugin['Version'] . ")$break";
			}

			if ( ! is_multisite() ) {
				return $html;
			}

			$html          .= $break . '-- Network Active Plugins --' . $break . $break;
			$plugins        = wp_get_active_network_plugins();
			$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

			foreach ( $plugins as $plugin_path ) {
				$plugin_base = plugin_basename( $plugin_path );
				if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
					continue;
				}
				$plugin = get_plugin_data( $plugin_path );
				$html  .= $plugin['Name'] . ': v(' . $plugin['Version'] . ")$break";
			}

			return $html;
		}

		/**
		 * Download button markup for the diagnostics UI.
		 *
		 * @return string
		 */
		private function wp_headers_and_footers_get_download_ui_html() {
			$html  = '<div class="wpheaderandfooter-log-download-wrap">';
			$html .= '<input type="button" class="button wpheaderandfooter-log-file" value="' . esc_attr__( 'Download Log File', 'wp-headers-and-footers' ) . '"/>';
			$html .= '<div class="wpheaderandfooter-log-message">';
			$html .= '<span class="log-file-spinner"><img src="' . esc_url( admin_url( 'images/wpspin_light.gif' ) ) . '" alt="" /></span>';
			$html .= '<span class="log-file-text">' . esc_html__( 'WP Headers and Footers Log File Downloaded Successfully!', 'wp-headers-and-footers' ) . '</span>';
			$html .= '</div>';
			$html .= '</div>';

			return $html;
		}

		/**
		 * Get the scripts in the diagnostic log
		 *
		 * @param bool   $printable  Whether output is for the admin UI (escape HTML).
		 * @param string $line_break The break tag / newline definition.
		 *
		 * @since 2.1.1
		 * @return string
		 */
		public function wp_headers_and_footers_get_scripts( $printable, $line_break ) {

			$setting = get_option( 'wpheaderandfooter_basics' );
			$html    = '';
			if ( is_array( $setting ) && ! empty( $setting ) ) {
				foreach ( $setting as $place_used => $value ) {
					$key   = '- ' . ucfirst( str_replace( array( '_', 'wp', 'textarea' ), '', $place_used ) ) . ' Scripts: -';
					$value = $printable ? esc_html( (string) $value ) : (string) $value;
					$html .= $line_break . $key . $line_break . $line_break;
					$html .= '<code>' . $value . '</code>' . $line_break;
				}
			}
			return $html;
		}
	}
}
