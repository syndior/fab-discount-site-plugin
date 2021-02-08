<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Woocommerce_Controller
{
    public function __construct()
    {
        /* adds the custom product type's label in the products type dropdown */
        add_filter( 'product_type_selector', array( $this, 'add_product_type_filter' ) );

        /* adds our custom product type extended class to be used with our product type*/
        add_filter( 'woocommerce_product_class', array( $this, 'add_woocommerce_product_class' ), 10, 2 );

        /* add custom product product data tab */
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'modify_woocommerce_product_data_tabs' ), 10, 1 );

        /* loads custom product data tab markup */
        add_action( 'woocommerce_product_data_panels', array( $this, 'add_woocommerce_product_data_panels'), 10 );
        
        /* inserts JS that hides and show the wc default product tabs on our custom product type*/
        add_action( 'admin_footer', array( $this, 'modify_woocommerce_tabs_visibility') );
        
        /* saves our custom fields with our custom product type*/
        add_action( 'woocommerce_process_product_meta', array( $this, 'process_product_meta') );

        /* adds back the add to cart buttons and product summary sections */
        add_action( 'woocommerce_fd_wc_offer_add_to_cart', array( $this, 'add_to_cart_template_include') );
        add_action( 'woocommerce_fd_wc_offer_variable_add_to_cart', array( $this, 'add_to_cart_template_include') );
        
        /* adds custom button before the add to cart button to pay with store credit */
        add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'add_functionality_before_add_to_cart_button'), 10 );

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
    }

    public function add_product_type_filter( $types )
    {
        $types[ 'fd_wc_offer' ]             = 'FD Offer';
        $types[ 'fd_wc_offer_variable' ]    = 'FD Offer Variable';
        return $types;
    }

    public function add_woocommerce_product_class( $classname, $product_type )
    {
        if ( $product_type == 'fd_wc_offer' ) {
            $classname = 'WC_Product_FD_Offer';
        }
        
        if ( $product_type == 'fd_wc_offer_variable' ) {
            $classname = 'WC_Product_FD_Offer_Variable';
        }

        return $classname;
    }

    public function modify_woocommerce_product_data_tabs( $original_tabs )
    {
        //hide shipping tab
        $original_tabs['shipping']['class'][] = 'hide_if_fd_wc_offer';
        $original_tabs['shipping']['class'][] = 'hide_if_fd_wc_offer_variable';

        //adds back the variations tab
        $original_tabs['variations']['class'][] = 'show_if_fd_wc_offer_variable';

        $fd_wc_offer_tab['fd_wc_offer'] = array(
            'label' => 'FD Offer Options',
            'target' => 'fd_wc_offer_options',
            'class' => 'show_if_fd_wc_offer show_if_fd_wc_offer_variable'
        );

        $tabs = $this->insert_item_at_array_position( 0, $fd_wc_offer_tab, $original_tabs );

        return $tabs;
    }

    public function add_woocommerce_product_data_panels()
    {
        require_once ( fdscf_path . 'templates/fd-html-wc-offer-product-data-tab.php' );
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
                jQuery('#general_product_data .pricing').addClass('show_if_fd_wc_offer_variable').show();
                
                jQuery('.product_data_tabs .general_tab').addClass('show_if_fd_wc_offer').show();
                jQuery('#general_product_data .pricing').addClass('show_if_fd_wc_offer_variable').show();

                //for Inventory tab
                jQuery('.inventory_options').addClass('show_if_fd_wc_offer').show();
                jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_fd_wc_offer').show();
                jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_fd_wc_offer').show();
                jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_fd_wc_offer').show();
                
                jQuery('.inventory_options').addClass('show_if_fd_wc_offer_variable').show();
                jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_fd_wc_offer_variable').show();
                jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_fd_wc_offer_variable').show();
                jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_fd_wc_offer_variable').show();
            });
        </script>
        <?php
    }


    public function process_product_meta( $post_id )
    {
        $fd_product_meta = array();

        $fd_product_meta['fd_wc_corner_banner']                     = ( $_POST['fd_wc_corner_banner'] == 'fd_wc_corner_banner_enabled' ) ? $_POST['fd_wc_corner_banner'] : 'fd_wc_corner_banner_disabled';
        $fd_product_meta['fd_wc_corner_banner_title']               = ( isset( $_POST['fd_wc_corner_banner_title'] ) ) ? $_POST['fd_wc_corner_banner_title'] : '';
        $fd_product_meta['fd_wc_corner_banner_headind']             = ( isset( $_POST['fd_wc_corner_banner_headind'] ) ) ? $_POST['fd_wc_corner_banner_headind'] : '';
        $fd_product_meta['fd_wc_offer_expiry']                      = ( $_POST['fd_wc_offer_expiry'] == 'fd_wc_offer_expiry_enabled' ) ? $_POST['fd_wc_offer_expiry'] : 'fd_wc_offer_expiry_disabled';
        $fd_product_meta['fd_wc_offer_use_global_expiry']           = ( $_POST['fd_wc_offer_use_global_expiry'] == 'fd_wc_offer_use_global_expiry_enabled' ) ? $_POST['fd_wc_offer_use_global_expiry'] : 'fd_wc_offer_use_global_expiry_disabled';
        $fd_product_meta['fd_wc_offer_expiry_date']                 = ( isset( $_POST['fd_wc_offer_expiry_date'] ) && $_POST['fd_wc_offer_expiry_date'] > 0 ) ? $_POST['fd_wc_offer_expiry_date'] : 0;
        $fd_product_meta['fd_offer_linked_product']                 = isset( $_POST['fd_offer_linked_product'] ) ? $_POST['fd_offer_linked_product'] : '';
        $fd_product_meta['fd_offer_linked_product_variation']       = isset( $_POST['fd_offer_linked_product_variation'] ) ? $_POST['fd_offer_linked_product_variation'] : '';
        $fd_product_meta['fd_wc_offer_voucher_expiry']              = ( $_POST['fd_wc_offer_voucher_expiry'] == 'fd_wc_offer_voucher_expiry_enabled' ) ? $_POST['fd_wc_offer_voucher_expiry'] : 'fd_wc_offer_voucher_expiry_disabled';
        $fd_product_meta['fd_wc_offer_voucher_use_global_expiry']   = ( $_POST['fd_wc_offer_voucher_use_global_expiry'] == 'fd_wc_offer_voucher_use_global_expiry_enabled' ) ? $_POST['fd_wc_offer_voucher_use_global_expiry'] : 'fd_wc_offer_voucher_use_global_expiry_disabled';
        $fd_product_meta['fd_wc_offer_voucher_expiry_date']         = ( isset( $_POST['fd_wc_offer_voucher_expiry_date'] ) && $_POST['fd_wc_offer_voucher_expiry_date'] > 0 ) ? $_POST['fd_wc_offer_voucher_expiry_date'] : 0;

        if( count( $fd_product_meta ) > 0 ){
            $product = wc_get_product( $post_id );
            
            foreach( $fd_product_meta as $meta_field_key => $meta_field_value ){

                $product->update_meta_data( $meta_field_key,  esc_attr( $meta_field_value ) );

            }

            $product->update_meta_data( 'fd_vendor_id',  esc_attr( get_current_user_id() ) );

            $product->save();
        }
    }

    public function add_to_cart_template_include()
    {
        do_action( 'woocommerce_simple_add_to_cart' );
    }


    public function add_functionality_before_add_to_cart_button()
    {
        require_once ( fdscf_path . 'templates/fd-html-before-add-to-cart-custom-options.php' );
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
        check_ajax_referer( 'admin_ajax_check', 'security' );

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

}

new FD_Woocommerce_Controller();