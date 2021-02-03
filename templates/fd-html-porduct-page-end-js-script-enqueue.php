<?php if ( ! defined( 'ABSPATH' ) ) exit;
    global $post;
    $product = wc_get_product( $post->ID );
?>

<?php if( is_user_logged_in() ): ?>

<script>
    window.addEventListener('DOMContentLoaded', function(){
        //hook ajax function on wink button click
        if( typeof fd_ajax_obj !== 'undefined' ){
            let requestObject = {};
            requestObject.productId = <?php echo $product->get_id(); ?>;
            requestObject.requestType = 'product_log';
            makeAjaxRequest(requestObject);
        }
    }, false);
</script>
<?php endif; ?>

<?php