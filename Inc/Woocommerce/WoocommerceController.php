<?php

namespace Inc\Woocommerce;
use \Inc\Base\BaseController;

class WoocommerceController extends BaseController
{
    public function register()
    {
        add_action( 'init', array( $this, 'activate' ) );
    }

    public function activate()
    {
        echo '<script>console.log("WoocommerceController activated")</script>';
    }
}
