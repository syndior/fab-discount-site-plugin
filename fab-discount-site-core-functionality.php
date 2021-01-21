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

if( !class_exists('FD_SITE_CORE_FUNCTIONALITY') ){

    class FD_SITE_CORE_FUNCTIONALITY{

        public function __construct()
        {
            /**
             * Include js and css files
             */
            add_action( 'wp_enqueue_scripts', array($this, 'fdscf_includes_resources') );
        }

        public function fdscf_includes_resources()
        {
            //plugin styles
            wp_enqueue_style( 'fdscf-styles', plugins_url( 'assets/css/main-styles.css', __FILE__ ),array(), '1.0.0');
            
            //plugin scripts
            wp_enqueue_script( 'fdscf-script', plugins_url( 'assets/js/main-scripts.js', __FILE__ ), array('jquery'),'1.0.0',true);
        }

    }//class end

}//if end

/**
 * Main Plugin instance
 */
new FD_SITE_CORE_FUNCTIONALITY();