<?php if ( ! defined( 'ABSPATH' ) ) exit;

    $vouchers_exists = true;
    $current_user_id = get_current_user_id();
    $results =  FD_Voucher::get_current_customer_vouchers($current_user_id);
    $claim_voucher_page_id = get_field('set_claim_voucher_page','options');
    $claim_voucher_page_url = get_permalink($claim_voucher_page_id);
    // echo "<h1>ff</h1>".$results;
?>

<div class="fd-wc-account-my-vouchers-tab-content">

    <div class="fd-wc-account-my-vouchers-tab-header">
        <h3>My Vouchers</h3>
    </div>

    <?php if(!is_array($results)): ?>
    
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
                    $url = "my-account/claim?key=".$result['fd_voucher_key']."";
                    ?>
                    <tr>
                    <!-- 1234-5678-9000-0000 -->
                        <td>
                            <form action="<?php echo $claim_voucher_page_url?>" method = "POST">
                                <input type="hidden" name="voucher_ids[]" value = "<?php echo $result['fd_voucher_id']?>">
                                <input type="submit" style="background:transparent;border:none;cursor:pointer" value="<?php echo $result['fd_voucher_key'];?>">
                            </form>   
                        </td>
                        <td><?php echo $result['expires_at'];?></td>
                        <td><a href="#">Print</a></td>
                        <td><a href="#">Email</a></td>
                        <td><a href="#">Convert to credit</a></td>
                    </tr>

                <?php }?>
            </tbody>
        </table>

    <?php endif; ?>

</div>