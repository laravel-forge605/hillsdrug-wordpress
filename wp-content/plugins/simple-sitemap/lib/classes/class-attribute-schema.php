<?php
/**
 * Canonical attribute definitions shared by shortcodes and dynamic blocks.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Defines one source of truth for public sitemap attribute defaults.
 *
 * Values are stored in their internal types. Boundary methods retain the
 * historic string booleans expected by shortcode filters and Pro extensions.
 */
class Attribute_Schema {

	/**
	 * Shared Content Sitemap defaults.
	 */
	const CONTENT_DEFAULTS = array(
		'id'             => '',
		'page_depth'     => 0,
		'orderby'        => 'title',
		'order'          => 'asc',
		'show_excerpt'   => false,
		'show_label'     => true,
		'links'          => true,
		'target_blank'   => false,
		'include'        => '',
		'exclude'        => '',
		'paginate'       => false,
		'page_size'      => 50,
		'title_tag'      => '',
		'post_type_tag'  => 'h3',
		'excerpt_tag'    => 'div',
		'container_tag'  => 'ul',
		'spacing_preset' => 'inherit',
		'item_spacing'   => '',
		'nested_spacing' => '',
	);

	/**
	 * Pro Content Sitemap defaults.
	 */
	const CONTENT_PRO_DEFAULTS = array(
		'exclude_child'             => false,
		'image'                     => false,
		'image_size'                => 22,
		'list_icon'                 => true,
		'separator'                 => false,
		'horizontal'                => false,
		'horizontal_separator'      => ', ',
		'nofollow'                  => false,
		'num_posts'                 => -1,
		'visibility'                => false,
		'page_excerpt_length'       => '25',
		'sitemap_item_line_height'  => '',
		'sitemap_container_margin'  => '',
		'responsive_breakpoint'     => '500px',
		'max_width'                 => '',
		'post_type_label_padding'   => '10px 20px',
		'post_type_label_font_size' => '',
		'tab_header_bg'             => '#de5737',
		'tab_color'                 => '#ffffff',
		'respect_noindex'           => false,
		'current_language_only'     => false,
		'search'                    => false,
		'search_placeholder'        => '',
	);

	/**
	 * Shared Grouped Sitemap defaults.
	 */
	const GROUP_DEFAULTS = array(
		'id'             => '',
		'page_depth'     => 0,
		'tax'            => 'category',
		'title_tag'      => '',
		'show_excerpt'   => false,
		'excerpt_tag'    => 'div',
		'links'          => true,
		'orderby'        => 'title',
		'order'          => 'asc',
		'post_type_tag'  => 'h3',
		'show_label'     => true,
		'container_tag'  => 'ul',
		'num_terms'      => 0,
		'paginate'       => false,
		'page_size'      => 50,
		'spacing_preset' => 'inherit',
		'item_spacing'   => '',
		'nested_spacing' => '',
	);

	/**
	 * Pro Grouped Sitemap defaults.
	 */
	const GROUP_PRO_DEFAULTS = array(
		'type'                      => 'post',
		'term_orderby'              => 'name',
		'term_order'                => 'asc',
		'separator'                 => false,
		'image'                     => false,
		'image_size'                => 22,
		'list_icon'                 => true,
		'include_terms'             => '',
		'exclude_terms'             => '',
		'visibility'                => false,
		'num_posts'                 => -1,
		'nofollow'                  => false,
		'taxonomy_links'            => false,
		'term_tag'                  => 'h3',
		'render_class'              => '',
		'post_type_label_font_size' => '',
		'sitemap_item_line_height'  => '',
		'sitemap_container_margin'  => '',
		'respect_noindex'           => false,
		'current_language_only'     => false,
		'search'                    => false,
		'search_placeholder'        => '',
	);

	/**
	 * Child Pages block defaults.
	 */
	const CHILD_PAGE_DEFAULTS = array(
		'parent_id'      => 0,
		'depth'          => 0,
		'orderby'        => 'menu_order',
		'order'          => 'asc',
		'show_parent'    => false,
		'spacing_preset' => 'inherit',
		'item_spacing'   => '',
		'nested_spacing' => '',
	);

	/**
	 * Pro Child Pages block defaults.
	 */
	const CHILD_PAGE_PRO_DEFAULTS = array(
		'include'               => '',
		'exclude'               => '',
		'exclude_descendants'   => false,
		'show_excerpt'          => false,
		'excerpt_length'        => 25,
		'image'                 => false,
		'image_size'            => 48,
		'separator'             => false,
		'nofollow'              => false,
		'post_type'             => 'page',
		'respect_noindex'       => false,
		'current_language_only' => false,
	);

