<?php
/**
 * Post List Widget Class.
 *
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BDP_Post_Widget extends WP_Widget {

	// Widget variables
	var $defaults;

	function __construct() {

		// Widget settings
		$widget_ops = array( 'classname' => 'bdpp-post-widget', 'description' => __( 'Display posts in a list view.', 'blog-designer-pack' ) );

		// Create the widget
		parent::__construct( 'bdpp-post-widget', __( 'BDP - Post List Widget', 'blog-designer-pack' ), $widget_ops );

		// Widgets defaults
		$this->defaults = array(
				'title' 				=> __( 'Post List', 'blog-designer-pack' ),
				'design'				=> 'design-6',
				'limit' 				=> 5,
				'show_date'				=> 1,
				'show_category'			=> 1,
				'category' 				=> '',
				'show_author'			=> 1,
				'show_content'			=> 0,
				'media_size' 			=> 'medium',
				'content_words_limit'	=> 20,
				'order'					=> 'DESC',
				'orderby'				=> 'date',
				'css_class'				=> '',
				'tab'					=> 'general',
			);
	}

	/**
	 * Updates the widget control options
	 *
	 * @since 1.0
	 */
	function update( $new_instance, $old_instance ) {

		$instance		= $old_instance;
		$new_instance 	= wp_parse_args( (array) $new_instance, $this->defaults );

		// Input fields
		$instance['title']					= bdp_clean( $new_instance['title'] );
		$instance['design']					= bdp_clean( $new_instance['design'] );
		$instance['category']				= bdp_clean( $new_instance['category'] );
		$instance['media_size']				= bdp_clean( $new_instance['media_size'] );
		$instance['orderby']				= bdp_clean( $new_instance['orderby'] );
		$instance['tab']					= bdp_clean( $new_instance['tab'] );
		$instance['limit']					= bdp_clean_number( $new_instance['limit'], 5, 'number' );
		$instance['show_author']			= ( !empty( $new_instance['show_author'] ) ) 			? 1 : 0;
		$instance['show_date']				= ( !empty( $new_instance['show_date'] ) )		? 1 : 0;
		$instance['show_category']			= ( !empty( $new_instance['show_category'] ) )	? 1 : 0;
		$instance['show_content']			= ( !empty( $new_instance['show_content'] ) )	? 1 : 0;
		$instance['content_words_limit']	= bdp_clean_number( $new_instance['content_words_limit'], 20 );
		$instance['order']					= ( strtolower($new_instance['order']) == 'asc' ) ? 'ASC' : 'DESC';
		$instance['css_class']				= bdp_sanitize_html_classes( $new_instance['css_class'] );

		return $instance;
	}

	/**
	 * Displays the widget form in widget area
	 *
	 * @since 1.0
	 */
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );
?>
		<div class="bdpp-widget-content">
			<div class="bdpp-widget-title bdpp-widget-acc" data-target="general"><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e('General Fields', 'blog-designer-pack'); ?> <span class="dashicons dashicons-arrow-down-alt2" title="<?php esc_attr_e('Click to toggle', 'blog-designer-pack'); ?>"></span></div>
			<div class="bdpp-widget-acc-cnt-wrap bdpp-widget-general <?php if( $instance['tab'] != 'general' ) { echo 'bdpp-hide'; } ?>">
				<!-- Title -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'blog-designer-pack'); ?>:</label> 
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
				</p>

				<!-- Show Date -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('show_date') ); ?>"><?php esc_html_e( 'Show Date', 'blog-designer-pack' ); ?>:</label>
					<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_date' ) ); ?>">
						<option value="1" <?php selected( $instance['show_date'], 1 ); ?>><?php esc_html_e('Yes', 'blog-designer-pack'); ?></option>
						<option value="0" <?php selected( $instance['show_date'], 0 ); ?>><?php esc_html_e('No', 'blog-designer-pack'); ?></option>
					</select>
				</p>
				
				<!-- Show Author -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('show_author') ); ?>"><?php esc_html_e( 'Show Post Author', 'blog-designer-pack' ); ?>:</label>
					<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_author' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_author' ) ); ?>">
						<option value="1" <?php selected( $instance['show_author'], 1 ); ?>><?php esc_html_e('Yes', 'blog-designer-pack'); ?></option>
						<option value="0" <?php selected( $instance['show_author'], 0 ); ?>><?php esc_html_e('No', 'blog-designer-pack'); ?></option>
					</select>
				</p>

				<!-- Show Category -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('show_category') ); ?>"><?php esc_html_e( 'Show Category', 'blog-designer-pack' ); ?>:</label>
					<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_category' ) ); ?>">
						<option value="1" <?php selected( $instance['show_category'], 1 ); ?>><?php esc_html_e('Yes', 'blog-designer-pack'); ?></option>
						<option value="0" <?php selected( $instance['show_category'], 0 ); ?>><?php esc_html_e('No', 'blog-designer-pack'); ?></option>
					</select>
				</p>
				
				<!-- Show Content -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('show_content') ); ?>"><?php esc_html_e( 'Show Short Content', 'blog-designer-pack' ); ?>:</label>
					<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_content' ) ); ?>">
						<option value="1" <?php selected( $instance['show_content'], 1 ); ?>><?php esc_html_e('Yes', 'blog-designer-pack'); ?></option>
						<option value="0" <?php selected( $instance['show_content'], 0 ); ?>><?php esc_html_e('No', 'blog-designer-pack'); ?></option>
					</select>
				</p>
				
				<!-- Content Word Limit -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'content_words_limit' ) ); ?>"><?php esc_html_e( 'Content Word Limit', 'blog-designer-pack'); ?>:</label> 
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'content_words_limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'content_words_limit' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['content_words_limit'] ); ?>" />
					<em><?php esc_html_e( 'Enter Content word limit. e.g 20. Content word limit will only work if Show Short Content is set to Yes.', 'blog-designer-pack' ); ?></em>
				</p>

				<!-- Image Size -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('media_size') ); ?>"><?php esc_html_e( 'Image Size', 'blog-designer-pack' ); ?>:</label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('media_size') ); ?>" name="<?php echo esc_attr( $this->get_field_name('media_size') ); ?>" type="text" value="<?php echo esc_attr( $instance['media_size'] ); ?>" />
					<em><?php esc_html_e( 'Choose WordPress registered media size. e.g thumbnail, medium, large, full.', 'blog-designer-pack' ); ?></em>
				</p>

				<!-- CSS Class -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('css_class') ); ?>"><?php esc_html_e( 'CSS Class', 'blog-designer-pack' ); ?>:</label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('css_class') ); ?>" name="<?php echo esc_attr( $this->get_field_name('css_class') ); ?>" type="text" value="<?php echo esc_attr( $instance['css_class'] ); ?>" />
					<em><?php esc_html_e( 'Add an extra CSS class for designing purpose.', 'blog-designer-pack' ); ?></em>
				</p>
			</div><!-- end .bdpp-widget-acc-cnt-wrap -->

			<div class="bdpp-widget-title bdpp-widget-acc" data-target="query"><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e('Query Fields', 'blog-designer-pack'); ?> <span class="dashicons dashicons-arrow-down-alt2" title="<?php esc_attr_e('Click to toggle', 'blog-designer-pack'); ?>"></span></div>
			<div class="bdpp-widget-acc-cnt-wrap bdpp-widget-query <?php if( $instance['tab'] != 'query' ) { echo 'bdpp-hide'; } ?>">
				<!-- Limit -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Number of Items', 'blog-designer-pack'); ?>:</label> 
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['limit'] ); ?>" />
				</p>

				<!-- Order By -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php esc_html_e( 'Order By', 'blog-designer-pack' ); ?>:</label>
					<select name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>">
						<option value="date" <?php selected( $instance['orderby'], 'date' ); ?>><?php esc_html_e( 'Post Date', 'blog-designer-pack' ); ?></option>
						<option value="modified" <?php selected( $instance['orderby'], 'modified' ); ?>><?php esc_html_e( 'Post Updated Date', 'blog-designer-pack' ); ?></option>
						<option value="ID" <?php selected( $instance['orderby'], 'ID' ); ?>><?php esc_html_e( 'Post Id', 'blog-designer-pack' ); ?></option>
						<option value="title" <?php selected( $instance['orderby'], 'title' ); ?>><?php esc_html_e( 'Post Title', 'blog-designer-pack' ); ?></option>
						<option value="rand" <?php selected( $instance['orderby'], 'rand' ); ?>><?php esc_html_e( 'Random', 'blog-designer-pack' ); ?></option>
						<option value="menu_order" <?php selected( $instance['orderby'], 'menu_order' ); ?>><?php esc_html_e( 'Menu Order (Sort Order)', 'blog-designer-pack' ); ?></option>
					</select>
				</p>

				<!-- Order -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"><?php esc_html_e( 'Order', 'blog-designer-pack' ); ?>:</label>
					<select name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
						<option value="asc" <?php selected( $instance['order'], 'ASC' ); ?>><?php esc_html_e( 'Ascending', 'blog-designer-pack' ); ?></option>
						<option value="desc" <?php selected( $instance['order'], 'DESC' ); ?>><?php esc_html_e( 'Descending', 'blog-designer-pack' ); ?></option>
					</select>
				</p>

				<!-- Category -->
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id('category') ); ?>"><?php esc_html_e( 'Display Specific Category', 'blog-designer-pack' ); ?>:</label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('category') ); ?>" name="<?php echo esc_attr( $this->get_field_name('category') ); ?>" type="text" value="<?php echo esc_attr( $instance['category'] ); ?>" />
					<em><?php esc_html_e( 'Enter category id or slug to display categories wise.', 'blog-designer-pack' ); ?> <label title="<?php esc_attr_e("You can pass multiple ids or slug with comma seperated. You can find id or slug at relevant category listing page. \n\nPlease be sure that you have added valid category id or slug for chosen post type otherwise no result will be displayed.", 'blog-designer-pack'); ?>">[?]</label></em>
				</p>
			</div><!-- end .bdpp-widget-acc-cnt-wrap -->
			<input type="hidden" name="<?php echo esc_attr( $this->get_field_name('tab') ); ?>" value="<?php echo esc_attr( $instance['tab'] ); ?>" class="bdpp-widget-sel-tab" />
			<div class="bdpp-widget-loader"></div>
		</div><!-- end .bdpp-widget-content -->
