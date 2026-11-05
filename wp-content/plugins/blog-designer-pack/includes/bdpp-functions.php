<?php
/**
 * Plugin generic functions file
 *
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 * 
 * @since 1.0
 */
function bdp_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'bdp_clean', $var );
	} else {
		$data = is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
		return wp_unslash($data);
	}
}

/**
 * Sanitize number value and return fallback value if it is blank
 * 
 * @since 1.0
 */
function bdp_clean_number( $var, $fallback = null, $type = 'int' ) {

	$var = trim( $var );

	if( $type == 'int' ) {
		$data = absint( $var );
	} elseif ( $type == 'number' ) {
		$data = intval( $var );
	} else {
		$data = abs( $var );
	}

	return ( empty($data) && isset($fallback) ) ? $fallback : $data;
}

/**
 * Sanitize url
 * 
 * @since 1.0
 */
function bdp_clean_url( $url ) {
	return esc_url_raw( trim($url) );
}

/**
 * Sanitize multiple HTML classes
 * 
 * @since 1.0
 */
function bdp_sanitize_html_classes( $classes, $sep = " " ) {
	$return = "";

	if( ! is_array($classes) ) {
		$classes = explode($sep, $classes);
	}

	if( ! empty($classes) ) {
		foreach($classes as $class) {
			$return .= sanitize_html_class($class) . " ";
		}
		$return = trim( $return );
	}

	return $return;
}

/**
 * Function to unique number value
 * 
 * @since 1.0
 */
function bdp_get_unique() {
	static $unique = 0;
	$unique++;

	// For VC front end editing
	if ( ( function_exists('vc_is_page_editable') && vc_is_page_editable() )
		 || ( defined('ELEMENTOR_PLUGIN_BASE') && isset( $_POST['action'] ) && $_POST['action'] == 'elementor_ajax' && isset($_POST['editor_post_id']) )
		)
	{
		return rand() .'-'. current_time( 'timestamp' );
	}

	return $unique;
}

/**
 * Converts a string (e.g. 'yes' or 'no') to a bool.
 *
 * @since 1.5
 * @param string|bool $string String to convert. If a bool is passed it will be returned as-is.
 * @return bool
 */
function bdp_string_to_bool( $string ) {
	$string = $string ? trim( $string ) : '';
	return is_bool( $string ) ? $string : ( 'yes' === strtolower( $string ) || 1 === $string || 'true' === strtolower( $string ) || '1' === $string );
}

/**
 * Explode the data.
 * 
 * @since 1.5
 */
function bdp_maybe_explode( $data, $separator = ',' ) {

	if( is_array( $data ) ) {
		return $data;
	}

	$data = trim( $data );
	if( '' == $data ) {
		return array();
	}

	return explode( $separator, $data );
}

/**
 * Convert shortcode arguments in to json and remove some unnecessary.
 * 
 * @since 1.5
 */
function bdp_shortcode_conf( $template_args ) {

	$template_args			= (array) $template_args;
	$unset_template_args	= array( 'format', 'feat_img', 'post_link', 'cate_name', 'tags', 'wrp_cls' );

	foreach( $unset_template_args as $unset_template_arg ) {
		if( isset( $template_args[ $unset_template_arg ] ) ) {
			unset( $template_args[ $unset_template_arg ] );
		}
	}

	return json_encode( $template_args );
}

/**
 * Function to validate that public script should be enqueue at last.
 * Call this function at last.
 * 
 * @since 1.0
 */
function bdp_enqueue_script() {

	// Check public script is in queue
	if( wp_script_is( 'bdpp-public-script', 'enqueued' ) ) {
		
		// Dequeue Script
		wp_dequeue_script( 'bdpp-public-script' );

		// Enqueue Script
		wp_enqueue_script( 'bdpp-public-script' );
	}
}

/**
 * Function to get allowed post types from setting.
 * 
 * @since 1.4.2
 */
function bdp_allowed_post_types() {
	return bdp_get_option( 'post_types', array() );
}

