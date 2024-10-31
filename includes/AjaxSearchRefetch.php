<?php

namespace StorePlugin\WcProductSearch;

/**
 * Refetch products data into ajax_search table
 *
 * @package StorePlugin\WcProductSearch
 */
class AjaxSearchRefetch {
    /**
     * Refetch product database and save in woo_ajax_search table
     *
     * @return void
     */
    public function ajax_search_refetch_products() {
        // Check the hit secuirity
        $refetch_nonce = sanitize_text_field( $_POST['ajxsrc_nonce'] );

        if( ! isset( $_POST['refetch'] ) ) {
            return;
        }

        if( ! wp_verify_nonce( $refetch_nonce, 'ajxsrc_refetch_nonce_action' ) ) {
            return;
        }

        // Fetch products data and save them into woo_ajax_search
        AJAX_SEARCH_DB()->truncate();
        AJAX_SEARCH_DB()->insert_ajax_search_cols();

        wp_send_json_success( [
            'success' => true,
            'message'   => __( 'Ajax Search Database Refetched!', 'product-search-for-woocommerce' ),
        ]);
    }

}
