<?php if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Product_FD_Offer extends  WC_Product
{
    public function __construct( $product = 0 )
    {
        $this->product_type = 'fd_wc_offer';
        parent::__construct( $product );
    }

    public function get_type() {
        return 'fd_wc_offer';
    }

    public function add_to_cart_url() {
        $url = $this->is_purchasable() && $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );
        return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
    }

    public function get_price( $context = 'view' ) {
		return $this->get_prop( 'price', $context );
    }
}

class WC_Product_FD_Offer_Variable extends WC_Product_Variable
{
    public function __construct( $product = 0 )
    {
        $this->product_type = 'fd_wc_offer_variable';
        parent::__construct( $product );
    }

    public function get_type() {
        return 'fd_wc_offer_variable';
    }

    public function is_type( $type ) {
        // Some themes/plugins will check to see if this is a Variable type before including files required for
        // the variable vouchers product to work correctly. By checking for 'variable' we make this compatible with these
        // types of themes and plugins.
        return ( $this->get_type() === $type || 'variable' === $type || ( is_array( $type ) && ( in_array( $this->get_type(), $type ) || in_array( 'variable', $type ) ) ));
    }

    public function get_price_html( $price = '' ) {
        return parent::get_price_html( $price );
    }

    public function add_to_cart_url() {
        $url = $this->is_purchasable() && $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );
        return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
    }

    public function get_price( $context = 'view' ) {
		return $this->get_prop( 'price', $context );
    }
}