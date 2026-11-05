<?php
/**
 * Elementor Classes.
 *
 * @package header-footer-elementor
 */

namespace HFE\WidgetsManager\Extensions;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * HFE Progress bar extension
 *
 * @since 2.2.1
 */
class Scroll_To_Top {

	/**
	 * Instance of Widgets_Loader.
	 *
	 * @since  2.2.1
	 * @var null
	 */
	private static $_instance = null;

	/**
	 * Get instance of Widgets_Loader
	 *
	 * @since  2.2.1
	 * @return Widgets_Loader
	 */
	public static function instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Setup actions and filters.
	 *
	 * @since  2.2.1
	 * @access private
	 */
	private function __construct() {

		require_once HFE_DIR . '/inc/widgets-manager/extensions/class-scroll-to-top-settings.php';

		add_action( 'elementor/kit/register_tabs', [ $this, 'register_extension_tab' ], 1, 40 );
		add_action( 'elementor/documents/register_controls', [ $this, 'page_scroll_to_top_controls' ], 10 );

		add_action( 'wp_footer', [ $this, 'render_scroll_to_top_html' ] );

		// Enqueue jQuery and add inline script.
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Enqueues the necessary scripts for the Scroll to Top functionality.
	 *
	 * This function is responsible for adding the required JavaScript and CSS files
	 * to the WordPress site to enable the Scroll to Top feature.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Ensure jQuery is enqueued.
		wp_enqueue_script( 'jquery' );

		// Add inline script.
		wp_add_inline_script(
			'jquery',
			'!function($){"use strict";$(document).ready(function(){$(this).scrollTop()>100&&$(".hfe-scroll-to-top-wrap").removeClass("hfe-scroll-to-top-hide"),$(window).scroll(function(){$(this).scrollTop()<100?$(".hfe-scroll-to-top-wrap").fadeOut(300):$(".hfe-scroll-to-top-wrap").fadeIn(300)}),$(".hfe-scroll-to-top-wrap").on("click",function(){$("html, body").animate({scrollTop:0},300);return!1})})}(jQuery);'
		);
	}

	/**
	 * Register extension tab
	 *
	 * @param \Elementor\Core\Kits\Documents\Kit $kit The Elementor Kit document.
	 * @since 2.2.1
	 */
	public function register_extension_tab( \Elementor\Core\Kits\Documents\Kit $kit ) {
		$kit->register_tab( 'hfe-scroll-to-top-settings', Scroll_To_Top_Settings::class );
	}

