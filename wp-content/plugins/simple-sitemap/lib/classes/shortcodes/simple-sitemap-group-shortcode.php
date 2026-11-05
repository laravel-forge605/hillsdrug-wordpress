<?php
/**
 * Class for the [simple-sitemap-group] shortcode and block.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Class definition.
 */
class Simple_Sitemap_Group_Shortcode {

	/**
	 * Store static class instance.
	 *
	 * @var self|null
	 */
	protected static $instance;

	/**
	 * Common root paths/directories.
	 *
	 * @var array<string, string>
	 */
	protected $module_roots;

	/**
	 * Main class constructor.
	 *
	 * @param array $module_roots Root plugin path/dir.
	 */
	public function __construct( $module_roots ) {

		$this->module_roots = $module_roots;

		add_shortcode( 'simple-sitemap-group', array( &$this, 'render_shortcode' ) );
		add_shortcode( 'ssg', array( &$this, 'render_shortcode' ) );
	}

	/**
	 * Create plugin instance.
	 *
	 * @param array $module_roots Root plugin path/dir.
	 * @return self Class instance.
	 */
	public static function create_instance( $module_roots ) {
		if ( ! self::$instance ) {
			self::$instance = new Simple_Sitemap_Group_Shortcode( $module_roots );
		}
		return self::$instance;
	}

	/**
	 * Get plugin instance.
	 *
	 * @return self Class instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			throw new \RuntimeException( 'Grouped Sitemap shortcode has not been initialized.' );
		}
		return self::$instance;
	}

	/**
	 * Render sitemap from an editor block.
	 *
	 * @param array $attributes Blocks attributes.
	 * @return string           Sitemap render.
	 */
	public function render_block( $attributes ) {
		// manually set this to true as we're rendering a block
		$attributes['gutenberg_block'] = true;
		return wp_kses( $this->render( $attributes ), self::output_allowed_html() );
	}

	/**
	 * Render sitemap from an editor shortcode.
	 *
	 * @param array|string $attributes Shortcode attributes.
	 * @return string           Sitemap render.
	 */
	public function render_shortcode( $attributes ) {

		// For a sitemap shortcode set 'gutenberg_block' to false in case it has been set to true manually.
		if ( ! is_array( $attributes ) ) {
			$attributes = array();
		} else {
			$attributes = array_map( 'sanitize_text_field', wp_unslash( $attributes ) );
		}
		$attributes['gutenberg_block'] = false;
		return wp_kses( $this->render( $attributes ), self::output_allowed_html() );
	}

