<?php
/**
 * Content-policy provider composition.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Coordinate optional SEO and multilingual integrations behind safe defaults.
 */
class Provider_Registry {

	/**
	 * Cached provider list.
	 *
	 * @var array<int, object>|null
	 */
	private static $providers;

	/**
	 * Allow providers to add narrowly scoped query arguments.
	 *
	 * @param array<string, mixed> $query_args WordPress query arguments.
	 * @param array<string, mixed> $args Sitemap arguments.
	 * @param string               $post_type Post type name.
	 * @return array<string, mixed>
	 */
	public static function filter_query_args( $query_args, $args, $post_type ) {
		foreach ( self::get_providers() as $provider ) {
			if ( is_callable( array( $provider, 'filter_query_args' ) ) ) {
				$filtered = $provider->filter_query_args( $query_args, $args, $post_type );
				if ( is_array( $filtered ) ) {
					$query_args = $filtered;
				}
			}
		}

		return (array) apply_filters( 'simple_sitemap_provider_query_args', $query_args, $args, $post_type );
	}

	/**
	 * Decide whether a loaded post remains eligible for the sitemap.
	 *
	 * @param int                  $post_id Post ID.
	 * @param array<string, mixed> $args Sitemap arguments.
	 * @return bool
	 */
	public static function should_include_post( $post_id, $args ) {
		$include = true;
		foreach ( self::get_providers() as $provider ) {
			if ( is_callable( array( $provider, 'should_include_post' ) ) && ! $provider->should_include_post( $post_id, $args ) ) {
				$include = false;
				break;
			}
		}

		return (bool) apply_filters( 'simple_sitemap_provider_should_include_post', $include, $post_id, $args );
	}

	/**
	 * Reset provider composition for deterministic tests and integrations.
	 */
	public static function reset() {
		self::$providers = null;
	}

	/**
	 * Return built-in providers plus documented third-party extensions.
	 *
	 * @return array<int, object>
	 */
	private static function get_providers() {
		if ( null === self::$providers ) {
			$providers       = array(
				new SEO_Noindex_Provider(),
				new Language_Provider(),
			);
			$filtered        = apply_filters( 'simple_sitemap_content_providers', $providers );
			self::$providers = is_array( $filtered ) ? array_values( array_filter( $filtered, 'is_object' ) ) : $providers;
		}

		return self::$providers;
	}
}
