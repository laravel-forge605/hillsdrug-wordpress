<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Custom walker class to render hierarchical pages.
 *
 * Backslash needed here as 'Walker' class is outside of the 'WPGO_Plugins\Simple_Sitemap' namespace.
 */
class WPGO_Walker_Page extends \Walker_Page {

	/**
	 * Normalized sitemap arguments supplied by the shortcode renderer.
	 *
	 * @var array<string, mixed>
	 */
	public $ssp_args;

	/**
	 * @see Walker::start_lvl()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of page. Used for padding.
	 * @param array  $args   Sitemap arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul class='children'>\n";
	}

	/**
	 * @see Walker::end_lvl()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of page. Used for padding.
	 * @param array  $args   Sitemap arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	/**
	 * @see Walker::start_el()
	 *
	 * @param string $output       Passed by reference. Used to append additional content.
	 * @param object $page         Page data object.
	 * @param int    $depth        Depth of page. Used for padding.
	 * @param array  $args         Sitemap arguments.
	 * @param int    $current_page Current page ID.
	 */
	public function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {

		$tmp              = isset( $args['_simple_sitemap_options'] ) && is_array( $args['_simple_sitemap_options'] ) ? $args['_simple_sitemap_options'] : Settings_Repository::get_options();
		$parent_page_link = isset( $tmp['chk_parent_page_link'] ) ? (string) $tmp['chk_parent_page_link'] : '0';

		$parent_page = false;

		if ( $depth ) {
			$indent = str_repeat( "\t", $depth );
		} else {
			$indent = '';
		}

		$css_class = array( 'page_item', 'page-item-' . $page->ID );

		if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
			$css_class[] = 'page_item_has_children';

			if ( '1' === $parent_page_link ) {
				if ( ! empty( $tmp['txt_exclude_parent_pages'] ) ) {
					// Process IDs.
					$ids = array_map( 'absint', explode( ',', $tmp['txt_exclude_parent_pages'] ) );
					if ( in_array( (int) $page->ID, $ids, true ) ) {
						$parent_page = true;
					}
				} else {
					// Remove all parent page IDs.
					$parent_page = true;
				}
			} else {
				// Remove all parent page IDs.
				$parent_page = true;
			}
		}

		$css_classes = implode( ' ', $css_class );

		if ( '' === $page->post_title ) {
			/* translators: %d: ID of a post */
			$page->post_title = sprintf( __( '#%d (no title)', 'simple-sitemap' ), $page->ID );
		}

		$args['link_before'] = empty( $args['link_before'] ) ? '' : $args['link_before'];
		$args['link_after']  = empty( $args['link_after'] ) ? '' : $args['link_after'];

		$image_html     = (string) apply_filters( '_simple_sitemap_image_html', '', $page->ID, $args );
		$separator_html = (string) apply_filters( '_simple_sitemap_separator_html', '', $args );
		$horizontal_sep = (string) apply_filters( '_simple_sitemap_horizontal_separator_v1', '', $args );
		$excerpt_text   = (string) apply_filters( '_simple_sitemap_page_excerpt_text', strip_shortcodes( $page->post_content ), $args );

		$title_text = Hooks::simple_sitemap_title_text( (string) $page->post_title, (int) $page->ID );
		$permalink  = (string) get_permalink( $page->ID );
		$title      = Shortcode_Utility::get_the_title( $title_text, $permalink, $args, $parent_page, $parent_page_link );
		$title      = Hooks::simple_sitemap_title_link_text( $title, $page->ID );
		$excerpt    = $args['show_excerpt'] == 'true' ? '<' . $args['excerpt_tag'] . ' class="excerpt">' . $excerpt_text . '</' . $args['excerpt_tag'] . '>' : '';

		$output .= Sitemap_Item_Renderer::open_item(
			$title,
			$excerpt,
			$image_html,
			$separator_html,
			$horizontal_sep,
			'sitemap-item ' . $css_classes,
			$indent
		);

		if ( ! empty( $args['show_date'] ) ) {
			if ( 'modified' == $args['show_date'] ) {
				$time = $page->post_modified;
			} else {
				$time = $page->post_date;
			}

			$date_format = empty( $args['date_format'] ) ? '' : $args['date_format'];
			$output     .= ' ' . mysql2date( $date_format, $time );
		}
	}

	/**
	 * @see Walker::end_el()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Page data object. Not used.
	 * @param int    $depth Depth of page. Not Used.
	 * @param array  $args
	 */
	public function end_el( &$output, $page, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}

	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max
	 * depth and no ignore elements under that depth. It is possible to set the
	 * max depth to include all depths, see walk() method.
	 *
	 * This method should not be called directly, use the walk() method instead.
	 *
	 * @param object $element           Data object.
	 * @param array  $children_elements List of elements to continue traversing.
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args              An array of arguments.
	 * @param string $output            Passed by reference. Used to append additional content.
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( Visibility_Policy::should_skip( $element->ID, $this->ssp_args ) ) {
			return;
		}

		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}
