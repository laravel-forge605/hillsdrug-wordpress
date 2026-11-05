<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Register ready-made sitemap layouts for the block inserter.
 */
class Block_Patterns {

	/**
	 * Whether licensed starter patterns should be registered.
	 *
	 * @var bool
	 */
	private $include_pro;

	/**
	 * Register WordPress hooks.
	 *
	 * @param bool|null $include_pro Whether to include licensed patterns. Null
	 *                               detects the current Freemius edition.
	 */
	public function __construct( $include_pro = null ) {
		if ( null === $include_pro ) {
			$include_pro = function_exists( 'ss_fs' ) && ss_fs()->can_use_premium_code__premium_only();
		}

		$this->include_pro = (bool) $include_pro;
		add_action( 'init', array( $this, 'register_patterns' ) );
	}

	/**
	 * Register the pattern category and bundled starter layouts.
	 */
	public function register_patterns() {
		if ( ! function_exists( 'register_block_pattern' ) || ! function_exists( 'register_block_pattern_category' ) ) {
			return;
		}

		register_block_pattern_category(
			'simple-sitemap',
			array(
				'label' => __( 'Simple Sitemap', 'simple-sitemap' ),
			)
		);

		foreach ( $this->get_patterns() as $name => $pattern ) {
			register_block_pattern( $name, $pattern );
		}
	}

	/**
	 * Return the bundled starter patterns.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private function get_patterns() {
		$post_types = wp_json_encode(
			array(
				array(
					'value' => 'page',
					'label' => 'Pages',
				),
				array(
					'value' => 'post',
					'label' => 'Posts',
				),
			)
		);

		$patterns = array(
			'simple-sitemap/posts-and-pages'        => array(
				'title'       => __( 'Posts and Pages Sitemap', 'simple-sitemap' ),
				'description' => __( 'A visitor-friendly alphabetical sitemap containing posts and pages.', 'simple-sitemap' ),
				'categories'  => array( 'simple-sitemap' ),
				'blockTypes'  => array( 'wpgoplugins/simple-sitemap-block' ),
				'inserter'    => true,
				'content'     => $this->serialize_dynamic_block(
					'wpgoplugins/simple-sitemap-block',
					array( 'block_post_types' => $post_types )
				),
			),
			'simple-sitemap/tabbed-posts-and-pages' => array(
				'title'       => __( 'Tabbed Posts and Pages Sitemap', 'simple-sitemap' ),
				'description' => __( 'Posts and pages displayed in separate tabs.', 'simple-sitemap' ),
				'categories'  => array( 'simple-sitemap' ),
				'blockTypes'  => array( 'wpgoplugins/simple-sitemap-block' ),
				'inserter'    => true,
				'content'     => $this->serialize_dynamic_block(
					'wpgoplugins/simple-sitemap-block',
					array(
						'block_post_types' => $post_types,
						'render_tab'       => true,
					)
				),
			),
			'simple-sitemap/child-pages'            => array(
				'title'       => __( 'Child Pages Sitemap', 'simple-sitemap' ),
				'description' => __( 'A page hierarchy that starts at the current page.', 'simple-sitemap' ),
				'categories'  => array( 'simple-sitemap' ),
				'blockTypes'  => array( 'wpgoplugins/simple-sitemap-child-pages-block' ),
				'inserter'    => true,
				'content'     => $this->serialize_dynamic_block( 'wpgoplugins/simple-sitemap-child-pages-block' ),
			),
			'simple-sitemap/posts-by-category'      => array(
				'title'       => __( 'Posts Grouped by Category', 'simple-sitemap' ),
				'description' => __( 'A browsable post index organised beneath category headings.', 'simple-sitemap' ),
				'categories'  => array( 'simple-sitemap' ),
				'blockTypes'  => array( 'wpgoplugins/simple-sitemap-group-block' ),
				'inserter'    => true,
				'content'     => $this->serialize_dynamic_block( 'wpgoplugins/simple-sitemap-group-block' ),
			),
			'simple-sitemap/paginated-posts'        => array(
				'title'       => __( 'Paginated Posts Sitemap', 'simple-sitemap' ),
				'description' => __( 'A bounded alphabetical post list with ordinary linked pagination.', 'simple-sitemap' ),
				'categories'  => array( 'simple-sitemap' ),
				'blockTypes'  => array( 'wpgoplugins/simple-sitemap-block' ),
				'inserter'    => true,
				'content'     => $this->serialize_dynamic_block(
					'wpgoplugins/simple-sitemap-block',
					array(
						'block_post_types' => wp_json_encode(
							array(
								array(
									'value' => 'post',
									'label' => 'Posts',
								),
							)
						),
						'paginate'         => true,
						'page_size'        => 25,
					)
				),
			),
		);

		if ( $this->include_pro ) {
			$patterns['simple-sitemap/taxonomy-terms']   = array(
				'title'       => __( 'Category Terms Directory', 'simple-sitemap' ),
				'description' => __( 'A category hierarchy with useful content counts.', 'simple-sitemap' ),
				'categories'  => array( 'simple-sitemap' ),
				'blockTypes'  => array( 'wpgoplugins/simple-sitemap-taxonomy-terms-block' ),
				'inserter'    => true,
				'content'     => $this->serialize_dynamic_block(
					'wpgoplugins/simple-sitemap-taxonomy-terms-block',
					array( 'show_count' => true )
				),
			);
			$patterns['simple-sitemap/monthly-archives'] = array(
				'title'       => __( 'Monthly Archive Directory', 'simple-sitemap' ),
				'description' => __( 'A bounded monthly archive list with entry counts.', 'simple-sitemap' ),
				'categories'  => array( 'simple-sitemap' ),
				'blockTypes'  => array( 'wpgoplugins/simple-sitemap-archive-links-block' ),
				'inserter'    => true,
				'content'     => $this->serialize_dynamic_block(
					'wpgoplugins/simple-sitemap-archive-links-block',
					array(
						'source'     => 'monthly',
						'label'      => __( 'Browse by month', 'simple-sitemap' ),
						'show_count' => true,
					)
				),
			);
		}

		return $patterns;
	}

	/**
	 * Serialize a dynamic block without introducing saved inner markup.
	 *
	 * @param string               $name Block name.
	 * @param array<string, mixed> $attributes Block attributes.
	 * @return string
	 */
	private function serialize_dynamic_block( $name, $attributes = array() ) {
		$serialized_attributes = empty( $attributes ) ? '' : ' ' . wp_json_encode( $attributes );

		return '<!-- wp:' . $name . $serialized_attributes . ' /-->';
	}
}
