<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Plugin utility functions.
 */
class Utility {

	/**
	 * Common root paths/directories.
	 *
	 * @var array<string, string>
	 */
	protected $module_roots;

	/**
	 * Custom plugin data.
	 *
	 * @var object
	 */
	protected $custom_plugin_data;

	/**
	 * Main class constructor.
	 *
	 * @param array $module_roots Root plugin path/dir.
	 * @param object $custom_plugin_data Plugin data.
	 */
	public function __construct( $module_roots, $custom_plugin_data ) {
		$this->module_roots       = $module_roots;
		$this->custom_plugin_data = $custom_plugin_data;
	}

	/**
	 * Return a plugin asset URL and a cache-busting version.
	 *
	 * Development builds use the file modification time when possible, while
	 * production builds use the plugin version for stable browser caching.
	 *
	 * @param string $relative_path Path relative to the plugin root.
	 * @param string $plugin_version Current plugin version.
	 * @return array{uri: string, ver: int|string}
	 */
	public function get_enqueue_version( $relative_path, $plugin_version ) {
		$relative_path = '/' . ltrim( $relative_path, '/\\' );
		$file_path     = $this->module_roots['dir'] . ltrim( $relative_path, '/' );
		$version       = $plugin_version;

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && file_exists( $file_path ) ) {
			$file_modified = filemtime( $file_path );
			if ( false !== $file_modified ) {
				$version = $file_modified;
			}
		}

