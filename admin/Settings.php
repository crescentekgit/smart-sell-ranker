<?php

if (!defined('ABSPATH'))
    exit;

/**
 * @version    1.0.0
 * @package    smart-sell-ranker
 */

class SSKR_Settings {

   public function __construct() {
      // Admin menu
      add_action( 'admin_menu', [ $this, 'add_settings_page' ], 100 );
   }

   /**
   * Add options page
   */
   public function add_settings_page() {

      add_menu_page(
         __( 'SmartSell Ranker', 'smart-sell-ranker' ),
         __( 'SmartSell Ranker', 'smart-sell-ranker' ),
         'manage_options',
         'smart-sale-ranker-setting',
         [ $this, 'create_smart_sale_ranker_settings' ],
         'dashicons-paperclip', 
         59
      );

      add_submenu_page(
         'smart-sale-ranker-setting',                                   // parent slug
         __( 'Settings', 'smart-sell-ranker' ),                         // page title
         __( 'Settings', 'smart-sell-ranker' ),                         // menu title
         'manage_options',                                                // capability
         'smart-sale-ranker-setting#&tab=settings&subtab=general',      // callback
         '__return_null'                                                  // position
      );

      remove_submenu_page( 'smart-sale-ranker-setting', 'smart-sale-ranker-setting' );
   }

   /**
   * Options page callback
   */
   public function create_smart_sale_ranker_settings() {
      echo '<div id="smart-sale-ranker-admin"></div>';
   }
}