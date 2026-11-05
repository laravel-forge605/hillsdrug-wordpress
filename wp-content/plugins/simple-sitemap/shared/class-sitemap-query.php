<?php
/**
 * Query construction and execution for Content and Grouped sitemaps.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Keeps WordPress query concerns out of the HTML renderer.
 */
class Sitemap_Query {

	/**
	 * Build the compatibility-sensitive WP_Query arguments.
	 *
	 * @param array  $args Normalized legacy sitemap arguments.
	 * @param string $post_type Post type name.
	 * @return array<string, mixed>
	 */
	public static function build_args( $args, $post_type ) {
		$extra_query_args = array(
			'post__in'     => Shortcode_Utility::parse_id_list( isset( $args['include'] ) ? $args['include'] : '' ),
			'post__not_in' => Shortcode_Utility::parse_id_list( isset( $args['exclude'] ) ? $args['exclude'] : '' ),
		);
		$extra_query_args = apply_filters( '_simple_sitemap_extra_query_args', $extra_query_args, $args );

		$tax_query  = empty( $args['tax_query'] ) ? '' : $args['tax_query'];
		$query_args = array(
			'post_type'           => $post_type,
			'order'               => $args['order'],
			'orderby'             => $args['orderby'],
			'tax_query'           => $tax_query,
			'posts_per_page'      => -1,
			'ignore_sticky_posts' => 1,
			'post_status'         => 'publish',
			'no_found_rows'       => true,
		);

		$query_args = Visibility_Policy::apply_to_query( $query_args, $args, $post_type );
		$query_args = Provider_Registry::filter_query_args( $query_args, $args, $post_type );

		$query_args = array_merge( $query_args, $extra_query_args );

		return Sitemap_Pagination::apply_query_args( $query_args, $args, $post_type );
	}

	/**
	 * Execute a prepared sitemap query.
	 *
	 * @param array<string, mixed> $query_args WP_Query arguments.
	 * @return \WP_Query
	 */
	public static function execute( $query_args ) {
		return new \WP_Query( $query_args );
	}
}
