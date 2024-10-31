<?php

namespace StorePlugin\WcProductSearch;

/**
 * Settings class.
 *
 * A class definition that includes attributes and functions used across both the
 * theme-facing side of the site and the admin area.
 *
 * @package StorePlugin\WcProductSearch
 */
class Settings {
    /**
     * Link to settings page from plugins screen
     *
     * Need to create this setting page.//
     */
    public function ajax_search_settings_link( $links ) {
        $links[] = '<a href="' . admin_url( 'admin.php?page=sp-product-search' ) . '">Settings</a>';
        return $links;
    }

    /**
     * Register ajax search settings
     *
     * @return void
     */
    public function ajax_search_setting_init() {

        register_setting( 'ajax_search_settings', 'ajax_search' );

        // Search config section
        add_settings_section(
            'ajax_search_config',
            __( 'Product Search Config', 'product-search-for-woocommerce' ),
            [ $this, 'ajax_search_config_section_cb' ],
            'sp-product-search'
        );

        // Search config fields
        add_settings_field(
            'ajax_search_enable_search',
            __( 'Enable Ajax Search', 'product-search-for-woocommerce' ),
            [ $this, 'ajax_search_enable_search_field' ],
            'sp-product-search',
            'ajax_search_config',
            [
                'label_for' => 'ajax_search_enable_search'
            ]
        );

        // It's not used anywhere to output the value.
        add_settings_field(
            'ajax_search_shortcode',
            __( 'Shortcode', 'product-search-for-woocommerce' ),
            [ $this, 'ajax_search_shortcode_cb' ],
            'sp-product-search',
            'ajax_search_config',
            [
                'label_for' => 'ajax_search_shortcode',
                'class'     => 'ajax_search_shortcode'
            ]
        );

        // Search bar section
        add_settings_section(
            'ajax_search_bar',
            __( 'Product Search Field', 'product-search-for-woocommerce' ),
            [ $this, 'ajax_search_section_search_field_cb' ],
            'sp-product-search'
        );

        // Search bar fields
        add_settings_field(
            'ajax_search_submit_btn',
            __( 'Submit button', 'product-search-for-woocommerce' ),
            [ $this, 'ajax_search_submit_button' ],
            'sp-product-search',
            'ajax_search_bar',
            [
                'label_for' => 'ajax_search_submit_btn'
            ]
        );

        add_settings_field(
            'ajax_search_no_result',
            __( 'Not found text', 'product-search-for-woocommerce' ),
            [ $this, 'ajax_search_empty_result_field' ],
            'sp-product-search',
            'ajax_search_bar',
            [
                'label_for' => 'ajax_search_no_result'
            ]
        );

        add_settings_field(
            'ajax_search_waiting_result',
            __( 'Add loading... text', 'product-search-for-woocommerce' ),
            [ $this, 'ajax_search_loading_field' ],
            'sp-product-search',
            'ajax_search_bar',
            [
                'label_for' => 'ajax_search_waiting_result'
            ]
        );

        add_settings_field(
            'ajax_search_placeholder',
            __( 'Add placeholder', 'product-search-for-woocommerce' ),
            [ $this, 'ajax_search_placeholder_field' ],
            'sp-product-search',
            'ajax_search_bar',
            [
                'label_for' => 'ajax_search_placeholder'
            ]
        );

        // Search bar items section
        add_settings_section(
            'ajax_search_items',
            __( 'Product Search Items', 'product-search-for-woocommerce' ),
            [ $this, 'ajax_search_items_section_cb' ],
            'sp-product-search'
        );

        // Search items fields
        add_settings_field(
            'ajax_search_item_limit',
            __( 'Limit search items', 'product-search-for-woocommerce' ),
            [ $this, 'ajax_search_limit_item_filed' ],
            'sp-product-search',
            'ajax_search_items',
            [
                'label_for' => 'ajax_search_item_limit'
            ]
        );

        add_settings_field(
            'ajax_search_item_product_img',
            __( 'Product image', 'product-search-for-woocommerce' ),
            [ $this, 'ajax_search_image_field' ],
            'sp-product-search',
            'ajax_search_items',
            [
                'label_for' => 'ajax_search_item_product_img'
            ]
        );

        add_settings_field(
            'ajax_search_item_product_price',
            __( 'Product price', 'product-search-for-woocommerce' ),
            [ $this, 'ajax_search_price_field' ],
            'sp-product-search',
            'ajax_search_items',
            [
                'label_for' => 'ajax_search_item_product_price'
            ]
        );

        // Refetch products' data section
        add_settings_section(
            'ajax_search_refetch_products_section',
            __( 'Refetch Latest Products\'s Data', 'product-search-for-woocommerce' ),
            [ $this, 'ajax_search_refetch_section' ],
            'sp-product-search'
        );

        add_settings_field(
            'ajax_search_refetch_products',
            __( 'Refetch Products', 'product-search-for-woocommerce' ),
            [ $this, 'ajax_search_refetch_product_table' ],
            'sp-product-search',
            'ajax_search_refetch_products_section',
            [
                'label_for' => 'ajax_search_refetch_products'
            ]
        );
    }

    /**
     * Get option field data
     *
     * @param string $name
     * @param string $default
     * @return mixed
     */
    public static function getData( $name, $default = '' ) {
        $value = get_option( 'ajax_search' );
        return $value[$name] ?? $default;
    }

