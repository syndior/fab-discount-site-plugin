<?php if ( ! defined( 'ABSPATH' ) ) exit;

    global $post;
    $product = wc_get_product( $post->ID );
    if( is_user_logged_in() ){
        $current_user =  wp_get_current_user();
        // var_dump( $current_user );
    }

    /**
     * Generate markup for 'buy with store credit' options
     */
?>

<?php if( is_user_logged_in() ): ?>
<div class="fd_add_to_cart_options_wrapper">
    <div>
        <a href="#">Pay With Store Credit</a>
    </div>
</div>
<?php endif; ?>

<?php