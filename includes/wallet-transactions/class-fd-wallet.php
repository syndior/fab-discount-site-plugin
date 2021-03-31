<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Wallet
{
    private $user_id;
    private $status;
    private $balance;
    private $last_updated;

    public function __construct( int $user_id = 0 )
    {
        if( $user_id > 0 ){
            $user_wallet_array = FD_Wallet::get_user_wallet( $user_id );
    
            if( $user_wallet_array !== false){
                $this->user_id           = $user_wallet_array['user_id'];
                $this->status            = $user_wallet_array['status'];
                $this->balance           = $user_wallet_array['balance'];
                $this->last_updated      = $user_wallet_array['last_updated'];
            }
        }
    }

    /**
     * Public Getter Functions
     */

    /*  */
    public function get_status()
    {
        return $this->status;
    }

    /*  */
    public function get_balance()
    {
        return $this->balance;
    }

    /*  */
    public function get_last_update_date()
    {
        return $this->last_updated;
    }

    /*  */
    public function get_user_id()
    {
        return $this->user_id;
    }

    /**
     * Public Setters
     */

    /*  */
    public function update_status( string $status = '' )
    {
        if( strlen( $status ) > 0 ){

            if( $status == 'active' || $status == 'inactive' ){
                $this->status = $status;

                $wallet = new FD_Wallet( $this->user_id );

                if( $this->update_wallet_properties( $wallet ) == true ){
                    return true;
                }else{
                    return false;
                }

            }else{
                return false;
            }
        }
        return false;
    }
    
    /*  */
    public function update_balance( string $update_type = '', int $amount = 0 )
    {
        if(  $this->get_status() == 'active' ){
            if( strlen( $update_type ) > 0 && $amount > 0 && ( $update_type == 'purchase' || $update_type == 'credit_addition' || $update_type == 'credit_deduction' ) ){

                if( ($amount > $this->get_balance()) && ( $update_type == 'purchase' || $update_type == 'credit_deduction') ){
                    return false;
                }else{
                    $transaction_data = array(
                        'transaction_type'      => $update_type,
                        'transaction_amount'    => $amount,
                        'user_id'               => $this->user_id
                    );
        
                    $transaction = FD_Transaction::create_transaction( $transaction_data );
                    $wallet = new FD_Wallet( $this->user_id );
        
                    if( $transaction !== false && $this->update_wallet_properties( $wallet ) == true ){
                        return true;
                    }else{
                        return false;
                    }
                }
            }
            return false;
        }
        return false;
    }

    /*  */
    public function convert_voucher_to_credit( int $voucher_id = 0 )
    {
        if( $voucher_id > 0 ){

            $voucher = new FD_Voucher( $voucher_id );

            if( $voucher->get_customer_id() == $this->user_id && ( $voucher->get_status() == 'active' || $voucher->get_status() == 'blocked' || $voucher->get_status() == 'refund_request' ) ){
                
                $voucher->update_status('credit_transferred');

                $transaction_data = array(
                    'transaction_type'      => 'voucher_credited',
                    'transaction_amount'    => $voucher->get_amount(),
                    'voucher_id'            => $voucher_id,
                    'order_id'              => $voucher->get_order_id(),
                    'user_id'               => $this->user_id
                );
                
                $transaction = FD_Transaction::create_transaction( $transaction_data );
                $wallet = new FD_Wallet( $this->user_id );

                if( $transaction !== false && $this->update_wallet_properties( $wallet ) == true ){
                    return true;
                }else{
                    return false;
                }

            }
            return false;
        }
        return false;
    }

    /**
     * Private Static Function: gets users wallet with user_id
     */
    private static function get_user_wallet( int $user_id = 0 )
    {

        if( $user_id > 0 ){

            $wallet_data = array(
                'user_id'           => $user_id,
                'status'            => 'active',
                'balance'           => 0,
                'last_updated'      => null
            );

            $transactions = FD_Transaction::get_user_transactions( $user_id );

            if( $transactions !== false && count( $transactions ) > 0 ){

                $positive_values = 0;
                $negative_values = 0;

                foreach( $transactions as $transaction ){

                    $type = $transaction->get_type();

                    switch( $type ){
                        case 'purchase':
                        case 'credit_deduction':
                            $negative_values += $transaction->get_amount();
                            break;
                        case 'voucher_credited':
                        case 'credit_addition':
                            $positive_values += $transaction->get_amount();
                            break;
                    }

                    $wallet_data['last_updated'] = $transaction->get_created_date();
                }

                $wallet_data['balance'] = $positive_values - $negative_values;
                $wallet_data['status'] = get_user_meta( $user_id, 'fdscf_user_wallet_status' , true );

                return $wallet_data;
            }
            //returning default wallet data
            return $wallet_data;
        }

        return false;
    }


    /**
     * Private Helper Function: updates wallet object properties
     */
    private function update_wallet_properties( FD_Wallet $wallet = null )
    {
        if( $wallet !== null ){
            $this->user_id           = $wallet->user_id ;
            $this->status            = $wallet->status;
            $this->balance           = $wallet->balance;
            $this->last_updated      = $wallet->last_updated;

            $user_wallet_meta = array(
                'status'        => $wallet->status,
                'balance'       => $wallet->balance,
                'last_updated'  => $wallet->last_updated,
            );

            if( $this->update_user_wallet_meta(  $user_wallet_meta ) == true ){
                return true;
            }else{
                return false;
            }
        }
        return false;
    }

    /**
     * Private Helper Function: update user eta
     */
    private function update_user_wallet_meta( array $user_wallet_meta = array() )
    {
        /**
         * User meta
         */
        if( isset( $user_wallet_meta['status'] ) && isset( $user_wallet_meta['balance'] ) && isset( $user_wallet_meta['last_updated'] ) ){
            update_user_meta( $this->user_id, 'fdscf_user_wallet_status' , $user_wallet_meta['status'] );
            update_user_meta( $this->user_id, 'fdscf_user_wallet_balance'  , $user_wallet_meta['balance'] );
            update_user_meta( $this->user_id, 'fdscf_user_wallet_last_updated' , $user_wallet_meta['last_updated'] );
            return true;
        }
        return false;
    }

}