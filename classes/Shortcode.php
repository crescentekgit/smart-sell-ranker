<?php

if (!defined('ABSPATH'))
    exit;

/**
 * @version		1.0.0
 * @package		smart-sell-ranker
 */

class Shortcode {

	public function __construct() {
		// Register the shortcode
		add_shortcode( 'ranker_products', [ $this, 'ss_ranker_products_shortcode' ] );
	}

    // Add custom WooCommerce product shortcode
    public function ss_ranker_products_shortcode($atts) {
        $category_id = ss_ranker_get_plugin_settings( 'top_sale_category' ) ? ss_ranker_get_plugin_settings( 'top_sale_category' )['value'] : '';
		if ( $category_id ) {

            if ( isset( $_GET['ss_ranker_orderby_nonce'] ) && ! wp_verify_nonce( wp_unslash( $_GET['ss_ranker_orderby_nonce'] ), 'smartsellranker_orderby' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                wp_send_json_error( 'bad_nonce' );
                wp_die();
            }

            $default_cat = get_term( $category_id, 'product_cat' );
            $cat_slug = $default_cat && ! is_wp_error( $default_cat ) ? $default_cat->slug : '';
            ob_start();

            // Shortcode attributes
            $atts = shortcode_atts(
                array(
                    'category' => $cat_slug,
                ),
                $atts,
                'ranker_products'
            );

            // Get products based on shortcode attributes
            $args = array(
                'post_type'      => 'product',
                'posts_per_page' => -1,
                'product_cat'    => $atts['category'],
                'orderby'        => 'date', // Default sorting order
                'order'          => 'desc',
            );

            // Check if sorting is specified in the URL
            $sort_order = isset( $_GET['sort'] ) ? sanitize_text_field( wp_unslash( $_GET['sort'] ) ) : '';

            // Modify query for sorting
            $meta_query = array();
            if ( $sort_order ) {
                switch ( $sort_order ) {
                    case 'price_low_high':
                        $meta_query[] = array(
                            'key'     => '_price',
                            'compare' => 'EXISTS', // Ensure the meta key exists
                        );
                        $args['orderby'] = array(
                            'meta_value_num' => 'ASC',
                            'date'           => 'DESC', // Secondary sort by date
                        );
                        break;

                    case 'price_high_low':
                        $meta_query[] = array(
                            'key'     => '_price',
                            'compare' => 'EXISTS',
                        );
                        $args['orderby'] = array(
                            'meta_value_num' => 'DESC',
                            'date'           => 'DESC',
                        );
                        break;

                    case 'total_sale':
                        $meta_query[] = array(
                            'key'     => 'ss_ranker_sales_count',
                            'compare' => 'EXISTS',
                        );
                        $args['orderby'] = array(
                            'meta_value_num' => 'DESC',
                            'date'           => 'DESC',
                        );
                        break;
                }
            }

            // Add meta_query to the main query args if it's not empty
            if ( ! empty( $meta_query ) ) {
                $args['meta_query'] = $meta_query;
            }

            // Run the query
            $products = new WP_Query( $args );

            if ( $products->have_posts() ) {
                echo '<div class="ss-ranker-product-list">';
                    // Sort dropdown
                    echo '<form id="ss-ranker-sort-form">';
                    wp_nonce_field( 'smartsellranker_orderby', 'ss_ranker_orderby_nonce' );
                        echo '<label for="sort-dropdown">' . esc_html_e( "Sort by:", "smart-sell-ranker" ) . '</label>';
                        echo '<select id="sort-dropdown" name="sort" onchange="this.form.submit()">';
                            echo '<option value="date" ' . selected( $sort_order, 'date', false ) . '>'. esc_attr( 'Latest', 'smart-sell-ranker' ).'</option>';
                            echo '<option value="total_sale"' . selected( $sort_order, 'total_sale', false ) . '>'. esc_attr( 'By Sale', 'smart-sell-ranker' ) .'</option>';
                            echo '<option value="price_low_high" ' . selected( $sort_order, 'price_low_high', false ) . '>'. esc_attr( 'Price: Low to High', 'smart-sell-ranker' ) .'</option>';
                            echo '<option value="price_high_low" ' . selected( $sort_order, 'price_high_low', false ) . '>'. esc_attr( 'Price: High to Low', 'smart-sell-ranker' ) .'</option>';
                        echo '</select>';
                    echo '</form>';

                // Display products
                if ( $products->have_posts() ) : ?>
        
                    <?php woocommerce_product_loop_start(); ?>
        
                    <?php while ( $products->have_posts() ) : $products->the_post(); ?>
        
                        <?php wc_get_template_part( 'content', 'product' ); ?>
        
                    <?php endwhile; // end of the loop. ?>
        
                    <?php woocommerce_product_loop_end(); ?>
        
                    <?php
        
                endif;    

                echo '</div>';
            } else {
                echo 'No products found';
            }

            wp_reset_postdata();

            return ob_get_clean();
        }
    }
}