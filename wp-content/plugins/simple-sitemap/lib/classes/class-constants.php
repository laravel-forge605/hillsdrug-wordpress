<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Plugin constants.
 */
class Constants {

	public $plugin_data;
	public $freemius_slug;
	public $main_menu_label;
	public $plugin_slug;
	public $plugin_cpt_slug;
	public $menu_type;
	public $cpt_slug;
	public $css_prefix;
	public $filter_prefix;
	public $db_option_prefix;
	public $enqueue_prefix;
	public $plugin_settings_prefix;
	public $donation_link;
	public $duplicate_post_label;
	public $is_premium;
	public $parent_slug;
	public $settings_page_hook;
	public $settings_page_hook_top;
	public $settings_page_hook_sub;
	public $settings_pages;
	public $url_prefix;
	public $main_settings_url;
	public $welcome_url;
	public $new_features_url;
	public $freemius_upgrade_url;
	public $freemius_discount_upgrade_url;
	public $contact_us_url;
	public $admin_url;

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
		$this->define_constants();
	}

	/**
	 * Define plugin constants.
	 */
	public function define_constants() {

		// **********************
		// START - EDIT CONSTANTS
		// **********************

		$this->plugin_data            = get_plugin_data( $this->module_roots['file'], false, false );
		$this->freemius_slug          = ss_fs()->get_slug();
		$this->main_menu_label        = 'Simple Sitemap';
		$this->plugin_slug            = 'simple-sitemap-menu';
		$this->plugin_cpt_slug        = 'simple-sitemap'; // use this as plugin (menu) slug if using CPT as parent menu.
		$this->menu_type              = (string) apply_filters( 'simple_sitemap_menu_type', 'top' ); // top|top-cpt|sub.
		$this->cpt_slug               = ''; // same one used in register_post_type().
		$this->css_prefix             = 'simple-sitemap';
		$this->filter_prefix          = 'simple_sitemap';
		$this->db_option_prefix       = 'simple_sitemap';
		$this->enqueue_prefix         = 'simple-sitemap';
		$this->plugin_settings_prefix = 'simple_sitemap';
		$this->donation_link          = 'https://www.paypal.com/donate?hosted_button_id=FBAG4ZHA4TTUC';
		$this->duplicate_post_label   = '';
		$coupon                       = '&coupon=30PCOFF';

		// ********************
		// END - EDIT CONSTANTS
		// ********************

		$root = $this->module_roots['dir'];

		// Store plugin premium status in variable.
		// The admin edition follows the active entitlement, not merely the
		// premium package installed on the site.
		$this->is_premium = ss_fs()->can_use_premium_code__premium_only();

		if ( 'sub' === $this->menu_type ) {
			$this->parent_slug        = 'options-general.php';
			$this->settings_page_hook = 'settings_page_' . $this->plugin_slug; // when main settings page is a submenu under the 'Settings' menu.
		} elseif ( 'top' === $this->menu_type ) {
			$this->parent_slug            = $this->plugin_slug; // when main settings page is a top-level menu.
			$this->settings_page_hook_top = 'toplevel_page_' . $this->plugin_slug;
			// Important: WordPress calculates the first part of this string (i.e. before '_page_') by basing it on the second argument passed to add_menu_page().
			// Unfortunately we can't generalize the argument in add_menu_page() into a variable because the language translation functions don't allow vars. @TODO actually we should be able to do this with translation string functions that allow vars.
			$this->settings_page_hook_sub = $this->db_option_prefix . '_page_' . $this->plugin_slug;
		} elseif ( 'top-cpt' === $this->menu_type ) {
			$this->parent_slug            = 'edit.php?post_type=' . $this->cpt_slug; // when main settings page is a top-level menu.
			$this->settings_page_hook_top = 'edit.php';
			// Important: WordPress calculates the first part of this string (i.e. before '_page_') by basing it on the second argument passed to add_menu_page().
			// Unfortunately we can't generalize the argument in add_menu_page() into a variable because the language translation functions don't allow vars.
			$this->settings_page_hook_sub = $this->cpt_slug . '_page_' . $this->plugin_slug;
		} else {
			wp_die( 'WPGO PLUGINS ERROR [' . esc_html( $this->main_menu_label ) . ']: $this->menu_type must be one of: top|top-cpt|sub.' );
		}

		// Define settings pages used in the plugin.
		$this->settings_pages = array(
			'settings'     => array(
				'slug'      => $this->plugin_slug,
				'label'     => 'Settings',
				'css_class' => 'home',
			),
			'new-features' => array(
				'slug'      => $this->plugin_slug . '-new-features',
				'label'     => 'New Features',
				'css_class' => 'new-features',
			),
			'welcome'      => array(
				'slug'      => $this->plugin_slug . '-welcome',
				'label'     => $this->main_menu_label . ' Home',
				'css_class' => 'welcome',
			),
		);

		// Define menu prefix and upgrade URL.
		$this->url_prefix = '';
		if ( 'sub' === $this->menu_type ) {
			$this->url_prefix = 'options-general.php';
		} elseif ( 'top' === $this->menu_type ) {
			$this->url_prefix = 'admin.php';
		} elseif ( 'top-cpt' === $this->menu_type ) {
			$this->url_prefix = 'edit.php?post_type=' . $this->cpt_slug;
		}
		// If using a CPT as top-level parent then the post type is already the first query string so subsequent values are separated by '&' and not '?'.
		$query_string_prefix = 'top-cpt' === $this->menu_type ? '&' : '?';

		// For Freemius pages if CPT is used for top-level menu then use slightly different slug.
		$freemius_slug                       = 'top-cpt' === $this->menu_type ? $this->plugin_cpt_slug : $this->settings_pages['settings']['slug'];
		$this->url_prefix                   .= $query_string_prefix;
		$this->main_settings_url             = admin_url() . $this->url_prefix . 'page=' . $this->settings_pages['settings']['slug'];
		$this->welcome_url                   = admin_url() . $this->url_prefix . 'page=' . $this->settings_pages['welcome']['slug'];
		$this->new_features_url              = admin_url() . $this->url_prefix . 'page=' . $this->settings_pages['new-features']['slug'];
		$this->freemius_upgrade_url          = admin_url() . $this->url_prefix . 'page=' . $freemius_slug . '-pricing';
		$this->freemius_discount_upgrade_url = admin_url() . $this->url_prefix . 'page=' . $freemius_slug . '-pricing&checkout=true&plan_id=6617&plan_name=pro&billing_cycle=annual&pricing_id=6018&currency=usd' . $coupon;
		$this->contact_us_url                = admin_url() . $this->url_prefix . 'page=' . $freemius_slug . '-contact';
		$this->admin_url                     = admin_url();

		// Don't allow tabs to be used when the plugin uses a top-level menu.
		if ( SITEMAP_FREEMIUS_NAVIGATION === 'tabs' && ( 'top' === $this->menu_type || 'top-cpt' === $this->menu_type ) ) {
			wp_die( 'WPGO PLUGINS ERROR [' . esc_html( $this->main_menu_label ) . ']: Freemius doesn\'t support using tabs with a top-level main settings page. Please change navigation to \'menu\' or use a submenu for the main settings page.' );
		}
	}
} /* End class definition */
