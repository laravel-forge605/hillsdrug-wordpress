<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Handle version upgrades and the new-features menu badge.
 */
class Upgrade {

	/**
	 * Common root paths/directories.
	 *
	 * @var array<string, string>
	 */
	protected $module_roots;

	/**
	 * Custom plugin data.
	 *
	 * @var object
	 */
	protected $custom_plugin_data;

	/**
	 * Main class constructor.
	 *
	 * @param array  $module_roots Root plugin path/dir.
	 * @param object $custom_plugin_data Plugin data.
	 */
	public function __construct( $module_roots, $custom_plugin_data ) {
		$this->module_roots       = $module_roots;
		$this->custom_plugin_data = $custom_plugin_data;

		add_action( 'plugins_loaded', array( $this, 'upgrade_routine' ) );
	}

	/**
	 * Update the stored plugin version and new-features badge state.
	 */
	public function upgrade_routine() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only editor-route detection.
		if ( ! is_admin() || isset( $_GET['post'] ) ) {
			return;
		}

		$option_prefix = $this->custom_plugin_data->db_option_prefix;
		$plugin_data   = get_plugin_data( $this->module_roots['file'], false, false );
		$current       = $plugin_data['Version'];
		$options       = get_option( $option_prefix . '_options', array() );
		$stored        = isset( $options['plugin_version'] ) ? $options['plugin_version'] : '0.0.0';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin-page detection.
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

		if ( version_compare( $current, $stored, '=' ) ) {
			if ( false !== strpos( $page, $this->custom_plugin_data->plugin_slug ) && false !== strpos( $page, '-new-features' ) ) {
				$options['new_features_numbered_icon'] = 'false';
				update_option( $option_prefix . '_options', $options );
			}
			return;
		}

		$options['new_features_numbered_icon'] = 'true';
		$options['plugin_version']             = $current;
		update_option( $option_prefix . '_options', $options );
	}

	/**
	 * Calculate the number displayed in the new-features menu badge.
	 *
	 * @param string $option_prefix Plugin option prefix.
	 * @param array  $new_features New feature definitions.
	 * @param array  $plugin_data WordPress plugin metadata.
	 * @return int
	 */
	public static function calc_new_features( $option_prefix, $new_features, $plugin_data ) {
		$options = get_option( $option_prefix . '_options', array() );
		if ( 'true' !== ( $options['new_features_numbered_icon'] ?? 'false' ) ) {
			return 0;
		}

		$count = 0;
		foreach ( $new_features as $new_feature ) {
			if ( ! is_object( $new_feature ) || ! isset( $new_feature->version ) ) {
				continue;
			}

			if ( $plugin_data['Version'] === $new_feature->version || 'latest' === $new_feature->version ) {
				++$count;
			}
		}

		return $count;
	}
}
