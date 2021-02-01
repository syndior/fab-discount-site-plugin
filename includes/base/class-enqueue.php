<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Enqueue
{
        public function __construct() 
        {
                add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
        }
	
        public function enqueue() 
        {
                //plugin styles
                wp_enqueue_style( 'fdscf-styles', fdscf_url . 'assets/css/main-styles.css', array(), '1.0.0');
                        
                //plugin scripts
                wp_enqueue_script( 'fdscf-script', fdscf_url . 'assets/js/main-scripts.js',  array('jquery'),'1.0.0',true);
        }
}

new FD_Enqueue();