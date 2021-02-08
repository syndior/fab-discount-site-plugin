<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Vouchers_Controller
{
    public function __construct()
    {
        /* hook ajax handler to log user's viewed products */
        // add_action('wp_ajax_fd_create_voucher_ajax',  array( $this, 'fd_create_voucher_ajax' ) );


        // $voucher_data = array(
        //     'customer_id' => 1,
        //     'vendor_id' => 1,
        //     'order_id' => 1,
        //     'product_id' => 1,
        //     'will_expire' => true,
        //     'expires_at' => date("Y-m-d H:i:s"),
        // );

        // $voucher = FD_Voucher::create_voucher( $voucher_data );

        // $voucher_id = 4;
        // $voucher = new FD_Voucher( $voucher_id );
        // var_dump( $voucher->get_key() );

        // $key = '4BC38D-EBF3EE-51AC8F-01469F';
        // $voucher = FD_Voucher::validate_voucher_key( $key );
        // var_dump( $voucher );

    }

    public function fd_create_voucher_ajax()
    {
        check_ajax_referer( 'ajax_check', 'security' );

        $voucher_data = array(
            'customer_id' => 1,
            'vendor_id' => 1,
            'order_id' => 1,
            'product_id' => 1,
            'will_expire' => true,
            'expires_at' => date("Y-m-d H:i:s"),
        );



        // $voucher_id = 4;
        // $voucher = new FD_Voucher( $voucher_id );

        $response = array(
            'voucher' => $voucher->get_key(),
        );
        
        wp_send_json_success($response);
        wp_die();
    }
    
}

new FD_Vouchers_Controller();