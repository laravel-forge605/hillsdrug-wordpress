<?php
/**
 * Shared WPGO introductory-pricing integration for Freemius upgrade screens.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Simple_Sitemap_Introductory_Pricing' ) ) {
	/**
	 * Display first-year pricing and carry the matching coupon into checkout.
	 */
	final class Simple_Sitemap_Introductory_Pricing {
		/**
		 * Register one product's pricing configuration.
		 *
		 * @param array<string, mixed> $configuration Product pricing configuration.
		 * @return void
		 */
		public static function register( array $configuration ) {
			add_action(
				'admin_enqueue_scripts',
				static function () use ( $configuration ) {
					self::enqueue( $configuration );
				}
			);
		}

		/**
		 * Enqueue the integration only on the configured product pricing page.
		 *
		 * @param array<string, mixed> $configuration Product pricing configuration.
		 * @return void
		 */
		private static function enqueue( array $configuration ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Page routing only; no state is changed.
			$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

			if ( empty( $configuration['page'] ) || $configuration['page'] !== $page ) {
				return;
			}

			wp_enqueue_style(
				$configuration['handle'],
				plugins_url( $configuration['style'], $configuration['plugin_file'] ),
				array(),
				$configuration['version']
			);
			wp_enqueue_script(
				$configuration['handle'],
				plugins_url( $configuration['script'], $configuration['plugin_file'] ),
				array(),
				$configuration['version'],
				true
			);
			wp_localize_script(
				$configuration['handle'],
				'wpgoIntroductoryPricingConfig',
				array(
					'tiers'  => $configuration['tiers'],
					'labels' => $configuration['labels'],
				)
			);
		}
	}
}
