<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Plugin options class.
 */
class Settings {
    /**
     * Common root paths/directories.
     *
     * @var array<string, string>
     */
    protected $module_roots;

    /**
     * Plugin data.
     *
     * @var array
     */
    protected $plugin_data;

    /**
     * Custom plugin data.
     *
     * @var Constants
     */
    protected $custom_plugin_data;

    /**
     * Hook prefix for the plugin.
     *
     * @var string
     */
    protected $hook_prefix;

    /**
     * Freemius upgrade URL.
     *
     * @var string
     */
    protected $freemius_upgrade_url;

    /**
     * Utility class instance.
     *
     * @var Utility
     */
    protected $utility;

    /**
     * New features array.
     *
     * @var array
     */
    protected $new_features_arr;

    /**
     * HTML for displaying the PRO attribute.
     *
     * @var string
     */
    protected $pro_attribute;

    /**
     * Slug for the settings page.
     *
     * @var string
     */
    protected $settings_slug;

    /**
     * Slug for the new features page.
     *
     * @var string
     */
    protected $new_features_slug;

    /**
     * Slug for the welcome page.
     *
     * @var string
     */
    protected $welcome_slug;

    /**
     * Privacy-conscious support report service.
     *
     * @var Support_Diagnostics
     */
    protected $support_diagnostics;

    /**
     * Main class constructor.
     *
     * @param array $module_roots Root plugin path/dir.
     * @param array $plugin_data Plugin data.
     * @param Constants $custom_plugin_data Custom plugin data.
     * @param Utility   $utility Utility data.
     * @param array               $new_features_arr Plugin new features.
     * @param Support_Diagnostics $support_diagnostics Support report service.
     */
    public function __construct(
        $module_roots,
        $plugin_data,
        $custom_plugin_data,
        $utility,
        $new_features_arr,
        $support_diagnostics
    ) {
        $this->module_roots = $module_roots;
        $this->custom_plugin_data = $custom_plugin_data;
        $this->hook_prefix = $this->custom_plugin_data->plugin_settings_prefix;
        $this->freemius_upgrade_url = $this->custom_plugin_data->freemius_upgrade_url;
        $this->utility = $utility;
        $this->plugin_data = $plugin_data;
        $this->new_features_arr = $new_features_arr;
        $this->support_diagnostics = $support_diagnostics;
        $this->pro_attribute = ( $this->custom_plugin_data->is_premium ? '' : '<span class="pro" title="Upgrade now for immediate access to this feature"><a href="' . $this->freemius_upgrade_url . '">PRO</a></span>' );
        $this->settings_slug = $this->custom_plugin_data->settings_pages['settings']['slug'];
        $this->new_features_slug = $this->custom_plugin_data->settings_pages['new-features']['slug'];
        $this->welcome_slug = $this->custom_plugin_data->settings_pages['welcome']['slug'];
        add_action( 'admin_init', array(&$this, 'register_plugin_settings') );
        add_action( 'admin_menu', array(&$this, 'add_options_page') );
    }

    /**
     * Register plugin options with Settings API.
     */
    public function register_plugin_settings() {
        /* Register plugin options settings for all tabs. */
        register_setting( 'simple_sitemap_options_group', 'simple_sitemap_options', array($this, 'sanitize_plugin_options') );
    }

    /**
     * Register plugin options page, and enqueue scripts/styles.
     */
    public function add_options_page() {
        // @todo calc this in constants.php just once and pass it in.
        $opt_pfx = $this->custom_plugin_data->db_option_prefix;
        $new_features_number = Upgrade::calc_new_features( $opt_pfx, $this->new_features_arr, $this->plugin_data );
        $title = ( 0 === $new_features_number ? __( 'Simple Sitemap', 'simple-sitemap' ) : 'Sitemap <span class="update-plugins count-' . $new_features_number . '"><span class="plugin-count">' . $new_features_number . '</span></span>' );
        add_menu_page(
            __( 'Simple Sitemap Settings Page', 'simple-sitemap' ),
            $title,
            'manage_options',
            $this->settings_slug,
            array(&$this, 'render'),
            'dashicons-pressthis',
            82
        );
        // Make the settings page have a 'Settings' menu title rather than 'Simple Sitemap'.
        add_submenu_page(
            'simple-sitemap-menu',
            'Simple Sitemap Settings Page',
            'Settings',
            'manage_options',
            $this->settings_slug
        );
    }

