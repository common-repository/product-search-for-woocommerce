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
class AjaxSearchShortCode {
    /**
     * Search form by shortcode
     *
     * @param string $atts
     * @param string $content
     * @return string|false
     */
    public function ajax_search_shortcode_cb( $atts, $content ) {
        $attr = shortcode_atts([
            'label'         => __( 'Search for:', 'product-search-for-woocommerce' ),
            'placeholder'   => __( 'Search', 'product-search-for-woocommerce' )
        ], $atts, 'product_search_for_woocommerce' );

        ob_start();
        ?>
        <div class="ajax-search-form-container">
            <form role="search" method="get" class="woo-ajax-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <label>
                    <span class="screen-reader-text"><?php echo esc_html_x( 'Search for:', 'label', 'product-search-for-woocommerce' ) ?></span>
                    <input type="search" class="search-field"
                        placeholder="<?php echo esc_attr( Settings::getData( 'ajax_search_placeholder' ) ? Settings::getData( 'ajax_search_placeholder' ) : __('Search products...','product-search-for-woocommerce') ) ?>"
                        value="<?php echo esc_attr( get_search_query() ) ?>" name="s"
                        title="<?php echo esc_attr_x( 'Search for:', 'label', 'product-search-for-woocommerce' ) ?>" />
                </label>
                <?php if( Settings::getData('ajax_search_submit_btn') ) : ?>
                <input type="submit" class="search-submit"
                    value="<?php echo esc_attr_x( 'Search', 'submit button', 'product-search-for-woocommerce' ) ?>" />
                <?php endif ?>
            </form>
            <div class="woo-ajax-search-results" id="woo_ajax_search"></div>
        </div>
        <?php
        return ob_get_clean();
    }

}
