<?php
/**
 * Shortcode Builder Class
 * Handles shortcode builder functionality
 *
 * @package Blog Designer Pack
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BDPP_Shortcode_Builder {

	function __construct() {
	}

	/**
	 * Render Fields HTML
	 * 
	 * @since 1.0
	 */
	function render( $args = array() ) {

		if ( ! empty( $args ) ) {

			$temp_dependency	= array();
			$ajax_fields		= array();

			// HTML start
			echo '<div id="bdpp-shrt-accordion" class="bdpp-shrt-accordion">';

			foreach ($args as $key => $value) {
				
				$section_title 	= isset( $value['title'] ) 		? $value['title'] 					: '';
				$section_params	= ! empty( $value['params'] )	? (array) $value['params'] 			: '';
				$is_premium		= ! empty( $value['premium'] ) 	? "bdpp-shrt-acc-header-premium"	: false;

				if( ! $section_params ) {
					continue;
				}

				echo '<div class="bdpp-shrt-acc-header '.esc_attr( $is_premium ).'">'.esc_html( $section_title ).' <i class="dashicons dashicons-warning bdpp-shrt-acc-header-warn-icon bdpp-hide"></i>';
						if( $is_premium ) {
							echo '<i class="dashicons dashicons-lock bdpp-shrt-acc-header-pro-icon"></i>';
						}
				echo '</div>';
				echo '<div class="bdpp-shrt-acc-cnt '.esc_attr( $is_premium ).'">';

				foreach ($value['params'] as $param_key => $param_val) {

					$field_type	= ! empty( $param_val['type'] ) ? $param_val['type'] : 'text';

					// If field name is not there then return
					if( empty($param_val['name']) && $field_type != 'info' ) {
						continue;
					}

					if( $field_type == 'info' ) {
						$param_val['id'] = "{$key}-{$param_key}";
					}

					$param_val['premium']		= !empty( $param_val['premium'] )		? 1		: 0;
					$param_val['allow_empty'] 	= !empty( $param_val['allow_empty'] )	? 1		: 0;
					$param_val['heading'] 		= !empty( $param_val['heading'] )		? $param_val['heading']			: '';
					$param_val['name']  		= !empty( $param_val['name'] )			? $param_val['name']			: '';
					$param_val['value'] 		= isset( $param_val['value'] ) 			? $param_val['value']			: '';
					$param_val['desc']  		= !empty( $param_val['desc'] ) 			? $param_val['desc']			: '';
					$param_val['refresh_time']  = !empty( $param_val['refresh_time'] ) 	? $param_val['refresh_time']	: '';
					$param_val['placeholder'] 	= !empty( $param_val['placeholder'] ) 	? $param_val['placeholder'] 	: '';
					$param_val['id']    		= !empty( $param_val['id'] )			? $param_val['id']				: 'bdpp-'.$param_val['name'];
					$param_val['class'] 		= !empty( $param_val['class'] ) 		? 'bdpp-'.$param_val['name'].' '.$param_val['class'] : 'bdpp-'.$param_val['name'];
					$field_type					= $param_val['type'];
					$field_type					= ( 'dropdown' == $param_val['type'] && ! empty( $param_val['multi'] ) ) ? 'multi-dropdown' : $param_val['type'];
					$row_class					= ( ! $param_val['premium'] ) ? "bdpp-customizer-row" : "bdpp-customizer-row bdpp-customizer-row-premium";

					// Dependency
					if( ! empty($param_val['dependency']) && $param_val['dependency']['element'] ) {

						if( isset($param_val['dependency']['value_not_equal_to']) ) {
							$temp_dependency[ $param_val['dependency']['element'] ]['hide'][ $param_val['name'] ] 	= (array)$param_val['dependency']['value_not_equal_to'];
						} else {
							$temp_dependency[ $param_val['dependency']['element'] ]['show'][ $param_val['name'] ] 	= (array)$param_val['dependency']['value'];
						}
					}

					// Ajax Fields
					if( ! empty( $param_val['ajax'] ) ) {
						$ajax_fields[] = $param_val['name'];
					}

					echo '<div class="'.esc_attr( $row_class ).'" data-type="'.esc_attr( $field_type ).'">';
						$this->render_field_label( $param_val );

						if( ! empty( $param_val['type'] ) && (method_exists( $this, 'render_field_'.$param_val['type'] )) ) {
							call_user_func( array($this, 'render_field_'.$param_val['type']), $param_val );
						} else {
							call_user_func( array($this, 'render_field_text'), $param_val );
						}

						$this->render_field_desc( $param_val );

						if( $param_val['premium'] ) {
							echo '<div class="bdpp-shrt-acc-overlay"></div>';
						}

					echo '</div><!-- end .bdpp-customizer-row -->';
				}

				if( $is_premium ) {
					echo '<div class="bdpp-shrt-acc-overlay"></div>';
				}
				echo '</div><!-- end .bdpp-shrt-acc-cnt -->';
			}
			echo '</div><!-- end .bdpp-shrt-accordion -->';

			// Dependency Values
			echo '<div class="bdpp-cust-conf bdpp-cust-dependency" '.( $temp_dependency ? 'data-dependency="'.htmlspecialchars( json_encode( $temp_dependency ) ).'"' : '' ).' '.( $ajax_fields ? 'data-ajax-fields="'.htmlspecialchars( json_encode( $ajax_fields ) ).'"' : '' ).'></div>';

		} else {
			echo '<p>'.esc_html__('Sorry, No Shortcode Parameter Found.', 'blog-designer-pack').'</p>';
		}
	}

	/**
	 * Render Field Label
	 * 
	 * @since 1.0
	 */
	function render_field_label( $args ) {

		if( $args['heading'] ) { ?>
		<label class="bdpp-shrt-lbl" for="<?php echo esc_attr( $args['id'] ); ?>"><?php echo wp_kses_post( $args['heading'] ); ?></label>
		<?php }
	}

	/**
	 * Render Field Description
	 * 
	 * @since 1.0
	 */
	function render_field_desc( $args ) {

		if( $args['desc'] ) { ?>
		<span class="description"><?php echo wp_kses_post( $args['desc'] ); ?></span>
		<?php }

		if( isset( $args['premium_desc'] ) ) { ?>
		<span class="description bdpp-premium-desc"><i class="dashicons dashicons-lock"></i> <?php echo wp_kses_post( $args['premium_desc'] ); ?> <a href="javascript:void(0);" class="bdpp-premium-link bdpp-show-popup"><?php esc_html_e('Use Premium', 'blog-designer-pack'); ?></a></span>
		<?php }
	}

	/**
	 * Render Text Field
	 * 
	 * @since 1.0
	 */
	function render_field_text( $args ) {
?>

		<input type="text" id="<?php echo esc_attr( $args['id'] ); ?>" class="<?php echo esc_attr( $args['class'] ); ?>" name="<?php echo esc_attr( $args['name'] ); ?>" value="<?php echo esc_attr( $args['value'] ); ?>" placeholder="<?php esc_attr( $args['placeholder'] ); ?>" data-default="<?php echo esc_attr( $args['value'] ); ?>" <?php if( $args['refresh_time'] ) { echo 'data-timeout="'.esc_attr( $args['refresh_time'] ).'"'; } ?> <?php if( $args['allow_empty'] ) { echo 'data-empty="'.esc_attr( $args['allow_empty'] ).'"'; } ?> />

<?php }

	/**
	 * Render Number Field
	 * 
	 * @since 1.0
	 */
	function render_field_number( $args ) {

		$min			= ! empty( $args['min'] )	? $args['min'] 		: 0;
		$max			= ! empty( $args['max'] )	? $args['max'] 		: '';
		$step			= ! empty( $args['step'] )	? $args['step'] 	: '';
		$default 		= ! empty($args['default']) ? $args['default'] 	: $args['value'];
?>		
		<input type="number" id="<?php echo esc_attr( $args['id'] ); ?>" class="<?php echo esc_attr( $args['class'] ); ?>" name="<?php echo esc_attr( $args['name'] ); ?>" value="<?php echo esc_attr( $args['value'] ); ?>" step="<?php echo esc_attr( $step ); ?>" min="<?php echo esc_attr( $min ); ?>" max="<?php echo esc_attr( $max ); ?>" placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>" data-default="<?php echo esc_attr( $default ); ?>" <?php if( $args['refresh_time'] ) { echo 'data-timeout="'.esc_attr( $args['refresh_time'] ).'"'; } ?> />

<?php }

	/**
	 * Render Select Field
	 * 
	 * @since 1.0
	 */
	function render_field_dropdown( $args ) {

		$disabled			= '';
		$default 			= isset($args['default']) 		? (array)$args['default'] 		: array();
		$args['value'] 		= ! empty($args['value'])		? (array)$args['value'] 		: array();
		$args['predefined']	= ! empty($args['predefined'])	? (array)$args['predefined']	: array();

		if( empty( $default ) ) {
			$default[] = key( $args['value'] );
		}
?>

		<select id="<?php echo esc_attr( $args['id'] ); ?>" class="bdpp-shrt-sel <?php echo esc_attr( $args['class'] ); ?>" name="<?php echo esc_attr( $args['name'] ); ?>" <?php echo (!empty( $args['multi'] )) ? 'multiple' : ''; ?> data-default="<?php echo esc_attr( implode(',', $default) ); ?>" <?php if( $args['refresh_time'] ) { echo 'data-timeout="'.esc_attr( $args['refresh_time'] ).'"'; } ?> <?php if( ! empty( $args['search_msg'] ) ) { echo 'data-search-msg="'.esc_attr( $args['search_msg'] ).'"'; } ?> <?php if( ! empty( $args['ajax_action'] ) ) { echo 'data-ajax-action="'.esc_attr( $args['ajax_action'] ).'"'; } ?> <?php if( $args['allow_empty'] ) { echo 'data-empty="'.esc_attr( $args['allow_empty'] ).'"'; } ?> <?php if( ! empty( $args['placeholder'] ) ) { echo 'data-placeholder="'.esc_attr( $args['placeholder'] ).'"'; } ?> <?php if( ! empty( $args['predefined'] ) ) { echo 'data-predefined="'.htmlspecialchars( json_encode($args['predefined']) ).'"'; } ?>>
			<?php if( $args['value'] && is_array($args['value']) ) {
				foreach ($args['value'] as $select_key => $select_value) {

					if( strpos($select_key, "|disabled") !== false ) {
						$disabled = 'disabled';
					}
			?>

					<option <?php echo (in_array($select_key, $default)) ? 'selected' : ''; ?> value="<?php echo esc_attr( $select_key ); ?>" <?php echo esc_attr( $disabled ); ?>><?php echo esc_html( $select_value ); ?></option>

			<?php } } ?>
		</select>

<?php }

	/**
	 * Render Radio Field
	 * 
	 * @since 1.0
	 */
	function render_field_radio( $args ) {

		$default 		= !empty($args['default']) 	? $args['default'] 		: '';
		$args['value'] 	= !empty($args['value']) 	? (array)$args['value'] : '';

		if( $args['value'] && is_array($args['value']) ) {
			foreach ($args['value'] as $select_key => $select_value) { ?>
				<label class="bdpp-shrt-field-lbl bdpp-cust-radio-lbl" for="<?php echo esc_attr( $args['id'].'-'.$select_key ); ?>">
					<input type="radio" id="<?php echo esc_attr( $args['id'].'-'.$select_key ); ?>" class="<?php echo esc_attr( $args['class'] ); ?>" name="<?php echo esc_attr( $args['name'] ); ?>" value="<?php echo esc_attr( $select_key ); ?>" <?php echo ($select_key == $default)? 'checked' : '' ; ?> />
					<span><?php echo esc_html( $select_value ); ?></span>
				</label>
		<?php } }
	}

	/**
	 * Render Checkbox Field
	 * 
	 * @since 1.0
	 */
	function render_field_checkbox( $args ) {

		$default 		= !empty($args['default']) 	? (array)$args['default'] 	: array();
		$args['value'] 	= !empty($args['value']) 	? (array)$args['value'] 	: '';

		if( $args['value'] && is_array($args['value']) ) {
			foreach ($args['value'] as $select_key => $select_value) { ?>
				<label class="bdpp-shrt-field-lbl bdpp-cust-checkbox-lbl" for="<?php echo esc_attr( $args['id'].'-'.$select_key ); ?>">
					<input type="checkbox" id="<?php echo esc_attr( $args['id'].'-'.$select_key ); ?>" class="<?php echo esc_attr( $args['class'] ); ?>" name="<?php echo esc_attr( $args['name'] ); ?>" value="<?php echo esc_attr( $select_key ); ?>" <?php echo (in_array($select_key, $default)) ? 'checked' : ''; ?> />
					<span><?php echo esc_html( $select_value ); ?></span>
				</label>
		<?php } }
	}

	/**
	 * Render Textarea Field
	 * 
	 * @since 1.0
	 */
	function render_field_textarea( $args ) {
?>

		<textarea id="<?php echo esc_attr( $args['id'] ); ?>" class="<?php echo esc_attr( $args['class'] ); ?>" name="<?php echo esc_attr( $args['name'] ); ?>" <?php if( $args['refresh_time'] ) { echo 'data-timeout="'.esc_attr( $args['refresh_time'] ).'"'; } ?>><?php echo esc_textarea( $args['value'] ); ?></textarea>

<?php
	}

	/**
	 * Render Text Field
	 * 
	 * @since 1.0
	 */
	function render_field_colorpicker( $args ) { ?>

		<input type="text" id="<?php echo esc_attr( $args['id'] ); ?>" class="bdpp-cust-color-box <?php echo esc_attr( $args['class'] ); ?>" name="<?php echo esc_attr( $args['name'] ); ?>" value="<?php echo esc_attr( $args['value'] ); ?>" data-default="<?php echo esc_attr( $args['value'] ); ?>" />

<?php }

	/**
	 * Render Text Field
	 * 
	 * @since 1.0
	 */
	function render_field_info( $args ) {
?>

<?php }
}