		return array(
			'uri' => $this->module_roots['uri'] . $relative_path,
			'ver' => $version,
		);
	}

	/**
	 * Build attributes for the element
	 *
	 * @param array  $class_attribute Array of strings of classes names to assign this element.
	 * @param array  $style_attribute Array of style attributes for inline styling of this element.
	 * @param string $title_attribute Title for this element.
	 * @return string $el_attributes Attributes for an HTML element.
	 */
	public static function build_el_attributes( $class_attribute, $style_attribute, $title_attribute ) {
		$el_attributes = '';
		if ( ! empty( $class_attribute ) ) {
			$el_attributes .= ' class="';
			foreach ( $class_attribute as $key => $value ) {
				$el_attributes .= $value;
			}
			$el_attributes .= '"';
		}
		if ( ! empty( $style_attribute ) ) {
			$el_attributes .= ' style="';
			foreach ( $style_attribute as $key => $value ) {
				$el_attributes .= $value;
			}
			$el_attributes .= '"';
		}
		if ( ! empty( $title_attribute ) ) {
			$el_attributes .= ' title="' . $title_attribute . '"';
		}

		return $el_attributes;
	}

	/**
	 * Decode and return the JSON encoded string in the form of Object.
	 *
	 * @param string $data JSON  encoded string.
	 * @return array $new_features List of premium new available features.
	 */
	public static function filter_and_decode_json( $data ) {
		$new_features = json_decode( $data );
		if ( ! is_array( $new_features ) ) {
			return array();
		}

		if ( ss_fs()->can_use_premium_code() ) {
			// Remove all entries that are 'free-only'.
			foreach ( $new_features as $key => $new_feature ) {
				if ( 'free' === $new_feature->license ) {
					unset( $new_features[ $key ] );
				}
			}
			$new_features = array_values( $new_features ); // reindex array.
		}

		return $new_features;
	}

	/**
	 * Utilized for converting the custom styled block's border object to CSS string.
	 *
	 * @param string  $json_obj JSON encoded object.
	 * @param boolean $border_bottom Parameter for border bottom state.
	 * @param boolean $border_bottom_only Parameter for border bottom state.
	 * @param boolean $border_top_only Parameter for border top state.
	 * @param array   $args Additional arguments passed as an array.
	 * @return string CSS property and value formatted as valid CSS statement.
	 */
	public static function build_css_from_border_object( $json_obj, $border_bottom = true, $border_bottom_only = false, $border_top_only = false, $args = array() ) {
		$css_obj = json_decode( $json_obj );

		// If for any reason the parsed JSON string doesn't evaluate to an object (it should) then return no CSS.
		if ( ! is_object( $css_obj ) ) {
			return '';
		}

		// If JSON border style is in shorthand and the styles haven't been customised then output the literal value the first value is always assumed to be the border width.
		if ( property_exists( $css_obj, 'literal' ) ) {
			$literal_arr = explode( ' ', $css_obj->literal );

			if ( true === $border_bottom_only ) {
				return true === $border_bottom ? 'border-bottom: ' . $literal_arr[0] . 'px ' . $literal_arr[1] . ' ' . $literal_arr[2] . ';' : '';
			}

			if ( true === $border_top_only ) {
				return 'border-top: ' . $literal_arr[0] . 'px ' . $literal_arr[1] . ' ' . $literal_arr[2] . ';';
			}

			return 'border: ' . $literal_arr[0] . 'px ' . $literal_arr[1] . ' ' . $literal_arr[2] . ';';
		} else {
			$border_top    = 'border-top: ' . $css_obj->top->width . 'px ' . $css_obj->top->color . ' ' . $css_obj->top->style . ';';
			$border_right  = 'border-right: ' . $css_obj->right->width . 'px ' . $css_obj->right->color . ' ' . $css_obj->right->style . ';';
			$border_bottom = true === $border_bottom ? 'border-bottom: ' . $css_obj->bottom->width . 'px ' . $css_obj->bottom->color . ' ' . $css_obj->bottom->style . ';' : '';
			$border_left   = 'border-left: ' . $css_obj->left->width . 'px ' . $css_obj->left->color . ' ' . $css_obj->left->style . ';';
		}

		if ( true === $border_bottom_only ) {
			return $border_bottom;
		}

		if ( true === $border_top_only ) {
			return $border_top;
		}

		if ( '1' === $css_obj->mode ) {
			return 'border: ' . $css_obj->top->width . 'px ' . $css_obj->top->color . ' ' . $css_obj->top->style;
		}
		if ( '4' === $css_obj->mode ) {

			return $border_top . ' ' . $border_right . ' ' . $border_bottom . ' ' . $border_left;
		}

		return '';
	}

	/**
	 * Gets the bool value.
	 *
	 * @param  mixed $val value to filter.
	 * @return bool|null
	 */
	public static function filter_boolean( $val ) {
		return filter_var( $val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
	}

	/**
	 * Normalize a user-provided suffix for use in an HTML id and CSS selector.
	 *
	 * Common historic IDs containing letters, numbers, underscores, and hyphens
	 * are preserved. Other characters are replaced instead of being allowed to
	 * escape the generated selector.
	 *
	 * @param mixed  $value Raw identifier value.
	 * @param string $fallback Value to use when no identifier characters remain.
	 * @return string
	 */
	public static function sanitize_identifier( $value, $fallback = '' ) {
		if ( ! is_scalar( $value ) ) {
			return $fallback;
		}

		$identifier = preg_replace( '/[^A-Za-z0-9_-]+/', '-', trim( (string) $value ) );
		$identifier = is_string( $identifier ) ? trim( $identifier, '-' ) : '';

		return '' !== $identifier ? $identifier : $fallback;
	}

	/**
	 * Validate a CSS value for a specific property.
	 *
	 * WordPress performs the property allow-list and protocol checks. The
	 * preliminary delimiter check prevents a value from terminating the current
	 * declaration or selector before it reaches safecss_filter_attr().
	 *
	 * @param string $property CSS property name.
	 * @param mixed  $value Raw CSS value.
	 * @return string A safe value without the property name, or an empty string.
	 */
	public static function sanitize_css_value( $property, $value ) {
		if ( ! is_scalar( $value ) ) {
			return '';
		}

		$property = strtolower( trim( $property ) );
		$value    = trim( (string) $value );

		if ( '' === $value || ! preg_match( '/^[a-z-]+$/', $property ) ) {
			return '';
		}

		if ( preg_match( '/[{};<>]|\/\*|\*\/|@import|expression\s*\(|url\s*\(/i', $value ) ) {
			return '';
		}

		$declaration = safecss_filter_attr( $property . ':' . $value . ';' );
		if ( '' === $declaration ) {
			return '';
		}

		$pattern = '/^' . preg_quote( $property, '/' ) . '\s*:\s*(.*?)\s*;?$/i';
		if ( 1 !== preg_match( $pattern, $declaration, $matches ) ) {
			return '';
		}

		return trim( $matches[1] );
	}
} /* End class definition */
