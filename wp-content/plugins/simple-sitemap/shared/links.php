<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Main WordPress plugin index page links and admin notices.
 */
class Links {
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
        add_filter(
            'plugin_row_meta',
            array(&$this, 'plugin_row_meta_links'),
            10,
            2
        );
        add_filter(
            'plugin_action_links',
            array(&$this, 'plugin_settings_link'),
            10,
            2
        );
    }

    /**
     * Display a Settings link on the main Plugins page.
     *
     * @param array $links List of plugin links.
     * @param string $file Plugin file.
     * @return array
     */
    public function plugin_row_meta_links( $links, $file ) {
        if ( 'simple-sitemap/simple-sitemap.php' !== $file ) {
            return $links;
        }
        $freemius_upgrade_url = admin_url() . 'admin.php?page=simple-sitemap-menu-pricing';
        $pccf_links = '<a href="' . esc_url( $freemius_upgrade_url ) . '" title="' . esc_attr__( 'More sitemap features', 'simple-sitemap' ) . '"><b>' . esc_html__( 'More features', 'simple-sitemap' ) . '</b></a>';
        array_push( $links, $pccf_links );
        foreach ( Product_Links::companion_plugins( 'plugin-meta' ) as $companion_plugin ) {
            $links[] = '<a href="' . esc_url( $companion_plugin['url'] ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $companion_plugin['title'] ) . '</a>';
        }
        return $links;
    }

    /**
     * Display a Settings link on the main Plugins page.
     *
     * @param array $links List of plugin links.
     * @param string $file Plugin file.
     * @return array
     */
    public function plugin_settings_link( $links, $file ) {
        if ( 'simple-sitemap/simple-sitemap.php' === $file ) {
            $pccf_links = '<a href="' . esc_url( get_admin_url() . 'admin.php?page=simple-sitemap-menu-welcome' ) . '">' . esc_html__( 'Get Started', 'simple-sitemap' ) . '</a>';
            array_unshift( $links, $pccf_links );
        }
        return $links;
    }

}

/* End class definition */