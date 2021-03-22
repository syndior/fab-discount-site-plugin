<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_User_Controller
{
    public function __construct()
    {
        /* hook ajax handler to log user's viewed products */
        add_action('wp_ajax_fd_log_user_viewed_product',  array( $this, 'fd_log_user_viewed_product' ) );
        
        /* setup initial user wallet meta data */
        add_action('user_register',  array( $this, 'fd_setup_initial_user_meta_data' ) );

    }

    public function fd_log_user_viewed_product()
    {
        check_ajax_referer( 'ajax_check', 'security' );
        $user = wp_get_current_user();
        
        //response defults
        $response = array(
            'type' => 'error',
        );

        if( isset( $_REQUEST['request_type'] ) ){

            if( $_REQUEST['request_type'] == 'product_log' && isset( $_REQUEST['product_id'] ) ){

                if( is_user_logged_in() ){
                    $user = wp_get_current_user();
                    $product_id = $_REQUEST['product_id'];

                    /**
                     * First get users previously viewed products array stored in the users meta.
                     * Update the array with the currently received product_id if dosen't aleardy exists
                     */

                    $users_viewed_products = get_user_meta($user->ID, 'fd_viewed_products', true);

                    if( !empty( $users_viewed_products ) ){
                        if( !in_array( $product_id, $users_viewed_products ) ){
                            
                            if( count( $users_viewed_products ) >= 10 ){
                                unset( $users_viewed_products[0] );
                            }

                            $users_viewed_products = array_merge( $users_viewed_products,  array($product_id) );
                        }else{
                            $response['product_viewed'] = true;
                        }
                    }else{
                        $users_viewed_products = array( $product_id );
                        $response['product_viewed'] = true;
                    }

                    /**
                     * Update the user meta with the updated formated array 
                     */
                    if( !empty( $users_viewed_products ) ){
                        $update_status = update_user_meta( $user->ID, 'fd_viewed_products', $users_viewed_products );

                        if( $update_status == true ){
                            $response['type'] = 'success';
                        }
                    }

                }

            }

        }


        wp_send_json_success($response);
        wp_die();
    }


    public function fd_setup_initial_user_meta_data( $user_id )
    {
        /**
         * Wallet default meta
         */
        $wallet_status          = 'active';
        $wallet_balance         = 0.00;
        $wallet_last_updated    = date("Y-m-d H:i:s") ;

        add_user_meta( $user_id, 'fdscf_user_wallet_status', $wallet_status );
        add_user_meta( $user_id, 'fdscf_user_wallet_balance', $wallet_balance );
        add_user_meta( $user_id, 'fdscf_user_wallet_last_updated', $wallet_last_updated );
    }
}

new FD_User_Controller();