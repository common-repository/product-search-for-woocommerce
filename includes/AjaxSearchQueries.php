<?php

namespace StorePlugin\WcProductSearch;

/**
 * The file that defines the main start class.
 *
 * A class definition that includes attributes and functions used across both the
 * theme-facing side of the site and the admin area.
 *
 * @package StorePlugin\WcProductSearch
 */
class AjaxSearchQueries {
    /**
     * Send data over json format
     *
     * @return void
     */
    public function ajax_search_query() {
        // Verify nonce
        $ajax_search_nonce = sanitize_text_field( $_POST['ajax_search_nonce'] );
        if( ! wp_verify_nonce( $ajax_search_nonce, 'ajax_search_nonce_action' ) ) {
            return;
        }

        // get the search query
        $product_per_list = absint( Settings::getData('ajax_search_item_limit') );
        $search_value   = sanitize_text_field( $_POST['indices'] );
        $search_offset  = absint( $_POST['offset'] );
        $total_pages    = ( $search_offset * $product_per_list );
        $all_pages      = AJAX_SEARCH_DB()->total_page( $search_value, $product_per_list );
        $search_results = AJAX_SEARCH_DB()->search_ajax_table( $search_value, $total_pages, $product_per_list );

        // Send query in json
        wp_send_json([
            'data' => $search_results,
            'page' => $all_pages
        ]);
    }

}
