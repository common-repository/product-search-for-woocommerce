<?php

namespace StorePlugin\WcProductSearch;

final class Boot {
    /**
     * Boot object constructor
     *
     * @return void
     */
    public static function execute() {
        // Enqueue assets object
        $assets = new Assets();
        \add_action( 'wp_enqueue_scripts', [ $assets, 'ajax_search_enqueue_frontend_assets' ] );
        \add_action( 'admin_enqueue_scripts', [ $assets, 'ajax_search_enqueue_admin_assets' ] );

        // Setting object
        $settings = new Settings();
        \add_action( 'admin_init', [ $settings, 'ajax_search_setting_init' ]);
        \add_action( 'admin_menu', [ $settings, 'ajax_search_settings_page' ] );
        \add_filter( 'plugin_action_links_' . plugin_basename( AJAX_SEARCH_PATH ), [ $settings, 'ajax_search_settings_link' ] );

        // Check if the ajax search is functional
        if( Settings::getData('ajax_search_enable_search') ) {
            // Search query object
            $query_search = new AjaxSearchQueries();
            \add_action( 'wp_ajax_query_ajax_search', [ $query_search, 'ajax_search_query' ] );
            \add_action( 'wp_ajax_nopriv_query_ajax_search', [ $query_search, 'ajax_search_query' ] );
        }

        // Shortcode for search form field
        $shortcode = new AjaxSearchShortCode();
        add_shortcode( 'product_search_for_woocommerce', [ $shortcode, 'ajax_search_shortcode_cb' ] );

        // Refetch search data
        $refetch_search_tbl = new AjaxSearchRefetch();
        \add_action( 'wp_ajax_refetch_ajax_search', [ $refetch_search_tbl, 'ajax_search_refetch_products' ] );
        \add_action( 'wp_ajax_nopriv_refetch_ajax_search', [ $refetch_search_tbl, 'ajax_search_refetch_products' ]  );

        // Sync product data
        $sync   = new SyncSearchData();
        \add_action( 'save_post_product', [ $sync, 'ajax_search_synchronize_product' ], 10, 3 );
        \add_action( 'delete_post', [ $sync, 'ajax_search_delete_product' ], 20, 3 );

    }

}
