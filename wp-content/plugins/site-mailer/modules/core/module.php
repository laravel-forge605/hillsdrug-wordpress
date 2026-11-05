<?php

namespace SiteMailer\Modules\Core;

use SiteMailer\Classes\Module_Base;
use SiteMailer\Classes\Utils;
use SiteMailer\Modules\Connect\Module as Connect;
use SiteMailer\Modules\Settings\Module as Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends Module_Base {
	public function get_name(): string {
		return 'core';
	}

	public static function component_list() : array {
		return [
			'Pointers',
			'Conflicts',
			'Not_Connected',
			'Quota_Exhausted_Banner',
			'Renewal_Notice',
			'Notificator',
		];
	}

	public function add_plugin_links( $links, $plugin_file_name ): array {
		if ( ! str_ends_with( $plugin_file_name, '/site-mailer.php' ) ) {
			return (array) $links;
		}

		$custom_links = [
			'dashboard' => sprintf(
				'<a href="%s">%s</a>',
				admin_url( 'admin.php?page=' . \SiteMailer\Modules\Settings\Module::SETTING_BASE_SLUG ),
				esc_html__( 'Dashboard', 'site-mailer' )
			),
		];

		if ( Connect::is_connected() && ! Settings::is_elementor_one() ) {

			$upgrade_link = Utils::get_upgrade_link( 'https://go.elementor.com/sm-panel-wp-dash-upgrade-plugins/' );

			$custom_links['upgrade'] = sprintf(
				'<a href="%s" style="color: #524CFF; font-weight: 700;" target="_blank" rel="noopener noreferrer">%s</a>',
				$upgrade_link,
				esc_html__( 'Upgrade', 'site-mailer' )
			);
		}
		if ( ! Connect::is_connected() ) {
			$custom_links['connect'] = sprintf(
				'<a href="%s" style="color: #524CFF; font-weight: 700;">%s</a>',
				admin_url( 'admin.php?page=' . \SiteMailer\Modules\Settings\Module::SETTING_BASE_SLUG ),
				esc_html__( 'Connect', 'site-mailer' )
			);
		}

		return array_merge( $custom_links, $links );
	}

	/**
	 * Module constructor.
	 */
	public function __construct() {
		$this->register_components();

		add_filter( 'plugin_action_links', [ $this, 'add_plugin_links' ], 10, 2 );
	}
}
