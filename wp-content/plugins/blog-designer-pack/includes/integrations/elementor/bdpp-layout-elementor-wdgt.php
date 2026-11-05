<?php
/**
 * BDP Elementor Widget Class
 *
 * @package Blog Designer Pack
 * @since 4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BDPP_Layout_Elementor_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'bdpp_layout_elementor_widget';
	}

	public function get_title() {
		return esc_html__( 'Blog Designer Pack', 'blog-designer-pack' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return [ 'general' ];
	}

	public function get_keywords() {
		return [ 'bdpp', 'blog', 'post', 'post grid', 'post grid', 'post masonry', 'post ticker', 'post timeline', 'post list', 'post slider', 'post carousel', 'post gridbox', 'taxonomy', 'category', 'category grid', 'category slider', 'category ticker' ];
	}

	protected function register_controls() {

		// Taking some variables
		$add_layout_url = add_query_arg( array('page' => 'bdpp-layout', 'shortcode' => 'bdp_post', 'action' => 'add'), admin_url('admin.php') );

		// Content Tab Start
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Choose Layout', 'blog-designer-pack' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'bdp_layout_id',
			[
				'label'			=> esc_html__( 'Layout', 'blog-designer-pack' ),
				'label_block'	=> true,
				'description'	=> sprintf( esc_html__( 'Choose your created layout by name or id. You can create the layout from %shere%s.', 'blog-designer-pack' ), '<a href="'.esc_url( $add_layout_url ).'" target="_black">', '</a>' ),
				'type'			=> 'bdpp-select2-ajax-control',
				'multiple'		=> false,
				'options'		=> '',
				'default'		=> '',
			]
		);

		$this->add_control(
			'bdp_layout_preview',
			[
				'label'			=> esc_html__( 'Preview', 'blog-designer-pack' ),
				'type'			=> \Elementor\Controls_Manager::SWITCHER,
				'label_off'		=> esc_html__( 'Hide', 'blog-designer-pack' ),
				'label_on'		=> esc_html__( 'Show', 'blog-designer-pack' ),
				'default'		=> 'yes',
				'description'	=> esc_html__( 'Enable layout preview in editor mode.', 'blog-designer-pack' ),
			]
		);

		$this->end_controls_section();
		// Content Tab End
	}

	/**
	 * Frontend Output
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		// Render Shortcode
		echo do_shortcode('[bdpp_tmpl layout_id="'.esc_attr( $settings['bdp_layout_id'] ).'" bdp_layout_preview="'.esc_attr( $settings['bdp_layout_preview'] ).'"]');
	}
}