<?php
/**
 * Simple Sitemap option defaults and reads.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Provides front-end-safe access to plugin options without an admin controller.
 */
class Settings_Repository {

	/**
	 * Register plugin-specific defaults.
	 */
	public function __construct() {
		add_filter( 'simple_sitemap_defaults', array( $this, 'add_defaults' ) );
	}

	/**
	 * Add Simple Sitemap option defaults.
	 *
	 * @param array<string, mixed> $defaults Current plugin defaults.
	 * @return array<string, mixed> Updated plugin defaults.
	 */
	public function add_defaults( $defaults ) {
		$defaults['txtar_sitemap_script']                          = '';
		$defaults['chk_parent_page_link']                          = '0';
		$defaults['txt_exclude_parent_pages']                      = '';
		$defaults['sitemap_spacing_preset']                        = 'inherit';
		$defaults['sitemap_item_spacing']                          = '';
		$defaults['sitemap_nested_spacing']                        = '';
		$defaults['default_on_checkboxes']['chk_parent_page_link'] = '0';

		return $defaults;
	}

	/**
	 * Get plugin option defaults after compatibility filters run.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_defaults() {
		return Hooks::simple_sitemap_defaults(
			array(
				'default_on_checkboxes' => array(),
			)
		);
	}

	/**
	 * Get stored options merged with current defaults.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_options() {
		$options  = get_option( 'simple_sitemap_options' );
		$defaults = self::get_defaults();

		return self::merge_options( $options, $defaults );
	}

	/**
	 * Merge a stored option value with a complete default definition.
	 *
	 * @param mixed                $options Stored WordPress option value.
	 * @param array<string, mixed> $defaults Filtered option defaults.
	 * @return array<string, mixed>
	 */
	public static function merge_options( $options, $defaults ) {
		$checkbox_defaults = isset( $defaults['default_on_checkboxes'] ) && is_array( $defaults['default_on_checkboxes'] ) ? $defaults['default_on_checkboxes'] : array();
		unset( $defaults['default_on_checkboxes'] );

		if ( is_array( $options ) ) {
			$options = array_merge( $checkbox_defaults, $options );
		}

		return wp_parse_args( $options, $defaults );
	}
}
