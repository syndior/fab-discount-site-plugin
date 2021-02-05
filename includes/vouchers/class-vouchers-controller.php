<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Vouchers_Controller
{
    public function __construct()
    {
        /* hook ajax handler to log user's viewed products */
        add_action('wp_ajax_fd_create_voucher_ajax',  array( $this, 'fd_create_voucher_ajax' ) );
    }

    public function fd_create_voucher_ajax()
    {
        check_ajax_referer( 'ajax_check', 'security' );

        $voucher_attr = array(
            'post_type' => 'voucher',
            'post_title' => 'voucher1',
            'post_content' => '1234',
            'meta_input' => array(
                'test_meta_1' => 'some random data',
                'test_meta_3' => 'some other random data'
            )
        );

        // $voucher_creation_status = wp_insert_post( $voucher_attr, false, true );

        $args = array(
            'post_type' => 'voucher',
            'numberposts' => -1
        );
        // $vouchers = get_posts( $args );

        $response = array(
            'type' => 'success',
            // 'post_status' => $voucher_creation_status,
            // 'created_vouchers' => $vouchers,
            'data' => array(
                'key' => $key = bin2hex(random_bytes(12)),
                'key_length' => strlen($key),
                'key_check' => hash_equals( $key, $key ),
            ),
        );

        wp_send_json_success($response);
        wp_die();
    }


    public static function fd_generate_voucher()
    {
        /**
         * fire this function after a successfull payment is made for an FD Vucher order
         * 
         * grab the order data and get the user info from that order
         * 
         * generate a unique voucher id for that post
         * create a new wp post with poet_type = voucher
         * add meta to voucher id and other information that we will be using to manage voucher
         * 
         * update the user data that purchase that FD voucher product
         * return the voucher info to the front-end so thinds lige voucher id and product linked to that voucher can be displayed
         */

         $voucher_meta = array(
             'user_id'      => 0,
             'order_id'     => 0,
             'product_id'   => 0,
             'voucher_key'  => null
         );

         $voucher_properties = array(
            'post_type'     => 'voucher',
            'post_status'   => 'valid',
            'post_title'    => 'FD VOUCHER',
            'post_content'  => 'FD VOUCHER',
            'meta_input'    => $voucher_meta
         );

         
    }
    
}

new FD_Vouchers_Controller();