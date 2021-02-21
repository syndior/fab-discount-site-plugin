<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Vendor_Product_Controller{
    public function __construct(){

        /**
         * Create/Update Offer Products when vendor/admin creates of updates a product
         */
        //create hooks
        add_action( 'dokan_new_product_added', array($this,'create_fd_offer_product'), 10, 2 );
        // add_action( 'save_post_product', array($this,'wc_create_fd_offer_product'), 10, 3 );
        add_action( 'publish_product', array($this,'wc_create_fd_offer_product'), 30, 2 );
        // add_action( 'edit_post_product', array($this,'wc_create_fd_offer_product'), 10, 2 );
        // add_action( 'transition_post_status', array($this,'wc_create_fd_offer_product'), 10, 3 );
        
        //update hooks
        add_action( 'dokan_product_updated', array($this,'update_fd_offer_product'), 10, 2 );
        // add_action( 'woocommerce_update_product', array($this,'wc_update_fd_offer_product'), 10, 1 ); 

        /* Add Custom Product Types to Vednor create new product dropdown */
        add_filter( 'dokan_product_types', array( $this, 'add_custom_product_type' ), 9999);

        //svave extra fields from vendor product
        add_action( 'dokan_new_product_added', array($this,'save_vendor_product_extra_fields'), 10, 2 );
        add_action( 'dokan_product_updated', array($this,'save_vendor_product_extra_fields'), 10, 2 );
    
        add_action('dokan_product_edit_after_product_tags',array($this,'show_vendor_product_extra_fields_edit_page'),99,2);

        /* Filter vendor product dashboard product query */
        add_filter( 'dokan_product_listing_exclude_type', array($this,'filter_vendor_dashboard_product_query'), 10, 1 );

    }


    /* Add Custom Product Types to Vednor create new product dropdown */
    public function add_custom_product_type( $product_types )
    {
        unset( $product_types['grouped'] );
        $product_types['simple'] =  'Simple Offer Product';
        $product_types['variable'] =  'Variable Offer Product';
        return $product_types;
    }

    //svave extra fields from vendor product
    public function save_vendor_product_extra_fields($product_id, $postdata)
    {
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

    }


    public function show_vendor_product_extra_fields_edit_page($post, $post_id)
    {
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
        echo require_once ( fdscf_path . 'templates/fd-html-wc-offer-product-data-tab-vendor.php' ); 
    }


    /**
     * Dokan Create product hook callback
     */
    public function create_fd_offer_product( $product_id, $product_data )
    {
        if ( ! dokan_is_user_seller( get_current_user_id() ) ) {
            return;
        }
        $this->create_update_fd_product( $product_id );
    }
    
    
    /**
     * Dokan Create product hook callback
     */
    public function wc_create_fd_offer_product( $post_ID, $post )
    {
        $product = wc_get_product( $post->ID );
            
        if( $product->get_type() === 'simple' && $product->get_type() === 'variable' ){
            $this->create_update_fd_product( $post->ID );
        }
    }

    /**
     * Dokan Update product hook callback
     */
    public function update_fd_offer_product( $product_id, $product_data )
    {
        if ( ! dokan_is_user_seller( get_current_user_id() ) ) {
            return;
        }
        $this->create_update_fd_product( $product_id );
    }
    
    
    /**
     * Woocommerece Update product hook callback
     */
    public function wc_update_fd_offer_product( $product_id )
    {
        $this->create_update_fd_product( $product_id );
    }
    


    /**
     * Create / Update FD Offers when vendor / admin creates or updates a product
     */
    public function create_update_fd_product( int $original_product_id = 0 )
    {

        if( $original_product_id > 0 ){

            $original_product = wc_get_product($original_product_id);

            if( !( $original_product->get_type() == 'simple' || $original_product->get_type() == 'variable' ) ){
                return;
            }

            ob_start();
            var_dump($original_product->get_id());
            var_dump($original_product->get_type());
            var_dump($original_product->meta_exists('fd_offer_product_id'));
            var_dump($original_product->get_meta( 'fd_offer_product_id' ));
            $log = ob_get_clean();
            error_log( $log );
            

            if( $original_product->meta_exists('fd_offer_product_id') == false ){
                $update = false;
            }elseif( $original_product->meta_exists('fd_offer_product_id') == true ){
                $update = true;

                $fd_product_id  = $original_product->get_meta( 'fd_offer_product_id' );
                $fd_product_id  = absint( $fd_product_id );
                $fd_product     = wc_get_product( $fd_product_id );

                if( $fd_product == false ){
                    return;
                }
                if( !($fd_product->meta_exists('fd_original_product_id') && $fd_product->get_meta( 'fd_original_product_id' ) ==  $original_product->get_id() ) ){
                    return;
                }
            }
            
            /**
             * Create / Update FD Offer Product
             */
            if( $update == false ){

                if( $original_product->get_type() === 'variable' ){
                    $fd_product = new WC_Product_FD_Offer_Variable();
                }elseif( $original_product->get_type() === 'simple' ){
                    $fd_product = new WC_Product_FD_Offer();
                }else {
                    $fd_product = new WC_Product_FD_Offer(); // "FD_Offer" By default
                }

            }elseif( $update == true ){

                if(  $original_product->get_type() === 'variable' ){
                    $new_product_type = 'fd_wc_offer_variable';
                }elseif( $original_product->get_type() === 'simple' ){
                    $new_product_type = 'fd_wc_offer';
                }else{
                    $new_product_type = 'fd_wc_offer';  //default
                }
    
                wp_set_object_terms( $fd_product->get_id(), $new_product_type, 'product_type' );
            }else{
                return;
            }

            $fd_product->set_name( $original_product->get_name() );
            $fd_product->set_description( $original_product->get_description() );
            $fd_product->set_short_description( $original_product->get_short_description() );
            $fd_product->set_status( $original_product->get_status() );
            $fd_product->set_catalog_visibility( $original_product->get_catalog_visibility() );
            $fd_product->set_featured( $original_product->get_featured() );
            $fd_product->set_virtual( $original_product->get_virtual() );
            $fd_product->set_regular_price( $original_product->get_regular_price() );
            $fd_product->set_sale_price( $original_product->get_sale_price() );
            $fd_product->set_date_on_sale_from( $original_product->get_date_on_sale_from() );
            $fd_product->set_date_on_sale_to( $original_product->get_date_on_sale_to() );
            $fd_product->set_downloadable( $original_product->get_downloadable() );
            $fd_product->set_downloads( $original_product->get_downloads() );
            $fd_product->set_download_limit( $original_product->get_download_limit() );
            $fd_product->set_download_expiry( $original_product->get_download_expiry() );
            $fd_product->set_tax_status( $original_product->get_tax_status() );
            $fd_product->set_tax_class( $original_product->get_tax_class() );
            $fd_product->set_sku( $original_product->get_sku() );
            $fd_product->set_manage_stock( $original_product->get_manage_stock() );
            $fd_product->set_stock_status( $original_product->get_stock_status() );
            $fd_product->set_backorders( $original_product->get_backorders() );
            $fd_product->set_sold_individually( $original_product->get_sold_individually() );
            $fd_product->set_weight( $original_product->get_weight() );
            $fd_product->set_length( $original_product->get_length() );
            $fd_product->set_width( $original_product->get_width() );
            $fd_product->set_height( $original_product->get_height() );
            $fd_product->set_upsell_ids( $original_product->get_upsell_ids() );
            $fd_product->set_cross_sell_ids( $original_product->get_cross_sell_ids() );
            $fd_product->set_attributes( $original_product->get_attributes() );
            $fd_product->set_default_attributes( $original_product->get_default_attributes() );
            $fd_product->set_reviews_allowed( $original_product->get_reviews_allowed() );
            $fd_product->set_purchase_note( $original_product->get_purchase_note() );
            $fd_product->set_menu_order( $original_product->get_menu_order() );
            $fd_product->set_category_ids( $original_product->get_category_ids() );
            $fd_product->set_tag_ids( $original_product->get_tag_ids() );
            $fd_product->set_image_id( $original_product->get_image_id() );
            $fd_product->set_gallery_image_ids( $original_product->get_gallery_image_ids() );

            $fd_product_id = $fd_product->save();

            if( $fd_product_id !== false ){

                /*Link original and FD product*/
                update_post_meta( $original_product->get_id(), 'fd_offer_product_id', $fd_product->get_id() );
                update_post_meta( $fd_product->get_id(), 'fd_original_product_id', $original_product->get_id() );

            }

            /**
             * Set prodct variation
             */ 
            $variation_ids = $original_product->get_children();
            if(  $original_product->get_type() === 'variable' && !empty( $variation_ids ) ){
                foreach ($variation_ids as $id) {
                    $original_variation = wc_get_product($id);
                    if( $original_variation !== null && $original_variation !== false ){

                        if( $original_variation->meta_exists('fd_offer_variation_id') ){
                            $fd_variation_id = $original_variation->get_meta( 'fd_offer_variation_id' );
                            $fd_variation_id = absint( $fd_variation_id );
                            $fd_variation = wc_get_product( $fd_variation_id );
                        }else{
                            $fd_variation = new WC_Product_Variation();
                            $fd_variation->set_parent_id($fd_product->get_id());
                        }

                        $fd_variation->set_status( $original_variation->get_status() );
                        $fd_variation->set_virtual( $original_variation->get_virtual() );
                        $fd_variation->set_downloadable( $original_variation->get_downloadable() );
                        $fd_variation->set_regular_price( $original_variation->get_regular_price() );
                        $fd_variation->set_sale_price( $original_variation->get_sale_price() );
                        $fd_variation->set_date_on_sale_from( $original_variation->get_date_on_sale_from() );
                        $fd_variation->set_date_on_sale_to( $original_variation->get_date_on_sale_to() );
                        $fd_variation->set_downloads( $original_variation->get_downloads() );
                        $fd_variation->set_download_limit( $original_variation->get_download_limit() );
                        $fd_variation->set_download_expiry( $original_variation->get_download_expiry() );
                        $fd_variation->set_sku( $original_variation->get_sku() );
                        $fd_variation->set_manage_stock( $original_variation->get_manage_stock() );
                        $fd_variation->set_stock_status( $original_variation->get_stock_status() );
                        $fd_variation->set_backorders( $original_variation->get_backorders() );
                        $fd_variation->set_weight( $original_variation->get_weight() );
                        $fd_variation->set_length( $original_variation->get_length() );
                        $fd_variation->set_width( $original_variation->get_width() );
                        $fd_variation->set_height( $original_variation->get_height() );
                        $fd_variation->set_description( $original_variation->get_description() );
                        $fd_variation->set_image_id( $original_variation->get_image_id() );
                        $fd_variation->set_attributes( $original_variation->get_attributes() );
                        
                        $fd_variation_id = $fd_variation->save();

                        update_post_meta( $original_variation->get_variation_id(), 'fd_offer_variation_id', $fd_variation_id );
                        update_post_meta( $fd_variation_id, 'fd_original_variation_id', $original_variation->get_variation_id() );
                    }
                }
            }

        }
    }

    public function filter_vendor_dashboard_product_query( $product_type_array  )
    {
        $product_type_array = array_merge( $product_type_array, array( 'fd_wc_offer', 'fd_wc_offer_variable' ) );
        return $product_type_array;
    }

}//class
new FD_Vendor_Product_Controller();
?>