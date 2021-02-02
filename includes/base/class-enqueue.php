<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Enqueue
{
        public function __construct() 
        {
                add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_end_files' ) );

                if( is_admin() ){
                        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_files' ) );
                }
        }
	
        public function enqueue_front_end_files() 
        {
                //plugin styles
                wp_enqueue_style( 'fdscf-styles', fdscf_url . 'assets/css/main-styles.css', array(), '1.0.0');
                        
                //plugin scripts
                wp_enqueue_script( 'fdscf-script', fdscf_url . 'assets/js/main-scripts.js',  array('jquery'),'1.0.0',true);
        }
        
        public function enqueue_admin_files()
        {
                //admin styles
                wp_enqueue_style( 'fdscf-admin-styles', fdscf_url . 'assets/css/admin-style.css', array(), '1.0.0');
                        
                //admin scripts
                wp_enqueue_script( 'fdscf-admin-script', fdscf_url . 'assets/js/admin-scripts.js',  array('jquery'),'1.0.0',true);
                
        }
}

new FD_Enqueue();