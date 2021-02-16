<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Activate
{
    public static function activate()
    {
        flush_rewrite_rules();

        FD_Activate::generate_custom_database_tables();

        // if ( ! get_term_by( 'slug', 'fd_wc_offer', 'product_type' ) ) {
        //     wp_insert_term( 'fd_wc_offer', 'product_type' );
        // }
        // if ( ! get_term_by( 'slug', 'fd_wc_offer_variable', 'product_type' ) ) {
        //     wp_insert_term( 'fd_wc_offer_variable', 'product_type' );
        // }
    }

    private static function generate_custom_database_tables()
    {
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $charset_collate = $wpdb->get_charset_collate();

        /**
         * Create Vouchers Table
         */
        $wpdb->fdscf_vouchers = fdscf_vouchers_db_table_name;

        $SQL = "CREATE TABLE IF NOT EXISTS `{$wpdb->fdscf_vouchers}` (
            `fd_voucher_id`         INT NOT NULL AUTO_INCREMENT,
            `fd_voucher_key`        VARCHAR(60) NOT NULL UNIQUE,
            `voucher_amount`        DECIMAL(15,6) NULL DEFAULT NULL,
            `created_at`            TIMESTAMP NOT NULL DEFAULT NOW(),
            `updated_at`            TIMESTAMP NOT NULL DEFAULT NOW() ON UPDATE now(),
            `expires_at`            TIMESTAMP NULL,
            `will_expire`           INT NOT NULL DEFAULT 0,
            `fd_voucher_status`     VARCHAR(60) NOT NULL DEFAULT 'active',
            `vendor_id`             BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            `customer_id`           BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            `order_id`              BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            `product_id`            BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            `variation_id`          BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            `product_type`          VARCHAR(60) NULL DEFAULT NULL,
            PRIMARY KEY  (fd_voucher_id)
            ) $charset_collate;";
        
        dbDelta( $SQL );

        /**
         * Create Transactions table
         */
        $wpdb->fdscf_transactions = fdscf_transactions_db_table_name;

        $SQL = "CREATE TABLE IF NOT EXISTS `{$wpdb->fdscf_transactions}` (
            `transaction_id`        INT NOT NULL AUTO_INCREMENT,
            `transaction_type`      VARCHAR(60) NULL DEFAULT NULL,
            `created_at`            TIMESTAMP NOT NULL DEFAULT NOW(),
            `voucher_id`            BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            `order_id`              BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            `user_id`               BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            `transaction_amount`    DECIMAL(15,6) NOT NULL DEFAULT 0,
            PRIMARY KEY  (transaction_id)
            ) $charset_collate;";
        
        dbDelta( $SQL );

    }
}