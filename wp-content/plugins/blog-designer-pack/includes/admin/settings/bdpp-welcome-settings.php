<?php
/**
 * Welcome Page Settings
 * 
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function bdp_render_welcome_settings() { 

// Taking some variables
$show_on_front		= get_option( 'show_on_front' );
$page_for_posts_id	= get_option( 'page_for_posts' );
$page_on_front_id	= get_option( 'page_on_front' );
$reading_page_url	= admin_url( 'options-reading.php' );
$new_layout_url		= add_query_arg( array('page' => 'bdpp-layout'), 'admin.php' );
?>

<div id="bdpp-welcome-sett-wrp" class="post-box-container bdpp-welcome-sett-wrp">
	<div class="metabox-holder">
		<div id="bdpp-welcome-sett" class="postbox bdpp-postbox">
			<div class="inside">
					<div class="bdpp-welcome-panel">
						<div class="bdpp-welcome-panel-content bdpp-pro-main-wrap" style="background:#f1f1f1; padding:20px;">
							<h2 class="bdpp-custom-size"><?php esc_html_e('Success, The Blog Designer Pack is now activated!', 'blog-designer-pack'); ?> 😊 <span class="bdpp-version-tag"><?php esc_html_e( BDP_VERSION ); ?></span></h2>
							<p class="bdpp-about-description"><?php esc_html_e('Would you like to create one layout to check usage of Blog Designer Pack plugin?', 'blog-designer-pack'); ?></p>
							<div class="bdpp-cnt-grid bdpp-clearfix">
								<div class="bdpp-cnt-grid-3 bdpp-columns">
									<h3 class="bdpp-custom-size"><?php esc_html_e('Get Started', 'blog-designer-pack'); ?></h3>
									<p><a class="button button-primary button-hero" href="<?php echo esc_url( $new_layout_url ); ?>"><?php esc_html_e('Create Your First Layout', 'blog-designer-pack'); ?></a></p>
									
								</div>
								<div class="bdpp-cnt-grid-3 bdpp-columns">
									<h3 class="bdpp-custom-size"><?php esc_html_e('Next Steps', 'blog-designer-pack'); ?></h3> 
									<ul>
										<li><a href="#Usages-of-bdpp"><span class="dashicons dashicons-welcome-widgets-menus"></span> <?php esc_html_e('Usages', 'blog-designer-pack'); ?></a></li>	
										<li><a href="https://infornweb.com/news-blog-designer-pack-pro/" target="_blank"><span class="dashicons dashicons-welcome-view-site"></span> <?php esc_html_e('Premium Demo', 'blog-designer-pack'); ?></a></li>
									</ul>
								</div>
								<div class="bdpp-cnt-grid-3 bdpp-columns">
									<h3 class="bdpp-custom-size"><?php esc_html_e('Documentation & Support', 'blog-designer-pack'); ?></h3>
									<ul>
										<li><a href="https://docs.infornweb.com/resources/news-blog-designer-pack/" target="_blank"><span class="dashicons dashicons-welcome-learn-more"></span> <?php esc_html_e('Documentation', 'blog-designer-pack'); ?></a></li>
										<li><a href="https://wordpress.org/support/plugin/blog-designer-pack/" target="_blank"><span class="dashicons dashicons-format-aside"></span> <?php esc_html_e('Support Forum', 'blog-designer-pack'); ?></a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div id="dashboard-widgets-wrap">
					<div id="dashboard-widgets" class="metabox-holder columns-2">
						<div class="postbox-container">
							<div class="meta-box-sortables">
								<div class="postbox">
									<div class="postbox-header">
										<h2 class="hndle">
											<span><?php _e( 'Looking to customize your existing blog page?', 'blog-designer-pack' ); ?></span>
										</h2>
									</div>	
									<div class="inside">
										<div class="bdpp-getting-started bdpp-box"> 
											<h4 class="bdpp-custom-size"><?php esc_html_e('Getting Started (Must Read)', 'blog-designer-pack'); ?></h4>
											<div class="bdpp-box-content">
												<p><?php esc_html_e("Once you've activated your plugin, you’ll be redirected to this page (Blog Designer Pack Welcome). Here, you can view the required and helpful steps to use plugin.", "blog-designer-pack"); ?></p>
												<p><?php esc_html_e('We recommend that please read the below sections for more details.', 'blog-designer-pack'); ?></p>
											</div>
										</div>
										
										<div class="bdpp-important-things bdpp-box">
											
											<h4 class="bdpp-custom-size"><?php esc_html_e('Important things (Required)', 'blog-designer-pack'); ?></h4>

											<?php if( $show_on_front == "posts" ) { ?>
												<div class="bdpp-post-page">	
													<div class="bdpp-notice">
														<p><?php echo esc_html_e('Your current homepage is set to "Your latest posts".', 'blog-designer-pack'); ?></p>
														<p><?php echo sprintf( __('If you want to customize and change the design of your current blog page with plugin layout and design then kindly go to %sSettings > Reading%s and change that selection to "A static page" and then select "Homepage" to any page (that you want to display as a homepage) from the drop down.', 'blog-designer-pack'), '<a href="'.esc_url( $reading_page_url ).'" target="_blank">', '</a>' ); ?></p>
													</div>
													<div class="bdpp-box-content">
														<p><?php esc_html_e('We recommend you to refresh this page once you done with above changes.', 'blog-designer-pack'); ?></p>
													</div>
												</div>
											<?php } else if( ! empty( $page_for_posts_id ) ) { ?>
												<div class="bdpp-static-page">

													<div class="bdpp-notice">
														<p><?php esc_html_e('Your current blog page is set to', 'blog-designer-pack'); ?> <span><?php echo get_the_title( $page_for_posts_id ); ?></span>.</p>
														<p><?php echo sprintf( __('If you want to customize and change the design of your current blog page with plugin layout and design then kindly go to %sSettings > Reading%s and change that selection to default one (%s" — Select — "%s) from the dropdown.', 'blog-designer-pack'), '<a href="'.esc_url( $reading_page_url ).'" target="_blank">', '</a>', '<strong>', '</strong>' ); ?></p>
													</div>

													<div class="bdpp-box-content">
														<p><?php esc_html_e('Blog page content is handled by WordPress it self.', 'blog-designer-pack'); ?></p>
														<p><?php echo sprintf( __('To enable Blog Designer Pack plugin design on Blog page, you need to make sure that Blog page should not be selected on "Posts page" of Reading settings. ( %sSettings > Reading%s)', 'blog-designer-pack'), '<a href="'.esc_url( $reading_page_url ).'" target="_blank">', '</a>' ); ?></p>
														<p><?php esc_html_e('First, We recommed you to refresh this page once you done with above changes.', 'blog-designer-pack'); ?></p>
														<p><?php esc_html_e('We recommed you to read the below sections in case if you need more details.', 'blog-designer-pack'); ?></p>
														<ul>
															<li>
																<h4 class="bdpp-custom-size"><?php esc_html_e('Blog page is already created', 'blog-designer-pack'); ?></h4>
																<p><?php echo sprintf( __('If "Blog" page is already created and assigned that page as a "Posts page" under %sWordPress Settings > Reading%s then please change that selection to default one (%s" — Select — "%s) from the dropdown.', 'blog-designer-pack'), '<a href="'.esc_url( $reading_page_url ).'" target="_blank">', '</a>', '<strong>', '</strong>' ); ?></p>
																<p><?php esc_html_e('Once you de-select this setting, open your "Blog" page in edit mode and add the plugin shortcode (Shortcodes that created under "Blog Designer Pack > Shortcode Builder")', 'blog-designer-pack'); ?></p>
															</li>
															<li>
																<h4 class="bdpp-custom-size"><?php esc_html_e('Blog page is not created', 'blog-designer-pack'); ?></h4>
																<p><?php esc_html_e('If Blog page is not created then go to Pages > Add New and create a blog page OR some other name as per your need and add the shortcode.', 'blog-designer-pack'); ?></p>
															</li>
														</ul>
														<p><?php echo sprintf( __('If still you have any question, please feel free to contact us on %sSupport Forum.%s', 'blog-designer-pack'), '<a href="https://wordpress.org/support/plugin/blog-designer-pack/" target="_blank">', '</a>' ); ?></p>
													</div>
												</div>
											<?php } else { ?>
												<div class="bdpp-static-page">
													<div class="bdpp-box-content">
														<p><?php esc_html_e('Well done', 'blog-designer-pack'); ?> 😊 !!</p>
														<p><?php echo sprintf( __('Edit your Blog page OR Home page (a static page created by you OR Chosen by you) and add the desired %sShortcode%s in it.', 'blog-designer-pack'), '<a href="'.esc_url( $new_layout_url ).'" class="welcome-icon welcome-edit-page">', '</a>' ); ?></p>
														<p><?php echo sprintf( __('If still you have any question, please feel free to contact us on %sSupport Forum.%s', 'blog-designer-pack'), '<a href="https://wordpress.org/support/plugin/blog-designer-pack/" target="_blank">', '</a>' ); ?></p>
													</div>	
												</div>
											<?php } ?>
										</div>
									</div><!-- .inside -->
								</div><!-- .postbox -->
							</div><!-- .meta-box-sortables -->
						</div><!-- .postbox-container -->

						<div class="postbox-container">
							<div id="Usages-of-bdpp" class="meta-box-sortables" style="margin-right:0px;">
								<div class="postbox">
									<div class="postbox-header">
										<h2 class="hndle">
											<span><?php esc_html_e( 'Usage of Blog Designer Pack', 'blog-designer-pack' ); ?></span>
										</h2>
									</div>	
									<div class="inside">
										<div class="bdpp-getting-started bdpp-box">
											<h4 class="bdpp-custom-size"><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Create a Blog OR News Website', 'blog-designer-pack'); ?></h4>
											<div class="bdpp-box-content">
												<p><?php esc_html_e('This is very helpful plugin to create a Blog website or News/Magazine website. Just use the layouts with the help of shortcode and design your page.', 'blog-designer-pack'); ?></p>
												<p><?php echo sprintf( __('Check sample %sBlog-1%s, %sBlog-2%s  and %sNews/Magazine%s page here created with Blog Designer Pack.', 'blog-designer-pack'), '<a href="https://infornweb.com/blog-3/" target="_blank">', '</a>', '<a href="https://infornweb.com/blog-4/" target="_blank">', '</a>', '<a href="https://infornweb.com/news-magazine/" target="_blank">', '</a>' ); ?></p>
											</div>
										</div>
										<div class="bdpp-getting-started bdpp-box">
											<h4 class="bdpp-custom-size"><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Display latest post on home page from blog', 'blog-designer-pack'); ?></h4>
											<div class="bdpp-box-content">
												<p><?php esc_html_e('You can display latest post from your blog on home page. You can use 9+ layout for this e.g. grid view OR slider view OR Carousel View etc.', 'blog-designer-pack'); ?></p>
												<p><?php echo sprintf( __('Check sample %sSlider%s, %sCarousel%s and %sPartial Slide%s created with Blog Designer Pack.', 'blog-designer-pack'), '<a href="https://infornweb.com/blog-designer-pack-pro-slider-designs/" target="_blank">', '</a>', '<a href="https://infornweb.com/blog-designer-pack-pro-carousel-designs/" target="_blank">', '</a>', '<a href="https://infornweb.com/blog-designer-pack-pro-carousel-with-partial-slide-designs/" target="_blank">', '</a>' ); ?></p>
											</div>
										</div>
										<div class="bdpp-getting-started bdpp-box">
											<h4 class="bdpp-custom-size"><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Display Featured and Trending Post', 'blog-designer-pack'); ?></h4>
											<div class="bdpp-box-content">
												<p><?php esc_html_e('Highlights your Featured and most Popular/Trending post. You can use 9+ layout for this e.g. grid view OR slider view OR Carousel View etc.', 'blog-designer-pack'); ?></p>
												<p><?php echo sprintf( __('Check sample %sDemo%s created with Blog Designer Pack.', 'blog-designer-pack'), '<a href="https://infornweb.com/blog-designer-pack-pro-featured-and-trending-post/" target="_blank">', '</a>' ); ?></p>
											</div>
										</div>
										<div class="bdpp-getting-started bdpp-box">
											<h4 class="bdpp-custom-size"><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Display Post Timeline', 'blog-designer-pack'); ?></h4>
											<div class="bdpp-box-content">
												<p><?php esc_html_e('Display posts in timeline view.', 'blog-designer-pack'); ?></p>
												<p><?php echo sprintf( __('Check sample %sDemo%s created with Blog Designer Pack.', 'blog-designer-pack'), '<a href="https://infornweb.com/blog-designer-pack-pro-timeline-designs/" target="_blank">', '</a>' ); ?></p>
											</div>
										</div>
										
									</div><!-- .inside -->
								</div><!-- .postbox -->
							</div><!-- .meta-box-sortables -->
						</div><!-- .postbox-container -->
						
					</div><!-- #dashboard-widgets -->
				</div><!-- #dashboard-widgets-wrap -->
			</div><!-- .inside -->
		</div><!-- .postbox -->
	</div><!-- .metabox-holder -->
</div><!-- #bdpp-welcome-sett-wrp -->

<?php }

// Action to add welcome settings
add_action( 'bdp_settings_tab_welcome', 'bdp_render_welcome_settings' );