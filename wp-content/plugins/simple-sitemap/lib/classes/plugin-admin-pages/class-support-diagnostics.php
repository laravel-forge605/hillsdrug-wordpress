<?php
/**
 * Privacy-conscious support diagnostics for the plugin settings page.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Collects environment metadata without reading site content or plugin options.
 */
class Support_Diagnostics {

	/**
	 * Main plugin header data.
	 *
	 * @var array<string, mixed>
	 */
	private $plugin_data;

	/**
	 * Set plugin metadata used in the report.
	 *
	 * @param array<string, mixed> $plugin_data Main plugin header data.
	 */
	public function __construct( $plugin_data ) {
		$this->plugin_data = $plugin_data;
	}

	/**
	 * Collect an allowlisted support report.
	 *
	 * Deliberately excludes URLs, filesystem paths, user data, post content,
	 * license values, and plugin settings.
	 *
	 * @return array<string, string|array<int, string>>
	 */
	public function collect() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$theme          = wp_get_theme();
		$active_plugins = $this->get_active_plugins();
		$block_names    = array();
		if ( class_exists( '\WP_Block_Type_Registry' ) ) {
			foreach ( \WP_Block_Type_Registry::get_instance()->get_all_registered() as $name => $block_type ) {
				if ( 0 === strpos( $name, 'wpgoplugins/simple-sitemap-' ) ) {
					$block_names[] = $name;
				}
			}
		}
		sort( $block_names, SORT_NATURAL | SORT_FLAG_CASE );

		$report = array(
			'Simple Sitemap version' => isset( $this->plugin_data['Version'] ) ? (string) $this->plugin_data['Version'] : 'Unknown',
			'Simple Sitemap tier'    => ss_fs()->can_use_premium_code__premium_only() ? 'Pro' : 'Free',
			'WordPress version'      => get_bloginfo( 'version' ),
			'PHP version'            => PHP_VERSION,
			'Locale'                 => get_locale(),
			'Multisite'              => is_multisite() ? 'Yes' : 'No',
			'Environment'            => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
			'Active theme'           => trim( $theme->get( 'Name' ) . ' ' . $theme->get( 'Version' ) ),
			'Registered blocks'      => $block_names,
			'Active plugins'         => $active_plugins,
		);

		$filtered = apply_filters( 'simple_sitemap_support_diagnostics', $report );

		return is_array( $filtered ) ? $filtered : $report;
	}

	/**
	 * Format a report for pasting into a support request.
	 *
	 * @param array<string, string|array<int, string>> $report Allowlisted report data.
	 * @return string
	 */
	public static function format_report( $report ) {
		$lines = array( 'Simple Sitemap support summary' );
		foreach ( $report as $label => $value ) {
			if ( is_array( $value ) ) {
				$lines[] = $label . ':';
				if ( empty( $value ) ) {
					$lines[] = '  None';
					continue;
				}
				foreach ( $value as $item ) {
					$lines[] = '  - ' . $item;
				}
				continue;
			}

			$lines[] = $label . ': ' . $value;
		}

		return implode( "\n", $lines );
	}

	/**
	 * Render the settings-page diagnostics panel.
	 */
	public function render() {
		$summary = self::format_report( $this->collect() );
		?>
		<h2><?php esc_html_e( 'Support diagnostics', 'simple-sitemap' ); ?></h2>
		<p><?php esc_html_e( 'Copy this summary when requesting support. It contains software versions and active plugin names, but no site content, URLs, user data, settings, or license details.', 'simple-sitemap' ); ?></p>
		<textarea id="simple-sitemap-support-summary" class="large-text code" rows="14" readonly><?php echo esc_textarea( $summary ); ?></textarea>
		<p>
			<button type="button" class="button button-secondary" data-simple-sitemap-copy="simple-sitemap-support-summary" data-copied-label="<?php esc_attr_e( 'Copied.', 'simple-sitemap' ); ?>"><?php esc_html_e( 'Copy support summary', 'simple-sitemap' ); ?></button>
			<span class="simple-sitemap-copy-status" aria-live="polite"></span>
		</p>
		<?php
	}

	/**
	 * Return names and versions for active plugins only.
	 *
	 * @return array<int, string>
	 */
	private function get_active_plugins() {
		$active = get_option( 'active_plugins', array() );
		$active = is_array( $active ) ? $active : array();
		if ( is_multisite() ) {
			$network = get_site_option( 'active_sitewide_plugins', array() );
			if ( is_array( $network ) ) {
				$active = array_merge( $active, array_keys( $network ) );
			}
		}

		$installed = get_plugins();
		$plugins   = array();
		foreach ( array_unique( $active ) as $basename ) {
			if ( ! isset( $installed[ $basename ] ) ) {
				continue;
			}
			$name      = isset( $installed[ $basename ]['Name'] ) ? $installed[ $basename ]['Name'] : $basename;
			$version   = isset( $installed[ $basename ]['Version'] ) ? $installed[ $basename ]['Version'] : '';
			$plugins[] = trim( $name . ' ' . $version );
		}
		sort( $plugins, SORT_NATURAL | SORT_FLAG_CASE );

		return $plugins;
	}
}