	/**
	 * Pro Taxonomy Terms block defaults.
	 */
	const TAXONOMY_TERMS_BLOCK_DEFAULTS = array(
		'taxonomy'       => 'category',
		'include'        => '',
		'exclude'        => '',
		'depth'          => 0,
		'child_of'       => 0,
		'title_li'       => '',
		'nofollow'       => false,
		'show_count'     => false,
		'orderby'        => 'name',
		'order'          => 'ASC',
		'hide_empty'     => false,
		'spacing_preset' => 'inherit',
		'item_spacing'   => '',
		'nested_spacing' => '',
	);

	/**
	 * Pro Navigation Menu block defaults.
	 */
	const NAVIGATION_MENU_BLOCK_DEFAULTS = array(
		'menu'                 => '',
		'label'                => '',
		'menu_class'           => 'simple-sitemap-nav-menu',
		'horizontal_separator' => '',
		'list_icon'            => true,
		'include_menu_ids'     => '',
		'exclude_menu_ids'     => '',
		'spacing_preset'       => 'inherit',
		'item_spacing'         => '',
		'nested_spacing'       => '',
	);

	/**
	 * Pro Archive Links block defaults.
	 */
	const ARCHIVE_LINKS_BLOCK_DEFAULTS = array(
		'source'         => 'monthly',
		'post_type'      => 'post',
		'taxonomy'       => 'category',
		'limit'          => 12,
		'order'          => 'DESC',
		'label'          => '',
		'heading_level'  => 3,
		'show_count'     => false,
		'hide_empty'     => true,
		'nofollow'       => false,
		'spacing_preset' => 'inherit',
		'item_spacing'   => '',
		'nested_spacing' => '',
	);

	/**
	 * Pro WooCommerce Product Sitemap block defaults.
	 */
	const PRODUCT_SITEMAP_BLOCK_DEFAULTS = array(
		'layout'                => 'list',
		'category_ids'          => '',
		'include'               => '',
		'exclude'               => '',
		'orderby'               => 'title',
		'order'                 => 'ASC',
		'limit'                 => 50,
		'stock_status'          => 'any',
		'featured_only'         => false,
		'on_sale_only'          => false,
		'label'                 => '',
		'heading_level'         => 2,
		'show_price'            => true,
		'show_sku'              => false,
		'show_stock_status'     => false,
		'show_excerpt'          => false,
		'show_image'            => false,
		'image_size'            => 48,
		'nofollow'              => false,
		'respect_noindex'       => false,
		'current_language_only' => false,
		'spacing_preset'        => 'inherit',
		'item_spacing'          => '',
		'nested_spacing'        => '',
	);

	/**
	 * Pro Child Pages shortcode defaults.
	 */
	const CHILD_SHORTCODE_DEFAULTS = array(
		'include'             => '',
		'exclude'             => '',
		'child_of'            => '0',
		'title_li'            => '',
		'nofollow'            => 'false',
		'post_type'           => 'page',
		'show_excerpt'        => 'false',
		'page_excerpt_length' => '25',
		'separator'           => 'false',
		'image'               => 'false',
		'image_size'          => '24',
		'spacing_preset'      => 'inherit',
		'item_spacing'        => '',
		'nested_spacing'      => '',
	);

	/**
	 * Pro Taxonomy Terms shortcode defaults.
	 */
	const TAXONOMY_SHORTCODE_DEFAULTS = array(
		'taxonomy'       => 'category',
		'include'        => '',
		'exclude'        => '',
		'depth'          => '0',
		'child_of'       => '0',
		'title_li'       => '',
		'nofollow'       => 'false',
		'show_count'     => '0',
		'orderby'        => 'name',
		'order'          => 'ASC',
		'hide_empty'     => '0',
		'echo'           => '0',
		'spacing_preset' => 'inherit',
		'item_spacing'   => '',
		'nested_spacing' => '',
	);

