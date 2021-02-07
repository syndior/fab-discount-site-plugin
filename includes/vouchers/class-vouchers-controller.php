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

        $voucher_data = array(
            'customer_id' => 1,
            'vendor_id' => 1,
            'order_id' => 1,
            'product_id' => 1,
            'will_expire' => true,
            'expires_at' => date("Y-m-d H:i:s"),
        );


        $voucher_id = 4;
        $voucher = new FD_Voucher( $voucher_id );

        $response = array(
            'voucher' => $voucher->get_key(),
        );
        
        wp_send_json_success($response);
        wp_die();
    }
    
}

new FD_Vouchers_Controller();