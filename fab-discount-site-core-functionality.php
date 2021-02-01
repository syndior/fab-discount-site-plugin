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

if( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ){
    require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

use Inc\Base\Activate;
use Inc\Base\Deactivate;
use Inc\Base\Enqueue;

if ( class_exists( 'Inc\\Init' ) ) {
    Inc\Init::register_services();
}