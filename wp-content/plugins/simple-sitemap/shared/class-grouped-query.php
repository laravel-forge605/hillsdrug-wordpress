<?php
/**
 * Batched query plan for grouped sitemaps.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Replaces compatible per-term N+1 queries with one post query and one
 * relationship query while preserving each term's post order and markup.
 */
class Grouped_Query {

	/**
	 * Build per-term in-memory WP_Query results when all query shapes are safe.
	 *
	 * Return null to use the legacy per-term query path. Integrations can disable
	 * batching through `_simple_sitemap_group_batch_queries` if they depend on
	 * query-loop side effects beyond the documented sitemap output.
	 *
	 * @param array          $args Sitemap arguments.
	 * @param string         $post_type Post type name.
	 * @param array<\WP_Term> $terms Terms rendered by the grouped sitemap.
	 * @return array<string, \WP_Query>|null Queries indexed by term slug.
	 */
	public static function execute( $args, $post_type, $terms ) {
		if ( Sitemap_Pagination::is_enabled( $args ) || ! apply_filters( '_simple_sitemap_group_batch_queries', true, $args, $post_type, $terms ) || count( $terms ) < 2 ) {
			return null;
		}

		$base_query_args = array();
		$has_base_query  = false;
		$term_ids        = array();
		$term_limits     = array();
		foreach ( $terms as $term ) {
			$term_tax_query         = array(
				array(
					'taxonomy' => $args['tax'],
					'field'    => 'slug',
					'terms'    => $term->slug,
				),
			);
			$term_args              = $args;
			$term_args['tax_query'] = $term_tax_query;

			$query_args = Sitemap_Query::build_args( $term_args, $post_type );

			$term_limits[ $term->slug ] = isset( $query_args['posts_per_page'] ) ? (int) $query_args['posts_per_page'] : -1;

			if ( $query_args['tax_query'] !== $term_tax_query || isset( $query_args['fields'] ) ) {
				return null;
			}

			$comparable = $query_args;
			unset( $comparable['tax_query'], $comparable['posts_per_page'] );
			if ( ! $has_base_query ) {
				$base_query_args = $comparable;
				$has_base_query  = true;
			} elseif ( $base_query_args !== $comparable ) {
				return null;
			}

			$term_ids[] = (int) $term->term_id;
		}

		$combined_query_args                   = $base_query_args;
		$combined_query_args['posts_per_page'] = -1;
		$combined_query_args['tax_query']      = array(
			array(
				'taxonomy' => $args['tax'],
				'field'    => 'term_id',
				'terms'    => $term_ids,
			),
		);
		$combined_query                        = Sitemap_Query::execute( $combined_query_args );
		$combined_posts                        = array_values(
			array_filter(
				$combined_query->posts,
				function ( $post ) {
					return $post instanceof \WP_Post;
				}
			)
		);

		$post_terms = array();
		foreach ( $combined_posts as $post ) {
			$relationships = get_the_terms( $post->ID, $args['tax'] );
			if ( ! is_array( $relationships ) ) {
				continue;
			}

			foreach ( $relationships as $relationship ) {
				$post_terms[ (int) $post->ID ][ (int) $relationship->term_id ] = true;
			}
		}

		$queries = array();
		foreach ( $terms as $term ) {
			$posts = array_values(
				array_filter(
					$combined_posts,
					function ( $post ) use ( $post_terms, $term ) {
						return isset( $post_terms[ (int) $post->ID ][ (int) $term->term_id ] );
					}
				)
			);
			$limit = $term_limits[ $term->slug ];
			if ( $limit >= 0 ) {
				$posts = array_slice( $posts, 0, $limit );
			}

			$queries[ $term->slug ] = self::create_query_result( $posts, $combined_query->query_vars );
		}

		return $queries;
	}

	/**
	 * Hydrate a query-shaped result so the existing renderer and loop hooks work.
	 *
	 * @param array<\WP_Post>      $posts Ordered posts for one term.
	 * @param array<string, mixed> $query_vars Normalized WP_Query variables.
	 * @return \WP_Query
	 */
	private static function create_query_result( $posts, $query_vars ) {
		$query                = new \WP_Query();
		$query->query_vars    = $query_vars;
		$query->posts         = $posts;
		$query->post_count    = count( $posts );
		$query->found_posts   = count( $posts );
		$query->max_num_pages = $posts ? 1 : 0;
		$query->current_post  = -1;

		return $query;
	}
}
