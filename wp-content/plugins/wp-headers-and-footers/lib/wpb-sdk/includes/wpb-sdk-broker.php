<?php
/**
 * WPBrigade SDK selection helpers (DB registry, reorder, redirect, fallback).
 *
 * Loaded early from start.php. Selection itself runs at file scope in start.php
 * so a newer bundle can still act after an older runtime already loaded.
 *
 * @package wpbrigade_sdk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wpb_sdk_normalize_path' ) ) {
	/**
	 * Normalize a filesystem path for comparisons.
	 * @param string $path Path.
	 * @return string
	 */
	function wpb_sdk_normalize_path( $path ) {
		if ( function_exists( 'wp_normalize_path' ) ) {
			return wp_normalize_path( $path );
		}
		return str_replace( '\\', '/', (string) $path );
	}
}

if ( ! function_exists( 'wpb_sdk_sanitize_redirect_location' ) ) {
	/**
	 * Ensure $location is a safe, same-host URL for use in the header() fallback.
	 *
	 * When wp_safe_redirect() is available it handles host validation itself.
	 * The raw header() fallback must NEVER receive an attacker-influenced value.
	 * Accepted inputs:
	 *   - An absolute URL whose host matches home_url() (safe pass-through).
	 *   - A path-only string starting with '/' (reconstructed against home_url).
	 * Anything else falls back to the current admin_url() so the redirect still
	 * reloads the page rather than silently no-op'ing.
	 *
	 * @param string $location Raw location (may be REQUEST_URI or a full URL).
	 * @return string Validated absolute URL safe for header().
	 */
	function wpb_sdk_sanitize_redirect_location( $location ) {
		$location = (string) $location;

		// Reject empty strings immediately.
		if ( '' === $location ) {
			return function_exists( 'admin_url' ) ? admin_url() : '';
		}

		$parsed = wp_parse_url( $location );

		// Path-only input (REQUEST_URI style: starts with '/' but no scheme/host).
		// Reconstruct as an absolute URL against the site's own host so a crafted
		// '//evil.com/...' is never reflected verbatim into the Location header.
		if ( ! isset( $parsed['scheme'] ) && ! isset( $parsed['host'] ) ) {
			// Must start with a single '/' (not '//').
			if ( isset( $parsed['path'] ) && '/' === substr( $parsed['path'], 0, 1 )
				&& ( strlen( $parsed['path'] ) < 2 || '/' !== $parsed['path'][1] )
			) {
				$base      = function_exists( 'home_url' ) ? home_url() : site_url();
				$base_path = wp_parse_url( $base, PHP_URL_PATH );
				$path      = $parsed['path'];
				$base_path = is_string( $base_path ) ? untrailingslashit( $base_path ) : '';
				// REQUEST_URI already includes the WordPress home subdirectory.
				// Strip it before joining to home_url() to avoid /blog/blog/ paths.
				if ( '' !== $base_path && ( $path === $base_path || 0 === strpos( $path, $base_path . '/' ) ) ) {
					$path = substr( $path, strlen( $base_path ) );
				}
				$safe = rtrim( $base, '/' ) . '/' . ltrim( $path, '/' );
				if ( ! empty( $parsed['query'] ) ) {
					$safe .= '?' . $parsed['query'];
				}
				return $safe;
			}
			// Anything else (protocol-relative, scheme-only, etc.) — fall through to admin_url.
			return function_exists( 'admin_url' ) ? admin_url() : '';
		}

		// Absolute URL: host must match our own site.
		$home_host = wp_parse_url( function_exists( 'home_url' ) ? home_url() : site_url(), PHP_URL_HOST );
		if ( isset( $parsed['host'] ) && (string) $parsed['host'] === (string) $home_host ) {
			return $location;
		}

		// Host mismatch — redirect home instead of leaking to a third-party domain.
		return function_exists( 'admin_url' ) ? admin_url() : '';
	}
}