/**
 * Function to get post excerpt
 * Custom function so some theme filter will not affect it.
 * 
 * @since 4.0
 */
function bdp_post_excerpt( $post = null ) {

	$post = get_post( $post );
	if ( empty( $post ) ) {
		return '';
	}
 
	if ( post_password_required( $post ) ) {
		return __( 'There is no excerpt because this is a protected post.', 'blog-designer-pack' );
	}

	return apply_filters( 'bdpp_post_excerpt', $post->post_excerpt, $post );
}

/**
 * Function to get post short content either via excerpt or content.
 * 
 * @since 4.0
 */
function bdp_get_post_excerpt( $post_id = null, $content = '', $word_length = 55, $more = '...' ) {

	$word_length		= ! empty( $word_length ) ? $word_length : 55;
	$post_content_fix	= bdp_get_option('post_content_fix');

	// If post id is passed
	if( ! empty( $post_id ) ) {
		if( has_excerpt( $post_id ) ) {
		  $content = bdp_post_excerpt( $post_id );
		} else {
		  $content = ! empty( $content ) ? $content : get_the_content( null, false, $post_id );
		}
	}

	// Storing original content
	$orig_content = $content;

	/***** Divi Theme Tweak Starts *****/
	if( function_exists('et_strip_shortcodes') ) {
		$content = et_strip_shortcodes( $content );
	}
	if( function_exists('et_builder_strip_dynamic_content') ) {
		$content = et_builder_strip_dynamic_content( $content );
	}

	/***** Avada Theme Tweak Starts *****/
	if( function_exists('fusion_extract_shortcode_contents') ) {
		$pattern = get_shortcode_regex();
		$content = preg_replace_callback( "/$pattern/s", 'fusion_extract_shortcode_contents', $content );
	}

	/* General tweak strip shortcodes and keep the content */
	if( $post_content_fix ) {
		$content = preg_replace( '~(?:\[/?)[^/\]]+/?\]~s', '', $content );
		$content = str_replace( ']]>', ']]&gt;', $content );
		$content = str_replace( [ '"', "'" ], [ '&quot;', '&#39;' ], $content );
	}

	if( $content ) {
		$content = strip_shortcodes( $content ); // Strip shortcodes
		$content = wp_trim_words( $content, $word_length, $more );
	}

	return apply_filters( 'bdpp_post_content', $content, $orig_content, $post_id, $word_length, $more );
}

/**
 * Function to get post featured image
 * 
 * @since 1.0
 */
function bdp_get_post_feat_image( $post_id = null, $size = 'large', $default_img = true ) {

	$size   			= ! empty( $size ) ? $size : 'large';
	$post_first_img		= bdp_get_option('post_first_img');
	$default_feat_img	= bdp_get_option('post_default_feat_img');
	
	$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );

	if( ! empty( $image ) ) {
		$image = isset($image[0]) ? $image[0] : '';
	}

	if( empty( $image ) && ! empty( $post_first_img ) ) {
		
		// Get post content
		$post_content = get_the_content( null, false, $post_id );

		preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"].*?>/i', $post_content, $matches);
		$image	= ! empty( $matches[1][0] ) ? $matches[1][0] : '';
	}

	// Getting default image
	if( empty( $image ) && $default_img && ! empty( $default_feat_img ) ) {
		$image = $default_feat_img;
	}

	return $image;
}

/**
 * Function to get post external link or permalink
 * 
 * @since 1.0
 */
function bdp_get_post_link( $post_id = '' ) {

	$post_link  = false;

	if( ! empty( $post_id ) ) {
		$post_link = get_permalink( $post_id );
	}
	
	return $post_link;
}

/**
 * Function to get term external link or permalink
 * 
 * @since 1.0
 */
function bdp_get_term_link( $term = '' ) {
	$term_link  		= false;
	$term_id			= is_object( $term ) ? $term->term_id : $term;

	if( ! empty( $term ) ) {

		// Get term object if term id is passed
		if( ! is_object( $term ) ) {
			$term = get_term( $term_id );
		}

		$term_link = get_term_link( $term );
	}
	return $term_link;
}

