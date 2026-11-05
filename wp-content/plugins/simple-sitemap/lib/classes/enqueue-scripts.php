<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Enqueue plugin scripts.
 */
class Enqueue_Scripts {
    /**
     * Common root paths/directories.
     *
     * @var array<string, string>
     */
    protected $module_roots;

    /**
     * Plugin utility instance.
     *
     * @var Utility
     */
    protected $utility;

    /**
     * New feature definitions.
     *
     * @var mixed
     */
    protected $new_features_arr;

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
     * Plugin version.
     *
     * @var string
     */
    protected $plugin_version;

    /**
     * Enqueue prefix.
     *
     * @var string
     */
    protected $enq_pfx;

    /**
     * Plugin settings prefix.
     *
     * @var string
     */
    protected $plugin_settings_prefix;

    /**
     * Main class constructor.
     *
     * @param array  $module_roots Root plugin path/dir.
     * @param Utility $utility Plugin utility instance.
     * @param mixed  $new_features_arr Plugin new features.
     * @param array  $plugin_data Plugin data.
     * @param Constants $custom_plugin_data Custom plugin data.
     */
    public function __construct(
        $module_roots,
        $utility,
        $new_features_arr,
        $plugin_data,
        $custom_plugin_data
    ) {
        $this->module_roots = $module_roots;
        $this->utility = $utility;
        $this->new_features_arr = $new_features_arr;
        $this->plugin_data = $plugin_data;
        $this->custom_plugin_data = $custom_plugin_data;
        $this->plugin_version = $custom_plugin_data->plugin_data['Version'];
        $this->enq_pfx = 'simple-sitemap';
        $this->plugin_settings_prefix = 'simple_sitemap';
        // Scripts for plugin settings page.
        add_action( 'admin_enqueue_scripts', array(&$this, 'enqueue_admin_settings_scripts') );
        // Enqueue frontend/editor scripts.
        add_action( 'init', array(&$this, 'enqueue_assets'), 5 );
        add_action( 'init', array(&$this, 'register_block_editor_assets'), 5 );
        add_action( 'enqueue_block_editor_assets', array(&$this, 'enqueue_block_editor_scripts') );
        add_filter( 'should_load_separate_core_block_assets', '__return_true' );
    }

    /**
     * Enqueue front end and editor JavaScript and CSS assets.
     */
    public function enqueue_assets() {
        $simple_sitemap_css = $this->utility->get_enqueue_version( '/lib/assets/css/simple-sitemap.css', $this->plugin_version );
        wp_register_style(
            'simple-sitemap-css',
            $simple_sitemap_css['uri'],
            array(),
            $simple_sitemap_css['ver']
        );
        $tabs_js = $this->utility->get_enqueue_version( '/lib/assets/js/simple-sitemap-tabs.js', $this->plugin_version );
        wp_register_script(
            'simple-sitemap-tabs',
            $tabs_js['uri'],
            array('wp-i18n'),
            $tabs_js['ver'],
            true
        );
    }

    /**
     * Scripts for plugin settings page only.
     *
     * @param string $hook Page hook name.
     * @return void
     */
    public function enqueue_admin_settings_scripts( $hook ) {
        if ( false !== strpos( $hook, 'simple-sitemap' ) ) {
            $admin_core_css = $this->utility->get_enqueue_version( '/lib/assets/css/admin-settings-core.css', $this->plugin_version );
            wp_enqueue_style(
                'simple-sitemap-settings-core-css',
                $admin_core_css['uri'],
                array(),
                $admin_core_css['ver']
            );
        }
        $main_settings_hooks = array('toplevel_page_simple-sitemap-menu', 'settings_page_simple-sitemap-menu', 'simple-sitemap_page_simple-sitemap-menu');
        if ( in_array( $hook, $main_settings_hooks, true ) ) {
            $ss_settings_css = $this->utility->get_enqueue_version( '/lib/assets/css/admin-settings.css', $this->plugin_version );
            $ss_settings_js = $this->utility->get_enqueue_version( '/lib/assets/js/simple-sitemap-admin.js', $this->plugin_version );
            wp_enqueue_style(
                'simple-sitemap-settings-welcome-css',
                $ss_settings_css['uri'],
                array(),
                $ss_settings_css['ver']
            );
            wp_enqueue_script(
                'simple-sitemap-settings-welcome-js',
                $ss_settings_js['uri'],
                array(),
                $ss_settings_js['ver'],
                true
            );
        }
        // Having to do it this way as for the welcome page the hook has the numbered icon number included (when rendered).
        if ( strpos( $hook, '_page_simple-sitemap-menu-welcome' ) !== false ) {
            // if ( 'simple-sitemap_page_simple-sitemap-menu-welcome' === $hook ) {
            $ss_settings_css = $this->utility->get_enqueue_version( '/lib/assets/css/admin-settings.css', $this->plugin_version );
            $ss_settings_js = $this->utility->get_enqueue_version( '/lib/assets/js/simple-sitemap-admin.js', $this->plugin_version );
            wp_enqueue_style(
                'simple-sitemap-settings-css',
                $ss_settings_css['uri'],
                array(),
                $ss_settings_css['ver']
            );
            // wp_enqueue_script( 'simple-sitemap-settings-js', $ss_settings_js['uri'], array(), $ss_settings_js['ver'] );
        }
    }

    /**
     * Register block-editor assets from build-generated dependency metadata.
     **/
    public function register_block_editor_assets() {
        $block_editor_js = $this->utility->get_enqueue_version( '/lib/block_assets/js/blocks.editor.js', $this->plugin_version );
        $asset = $this->get_asset_metadata( '/lib/block_assets/js/blocks.editor.asset.php' );
        $dependencies = $asset['dependencies'];
        // Block editor script.
        wp_register_script(
            $this->enq_pfx . '-block-editor-js',
            $block_editor_js['uri'],
            array_values( array_unique( $dependencies ) ),
            $asset['version'],
            true
        );
        $data = array(
            'is_premium'           => ss_fs()->is_premium(),
            'can_use_premium_code' => ss_fs()->can_use_premium_code(),
        );
        wp_localize_script( $this->enq_pfx . '-block-editor-js', $this->plugin_settings_prefix . '_editor_data', $data );
        $block_editor_css = $this->utility->get_enqueue_version( '/lib/assets/css/simple-sitemap-block-editor.css', $this->plugin_version );
        wp_register_style(
            'simple-sitemap-block-editor-css',
            $block_editor_css['uri'],
            array(),
            $block_editor_css['ver']
        );
    }

    /**
     * Enqueue registered editor assets while retaining the existing all-editor behavior.
     */
    public function enqueue_block_editor_scripts() {
        wp_enqueue_script( $this->enq_pfx . '-block-editor-js' );
        wp_enqueue_style( 'simple-sitemap-block-editor-css' );
    }

    /**
     * Read build-generated WordPress script dependencies and content version.
     *
     * @param string $relative_path Asset metadata path relative to the plugin.
     * @return array{dependencies: array<int, string>, version: string}
     */
    private function get_asset_metadata( $relative_path ) {
        $path = $this->module_roots['dir'] . ltrim( $relative_path, '/\\' );
        $asset = ( file_exists( $path ) ? include $path : array() );
        return array(
            'dependencies' => ( isset( $asset['dependencies'] ) && is_array( $asset['dependencies'] ) ? $asset['dependencies'] : array() ),
            'version'      => ( isset( $asset['version'] ) && is_string( $asset['version'] ) ? $asset['version'] : $this->plugin_version ),
        );
    }

}

/* End class definition */