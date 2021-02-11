<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Vouchers_Controller
{
    public function __construct()
    {
        /* create a new voucher on new order */
        // add_action('woocommerce_thankyou',  array( $this, 'create_voucher_on_new_order' ) );
        add_action('init',  array( $this, 'create_voucher_on_new_order' ) );
    }

    public function create_voucher_on_new_order( $order_id )
    {
        $order_id = 56;

        $order = wc_get_order( $order_id );

        if( $order !== false ){

            // var_dump( $order );

            $voucher_data = array(
                'customer_id'       => '',
                'vendor_id'         => '',
                'order_id'          => '',
                'voucher_amount'    => '',
                'product_id'        => ''
            );
            // var_dump( $order );
            // var_dump( $order->get_user_id() );
            foreach ( $order->get_items() as $item_id => $item ) {
                // $product_id = $item->get_product_id();
                // $variation_id = $item->get_variation_id();
                $product = $item->get_product();
                // $name = $item->get_name();
                // $quantity = $item->get_quantity();
                // $subtotal = $item->get_subtotal();
                // $total = $item->get_total();
                // $tax = $item->get_subtotal_tax();
                // $taxclass = $item->get_tax_class();
                // $taxstat = $item->get_tax_status();
                // $allmeta = $item->get_meta_data();
                // $somemeta = $item->get_meta( '_whatever', true );
                // $type = $item->get_type();
                $type = $product->get_type();
                if( $type == "fd_wc_offer"  || $type == "fd_wc_offer_variable" ){
                    $product = $item->get_product();
                    $product_meta = get_post_meta( $product->get_ID() );
                    $allmeta = $item->get_meta_data();
                    // var_dump( $product );
                }
             }
        }

    }
    
}

new FD_Vouchers_Controller();