<?php
/**
 * Opt-in SEO noindex provider.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Recognize conservative noindex signals from established SEO plugins.
 */
class SEO_Noindex_Provider {

	/**
	 * SEO metadata does not need to alter the base query.
	 *
	 * @param array<string, mixed> $query_args WordPress query arguments.
	 * @return array<string, mixed>
	 */
	public function filter_query_args( $query_args ) {
		return $query_args;
	}

	/**
	 * Exclude explicit noindex content only when the sitemap opts in.
	 *
	 * @param int                  $post_id Post ID.
	 * @param array<string, mixed> $args Sitemap arguments.
	 * @return bool
	 */
	public function should_include_post( $post_id, $args ) {
		if ( empty( $args['respect_noindex'] ) || ! Utility::filter_boolean( $args['respect_noindex'] ) ) {
			return true;
		}

		$yoast    = (string) get_post_meta( $post_id, '_yoast_wpseo_meta-robots-noindex', true );
		$seopress = (string) get_post_meta( $post_id, '_seopress_robots_index', true );
		$rankmath = get_post_meta( $post_id, 'rank_math_robots', true );
		$noindex  = '1' === $yoast || in_array( strtolower( $seopress ), array( '1', 'yes' ), true );

		if ( is_array( $rankmath ) ) {
			$noindex = $noindex || in_array( 'noindex', array_map( 'strtolower', $rankmath ), true );
		} elseif ( is_string( $rankmath ) ) {
			$noindex = $noindex || false !== stripos( $rankmath, 'noindex' );
		}

		$indexable = ! $noindex;

		return (bool) apply_filters( 'simple_sitemap_seo_post_is_indexable', $indexable, $post_id, $args );
	}
}
