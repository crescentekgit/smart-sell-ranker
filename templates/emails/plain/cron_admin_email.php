<?php

if (!defined('ABSPATH'))
    exit;

/**
 * @author 	
 * @version   1.0.0
 */


 echo esc_html__( 'Cron report SmartSell Ranker', 'smart-sell-ranker' ) . "\n\n";

echo sprintf( esc_html__( "Hi there!", 'smart-sell-ranker' ) ) . "\n\n";

echo "\n****************************************************\n\n";

echo sprintf( esc_html__( "This is to inform you that our system has automatically assigned products to categories based on predefined criteria.", 'smart-sell-ranker' ) ) . "\n\n";

echo sprintf( esc_html__( "Kindly download the CSV of all previously unassign products.", 'smart-sell-ranker' ) ) . "\n\n";

echo "\n****************************************************\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );