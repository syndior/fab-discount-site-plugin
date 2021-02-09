<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Test_Controller
{
    public function __construct()
    {
        // $voucher_id = 1;
        // $voucher = new FD_Voucher( $voucher_id );
        // var_dump( $voucher );

        // $transaction_data = array(
        //     'transaction_type'      => 'voucher_credited',
        //     'transaction_amount'    => 9.99,
        //     'voucher_id'            => 1,
        //     // 'order_id'              => 12,
        //     'user_id'               => 1,
        // );

        // $transaction_id = 17;
        // $transaction = new FD_Transaction($transaction_id);
        // var_dump( $transaction->get_amount() );


        // $user_id = 2;
        // $transactions = FD_Transaction::get_user_transactions( $user_id );
        // var_dump( $transactions );
        

        // if( function_exists('is_user_logged_in') ){
        //     if( is_user_logged_in() ){
        //         $user_id = get_current_user_id();
        //         $wallet = new FD_Wallet( $user_id );
        //         $amount = 100;
        //         // $type = 'purchase';
        //         // $type = 'credit_deduction';;
        //         // $type = 'credit_addition';
        //         var_dump($wallet->get_balance());
        //     }
        // }






    }
    
}

new FD_Test_Controller();