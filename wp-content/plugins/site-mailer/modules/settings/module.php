<?php

namespace SiteMailer\Modules\Settings;

use ElementorOne\Connect\Classes\Data;
use Exception;
use SiteMailer\Classes\Logger;
use SiteMailer\Classes\Module_Base;
use SiteMailer\Classes\Utils;
use SiteMailer\Modules\Connect\Classes\Config;
use SiteMailer\Modules\Connect\Module as Connect;
use SiteMailer\Modules\Logs\Components\Log_Pull;
use SiteMailer\Modules\Settings\Banners\Elementor_Birthday_Banner;
use SiteMailer\Modules\Settings\Banners\Sale_Banner;
use SiteMailer\Modules\Settings\Classes\Settings;
use SiteMailer\Modules\Settings\Module as SettingsModule;
use Throwable;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class Module
 */
class Module extends Module_Base {
    const SETTING_PREFIX = 'site_mailer_';
    const SETTING_GROUP = 'site_mailer_settings';
    const SETTING_BASE_SLUG = 'site-mailer-settings';
    const SETTING_CAPABILITY = 'manage_options';
    const ONE_MISMATCH_URL = '/wp-admin/admin.php?page=elementor-home#/home/url-mismatch';

    /**
     * Get module name.
     * Retrieve the module name.
     *
     * @access public
     * @return string Module name.
     */
    public function get_name(): string {
        return 'settings';
    }

    public static function component_list(): array {
        return [
                'Settings_Pointer',
        ];
    }

    public static function routes_list(): array {
        return [
                'Get_Settings',
        ];
    }

    public function render_app() {
        ?>
        <?php Sale_Banner::get_banner( 'https://go.elementor.com/SM-BF-sale' ); ?>
        <?php Elementor_Birthday_Banner::get_banner( 'https://go.elementor.com/SM-10th-bd-sale' ); ?>

        <!-- The hack required to wrap WP notifications -->
        <div class="wrap">
            <h1 style="display: none;" role="presentation"></h1>
        </div>

        <div id="site-mailer-app"></div>
        <?php
    }

    public function register_page() {

        add_submenu_page(
                'elementor-home',
                __( 'Email Deliverability', 'site-mailer' ),
                __( 'Email Deliverability', 'site-mailer' ),
                self::SETTING_CAPABILITY,
                self::SETTING_BASE_SLUG,
                [ $this, 'render_app' ],
                20
        );
    }

    /**
     * @throws Throwable
     */
    public function maybe_add_url_mismatch_notice() {

        if ( ! Utils::is_plugin_page() || Connect::get_connect()->utils()->is_valid_home_url() ) {
            return;
        }

        $onclick = SettingsModule::is_elementor_one() ? 'window.location.href = "' . self::ONE_MISMATCH_URL . '";' : 'document.dispatchEvent( new Event( "site-mailer/auth/url-mismatch-modal/open" ) );';

        ?>
        <div class="notice notice-error notice site-mailer__notice site-mailer__notice--error">
            <p>
                <b>
                    <?php esc_html_e(
                            'Your license key does not match your current domain, causing a mismatch.',
                            'site-mailer'
                    ); ?>
                </b>

                <span>
					<?php esc_html_e(
                            'This is most likely due to a change in the domain URL of your site (including HTTP/SSL migration).',
                            'site-mailer'
                    ); ?>

					<button type="button"
                            onclick="<?php echo esc_js( $onclick ); ?>">
						<?php esc_html_e(
                                'Fix mismatched URL',
                                'site-mailer'
                        ); ?>
					</button>
				</span>
            </p>
        </div>
        <?php
    }

    /**
     * Enqueue Scripts and Styles
     */
    public function enqueue_scripts(): void {
        if ( ! Utils::is_plugin_page() ) {
            return;
        }

        if ( version_compare( get_bloginfo( 'version' ), '6.6', '<' ) ) {
            wp_register_script(
                    'react-jsx-runtime',
                    SITE_MAILER_ASSETS_URL . 'lib/react-jsx-runtime.js',
                    [ 'react' ],
                    '18.3.1',
                    true
            );
        }

        wp_enqueue_style(
                'site-mailer-admin-fonts',
                'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap',
                [],
                SITE_MAILER_VERSION
        );

        Utils\Assets::enqueue_app_assets( 'admin' );
        wp_localize_script(
            'admin',
            'siteMailerSettingsData',
            [
                'wpRestNonce' => wp_create_nonce( 'wp_rest' ),
                'pluginEnv' => self::get_plugin_env(),
                'isDevelopment' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG,
                'planData' => Settings::get( Settings::PLAN_DATA ),
                'planScope' => Settings::get( Settings::PLAN_SCOPE ),
                'appSlug' => Config::PLUGIN_SLUG,
                'appVersion' => SITE_MAILER_VERSION,
            ]
        );
    }