    /**
     * Sanitize plugin options.
     *
     * @param array $input Current input content.
     * @return array $input Sanitized input content.
     */
    public function sanitize_plugin_options( $input ) {
        // Strip html from textboxes.
        $input['txtar_sitemap_script'] = wp_filter_nohtml_kses( ( isset( $input['txtar_sitemap_script'] ) ? $input['txtar_sitemap_script'] : '' ) );
        $input['txt_exclude_parent_pages'] = wp_filter_nohtml_kses( ( isset( $input['txt_exclude_parent_pages'] ) ? $input['txt_exclude_parent_pages'] : '' ) );
        $input['sitemap_spacing_preset'] = Sitemap_Styles::sanitize_preset( ( isset( $input['sitemap_spacing_preset'] ) ? $input['sitemap_spacing_preset'] : 'inherit' ) );
        $input['sitemap_item_spacing'] = Sitemap_Styles::sanitize_spacing( ( isset( $input['sitemap_item_spacing'] ) ? $input['sitemap_item_spacing'] : '' ) );
        $input['sitemap_nested_spacing'] = Sitemap_Styles::sanitize_spacing( ( isset( $input['sitemap_nested_spacing'] ) ? $input['sitemap_nested_spacing'] : '' ) );
        // Sanitize plugin options via this filter hook. Allows you to sanitize options via another class.
        // return Hooks::wpgo_sanitize_plugin_options( $input );
        return $input;
    }

