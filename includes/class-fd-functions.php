<?php if ( ! defined( 'ABSPATH' ) ) exit;

function get_acf_option( string $field_key = '' ){
    return get_field($field_key, 'option');
}