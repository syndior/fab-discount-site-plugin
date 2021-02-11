<?php
/**
 * Plugin Name:       Fab Discount Core Site Functionality
 * Plugin URI:        https://kristall.io/
 * Description:       Handles the Fab Discount site core functionality
 * Version:           1.0.0
 * Requires at least: 5.5
 * Requires PHP:      7.2
 * Author:            Kristall Studios
 * Author URI:        https://kristall.io/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

 /**
 * Direct access protection
 */
defined('ABSPATH') or die('This path is not accessible');

define( 'fdscf_url', plugin_dir_url( __FILE__ ) );
define( 'fdscf_path', plugin_dir_path( __FILE__ ) );
define( 'fdscf_plugin', plugin_basename( __FILE__ ) );

global $wpdb;
$fdscf_vouchers_db_table_name = ( $wpdb->prefix . "fdscf_vouchers" );
define( 'fdscf_vouchers_db_table_name', $fdscf_vouchers_db_table_name );

$fdscf_transactions_db_table_name = ( $wpdb->prefix . "fdscf_user_transactions" );
define( 'fdscf_transactions_db_table_name', $fdscf_transactions_db_table_name );

if( !class_exists( 'FD_CORE_PLUGIN_CLASS' ) ){

    class FD_CORE_PLUGIN_CLASS
    {
        public function __construct()
        {
            if( is_admin() ){
                require_once ( fdscf_path . 'includes/admin/class-admin-pages.php' );
            }
            require_once ( fdscf_path . 'includes/class-fd-functions.php' );
            require_once ( fdscf_path . 'includes/base/class-activate.php' );
            require_once ( fdscf_path . 'includes/base/class-deactivate.php' );
            require_once ( fdscf_path . 'includes/base/class-enqueue.php' );
            require_once ( fdscf_path . 'includes/base/class-settings-links.php' );
            require_once ( fdscf_path . 'includes/base/class-wp-cron.php' );
            require_once ( fdscf_path . 'includes/user/class-user-controller.php' );
            require_once ( fdscf_path . 'includes/dokan/class-dokan-controller.php' );
            require_once ( fdscf_path . 'includes/refunds/class-refunds-controller.php' );
            require_once ( fdscf_path . 'includes/vouchers/class-fd-voucher.php' );
            require_once ( fdscf_path . 'includes/vouchers/class-vouchers-controller.php' );
            require_once ( fdscf_path . 'includes/vendor/class-vendor-controller.php' );
            require_once ( fdscf_path . 'includes/vendor/class-vendor-product-controller.php' );
            

            /**
             * Loads WC script classes after plugins have loaded
             */
            add_action( 'plugins_loaded', array( $this, 'load_wc_class_controllers' ) );


            /**
             * PLugin activation hook
             */
            register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );

            
            /**
             * PLugin deactivation hook
             */
            register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivation' ) );
        }
        
        public function load_wc_class_controllers()
        {
            require_once ( fdscf_path . 'includes/woocommerce/class-wc-custom-product-type.php' );
            require_once ( fdscf_path . 'includes/woocommerce/class-wc-controller.php' );

        }

        public function plugin_activation()
        {
            if( class_exists('FD_Activate') ){
                FD_Activate::activate();
            }
        }

        public function plugin_deactivation()
        {
            if( class_exists('FD_Deactivate') ){
                FD_Deactivate::deactivate();
            }
        }
    }//class

    new FD_CORE_PLUGIN_CLASS();
}