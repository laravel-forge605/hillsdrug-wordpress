<?php
/**
 * Visibility policy shared by sitemap queries and renderers.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Centralizes private-content authorization while retaining the legacy hook.
 */
class Visibility_Policy {

	/**
	 * Apply authorized visibility settings to a prepared query.
	 *
	 * @param array<string, mixed> $query_args Query arguments.
	 * @param array<string, mixed> $args Sitemap arguments.
	 * @param string               $post_type Post type name.
	 * @return array<string, mixed>
	 */
	public static function apply_to_query( $query_args, $args, $post_type ) {
		$show_private  = isset( $args['visibility'] ) && true === Utility::filter_boolean( $args['visibility'] );
		$post_type_obj = get_post_type_object( $post_type );
		$private_cap   = is_object( $post_type_obj ) && isset( $post_type_obj->cap->read_private_posts ) ? $post_type_obj->cap->read_private_posts : '';

		if ( $show_private && $private_cap && current_user_can( $private_cap ) ) {
			$query_args['post_status'] = array( 'publish', 'private' );
			$query_args['perm']        = 'readable';
		}

		return $query_args;
	}

	/**
	 * Decide whether an already-loaded post should be omitted.
	 *
	 * The underscore-prefixed filter remains in place for backwards
	 * compatibility with Pro and existing integrations.
	 *
	 * @param int                  $post_id Post ID.
	 * @param array<string, mixed> $args Sitemap arguments.
	 * @return bool
	 */
	public static function should_skip( $post_id, $args ) {
		if ( (bool) apply_filters( '_simple_sitemap_visibility', false, $post_id, $args ) ) {
			return true;
		}

		return class_exists( Provider_Registry::class ) && ! Provider_Registry::should_include_post( $post_id, $args );
	}
}
