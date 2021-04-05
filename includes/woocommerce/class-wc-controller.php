<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Woocommerce_Controller
{
    public function __construct()
    {
        /*updating visibility of product*/
        add_filter('woocommerce_product_get_catalog_visibility',array( $this, 'fd_update_product_visibility' ),10,2);

        /* adds the custom product type's label in the products type dropdown */
        add_filter( 'product_type_selector', array( $this, 'add_product_type_filter' ), 10 );

        /* adds our custom product type extended class to be used with our product type*/
        add_filter( 'woocommerce_product_class', array( $this, 'add_woocommerce_product_class' ), 99, 2 );
        
        /* add custom product product data tab */
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'modify_woocommerce_product_data_tabs' ), 9999, 1 );

        /* loads custom product data tab markup */
        add_action( 'woocommerce_product_data_panels', array( $this, 'add_woocommerce_product_data_panels'), 9999 );
        
        /* add custom meta boxes */
        add_action( 'add_meta_boxes', array( $this, 'add_custom_meta_wc_meta_box') );

        /* general tab fix */
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_woocommerce_general_tab_fix_html'));
        
        /* inserts JS that hides and show the wc default product tabs on our custom product type*/
        add_action( 'admin_footer', array( $this, 'modify_woocommerce_tabs_visibility') );
        
        /* saves our custom fields with our custom product type*/
        add_action( 'woocommerce_process_product_meta', array( $this, 'process_product_meta') );

        /* Save Custom Product/Offer Front-end Tabs Content*/
        add_action( 'save_post', array( $this, 'process_custom_product_tabs_content'), 10, 3 );

        /* adds back the add to cart buttons and product summary sections */
        add_action( 'woocommerce_fd_wc_offer_add_to_cart', array( $this, 'add_to_cart_template_include') );
        
        /* adds custom button after the add to cart button to pay with store credit */
        add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'add_functionality_after_add_to_cart_button'), 10 );

        /* Register new endpoint to use for My Account pages */
        add_action( 'woocommerce_before_single_product', array( $this, 'proccess_custom_add_to_cart_method' ) );

        /* inlude page js that logs users product view */
        add_action( 'woocommerce_after_single_product', array( $this, 'enqueue_user_product_view_log_script' ) );

        /* Register new endpoint to use for My Account pages */
        add_action( 'init', array( $this, 'register_custom_page_endpoints' ) );

        /* Add a new query var for custom my account pages */
        add_filter( 'query_vars', array( $this, 'add_custom_query_vars_for_my_acccuont_pages' ), 0 );

        /* Hook custom endpoint in  WC my account area*/
        add_filter( 'woocommerce_account_menu_items', array( $this, 'hook_custom_endpoints_with_woocommerce' ) );

        /* load custom markup for the newly added endpoints */
        add_action( 'woocommerce_account_fd-my-vouchers_endpoint', array( $this, 'load_wc_my_vouchers_page_markup' ) );
        add_action( 'woocommerce_account_fd-my-transactions_endpoint', array( $this, 'load_wc_my_transactions_page_markup' ) );
        add_action( 'woocommerce_account_fd-my-wallet_endpoint', array( $this, 'load_wc_my_wallet_page_markup' ) );
        add_action( 'woocommerce_account_fd-my-viewed-items_endpoint', array( $this, 'load_wc_previously_viewed_items_page_markup' ) );
        
        /* ajax handler, returns offer linked variations data*/
        add_action('wp_ajax_fd_wc_get_linked_variations',  array( $this, 'fd_wc_get_linked_variations' ) );

        /* Override WC templates from FD Plugin */
        add_filter( 'woocommerce_locate_template', array( $this, 'fd_load_wc_templates' ), 1, 3 );

        /* Custom Hook: Add in claim offer feater after successfull checkout */
        add_action( 'fdscf_checkout_order_processed_claim_offer', array( $this, 'add_in_claim_offer_feature' ) );
        
        /* Hook custom product tabs, Displayed on the product page */
        add_filter( 'woocommerce_product_tabs', array( $this, 'add_new_product_tabs' ) );
        
        /* add custom total row to show remaining store credit */
        add_filter( 'woocommerce_get_order_item_totals', array( $this, 'add_custom_order_total_rows' ), 10, 2 );
        
        /* add custom total row to show remaining store credit */
        add_action( 'woocommerce_cart_totals_after_order_total', array( $this, 'print_custom_total_rows' ), 10 );
        add_action( 'woocommerce_review_order_after_order_total', array( $this, 'print_custom_total_rows' ), 10 );

        /* add order item meta */
        add_action('woocommerce_add_order_item_meta',  array( $this, 'add_order_item_meta_for_pay_with_store_credit_order' ), 10, 2);
        
        /* modify wallet amount if order is proccessedd */
        add_action('woocommerce_checkout_order_processed',  array( $this, 'modify_wallet_totals' ) );
        
    }

    public function add_product_type_filter( $types )
    {
        $types[ 'fd_wc_offer' ]             = 'FD Offer';

        return $types;
    }

    public function add_woocommerce_product_class( $classname, $product_type )
    {
        if ( $product_type == 'fd_wc_offer' ) {
            $classname = 'WC_Product_FD_Offer';
        }

        return $classname;
    }



    public function modify_woocommerce_product_data_tabs( $original_tabs )
    {
        //enable the general tab for custom product types
        $original_tabs['general']['class'][] = 'show_if_simple';
        $original_tabs['general']['class'][] = 'show_if_fd_wc_offer';

        //hide shipping tab
        $original_tabs['shipping']['class'][] = 'hide_if_fd_wc_offer';

        $fd_wc_offer_tab['fd_wc_offer'] = array(
            'label' => 'FD Offer Options',
            'target' => 'fd_wc_offer_options',
            'class' => 'show_if_fd_wc_offer'
        );

        $tabs = $this->insert_item_at_array_position( 0, $fd_wc_offer_tab, $original_tabs );

        return $tabs;
    }

    public function add_woocommerce_product_data_panels()
    {
        require_once ( fdscf_path . 'templates/fd-html-wc-offer-product-data-tab.php' );
    }


    public function add_woocommerce_general_tab_fix_html()
    {
        echo '<div class="options_group show_if_fd_wc_offer clear"></div>';
    }


    public function modify_woocommerce_tabs_visibility()
    {
        if ('product' != get_post_type()) :
            return;
        endif;
        ?>
        <script type='text/javascript'>
            jQuery(document).ready(function () {
                //for Price tab
                jQuery('.product_data_tabs .general_tab').addClass('show_if_fd_wc_offer').show();
                jQuery('#general_product_data .pricing').addClass('show_if_fd_wc_offer').show();
                

                //for Inventory tab
                jQuery('.inventory_options').addClass('show_if_fd_wc_offer').show();
                jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_fd_wc_offer').show();
                jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_fd_wc_offer').show();
                jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_fd_wc_offer').show();
        </script>
        <?php
    }


    public function process_product_meta( $post_id )
    {
        $fd_product_meta = array();

        $fd_product_meta['fd_wc_corner_banner']                     = ( $_POST['fd_wc_corner_banner'] == 'fd_wc_corner_banner_enabled' ) ? $_POST['fd_wc_corner_banner'] : 'fd_wc_corner_banner_disabled';
        $fd_product_meta['fd_wc_corner_banner_title']               = ( isset( $_POST['fd_wc_corner_banner_title'] ) ) ? $_POST['fd_wc_corner_banner_title'] : '';
        $fd_product_meta['fd_wc_corner_banner_headind']             = ( isset( $_POST['fd_wc_corner_banner_headind'] ) ) ? $_POST['fd_wc_corner_banner_headind'] : '';
        
        //scheduling 
        $fd_product_meta['fd_wc_offer_schedule']                     = ( $_POST['fd_wc_offer_schedule'] == 'enabled' ) ? $_POST['fd_wc_offer_schedule'] : 'disabled';
        $fd_product_meta['fd_wc_offer_schedule_date']                = ( isset( $_POST['fd_wc_offer_schedule_date'] ) ) ? $_POST['fd_wc_offer_schedule_date'] : '';
        $fd_product_meta['fd_wc_offer_schedule_time']                = ( isset( $_POST['fd_wc_offer_schedule_time'] ) ) ? $_POST['fd_wc_offer_schedule_time'] : '';
        
        
        $fd_product_meta['fd_wc_offer_expiry']                      = ( $_POST['fd_wc_offer_expiry'] == 'fd_wc_offer_expiry_enabled' ) ? $_POST['fd_wc_offer_expiry'] : 'fd_wc_offer_expiry_disabled';
        $fd_product_meta['fd_wc_offer_use_global_expiry']           = ( $_POST['fd_wc_offer_use_global_expiry'] == 'fd_wc_offer_use_global_expiry_enabled' ) ? $_POST['fd_wc_offer_use_global_expiry'] : 'fd_wc_offer_use_global_expiry_disabled';
        $fd_product_meta['fd_wc_offer_expiry_date']                 = ( isset( $_POST['fd_wc_offer_expiry_date'] ) && $_POST['fd_wc_offer_expiry_date'] > 0 ) ? $_POST['fd_wc_offer_expiry_date'] : 0;
        $fd_product_meta['fd_offer_linked_product']                 = isset( $_POST['fd_offer_linked_product'] ) ? $_POST['fd_offer_linked_product'] : '';
        $fd_product_meta['fd_offer_linked_product_variation']       = isset( $_POST['fd_offer_linked_product_variation'] ) ? $_POST['fd_offer_linked_product_variation'] : '';
        $fd_product_meta['fd_wc_offer_voucher_expiry']              = ( $_POST['fd_wc_offer_voucher_expiry'] == 'fd_wc_offer_voucher_expiry_enabled' ) ? $_POST['fd_wc_offer_voucher_expiry'] : 'fd_wc_offer_voucher_expiry_disabled';
        $fd_product_meta['fd_wc_offer_voucher_use_global_expiry']   = ( $_POST['fd_wc_offer_voucher_use_global_expiry'] == 'fd_wc_offer_voucher_use_global_expiry_enabled' ) ? $_POST['fd_wc_offer_voucher_use_global_expiry'] : 'fd_wc_offer_voucher_use_global_expiry_disabled';
        $fd_product_meta['fd_wc_offer_voucher_expiry_date']         = ( isset( $_POST['fd_wc_offer_voucher_expiry_date'] ) && $_POST['fd_wc_offer_voucher_expiry_date'] > 0 ) ? $_POST['fd_wc_offer_voucher_expiry_date'] : 0;
        $fd_product_meta['fd_wc_offer_savings']         = 0;



        // Cancellation Policy
        $fd_product_meta['fd_wc_offer_voucher_cancellation_policy']                     =  (isset( $_POST['fd_wc_offer_voucher_cancellation_policy'] ) && $_POST['fd_wc_offer_voucher_cancellation_policy']!="" ) ? $_POST['fd_wc_offer_voucher_cancellation_policy'] : "no policy"; 

        // for delivery cost
        $fd_product_meta['fd_wc_offer_voucher_delivery_cost']                     =  (isset( $_POST['fd_wc_offer_voucher_delivery_cost'] ) && $_POST['fd_wc_offer_voucher_delivery_cost'] !== "" )? $_POST['fd_wc_offer_voucher_delivery_cost'] : 0; 

        // estimated delivery time
        $fd_product_meta['fd_wc_offer_voucher_delivery_time']                     =  (isset( $_POST['fd_wc_offer_voucher_delivery_time'] ) && $_POST['fd_wc_offer_voucher_delivery_time'] !== "" )? $_POST['fd_wc_offer_voucher_delivery_time'] : "2-3 days"; 

        if(isset( $_POST['_regular_price'] ) && isset( $_POST['_sale_price'] ) ){
            $fd_product_meta['fd_wc_offer_savings'] = ($_POST['_sale_price']/$_POST['_regular_price'])*100;
         }
          
         $fd_product_meta['fd_product_edit_note'] = "";

        if( count( $fd_product_meta ) > 0 ){
            $product = wc_get_product( $post_id );
                    
            //if schedule enabled then make visibility of offer/product hidden
            if($_POST['fd_wc_offer_schedule'] == "enabled"){

                $product->set_catalog_visibility('hidden');
                $product->save();                
            
            }//if enabled
        
        
            foreach( $fd_product_meta as $meta_field_key => $meta_field_value ){

                $product->update_meta_data( $meta_field_key,  esc_attr( $meta_field_value ) );

            }
            
            $vendor_id = (int)$product->get_meta('fd_vendor_id');
            if($vendor_id=="" || $vendor_id==0 || $vendor_id==null){
                $product->update_meta_data( 'fd_vendor_id',  esc_attr( get_current_user_id() ) );
            }

            $product->save();
                
        }
    }

    public function add_to_cart_template_include()
    {
        global $post;
        $product = wc_get_product($post->ID);

        if( $product->get_type() == 'fd_wc_offer' ){
            do_action( 'woocommerce_simple_add_to_cart' );
        }
    }


    public function add_functionality_after_add_to_cart_button()
    {
        require_once ( fdscf_path . 'templates/fd-html-after-add-to-cart-custom-options.php' );
    }


    public function proccess_custom_add_to_cart_method()
    {
        /**
         * Handle Pay with store credit request 
         */
        if( isset( $_POST['fd_pay_with_credit'] ) && $_POST['fd_pay_with_credit'] == 'fd_custom_add_to_cart' && isset( $_POST['fd_wp_nonce'] ) ){

            if( wp_verify_nonce( $_POST['fd_wp_nonce'], 'fd_custom_add_to_cart' ) ){

                global $post; 
                $product = wc_get_product( $post->ID );
                if( $product->get_type() == 'fd_wc_offer' ){

                    $user_id = get_current_user_id();
                    $wallet = new FD_Wallet( $user_id );

                    $original_product_id = get_post_meta( $product->get_id(), 'fd_offer_linked_product', true );
                    $original_product = wc_get_product( $original_product_id );

                    WC()->cart->empty_cart();
                    
                    $cart_item_data = array(
                        'pay_with_wallet'   => 'yes',
                        'wallet_balance'    => $wallet->get_balance(),
                    );
                    
                    switch( $original_product->get_type() ){
                        case 'simple':
                            $product_id = $original_product->get_id();
                            $variation_id = 0;
                            $cart_item_data['credit_required'] = $original_product->get_price();
                            break;

                        case 'variable':
                            $product_id     = $original_product->get_id();
                            $variation_id   = get_post_meta( $product->get_id(), 'fd_offer_linked_product_variation', true );
                            $variation      = wc_get_product( $variation_id );
                            $cart_item_data['credit_required'] = $variation->get_price();
                            break;
                    }
                    
                    $qty = isset( $_POST['quantity'] ) ? $_POST['quantity'] : 1;
                    $added_to_cart_status = WC()->cart->add_to_cart(  $product_id, $qty, $variation_id, $variation = array(), $cart_item_data);

                    if( $added_to_cart_status !== false ){

                        $url =  wc_get_cart_url();
                        echo '<script>window.location.replace("'.$url.'");</script>';
            
                    }

                }

            }

        }
    }


    public function enqueue_user_product_view_log_script()
    {
        require_once ( fdscf_path . 'templates/fd-html-porduct-page-end-js-script-enqueue.php' );
    }
    
    
    public function register_custom_page_endpoints()
    {
        add_rewrite_endpoint( 'fd-my-vouchers', EP_ROOT | EP_PAGES );
        add_rewrite_endpoint( 'fd-my-transactions', EP_ROOT | EP_PAGES );
        add_rewrite_endpoint( 'fd-my-wallet', EP_ROOT | EP_PAGES );
        add_rewrite_endpoint( 'fd-my-viewed-items', EP_ROOT | EP_PAGES );
    }
    
    
    public function add_custom_query_vars_for_my_acccuont_pages( $vars )
    {
        $vars[] = 'fd-my-vouchers';
        $vars[] = 'fd-my-transactions';
        $vars[] = 'fd-my-wallet';
        $vars[] = 'fd-my-viewed-items';
        return $vars;
    }
    
    public function hook_custom_endpoints_with_woocommerce( $items )
    {
        $my_vouchers_page['fd-my-vouchers'] = 'My Vouchers';
        $items = $this->insert_item_at_array_position( 1, $my_vouchers_page, $items );

        $my_transactions['fd-my-transactions'] = 'My Transactions';
        $items = $this->insert_item_at_array_position( 2, $my_transactions, $items );

        $my_wallet['fd-my-wallet'] = 'My Wallet';
        $items = $this->insert_item_at_array_position( 3, $my_wallet, $items );
        
        $previously_viewed_items['fd-my-viewed-items'] = 'Previously Viewed Items';
        $items = $this->insert_item_at_array_position( 4, $previously_viewed_items, $items );
        

        return $items;
    }
    
    
    public function load_wc_my_vouchers_page_markup()
    {
        require_once ( fdscf_path . 'templates/fd-html-wc-account-tabs-my-vouchers-page.php' );
    }
    
    public function load_wc_my_transactions_page_markup()
    {
        require_once ( fdscf_path . 'templates/fd-html-wc-account-tabs-my-transactions-page.php' );
    }
    
    public function load_wc_my_wallet_page_markup()
    {
        require_once ( fdscf_path . 'templates/fd-html-wc-account-tabs-my-wallet-page.php' );
    }
    
    public function load_wc_previously_viewed_items_page_markup()
    {
        require_once ( fdscf_path . 'templates/fd-html-wc-account-tabs-my-previouly-viewed-items-page.php' );
    }


    /**
     * Helper function: add custom item at the start of an array
     */

    public static function insert_item_at_array_position( int $insert_position, $custom_item , $original_array)
    {
        $items = array_slice( $original_array, 0, $insert_position, true );
        $items = array_merge( $items, $custom_item );
        $items = array_merge( $items, array_slice( $original_array, $insert_position, null, true ) );

        return $items;
    }

     /**
      * Ajax - returns the variable product variations data
      */
    public function fd_wc_get_linked_variations()
    {
        check_ajax_referer( 'ajax_check', 'security' );

        $response = array(
            'type' => 'error'
        );

        if( isset( $_REQUEST['product_id'] ) ){
            $product_id = $_REQUEST['product_id'];
            $product = wc_get_product( $product_id );
            $variations_ids = $product->get_children();

            $variation_products = array();

            foreach( $variations_ids as $product_id ){
                $variation = wc_get_product( $product_id );

                $option['product_id']           = $variation->get_ID();
                $option['product_title']        = $variation->get_name();

                $variation_products[] = $option;
            }

            if( count( $variation_products ) > 0 ){
                $response['type'] = 'success';
                $response['variations'] = $variation_products;
            }

        }

        wp_send_json_success($response);
        wp_die();
    }

      /**
       * Load custom/override woocommerce templates
       */
    public function fd_load_wc_templates( $template, $template_name, $template_path )
    {
        /**
         * array with custom templates with folder names
         */
        $custom_templates = array(
            'checkout' => 'thankyou.php',
        );

        $wc_template_filename = basename( $template );
        foreach ( $custom_templates as $custom_template_dir => $custom_template_filename ){
            if( $custom_template_filename == $wc_template_filename ){
                
                $template = fdscf_wc_dir . $custom_template_dir .'/'. $custom_template_filename;

            }
        }

        return $template;
    }

    /**
     * Load templates for claim offer feature
     */
    public function add_in_claim_offer_feature( $order_id )
    {
        require_once ( fdscf_path . 'templates/fd-html-wc-claim-offer.php' );
    }


    /**
     * Adds a custom metabox for the custom front-end product/offer tabs 
     */
    public function add_custom_meta_wc_meta_box()
    {
        global $post;
        if( $post->post_type == 'product' ){
            $product = wc_get_product($post->ID);

            if( $product->get_type() == 'fd_wc_offer' ){
                $id             = 'fdscf_product_meta_box';
                $title          = 'FD Offer Options';
                $callback       = array( $this, 'print_meta_box_content');
                $screen         = 'product';
                $context        = 'normal';
                $priority       = 'default';
        
                add_meta_box( $id, $title, $callback, $screen, $context, $priority );
            }
        }
    }

    /**
     * Print HTML for the custom metabox
     */
    public function print_meta_box_content($post)
    {
        if( function_exists('get_field') ){
            $product_tabs = get_field( 'offer_tabs', 'options' );

            if( $product_tabs !== null && !empty($product_tabs)){
                $output = '';
                $output .= '<div class="fd_meta_box_content_wrapper">';
                $counter = 0;
                foreach( $product_tabs as $tab ){
                    if( $tab['tab_status'] == true){

                        $field_id = $tab['tab_id'] .'_'. $post->ID;

                        $field_content = get_post_meta( $post->ID, $field_id, true  );

                        $content    = $field_content;
                        $editor_id  = $field_id;

                        ob_start();
                        wp_editor( $content, $editor_id );
                        $editor     = ob_get_clean();
                        $editor     .= _WP_Editors::enqueue_scripts();
                        $editor     .= _WP_Editors::editor_js();

                        $output .= '<div class="fd_meta_box_item">';
                        $output .= '<label class="" for="'. $field_id .'">';
                        $output .= '<h3>' . $tab['tab_title'] . '</h3>';
                        $output .= '</label>';
                        $output .= $editor;
                        $output .= '</div>';
                    }
                    $counter++;
                }
                $output .= '</div>';

                echo $output;
            }

        }
    }

    /**
     * Save/Process custom tabs wp_editor fields
     */
    public function process_custom_product_tabs_content( $post_id, $post, $update )
    {
        if( function_exists('get_field') ){
            $product_tabs = get_field( 'offer_tabs', 'options' );

            if( $product_tabs !== null && !empty($product_tabs)){

                foreach( $product_tabs as $tab ){
                    
                    $field_id = $tab['tab_id'] .'_'. $post->ID;

                    if( $_POST[$field_id] ){
                        update_post_meta($post_id, $field_id, $_POST[$field_id]);
                    }

                }


            }

        }
    }


    /**
     * Add custom products tabs displayed on the product page. defined in the admin dashboard
     */
    public function add_new_product_tabs( $tabs )
    {
        if( function_exists('get_field') ){
            
            $custom_tabs = array();

            $product_tabs = get_field( 'offer_tabs', 'options' );
            if( $product_tabs !== null && !empty($product_tabs)){
                foreach( $product_tabs as $tab ){
                    if( $tab['tab_status'] == true){
                        
                        $custom_tab_item = array(
                            $tab['tab_id'] => array(
                                'title'     => $tab['tab_title'],
                                'priority'  => 10,
                                'callback' 	=> array( $this, 'custom_product_tab_content_callback' ),
                            ),
                        );

                        $custom_tabs = array_merge( $custom_tabs, $custom_tab_item );
                    }
                }

                if( !empty( $custom_tabs ) ){
                    $tabs = array_merge( $tabs, $custom_tabs );
                }
            }

        }

        return $tabs;
    }

    /**
     * Callback for custom product tabs displayed on the product page
     */
    public function custom_product_tab_content_callback( $key, $product_tab )
    {
        global $post;
        $field_id = $key .'_'. $post->ID;
        $tab_content = get_post_meta( $post->ID , $field_id , true );
        
        echo '<h2>' . $product_tab['title'] . '</h2>';
        echo $tab_content;
    }


    public function fd_update_product_visibility($value,$obj){

        $product_id = $obj->get_id();
        $schdule = get_post_meta($product_id,'fd_wc_offer_schedule',true);
        if($schdule == "enabled"){
            $value = "hidden";
        }else{
            $value = "visible";
        }
        return $value;
        
    }


    public function add_custom_order_total_rows( $total_rows, $order )
    {
        $user_id = get_current_user_id();
        $wallet = new FD_Wallet( $user_id );

        foreach ( $order->get_items() as $item_key => $item ){

            $product = $item['data'];

            if( isset( $cart_item['pay_with_wallet'] ) && $cart_item['pay_with_wallet'] == 'yes' ){

                $credit_used = ( $cart_item['credit_required'] > $cart_item['wallet_balance'] ) ? $cart_item['wallet_balance'] : $cart_item['credit_required'] ;
                $credit_remaining = ( $cart_item['credit_required'] > $cart_item['wallet_balance'] ) ? 0 : ( $cart_item['wallet_balance'] - $cart_item['credit_required'] );

                $total_rows['fd_credit_used'] = array(
                    'label' => 'Credit Used',
                    'value' => wc_price( (float)$credit_used ),
                );

                $total_rows['fd_remaining_credit'] = array(
                    'label' => 'Remaining Credit',
                    'value' => wc_price( (float)$credit_remaining ),
                );
            }

        }

        return $total_rows;
    }


    public function print_custom_total_rows()
    {
        if( !WC()->cart->is_empty() ){

            $user_id = get_current_user_id();
            $wallet = new FD_Wallet( $user_id );

            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ){

                $product = $cart_item['data'];
                if( isset( $cart_item['pay_with_wallet'] ) && $cart_item['pay_with_wallet'] == 'yes' ){

                    $credit_used = ( $cart_item['credit_required'] > $cart_item['wallet_balance'] ) ? $cart_item['wallet_balance'] : $cart_item['credit_required'] ;
                    $credit_remaining = ( $cart_item['credit_required'] > $cart_item['wallet_balance'] ) ? 0 : ( $cart_item['wallet_balance'] - $cart_item['credit_required'] );

                    ?>
                    <tr class="order-total">
                        <th>Credit Used</th>
                        <td><?php echo wc_price( (float)$credit_used ); ?></td>
                    </tr>
                    <tr class="order-total">
                        <th>Remaining Credit</th>
                        <td><?php echo wc_price( (float)$credit_remaining ); ?></td>
                    </tr>
                    <?php
                }

            }

        }
    }


    public function add_order_item_meta_for_pay_with_store_credit_order( $item_id, $values )
    {
        if( isset($values['pay_with_wallet']) ){
            wc_add_order_item_meta( $item_id, '_pay_with_wallet', $values['pay_with_wallet'] );
        }
        
        if( isset($values['credit_required']) ){
            wc_add_order_item_meta( $item_id, '_credit_required', $values['credit_required'] );
        }
    }

    public function modify_wallet_totals( $order_id )
    {
        $order = wc_get_order( $order_id );
        if( $order !== false ){

            foreach ( $order->get_items() as $item_id => $item ) {
                
                $pay_with_credit = $item->get_meta('_pay_with_wallet', true);
                
                if( $pay_with_credit == 'yes' ){

                    $user_id = get_current_user_id();
                    $wallet = new FD_Wallet( $user_id );

                    $credit_required = (float)$item->get_meta('_credit_required', true);
                    $wallet_balance = $wallet->get_balance();

                    $credit_used = ( $credit_required > $wallet_balance ) ? $wallet_balance : $credit_required ;

                    $update_type = 'purchase';
                    $wallet->update_balance( $update_type, $credit_used );

                }
                
            }

        }
    }
}

new FD_Woocommerce_Controller();