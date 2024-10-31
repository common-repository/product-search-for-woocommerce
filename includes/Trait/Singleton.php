<?php

namespace StorePlugin\WcProductSearch\Trait;

trait Singleton {
    /**
     * Singleton instance
     *
     * @var null|object
     */
    private static $_instance = null;

    /**
     * Singleton does not allow cloning and serialization
     *
     * @return void
     */
    private function __clone() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden!', 'product-search-for-woocommerce' ), '1.0.0' );
    }

    /**
     * Singleton does not allow cloning and unserialization
     *
     * @return void
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, esc_html__( 'Unserialization is forbidden!', 'product-search-for-woocommerce' ), '1.0.0' );
    }

    /**
     * Instantiate singleton object
     *
     * @return object|null
     */
    public static function init() {
        if( is_null( self::$_instance ) ) {
            self::$_instance = new static();
        }

        return self::$_instance;
    }

}
