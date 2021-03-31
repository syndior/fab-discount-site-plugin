<?php if ( ! defined( 'ABSPATH' ) ) exit;

    /**
     * Generate markup for 'buy with store credit' options
     */
    global $post;
    $product = wc_get_product( $post->ID );
    $generate_option = false;
    if( is_user_logged_in() &&  $product->get_type() == 'fd_wc_offer' ){
        
        $user_id = get_current_user_id();
        $wallet = new FD_Wallet( $user_id );

        if( $wallet->get_status() == 'active' && $wallet->get_balance() > 0 ){
            $generate_option = true;
        }
        
    }
?>

<?php if( $generate_option ): ?>
<div class="fd_add_to_cart_options_wrapper">
    <form method="POST">
        <input type="hidden" name="fd_wp_nonce" value="<?=wp_create_nonce('fd_custom_add_to_cart')?>">
        <button type="submit" name="fd_pay_with_credit" value="fd_custom_add_to_cart" class="button">Pay With Store Credit</button>
    </form>
</div>
<?php endif; ?>