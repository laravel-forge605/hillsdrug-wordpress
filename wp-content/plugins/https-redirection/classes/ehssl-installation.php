<?php
/**
 * The installation class that handles the installation and upgrade tasks.
 */
class EHSSL_Installation {

	/*
	 * This function is capable of handing both single site or multi-site install and upgrade all in one.
	 */
	public static function run_safe_installer() {
		global $wpdb;

		//Do this if multi-site setup
		if (function_exists('is_multisite') && is_multisite()) {
			// check if it is a network activation - if so, run the activation function for each blog id
			if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					self::installer();
				}
				switch_to_blog($old_blog);
				return;
			}
		}

		//Do this if single site standard install
		self::installer();
	}

	public static function installer() {
		global $wpdb;

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$charset_collate = '';
		if (!empty($wpdb->charset)) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		} else {
			$charset_collate = "DEFAULT CHARSET=utf8";
		}
		if (!empty($wpdb->collate)) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}

		//The members table
		$sql = "CREATE TABLE {$wpdb->prefix}ehssl_resource_scan_tbl (
			id bigint(20) unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
        	source_table varchar(100) NOT NULL,
            source_uid varchar(255) NOT NULL,
			cols_map text DEFAULT NULL,
			meta_map text DEFAULT NULL,
			fixed bool DEFAULT false
          ) {$charset_collate};";

		dbDelta($sql);

		//Save the current DB version
		update_option("ehssl_db_version", EASY_HTTPS_SSL_DB_VERSION);
	}
}
