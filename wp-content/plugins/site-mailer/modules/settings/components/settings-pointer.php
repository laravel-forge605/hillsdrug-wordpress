<?php

namespace SiteMailer\Modules\Settings\Components;

use SiteMailer\Modules\Core\Components\Pointers;
use SiteMailer\Modules\Settings\Module as SettingsModule;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Settings_Pointer {
    const CURRENT_POINTER_SLUG = 'site-mailer-settings';

    public function admin_print_script() {
        if ( SettingsModule::is_elementor_one() ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( Pointers::is_dismissed( self::CURRENT_POINTER_SLUG ) ) {
            return;
        }

        wp_enqueue_script( 'wp-pointer' );
        wp_enqueue_style( 'wp-pointer' );

        $pointer_content = '<h3>' . esc_html__( 'Email Deliverability', 'site-mailer' ) . '</h3>';
        $pointer_content .= '<p>' . esc_html__( 'Go to Email Deliverability to send test email and set your domain', 'site-mailer' ) . '</p>';

        $pointer_content .= sprintf(
                '<p><a class="button button-primary site-mailer-pointer-settings-link" href="%s">%s</a></p>',
                admin_url( 'admin.php?page=' . SettingsModule::SETTING_BASE_SLUG ),
                esc_html__( 'Take me there', 'site-mailer' )
        );

        $allowed_tags = [
                'h3' => [],
                'p' => [],
                'a' => [
                        'class' => [],
                        'href' => [],
                ],
        ];
        ?>
        <script>
			const onClose = () => {
				return wp.ajax.post( 'site_mailer_pointer_dismissed', {
					data: {
						pointer: '<?php echo esc_attr( static::CURRENT_POINTER_SLUG ); ?>',
					},
					nonce: '<?php echo esc_attr( wp_create_nonce( 'site-mailer-pointer-dismissed' ) ); ?>',
				} );
			};

			jQuery( document ).ready( function( $ ) {
				$( '#toplevel_page_elementor-home' ).pointer( {
					content: '<?php echo wp_kses( $pointer_content, $allowed_tags ); ?>',
					pointerClass: 'site-mailer-settings-pointer',
					position: {
						edge: 'top',
						align: 'left',
						at: 'left+20 bottom',
						my: 'left top'
					},
					close: onClose,
				} ).pointer( 'open' );

				$( '.site-mailer-pointer-settings-link' ).first().on( 'click', function( e ) {
					e.preventDefault();

					$( this ).attr( 'disabled', true );

					onClose().promise().done( () => {
						location = $( this ).attr( 'href' );
					} );
				} );
			} );
        </script>

        <style>
            .site-mailer-settings-pointer .wp-pointer-content h3::before {
                content: '';
                background: transparent;
                border-radius: 0;
                background-image: url('data:image/svg+xml,%3Csvg%20width%3D%2232%22%20height%3D%2232%22%20viewBox%3D%220%200%2032%2032%22%20fill%3D%22none%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Crect%20x%3D%221.375%22%20y%3D%222.625%22%20width%3D%2229.375%22%20height%3D%2229.375%22%20rx%3D%225%22%20fill%3D%22%23FAE4FA%22%2F%3E%3Cpath%20d%3D%22M13.625%2013.8181L15.181%2012.0253C15.6793%2011.451%2016.5707%2011.451%2017.069%2012.0253L18.625%2013.8181M11.125%2015.9375L10.1967%2016.967C9.98961%2017.1966%209.875%2017.4949%209.875%2017.8041V21.875C9.875%2022.9105%2010.7145%2023.75%2011.75%2023.75H20.5C21.5355%2023.75%2022.375%2022.9105%2022.375%2021.875V17.8041C22.375%2017.4949%2022.2604%2017.1966%2022.0533%2016.967L21.125%2015.9375%22%20stroke%3D%22%23ED01EE%22%20stroke-width%3D%220.625%22%2F%3E%3Cpath%20d%3D%22M11.125%2018.75V15.625C11.125%2014.5895%2011.9645%2013.75%2013%2013.75H19.25C20.2855%2013.75%2021.125%2014.5895%2021.125%2015.625V18.75%22%20stroke%3D%22%23ED01EE%22%20stroke-width%3D%220.625%22%2F%3E%3Cpath%20d%3D%22M9.875%2018.125L15.7121%2020.168C15.9794%2020.2615%2016.2706%2020.2615%2016.5379%2020.168L22.375%2018.125%22%20stroke%3D%22%23ED01EE%22%20stroke-width%3D%220.625%22%2F%3E%3Crect%20x%3D%2219.5%22%20width%3D%2212.5%22%20height%3D%2212.5%22%20rx%3D%226.25%22%20fill%3D%22%23ED01EE%22%2F%3E%3Ccircle%20cx%3D%2225.75%22%20cy%3D%226.25%22%20r%3D%225.625%22%20fill%3D%22white%22%2F%3E%3Cpath%20d%3D%22M25.75%200.625C22.6439%200.625%2020.125%203.14387%2020.125%206.25C20.125%209.35612%2022.6439%2011.875%2025.75%2011.875C28.8561%2011.875%2031.375%209.35612%2031.375%206.25C31.375%203.14387%2028.8561%200.625%2025.75%200.625ZM24.0625%209.0625H22.9375V3.4375H24.0625V9.0625ZM28.5625%209.0625H25.1875V7.9375H28.5625V9.0625ZM28.5625%206.8125H25.1875V5.6875H28.5625V6.8125ZM28.5625%204.5625H25.1875V3.4375H28.5625V4.5625Z%22%20fill%3D%22%23ED01EE%22%2F%3E%3C%2Fsvg%3E');
            }
        </style>
        <?php
    }

    public function __construct() {
        add_action( 'in_admin_header', [ $this, 'admin_print_script' ] );
    }
}
