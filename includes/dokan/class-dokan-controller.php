<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Dokan_Controller
{
    public function __construct()
    {
        add_action( 'init', array( $this, 'activate' ) );
    }

    public function activate()
    {
        echo '<script>console.log("FD_Dokan_Controller activated")</script>';
    }
}

new FD_Dokan_Controller();