    /**
     * Button field
     *
     * @param string $name
     * @param string $caption Provide a button caption
     * @return void
     */
    protected function button( $name, $caption = 'button' ) {
        printf( '<button type="button" name="%1$s[%2$s]" id="%2$s" class="button button-primary">%3$s</button>', 'ajax_search', esc_attr( $name ), esc_html( $caption ) );
    }

    /**
     * Text field
     *
     * @param string $name
     * @param string $placeholder
     * @return void
     */
    protected function text( $name, $placeholder ) {
        printf( '<input type="text" name="%1$s[%2$s]" id="%2$s" value="%3$s" placeholder="%4$s">', 'ajax_search', esc_attr( $name ), esc_attr( self::getData( $name ) ), esc_attr( $placeholder ) );
    }

    /**
     * Number field
     *
     * @param string $name
     * @param string $placeholder
     * @return void
     */
    protected function number( $name, $placeholder ) {
        printf( '<input type="number" name="%1$s[%2$s]" id="%2$s" value="%3$s" placeholder="%4$s">', 'ajax_search', esc_attr( $name ), esc_attr( self::getData( $name, 7 ) ), esc_attr( $placeholder ) );
    }

    /**
     * Checkbox
     *
     * @param string $name
     * @return void
     */
    protected function checkbox( $name ) {
        printf( '<input type="checkbox" name="%1$s[%2$s]" id="%2$s" %3$s>', 'ajax_search', esc_attr( $name ), checked( esc_attr( self::getData( $name ) ), 'on', false ) );
    }

    /**
     * Search config section
     *
     * @return string
     */
    public function ajax_search_config_section_cb() {}

    /**
     * Search bar section
     *
     * @return string
     */
    public function ajax_search_section_search_field_cb() {}

    /**
     * Search items section
     *
     * @return string
     */
    public function ajax_search_items_section_cb() {}

    /**
     * Refetch product section
     *
     * @return string
     */
    public function ajax_search_refetch_section() {}

    /**
     * Enable search field
     *
     * @return void
     */
    public function ajax_search_enable_search_field( $args ) {
        $this->checkbox( $args['label_for'] );
    }

    /**
     * Enable search field
     *
     * @return void
     */
    public function ajax_search_shortcode_cb( $args ) {
        echo wp_kses_post( '<p class="ajax_search_shortcode">[product_search_for_woocommerce]</p>' );
        echo wp_kses_post( '<small class="ajax_search_shortcode_desc">*Use the shortcode to activate ajax search to your shop.</small>' );
    }

    /**
     * Add submit button to search bar
     *
     * @return void
     */
    public function ajax_search_submit_button( $args ) {
        $this->checkbox( $args['label_for'] );
    }

    /**
     * Add no result found input text for search field
     *
     * @return void
     */
    public function ajax_search_empty_result_field( $args ) {
        $this->text( $args['label_for'], __( 'No result found', 'product-search-for-woocommerce' ) );
    }

    /**
     * Add waiting input text for search field
     *
     * @return void
     */
    public function ajax_search_loading_field( $args ) {
        $this->text( $args['label_for'], __( 'Loading...', 'product-search-for-woocommerce') );
    }

    /**
     * Add placeholder for search field
     *
     * @return void
     */
    public function ajax_search_placeholder_field( $args ) {
        $this->text( $args['label_for'], __( 'Add placeholder for search bar', 'product-search-for-woocommerce' ) );
    }

    /**
     * Limit item number
     *
     * @return void
     */
    public function ajax_search_limit_item_filed( $args ) {
        $this->number( $args['label_for'], __( 'Limit items', 'product-search-for-woocommerce' ) );
    }

    /**
     * Show product image in search item
     *
     * @return void
     */
    public function ajax_search_image_field( $args ) {
        $this->checkbox( $args['label_for'] );
    }

    /**
     * Show product price in search item
     *
     * @return void
     */
    public function ajax_search_price_field( $args ) {
        $this->checkbox( $args['label_for'] );
    }

    /**
     * Refetch products' items in ajax_search_table
     *
     * @return void
     */
    public function ajax_search_refetch_product_table( $args ) {
        $this->button( $args['label_for'], __( 'Refetch', 'product-search-for-woocommerce' ) );
    }

    /**
     * Ajax setting page
     *
     * @return void
     */
    public function ajax_search_settings_page() {
        add_menu_page(
            __( 'Product Search for WooCommerce', 'product-search-for-woocommerce' ),
            __( 'Product Search', 'product-search-for-woocommerce' ),
            'manage_options',
            'sp-product-search',
            [ $this, 'ajax_search_options' ],
            'dashicons-search'
        );
    }

    /**
     * Save settings page
     *
     * @return void
     */
    public function ajax_search_options() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // add error/update messages
        // phpcs:ignore
        if ( isset( $_GET['settings-updated'] ) ) {
            add_settings_error(
                'ajax_search_messages',
                'ajax_search_message',
                __( 'Settings Saved', 'product-search-for-woocommerce' ),
                'updated'
            );
        }

        // show error/update messages
        settings_errors( 'ajax_search_messages' );
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
            <?php
                settings_fields( 'ajax_search_settings' );
                do_settings_sections( 'sp-product-search' );
                submit_button( 'Save Settings' );
            ?>
            </form>
        </div>
        <?php

    }
}
