<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'ss_ranker_woocommerce_inactive_notice' ) ) {
    function ss_ranker_woocommerce_inactive_notice() {
        ?>
        <div id="message" class="error">
            <p><?php 
                printf(
                    /* translators: %1$s: <strong> */
                    /* translators: %2$s: </strong> */
                    /* translators: %3$s: <a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/"> */
                    /* translators: %4$s: </a> */
                    /* translators: %5$s: <a href="' . admin_url( 'plugins.php' ) . '"> */
                    /* translators: %6$s: &nbsp;&raquo;</a> */
                    esc_html__(
                        '%1$sSmartSell Ranker is inactive.%2$s The %3$sWooCommerce plugin%4$s must be active for the SmartSell Ranker to work. Please %5$sinstall & activate WooCommerce%6$s',
                        'smart-sell-ranker'
                    ),
                    '<strong>',
                    '</strong>',
                    '<a target="_blank" href="' . esc_url( 'http://wordpress.org/extend/plugins/woocommerce/' ) . '">',
                    '</a>',
                    '<a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">',
                    '&nbsp;&raquo;</a>'
                );

                ?>
            </p>
        </div>
        <?php
    }
}

if ( ! function_exists( 'ss_ranker_get_plugin_settings' ) ) {
    function ss_ranker_get_plugin_settings( $key = '', $default = false ) {
        $ss_ranker_plugin_settings = array();
        $all_options = apply_filters( 'ss_ranker_all_admin_options', array(
            'ss_ranker_frontend_tab_settings',
            'ss_ranker_general_tab_settings',
            )
        );
        foreach ( $all_options as $option_name ) {
            if ( is_array( get_option( $option_name, array() ) ) ) {
                $ss_ranker_plugin_settings = array_merge( $ss_ranker_plugin_settings, get_option( $option_name, array() ) );
            }
        }
        if ( empty( $key ) ) {
            return $default;
        }
        if ( ! isset( $ss_ranker_plugin_settings[$key] ) || empty( $ss_ranker_plugin_settings[$key] ) ) {
            return $default;
        }
        return $ss_ranker_plugin_settings[$key];
    }
}

if ( ! function_exists( 'ss_ranker_cron_function' ) ) {
    function ss_ranker_cron_function() {
        $product_id = $dufault_limit = '';
        $category_id = ss_ranker_get_plugin_settings( 'top_sale_category' ) ? absint( ss_ranker_get_plugin_settings( 'top_sale_category' )['value'] ) : '';
        if ( $category_id ) {
            $category = get_term( $category_id, 'product_cat' );
            $cat_slug = $category && !is_wp_error( $category ) ? sanitize_title( $category->slug ) : '';

            $selected_month = ss_ranker_get_plugin_settings( 'get_items_from_last_date' ) ? absint( ss_ranker_get_plugin_settings( 'get_items_from_last_date' )['value'] ) : 1;
            if ( ss_ranker_get_plugin_settings( 'order_status_to_include' ) ) {
                $status = wp_list_pluck( array_filter( ss_ranker_get_plugin_settings( 'order_status_to_include' ) ), 'value' );
                $order_statuses_sql = array_values( $status );
            } else {
                $order_statuses_sql = array('wc-processing', 'wc-completed');
            }

            // Create a DateTime object for the current date and time
            $currentDate = new DateTime();
            $MonthsAgo = $currentDate->modify('-'.$selected_month.' months');
            $formattedDate = sanitize_text_field( $MonthsAgo->format('Y-m-d') );

            if ( ss_ranker_get_plugin_settings( 'unassign_prev_products' ) ) {
                $dufault_limit = ss_ranker_get_plugin_settings( 'max_top_sale_products' ) ? absint( ss_ranker_get_plugin_settings( 'max_top_sale_products' ) ) : '';
                ss_ranker_unassign_old_product_cat( $category_id );
            }

            // Get all products count
            $orders = wc_get_orders( array(
                'type'          => 'shop_order',
                'limit'         => -1,
                'status'        => $order_statuses_sql,
                'date_after'    => $formattedDate,
            ) );

            $order_item_counts = array();

            // Loop through each order
            foreach ( $orders as $order ) {
                // Get order items
                $items = $order->get_items();
                
                // Loop through each order item
                foreach ( $items as $item ) {
                    $product_id = absint( $item->get_product_id() );
                    
                    // Increase the count for this product
                    if ( isset( $order_item_counts[$product_id] ) ) {
                        $order_item_counts[$product_id] += absint( $item->get_quantity() );
                    } else {
                        $order_item_counts[$product_id] = absint( $item->get_quantity() );
                    }
                }
            }

            if ( $order_item_counts ) {
                // Sort the product array by count
                arsort( $order_item_counts );
                if ( $dufault_limit && !empty( $dufault_limit ) ) {
                    $order_item_counts = array_slice( $order_item_counts, 0, $dufault_limit, true );
                }
                // Output the counts
                foreach ( $order_item_counts as $product_id => $count ) {
                    $minimum_order = absint( ss_ranker_get_plugin_settings( 'minimum_order_of_product', 0 ) );
                    if ( $count >= $minimum_order ) {
                        wp_set_object_terms( $product_id, sanitize_title( $cat_slug ), 'product_cat', true );
                        update_post_meta( $product_id, 'ss_ranker_sales_count', absint( $count ) );
                    }
                }
            }
        }
    }
}