    /**
     * Get Mixpanel project Token
     * @return string
     */
    private static function get_plugin_env(): string {
        return apply_filters( 'site_mailer_plugin_env', 'production' );
    }

    /**
     * @throws Exception
     */
    public function on_connect() {
        if ( ! Connect::is_connected() ) {
            return;
        }

        $register_response = Utils::get_api_client()->make_request(
                'POST',
                'site/register'
        );

        if ( $register_response && ! is_wp_error( $register_response ) ) {
            update_option( Settings::SUBSCRIPTION_ID, $register_response->id );

            $response = Utils::get_api_client()->make_request(
                    'POST',
                    'site/info'
            );
        }
        if ( ! is_wp_error( $response ) ) {
            update_option( Settings::PLAN_DATA, $response );
            update_option( Settings::IS_VALID_PLAN_DATA, true );

            if ( isset( $response->scopes ) ) {
                Settings::set( Settings::PLAN_SCOPE, $response->scopes );
            }
        } else {
            Logger::error( esc_html( $response->get_error_message() ) );
            update_option( Settings::IS_VALID_PLAN_DATA, false );
        }
    }

    /**
     * Register or update site data for One connect
     * @throws Exception
     */
    public function on_migration_run() {
        if ( ! Connect::is_connected() ) {
            return;
        }

        $client_id = Settings::get( Settings::CLIENT_ID );

        if ( $client_id ) {
            try {
                Utils::get_api_client()->make_request(
                        'POST',
                        'site/migration',
                        ['old_client_id' => $client_id],
                );
                self::refresh_plan_data();

                $old_options = [
                        'site_mailer_client_secret',
                        'site_mailer_home_url',
                        'site_mailer_access_token',
                        'site_mailer_token_id',
                        'site_mailer_refresh_token',
                        'site_mailer_user_access_token',
                        'site_mailer_owner_user_id',
                        Settings::SUBSCRIPTION_ID,
                        Settings::CLIENT_ID,
                ];

                foreach ( $old_options as $option ) {
                    delete_option( $option );
                }
            } catch ( Throwable $t ) {
                Logger::error( esc_html( $t->getMessage() ) );
            }
        } else {
            $this->on_connect();
        }
    }

    /**
     * On disconnect
     * @return void
     */
    public function on_disconnect() {
        delete_option( Settings::SUBSCRIPTION_ID );
    }

    /**
     * Register settings.
     *
     * Register settings for the plugin.
     *
     * @return void
     * @throws Throwable
     */
    public function register_settings() {
        $settings = [
                'keep_log' => [
                        'type' => 'boolean',
                ],
                'unsubscribe' => [
                        'type' => 'boolean',
                ],
                'close_post_connect_modal' => [
                        'type' => 'boolean',
                        'description' => _x( 'Email Deliverability Close Post Connect Modal', 'Description for API usage. no need to translate', 'site-mailer' ),
                ],
                'from_name' => [
                        'type' => 'string',
                        'description' => _x( 'Email Deliverability From Email', 'Description for API usage. no need to translate', 'site-mailer' ),
                ],
                'reply_to_email' => [
                        'type' => 'string',
                        'description' => _x( 'Email Deliverability Reply Email', 'Description for API usage. no need to translate', 'site-mailer' ),
                ],
                'sender_domain' => [
                        'type' => 'string',
                        'description' => _x( 'Email Deliverability Sender Domain', 'Description for API usage. no need to translate', 'site-mailer' ),
                ],
                'sender_email_prefix' => [
                        'type' => 'string',
                        'description' => _x( 'Email Deliverability Sender Mail Prefix', 'Description for API usage. no need to translate', 'site-mailer' ),
                ],
                'custom_domain_dns_records' => [
                        'type' => 'string',
                        'description' => _x( 'Email Deliverability custom domain DNS records', 'Description for API usage. no need to translate', 'site-mailer' ),
                ],
                'verification_started' => [
                        'type' => 'boolean',
                ],
                'registration_started' => [
                        'type' => 'boolean',
                ],
                'confirm_domain_settings_opened' => [
                        'type' => 'boolean',
                ],
                'confirm_dns_settings_added' => [
                        'type' => 'boolean',
                ],
                'active_step' => [
                        'type' => 'boolean',
                ],
                'custom_domain_verified' => [
                        'type' => 'boolean',
                ],
                'custom_domain_verified_alert' => [
                        'type' => 'boolean',
                ],
                'custom_domain_verification_start_time' => [
                        'type' => 'string',
                ],
                'is_valid_plan_data' => [
                        'type' => 'boolean',
                ],
                'hide_logs_status_refresh_infotip' => [
                        'type' => 'boolean',
                        'show_in_rest' => true,
                ],
                'hide_walkthrough_modal' => [
                        'type' => 'boolean',
                ],
                'is_migration_popup_dismissed' => [
                        'type' => 'boolean',
                        'description' => _x( 'Site Mailer Migration Popup Dismissed', 'Description for API usage. no need to translate', 'site-mailer' ),
                ],
        ];

        foreach ( $settings as $setting => $args ) {
            if ( ! isset( $args['show_in_rest'] ) ) {
                $args['show_in_rest'] = true;
            }
            register_setting( 'options', self::SETTING_PREFIX . $setting, $args );
        }
    }

