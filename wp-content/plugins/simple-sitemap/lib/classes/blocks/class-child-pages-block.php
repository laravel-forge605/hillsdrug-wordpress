<?php
/**
 * Dynamic Child Pages block renderer.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Render an isolated published-page hierarchy for the Child Pages block.
 */
class Child_Pages_Block {

	/**
	 * Convert block attributes to the narrow core page-tree contract.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 * @param int                  $context_post_id Page containing the block.
	 * @return array<string, mixed>
	 */
	public static function build_list_args( $attributes, $context_post_id = 0 ) {
		$attributes = wp_parse_args( $attributes, Attribute_Schema::CHILD_PAGE_DEFAULTS );
		$post_type  = self::get_post_type( $attributes );
		$parent_id  = absint( $attributes['parent_id'] );
		if ( 0 === $parent_id ) {
			$parent_id = absint( $context_post_id );
		}

		$sort_columns = array(
			'menu_order' => 'menu_order,post_title',
			'title'      => 'post_title',
			'date'       => 'post_date',
			'ID'         => 'ID',
		);
		$orderby      = isset( $sort_columns[ $attributes['orderby'] ] ) ? $attributes['orderby'] : 'menu_order';
		$order        = 'desc' === strtolower( (string) $attributes['order'] ) ? 'DESC' : 'ASC';
		$depth        = min( 10, max( 0, absint( $attributes['depth'] ) ) );

		$list_args = array(
			'child_of'     => $parent_id,
			'depth'        => $depth,
			'hierarchical' => true,
			'post_status'  => 'publish',
			'post_type'    => $post_type,
			'sort_column'  => $sort_columns[ $orderby ],
			'sort_order'   => $order,
		);

		if ( class_exists( Provider_Registry::class ) ) {
			$list_args = Provider_Registry::filter_query_args( $list_args, $attributes, $post_type );
		}

		return $list_args;
	}

	/**
	 * Resolve a public hierarchical post type without changing the Free default.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 * @return string
	 */
	public static function get_post_type( $attributes ) {
		$post_type = isset( $attributes['post_type'] ) ? sanitize_key( (string) $attributes['post_type'] ) : 'page';
		if ( 'page' === $post_type ) {
			return 'page';
		}

		$post_type_object = get_post_type_object( $post_type );
		if ( ! $post_type_object || empty( $post_type_object->hierarchical ) || ! is_post_type_viewable( $post_type_object ) ) {
			return 'page';
		}

		return $post_type;
	}

	/**
	 * Render the dynamic block.
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 * @param string               $content Saved block content (unused for a dynamic block).
	 * @param \WP_Block|null        $block Parsed block and context.
	 * @return string
	 */
	public static function render( $attributes, $content = '', $block = null ) {
		$context_post_id = 0;
		$post_type       = self::get_post_type( $attributes );
		if ( $block instanceof \WP_Block && isset( $block->context['postId'] ) && $post_type === ( $block->context['postType'] ?? '' ) ) {
			$context_post_id = absint( $block->context['postId'] );
		}

		$list_args          = self::build_list_args( $attributes, $context_post_id );
		$filtered_list_args = apply_filters( 'simple_sitemap_child_pages_list_args', $list_args, $attributes, $block );
		if ( is_array( $filtered_list_args ) ) {
			$list_args = $filtered_list_args;
		}
		$list = '';
		if ( ! empty( $list_args['child_of'] ) ) {
			$pages = get_pages( $list_args );
			if ( class_exists( Provider_Registry::class ) ) {
				$pages = array_values(
					array_filter(
						$pages,
						static function ( $page ) use ( $attributes ) {
							return Provider_Registry::should_include_post( (int) $page->ID, $attributes );
						}
					)
				);
			}
			if ( ! empty( $pages ) ) {
				foreach ( $pages as $page ) {
					if ( $page->post_parent ) {
						$list_args['pages_with_children'][ $page->post_parent ] = true;
					}
				}
				$list = walk_page_tree( $pages, $list_args['depth'], 0, $list_args );
			}
		}
		$list = (string) apply_filters( 'simple_sitemap_child_pages_list_html', $list, $attributes, $list_args, $block );

		$parent_id   = isset( $list_args['child_of'] ) ? absint( $list_args['child_of'] ) : 0;
		$show_parent = ! empty( $attributes['show_parent'] ) && Utility::filter_boolean( $attributes['show_parent'] );
		$parent      = $show_parent && $parent_id ? get_post( $parent_id ) : null;
		if ( $parent instanceof \WP_Post && 'publish' === $parent->post_status ) {
			$parent_link = '<a href="' . esc_url( get_permalink( $parent ) ) . '">' . esc_html( get_the_title( $parent ) ) . '</a>';
			$children    = '' !== trim( $list ) ? '<ul class="children">' . $list . '</ul>' : '';
			$body        = '<ul class="simple-sitemap-page main"><li class="simple-sitemap-parent-page page_item page-item-' . absint( $parent->ID ) . '">' . $parent_link . $children . '</li></ul>';
		} elseif ( '' === trim( $list ) ) {
			$body = '<p class="no-posts">' . esc_html__( 'No child pages found.', 'simple-sitemap' ) . '</p>';
		} else {
			$body = '<ul class="simple-sitemap-page main">' . $list . '</ul>';
		}

		$container_classes = 'simple-sitemap-container simple-sitemap-spacing-root simple-sitemap-child-pages';
		$filtered_classes  = apply_filters( 'simple_sitemap_child_pages_container_classes', $container_classes, $attributes, $block );
		if ( is_string( $filtered_classes ) && '' !== trim( $filtered_classes ) ) {
			$container_classes = $filtered_classes;
		}
		$style_properties = apply_filters( 'simple_sitemap_child_pages_style_properties', array(), $attributes, $block );
		$style_properties = is_array( $style_properties ) ? $style_properties : array();
		$html             = '<div class="' . esc_attr( $container_classes ) . '"' . Sitemap_Styles::style_attribute( $attributes, $style_properties ) . '>' . $body . '</div>';
		$html             = Block_Wrapper::wrap( $html, 'simple-sitemap-child-pages-block' );

		return (string) apply_filters( 'simple_sitemap_child_pages_output', $html, $attributes, $list_args, $block );
	}
}
