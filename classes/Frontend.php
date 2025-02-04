<?php

if (!defined('ABSPATH'))
    exit;

/**
 * @version		1.0.0
 * @package		smart-sell-ranker
 */

class Frontend {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_plugin_styles' ] );
		
		if ( ss_ranker_get_plugin_settings( 'enable_show_order_count' ) ) {
			add_action( 'woocommerce_single_product_summary', [ $this, 'ss_ranker_product_sold_count' ], 15 );
		}
	}

	public function enqueue_plugin_styles() {
		global $SmartSellRanker;
		wp_enqueue_style( 'ss-ranker-shortcode-style', $SmartSellRanker->plugin_url . 'assets/css/frontend.css', array(), $SmartSellRanker->version );
	}
  
	public function ss_ranker_product_sold_count() {
		global $product;
		$category_id = ss_ranker_get_plugin_settings( 'top_sale_category' ) ? ss_ranker_get_plugin_settings( 'top_sale_category' )['value'] : '';
		if ( $category_id ) {
            $default_cat = get_term( $category_id, 'product_cat' );
            $cat_slug = $default_cat && !is_wp_error($default_cat) ? $default_cat->slug : '';
			if ( has_term( $cat_slug, 'product_cat' ) ) {
				$days = ss_ranker_get_plugin_settings( 'order_in_days' , '7' );
				$all_orders = wc_get_orders(
					array(
						'limit' => -1,
						'status' => wc_get_is_paid_statuses(),
						'date_after' => gmdate( 'Y-m-d', strtotime( '-'.$days.' days' ) ),
						'return' => 'ids',
					)
				);
				$count = 0;
				foreach ( $all_orders as $all_order ) {
					$order = wc_get_order( $all_order );
					$items = $order->get_items();
					foreach ( $items as $item ) {
						$product_id = $item->get_product_id();
						if ( $product_id == $product->get_id() ) {
							$count = $count + absint( $item['qty'] ); 
						}
					}
				}
				$minimum_order = ss_ranker_get_plugin_settings( 'minimum_order_of_product', 0 );
					
				if ( $count >= $minimum_order ) {
					$default_massages = ss_ranker_default_massages();
					$row_massage = ss_ranker_get_plugin_settings( 'shown_order_count_text' , $default_massages['shown_order_count_text'] );
					$shown_order_text = str_replace( "%day_count%", $days, $row_massage );
					$shown_order_text = str_replace( "%order_count%", $count, $shown_order_text );
					echo "<p>" . esc_html($shown_order_text) . "</p>";
				}
			}
		}
	}
}