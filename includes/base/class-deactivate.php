<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Deactivate
{
    public static function deactivate()
    {
        flush_rewrite_rules();
    }
}

new FD_Deactivate();