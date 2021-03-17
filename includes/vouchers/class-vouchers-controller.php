<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Vouchers_Controller
{
    public function __construct()
    {
        /* create a new voucher on new order */
        add_action('woocommerce_checkout_order_processed',  array( $this, 'create_voucher_on_new_order' ) );
        add_action('woocommerce_checkout_order_processed',  array( $this, 'mark_voucher_as_reedemed' ) );

        /* Generates claim voucher/offer form for fornt-end */
        add_shortcode( 'fd_claim_voucher_form', array( $this, 'print_claim_voucher_form' ) );
        
        /* Hook ajax request to handle voucher validation */
        add_action('wp_ajax_claim_voucher_ajax_request_handler',  array( $this, 'claim_voucher_ajax_request_handler' ) );
        add_action('wp_ajax_nopriv_claim_voucher_ajax_request_handler',  array( $this, 'claim_voucher_ajax_request_handler' ) );
        
        /* Modify product prices */
        add_action('woocommerce_before_calculate_totals',  array( $this, 'modify_product_prices' ), 9999, 1 );
        
        /* add order item meta */
        add_action('woocommerce_add_order_item_meta',  array( $this, 'add_order_item_meta_for_claimed_vouchers' ), 10, 2);

    }



    /**
     * 
     */
    public function create_voucher_on_new_order( $order_id )
    {
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
                $quantity = $item->get_quantity();

                if( $type == "fd_wc_offer" ){
                    
                    $product = $item->get_product();
                    $author = get_userdata($product->post->post_author);


                    $fd_offer_linked_product                = (int)get_post_meta( $product->get_id(), 'fd_offer_linked_product' )[0];
                    $fd_offer_linked_product_variation      = (int)get_post_meta( $product->get_id(), 'fd_offer_linked_product_variation' )[0];

                    if( $fd_offer_linked_product_variation > 0 ){
                        $product_id = $fd_offer_linked_product_variation;
                    }else{
                        $product_id = $fd_offer_linked_product;
                    }


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
                    

                    if( in_array( 'seller', $author->roles ) || in_array( 'administrator', $author->roles ) ){

                        $voucher_data['customer_id']        = $order->get_user_id();
                        $voucher_data['order_id']           = $order_id;
                        $voucher_data['vendor_id']          = $author->ID;
                        $voucher_data['voucher_amount']     = $item->get_total();
                        $voucher_data['product_id']         = $product_id;

                        $meta_values = array();
                        for ($i=0 ; $i < $quantity ; $i++) { 
                            $voucher = FD_Voucher::create_voucher($voucher_data);   
                            if( $voucher == false ){
                                wp_die( 'An error occured while generating the voucher for this order' );
                            }                             
                            $meta_values[]     = $voucher->get_ID();                            
                        }//generate vouchers equal to item quantity

                        $meta_key       = '_fd_voucher_id';
                        $meta_value = $meta_values;
                        wc_add_order_item_meta( $item_id, $meta_key ,$meta_value );
                        



                    }

                }//$type == "fd_wc_offer"

            }
    
        }
    
    }


    /**
     * CLaim voucher form shortcode
     */
    public function print_claim_voucher_form()
    {
        $template = '';
        require_once ( fdscf_path . 'templates/fd-html-wc-claim-voucher-form.php' );
        return $template;
    }

    /**
     * Claim voucher ajax request handler
     */
    public function claim_voucher_ajax_request_handler()
    {
        check_ajax_referer( 'ajax_check', 'security' );

        //ajax response defaults
        $ajax_response = array(
            'type' => 'error',
        );

        if( isset($_REQUEST['voucher_key']) && strlen($_REQUEST['voucher_key']) > 0 && class_exists('FD_Voucher') ){

            $ajax_response['type']          = 'success';
            $ajax_response['user_id']       = is_user_logged_in() ? get_current_user_id() : 0;

            $voucher_key =  sanitize_text_field( $_REQUEST['voucher_key'] );
            $voucher_key_status = FD_Voucher::validate_voucher_key($voucher_key);

            if( $voucher_key_status !== false ){
                
                $voucher = $voucher_key_status;
                $ajax_response['voucher_id']        = $voucher->get_id();
                $ajax_response['voucher_status']    = $voucher->get_status();
                $ajax_response['voucher_key']       = $voucher->get_key();
                $ajax_response['voucher_amount']    = $voucher->get_amount();
                $product = wc_get_product( $voucher->get_product_id() );

                if( $product !== null && $product !== false ){

                    $ajax_response['product_id']     = $product->get_id();
                    $ajax_response['product_name']   = $product->get_name();
                    $ajax_response['product_img']    = ( wp_get_attachment_url($product->get_image_id()) ) ? wp_get_attachment_url($product->get_image_id()) : wc_placeholder_img_src();

                }

            }else{
                $ajax_response['voucher_status'] = false;
            }

            
        }



        wp_send_json_success($ajax_response);
        wp_die();
    }

    /**
     * 
     */
    public function modify_product_prices( $cart )
    {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
        if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) return;

        foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
            
            $product = $cart_item['data'];
            if( isset( $cart_item['voucher_id'] ) && isset( $cart_item['voucher_amount'] ) ){

                $cart_item['data']->set_sold_individually( TRUE );
                
                $original_price = (float)$product->get_price();
                $discount_price = (float)$cart_item['voucher_amount'];

                if( $discount_price > $original_price ){
                    $new_price = 0;
                }else{
                    $new_price = $original_price - $discount_price;
                }

                $cart_item['data']->set_price( $new_price );

            }

        }
    }


    /**
     * 
     */
    public function add_order_item_meta_for_claimed_vouchers( $item_id, $values )
    {
        if( isset($values['voucher_id']) ){
            wc_add_order_item_meta( $item_id, '_fd_voucher_id', $values['voucher_id'] );
        }
    }


    /**
     * 
     */
    public function mark_voucher_as_reedemed( $order_id )
    {
        $order = wc_get_order( $order_id );
        if( $order !== false ){

            foreach ( $order->get_items() as $item_id => $item ) {
                

                $product    = $item->get_product();
                $product_type = $product->get_type();
                
                if($product_type == "simple"){

                    $voucher_id = $item->get_meta('_fd_voucher_id', true);
                    if( isset($voucher_id) && $voucher_id !== null && $voucher_id !== false ){
                    
                        $voucher = new FD_Voucher($voucher_id);
                        $voucher->update_status('redeemed');
                        
                    }// voucher id
    
                }//if product type
                
            }

        }
    }
    
}

new FD_Vouchers_Controller();