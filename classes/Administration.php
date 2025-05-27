<?php

if (!defined('ABSPATH'))
    exit;

/**
 * @version		1.0.0
 * @package		smart-sell-ranker
 */

class SSKR_Admin {

	public $settings;
	
	public function __construct() {
		// load menu
        $this->load_class( 'Settings' );
        $this->settings = new SSKR_Settings();

        // load Script
        add_action( 'admin_enqueue_scripts', [ $this, 'ss_ranker_enqueue_admin_script' ] );
	}

    /**
     * Admin Scripts
     */
    public function ss_ranker_enqueue_admin_script() {
        global $SmartSellRanker;
        if ( get_current_screen()->id == 'toplevel_page_smart-sale-ranker-setting' ) {
            wp_enqueue_script( 'smart-sell-ranker-script', $SmartSellRanker->plugin_url . 'build/index.js', [ 'wp-element', 'wp-i18n', 'react-jsx-runtime' ], $SmartSellRanker->version, true );
            wp_localize_script( 'smart-sell-ranker-script', 'SSRLocalizer', apply_filters( 'smart_sell_ranker_admin_default', 
                [
                    'apiUrl'        => home_url('/wp-json'),
                    'nonce'         => wp_create_nonce('wp_rest'),
                ]
            ) );
            wp_enqueue_style( 'smart-sell-ranker-style', $SmartSellRanker->plugin_url . 'build/index.css', array(),$SmartSellRanker->version);
        }
    }

	public function load_class( $class_name = '' ) {
        global $SmartSellRanker;
        if ( '' != $class_name ) {
            require_once( $SmartSellRanker->plugin_path . 'admin/' . esc_attr( $class_name ) . '.php' );
        }
    }
}