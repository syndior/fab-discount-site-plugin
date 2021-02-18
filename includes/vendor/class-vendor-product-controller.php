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
        

        $fd_product_meta = array();

        $fd_product_meta['fd_wc_corner_banner']                     = ( $_POST['fd_wc_corner_banner'] == 'fd_wc_corner_banner_enabled' ) ? $_POST['fd_wc_corner_banner'] : 'fd_wc_corner_banner_disabled';
        $fd_product_meta['fd_wc_corner_banner_title']               = ( isset( $_POST['fd_wc_corner_banner_title'] ) ) ? $_POST['fd_wc_corner_banner_title'] : '';
        $fd_product_meta['fd_wc_corner_banner_headind']             = ( isset( $_POST['fd_wc_corner_banner_headind'] ) ) ? $_POST['fd_wc_corner_banner_headind'] : '';
        
        //scheduling 
        $fd_product_meta['fd_wc_offer_schedule']                     = ( $_POST['fd_wc_offer_schedule'] == 'enabled' ) ? $_POST['fd_wc_offer_schedule'] : 'disabled';
        $fd_product_meta['fd_wc_offer_schedule_date']                     = ( isset( $_POST['fd_wc_offer_schedule_date'] ) ) ? $_POST['fd_wc_offer_schedule_date'] : '';
        $fd_product_meta['fd_wc_offer_schedule_time']                     = ( isset( $_POST['fd_wc_offer_schedule_time'] ) ) ? $_POST['fd_wc_offer_schedule_time'] : '';
        
        
        $fd_product_meta['fd_wc_offer_expiry']                      = ( $_POST['fd_wc_offer_expiry'] == 'fd_wc_offer_expiry_enabled' ) ? $_POST['fd_wc_offer_expiry'] : 'fd_wc_offer_expiry_disabled';
        $fd_product_meta['fd_wc_offer_use_global_expiry']           = ( $_POST['fd_wc_offer_use_global_expiry'] == 'fd_wc_offer_use_global_expiry_enabled' ) ? $_POST['fd_wc_offer_use_global_expiry'] : 'fd_wc_offer_use_global_expiry_disabled';
        $fd_product_meta['fd_wc_offer_expiry_date']                 = ( isset( $_POST['fd_wc_offer_expiry_date'] ) && $_POST['fd_wc_offer_expiry_date'] > 0 ) ? $_POST['fd_wc_offer_expiry_date'] : 0;
        $fd_product_meta['fd_offer_linked_product']                 = isset( $_POST['fd_offer_linked_product'] ) ? $_POST['fd_offer_linked_product'] : '';
        $fd_product_meta['fd_offer_linked_product_variation']       = isset( $_POST['fd_offer_linked_product_variation'] ) ? $_POST['fd_offer_linked_product_variation'] : '';
        $fd_product_meta['fd_wc_offer_voucher_expiry']              = ( $_POST['fd_wc_offer_voucher_expiry'] == 'fd_wc_offer_voucher_expiry_enabled' ) ? $_POST['fd_wc_offer_voucher_expiry'] : 'fd_wc_offer_voucher_expiry_disabled';
        $fd_product_meta['fd_wc_offer_voucher_use_global_expiry']   = ( $_POST['fd_wc_offer_voucher_use_global_expiry'] == 'fd_wc_offer_voucher_use_global_expiry_enabled' ) ? $_POST['fd_wc_offer_voucher_use_global_expiry'] : 'fd_wc_offer_voucher_use_global_expiry_disabled';
        $fd_product_meta['fd_wc_offer_voucher_expiry_date']         = ( isset( $_POST['fd_wc_offer_voucher_expiry_date'] ) && $_POST['fd_wc_offer_voucher_expiry_date'] > 0 ) ? $_POST['fd_wc_offer_voucher_expiry_date'] : 0;

        if( count( $fd_product_meta ) > 0 ){
            $product = wc_get_product( $product_id );
            
            foreach( $fd_product_meta as $meta_field_key => $meta_field_value ){

                $product->update_meta_data( $meta_field_key,  esc_attr( $meta_field_value ) );

            }

            $product->save();
        }



        // if ( ! empty( $postdata['fd_product_edit_note'] ) ) {
        //     update_post_meta( $product_id, 'fd_product_edit_note', $postdata['fd_product_edit_note'] );
        // }

    }


function show_vendor_product_extra_fields_edit_page($post, $post_id){
$fd_product_edit_note         = get_post_meta( $post_id, 'fd_product_edit_note', true );

$fields = '';
$fields.='
<div class="dokan-form-group">
<label>Edit Note <span style = "color:red">Please describe why do you want to edit it*</span></label>
<textarea class="dokan-form-control" name="fd_product_edit_note" placeholder="Edit Note" required >'.$fd_product_edit_note.'</textarea>
</div>
<div class="dokan-form-group">
<label>Proof Of Stock <span style = "color:red">Please attach any document as a proof of stock*</span></label>
<input type= "file" class="dokan-form-control" name="fd_product_proof_of_stock"/>
</div>

';
echo $fields;
echo require_once ( fdscf_path . 'templates/fd-html-wc-offer-product-data-tab_vendor.php' ); 
}


}//class
new FD_Vendor_Product_Controller();

?>