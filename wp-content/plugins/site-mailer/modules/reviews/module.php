<?php

namespace SiteMailer\Modules\Reviews;

use SiteMailer\Classes\Logger;
use SiteMailer\Classes\Module_Base;
use SiteMailer\Classes\Utils;
use SiteMailer\Modules\Settings\Module as SettingsModule;
use Throwable;
use SiteMailer\Modules\Logs\Database\Logs_Table;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Module
 */
class Module extends Module_Base {

	const REVIEW_DATA_OPTION = SettingsModule::SETTING_PREFIX . 'review_data';

	/**
	 * Get module name.
	 * Retrieve the module name.
	 *
	 * @access public
	 * @return string Module name.
	 */
	public function get_name(): string {
		return 'reviews';
	}

	public static function routes_list() : array {
		return [
			'Feedback',
		];
	}

	public function render_app(): void {
		echo '<div id="reviews-app"></div>';
	}

	/**
	 * Enqueue Scripts and Styles
	 */
	public function enqueue_scripts(): void {
		if ( ! Utils::is_plugin_page() ) {
			return;
		}

		if ( ! $this->maybe_show_review_popup() ) {
			return;
		}

		Utils\Assets::enqueue_app_assets( 'reviews', false );

		wp_localize_script(
			'reviews',
			'siteMailerReviewData',
			[
				'wpRestNonce' => wp_create_nonce( 'wp_rest' ),
				'reviewData' => $this->get_review_data(),
				'isRTL' => is_rtl(),
			]
		);

		$this->render_app();
	}

	public function register_base_data(): void {

		if ( get_option( self::REVIEW_DATA_OPTION ) ) {
			return;
		}

		$data = [
			'dismissals' => 0,
			'hide_for_days' => 0,
			'last_dismiss' => null,
			'rating' => null,
			'feedback' => null,
			'added_on' => gmdate( 'Y-m-d H:i:s' ),
			'submitted' => false,
			'repo_review_clicked' => false,
		];

		update_option( self::REVIEW_DATA_OPTION, $data, false );
	}

	/**
	 * Register settings.
	 *
	 * Register settings for the plugin.
	 *
	 * @return void
	 * @throws Throwable
	 */
	public function register_settings(): void {
		$settings = [
			'review_data' => [
				'type' => 'object',
				'show_in_rest' => [
					'schema' => [
						'type' => 'object',
						'additionalProperties' => true,
					],
				],
			],
		];

		foreach ( $settings as $setting => $args ) {
			if ( ! isset( $args['show_in_rest'] ) ) {
				$args['show_in_rest'] = true;
			}
			register_setting( 'options', SettingsModule::SETTING_PREFIX . $setting, $args );
		}
	}

	public function get_review_data(): array {
		return get_option( self::REVIEW_DATA_OPTION );
	}

	public function get_days_since_installed() {

		global $wpdb;
		$table_name = Logs_Table::table_name();

		// Check if the table exists
		$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );

		if ( ! $table_exists ) {
			Logger::error( "Error: Table '{$table_name}' does not exist." );
			return false;
		}

		$earliest_entry = $wpdb->get_row( $wpdb->prepare(
			'SELECT * FROM ' . esc_sql( $table_name ) . ' ORDER BY id ASC LIMIT %d',
			1
		) );

		if ( $earliest_entry ) {
			$install_date = strtotime( $earliest_entry->created_at );
			return floor( ( time() - $install_date ) / DAY_IN_SECONDS );
		} else {
			return false;
		}
	}

	public function get_total_mails_sent() {
		global $wpdb;
		$table_name = Logs_Table::table_name();

		// Check if the table exists
		$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );

		if ( ! $table_exists ) {
			Logger::error( "Error: Table '{$table_name}' does not exist." );
			return 0;
		}

		// Get the total count of mails sent
		$total_mails_sent = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . esc_sql( $table_name ) );

		return $total_mails_sent ? intval( $total_mails_sent ) : 0;
	}

	public function maybe_show_review_popup() {
		if ( $this->get_days_since_installed() > 7 && $this->get_total_mails_sent() > 20 ) {

			$review_data = $this->get_review_data();

			// Don't show if user has already submitted feedback when rating is less than 4.
			if ( isset( $review_data['rating'] ) && (int) $review_data['rating'] < 4 ) {
				return false;
			}

			// Hide if rating is submitted but repo review is not clicked.
			if ( (int) $review_data['rating'] > 3 && $review_data['repo_review_clicked'] ) {
				return false;
			}

			// Don't show if user has dismissed the popup 3 times.
			if ( 3 === (int) $review_data['dismissals'] ) {
				return false;
			}

			if ( $review_data['hide_for_days'] > 0 && isset( $review_data['hide_for_days'] ) ) {
				$hide_for_days = $review_data['hide_for_days'];
				$last_dismiss = strtotime( $review_data['last_dismiss'] );
				$days_since_dismiss = floor( ( time() - $last_dismiss ) / DAY_IN_SECONDS );

				if ( $days_since_dismiss < $hide_for_days ) {
					return false;
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * Add review link to plugin row meta
	 *
	 * @param array $links
	 * @param string $file
	 * @return array
	 * 
	 */
	public function add_plugin_row_meta( $links, $file ) {

		if ( ! defined( 'SITE_MAILER_FILE' ) || plugin_basename(SITE_MAILER_FILE) !== $file ) {
			return $links;
		}

		$links[] = '<a class="site-mailer-review" 
						href="https://wordpress.org/support/plugin/site-mailer/reviews/#new-post"
						target="_blank" rel="noopener noreferrer" 
						title="' . esc_attr__( 'Rate our plugin', 'site-mailer' ) 
					. '">
							<span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
					</a>';

		echo '<style>
				.site-mailer-review{ display: inline-flex;flex-direction: row-reverse;} 
				.site-mailer-review span{ color:#888}
				.site-mailer-review span:hover{color:#ffa400}
				.site-mailer-review span:hover~span{color:#ffa400}
			</style>';

		return $links;
	}

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_init', [ $this, 'register_base_data' ] );
		add_action( 'rest_api_init', [ $this, 'register_settings' ] );
		add_filter( 'plugin_row_meta', [ $this, 'add_plugin_row_meta' ], 10, 2 );

		$this->register_routes();
	}
}
