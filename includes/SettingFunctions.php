<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'ss_ranker_admin_tabs' ) ) {
    function ss_ranker_admin_tabs() {
        $default_massages = ss_ranker_default_massages();
        //all category
        $args_cat = array( 'taxonomy'   => 'product_cat', 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => false  );
		$terms = get_terms( $args_cat );
        $all_product_cat = array();
		if ( $terms && empty( $terms->errors ) ) {
			foreach ( $terms as $term) {
				if ($term) {
					$all_product_cat[] = array(
						'value' => $term->term_id,
						'label' => $term->name,
						'key'   => $term->term_id,
					);
				}
			}
		}
        //all order status
        $order_statuses = wc_get_order_statuses();
        $statuses = array();
        foreach ( $order_statuses as $key => $status ) {
            $statuses[] = array(
                'value' => $key,
                'label' => $status,
                'key'   => $key,
            );
        }

        $periods = array(
            1   => __( 'One', 'smart-sell-ranker' ),
            2   => __( 'Two', 'smart-sell-ranker' ),
            3   => __( 'Three', 'smart-sell-ranker' ),
            4   => __( 'Four', 'smart-sell-ranker' ), 
            5   => __( 'Five', 'smart-sell-ranker' ),
            6   => __( 'Six', 'smart-sell-ranker' ),
            7   => __( 'Seven', 'smart-sell-ranker' ),
            8   => __( 'Eight', 'smart-sell-ranker' ),
            9   => __( 'Nine', 'smart-sell-ranker' ),
            10  => __( 'Ten', 'smart-sell-ranker' ),
            11  => __( 'Eleven', 'smart-sell-ranker' ),
            12  => __( 'Twelve', 'smart-sell-ranker' )
        );
        //all order periods
        foreach ( $periods as $key => $value ) {
            $order_periods[] = array(
                'value' => $key,
                /* translators: %s: month name */
                'label' => sprintf( __( 'Last %s Month', 'smart-sell-ranker' ), $value ), 
                'key'   => $key,
            );
        } 

        $s_ranker_settings_page_endpoint = apply_filters( 'ss_ranker_by_sale_endpoint_fields_before_value', array(
            'general' => array(
                'tablabel'        => __( 'General', 'smart-sell-ranker' ),
                'apiurl'          => 'save_admin_settings',
                'description'     => __( 'Configure Basic SmartSell Ranker Settings. ', 'smart-sell-ranker' ),
                'icon'            => 'dashicons dashicons-admin-generic',
                'submenu'         => 'settings',
                'modulename'      => [
                    [
                        'key'       => 'top_sale_category',
                        'type'      => 'select',
                        'label'     => __( 'Choose a Category as Top Sale', 'smart-sell-ranker' ),
                        'desc'      => __( 'Select a Category to Assign All Top Salling Products', 'smart-sell-ranker' ),
                        'placeholder'=> __( 'Choose options', 'smart-sell-ranker' ),
                        'options' => $all_product_cat,
                        'database_value' => '',
                    ],
                    [
                        'key'        => 'get_items_from_last_date',
                        'type'       => 'select',
                        'label'      => __( 'Choose Previous Ordering Periods', 'smart-sell-ranker' ),
                        'desc'       => __( 'Choose Ordering Periods for Item Searches and Sales Count', 'smart-sell-ranker' ),
                        'placeholder'=> __( 'Choose options', 'smart-sell-ranker' ),
                        'options'    => $order_periods,
                        'database_value' => '',
                    ],
                	[
                        'key'       => 'unassign_prev_products',
                        'label'     => __("Unassign Previous Top Salling Products", 'smart-sell-ranker'),
                        'class'     => 's_ranker-toggle-checkbox',
                        'type'      => 'checkbox',
                        'options'   => array(
                            array(
                                'key'   => "unassign_prev_products",
                                'label' => __( 'Unassign Previous Top Salling Products From Selected Catagory', 'smart-sell-ranker' ),
                                'value' => "unassign_prev_products"
                            ),
                        ),
                        'database_value' => array(),
                    ],
                    [
                        'key'               => 'max_top_sale_products',
                        'type'              => 'number',
                        'class'             => 's_ranker-setting-wpeditor-class',
                        'depend_checkbox'   => 'unassign_prev_products',
                        'label'             => __( 'Max Assign Products', 'smart-sell-ranker' ),
                        'desc'              => __( 'Maximum Products to be Assign in Selected Category.(Default: Not Set)', 'smart-sell-ranker' ),
                        'database_value'    => '',
                    ],
                    [
                        'key'      => 'minimum_order_of_product',
                        'label'    => __( "Select Minimum Order Quantity of a Product", 'smart-sell-ranker' ),
                        'desc'     => __( 'Minimum Quantity Required to Assign a Product in the Top Salling Category.(Default: 0)', 'smart-sell-ranker' ),
                        'class'    => 's_ranker-toggle-checkbox',
                        'type'     => 'number',
                        'database_value' => '',
                    ],
                    [
                        'key'        => 'order_status_to_include',
                        'type'       => 'multi-select',
                        'label'      => __( 'Select Order Status', 'smart-sell-ranker' ),
                        'desc'       => __( 'Choose Order Statuses to Include in Order Item Count', 'smart-sell-ranker' ),
                        'placeholder'=> __( 'Choose options', 'smart-sell-ranker' ),
                        'options'    => $statuses,
                        'database_value' => '',
                    ],
                ]
            ),
            'frontend' => array(
                'tablabel'        => __( 'Frontend', 'smart-sell-ranker' ),
                'apiurl'          => 'save_admin_settings',
                'description'     => __( 'Frontend Settings', 'smart-sell-ranker' ),
                'icon'            => 'dashicons dashicons-format-aside',
                'submenu'         => 'settings',
                'modulename'      =>  [
                    [
                        'key'       => 'enable_show_order_count',
                        'label'     => __( "Show Order Count in Single Product Page", 'smart-sell-ranker' ),
                        'class'     => 's_ranker-toggle-checkbox',
                        'type'      => 'checkbox',
                        'options'   => array(
                            array(
                                'key'   => "enable_show_order_count",
                                'label' => __( 'Show Order Count in Single Product Page', 'smart-sell-ranker' ),
                                'value' => "enable_show_order_count"
                            ),
                        ),
                        'database_value' => array(),
                    ],
                    [
                        'key'               => 'order_in_days',
                        'label'             => __( "Specify the Duration for Order Item Sale (Days)", 'smart-sell-ranker' ),
                        'desc'              => __( 'Specify the Duration for Order Item Sale To Show in Single Product Page.(Default: 7 days)', 'smart-sell-ranker' ),
                        'depend_checkbox'   => 'enable_show_order_count',
                        'class'             => 's_ranker-toggle-checkbox',
                        'type'              => 'number',
                        'database_value'    => '',
                    ],
                    [
                        'key'               => 'shown_order_count_text',
                        'type'              => 'textarea',
                        'class'             => 's_ranker-setting-wpeditor-class',
                        'depend_checkbox'   => 'enable_show_order_count',
                        'label'             => __( 'Order Count Text', 'smart-sell-ranker' ),
                        'placeholder'       => $default_massages['shown_order_count_text'],
                        // translators: %order_count% is replaced with the number of orders, and %day_count% is replaced with the number of days.
                        'desc'              => __( 'Customize the Massage to Show Recent Orders. Note: Use %order_count% as number of orders and %day_count% as days.', 'smart-sell-ranker' ),
                        'database_value'    => '',
                    ],
                    [
                        'key'       => 'avialable_shortcodes',
                        'type'      => 'table',
                        'label'     => __( 'Available Shortcodes', 'smart-sell-ranker' ),
                        'label_options' =>  array(
                            __( 'Shortcodes', 'smart-sell-ranker' ),
                            __( 'Description', 'smart-sell-ranker' ),
                        ),
                        'options' => array(
                            array(
                                'variable'=> "<code>[ss_ranker_products]</code>",
                                'description'=> __( 'Show all top Selling products in a page', 'smart-sell-ranker' ),
                            ),
                        ),
                        'database_value' => '',
                    ],
                                        
                ]
            )
        ));

        if ( ! empty( $s_ranker_settings_page_endpoint ) ) {
            foreach ( $s_ranker_settings_page_endpoint as $settings_key => $settings_value ) {
                if ( isset( $settings_value['modulename'] ) && !empty( $settings_value['modulename'] ) ) {
                    foreach ( $settings_value['modulename'] as $inter_key => $inter_value ) {
                        $change_settings_key = str_replace( "-", "_", $settings_key );
                        $option_name = 'ss_ranker_'.$change_settings_key.'_tab_settings';
                        $database_value = get_option($option_name) ? get_option($option_name) : array();
                        if ( ! empty( $database_value ) ) {
                            if ( isset( $inter_value['key'] ) && array_key_exists( $inter_value['key'], $database_value ) ) {
                                if ( empty( $inter_value['database_value'] ) ) {
                                   $s_ranker_settings_page_endpoint[$settings_key]['modulename'][$inter_key]['database_value'] = $database_value[$inter_value['key']];
                                }
                            }
                        }
                    }
                }
            }
        }

        $ss_ranker_backend_tab_list = apply_filters( 'ss_ranker_admin_tab_list', array(
            'top-sale-settings' => $s_ranker_settings_page_endpoint,
        ) );
        
        return $ss_ranker_backend_tab_list;
    }
}

if ( ! function_exists('ss_ranker_default_massages' ) ) {
    function ss_ranker_default_massages() {
        $default_massages = array(
            // translators: %order_count% is the number of orders, and %day_count% is the number of days.
            'shown_order_count_text' => __( '%order_count% bought in past %day_count% days.', 'smart-sell-ranker' ),
        );
        return $default_massages;
    }
}