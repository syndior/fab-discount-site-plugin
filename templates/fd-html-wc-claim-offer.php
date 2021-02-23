<?php if ( ! defined( 'ABSPATH' ) ) exit;

    $order = wc_get_order( $order_id );
    $offer_vouchers = array();
    foreach ( $order->get_items() as $item_id => $item ) {
        $product = $item->get_product();
        $type = $product->get_type();

        if( $type == "fd_wc_offer"  ){
            $voucher_id = $item->get_meta('_fd_voucher_id');
            $voucher = new FD_Voucher( $voucher_id );

            $offer = array();
            $offer['offer_img_src']     = ( wp_get_attachment_url($product->get_image_id()) ) ? wp_get_attachment_url($product->get_image_id()) : wc_placeholder_img_src();
            $offer['offer_title']       = $product->get_name();
            $offer['offer_key']         = $voucher->get_key();

            $offer_vouchers[] = $offer;
        }

    }

?>

<?php if( !empty( $offer_vouchers ) ):?>

<div class="fd_wc_order_claim_offer_wrapper" >

<?php foreach( $offer_vouchers as $voucher ):?>
    <div class="fd_wc_order_claim_offer fd_boxshadow_1">
        <div class="fd_offer_details_wrapper">
            <div class="fd_offer_image">
                <img src="http://localhost/site/wp-content/uploads/2021/02/224547_36814_41.jpg" alt="">
            </div>
            <div class="fd_offer_details">
                <p class="fd_offer_title">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Reiciendis, delectus?</p>
                <div class="fd_claim_offer_option">
                    <button class="fd_claim_offer_btn fd_button_link">Claim Offer</button>
                    <a class="fd_offer_voucher_key_wrapper" href="#">
                        <p class="fd_offer_voucher_key">A222AE-30A6F7-0E5788-8E6F34</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endforeach;?>

</div>

<?php endif;?>