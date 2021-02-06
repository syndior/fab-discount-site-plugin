<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Voucher
{
    /**
     * Voucher Object Properties
     */

    private $id;
    private $key;
    private $status;
    private $user_id;
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
                $this->status               = $db_result_obj->fd_voucher_status;
                $this->user_id              = $db_result_obj->user_id;
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
        return $this->key;
    }
    
    public function get_status()
    {
        return $this->status;
    }
    
    public function get_user_id()
    {
        return $this->user_id;
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
            if( $status == 'active' || $status == 'expired' || $status == 'blocked' || $status == 'pending' ){
                global $wpdb;
                $table_name = fdscf_db_table_name;

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

    /**
     * Helper function: attempts to creeate a new voucher
     */
    public static function create_voucher()
    {
        $counter = 0;
        $max_attempts = 100;

        do{

            $voucher = FD_Voucher::insert_voucher_data_in_db();

        } while ( !( $voucher instanceof self ) && $counter < $max_attempts );

        if( $voucher instanceof self ){
            return $voucher;
        }else{
            wp_die( "Error in creating new voucher" );
        }

    }

    /**
     * Helper function: gets voucher from DB
     */
    private static function get_voucher_data_from_db( int $voucher_id = 0 )
    {
        if( $voucher_id > 0 ){
            global $wpdb;
            $table_name = fdscf_db_table_name;
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
            $this->id                   = $voucher->id;
            $this->key                  = $voucher->key;
            $this->status               = $voucher->status;
            $this->user_id              = $voucher->user_id;
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
    private static function insert_voucher_data_in_db()
    {
        global $wpdb;

        $key = FD_Voucher::generate_voucher_key();
        $data = array(
            'fd_voucher_key'    => $key,
            'user_id'           => 1,
            'order_id'          => 1,
            'product_id'        => 1
        );

        $result = $wpdb->insert( fdscf_db_table_name, $data);

        if( $result !== false && $result == 1 ){
            $voucher = new FD_Voucher( $wpdb->insert_id );
            return $voucher;
        }else{
            return $wpdb->last_error;
        }
    }


    /**
     * Helper Function: checks validates a voucher
     */
    public static function validate_voucher_key( string $voucher_key = '' )
    {
        if( strlen( $voucher_key ) > 0 ){
            global $wpdb;
            $table_name = fdscf_db_table_name;

            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$table_name}` WHERE `fd_voucher_key` = %s LIMIT 1;", $voucher_key ), OBJECT );
            if( $result !== null ){
                $voucher = new FD_Voucher( $result->fd_voucher_id );
                return $voucher;
            }
        }

        return false;
    }
}