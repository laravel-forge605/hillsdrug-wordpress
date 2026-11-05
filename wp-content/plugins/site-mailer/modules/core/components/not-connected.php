<?php

namespace SiteMailer\Modules\Core\Components;

use SiteMailer\Classes\Utils;
use SiteMailer\Modules\Connect\Module as Connect;
use SiteMailer\Modules\Settings\Module;
use const SITE_MAILER_ASSETS_URL;
use const SITE_MAILER_VERSION;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Not_Connected {
    const NOT_CONNECTED_NOTICE_SLUG = 'site-mailer-not-connected';

    public function render_not_connected_notice() {
        if ( Pointers::is_dismissed( self::NOT_CONNECTED_NOTICE_SLUG ) ) {
            return;
        }

        ?>
        <div class="notice notice-info notice is-dismissible site-mailer__notice site-mailer__notice--pink"
             data-notice-slug="<?php echo esc_attr( self::NOT_CONNECTED_NOTICE_SLUG ); ?>">

             <div class="site-mailer__notice-icon">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 0H48V48H0V0Z" fill="#FAE4FA" fill-opacity="0.5"/>
                    <path d="M24 12C17.37 12 12 17.37 12 24C12 30.63 17.37 36 24 36C30.63 36 36 30.63 36 24C36 17.37 30.63 12 24 12ZM20.4 30H18V18H20.4V30ZM30 30H22.8V27.6H30V30ZM30 25.2H22.8V22.8H30V25.2ZM30 20.4H22.8V18H30V20.4Z" fill="#ED01EE"/>
                </svg>
            </div>

            <p>
                <b>
                    <?php esc_html_e(
                            'Email Deliverability is not connected right now. Connect your account to ensure your site reliably sends emails.',
                            'site-mailer'
                    ); ?>
                </b>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . Module::SETTING_BASE_SLUG ) ); ?>">
                    <?php esc_html_e(
                            'Connect now',
                            'site-mailer'
                    ); ?>
                </a>
            </p>
        </div>

        <script>
			const onNotConnectedNoticeClose = () => {
				const pointer = '<?php echo esc_js( self::NOT_CONNECTED_NOTICE_SLUG ); ?>';

				return wp.ajax.post( 'site_mailer_pointer_dismissed', {
					data: {
						pointer,
					},
					nonce: '<?php echo esc_js( wp_create_nonce( 'site-mailer-pointer-dismissed' ) ); ?>',
				} );
			};

			jQuery( document ).ready( function( $ ) {
				setTimeout( () => {
					const $closeButton = $( '[data-notice-slug="<?php echo esc_js( self::NOT_CONNECTED_NOTICE_SLUG ); ?>"] .notice-dismiss' );

					$closeButton
						.first()
						.on( 'click', onNotConnectedNoticeClose );

					$( '[data-notice-slug="<?php echo esc_js( self::NOT_CONNECTED_NOTICE_SLUG ); ?>"] a' )
						.first()
						.on( 'click', function( e ) {
							e.preventDefault();

							onNotConnectedNoticeClose().promise().done( () => {
								window.open( $( this ).attr( 'href' ), '_blank' ).focus();
								$closeButton.click();
							} );
						} );
				}, 0 );
			} );
        </script>
        <?php
    }

    public function enqueue_styles() {
        if ( Connect::is_connected() ) {
            return;
        }

        if ( Pointers::is_dismissed( self::NOT_CONNECTED_NOTICE_SLUG ) ) {
            return;
        }

        wp_enqueue_style(
                'site-mailer-notice',
                SITE_MAILER_ASSETS_URL . 'css/notice.css',
                [],
                SITE_MAILER_VERSION
        );
    }

    public function __construct() {
        add_action( 'current_screen', function () {
            if ( ! Utils::user_is_admin() ) {
                return;
            }

            if ( Connect::is_connected() ) {
               return;
            }

            if ( Utils::is_wp_dashboard_page() || Utils::is_wp_settings_page() ) {
                add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
                add_action( 'admin_notices', [ $this, 'render_not_connected_notice' ] );
            }
        } );
    }
}
