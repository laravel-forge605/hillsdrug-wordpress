<?php
/**
 * Premium Feature Setting Page
 * 
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function bdp_render_pro_settings() { ?>

<div id="bdpp-pro-sett-wrp" class="post-box-container bdpp-pro-sett-wrp">
	<div class="metabox-holder">
		<div id="bdpp-pro-features-sett" class="postbox bdpp-postbox">

			<div class="postbox-header">
				<h2 class="hndle">
					<span><?php esc_html_e( 'Premium Features', 'blog-designer-pack' ); ?></span> <a class="pro-badge bdpp-right" href="<?php echo esc_url( BDP_UPGRADE_URL ); ?>"><?php esc_html_e( 'Upgrade to Premium', 'blog-designer-pack' ); ?></a>
				</h2>
			</div>

			<div class="inside bdpp-postbox-inside">
				<div class="bdpp-pro-main-wrap">
					<h2 class="bdpp-custom-size bdpp-text-center">The Best Minimalist Blog Designer Plugin</h2>
					<h3 class="bdpp-text-center">Change face of static websites into new and improved <br />online news sites, magazine hubs, online blogs, portals, and more!</h3>
					<div class="bdpp-img-wrp bdpp-text-center">
						<img src="<?php echo esc_url( BDP_URL."/assets/images/pro/main-banner-1536x768.png" ); ?>" alt="main banner image" />
					</div>
					<div class="bdpp-pro-button bdpp-pro-large-button"><a href="<?php echo esc_url( BDP_UPGRADE_URL ); ?>"><?php esc_html_e( 'Upgrade to Premium', 'blog-designer-pack' ); ?></a></div>
				</div>
				<div class="bdpp-pro-main-wrap">
					<div class="bdpp-cnt-wrp">
						<h3 class="bdpp-custom-size bdpp-text-center">Get Full Premium Access, Priority Support & Smarter Savings</h3>				
						<h3 class="bdpp-text-center">More features, faster help, and plans designed to save you money. <br />Multiple currencies supported: USD, EUR & GBP </h3>
					</div>
					<div class="bdpp-pricing-plans">
						<div class="bdpp-plan-card">
							<h3>Annual Plan <span class="bdpp-plan-sub">Billed yearly</span></h3>
							<del class="bdpp-regular-price">
								<span class="bdpp-currency">$</span>59
								<span class="bdpp-duration"> /year</span>
							</del>
							<div class="bdpp-price">
								<span class="bdpp-duration">From</span>
								<span class="bdpp-currency">$</span>39
								<span class="bdpp-duration"> /year</span>
							</div>
							<ul class="bdpp-plan-features">
								<li>- Yearly Renewal, Cancel Anytime</li>
								<li>- 1 Year Updates & Support</li>	
								<li>- <strong>14-Day Risk-Free  </strong> Money-Back Guarantee</li>
							</ul>
						</div>
						<div class="bdpp-plan-card bdpp-highlight">
							<span class="bdpp-badge">🎉 Limited-Time Offer</span>
							<h3>Lifetime Plan <span class="bdpp-plan-sub">One-time payment</span></h3>
							<del class="bdpp-regular-price">
								<span class="bdpp-currency">$</span>149
								<span class="bdpp-duration"> /One-time</span>
							</del>	
							<div class="bdpp-price">
								<span class="bdpp-duration">From</span>
								<span class="bdpp-currency">$</span>99
								<span class="bdpp-duration"> /One-time</span>
							</div>
							<ul class="bdpp-plan-features">	
								<li>- Pay Once, Use Forever</li>
								<li>- Lifetime Updates & Support</li>
								<li>- <strong>14-Day Risk-Free  </strong> Money-Back Guarantee</li>
							</ul>
						</div>
					</div>
					<div class="bdpp-pro-button bdpp-pro-large-button"><a href="<?php echo esc_url( BDP_UPGRADE_URL ); ?>"><?php esc_html_e( 'Compare Premium Plans', 'blog-designer-pack' ); ?></a></div>
				</div>
				<div class="bdpp-cnt-row bdpp-pro-main-wrap">
					<div class="bdpp-cnt-wrp">
						<h3 class="bdpp-custom-size bdpp-text-center">Works with your favorite page builders <br />Elementor, WPBakery, Visual Composer, Gutenberg and etc.</h3>				
						<h3 class="bdpp-text-center">And Much More Advanced Options…</h3>
					</div>
					<div class="bdpp-img-wrp bdpp-text-center">
						<img src="<?php echo esc_url( BDP_URL."/assets/images/pro/pro-features.png" ); ?>" alt="Pro features" />
					</div>
					<div class="bdpp-pro-button bdpp-pro-large-button"><a href="<?php echo esc_url( BDP_UPGRADE_URL ); ?>"><?php esc_html_e( 'Upgrade to Premium', 'blog-designer-pack' ); ?></a></div>
				</div>
				
				<div class="bdpp-pro-main-wrap">
					<div class="bdpp-cnt-wrp">
						<h3 class="bdpp-custom-size bdpp-text-center">15+ Layouts and 90+ Designs</h3>
						<h3 class="bdpp-text-center">Create unlimited layouts with more than 90+ predefined designs includes <br /> Blog Modules, Post Sliders, Post Carousel, Timeline, GridBox, Masonry and many more...</h3>
					</div>
					<div class="bdpp-img-wrp bdpp-text-center">
						<img src="<?php echo esc_url( BDP_URL."/assets/images/pro/pro-layout-1.png" ); ?>" alt="Pro features" />
						<img src="<?php echo esc_url( BDP_URL."/assets/images/pro/pro-layout-2.png" ); ?>" alt="Pro features" />
						<img src="<?php echo esc_url( BDP_URL."/assets/images/pro/pro-layout-3.png" ); ?>" alt="Pro features" />
						<img src="<?php echo esc_url( BDP_URL."/assets/images/pro/pro-layout-4.png" ); ?>" alt="Pro features" />
						<img src="<?php echo esc_url( BDP_URL."/assets/images/pro/pro-layout-5.png" ); ?>" alt="Pro features" />
					</div>
					<div class="bdpp-pro-button bdpp-pro-large-button"><a href="<?php echo esc_url( BDP_UPGRADE_URL ); ?>"><?php esc_html_e( 'Upgrade to Premium', 'blog-designer-pack' ); ?></a></div>
				</div>
				<div class="bdpp-cnt-row bdpp-pro-main-wrap">
					<div class="bdpp-cnt-wrp">
						<h3 class="bdpp-custom-size bdpp-text-center">News and Blog Designer Pack Pro comes with more features</h3>
						<h3 class="bdpp-text-center">Everything you need to build news, blog and magazine website or blog page</h3>
					</div>
					<div class="bdpp-cnt-grid bdpp-clearfix">
						<div class="bdpp-cnt-grid-3 bdpp-columns">
							<i class="dashicons dashicons-welcome-widgets-menus"></i>
							<h3>90+ Designs</h3>
							<p>Each layout comes with predefined designs and can be customized in order to fit your website design.</p>
						</div>
						<div class="bdpp-cnt-grid-3 bdpp-columns">
							<i class="dashicons dashicons-image-flip-horizontal"></i>
							<h3>6 Types of different pagination</h3>
							<p>Each module have pagination feature. From normal pagination, next/prev, load more, and auto load on scroll.</p>
						</div>
						<div class="bdpp-cnt-grid-3 bdpp-columns">
							<i class="dashicons dashicons-admin-post"></i>
							<h3>Post Type Support</h3>
							<p>Plugin support WordPress Post type as well custom post type created by you or with any plugin.</p>
						</div>
						
						<div class="bdpp-cnt-grid-3 bdpp-columns">
							<i class="dashicons dashicons-category"></i>
							<h3>Custom Taxonomy Support</h3>
							<p>Plugin support WordPress category as well custom taxonomy type created by you or with any plugin. Plugin also enable option to upload the image for category.</p>
						</div>
						<div class="bdpp-cnt-grid-3 bdpp-columns">
							<i class="dashicons dashicons-filter"></i>
							<h3>Advanced Query Builder   </h3>
							<p>Customize queries as you want. You can easily display your posts according to different criteria. Number of posts, Category, Tag, Order By, Order, Exclude, Offset etc.</p>
						</div>
						<div class="bdpp-cnt-grid-3 bdpp-columns">
							<i class="dashicons dashicons-media-interactive"></i>
							<h3>Ready made Design Library</h3>
							<p>Plugin provide you ready made designs where you just need to add the shortcode with design number. same option given with Elementor, WPBakery page builder.</p>
						</div>
						<div class="bdpp-cnt-grid-3 bdpp-columns">
							<i class="dashicons dashicons-controls-repeat"></i>
							<h3>Template Overriding</h3>
							<p>Created with WordPress Template Functionality – Modify plugin design from your theme.</p>
						</div>
						<div class="bdpp-cnt-grid-3 bdpp-columns">
							<i class="dashicons dashicons-media-code"></i>
							<h3>No Coding Required</h3>
							<p>You can use our plugins with your favorite themes without any coding.</p>
						</div>
						<div class="bdpp-cnt-grid-3 bdpp-columns">
							<i class="dashicons dashicons-embed-generic"></i>
							<h3>Shortcode Generator</h3>
							<p>Shortcode Generator with Live Preview Panel – No hassles for documentation.</p>
						</div>
						<div class="bdpp-cnt-grid-3 bdpp-columns">
							<i class="dashicons dashicons-translation"></i>
							<h3>Translation Ready</h3>
							<p>This plugin is translation ready. We provided translation files: .po and .mo so you can easily translate it with your favorite translation tools.</p>
						</div>
						<div class="bdpp-cnt-grid-3 bdpp-columns">
							<i class="dashicons dashicons-image-rotate-left"></i>
							<h3>Responsive & Light weight</h3>
							<p>All designs are Responsive. Light weight and Fast – Created with ground level with WordPress Coding Standard.</p>
						</div>
						<div class="bdpp-cnt-grid-3 bdpp-columns">
							<i class="dashicons dashicons-testimonial"></i>
							<h3>Support & Documentation</h3>
							<p>We provide online & offline detailed documentation and also dedicated support to help you with whatever issue or questions you might have.</p>
						</div>
					</div>
					<div class="bdpp-pro-button bdpp-pro-large-button"><a href="<?php echo esc_url( BDP_UPGRADE_URL ); ?>"><?php esc_html_e( 'Upgrade to Premium', 'blog-designer-pack' ); ?></a></div>
				</div>
			</div><!-- .inside -->
		</div><!-- .postbox -->
	</div><!-- end .metabox-holder -->
</div><!-- #bdpp-pro-sett-wrp -->

<?php }

add_action( 'bdp_settings_tab_pro', 'bdp_render_pro_settings' );