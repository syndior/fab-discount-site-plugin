<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Voucher
{
    /**
     * Voucher Object Properties
     */

    private $id;
    private $key;
    private $voucher_amount;
    private $status;
    private $vendor_id;
    private $customer_id;
    private $order_id;
    private $product_id;
    private $created_at;
    private $updated_at;
    private $expires_at;
    private $will_expire;

    public function __construct( int $voucher_id = 0 )
    {
        if( $voucher_id > 0 ){
            $db_result_obj = FD_Voucher::get_voucher_data_from_db( $voucher_id );
        
            if( $db_result_obj !== false){
                $this->id                   = $db_result_obj->fd_voucher_id;
                $this->key                  = $db_result_obj->fd_voucher_key;
                $this->voucher_amount       = $db_result_obj->voucher_amount;
                $this->status               = $db_result_obj->fd_voucher_status;
                $this->vendor_id            = $db_result_obj->vendor_id;
                $this->customer_id          = $db_result_obj->customer_id;
                $this->order_id             = $db_result_obj->order_id;
                $this->product_id           = $db_result_obj->product_id;
                $this->created_at           = $db_result_obj->created_at;
                $this->updated_at           = $db_result_obj->updated_at;
                $this->expires_at           = $db_result_obj->expires_at;
                $this->will_expire          = $db_result_obj->will_expire;
            }
        }
    }

    /**
     * Public getter functions
     */

    public function get_ID()
    {   
        return $this->id;
    }
    
    public function get_key()
    {
        $this->key;
        
        $key_length         = strlen( $this->key );
        $key_part_length    = $key_length / 4;
        $key_parts          = str_split( $this->key, $key_part_length );

        $formated_key       = '';

        for ( $i = 0; $i < count($key_parts); $i++ ) { 
            if( $i == 0 ){
                $formated_key = $formated_key . $key_parts[$i];
            }else{
                $formated_key = $formated_key . '-' . $key_parts[$i];
            }
        }

        $formated_key = strtoupper( $formated_key );

        return $formated_key;
    }

    public function get_amount()
    {
        return $this->voucher_amount;
    }
    
    public function get_status()
    {
        return $this->status;
    }
    
    public function get_vendor_id()
    {
        return $this->vendor_id;
    }

    public function get_customer_id()
    {
        return $this->customer_id;
    }
    
    public function get_order_id()
    {
        return $this->order_id;
    }
    
    public function get_product_id()
    {
        return $this->product_id;
    }
    
    public function get_created_date()
    {
        return $this->created_at;
    }
    
    public function get_updated_date()
    {
        return $this->updated_at;
    }
    
    public function get_expiry_date()
    {
        return $this->expires_at;
    }
    
    public function is_set_to_expire()
    {
        if( $this->will_expire == 1 ){
            return true;
        }elseif( $this->will_expire == 0 ){
            return false;
        }
    }


    /**
     * Public Setter Functions
     */

    public function update_status(  string $status = '' )
    {
        if( strlen( $status ) > 0 ){
            if( $status == 'active' || $status == 'redeemed' || $status == 'credit_transferred' || $status == 'expired' || $status == 'blocked' ){
                global $wpdb;
                $table_name = fdscf_vouchers_db_table_name;

                $data = array(
                    'fd_voucher_status' => $status
                );

                $where = array(
                    'fd_voucher_id' => $this->id
                );

                $format = array(
                    '%s'
                );

                $where_format = array(
                    '%d'
                );
                
                $result = $wpdb->update( $table_name , $data, $where, $format, $where_format );

                if( $result !== false ){
                    $voucher = new FD_Voucher( $this->id );
                    $update_status = FD_Voucher::update_voucher_properties( $voucher );
                    if( $voucher !== false && $update_status == true ){
                        return $voucher;
                    }  
                }
            }
        }

        return false;
    }


    public function set_to_expire(bool $value , string $expiry_date = '' )
    {
        if( $this->is_set_to_expire() !== $value ){
            
            if( $value == true  && (strlen($expiry_date) > 0) ){
                
                $current_date = new DateTime( date("Y-m-d H:i:s") );
                $expiray_date = DateTime::createFromFormat( "Y-m-d H:i:s", $expiry_date );
                
                if( ( $current_date instanceof DateTime ) && ( $expiray_date instanceof DateTime ) ){
                    
                    $current_date_timestamp = $current_date->getTimestamp();
                    $expiray_date_timestamp = $expiray_date->getTimestamp();
                    
                    if( $expiray_date_timestamp > $current_date_timestamp ){
                        global $wpdb;
                        $table_name = fdscf_vouchers_db_table_name;

                        $data = array(
                            'will_expire' => $value,
                            'expires_at' => $expiray_date->format( "Y-m-d H:i:s" ),
                        );

                        $where = array(
                            'fd_voucher_id' => $this->id
                        );

                        $format = array(
                            '%d',
                            '%s'
                        );

                        $where_format = array(
                            '%d'
                        );
                        
                        $result = $wpdb->update( $table_name , $data, $where, $format, $where_format );
                        if( $result !== false ){
                            $voucher = new FD_Voucher( $this->id );
                            $update_status = FD_Voucher::update_voucher_properties( $voucher );
                            if( $voucher !== false && $update_status == true ){
                                return $voucher;
                            }  
                        }
                    }

                }
            }elseif ( $value == false ) {
                global $wpdb;
                $table_name = fdscf_vouchers_db_table_name;

                $data = array(
                    'will_expire' => $value,
                    'expires_at' => null,
                );

                $where = array(
                    'fd_voucher_id' => $this->id
                );

                $format = array(
                    '%d'
                );

                $where_format = array(
                    '%d'
                );
                
                $result = $wpdb->update( $table_name , $data, $where, $format, $where_format );
                if( $result !== false ){
                    $voucher = new FD_Voucher( $this->id );
                    $update_status = FD_Voucher::update_voucher_properties( $voucher );
                    if( $voucher !== false && $update_status == true ){
                        return $voucher;
                    }  
                }
            }
            return false;
        }

        return $this;

    }


    /**
     * Helper function: attempts to creeate a new voucher
     */
    public static function create_voucher( array $voucher_data )
    {
        if( isset( $voucher_data['customer_id'] ) && 
            isset( $voucher_data['vendor_id'] ) && 
            isset( $voucher_data['order_id'] ) && 
            isset( $voucher_data['voucher_amount'] ) && 
            isset( $voucher_data['product_id'] ) ){

                if( ( isset( $voucher_data['will_expire'] ) && $voucher_data['will_expire'] == true ) && !isset( $voucher_data['expires_at'] ) ){
                    
                    // if voucher is set to expire, have to provide a expiry date
                    return false;
                }

            $counter = 0;
            $max_attempts = 100;
    
            do{
    
                $voucher = FD_Voucher::insert_voucher_data_in_db( $voucher_data );
    
            } while ( !( $voucher instanceof self ) && $counter < $max_attempts );
    
            if( $voucher instanceof self ){
                return $voucher;
            }else{
                wp_die( "Error in creating new voucher" );
            }
        }

        return false;

    }

    /**
     * Helper function: gets voucher from DB
     */
    private static function get_voucher_data_from_db( int $voucher_id = 0 )
    {
        if( $voucher_id > 0 ){
            global $wpdb;
            $table_name = fdscf_vouchers_db_table_name;
            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$table_name}` WHERE `fd_voucher_id` = %d LIMIT 1;", absint( $voucher_id ) ), OBJECT );

            if( $result !== null ){
                return $result;
            }
        }

        return false;
    }

    /**
     * Helper Function: Update Voucher Objects Porpertoes
     */
    private function update_voucher_properties( FD_Voucher $voucher = null )
    {
        if( $voucher !== null ){
            $this->id                   = $voucher->fd_voucher_id;
            $this->key                  = $voucher->fd_voucher_key;
            $this->voucher_amount       = $voucher->voucher_amount;
            $this->status               = $voucher->fd_voucher_status;
            $this->vendor_id            = $voucher->vendor_id;
            $this->customer_id          = $voucher->customer_id;
            $this->order_id             = $voucher->order_id;
            $this->product_id           = $voucher->product_id;
            $this->created_at           = $voucher->created_at;
            $this->updated_at           = $voucher->updated_at;
            $this->expires_at           = $voucher->expires_at;
            $this->will_expire          = $voucher->will_expire;

            return true;
        }
        return false;
    }

    /**
     * Heper Functions: generates unique voucker key 
     */
    private static function generate_voucher_key()
    {
        /**
         * length is multiplyed by 2
         * 6 means 12 characters long
         * 12 means 24 characters long
        */
        $voucher_key_length = apply_filters( 'fd_voucher_key_length', 12 );
        $unformated_key = bin2hex( random_bytes( $voucher_key_length ) );


        return $unformated_key;
    }

    /**
     * Heper Functions: Insets a new voucher in database
     */
    private static function insert_voucher_data_in_db( array $voucher_data = array() )
    {
        global $wpdb;

        $key = FD_Voucher::generate_voucher_key();
        $defaults = array(
            'fd_voucher_key'        => $key,
            'expires_at'            => null,
            'voucher_amount'        => 0,
            'will_expire'           => 0,
            'fd_voucher_status'     => 'active',
            'vendor_id'             => 0,
            'customer_id'           => 0,
            'order_id'              => 0,
            'product_id'            => 0,
        );

        $data = wp_parse_args( $voucher_data, $defaults );

        // checks if table exists in db before inserting
        $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( fdscf_vouchers_db_table_name ) );

        if ( $wpdb->get_var( $query ) == fdscf_vouchers_db_table_name ){
            $result = $wpdb->insert( fdscf_vouchers_db_table_name, $data);
            if( $result !== false && $result == 1 ){
                $voucher = new FD_Voucher( $wpdb->insert_id );
                return $voucher;
            }else{
                return $wpdb->last_error;
            }
        }

        return false;
    }


    /**
     * Helper Function: checks validates a voucher
     */
    public static function validate_voucher_key( string $voucher_key = '' )
    {
        if( strlen( $voucher_key ) > 0 ){
            global $wpdb;
            $table_name = fdscf_vouchers_db_table_name;

            $voucher_key = preg_replace('/-/i', '', $voucher_key);
            $voucher_key = strtolower( $voucher_key );

            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$table_name}` WHERE `fd_voucher_key` = %s LIMIT 1;", $voucher_key ), OBJECT );
            if( $result !== null ){
                $voucher = new FD_Voucher( $result->fd_voucher_id );
                return $voucher;
            }
        }

        return false;
    }
}