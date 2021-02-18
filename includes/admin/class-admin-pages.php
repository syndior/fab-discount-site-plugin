<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Admin_Pages
{
    public function __construct()
    {
        if( function_exists('acf_add_options_page') ){
            acf_add_options_page(array(
                'page_title' 	=> 'FD - Site Settings',
                'menu_title'	=> 'FD - Site Settings',
                'menu_slug' 	=> 'fd-site-settings',
                'capability'	=> 'edit_posts',
                'redirect'		=> false
            ));
            
            acf_add_options_sub_page(array(
                'page_title' 	=> 'Product Settings',
                'menu_title'	=> 'Product Settings',
                'parent_slug'	=> 'fd-site-settings',
            ));
            
            acf_add_options_sub_page(array(
                'page_title' 	=> 'Vednor Settings',
                'menu_title'	=> 'Vednor Settings',
                'parent_slug'	=> 'fd-site-settings',
            ));

            acf_add_options_sub_page(array(
                'page_title' 	=> 'Voucher Settings',
                'menu_title'	=> 'Voucher Settings',
                'parent_slug'	=> 'fd-site-settings',
            ));
        }

        /* Load Custom ACF Field / Unique ID */
        add_action('acf/include_field_types', array($this,'include_field_types_unique_id') );

        /* Populates acf field with custom values */
        add_filter('acf/load_field/name=select_static_product_to_show_on_home_page',array($this,'acf_load_products_field_choices') );

        /* Add vouchers management page */
        add_action( 'admin_menu', array( $this, 'add_admin_vouchers_management_page' ) );

    }//constructor


    public function include_field_types_unique_id()
    {
        require_once ( fdscf_path . 'includes/admin/acf-unique_id-v5.php' );
    }

    public function acf_load_products_field_choices( $field ) 
    {
    
        // reset choices
        $field['choices'] = array();
        
        $args = array(
            'post_type'=>'product',
            'numberposts'=>-1
        );

        //setting deafult values in choices
        $label = "Select Product To Show";
        $value = 0;
        $field['choices'][$value] = $label;
        
        // getting all the products except offer
        $products= get_posts($args);
        foreach ($products as $key => $product) {
            $product = wc_get_product($product->ID);
            
            if($product->is_type('fd_wc_offer') || $product->is_type('fd_wc_offer_variable')){
                $label = $product->get_title();
                $value = $product->get_ID();
                $field['choices'][$value] = $label; 
            }//if
        }//for each
        
        // return the field
        return $field;
        
    }


    public function add_admin_vouchers_management_page()
    {
        if( function_exists( 'add_menu_page' ) ){
            
            $page_title         = 'Vouchers Management';
            $menu_title         = 'Vouchers Management';
            $capability         = 'manage_options';
            $menu_slug          = 'fdscf_vouchers_management';
            $function           = array( $this, 'load_admin_vouchers_management_page_html' );
            $icon_url           = 'dashicons-admin-generic';
            $position           = 99;

            add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
        }
    }

    public function load_admin_vouchers_management_page_html()
    {
        require_once ( fdscf_path . 'templates/fd-html-admin-page-vouchers-management.php' );
    }
    

}//class

new FD_Admin_Pages();