<?php
/**
 * Structured editor-preview data for dynamic sitemap blocks.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Supply bounded, capability-gated data while front-end rendering stays in PHP.
 */
class Editor_Preview_Controller {

	/** Maximum records returned to one editor preview. */
	const PREVIEW_LIMIT = 100;

	/**
	 * Register preview routes.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register structured preview endpoints.
	 */
	public function register_routes() {
		$routes = array(
			'/child-pages-preview'     => 'get_child_pages',
			'/taxonomy-terms-preview'  => 'get_taxonomy_terms',
			'/navigation-menu-preview' => 'get_navigation_menu_items',
		);

		foreach ( $routes as $route => $callback ) {
			register_rest_route(
				'simple-sitemap/v1',
				$route,
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, $callback ),
					'permission_callback' => array( $this, 'check_permission' ),
					'args'                => array(
						'attributes' => array(
							'type'    => 'object',
							'default' => array(),
						),
					),
				)
			);
		}
	}

	/**
	 * Restrict preview data to users who can edit content.
	 *
	 * @return \WP_Error|true
	 */
	public function check_permission() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'No permissions to preview sitemap data.', 'simple-sitemap' ), array( 'status' => 403 ) );
		}

		return true;
	}

	/**
	 * Return a bounded flat page tree for the native editor preview.
	 *
	 * @param \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response
	 */
	public function get_child_pages( $request ) {
		$attributes      = $this->get_attributes( $request );
		$context_post_id = absint( $request->get_param( 'context_post_id' ) );
		$list_args       = Child_Pages_Block::build_list_args( $attributes, $context_post_id );
		$filtered_args   = apply_filters( 'simple_sitemap_child_pages_list_args', $list_args, $attributes, null );
		if ( is_array( $filtered_args ) ) {
			$list_args = $filtered_args;
		}
		unset( $list_args['walker'], $list_args['pages_with_children'] );
		$list_args['number'] = self::PREVIEW_LIMIT + 1;

		$pages = empty( $list_args['child_of'] ) ? array() : get_pages( $list_args );
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

		$items = array_map(
			static function ( $page ) {
				$title = $page->post_title;
				if ( '' === $title ) {
					/* translators: %d: ID of an untitled post. */
					$title = sprintf( __( '#%d (no title)', 'simple-sitemap' ), $page->ID );
				}

				return array(
					'id'     => (int) $page->ID,
					'parent' => (int) $page->post_parent,
					'title'  => $title,
					'url'    => (string) get_permalink( $page ),
				);
			},
			$pages
		);

		if ( ! empty( $attributes['show_parent'] ) && ! empty( $list_args['child_of'] ) ) {
			$parent = get_post( absint( $list_args['child_of'] ) );
			if ( $parent && 'publish' === $parent->post_status ) {
				$parent_title = $parent->post_title;
				if ( '' === $parent_title ) {
					/* translators: %d: ID of an untitled post. */
					$parent_title = sprintf( __( '#%d (no title)', 'simple-sitemap' ), $parent->ID );
				}
				array_unshift(
					$items,
					array(
						'id'     => (int) $parent->ID,
						'parent' => 0,
						'title'  => $parent_title,
						'url'    => (string) get_permalink( $parent ),
					)
				);
			}
		}

		return $this->respond_with_items(
			$items,
			esc_html__( 'No child pages found.', 'simple-sitemap' )
		);
	}

	/**
	 * Return a bounded flat public-term tree.
	 *
	 * @param \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response
	 */
	public function get_taxonomy_terms( $request ) {
		$attributes = wp_parse_args( $this->get_attributes( $request ), Attribute_Schema::TAXONOMY_TERMS_BLOCK_DEFAULTS );
		$taxonomy   = get_taxonomy( sanitize_key( (string) $attributes['taxonomy'] ) );
		if ( ! $taxonomy || ! $taxonomy->public ) {
			return $this->respond_with_items( array(), esc_html__( 'Select a public taxonomy.', 'simple-sitemap' ) );
		}

		$allowed_orderby = array( 'name', 'count', 'slug', 'term_id' );
		$orderby         = in_array( $attributes['orderby'], $allowed_orderby, true ) ? $attributes['orderby'] : 'name';
		$terms           = get_terms(
			array(
				'taxonomy'   => $taxonomy->name,
				'include'    => Shortcode_Utility::parse_id_list( $attributes['include'] ),
				'exclude'    => Shortcode_Utility::parse_id_list( $attributes['exclude'] ),
				'child_of'   => absint( $attributes['child_of'] ),
				'hide_empty' => Utility::filter_boolean( $attributes['hide_empty'] ),
				'orderby'    => $orderby,
				'order'      => 'DESC' === strtoupper( (string) $attributes['order'] ) ? 'DESC' : 'ASC',
				'number'     => self::PREVIEW_LIMIT + 1,
			)
		);

		if ( is_wp_error( $terms ) ) {
			return $this->respond_with_items( array(), esc_html__( 'The taxonomy preview could not be loaded.', 'simple-sitemap' ) );
		}

		$show_count = Utility::filter_boolean( $attributes['show_count'] );

		return $this->respond_with_items(
			array_map(
				static function ( $term ) use ( $show_count ) {
					$title = $term->name;
					if ( $show_count ) {
						$title .= ' (' . number_format_i18n( $term->count ) . ')';
					}

					$url = get_term_link( $term );

					return array(
						'id'     => (int) $term->term_id,
						'parent' => (int) $term->parent,
						'title'  => $title,
						'url'    => is_wp_error( $url ) ? '' : (string) $url,
					);
				},
				$terms
			),
			esc_html__( 'No taxonomy terms found.', 'simple-sitemap' )
		);
	}

	/**
	 * Return a bounded flat navigation-menu tree.
	 *
	 * @param \WP_REST_Request $request REST request.
	 * @return \WP_REST_Response
	 */
	public function get_navigation_menu_items( $request ) {
		$attributes = wp_parse_args( $this->get_attributes( $request ), Attribute_Schema::NAVIGATION_MENU_BLOCK_DEFAULTS );
		$menu       = wp_get_nav_menu_object( sanitize_text_field( (string) $attributes['menu'] ) );
		if ( ! $menu ) {
			return $this->respond_with_items( array(), esc_html__( 'Select a navigation menu in the block settings.', 'simple-sitemap' ) );
		}

		$include = Shortcode_Utility::parse_id_list( $attributes['include_menu_ids'] );
		$exclude = Shortcode_Utility::parse_id_list( $attributes['exclude_menu_ids'] );
		$items   = wp_get_nav_menu_items( $menu->term_id );
		$items   = is_array( $items ) ? $items : array();
		$items   = array_values(
			array_filter(
				$items,
				static function ( $item ) use ( $include, $exclude ) {
					$id = (int) $item->ID;
					return ( empty( $include ) || in_array( $id, $include, true ) ) && ! in_array( $id, $exclude, true );
				}
			)
		);

		return $this->respond_with_items(
			array_map(
				static function ( $item ) {
					return array(
						'id'     => (int) $item->ID,
						'parent' => (int) $item->menu_item_parent,
						'title'  => $item->title,
						'url'    => $item->url,
					);
				},
				$items
			),
			esc_html__( 'No navigation menu items found.', 'simple-sitemap' )
		);
	}

	/**
	 * Read the route's attributes object defensively.
	 *
	 * @param \WP_REST_Request $request REST request.
	 * @return array<string, mixed>
	 */
	private function get_attributes( $request ) {
		$attributes = $request->get_param( 'attributes' );

		return is_array( $attributes ) ? $attributes : array();
	}

	/**
	 * Normalize the shared structured-preview response.
	 *
	 * @param array<int, array<string, mixed>> $items Preview items.
	 * @param string                           $empty_message Empty-state copy.
	 * @return \WP_REST_Response
	 */
	private function respond_with_items( $items, $empty_message ) {
		$truncated = count( $items ) > self::PREVIEW_LIMIT;
		if ( $truncated ) {
			$items = array_slice( $items, 0, self::PREVIEW_LIMIT );
		}

		return rest_ensure_response(
			array(
				'items'         => array_values( $items ),
				'empty_message' => $empty_message,
				'truncated'     => $truncated,
			)
		);
	}
}