/**
 * Function to get post categories with HTML
 * 
 * @since 1.0
 */
function bdp_get_post_terms( $post_id = '', $taxonomy = BDP_CAT, $limit = null, $join = ' ' ) {

	$cat_count  = 1;
	$cat_links  = array();
	$terms      = get_the_terms( $post_id, $taxonomy );

	if( ! is_wp_error( $terms ) && $terms ) {
		foreach ( $terms as $term ) {
			$term_link      = bdp_get_term_link( $term );
			$cat_links[]    = '<a class="bdpp-post-cat-link bdpp-post-cat-'.esc_attr( $term->term_id ).' bdpp-post-cat-'.esc_attr( $term->slug ).'" href="' . esc_url( $term_link ) . '">'.esc_html( $term->name ).'</a>';

			// Upto number of limits
			if( $cat_count == $limit ) {
				break;
			}

			$cat_count++;
		}
	}
	$cat_links = join( $join, $cat_links );

	return $cat_links;
}

/**
 * Function to get post meta data like author, date and etc
 * 
 * @since 1.0
 */
function bdp_post_meta_data( $meta = array(), $args = array(), $join = ' &ndash; ', $output = 'html' ) {

	global $post;

	$result				= array();
	$join				= '<span class="bdpp-post-meta-sep">'. $join .'</span>';
	$meta				= is_array( $meta ) ? $meta : (array)$meta;
	$default_meta_args	= array(
								'icon'				=> true,
								'hide_empty'		=> true,
								'comment_text'		=> _n( 'Reply', 'Replies', get_comments_number(), 'blog-designer-pack' ),
								'post_id'			=> !empty( $args['post_id'] ) ? $args['post_id'] : $post->ID,
								'taxonomy'			=> BDP_CAT,
								'tag_taxonomy'		=> '',
								'cat_limit'			=> '',
								'tag_limit'			=> '',
							);
	$args				= wp_parse_args( $args, $default_meta_args );

	// Loop of meta data
	if( !empty( $meta ) ) {
		foreach ($meta as $meta_key => $meta_val) {

			if( empty( $meta_key ) || empty( $meta_val ) ) {
				continue;
			}

			// Post Author
			if( $meta_key == 'author' ) {
				$icon				= ( $args['icon'] ) ? '<i class="bdpp-fa fa-solid fa-user"></i>' : null;
				$result[$meta_key]	= '<span class="bdpp-post-meta-data bdpp-post-author">'. $icon . ucfirst( get_the_author() ).'</span>';
			}

			// Post Date
			if( $meta_key == 'post_date' ) {
				$icon				= ( $args['icon'] ) ? '<i class="bdpp-fa fa-regular fa-clock"></i>' : null;
				$result[$meta_key]	= '<span class="bdpp-post-meta-data bdpp-post-date">'. $icon . get_the_date().'</span>';
			}

			// Post Date
			if( $meta_key == 'comments' ) {

				$comment_count	= get_comments_number();
				$icon			= ( $args['icon'] ) ? '<i class="bdpp-fa fa-solid fa-comments"></i>' : null;

				if( (! $args['hide_empty']) || ($args['hide_empty'] && $comment_count > 0) ) {
					$result[$meta_key] = '<span class="bdpp-post-meta-data bdpp-post-comments">'. $icon . $comment_count .' '. $args['comment_text'].'</span>';
				}
			}

			// Post Category
			if( $meta_key == 'category' ) {
				$icon		= ( $args['icon'] ) ? '<i class="bdpp-fa fa-solid fa-folder-open"></i>' : null;
				$cat_list	= bdp_get_post_terms( $args['post_id'], $args['taxonomy'], $args['tag_limit'] );

				if( $cat_list ) {
					$result[$meta_key] = '<span class="bdpp-post-meta-data bdpp-post-cats">'. $icon . $cat_list.'</span>';
				}
			}

			// Post Category
			if( $meta_key == 'tag' ) {
				$icon		= ( $args['icon'] ) ? '<i class="bdpp-fa fa-solid fa-tags"></i>' : null;
				$tag_list	= bdp_get_post_terms( $args['post_id'], $args['tag_taxonomy'], $args['tag_limit'], ', ' );

				if( $tag_list ) {
					$result[$meta_key] = '<span class="bdpp-post-meta-data bdpp-post-tags">'. $icon . $tag_list.'</span>';
				}
			}
		}
	}

	// HTML Output
	if( $output == 'html' ) {
		$result = join( $join, $result );
	}

	return $result;
}

