<?php if ( ! defined( 'ABSPATH' ) ) exit;
    add_action( 'wp_loaded', function(){
        var_dump(ABSPATH);
    } );
    $voucher_result_item = '';
    $voucher_data = '';
    if( isset($_POST['voucher_ids']) && !empty($_POST['voucher_ids']) ){
        $voucher_ids = $_POST['voucher_ids'];

        $voucher_objects = array();
        foreach( $voucher_ids as $id ){
            $id = absint( $id );
            $voucher = new FD_Voucher( $id );
            if( $voucher->get_status() == 'active' ){
                $voucher_objects[] = $voucher;
            }
        }

        if( !empty($voucher_objects) ){

            $vouchers_data = array( 'voucher_ids' => array() );

            foreach( $voucher_objects as $voucher ){
                $vouchers_data['voucher_ids'][] = $voucher->get_id();
                $product        = wc_get_product( $voucher->get_product_id() );
                $product_img    = ( wp_get_attachment_url($product->get_image_id()) ) ? wp_get_attachment_url($product->get_image_id()) : wc_placeholder_img_src();

                $voucher_result_item .= '<div class="fd_claim_voucher_result_item">';
                
                $voucher_result_item .= '<input type="hidden" name="fd_voucher_ids[]" value="'. $voucher->get_id() .'">';
                
                $voucher_result_item .= '<div class="fd_claim_voucher_result_item_img">';
                $voucher_result_item .= '<img src="'. $product_img .'">';
                $voucher_result_item .= '</div>';
                
                $voucher_result_item .= '<div class="fd_claim_voucher_result_item_info">';
                
                $voucher_result_item .= '<p class="fd_claim_voucher_result_item_title">'. $product->get_name() .'</p>';
                
                $voucher_result_item .= '<table class="fd_claim_voucher_result_item_data">';
                
                $voucher_result_item .= '<tr>';
                $voucher_result_item .= '<th>Status:</th>';
                $voucher_result_item .= '<td>'. $voucher->get_status() .'</td>';
                $voucher_result_item .= '</tr>';
                
                $voucher_result_item .= '<tr>';
                $voucher_result_item .= '<th>Amount:</th>';
                $voucher_result_item .= '<td>'. $voucher->get_amount() .'</td>';
                $voucher_result_item .= '</tr>';
                
                $voucher_result_item .= '<tr>';
                $voucher_result_item .= '<th>Key:</th>';
                $voucher_result_item .= '<td>'. $voucher->get_key() .'</td>';
                $voucher_result_item .= '</tr>';

                $voucher_result_item .= '</table>';
                
                $voucher_result_item .= '</div>';
                
                $voucher_result_item .= '</div>';
            }

            $voucher_data = json_encode($vouchers_data);
        }

    }


    if( isset( $_POST['fd_request_type'] ) && isset( $_POST['fd_voucher_ids'] ) && $_POST['fd_request_type'] == 'fd_add_vouchers_to_cart' ){
        $voucher_ids = $_POST['fd_voucher_ids'];
        
        WC()->cart->empty_cart();
        foreach( $voucher_ids as $id ){
            $id = absint( $id );

            $voucher = new FD_Voucher( $id );

            if( $voucher->get_status() == 'active' ){

                $product = wc_get_product( $voucher->get_product_id() );

                $qty = 1;
                $cart_item_data = array( 
                    'voucher_id' => $voucher->get_id(),
                    'voucher_amount' => $voucher->get_amount(),
                );

                if( $product->get_type() == 'simple' ){
                    
                    $product_id = $product->get_id();
                    $variation_id = 0;
                    
                }elseif ( $product->get_type() == 'variation' ) {
                    
                    $product_id = $product->get_parent_id();
                    $variation_id = $product->get_id();
                    
                }

                $added_to_cart_status = WC()->cart->add_to_cart(  $product_id, $qty, $variation_id, $variation = array(),$cart_item_data);

            }

        }

        if( $added_to_cart_status !== false ){

            $log =  wc_get_cart_url();
            echo '<script>window.location.replace("'.$log.'");</script>';

        }

    }
?>
<div class="fd_claim_voucher_form_wrapper">
    <form action="" id="fd_claim_voucher_form">
        <input type="text" name="fd_voucher_key" id="fd_voucher_key">
        <input type="submit" value="Check Voucher" class="fd_claim_voucher_btn" id="fd_claim_voucher_submit">
    </form>
    <form action="<?=$_SERVER['REQUEST_URI'];?>" method="POST" class="fd_claim_result_wrapper">
        <div class="fd_claim_results" data-active-vouchers='<?=$voucher_data;?>'>
            <?=$voucher_result_item;?>
        </div>
        <input type="hidden" name="fd_request_type" value="fd_add_vouchers_to_cart">
        <input type="submit" value="Claim Vouchers" class="fd_claim_voucher_btn">
    </form>
</div>
<?php
    $template = ob_get_clean();
?>
