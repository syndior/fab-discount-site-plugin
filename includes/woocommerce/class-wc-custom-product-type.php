<?php if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Product_FD_Offer extends  WC_Product
{
    public function __construct( $product = 0 )
    {
        $this->product_type = 'fd_wc_offer';
        parent::__construct( $product );
        $this->set_virtual( true );
    }

    public function get_type() {
        return 'fd_wc_offer';
    }

    public function get_virtual( $context = 'view' )
    {
        return true;
    }

    public function add_to_cart_url() {
        $url = $this->is_purchasable() && $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );
        return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
    }

    public function get_price( $context = 'view' ) {
		return $this->get_prop( 'price', $context );
    }
}