<?php
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @since 1.0
	 */
	function widget( $widget_args, $instance ) {

		// Taking some globals
		global $post;

		$atts					= wp_parse_args( (array) $instance, $this->defaults );
		$widget_designs			= bdp_post_widget_designs();
		$title					= apply_filters( 'widget_title', $atts['title'], $atts, $this->id_base );
		
		$atts['category'] 		= ! empty( $atts['category'] )	? explode(',', $atts['category']) : array();
		$atts['limit']			= bdp_clean_number( $atts['limit'] );
		$atts['order']			= bdp_clean( $atts['order'] );
		$atts['orderby']		= bdp_clean( $atts['orderby'] );
		$atts['media_size']		= bdp_clean( $atts['media_size'] );
		$atts['design'] 		= ($atts['design'] && (array_key_exists(trim($atts['design']), $widget_designs))) ? trim( $atts['design'] ) : 'design-6';
		$atts['css_class']		= bdp_sanitize_html_classes( $atts['css_class'] );

		// WP Query Parameters
		$args = array(
			'post_type'				=> BDP_POST_TYPE,
			'post_status'			=> array('publish'),
			'order'					=> $atts['order'],
			'orderby'				=> $atts['orderby'],
			'posts_per_page'		=> $atts['limit'],
			'no_found_rows'			=> true,
			'ignore_sticky_posts'	=> true,
		);

		// Category Parameter
		if( $atts['category'] ) {

			$args['tax_query'] = array(
									array( 
										'taxonomy' 	=> BDP_CAT,
										'terms' 	=> $atts['category'],
										'field' 	=> ( isset( $atts['category'][0] ) && is_numeric( $atts['category'][0] ) ) ? 'term_id' : 'slug',
									));
		}

		$args = apply_filters( 'bdpp_post_widget_query_args', $args, $atts );

		// WP Query
		$query = new WP_Query( $args );

		// Start Widget Output
		echo $widget_args['before_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

		if ( $title ) {
			echo $widget_args['before_title'] . $title . $widget_args['after_title']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
		}

		if( $query->have_posts() ) {

			include( BDP_DIR . '/templates/widget/post-list/loop-start.php' );

			while ( $query->have_posts() ) : $query->the_post();

				$atts['format']		= bdp_get_post_format();
				$atts['feat_img'] 	= bdp_get_post_feat_image( $post->ID, $atts['media_size'] );
				$atts['post_link'] 	= bdp_get_post_link( $post->ID );
				$atts['cate_name'] 	= bdp_get_post_terms( $post->ID, BDP_CAT );

				$atts['wrp_cls']	= "bdpp-post-{$post->ID} bdpp-post-{$atts['format']}";
				$atts['wrp_cls']	.= ( is_sticky( $post->ID ) )	? ' bdpp-sticky'	: '';
				$atts['wrp_cls'] 	.= empty( $atts['feat_img'] )	? ' bdpp-no-thumb'	: ' bdpp-has-thumb';

				// Include Dsign File
				include( BDP_DIR . '/templates/widget/post-list/design-6.php' );

			endwhile;

			include( BDP_DIR . '/templates/widget/post-list/loop-end.php' );
		}

		wp_reset_postdata(); // Reset WP Query

		echo $widget_args['after_widget']; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
	}
}