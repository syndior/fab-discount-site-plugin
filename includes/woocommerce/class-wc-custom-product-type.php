<?php if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Product_FD_Voucher extends  WC_Product
{
    public function __construct( $product )
    {
        $this->product_type = 'fd_wc_voucher';
        parent::__construct( $product );
    }

    public function get_type() {
        return 'fd_wc_voucher';
    }

    public function get_price( $context = 'view' ) {

        // if ( current_user_can('manage_options') ) {
        //     $price = $this->get_meta( '_member_price', true );
        //     if ( is_numeric( $price ) ) {
        //         return $price;
        //     }
        
        // }
		return $this->get_prop( 'price', $context );
      }
}