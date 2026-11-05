<?php
/**
 * Uninstall Insert Headers and Footers settings on uninstall.
 *
 * @since 2.1.0
 * @package WP Headers and Footers
 */

/**
 * Delete plugin options when "Remove Settings on Uninstall" is enabled.
 *
 * @since 2.1.0
 * @return void
 */
$settings = get_option( 'wpheaderandfooter_settings' );
global $wpdb;

// If not a multi-site.
if ( ! is_multisite() ) {

	if ( is_array( $settings ) && isset( $settings['remove_all_settings'] ) && 'on' === $settings['remove_all_settings'] ) {
		delete_option( 'wpheaderandfooter_basics' );
		delete_option( 'wpheaderandfooter_settings' );
		delete_option( 'wpheaderandfooter_diagnostic_log' );
		delete_option( 'wpheaderandfooter_basics_logger' );
		delete_option( 'wpheaderandfooter_active_time' );
		delete_option( 'wpheaderandfooter_review_dismiss' );
		delete_option( 'wpheaderandfooter_unslash_migrated' );
	}

} else {

	// Multi-site: check each blog's own settings before deleting.
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

	foreach ( $blog_ids as $blog_id ) {

		switch_to_blog( (int) $blog_id );

		$blog_settings = get_option( 'wpheaderandfooter_settings' );

		if ( is_array( $blog_settings ) && isset( $blog_settings['remove_all_settings'] ) && 'on' === $blog_settings['remove_all_settings'] ) {
			delete_option( 'wpheaderandfooter_basics' );
			delete_option( 'wpheaderandfooter_settings' );
			delete_option( 'wpheaderandfooter_diagnostic_log' );
			delete_option( 'wpheaderandfooter_basics_logger' );
			delete_option( 'wpheaderandfooter_active_time' );
			delete_option( 'wpheaderandfooter_review_dismiss' );
			delete_option( 'wpheaderandfooter_unslash_migrated' );
		}

		restore_current_blog();

	}
}
