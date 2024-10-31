<?php

namespace StorePlugin\WcProductSearch;

/**
 * Populate ajax search database
 *
 * @package StorePlugin\WcProductSearch
 */
class SyncSearchData {
    /**
     * Save a product data to search table on submit
     *
     * @param int $post_id
     * @param WP_Post $post
     * @param bool $update
     * @return void
     */
    public function ajax_search_synchronize_product( $post_id, $post, $update ) {
        AJAX_SEARCH_DB()->update_search_table_on_product_submit( $post_id, $post, $update );
    }

    /**
     * Remove a product from search table when owner a will delete one
     *
     * @param int $post_id
     * @return void
     */
    public function ajax_search_delete_product( $post_id ) {
        AJAX_SEARCH_DB()->delete_product_from_search_table( $post_id );
    }

}
