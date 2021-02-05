<?php if ( ! defined( 'ABSPATH' ) ) exit;

    $vouchers_exists = true;

?>

<div class="fd-wc-account-my-vouchers-tab-content">

    <div class="fd-wc-account-my-vouchers-tab-header">
        <h3>My Vouchers</h3>
    </div>

    <?php if(!$vouchers_exists == true): ?>
    
    <div class="fd-wc-account-my-vouchers-tab-notice">
        <p>You haven't purchased any vouchers yet.</p>
    </div>
    
    <?php else: ?>
    
        <table>
            <thead>
                <tr>
                    <th>No#</th>
                    <th>Item</th>
                    <th>Voucher Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php for( $i = 1; $i <= 10; $i++ ):?>
                    <tr>
                        <td><?=$i?></td>
                        <td><a href="#"><?="Item no $i"?></a></td>
                        <td><a href="#">1234-5678-9000</a></td>
                        <td>
                            <small><a href="#">Convert to credit</a></small><br>
                            <small><a href="#">Report a problem</a></small>
                        </td>
                    </tr>
                <?php endfor;?>
            </tbody>
        </table>

    <?php endif; ?>

</div>

<?php