<?php
/**
 * Theme-aware wrapper for new dynamic sitemap blocks.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Applies WordPress block supports without changing legacy shortcode markup.
 */
class Block_Wrapper {

	/**
	 * Wrap rendered block HTML with generated support classes and styles.
	 *
	 * @param string $html Inner block markup.
	 * @param string $class_name Plugin-specific wrapper class.
	 * @return string
	 */
	public static function wrap( $html, $class_name ) {
		$class_name = sanitize_html_class( $class_name );
		$attributes = 'class="' . esc_attr( $class_name ) . '"';

		// Core only exposes support context while rendering an actual block.
		// Direct calls, including REST previews, retain the stable plugin class.
		if ( class_exists( '\WP_Block_Supports' ) && is_array( \WP_Block_Supports::$block_to_render ) ) {
			$attributes = get_block_wrapper_attributes(
				array(
					'class' => $class_name,
				)
			);
		}

		return '<div ' . $attributes . '>' . $html . '</div>';
	}
}
