<?php

namespace StorePlugin\WcProductSearch;

use StorePlugin\WcProductSearch\Trait\Singleton;

/**
 * Populate ajax search database
 *
 * @package StorePlugin\WcProductSearch
 */
final class AjaxSearchDatabase {
    // Singleton trait
    use Singleton;

    /**
     * Truncate the search table
     *
     * @return void
     */
    public function truncate() {
        global $wpdb;
        $wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}woo_ajax_search" );
    }

    /**
     * Check whether the search table is empty
     *
     * @return bool
     */
    protected function is_search_table_populated_with_data() {
        global $wpdb;
        return $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}woo_ajax_search" );
    }

    /**
     * Query all search contents from wp_posts table.
     *
     * @return array
     */
    public function get_all_search_queries() {
        global $wpdb;
        return $wpdb->get_results(
            "SELECT DISTINCT {$wpdb->posts}.ID, {$wpdb->posts}.post_title, {$wpdb->posts}.post_content, {$wpdb->posts}.post_excerpt
            FROM {$wpdb->posts}, {$wpdb->postmeta}
            WHERE {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
            AND {$wpdb->posts}.post_status = 'publish'
            AND {$wpdb->posts}.post_type = 'product'
            ORDER BY $wpdb->posts.post_date"
        );
    }

    /**
     * Insert search content into a new table
     *
     * @return void
     */
    public function insert_ajax_search_cols() {
        /**
         * DB Object
         *
         * @var \wpdb $wpdb
         */
        global $wpdb;
        $search_entries = $this->get_all_search_queries();

        if( ! $this->is_search_table_populated_with_data() && is_array( $search_entries ) ) {
            foreach( $search_entries as $search_entry ) {
                $wpdb->query( $wpdb->prepare(
                    "INSERT INTO {$wpdb->prefix}woo_ajax_search
                    (`post_id`, `post_title`, `post_content`, `post_excerpt`, `price`, `attachment`, `url`)
                    VALUES (%d, %s, %s, %s, %s, %s, %s)",
                    [
                        $search_entry->ID,
                        $search_entry->post_title,
                        $search_entry->post_content,
                        $search_entry->post_excerpt,
                        wc_price( get_post_meta( $search_entry->ID, '_price', true ) ),
                        wp_get_attachment_image( get_post_thumbnail_id( $search_entry->ID ) ),
                        get_permalink( $search_entry->ID ),
                    ]
                ));
            }
        }
    }

    /**
     * Save a product data to search table on submit
     *
     * @param int $post_id
     * @param WP_Post $post
     * @param bool $update
     * @return void
     */
    public function update_search_table_on_product_submit( $post_id, $post, $update ) {
        // Check user's capability and prevent autosaving
        if( ! current_user_can( 'edit_page', $post_id ) && defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        global $wpdb;
        $title      = get_the_title($post_id);
        $content    = get_post_field( 'post_content', $post_id, 'raw' );
        $excerpt    = get_the_excerpt( $post_id );
        $price      = wc_price( get_post_meta( $post_id, '_price', true ) );
        $attachment = wp_get_attachment_image( get_post_thumbnail_id( $post_id ) );
        $link       = get_permalink( $post_id );

        if( $update ) {
            $wpdb->update( "{$wpdb->prefix}woo_ajax_search",
            [
                'post_title'    => $title,
                'post_content'  => $content,
                'post_excerpt'  => $excerpt,
                'price'         => $price,
                'attachment'    => $attachment,
                'url'           => $link,
            ],
            [
                'post_id'   => $post_id
            ]);

            return;
        }

        // Insert new product data
        $wpdb->insert( "{$wpdb->prefix}woo_ajax_search",
            [
                'post_id'       => $post_id,
                'post_title'    => $title,
                'post_content'  => $content,
                'post_excerpt'  => $excerpt,
                'price'         => $price,
                'attachment'    => $attachment,
                'url'           => $link
            ]
        );
    }

    /**
     * Remove a product from search table when owner a will delete one
     *
     * @param int $post_id
     * @return void
     */
    public function delete_product_from_search_table( $post_id ) {
        if( ! get_post_type( $post_id ) === 'product' ) return;
        global $wpdb;
        $wpdb->delete( "{$wpdb->prefix}woo_ajax_search", [ 'post_id' => $post_id ] );
    }

    /**
     * Count total product entry
     *
     * @return int
     */
    public function total_page( $search_val, $product_per_list ) {
        global $wpdb;
        $total = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*)
                FROM {$wpdb->prefix}woo_ajax_search
                WHERE post_title
                LIKE %s",
               esc_sql( $wpdb->esc_like( $search_val ) )
            )
        );

        return ceil( $total / $product_per_list );
    }

    /**
     * Query from ajax search table
     *
     * @param string $src
     * @return array|object|null
     */
    public function search_ajax_table( $src, $offset, $product_per_list ) {
        global $wpdb;
        $search_query = esc_sql( $src );
        $offsets      = esc_sql( $offset );
        $product_list = esc_sql( $product_per_list );

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}woo_ajax_search
                WHERE post_title LIKE %s
                LIMIT %d, %d;",
                '%'.$wpdb->esc_like($search_query).'%',
                $offsets,
                $product_list
            ),
            ARRAY_A
        );

    }

    /**
     * Create table for ajax search plugin
     *
     * @return void
     */
    public function create_database() {
        global $wpdb;

        $ajax_search_tbl = $wpdb->query(
            $wpdb->prepare(
                "CREATE TABLE `{$wpdb->prefix}woo_ajax_search` (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `post_id` bigint(20) NOT NULL,
                    `post_title` text,
                    `post_content` longtext,
                    `post_excerpt` text,
                    `price` text,
                    `attachment` text,
                    `url` text,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `id` (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=%s;",
                esc_sql( $wpdb->charset )
            )
        );

        // Create search index
        $ajx_search_idx = $wpdb->query( "CREATE INDEX ajx_search_title_idx ON {$wpdb->prefix}woo_ajax_search(post_title);" );

        if( ! function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        dbDelta( [ $ajax_search_tbl, $ajx_search_idx ] );

    }

}
