<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Dokan_Controller
{
    public function __construct()
    {
     
        /* Add Custom Product Types to Vednor create new product dropdown */
        add_filter( 'dokan_product_types', array( $this, 'add_custom_product_type' ), 9999 );
    }

    public function add_custom_product_type( $product_types )
    {
        $product_types['fd_wc_offer'] =  'FD Offer';
        $product_types['fd_wc_offer_variable'] =  'FD Offer Variable';
        
        return $product_types;
    }
}

new FD_Dokan_Controller();