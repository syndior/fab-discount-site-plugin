<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Vendor_Product_Controller{
    public function __construct(){

        /**
         * Create/Update Offer Products when vendor/admin creates of updates a product
         */
        add_action( 'dokan_new_product_added', array($this,'create_fd_offer_product'), 1, 2 );
        add_action( 'dokan_product_updated', array($this,'update_fd_offer_product'), 9999, 2 );

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
        unset( $product_types['grouped'] );
        $product_types['simple'] =  'Simple Offer Product';
        $product_types['variable'] =  'Offer Variable Product';
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

    }


    public function show_vendor_product_extra_fields_edit_page($post, $post_id){
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


    /**
     * Create/Update FD Offers when vendor / admin creates or updates a product
     */
    public function create_fd_offer_product( $product_id, $product_data )
    {
        $original_product = wc_get_product($product_id);

        if( $original_product->meta_exists('fd_offer_product_id') ){
            $fd_product_id  = $original_product->get_meta( 'fd_offer_product_id' );
            $fd_product_id  = absint( $fd_product_id );
            $fd_product     = wc_get_product( $fd_product_id );

            if( $fd_product->meta_exists('original_product_id') && $fd_product->get_meta( 'original_product_id' ) ==  $original_product->get_id()){
                return;
                /**
                 * Return early if linked offer already exists and this hook is being fired multiple times
                 */
            }
        }

        $args = array(
            'type'                  => $original_product->get_type(),
            'name'                  => $original_product->get_name(),
            'description'           => $original_product->get_description(),
            'short_description'     => $original_product->get_short_description(),
            'status'                => $original_product->get_status(),
            'visibility'            => $original_product->get_catalog_visibility(),
            'featured'              => $original_product->get_featured(),
            'virtual'               => $original_product->get_virtual(),
            'regular_price'         => $original_product->get_regular_price(),
            'sale_price'            => $original_product->get_sale_price(),
            'sale_from'             => $original_product->get_date_on_sale_from(),
            'sale_to'               => $original_product->get_date_on_sale_to(),
            'downloadable'          => $original_product->get_downloadable(),
            'downloads'             => $original_product->get_downloads(),
            'download_limit'        => $original_product->get_download_limit(),
            'download_expiry'       => $original_product->get_download_expiry(),
            'tax_status'            => $original_product->get_tax_status(),
            'tax_class'             => $original_product->get_tax_class(),
            'sku'                   => $original_product->get_sku(),
            'manage_stock'          => $original_product->get_manage_stock(),
            'stock_status'          => $original_product->get_stock_status(),
            'stock_qty'             => $original_product->get_stock_status(),
            'backorders'            => $original_product->get_backorders(),
            'sold_individually'     => $original_product->get_sold_individually(),
            'weight'                => $original_product->get_weight(),
            'length'                => $original_product->get_length(),
            'width'                 => $original_product->get_width(),
            'height'                => $original_product->get_height(),
            'shipping_class_id'     => $original_product->get_shipping_class_id(),
            'upsells'               => $original_product->get_upsell_ids(),
            'cross_sells'           => $original_product->get_cross_sell_ids(),
            'attributes'            => $original_product->get_attributes(),
            'default_attributes'    => $original_product->get_default_attributes(),
            'reviews'               => $original_product->get_reviews_allowed(),
            'note'                  => $original_product->get_purchase_note(),
            'menu_order'            => $original_product->get_menu_order(),
            'category_ids'          => $original_product->get_category_ids(),
            'tag_ids'               => $original_product->get_tag_ids(),
            'image_id'              => $original_product->get_image_id(),
            'gallery_ids'           => $original_product->get_gallery_image_ids(),
        );

        $fd_product_id = $this->create_product($args);
        if( $fd_product_id !== false ){
            $fd_product = wc_get_product( $fd_product_id );

            /* Name fix for fd product */
            $fd_product->set_name($original_product->get_name());

            /*Link original and FD product*/
            update_post_meta( $original_product->get_id(), 'fd_offer_product_id', $fd_product->get_id() );
            update_post_meta( $fd_product->get_id(), 'original_product_id', $original_product->get_id() );

            // $original_product->set_meta_data( array( 'fd_offer_product_id' => $fd_product->get_id() ) );
            // $fd_product->set_meta_data( array( 'original_product_id' => $original_product->get_id() ) );

        }

        
    }
    
    public function update_fd_offer_product( $product_id, $product_data )
    {
        $original_product = wc_get_product($product_id);
        if( $original_product->meta_exists('fd_offer_product_id') == true ){
            $fd_product_id = $original_product->get_meta( 'fd_offer_product_id' );
            $fd_product_id = absint( $fd_product_id );
            $fd_product = wc_get_product( $fd_product_id );

            if( $fd_product !== null && $fd_product !== false ){

                // if(  $original_product->get_type() === 'variable' ){
                //     $fd_product->set_type( 'fd_wc_offer_variable' );
                // }elseif( $original_product->get_type() === 'simple' ){
                //     $fd_product->set_type( 'fd_wc_offer' );
                // }else{
                //     $fd_product->set_type( 'fd_wc_offer' ); //default
                // }

                // var_dump( $fd_product->set_name( 'Something Random' ) );
                // die();
                // // $fd_product->set_name( 'Something Random' );

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
                $fd_product->set_shipping_class_id( $original_product->get_shipping_class_id() );
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
            }

        }

        // $args = array(
        //     'type'                  => $original_product->get_type(),
        //     'name'                  => $original_product->get_name(),
        //     'slug'                  => $original_product->get_slug(),
        //     'description'           => $original_product->get_description(),
        //     'short_description'     => $original_product->get_short_description(),
        //     'status'                => $original_product->get_status(),
        //     'visibility'            => $original_product->get_catalog_visibility(),
        //     'featured'              => $original_product->get_featured(),
        //     'virtual'               => $original_product->get_virtual(),
        //     'regular_price'         => $original_product->get_regular_price(),
        //     'sale_price'            => $original_product->get_sale_price(),
        //     'sale_from'             => $original_product->get_date_on_sale_from(),
        //     'sale_to'               => $original_product->get_date_on_sale_to(),
        //     'downloadable'          => $original_product->get_downloadable(),
        //     'downloads'             => $original_product->get_downloads(),
        //     'download_limit'        => $original_product->get_download_limit(),
        //     'download_expiry'       => $original_product->get_download_expiry(),
        //     'tax_status'            => $original_product->get_tax_status(),
        //     'tax_class'             => $original_product->get_tax_class(),
        //     'sku'                   => $original_product->get_sku(),
        //     'manage_stock'          => $original_product->get_manage_stock(),
        //     'stock_status'          => $original_product->get_stock_status(),
        //     'stock_qty'             => $original_product->get_stock_status(),
        //     'backorders'            => $original_product->get_backorders(),
        //     'sold_individually'     => $original_product->get_sold_individually(),
        //     'weight'                => $original_product->get_weight(),
        //     'length'                => $original_product->get_length(),
        //     'width'                 => $original_product->get_width(),
        //     'height'                => $original_product->get_height(),
        //     'shipping_class_id'     => $original_product->get_shipping_class_id(),
        //     'upsells'               => $original_product->get_upsell_ids(),
        //     'cross_sells'           => $original_product->get_cross_sell_ids(),
        //     'attributes'            => $original_product->get_attributes(),
        //     'default_attributes'    => $original_product->get_default_attributes(),
        //     'reviews'               => $original_product->get_reviews_allowed(),
        //     'note'                  => $original_product->get_purchase_note(),
        //     'menu_order'            => $original_product->get_menu_order(),
        //     'category_ids'          => $original_product->get_category_ids(),
        //     'tag_ids'               => $original_product->get_tag_ids(),
        //     'image_id'              => $original_product->get_image_id(),
        //     'gallery_ids'           => $original_product->get_gallery_image_ids(),
        // );

        // $this->create_product($args);

        // $post_id = wp_insert_post( array(
        //     'post_title' => 'Great product 2',
        //     'post_content' => $this->varDumpToString( $original_product->get_children() ) .'<br>'. $this->varDumpToString( $original_product->get_attributes() ).'<br>'. $this->varDumpToString( $original_product->get_default_attributes() ),
        //     'post_status' => 'publish',
        //     'post_type' => "product",
        // ) );
        // wp_set_object_terms( $post_id, 'simple', 'product_type' );


        // $this->fd_offer_create_update_handler( $product_id , $original_product, true );
    }


    /**
     * Helper Function: Creates a WC Product
     */

    public function create_product( $args )
    {

        if( ! method_exists( $this, 'wc_get_product_object_type') && ! method_exists( $this,'wc_prepare_product_attributes') ){
            return false;
        }
    
        // Get an empty instance of the product object (defining it's type)
        $product =  $this->wc_get_product_object_type( $args['type'] );
        if( ! $product )
            return false;
    
        // Product name (Title) and slug
        $product->set_name( $args['name'] ); // Name (title).
    
        // Description and short description:
        $product->set_description( $args['description'] );
        $product->set_short_description( $args['short_description'] );
    
        // Status ('publish', 'pending', 'draft' or 'trash')
        $product->set_status( isset($args['status']) ? $args['status'] : 'publish' );
    
        // Visibility ('hidden', 'visible', 'search' or 'catalog')
        $product->set_catalog_visibility( isset($args['visibility']) ? $args['visibility'] : 'visible' );
    
        // Featured (boolean)
        $product->set_featured(  isset($args['featured']) ? $args['featured'] : false );
    
        // Virtual (boolean)
        $product->set_virtual( isset($args['virtual']) ? $args['virtual'] : false );
    
        // Prices
        $product->set_regular_price( $args['regular_price'] );
        $product->set_sale_price( isset( $args['sale_price'] ) ? $args['sale_price'] : '' );
        $product->set_price( isset( $args['sale_price'] ) ? $args['sale_price'] :  $args['regular_price'] );
        if( isset( $args['sale_price'] ) ){
            $product->set_date_on_sale_from( isset( $args['sale_from'] ) ? $args['sale_from'] : '' );
            $product->set_date_on_sale_to( isset( $args['sale_to'] ) ? $args['sale_to'] : '' );
        }
    
        // Downloadable (boolean)
        $product->set_downloadable(  isset($args['downloadable']) ? $args['downloadable'] : false );
        if( isset($args['downloadable']) && $args['downloadable'] ) {
            $product->set_downloads(  isset($args['downloads']) ? $args['downloads'] : array() );
            $product->set_download_limit(  isset($args['download_limit']) ? $args['download_limit'] : '-1' );
            $product->set_download_expiry(  isset($args['download_expiry']) ? $args['download_expiry'] : '-1' );
        }
    
        // Taxes
        if ( get_option( 'woocommerce_calc_taxes' ) === 'yes' ) {
            $product->set_tax_status(  isset($args['tax_status']) ? $args['tax_status'] : 'taxable' );
            $product->set_tax_class(  isset($args['tax_class']) ? $args['tax_class'] : '' );
        }
    
        // SKU and Stock (Not a virtual product)
        if( isset($args['virtual']) && ! $args['virtual'] ) {
            $product->set_sku( isset( $args['sku'] ) ? $args['sku'] : '' );
            $product->set_manage_stock( isset( $args['manage_stock'] ) ? $args['manage_stock'] : false );
            $product->set_stock_status( isset( $args['stock_status'] ) ? $args['stock_status'] : 'instock' );
            if( isset( $args['manage_stock'] ) && $args['manage_stock'] ) {
                $product->set_stock_status( $args['stock_qty'] );
                $product->set_backorders( isset( $args['backorders'] ) ? $args['backorders'] : 'no' ); // 'yes', 'no' or 'notify'
            }
        }
    
        // Sold Individually
        $product->set_sold_individually( isset( $args['sold_individually'] ) ? $args['sold_individually'] : false );
    
        // Weight, dimensions and shipping class
        $product->set_weight( isset( $args['weight'] ) ? $args['weight'] : '' );
        $product->set_length( isset( $args['length'] ) ? $args['length'] : '' );
        $product->set_width( isset(  $args['width'] ) ?  $args['width']  : '' );
        $product->set_height( isset( $args['height'] ) ? $args['height'] : '' );
        if( isset( $args['shipping_class_id'] ) )
            $product->set_shipping_class_id( $args['shipping_class_id'] );
    
        // Upsell and Cross sell (IDs)
        $product->set_upsell_ids( isset( $args['upsells'] ) ? $args['upsells'] : '' );
        $product->set_cross_sell_ids( isset( $args['cross_sells'] ) ? $args['upsells'] : '' );
    
        // Attributes et default attributes
        if( isset( $args['attributes'] ) )
            // $product->set_attributes(  $this->wc_prepare_product_attributes($args['attributes']) );
            $product->set_attributes( $args['attributes'] );
        if( isset( $args['default_attributes'] ) )
            $product->set_default_attributes( $args['default_attributes'] ); // Needs a special formatting
    
        // Reviews, purchase note and menu order
        $product->set_reviews_allowed( isset( $args['reviews'] ) ? $args['reviews'] : false );
        $product->set_purchase_note( isset( $args['note'] ) ? $args['note'] : '' );
        if( isset( $args['menu_order'] ) )
            $product->set_menu_order( $args['menu_order'] );
    
        // Product categories and Tags
        if( isset( $args['category_ids'] ) )
            $product->set_category_ids( $args['category_ids'] );
        if( isset( $args['tag_ids'] ) )
            $product->set_tag_ids( $args['tag_ids'] );
    
    
        // Images and Gallery
        $product->set_image_id( isset( $args['image_id'] ) ? $args['image_id'] : "" );
        $product->set_gallery_image_ids( isset( $args['gallery_ids'] ) ? $args['gallery_ids'] : array() );
    
        ## --- SAVE PRODUCT --- ##
        $product_id = $product->save();
    
        return $product_id;
    }

    // Utility function that returns the correct product object instance
    public function wc_get_product_object_type( $type )
    {

        // Get an instance of the WC_Product object (depending on his type)
        if( isset($type) && $type === 'variable' ){
            $product = new WC_Product_FD_Offer_Variable();
        }elseif( isset($type) && $type === 'simple' ){
            $product = new WC_Product_FD_Offer();
        }else {
            $product = new WC_Product_FD_Offer(); // "FD_Offer" By default
        }

        if( ! is_a( $product, 'WC_Product' ) )
            return false;
        else
            return $product;
    }

    // Utility function that prepare product attributes before saving
    public function wc_prepare_product_attributes( $attributes ){
        global $woocommerce;
        $attribute_objects = array();

        foreach( $attributes as $attr ){
            $attribute = new WC_Product_Attribute();
            $attribute->set_id( $attr->get_id() );
            $attribute->set_name( $attr->get_name() );
            $attribute->set_options( $attr->get_options() );
            $attribute->set_position( $attr->get_position() );
            $attribute->set_visible( $attr->get_visible() );
            $attribute->set_variation( $attr->get_variation() );

            $attribute_objects[] = $attribute;
        }
        return $attribute_objects;
    }


    /**
     * Helper function
     */
    public function varDumpToString($var) {
        ob_start();
        var_dump($var);
        $result = ob_get_clean();
        return $result;
     }

}//class
new FD_Vendor_Product_Controller();
?>