<?php
/**
 * Admin menu ordering for Simple Sitemap.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Keeps submenu composition separate from settings registration and views.
 */
class Settings_Menu_Controller {

	/**
	 * Plugin configuration.
	 *
	 * @var Constants
	 */
	private $plugin;

	/**
	 * Register menu ordering.
	 *
	 * @param Constants $plugin Plugin configuration.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		add_filter( 'custom_menu_order', array( $this, 'filter_menu_order' ) );
	}

	/**
	 * Keep Home first and put optional plugin pages after stable slug anchors.
	 *
	 * Missing Freemius or plugin submenu entries are intentionally left alone.
	 * This avoids treating array index zero as both a valid index and a sentinel.
	 *
	 * @param bool $custom Whether custom menu ordering is enabled.
	 * @return bool Whether custom menu ordering is enabled.
	 */
	public function filter_menu_order( $custom ) {
		global $submenu;

		$parent_slug = $this->plugin->parent_slug;
		if ( empty( $submenu[ $parent_slug ] ) || ! is_array( $submenu[ $parent_slug ] ) ) {
			return $custom;
		}

		$items = $submenu[ $parent_slug ];
		if ( $this->plugin->is_premium ) {
			$items = self::remove_slug( $items, $this->plugin->plugin_cpt_slug . '-wp-support-forum' );
		}

		if ( 'sub' === $this->plugin->menu_type && 'menu' === SITEMAP_FREEMIUS_NAVIGATION ) {
			$items = self::move_after(
				$items,
				$this->plugin->settings_pages['new-features']['slug'],
				$this->plugin->settings_pages['settings']['slug']
			);
		}

		if ( 'menu' === SITEMAP_FREEMIUS_NAVIGATION ) {
			$items = self::move_before(
				$items,
				$this->plugin->settings_pages['welcome']['slug'],
				$this->plugin->settings_pages['settings']['slug']
			);
		}

		$submenu[ $parent_slug ] = $items;

		return $custom;
	}

	/**
	 * Move one submenu entry immediately after another without dropping entries.
	 *
	 * @param array<int, array<int, mixed>> $items Menu items.
	 * @param string                        $source_slug Slug to move.
	 * @param string                        $anchor_slug Slug to move after.
	 * @return array<int, array<int, mixed>> Reordered menu items.
	 */
	public static function move_after( $items, $source_slug, $anchor_slug ) {
		$source_index = self::find_index( $items, $source_slug );
		$anchor_index = self::find_index( $items, $anchor_slug );
		if ( null === $source_index || null === $anchor_index || $source_index === $anchor_index ) {
			return $items;
		}

		$source = $items[ $source_index ];
		array_splice( $items, $source_index, 1 );
		$anchor_index = self::find_index( $items, $anchor_slug );
		if ( null === $anchor_index ) {
			return $items;
		}

		array_splice( $items, $anchor_index + 1, 0, array( $source ) );

		return $items;
	}

	/**
	 * Move one submenu entry immediately before another without dropping entries.
	 *
	 * @param array<int, array<int, mixed>> $items Menu items.
	 * @param string                        $source_slug Slug to move.
	 * @param string                        $anchor_slug Slug to move before.
	 * @return array<int, array<int, mixed>> Reordered menu items.
	 */
	public static function move_before( $items, $source_slug, $anchor_slug ) {
		$source_index = self::find_index( $items, $source_slug );
		$anchor_index = self::find_index( $items, $anchor_slug );
		if ( null === $source_index || null === $anchor_index || $source_index === $anchor_index ) {
			return $items;
		}

		$source = $items[ $source_index ];
		array_splice( $items, $source_index, 1 );
		$anchor_index = self::find_index( $items, $anchor_slug );
		if ( null === $anchor_index ) {
			return $items;
		}

		array_splice( $items, $anchor_index, 0, array( $source ) );

		return $items;
	}

	/**
	 * Remove one optional submenu entry without changing the remaining order.
	 *
	 * @param array<int, array<int, mixed>> $items Menu items.
	 * @param string                        $slug Slug to remove.
	 * @return array<int, array<int, mixed>>
	 */
	private static function remove_slug( $items, $slug ) {
		$index = self::find_index( $items, $slug );
		if ( null !== $index ) {
			array_splice( $items, $index, 1 );
		}

		return $items;
	}

	/**
	 * Find a submenu entry by its WordPress slug field.
	 *
	 * @param array<int, array<int, mixed>> $items Menu items.
	 * @param string                        $slug Menu slug.
	 * @return int|null
	 */
	private static function find_index( $items, $slug ) {
		foreach ( $items as $index => $item ) {
			if ( isset( $item[2] ) && $slug === $item[2] ) {
				return $index;
			}
		}

		return null;
	}
}