/**
 * Pagination function
 * 
 * @since 1.0
 */
function bdp_pagination( $args = array(), $atts = array() ) {

	$big				= 999999999; // need an unlikely integer
	$page_links_temp	= array();
	$multi_page			= ! empty( $args['multi_page'] )	? 1 : 0;
	$base_url			= isset( $args['base_url'] )		? $args['base_url']		: false;
	$base_param			= isset( $args['base_param'] )		? $args['base_param']	: 'bdpp_page';

	$paging_args = array(
					'base'      => isset( $args['base'] ) ? $args['base'] : str_replace( $big, '%#%', esc_url_raw( get_pagenum_link( $big, false ) ) ),
					'format'    => isset( $args['format'] ) ? $args['format'] : '?paged=%#%',
					'current'   => max( 1, $args['paged'] ),
					'total'     => $args['total'],
					'prev_next' => true,
					'prev_text' => "&laquo; " . __('Previous', 'blog-designer-pack'),
					'next_text' => __('Next', 'blog-designer-pack') . " &raquo;",
				);

	// If shortcode is placed in single post and pgination type is 'prev-next'
	if( $multi_page ) {
		$paging_args['type']	= 'plain';
		$paging_args['base']	= esc_url_raw( add_query_arg( array( $base_param => '%#%' ), $base_url ) );
		$paging_args['format']	= isset( $args['format'] ) ? $args['format'] : "?{$base_param}=%#%";
	}

	$page_links = paginate_links( $paging_args );

	return $page_links;
}

/**
 * Function to get registered post types
 * 
 * @since 1.0
 */
function bdp_get_post_types() {     

	$post_types     = array();
	$reg_post_types = get_post_types( array('public' => true), 'name' );

	// Exclude some builin WP Post Types
	$exclude_post = array('attachment', 'revision', 'nav_menu_item');

	foreach ($reg_post_types as $post_type_key => $post_data) {
		if( ! in_array( $post_type_key, $exclude_post) ) {
			$post_types[$post_type_key] = $post_data->label;
		}
	}

	return $post_types;
}

/**
 * Function to get registered Taxonomies List based on post type
 * 
 * @since 1.0
 */
function bdp_get_taxonomies( $post_type = '', $output = '' ) {
	
	// Taking some variables
	$result         = array();
	$taxonomy_list  = '';

	if( $post_type ) {

		$taxonomy_objects = get_object_taxonomies( $post_type, 'object' );

		if( ! empty($taxonomy_objects) && ! is_wp_error($taxonomy_objects) ) {
			foreach($taxonomy_objects as $object => $taxonomy) { 
				if( ! empty( $taxonomy->public ) && 'post_format' != $object ) {
					
					if( $output == 'list' ) {
						$result[] = $object;
					} else {
						$result[$object] = !empty( $taxonomy->label ) ? $taxonomy->label : $object;
					}
				}
			}
		}

		// If output is list
		if( $output == 'list' ) {
			$result = implode(', ', $result);
		}
	}
	return $result;
}

/**
 * Get Post Format
 * 
 * @since 1.0
 */
function bdp_get_post_format($post_id = '') {

	$format	= get_post_format( $post_id );
	$format	= empty( $format ) ? 'standard' : $format;

	return $format;
}

/**
 * Get Post Format HTML
 * 
 * @since 1.0
 */
