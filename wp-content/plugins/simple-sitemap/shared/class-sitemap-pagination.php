<?php
/**
 * Opt-in linked pagination for dynamic sitemaps.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Keeps pagination query state and markup out of legacy renderers.
 */
class Sitemap_Pagination {

	/**
	 * Maximum page size accepted from public block and shortcode attributes.
	 */
	const MAX_PAGE_SIZE = 200;

	/**
	 * Prevent hostile query strings from creating extreme database offsets.
	 */
	const MAX_PAGE_NUMBER = 10000;

	/**
	 * Whether linked pagination was explicitly enabled.
	 *
	 * @param array<string, mixed> $args Sitemap arguments.
	 * @return bool
	 */
	public static function is_enabled( $args ) {
		return isset( $args['paginate'] ) && Utility::filter_boolean( $args['paginate'] );
	}

	/**
	 * Create a stable URL-key fragment before the renderer generates its visual ID.
	 *
	 * @param string               $requested_id User-supplied sitemap ID.
	 * @param array<string, mixed> $identity Stable sitemap attributes.
	 * @return string
	 */
	public static function create_instance_key( $requested_id, $identity ) {
		$requested_id = sanitize_key( $requested_id );
		if ( '' !== $requested_id ) {
			return substr( $requested_id, 0, 32 );
		}

		$encoded = wp_json_encode( $identity );

		return substr( hash( 'sha256', is_string( $encoded ) ? $encoded : '' ), 0, 16 );
	}

	/**
	 * Build the query-string key for one sitemap post type.
	 *
	 * @param array<string, mixed> $args Sitemap arguments.
	 * @param string               $post_type Post type name.
	 * @return string
	 */
	public static function get_query_var( $args, $post_type ) {
		$instance_key = isset( $args['_pagination_key'] ) ? sanitize_key( (string) $args['_pagination_key'] ) : 'sitemap';
		$post_type    = sanitize_key( $post_type );
		$scope        = isset( $args['_pagination_scope'] ) ? sanitize_key( (string) $args['_pagination_scope'] ) : '';
		$scope        = '' === $scope ? '' : '_' . $scope;

		return 'ss_' . $instance_key . '_' . $post_type . $scope . '_page';
	}

	/**
	 * Apply bounded pagination arguments without touching the default query path.
	 *
	 * @param array<string, mixed> $query_args Prepared WP_Query arguments.
	 * @param array<string, mixed> $args Sitemap arguments.
	 * @param string               $post_type Post type name.
	 * @return array<string, mixed>
	 */
	public static function apply_query_args( $query_args, $args, $post_type ) {
		if ( ! self::is_enabled( $args ) ) {
			return $query_args;
		}

		$page_size = 50;
		if ( isset( $args['page_size'] ) && is_numeric( $args['page_size'] ) ) {
			$page_size = absint( $args['page_size'] );
		}
		$page_size = min( self::MAX_PAGE_SIZE, max( 1, $page_size ) );
		$query_var = self::get_query_var( $args, $post_type );
		$page      = 1;
		if ( isset( $_GET[ $query_var ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only public pagination state.
			$page = min(
				self::MAX_PAGE_NUMBER,
				max( 1, absint( wp_unslash( $_GET[ $query_var ] ) ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only public pagination state.
			);
		}

		$query_args['posts_per_page'] = $page_size;
		$query_args['paged']          = $page;
		$query_args['no_found_rows']  = false;

		// Random ordering cannot produce stable pages across requests.
		if ( isset( $query_args['orderby'] ) && 'rand' === strtolower( (string) $query_args['orderby'] ) ) {
			$query_args['orderby'] = 'title';
		}

		$filtered = apply_filters( 'simple_sitemap_pagination_query_args', $query_args, $args, $post_type, $query_var );

		return is_array( $filtered ) ? $filtered : $query_args;
	}

	/**
	 * Render accessible server-side pagination links for an executed query.
	 *
	 * @param \WP_Query           $query Executed sitemap query.
	 * @param array<string, mixed> $args Sitemap arguments.
	 * @param string               $post_type Post type name.
	 * @return string
	 */
	public static function render( $query, $args, $post_type ) {
		if ( ! self::is_enabled( $args ) || $query->max_num_pages < 2 ) {
			return '';
		}

		$query_var = self::get_query_var( $args, $post_type );
		$current   = isset( $query->query_vars['paged'] ) ? max( 1, absint( $query->query_vars['paged'] ) ) : 1;
		$base      = add_query_arg( $query_var, '%#%', remove_query_arg( $query_var ) );
		$base      = str_replace( rawurlencode( '%#%' ), '%#%', $base );
		$base      = (string) apply_filters( 'simple_sitemap_pagination_base_url', $base, $args, $post_type, $query_var );
		$links     = paginate_links(
			array(
				'base'      => $base,
				'format'    => '',
				'current'   => min( $current, (int) $query->max_num_pages ),
				'total'     => (int) $query->max_num_pages,
				'mid_size'  => 1,
				'end_size'  => 1,
				'prev_text' => esc_html__( 'Previous', 'simple-sitemap' ),
				'next_text' => esc_html__( 'Next', 'simple-sitemap' ),
				'type'      => 'list',
			)
		);

		if ( '' === $links ) {
			return '';
		}

		$post_type_object = get_post_type_object( $post_type );
		$post_type_label  = isset( $args['_pagination_label'] ) && '' !== trim( (string) $args['_pagination_label'] )
			? sanitize_text_field( (string) $args['_pagination_label'] )
			: ( $post_type_object ? $post_type_object->labels->name : $post_type );
		/* translators: %s: Post type label. */
		$aria_label = sprintf( __( '%s sitemap pages', 'simple-sitemap' ), $post_type_label );

		return '<nav class="simple-sitemap-pagination" aria-label="' . esc_attr( $aria_label ) . '">' . $links . '</nav>';
	}
}