	/**
	 * Pro Navigation Menu shortcode defaults.
	 */
	const NAVIGATION_MENU_SHORTCODE_DEFAULTS = array(
		'menu'                 => '',
		'container'            => false,
		'menu_class'           => 'simple-sitemap-nav-menu',
		'horizontal_separator' => ', ',
		'list_icon'            => 'true',
		'container_class'      => '',
		'label'                => '',
		'exclude_menu_ids'     => '',
		'include_menu_ids'     => '',
		'spacing_preset'       => 'inherit',
		'item_spacing'         => '',
		'nested_spacing'       => '',
	);

	/**
	 * Content Sitemap shortcode defaults in the historic public value format.
	 *
	 * @return array<string, mixed>
	 */
	public static function content_shortcode_defaults() {
		$defaults = array_merge(
			self::CONTENT_DEFAULTS,
			array(
				'render' => '',
				'types'  => 'page',
			)
		);

		return self::to_legacy_booleans( $defaults, array( 'target_blank' ) );
	}

	/**
	 * Grouped Sitemap shortcode defaults in the historic public value format.
	 *
	 * @return array<string, mixed>
	 */
	public static function group_shortcode_defaults() {
		return self::to_legacy_booleans( self::GROUP_DEFAULTS );
	}

	/**
	 * Pro Content Sitemap shortcode defaults in the historic public format.
	 *
	 * @return array<string, mixed>
	 */
	public static function content_pro_shortcode_defaults() {
		return self::to_legacy_booleans( self::CONTENT_PRO_DEFAULTS );
	}

	/**
	 * Pro Grouped Sitemap shortcode defaults in the historic public format.
	 *
	 * @return array<string, mixed>
	 */
	public static function group_pro_shortcode_defaults() {
		return self::to_legacy_booleans( self::GROUP_PRO_DEFAULTS );
	}

	/**
	 * Pro Child Pages shortcode defaults.
	 *
	 * @return array<string, mixed>
	 */
	public static function child_shortcode_defaults() {
		return self::CHILD_SHORTCODE_DEFAULTS;
	}

	/**
	 * Pro Taxonomy Terms shortcode defaults.
	 *
	 * @return array<string, mixed>
	 */
	public static function taxonomy_shortcode_defaults() {
		return self::TAXONOMY_SHORTCODE_DEFAULTS;
	}

	/**
	 * Pro Navigation Menu shortcode defaults.
	 *
	 * @return array<string, mixed>
	 */
	public static function navigation_menu_shortcode_defaults() {
		return self::NAVIGATION_MENU_SHORTCODE_DEFAULTS;
	}

	/**
	 * Dynamic-block attribute schema for the existing Content Sitemap block.
	 *
	 * @param bool $include_pro Include licensed Pro attributes.
	 * @return array<string, array<string, mixed>>
	 */
	public static function content_block_attributes( $include_pro = false ) {
		$defaults = array_merge(
			self::CONTENT_DEFAULTS,
			array(
				'render_tab'       => false,
				'block_post_types' => '[{ "value": "page", "label": "Pages" }]',
				'gutenberg_block'  => true,
			)
		);
		unset( $defaults['target_blank'] );
		$defaults['target_blank'] = false;

		$types = array(
			'id'               => 'string',
			'page_depth'       => 'number',
			'orderby'          => 'string',
			'order'            => 'string',
			'show_excerpt'     => 'boolean',
			'show_label'       => 'boolean',
			'links'            => 'boolean',
			'target_blank'     => 'boolean',
			'include'          => 'string',
			'exclude'          => 'string',
			'paginate'         => 'boolean',
			'page_size'        => 'number',
			'title_tag'        => 'string',
			'post_type_tag'    => 'string',
			'excerpt_tag'      => 'string',
			'container_tag'    => 'string',
			'spacing_preset'   => 'string',
			'item_spacing'     => 'string',
			'nested_spacing'   => 'string',
			'render_tab'       => 'boolean',
			'block_post_types' => 'string',
			'gutenberg_block'  => 'boolean',
		);

		if ( $include_pro ) {
			$defaults                     = array_merge( $defaults, self::CONTENT_PRO_DEFAULTS );
			$defaults['section_settings'] = array();
			$types                        = array_merge(
				$types,
				array(
					'exclude_child'             => 'boolean',
					'image'                     => 'boolean',
					'image_size'                => 'number',
					'list_icon'                 => 'boolean',
					'separator'                 => 'boolean',
					'horizontal'                => 'boolean',
					'horizontal_separator'      => 'string',
					'nofollow'                  => 'boolean',
					'num_posts'                 => 'number',
					'visibility'                => 'boolean',
					'page_excerpt_length'       => 'string',
					'sitemap_item_line_height'  => 'string',
					'sitemap_container_margin'  => 'string',
					'responsive_breakpoint'     => 'string',
					'max_width'                 => 'string',
					'post_type_label_padding'   => 'string',
					'post_type_label_font_size' => 'string',
					'tab_header_bg'             => 'string',
					'tab_color'                 => 'string',
					'respect_noindex'           => 'boolean',
					'current_language_only'     => 'boolean',
					'search'                    => 'boolean',
					'search_placeholder'        => 'string',
					'section_settings'          => 'object',
				)
			);
		}

		$schema                    = self::build_block_schema( $defaults, $types );
		$schema['post_type_label'] = array( 'type' => 'object' );

		return $schema;
	}

