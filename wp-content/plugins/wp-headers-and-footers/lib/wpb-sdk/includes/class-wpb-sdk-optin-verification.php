<?php
/**
 * Shared email opt-in verification (one implementation for all WPBrigade SDK products).
 *
 * @package wpbrigade_sdk
 * @since   3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dynamic opt-in verification driven by each plugin's wpb_sdk_dynamic_init() module config.
 */
class WPBRIGADE_Optin_Verification {

	/**
	 * Registered modules keyed by slug.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	private static $modules = array();

	/**
	 * Whether the single init handler is registered.
	 *
	 * @var bool
	 */
	private static $init_hooked = false;

	/**
	 * Register a product module that uses email opt-in verification.
	 *
	 * @param array<string, mixed> $module Module definition from wpb_dynamic_init().
	 * @return void
	 */
	public static function register_module( array $module ) {
		if ( empty( $module['slug'] ) ) {
			return;
		}
		if (
			empty( $module['optin_user_meta']['token'] )
			|| ( function_exists( 'wpb_sdk_email_verified_meta_key_from_module' )
				&& '' === wpb_sdk_email_verified_meta_key_from_module( $module ) )
		) {
			return;
		}

		$slug                   = (string) $module['slug'];
		self::$modules[ $slug ] = $module;

		if ( did_action( 'init' ) ) {
			self::handle_request();
		} elseif ( ! self::$init_hooked ) {
			self::$init_hooked = true;
			add_action( 'init', array( __CLASS__, 'handle_request' ), 1 );
		}
	}

	/**
	 * Handle verification link for whichever registered product matches the query string.
	 *
	 * @return void
	 */
	public static function handle_request() {
		foreach ( self::$modules as $slug => $module ) {
			if ( self::request_has_verify_token( $slug, $module ) ) {
				self::verify( $slug, $module );
				return;
			}
		}
	}

	/**
	 * Admin user ID whose meta holds the token matching the email link.
	 *
	 * @param string $slug               Product slug.
	 * @param string $token_meta         Verification token user-meta key.
	 * @param string $verification_token Token from the verify URL.
	 * @param int    $current_user_id    Logged-in user ID.
	 * @return int
	 */
	private static function resolve_verification_token_owner_id( $slug, $token_meta, $verification_token, $current_user_id ) {
		$candidate_ids = array();
		if ( $current_user_id > 0 ) {
			$candidate_ids[] = $current_user_id;
		}
		if ( function_exists( 'wpb_sdk_resolve_optin_verification_user_id' ) ) {
			$contact_id = (int) wpb_sdk_resolve_optin_verification_user_id( $slug );
			if ( $contact_id > 0 && ! in_array( $contact_id, $candidate_ids, true ) ) {
				$candidate_ids[] = $contact_id;
			}
		}

		foreach ( $candidate_ids as $user_id ) {
			$stored = get_user_meta( $user_id, $token_meta, true );
			if (
				is_string( $stored )
				&& '' !== $stored
				&& hash_equals( (string) $stored, (string) $verification_token )
			) {
				return (int) $user_id;
			}
		}

		return 0;
	}