if ( ! function_exists( 'wpb_sdk_safe_redirect' ) ) {
	/**
	 * Redirect and exit (same-host redirect helper).
	 *
	 * wp_safe_redirect() is a pluggable function that does not exist until after
	 * all active plugins have finished loading (pluggable.php is included at the
	 * end of wp-settings.php, after plugins_loaded fires). When the SDK broker
	 * runs at file scope during plugin loading wp_safe_redirect() may therefore
	 * be unavailable, and we must fall through to the raw header() call — but
	 * only after sanitizing the location through wpb_sdk_sanitize_redirect_location()
	 * so that attacker-influenced REQUEST_URI values cannot produce an open redirect.
	 *
	 * @param string $location URL or path to redirect to.
	 * @param bool   $exit     Whether to exit after sending the header.
	 * @return bool
	 */
	function wpb_sdk_safe_redirect( $location, $exit = true ) {
		if ( empty( $location ) ) {
			return false;
		}

		if ( function_exists( 'wp_safe_redirect' ) ) {
			// wp_safe_redirect() validates the host itself — pass through directly.
			wp_safe_redirect( $location );
		} else {
			// pluggable.php is not loaded yet. Sanitize before touching header().
			$safe_location = function_exists( 'wpb_sdk_sanitize_redirect_location' )
				? wpb_sdk_sanitize_redirect_location( $location )
				: ( function_exists( 'admin_url' ) ? admin_url() : '' );

			if ( empty( $safe_location ) ) {
				return false;
			}

			header( 'Location: ' . $safe_location );
		}

		if ( $exit ) {
			exit;
		}

		return true;
	}
}

if ( ! function_exists( 'wpb_sdk_can_redirect_for_sdk_switch' ) ) {
	/**
	 * Whether an SDK-switch redirect is safe for this request.
	 *
	 * Skip AJAX, REST, CLI, cron, and POST so we never drop form bodies or
	 * break JSON responses. Reorder still runs; only the redirect is gated.
	 *
	 * @return bool
	 */
	function wpb_sdk_can_redirect_for_sdk_switch() {
		if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) {
			return false;
		}
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return false;
		}
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return false;
		}
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			return false;
		}
		if ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() ) {
			return false;
		}
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return false;
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Method check only.
		if ( isset( $_SERVER['REQUEST_METHOD'] )
			&& 'POST' === strtoupper( (string) $_SERVER['REQUEST_METHOD'] )
		) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'wpb_sdk_maybe_redirect_for_sdk_switch' ) ) {
	/**
	 * Redirect after an SDK order change, subject to request-type safety checks.
	 *
	 * 
	 *
	 * @param string $location URL to reload.
	 * @return bool True if a redirect was issued (and the process exits).
	 */
	function wpb_sdk_maybe_redirect_for_sdk_switch( $location ) {
		if ( ! wpb_sdk_can_redirect_for_sdk_switch() ) {
			return false;
		}

		if ( empty( $location ) ) {
			return false;
		}


		return wpb_sdk_safe_redirect( $location );
	}
}

