<?php
/**
 * Opt-in rendered-list cache with centralized invalidation.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Provides a safe performance seam without changing unlimited legacy output.
 */
class Sitemap_Cache {

	/**
	 * Cache-generation option.
	 */
	const VERSION_OPTION = 'simple_sitemap_cache_version';

	/**
	 * Register content and taxonomy invalidation hooks.
	 */
	public function __construct() {
		add_action( 'save_post', array( $this, 'invalidate_post' ) );
		add_action( 'deleted_post', array( $this, 'invalidate' ) );
		add_action( 'created_term', array( $this, 'invalidate' ) );
		add_action( 'edited_term', array( $this, 'invalidate' ) );
		add_action( 'delete_term', array( $this, 'invalidate' ) );
		add_action( 'set_object_terms', array( $this, 'invalidate' ) );
		add_action( 'wp_update_nav_menu', array( $this, 'invalidate' ) );
		add_action( 'update_option_simple_sitemap_options', array( $this, 'invalidate' ) );
	}

	/**
	 * Avoid invalidating for revisions while reacting to real content saves.
	 *
	 * @param int $post_id Saved post ID.
	 */
	public function invalidate_post( $post_id ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$this->invalidate();
	}

	/**
	 * Advance the cache generation instead of enumerating transient keys.
	 */
	public function invalidate() {
		update_option( self::VERSION_OPTION, microtime( true ), false );
	}

	/**
	 * Retrieve a rendered fragment when caching was explicitly enabled.
	 *
	 * @param string $key Cache key.
	 * @return string|null
	 */
	public static function get( $key ) {
		$cached = get_transient( $key );

		return is_string( $cached ) ? $cached : null;
	}

	/**
	 * Store a rendered fragment.
	 *
	 * @param string $key Cache key.
	 * @param string $html Rendered HTML.
	 * @param int    $ttl Cache lifetime in seconds.
	 */
	public static function set( $key, $html, $ttl ) {
		set_transient( $key, $html, $ttl );
	}

	/**
	 * Create a generation-aware key for a rendered sitemap fragment.
	 *
	 * @param string               $namespace Fragment namespace.
	 * @param array<string, mixed> $payload Values that affect output.
	 * @param mixed                $version Cache generation.
	 * @return string
	 */
	public static function build_key( $namespace, $payload, $version ) {
		$encoded = wp_json_encode(
			array(
				'namespace' => $namespace,
				'payload'   => $payload,
				'version'   => $version,
			)
		);

		return 'simple_sitemap_' . hash( 'sha256', is_string( $encoded ) ? $encoded : '' );
	}

	/**
	 * Return the current cache generation.
	 *
	 * @return mixed
	 */
	public static function get_version() {
		return get_option( self::VERSION_OPTION, 1 );
	}
}