    /**
     * Get sender email
     *
     * @return string
     */
    public static function get_sender_email(): string {
        $settings = Settings::get( Settings::PLAN_DATA );
        $sender_in_used = Settings::get( Settings::SENDER_EMAIL_PREFIX );
        $sender_mail = '';
        $domain_verified = 'verified' === Settings::get( Settings::CUSTOM_DOMAIN_VERIFICATION_STATUS );

        if ( ! empty( $settings ) && ! is_wp_error( $settings ) ) {
            if ( ! empty( $settings->sender->sender_in_used ) ) {
                $sender_mail = $settings->sender->sender_in_used;

                $sender = explode( '@', $sender_mail );
                if ( $domain_verified && ! empty( $sender_in_used ) && $sender[0] !== $sender_in_used ) {
                    $sender_mail = $sender_in_used . '@' . $sender[1];
                }
            }
        }

        return $sender_mail;
    }

    /**
     * Get sender from name
     *
     * @return string
     */
    public static function get_sender_from_name(): string {
        return Settings::get( Settings::FROM_NAME );
    }

    /**
     * Get sender reply to email
     *
     * @return string
     */
    public static function get_sender_reply_email(): string {
        $email = Settings::get( Settings::REPLY_TO_EMAIL );

        if ( ( ! empty( $email ) && ! is_email( $email ) ) || empty( $email ) ) {
            return get_bloginfo( 'admin_email' );
        }

        return $email;
    }

    /**
     * Get unsubscribe encryption key
     *
     * @return array|bool
     */
    public static function get_unsubscribe_encryption_data() {
        if ( ! function_exists( 'openssl_encrypt' ) ) {
            Logger::error( 'the openssl extension is not installed in the environment' );

            return false;
        }
        $data = Settings::get( Settings::UNSUBSCRIBE_ENCRYPTION_DATA );
        if ( ! $data ) {
            $cipher = 'aes128';
            $iv_length = openssl_cipher_iv_length( $cipher );
            $iv = openssl_random_pseudo_bytes( $iv_length );
            $data = [
                    'key' => wp_generate_uuid4(),
                    'cipher' => $cipher,
                    'iv' => base64_encode( $iv ),
            ];
            update_option( Settings::UNSUBSCRIBE_ENCRYPTION_DATA, $data );
        }

        return $data;
    }

    /**
     * Refresh plan data when the settings page is loaded.
     *
     * @return void
     * @throws Throwable
     */
    public static function refresh_plan_data() {
        if ( ! Connect::is_connected() ) {
            return;
        }
        $endpoint = self::is_elementor_one() ? 'site/register' : 'site/info';
        $response = Utils::get_api_client()->make_request(
                'POST',
                $endpoint
        );

        if ( ! is_wp_error( $response ) ) {
            update_option( Settings::PLAN_DATA, $response );
            update_option( Settings::IS_VALID_PLAN_DATA, true );
        } else {
            Logger::error( esc_html( $response->get_error_message() ) );
            update_option( Settings::IS_VALID_PLAN_DATA, false );
        }
    }

    public static function clear_settings_cache() {
        wp_cache_delete( Settings::KEEP_LOG, 'option' );
        wp_cache_delete( Settings::UNSUBSCRIBE, 'option' );
        wp_cache_delete( Settings::CLOSE_POST_CONNECT_MODAL, 'option' );
        wp_cache_delete( Settings::PLAN_DATA, 'option' );
        wp_cache_delete( Settings::SENDER_EMAIL_PREFIX, 'option' );
        wp_cache_delete( Settings::FROM_NAME, 'option' );
        wp_cache_delete( Settings::REPLY_TO_EMAIL, 'option' );
        wp_cache_delete( Settings::CUSTOM_DOMAIN_DNS_RECORDS, 'option' );
        wp_cache_delete( Settings::CUSTOM_DOMAIN_VERIFICATION_STATUS, 'option' );
        wp_cache_delete( Settings::CUSTOM_DOMAIN_VERIFICATION_RECORDS, 'option' );
        wp_cache_delete( Settings::CUSTOM_DOMAIN_VERIFICATION_START_TIME, 'option' );
        wp_cache_delete( Settings::SUBSCRIPTION_ID, 'option' );
        wp_cache_delete( Config::APP_PREFIX . Data::ACCESS_TOKEN, 'option' );
        wp_cache_delete( Config::APP_PREFIX . Data::CLIENT_ID, 'option' );
        wp_cache_delete( Config::APP_PREFIX . Data::HOME_URL, 'option' );
        wp_cache_delete( Config::APP_PREFIX . Data::OPTION_OWNER_USER_ID, 'option' );
        wp_cache_delete( Settings::IS_VALID_PLAN_DATA, 'option' );
    }