if ( ! function_exists( 'wpb_sdk_find_caller_plugin_file' ) ) {
	/**
	 * Resolve the host plugin main file for this SDK start.php.
	 *
	 * @param string $sdk_start_file Absolute path to start.php.
	 * @return string Absolute path to plugin main file, or SDK start file.
	 */
	function wpb_sdk_find_caller_plugin_file( $sdk_start_file ) {
		$sdk_dir = wpb_sdk_normalize_path( dirname( $sdk_start_file ) );

		if ( preg_match( '#/lib/wpb-sdk$#', $sdk_dir ) ) {
			$root = dirname( dirname( $sdk_dir ) );
			$slug = basename( $root );
			$try  = array(
				$root . '/' . $slug . '.php',
				$root . '/wp-' . $slug . '.php',
			);

			foreach ( $try as $candidate ) {
				if ( is_readable( $candidate ) ) {
					return $candidate;
				}
			}

			$files = glob( $root . '/*.php' );
			if ( is_array( $files ) ) {
				foreach ( $files as $file ) {
					$contents = @file_get_contents( $file, false, null, 0, 8192 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged,WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
					if ( is_string( $contents ) && false !== strpos( $contents, 'Plugin Name:' ) ) {
						return $file;
					}
				}
			}

			return $root . '/' . $slug . '.php';
		}

		// Standalone plugins/wpb-sdk package.
		$standalone = dirname( $sdk_dir ) === wpb_sdk_normalize_path( WP_PLUGIN_DIR )
			? $sdk_dir . '/wpb-sdk.php'
			: '';
		if ( '' !== $standalone && is_readable( $standalone ) ) {
			return $standalone;
		}

		return $sdk_start_file;
	}
}

if ( ! function_exists( 'wpb_sdk_sdk_relative_path' ) ) {
	/**
	 * Plugin-dir-relative path to this SDK folder (option key).
	 *
	 * @param string $sdk_dir Absolute SDK directory.
	 * @return string
	 */
	function wpb_sdk_sdk_relative_path( $sdk_dir ) {
		$sdk_dir    = wpb_sdk_normalize_path( $sdk_dir );
		$plugins    = wpb_sdk_normalize_path( WP_PLUGIN_DIR );
		$relative   = $sdk_dir;
		if ( 0 === strpos( $sdk_dir, $plugins ) ) {
			$relative = ltrim( substr( $sdk_dir, strlen( $plugins ) ), '/' );
		}
		return $relative;
	}
}

if ( ! function_exists( 'wpb_sdk_get_active_plugins_option' ) ) {
	/**
	 * Load or initialize the shared WPB SDK registry.
	 *
	 * @return object
	 */
	function wpb_sdk_get_active_plugins_option() {
		global $wpb_sdk_active_plugins;

		if ( isset( $wpb_sdk_active_plugins ) && is_object( $wpb_sdk_active_plugins ) ) {
			if ( ! isset( $wpb_sdk_active_plugins->plugins ) || ! is_array( $wpb_sdk_active_plugins->plugins ) ) {
				$wpb_sdk_active_plugins->plugins = array();
			}
			return $wpb_sdk_active_plugins;
		}

		$stored = get_option( 'wpb_sdk_active_plugins' );
		if ( ! is_object( $stored ) ) {
			$stored = new stdClass();
		}
		if ( ! isset( $stored->plugins ) || ! is_array( $stored->plugins ) ) {
			$stored->plugins = array();
		}

		$wpb_sdk_active_plugins = $stored;
		return $wpb_sdk_active_plugins;
	}
}

if ( ! function_exists( 'wpb_sdk_save_active_plugins_option' ) ) {
	/**
	 * Persist the DB SDK registry.
	 *
	 * @return void
	 */
	function wpb_sdk_save_active_plugins_option() {
		global $wpb_sdk_active_plugins;
		if ( ! isset( $wpb_sdk_active_plugins ) || ! is_object( $wpb_sdk_active_plugins ) ) {
			return;
		}
		update_option( 'wpb_sdk_active_plugins', $wpb_sdk_active_plugins, false );
	}
}

if ( ! function_exists( 'wpb_sdk_prune_active_plugins_option' ) ) {
	/**
	 * Handle ABSPATH changes and remove missing SDK paths.
	 *
	 * @return void
	 */
	function wpb_sdk_prune_active_plugins_option() {
		$registry = wpb_sdk_get_active_plugins_option();

		if ( empty( $registry->abspath ) ) {
			$registry->abspath = ABSPATH;
			wpb_sdk_save_active_plugins_option();
			return;
		}

		if ( ABSPATH !== $registry->abspath ) {
			$registry->abspath = ABSPATH;
			$registry->plugins = array();
			unset( $registry->newest );
			wpb_sdk_save_active_plugins_option();
			return;
		}

		$has_changes = false;
		foreach ( $registry->plugins as $sdk_path => $data ) {
			$abs = wpb_sdk_normalize_path( WP_PLUGIN_DIR . '/' . $sdk_path );
			if ( ! file_exists( $abs ) ) {
				unset( $registry->plugins[ $sdk_path ] );
				if ( ! empty( $registry->newest ) && isset( $registry->newest->sdk_path )
					&& $sdk_path === $registry->newest->sdk_path
				) {
					unset( $registry->newest );
				}
				$has_changes = true;
			}
		}

		if ( $has_changes ) {
			if ( empty( $registry->plugins ) ) {
				unset( $registry->newest );
			}
			wpb_sdk_save_active_plugins_option();
		}
	}
}

if ( ! function_exists( 'wpb_sdk_update_newest_version' ) ) {
	/**
	 * Remember the newest SDK path/version in the DB option.
	 *
	 * @param string      $sdk_relative_path Relative SDK path.
	 * @param string|bool $plugin_file       Plugin basename or false.
	 * @return void
	 */
	function wpb_sdk_update_newest_version( $sdk_relative_path, $plugin_file = false ) {
		$registry = wpb_sdk_get_active_plugins_option();

		if ( empty( $registry->plugins[ $sdk_relative_path ] ) ) {
			return;
		}

		$newest_sdk = $registry->plugins[ $sdk_relative_path ];

		if ( ! is_string( $plugin_file ) || '' === $plugin_file ) {
			$plugin_file = isset( $newest_sdk->plugin_path ) ? (string) $newest_sdk->plugin_path : '';
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$in_activation = ( '' === $plugin_file ) ? true : ( ! is_plugin_active( $plugin_file ) );

		$registry->newest = (object) array(
			'plugin_path'   => $plugin_file,
			'sdk_path'      => $sdk_relative_path,
			'version'       => $newest_sdk->version,
			'in_activation' => $in_activation,
			'timestamp'     => time(),
		);

		wpb_sdk_save_active_plugins_option();
	}
}

if ( ! function_exists( 'wpb_sdk_deferred_newest_sdk_plugin_first' ) ) {
	/**
	 * Reorder and reload after WordPress has loaded pluggable user functions.
	 *
	 * @return void
	 */
	function wpb_sdk_deferred_newest_sdk_plugin_first() {
		if ( wpb_sdk_newest_sdk_plugin_first() && wpb_sdk_can_redirect_for_sdk_switch() ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reload current URL after reorder.
			$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : admin_url();
			wpb_sdk_maybe_redirect_for_sdk_switch( $request_uri );
		}
	}
}
if ( ! function_exists( 'wpb_sdk_newest_sdk_plugin_first' ) ) {
	/**
	 * Reorder active_plugins so the newest SDK host loads first.
	 *
	 * Writing active_plugins is a privileged operation. Outside of an admin
	 * context with the activate_plugins capability the reorder is silently
	 * skipped; the SDK will still load but the per-request version preference
	 * is retried during admin_init when the current user's capability is available.
	 *
	 * @return bool True if order changed.
	 */
	function wpb_sdk_newest_sdk_plugin_first() {
		// Gate: only reorder when running in an admin context with the
		// activate_plugins capability, or during WP-CLI / cron where no
		// interactive user is involved but the request is trusted.
		$is_cli  = defined( 'WP_CLI' ) && WP_CLI;
		$is_cron = ( defined( 'DOING_CRON' ) && DOING_CRON )
			|| ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() );

		if ( ! $is_cli && ! $is_cron ) {
			// Require an admin page request from a user who can manage plugins.
			if ( ! is_admin() ) {
				return false;
			}
			// current_user_can() is pluggable and unavailable while plugins load.
			if ( ! function_exists( 'current_user_can' ) || ! function_exists( 'wp_get_current_user' ) ) {
				// The broker runs while plugins load, before pluggable user functions.
				// Retry on admin_init, when the current user's capability is available.
				if ( function_exists( 'add_action' ) ) {
					add_action( 'admin_init', 'wpb_sdk_deferred_newest_sdk_plugin_first', 0 );
				}
				return false;
			}
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return false;
			}
		}

		$registry = wpb_sdk_get_active_plugins_option();
		if ( empty( $registry->newest ) || empty( $registry->newest->plugin_path ) ) {
			return false;
		}

		$newest_sdk_plugin_path = (string) $registry->newest->plugin_path;
		$active_plugins         = get_option( 'active_plugins', array() );
		if ( ! is_array( $active_plugins ) ) {
			$active_plugins = array();
		}

		$updated_active_plugins = array( $newest_sdk_plugin_path );
		$plugin_found           = false;
		$is_first_path          = true;

		foreach ( $active_plugins as $plugin_path ) {
			if ( $plugin_path === $newest_sdk_plugin_path ) {
				if ( $is_first_path ) {
					return false;
				}
				$plugin_found = true;
				continue;
			}
			$updated_active_plugins[] = $plugin_path;
			$is_first_path            = false;
		}

		if ( $plugin_found ) {
			update_option( 'active_plugins', $updated_active_plugins );
			return true;
		}

		if ( is_multisite() ) {
			$network_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
			if ( isset( $network_active_plugins[ $newest_sdk_plugin_path ] ) ) {
				reset( $network_active_plugins );
				if ( $newest_sdk_plugin_path === key( $network_active_plugins ) ) {
					return false;
				}
				$time = $network_active_plugins[ $newest_sdk_plugin_path ];
				unset( $network_active_plugins[ $newest_sdk_plugin_path ] );
				$network_active_plugins = array( $newest_sdk_plugin_path => $time ) + $network_active_plugins;
				update_site_option( 'active_sitewide_plugins', $network_active_plugins );
				return true;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'wpb_sdk_fallback_to_newest_active_sdk' ) ) {
	/**
	 * Pick the newest SDK still present and preferably active.
	 *
	 * @return void
	 */
	function wpb_sdk_fallback_to_newest_active_sdk() {
		$registry = wpb_sdk_get_active_plugins_option();

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$newest_sdk_data = null;
		$newest_sdk_path = null;

		foreach ( $registry->plugins as $sdk_relative_path => $data ) {
			$abs = wpb_sdk_normalize_path( WP_PLUGIN_DIR . '/' . $sdk_relative_path . '/start.php' );
			if ( ! file_exists( $abs ) ) {
				unset( $registry->plugins[ $sdk_relative_path ] );
				continue;
			}

			$plugin_path = isset( $data->plugin_path ) ? (string) $data->plugin_path : '';
			if ( '' !== $plugin_path && function_exists( 'is_plugin_active' ) && ! is_plugin_active( $plugin_path ) ) {
				continue;
			}

			if ( is_null( $newest_sdk_data )
				|| version_compare( (string) $data->version, (string) $newest_sdk_data->version, '>' )
			) {
				$newest_sdk_data = $data;
				$newest_sdk_path = $sdk_relative_path;
			}
		}

		if ( is_null( $newest_sdk_data ) || null === $newest_sdk_path ) {
			unset( $registry->newest );
			wpb_sdk_save_active_plugins_option();
			return;
		}

		wpb_sdk_update_newest_version(
			$newest_sdk_path,
			isset( $newest_sdk_data->plugin_path ) ? (string) $newest_sdk_data->plugin_path : false
		);
	}
}

if ( ! function_exists( 'wpb_sdk_is_older_runtime_loaded' ) ) {
	/**
	 * Whether an older WPBrigade SDK runtime is already loaded in this request.
	 *
	 * @param string $this_sdk_version Current bundle version.
	 * @return bool
	 */
	function wpb_sdk_is_older_runtime_loaded( $this_sdk_version ) {
		if ( ! class_exists( 'WPBRIGADE_Logger', false ) ) {
			return false;
		}

		$loaded = '0';
		if ( defined( 'WP_WPBRIGADE_SDK_VERSION' ) ) {
			$loaded = (string) WP_WPBRIGADE_SDK_VERSION;
		} elseif ( defined( 'WPBRIGADE_SDK_VERSION' ) ) {
			$loaded = (string) WPBRIGADE_SDK_VERSION;
		}

		return version_compare( $loaded, (string) $this_sdk_version, '<' );
	}
}

if ( ! function_exists( 'wpb_sdk_is_safe_sdk_path' ) ) {
	/**
	 * Validate a DB-stored sdk_path before using it on the filesystem.
	 *
	 * Rejects:
	 *  - Absolute paths (must be relative to WP_PLUGIN_DIR).
	 *  - Any segment containing '..' (directory traversal).
	 *  - Paths that, once resolved, escape WP_PLUGIN_DIR.
	 *
	 * @param string $sdk_path The sdk_path value stored in the DB option.
	 * @return bool True if safe to use.
	 */
	function wpb_sdk_is_safe_sdk_path( $sdk_path ) {
		$sdk_path = (string) $sdk_path;

		if ( '' === $sdk_path ) {
			return false;
		}

		// Reject absolute paths (Unix '/' prefix or Windows drive letter).
		$normalized = wpb_sdk_normalize_path( $sdk_path );
		if ( '/' === $normalized[0] || preg_match( '#^[a-zA-Z]:#', $normalized ) ) {
			return false;
		}

		// Reject traversal sequences.
		if ( false !== strpos( $normalized, '..' ) ) {
			return false;
		}

		// Confirm the resolved path stays inside WP_PLUGIN_DIR.
		$abs_sdk_dir  = wpb_sdk_normalize_path( WP_PLUGIN_DIR . '/' . $normalized );
		$plugins_root = wpb_sdk_normalize_path( WP_PLUGIN_DIR );
		if ( 0 !== strpos( $abs_sdk_dir, $plugins_root . '/' ) ) {
			return false;
		}

		return true;
	}
}

if ( ! function_exists( 'wpb_sdk_resolve_newest_starter_path' ) ) {
	/**
	 * Absolute path to the stored newest start.php, if valid.
	 *
	 * The sdk_path comes from the database. It is validated through
	 * wpb_sdk_is_safe_sdk_path() before being joined onto WP_PLUGIN_DIR so that
	 * a tampered option value cannot force a require_once of an arbitrary file.
	 *
	 * @return string
	 */
	function wpb_sdk_resolve_newest_starter_path() {
		$registry = wpb_sdk_get_active_plugins_option();
		if ( empty( $registry->newest ) || empty( $registry->newest->sdk_path ) ) {
			return '';
		}

		$sdk_path = (string) $registry->newest->sdk_path;
		if ( ! wpb_sdk_is_safe_sdk_path( $sdk_path ) ) {
			return '';
		}

		$path = wpb_sdk_normalize_path(
			WP_PLUGIN_DIR . '/' . $sdk_path . '/start.php'
		);
		return file_exists( $path ) ? $path : '';
	}
}

if ( ! function_exists( 'wpb_sdk_provider_from_db_newest' ) ) {
	/**
	 * Build an in-memory provider payload from the DB newest entry.
	 *
	 * The sdk_path is validated through wpb_sdk_is_safe_sdk_path() before
	 * being joined onto WP_PLUGIN_DIR to prevent path-traversal via a tampered
	 * database option.
	 *
	 * @return array<string, mixed>|null
	 */
	function wpb_sdk_provider_from_db_newest() {
		$registry = wpb_sdk_get_active_plugins_option();
		if ( empty( $registry->newest ) || empty( $registry->newest->sdk_path ) ) {
			return null;
		}

		$sdk_path = (string) $registry->newest->sdk_path;

		// Reject traversal / absolute paths before touching the filesystem.
		if ( ! wpb_sdk_is_safe_sdk_path( $sdk_path ) ) {
			return null;
		}

		$sdk_dir = wpb_sdk_normalize_path( WP_PLUGIN_DIR . '/' . $sdk_path );
		$runtime = $sdk_dir . '/require.php';
		if ( ! is_readable( $runtime ) ) {
			return null;
		}

		$key = '';
		if ( preg_match( '#/lib/wpb-sdk$#', $sdk_dir ) ) {
			$key = sanitize_key( basename( dirname( dirname( $sdk_dir ) ) ) );
		}

		return array(
			'provider_key' => $key,
			'sdk_version'  => isset( $registry->newest->version ) ? (string) $registry->newest->version : '0',
			'runtime_file' => $runtime,
			'sdk_dir'      => $sdk_dir,
			'api_endpoint' => '',
			'plugin_path'  => isset( $registry->newest->plugin_path ) ? (string) $registry->newest->plugin_path : '',
			'sdk_path'     => $sdk_path,
		);
	}
}

if ( ! function_exists( 'wpb_sdk_uninstall_cleanup' ) ) {
	/**
	 * Remove all SDK-managed DB options on plugin uninstall.
	 *
	 * Called from wpb_sdk_run_product_uninstall() in start.php. Only cleans up
	 * when every WPBrigade plugin has been removed (the option tracks all of
	 * them, so deleting it while another is still active would break that
	 * plugin's SDK broker on next load). The registry is consulted first: if it
	 * still lists active SDK bundles the cleanup is deferred.
	 *
	 * @param string $plugin_file Main plugin file from the uninstalling module.
	 * @return void
	 */
	function wpb_sdk_uninstall_cleanup( $plugin_file = '' ) {
		$registry = wpb_sdk_get_active_plugins_option();
		$plugin_basename = '';
		if ( is_string( $plugin_file ) && '' !== $plugin_file ) {
			$plugin_basename = plugin_basename( $plugin_file );
		} elseif ( defined( 'WP_UNINSTALL_PLUGIN' ) && is_string( WP_UNINSTALL_PLUGIN ) ) {
			$plugin_basename = plugin_basename( WP_UNINSTALL_PLUGIN );
		}

		if ( '' !== $plugin_basename ) {
			foreach ( $registry->plugins as $sdk_path => $data ) {
				if ( isset( $data->plugin_path ) && $plugin_basename === (string) $data->plugin_path ) {
					unset( $registry->plugins[ $sdk_path ] );
				}
			}
			if ( ! empty( $registry->newest->plugin_path ) && $plugin_basename === (string) $registry->newest->plugin_path ) {
				unset( $registry->newest );
			}
		}

		// If any SDK entries remain, another plugin is still using the registry.
		if ( ! empty( $registry->plugins ) ) {
			wpb_sdk_fallback_to_newest_active_sdk();
			return;
		}

		delete_option( 'wpb_sdk_active_plugins' );
	}
}