function bdp_post_format_html( $format ) {
	$result = '';

	if($format == 'video') {
		$result = '<span class="bdpp-format-icon"><i class="bdpp-post-icon fa-solid fa-play"></i></span>';
	} else if ($format == 'audio') {
			$result = '<span class="bdpp-format-icon"><i class="bdpp-post-icon fa-solid fa-music"></i></span>';
	} else if ($format == 'quote') {
			$result = '<span class="bdpp-format-icon"><i class="bdpp-post-icon fa-solid fa-quote-left"></i></span>';
	} else if ($format == 'gallery') {
			$result = '<span class="bdpp-format-icon"><i class="bdpp-post-icon fa-solid fa-image"></i></span>';
	} else if ($format == 'link') {
			$result = '<span class="bdpp-format-icon"><i class="bdpp-post-icon fa-solid fa-link"></i></span>';
	} else {
		$result = '<span class="bdpp-format-icon"><i class="bdpp-post-icon fa-solid fa-thumbtack"></i></span>';
	}

	return $result;
}

/**
 * Function to get post grig 'bdp_post' shortcode design
 * 
 * @since 1.0
 */
function bdp_post_designs() {
	
	$design_arr = array(
		'design-1'	=> esc_html__('Design 1', 'blog-designer-pack'),
		'design-2'	=> esc_html__('Design 2', 'blog-designer-pack'),
	);
	
	return $design_arr;
}

/**
 * Function to get post carousel 'bdp_post_carousel' shortcode design
 * 
 * @since 1.0
 */
function bdp_post_carousel_designs() {
	
	$design_arr = array(
		'design-1'	=> esc_html__('Design 1', 'blog-designer-pack'),
		'design-2'	=> esc_html__('Design 2', 'blog-designer-pack'),
	);
	
	return $design_arr;
}

/**
 * Function to get post slider 'bdp_post_slider' shortcode design
 * 
 * @since 1.0
 */
function bdp_post_slider_designs() {
	
	$design_arr = array(
		'design-1'	=> esc_html__('Design 1', 'blog-designer-pack'),
		'design-2'	=> esc_html__('Design 2', 'blog-designer-pack'),
	);

	return $design_arr;
}

/**
 * Function to get post lists 'bdp_post_list' shortcode design
 * 
 * @since 1.0
 */
function bdp_post_list_designs() {
	
	$design_arr = array(
		'design-1'	=> esc_html__('Design 1', 'blog-designer-pack'),
		'design-2'	=> esc_html__('Design 2', 'blog-designer-pack'),
	);
	
	return $design_arr;
}

/**
 * Function to get post grigbox 'bdp_post_gridbox' shortcode design
 * 
 * @since 1.0
 */
function bdp_post_gridbox_designs() {

	$design_arr = array(
		'design-1'	=> esc_html__('Design 1', 'blog-designer-pack'),
	);

	return $design_arr;
}

/**
 * Function to get post masonry 'bdp_masonry' shortcode design
 * 
 * @since 1.0
 */
function bdp_post_masonry_designs() {
	
	$design_arr = array(
		'design-1'	=> esc_html__('Design 1', 'blog-designer-pack'),
		'design-2'	=> esc_html__('Design 2', 'blog-designer-pack'),
	);

	return $design_arr;
}

/**
 * Function to get post list widgets design
 * 
 * @since 1.0
 */
function bdp_post_widget_designs() {
	
	$design_arr = array(
		'design-6'	=> esc_html__('Design 6', 'blog-designer-pack'),
	);
	
	return $design_arr;
}

/**
 * Function to get post scrolling widgets design
 * 
 * @since 1.0
 */
function bdp_post_scrolling_widget_designs() {
	$design_arr = array(
		'design-1'	=> esc_html__('Design 1', 'blog-designer-pack'),
	);
	return $design_arr;
}

/**
 * Get plugin registered shortcodes
 * 
 * @since 1.0
 */
