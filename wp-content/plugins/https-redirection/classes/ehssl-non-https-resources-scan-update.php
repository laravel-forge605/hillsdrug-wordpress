<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EHSSL_Non_HTTPS_Resources_Scan_Update {

    public $batch_size = 100;

    public $scan_results = array();

    public $post_types;

    public $other_tables;

    public $flags;

    private $scan_type = 'scan_static_resources_only';

    public function __construct() {
        add_action( 'wp_ajax_ehssl_non_https_resources_scan', array( $this, 'handle_non_https_resources_scan' ) );
        add_action( 'wp_ajax_ehssl_get_scanned_resources_table', array( $this, 'handle_get_non_https_resources_table' ) );
        add_action( 'wp_ajax_ehssl_load_static_resources_table_page', array( $this, 'handle_load_non_https_resources_table_page' ) );
        add_action( 'wp_ajax_ehssl_update_http_urls', array( $this, 'handle_update_http_urls' ) );
    }

    private function check_ajax_permissions( $nonce_action, $nonce_field ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'You do not have permission to perform this action.', 'https-redirection' ),
            ), 403 );
        }

        if ( ! check_ajax_referer( $nonce_action, $nonce_field, false ) ) {
            wp_send_json_error( array(
                'message' => __( 'Nonce verification failed!', 'https-redirection' ),
            ), 403 );
        }
    }

    public function handle_non_https_resources_scan() {
        $this->check_ajax_permissions( 'ehssl_non_https_resources_scan_form_nonce', false );

        $post_types       = isset( $_POST['ehssl_post_types'] ) ? $_POST['ehssl_post_types'] : array();
        $this->post_types = $post_types;

        $other_tables       = isset( $_POST['ehssl_other_tables'] ) ? $_POST['ehssl_other_tables'] : array();
        $this->other_tables = $other_tables;

        $flags       = isset( $_POST['ehssl_additional_flags'] ) ? $_POST['ehssl_additional_flags'] : array();
        $this->flags = $flags;

        if ( isset( $_POST['ehssl_scan_type'] ) ) {
            $this->scan_type = sanitize_text_field( $_POST['ehssl_scan_type'] );
        }

        $total = isset( $_POST['total'] ) ? json_decode( ( stripslashes( $_POST['total'] ) ), true ) : array();

        $offset = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;

        // Check if this is the initial request.
        if ( $offset == 0 ) {
            // Clear old data.
            $this->clear_scan_results();

            // Get the scannable records count.
            $total = $this->count_scannable_items();
        }

        try {
            if ( ! empty( $post_types ) ) {
                $this->scan_post_types( $offset );
            }

            if ( ! empty( $other_tables ) && in_array( 'wp_options_table', $other_tables ) ) {
                $this->scan_wp_options( $offset );
            }

            // Debug purpose only. Uncomment to inspect found matches.
            //EHSSL_Logger::log( $this->scan_results );

            if ( ! empty( $this->scan_results ) ) {
                $this->save_scan_result();
            }

            $next_offset = $offset + $this->batch_size;

            $response = array(
                    'message'     => __( 'URLs scanned successfully!', 'https-redirection' ),
                    'completed'   => $next_offset >= $total['ehssl_post_types'] && $next_offset >= $total['ehssl_other_tables'],
                    'processed'   => $next_offset,
                    'total'       => $total,
                    'next_offset' => $next_offset,
            );

            wp_send_json_success( $response );

        } catch ( \Exception $e ) {
            EHSSL_Logger::log( $e->getMessage(), 4 );
            wp_send_json_error( array(
                    'message' => $e->getMessage(),
            ) );
        }
    }

    public function count_scannable_items() {
        global $wpdb;

        $result = array(
            'ehssl_post_types' => 0,
            'ehssl_other_tables' => 0,
        );

        try {
            $post_types = isset( $_POST['ehssl_post_types'] ) ? $_POST['ehssl_post_types'] : array();
            if ( ! empty( $post_types ) ) {
                $post_types_placeholders = implode( ',', array_fill( 0, count( $post_types ), '%s' ) );
                $count = $wpdb->get_var( $wpdb->prepare(
                        "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type IN ($post_types_placeholders)",
                        array( ...$post_types ) ) );
                if ( ! empty( $count ) ) {
                    $result['ehssl_post_types'] = (int) $count;
                }
            }


            $other_tables = isset( $_POST['ehssl_other_tables'] ) ? $_POST['ehssl_other_tables'] : array();
            if ( ! empty( $other_tables ) ) {
                $count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . $wpdb->options . " WHERE option_name NOT LIKE %s AND option_name NOT LIKE %s ",
                        array( '_transient_%', '_site_transient_%' ),
                ) );
                if ( ! empty( $count ) ) {
                    $result['ehssl_other_tables'] = (int) $count;
                }
            }

        } catch ( \Exception $e ) {
            EHSSL_Logger::log( $e->getMessage(), 4);
        }

        return $result;
    }

    public function handle_get_non_https_resources_table() {
        $this->check_ajax_permissions( 'ehssl_scan_results', 'nonce' );

        try {
            self::render_http_scan_result_table();
            wp_die();
        } catch ( Exception $e ) {
            wp_die( esc_html( $e->getMessage() ) );
        }
    }

    public function scan_post_types( $offset = 0 ) {
        global $wpdb;

        $post_types = $this->post_types;
        $flags      = $this->flags;
        $limit      = $this->batch_size;

        $post_types_placeholders = implode( ',', array_fill( 0, count( $post_types ), '%s' ) );
        $posts = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_content, post_excerpt FROM {$wpdb->posts} WHERE post_type IN ($post_types_placeholders) LIMIT %d OFFSET %d",
                array( ...$post_types, $limit, $offset ) ) );

        $post_table_columns = array( 'post_content', 'post_excerpt' );

        foreach ( $posts as $post ) {
            $matches = array();

            foreach ( $post_table_columns as $p_col ) {
                $content = $post->$p_col;
                $scan_static_resources_only = $this->scan_type == 'scan_static_resources_only';
                $this->find_matches( $content, $matches[$p_col], $scan_static_resources_only );
            }

            $cols_map = array();

            foreach ( $matches as $column => $url_matches ) {
                if ( ! empty( $url_matches[0] ) ) {
                    $cols_map[ $column ] = array_unique( $url_matches[0] );
                }
            }

            $found_post_urls = array();
            if ( ! empty( $cols_map ) ) {
                $found_post_urls = array(
                        'source_table' => $wpdb->posts,
                        'source_uid'   => $post->ID,
                        'cols_map'     => $cols_map,
                        'meta_map'     => array(),
                );
            }

            if ( ! empty( $flags ) && in_array( 'include_post_meta', $flags ) ) {
                // Scan post meta
                $found_urls_in_meta = array();
                $all_post_meta      = get_post_meta( $post->ID );

                foreach ( $all_post_meta as $meta_key => $meta_values ) {
                    foreach ( $meta_values as $meta_value ) {
                        // Convert arrays/objects into searchable text
                        if ( is_array( $meta_value ) || is_object( $meta_value ) ) {
                            $meta_value = wp_json_encode( $meta_value );
                        }

                        if ( ! is_string( $meta_value ) ) {
                            continue;
                        }

                        $scan_static_resources_only = $this->scan_type == 'scan_static_resources_only';
                        $this->find_matches( $meta_value, $meta_matches, $scan_static_resources_only);

                        if ( empty( $meta_matches[0] ) ) {
                            continue;
                        }

                        foreach ( array_unique( $meta_matches[0] ) as $url ) {
                            $found_urls_in_meta[] = array(
                                    'url'      => $url,
                                    'meta_key' => $meta_key,
                            );
                        }
                    }
                }

                if ( ! empty( $found_urls_in_meta ) ) {
                    if ( empty( $found_post_urls ) ) {
                        $found_post_urls = array(
                                'source_table' => $wpdb->posts,
                                'source_uid'   => $post->ID,
                                'cols_map'     => array(),
                                'meta_map'     => array(),
                        );
                    }

                    foreach ( $found_urls_in_meta as $item ) {
                        $found_post_urls['meta_map'][ $item['meta_key'] ][] = $item['url'];
                    }
                }
            }

            if ( ! empty( $found_post_urls ) ) {
                $this->scan_results[] = $found_post_urls;
            }
        }
    }

    public function scan_wp_options( $offset = 0 ) {
        global $wpdb;

        $limit = $this->batch_size;

        $options = $wpdb->get_results( $wpdb->prepare( "SELECT option_name, option_value
            FROM " . $wpdb->options . " 
            WHERE option_name NOT LIKE %s AND option_name NOT LIKE %s 
            LIMIT %d OFFSET %d",
                array( '_transient_%', '_site_transient_%', $limit, $offset ),
        ) );

        $table_columns = array( 'option_value' );

        foreach ( $options as $option ) {
            $matches = array();

            foreach ( $table_columns as $col ) {
                $content = $option->$col;
                $scan_static_resources_only = $this->scan_type == 'scan_static_resources_only';
                $this->find_matches( $content, $matches[$col], $scan_static_resources_only );
            }

            $cols_map = array();

            foreach ( $matches as $column => $url_matches ) {
                if ( ! empty( $url_matches[0] ) ) {
                    $cols_map[ $column ] = array_unique( $url_matches[0] );
                }
            }

            if ( ! empty( $cols_map ) ) {
                $this->scan_results[] = array(
                        'source_table' => $wpdb->options,
                        'source_uid'   => $option->option_name,
                        'cols_map'     => $cols_map,
                        'meta_map'     => array(),
                );
            }
        }
    }

    public function find_matches( $haystack, &$result, $scan_static_resources_only ) {
        if ( ! $scan_static_resources_only ) {
            // Match any non-http urls
            preg_match_all(
                    '#http://[^\s"\'<>{}|\\\\^`]+#i',
                    $haystack,
                    $result
            );

            return;
        }

        /*
         * Match URLs from:
         * - img[src]
         * - script[src]
         * - link[href]
         * - iframe[src]
         * - source[src]
         * - video[src]
         * - audio[src]
         * - object[data]
         * - video[poster]
         */
        preg_match_all(
                '/<(?:img|script|link|iframe|source|video|audio|object)\b[^>]*\b(?:src|href|data|poster)\s*=\s*["\']\Khttp:\/\/[^"\']+/i',
                $haystack,
                $result
        );

        /*
         * Match URLs from srcset attributes.
         */
        preg_match_all(
                '/\bsrcset\s*=\s*["\']([^"\']+)["\']/i',
                $haystack,
                $srcset_matches
        );

        foreach ( $srcset_matches[1] as $srcset ) {
            preg_match_all(
                    '#http://[^\s,]+#i',
                    $srcset,
                    $matches
            );


            array_push( $result[0], ...$matches[0] );
        }

        /*
         * Match URLs from CSS url(...)
         * Examples:
         * background-image:url(http://example.com/bg.jpg)
         * background:url('http://example.com/bg.jpg')
         */
        preg_match_all(
                '#url\s*\(\s*[\'"]?\Khttp://[^)"\'\s]+#i',
                $haystack,
                $css_urls
        );

        array_push( $result[0], ...$css_urls[0] );
    }

    public function save_scan_result() {
        global $wpdb;
        $inserted = 0;
        foreach ( $this->scan_results as $row ) {
            $result = $wpdb->insert(
                $wpdb->prefix . 'ehssl_resource_scan_tbl',
                array(
                    'source_table' => $row['source_table'],
                    'source_uid'   => $row['source_uid'],
                    'cols_map'     => maybe_serialize( isset( $row['cols_map'] ) ? $row['cols_map'] : array() ),
                    'meta_map'     => maybe_serialize( isset( $row['meta_map'] ) ? $row['meta_map'] : array() ),
                ),
                array( '%s', '%s', '%s', '%s' )
            );
            if ( false === $result ) {
                return false;
            }
            $inserted += $result;
        }
        return $inserted;
    }

    public static function get_scan_results_count( $skip_fixed = false ) {
        global $wpdb;
        return (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}ehssl_resource_scan_tbl WHERE (%d = 0 OR fixed != 1)",
            (int) $skip_fixed
        ) );
    }

    public static function get_scan_results_chunk( $offset = 0, $limit = 10, $where = array(), $skip_fixed = false ) {
        global $wpdb;

        // Only the scan-row ID is a supported selection filter.
        if ( isset( $where['id'] ) ) {
            if ( ! is_array( $where['id'] ) ) {
                return array();
            }
            $ids = array_values( array_filter( array_map( 'absint', $where['id'] ) ) );
            if ( empty( $ids ) ) {
                return array();
            }
            $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
            return $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}ehssl_resource_scan_tbl WHERE (%d = 0 OR fixed != 1) AND id IN ($placeholders) ORDER BY id LIMIT %d OFFSET %d",
                array_merge( array( (int) $skip_fixed ), $ids, array( $limit, $offset ) )
            ), ARRAY_A );
        }

        return $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}ehssl_resource_scan_tbl WHERE (%d = 0 OR fixed != 1) ORDER BY id LIMIT %d OFFSET %d",
            (int) $skip_fixed, $limit, $offset
        ), ARRAY_A );
    }

    public function mark_items_fixed( &$batch ) {
        if (empty($batch) || !is_array($batch)) {
            return;
        }

        global $wpdb;

        $ids = array_column( $batch, 'id' );
        $ids = array_unique( $ids );

        if ( empty( $ids ) ) {
            return;
        }

        $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );

        $wpdb->query( $wpdb->prepare(
            "UPDATE {$wpdb->prefix}ehssl_resource_scan_tbl SET fixed = 1 WHERE id IN ($placeholders)",
            $ids
        ) );
    }

    public function clear_scan_results() {
        global $wpdb;
        return $wpdb->query( "DELETE FROM {$wpdb->prefix}ehssl_resource_scan_tbl" );
    }

    public function handle_load_non_https_resources_table_page() {
        $this->check_ajax_permissions( 'ehssl_scan_results', 'nonce' );

        self::render_http_scan_result_table();

        wp_die();
    }

    public static function render_http_scan_result_table() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $table = new EHSSL_Static_Resources_Scan_Result_Table();
        $table->prepare_items();
        ?>
        <div class="wrap" id="nhs-table-container">
            <form method="post" id="ehssl_non_https_resources_table_form">
                <?php $table->display(); ?>
                <input type="hidden" name="ehssl_update_all_http_urls_nonce" value="<?php echo esc_attr( wp_create_nonce( 'ehssl_update_all_http_urls' ) ); ?>">
            </form>
        </div>
        <?php
    }

    public function handle_update_http_urls() {
        $this->check_ajax_permissions( 'ehssl_update_all_http_urls', 'nonce' );

        $limit = $this->batch_size;
        $offset = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;
        $selected_ids = isset( $_POST['selected_ids'] ) ? json_decode(stripslashes($_POST['selected_ids'])) : null;

        $total = 0;

        if ( is_null( $selected_ids ) ) {
            // Need to update all items.
            $total = self::get_scan_results_count();
        } else {
            // Need to update selected items.
            $total = count( $selected_ids );
        }

        if ( empty( $total ) ) {
            wp_send_json_success(
                array(
                    'completed' => true,
                    'processed' => 0,
                )
            );
        }

        $batch = self::get_scan_results_chunk( $offset, $limit, array( 'id' => $selected_ids ) );

        if ( empty( $batch ) ) {
            wp_send_json_success(
                array(
                    'completed' => true,
                    'processed' => 0,
                )
            );
        }

        try {
            $this->update_urls( $batch );

            $this->mark_items_fixed($batch);

            $next_offset = $offset + count( $batch );

            $response = array(
                'message'     => __( 'URLs update to https successfully.', 'https-redirection' ),
                'completed'   => $next_offset >= $total,
                'processed'   => $next_offset,
                'total'       => $total,
                'next_offset' => $next_offset,
            );

            wp_send_json_success($response);

        } catch ( \Exception $e ) {
            EHSSL_Logger::log( $e->getMessage(), 4 );
            wp_send_json_error( array(
                    'message' => $e->getMessage(),
            ) );
        }
    }

    public function update_urls( &$batch ) {
        global $wpdb;
        foreach ( $batch as $item ) {
            switch ( $item['source_table'] ) {
                case $wpdb->posts:
                    $this->update_urls_in_post_type_item( $item );
                    break;
                case $wpdb->options:
                    $this->update_urls_in_option_table( $item );
                    break;
            }
        }
    }

    public function update_urls_in_post_type_item( &$item ) {
        $cols_map = isset( $item['cols_map'] ) ? maybe_unserialize( $item['cols_map'] ) : array();
        $meta_map = isset( $item['meta_map'] ) ? maybe_unserialize( $item['meta_map'] ) : array();

        $post_id = (int) $item['source_uid'];

        if (! empty( $cols_map ) ) {

            global $wpdb;
            $post = $wpdb->get_row( $wpdb->prepare( "SELECT ID, post_content, post_excerpt FROM {$wpdb->posts} WHERE ID = %d", $post_id ) );
            if ( empty( $post ) ) {
                return;
            }

            $update_columns = array();

            foreach ( $cols_map as $column => $urls ) {
                if ( ! property_exists( $post, $column ) || empty( $urls )  ) {
                    continue;
                }

                $value = $post->$column;

                // Check if the column data is serialized or not.
                $was_serialized = is_serialized( $value );

                $value = $this->replace_urls_recursively( $value, $urls );

                if ( $was_serialized ) {
                    $value = maybe_serialize( $value );
                }

                $update_columns[ $column ] = $value;
            }

            if ( ! empty( $update_columns ) ) {
                $wpdb->update(
                        $wpdb->posts,
                        $update_columns,
                        array(
                            'ID' => $post_id
                        )
                );

                clean_post_cache( $post_id );
            }
        }

        if ( ! empty( $meta_map ) ) {
            $meta_keys_to_update = array_keys( $meta_map );
            $all_meta            = get_post_meta( $post_id );
            foreach ( $meta_keys_to_update as $meta_key ) {
                $value = isset($all_meta[$meta_key][0]) ? $all_meta[$meta_key][0] : '';

                $value = $this->replace_urls_recursively( $value, $meta_map[ $meta_key ] );

                update_post_meta( $post_id, $meta_key, $value );
            }
        }

    }

    public function update_urls_in_option_table( &$item ) {
        $cols_map = isset( $item['cols_map'] ) ? maybe_unserialize( $item['cols_map'] ) : '';

        $option_name = isset( $item['source_uid'] ) ? sanitize_key( $item['source_uid'] ) : '';

        if ( empty( $cols_map ) || empty( $option_name ) ) {
            return;
        }

        foreach ( $cols_map as $urls ) {
            if ( empty( $urls ) ) {
                continue;
            }

            $option_value = get_option( $option_name, '' );

            $option_value = $this->replace_urls_recursively( $option_value, $urls );

            update_option( $option_name, $option_value );
        }

    }

    public function replace_with_https( $http_url, $subject ) {
        $https_url = preg_replace( '#^http://#i', 'https://', $http_url );

        return str_replace( $http_url, $https_url, $subject );
    }

    /**
     * Makes sure it properly updates serialized data.
     */
    public function replace_urls_recursively( $value_to_update, $urls ) {
        $value_to_update = maybe_unserialize( $value_to_update );

        if ( is_array( $value_to_update ) ) {
            foreach ( $value_to_update as $key => $value ) {
                $value_to_update[ $key ] = $this->replace_urls_recursively( $value, $urls );
            }

            return $value_to_update;
        }

        if ( is_object( $value_to_update ) ) {
            foreach ( $value_to_update as $key => $value ) {
                $value_to_update->$key = $this->replace_urls_recursively( $value, $urls );
            }

            return $value_to_update;
        }

        if ( is_string( $value_to_update ) ) {
            foreach ( $urls as $url ) {
                $value_to_update = $this->replace_with_https( $url, $value_to_update );
            }
        }

        return $value_to_update;
    }

}

new EHSSL_Non_HTTPS_Resources_Scan_Update();