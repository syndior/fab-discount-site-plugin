<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Woocommerce_Controller
{
    public function __construct()
    {
        add_filter( 'product_type_selector', array( $this, 'addProductTypeFilter' ) );
    }

    public function activate()
    {
        // new WC_Product_Voucher();
    }

    public function addProductTypeFilter( $types )
    {
        $types[ 'fd_wc_voucher' ] = 'FD Voucher';
        return $types;
    }


}

new FD_Woocommerce_Controller();