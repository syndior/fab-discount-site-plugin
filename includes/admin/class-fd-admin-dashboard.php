<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class FD_ADMIN_DAHSBOARD
{
    function __construct()
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
        }
    }
}

new FD_ADMIN_DAHSBOARD();