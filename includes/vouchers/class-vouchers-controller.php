<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Vouchers_Controller
{
    public function __construct()
    {
        add_action( 'init', array( $this, 'activate' ) );
    }

    public function activate()
    {
       //code...
    }
}

new FD_Vouchers_Controller();