	/**
	 * Render scroll to top html
	 *
	 * @since 2.2.1
	 */
	public function render_scroll_to_top_html() {

		$post_id                = get_the_ID();
		$document               = [];
		$document_settings_data = [];

		if ( \Elementor\Plugin::instance()->preview->is_preview_mode() ) {
			// get auto save data.
			$document = \Elementor\Plugin::$instance->documents->get_doc_for_frontend( $post_id );
		} else {
			$document = \Elementor\Plugin::$instance->documents->get( $post_id, false );
		}
		if ( isset( $document ) && is_object( $document ) ) {
			$document_settings_data = $document->get_settings();
		}

		$scroll_to_top_global = $this->get_elementor_settings( 'hfe_scroll_to_top_global' );

		$scroll_to_top = false;

		if ( 'yes' == $scroll_to_top_global ) {
			$scroll_to_top = true;
		}

		if ( isset( $document_settings_data['hfe_scroll_to_top_single_disable'] ) && 'yes' == $document_settings_data['hfe_scroll_to_top_single_disable'] ) {
			$scroll_to_top = false;
		}

		if ( ! \Elementor\Plugin::instance()->preview->is_preview_mode() && $scroll_to_top ) {

			$scrolltop_icon_html = $this->get_scroll_to_top_icon_html();

			$elementor_page = get_post_meta( get_the_ID(), '_elementor_edit_mode', true );
			if ( (bool) $elementor_page ) {
				$this->print_scroll_to_top_html( $scrolltop_icon_html );
			}
		}

		if ( \Elementor\Plugin::instance()->preview->is_preview_mode() ) {
			if ( $scroll_to_top ) {
				$scrolltop_icon_html = $this->get_scroll_to_top_icon_html();

				$elementor_page = get_post_meta( get_the_ID(), '_elementor_edit_mode', true );
				if ( (bool) $elementor_page ) {
					$this->print_scroll_to_top_html( $scrolltop_icon_html );
				}
			}
			?>
			<script>
				;(function($) {
					'use strict';
					var markup = '<div class="hfe-scroll-to-top-wrap edit-mode hfe-scroll-to-top-hide"><span class="hfe-scroll-to-top-button"><i class="fas fa-chevron-up"></i></span></div>';
					var scrolltop = jQuery('.hfe-scroll-to-top-wrap');

					if ( ! scrolltop.length ) {
						jQuery('body').append(markup);
					}

					function hfeSanitizeString(input) {
						var htmlTags = /<[^>]*>/g;
						var sanitizedInput = input.replace(htmlTags, "");
						return sanitizedInput;
					}

					function hfeSanitizeURL(url) {
						var urlPattern = /^(https?|ftp):\/\/[^\s/$.?#].[^\s]*$/i;
						if (url.match(urlPattern)) {
							return url;
						} else {
							return "";
						}
					}

					window.addEventListener('message',function(e) {
						var data = e.data;
						if( 'hfeMessage' == data.check ) {
							if (e.origin != window.origin) {
								return;
							}
							if (e.source.location.href != window.parent.location.href) {
								return;
							}
							var scrolltopWrap = jQuery('.hfe-scroll-to-top-wrap');
							var button = scrolltopWrap.find('.hfe-scroll-to-top-button');
							var changeValue = data.changeValue;
							var changeItem = data.changeItem;

							if ( 'hfe_scroll_to_top_single_disable' != changeItem[0] ) {
								var icon = '';
								var image = '';
								var text = '';
								var items = {
									'enable_global_hfe' : ('hfe_scroll_to_top_global' == changeItem[0]) ? changeValue : data.enable_global_hfe,
									'media_type' : ('hfe_scroll_to_top_media_type' == changeItem[0]) ? changeValue : data.media_type,
									'icon' : ('hfe_scroll_to_top_button_icon' == changeItem[0]) ? changeValue : data.icon,
									'image' : ('hfe_scroll_to_top_button_image' == changeItem[0]) ? changeValue : data.image,
									'text' : ('hfe_scroll_to_top_button_text' == changeItem[0]) ? changeValue : data.text,
								};

								if( 'hfe_scroll_to_top_button_icon' == changeItem[0] ) {
									items.media_type = 'icon';
								} else if( 'hfe_scroll_to_top_button_image' == changeItem[0] ) {
									items.media_type = 'image';
								} else if( 'hfe_scroll_to_top_button_text' == changeItem[0] ) {
									items.media_type = 'text';
								}

								if ('icon' == items.media_type) {
									// SVG uploads store an object in `value`; they cannot be
									// rebuilt client side, so leave the rendered markup alone.
									if ( items.icon && 'string' === typeof items.icon.value ) {
										icon = '<i class="' + hfeSanitizeString(items.icon.value) + '"></i>';
										button.html(icon);
									}
								} else if ('image' == items.media_type) {
									image = '<img src="' + hfeSanitizeURL(items.image.url) + '">';
									button.html(image);
								} else if ('text' == items.media_type) {
									text = '<span>' + hfeSanitizeString(items.text) + '</span>';
									button.html(text);
								}

								if( 'yes' == items.enable_global_hfe && scrolltopWrap.hasClass("edit-mode") ) {
									scrolltopWrap.removeClass("edit-mode");
								} else if( '' == changeValue && !scrolltopWrap.hasClass("edit-mode") ) {
									scrolltopWrap.addClass("edit-mode");
								}
							}

							if( 'hfe_scroll_to_top_single_disable' == changeItem[0] ) {
								if( 'yes' == changeValue && !scrolltopWrap.hasClass("single-page-off") ) {
									scrolltopWrap.addClass("single-page-off");
								} else if( '' == changeValue && scrolltopWrap.hasClass("single-page-off") ) {
									scrolltopWrap.removeClass("single-page-off");
								}
							}
						}
					})
				}(jQuery));
				!function(o){"use strict";o((function(){o(this).scrollTop()>100&&o(".hfe-scroll-to-top-wrap").removeClass("hfe-scroll-to-top-hide"),o(window).scroll((function(){o(this).scrollTop()<100?o(".hfe-scroll-to-top-wrap").fadeOut(300):o(".hfe-scroll-to-top-wrap").fadeIn(300)})),o(".hfe-scroll-to-top-wrap").on("click",(function(){return o("html, body").animate({scrollTop:0},300),!1}))}))}(jQuery);
			</script>
			<?php
		}

	}

	/**
	 * Build the inner HTML for the scroll to top button.
	 *
	 * The Icon control (Controls_Manager::ICONS) stores its value as an array
	 * whose 'value' key is a string for font icons but an array for SVG uploads.
	 * Only the SVG upload is routed through Elementor's Icons_Manager so that the
	 * inline SVG is rendered. Font icons keep the historical `<i class="...">`
	 * markup — Icons_Manager would convert them to inline SVG and break any
	 * custom CSS that targets `.hfe-scroll-to-top-button i`.
	 *
	 * @since 2.9.4.1
	 * @return string Escaped HTML for the button contents.
	 */
	private function get_scroll_to_top_icon_html() {

		$media_type = ! empty( $this->get_elementor_settings( 'hfe_scroll_to_top_media_type' ) ) ? $this->get_elementor_settings( 'hfe_scroll_to_top_media_type' ) : 'icon';

		if ( 'image' === $media_type ) {
			$image = $this->get_elementor_settings( 'hfe_scroll_to_top_button_image' );
			$url   = ! empty( $image['url'] ) ? $image['url'] : '';
			return '' !== $url ? '<img src="' . esc_url( $url ) . '">' : '';
		}

		if ( 'text' === $media_type ) {
			$text = ! empty( $this->get_elementor_settings( 'hfe_scroll_to_top_button_text' ) ) ? $this->get_elementor_settings( 'hfe_scroll_to_top_button_text' ) : '';
			return '<span>' . esc_html( $text ) . '</span>';
		}

		// Default: icon media type (supports both font icons and SVG uploads).
		$icon = $this->get_elementor_settings( 'hfe_scroll_to_top_button_icon' );

		if ( ! is_array( $icon ) || empty( $icon['value'] ) ) {
			$icon = [
				'value'   => 'fas fa-chevron-up',
				'library' => 'fa-solid',
			];
		}

		$is_svg_upload = ( isset( $icon['library'] ) && 'svg' === $icon['library'] ) || is_array( $icon['value'] );

		if ( ! $is_svg_upload ) {
			// Font icon: keep the original markup so existing custom CSS keeps working.
			return '<i class="' . esc_attr( $icon['value'] ) . '" aria-hidden="true"></i>';
		}

		if ( ! class_exists( '\Elementor\Icons_Manager' ) ) {
			return '';
		}

		ob_start();
		\Elementor\Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
		return ob_get_clean();
	}

	/**
	 * Print the scroll to top markup.
	 *
	 * The inner contents (icon/image/text) are already escaped by
	 * get_scroll_to_top_icon_html(), so they are printed as-is to preserve the
	 * inline SVG markup that wp_kses_post() would otherwise strip.
	 *
	 * @since 2.9.4.1
	 * @param string $icon_html Pre-escaped inner HTML for the button.
	 * @return void
	 */
	private function print_scroll_to_top_html( $icon_html ) {
		echo "<div class='hfe-scroll-to-top-wrap hfe-scroll-to-top-hide'><span class='hfe-scroll-to-top-button'>";
		echo $icon_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Pre-escaped in get_scroll_to_top_icon_html(); inline SVG must not be re-filtered through wp_kses_post().
		echo '</span></div>';
	}

	/**
	 * Get Elementor settings
	 *
	 * @param string $setting_id Setting ID.
	 * @return string
	 */
	public function get_elementor_settings( $setting_id ) {

		$return              = '';
		$extensions_settings = [];

		if ( ! isset( $extensions_settings['kit_settings'] ) ) {
			if ( \Elementor\Plugin::instance()->preview->is_preview_mode() ) {
				// get auto save data.
				$kit = \Elementor\Plugin::$instance->documents->get_doc_for_frontend( \Elementor\Plugin::$instance->kits_manager->get_active_id() );
			} else {
				$kit = \Elementor\Plugin::$instance->documents->get( \Elementor\Plugin::$instance->kits_manager->get_active_id(), true );
			}
			if ( isset( $kit ) && is_object( $kit ) ) {
				$extensions_settings['kit_settings'] = $kit->get_settings();
			}
		}

		if ( isset( $extensions_settings ) && isset( $extensions_settings['kit_settings'][ $setting_id ] ) ) {
			$return = $extensions_settings['kit_settings'][ $setting_id ];
		}

		return $return;
	}

	/**
	 * Add scroll to top controls
	 *
	 * @param \Elementor\Widget_Base $element Elementor Widget.
	 */
	public function page_scroll_to_top_controls( $element ) {

		$scroll_to_top_global = $this->get_elementor_settings( 'hfe_scroll_to_top_global' );
		if ( 'yes' !== $scroll_to_top_global ) {
			return;
		}

		$element->start_controls_section(
			'hfe_scroll_to_top_single_section',
			[
				'label' => __( 'Scroll to Top', 'header-footer-elementor' ),
				'tab'   => Controls_Manager::TAB_SETTINGS,
			]
		);

		$element->add_control(
			'hfe_scroll_to_top_single_disable',
			[
				'label'        => __( 'Disable Scroll to Top for This Page', 'header-footer-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'header-footer-elementor' ),
				'label_off'    => __( 'No', 'header-footer-elementor' ),
				'return_value' => 'yes',
			]
		);

		$element->end_controls_section();
	}


}

Scroll_To_Top::instance();
