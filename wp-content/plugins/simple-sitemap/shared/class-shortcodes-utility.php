<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Shortcodes utility class
 */
class Shortcode_Utility {

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
	}

	/**
	 * Create class instance.
	 *
	 * @param array $module_roots Root plugin path/dir.
	 * @return self Class instance.
	 */
	public static function create_instance( $module_roots ) {
		if ( ! self::$instance ) {
			self::$instance = new Shortcode_Utility( $module_roots );
		}
		return self::$instance;
	}

	/**
	 * Get class instance.
	 *
	 * @return self Class instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			throw new \RuntimeException( 'Shortcode utility has not been initialized.' );
		}
		return self::$instance;
	}

	/**
	 * Create class instance.
	 *
	 * @param array  $args Sitemap arguments.
	 * @param string $post_type Post type.
	 * @param string $post_type_label_font_size Post type label font size.
	 * @return string post type label.
	 */
	public static function get_post_type_label( $args, $post_type, $post_type_label_font_size ) {

		$show_label                = $args['show_label'];
		$post_type_tag             = $args['post_type_tag'];
		$post_type_label_styles    = '';
		$post_type_label_font_size = Utility::sanitize_css_value( 'font-size', $post_type_label_font_size );
		if ( ! empty( $post_type_label_font_size ) ) {
			$post_type_label_styles .= 'font-size:' . $post_type_label_font_size . ';';
		}
		if ( ! empty( $post_type_label_styles ) ) {
			$post_type_label_styles = ' style="' . $post_type_label_styles . '"';
		}

		$label = '';
		// Conditionally show label for each post type.
		if ( 'true' === $show_label ) {
			$post_type_obj  = get_post_type_object( $post_type );
			$post_type_name = isset( $args['post_type_label'] ) && isset( $args['post_type_label'][ $post_type ] ) ? $args['post_type_label'][ $post_type ] : $post_type_obj->labels->name;
			$label          = '<' . $post_type_tag . ' class="post-type"' . $post_type_label_styles . '>' . $post_type_name . '</' . $post_type_tag . '>';
		}

		return $label;
	}

	/**
	 * Get post type label styles.
	 *
	 * @param string $post_type_label_padding Post type label padding.
	 * @return string post type label styles.
	 */
	public static function get_post_type_label_styles( $post_type_label_padding ) {

		$post_type_label_styles  = '';
		$post_type_label_padding = Utility::sanitize_css_value( 'padding', $post_type_label_padding );
		if ( ! empty( $post_type_label_padding ) ) {
			$post_type_label_styles .= 'padding:' . $post_type_label_padding . ';';
		}
		if ( ! empty( $post_type_label_styles ) ) {
			$post_type_label_styles = ' style="' . $post_type_label_styles . '"';
		}

		return $post_type_label_styles;
	}

	/**
	 * Get query args.
	 *
	 * @param array  $args Query args.
	 * @param string $post_type Post type.
	 * @return array
	 */
	public static function get_query_args( $args, $post_type ) {
		return Sitemap_Query::build_args( $args, $post_type );
	}

	/**
	 * Normalize a comma-separated list of post IDs.
	 *
	 * @param mixed $value Raw shortcode or block value.
	 * @return array<int>
	 */
	public static function parse_id_list( $value ) {
		if ( ! is_string( $value ) && ! is_numeric( $value ) ) {
			return array();
		}

		$ids = array_map( 'absint', explode( ',', (string) $value ) );
		$ids = array_values( array_unique( array_filter( $ids ) ) );

		return $ids;
	}

	/**
	 * Render the sitemap list items.
	 *
	 * @param array $args Shortcode/block args.
	 * @param string $post_type Post type.
	 * @param array $query_args Query args used to retrieve posts.
	 */
	public static function render_list_items( $args, $post_type, $query_args ) {
		echo wp_kses_post( self::get_list_items_html( $args, $post_type, $query_args ) );
		echo '</div>';
	}

	/**
	 * Build the sitemap list contents without owning the caller's container.
	 *
	 * This is the value-returning renderer used by current plugin code. The
	 * legacy render_list_items() wrapper remains available for integrations that
	 * rely on its historic echo-and-close behavior.
	 *
	 * @param array  $args Shortcode/block args.
	 * @param string $post_type Post type.
	 * @param array  $query_args Query args used to retrieve posts.
	 * @return string
	 */
	public static function get_list_items_html( $args, $post_type, $query_args ) {
		$cache_ttl = 0;
		if ( ! Sitemap_Pagination::is_enabled( $args ) ) {
			$cache_ttl = absint( apply_filters( '_simple_sitemap_cache_ttl', 0, $args, $post_type, $query_args ) );
		}
		$cache_key = '';
		if ( $cache_ttl > 0 ) {
			$cache_key = Sitemap_Cache::build_key(
				'list',
				array(
					'args'       => $args,
					'post_type'  => $post_type,
					'query_args' => $query_args,
					'locale'     => get_locale(),
					'user'       => get_current_user_id(),
				),
				Sitemap_Cache::get_version()
			);
			$cached    = Sitemap_Cache::get( $cache_key );
			if ( null !== $cached ) {
				return $cached;
			}
		}

		$query = Sitemap_Query::execute( $query_args );
		$html  = self::get_query_items_html( $args, $post_type, $query );
		$html .= Sitemap_Pagination::render( $query, $args, $post_type );
		if ( $cache_ttl > 0 ) {
			Sitemap_Cache::set( $cache_key, $html, $cache_ttl );
		}

		return $html;
	}

	/**
	 * Render the results of an already-executed query.
	 *
	 * @param array     $args Shortcode/block args.
	 * @param string    $post_type Post type.
	 * @param \WP_Query $sitemap_query Executed sitemap query.
	 * @return string
	 */
	public static function get_query_items_html( $args, $post_type, $sitemap_query ) {

		$html = '';

		if ( $sitemap_query->have_posts() ) :

			if ( 'page' === $post_type && ! Sitemap_Pagination::is_enabled( $args ) ) :
				$html .= self::get_page_list_html( $sitemap_query->posts, $args );
			else :
				$horizontal_sep_arr = apply_filters(
					'_simple_sitemap_horizontal_separator_v2',
					array(
						'ul_class'       => 'simple-sitemap-' . $post_type . ' main',
						'page_depth'     => $args['page_depth'],
						'horizontal_sep' => '',
					),
					$args
				);

				$horizontal_sep     = $horizontal_sep_arr['horizontal_sep'];
				$ul_class           = $horizontal_sep_arr['ul_class'];
				$args['page_depth'] = $horizontal_sep_arr['page_depth'];

				$html .= '<ul class="' . esc_attr( $ul_class ) . '">';

				// Start of the loop.
				while ( $sitemap_query->have_posts() ) :
					$sitemap_query->the_post();

					// Post visibility.
					if ( Visibility_Policy::should_skip( get_the_ID(), $args ) ) {
						continue;
					}

					// Check if we're on the last post.
					if ( ( $sitemap_query->current_post + 1 ) == $sitemap_query->post_count ) {
						$horizontal_sep = '';
					}

					$image_html     = apply_filters( '_simple_sitemap_image_html', '', null, $args );
					$separator_html = apply_filters( '_simple_sitemap_separator_html', '', $args );

					$title_text = (string) get_the_title();

					$title_text = Hooks::simple_sitemap_title_text( $title_text, (int) get_the_ID() );

					$permalink = (string) get_permalink();
					$title     = self::get_the_title( $title_text, $permalink, $args );
					$title     = Hooks::simple_sitemap_title_link_text( $title, (int) get_the_ID() );

					$excerpt = $args['show_excerpt'] == 'true' ? '<' . $args['excerpt_tag'] . ' class="excerpt">' . get_the_excerpt() . '</' . $args['excerpt_tag'] . '>' : '';

					// Render the shared list-item template.
					$html .= Sitemap_Item_Renderer::open_item(
						wp_kses_post( $title ),
						wp_kses_post( $excerpt ),
						wp_kses_post( $image_html ),
						wp_kses_post( $separator_html ),
						wp_kses_post( $horizontal_sep )
					);
					$html .= '</li>';

				endwhile; // end of post loop -->

				$html .= '</ul>';

				// Put pagination functions here.
				wp_reset_postdata();
			endif;

		else :

			$post_type_obj  = get_post_type_object( $post_type );
			$post_type_name = strtolower( $post_type_obj->labels->name );

			$html .= '<p class="no-posts">Sorry, no ' . wp_kses_post( $post_type_name ) . ' found.</p>';

		endif;

		return $html;
	}

	/**
	 * Get the post title.
	 *
	 * @param string $title_text Title text.
	 * @param string $permalink Permalink.
	 * @param array  $args Sitemap args.
	 * @param bool   $parent_page Parent page.
	 * @param string $parent_page_link Parent page link.
	 * @return string
	 */
	public static function get_the_title( $title_text, $permalink, $args, $parent_page = false, $parent_page_link = '1' ) {

		$links       = $args['links'];
		$title_open  = $args['title_open'];
		$title_close = $args['title_close'];
		$new_tab     = isset( $args['target_blank'] ) && true === Utility::filter_boolean( $args['target_blank'] ) ? ' target="_blank"' : '';
		$rel         = apply_filters( '_simple_sitemap_nofollow', '', $args );
		$atts        = $rel . $new_tab;

		if ( ! empty( $title_text ) ) {
			if ( $links == 'true' && $parent_page === false ) {
				$title = $title_open . '<a href="' . esc_url( $permalink ) . '"' . $atts . '>' . $title_text . '</a>' . $title_close;
			} elseif ( $links == 'true' && $parent_page && $parent_page_link != '1' ) {
				$title = $title_open . '<a href="' . esc_url( $permalink ) . '"' . $atts . '>' . $title_text . '</a>' . $title_close;
			} else {
				$title = $title_open . $title_text . $title_close;
			}
		} elseif ( $links == 'true' && $parent_page === false ) {
				$title = $title_open . '<a href="' . esc_url( $permalink ) . '"' . $atts . '>' . '(no title)' . '</a>' . $title_close;
		} elseif ( $links == 'true' && $parent_page && $parent_page_link != '1' ) {
			$title = $title_open . '<a href="' . esc_url( $permalink ) . '"' . $atts . '>' . '(no title)' . '</a>' . $title_close;
		} else {
			$title = $title_open . '(no title)' . $title_close;
		}

		return $title;
	}

	/**
	 * Walk the page tree.
	 *
	 * @param array $pages Pages.
	 * @param int   $depth Depth.
	 * @param array $r Sitemap args.
	 * @return mixed
	 */
	public static function walk_page_tree( $pages, $depth, $r ) {

		$r['_simple_sitemap_options'] = Settings_Repository::get_options();

		$walker           = new WPGO_Walker_Page();
		$walker->ssp_args = $r;

		foreach ( (array) $pages as $page ) {
			if ( $page->post_parent ) {
				$r['pages_with_children'][ $page->post_parent ] = true;
			}
		}

		$args = array( $pages, $depth, $r );
		return call_user_func_array( array( $walker, 'walk' ), $args );
	}

	/**
	 * List pages.
	 *
	 * @param array $pages Pages.
	 * @param array $args Sitemap args.
	 * @return string
	 */
	public static function list_pages( $pages, $args ) {
		return self::get_page_list_html( $pages, $args ) . '</div>';
	}

	/**
	 * Build a hierarchical page list without closing the caller's container.
	 *
	 * @param array $pages Pages.
	 * @param array $args Sitemap args.
	 * @return string
	 */
	public static function get_page_list_html( $pages, $args ) {

		$output = '';

		if ( empty( $pages ) ) {
			return $output;
		}

		$class = apply_filters( '_simple_sitemap_horizontal_separator_v3', 'simple-sitemap-page main', $args );

		$output  = '<ul class="' . $class . '">';
		$output .= self::walk_page_tree( $pages, $args['page_depth'], $args );
		$output .= '</ul>';

		return $output;
	}

	/**
	 * Convert block booleans to string booleans.
	 *
	 * Example: true (bool) => 'true' (string)
	 * Example: false (bool) => 'false' (string)
	 *
	 * @param array $args Sitemap args.
	 * @return array
	 */
	public static function format_booleans( $args ) {

		// Make a copy.
		$tmp = $args;

		foreach ( $tmp as $key => $value ) {
			if ( 'boolean' === gettype( $value ) ) {
				if ( true === $value ) {
					$tmp[ $key ] = 'true';
				}
				if ( false === $value ) {
					$tmp[ $key ] = 'false';
				}
			}
		}

		return $tmp;
	}
}
