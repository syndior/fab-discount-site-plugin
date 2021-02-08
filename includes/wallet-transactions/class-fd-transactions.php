<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Transaction
{
    private $transaction_id;
    private $transaction_type;
    private $created_at;
    private $user_id;

    public function __construct( int $transaction_id = 0 )
    {}


    /**
     * Getter functions
     */

    /*  */
    public function get_ID()
    {
        # code...
    }

    /*  */
    public function get_type()
    {
        # code...
    }

    /*  */
    public function get_created_date()
    {
        # code...
    }

    /*  */
    public function get_user_id()
    {
        # code...
    }


    /**
     * Public Static function: creates a new transaction
     */
    public static function create_transaction( int $user_id = 0, string $transaction_type = '' )
    {
        return $transaction;
    }

    /**
     * Public Static function: gets a single transaction
     */
    public static function get_transaction( int $transaction_id = 0 )
    {
        return $transaction;
    }
    
    /**
     * Public Static function: gets all transactions of a user
     */
    public static function get_user_transactions( int $user_id = 0 )
    {
        return $transactions;
    }

}