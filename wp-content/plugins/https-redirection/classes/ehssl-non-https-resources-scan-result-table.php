<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class EHSSL_Static_Resources_Scan_Result_Table extends WP_List_Table {

    private $total_items_count;

    public function __construct() {
        parent::__construct(
            array(
                'singular' => 'result',
                'plural'   => 'results',
                'ajax'     => true,
            )
        );
    }

    public function get_columns() {
        return array(
            'cb'         => '<input type="checkbox" />',
            'id'        => 'ID',
            'context'        => 'Context/Location',
            'urls'        => 'Non HTTPS URLs',
        );
    }

    public function prepare_items() {
        $per_page     = 10;

        $current_page = $this->get_pagenum();

        $offset = ( $current_page - 1 ) * $per_page;

        $total_items = EHSSL_Non_HTTPS_Resources_Scan_Update::get_scan_results_count(true);

        $this->total_items_count = $total_items;

        $this->items = EHSSL_Non_HTTPS_Resources_Scan_Update::get_scan_results_chunk( $offset, $per_page, array(), true);

        $this->_column_headers = array(
                $this->get_columns(),
                array(),
                array(),
        );

        $this->set_pagination_args(
                array(
                        'total_items' => $total_items,
                        'per_page'    => $per_page,
                        'total_pages' => ceil( $total_items / $per_page ),
                )
        );
    }

    public function column_default( $item, $column_name ) {
        global $wpdb;

        switch ( $column_name ) {
            case 'id':
                return $item['id'];

            case 'urls':
                $output = '';
                $urls_map = array_merge( maybe_unserialize($item['cols_map']),  maybe_unserialize($item['meta_map']));
                if (!empty($urls_map)) {
                    $this->get_urls_output( $urls_map, $output);
                }

                return $output;

            case 'context':
                $output = 'Unknown';
                if ($item['source_table'] == $wpdb->posts) {
                    $output =  get_the_title( $item['source_uid'] ) . " (Post ID: ".$item['source_uid'].")";
                } elseif ($item['source_table'] == $wpdb->options) {
                    $output = $item['source_uid'] . " (WP Option)";
                } else {
                    $output = __('Unknown', 'https-redirection');
                }

                return $output;
        }

        return '';
    }

    public function column_cb( $item ) {
        return sprintf(
                '<input type="checkbox" name="ehssl_non_https_resources_scan_ids[]" value="%d" />',
                $item['id']
        );
    }

    public function get_bulk_actions() {
        return array(
                'update_to_https' => __( 'Update to HTTPS Version', 'https-redirection' ),
        );
    }

    public function get_urls_output($urls, &$output ) {
        foreach ($urls as $url) {
            if(is_array($url)) {
                $this->get_urls_output($url, $output);
            } else {
                $output .= '<div style="margin-bottom: 4px"><code>' . esc_url_raw( $url ) . '</code></div>';
            }
        }
    }

    protected function extra_tablenav( $which ) {
        if ( 'top' !== $which ) {
            return;
        }

        if (empty($this->total_items_count)){
            return;
        }

        ?>
        <div class="alignleft actions">
            <button
                    type="button"
                    class="button"
                    id="ehssl_update_all_found_http_urls"
            >
                <?php esc_html_e('Update All URLs', 'https-redirection'); ?>
            </button>

        </div>
        <style>
            .column-urls {
                width: 50%;
            }

            .column-id {
                width: 10%;
            }
        </style>
        <?php
    }
}