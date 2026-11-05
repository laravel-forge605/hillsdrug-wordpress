<?php
/**
 * Shared sitemap spacing configuration.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Resolves global spacing defaults and per-instance overrides.
 */
class Sitemap_Styles {

	/**
	 * Named spacing presets. Values intentionally match the historic polished
	 * layout (comfortable) or offer a denser alternative (compact).
	 */
	const PRESETS = array(
		'compact'     => array(
			'item_spacing'   => '0.15em',
			'nested_spacing' => '0.25em 0.4em',
		),
		'comfortable' => array(
			'item_spacing'   => '0.35em',
			'nested_spacing' => '0.4em 0.65em',
		),
	);

	/**
	 * Public attributes shared by every sitemap block and shortcode.
	 *
	 * @return array<string, string>
	 */
	public static function attribute_defaults() {
		return array(
			'spacing_preset' => 'inherit',
			'item_spacing'   => '',
			'nested_spacing' => '',
		);
	}

	/**
	 * Build a safe inline custom-property attribute for one sitemap root.
	 *
	 * Blank global and instance values return no markup, preserving existing
	 * output and theme styling until a site owner opts into the new controls.
	 *
	 * @param array<string, mixed>  $attributes Sitemap attributes.
	 * @param array<string, string> $custom_properties Additional scoped custom properties.
	 * @return string
	 */
	public static function style_attribute( $attributes, $custom_properties = array() ) {
		$values = self::resolve( $attributes );
		$styles = array();

		if ( '' !== $values['item_spacing'] ) {
			$styles[] = '--simple-sitemap-item-spacing:' . $values['item_spacing'];
		}
		if ( '' !== $values['nested_spacing'] ) {
			$styles[] = '--simple-sitemap-nested-spacing:' . $values['nested_spacing'];
		}
		foreach ( $custom_properties as $property => $value ) {
			if ( 1 !== preg_match( '/^--[a-z0-9-]+$/', (string) $property ) ) {
				continue;
			}
			$value = Utility::sanitize_css_value( 'width', $value );
			if ( '' !== $value ) {
				$styles[] = $property . ':' . $value;
			}
		}

		return $styles ? ' style="' . esc_attr( implode( ';', $styles ) ) . '"' : '';
	}

	/**
	 * Resolve global values followed by bounded per-instance overrides.
	 *
	 * @param array<string, mixed> $attributes Sitemap attributes.
	 * @return array{item_spacing: string, nested_spacing: string}
	 */
	public static function resolve( $attributes ) {
		$options  = Settings_Repository::get_options();
		$resolved = self::resolve_source(
			isset( $options['sitemap_spacing_preset'] ) ? $options['sitemap_spacing_preset'] : 'inherit',
			isset( $options['sitemap_item_spacing'] ) ? $options['sitemap_item_spacing'] : '',
			isset( $options['sitemap_nested_spacing'] ) ? $options['sitemap_nested_spacing'] : ''
		);

		$preset = self::sanitize_preset( isset( $attributes['spacing_preset'] ) ? $attributes['spacing_preset'] : 'inherit' );
		if ( isset( self::PRESETS[ $preset ] ) ) {
			$resolved = self::PRESETS[ $preset ];
		}

		$item_spacing = self::sanitize_spacing( isset( $attributes['item_spacing'] ) ? $attributes['item_spacing'] : '' );
		if ( '' !== $item_spacing ) {
			$resolved['item_spacing'] = $item_spacing;
		}

		$nested_spacing = self::sanitize_spacing( isset( $attributes['nested_spacing'] ) ? $attributes['nested_spacing'] : '' );
		if ( '' !== $nested_spacing ) {
			$resolved['nested_spacing'] = $nested_spacing;
		}

		return $resolved;
	}

	/**
	 * Normalize one configured source.
	 *
	 * @param mixed $preset Named preset.
	 * @param mixed $item_spacing Item spacing value.
	 * @param mixed $nested_spacing Nested-list spacing value.
	 * @return array{item_spacing: string, nested_spacing: string}
	 */
	private static function resolve_source( $preset, $item_spacing, $nested_spacing ) {
		$preset = self::sanitize_preset( $preset );
		if ( isset( self::PRESETS[ $preset ] ) ) {
			return self::PRESETS[ $preset ];
		}

		return array(
			'item_spacing'   => self::sanitize_spacing( $item_spacing ),
			'nested_spacing' => self::sanitize_spacing( $nested_spacing ),
		);
	}

	/**
	 * Allow only supported preset names.
	 *
	 * @param mixed $preset Raw preset name.
	 * @return string
	 */
	public static function sanitize_preset( $preset ) {
		$preset = is_scalar( $preset ) ? sanitize_key( (string) $preset ) : 'inherit';

		return in_array( $preset, array( 'inherit', 'compact', 'comfortable', 'custom' ), true ) ? $preset : 'inherit';
	}

	/**
	 * Sanitize one or two CSS length values through WordPress' safe CSS list.
	 *
	 * @param mixed $value Raw spacing value.
	 * @return string
	 */
	public static function sanitize_spacing( $value ) {
		return Utility::sanitize_css_value( 'margin', $value );
	}
}
