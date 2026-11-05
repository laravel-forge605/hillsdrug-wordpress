<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Register custom REST API endpoints
 */
class Custom_Sitemap_Endpoints {

	/**
	 * Common root paths/directories.
	 *
	 * @var array<string, string>
	 */
	protected $module_roots;

	private $rest_api_namespace;

	/**
	 * Main class constructor.
	 *
	 * @param array $module_roots Root plugin path/dir.
	 */
	public function __construct( $module_roots ) {

		$this->module_roots       = $module_roots;
		$this->rest_api_namespace = 'simple-sitemap/v1';

		add_action( 'rest_api_init', array( &$this, 'register_endpoints' ) );
	}

	/**
	 * Register REST API
	 */
	public function register_endpoints() {

		// Get public CPT.
		register_rest_route(
			$this->rest_api_namespace,
			'/post-types',
			array(
				'methods'             => 'GET',
				'callback'            => array( &$this, 'get_post_types' ),
				'permission_callback' => array( &$this, 'check_post_permissions' ),
			)
		);

		// Get registered taxonomies for specified post type.
		register_rest_route(
			$this->rest_api_namespace,
			'/post-type-taxonomies/(?P<type>[a-zA-Z0-9-_]+)', // allowed chars [a-z] [A-Z] [0-9] [-_].
			array(
				'methods'             => 'GET',
				'callback'            => array( &$this, 'get_post_type_taxonomies' ),
				'permission_callback' => array( &$this, 'check_post_permissions' ),
				'args'                => array(
					'type' => array(
						'sanitize_callback' => 'sanitize_key',
						'validate_callback' => array( $this, 'validate_post_type' ),
					),
				),
			)
		);

		register_rest_route(
			$this->rest_api_namespace,
			'/taxonomies',
			array(
				'methods'             => 'GET',
				'callback'            => array( &$this, 'get_taxonomies' ),
				'permission_callback' => array( &$this, 'check_post_permissions' ),
			)
		);

		register_rest_route(
			$this->rest_api_namespace,
			'/navigation-menus',
			array(
				'methods'             => 'GET',
				'callback'            => array( &$this, 'get_navigation_menus' ),
				'permission_callback' => array( &$this, 'check_post_permissions' ),
			)
		);

		register_rest_route(
			$this->rest_api_namespace,
			'/hierarchical-post-types',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_hierarchical_post_types' ),
				'permission_callback' => array( $this, 'check_post_permissions' ),
			)
		);
	}

	/**
	 * Get public post types.
	 *
	 * @return array
	 */
	public function get_post_types() {

		$post_type_args        = array(
			'public' => true,
		);
		$registered_post_types = get_post_types( $post_type_args );

		// Remove 'attachment' (media) from list of post types.
		if ( in_array( 'attachment', $registered_post_types, true ) ) {
			unset( $registered_post_types['attachment'] );
		}

		$sitemap_post_types = array();
		foreach ( $registered_post_types as $key => $value ) {
			$post_type = get_post_type_object( $key );
			if ( $post_type ) {
				$sitemap_post_types[ $key ] = $post_type->label;
			}
		}

		return $sitemap_post_types;
	}

	/**
	 * Get taxonomies for specific post type.
	 *
	 * @param \WP_REST_Request $request Request object passed in from the REST endpoint.
	 * @return array
	 */
	public function get_post_type_taxonomies( $request ) {

		$post_type            = $request->get_param( 'type' );
		$post_type_taxonomies = get_object_taxonomies( $post_type );

		// If empty array no taxonomies return empty.
		if ( empty( $post_type_taxonomies ) ) {
			return array();
		}

		// Remove 'post_format' from list of taxonomies.
		$key = array_search( 'post_format', $post_type_taxonomies, true );
		if ( false !== $key ) {
			unset( $post_type_taxonomies[ $key ] );
		}

		// Format into array.
		$taxonomies = array();
		foreach ( $post_type_taxonomies as $post_type_taxonomy ) {
			$tax = get_taxonomy( $post_type_taxonomy );
			if ( $tax ) {
				$taxonomies[ $tax->name ] = $tax->label;
			}
		}

		return $taxonomies;
	}

	/**
	 * Get all public taxonomy names available to the Pro Terms block.
	 *
	 * This complements the core endpoint for public taxonomies that deliberately
	 * do not opt into the REST API themselves.
	 *
	 * @return array<string, string>
	 */
	public function get_taxonomies() {
		$registered = get_taxonomies( array( 'public' => true ), 'objects' );
		$taxonomies = array();
		foreach ( $registered as $taxonomy ) {
			if ( 'post_format' !== $taxonomy->name ) {
				$taxonomies[ $taxonomy->name ] = $taxonomy->label;
			}
		}
		asort( $taxonomies, SORT_NATURAL | SORT_FLAG_CASE );

		return $taxonomies;
	}

	/**
	 * Get saved navigation menus available to the Pro Menu block.
	 *
	 * @return array<int|string, string>
	 */
	public function get_navigation_menus() {
		$menus   = wp_get_nav_menus( array( 'orderby' => 'name' ) );
		$options = array();
		foreach ( $menus as $menu ) {
			$options[ (string) $menu->term_id ] = $menu->name;
		}

		return $options;
	}

	/**
	 * Get public hierarchical content types for the licensed hierarchy control.
	 *
	 * @return array<string, string>
	 */
	public function get_hierarchical_post_types() {
		$post_types = get_post_types(
			array(
				'public'       => true,
				'hierarchical' => true,
			),
			'objects'
		);
		$options    = array();
		foreach ( $post_types as $post_type ) {
			if ( 'attachment' !== $post_type->name && is_post_type_viewable( $post_type ) ) {
				$options[ $post_type->name ] = $post_type->label;
			}
		}
		asort( $options, SORT_NATURAL | SORT_FLAG_CASE );

		return $options;
	}

	/**
	 * Check post permissions.
	 *
	 * @return \WP_Error|true
	 */
	public function check_post_permissions() {

		// Restrict endpoint to only users who have the edit_posts capability.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'No permissions to view post data.', 'simple-sitemap' ), array( 'status' => 403 ) );
		}

		return true;
	}

	/**
	 * Validate the legacy post-type route argument.
	 *
	 * @param string $post_type Post type name.
	 * @return bool
	 */
	public function validate_post_type( $post_type ) {
		return post_type_exists( $post_type );
	}
} /* End class definition. */
