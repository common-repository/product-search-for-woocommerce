<?php
/**
 * Global functions for __plugin_structure
 *
 * @since 1.0.0
 */
if( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Instantiate Singleton of AjaxSearchDatabase
 *
 * @return AjaxSearchDatabase
*/
if( ! function_exists( 'AJAX_SEARCH_DB' ) ) {
    function AJAX_SEARCH_DB() {
        return \StorePlugin\WcProductSearch\AjaxSearchDatabase::init();
    }
}
