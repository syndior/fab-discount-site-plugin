<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Refunds_Controller
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

new FD_Refunds_Controller();