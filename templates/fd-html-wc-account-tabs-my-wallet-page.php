<?php if ( ! defined( 'ABSPATH' ) ) exit;

    $wallet_credit_exists = false;
    if( is_user_logged_in() ){
        $user_id = get_current_user_id();
        $wallet = new FD_Wallet( $user_id );
        if( $wallet->get_balance() > 0 ){
            $wallet_credit_exists = true;
        }
    }

?>

<div class="fd-wc-account-my-wallet-tab-content">

    <div class="fd-wc-account-my-wallet-tab-header">
        <h3>My Wallet</h3>
    </div>

    <?php if(!$wallet_credit_exists == true): ?>
    
    <div class="fd-wc-account-my-wallet-tab-notice">
        <p>You don't have any store credit in your wallet yet.</p>
    </div>
    
    <?php else: ?>
    
        <table>
            <thead>
            <tbody>
                <tr>
                    <th>Account ID</th>
                    <td><?php echo $wallet->get_user_id(); ?></td>
                </tr>
                <tr>
                    <th>Account Status</th>
                    <td><?php echo $wallet->get_status(); ?></td>
                </tr>
                <tr>
                    <th>Current Balance</th>
                    <td><?php echo wc_price( $wallet->get_balance() ); ?></td>
                </tr>
                <tr>
                    <th>Last updated:</th>
                    <td> <?php echo $wallet->get_last_update_date(); ?> </td>
                </tr>
            </tbody>
        </table>

    <?php endif; ?>

</div>