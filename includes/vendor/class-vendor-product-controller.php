<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Vendor_Product_Controller{
    public function __construct(){


        /* Add Custom Product Types to Vednor create new product dropdown */
        add_filter( 'dokan_product_types', array( $this, 'add_custom_product_type' ), 9999);

        //svave extra fields from vendor product
        add_action( 'dokan_new_product_added', array($this,'save_vendor_product_extra_fields'), 10, 2 );
        add_action( 'dokan_product_updated', array($this,'save_vendor_product_extra_fields'), 10, 2 );
    
        add_action('dokan_product_edit_after_product_tags',array($this,'show_vendor_product_extra_fields_edit_page'),99,2);

    }
    /* Add Custom Product Types to Vednor create new product dropdown */
    public function add_custom_product_type( $product_types )
    {
        $product_types['fd_wc_offer'] =  'FD Offer';
        $product_types['fd_wc_offer_variable'] =  'FD Offer Variable';
        return $product_types;
    }


    //svave extra fields from vendor product
    public function save_vendor_product_extra_fields($product_id, $postdata){
        if ( ! dokan_is_user_seller( get_current_user_id() ) ) {
            return;
        }
        
        $fd_product_meta['fd_wc_corner_banner']                     = ( $postdata['fd_wc_corner_banner'] == 'fd_wc_corner_banner_enabled' ) ? $postdata['fd_wc_corner_banner'] : 'fd_wc_corner_banner_disabled';
        $fd_product_meta['fd_wc_corner_banner_title']               = ( isset( $postdata['fd_wc_corner_banner_title'] ) ) ? $postdata['fd_wc_corner_banner_title'] : '';
        $fd_product_meta['fd_wc_corner_banner_headind']             = ( isset( $postdata['fd_wc_corner_banner_headind'] ) ) ? $postdata['fd_wc_corner_banner_headind'] : '';
        
        //scheduling 
        $fd_product_meta['fd_wc_offer_schedule']                     = ( $postdata['fd_wc_offer_schedule'] == 'enabled' ) ? $postdata['fd_wc_offer_schedule'] : 'disabled';
        $fd_product_meta['fd_wc_offer_schedule_date']                     = ( isset( $postdata['fd_wc_offer_schedule_date'] ) ) ? $postdata['fd_wc_offer_schedule_date'] : '';
        $fd_product_meta['fd_wc_offer_schedule_time']                     = ( isset( $postdata['fd_wc_offer_schedule_time'] ) ) ? $postdata['fd_wc_offer_schedule_time'] : '';
        
        
        $fd_product_meta['fd_wc_offer_expiry']                      = ( $postdata['fd_wc_offer_expiry'] == 'fd_wc_offer_expiry_enabled' ) ? $postdata['fd_wc_offer_expiry'] : 'fd_wc_offer_expiry_disabled';
        $fd_product_meta['fd_wc_offer_use_global_expiry']           = ( $postdata['fd_wc_offer_use_global_expiry'] == 'fd_wc_offer_use_global_expiry_enabled' ) ? $postdata['fd_wc_offer_use_global_expiry'] : 'fd_wc_offer_use_global_expiry_disabled';
        $fd_product_meta['fd_wc_offer_expiry_date']                 = ( isset( $postdata['fd_wc_offer_expiry_date'] ) && $postdata['fd_wc_offer_expiry_date'] > 0 ) ? $postdata['fd_wc_offer_expiry_date'] : 0;
        $fd_product_meta['fd_offer_linked_product']                 = isset( $postdata['fd_offer_linked_product'] ) ? $postdata['fd_offer_linked_product'] : '';
        $fd_product_meta['fd_offer_linked_product_variation']       = isset( $postdata['fd_offer_linked_product_variation'] ) ? $postdata['fd_offer_linked_product_variation'] : '';
        $fd_product_meta['fd_wc_offer_voucher_expiry']              = ( $postdata['fd_wc_offer_voucher_expiry'] == 'fd_wc_offer_voucher_expiry_enabled' ) ? $postdata['fd_wc_offer_voucher_expiry'] : 'fd_wc_offer_voucher_expiry_disabled';
        $fd_product_meta['fd_wc_offer_voucher_use_global_expiry']   = ( $postdata['fd_wc_offer_voucher_use_global_expiry'] == 'fd_wc_offer_voucher_use_global_expiry_enabled' ) ? $postdata['fd_wc_offer_voucher_use_global_expiry'] : 'fd_wc_offer_voucher_use_global_expiry_disabled';
        $fd_product_meta['fd_wc_offer_voucher_expiry_date']         = ( isset( $postdata['fd_wc_offer_voucher_expiry_date'] ) && $postdata['fd_wc_offer_voucher_expiry_date'] > 0 ) ? $postdata['fd_wc_offer_voucher_expiry_date'] : 0;
        $fd_product_meta['fd_product_edit_note']         = ( isset( $postdata['fd_product_edit_note'] ) ) ? $postdata['fd_product_edit_note'] : '';
        $fd_product_meta['fd_product_proof_of_stock']         = ( isset( $postdata['fd_product_proof_of_stock'] ) ) ? $postdata['fd_product_proof_of_stock'] : '';
        $fd_product_meta['fd_product_video']         = ( isset( $postdata['fd_product_video'] ) ) ? $postdata['fd_product_video'] : '';

        $fd_product_meta['fd_wc_offer_savings']        =  0;
         if(isset( $postdata['_regular_price'] ) && isset( $postdata['_sale_price'] ) ){
            $fd_product_meta['fd_wc_offer_savings'] = ($postdata['_sale_price']/$postdata['_regular_price'])*100;
         } 

        if( count( $fd_product_meta ) > 0 ){
            $product = wc_get_product( $product_id );
            
            foreach( $fd_product_meta as $meta_field_key => $meta_field_value ){

                if ( ! empty( $fd_product_meta[$meta_field_key] ) ) {
                    update_post_meta( $product_id, $meta_field_key, esc_attr( $meta_field_value ) );
                }

                // $product->update_meta_data( $meta_field_key,  esc_attr( $meta_field_value ) );

            }

            $product->save();
        }



        // if ( ! empty( $postdata['fd_product_edit_note'] ) ) {
        //     update_post_meta( $product_id, 'fd_product_edit_note', $postdata['fd_product_edit_note'] );
        // }

    }


// public function show_vendor_product_extra_fields_edit_page($post, $post_id){
// $fd_product_edit_note = get_post_meta( $post_id, 'fd_product_edit_note', true );

// $fields = '';
// $fields.='
// <div class="dokan-form-group">
// <label>Edit Note <span style = "color:red">Please describe why do you want to edit it*</span></label>
// <textarea class="dokan-form-control" name="fd_product_edit_note" placeholder="Edit Note" required >'.$fd_product_edit_note.'</textarea>
// </div>
// <div class="dokan-form-group">
// <label>Proof Of Stock <span style = "color:red">Please attach any document as a proof of stock*</span></label>
// <input type= "file" class="dokan-form-control" name="fd_product_proof_of_stock"/>
// </div>

// ';
// echo $fields;
// echo require_once ( fdscf_path . 'templates/fd-html-wc-offer-product-data-tab_vendor.php' ); 
// }

// Add extra field in seller settings
public function show_vendor_product_extra_fields_edit_page($post, $post_id){
    $fd_product_edit_note = get_post_meta( $post_id, 'fd_product_edit_note', true );
    $dokan_seller_fd_product_proof_of_stock_attachment_id = get_post_meta( $post_id, 'fd_product_proof_of_stock', true );
    $dokan_seller_fd_product_proof_of_stock  = wp_get_attachment_url($dokan_seller_fd_product_proof_of_stock_attachment_id);
    $fd_product_video_attachment_id = get_post_meta( $post_id, 'fd_product_video', true );
    $dokan_seller_fd_product  = wp_get_attachment_url($fd_product_video_attachment_id);
    
    $fd_wc_offer_savings = get_post_meta( $post_id, 'fd_wc_offer_savings', true );

?>
 <div class="gregcustom dokan-form-group">
        <h1><?php echo $fd_wc_offer_savings?></h1>
        <label class="dokan-w3 dokan-control-label" for="">
        Edit Note <span style = "color:red">Please describe why do you want to edit it*</span>
        </label>
        <div class="dokan-w5">
            <textarea class="dokan-form-control" name="fd_product_edit_note" placeholder="Edit Note" required ><?php  echo $fd_product_edit_note?></textarea>
        </div>
    </div>

    <div class="gregcustom dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="Identity Doc">
            <?php _e( 'Proof Of Stock', 'dokan' ); ?>
        </label>
        <div class="dokan-w5">
            <input type="hidden" class="dokan-form-control input-md valid" name="fd_product_proof_of_stock" id="fd_product_proof_of_stock" value = "<?php echo $dokan_seller_fd_product_proof_of_stock_attachment_id?>"/>
            <div class="gravatar-button-area">
                <a href="#" data-input-name="fd_product_proof_of_stock" class="dokan-btn dokan-btn-default fd_upload_btn"><i class="fa fa-cloud-upload"></i> Upload Proof Of Stock</a>
            </div>
            <small class = "green" id = "proof_success_msg"></small>
        </div>
        <?php if($dokan_seller_fd_product_proof_of_stock != ""){?>
        <a href="<?php echo $dokan_seller_fd_product_proof_of_stock; ?>">Download Attachment</a>
        <?php
        }
        ?>    
    </div>


    <div class="gregcustom dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="Identity Doc">
            <?php _e( 'Upload Video', 'dokan' ); ?>
        </label>
        <div class="dokan-w5">
            <input type="hidden" class="dokan-form-control input-md valid" name="fd_product_video" id="fd_product_video" value = "<?php echo $fd_product_video_attachment_id?>"/>
            <div class="gravatar-button-area">
                <a href="#" data-input-name="fd_product_video" class="dokan-btn dokan-btn-default fd_upload_btn"><i class="fa fa-cloud-upload"></i> Upload Video</a>
            </div>
            <small class = "green" id = "video_success_msg"></small>
        </div>
        <?php if($dokan_seller_fd_product != ""){?>
        <a href="<?php echo $dokan_seller_fd_product; ?>">Download Video</a>
        <?php
        }
        ?>    
    </div>

    <br>
    <?php
    require_once ( fdscf_path . 'templates/fd-html-wc-offer-product-data-tab_vendor.php' ); 

}



}//class
new FD_Vendor_Product_Controller();

?>