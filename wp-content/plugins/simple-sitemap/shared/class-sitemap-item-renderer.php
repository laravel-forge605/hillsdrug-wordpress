<?php
/**
 * Shared sitemap list-item template.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Renders one already-escaped sitemap item without querying or global state.
 */
class Sitemap_Item_Renderer {

	/**
	 * Build an opening list item and its content.
	 *
	 * The caller owns the closing tag because hierarchical walkers insert child
	 * lists before closing their parent item.
	 *
	 * @param string $title Title/link HTML.
	 * @param string $excerpt Excerpt HTML.
	 * @param string $image Image HTML.
	 * @param string $separator Separator HTML.
	 * @param string $horizontal_separator Horizontal separator HTML.
	 * @param string $classes List-item classes.
	 * @param string $indent Optional walker indentation.
	 * @return string
	 */
	public static function open_item( $title, $excerpt, $image = '', $separator = '', $horizontal_separator = '', $classes = 'sitemap-item', $indent = '' ) {
		return $indent . '<li class="' . $classes . '">' . $image . $title . $excerpt . $separator . $horizontal_separator;
	}
}
