<?php

namespace Inc\Refunds;
use \Inc\Base\BaseController;

class RefundsController extends BaseController
{
    public function register()
    {
        add_action( 'init', array( $this, 'activate' ) );
    }

    public function activate()
    {
        echo '<script>console.log("RefundsController activated")</script>';
    }
}