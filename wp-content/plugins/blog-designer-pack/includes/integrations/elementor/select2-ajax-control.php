<?php
use Elementor\Base_Data_Control;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class BDPP_Elementor_Select2_Ajax_Control extends Base_Data_Control {

	public function get_type() {
		return 'bdpp-select2-ajax-control';
	}

	public function enqueue() {
		wp_register_script( 'bdpp-elementor-select2-ajax-control', BDP_URL . 'includes/integrations/elementor/assets/js/select2-ajax-control.js' );
		wp_localize_script( 'bdpp-elementor-select2-ajax-control', 'BdppES2AC', array( 
																					'ajax_url' 		=> admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
																					'loading_text'	=> esc_js( __('Loading...', 'blog-designer-pack') ),
																					'post_type'		=> esc_js( BDP_LAYOUT_POST_TYPE ),
																					'post_status'	=> 'publish,pending',
																				));
		wp_enqueue_script( 'bdpp-elementor-select2-ajax-control' );
	}

	protected function get_default_settings() {
		return [
			'options'			=> [],
			'multiple'			=> false,
			'select2options'	=> [],
			'query_slug'		=> '',
		];
	}

	public function content_template() {
		$control_uid = $this->get_control_uid();
	?>
		<div class="elementor-control-field">
			<label for="<?php echo esc_attr($control_uid); ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
                <# var multiple = ( data.multiple ) ? 'multiple' : ''; #>
				<select id="<?php echo esc_attr($control_uid); ?>" class="elementor-control-type-bdpp-ajaxselect2" {{ multiple }} data-setting="{{ data.name }}" data-nonce="<?php echo esc_attr( wp_create_nonce('bdpp-shortcode-builder') ); ?>">
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}
}