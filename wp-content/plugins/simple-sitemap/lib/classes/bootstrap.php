<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Bootstrap plugin
 */
class BootStrap {
    /**
     * Common root paths/directories.
     *
     * @var array<string, string>
     */
    protected $module_roots;

    /**
     * Main class constructor.
     */
    public function __construct() {
        $this->module_roots = Main::$module_roots;
        $this->load_supported_features();
    }

    /**
     * Load plugin features.
     */
    public function load_supported_features() {
        $root = $this->module_roots['dir'];
        // Load plugin constants/data.
        require_once $root . 'lib/classes/class-constants.php';
        $custom_plugin_data = new Constants($this->module_roots);
        $plugin_data = get_plugin_data( $this->module_roots['file'], false, false );
        // Data to pass to certain classes.
        $new_features_json = '';
        if ( file_exists( $root . 'lib/assets/misc/new-features.json' ) ) {
            $new_features_json = file_get_contents( $root . 'lib/assets/misc/new-features.json' );
        }
        require_once $root . 'lib/classes/class-utility.php';
        $utility = new Utility($this->module_roots, $custom_plugin_data);
        $new_features_arr = Utility::filter_and_decode_json( $new_features_json );
        require_once $root . 'lib/classes/class-attribute-schema.php';
        require_once $root . 'shared/class-block-wrapper.php';
        require_once $root . 'shared/class-settings-repository.php';
        new Settings_Repository();
        require_once $root . 'shared/class-sitemap-styles.php';
        require_once $root . 'shared/class-sitemap-cache.php';
        new Sitemap_Cache();
        require_once $root . 'shared/providers/class-seo-noindex-provider.php';
        require_once $root . 'shared/providers/class-language-provider.php';
        require_once $root . 'shared/providers/class-provider-registry.php';
        // Disable the Freemius feedback popup that appears when deactivating plugin.
        ss_fs()->add_filter( 'show_deactivation_feedback_form', function () {
            return false;
        } );
        // Show the amount charged each year rather than a monthly equivalent.
        ss_fs()->add_filter( 'pricing/show_annual_in_monthly', '__return_false' );
        require_once $root . 'lib/classes/class-simple-sitemap-introductory-pricing.php';
        /* translators: %s: regular annual renewal price. */
        $renews_label = __( 'Renews at %s/year.', 'simple-sitemap' );
        \Simple_Sitemap_Introductory_Pricing::register( array(
            'page'        => 'simple-sitemap-menu-pricing',
            'handle'      => 'wpgo-simple-sitemap-introductory-pricing',
            'plugin_file' => $this->module_roots['file'],
            'script'      => 'lib/assets/js/wpgo-introductory-pricing.js',
            'style'       => 'lib/assets/css/wpgo-introductory-pricing.css',
            'version'     => $plugin_data['Version'],
            'tiers'       => array(
                '1'  => array(
                    'firstYear' => '$29',
                    'renewal'   => '$39',
                    'coupon'    => 'SS1SITE10',
                ),
                '3'  => array(
                    'firstYear' => '$59',
                    'renewal'   => '$79',
                    'coupon'    => 'SS3SITE20',
                ),
                '25' => array(
                    'firstYear' => '$109',
                    'renewal'   => '$129',
                    'coupon'    => 'SS25SITE20',
                ),
            ),
            'labels'      => array(
                'firstYear' => __( 'First year', 'simple-sitemap' ),
                'renews'    => $renews_label,
            ),
        ) );
        // Enqueue plugin scripts.
        require_once $root . 'lib/classes/enqueue-scripts.php';
        new Enqueue_Scripts(
            $this->module_roots,
            $utility,
            $new_features_arr,
            $plugin_data,
            $custom_plugin_data
        );
        // Keep the legacy settings class available for its public static adapters.
        require_once $root . 'lib/classes/plugin-admin-pages/class-settings.php';
        if ( is_admin() ) {
            // Run upgrade routines and register admin pages/controllers.
            require_once $root . 'lib/classes/class-product-links.php';
            require_once $root . 'lib/classes/class-upgrade.php';
            new Upgrade($this->module_roots, $custom_plugin_data);
            require_once $root . 'lib/classes/plugin-admin-pages/class-support-diagnostics.php';
            $support_diagnostics = new Support_Diagnostics($plugin_data);
            new Settings(
                $this->module_roots,
                $plugin_data,
                $custom_plugin_data,
                $utility,
                $new_features_arr,
                $support_diagnostics
            );
            require_once $root . 'lib/classes/plugin-admin-pages/class-settings-menu-controller.php';
            new Settings_Menu_Controller($custom_plugin_data);
            require_once $root . 'lib/classes/plugin-admin-pages/class-settings-reset-controller.php';
            new Settings_Reset_Controller($custom_plugin_data);
            require_once $root . 'lib/classes/plugin-admin-pages/class-settings-new-features.php';
            new Settings_New_Features(
                $this->module_roots,
                $new_features_arr,
                $plugin_data,
                $custom_plugin_data,
                $utility
            );
            require_once $root . 'lib/classes/plugin-admin-pages/class-settings-welcome.php';
            new Settings_Welcome($this->module_roots, $plugin_data, $custom_plugin_data);
            require_once $root . 'shared/links.php';
            new Links($this->module_roots);
        }
        // Register blocks.
        require_once $root . 'lib/classes/class-block-patterns.php';
        new Block_Patterns();
        require_once $root . 'lib/classes/blocks/class-child-pages-block.php';
        require_once $root . 'lib/classes/register-blocks.php';
        new Register_Blocks($this->module_roots);
        // Sitemap shortcodes.
        require_once $root . 'lib/classes/shortcodes/shortcodes.php';
        new Shortcodes($this->module_roots);
        // Localize plugin.
        require_once $root . 'shared/localize.php';
        new Localize($this->module_roots);
        // Register endpoints.
        require_once $root . 'shared/rest-api-endpoints.php';
        new Custom_Sitemap_Endpoints($this->module_roots);
        require_once $root . 'shared/class-editor-preview-controller.php';
        new Editor_Preview_Controller();
        // Plugin hooks.
        require_once $root . 'shared/hooks.php';
        // Walker class to render hierarchical pages.
        require_once $root . 'shared/class-wpgo-walker-page.php';
    }

}

/* End class definition */