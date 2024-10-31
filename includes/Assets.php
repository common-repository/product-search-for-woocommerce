<?php

namespace StorePlugin\WcProductSearch;

class Assets {
    /**
     * Enqueue frontend assets
     *
     * @return void
     */
    public function ajax_search_enqueue_frontend_assets() {
        // Style file
        wp_enqueue_style( 'ajax-search-style', AJAX_SEARCH_URL . '/assets/css/wp-ajax-search-style.css', [], '1.0.0' );

        // Script file
        wp_enqueue_script( 'wp-ajax-search', AJAX_SEARCH_URL . '/assets/js/wp-ajax-search-script.js', array( 'jquery' ), '1.0.0', true );

		if( Settings::getData('ajax_search_enable_search') ) {
            $searchDOM = '/assets/js/wp-ajax-search-dom-script.js';
			wp_enqueue_script( 'wc-ajax-search-dom', AJAX_SEARCH_URL . $searchDOM, array( 'jquery' ), filemtime(AJAX_SEARCH_DIR . $searchDOM), true );
			wp_localize_script( 'wc-ajax-search-dom', 'ajax_search', array(
				'admin_url'		=> admin_url('admin-ajax.php'),
				'wp_nonce'		=> wp_create_nonce('ajax_search_nonce_action'),
				'notFound'		=> Settings::getData('ajax_search_no_result'),
				'loading'		=> Settings::getData('ajax_search_waiting_result'),
				'placeholder'	=> Settings::getData('ajax_search_placeholder'),
				'product_img'	=> Settings::getData('ajax_search_item_product_img'),
				'product_price'	=> Settings::getData('ajax_search_item_product_price'),
			));
		}
    }

    /**
     * Enqueue admin assets
     *
     * @return void
     */
    public function ajax_search_enqueue_admin_assets() {
        // Style file
        wp_enqueue_style( 'ajax-search-admin-style', AJAX_SEARCH_URL . '/assets/css/admin-ajax-search-style.css', [], '1.0.0' );

        // Script file
        wp_enqueue_script( 'admin-ajax-search', AJAX_SEARCH_URL . '/assets/js/admin-ajax-search-script.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'ajax-refetch-search', AJAX_SEARCH_URL . '/assets/js/admin-ajax-refetch-data.js', array( 'jquery' ), '1.0.0', true );
		wp_localize_script( 'ajax-refetch-search', 'refetch_product', [
			'admin_url'		=> admin_url('admin-ajax.php'),
			'wp_nonce'		=> wp_create_nonce('ajxsrc_refetch_nonce_action'),
		]);
    }

}
