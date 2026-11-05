<?php
/**
 * WPGO product destinations used by low-key companion discovery.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Keeps companion-product copy and URLs consistent across admin surfaces.
 */
class Product_Links {

	const CHARTQUILL_URL = 'https://wpgoplugins.com/plugins/chartquill/';
	const TABLEQUILL_URL = 'https://wpgoplugins.com/plugins/tablequill/';

	/**
	 * Add GA4 campaign parameters to a WPGO-owned destination.
	 *
	 * This keeps measurement on the destination site. The plugin does not send
	 * administrator or site data directly to Google Analytics.
	 *
	 * @param string $url     Destination URL.
	 * @param string $content Identifies the in-plugin placement.
	 * @return string
	 */
	public static function tracked_url( $url, $content ) {
		$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );

		if ( ! in_array( $host, array( 'wpgoplugins.com', 'www.wpgoplugins.com', 'demo.wpgoplugins.com' ), true ) ) {
			return $url;
		}

		$fragment_position = strpos( $url, '#' );
		$fragment          = false === $fragment_position ? '' : substr( $url, $fragment_position );
		$base_url          = false === $fragment_position ? $url : substr( $url, 0, $fragment_position );

		return add_query_arg(
			array(
				'utm_source'   => 'simple-sitemap',
				'utm_medium'   => 'wordpress-plugin',
				'utm_campaign' => 'admin-discovery',
				'utm_content'  => sanitize_key( $content ),
			),
			$base_url
		) . $fragment;
	}

	/**
	 * Return the companion plugins relevant to sitemap users.
	 *
	 * @param string $placement Source placement used in GA4 reporting.
	 * @return array<int, array{slug: string, icon: string, title: string, description: string, url: string, action: string}>
	 */
	public static function companion_plugins( $placement = 'companion' ) {
		return array(
			array(
				'slug'        => 'chartquill',
				'icon'        => 'chart-bar',
				'title'       => __( 'ChartQuill', 'simple-sitemap' ),
				'description' => __( 'Create responsive, accessible charts from WordPress or imported data.', 'simple-sitemap' ),
				'url'         => self::tracked_url( self::CHARTQUILL_URL, $placement . '-chartquill' ),
				'action'      => __( 'Explore ChartQuill', 'simple-sitemap' ),
			),
			array(
				'slug'        => 'tablequill',
				'icon'        => 'editor-table',
				'title'       => __( 'TableQuill', 'simple-sitemap' ),
				'description' => __( 'Build reusable, responsive tables from WordPress content or imported data.', 'simple-sitemap' ),
				'url'         => self::tracked_url( self::TABLEQUILL_URL, $placement . '-tablequill' ),
				'action'      => __( 'Explore TableQuill', 'simple-sitemap' ),
			),
		);
	}
}
