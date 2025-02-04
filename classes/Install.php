<?php

if (!defined('ABSPATH'))
    exit;

/**
 * @version		1.0.0
 * @package		smart-sell-ranker
 */

class Install {

	public function __construct() {
		if ( ! get_option( 'SmartSellRanker_cron_start' ) ) {
            $this->ss_ranker_start_sale_cron_job();
        }
        if ( ! get_option( 'SmartSellRanker_setting_saved' ) ) {
            $this->ss_ranker_start_default_settings();
        }
	}

	/*
     * This function will start the cron job
     */
    public function ss_ranker_start_sale_cron_job() {
        wp_clear_scheduled_hook( 'smart_sell_ranker_cron_job' );
        wp_schedule_event( time(), 'daily', 'smart_sell_ranker_cron_job' );
        update_option( 'SmartSellRanker_cron_start', 1 );
    }

    public function ss_ranker_start_default_settings() {
        $ss_ranker_settings = array(
            'unassign_prev_products' => array('unassign_prev_products'),
            'get_items_from_last_date' => array( 
                'value' => 6,
                'label' => 'Last Six Month',
                'index' => 5,
            ),
            'order_status_to_include' => array(
                array(
                    'value' => 'wc-completed',
                    'label' => 'Completed',
                    'index' => 3
                ),
                array(
                    'value' => 'wc-processing',
                    'label' => 'Processing',
                    'index' => 1
                )
            ),
        );

        if ( ! get_option( 'ss_ranker_general_tab_settings' ) ) {
            update_option( 'ss_ranker_general_tab_settings', $ss_ranker_settings );
            update_option( 'SmartSellRanker_setting_saved', 1 ); 
        }
    }
}