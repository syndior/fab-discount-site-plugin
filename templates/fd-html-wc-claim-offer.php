<?php if ( ! defined( 'ABSPATH' ) ) exit;

    $order = wc_get_order( $order_id );
    $offer_vouchers = array();
    $voucher_ids_array = array();
    $claim_voucher_page_id = get_field('set_claim_voucher_page','options');
    $claim_voucher_page_url = get_permalink($claim_voucher_page_id);

    foreach ( $order->get_items() as $item_id => $item ) {
        $product = $item->get_product();
        $type = $product->get_type();

        if( $type == "fd_wc_offer" ){
            $voucher_ids = $item->get_meta('_fd_voucher_id');
            $voucher_keys = array();

            for ($i=0; $i < sizeof($voucher_ids); $i++) { 
                $voucher_id = $voucher_ids[$i]; 
                $voucher_ids_array[] = $voucher_id;
                $voucher = new FD_Voucher( $voucher_id ); 
                $voucher_keys[] = array('voucher_id'=>$voucher_id,'voucher_key'=>$voucher->get_key());                
            }

            $offer = array();
            $offer['offer_img_src']     = ( wp_get_attachment_url($product->get_image_id()) ) ? wp_get_attachment_url($product->get_image_id()) : wc_placeholder_img_src();
            $offer['offer_title']       = $product->get_name();
            $offer['offer_key']         = $voucher_keys;

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
                <img src="<?php echo $voucher['offer_img_src']?>" alt="">
            </div>
            <div class="fd_offer_details">
                <p class="fd_offer_title"> <?php echo $voucher['offer_title']?> </p>
                <div class="fd_claim_offer_option">
                    <button class="fd_claim_offer_btn fd_button_link">Claim Offer</button>
                    <div class="fd_offer_voucher_key_wrapper">
                        <?php foreach ($voucher['offer_key'] as $key => $value) {?>
                        
                            <form  action="<?php echo $claim_voucher_page_url?>" method = "POST">
                              <input type="hidden" name="voucher_ids[]" value="<?php echo $value['voucher_id'];?>">
                              <input type="submit" class="claim_individual_voucher fd_offer_voucher_key" name = "claim_offers" value = "<?php echo $value['voucher_key'];?>">
                            </form>
                        <br>
                        <br>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach;?>

<form action="<?php echo $claim_voucher_page_url?>" method = "POST">
    <?php foreach ($voucher_ids_array as $key => $value) {?>
        <input type="hidden" name = "voucher_ids[]" value = "<?php echo $value?>">
    <?php }?>

    <input type="submit" class="claim_all_vouchers" name = "claim_offers" value = "Claim All Vouchers">
</form>
</div>

<?php endif;?>