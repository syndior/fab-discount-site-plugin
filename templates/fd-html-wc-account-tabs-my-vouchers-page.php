<?php if ( ! defined( 'ABSPATH' ) ) exit;

    $vouchers_exists = true;
    $current_user_id = get_current_user_id();
    $results =  FD_Voucher::get_current_customer_vouchers($current_user_id);
    echo "<pre>".var_dump($results[0])."</pre>";
?>

<div class="fd-wc-account-my-vouchers-tab-content">

    <div class="fd-wc-account-my-vouchers-tab-header">
        <h3>My Vouchers</h3>
    </div>

    <?php if(sizeof($results) <= 0): ?>
    
    <div class="fd-wc-account-my-vouchers-tab-notice">
        <p>You haven't purchased any vouchers yet.</p>
    </div>
    
    <?php else: ?>
    
        <table>
            <thead>
                <tr>
                    <th>Voucher Code</th>
                    <th>Voucher Expiry</th>
                    <th colspan="3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $key => $result) {
                    $url = "my-account/claim?key=".$result->fd_voucher_key."";
                    ?>
                    <tr>
                    <!-- 1234-5678-9000-0000 -->
                        <td><a href=<?php echo $url;?>><?php echo $result->fd_voucher_key;?></a></td>
                        <td><?php echo $result->expires_at?></td>
                        <td><a href="#">Print</a></td>
                        <td><a href="#">Email</a></td>
                        <td><a href="#">Convert to credit</a></td>
                    </tr>

                <?php }?>

                    
                <?php for( $i = 1; $i <= 10; $i++ ):?>
                    <!-- <tr>
                        <td><a href="#">1234-5678-9000-0000</a></td>
                        <td>22 Feb, 2021</td>
                        <td><a href="#">Print</a></td>
                        <td><a href="#">Email</a></td>
                        <td><a href="#">Convert to credit</a></td>
                    </tr> -->
                <?php endfor;?>
            </tbody>
        </table>

    <?php endif; ?>

</div>