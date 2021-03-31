<?php if ( ! defined( 'ABSPATH' ) ) exit;

    $transactions_exists = true;
    $user_id = get_current_user_id();
    $transactions = FD_Transaction::get_user_transactions( $user_id );
    if(!is_array($transactions)){
        $transactions_exists = false;
    }

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
                    <th>Transaction ID</th>
                    <th>Transaction Type</th>
                    <th>Transaction Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $key => $transaction) { ?>
                    <tr>
                        <td><?=$key+1?></td>
                        <td><?=$transaction->id?></td>
                        <td>
                            <?php
                            if($transaction->type == "purchase"){
                                echo "purchase";
                            }elseif($transaction->type == "voucher_credited"){
                                echo "Voucher Credited";
                            }elseif($transaction->type == "credit_addition"){
                                echo "Credit Addition";
                            }elseif($transaction->type == "credit_deduction"){
                                echo "Credit Deduction";
                            }
                            ?>
                        
                        </td>
                        <td><?=$transaction->created_at;?></td>
                        <td>
                            <small><a href="#">Report a problem</a></small>
                        </td>
                    </tr>
                <?php }//end foreach;?>
            </tbody>
        </table>

    <?php endif; ?>

</div>