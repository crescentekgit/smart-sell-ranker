<?php

if (!defined('ABSPATH'))
    exit;

/**
 * @version     1.0.0
 * @package     smart-sell-ranker
 * 
 */

class SmartSellRanker {
    public $token;
    public $plugin_url;
    public $plugin_path;
    public $version;
    public $template;
    public $admin;
    public $shortcode;
    public $frontend;
    private $file;
    private $plugin_basename;

    public function __construct( $file ) {

        $this->plugin_basename = plugin_basename( $file );

        $this->file = $file;
        $this->plugin_url = trailingslashit( plugins_url( '', $plugin = $file ) );
        $this->plugin_path = trailingslashit( dirname( $file ) );
        $this->token = 'smart-sell-ranker';
        $this->version = '1.0.0';

        add_action( 'init', [ &$this, 'init' ] );
        // Woocommerce Email structure
        add_filter( 'woocommerce_email_classes', [ &$this, 'smart_sell_ranker_mail' ] );
        add_action( 'smart_sell_ranker_cron_job', 'ss_ranker_cron_function' );
        add_action( 'widgets_init', [ $this, 'smart_sell_ranker_product_vendor_register_widgets' ] );
    }

    /**
     * initilize plugin on init
     */
    function init() {

        if ( is_admin() ) {
            $this->load_class( 'Admin' );
            $this->admin = new SSKR_Admin();
        }

        if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
            $this->load_class( 'Frontend' );
            $this->frontend = new SSKR_Frontend();

            $this->load_class( 'Shortcode' );
            $this->shortcode = new SSKR_Shortcode();
        }
        $this->load_class( 'Template' );
        $this->template = new SSKR_Template();

        if ( current_user_can( 'manage_options' ) ) {
            add_action( 'rest_api_init', [ $this, 'smart_sell_ranker_rest_routes' ] );
        }

        add_filter('plugin_action_links_' . $this->plugin_basename, array($this, 'setting_page_link'));
        add_filter( 'plugin_row_meta', array( $this, 'addon_plugin_links' ), 10, 2 );
    }

    public function setting_page_link($links) {
        // Add your custom links
        $settings_link = '<a href="' . admin_url('admin.php?page=smart-sale-ranker-setting') . '">' . __('Settings', 'smart-sell-ranker') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public function addon_plugin_links( $links, $file ) {
        if ( $file === $this->plugin_basename ) {
            $links[] = __( 'Made with Love ❤️', 'smart-sell-ranker' );
        }

        return $links;
    }


    public function load_class( $class_name = '' ) {
        if ( '' != $class_name ) {
            require_once ( esc_attr( $class_name ) . '.php' );
        }
    }

    /**
     * Sets a constant preventing some caching plugins from caching a page. Used on dynamic pages
     */
    public function nocache() {
        if ( ! defined( 'SSKR_DONOTCACHEPAGE' ) )
            define( "SSKR_DONOTCACHEPAGE", "true" );
            // WP Super Cache constant
    }

    /**
     * Install upon activation
     */
    public static function activate_SmartSellRanker() {
        global $SmartSellRanker;
        update_option( 'SmartSellRanker_installed', 1 );
        // Init install
        $SmartSellRanker->load_class( 'Install' );
        new SSKR_Install();
    }

    /**
     * Install upon deactivation
     *
     */
    public static function deactivate_SmartSellRanker() {
        delete_option( 'SmartSellRanker_installed' );
    }

    public function smart_sell_ranker_rest_routes() {
        register_rest_route( 'smart_sell_ranker/v1', '/fetch_admin_tabs', [
            'methods'   => WP_REST_Server::READABLE,
            'callback'  => array( $this, 'smart_sell_ranker_fetch_admin_tabs' ),
            'permission_callback' => array( $this, 'smart_sell_ranker_permission' ),
        ] );
        register_rest_route( 'smart_sell_ranker/v1', '/save_admin_settings', [
            'methods'   => WP_REST_Server::EDITABLE,
            'callback'  => array( $this, 'smart_sell_ranker_save_admin_settings' ),
            'permission_callback' => array( $this, 'smart_sell_ranker_permission' ),
        ] );
    }

    public function smart_sell_ranker_permission() {
        return current_user_can('manage_options');
    }
    
    public function smart_sell_ranker_fetch_admin_tabs() {
        $smart_sell_ranker_admin_tabs_data = ss_ranker_admin_tabs() ? ss_ranker_admin_tabs() : [];
        return rest_ensure_response( $smart_sell_ranker_admin_tabs_data );
    }

    public function smart_sell_ranker_save_admin_settings( $request ) {
        $all_details = [];
        $modulename = $request->get_param( 'modulename' );
        $modulename = str_replace( "-", "_", $modulename );
        $get_managements_data = $request->get_param( 'model' );
        $optionname = 'ss_ranker_'.$modulename.'_tab_settings';
        update_option( $optionname, $get_managements_data );
        do_action( 'smart_sell_ranker_settings_after_save', $modulename, $get_managements_data );
        $all_details['error'] = __( 'Settings Saved', 'smart-sell-ranker' );
        return $all_details;
        die;
    }

    public function smart_sell_ranker_mail( $emails ) {
        require_once( 'Emails/CronEmail.php' );
        $emails['WC_Admin_Email_Cron_Update'] = new SSKR_CronEmailUpdate();
        return $emails;
    }

    /**
     * Add vendor widgets
     */
    public function smart_sell_ranker_product_vendor_register_widgets() {
        require_once( 'Widgets/SmartSellRankerWidget.php' );
        register_widget( 'SmartSellRanker_Widget_Products' );
    }
}