	/**
	 * @param string               $slug   Product slug.
	 * @param array<string, mixed> $module Module definition.
	 * @return bool
	 */
	private static function request_has_verify_token( $slug, array $module ) {
		$optin      = isset( $module['optin'] ) && is_array( $module['optin'] ) ? $module['optin'] : array();
		$query_args = self::verify_query_args( $slug, $optin );

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Secret token in email verification link.
		foreach ( $query_args as $query_arg ) {
			if ( ! isset( $_GET[ $query_arg ] ) ) {
				continue;
			}
			$token = sanitize_text_field( wp_unslash( (string) $_GET[ $query_arg ] ) );
			if ( '' !== $token ) {
				return true;
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		return false;
	}

	/**
	 * Extract verification token from GET query parameters.
	 *
	 * @param string[] $query_args Query argument keys to check.
	 * @return string Verification token or empty string.
	 */
	private static function extract_token_from_request( array $query_args ) {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Email link carries a secret verification token.
		foreach ( $query_args as $query_arg ) {
			if ( isset( $_GET[ $query_arg ] ) ) {
				$token = sanitize_text_field( wp_unslash( (string) $_GET[ $query_arg ] ) );
				if ( '' !== $token ) {
					return $token;
				}
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		return '';
	}

	/**
	 * Retrieve saved verification token from user meta or option fallbacks.
	 *
	 * @param int                  $user_id    Current user ID.
	 * @param string               $token_meta User meta key.
	 * @param array<string, mixed> $optin      Optin config.
	 * @param string               $slug       Product slug.
	 * @return string Saved token or empty string.
	 */
	private static function get_persisted_verification_token( $user_id, $token_meta, array $optin, $slug ) {
		if ( $user_id > 0 && '' !== $token_meta ) {
			$meta_val = get_user_meta( $user_id, $token_meta, true );
			if ( is_string( $meta_val ) && '' !== $meta_val ) {
				return $meta_val;
			}
		}
		if ( ! empty( $optin['legacy_token_option'] ) ) {
			$legacy = get_option( (string) $optin['legacy_token_option'], '' );
			if ( is_string( $legacy ) && '' !== $legacy ) {
				return $legacy;
			}
		}
		$fallback = get_option( 'wpb_sdk_' . $slug . '_fallback_verify_token', '' );
		return ( is_string( $fallback ) && '' !== $fallback ) ? $fallback : '';
	}

	/**
	 * Execute state updates and hooks upon successful opt-in verification.
	 *
	 * @param WP_User              $current_user Current user object.
	 * @param string               $slug         Product slug.
	 * @param array<string, mixed> $module       Module definition.
	 * @param string               $token_meta     User meta key.
	 * @param int                  $target_user_id Target user ID whose token was verified.
	 * @return void
	 */
	private static function perform_optin_success_actions( WP_User $current_user, $slug, array $module, $token_meta, $target_user_id = 0 ) {
		$user_id = $target_user_id > 0 ? $target_user_id : (int) $current_user->ID;
		$optin   = isset( $module['optin'] ) && is_array( $module['optin'] ) ? $module['optin'] : array();

		if ( function_exists( 'wpb_sdk_set_user_email_verified' ) ) {
			wpb_sdk_set_user_email_verified( $slug, $user_id );
		}

		if ( function_exists( 'wpb_sdk_legacy_upgrade_optin_option_key' ) ) {
			delete_option( wpb_sdk_legacy_upgrade_optin_option_key( $slug ) );
		}
		if ( '' !== $token_meta ) {
			delete_user_meta( $user_id, $token_meta );
		}
		$expires_meta = function_exists( 'wpb_sdk_verification_token_expires_meta_key' )
			? wpb_sdk_verification_token_expires_meta_key( $module )
			: $token_meta . '_expires';
		delete_user_meta( $user_id, $expires_meta );
		delete_option( 'wpb_sdk_' . $slug . '_fallback_verify_token' );
		delete_transient( 'wpb_sdk_' . $slug . '_pending_verify_notice' );
		if ( ! empty( $optin['legacy_token_option'] ) ) {
			delete_option( (string) $optin['legacy_token_option'] );
		}

		$optin_option = ! empty( $optin['option_name'] )
			? (string) $optin['option_name']
			: self::guess_option_name( $module );
		if ( '' !== $optin_option ) {
			update_option( $optin_option, 'yes' );
		}

		if ( ! empty( $optin['verified_by_option'] ) && ! empty( $optin['store_verified_by_user'] ) ) {
			update_option(
				(string) $optin['verified_by_option'],
				array(
					'user_id'     => $user_id,
					'email'       => sanitize_email( $current_user->user_email ),
					'verified_at' => current_time( 'mysql' ),
				)
			);
		}

		do_action( 'wpb_sdk_optin_verified', $current_user, $module );
	}

	/**
	 * Verify permissions and already-verified state before proceeding.
	 *
	 * @param array<string, mixed> $optin        Module optin config.
	 * @param string               $settings_url Fallback redirect URL.
	 * @return void
	 */
	private static function check_verification_prerequisites( array $optin, $settings_url ) {
		if ( ! empty( $optin['require_manage_options'] ) && ! current_user_can( 'manage_options' ) ) {
			wp_safe_redirect( add_query_arg( 'verified', 'unauthorized', $settings_url ) );
			exit;
		}

		if ( ! empty( $optin['verified_by_option'] ) && ! empty( get_option( (string) $optin['verified_by_option'] ) ) ) {
			$already = ! empty( $optin['already_verified_query'] )
				? (string) $optin['already_verified_query']
				: 'already_verified';
			wp_safe_redirect( add_query_arg( 'verified', $already, $settings_url ) );
			exit;
		}
	}

	/**
	 * @param string               $slug   Product slug.
	 * @param array<string, mixed> $module Module definition.
	 * @return void
	 */
	private static function verify( $slug, array $module ) {
		$optin              = isset( $module['optin'] ) && is_array( $module['optin'] ) ? $module['optin'] : array();
		$token_meta         = isset( $module['optin_user_meta']['token'] ) ? (string) $module['optin_user_meta']['token'] : '';
		$query_args         = self::verify_query_args( $slug, $optin );
		$verification_token = self::extract_token_from_request( $query_args );

		if ( '' === $verification_token ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			auth_redirect();
			return;
		}

		$current_user = wp_get_current_user();
		$settings_url = function_exists( 'wpb_sdk_resolve_optin_redirect_url' )
			? wpb_sdk_resolve_optin_redirect_url( $slug )
			: self::settings_redirect_url( $optin );

		self::check_verification_prerequisites( $optin, $settings_url );

		$token_owner_id = self::resolve_verification_token_owner_id(
			$slug,
			$token_meta,
			$verification_token,
			(int) $current_user->ID
		);

		if ( $token_owner_id > 0
			&& function_exists( 'wpb_sdk_is_verification_token_expired' )
			&& wpb_sdk_is_verification_token_expired( $slug, $token_owner_id )
		) {
			wp_safe_redirect( add_query_arg( 'verified', 'expired', $settings_url ) );
			exit;
		}

		$target_user_id = $token_owner_id > 0 ? $token_owner_id : (int) $current_user->ID;
		$saved_token    = self::get_persisted_verification_token( $target_user_id, $token_meta, $optin, $slug );
		if ( '' !== $saved_token && hash_equals( $saved_token, $verification_token ) ) {
			self::perform_optin_success_actions( $current_user, $slug, $module, $token_meta, $target_user_id );
			wp_safe_redirect( add_query_arg( 'verified', 'success', $settings_url ) );
			exit;
		}

		wp_safe_redirect( add_query_arg( 'verified', 'failed', $settings_url ) );
		exit;
	}

	/**
	 * @param string               $slug  Product slug.
	 * @param array<string, mixed> $optin Module optin config.
	 * @return string[]
	 */
	private static function verify_query_args( $slug, array $optin ) {
		$args = array();
		if ( ! empty( $optin['verify_query_args'] ) && is_array( $optin['verify_query_args'] ) ) {
			$args = array_map( 'strval', $optin['verify_query_args'] );
		} elseif ( ! empty( $optin['verify_query_arg'] ) ) {
			$args[] = (string) $optin['verify_query_arg'];
		}

		// Laravel / default email links use {slug}_optin_verify (e.g. simple-social-buttons_optin_verify).
		$default = $slug . '_optin_verify';
		if ( ! in_array( $default, $args, true ) ) {
			$args[] = $default;
		}

		return $args;
	}

	/**
	 * @param array<string, mixed> $optin Module optin config.
	 * @return string
	 */
	private static function settings_redirect_url( array $optin ) {
		if ( ! empty( $optin['settings_admin_path'] ) ) {
			return admin_url( (string) $optin['settings_admin_path'] );
		}
		if ( ! empty( $optin['settings_page'] ) ) {
			return admin_url( 'admin.php?page=' . (string) $optin['settings_page'] );
		}
		return admin_url();
	}

	/**
	 * @param array<string, mixed> $module Module definition.
	 * @return string
	 */
	private static function guess_option_name( array $module ) {
		if ( empty( $module['settings'] ) || ! is_array( $module['settings'] ) ) {
			return '';
		}
		foreach ( array_keys( $module['settings'] ) as $key ) {
			if ( is_string( $key ) && preg_match( '/^_.*_optin$/', $key ) ) {
				return $key;
			}
		}
		return '';
	}
}
