<?php
/**
 * Shortcode Fields for Shortcode Preview 
 *
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Generate 'bdp_post' shortcode fields
 * 
 * @since 1.0
 */
function bdp_post_lite_shortcode_fields( $shortcode = '' ) {
	$fields = array(
			// General Settings
			'general' => array(
					'title'		=> __('General & Designs', 'blog-designer-pack'),
					'params'	=>  array(
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Design', 'blog-designer-pack' ),
											'name' 		=> 'design',
											'value' 	=> bdp_post_designs(),
											'desc' 		=> __( 'Choose layout design.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Grid', 'blog-designer-pack' ),
											'name' 			=> 'grid',
											'value' 		=> array(
																	'1'	 => __( 'Grid 1', 'blog-designer-pack' ),
																	'2'	 => __( 'Grid 2', 'blog-designer-pack' ),
																	'3'	 => __( 'Grid 3', 'blog-designer-pack' ),
																	'4'	 => __( 'Grid 4', 'blog-designer-pack' ),
																	'5'	 => __( 'Grid 5', 'blog-designer-pack' ),
																	'6'	 => __( 'Grid 6', 'blog-designer-pack' ),
																	'7'	 => __( 'Grid 7', 'blog-designer-pack' ),
																	'8'	 => __( 'Grid 8', 'blog-designer-pack' ),
																	'9'	 => __( 'Grid 9', 'blog-designer-pack' ),
																	'10' => __( 'Grid 10', 'blog-designer-pack' ),
																	'11' => __( 'Grid 11', 'blog-designer-pack' ),
																	'12' => __( 'Grid 12', 'blog-designer-pack' ),
																),
											'default'		=> 3,
											'desc' 			=> __( 'Choose number of column to be displayed.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Image Size', 'blog-designer-pack' ),
											'name' 			=> 'media_size',
											'value' 		=> 'large',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Choose WordPress registered image size. e.g.', 'blog-designer-pack' ).' bdpp-medium, thumbnail, medium, large, full.',
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'CSS Class', 'blog-designer-pack' ),
											'name' 			=> 'css_class',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Enter an extra CSS class for design customization.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Extra class will be added at top most parent so using extra class you customize your design.', 'blog-designer-pack').'"> [?]</label>',
										),
									)
			),

			// Meta Fields
			'meta' => array(
					'title'     => __('Meta & Content', 'blog-designer-pack'),
					'params'   	=>  array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Post Date', 'blog-designer-pack' ),
											'name' 			=> 'show_date',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post date.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Author', 'blog-designer-pack' ),
											'name' 			=> 'show_author',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post author.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Tags', 'blog-designer-pack' ),
											'name' 			=> 'show_tags',
											'value' 		=> array( 
																	'true'		=> __( 'True', 'blog-designer-pack' ),
																	'false'		=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post tags.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Comments Count', 'blog-designer-pack' ),
											'name' 			=> 'show_comments',
											'value' 		=> array(
																	'true'		=> __( 'True', 'blog-designer-pack' ),
																	'false'		=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post comment count.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Category', 'blog-designer-pack' ),
											'name' 			=> 'show_category',
											'value' 		=> array( 
																	'true'		=> __( 'True', 'blog-designer-pack' ),
																	'false'		=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post category.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Content', 'blog-designer-pack' ),
											'name' 			=> 'show_content',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post content.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Content Word Limit', 'blog-designer-pack' ),
											'name' 			=> 'content_words_limit',
											'value' 		=> 20,
											'desc' 			=> __( 'Enter content word limit.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'show_content',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Read More', 'blog-designer-pack' ),
											'name' 			=> 'show_read_more',
											'value' 		=> array(
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Show read more.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'show_content',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Read More Text', 'blog-designer-pack' ),
											'name' 			=> 'read_more_text',
											'value' 		=> __( 'Read More', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter read more text.', 'blog-designer-pack' ),
											'refresh_time'	=> 1000,
											'dependency' 	=> array(
																	'element' 	=> 'show_read_more',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more Meta & Content options like Read More Text etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Sub Title', 'blog-designer-pack' ),
											'name' 			=> 'show_sub_title',
											'premium'		=> true,
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display sub title or not.', 'blog-designer-pack' ) . '<label title="'.esc_attr__("Sub title can be added via 'Blog Designer Pack Pro - Settings' metabox from Post add / edit screen.", 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Post Link Target', 'blog-designer-pack' ),
											'name'		=> 'link_behaviour',
											'premium'		=> true,
											'value' 	=> array(
																'self'	=> __( 'Same Tab', 'blog-designer-pack' ),
																'new'	=> __( 'New Tab', 'blog-designer-pack' ),
															),
											'desc'		=> __( 'Choose post link behaviour.', 'blog-designer-pack' ),
										),
									)
			),
			
			// Data Fields
			'query' => array(
					'title'		=> __('Query', 'blog-designer-pack'),
					'params'	=> array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Type', 'blog-designer-pack' ),
											'name' 			=> 'post_type',
											'class'			=> 'bdpp-post-type-sel',
											'value' 		=> bdp_get_supported_post_types(),
											'ajax'			=> true,
											'desc' 			=> sprintf( __( 'Choose registered post type. You can enable it from plugin %ssetting%s page.', 'blog-designer-pack' ), '<a href="'.esc_url( BDP_SETTING_PAGE_URL ).'" target="_blank">', '</a>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include By Category', 'blog-designer-pack' ),
											'name' 			=> 'category',
											'value' 		=> '',
											'class'			=> 'bdpp-ajax-select2 bdpp-category-sel',
											'multi'			=> true,
											'ajax'			=> true,
											'ajax_action'	=> 'bdpp_category_sugg',
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose categories to display category wise posts.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Order By', 'blog-designer-pack' ),
											'name' 			=> 'orderby',
											'value' 		=>  array(
																	'date' 			=> __( 'Post Date', 'blog-designer-pack' ),
																	'ID' 			=> __( 'Post ID', 'blog-designer-pack' ),
																	'author' 		=> __( 'Post Author', 'blog-designer-pack' ),
																	'title' 		=> __( 'Post Title', 'blog-designer-pack' ),
																	'name' 			=> __( 'Post Slug', 'blog-designer-pack' ),
																	'modified' 		=> __( 'Post Modified Date', 'blog-designer-pack' ),
																	'menu_order'	=> __( 'Menu Order', 'blog-designer-pack' ),
																	'parent'		=> __( 'Parent ID', 'blog-designer-pack' ),
																	'rand' 			=> __( 'Random', 'blog-designer-pack' ),
																	'comment_count'	=> __( 'Number of Comments', 'blog-designer-pack' ),
																	'relevance'		=> __( 'Relevance', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select order type.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Order', 'blog-designer-pack' ),
											'name' 			=> 'order',
											'value' 		=> array(
																	'desc'	=> __( 'Descending', 'blog-designer-pack' ),
																	'asc'	=> __( 'Ascending', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select sorting order.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Custom Parameter 1', 'blog-designer-pack' ),
											'name' 			=> 'custom_param_1',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Give your Query a custom unique parameter to allow server side filtering.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: You can customize the plugin query via Hooks and Filters with the help of this parameter.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Custom Parameter 2', 'blog-designer-pack' ),
											'name' 			=> 'custom_param_2',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Give your Query a custom unique parameter to allow server side filtering.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: You can customize the plugin query via Hooks and Filters with the help of this parameter.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more Query options like Cat Taxonomy, Exclude By Category etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'taxonomy',
											'premium'		=> true,
											'value' 		=> bdp_get_post_type_taxonomy( BDP_POST_TYPE ),
											'class'			=> 'bdpp-taxonomy-sel',
											'desc' 			=> __( 'Choose registered taxonomy if you want to display category wise post.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Cat Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'cat_taxonomy',
											'class'			=> 'bdpp-cat-taxonomy-sel',
											'premium'		=> true,
											'value' 		=> array( '' => __('Select Taxonomy', 'blog-designer-pack') ),
											'desc' 			=> __( 'Choose a category taxonomy just to display categories as meta information.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Tag Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'tag_taxonomy',
											'class'			=> 'bdpp-tag-taxonomy-sel',
											'premium'		=> true,
											'value' 		=> array( '' => __('Select Taxonomy', 'blog-designer-pack') ),
											'desc' 			=> __( 'Choose a tag taxonomy just to display tags as meta information.', 'blog-designer-pack' ),
										),
										array(
											'type'			=> 'dropdown',
											'class'			=> '',
											'heading'		=> __( 'Category Operator', 'blog-designer-pack'),
											'name'			=> 'category_operator',
											'premium'		=> true,
											'value'			=> array( 
																	'IN'	=> __( 'IN', 'blog-designer-pack' ),
																	'AND'	=> __( 'AND', 'blog-designer-pack' ),
																),
											'desc'			=> __( 'Select category operator. Default value is IN', 'blog-designer-pack' ),
										),
										array(
											'type'			=> 'dropdown',
											'class'			=> '',
											'heading'		=> __( 'Display Child Category Posts', 'blog-designer-pack'),
											'name'			=> 'include_cat_child',
											'premium'		=> true,
											'value'			=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc'			=> __( 'Whether or not to include children category posts if parent category is there.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude By Category', 'blog-designer-pack' ),
											'name' 			=> 'exclude_cat',
											'premium'		=> true,
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose categories to exclude posts of it. Works only if `Category` field is empty.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include Post', 'blog-designer-pack' ),
											'name' 			=> 'posts',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search posts by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __('Choose posts which you want to display.', 'blog-designer-pack'),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude Post', 'blog-designer-pack' ),
											'name' 			=> 'hide_post',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search posts by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __('Choose posts which you do not want to display.', 'blog-designer-pack'),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include By Author', 'blog-designer-pack' ),
											'name' 			=> 'author',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search authors by its name, email or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose authors to show posts associated with that.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude By Author', 'blog-designer-pack' ),
											'name' 			=> 'exclude_author',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search authors by its name, email or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose authors to hide posts associated with that. Works only if `Include Author` field is empty.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Sticky Posts', 'blog-designer-pack' ),
											'name' 			=> 'sticky_posts',
											'value' 		=> array(
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'premium'		=> true,
											'desc' 			=> __( 'Display sticky posts. This only effects the frontend.', 'blog-designer-pack' ) . '<label title="'.esc_attr__("Note: Sticky post only be displayed at front side. In preview mode sticky post will not be displayed.", 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Type', 'blog-designer-pack' ),
											'name' 			=> 'type',
											'premium'		=> true,
											'value' 		=> array(
																	'' 			=> __( 'Select Type', 'blog-designer-pack' ),
																	'featured'	=> __( 'Featured', 'blog-designer-pack' ),
																	'trending'	=> __( 'Trending', 'blog-designer-pack'),
																),
											'desc' 			=> __( 'Select display type of post. Is it Featured or Trending?', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: For trending post type make sure you have enabled the post type from Plugin Settings > Trending Post.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Query Offset', 'blog-designer-pack' ),
											'name' 			=> 'query_offset',
											'premium'		=> true,
											'value' 		=> '',
											'desc' 			=> __( 'Skip number of posts from starting.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('e.g. 5 to skip over 5 posts. Note: Do not use limit=-1 and pagination=true with this.', 'blog-designer-pack').'"> [?]</label>',
										),
									)
			),

			// Data Fields
			'pagination' => array(
					'title'		=> __('Pagination', 'blog-designer-pack'),
					'params'	=> array(
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Total Number of Post', 'blog-designer-pack' ),
											'name' 			=> 'limit',
											'value' 		=> 20,
											'min'			=> -1,
											'desc' 			=> __( 'Enter total number of post to be displayed. Enter -1 to display all.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Pagination', 'blog-designer-pack' ),
											'name' 			=> 'pagination',
											'value' 		=> array( 
																'true'	=> __( 'True', 'blog-designer-pack' ),
																'false'	=> __( 'False', 'blog-designer-pack' ),
															),
											'dependency' 	=> array(
																		'element' 				=> 'limit',
																		'value_not_equal_to' 	=> '-1',
																	),
											'desc' 			=> __( 'Display Pagination.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Pagination Type', 'blog-designer-pack' ),
											'name' 			=> 'pagination_type',
											'value' 		=> array(
																	'numeric'					=> __( 'Numeric', 'blog-designer-pack' ),
																	'numeric-ajax|disabled'		=> __( 'Numeric Ajax', 'blog-designer-pack' ),
																	'prev-next|disabled'		=> __( 'Next - Prev', 'blog-designer-pack' ),
																	'prev-next-ajax|disabled'	=> __( 'Next - Prev Ajax', 'blog-designer-pack' ),
																	'load-more|disabled'		=> __( 'Load More', 'blog-designer-pack' ),
																	'infinite|disabled'			=> __( 'Infinite Scroll', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose pagination type.', 'blog-designer-pack' ),											
											'dependency' 	=> array(
																'element' 				=> 'pagination',
																'value_not_equal_to' 	=> array( 'false' ),
															),
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more pagination type like Load More, Infinite Scroll etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Previous Button Text', 'blog-designer-pack' ),
											'name' 			=> 'prev_text',
											'value' 		=> '',
											'desc' 			=> __( 'Pagination previous button text. Leave it empty for default.', 'blog-designer-pack' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Next Button Text', 'blog-designer-pack' ),
											'name' 			=> 'next_text',
											'value' 		=> '',
											'desc' 			=> __( 'Pagination next button text. Leave it empty for default.', 'blog-designer-pack' ),
											'premium'		=> true,
										)
									)
			),
			
			// Social Sharing
			'social_sharing' => array(
					'title'		=> __('Social Sharing', 'blog-designer-pack'),
					'premium'	=> true,
					'params'	=> array(
										array(
											'type'	=> 'dropdown',
											'name'	=> 'sharing',
											'value'	=> array('' => __('No Social Sharing', 'blog-designer-pack') ),
											'desc'	=> __( 'Enable social sharing. You can enable it from plugin setting page.', 'blog-designer-pack' ) . '<label> [?]</label>',
										),
									)
			),

			// Filter Settings
			'filter' => array(
					'title'		=> __('Filter', 'blog-designer-pack'),
					'premium'	=> true,
					'params'	=>  array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Enable Filter', 'blog-designer-pack' ),
											'name' 			=> 'filter',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Enable category filter.', 'blog-designer-pack' ),
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Filter Design', 'blog-designer-pack' ),
											'name' 		=> 'filter_design',
											'value' 	=> array( 
																	'design-1'	=> __( 'Design 1', 'blog-designer-pack' ),
															),
											'desc' 		=> __( 'Choose filter design.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Filter All Text', 'blog-designer-pack' ),
											'name' 			=> 'filter_all_text',
											'value' 		=> __( 'All', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter `ALL` field text. Leave it empty to remove it.', 'blog-designer-pack' ),
											'allow_empty'	=> true,
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Filter More Text', 'blog-designer-pack' ),
											'name' 			=> 'filter_more_text',
											'value' 		=> __( 'More', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter filter `More` field text. This will be displayed when the category filter is wider than screen.', 'blog-designer-pack' ),
											'allow_empty'	=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Position', 'blog-designer-pack' ),
											'name' 			=> 'filter_position',
											'value' 		=> array( 
																	'top'		=> __( 'Top', 'blog-designer-pack' ),
																	'left'		=> __( 'Left', 'blog-designer-pack' ),
																	'right'		=> __( 'Right', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose filter position.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Alignment', 'blog-designer-pack' ),
											'name' 			=> 'filter_align',
											'value' 		=> array( 
																	'right'		=> __( 'Right', 'blog-designer-pack' ),
																	'left'		=> __( 'Left', 'blog-designer-pack' ),
																	'center'	=> __( 'Center', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose filter alignment.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Filter Responsive Screen', 'blog-designer-pack' ),
											'name' 			=> 'filter_res_screen',
											'value' 		=> 768,
											'desc' 			=> __( 'Enter filter responsive screen. Filter will be on top position below this screen resolution.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Total Number of Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_limit',
											'value' 		=> 10,
											'desc' 			=> __( 'Enter number of categories to display at a time. Enter 0 to display all.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Categories Order By', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_orderby',
											'value' 		=>  array(
																	'name' 			=> __( 'Category Name', 'blog-designer-pack' ),
																	'slug' 			=> __( 'Category Slug', 'blog-designer-pack' ),
																	'term_group' 	=> __( 'Category Group', 'blog-designer-pack' ),
																	'term_id' 		=> __( 'Category ID', 'blog-designer-pack' ),
																	'id' 			=> __( 'ID', 'blog-designer-pack' ),
																	'description' 	=> __( 'Category Description', 'blog-designer-pack' ),
																	'parent'		=> __( 'Category Parent', 'blog-designer-pack' ),
																	'term_order'	=> __( 'Category Order', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select filter category order type.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Categories Order', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_order',
											'value' 		=> array(
																	'asc'	=> __( 'Ascending', 'blog-designer-pack' ),
																	'desc'	=> __( 'Descending', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select filter category sorting order.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Child of Category', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_child_of',
											'value' 		=> array(
																	'' => __('Select Category', 'blog-designer-pack')
																),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Select term id to retrieve child terms of.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Parent Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_parent',
											'value' 		=> array(
																	'' => __('Select Category', 'blog-designer-pack')
																),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Select parent term id to retrieve direct child terms of. Add 0 to display only parent categories.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Active Filter Category', 'blog-designer-pack' ),
											'name' 			=> 'filter_active',
											'value' 		=> '',
											'desc' 			=> __( 'Choose active category. Enter number starting form 1 OR category ID like cat-ID. Default first will be active.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Allow Multiple Filter Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_allow_multiple',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Allow multiple filter category selection at a time.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Reload Filter', 'blog-designer-pack' ),
											'name' 			=> 'filter_reload',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Reload page on filter category selection.', 'blog-designer-pack' ),
										),
									)
			),

			// Style Manager
			'style_manager' => array(
					'title'		=> __('Style Manager', 'blog-designer-pack'),
					'premium'	=> true,
					'params'	=> array(
										array(
											'type'		=> 'dropdown',
											'name'		=> 'style_id',
											'value'		=> array('' => __('Choose Style', 'blog-designer-pack')),
											'desc'		=> __( 'Choose your created style from style manager or create a new one.', 'blog-designer-pack' ),
										)
									)
			)
		);
	return $fields;
}

/**
 * Generate 'bdp_post_slider' shortcode fields
 * 
 * @since 1.0
 */
function bdp_post_slider_lite_shortcode_fields( $shortcode = '' ) {
	$fields = array(
			// General Settings
			'general' => array(
					'title'		=> __('General & Designs', 'blog-designer-pack'),
					'params'	=>  array(
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Design', 'blog-designer-pack' ),
											'name' 		=> 'design',
											'value' 	=> bdp_post_slider_designs(),
											'desc' 		=> __( 'Choose layout design.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Image Size', 'blog-designer-pack' ),
											'name' 			=> 'media_size',
											'value' 		=> 'large',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Choose WordPress registered image size. e.g', 'blog-designer-pack' ).' thumbnail, medium, large, full.',
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'CSS Class', 'blog-designer-pack' ),
											'name' 			=> 'css_class',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Enter an extra CSS class for design customization.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Extra class will be added at top most parent so using extra class you customize your design.', 'blog-designer-pack').'"> [?]</label>',
										),
									)
			),

			// Slider Fields
			'slider' => array(
					'title'		=> __('Slider', 'blog-designer-pack'),
					'params'    => array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Loop', 'blog-designer-pack' ),
											'name' 			=> 'loop',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Enable slider loop.', 'blog-designer-pack' ),
										),
										array(
											'type'		=> 'dropdown',
											'heading' 	=> __( 'Show Arrows', 'blog-designer-pack' ),
											'name' 		=> 'arrows',
											'value' 	=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc'		=> __( 'Show prev - next arrows.', 'blog-designer-pack' ),
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Show Dots', 'blog-designer-pack' ),
											'name' 		=> 'dots',
											'value' 	=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 		=> __( 'Show pagination dots.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Autoplay', 'blog-designer-pack' ),
											'name' 			=> 'autoplay',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Enable slider autoplay.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Autoplay Interval', 'blog-designer-pack' ),
											'name' 			=> 'autoplay_interval',
											'value' 		=> 5000,
											'desc' 			=> __( 'Enter autoplay interval.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'autoplay',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Speed', 'blog-designer-pack' ),
											'name' 			=> 'speed',
											'value' 		=> 500,
											'desc' 			=> __( 'Enter slider speed.', 'blog-designer-pack' ),
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more Slider options like Show Thumbnail etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Previous Button Text', 'blog-designer-pack' ),
											'name' 			=> 'prev_text',
											'value' 		=> '',
											'desc' 			=> __( 'Slider previous button text.', 'blog-designer-pack' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Next Button Text', 'blog-designer-pack' ),
											'name' 			=> 'next_text',
											'value' 		=> '',
											'desc' 			=> __( 'Slider next button text.', 'blog-designer-pack' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Autoplay Pause on Hover', 'blog-designer-pack' ),
											'name' 			=> 'autoplay_hover_pause',
											'premium'		=> true,
											'value' 		=> array(
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
															),
											'desc' 			=> __( 'Autoplay pause on hover.', 'blog-designer-pack' ),											
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Slider Auto Height', 'blog-designer-pack' ),
											'name' 			=> 'auto_height',
											'premium'		=> true,
											'value' 		=> array( 
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Enable slider auto height.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Slider Start Position', 'blog-designer-pack' ),
											'name' 			=> 'start_position',
											'value' 		=> '',
											'premium'		=> true,
											'desc' 			=> __( 'Enter slide number to start from that.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Slide Margin', 'blog-designer-pack' ),
											'name' 			=> 'slide_margin',
											'premium'		=> true,
											'value' 		=> 5,
											'desc' 			=> __( 'Slide margin.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Slider Stage Padding', 'blog-designer-pack' ),
											'name' 			=> 'stage_padding',
											'premium'		=> true,
											'value' 		=> 0,
											'desc' 			=> __( 'Enter slider stage padding. A partial slide will be visible at both the end.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Thumbnail', 'blog-designer-pack' ),
											'name' 			=> 'show_thumbnail',
											'premium'		=> true,
											'value' 		=> array(
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display slider thumbnail.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Thumbnail Image', 'blog-designer-pack' ),
											'name' 			=> 'show_thumbnail_img',
											'premium'		=> true,
											'value' 		=> array( 
																'true'	=> __( 'True', 'blog-designer-pack' ),
																'false'	=> __( 'False', 'blog-designer-pack' ),
															),
											'desc' 			=> __( 'Display Thumbnail Images or Not.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Thumbnail Title', 'blog-designer-pack' ),
											'name' 			=> 'show_thumbnail_title',
											'premium'		=> true,
											'value' 		=> array( 
																'false'	=> __( 'False', 'blog-designer-pack' ),
																'true'	=> __( 'True', 'blog-designer-pack' ),
															),
											'desc' 			=> __( 'Display Thumbnail Title or Not.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Thumbnail Date', 'blog-designer-pack' ),
											'name' 			=> 'show_thumbnail_date',
											'premium'		=> true,
											'value' 		=> array( 
																'false'	=> __( 'False', 'blog-designer-pack' ),
																'true'	=> __( 'True', 'blog-designer-pack' ),
															),
											'desc' 			=> __( 'Display Thumbnail Date or Not.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Number of Thumbnails', 'blog-designer-pack' ),
											'name' 			=> 'thumbnail',
											'value' 		=> 7,
											'min'			=> 1,
											'premium'		=> true,
											'desc' 			=> __( 'Enter number of thumbnails. The ideal value should be 7.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: Number of thumbnails will adjust according to responsive layout mode.', 'blog-designer-pack').'"> [?]</label>',											
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'URL Hash Listner', 'blog-designer-pack' ),
											'name' 			=> 'url_hash_listener',
											'premium'		=> true,
											'value' 		=> array(
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Enable url hash listner of slider.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Lazyload', 'blog-designer-pack' ),
											'name' 			=> 'lazyload',
											'premium'		=> true,
											'value' 		=> array(
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Enable slider lazyload behaviour.', 'blog-designer-pack' ),
										),
								)
			),

			// Meta Fields
			'meta' => array(
					'title'     => __('Meta & Content', 'blog-designer-pack'),
					'params'   	=>  array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Post Date', 'blog-designer-pack' ),
											'name' 			=> 'show_date',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post date.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Author', 'blog-designer-pack' ),
											'name' 			=> 'show_author',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post author.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Tags', 'blog-designer-pack' ),
											'name' 			=> 'show_tags',
											'value' 		=> array( 
																	'false'		=> __( 'False', 'blog-designer-pack' ),
																	'true'		=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post tags.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Comments Count', 'blog-designer-pack' ),
											'name' 			=> 'show_comments',
											'value' 		=> array(
																	'true'		=> __( 'True', 'blog-designer-pack' ),
																	'false'		=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post comment count.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Category', 'blog-designer-pack' ),
											'name' 			=> 'show_category',
											'value' 		=> array( 
																	'true'		=> __( 'True', 'blog-designer-pack' ),
																	'false'		=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post category.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Content', 'blog-designer-pack' ),
											'name' 			=> 'show_content',
											'value' 		=> array(
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post content.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Content Word Limit', 'blog-designer-pack' ),
											'name' 			=> 'content_words_limit',
											'value' 		=> 20,
											'desc' 			=> __( 'Enter content word limit.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'show_content',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Read More', 'blog-designer-pack' ),
											'name' 			=> 'show_read_more',
											'value' 		=> array(
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Show read more.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'show_content',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Read More Text', 'blog-designer-pack' ),
											'name' 			=> 'read_more_text',
											'value' 		=> __( 'Read More', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter read more text.', 'blog-designer-pack' ),
											'refresh_time'	=> 1000,
											'dependency' 	=> array(
																	'element' 	=> 'show_read_more',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more Meta & Content options like Read More Text etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Sub Title', 'blog-designer-pack' ),
											'name' 			=> 'show_sub_title',
											'premium'		=> true,
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display sub title or not.', 'blog-designer-pack' ) . '<label title="'.esc_attr__("Sub title can be added via 'Blog Designer Pack Pro - Settings' metabox from Post add / edit screen.", 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Post Link Target', 'blog-designer-pack' ),
											'name'		=> 'link_behaviour',
											'premium'	=> true,
											'value' 	=> array(
																'self'	=> __( 'Same Tab', 'blog-designer-pack' ),
																'new'	=> __( 'New Tab', 'blog-designer-pack' ),
															),
											'desc'		=> __( 'Choose post link behaviour.', 'blog-designer-pack' ),
										),
								)
			),
			

			// Data Fields
			'query' => array(
					'title'		=> __('Query', 'blog-designer-pack'),
					'params'    => array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Type', 'blog-designer-pack' ),
											'name' 			=> 'post_type',
											'value' 		=> bdp_get_supported_post_types(),
											'class'			=> 'bdpp-post-type-sel',
											'ajax'			=> true,
											'desc' 			=> sprintf( __( 'Choose registered post type. You can enable it from plugin %ssetting%s page.', 'blog-designer-pack' ), '<a href="'.esc_url( BDP_SETTING_PAGE_URL ).'" target="_blank">', '</a>' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Total Number of Post', 'blog-designer-pack' ),
											'name' 			=> 'limit',
											'value' 		=> 20,
											'min'			=> -1,
											'desc' 			=> __( 'Enter total number of post to be displayed. Enter -1 to display all.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include By Category', 'blog-designer-pack' ),
											'name' 			=> 'category',
											'value' 		=> '',
											'class'			=> 'bdpp-ajax-select2 bdpp-category-sel',
											'multi'			=> true,
											'ajax'			=> true,
											'ajax_action'	=> 'bdpp_category_sugg',
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose categories to display category wise posts.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Order By', 'blog-designer-pack' ),
											'name' 			=> 'orderby',
											'value' 		=>  array(
																	'date' 			=> __( 'Post Date', 'blog-designer-pack' ),
																	'ID' 			=> __( 'Post ID', 'blog-designer-pack' ),
																	'author' 		=> __( 'Post Author', 'blog-designer-pack' ),
																	'title' 		=> __( 'Post Title', 'blog-designer-pack' ),
																	'name' 			=> __( 'Post Slug', 'blog-designer-pack' ),
																	'modified' 		=> __( 'Post Modified Date', 'blog-designer-pack' ),
																	'menu_order'	=> __( 'Menu Order', 'blog-designer-pack' ),
																	'parent'		=> __( 'Parent ID', 'blog-designer-pack' ),
																	'rand' 			=> __( 'Random', 'blog-designer-pack' ),
																	'comment_count'	=> __( 'Number of Comments', 'blog-designer-pack' ),
																	'relevance'		=> __( 'Relevance', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select order type.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Order', 'blog-designer-pack' ),
											'name' 			=> 'order',
											'value' 		=> array(
																	'desc'	=> __( 'Descending', 'blog-designer-pack' ),
																	'asc'	=> __( 'Ascending', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select sorting order.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Custom Parameter 1', 'blog-designer-pack' ),
											'name' 			=> 'custom_param_1',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Give your Query a custom unique parameter to allow server side filtering.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: You can customize the plugin query via Hooks and Filters with the help of this parameter.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Custom Parameter 2', 'blog-designer-pack' ),
											'name' 			=> 'custom_param_2',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Give your Query a custom unique parameter to allow server side filtering.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: You can customize the plugin query via Hooks and Filters with the help of this parameter.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more Query options like Cat Taxonomy, Exclude By Category etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'taxonomy',
											'premium'		=> true,
											'value' 		=> bdp_get_post_type_taxonomy( BDP_POST_TYPE ),
											'class'			=> 'bdpp-taxonomy-sel',
											'desc' 			=> __( 'Choose registered taxonomy if you want to display category wise post.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Cat Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'cat_taxonomy',
											'class'			=> 'bdpp-cat-taxonomy-sel',
											'premium'		=> true,
											'value' 		=> array( '' => __('Select Taxonomy', 'blog-designer-pack') ),
											'desc' 			=> __( 'Choose a category taxonomy just to display categories as meta information.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Tag Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'tag_taxonomy',
											'class'			=> 'bdpp-tag-taxonomy-sel',
											'premium'		=> true,
											'value' 		=> array( '' => __('Select Taxonomy', 'blog-designer-pack') ),
											'desc' 			=> __( 'Choose a tag taxonomy just to display tags as meta information.', 'blog-designer-pack' ),
										),
										
										array(
											'type'			=> 'dropdown',
											'class'			=> '',
											'heading'		=> __( 'Category Operator', 'blog-designer-pack'),
											'name'			=> 'category_operator',
											'premium'		=> true,
											'value'			=> array( 
																	'IN'	=> __( 'IN', 'blog-designer-pack' ),
																	'AND'	=> __( 'AND', 'blog-designer-pack' ),
																),
											'desc'			=> __( 'Select category operator. Default value is IN', 'blog-designer-pack' ),
										),
										array(
											'type'			=> 'dropdown',
											'class'			=> '',
											'heading'		=> __( 'Display Child Category Posts', 'blog-designer-pack'),
											'name'			=> 'include_cat_child',
											'premium'		=> true,
											'value'			=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc'			=> __( 'Whether or not to include children category posts if parent category is there.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude By Category', 'blog-designer-pack' ),
											'name' 			=> 'exclude_cat',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose categories to exclude posts of it. Works only if `Category` field is empty.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include Post', 'blog-designer-pack' ),
											'name' 			=> 'posts',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search posts by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __('Choose posts which you want to display.', 'blog-designer-pack'),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude Post', 'blog-designer-pack' ),
											'name' 			=> 'hide_post',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search posts by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __('Choose posts which you do not want to display.', 'blog-designer-pack'),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include By Author', 'blog-designer-pack' ),
											'name' 			=> 'author',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search authors by its name, email or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose authors to show posts associated with that.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude By Author', 'blog-designer-pack' ),
											'name' 			=> 'exclude_author',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search authors by its name, email or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose authors to hide posts associated with that. Works only if `Include Author` field is empty.', 'blog-designer-pack' ),
										),
										
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Sticky Posts', 'blog-designer-pack' ),
											'name' 			=> 'sticky_posts',
											'premium'		=> true,
											'value' 		=> array(
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display sticky posts. This only effects the frontend.', 'blog-designer-pack' ) . '<label title="'.esc_attr__("Note: Sticky post only be displayed at front side. In preview mode sticky post will not be displayed.", 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Type', 'blog-designer-pack' ),
											'name' 			=> 'type',
											'premium'		=> true,
											'value' 		=> array(
																	'' 			=> __( 'Select Type', 'blog-designer-pack' ),
																	'featured'	=> __( 'Featured', 'blog-designer-pack' ),
																	'trending'	=> __( 'Trending', 'blog-designer-pack'),
																),
											'desc' 			=> __( 'Select display type of post. Is it Featured or Trending?', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: For trending post type make sure you have enabled the post type from Plugin Settings > Trending Post.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Query Offset', 'blog-designer-pack' ),
											'name' 			=> 'query_offset',
											'value' 		=> '',
											'premium'		=> true,
											'desc' 			=> __( 'Skip number of posts from starting.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('e.g. 5 to skip over 5 posts. Note: Do not use limit=-1 and pagination=true with this.', 'blog-designer-pack').'"> [?]</label>',
										),										
									)
			),

			// Social Sharing
			'social_sharing' => array(
					'title'		=> __('Social Sharing', 'blog-designer-pack'),
					'premium'	=> true,
					'params'	=> array(
										array(
											'type'	=> 'dropdown',
											'name'	=> 'sharing',
											'value'	=> array('' => __('No Social Sharing', 'blog-designer-pack') ),
											'desc'	=> __( 'Enable social sharing. You can enable it from plugin setting page.', 'blog-designer-pack' ) . '<label> [?]</label>',
										)
									)
			),

			// Filter Settings
			'filter' => array(
					'title'		=> __('Filter', 'blog-designer-pack'),
					'premium'	=> true,
					'params'	=>  array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Enable Filter', 'blog-designer-pack' ),
											'name' 			=> 'filter',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Enable category filter.', 'blog-designer-pack' ),
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Filter Design', 'blog-designer-pack' ),
											'name' 		=> 'filter_design',
											'value' 	=> array( 
																	'design-1'	=> __( 'Design 1', 'blog-designer-pack' ),
															),
											'desc' 		=> __( 'Choose filter design.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Filter All Text', 'blog-designer-pack' ),
											'name' 			=> 'filter_all_text',
											'value' 		=> __( 'All', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter `ALL` field text. Leave it empty to remove it.', 'blog-designer-pack' ),
											'allow_empty'	=> true,
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Filter More Text', 'blog-designer-pack' ),
											'name' 			=> 'filter_more_text',
											'value' 		=> __( 'More', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter filter `More` field text. This will be displayed when the category filter is wider than screen.', 'blog-designer-pack' ),
											'allow_empty'	=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Position', 'blog-designer-pack' ),
											'name' 			=> 'filter_position',
											'value' 		=> array( 
																	'top'		=> __( 'Top', 'blog-designer-pack' ),
																	'left'		=> __( 'Left', 'blog-designer-pack' ),
																	'right'		=> __( 'Right', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose filter position.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Alignment', 'blog-designer-pack' ),
											'name' 			=> 'filter_align',
											'value' 		=> array( 
																	'right'		=> __( 'Right', 'blog-designer-pack' ),
																	'left'		=> __( 'Left', 'blog-designer-pack' ),
																	'center'	=> __( 'Center', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose filter alignment.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Filter Responsive Screen', 'blog-designer-pack' ),
											'name' 			=> 'filter_res_screen',
											'value' 		=> 768,
											'desc' 			=> __( 'Enter filter responsive screen. Filter will be on top position below this screen resolution.', 'blog-designer-pack' ),											
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Total Number of Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_limit',
											'value' 		=> 10,
											'desc' 			=> __( 'Enter number of categories to display at a time. Enter 0 to display all.', 'blog-designer-pack' ),											
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Categories Order By', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_orderby',
											'value' 		=>  array(
																	'name' 			=> __( 'Category Name', 'blog-designer-pack' ),
																	'slug' 			=> __( 'Category Slug', 'blog-designer-pack' ),
																	'term_group' 	=> __( 'Category Group', 'blog-designer-pack' ),
																	'term_id' 		=> __( 'Category ID', 'blog-designer-pack' ),
																	'id' 			=> __( 'ID', 'blog-designer-pack' ),
																	'description' 	=> __( 'Category Description', 'blog-designer-pack' ),
																	'parent'		=> __( 'Category Parent', 'blog-designer-pack' ),
																	'term_order'	=> __( 'Category Order', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select filter category order type.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Categories Order', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_order',
											'value' 		=> array(
																	'asc'	=> __( 'Ascending', 'blog-designer-pack' ),
																	'desc'	=> __( 'Descending', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select filter category sorting order.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Child of Category', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_child_of',
											'value' 		=> array(
																	'' => __('Select Category', 'blog-designer-pack')
																),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Select term id to retrieve child terms of.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Parent Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_parent',
											'value' 		=> array(
																	'' => __('Select Category', 'blog-designer-pack')
																),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Select parent term id to retrieve direct child terms of. Add 0 to display only parent categories.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Active Filter Category', 'blog-designer-pack' ),
											'name' 			=> 'filter_active',
											'value' 		=> '',
											'desc' 			=> __( 'Choose active category. Enter number starting form 1 OR category ID like cat-ID. Default first will be active.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Allow Multiple Filter Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_allow_multiple',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Allow multiple filter category selection at a time.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Reload Filter', 'blog-designer-pack' ),
											'name' 			=> 'filter_reload',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Reload page on filter category selection.', 'blog-designer-pack' ),
										),
									)
			),

			// Style Manager
			'style_manager' => array(
					'title'		=> __('Style Manager', 'blog-designer-pack'),
					'premium'	=> true,
					'params'	=> array(
										array(
											'type' 		=> 'dropdown',
											'name'		=> 'style_id',
											'value' 	=> array('' => __('Choose Style', 'blog-designer-pack')),
											'desc' 		=> __( 'Choose your created style from style manager or create a new one.', 'blog-designer-pack' ),
										)
									)
								)
	);
	return $fields;
}

/**
 * Generate 'bdp_post_carousel' shortcode fields
 * 
 * @since 1.0
 */
function bdp_post_carousel_lite_shortcode_fields( $shortcode = '' ) {
	$fields = array(
			// General Settings
			'general' => array(
					'title'		=> __('General & Designs', 'blog-designer-pack'),
					'params'   	=>  array(
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Design', 'blog-designer-pack' ),
											'name' 		=> 'design',
											'value' 	=> bdp_post_carousel_designs(),
											'desc'		=> __( 'Choose layout design.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Image Size', 'blog-designer-pack' ),
											'name' 			=> 'media_size',
											'value' 		=> 'bdpp-medium',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Choose WordPress registered image size. e.g.', 'blog-designer-pack' ).' bdpp-medium, thumbnail, medium, large, full.',
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'CSS Class', 'blog-designer-pack' ),
											'name' 			=> 'css_class',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Enter an extra CSS class for design customization.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Extra class will be added at top most parent so using extra class you customize your design.', 'blog-designer-pack').'"> [?]</label>',
										),
									)
			),

			// Slider Fields
			'slider' => array(
					'title'		=> __('Slider', 'blog-designer-pack'),
					'params'    => array(
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Slides Column', 'blog-designer-pack' ),
											'name' 			=> 'slide_show',
											'value' 		=> 3,
											'desc' 			=> __( 'Enter number of slides to show.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Slides to Scroll', 'blog-designer-pack' ),
											'name' 			=> 'slide_scroll',
											'value' 		=> 1,
											'desc' 			=> __( 'Enter number of slides to scroll at a time.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Loop', 'blog-designer-pack' ),
											'name' 			=> 'loop',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Enable slider loop.', 'blog-designer-pack' ),
										),
										array(
											'type'		=> 'dropdown',
											'heading' 	=> __( 'Show Arrows', 'blog-designer-pack' ),
											'name' 		=> 'arrows',
											'value' 	=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc'		=> __( 'Show prev - next arrows.', 'blog-designer-pack' ),
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Show Dots', 'blog-designer-pack' ),
											'name' 		=> 'dots',
											'value' 	=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 		=> __( 'Show pagination dots.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Autoplay', 'blog-designer-pack' ),
											'name' 			=> 'autoplay',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Enable slider autoplay.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Autoplay Interval', 'blog-designer-pack' ),
											'name' 			=> 'autoplay_interval',
											'value' 		=> 5000,
											'desc' 			=> __( 'Enter autoplay interval.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																'element' 	=> 'autoplay',
																'value' 	=> array( 'true' ),
															),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Speed', 'blog-designer-pack' ),
											'name' 			=> 'speed',
											'value' 		=> 500,
											'desc' 			=> __( 'Enter slider speed.', 'blog-designer-pack' ),
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more Slider options like Center Mode, Slide Margin etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Previous Button Text', 'blog-designer-pack' ),
											'name' 			=> 'prev_text',
											'value' 		=> '',
											'desc' 			=> __( 'Slider previous button text.', 'blog-designer-pack' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Next Button Text', 'blog-designer-pack' ),
											'name' 			=> 'next_text',
											'value' 		=> '',
											'desc' 			=> __( 'Slider next button text.', 'blog-designer-pack' ),
											'premium'		=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Autoplay Pause on Hover', 'blog-designer-pack' ),
											'name' 			=> 'autoplay_hover_pause',
											'premium'		=> true,
											'value' 		=> array(
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Autoplay pause on hover.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Slider Center Mode', 'blog-designer-pack' ),
											'name' 			=> 'center',
											'premium'		=> true,
											'value' 		=> array(
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Enable slider center mode.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Slider Auto Height', 'blog-designer-pack' ),
											'name' 			=> 'auto_height',
											'premium'		=> true,
											'value' 		=> array( 
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Enable slider auto height.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Slider Start Position', 'blog-designer-pack' ),
											'name' 			=> 'start_position',
											'premium'		=> true,
											'value' 		=> '',
											'desc' 			=> __( 'Enter slide number to start from that.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Slide Margin', 'blog-designer-pack' ),
											'name' 			=> 'slide_margin',
											'premium'		=> true,
											'value' 		=> 5,
											'desc' 			=> __( 'Slide margin.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Slider Stage Padding', 'blog-designer-pack' ),
											'name' 			=> 'stage_padding',
											'premium'		=> true,
											'value' 		=> '',
											'desc' 			=> __( 'Enter slider stage padding. A partial slide will be visible at both the end.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'URL Hash Listner', 'blog-designer-pack' ),
											'name' 			=> 'url_hash_listener',
											'premium'		=> true,
											'value' 		=> array(
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Enable url hash listner of slider.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Lazyload', 'blog-designer-pack' ),
											'name' 			=> 'lazyload',
											'premium'		=> true,
											'value' 		=> array(
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Enable slider lazyload behaviour.', 'blog-designer-pack' ),
										),
								)
			),

			// Meta Fields
			'meta' => array(
					'title'     => __('Meta & Content', 'blog-designer-pack'),
					'params'   	=>  array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Post Date', 'blog-designer-pack' ),
											'name' 			=> 'show_date',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post date.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Author', 'blog-designer-pack' ),
											'name' 			=> 'show_author',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post author.', 'blog-designer-pack' ),
										),										
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Tags', 'blog-designer-pack' ),
											'name' 			=> 'show_tags',
											'value' 		=> array( 
																	'false'		=> __( 'False', 'blog-designer-pack' ),
																	'true'		=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post tags.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Comments Count', 'blog-designer-pack' ),
											'name' 			=> 'show_comments',
											'value' 		=> array(
																	'true'		=> __( 'True', 'blog-designer-pack' ),
																	'false'		=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post comment count.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Category', 'blog-designer-pack' ),
											'name' 			=> 'show_category',
											'value' 		=> array( 
																	'true'		=> __( 'True', 'blog-designer-pack' ),
																	'false'		=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post category.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Content', 'blog-designer-pack' ),
											'name' 			=> 'show_content',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post content.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Content Word Limit', 'blog-designer-pack' ),
											'name' 			=> 'content_words_limit',
											'value' 		=> 20,
											'desc' 			=> __( 'Enter content word limit.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'show_content',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Read More', 'blog-designer-pack' ),
											'name' 			=> 'show_read_more',
											'value' 		=> array(
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Show read more.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'show_content',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Read More Text', 'blog-designer-pack' ),
											'name' 			=> 'read_more_text',
											'value' 		=> __( 'Read More', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter read more text.', 'blog-designer-pack' ),
											'refresh_time'	=> 1000,
											'dependency' 	=> array(
																	'element' 	=> 'show_read_more',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more Meta & Content options like Read More Text etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Sub Title', 'blog-designer-pack' ),
											'name' 			=> 'show_sub_title',
											'premium'		=> true,
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display sub title or not.', 'blog-designer-pack' ) . '<label title="'.esc_attr__("Sub title can be added via 'Blog Designer Pack Pro - Settings' metabox from Post add / edit screen.", 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Post Link Target', 'blog-designer-pack' ),
											'name'		=> 'link_behaviour',
											'premium'		=> true,
											'value' 	=> array(
																'self'	=> __( 'Same Tab', 'blog-designer-pack' ),
																'new'	=> __( 'New Tab', 'blog-designer-pack' ),
															),
											'desc'		=> __( 'Choose post link behaviour.', 'blog-designer-pack' ),
										),
									)
			),			

			// Data Fields
			'query' => array(
					'title'		=> __('Query', 'blog-designer-pack'),
					'params'	=> array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Type', 'blog-designer-pack' ),
											'name' 			=> 'post_type',
											'value' 		=> bdp_get_supported_post_types(),
											'class'			=> 'bdpp-post-type-sel',
											'ajax'			=> true,
											'desc' 			=> sprintf( __( 'Choose registered post type. You can enable it from plugin %ssetting%s page.', 'blog-designer-pack' ), '<a href="'.esc_url( BDP_SETTING_PAGE_URL ).'" target="_black">', '</a>' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Total Number of Post', 'blog-designer-pack' ),
											'name' 			=> 'limit',
											'value' 		=> 20,
											'min'			=> -1,
											'validation'	=> 'number',
											'desc' 			=> __( 'Enter total number of post to be displayed. Enter -1 to display all.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include By Category', 'blog-designer-pack' ),
											'name' 			=> 'category',
											'value' 		=> '',
											'class'			=> 'bdpp-ajax-select2 bdpp-category-sel',
											'multi'			=> true,
											'ajax'			=> true,
											'ajax_action'	=> 'bdpp_category_sugg',
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose categories to display category wise posts.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Order By', 'blog-designer-pack' ),
											'name' 			=> 'orderby',
											'value' 		=>  array(
																	'date' 			=> __( 'Post Date', 'blog-designer-pack' ),
																	'ID' 			=> __( 'Post ID', 'blog-designer-pack' ),
																	'author' 		=> __( 'Post Author', 'blog-designer-pack' ),
																	'title' 		=> __( 'Post Title', 'blog-designer-pack' ),
																	'name' 			=> __( 'Post Slug', 'blog-designer-pack' ),
																	'modified' 		=> __( 'Post Modified Date', 'blog-designer-pack' ),
																	'menu_order'	=> __( 'Menu Order', 'blog-designer-pack' ),
																	'parent'		=> __( 'Parent ID', 'blog-designer-pack' ),
																	'rand' 			=> __( 'Random', 'blog-designer-pack' ),
																	'comment_count'	=> __( 'Number of Comments', 'blog-designer-pack' ),
																	'relevance'		=> __( 'Relevance', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select order type.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Order', 'blog-designer-pack' ),
											'name' 			=> 'order',
											'value' 		=> array(
																	'desc'	=> __( 'Descending', 'blog-designer-pack' ),
																	'asc'	=> __( 'Ascending', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select sorting order.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Custom Parameter 1', 'blog-designer-pack' ),
											'name' 			=> 'custom_param_1',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Give your Query a custom unique parameter to allow server side filtering.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: You can customize the plugin query via Hooks and Filters with the help of this parameter.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Custom Parameter 2', 'blog-designer-pack' ),
											'name' 			=> 'custom_param_2',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Give your Query a custom unique parameter to allow server side filtering.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: You can customize the plugin query via Hooks and Filters with the help of this parameter.', 'blog-designer-pack').'"> [?]</label>',
										),										
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more Query options like Cat Taxonomy, Exclude By Category etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'taxonomy',
											'premium'		=> true,
											'value' 		=> bdp_get_post_type_taxonomy( BDP_POST_TYPE ),
											'class'			=> 'bdpp-taxonomy-sel',
											'desc' 			=> __( 'Choose registered taxonomy if you want to display category wise post.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Cat Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'cat_taxonomy',
											'premium'		=> true,
											'class'			=> 'bdpp-cat-taxonomy-sel',
											'value' 		=> array( '' => __('Select Taxonomy', 'blog-designer-pack') ),
											'desc' 			=> __( 'Choose a category taxonomy just to display categories as meta information.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Tag Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'tag_taxonomy',
											'premium'		=> true,
											'class'			=> 'bdpp-tag-taxonomy-sel',
											'value' 		=> array( '' => __('Select Taxonomy', 'blog-designer-pack') ),
											'desc' 			=> __( 'Choose a tag taxonomy just to display tags as meta information.', 'blog-designer-pack' ),
										),
										array(
											'type'			=> 'dropdown',
											'class'			=> '',
											'heading'		=> __( 'Category Operator', 'blog-designer-pack'),
											'name'			=> 'category_operator',
											'premium'		=> true,
											'value'			=> array( 
																	'IN'	=> __( 'IN', 'blog-designer-pack' ),
																	'AND'	=> __( 'AND', 'blog-designer-pack' ),
																),
											'desc'			=> __( 'Select category operator. Default value is IN', 'blog-designer-pack' ),
										),
										array(
											'type'			=> 'dropdown',
											'class'			=> '',
											'heading'		=> __( 'Display Child Category Posts', 'blog-designer-pack'),
											'name'			=> 'include_cat_child',
											'premium'		=> true,
											'value'			=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc'			=> __( 'Whether or not to include children category posts if parent category is there.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude By Category', 'blog-designer-pack' ),
											'name' 			=> 'exclude_cat',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose categories to exclude posts of it. Works only if `Category` field is empty.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include Post', 'blog-designer-pack' ),
											'name' 			=> 'posts',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search posts by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __('Choose posts which you want to display.', 'blog-designer-pack'),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude Post', 'blog-designer-pack' ),
											'name' 			=> 'hide_post',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search posts by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __('Choose posts which you do not want to display.', 'blog-designer-pack'),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include By Author', 'blog-designer-pack' ),
											'name' 			=> 'author',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search authors by its name, email or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose authors to show posts associated with that.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude By Author', 'blog-designer-pack' ),
											'name' 			=> 'exclude_author',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search authors by its name, email or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose authors to hide posts associated with that. Works only if `Include Author` field is empty.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Sticky Posts', 'blog-designer-pack' ),
											'name' 			=> 'sticky_posts',
											'premium'		=> true,
											'value' 		=> array(
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display sticky posts. This only effects the frontend.', 'blog-designer-pack' ) . '<label title="'.esc_attr__("Note: Sticky post only be displayed at front side. In preview mode sticky post will not be displayed.", 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Type', 'blog-designer-pack' ),
											'name' 			=> 'type',
											'premium'		=> true,
											'value' 		=> array(
																	'' 			=> __( 'Select Type', 'blog-designer-pack' ),
																	'featured'	=> __( 'Featured', 'blog-designer-pack' ),
																	'trending'	=> __( 'Trending', 'blog-designer-pack'),
																),
											'desc' 			=> __( 'Select display type of post. Is it Featured or Trending?', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: For trending post type make sure you have enabled the post type from Plugin Settings > Trending Post.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Query Offset', 'blog-designer-pack' ),
											'name' 			=> 'query_offset',
											'premium'		=> true,
											'value' 		=> '',
											'desc' 			=> __( 'Skip number of posts from starting.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('e.g. 5 to skip over 5 posts. Note: Do not use limit=-1 and pagination=true with this.', 'blog-designer-pack').'"> [?]</label>',
										),
										
									)
			),
			
			// Social Sharing
			'social_sharing' => array(
					'title'		=> __('Social Sharing', 'blog-designer-pack'),
					'premium'	=> true,
					'params'	=> array(
										array(
											'type' 	=> 'dropdown',
											'name' 	=> 'sharing',
											'value' => array('' => __('No Social Sharing', 'blog-designer-pack') ),
											'desc' 	=> __( 'Enable social sharing. You can enable it from plugin setting page.', 'blog-designer-pack' ) . '<label> [?]</label>',
										)
									)
			),
			
			// Filter Settings
			'filter' => array(
					'title'		=> __('Filter', 'blog-designer-pack'),
					'premium'	=> true,
					'params'	=>  array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Enable Filter', 'blog-designer-pack' ),
											'name' 			=> 'filter',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Enable category filter.', 'blog-designer-pack' ),
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Filter Design', 'blog-designer-pack' ),
											'name' 		=> 'filter_design',
											'value' 	=> array( 
																'design-1'	=> __( 'Design 1', 'blog-designer-pack' ),
															),
											'desc' 		=> __( 'Choose filter design.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Filter All Text', 'blog-designer-pack' ),
											'name' 			=> 'filter_all_text',
											'value' 		=> __( 'All', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter `ALL` field text. Leave it empty to remove it.', 'blog-designer-pack' ),
											'allow_empty'	=> true,
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Filter More Text', 'blog-designer-pack' ),
											'name' 			=> 'filter_more_text',
											'value' 		=> __( 'More', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter filter `More` field text. This will be displayed when the category filter is wider than screen.', 'blog-designer-pack' ),
											'allow_empty'	=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Position', 'blog-designer-pack' ),
											'name' 			=> 'filter_position',
											'value' 		=> array( 
																	'top'		=> __( 'Top', 'blog-designer-pack' ),
																	'left'		=> __( 'Left', 'blog-designer-pack' ),
																	'right'		=> __( 'Right', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose filter position.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Alignment', 'blog-designer-pack' ),
											'name' 			=> 'filter_align',
											'value' 		=> array( 
																	'right'		=> __( 'Right', 'blog-designer-pack' ),
																	'left'		=> __( 'Left', 'blog-designer-pack' ),
																	'center'	=> __( 'Center', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose filter alignment.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Filter Responsive Screen', 'blog-designer-pack' ),
											'name' 			=> 'filter_res_screen',
											'value' 		=> 768,
											'desc' 			=> __( 'Enter filter responsive screen. Filter will be on top position below this screen resolution.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Total Number of Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_limit',
											'value' 		=> 10,
											'desc' 			=> __( 'Enter number of categories to display at a time. Enter 0 to display all.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Categories Order By', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_orderby',
											'value' 		=>  array(
																	'name' 			=> __( 'Category Name', 'blog-designer-pack' ),
																	'slug' 			=> __( 'Category Slug', 'blog-designer-pack' ),
																	'term_group' 	=> __( 'Category Group', 'blog-designer-pack' ),
																	'term_id' 		=> __( 'Category ID', 'blog-designer-pack' ),
																	'id' 			=> __( 'ID', 'blog-designer-pack' ),
																	'description' 	=> __( 'Category Description', 'blog-designer-pack' ),
																	'parent'		=> __( 'Category Parent', 'blog-designer-pack' ),
																	'term_order'	=> __( 'Category Order', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select filter category order type.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Categories Order', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_order',
											'value' 		=> array(
																	'asc'	=> __( 'Ascending', 'blog-designer-pack' ),
																	'desc'	=> __( 'Descending', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select filter category sorting order.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Child of Category', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_child_of',
											'value' 		=> array(
																	'' => __('Select Category', 'blog-designer-pack')
																),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Select term id to retrieve child terms of.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Parent Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_parent',
											'value' 		=> array(
																	'' => __('Select Category', 'blog-designer-pack')
																),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Select parent term id to retrieve direct child terms of. Add 0 to display only parent categories.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Active Filter Category', 'blog-designer-pack' ),
											'name' 			=> 'filter_active',
											'value' 		=> '',
											'desc' 			=> __( 'Choose active category. Enter number starting form 1 OR category ID like cat-ID. Default first will be active.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Allow Multiple Filter Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_allow_multiple',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Allow multiple filter category selection at a time.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Reload Filter', 'blog-designer-pack' ),
											'name' 			=> 'filter_reload',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Reload page on filter category selection.', 'blog-designer-pack' ),
										),
									)
			),

			// Style Manager
			'style_manager' => array(
					'title'		=> __('Style Manager', 'blog-designer-pack'),
					'premium'	=> true,
					'params'	=> array(
										array(
											'type' 		=> 'dropdown',
											'name'		=> 'style_id',
											'value' 	=> array('' => __('Choose Style', 'blog-designer-pack')),
											'desc' 		=> __( 'Choose your created style from style manager or create a new one.', 'blog-designer-pack' ),
										)
									)
								)
	);
	return $fields;
}

/**
 * Generate 'bdp_post_gridbox' shortcode fields
 * 
 * @since 1.0
 */
function bdp_post_gridbox_lite_shortcode_fields( $shortcode = '' ) {
	$fields = array(
			// General Settings
			'general' => array(
					'title'		=> __('General & Designs', 'blog-designer-pack'),
					'params'	=>  array(
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Design', 'blog-designer-pack' ),
											'name' 		=> 'design',
											'value' 	=> bdp_post_gridbox_designs(),
											'desc' 		=> __( 'Choose layout design.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'CSS Class', 'blog-designer-pack' ),
											'name' 			=> 'css_class',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Enter an extra CSS class for design customization.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Extra class will be added at top most parent so using extra class you customize your design.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock Height option.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Height', 'blog-designer-pack' ),
											'name' 			=> 'height',
											'premium'		=> true,
											'value' 		=> '',
											'desc' 			=> __( 'Enter post image or box height. Leave empty for default.', 'blog-designer-pack' ),
										),
									)
			),

			// Meta Fields
			'meta' => array(
					'title'		=> __('Meta & Content', 'blog-designer-pack'),
					'params'	=>  array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Post Date', 'blog-designer-pack' ),
											'name' 			=> 'show_date',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post date.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Author', 'blog-designer-pack' ),
											'name' 			=> 'show_author',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post author.', 'blog-designer-pack' ),
										),										
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Tags', 'blog-designer-pack' ),
											'name' 			=> 'show_tags',
											'value' 		=> array( 
																	'false'		=> __( 'False', 'blog-designer-pack' ),
																	'true'		=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post tags.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Comments Count', 'blog-designer-pack' ),
											'name' 			=> 'show_comments',
											'value' 		=> array(
																	'true'		=> __( 'True', 'blog-designer-pack' ),
																	'false'		=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post comment count.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Category', 'blog-designer-pack' ),
											'name' 			=> 'show_category',
											'value' 		=> array( 
																	'true'		=> __( 'True', 'blog-designer-pack' ),
																	'false'		=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post category.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Content', 'blog-designer-pack' ),
											'name' 			=> 'show_content',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post content.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Content Word Limit', 'blog-designer-pack' ),
											'name' 			=> 'content_words_limit',
											'value' 		=> 20,
											'desc' 			=> __( 'Enter content word limit.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'show_content',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Read More', 'blog-designer-pack' ),
											'name' 			=> 'show_read_more',
											'value' 		=> array(
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Show read more.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'show_content',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Read More Text', 'blog-designer-pack' ),
											'name' 			=> 'read_more_text',
											'value' 		=> __( 'Read More', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter read more text.', 'blog-designer-pack' ),
											'refresh_time'	=> 1000,
											'dependency' 	=> array(
																	'element' 	=> 'show_read_more',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more Meta & Content options like Read More Text, Sub Title etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Sub Title', 'blog-designer-pack' ),
											'name' 			=> 'show_sub_title',
											'premium'		=> true,
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display sub title or not.', 'blog-designer-pack' ) . '<label title="'.esc_attr__("Sub title can be added via 'Blog Designer Pack Pro - Settings' metabox from Post add / edit screen.", 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Post Link Target', 'blog-designer-pack' ),
											'name'		=> 'link_behaviour',
											'premium'		=> true,
											'value' 	=> array(
																'self'	=> __( 'Same Tab', 'blog-designer-pack' ),
																'new'	=> __( 'New Tab', 'blog-designer-pack' ),
															),
											'desc'		=> __( 'Choose post link behaviour.', 'blog-designer-pack' ),
										),
									)
			),			

			// Data Fields
			'query' => array(
					'title'		=> __('Query', 'blog-designer-pack'),
					'params'	=> array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Type', 'blog-designer-pack' ),
											'name' 			=> 'post_type',
											'value' 		=> bdp_get_supported_post_types(),
											'class'			=> 'bdpp-post-type-sel',
											'ajax'			=> true,
											'desc' 			=> sprintf( __( 'Choose registered post type. You can enable it from plugin %ssetting%s page.', 'blog-designer-pack' ), '<a href="'.esc_url( BDP_SETTING_PAGE_URL ).'" target="_black">', '</a>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Order By', 'blog-designer-pack' ),
											'name' 			=> 'orderby',
											'value' 		=>  array(
																	'date' 			=> __( 'Post Date', 'blog-designer-pack' ),
																	'ID' 			=> __( 'Post ID', 'blog-designer-pack' ),
																	'author' 		=> __( 'Post Author', 'blog-designer-pack' ),
																	'title' 		=> __( 'Post Title', 'blog-designer-pack' ),
																	'name' 			=> __( 'Post Slug', 'blog-designer-pack' ),
																	'modified' 		=> __( 'Post Modified Date', 'blog-designer-pack' ),
																	'menu_order'	=> __( 'Menu Order', 'blog-designer-pack' ),
																	'parent'		=> __( 'Parent ID', 'blog-designer-pack' ),
																	'rand' 			=> __( 'Random', 'blog-designer-pack' ),
																	'comment_count'	=> __( 'Number of Comments', 'blog-designer-pack' ),
																	'relevance'		=> __( 'Relevance', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select order type.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Order', 'blog-designer-pack' ),
											'name' 			=> 'order',
											'value' 		=> array(
																	'desc'	=> __( 'Descending', 'blog-designer-pack' ),
																	'asc'	=> __( 'Ascending', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select sorting order.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Custom Parameter 1', 'blog-designer-pack' ),
											'name' 			=> 'custom_param_1',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Give your Query a custom unique parameter to allow server side filtering.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: You can customize the plugin query via Hooks and Filters with the help of this parameter.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Custom Parameter 2', 'blog-designer-pack' ),
											'name' 			=> 'custom_param_2',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Give your Query a custom unique parameter to allow server side filtering.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: You can customize the plugin query via Hooks and Filters with the help of this parameter.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more Query options like Cat Taxonomy, Exclude By Category etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'taxonomy',
											'premium'		=> true,
											'value' 		=> bdp_get_post_type_taxonomy( BDP_POST_TYPE ),
											'class'			=> 'bdpp-taxonomy-sel',
											'desc' 			=> __( 'Choose registered taxonomy if you want to display category wise post.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Cat Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'cat_taxonomy',
											'premium'		=> true,
											'class'			=> 'bdpp-cat-taxonomy-sel',
											'value' 		=> array( '' => __('Select Taxonomy', 'blog-designer-pack') ),
											'desc' 			=> __( 'Choose a category taxonomy just to display categories as meta information.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Tag Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'tag_taxonomy',
											'class'			=> 'bdpp-tag-taxonomy-sel',
											'premium'		=> true,
											'value' 		=> array( '' => __('Select Taxonomy', 'blog-designer-pack') ),
											'desc' 			=> __( 'Choose a tag taxonomy just to display tags as meta information.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include By Category', 'blog-designer-pack' ),
											'name' 			=> 'category',
											'value' 		=> '',
											'class'			=> 'bdpp-ajax-select2 bdpp-category-sel',
											'multi'			=> true,
											'ajax'			=> true,
											'premium'		=> true,
											'ajax_action'	=> 'bdpp_category_sugg',
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose categories to display category wise posts.', 'blog-designer-pack' ),
										),
										array(
											'type'			=> 'dropdown',
											'class'			=> '',
											'heading'		=> __( 'Category Operator', 'blog-designer-pack'),
											'name'			=> 'category_operator',
											'premium'		=> true,
											'value'			=> array( 
																	'IN'	=> __( 'IN', 'blog-designer-pack' ),
																	'AND'	=> __( 'AND', 'blog-designer-pack' ),
																),
											'desc'			=> __( 'Select category operator. Default value is IN', 'blog-designer-pack' ),
										),
										array(
											'type'			=> 'dropdown',
											'class'			=> '',
											'premium'		=> true,
											'heading'		=> __( 'Display Child Category Posts', 'blog-designer-pack'),
											'name'			=> 'include_cat_child',
											'value'			=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc'			=> __( 'Whether or not to include children category posts if parent category is there.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude By Category', 'blog-designer-pack' ),
											'name' 			=> 'exclude_cat',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose categories to exclude posts of it. Works only if `Category` field is empty.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include Post', 'blog-designer-pack' ),
											'name' 			=> 'posts',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search posts by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __('Choose posts which you want to display.', 'blog-designer-pack'),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude Post', 'blog-designer-pack' ),
											'name' 			=> 'hide_post',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search posts by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __('Choose posts which you do not want to display.', 'blog-designer-pack'),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include By Author', 'blog-designer-pack' ),
											'name' 			=> 'author',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search authors by its name, email or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose authors to show posts associated with that.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude By Author', 'blog-designer-pack' ),
											'name' 			=> 'exclude_author',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search authors by its name, email or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose authors to hide posts associated with that. Works only if `Include Author` field is empty.', 'blog-designer-pack' ),
										),
										
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Sticky Posts', 'blog-designer-pack' ),
											'name' 			=> 'sticky_posts',
											'premium'		=> true,
											'value' 		=> array(
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display sticky posts. This only effects the frontend.', 'blog-designer-pack' ) . '<label title="'.esc_attr__("Note: Sticky post only be displayed at front side. In preview mode sticky post will not be displayed.", 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Type', 'blog-designer-pack' ),
											'name' 			=> 'type',
											'premium'		=> true,
											'value' 		=> array(
																	'' 			=> __( 'Select Type', 'blog-designer-pack' ),
																	'featured'	=> __( 'Featured', 'blog-designer-pack' ),
																	'trending'	=> __( 'Trending', 'blog-designer-pack'),
																),
											'desc' 			=> __( 'Select display type of post. Is it Featured or Trending?', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: For trending post type make sure you have enabled the post type from Plugin Settings > Trending Post.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Query Offset', 'blog-designer-pack' ),
											'name' 			=> 'query_offset',
											'premium'		=> true,
											'value' 		=> '',
											'desc' 			=> __( 'Skip number of posts from starting.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('e.g. 5 to skip over 5 posts. Note: Do not use limit=-1 and pagination=true with this.', 'blog-designer-pack').'"> [?]</label>',
										),
									)
			),

			// Data Fields
			'pagination' => array(
					'title'		=> __('Pagination', 'blog-designer-pack'),
					'params'	=> array(
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Total Number of Post', 'blog-designer-pack' ),
											'name' 			=> 'limit',
											'value' 		=> 20,
											'min'			=> -1,
											'desc' 			=> __( 'Enter total number of post to be displayed. Enter -1 to display all.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Pagination', 'blog-designer-pack' ),
											'name' 			=> 'pagination',
											'value' 		=> array( 
																'true'	=> __( 'True', 'blog-designer-pack' ),
																'false'	=> __( 'False', 'blog-designer-pack' ),
															),
											'dependency' 	=> array(
																		'element' 				=> 'limit',
																		'value_not_equal_to' 	=> '-1',
																	),
											'desc' 			=> __( 'Display Pagination.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Pagination Type', 'blog-designer-pack' ),
											'name' 			=> 'pagination_type',
											'value' 		=> array(
																	'numeric'					=> __( 'Numeric', 'blog-designer-pack' ),
																	'numeric-ajax|disabled'		=> __( 'Numeric Ajax', 'blog-designer-pack' ),
																	'prev-next|disabled'		=> __( 'Next - Prev', 'blog-designer-pack' ),
																	'prev-next-ajax|disabled'	=> __( 'Next - Prev Ajax', 'blog-designer-pack' ),
																	'load-more|disabled'		=> __( 'Load More', 'blog-designer-pack' ),
																	'infinite|disabled'			=> __( 'Infinite Scroll', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose pagination type.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																'element' 				=> 'pagination',
																'value_not_equal_to' 	=> array( 'false' ),
															),
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more pagination type like Load More, Infinite Scroll etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Previous Button Text', 'blog-designer-pack' ),
											'name' 			=> 'prev_text',
											'value' 		=> '',
											'premium'		=> true,
											'desc' 			=> __( 'Pagination previous button text. Leave it empty for default.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Next Button Text', 'blog-designer-pack' ),
											'name' 			=> 'next_text',
											'value' 		=> '',
											'premium'		=> true,
											'desc' 			=> __( 'Pagination next button text. Leave it empty for default.', 'blog-designer-pack' ),
										)
									)
			),
			
			// Social Sharing
			'social_sharing' => array(
					'title'		=> __('Social Sharing', 'blog-designer-pack'),
					'premium'	=> true,
					'params'	=> array(
										array(
											'type' 	=> 'dropdown',
											'name' 	=> 'sharing',
											'value' => array('' => __('No Social Sharing', 'blog-designer-pack') ),
											'desc' 	=> __( 'Enable social sharing. You can enable it from plugin setting page.', 'blog-designer-pack' ) . '<label> [?]</label>',
										)
									)
			),

			// Filter Settings
			'filter' => array(
					'title'		=> __('Filter', 'blog-designer-pack'),
					'premium'	=> true,
					'params'	=>  array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Enable Filter', 'blog-designer-pack' ),
											'name' 			=> 'filter',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	
																),
											'desc' 			=> __( 'Enable category filter.', 'blog-designer-pack' ),
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Filter Design', 'blog-designer-pack' ),
											'name' 		=> 'filter_design',
											'value' 	=> array( 
																'design-1'	=> __( 'Design 1', 'blog-designer-pack' ),
															),
											'desc' 		=> __( 'Choose filter design.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'filter',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Filter All Text', 'blog-designer-pack' ),
											'name' 			=> 'filter_all_text',
											'value' 		=> __( 'All', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter `ALL` field text. Leave it empty to remove it.', 'blog-designer-pack' ),
											'allow_empty'	=> true,
											'dependency' 	=> array(
																	'element' 	=> 'filter',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Filter More Text', 'blog-designer-pack' ),
											'name' 			=> 'filter_more_text',
											'value' 		=> __( 'More', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter filter `More` field text. This will be displayed when the category filter is wider than screen.', 'blog-designer-pack' ),
											'allow_empty'	=> true,
											'dependency' 	=> array(
																	'element' 	=> 'filter',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Position', 'blog-designer-pack' ),
											'name' 			=> 'filter_position',
											'value' 		=> array( 
																	'top'		=> __( 'Top', 'blog-designer-pack' ),
																	'left'		=> __( 'Left', 'blog-designer-pack' ),
																	'right'		=> __( 'Right', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose filter position.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'filter',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Alignment', 'blog-designer-pack' ),
											'name' 			=> 'filter_align',
											'value' 		=> array( 
																	'right'		=> __( 'Right', 'blog-designer-pack' ),
																	'left'		=> __( 'Left', 'blog-designer-pack' ),
																	'center'	=> __( 'Center', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose filter alignment.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'filter_position',
																	'value' 	=> array( 'top' ),
																),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Filter Responsive Screen', 'blog-designer-pack' ),
											'name' 			=> 'filter_res_screen',
											'value' 		=> 768,
											'desc' 			=> __( 'Enter filter responsive screen. Filter will be on top position below this screen resolution.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'filter_position',
																	'value' 	=> array( 'left', 'right' ),
																),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Total Number of Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_limit',
											'value' 		=> 10,
											'desc' 			=> __( 'Enter number of categories to display at a time. Enter 0 to display all.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'filter',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Categories Order By', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_orderby',
											'value' 		=>  array(
																	'name' 			=> __( 'Category Name', 'blog-designer-pack' ),
																	'slug' 			=> __( 'Category Slug', 'blog-designer-pack' ),
																	'term_group' 	=> __( 'Category Group', 'blog-designer-pack' ),
																	'term_id' 		=> __( 'Category ID', 'blog-designer-pack' ),
																	'id' 			=> __( 'ID', 'blog-designer-pack' ),
																	'description' 	=> __( 'Category Description', 'blog-designer-pack' ),
																	'parent'		=> __( 'Category Parent', 'blog-designer-pack' ),
																	'term_order'	=> __( 'Category Order', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select filter category order type.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'filter',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Categories Order', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_order',
											'value' 		=> array(
																	'asc'	=> __( 'Ascending', 'blog-designer-pack' ),
																	'desc'	=> __( 'Descending', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select filter category sorting order.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'filter',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Child of Category', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_child_of',
											'value' 		=> array(
																	'' => __('Select Category', 'blog-designer-pack')
																),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Select term id to retrieve child terms of.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'filter',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Parent Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_parent',
											'value' 		=> array(
																	'' => __('Select Category', 'blog-designer-pack')
																),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Select parent term id to retrieve direct child terms of. Add 0 to display only parent categories.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'filter',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Active Filter Category', 'blog-designer-pack' ),
											'name' 			=> 'filter_active',
											'value' 		=> '',
											'desc' 			=> __( 'Choose active category. Enter number starting form 1 OR category ID like cat-ID. Default first will be active.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'filter',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Allow Multiple Filter Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_allow_multiple',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Allow multiple filter category selection at a time.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'filter',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Reload Filter', 'blog-designer-pack' ),
											'name' 			=> 'filter_reload',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Reload page on filter category selection.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'filter',
																	'value' 	=> array( 'true' ),
																),
										),
									)
			),

			// Style Manager
			'style_manager' => array(
					'title'		=> __('Style Manager', 'blog-designer-pack'),
					'premium'	=> true,
					'params'	=> array(
										array(
											'type' 		=> 'dropdown',
											'name'		=> 'style_id',
											'value' 	=> array('' => __('Choose Style', 'blog-designer-pack')),
											'desc' 		=> __( 'Choose your created style from style manager or create a new one.', 'blog-designer-pack' ),
										)
									)
								)
		);
	return $fields;
}

/**
 * Generate 'bdp_post_list' shortcode fields
 * 
 * @since 1.0
 */
function bdp_post_list_lite_shortcode_fields( $shortcode = '' ) {
	$fields = array(
			// General Settings
			'general' => array(
					'title'		=> __('General & Designs', 'blog-designer-pack'),
					'params'	=>  array(
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Design', 'blog-designer-pack' ),
											'name' 		=> 'design',
											'value' 	=> bdp_post_list_designs(),
											'desc' 		=> __( 'Choose layout design.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Image Size', 'blog-designer-pack' ),
											'name' 			=> 'media_size',
											'value' 		=> 'bdpp-medium',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Choose WordPress registered image size. e.g.', 'blog-designer-pack' ).' bdpp-medium, thumbnail, medium, large, full.',
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'CSS Class', 'blog-designer-pack' ),
											'name' 			=> 'css_class',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Enter an extra CSS class for design customization.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Extra class will be added at top most parent so using extra class you customize your design.', 'blog-designer-pack').'"> [?]</label>',
										),
									)
			),

			// Meta Fields
			'meta' => array(
					'title'     => __('Meta & Content', 'blog-designer-pack'),
					'params'   	=>  array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Post Date', 'blog-designer-pack' ),
											'name' 			=> 'show_date',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post date.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Author', 'blog-designer-pack' ),
											'name' 			=> 'show_author',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post author.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Tags', 'blog-designer-pack' ),
											'name' 			=> 'show_tags',
											'value' 		=> array( 
																	'true'		=> __( 'True', 'blog-designer-pack' ),
																	'false'		=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post tags.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Comments Count', 'blog-designer-pack' ),
											'name' 			=> 'show_comments',
											'value' 		=> array(
																	'true'		=> __( 'True', 'blog-designer-pack' ),
																	'false'		=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post comment count.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Category', 'blog-designer-pack' ),
											'name' 			=> 'show_category',
											'value' 		=> array( 
																	'true'		=> __( 'True', 'blog-designer-pack' ),
																	'false'		=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post category.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Content', 'blog-designer-pack' ),
											'name' 			=> 'show_content',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post content.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Content Word Limit', 'blog-designer-pack' ),
											'name' 			=> 'content_words_limit',
											'value' 		=> 20,
											'desc' 			=> __( 'Enter content word limit.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'show_content',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Read More', 'blog-designer-pack' ),
											'name' 			=> 'show_read_more',
											'value' 		=> array(
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Show read more.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'show_content',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Read More Text', 'blog-designer-pack' ),
											'name' 			=> 'read_more_text',
											'value' 		=> __( 'Read More', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter read more text.', 'blog-designer-pack' ),
											'refresh_time'	=> 1000,
											'dependency' 	=> array(
																	'element' 	=> 'show_read_more',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more Meta & Content options like Read More Text etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Sub Title', 'blog-designer-pack' ),
											'name' 			=> 'show_sub_title',
											'premium'		=> true,
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display sub title or not.', 'blog-designer-pack' ) . '<label title="'.esc_attr__("Sub title can be added via 'Blog Designer Pack Pro - Settings' metabox from Post add / edit screen.", 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Post Link Target', 'blog-designer-pack' ),
											'name'		=> 'link_behaviour',
											'premium'		=> true,
											'value' 	=> array(
																'self'	=> __( 'Same Tab', 'blog-designer-pack' ),
																'new'	=> __( 'New Tab', 'blog-designer-pack' ),
															),
											'desc'		=> __( 'Choose post link behaviour.', 'blog-designer-pack' ),
										),
									)
			),			

			// Query
			'query' => array(
					'title'		=> __('Query', 'blog-designer-pack'),
					'params'	=> array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Type', 'blog-designer-pack' ),
											'name' 			=> 'post_type',
											'value' 		=> bdp_get_supported_post_types(),
											'class'			=> 'bdpp-post-type-sel',
											'ajax'			=> true,
											'desc' 			=> sprintf( __( 'Choose registered post type. You can enable it from plugin %ssetting%s page.', 'blog-designer-pack' ), '<a href="'.esc_url( BDP_SETTING_PAGE_URL ).'" target="_black">', '</a>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include By Category', 'blog-designer-pack' ),
											'name' 			=> 'category',
											'value' 		=> '',
											'class'			=> 'bdpp-ajax-select2 bdpp-category-sel',
											'multi'			=> true,
											'ajax'			=> true,
											'ajax_action'	=> 'bdpp_category_sugg',
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose categories to display category wise posts.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Order By', 'blog-designer-pack' ),
											'name' 			=> 'orderby',
											'value' 		=>  array(
																	'date' 			=> __( 'Post Date', 'blog-designer-pack' ),
																	'ID' 			=> __( 'Post ID', 'blog-designer-pack' ),
																	'author' 		=> __( 'Post Author', 'blog-designer-pack' ),
																	'title' 		=> __( 'Post Title', 'blog-designer-pack' ),
																	'name' 			=> __( 'Post Slug', 'blog-designer-pack' ),
																	'modified' 		=> __( 'Post Modified Date', 'blog-designer-pack' ),
																	'menu_order'	=> __( 'Menu Order', 'blog-designer-pack' ),
																	'parent'		=> __( 'Parent ID', 'blog-designer-pack' ),
																	'rand' 			=> __( 'Random', 'blog-designer-pack' ),
																	'comment_count'	=> __( 'Number of Comments', 'blog-designer-pack' ),
																	'relevance'		=> __( 'Relevance', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select order type.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Order', 'blog-designer-pack' ),
											'name' 			=> 'order',
											'value' 		=> array(
																	'desc'	=> __( 'Descending', 'blog-designer-pack' ),
																	'asc'	=> __( 'Ascending', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select sorting order.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Custom Parameter 1', 'blog-designer-pack' ),
											'name' 			=> 'custom_param_1',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Give your Query a custom unique parameter to allow server side filtering.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: You can customize the plugin query via Hooks and Filters with the help of this parameter.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Custom Parameter 2', 'blog-designer-pack' ),
											'name' 			=> 'custom_param_2',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Give your Query a custom unique parameter to allow server side filtering.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: You can customize the plugin query via Hooks and Filters with the help of this parameter.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more Query options like Cat Taxonomy, Exclude By Category etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'taxonomy',
											'premium'		=> true,
											'value' 		=> bdp_get_post_type_taxonomy( BDP_POST_TYPE ),
											'class'			=> 'bdpp-taxonomy-sel',
											'desc' 			=> __( 'Choose registered taxonomy if you want to display category wise post.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Cat Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'cat_taxonomy',
											'class'			=> 'bdpp-cat-taxonomy-sel',
											'premium'		=> true,
											'value' 		=> array( '' => __('Select Taxonomy', 'blog-designer-pack') ),
											'desc' 			=> __( 'Choose a category taxonomy just to display categories as meta information.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Tag Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'tag_taxonomy',
											'class'			=> 'bdpp-tag-taxonomy-sel',
											'premium'		=> true,
											'value' 		=> array( '' => __('Select Taxonomy', 'blog-designer-pack') ),
											'desc' 			=> __( 'Choose a tag taxonomy just to display tags as meta information.', 'blog-designer-pack' ),
										),
										array(
											'type'			=> 'dropdown',
											'class'			=> '',
											'heading'		=> __( 'Category Operator', 'blog-designer-pack'),
											'name'			=> 'category_operator',
											'premium'		=> true,
											'value'			=> array( 
																	'IN'	=> __( 'IN', 'blog-designer-pack' ),
																	'AND'	=> __( 'AND', 'blog-designer-pack' ),
																),
											'desc'			=> __( 'Select category operator. Default value is IN', 'blog-designer-pack' ),
										),
										array(
											'type'			=> 'dropdown',
											'class'			=> '',
											'heading'		=> __( 'Display Child Category Posts', 'blog-designer-pack'),
											'name'			=> 'include_cat_child',
											'premium'		=> true,
											'value'			=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc'			=> __( 'Whether or not to include children category posts if parent category is there.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude By Category', 'blog-designer-pack' ),
											'name' 			=> 'exclude_cat',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose categories to exclude posts of it. Works only if `Category` field is empty.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include Post', 'blog-designer-pack' ),
											'name' 			=> 'posts',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search posts by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __('Choose posts which you want to display.', 'blog-designer-pack'),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude Post', 'blog-designer-pack' ),
											'name' 			=> 'hide_post',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search posts by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __('Choose posts which you do not want to display.', 'blog-designer-pack'),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include By Author', 'blog-designer-pack' ),
											'name' 			=> 'author',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search authors by its name, email or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose authors to show posts associated with that.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude By Author', 'blog-designer-pack' ),
											'name' 			=> 'exclude_author',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search authors by its name, email or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose authors to hide posts associated with that. Works only if `Include Author` field is empty.', 'blog-designer-pack' ),
										),
										
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Sticky Posts', 'blog-designer-pack' ),
											'name' 			=> 'sticky_posts',
											'premium'		=> true,
											'value' 		=> array(
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display sticky posts. This only effects the frontend.', 'blog-designer-pack' ) . '<label title="'.esc_attr__("Note: Sticky post only be displayed at front side. In preview mode sticky post will not be displayed.", 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Type', 'blog-designer-pack' ),
											'name' 			=> 'type',
											'premium'		=> true,
											'value' 		=> array(
																	'' 			=> __( 'Select Type', 'blog-designer-pack' ),
																	'featured'	=> __( 'Featured', 'blog-designer-pack' ),
																	'trending'	=> __( 'Trending', 'blog-designer-pack'),
																),
											'desc' 			=> __( 'Select display type of post. Is it Featured or Trending?', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: For trending post type make sure you have enabled the post type from Plugin Settings > Trending Post.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Query Offset', 'blog-designer-pack' ),
											'name' 			=> 'query_offset',
											'premium'		=> true,
											'value' 		=> '',
											'desc' 			=> __( 'Skip number of posts from starting.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('e.g. 5 to skip over 5 posts. Note: Do not use limit=-1 and pagination=true with this.', 'blog-designer-pack').'"> [?]</label>',
										),
									)
			),

			// Pagination
			'pagination' => array(
					'title'		=> __('Pagination', 'blog-designer-pack'),
					'params'	=> array(
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Total Number of Post', 'blog-designer-pack' ),
											'name' 			=> 'limit',
											'value' 		=> 20,
											'min'			=> -1,
											'desc' 			=> __( 'Enter total number of post to be displayed. Enter -1 to display all.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Pagination', 'blog-designer-pack' ),
											'name' 			=> 'pagination',
											'value' 		=> array( 
																'true'	=> __( 'True', 'blog-designer-pack' ),
																'false'	=> __( 'False', 'blog-designer-pack' ),
															),
											'dependency' 	=> array(
																		'element' 				=> 'limit',
																		'value_not_equal_to' 	=> '-1',
																	),
											'desc' 			=> __( 'Display Pagination.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Pagination Type', 'blog-designer-pack' ),
											'name' 			=> 'pagination_type',
											'value' 		=> array(
																	'numeric'					=> __( 'Numeric', 'blog-designer-pack' ),
																	'numeric-ajax|disabled'		=> __( 'Numeric Ajax', 'blog-designer-pack' ),
																	'prev-next|disabled'		=> __( 'Next - Prev', 'blog-designer-pack' ),
																	'prev-next-ajax|disabled'	=> __( 'Next - Prev Ajax', 'blog-designer-pack' ),
																	'load-more|disabled'		=> __( 'Load More', 'blog-designer-pack' ),
																	'infinite|disabled'			=> __( 'Infinite Scroll', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose pagination type.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																'element' 				=> 'pagination',
																'value_not_equal_to' 	=> array( 'false' ),
															),
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more pagination type like Load More, Infinite Scroll etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Previous Button Text', 'blog-designer-pack' ),
											'name' 			=> 'prev_text',
											'value' 		=> '',
											'premium'		=> true,
											'desc' 			=> __( 'Pagination previous button text. Leave it empty for default.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Next Button Text', 'blog-designer-pack' ),
											'name' 			=> 'next_text',
											'value' 		=> '',
											'premium'		=> true,
											'desc' 			=> __( 'Pagination next button text. Leave it empty for default.', 'blog-designer-pack' ),
										),
									)
			),
			
			// Social Sharing
			'social_sharing' => array(
					'title'		=> __('Social Sharing', 'blog-designer-pack'),
					'premium'	=> true,
					'params'	=> array(
										array(
											'type' 	=> 'dropdown',
											'name' 	=> 'sharing',
											'value' => array('' => __('No Social Sharing', 'blog-designer-pack') ),
											'desc' 	=> __( 'Enable social sharing. You can enable it from plugin setting page.', 'blog-designer-pack' ) . '<label> [?]</label>',
										),
									)
			),
			
			// Filter Settings
			'filter' => array(
					'title'		=> __('Filter', 'blog-designer-pack'),
					'premium'		=> true,
					'params'	=>  array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Enable Filter', 'blog-designer-pack' ),
											'name' 			=> 'filter',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),												
																),
											'desc' 			=> __( 'Enable category filter.', 'blog-designer-pack' ),
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Filter Design', 'blog-designer-pack' ),
											'name' 		=> 'filter_design',
											'value' 	=> array( 
																'design-1'	=> __( 'Design 1', 'blog-designer-pack' ),
															),
											'desc' 		=> __( 'Choose filter design.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Filter All Text', 'blog-designer-pack' ),
											'name' 			=> 'filter_all_text',
											'value' 		=> __( 'All', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter `ALL` field text. Leave it empty to remove it.', 'blog-designer-pack' ),
											'allow_empty'	=> true,
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Filter More Text', 'blog-designer-pack' ),
											'name' 			=> 'filter_more_text',
											'value' 		=> __( 'More', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter filter `More` field text. This will be displayed when the category filter is wider than screen.', 'blog-designer-pack' ),
											'allow_empty'	=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Position', 'blog-designer-pack' ),
											'name' 			=> 'filter_position',
											'value' 		=> array( 
																	'top'		=> __( 'Top', 'blog-designer-pack' ),
																	'left'		=> __( 'Left', 'blog-designer-pack' ),
																	'right'		=> __( 'Right', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose filter position.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Alignment', 'blog-designer-pack' ),
											'name' 			=> 'filter_align',
											'value' 		=> array( 
																	'right'		=> __( 'Right', 'blog-designer-pack' ),
																	'left'		=> __( 'Left', 'blog-designer-pack' ),
																	'center'	=> __( 'Center', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose filter alignment.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Filter Responsive Screen', 'blog-designer-pack' ),
											'name' 			=> 'filter_res_screen',
											'value' 		=> 768,
											'desc' 			=> __( 'Enter filter responsive screen. Filter will be on top position below this screen resolution.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Total Number of Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_limit',
											'value' 		=> 10,
											'desc' 			=> __( 'Enter number of categories to display at a time. Enter 0 to display all.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Categories Order By', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_orderby',
											'value' 		=>  array(
																	'name' 			=> __( 'Category Name', 'blog-designer-pack' ),
																	'slug' 			=> __( 'Category Slug', 'blog-designer-pack' ),
																	'term_group' 	=> __( 'Category Group', 'blog-designer-pack' ),
																	'term_id' 		=> __( 'Category ID', 'blog-designer-pack' ),
																	'id' 			=> __( 'ID', 'blog-designer-pack' ),
																	'description' 	=> __( 'Category Description', 'blog-designer-pack' ),
																	'parent'		=> __( 'Category Parent', 'blog-designer-pack' ),
																	'term_order'	=> __( 'Category Order', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select filter category order type.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Categories Order', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_order',
											'value' 		=> array(
																	'asc'	=> __( 'Ascending', 'blog-designer-pack' ),
																	'desc'	=> __( 'Descending', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select filter category sorting order.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Child of Category', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_child_of',
											'value' 		=> array(
																	'' => __('Select Category', 'blog-designer-pack')
																),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Select term id to retrieve child terms of.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Parent Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_parent',
											'value' 		=> array(
																	'' => __('Select Category', 'blog-designer-pack')
																),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Select parent term id to retrieve direct child terms of. Add 0 to display only parent categories.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Active Filter Category', 'blog-designer-pack' ),
											'name' 			=> 'filter_active',
											'value' 		=> '',
											'desc' 			=> __( 'Choose active category. Enter number starting form 1 OR category ID like cat-ID. Default first will be active.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Allow Multiple Filter Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_allow_multiple',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Allow multiple filter category selection at a time.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Reload Filter', 'blog-designer-pack' ),
											'name' 			=> 'filter_reload',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Reload page on filter category selection.', 'blog-designer-pack' ),
										),
									)
			),

			// Style Manager
			'style_manager' => array(
					'title'		=> __('Style Manager', 'blog-designer-pack'),
					'premium'	=> true,
					'params'	=> array(
										array(
											'type' 		=> 'dropdown',
											'name'		=> 'style_id',
											'value' 	=> array('' => __('Choose Style', 'blog-designer-pack')),
											'desc' 		=> __( 'Choose your created style from style manager or create a new one.', 'blog-designer-pack' ),
										)
									)
								)
		);
	return $fields;
}

/**
 * Generate 'bdp_masonry' shortcode fields
 * 
 * @since 1.0
 */
function bdp_masonry_lite_shortcode_fields( $shortcode = '' ) {
	$fields = array(
			// General Settings
			'general' => array(
					'title'		=> __('General & Designs', 'blog-designer-pack'),
					'params'   	=>  array(
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Design', 'blog-designer-pack' ),
											'name' 		=> 'design',
											'value' 	=> bdp_post_masonry_designs(),
											'desc' 		=> __( 'Choose layout design.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Grid', 'blog-designer-pack' ),
											'name' 			=> 'grid',
											'value' 		=> array(
																	'1'	 => __( 'Grid 1', 'blog-designer-pack' ),
																	'2'	 => __( 'Grid 2', 'blog-designer-pack' ),
																	'3'	 => __( 'Grid 3', 'blog-designer-pack' ),
																	'4'	 => __( 'Grid 4', 'blog-designer-pack' ),
																	'5'	 => __( 'Grid 5', 'blog-designer-pack' ),
																	'6'	 => __( 'Grid 6', 'blog-designer-pack' ),
																	'7'	 => __( 'Grid 7', 'blog-designer-pack' ),
																	'8'	 => __( 'Grid 8', 'blog-designer-pack' ),
																	'9'	 => __( 'Grid 9', 'blog-designer-pack' ),
																	'10' => __( 'Grid 10', 'blog-designer-pack' ),
																	'11' => __( 'Grid 11', 'blog-designer-pack' ),
																	'12' => __( 'Grid 12', 'blog-designer-pack' ),
																),
											'default'		=> 2,
											'desc' 			=> __( 'Choose number of column to be displayed.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Image Size', 'blog-designer-pack' ),
											'name' 			=> 'media_size',
											'value' 		=> 'large',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Choose WordPress registered image size. e.g.', 'blog-designer-pack' ).' bdpp-medium, thumbnail, medium, large, full.',
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'CSS Class', 'blog-designer-pack' ),
											'name' 			=> 'css_class',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Enter an extra CSS class for design customization.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Extra class will be added at top most parent so using extra class you customize your design.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock to enable Display Effect.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Effect', 'blog-designer-pack' ),
											'name' 			=> 'effect',
											'premium'		=> true,
											'value' 		=> array(
																	'effect-1'	=> __( 'Effect 1', 'blog-designer-pack' ),
																	'effect-2'	=> __( 'Effect 2', 'blog-designer-pack' ),
																	'effect-3'	=> __( 'Effect 3', 'blog-designer-pack' ),
																	'effect-4'	=> __( 'Effect 4', 'blog-designer-pack' ),
																	'effect-5'	=> __( 'Effect 5', 'blog-designer-pack' ),
																	'effect-6'	=> __( 'Effect 6', 'blog-designer-pack' ),
																	'effect-7'	=> __( 'Effect 7', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose display effect.', 'blog-designer-pack' ),
										),
									)
			),

			// Meta Fields
			'meta' => array(
					'title'     => __('Meta & Content', 'blog-designer-pack'),
					'params'   	=>  array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Post Date', 'blog-designer-pack' ),
											'name' 			=> 'show_date',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post date.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Author', 'blog-designer-pack' ),
											'name' 			=> 'show_author',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post author.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Tags', 'blog-designer-pack' ),
											'name' 			=> 'show_tags',
											'value' 		=> array( 
																	'true'		=> __( 'True', 'blog-designer-pack' ),
																	'false'		=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post tags.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Comments Count', 'blog-designer-pack' ),
											'name' 			=> 'show_comments',
											'value' 		=> array(
																	'true'		=> __( 'True', 'blog-designer-pack' ),
																	'false'		=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post comment count.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Category', 'blog-designer-pack' ),
											'name' 			=> 'show_category',
											'value' 		=> array( 
																	'true'		=> __( 'True', 'blog-designer-pack' ),
																	'false'		=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post category.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Content', 'blog-designer-pack' ),
											'name' 			=> 'show_content',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post content.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Content Word Limit', 'blog-designer-pack' ),
											'name' 			=> 'content_words_limit',
											'value' 		=> 20,
											'desc' 			=> __( 'Enter content word limit.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'show_content',
																	'value' 	=> array( 'true' ),
																),
										),
										
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Read More', 'blog-designer-pack' ),
											'name' 			=> 'show_read_more',
											'value' 		=> array(
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Show read more.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element' 	=> 'show_content',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Read More Text', 'blog-designer-pack' ),
											'name' 			=> 'read_more_text',
											'value' 		=> __( 'Read More', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter read more text.', 'blog-designer-pack' ),
											'refresh_time'	=> 1000,
											'dependency' 	=> array(
																	'element' 	=> 'show_read_more',
																	'value' 	=> array( 'true' ),
																),
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more Meta & Content options like Read More Text etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Sub Title', 'blog-designer-pack' ),
											'name' 			=> 'show_sub_title',
											'premium'		=> true,
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display sub title or not.', 'blog-designer-pack' ) . '<label title="'.esc_attr__("Sub title can be added via 'Blog Designer Pack Pro - Settings' metabox from Post add / edit screen.", 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Post Link Target', 'blog-designer-pack' ),
											'name'		=> 'link_behaviour',
											'premium'	=> true,
											'value' 	=> array(
																'self'	=> __( 'Same Tab', 'blog-designer-pack' ),
																'new'	=> __( 'New Tab', 'blog-designer-pack' ),
															),
											'desc'		=> __( 'Choose post link behaviour.', 'blog-designer-pack' ),
										),
									)
			),			

			// Query
			'query' => array(
					'title'		=> __('Query', 'blog-designer-pack'),
					'params'	=> array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Type', 'blog-designer-pack' ),
											'name' 			=> 'post_type',
											'value' 		=> bdp_get_supported_post_types(),
											'class'			=> 'bdpp-post-type-sel',
											'ajax'			=> true,
											'desc' 			=> sprintf( __( 'Choose registered post type. You can enable it from plugin %ssetting%s page.', 'blog-designer-pack' ), '<a href="'.esc_url( BDP_SETTING_PAGE_URL ).'" target="_black">', '</a>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include By Category', 'blog-designer-pack' ),
											'name' 			=> 'category',
											'value' 		=> '',
											'class'			=> 'bdpp-ajax-select2 bdpp-category-sel',
											'multi'			=> true,
											'ajax'			=> true,
											'ajax_action'	=> 'bdpp_category_sugg',
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose categories to display category wise posts.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Order By', 'blog-designer-pack' ),
											'name' 			=> 'orderby',
											'value' 		=>  array(
																	'date' 			=> __( 'Post Date', 'blog-designer-pack' ),
																	'ID' 			=> __( 'Post ID', 'blog-designer-pack' ),
																	'author' 		=> __( 'Post Author', 'blog-designer-pack' ),
																	'title' 		=> __( 'Post Title', 'blog-designer-pack' ),
																	'name' 			=> __( 'Post Slug', 'blog-designer-pack' ),
																	'modified' 		=> __( 'Post Modified Date', 'blog-designer-pack' ),
																	'menu_order'	=> __( 'Menu Order', 'blog-designer-pack' ),
																	'parent'		=> __( 'Parent ID', 'blog-designer-pack' ),
																	'rand' 			=> __( 'Random', 'blog-designer-pack' ),
																	'comment_count'	=> __( 'Number of Comments', 'blog-designer-pack' ),
																	'relevance'		=> __( 'Relevance', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select order type.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Order', 'blog-designer-pack' ),
											'name' 			=> 'order',
											'value' 		=> array(
																	'desc'	=> __( 'Descending', 'blog-designer-pack' ),
																	'asc'	=> __( 'Ascending', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select sorting order.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Custom Parameter 1', 'blog-designer-pack' ),
											'name' 			=> 'custom_param_1',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Give your Query a custom unique parameter to allow server side filtering.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: You can customize the plugin query via Hooks and Filters with the help of this parameter.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Custom Parameter 2', 'blog-designer-pack' ),
											'name' 			=> 'custom_param_2',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Give your Query a custom unique parameter to allow server side filtering.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: You can customize the plugin query via Hooks and Filters with the help of this parameter.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more Query options like Cat Taxonomy, Exclude By Category etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'taxonomy',
											'premium'		=> true,
											'value' 		=> bdp_get_post_type_taxonomy( BDP_POST_TYPE ),
											'class'			=> 'bdpp-taxonomy-sel',
											'desc' 			=> __( 'Choose registered taxonomy if you want to display category wise post.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Cat Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'cat_taxonomy',
											'class'			=> 'bdpp-cat-taxonomy-sel',
											'premium'		=> true,
											'value' 		=> array( '' => __('Select Taxonomy', 'blog-designer-pack') ),
											'desc' 			=> __( 'Choose a category taxonomy just to display categories as meta information.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Tag Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'tag_taxonomy',
											'class'			=> 'bdpp-tag-taxonomy-sel',
											'premium'		=> true,
											'value' 		=> array( '' => __('Select Taxonomy', 'blog-designer-pack') ),
											'desc' 			=> __( 'Choose a tag taxonomy just to display tags as meta information.', 'blog-designer-pack' ),
										),
										array(
											'type'			=> 'dropdown',
											'class'			=> '',
											'heading'		=> __( 'Category Operator', 'blog-designer-pack'),
											'name'			=> 'category_operator',
											'premium'		=> true,
											'value'			=> array( 
																	'IN'	=> __( 'IN', 'blog-designer-pack' ),
																	'AND'	=> __( 'AND', 'blog-designer-pack' ),
																),
											'desc'			=> __( 'Select category operator. Default value is IN', 'blog-designer-pack' ),
										),
										array(
											'type'			=> 'dropdown',
											'class'			=> '',
											'heading'		=> __( 'Display Child Category Posts', 'blog-designer-pack'),
											'name'			=> 'include_cat_child',
											'premium'		=> true,
											'value'			=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc'			=> __( 'Whether or not to include children category posts if parent category is there.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude By Category', 'blog-designer-pack' ),
											'name' 			=> 'exclude_cat',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose categories to exclude posts of it. Works only if `Category` field is empty.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include Post', 'blog-designer-pack' ),
											'name' 			=> 'posts',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search posts by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __('Choose posts which you want to display.', 'blog-designer-pack'),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude Post', 'blog-designer-pack' ),
											'name' 			=> 'hide_post',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search posts by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __('Choose posts which you do not want to display.', 'blog-designer-pack'),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include By Author', 'blog-designer-pack' ),
											'name' 			=> 'author',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search authors by its name, email or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose authors to show posts associated with that.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude By Author', 'blog-designer-pack' ),
											'name' 			=> 'exclude_author',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search authors by its name, email or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose authors to hide posts associated with that. Works only if `Include Author` field is empty.', 'blog-designer-pack' ),
										),
										
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Sticky Posts', 'blog-designer-pack' ),
											'name' 			=> 'sticky_posts',
											'premium'		=> true,
											'value' 		=> array(
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display sticky posts. This only effects the frontend.', 'blog-designer-pack' ) . '<label title="'.esc_attr__("Note: Sticky post only be displayed at front side. In preview mode sticky post will not be displayed.", 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Type', 'blog-designer-pack' ),
											'name' 			=> 'type',
											'premium'		=> true,
											'value' 		=> array(
																	'' 			=> __( 'Select Type', 'blog-designer-pack' ),
																	'featured'	=> __( 'Featured', 'blog-designer-pack' ),
																	'trending'	=> __( 'Trending', 'blog-designer-pack'),
																),
											'desc' 			=> __( 'Select display type of post. Is it Featured or Trending?', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: For trending post type make sure you have enabled the post type from Plugin Settings > Trending Post.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Query Offset', 'blog-designer-pack' ),
											'name' 			=> 'query_offset',
											'value' 		=> '',
											'premium'		=> true,
											'desc' 			=> __( 'Skip number of posts from starting.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('e.g. 5 to skip over 5 posts. Note: Do not use limit=-1 and pagination=true with this.', 'blog-designer-pack').'"> [?]</label>',
										),
									)
			),

			// Pagination
			'pagination' => array(
					'title'		=> __('Pagination', 'blog-designer-pack'),
					'params'	=> array(
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Total Number of Post', 'blog-designer-pack' ),
											'name' 			=> 'limit',
											'value' 		=> 20,
											'min'			=> -1,
											'desc' 			=> __( 'Enter total number of post to be displayed. Enter -1 to display all.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Pagination', 'blog-designer-pack' ),
											'name' 			=> 'pagination',
											'value' 		=> array( 
																'true'	=> __( 'True', 'blog-designer-pack' ),
																'false'	=> __( 'False', 'blog-designer-pack' ),
															),
											'dependency' 	=> array(
																		'element' 				=> 'limit',
																		'value_not_equal_to' 	=> '-1',
																	),
											'desc' 			=> __( 'Display Pagination.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Pagination Type', 'blog-designer-pack' ),
											'name' 			=> 'pagination_type',
											'value' 		=> array(
																	'load-more'					=> __( 'Load More', 'blog-designer-pack' ),
																	'numeric|disabled'			=> __( 'Numeric', 'blog-designer-pack' ),
																	'numeric-ajax|disabled'		=> __( 'Numeric Ajax', 'blog-designer-pack' ),
																	'prev-next|disabled'		=> __( 'Next - Prev', 'blog-designer-pack' ),
																	'prev-next-ajax|disabled'	=> __( 'Next - Prev Ajax', 'blog-designer-pack' ),
																	'infinite|disabled'			=> __( 'Infinite Scroll', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose pagination type.', 'blog-designer-pack' ),
											'dependency' 	=> array(
																'element' 				=> 'pagination',
																'value_not_equal_to' 	=> array( 'false' ),
															),
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more pagination type like Load More, Infinite Scroll etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Previous Button Text', 'blog-designer-pack' ),
											'name' 			=> 'prev_text',
											'value' 		=> '',
											'desc' 			=> __( 'Pagination previous button text. Leave it empty for default.', 'blog-designer-pack' ),
											'premium'		=> true,
											'dependency' 	=> array(
																'element' 				=> 'pagination_type',
																'value_not_equal_to' 	=> array( 'load-more', 'infinite' ),
															),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Next Button Text', 'blog-designer-pack' ),
											'name' 			=> 'next_text',
											'value' 		=> '',
											'desc' 			=> __( 'Pagination next button text. Leave it empty for default.', 'blog-designer-pack' ),
											'premium'		=> true,
											'dependency' 	=> array(
																'element' 				=> 'pagination_type',
																'value_not_equal_to' 	=> array( 'load-more', 'infinite' ),
															),
										)
									)
			),
			
			// Social Sharing
			'social_sharing' => array(
					'title'		=> __('Social Sharing', 'blog-designer-pack'),
					'premium'	=> true,
					'params'	=> array(
										array(
											'type' 	=> 'dropdown',
											'name' 	=> 'sharing',
											'value' => array('' => __('No Social Sharing', 'blog-designer-pack') ),
											'desc' 	=> __( 'Enable social sharing. You can enable it from plugin setting page.', 'blog-designer-pack' ) . '<label> [?]</label>',
										),
									)
			),
			
			// Filter Settings
			'filter' => array(
					'title'		=> __('Filter', 'blog-designer-pack'),
					'premium'	=> true,
					'params'	=>  array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Enable Filter', 'blog-designer-pack' ),
											'name' 			=> 'filter',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),												
																),
											'desc' 			=> __( 'Enable category filter.', 'blog-designer-pack' ),
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Filter Design', 'blog-designer-pack' ),
											'name' 		=> 'filter_design',
											'value' 	=> array( 
																'design-1'	=> __( 'Design 1', 'blog-designer-pack' ),
															),
											'desc' 		=> __( 'Choose filter design.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Filter All Text', 'blog-designer-pack' ),
											'name' 			=> 'filter_all_text',
											'value' 		=> __( 'All', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter `ALL` field text. Leave it empty to remove it.', 'blog-designer-pack' ),
											'allow_empty'	=> true,																
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Filter More Text', 'blog-designer-pack' ),
											'name' 			=> 'filter_more_text',
											'value' 		=> __( 'More', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter filter `More` field text. This will be displayed when the category filter is wider than screen.', 'blog-designer-pack' ),
											'allow_empty'	=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Position', 'blog-designer-pack' ),
											'name' 			=> 'filter_position',
											'value' 		=> array( 
																	'top'		=> __( 'Top', 'blog-designer-pack' ),
																	'left'		=> __( 'Left', 'blog-designer-pack' ),
																	'right'		=> __( 'Right', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose filter position.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Alignment', 'blog-designer-pack' ),
											'name' 			=> 'filter_align',
											'value' 		=> array( 
																	'right'		=> __( 'Right', 'blog-designer-pack' ),
																	'left'		=> __( 'Left', 'blog-designer-pack' ),
																	'center'	=> __( 'Center', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose filter alignment.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Filter Responsive Screen', 'blog-designer-pack' ),
											'name' 			=> 'filter_res_screen',
											'value' 		=> 768,
											'desc' 			=> __( 'Enter filter responsive screen. Filter will be on top position below this screen resolution.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Total Number of Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_limit',
											'value' 		=> 10,
											'desc' 			=> __( 'Enter number of categories to display at a time. Enter 0 to display all.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Categories Order By', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_orderby',
											'value' 		=>  array(
																	'name' 			=> __( 'Category Name', 'blog-designer-pack' ),
																	'slug' 			=> __( 'Category Slug', 'blog-designer-pack' ),
																	'term_group' 	=> __( 'Category Group', 'blog-designer-pack' ),
																	'term_id' 		=> __( 'Category ID', 'blog-designer-pack' ),
																	'id' 			=> __( 'ID', 'blog-designer-pack' ),
																	'description' 	=> __( 'Category Description', 'blog-designer-pack' ),
																	'parent'		=> __( 'Category Parent', 'blog-designer-pack' ),
																	'term_order'	=> __( 'Category Order', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select filter category order type.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Categories Order', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_order',
											'value' 		=> array(
																	'asc'	=> __( 'Ascending', 'blog-designer-pack' ),
																	'desc'	=> __( 'Descending', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select filter category sorting order.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Child of Category', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_child_of',
											'value' 		=> array(
																	'' => __('Select Category', 'blog-designer-pack')
																),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Select term id to retrieve child terms of.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Parent Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_parent',
											'value' 		=> array(
																	'' => __('Select Category', 'blog-designer-pack')
																),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Select parent term id to retrieve direct child terms of. Add 0 to display only parent categories.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Active Filter Category', 'blog-designer-pack' ),
											'name' 			=> 'filter_active',
											'value' 		=> '',
											'desc' 			=> __( 'Choose active category. Enter number starting form 1 OR category ID like cat-ID. Default first will be active.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Allow Multiple Filter Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_allow_multiple',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Allow multiple filter category selection at a time.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Reload Filter', 'blog-designer-pack' ),
											'name' 			=> 'filter_reload',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Reload page on filter category selection.', 'blog-designer-pack' ),
										),
									)
			),

			// Style Manager
			'style_manager' => array(
					'title'		=> __('Style Manager', 'blog-designer-pack'),
					'premium'	=> true,
					'params'	=> array(
										array(
											'type' 		=> 'dropdown',
											'name'		=> 'style_id',
											'value' 	=> array('' => __('Choose Style', 'blog-designer-pack')),
											'desc' 		=> __( 'Choose your created style from style manager or create a new one.', 'blog-designer-pack' ),
										)
									)
								)
		);
	return $fields;
}

/**
 * Generate 'bdp_ticker' shortcode fields
 * 
 * @since 1.0
 */
function bdp_ticker_lite_shortcode_fields( $shortcode = '' ) {

	$fields = array(
			// General fields
			'general' => array(
					'title'		=> __('General', 'blog-designer-pack'),
					'params'	=>  array(
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Ticker Title', 'blog-designer-pack' ),
											'name' 			=> 'ticker_title',
											'value' 		=> __('Latest Post', 'blog-designer-pack'),
											'desc' 			=> __( 'Title for the ticker.', 'blog-designer-pack' ),
											'allow_empty'	=> true,
										),
										array(
												'type' 			=> 'text',
												'heading' 		=> __( 'CSS Class', 'blog-designer-pack' ),
												'name' 			=> 'css_class',
												'value' 		=> '',
												'refresh_time'	=> 1000,
												'desc' 			=> __( 'Enter an extra CSS class for design customization.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Extra class will be added at top most parent so using extra class you customize your design.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more Ticker options like Position, Height etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),										
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Position Of Ticker', 'blog-designer-pack' ),
											'name' 			=> 'position',
											'premium'		=> true,
											'value' 		=> array(
																	'auto' 				=> __( 'Auto', 'blog-designer-pack' ),
																	'fixed-bottom' 		=>  __( 'Fixed Bottom', 'blog-designer-pack' ),
																	'fixed-top' 		=>  __( 'Fixed Top', 'blog-designer-pack' ),
																	
																),
											'desc' 			=> __( 'Set position of ticker.', 'blog-designer-pack' ),											
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Height Of Ticker', 'blog-designer-pack' ),
											'name' 			=> 'height',
											'value' 		=> 40,
											'refresh_time'	=> 1000,
											'premium'		=> true,
											'desc' 			=> __( 'Set height of the ticker.', 'blog-designer-pack' ),
										),
										array(
											'type'		=> 'dropdown',
											'heading' 	=> __( 'Show Title In Mobile', 'blog-designer-pack' ),
											'name' 		=> 'show_title_in_mobile',
											'premium'	=> true,
											'value' 	=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc'		=> __( 'Show title in mobile.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Featured Image', 'blog-designer-pack' ),
											'name' 			=> 'show_feat_image',
											'premium'		=> true,
											'value' 		=> array(
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display post featured image.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Featured Image Size', 'blog-designer-pack' ),
											'name' 			=> 'media_size',
											'premium'		=> true,
											'value' 		=> 'thumbnail',
											'desc' 			=> __( 'Choose WordPress registered image size. e.g.', 'blog-designer-pack' ).' bdpp-medium, thumbnail, medium, large, full.',											
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Post Link Target', 'blog-designer-pack' ),
											'name'		=> 'link_behaviour',
											'premium'	=> true,
											'value' 	=> array(
																'self'	=> __( 'Same Tab', 'blog-designer-pack' ),
																'new'	=> __( 'New Tab', 'blog-designer-pack' ),
															),
											'desc'		=> __( 'Choose post link behaviour.', 'blog-designer-pack' ),
										),
									)
			),

			// Ticker Fields
			'ticker' => array(
					'title'		=> __('Ticker', 'blog-designer-pack'),
					'params'	=>  array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Autoplay', 'blog-designer-pack' ),
											'name' 			=> 'autoplay',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Autoplay ticker.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Autoplay Interval', 'blog-designer-pack' ),
											'name' 			=> 'speed',
											'value' 		=> 3000,
											'desc' 			=> __( 'Autoplay interval of the ticker. Note: 1000 = 1 Sec', 'blog-designer-pack' ),
											'dependency' 	=> array(
																	'element'	=> 'autoplay',
																	'value'		=> array( 'true' ),
																),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Ticker Effect', 'blog-designer-pack' ),
											'name' 			=> 'ticker_effect',
											'value' 		=> array(
																	'slide-up'		=> __( 'Verticle Up','blog-designer-pack' ),
																	'slide-down'	=> __( 'Verticle Down','blog-designer-pack' ),
																	'slide-right|disabled'	=> __( 'Horizontal Right', 'blog-designer-pack' ),
																	'slide-left|disabled'	=> __( 'Horizontal Left', 'blog-designer-pack' ),
																	'fade|disabled'			=> __( 'Fade', 'blog-designer-pack' ),
																	'typography|disabled'	=> __( 'Typography', 'blog-designer-pack' ),
																	'scroll|disabled'		=> __( 'Continuous Scroll', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Set the ticker effect. e.g. Vertical, Horizontal, Fade.', 'blog-designer-pack' ),
										),
										
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock to get more Ticker Effects like Fade, Typography, Continuous Scroll etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Continuous Scroll Speed', 'blog-designer-pack' ),
											'name' 			=> 'scroll_speed',
											'value' 		=> 2,
											'premium'		=> true,
											'desc' 			=> __( 'Set continuous scroll speed of the ticker', 'blog-designer-pack' )
										),
										array(
											'type'		=> 'dropdown',
											'heading' 	=> __( 'Show Arrows', 'blog-designer-pack' ),
											'name' 		=> 'arrows',
											'premium'		=> true,
											'value' 	=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc'		=> __( 'Show prev - next arrows.', 'blog-designer-pack' ),
										),
										array(
											'type'		=> 'dropdown',
											'heading' 	=> __( 'Show Pause Button', 'blog-designer-pack' ),
											'name' 		=> 'pause_button',
											'premium'		=> true,
											'value' 	=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc'		=> __( 'Show pause button.', 'blog-designer-pack' ),
										),
										array(
											'type'		=> 'dropdown',
											'heading' 	=> __( 'Pause On Hover', 'blog-designer-pack' ),
											'name' 		=> 'hover_stop',
											'premium'	=> true,
											'value' 	=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc'		=> __( 'Stop ticker on mouse hover.', 'blog-designer-pack' ),
										),
										array(
											'type'		=> 'dropdown',
											'heading' 	=> __( 'Hide Control In Mobile', 'blog-designer-pack' ),
											'name' 		=> 'hide_ctrl_in_mobile',
											'premium'		=> true,
											'value' 	=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc'		=> __( 'Hide control in mobile i.e. arrows and pause button.', 'blog-designer-pack' ),
										),
									),
			),

			// Query
			'query' => array(
					'title'		=> __('Query', 'blog-designer-pack'),
					'params'	=> array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Type', 'blog-designer-pack' ),
											'name' 			=> 'post_type',
											'value' 		=> bdp_get_supported_post_types(),
											'class'			=> 'bdpp-post-type-sel',
											'ajax'			=> true,
											'desc' 			=> sprintf( __( 'Choose registered post type. You can enable it from plugin %ssetting%s page.', 'blog-designer-pack' ), '<a href="'.esc_url( BDP_SETTING_PAGE_URL ).'" target="_black">', '</a>' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Total Ticker Items Limit', 'blog-designer-pack' ),
											'name' 			=> 'limit',
											'value' 		=> 20,
											'min' 			=> -1,
											'validation'	=> 'number',
											'desc' 			=> __( 'Enter number to be displayed. Enter -1 to display all.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include By Category', 'blog-designer-pack' ),
											'name' 			=> 'category',
											'value' 		=> '',
											'class'			=> 'bdpp-ajax-select2 bdpp-category-sel',
											'multi'			=> true,
											'ajax'			=> true,
											'ajax_action'	=> 'bdpp_category_sugg',
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose categories to display category wise posts.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Order By', 'blog-designer-pack' ),
											'name' 			=> 'orderby',
											'value' 		=>  array(
																	'date' 			=> __( 'Post Date', 'blog-designer-pack' ),
																	'ID' 			=> __( 'Post ID', 'blog-designer-pack' ),
																	'author' 		=> __( 'Post Author', 'blog-designer-pack' ),
																	'title' 		=> __( 'Post Title', 'blog-designer-pack' ),
																	'name' 			=> __( 'Post Slug', 'blog-designer-pack' ),
																	'modified' 		=> __( 'Post Modified Date', 'blog-designer-pack' ),
																	'menu_order'	=> __( 'Menu Order', 'blog-designer-pack' ),
																	'parent'		=> __( 'Parent ID', 'blog-designer-pack' ),
																	'rand' 			=> __( 'Random', 'blog-designer-pack' ),
																	'comment_count'	=> __( 'Number of Comments', 'blog-designer-pack' ),
																	'relevance'		=> __( 'Relevance', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select order type.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Post Order', 'blog-designer-pack' ),
											'name' 			=> 'order',
											'value' 		=> array(
																	'desc'	=> __( 'Descending', 'blog-designer-pack' ),
																	'asc'	=> __( 'Ascending', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select sorting order.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Custom Parameter 1', 'blog-designer-pack' ),
											'name' 			=> 'custom_param_1',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Give your Query a custom unique parameter to allow server side filtering.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: You can customize the plugin query via Hooks and Filters with the help of this parameter.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Custom Parameter 2', 'blog-designer-pack' ),
											'name' 			=> 'custom_param_2',
											'value' 		=> '',
											'refresh_time'	=> 1000,
											'desc' 			=> __( 'Give your Query a custom unique parameter to allow server side filtering.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: You can customize the plugin query via Hooks and Filters with the help of this parameter.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock more Query options like Category Operator, Exclude By Category etc.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Taxonomy', 'blog-designer-pack' ),
											'name' 			=> 'taxonomy',
											'premium'		=> true,
											'value' 		=> bdp_get_post_type_taxonomy( BDP_POST_TYPE ),
											'class'			=> 'bdpp-taxonomy-sel',
											'desc' 			=> __( 'Choose registered taxonomy if you want to display category wise post.', 'blog-designer-pack' ),
										),
										array(
											'type'			=> 'dropdown',
											'class'			=> '',
											'heading'		=> __( 'Category Operator', 'blog-designer-pack'),
											'name'			=> 'category_operator',
											'premium'		=> true,
											'value'			=> array( 
																	'IN'	=> __( 'IN', 'blog-designer-pack' ),
																	'AND'	=> __( 'AND', 'blog-designer-pack' ),
																),
											'desc'			=> __( 'Select category operator. Default value is IN', 'blog-designer-pack' ),
										),
										array(
											'type'			=> 'dropdown',
											'class'			=> '',
											'heading'		=> __( 'Display Child Category Posts', 'blog-designer-pack'),
											'name'			=> 'include_cat_child',
											'premium'		=> true,
											'value'			=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc'			=> __( 'Whether or not to include children category posts if parent category is there.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude By Category', 'blog-designer-pack' ),
											'name' 			=> 'exclude_cat',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose categories to exclude posts of it. Works only if `Category` field is empty.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include Post', 'blog-designer-pack' ),
											'name' 			=> 'posts',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search posts by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __('Choose posts which you want to display.', 'blog-designer-pack'),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude Post', 'blog-designer-pack' ),
											'name' 			=> 'hide_post',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search posts by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __('Choose posts which you do not want to display.', 'blog-designer-pack'),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Include By Author', 'blog-designer-pack' ),
											'name' 			=> 'author',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search authors by its name, email or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose authors to show posts associated with that.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Exclude By Author', 'blog-designer-pack' ),
											'name' 			=> 'exclude_author',
											'value' 		=> array('' => __('Select Data', 'blog-designer-pack') ),
											'premium'		=> true,
											'search_msg'	=> __( 'Search authors by its name, email or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Choose authors to hide posts associated with that. Works only if `Include Author` field is empty.', 'blog-designer-pack' ),
										),										
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Show Sticky Posts', 'blog-designer-pack' ),
											'name' 			=> 'sticky_posts',
											'premium'		=> true,
											'value' 		=> array(
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Display sticky posts. This only effects the frontend.', 'blog-designer-pack' ) . '<label title="'.esc_attr__("Note: Sticky post only be displayed at front side. In preview mode sticky post will not be displayed.", 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Type', 'blog-designer-pack' ),
											'name' 			=> 'type',
											'premium'		=> true,
											'value' 		=> array(
																	'' 			=> __( 'Select Type', 'blog-designer-pack' ),
																	'featured'	=> __( 'Featured', 'blog-designer-pack' ),
																	'trending'	=> __( 'Trending', 'blog-designer-pack'),
																),
											'desc' 			=> __( 'Select display type of post. Is it Featured or Trending?', 'blog-designer-pack' ) . '<label title="'.esc_attr__('Note: For trending post type make sure you have enabled the post type from Plugin Settings > Trending Post.', 'blog-designer-pack').'"> [?]</label>',
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Query Offset', 'blog-designer-pack' ),
											'name' 			=> 'query_offset',
											'premium'		=> true,
											'value' 		=> '',
											'desc' 			=> __( 'Skip number of posts from starting.', 'blog-designer-pack' ) . '<label title="'.esc_attr__('e.g. 5 to skip over 5 posts. Note: Do not use limit=-1 and pagination=true with this.', 'blog-designer-pack').'"> [?]</label>',
										),										
									)
			),
			
			// Style Fields
			'style' => array(
					'title'		=> __('Style', 'blog-designer-pack'),
					'params'    => array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Font Style', 'blog-designer-pack' ),
											'name' 			=> 'font_style',
											'value' 		=> array(
																	'normal' 		=> __( 'Normal', 'blog-designer-pack' ),
																	'italic' 		=>  __( 'Italic', 'blog-designer-pack' ),
																	
																),
											'desc' 			=> __( 'Set font style of the post.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'colorpicker',
											'heading' 		=> __( 'Theme Color', 'blog-designer-pack' ),
											'name' 			=> 'theme_color',
											'value' 		=> '#2096cd',
											'desc' 			=> __( 'Set ticker theme color.', 'blog-designer-pack' )
										),
										array(
											'type' 			=> 'colorpicker',
											'heading' 		=> __( 'Ticker Heading Color', 'blog-designer-pack' ),
											'name' 			=> 'heading_font_color',
											'value' 		=> '#fff',
											'desc' 			=> __( 'Set ticker heading font color.', 'blog-designer-pack' )
										),
										array(
											'type' 			=> 'colorpicker',
											'heading' 		=> __( 'Font Color', 'blog-designer-pack' ),
											'name' 			=> 'font_color',
											'value' 		=> '#2096cd',
											'desc' 			=> __( 'Set ticker text font color.', 'blog-designer-pack' ),
										),
										array(
											'type'			=> 'info',
											'heading'		=> __( 'Premium Features', 'blog-designer-pack' ),
											'desc'			=> sprintf( __( '%s Unlock to get Ticker Control options.', 'blog-designer-pack' ), '<i class="dashicons dashicons-lock"></i>' ),
										),
										array(
											'type' 			=> 'colorpicker',
											'heading' 		=> __( 'Control Bg Color', 'blog-designer-pack' ),
											'name' 			=> 'ctrl_bg_color',
											'value' 		=> '#f6f6f6',
											'premium'		=> true,
											'desc' 			=> __( 'Set control background color.', 'blog-designer-pack' )
										),
										array(
											'type' 			=> 'colorpicker',
											'heading' 		=> __( 'Control Bg Hover Color', 'blog-designer-pack' ),
											'name' 			=> 'ctrl_bgh_color',
											'value' 		=> '#eeeeee',
											'premium'		=> true,
											'desc' 			=> __( 'Set control background hover color.', 'blog-designer-pack' )
										),
										array(
											'type' 			=> 'colorpicker',
											'heading' 		=> __( 'Control Text Color', 'blog-designer-pack' ),
											'name' 			=> 'ctrl_txt_color',
											'value' 		=> '#999999',
											'premium'		=> true,
											'desc' 			=> __( 'Set control text color.', 'blog-designer-pack' )
										),
										array(
											'type' 			=> 'colorpicker',
											'heading' 		=> __( 'Control Text Hover Color', 'blog-designer-pack' ),
											'name' 			=> 'ctrl_txth_color',
											'value' 		=> '#999999',
											'premium'		=> true,
											'desc' 			=> __( 'Set control text hover color.', 'blog-designer-pack' )
										),
									)
			),

			// Filter Settings
			'filter' => array(
					'title'		=> __('Filter', 'blog-designer-pack'),
					'premium'	=> true,
					'params'	=>  array(
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Enable Filter', 'blog-designer-pack' ),
											'name' 			=> 'filter',
											'value' 		=> array( 
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Enable category filter.', 'blog-designer-pack' ),
										),
										array(
											'type' 		=> 'dropdown',
											'heading' 	=> __( 'Filter Design', 'blog-designer-pack' ),
											'name' 		=> 'filter_design',
											'value' 	=> array( 
																'design-1'	=> __( 'Design 1', 'blog-designer-pack' ),
															),
											'desc' 		=> __( 'Choose filter design.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Filter All Text', 'blog-designer-pack' ),
											'name' 			=> 'filter_all_text',
											'value' 		=> __( 'All', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter `ALL` field text. Leave it empty to remove it.', 'blog-designer-pack' ),
											'allow_empty'	=> true,
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Filter More Text', 'blog-designer-pack' ),
											'name' 			=> 'filter_more_text',
											'value' 		=> __( 'More', 'blog-designer-pack' ),
											'desc' 			=> __( 'Enter filter `More` field text. This will be displayed when the category filter is wider than screen.', 'blog-designer-pack' ),
											'allow_empty'	=> true,
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Position', 'blog-designer-pack' ),
											'name' 			=> 'filter_position',
											'value' 		=> array( 
																	'top'		=> __( 'Top', 'blog-designer-pack' ),
																	'left'		=> __( 'Left', 'blog-designer-pack' ),
																	'right'		=> __( 'Right', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose filter position.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Alignment', 'blog-designer-pack' ),
											'name' 			=> 'filter_align',
											'value' 		=> array( 
																	'right'		=> __( 'Right', 'blog-designer-pack' ),
																	'left'		=> __( 'Left', 'blog-designer-pack' ),
																	'center'	=> __( 'Center', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Choose filter alignment.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Filter Responsive Screen', 'blog-designer-pack' ),
											'name' 			=> 'filter_res_screen',
											'value' 		=> 768,
											'desc' 			=> __( 'Enter filter responsive screen. Filter will be on top position below this screen resolution.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'number',
											'heading' 		=> __( 'Total Number of Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_limit',
											'value' 		=> 10,
											'desc' 			=> __( 'Enter number of categories to display at a time. Enter 0 to display all.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Categories Order By', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_orderby',
											'value' 		=>  array(
																	'name' 			=> __( 'Category Name', 'blog-designer-pack' ),
																	'slug' 			=> __( 'Category Slug', 'blog-designer-pack' ),
																	'term_group' 	=> __( 'Category Group', 'blog-designer-pack' ),
																	'term_id' 		=> __( 'Category ID', 'blog-designer-pack' ),
																	'id' 			=> __( 'ID', 'blog-designer-pack' ),
																	'description' 	=> __( 'Category Description', 'blog-designer-pack' ),
																	'parent'		=> __( 'Category Parent', 'blog-designer-pack' ),
																	'term_order'	=> __( 'Category Order', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select filter category order type.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Filter Categories Order', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_order',
											'value' 		=> array(
																	'asc'	=> __( 'Ascending', 'blog-designer-pack' ),
																	'desc'	=> __( 'Descending', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Select filter category sorting order.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Child of Category', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_child_of',
											'value' 		=> array(
																	'' => __('Select Category', 'blog-designer-pack')
																),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Select term id to retrieve child terms of.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Display Parent Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_cat_parent',
											'value' 		=> array(
																	'' => __('Select Category', 'blog-designer-pack')
																),
											'search_msg'	=> __( 'Search category by its name, slug or ID', 'blog-designer-pack' ),
											'desc' 			=> __( 'Select parent term id to retrieve direct child terms of. Add 0 to display only parent categories.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'text',
											'heading' 		=> __( 'Active Filter Category', 'blog-designer-pack' ),
											'name' 			=> 'filter_active',
											'value' 		=> '',
											'desc' 			=> __( 'Choose active category. Enter number starting form 1 OR category ID like cat-ID. Default first will be active.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Allow Multiple Filter Categories', 'blog-designer-pack' ),
											'name' 			=> 'filter_allow_multiple',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Allow multiple filter category selection at a time.', 'blog-designer-pack' ),
										),
										array(
											'type' 			=> 'dropdown',
											'heading' 		=> __( 'Reload Filter', 'blog-designer-pack' ),
											'name' 			=> 'filter_reload',
											'value' 		=> array( 
																	'false'	=> __( 'False', 'blog-designer-pack' ),
																	'true'	=> __( 'True', 'blog-designer-pack' ),
																),
											'desc' 			=> __( 'Reload page on filter category selection.', 'blog-designer-pack' ),
										),
									)
			),
	);
	return $fields;	
}