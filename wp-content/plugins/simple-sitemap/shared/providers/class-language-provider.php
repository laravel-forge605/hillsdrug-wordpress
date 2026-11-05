<?php
/**
 * Opt-in multilingual query provider.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Scope supported WPML and Polylang queries to the current language.
 */
class Language_Provider {

	/**
	 * Add the current language to a sitemap query when explicitly enabled.
	 *
	 * @param array<string, mixed> $query_args WordPress query arguments.
	 * @param array<string, mixed> $args Sitemap arguments.
	 * @return array<string, mixed>
	 */
	public function filter_query_args( $query_args, $args ) {
		if ( empty( $args['current_language_only'] ) || ! Utility::filter_boolean( $args['current_language_only'] ) ) {
			return $query_args;
		}

		$language = apply_filters( 'wpml_current_language', null );
		if ( function_exists( 'pll_current_language' ) ) {
			$polylang_language = pll_current_language( 'slug' );
			if ( is_string( $polylang_language ) && '' !== $polylang_language ) {
				$language = $polylang_language;
			}
		}

		if ( is_string( $language ) && '' !== $language ) {
			$query_args['lang']             = sanitize_key( $language );
			$query_args['suppress_filters'] = false;
		}

		return $query_args;
	}

	/**
	 * Language integrations filter the query, so loaded posts remain eligible.
	 *
	 * @return bool
	 */
	public function should_include_post() {
		return true;
	}
}
