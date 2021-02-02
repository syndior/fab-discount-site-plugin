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
        add_action( 'woocommerce_product_data_panels', array( $this, 'add_woocommerce_product_data_panels') );
        
        /* inserts JS that hides and show the wc default product tabs on our custom product type*/
        add_action( 'admin_footer', array( $this, 'modify_woocommerce_tabs_visibility') );
        
        /* saves our custom fields with our custom product type*/
        add_action( 'woocommerce_process_product_meta', array( $this, 'process_product_meta') );
        
        /* adds custom button before the add to cart button to pay with store credit */
        add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'add_functionality_before_add_to_cart_button') );
        
    }

    public function add_product_type_filter( $types )
    {
        $types[ 'fd_wc_voucher' ] = 'FD Voucher';
        return $types;
    }

    public function add_woocommerce_product_class( $classname, $product_type )
    {
        if ( $product_type == 'fd_wc_voucher' ) {
            $classname = 'WC_Product_FD_Voucher';
        }
        return $classname;
    }

    public function modify_woocommerce_product_data_tabs( $original_tabs )
    {
        //hide shipping tab
        $original_tabs['shipping']['class'][] = 'hide_if_fd_wc_voucher';

        $fd_wc_voucher_tab['fd_wc_voucher'] = array(
            'label' => 'FD Voucher Options',
            'target' => 'fd_wc_voucher_options',
            'class' => 'show_if_fd_wc_voucher'
        );

        $insert_at_position = 0;
        $tabs = array_slice( $original_tabs, 0, $insert_at_position, true );
        $tabs = array_merge( $tabs, $fd_wc_voucher_tab );
        $tabs = array_merge( $tabs, array_slice( $original_tabs, $insert_at_position, null, true ) );

        return $tabs;
    }

    public function add_woocommerce_product_data_panels()
    {
        require_once ( fdscf_path . './templates/fd-html-wc-voucher-product-data-tab.php' );
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
                jQuery('.product_data_tabs .general_tab').addClass('show_if_fd_wc_voucher').show();
                jQuery('#general_product_data .pricing').addClass('show_if_fd_wc_voucher').show();

                //for Inventory tab
                jQuery('.inventory_options').addClass('show_if_fd_wc_voucher').show();
                jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_fd_wc_voucher').show();
                jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_fd_wc_voucher').show();
                jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_fd_wc_voucher').show();
            });
        </script>
        <?php
    }


    public function process_product_meta( $post_id )
    {
        $fd_product_meta = array();

        $fd_product_meta['fd_wc_corner_banner']             = ( $_POST['fd_wc_corner_banner'] == 'fd_wc_corner_banner_enabled' ) ? $_POST['fd_wc_corner_banner'] : 'fd_wc_corner_banner_disabled';
        $fd_product_meta['fd_wc_corner_banner_title']       = ( isset( $_POST['fd_wc_corner_banner_title'] ) ) ? $_POST['fd_wc_corner_banner_title'] : '';
        $fd_product_meta['fd_wc_corner_banner_headind']     = ( isset( $_POST['fd_wc_corner_banner_headind'] ) ) ? $_POST['fd_wc_corner_banner_headind'] : '';
        
        $fd_product_meta['fd_wc_voucher_expiry']            = ( $_POST['fd_wc_voucher_expiry'] == 'fd_wc_voucher_expiry_enabled' ) ? $_POST['fd_wc_voucher_expiry'] : 'fd_wc_voucher_expiry_disabled';
        $fd_product_meta['fd_wc_voucher_use_global_expiry'] = ( $_POST['fd_wc_voucher_use_global_expiry'] == 'fd_wc_voucher_use_global_expiry_enabled' ) ? $_POST['fd_wc_voucher_use_global_expiry'] : 'fd_wc_voucher_use_global_expiry_disabled';
        
        $fd_product_meta['fd_wc_voucher_expiry_date']       = ( isset( $_POST['fd_wc_voucher_expiry_date'] ) && $_POST['fd_wc_voucher_expiry_date'] > 0 ) ? $_POST['fd_wc_voucher_expiry_date'] : 0;

        if( count( $fd_product_meta ) > 0 ){
            $product = wc_get_product( $post_id );
            
            foreach( $fd_product_meta as $meta_field_key => $meta_field_value ){

                $product->update_meta_data( $meta_field_key,  esc_attr( $meta_field_value ) );

            }

            $product->save();
        }
    }

    public function add_functionality_before_add_to_cart_button()
    {
        echo '<a href="#">here</a>';
    }


}

new FD_Woocommerce_Controller();