	/**
	 * Dynamic-block attribute schema for the existing Grouped Sitemap block.
	 *
	 * @param bool $include_pro Include licensed Pro attributes.
	 * @return array<string, array<string, mixed>>
	 */
	public static function group_block_attributes( $include_pro = false ) {
		$defaults = array_merge(
			self::GROUP_DEFAULTS,
			array(
				'block_taxonomy'  => 'category',
				'block_post_type' => 'post',
				'gutenberg_block' => true,
			)
		);
		unset( $defaults['tax'] );

		$types = array(
			'id'              => 'string',
			'page_depth'      => 'number',
			'block_taxonomy'  => 'string',
			'title_tag'       => 'string',
			'show_excerpt'    => 'boolean',
			'excerpt_tag'     => 'string',
			'links'           => 'boolean',
			'orderby'         => 'string',
			'order'           => 'string',
			'post_type_tag'   => 'string',
			'show_label'      => 'boolean',
			'container_tag'   => 'string',
			'num_terms'       => 'number',
			'paginate'        => 'boolean',
			'page_size'       => 'number',
			'spacing_preset'  => 'string',
			'item_spacing'    => 'string',
			'nested_spacing'  => 'string',
			'block_post_type' => 'string',
			'gutenberg_block' => 'boolean',
		);

		if ( $include_pro ) {
			$defaults = array_merge( $defaults, self::GROUP_PRO_DEFAULTS );
			$types    = array_merge(
				$types,
				array(
					'term_orderby'              => 'string',
					'term_order'                => 'string',
					'separator'                 => 'boolean',
					'image'                     => 'boolean',
					'image_size'                => 'number',
					'list_icon'                 => 'boolean',
					'include_terms'             => 'string',
					'exclude_terms'             => 'string',
					'visibility'                => 'boolean',
					'num_posts'                 => 'number',
					'nofollow'                  => 'boolean',
					'taxonomy_links'            => 'boolean',
					'term_tag'                  => 'string',
					'render_class'              => 'string',
					'post_type_label_font_size' => 'string',
					'sitemap_item_line_height'  => 'string',
					'sitemap_container_margin'  => 'string',
					'respect_noindex'           => 'boolean',
					'current_language_only'     => 'boolean',
					'search'                    => 'boolean',
					'search_placeholder'        => 'string',
				)
			);
		}

		return self::build_block_schema( $defaults, $types );
	}