if ( ! function_exists( 'ss_ranker_unassign_old_product_cat' ) ) {
    function ss_ranker_unassign_old_product_cat( $cat_id ) {
        $attachments = array();
        //get already assign products of selected product catagory
        $product_args = array(
            'numberposts' => -1,
            'post_status' => array( 'publish', 'pending', 'private', 'draft' ),
            'post_type' => array( 'product', 'product_variation' ),
            'orderby' => 'ID',
            'suppress_filters' => false,
            'order' => 'ASC',
            'offset' => 0,
            'fields' => 'ids',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'id',
                    'terms' => array( $cat_id ),
                    'operator' => 'IN',
                )
            )
        );
        $product_ids = get_posts( $product_args );
        $email = WC()->mailer()->emails['WC_Admin_Email_Cron_Update'];
        
        $args = array(
            'filename' => gmdate('d-m-Y').'-unassign-products.csv',
            'action' => 'temp',
        );
        
        if ( isset( $product_ids ) && ! empty( $product_ids ) ) {
            $csv = ss_ranker_export_assign_products_data( $product_ids, $args, 'Removed' );
            if ( $csv )
            $attachments[] = $csv;
            if ( $email->trigger( $attachments ) ) {
                if ( file_exists( $csv ) ) {
                    wp_delete_file($csv);
                }
            } else {
                if ( file_exists( $csv ) ) {
                    wp_delete_file( $csv );
                }
            }
            foreach ( $product_ids as $product_id ) {
               delete_post_meta( $product_id, 'ss_ranker_sales_count' );
               wp_remove_object_terms( $product_id, $cat_id, 'product_cat' ); 
            }
        }
    }
}

if ( ! function_exists( 'ss_ranker_export_assign_products_data' ) ) {
    function ss_ranker_export_assign_products_data( $products, $args, $status = null ) {

        error_log('CSV function call');

        // Load WP_Filesystem
        if ( ! function_exists( 'request_filesystem_credentials' ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        global $wp_filesystem;
        if ( ! WP_Filesystem() ) {
            return false; // Stop if WP_Filesystem is not initialized
        }

        $index = 0;
        if ( ! empty( $products ) ) {
            $export_data_index = array();

            // Default arguments
            $default = array(
                'filename' => 'unassign-list.csv',
                'iostream' => 'php://output',
                'buffer'   => 'w',
                'action'   => 'download',
            );
            $args = wp_parse_args( $args, $default );

            $filename = $args['filename'];
            $file_path = $args['action'] === 'temp' ? sys_get_temp_dir() . '/' . $filename : false;

            // Set headers for download action
            if ( $args['action'] === 'download' ) {
                header( "Pragma: public" );
                header( "Expires: 0" );
                header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
                header( "Content-Type: application/octet-stream" );
                header( "Content-Disposition: attachment; filename={$filename}" );
                header( "Content-Transfer-Encoding: binary" );
            }

            // CSV headers
            $headers = array(
                'product_id'   => __( 'Product Id', 'smart-sell-ranker' ),
                'product_name' => __( 'Product Name', 'smart-sell-ranker' ),
                'product_sku'  => __( 'Product SKU', 'smart-sell-ranker' ),
                'sales_count'  => __( 'Sales Count', 'smart-sell-ranker' ),
                'status'       => __( 'Status', 'smart-sell-ranker' ),
            );

            // Escape headers for CSV
            $escaped_headers = array_map( 'esc_html', $headers );
            $csv_data = '"' . implode( '","', $escaped_headers ) . '"' . "\n";

            // Prepare product data
            foreach ( $products as $product_id ) {
                $product = wc_get_product( $product_id );
                $sales_count = get_post_meta( $product_id, 'ss_ranker_sales_count', true );

                // Escape individual product data for CSV
                $data_row = array(
                    'product_id'   => esc_html( $product_id ),
                    'product_name' => esc_html( $product->get_name() ),
                    'product_sku'  => esc_html( $product->get_sku() ),
                    'sales_count'  => esc_html( $sales_count ),
                    'status'       => esc_html( $status ? $status : '-' ),
                );

                $csv_data .= '"' . implode( '","', $data_row ) . '"' . "\n";
            }

            error_log("CSV Data: " . print_r($csv_data, true));

            // Write to file using WP_Filesystem
            if ( $args['action'] === 'temp' && $file_path ) {
                $wp_filesystem->put_contents( $file_path, $csv_data, FS_CHMOD_FILE );
                error_log('file path ' . $file_path);
                return $file_path; // Return the temp file path
            } else {
                echo $csv_data; // Output CSV for download
                error_log("CSV Data: " . print_r($csv_data, true));
                die();
            }
        }

        return false;
    }
}

if ( ! function_exists( 'ss_ranker_get_settings_value' ) ) {

    /**
     * get settings value by key
     * @return string
     */
    function ss_ranker_get_settings_value( $key = array(), $default = 'false' ) {
        if ( empty( $key ) ) {
            return $default;
        }
        if ( is_array( $key ) && isset( $key['value'] ) ) {
            return $key['value'];
        }
        return $default;
    }

}

