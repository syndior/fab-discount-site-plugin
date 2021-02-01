<?php

namespace Inc\Base;
use \Inc\Base\BaseController;

class Enqueue extends BaseController
{
    public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}
	
	public function enqueue() {
        
        //plugin styles
        wp_enqueue_style( 'fdscf-styles', $this->plugin_url . 'assets/css/main-styles.css', array(), '1.0.0');
            
        //plugin scripts
        wp_enqueue_script( 'fdscf-script', $this->plugin_url . 'assets/js/main-scripts.js',  array('jquery'),'1.0.0',true);
	}
}