    /**
     * Display plugin options page.
     */
    public function render() {
        $freemius_upgrade_url = admin_url() . 'admin.php?page=simple-sitemap-menu-pricing';
        $pro_attribute = '';
        $is_premium = ss_fs()->can_use_premium_code__premium_only();
        $support_url = ( $is_premium ? admin_url( 'admin.php?page=simple-sitemap-menu-contact' ) : 'https://wordpress.org/support/plugin/simple-sitemap/' );
        $support_label = ( $is_premium ? __( 'Contact Pro support', 'simple-sitemap' ) : __( 'Visit the Free support forum', 'simple-sitemap' ) );
        $support_description = ( $is_premium ? __( 'Get licensed support or request a feature through the private support channel.', 'simple-sitemap' ) : __( 'Ask a usage question or report a Free-edition issue in the public WordPress.org forum.', 'simple-sitemap' ) );
        if ( !$is_premium ) {
            $pro_attribute = '&nbsp;<span class="pro" title="Click to get immediate access to this feature"><a href="' . $freemius_upgrade_url . '">PRO</a></span>';
        }
        ?>
		<div class="wrap welcome main no-tabs">

		<?php 
        // Display setting update messages.
        settings_errors();
        ?>
			<div class="wpgo-settings-inner">

				<div class="wpgo-settings-page-header">
					<h1 class="heading">
							<img src="<?php 
        echo esc_url( $this->module_roots['uri'] . '/lib/assets/images/simple-sitemap.svg' );
        ?>">
						<?php 
        esc_html_e( 'Simple Sitemap', 'simple-sitemap' );
        ?>
					</h1>
					<div class="wpgo-header-btns">
						<a class="plugin-btn" href="<?php 
        echo esc_url( $this->custom_plugin_data->welcome_url );
        ?>#getting-started">Start Here<span style="width:15px;height:15px;" class="dashicons dashicons-arrow-right-alt2"></span></a>
						<a style="background:#f5a356;border:2px #d9914e solid;" class="plugin-btn" href="<?php 
        echo esc_url( Product_Links::tracked_url( 'https://demo.wpgoplugins.com/simple-sitemap/', 'settings-live-demo' ) );
        ?>" target="_blank" rel="noopener noreferrer">Live Demo</a></span>
						<a style="background:#933c60;border:2px #6d314a solid;" class="plugin-btn" href="<?php 
        echo esc_url( Product_Links::tracked_url( 'https://wpgoplugins.com/document/simple-sitemap-pro-documentation/', 'settings-documentation' ) );
        ?>" target="_blank" rel="noopener noreferrer">Plugin Docs</a>
					</div>
				</div>

				<div class="wpgo-header-description">
					<p class="description-txt">
						To see what's new at a glance and how to use the plugin we recommend visiting the <a href="<?php 
        echo esc_url( $this->custom_plugin_data->welcome_url );
        ?>">Home</a> plugin page. Or, why not take a look at the Simple Sitemap <a href="<?php 
        echo esc_url( Product_Links::tracked_url( 'https://demo.wpgoplugins.com/simple-sitemap/', 'settings-description-demo' ) );
        ?>" target="_blank" rel="noopener noreferrer">Live Demo</a> to see plenty of sitemap examples in action.
					</p>
				</div>

			<h2 style="margin:35px 0 0 0;">Sitemap Blocks</h2>

			<p>Using blocks is the new preferred way to add content to posts and pages. The main benefit is that the editor view is exactly the same as the frontend. This means no more having to switch between the editor and frontend to check how everything looks.</p>

			<p>Expand the section directly below to see all editor blocks included with the Simple Sitemap plugin.</p>

			<div class="wpgo-expand-box" style="margin-top:20px;">
				<h4 style="margin-top:5px;display:inline-block;margin-bottom:10px;">Available Blocks</h4><button type="button" id="blocks-btn" class="button" aria-controls="blocks-wrap" aria-expanded="false">Expand <span class="dashicons dashicons-arrow-down-alt2"></span></button>

				<div style="padding-top:0;" id="blocks-wrap">

					<p><?php 
        esc_html_e( 'Simple Sitemap provides focused blocks for building and previewing visitor-friendly HTML sitemaps directly in the WordPress editor.', 'simple-sitemap' );
        ?></p>

					<p><?php 
        esc_html_e( 'The Free edition includes these blocks:', 'simple-sitemap' );
        ?></p>

					<ul>
						<li><strong><?php 
        esc_html_e( 'Simple Sitemap', 'simple-sitemap' );
        ?></strong> — <?php 
        esc_html_e( 'lists posts or pages from selected content types.', 'simple-sitemap' );
        ?></li>
						<li><strong><?php 
        esc_html_e( 'Simple Sitemap Group', 'simple-sitemap' );
        ?></strong> — <?php 
        esc_html_e( 'groups entries by taxonomy term.', 'simple-sitemap' );
        ?></li>
						<li><strong><?php 
        esc_html_e( 'Simple Sitemap: Child Pages', 'simple-sitemap' );
        ?></strong> — <?php 
        esc_html_e( 'displays a published page hierarchy below a selected parent.', 'simple-sitemap' );
        ?></li>
					</ul>

					<?php 
        ?>
						<p><?php 
        esc_html_e( 'Pro adds dedicated Taxonomy Terms, Navigation Menu, Archive Links, and WooCommerce Product blocks, plus enhanced Child Pages and per-section controls.', 'simple-sitemap' );
        ?></p>
					<?php 
        ?>

					<p><?php 
        esc_html_e( 'Existing shortcodes remain available for classic content and page-builder workflows.', 'simple-sitemap' );
        ?></p>

					<div style="margin-top:20px;"><img style="max-width:550px;" src="<?php 
        echo esc_url( $this->module_roots['pdir'] );
        ?>shared/images/simple-sitemap-block.png" /></div>

					<div>
						<h4>Usage Instructions:</h4>
						<ol>
							<li>Inside the new editor click the plus icon to insert a new block.</li>
							<li>In the popup window search for 'sitemap' or scroll down until you see the Simple Sitemap blocks section, and expand it.</li>
							<li>Click on the particular sitemap block icon you want to insert.</li>
							<li>Once the block has been added to the editor you can access block settings in the inspector panel to the right.</li>
							<li>Make sure to save your changes and then view the sitemap on the front end!</li>
						</ol>
					</div>
				</div>
			</div>

			<h2 style="margin:35px 0 0 0;">Sitemap Shortcodes</h2>

			<p>Shortcodes have been around for a long time in WordPress and, before blocks were available, they were the only really accessible way to add complex or dynamic content to the editor. We've included sitemap shortcodes for those who prefer them to blocks, or if you don't have any choice. e.g. If using a 3rd party page builder, or the block editor has been disabled.</p>

			<p style="font-size:14px;">Expand the section directly below to see all Simple Sitemap shortcodes currently available, along with a full list of supported shortcode attributes.</p>

			<div class="wpgo-expand-box">
				<h4 style="margin-top:5px;display:inline-block;margin-bottom:10px;">Available Shortcodes & Supported Attributes</h4><button type="button" id="attributes-btn" class="button" aria-controls="attributes-wrap" aria-expanded="false">Expand <span class="dashicons dashicons-arrow-down-alt2"></span></button>
				<div id="attributes-wrap" style="padding-bottom:10px;">
					<div style="padding-top:0;">
						<p>Click on a shortcode below to view the full list of supported attributes, along with a description and the default value used if the shortcode attribute is omitted.</p>
						
						<p><strong>Note:</strong> We now recommend using sitemap blocks instead of shortcodes where possible for a better user experience.</p>

						<p style="margin:25px 0 0 0;"><code style="font-size:15px;"><a class="code-link" href="#simple-sitemap-shortcode">[simple-sitemap]</a></code> <?php 
        esc_html_e( 'Displays a list of posts for one or more post types.', 'simple-sitemap' );
        ?></p>

						<p style="margin:15px 0 0 0;"><code style="font-size:15px;"><a class="code-link" href="#simple-sitemap-group-shortcode">[simple-sitemap-group]</a></code> <?php 
        esc_html_e( 'Displays a list of posts grouped by category, OR tags.', 'simple-sitemap' );
        ?></p>

						<p style="margin:15px 0 0 0;"><code style="font-size:15px;"><a class="code-link" href="#simple-sitemap-tax-shortcode">[simple-sitemap-tax]</a></code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> <?php 
        esc_html_e( 'Displays a list of taxonomy terms for any registered taxonomy (e.g. categories).', 'simple-sitemap' );
        ?></p>

						<p style="margin:15px 0 0 0;"><code style="font-size:15px;"><a class="code-link" href="#simple-sitemap-menu-shortcode">[simple-sitemap-menu]</a></code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> <?php 
        esc_html_e( 'Displays a sitemap based on a nav menu.', 'simple-sitemap' );
        ?></p>

						<p style="margin:15px 0 30px 0;"><code style="font-size:15px;"><a class="code-link" href="#simple-sitemap-child-shortcode">[simple-sitemap-child]</a></code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> <?php 
        esc_html_e( 'Displays a list of child pages for a specific parent page.', 'simple-sitemap' );
        ?></p>

						<?php 
        ?>
						<hr><br>
					</div>

					<p id="simple-sitemap-shortcode" style="margin:10px 0 20px 0;"><code style="font-size:15px;">[simple-sitemap ... ]</code></p>
					<ul class="shortcode-attributes">
						<li><code>render=""</code> - Set to "tab" to display posts in a tabbed layout!</li>
						<li><code>page_depth="0"</code> - For the 'page' post type allow the indentation depth to be specified.</li>
						<li><code>orderby="title"</code> - Value to sort posts by (title, date, author etc.). See the full list <a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">here</a>.</li>
						<li><code>order="asc"</code> - List posts for each post type in ascending, or descending order.</li>
						<li><code>show_excerpt="false"</code> - Optionally show post excerpt (if defined) under each sitemap item.</li>
						<li><code>show_label="true"</code> - Optionally show post type label above the sitemap list of posts.</li>
						<li><code>links="true"</code> - Show sitemap items as links or plain text.</li>
						<li><code>title_tag=""</code> - Tag used to wrap each sitemap item in a specified tag.</li>
						<li><code>post_type_tag="h3"</code> - Tag used to display the post type label.</li>
						<li><code>excerpt_tag="div"</code> - Tag used to wrap the post excerpt text.</li>
						<li><code>container_tag="ul"</code> - List type tag, ordered, or unordered.</li>
						<li><code>types="page"</code> - List posts or pages (or both) in the order entered. e.g. <code>types="post, page"</code></li>
						<li><code>include=""</code> - Comma separated list of post IDs to include in the sitemap only. Other posts will be ignored.</li>
						<li><code>exclude=""</code> - Comma separated list of post IDs to exclude from the sitemap.</li>
						<li><code>paginate="false"</code> - Set to "true" to use bounded, linked pagination rather than loading every item in one request. Add a unique <code>id</code> when a page contains multiple similar sitemaps.</li>
						<li><code>page_size="50"</code> - Number of sitemap entries per page when pagination is enabled (1-200).</li>
						<li><code>spacing_preset="inherit"</code> - Use the global sitemap spacing, or choose <code>compact</code>, <code>comfortable</code>, or <code>custom</code> for this shortcode.</li>
						<li><code>item_spacing=""</code> - Custom vertical spacing around sitemap items. Used with <code>spacing_preset="custom"</code>.</li>
						<li><code>nested_spacing=""</code> - Custom spacing before and after nested levels. Used with <code>spacing_preset="custom"</code>.</li>
						<li><code>image="false"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Optionally show the post featured image (if defined) next to each sitemap item.</li>
						<li><code>image_size="22"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Size of the post featured image (if displayed).</li>
						<li><code>list_icon="true"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Optionally display HTML bullet icons.</li>
						<li><code>separator="false"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Optionally render separator lines between sitemap items.</li>
						<li><code>horizontal="false"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Set to "true" to display sitemap items in a flat horizontal list. Great for adding a sitemap to the footer!</li>
						<li><code>horizontal_separator=", "</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - The character(s) used to separate sitemap items. Use with the 'horizontal' attribute.</li>
						<li><code>nofollow="false"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Set to "true" to make sitemap links <a href="https://en.wikipedia.org/wiki/Nofollow" target="_blank">nofollow</a>.</li>
						<li><code>num_posts="-1"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Limit the number of posts outputted in the sitemap.</li>
						<li><code>visibility="true"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Control whether private posts/pages are displayed in the sitemap.</li>
						<li><code>page_excerpt_length="25"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Trim page excerpt length to specific number of words.</li>
						<li><code>sitemap_item_line_height=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - CSS <code>line-height</code> for individual sitemap items.</li>
						<li><code>sitemap_container_margin=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - CSS <code>margin</code> for the sitemap container.</li>
						<li><code>responsive_breakpoint="500px"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - CSS responsive breakpoint value.</li>
						<li><code>max_width=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Controls the CSS <code>max-width</code> of the sitemap container.</li>
						<li><code>post_type_label_padding="10px 20px"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - CSS <code>padding</code> for the post type label (if displayed).</li>
						<li><code>post_type_label_font_size=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - CSS <code>font-size</code> for the post type label (if displayed).</li>
						<li><code>tab_header_bg="#de5737"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - CSS <code>background-color</code> for the active sitemap tab.</li>
						<li><code>tab_color="#ffffff"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - CSS <code>color</code> for the sitemap tab text.</li>
					</ul>

					<p id="simple-sitemap-group-shortcode" style="margin:35px 0 20px 0;"><code style="font-size:15px;">[simple-sitemap-group ... ]</code></p>

					<ul class="shortcode-attributes">
						<li><code>tax="category"</code> - List posts grouped by categories OR tags ('post_tag').</li>
						<li><code>title_tag=""</code> - Tag used to wrap each sitemap item in a specified tag.</li>
						<li><code>show_excerpt="false"</code> - Optionally show post excerpt (if defined) under each sitemap item.</li>
						<li><code>excerpt_tag="div"</code> - Tag used to wrap the post excerpt text.</li>
						<li><code>links="true"</code> - Show sitemap items as links or plain text.</li>
						<li><code>orderby="title"</code> - Value to sort posts by (title, date, author etc.). See the full list <a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">here</a>.</li>
						<li><code>order="asc"</code> - List posts for each post type in ascending, or descending order.</li>
						<li><code>post_type_tag="h3"</code> - Tag used to display the post type label.</li>
						<li><code>show_label="true"</code> - Optionally show post type label above the sitemap list of posts.</li>
						<li><code>page_depth="0"</code> - For the 'page' post type allow the indentation depth to be specified.</li>
						<li><code>container_tag="ul"</code> - List type tag, ordered, or unordered.</li>
						<li><code>num_terms="0"</code> - Limit the number of taxonomy terms displayed.</li>
						<li><code>paginate="false"</code> - Set to "true" to give each taxonomy section its own bounded, linked pagination.</li>
						<li><code>page_size="50"</code> - Number of entries per taxonomy-section page when pagination is enabled (1-200).</li>
						<li><code>spacing_preset="inherit"</code> - Use the global sitemap spacing, or choose <code>compact</code>, <code>comfortable</code>, or <code>custom</code> for this shortcode.</li>
						<li><code>item_spacing=""</code> - Custom vertical spacing around sitemap items.</li>
						<li><code>nested_spacing=""</code> - Custom spacing before and after nested levels.</li>
						<li><code>type="post"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - List posts grouped by taxonomy from ANY post type.</li>
						<li><code>term_orderby="name"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Order post taxonomy term labels by title etc.</li>
						<li><code>term_order="asc"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - List taxonomy term labels in ascending, or descending order.</li>
						<li><code>separator="false"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Optionally render separator lines between sitemap items.</li>
						<li><code>image="false"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Optionally show the post featured image (if defined) next to each sitemap item.</li>
						<li><code>list_icon="true"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Optionally display HTML bullet icons.</li>
						<li><code>include_terms=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Comma separated list of taxonomy terms to include.</li>
						<li><code>exclude_terms=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Comma separated list of taxonomy terms to exclude.</li>
						<li><code>visibility="true"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Control whether private posts/pages are displayed in the sitemap.</li>
						<li><code>num_posts="-1"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Limit the number of posts outputted in the sitemap.</li>
						<li><code>nofollow="false"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Set to "true" to make sitemap links <a href="https://en.wikipedia.org/wiki/Nofollow" target="_blank">nofollow</a>.</li>
						<li><code>taxonomy_links="false"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> Show sitemap taxonomy items as links or plain text.</li>
						<li><code>term_tag="h3"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> HTML tag for the term title.</li>
						<li><code>render_class=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> Add custom CSS class(es) to the sitemap container element.</li>
						<li><code>post_type_label_font_size=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - CSS <code>font-size</code> for the post type label (if displayed).</li>
						<li><code>sitemap_item_line_height=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - CSS <code>line-height</code> for individual sitemap items.</li>
						<li><code>sitemap_container_margin=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - CSS <code>margin</code> for the sitemap container.</li>
					</ul>

					<p id="simple-sitemap-tax-shortcode" style="margin:35px 0 20px 0;"><code style="font-size:15px;">[simple-sitemap-tax ... ]</code></p>

					<ul class="shortcode-attributes">
						<li><code>taxonomy="category"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Taxonomy type.</li>
						<li><code>include=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Comma separated list of taxonomy IDs to include in the sitemap only. Other taxonomies will be ignored.</li>
						<li><code>exclude=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Comma separated list of taxonomy IDs to exclude from the sitemap.</li>
						<li><code>depth="0"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Controls indentation depth.</li>
						<li><code>child_of="0"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Only show children of a specific category.</li>
						<li><code>title_li=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Text used for the list title element. Pass an empty string to disable.</li>
						<li><code>nofollow="false"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Set to "true" to make sitemap links <a href="https://en.wikipedia.org/wiki/Nofollow" target="_blank">nofollow</a>.</li>
						<li><code>show_count="false"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Display post counts when set to true.</li>
						<li><code>orderby="name"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Value to sort taxonomies by (name, id etc.).</li>
						<li><code>order="asc"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - List taxonomies in ascending, or descending order.</li>
						<li><code>hide_empty="0"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Hide empty taxonomies (accepts '0' or '1').</li>
						<li><code>spacing_preset="inherit"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Use the global sitemap spacing, or choose a per-shortcode preset.</li>
						<li><code>item_spacing=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Custom vertical spacing around sitemap items.</li>
						<li><code>nested_spacing=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Custom spacing before and after nested levels.</li>
					</ul>

					<p id="simple-sitemap-menu-shortcode" style="margin:35px 0 20px 0;"><code style="font-size:15px;">[simple-sitemap-menu ... ]</code></p>

					<ul class="shortcode-attributes">
						<li><code>menu=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Menu to be displayed. specify the menu name, menu ID, slug, or object. e.g. <code>menu='Main Menu'</code>.</li>
						<li><code>container="false"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Whether to wrap the ul, and what to wrap it with. e.g. <code>"div"</code>. Also accepts <code>"false"</code>.</li>
						<li><code>menu_class="simple-sitemap-nav-menu"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - CSS class to use for the ul element which forms the menu.</li>
						<li><code>horizontal_separator=", "</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Separator character used when displaying menu items as a horizontal list.</li>
						<li><code>list_icon="true"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Display list icon for each menu sitemap item.</li>
						<li><code>container_class=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Class applied to the menu container element.</li>
						<li><code>label=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Text label displayed above menu items.</li>
						<li><code>exclude_menu_ids=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Comma separated list of menu IDs to exclude from the sitemap..</li>
						<li><code>include_menu_ids=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Comma separated list of menu IDs to include in the sitemap.</li>
						<li><code>spacing_preset="inherit"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Use the global sitemap spacing, or choose a per-shortcode preset.</li>
						<li><code>item_spacing=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Custom vertical spacing around sitemap items.</li>
						<li><code>nested_spacing=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Custom spacing before and after nested levels.</li>
					</ul>

					<p id="simple-sitemap-child-shortcode" style="margin:35px 0 20px 0;"><code style="font-size:15px;">[simple-sitemap-child ... ]</code></p>

					<ul class="shortcode-attributes">
						<li><code>child_of="0"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Display only the sub-pages of a single page by ID. Default 0 (all pages).</li>
						<li><code>title_li=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Text used for the list title element. Pass an empty string to disable.</li>
						<li><code>nofollow="false"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Set to "true" to make sitemap links <a href="https://en.wikipedia.org/wiki/Nofollow" target="_blank">nofollow</a>.</li>
						<li><code>post_type="page"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Post type to query for.</li>
						<li><code>show_excerpt="false"</code> - Optionally show post excerpt (if defined) under each sitemap item.</li>
						<li><code>page_excerpt_length="25"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Trim page excerpt length to specific number of words.</li>
						<li><code>separator="false"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Add a subtle separator between child items.</li>
						<li><code>image="false"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Show compact featured-image thumbnails beside child items.</li>
						<li><code>image_size="24"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Square thumbnail size in pixels. Values from 16 to 32 pixels work well for compact child lists.</li>
						<li><code>spacing_preset="inherit"</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Use the global sitemap spacing, or choose a per-shortcode preset.</li>
						<li><code>item_spacing=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Custom vertical spacing around child items.</li>
						<li><code>nested_spacing=""</code><?php 
        echo wp_kses_post( $pro_attribute );
        ?> - Custom spacing before and after nested child levels.</li>
					</ul>
				</div>
			</div>

			<h2 style="margin:35px 0 0 0;">Sitemap Settings</h2>

			<div class="wpgo-expand-box" style="margin-top:20px;">
				<p>Use this section to manage plugin options.</p>

				<div style="padding-top:10px;" class="settings-wrap">

					<!-- Start Main Form -->
					<form id="plugin-options-form" method="post" action="options.php">
						<?php 
        $options = self::get_plugin_options();
        settings_fields( 'simple_sitemap_options_group' );
        ?>

						<table class="form-table" role="presentation">
							<tr>
								<th scope="row"><label for="simple-sitemap-spacing-preset"><?php 
        esc_html_e( 'Sitemap spacing', 'simple-sitemap' );
        ?></label></th>
								<td>
									<select id="simple-sitemap-spacing-preset" name="simple_sitemap_options[sitemap_spacing_preset]">
										<option value="inherit" <?php 
        selected( $options['sitemap_spacing_preset'], 'inherit' );
        ?>><?php 
        esc_html_e( 'Current theme/plugin default', 'simple-sitemap' );
        ?></option>
										<option value="compact" <?php 
        selected( $options['sitemap_spacing_preset'], 'compact' );
        ?>><?php 
        esc_html_e( 'Compact', 'simple-sitemap' );
        ?></option>
										<option value="comfortable" <?php 
        selected( $options['sitemap_spacing_preset'], 'comfortable' );
        ?>><?php 
        esc_html_e( 'Comfortable', 'simple-sitemap' );
        ?></option>
										<option value="custom" <?php 
        selected( $options['sitemap_spacing_preset'], 'custom' );
        ?>><?php 
        esc_html_e( 'Custom', 'simple-sitemap' );
        ?></option>
									</select>
									<p class="description"><?php 
        esc_html_e( 'Sets the default item and nested-list spacing for every Simple Sitemap block and shortcode. Existing output remains unchanged until you choose a preset or custom value.', 'simple-sitemap' );
        ?></p>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="simple-sitemap-item-spacing"><?php 
        esc_html_e( 'Custom item spacing', 'simple-sitemap' );
        ?></label></th>
								<td>
									<input id="simple-sitemap-item-spacing" type="text" class="regular-text code" name="simple_sitemap_options[sitemap_item_spacing]" value="<?php 
        echo esc_attr( $options['sitemap_item_spacing'] );
        ?>" placeholder="0.2em">
									<p class="description"><?php 
        esc_html_e( 'Vertical space around each sitemap item. Used when Custom is selected; for example 0.2em or 4px.', 'simple-sitemap' );
        ?></p>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="simple-sitemap-nested-spacing"><?php 
        esc_html_e( 'Custom nested spacing', 'simple-sitemap' );
        ?></label></th>
								<td>
									<input id="simple-sitemap-nested-spacing" type="text" class="regular-text code" name="simple_sitemap_options[sitemap_nested_spacing]" value="<?php 
        echo esc_attr( $options['sitemap_nested_spacing'] );
        ?>" placeholder="0.3em 0.5em">
									<p class="description"><?php 
        esc_html_e( 'Space before and after nested sitemap levels. Use one or two CSS length values.', 'simple-sitemap' );
        ?></p>
								</td>
							</tr>
						</table>

						<div class="simple-sitemap-pro-tab">
							<table class="form-table">

								<tr valign="top">
									<td colspan="2" style="padding:0;">
										<div>

											<label><input name="simple_sitemap_options[chk_parent_page_link]" type="checkbox" value="1" 
											<?php 
        if ( isset( $options['chk_parent_page_link'] ) ) {
            checked( '1', $options['chk_parent_page_link'] );
        }
        ?>
											> Remove parent page links?</label><br><br>

											<input type="text" class="exclude regular-text code" name="simple_sitemap_options[txt_exclude_parent_pages]" value="<?php 
        echo esc_attr( $options['txt_exclude_parent_pages'] );
        ?>">

											<p class="description">Enter comma separated list of parent page IDs to remove specific links. Leave blank to remove ALL parent page links.</p>
											</div>
									</td>
								</tr>

								<tr valign="top" style="display:none;">
									<th scope="row">Advanced Configuration</th>
									<td>
										<textarea name="simple_sitemap_options[txtar_sitemap_script]" rows="7" cols="50" type='textarea'><?php 
        echo esc_textarea( $options['txtar_sitemap_script'] );
        ?></textarea>
										<p class="description">Add script into the box above to output an advanced sitemap.</p>
									</td>
								</tr>
							</table>
						</div>

						<div class="support-tab">
							<?php 
        do_settings_sections( 'simple-sitemap-menu' );
        ?>
						</div>

						<?php 
        submit_button();
        ?>

					</form>
					<!-- main form closing tag -->

					<form action="<?php 
        echo esc_url( admin_url( 'admin-post.php' ) );
        ?>" method="post" id="simple-sitemap-reset-form" style="display:inline;">
						<?php 
        wp_nonce_field( 'simple_sitemap_reset_action', 'simple_sitemap_reset_nonce' );
        ?>
						<input type="hidden" name="action" value="simple_sitemap_reset_options">
						<div style="padding-bottom:10px;"><span id="simple-sitemap-reset"><a href="#">Reset plugin options</a></span></div>
					</form>
				</div>
			</div>

			<div style="margin-top:25px;"></div>

			<?php 
        $this->support_diagnostics->render();
        ?>

			<div style="margin-top:25px;"></div>

			<h2 style="margin:35px 0 0 0;"><?php 
        esc_html_e( 'Help, updates, and more', 'simple-sitemap' );
        ?></h2>

			<table class="form-table">
				<?php 
        if ( !$is_premium ) {
            ?>
					<tr>
						<th scope="row"><?php 
            esc_html_e( 'Support development', 'simple-sitemap' );
            ?></th>
						<td>
							<p><?php 
            esc_html_e( 'If Simple Sitemap saves you time, a donation helps fund continued maintenance and new features.', 'simple-sitemap' );
            ?></p>
							<a class="button button-secondary" href="<?php 
            echo esc_url( 'https://www.paypal.com/donate?hosted_button_id=FBAG4ZHA4TTUC' );
            ?>" target="_blank" rel="noopener noreferrer"><?php 
            esc_html_e( 'Make a donation', 'simple-sitemap' );
            ?></a>
						</td>
					</tr>
				<?php 
        }
        ?>
				<tr>
					<th scope="row"><?php 
        esc_html_e( 'Product news', 'simple-sitemap' );
        ?></th>
					<td>
						<p><?php 
        esc_html_e( 'Get release news, practical WordPress tips, and occasional offers by email.', 'simple-sitemap' );
        ?></p>
						<a class="button button-secondary" href="<?php 
        echo esc_url( 'https://eepurl.com/bXZmmD' );
        ?>" target="_blank" rel="noopener noreferrer"><?php 
        esc_html_e( 'Join the newsletter', 'simple-sitemap' );
        ?></a>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php 
        esc_html_e( 'Support and feature requests', 'simple-sitemap' );
        ?></th>
					<td>
						<p><?php 
        echo esc_html( $support_description );
        ?></p>
						<?php 
        if ( $is_premium ) {
            ?>
							<a class="button button-secondary" href="<?php 
            echo esc_url( $support_url );
            ?>"><?php 
            echo esc_html( $support_label );
            ?></a>
						<?php 
        } else {
            ?>
							<a class="button button-secondary" href="<?php 
            echo esc_url( $support_url );
            ?>" target="_blank" rel="noopener noreferrer"><?php 
            echo esc_html( $support_label );
            ?></a>
						<?php 
        }
        ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php 
        esc_html_e( 'Billing and renewals', 'simple-sitemap' );
        ?></th>
					<td>
						<p><?php 
        esc_html_e( 'Manage your license, billing, and renewals from your secure account. Deactivating a license does not cancel its subscription.', 'simple-sitemap' );
        ?></p>
						<a class="button button-secondary" href="<?php 
        echo esc_url( ss_fs()->get_account_url() );
        ?>"><?php 
        esc_html_e( 'Manage account', 'simple-sitemap' );
        ?></a>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php 
        esc_html_e( 'More WPGO Plugins', 'simple-sitemap' );
        ?></th>
					<td>
						<p><?php 
        esc_html_e( 'Explore our other focused WordPress tools.', 'simple-sitemap' );
        ?></p>
							<a class="button button-secondary" href="<?php 
        echo esc_url( Product_Links::tracked_url( 'https://wpgoplugins.com/plugins/', 'settings-browse-plugins' ) );
        ?>" target="_blank" rel="noopener noreferrer"><?php 
        esc_html_e( 'Browse plugins', 'simple-sitemap' );
        ?></a>
					</td>
				</tr>
			</table>
			</div>
		</div>
		<?php 
    }

    /**
     * Get URL of current page.
     *
     * @return string Current URL.
     */
    public static function current_url() {
        $protocol = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' );
        $host = ( isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '' );
        $uri = ( isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '' );
        return $protocol . '://' . $host . $uri;
    }

    /**
     * Get plugin option default settings.
     *
     * @return array the plugin options defaults.
     */
    public static function get_default_plugin_options() {
        return Settings_Repository::get_defaults();
    }

    /**
     * Get current plugin options.
     *
     * Merges plugin options with the defaults to ensure any gaps are filled.
     * i.e. when adding new options.
     *
     * @return array the plugin options.
     */
    public static function get_plugin_options() {
        return Settings_Repository::get_options();
    }

}
