<?php

/**
 * Plugin Name:       Product Search for WooCommerce
 * Plugin URI:        https://storeplugin.net/plugins/product-search-for-woocommerce
 * Description:       Product Search for WooCommerce
 * Version:           1.0.0
 * Author:            StorePlugin
 * Author URI:        https://storeplugin.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       product-search-for-woocommerce
 * Domain Path:       /languages
 */
if ( ! defined( 'WPINC' ) ) {
	exit;
}

if( class_exists( 'AJAX_SEARCH_INIT_PRO' ) ) {
    return;
}

require __DIR__ . '/vendor/autoload.php';

final class AJAX_SEARCH_INIT {

    /**
     * Check if the pro plugin is active
     *
     * @return bool
     */
    private static function is_active() {
        $plugin_name = 'product-search-pro-for-woocommerce';
        $plugin_path = trailingslashit( WP_PLUGIN_DIR ) . "{$plugin_name}/{$plugin_name}.php";

        // Check if pro version is active.
        if ( in_array( $plugin_path, wp_get_active_and_valid_plugins() ) ) {
            return false;
        }

        return true;
    }

    /**
     * Boot plugin
     *
     * @return void
     */
    public static function ajax_search_init() {
        if( self::is_active() ) {
            self::constants();
            load_plugin_textdomain( 'product-search-for-woocommerce', false, AJAX_SEARCH_DIR . '/languages/' );
            StorePlugin\WcProductSearch\Boot::execute();
        }
    }

    /**
     * Add plugin constants
     *
     * @return void
     */
    public static function constants() {
        define( 'AJAX_SEARCH_NAME', 'product-search-for-woocommerce' );
        define( 'AJAX_SEARCH_PATH', __FILE__ );
        define( 'AJAX_SEARCH_DIR', __DIR__ );
        define( 'AJAX_SEARCH_URL', plugins_url( '', AJAX_SEARCH_PATH ) );
    }

    /**
     * Fire on plugin activation
     *
     * @return void
     */
    public static function activate() {
        update_option( 'ajax_search_woocommerce_activation_details', [
            'version'	=> '1.0.0',
            'date'		=> gmdate('m d Y'),
        ]);

        AJAX_SEARCH_DB()->create_database();
        AJAX_SEARCH_DB()->insert_ajax_search_cols();

        flush_rewrite_rules();
    }

    /**
     * Fire on plugin deactivation
     *
     * @return void
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }

}

register_activation_hook( __FILE__, [ 'AJAX_SEARCH_INIT', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'AJAX_SEARCH_INIT', 'deactivate' ] );
add_action( 'plugins_loaded', [ 'AJAX_SEARCH_INIT', 'ajax_search_init' ] );
