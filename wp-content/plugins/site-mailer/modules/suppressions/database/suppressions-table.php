<?php

namespace SiteMailer\Modules\Suppressions\Database;

use SiteMailer\Classes\Database\{
	Table,
	Database_Constants
};

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Suppressions_Table extends Table {
	// override base's const:
	const DB_VERSION = '1';
	const DB_VERSION_FLAG_NAME = 'site_mailer_suppressions_db_version';

	const ID = 'id';
	const EMAIL = 'email';
	const REASON = 'reason';
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';

	public static $table_name = 'site_mail_suppressions';

	public static function is_order_by_valid( $order_by ): bool {
		$is_valid_order_by = true;
		$allowed_columns   = array_keys( self::get_columns() );

		foreach ( $order_by as $column => $direction ) {
			$raw_column = str_replace( self::table_name() . '.', '', $column );
			$raw_column = trim( $raw_column, '`' );

			if ( ! in_array( $raw_column, $allowed_columns, true ) ) {
				return false;
			}

			$direction = strtoupper( trim( $direction ) );
			if ( ! sanitize_sql_orderby( "{$raw_column} {$direction}" ) ) {
				$is_valid_order_by = false;
				break;
			}
		}

		return $is_valid_order_by;
	}

	/**
	 * build_sql_string
	 *
	 * Override base Table::build_sql_string to validate ORDER BY before concatenation.
	 */
	public static function build_sql_string( $fields = '*', $where = '1', ?int $limit = null, ?int $offset = null, string $join = '', array $order_by = [] ): string {
		if ( is_array( $fields ) ) {
			$fields = implode( ', ', $fields );
		}

		$db = static::db();
		$query_string = 'SELECT %s FROM %s %s WHERE %s';
		$query_string = sprintf(
			$query_string,
			$fields,
			static::table_name(),
			$join,
			static::where( $where )
		);

		if ( $order_by && self::is_order_by_valid( $order_by ) ) {
			$query_string .= static::build_order_by_sql_string( $order_by );
		}

		if ( $limit ) {
			$query_string .= $db->prepare( ' LIMIT %d', $limit );
		}

		if ( $offset ) {
			$query_string .= $db->prepare( ' OFFSET %d', $offset );
		}

		return $query_string;
	}

	public static function get_columns(): array {
		return [
			self::ID => [
				'type'  => Database_Constants::get_col_type( Database_Constants::INT, 11 ),
				'flags' => Database_Constants::build_flags_string( [
					Database_Constants::UNSIGNED,
					Database_Constants::NOT_NULL,
					Database_Constants::AUTO_INCREMENT,
				] ),
				'key'   => Database_Constants::get_primary_key_string( self::ID ),
			],
			self::EMAIL => [
				'type'  => Database_Constants::get_col_type( Database_Constants::VARCHAR, 255 ),
				'flags' => Database_Constants::build_flags_string( [
					Database_Constants::NOT_NULL,
				] ),

				'key' => Database_Constants::build_key_string( Database_Constants::UNIQUE, self::EMAIL ),
			],
			self::REASON => [
				'type'  => Database_Constants::get_col_type( Database_Constants::VARCHAR, 255 ),
				'flags' => Database_Constants::build_flags_string( [
					Database_Constants::COMMENT,
					'\'unsubscribed | manual | bounced | blocked\'',
				] ),
			],
			self::CREATED_AT => [
				'type'  => Database_Constants::get_col_type( Database_Constants::DATETIME ),
				'flags' => Database_Constants::build_flags_string( [
					Database_Constants::NOT_NULL,
					Database_Constants::DEFAULT,
					Database_Constants::CURRENT_TIMESTAMP,
				] ),
			],
			self::UPDATED_AT => [
				'type'  => Database_Constants::get_col_type( Database_Constants::DATETIME ),
				'flags' => Database_Constants::build_flags_string( [
					Database_Constants::NOT_NULL,
					Database_Constants::DEFAULT,
					Database_Constants::CURRENT_TIMESTAMP,
					Database_Constants::ON_UPDATE,
					Database_Constants::CURRENT_TIMESTAMP,
				] ),
			],
		];
	}
}
