<?php if ( ! defined( 'ABSPATH' ) ) exit;

    $transactions_exists = true;

?>

<div class="fd-wc-account-my-transactions-tab-content">

    <div class="fd-wc-account-my-transactions-tab-header">
        <h3>My Transactions</h3>
    </div>

    <?php if(!$transactions_exists == true): ?>
    
    <div class="fd-wc-account-my-transactions-tab-notice">
        <p>You haven't made any transactions yet.</p>
    </div>
    
    <?php else: ?>
    
        <table>
            <thead>
                <tr>
                    <th>No#</th>
                    <th>Item</th>
                    <th>Transactions ID</th>
                    <th>Transactions Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php for( $i = 1; $i <= 10; $i++ ):?>
                    <tr>
                        <td><?=$i?></td>
                        <td><a href="#"><?="Item no $i"?></a></td>
                        <td>1234</td>
                        <td>Purchase</td>
                        <td>
                            <small><a href="#">Report a problem</a></small>
                        </td>
                    </tr>
                <?php endfor;?>
            </tbody>
        </table>

    <?php endif; ?>

</div>

<?php