	/**
	 * Render sitemap group.
	 *
	 * @param array $attributes Sitemap attributes.
	 * @return string           Sitemap render.
	 */
	public function render( $attributes ) {

		$render_err = '';

		// If $attributes are coming from a shortcode parse here.
		if ( ! ( isset( $attributes['gutenberg_block'] ) && ( true === $attributes['gutenberg_block'] ) ) ) {

			// Attributes come from the shortcode.
			$args = shortcode_atts(
				Attribute_Schema::group_shortcode_defaults(),
				$attributes,
				'simple-sitemap-group'
			);

			$args['attr_source'] = 'shortcode';

			// Manually enqueue styles if using the sitemap shortcode.
			wp_enqueue_style( 'simple-sitemap-css' );
		} else {

			// Attributes come from the block.
			$args                = $attributes;
			$args['attr_source'] = 'block';

			if ( empty( $args['block_taxonomy'] ) ) {
				$render_err = '<h5 style="line-height:1.25em;">Please select a post type that supports taxonomies.</h5>';
			} else {
				$args['tax'] = $args['block_taxonomy'];
			}

			$post_type_label_font_size = apply_filters( '_simple_sitemap_group_post_type_label_fs', '', $args );

			$args = Shortcode_Utility::format_booleans( $args );
		}

		if ( Sitemap_Pagination::is_enabled( $args ) ) {
			$args['_pagination_key'] = Sitemap_Pagination::create_instance_key( (string) $args['id'], $args );
		}

		// Format attributes as necessary.
		if ( $args['id'] === '' ) {
			$args['id'] = uniqid(); // Helps avoid conflicts if using multiple sitemaps on the same page. e.g. 5d026c6168954.
		}

		// Sanitize text.
		$args['id'] = Utility::sanitize_identifier( $args['id'], uniqid() );

		// Internal only?
		$args['shortcode_type'] = 'group'; // undocumented.

		// Escape tag names.
		$args['container_tag'] = tag_escape( $args['container_tag'] );
		$args['title_tag']     = tag_escape( $args['title_tag'] );
		$args['excerpt_tag']   = tag_escape( $args['excerpt_tag'] );
		$args['post_type_tag'] = tag_escape( $args['post_type_tag'] );

		// Force 'ul' or 'ol' to be used as the container tag.
		$allowed_container_tags = array( 'ul', 'ol' );
		if ( ! in_array( $args['container_tag'], $allowed_container_tags, true ) ) {
			$args['container_tag'] = 'ul';
		}

		$container_format_class = apply_filters( '_simple_sitemap_group_list_icon', '', $args );
		$render_class           = apply_filters( '_simple_sitemap_group_render_class', '', $args );

		$post_type_arr = apply_filters( '_simple_sitemap_group_post_type', array( 'post', $render_err ), $args );
		$term_orderby  = apply_filters( '_simple_sitemap_group_term_orderby', 'name', $args );
		$term_order    = apply_filters( '_simple_sitemap_group_term_order', 'asc', $args );
		$num_terms     = $args['num_terms'];

		$post_type  = $post_type_arr[0];
		$render_err = $post_type_arr[1];

		// ******************
		// ** OUTPUT START **
		// ******************

		if ( $render_err ) {
			return $render_err;
		}

		$sitemap = '';

		// Output styles.
		$container_css_id    = '#simple-sitemap-container-' . $args['id'];
		$container_css_class = '.simple-sitemap-container-' . $args['id']; // Applies styles to group sitemap.
		$sitemap_styles      = apply_filters( '_simple_sitemap_group_styles', '', $args, $container_css_id, $container_css_class );

		$sitemap .= '<style type="text/css">';
		$sitemap .= wp_kses( $sitemap_styles, array() );
		$sitemap .= '</style>';

		$sitemap_unique_id = 'simple-sitemap-container-' . $args['id'];
		$container_classes = 'simple-sitemap-container simple-sitemap-spacing-root ' . $sitemap_unique_id . $render_class . $container_format_class;
		$container_classes = apply_filters( '_simple_sitemap_group_container_classes', $container_classes, $args );

		$sitemap       .= '<div id="' . esc_attr( $sitemap_unique_id ) . '" class="' . esc_attr( $container_classes ) . '"' . Sitemap_Styles::style_attribute( $args ) . '>';
		$before_content = apply_filters( '_simple_sitemap_group_before_content', '', $args, $sitemap_unique_id );
		$sitemap       .= is_string( $before_content ) ? wp_kses( $before_content, self::before_content_allowed_html() ) : '';

		// Set opening and closing title tag.
		if ( ! empty( $args['title_tag'] ) ) {
			$args['title_open']  = '<' . $args['title_tag'] . '>';
			$args['title_close'] = '</' . $args['title_tag'] . '>';
		} else {
			$args['title_open']  = '';
			$args['title_close'] = '';
		}

		$post_type_label = Shortcode_Utility::get_post_type_label( $args, $post_type, '' );

		$list_item_wrapper_class = 'simple-sitemap-wrap' . $render_class;

		$sitemap .= wp_kses_post( $post_type_label );

		$taxonomy_arr = get_object_taxonomies( $post_type );

		// Sort via specified taxonomy.
		if ( ! empty( $args['tax'] ) && in_array( $args['tax'], $taxonomy_arr, true ) ) {

			$term_attr = array(
				'orderby' => $term_orderby,
				'order'   => $term_order,
				'number'  => $num_terms,
			);

			$term_attr['taxonomy'] = $args['tax'];
			$terms                 = get_terms( $term_attr );
			if ( is_wp_error( $terms ) ) {
				$sitemap .= '<p class="no-posts">' . esc_html__( 'Unable to load sitemap terms.', 'simple-sitemap' ) . '</p>';

				$terms = array();
			}
			$batched_queries = Grouped_Query::execute( $args, $post_type, $terms );
			foreach ( $terms as $term ) {

				if ( apply_filters( '_simple_sitemap_group_include_exclude_terms', false, strtolower( $term->slug ), $args ) ) {
					continue;
				}

				$sitemap .= '<div class="' . esc_attr( $list_item_wrapper_class ) . ' ' . esc_attr( strtolower( $term->slug ) ) . '">';

				$args['tax_query'] = array(
					array(
						'taxonomy' => $args['tax'],
						'field'    => 'slug',
						'terms'    => $term->slug,
					),
				);
				if ( Sitemap_Pagination::is_enabled( $args ) ) {
					$args['_pagination_scope'] = 'term-' . $term->term_id;
					$args['_pagination_label'] = $term->name;
				}

				$term_html = '<h3 class="term-tag">' . $term->name . '</h3>';
				$term_html = apply_filters( '_simple_sitemap_group_tax_links', $term_html, $term->name, $term->slug, $args );

				$sitemap .= wp_kses_post( $term_html );

				if ( is_array( $batched_queries ) && isset( $batched_queries[ $term->slug ] ) ) {
					$sitemap .= wp_kses_post( Shortcode_Utility::get_query_items_html( $args, $post_type, $batched_queries[ $term->slug ] ) );
				} else {
					$query_args = Sitemap_Query::build_args( $args, $post_type );
					$sitemap   .= wp_kses_post( Shortcode_Utility::get_list_items_html( $args, $post_type, $query_args ) );
				}
				$sitemap .= '</div>';
			}
		} else {
			$sitemap .= 'No posts found.';
		}

		$sitemap .= '</div>'; // .simple-sitemap-container

		// Retained for front-end layout compatibility with existing themes.
		$sitemap .= '<br style="clear: both;">';

		// ****************
		// ** OUTPUT END **
		// ****************

		return $sitemap;
	}

	/**
	 * Allowed markup for progressively enhanced controls before sitemap content.
	 *
	 * @return array<string, array<string, bool>>
	 */
	private static function before_content_allowed_html() {
		return array(
			'div'    => array(
				'class'  => true,
				'hidden' => true,
			),
			'label'  => array( 'for' => true ),
			'input'  => array(
				'id'               => true,
				'class'            => true,
				'type'             => true,
				'autocomplete'     => true,
				'aria-controls'    => true,
				'aria-describedby' => true,
				'placeholder'      => true,
				'data-no-results'  => true,
			),
			'button' => array(
				'class'  => true,
				'type'   => true,
				'hidden' => true,
			),
			'p'      => array(
				'id'        => true,
				'class'     => true,
				'aria-live' => true,
			),
		);
	}

	/**
	 * Retain the established post markup plus the progressive search field.
	 *
	 * @return array<string, array<string, bool>>
	 */
	private static function output_allowed_html() {
		$allowed          = wp_kses_allowed_html( 'post' );
		$allowed['input'] = self::before_content_allowed_html()['input'];

		return $allowed;
	}
}
