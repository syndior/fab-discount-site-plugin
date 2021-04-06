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
                //wordpress media uploader
                wp_enqueue_media();

                //plugin styles
                wp_enqueue_style( 'fdscf-styles', fdscf_url . 'assets/css/main-styles.css', array(), '1.0.0');
                        
                //plugin scripts
                wp_enqueue_script( 'fdscf-script', fdscf_url . 'assets/js/main-scripts.js',  array('jquery'),'1.0.0',true);

                //localize script if user logged in
                //include ajax vars
                $nonce_val = wp_create_nonce('ajax_check');
                $js_object = array(
                        'ajax_url' => admin_url( 'admin-ajax.php' ),
                        'nonce'    => $nonce_val,
                );

                if( is_user_logged_in() ){
                        $js_object['user_id'] = get_current_user_id();
                }
                
                wp_localize_script( 'fdscf-script', 'fd_ajax_obj', $js_object);

                //load slick-slider files
                wp_enqueue_style( 'fdscf-slick-styles', fdscf_url . 'assets/slick/slick.css', array(), '1.0.0');
                wp_enqueue_style( 'fdscf-slick-theme-styles', fdscf_url . 'assets/slick/slick-theme.css', array(), '1.0.0');
                wp_enqueue_script( 'fdscf-slick-script', fdscf_url . 'assets/slick/slick.min.js',  array('jquery'),'1.0.0',true);
                
                //load splide-slider files
                wp_enqueue_style( 'fdscf-splide-theme-styles', fdscf_url . 'assets/splide/css/splide-default.min.css', array(), '1.0.0');
                wp_enqueue_style( 'fdscf-splide-styles', fdscf_url . 'assets/splide/css/splide.min.css', array(), '1.0.0');
                wp_enqueue_script( 'fdscf-splide-script', fdscf_url . 'assets/splide/js/splide.min.js',  array(),'1.0.0',true);

        }
        
        public function enqueue_admin_files()
        {
                //admin styles
                wp_enqueue_style( 'fdscf-admin-styles', fdscf_url . 'assets/css/admin-style.css', array(), '1.0.0');
                        
                //admin scripts
                wp_enqueue_script( 'fdscf-admin-script', fdscf_url . 'assets/js/admin-scripts.js',  array('jquery'),'1.0.0',true);

                //localize script if user logged in
                //include ajax vars
                if( is_user_logged_in() ){
                        $nonce_val = wp_create_nonce('ajax_check');
                        $admin_js_object = array(
                                'ajax_url' => admin_url( 'admin-ajax.php' ),
                                'nonce'    => $nonce_val,
                        );
                        wp_localize_script( 'fdscf-admin-script', 'fd_admin_ajax_obj', $admin_js_object);
                }
                
        }
}

new FD_Enqueue();