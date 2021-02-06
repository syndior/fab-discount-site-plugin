<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Vouchers_Controller
{
    public function __construct()
    {
        /* hook ajax handler to log user's viewed products */
        add_action('wp_ajax_fd_create_voucher_ajax',  array( $this, 'fd_create_voucher_ajax' ) );

        FD_Voucher::create_voucher();
        // $result = FD_Voucher::validate_voucher_key('a340b47bb1d48e40ac955b44');
        // var_dump( $result->update_status('blocked') );
    }

    public function fd_create_voucher_ajax()
    {
        check_ajax_referer( 'ajax_check', 'security' );

        $response = array();

        wp_send_json_success($response);
        wp_die();
    }
    
}

new FD_Vouchers_Controller();