function bdp_registered_shortcodes( $type = 'simplified' ) {

	$result		= array();
	$shortcodes = array(
					'general' => array(
									'name'			=> __('General', 'blog-designer-pack'),
									'shortcodes'	=> array(
															'bdp_post'					=> __('Post Grid', 'blog-designer-pack'),
															'bdp_post_slider'			=> __('Post Slider', 'blog-designer-pack'),
															'bdp_post_carousel'			=> __('Post Carousel', 'blog-designer-pack'),
															'bdp_post_gridbox'			=> __('Post GridBox', 'blog-designer-pack'),
															'bdp_post_list'				=> __('Post List', 'blog-designer-pack'),
															'bdp_masonry'				=> __('Post Masonry', 'blog-designer-pack'),
															'bdp_ticker'				=> __('Post Ticker', 'blog-designer-pack'),
															'bdp_post_gridbox_slider'	=> __('Post GridBox Slider', 'blog-designer-pack'),
															'bdp_timeline'				=> __('Post Timeline', 'blog-designer-pack'),
															'bdp_simple_list'			=> __('Post Simple List', 'blog-designer-pack'),
															'bdp_archive_list'			=> __('Post Archive List', 'blog-designer-pack'),
														)
									),
					'taxonomy' => array(
									'name'			=> __('Taxonomy', 'blog-designer-pack'),
									'shortcodes'	=> array(
															'bdp_cat_grid'		=> __('Category Grid', 'blog-designer-pack'), 
															'bdp_cat_slider'	=> __('Category Slider', 'blog-designer-pack'),
															'bdp_cat_ticker'	=> __('Category Ticker', 'blog-designer-pack'),
														)
									),
					'misc' => array(
									'name'			=> __('Miscellaneous', 'blog-designer-pack'),
									'shortcodes'	=> array(
															'bdp_post_ctv1'	=> __('Creative Post Design - 1', 'blog-designer-pack'),
														)
									),
					);

	// For simplified result
	if( $type == 'simplified' && ! empty( $shortcodes ) ) {
		foreach ($shortcodes as $shrt_key => $shrt_val) {
			if( is_array( $shrt_val ) && ! empty( $shrt_val['shortcodes'] ) ) {
				$result = array_merge( $result, $shrt_val['shortcodes'] );
			} else {
				$result[ $shrt_key ] = $shrt_val;
			}
		}
	} else {
		$result = $shortcodes;
	}
	return $result;
}

/**
 * Get plugin allowed registered shortcodes
 * 
 * @since 1.0
 */
function bdp_allowed_reg_shortcodes() {
	return array( 'bdp_post', 'bdp_post_list', 'bdp_masonry', 'bdp_post_slider', 'bdp_post_carousel', 'bdp_post_gridbox', 'bdp_ticker' );
}

/**
 * Get plugin supported / enabled post types
 * 
 * @since 3.4.2
 */
function bdp_get_supported_post_types() {

	$result					= array();
	$registered_post_types	= bdp_get_post_types();
	$enabled_post_types		= bdp_get_option( 'post_types', array() );

	if( ! empty( $enabled_post_types ) && ! empty( $registered_post_types ) ) {
		foreach ( $enabled_post_types as $post_key => $post_value ) {

			if( isset( $registered_post_types[ $post_value ] ) ) {
				$result[ $post_value ] = $registered_post_types[ $post_value ];
			}
		}
	}

	return $result;
}

/**
 * Get plugin supported / enabled post types
 * 
 * @since 4.0
 */
function bdp_get_post_type_taxonomy( $post_type = BDP_POST_TYPE, $empty_option = false ) {

	// Taking some variables
	$taxonomies = array();

	if( empty( $post_type ) ) {
		return $taxonomies;
	}

	// Get associated taxonomy
	$taxonomy_objects = get_object_taxonomies( $post_type, 'object' );

	if( ! empty( $taxonomy_objects ) && ! is_wp_error( $taxonomy_objects ) ) {
		
		if( $empty_option ) {
			$taxonomies[''] = __('Select Taxonomy', 'blog-designer-pack');
		}

		foreach( $taxonomy_objects as $object => $taxonomy ) {
			if( 'post_format' != $object && ! empty( $taxonomy->public ) ) {
				$taxonomies[ $object ] = ( $taxonomy->label . ' - ('.$taxonomy->name.')' );
			}
		}
	}

	return $taxonomies;
}