	/**
	 * Dynamic-block attribute schema for the Child Pages block.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function child_page_block_attributes( $include_pro = false ) {
		$defaults = self::CHILD_PAGE_DEFAULTS;
		$types    = array(
			'parent_id'      => 'number',
			'depth'          => 'number',
			'orderby'        => 'string',
			'order'          => 'string',
			'show_parent'    => 'boolean',
			'spacing_preset' => 'string',
			'item_spacing'   => 'string',
			'nested_spacing' => 'string',
		);

		if ( $include_pro ) {
			$defaults = array_merge( $defaults, self::CHILD_PAGE_PRO_DEFAULTS );
			$types    = array_merge(
				$types,
				array(
					'include'               => 'string',
					'exclude'               => 'string',
					'exclude_descendants'   => 'boolean',
					'show_excerpt'          => 'boolean',
					'excerpt_length'        => 'number',
					'image'                 => 'boolean',
					'image_size'            => 'number',
					'separator'             => 'boolean',
					'nofollow'              => 'boolean',
					'post_type'             => 'string',
					'respect_noindex'       => 'boolean',
					'current_language_only' => 'boolean',
				)
			);
		}

		return self::build_block_schema( $defaults, $types );
	}

	/**
	 * Dynamic-block attribute schema for the Pro Taxonomy Terms block.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function taxonomy_terms_block_attributes() {
		return self::build_block_schema(
			self::TAXONOMY_TERMS_BLOCK_DEFAULTS,
			array(
				'taxonomy'       => 'string',
				'include'        => 'string',
				'exclude'        => 'string',
				'depth'          => 'number',
				'child_of'       => 'number',
				'title_li'       => 'string',
				'nofollow'       => 'boolean',
				'show_count'     => 'boolean',
				'orderby'        => 'string',
				'order'          => 'string',
				'hide_empty'     => 'boolean',
				'spacing_preset' => 'string',
				'item_spacing'   => 'string',
				'nested_spacing' => 'string',
			)
		);
	}

	/**
	 * Dynamic-block attribute schema for the Pro Navigation Menu block.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function navigation_menu_block_attributes() {
		return self::build_block_schema(
			self::NAVIGATION_MENU_BLOCK_DEFAULTS,
			array(
				'menu'                 => 'string',
				'label'                => 'string',
				'menu_class'           => 'string',
				'horizontal_separator' => 'string',
				'list_icon'            => 'boolean',
				'include_menu_ids'     => 'string',
				'exclude_menu_ids'     => 'string',
				'spacing_preset'       => 'string',
				'item_spacing'         => 'string',
				'nested_spacing'       => 'string',
			)
		);
	}

	/**
	 * Dynamic-block attribute schema for the Pro Archive Links block.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function archive_links_block_attributes() {
		return self::build_block_schema(
			self::ARCHIVE_LINKS_BLOCK_DEFAULTS,
			array(
				'source'         => 'string',
				'post_type'      => 'string',
				'taxonomy'       => 'string',
				'limit'          => 'number',
				'order'          => 'string',
				'label'          => 'string',
				'heading_level'  => 'number',
				'show_count'     => 'boolean',
				'hide_empty'     => 'boolean',
				'nofollow'       => 'boolean',
				'spacing_preset' => 'string',
				'item_spacing'   => 'string',
				'nested_spacing' => 'string',
			)
		);
	}

	/**
	 * Dynamic-block attribute schema for the Pro Product Sitemap block.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function product_sitemap_block_attributes() {
		return self::build_block_schema(
			self::PRODUCT_SITEMAP_BLOCK_DEFAULTS,
			array(
				'layout'                => 'string',
				'category_ids'          => 'string',
				'include'               => 'string',
				'exclude'               => 'string',
				'orderby'               => 'string',
				'order'                 => 'string',
				'limit'                 => 'number',
				'stock_status'          => 'string',
				'featured_only'         => 'boolean',
				'on_sale_only'          => 'boolean',
				'label'                 => 'string',
				'heading_level'         => 'number',
				'show_price'            => 'boolean',
				'show_sku'              => 'boolean',
				'show_stock_status'     => 'boolean',
				'show_excerpt'          => 'boolean',
				'show_image'            => 'boolean',
				'image_size'            => 'number',
				'nofollow'              => 'boolean',
				'respect_noindex'       => 'boolean',
				'current_language_only' => 'boolean',
				'spacing_preset'        => 'string',
				'item_spacing'          => 'string',
				'nested_spacing'        => 'string',
			)
		);
	}

	/**
	 * Convert internal booleans to the strings expected by legacy filters.
	 *
	 * @param array<string, mixed> $defaults Attribute defaults.
	 * @param array<int, string>   $native_boolean_keys Keys that historically used native booleans.
	 * @return array<string, mixed>
	 */
	private static function to_legacy_booleans( $defaults, $native_boolean_keys = array() ) {
		foreach ( $defaults as $key => $value ) {
			if ( is_bool( $value ) && ! in_array( $key, $native_boolean_keys, true ) ) {
				$defaults[ $key ] = $value ? 'true' : 'false';
			}
		}

		return $defaults;
	}

	/**
	 * Build the WordPress block attribute shape from defaults and scalar types.
	 *
	 * @param array<string, mixed>  $defaults Attribute defaults.
	 * @param array<string, string> $types Attribute types.
	 * @return array<string, array<string, mixed>>
	 */
	private static function build_block_schema( $defaults, $types ) {
		$schema = array();
		foreach ( $types as $key => $type ) {
			$schema[ $key ] = array(
				'type'    => $type,
				'default' => $defaults[ $key ],
			);
		}

		return $schema;
	}
}