    /**
     * Check if site mailer is connected to elementor one
     * @return bool
     */
    public static function is_elementor_one(): bool {
        return Connect::get_connect()->get_config( 'app_type' ) !== Config::APP_TYPE;
    }

    /**
     * Check if user generally has an active Elementor One subscription,
     * regardless of whether Site Mailer itself is connected to One.
     * @return bool
     */
    public static function has_elementor_one_subscription(): bool {
        if ( ! class_exists( '\ElementorOne\Admin\Helpers\Utils' ) ) {
            return false;
        }

        $one_facade = \ElementorOne\Admin\Helpers\Utils::get_one_connect();

        return $one_facade && $one_facade->utils()->is_connected();
    }

    /**
     * Get all plugin settings data
     * @return array
     * @throws Throwable
     */
    public static function get_plugin_settings(): array {
        self::refresh_plan_data();
        self::clear_settings_cache();

        return [
                'isConnected' => Connect::is_connected(),
                'isUrlMismatch' => ! Connect::get_connect()->utils()->is_valid_home_url(),
                'subscriptionId' => Settings::get( Settings::SUBSCRIPTION_ID ),
                'keepLog' => Settings::get( Settings::KEEP_LOG ),
                'unsubscribe' => Settings::get( Settings::UNSUBSCRIBE ),
                'isDevelopment' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG,
                'siteUrl' => wp_parse_url( get_site_url(), PHP_URL_HOST ),
                'closePostConnectModal' => Settings::get( Settings::CLOSE_POST_CONNECT_MODAL ),
                'senderEmail' => self::get_sender_email() ?? '',
                'fromName' => self::get_sender_from_name(),
                'replyToEmail' => self::get_sender_reply_email(),
                'settings' => Settings::get( Settings::PLAN_DATA ),
                'customDomainDNSRecords' => Settings::get( Settings::CUSTOM_DOMAIN_DNS_RECORDS ),
                'verificationStatus' => Settings::get( Settings::CUSTOM_DOMAIN_VERIFICATION_STATUS ),
                'verificationRecords' => Settings::get( Settings::CUSTOM_DOMAIN_VERIFICATION_RECORDS ),
                'verificationStartTime' => Settings::get( Settings::CUSTOM_DOMAIN_VERIFICATION_START_TIME ),
            //TODO: check use of this
                'isValidPlanData' => Settings::get( Settings::IS_VALID_PLAN_DATA ),
                'isRTL' => is_rtl(),
                'lastLogRefresh' => Log_Pull::check_refresh_time(),
                'isElementorOne' => self::is_elementor_one(),
                'hasElementorOneSubscription' => self::has_elementor_one_subscription(),
                'isMigrationPopupDismissed' => (bool) Settings::get( Settings::IS_MIGRATION_POPUP_DISMISSED ),
        ];
    }

    public function __construct() {
        $this->register_routes();
        $this->register_components( self::component_list() );

        add_action( 'current_screen', function () {
            add_action( 'admin_notices', [ $this, 'maybe_add_url_mismatch_notice' ] );
        } );

        add_action( 'admin_menu', [ $this, 'register_page' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'elementor_one/' . Config::APP_PREFIX . '_connected', [ $this, 'on_connect' ] );
        add_action( 'elementor_one/' . Config::APP_PREFIX . '_disconnected', [ $this, 'on_disconnect' ] );
        add_action( 'elementor_one/' . Config::APP_PREFIX . '_migration_run', [ $this, 'on_migration_run' ] );

        add_action( 'rest_api_init', [ $this, 'register_settings' ] );

        // Add action on switch domain for update access token
        add_action( 'elementor_one/' . Config::APP_PREFIX . '_switched_domain', function($facade) {
            $facade->service()->renew_access_token();
        } );
        add_action( 'elementor_one/switched_domain', function( $facade ) {
            $facade->service()->renew_access_token();
        } );
    }
}
