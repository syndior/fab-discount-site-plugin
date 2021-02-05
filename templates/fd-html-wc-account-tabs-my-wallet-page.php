<?php if ( ! defined( 'ABSPATH' ) ) exit;

    $wallet_credit_exists = true;

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
                    <td>1234567890</td>
                </tr>
                <tr>
                    <th>Current Balance</th>
                    <td>19.99$</td>
                </tr>
                <tr>
                    <th>Last updated:</th>
                    <td>Friday 22 Jan, 2021</td>
                </tr>
            </tbody>
        </table>

    <?php endif; ?>

</div>

<?php