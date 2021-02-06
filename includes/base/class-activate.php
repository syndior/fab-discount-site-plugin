<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Activate
{
    public static function activate()
    {
        flush_rewrite_rules();

        FD_Activate::generate_custom_database_tables();
    }

    private static function generate_custom_database_tables()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $wpdb->fdscf_vouchers = fdscf_db_table_name;

        $SQL = "CREATE TABLE IF NOT EXISTS `{$wpdb->fdscf_vouchers}` (
            `fd_voucher_id`         INT NOT NULL AUTO_INCREMENT,
            `fd_voucher_key`        VARCHAR(60) NOT NULL UNIQUE,
            `created_at`            TIMESTAMP NOT NULL DEFAULT NOW(),
            `updated_at`            TIMESTAMP NOT NULL DEFAULT NOW() ON UPDATE now(),
            `expires_at`            TIMESTAMP NULL,
            `will_expire`           INT NOT NULL DEFAULT 0,
            `fd_voucher_status`     VARCHAR(60) NOT NULL DEFAULT 'active',
            `user_id`               BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            `order_id`              BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            `product_id`            BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            PRIMARY KEY  (fd_voucher_id)
            ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $SQL );
    }
}