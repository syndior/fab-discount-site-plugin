<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Wallet
{
    private $user_id;
    private $status;
    private $balance;
    private $last_updated;

    public function __construct( int $user_id = 0 )
    {}

    /**
     * Public Getter Functions
     */

    /*  */
    public function get_status()
    {
        # code...
    }

    /*  */
    public function get_balance()
    {
        # code...
    }

    /*  */
    public function get_last_update_date()
    {
        # code...
    }

    /*  */
    public function get_user_id()
    {
        # code...
    }

    /**
     * Public Setters
     */

    /*  */
    public function update_status( string $status = '' )
    {
        return true;
    }
    
    /*  */
    public function update_balance( string $update_type = '', int $amount = 0 )
    {
        return true;
    }

    /*  */
    public function convert_voucher_to_credit( FD_Voucher $voucher = null )
    {
        return true;
    }

    /**
     * Private Static Function: gets users wallet with user_id
     */
    private static function get_user_wallet( int $user_id = 0 )
    {
        return $wallet;
    }


    /**
     * Private Helper Function: updates wallet object properties
     */
    private function update_wallet_properties( FD_Wallet $wallet = null )
    {
        return true;
    }

    /**
     * Private Helper Function: update user eta
     */
    private function update_user_wallet_meta()
    {
        return true;
    }

}