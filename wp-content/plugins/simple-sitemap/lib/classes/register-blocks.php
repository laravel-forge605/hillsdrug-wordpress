<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 *    Register blocks
 */
class Register_Blocks {

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

		add_filter( 'block_categories_all', array( &$this, 'add_block_category' ), 10, 2 );
		add_action( 'init', array( &$this, 'register_blocks' ) );
	}

	/**
	 * Add custom block category.
	 *
	 * @param array  $categories Current block categories.
	 * @param object $post Post object.
	 * @return array Result of array merge.
	 */
	public function add_block_category( $categories, $post ) {

		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'simple-sitemap',
					'title' => __( 'Simple Sitemap', 'simple-sitemap' ),
				),
			)
		);
	}

	/**
	 * Register dynamic blocks.
	 */
	public function register_blocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		$include_pro = ss_fs()->can_use_premium_code__premium_only();
		$block_root  = $this->module_roots['dir'] . 'lib/blocks/';

		$this->register_block(
			$block_root,
			'content',
			array(
				'attributes'      => Attribute_Schema::content_block_attributes( $include_pro ),
				'render_callback' => array( Simple_Sitemap_Shortcode::get_instance(), 'render_block' ),
			)
		);

		$this->register_block(
			$block_root,
			'grouped',
			array(
				'attributes'      => Attribute_Schema::group_block_attributes( $include_pro ),
				'render_callback' => array( Simple_Sitemap_Group_Shortcode::get_instance(), 'render_block' ),
			)
		);

		$this->register_block(
			$block_root,
			'child-pages',
			array(
				'attributes'      => Attribute_Schema::child_page_block_attributes( $include_pro ),
				'render_callback' => array( Child_Pages_Block::class, 'render' ),
			)
		);

		if ( $include_pro && class_exists( Pro_Block_Registry::class ) ) {
			Pro_Block_Registry::register( $this->module_roots['dir'] . 'modules/blocks/' );
		}
	}

	/**
	 * Register a shared dynamic block from metadata and a focused callback map.
	 *
	 * @param string               $block_root Shared block metadata root.
	 * @param string               $directory Block metadata directory.
	 * @param array<string, mixed> $settings Runtime registration settings.
	 * @return \WP_Block_Type|false
	 */
	private function register_block( $block_root, $directory, $settings ) {
		return register_block_type( trailingslashit( $block_root ) . $directory, $settings );
	}
} /* End class definition */
