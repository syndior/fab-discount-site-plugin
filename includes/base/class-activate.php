<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Activate
{
    public static function activate()
    {
        flush_rewrite_rules();
    }
}

new FD_Activate();