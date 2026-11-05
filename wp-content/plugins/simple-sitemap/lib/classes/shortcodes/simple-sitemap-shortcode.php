<?php
/**
 * Class for the [simple-sitemap] shortcode and block.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Class definition.
 */
class Simple_Sitemap_Shortcode {

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

		add_shortcode( 'simple-sitemap', array( &$this, 'render_shortcode' ) );
		add_shortcode( 'ss', array( &$this, 'render_shortcode' ) );
	}

	/**
	 * Create plugin instance.
	 *
	 * @param array $module_roots Root plugin path/dir.
	 * @return self Class instance.
	 */
	public static function create_instance( $module_roots ) {
		if ( ! self::$instance ) {
			self::$instance = new Simple_Sitemap_Shortcode( $module_roots );
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
			throw new \RuntimeException( 'Content Sitemap shortcode has not been initialized.' );
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
		// Manually set this to true as we're rendering a block.
		$attributes['gutenberg_block'] = true;
		return $this->render( $attributes );
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
		return $this->render( $attributes );
	}

	/**
	 * Render sitemap.
	 *
	 * @param array $attributes Sitemap attributes.
	 * @return string           Sitemap output.
	 */
	public function render( $attributes ) {

		$block_err = '';

		// If $attributes are coming from a shortcode parse here.
		if ( ! ( isset( $attributes['gutenberg_block'] ) && ( true === $attributes['gutenberg_block'] ) ) ) {

			// Attributes come from the shortcode.
			$args = shortcode_atts(
				Attribute_Schema::content_shortcode_defaults(),
				$attributes,
				'simple-sitemap'
			);

			// The 'types' shortcode attribute could be empty if the shortcode is [simple-sitemap types=""].
			if ( empty( $args['types'] ) ) {
				$block_err = '<div>Use the \'types\' shortcode attribute to select one or more post types.</div>';
			}

			$args['attr_source'] = 'shortcode';

			// Manually enqueue styles if using the sitemap shortcode.
			wp_enqueue_style( 'simple-sitemap-css' );
		} else {
			// Attributes come from the block.
			$args                = $attributes;
			$args['attr_source'] = 'block';

			// Set up block types coming from block.
			$args['types'] = '';
			$block_cpts    = isset( $args['block_post_types'] ) && is_string( $args['block_post_types'] ) ? json_decode( $args['block_post_types'] ) : array();
			if ( empty( $block_cpts ) ) {
				$block_err = '<div>Select one or more post types for the sitemap block via the \'General Settings\' panel.</div>';
			} else {
				foreach ( $block_cpts as $cpt ) {
					if ( is_object( $cpt ) && isset( $cpt->value ) ) {
						$args['types'] .= sanitize_key( $cpt->value ) . ', ';
					}
				}
			}

			// Enable tabs depending on block settings.
			if ( true === $args['render_tab'] ) {
				$args['render'] = 'tab';
			} else {
				$args['render'] = '';
			}

			$args = Shortcode_Utility::format_booleans( $args );
		}

		if ( Sitemap_Pagination::is_enabled( $args ) ) {
			$args['_pagination_key'] = Sitemap_Pagination::create_instance_key( (string) $args['id'], $args );
		}

		// Format attributes as necessary.
		if ( $args['id'] === '' ) {
			$args['id'] = uniqid(); // Helps avoid conflicts if using multiple sitemaps on the same page. e.g. 5d026c6168954.
		}

		// Internal only.
		$args['shortcode_type'] = 'normal';

		// Sanitize text.
		$args['id']    = Utility::sanitize_identifier( $args['id'], uniqid() );
		$args['types'] = sanitize_text_field( $args['types'] );

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

		// Validate numeric values.
		$args['page_depth'] = intval( $args['page_depth'] );

		$container_format_class = apply_filters( '_simple_sitemap_list_icon', '', $args );
		$render_class           = empty( $args['render'] ) ? ' tab-disabled' : ' tab-enabled';

		// ******************
		// ** OUTPUT START **
		// ******************

		if ( $block_err ) {
			return $block_err;
		}

		$sitemap = '';

		// Output styles.
		$container_css_id          = '#simple-sitemap-container-' . $args['id'];
		$container_css_class       = '.simple-sitemap-container-' . $args['id']; // Applies styles to tabbed AND normal sitemap.
		$container_tab             = $container_css_class . '.tab-enabled'; // Applies styles ONLY to tabbed sitemap.
		$sitemap_styles            = apply_filters( '_simple_sitemap_styles', '', $args, $container_css_id, $container_css_class );
		$filtered_tab_color        = sanitize_hex_color( apply_filters( '_simple_sitemap_tab_color', '#ffffff', $args ) );
		$filtered_tab_header_bg    = sanitize_hex_color( apply_filters( '_simple_sitemap_tab_header_bg', '#de5737', $args ) );
		$tab_color                 = ! empty( $filtered_tab_color ) ? $filtered_tab_color : '#ffffff';
		$tab_header_bg             = ! empty( $filtered_tab_header_bg ) ? $filtered_tab_header_bg : '#de5737';
		$post_type_label_padding   = apply_filters( '_simple_sitemap_post_type_label_pd', '10px 20px', $args );
		$post_type_label_font_size = apply_filters( '_simple_sitemap_post_type_label_fs', '', $args );

		$sitemap_tab_styles = '';
		if ( 'tab' === $args['render'] ) {
			wp_enqueue_script( 'simple-sitemap-tabs' );
			$sitemap_tab_styles .= $container_tab . ' .panel { border-top: 4px solid ' . $tab_header_bg . '; } ';
			$sitemap_tab_styles .= $container_tab . ' input:checked + label { background-color: ' . $tab_header_bg . '; } ';
			$sitemap_tab_styles .= $container_tab . ' input:checked + label > * { color: ' . $tab_color . '; } ';
		}

		$post_types      = array_map( 'trim', explode( ',', $args['types'] ) ); // Convert comma separated string to array.
		$post_type_count = count( $post_types );
		if ( 'tab' === $args['render'] && $post_type_count > 10 ) {
			for ( $tab_index = 11; $tab_index <= $post_type_count; ++$tab_index ) {
				$tab_id              = 'simple-sitemap-tab-' . $tab_index . '-' . $args['id'];
				$sitemap_tab_styles .= $container_tab . ' input#' . $tab_id . ':checked ~ .simple-sitemap-content .simple-sitemap-tab-' . $tab_index . ' { display: block; } ';
			}
		}

		if ( ! empty( $sitemap_tab_styles ) || ! empty( $sitemap_styles ) ) {
			$sitemap .= '<style type="text/css">';
			$sitemap .= wp_kses( $sitemap_tab_styles . $sitemap_styles, array() );
			$sitemap .= '</style>';
		}

		$registered_post_types = get_post_types();

		$sitemap_unique_id = 'simple-sitemap-container-' . $args['id'];
		$container_classes = 'simple-sitemap-container simple-sitemap-spacing-root ' . $sitemap_unique_id . $render_class . $container_format_class;
		$container_classes = apply_filters( '_simple_sitemap_container_classes', $container_classes, $args );

		$sitemap       .= '<div id="' . esc_attr( $sitemap_unique_id ) . '" class="' . esc_attr( $container_classes ) . '"' . Sitemap_Styles::style_attribute( $args ) . '>';
		$before_content = apply_filters( '_simple_sitemap_before_content', '', $args, $sitemap_unique_id );
		$sitemap       .= is_string( $before_content ) ? wp_kses( $before_content, self::before_content_allowed_html() ) : '';

		// Conditionally output tab headers.
		if ( 'tab' === $args['render'] ) :

			// Create tab headers.
			$header_tab_index = 1; // initialize to 1.
			foreach ( $post_types as $post_type ) {

				if ( ! array_key_exists( $post_type, $registered_post_types ) ) {
					break; // Bail if post type isn't valid.
				}

				$checked         = 1 === $header_tab_index ? 'checked' : '';
				$post_type_label = Shortcode_Utility::get_post_type_label( $args, $post_type, $post_type_label_font_size );

				$post_type_label_styles = Shortcode_Utility::get_post_type_label_styles( $post_type_label_padding );

				$tab_id   = 'simple-sitemap-tab-' . $header_tab_index . '-' . $args['id'];
				$label_id = 'simple-sitemap-tab-label-' . $header_tab_index . '-' . $args['id'];
				$panel_id = 'simple-sitemap-panel-' . $header_tab_index . '-' . $args['id'];

				$sitemap .= '<input class="simple-sitemap-tab-control" type="radio" name="tab-' . esc_attr( $args['id'] ) . '" id="' . esc_attr( $tab_id ) . '" aria-controls="' . esc_attr( $panel_id ) . '" ' . esc_attr( $checked ) . '>
				<label id="' . esc_attr( $label_id ) . '"' . esc_attr( $post_type_label_styles ) . ' for="' . esc_attr( $tab_id ) . '">' . wp_kses_post( $post_type_label ) . '</label>';

				++$header_tab_index;
			}

		endif;

		// Tab panel wrapper - open.
		if ( 'tab' === $args['render'] ) {
			$sitemap .= '<div class="simple-sitemap-content">'; }

		// Conditionally create tab panels.
		$header_tab_index = 1; // Reset to 1.
		foreach ( $post_types as $post_type ) :

			if ( ! array_key_exists( $post_type, $registered_post_types ) ) {
				break; // bail if post type isn't valid.
			}

			// Set opening and closing title tag.
			if ( ! empty( $args['title_tag'] ) ) {
				$args['title_open']  = '<' . $args['title_tag'] . '>';
				$args['title_close'] = '</' . $args['title_tag'] . '>';
			} else {
				$args['title_open']  = '';
				$args['title_close'] = '';
			}

			$post_type_label = Shortcode_Utility::get_post_type_label( $args, $post_type, $post_type_label_font_size );

			// Tab panel wrapper - open.
			if ( 'tab' === $args['render'] ) {
				$list_item_wrapper_class = 'simple-sitemap-wrap simple-sitemap-tab-' . $header_tab_index . ' panel';
				$panel_id                = 'simple-sitemap-panel-' . $header_tab_index . '-' . $args['id'];
				$label_id                = 'simple-sitemap-tab-label-' . $header_tab_index . '-' . $args['id'];
			} else {
				$list_item_wrapper_class = 'simple-sitemap-wrap';
				$panel_id                = '';
				$label_id                = '';
			}

			++$header_tab_index;
			if ( '' !== $panel_id ) {
				$sitemap .= '<div id="' . esc_attr( $panel_id ) . '" role="tabpanel" aria-labelledby="' . esc_attr( $label_id ) . '" class="' . esc_attr( $list_item_wrapper_class ) . '">';
			} else {
				$sitemap .= '<div class="' . esc_attr( $list_item_wrapper_class ) . '">';
			}
			if ( 'tab' !== $args['render'] ) {
				$sitemap .= wp_kses_post( $post_type_label );
			}

			$section_args = self::get_section_args( $args, $post_type );
			$query_args   = Sitemap_Query::build_args( $section_args, $post_type );
			$sitemap     .= wp_kses_post( Shortcode_Utility::get_list_items_html( $section_args, $post_type, $query_args ) );
			$sitemap     .= '</div>';

		endforeach;

		// Tab panel wrapper - close.
		if ( 'tab' === $args['render'] ) {
			$sitemap .= '</div>';
		} // .simple-sitemap-content

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
	 * Apply bounded per-section overrides supplied by the licensed block UI.
	 *
	 * Empty settings return the original values, preserving legacy rendering.
	 *
	 * @param array<string, mixed> $args Normalized sitemap arguments.
	 * @param string               $post_type Current section post type.
	 * @return array<string, mixed>
	 */
	public static function get_section_args( $args, $post_type ) {
		$settings = isset( $args['section_settings'] ) && is_array( $args['section_settings'] ) ? $args['section_settings'] : array();
		$section  = isset( $settings[ $post_type ] ) && is_array( $settings[ $post_type ] ) ? $settings[ $post_type ] : array();
		if ( empty( $section ) ) {
			return $args;
		}

		$allowed_orderby = array( 'title', 'date', 'ID', 'author', 'name', 'modified', 'menu_order', 'comment_count' );
		if ( isset( $section['orderby'] ) && in_array( $section['orderby'], $allowed_orderby, true ) ) {
			$args['orderby'] = $section['orderby'];
		}
		if ( isset( $section['order'] ) ) {
			$args['order'] = 'desc' === strtolower( (string) $section['order'] ) ? 'desc' : 'asc';
		}
		foreach ( array( 'include', 'exclude' ) as $id_key ) {
			if ( isset( $section[ $id_key ] ) ) {
				$args[ $id_key ] = implode( ',', Shortcode_Utility::parse_id_list( $section[ $id_key ] ) );
			}
		}
		if ( isset( $section['num_posts'] ) && is_numeric( $section['num_posts'] ) ) {
			$num_posts         = (int) $section['num_posts'];
			$args['num_posts'] = -1 === $num_posts ? -1 : min( 200, max( 1, $num_posts ) );
		}

		$filtered = apply_filters( 'simple_sitemap_section_args', $args, $post_type, $section );

		return is_array( $filtered ) ? $filtered : $args;
	}
}
