<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Vouchers_Controller
{
    public function __construct()
    {
        /* create a new voucher on new order */
        add_action('woocommerce_checkout_order_processed',  array( $this, 'create_voucher_on_new_order' ) );

    }

    public function create_voucher_on_new_order( $order_id )
    {
        // $order_id = 56;
        $order = wc_get_order( $order_id );

        if( $order !== false ){

            $voucher_data = array(
                'customer_id'       => 0,
                'vendor_id'         => 0,
                'order_id'          => 0,
                'voucher_amount'    => 0.00,
                'product_id'        => 0
            );

            foreach ( $order->get_items() as $item_id => $item ) {
                $product = $item->get_product();
                $type = $product->get_type();

                if( $type == "fd_wc_offer" ){
                    
                    $product_id = $item->get_product_id();
                    $product = $item->get_product();
                    $author = get_userdata($product->post->post_author);

                    $fd_wc_offer_voucher_expiry             = get_post_meta( $product->get_id(), 'fd_wc_offer_voucher_expiry' )[0];
                    $fd_wc_offer_voucher_use_global_expiry  = get_post_meta( $product->get_id(), 'fd_wc_offer_voucher_use_global_expiry' )[0];
                    $fd_wc_offer_voucher_expiry_date        = get_post_meta( $product->get_id(), 'fd_wc_offer_voucher_expiry_date' )[0];

                    if( $fd_wc_offer_voucher_expiry == 'fd_wc_offer_voucher_expiry_enabled' || true ){
                        $voucher_data['will_expire'] = true;

                        if( $fd_wc_offer_voucher_use_global_expiry == 'fd_wc_offer_voucher_use_global_expiry_enabled' ){
                            //get global setting set by the admin for voucher expiry
                            $voucher_data['expires_at'] = date('Y-m-d', strtotime(' +14 day'));
                        }else{
                            $fd_wc_offer_voucher_expiry_date = date('Y-m-d', strtotime(' +7 day'));
                            $voucher_data['expires_at'] = $fd_wc_offer_voucher_expiry_date;
                        }

                    }
                    

                    if( in_array( 'seller', $author->roles ) ){

                        $voucher_data['customer_id']        = $order->get_user_id();
                        $voucher_data['order_id']           = $order_id;
                        $voucher_data['vendor_id']          = $author->ID;
                        $voucher_data['voucher_amount']     = $item->get_total();
                        $voucher_data['product_id']         = $product_id;

                        $voucher = FD_Voucher::create_voucher($voucher_data);

                        if( $voucher == false ){
                            wp_die( 'An error occured while generating the voucher for this order' );
                        }else{
                            $meta_key       = '_fd_voucher_id';
                            $meta_value     = $voucher->get_ID();
                            wc_add_order_item_meta( $item_id, $meta_key ,$meta_value );
                        }



                    }

                }

            }
        }

    }
    
}

new FD_Vouchers_Controller();