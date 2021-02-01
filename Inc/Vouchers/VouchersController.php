<?php

namespace Inc\Vouchers;
use \Inc\Base\BaseController;

class VouchersController extends BaseController
{
    public function register()
    {
        add_action( 'init', array( $this, 'activate' ) );
    }

    public function activate()
    {
        echo '<script>console.log("VouchersController activated")</script>';
    }
}