<?php
/**
 * Plugin Name: SmartSell Ranker
 * Description: Assign the top-selling products within a specified time frame to a chosen category. 
 * Author: Outright Solutions
 * Version: 1.0.0
 * Requires at least: 6.3
 * Tested up to: 6.8
 * WC requires at least: 3.0
 * WC tested up to: 9.8.5
 * Author URI: https://outrightsolutions.net/
 * Text Domain: smart-sell-ranker
 * Domain Path: /languages/
 * License: GPLv3 or later
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'SSKR_Dependencies' ) )
	require_once 'classes/Dependencies.php';


require_once 'includes/CoreFunctions.php';
require_once 'includes/SettingFunctions.php';


if ( ! SSKR_Dependencies::woocommerce_plugin_active_check() ) {
  add_action( 'admin_notices', 'ss_ranker_woocommerce_inactive_notice' );
}

/**
 * Declare support for 'High-Performance order storage (COT)' in WooCommerce
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
  add_action(
    'before_woocommerce_init',
    function () {
      if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', plugin_basename( __FILE__ ), true );
      }
    }
  );
}

if ( ! class_exists( 'SmartSellRanker' ) && SSKR_Dependencies::woocommerce_plugin_active_check() ) {
    require_once('classes/SmartSellRanker.php');
    global $SmartSellRanker;
    $SmartSellRanker = new SmartSellRanker( __FILE__ );
    $GLOBALS['SmartSellRanker'] = $SmartSellRanker;
    // Activation Hooks
    register_activation_hook( __FILE__, [ 'SmartSellRanker', 'activate_SmartSellRanker' ] );
    // Deactivation Hooks
    register_deactivation_hook( __FILE__, [ 'SmartSellRanker', 'deactivate_SmartSellRanker' ] );
}
