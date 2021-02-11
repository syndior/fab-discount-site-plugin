<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Transaction
{
    private $id;
    private $type;
    private $created_at;
    private $voucher_id;
    private $order_id;
    private $user_id;
    private $amount;

    public function __construct( int $transaction_id = 0 )
    {
        if( $transaction_id > 0 ){
            $db_result_obj = FD_Transaction::get_transaction_data_from_db( $transaction_id );
        
            if( $db_result_obj !== false){
                $this->id                   = $db_result_obj->transaction_id ;
                $this->type                 = $db_result_obj->transaction_type;
                $this->created_at           = $db_result_obj->created_at;
                $this->voucher_id           = $db_result_obj->voucher_id;
                $this->order_id             = $db_result_obj->order_id;
                $this->user_id              = $db_result_obj->user_id;
                $this->amount               = $db_result_obj->transaction_amount;
            }
        }
    }


    /**
     * Getter functions
     */

    /*  */
    public function get_ID()
    {
        return $this->id;
    }

    /*  */
    public function get_type()
    {
        return $this->type;
    }

    /*  */
    public function get_created_date()
    {
        return $this->created_at;
    }

    /*  */
    public function get_voucher_id()
    {
        return $this->voucher_id;
    }
    
    /*  */
    public function get_order_id()
    {
        return $this->order_id;
    }
    
    /*  */
    public function get_user_id()
    {
        return $this->user_id;
    }

    /*  */
    public function get_amount()
    {
        return $this->amount;
    }


    /**
     * Public Static function: creates a new transaction
     */
    public static function create_transaction( array $transaction_data = array() )
    {
        if( $transaction_data['user_id'] > 0 ){

            $defaults = array(
                'transaction_type'      => null,
                'transaction_amount'    => 0,
                'voucher_id'            => 0,
                'order_id'              => 0,
                'user_id'               => 0,
            );
    
            $data = wp_parse_args( $transaction_data, $defaults );

            if( isset($data['transaction_type']) && ( $data['transaction_type'] == 'purchase' || $data['transaction_type'] == 'voucher_credited' || $data['transaction_type'] == 'credit_addition' || $data['transaction_type'] == 'credit_deduction' ) ){

                if( $data['voucher_id'] > 0 && $data['transaction_type'] == 'voucher_credited' ){
                    $voucher_id = $data['voucher_id'];
                    $voucher = new FD_Voucher( $voucher_id );
    
                    $data['transaction_amount']         = $voucher->get_amount();
                    $data['order_id']                   = $voucher->get_order_id();
                }

                if( $data['user_id'] > 0 && $data['transaction_amount'] > 0 &&  $data['transaction_type'] !== null ){
                    $transaction = FD_Transaction::insert_transaction_record_in_db( $data );
    
                    if( $transaction !== false ){
                        return $transaction; 
                    }else{
                        return false; 
                    }
    
                }

            }
            return false;

        }
        return false;
    }

    /**
     * Private static function: inset transaction record in database
     */
    private static function insert_transaction_record_in_db( array $transaction_data = array() )
    {
        //sanity check
        if( $transaction_data['user_id'] > 0 && $transaction_data['transaction_amount'] > 0 &&  $transaction_data['transaction_type'] !== null ){
            global $wpdb;

            // checks if table exists in db before inserting
            $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( fdscf_transactions_db_table_name ) );

            if ( $wpdb->get_var( $query ) == fdscf_transactions_db_table_name ){
                $result = $wpdb->insert( fdscf_transactions_db_table_name, $transaction_data);
                if( $result !== false && $result == 1 ){
                    $transaction = new FD_Transaction( $wpdb->insert_id );
                    return $transaction;
                }else{
                    return $wpdb->last_error;
                }
            }
            return false;
        }
        return false;
    }

    /**
     * Private static function: get a transaction data from sb
     */
    private function get_transaction_data_from_db( int $transaction_id = 0 )
    {
        if( $transaction_id > 0 ){
            global $wpdb;
            $table_name = fdscf_transactions_db_table_name;
            $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$table_name}` WHERE `transaction_id` = %d LIMIT 1;", absint( $transaction_id ) ), OBJECT );

            if( $result !== null ){
                return $result;
            }
        }

        return false;
    }

    
    /**
     * Public Static function: gets all transactions of a user
     */
    public static function get_user_transactions( int $user_id = 0 )
    {
        if( $user_id > 0 ){
            global $wpdb;
            $table_name = fdscf_transactions_db_table_name;

            $sql_query          = "SELECT * FROM `{$table_name}` WHERE `user_id` = %d ORDER BY `created_at` ASC;";
            $prepared_query     = $wpdb->prepare( $sql_query, absint( $user_id ) );
            $results            = $wpdb->get_results( $prepared_query , OBJECT);

            if( !empty( $results ) ){

                $transactions = array();

                foreach( $results as $row ){
                    $transaction = new FD_Transaction( $row->transaction_id );
                    $transactions[] = $transaction;
                }

                return $transactions;
            }
            return false;
        }
        return false;
    }

}