<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Vendor_Product_Controller{
    public function __construct(){


        /* Add Custom Product Types to Vednor create new product dropdown */
        add_filter( 'dokan_product_types', array( $this, 'add_custom_product_type' ), 9999);

        //template for extras in edit product template
        // add_action( 'dokan_product_edit_after_options',array($this,'show_vendor_product_extra_fields_edit_page'),9999 );

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

    
    //adding extra fields in add  product of vendor
    // public function vendorProductExtraFields(){
    //     $fields = '';
    //     $fields.='
    //     <div class="dokan-form-group">
    //     <label>Edit Note <span style = "color:red">Please describe why do you want to edit it*</span></label>
    //     <textarea class="dokan-form-control" name="fd_product_edit_note" placeholder="Edit Note" required ></textarea>
    //     </div>
    //     <div class="dokan-form-group">
    //     <label>Proof Of Stock <span style = "color:red">Please attach any document as a proof of stock*</span></label>
    //     <input type= "file" class="dokan-form-control" name="fd_product_proof_of_stock"/>
    //     </div>

    //     ';
    //     echo $fields;
    // }


    //svave extra fields from vendor product
    public function save_vendor_product_extra_fields($product_id, $postdata){
        if ( ! dokan_is_user_seller( get_current_user_id() ) ) {
            return;
        }

        if ( ! empty( $postdata['fd_product_edit_note'] ) ) {
            update_post_meta( $product_id, 'fd_product_edit_note', $postdata['fd_product_edit_note'] );
        }

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

}


}
new FD_Vendor_Product_Controller();

?>