<?php if ( ! defined( 'ABSPATH' ) ) exit;

    $vouchers_exists = true;
    if(is_user_logged_in()){

    //mailing voucher if getting request    
    $email_obj = new FD_Emails();
    $email_obj->customer_mail_voucher();
    $email_obj->customer_refund_voucher_request_mail();
    
    $current_user_id = get_current_user_id();
    $results =  FD_Voucher::get_current_customer_vouchers($current_user_id);

    $claim_voucher_page_id = get_field('set_claim_voucher_page','options');
    $claim_voucher_page_url = get_permalink($claim_voucher_page_id);

    $print_voucher_page_id = get_field('set_print_voucher_page','options');
    $print_voucher_page_url = get_permalink($print_voucher_page_id);

    $claim_guide = "";
    $claim_guide_enabled = get_field('claim_voucher_steps','option');
    if($claim_guide_enabled){
        $claim_guide = get_field("enter_steps_to_claim_voucher","option");
    }
?>

<div class="fd-wc-account-my-vouchers-tab-content">

    <div class="fd-wc-account-my-vouchers-tab-header">
        <h3>My Vouchers</h3>

        <p class = "claim_guide">
            <?=$claim_guide?>
        </p>
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
                            <?php if($result['status'] == "active"){?>
                            <form action="<?php echo $claim_voucher_page_url;?>" method = "POST">
                                <input type="hidden" name="voucher_ids[]" value = "<?php echo $result['fd_voucher_id'];?>">
                                <input type="submit" style="background:transparent;border:none;cursor:pointer" value="<?php echo $result['fd_voucher_key'];?>">
                            </form>
                            <?php }else{ ?>
                            <form>
                                <input type="button" style="background:transparent;border:none;cursor:pointer" value="<?php echo $result['fd_voucher_key'];?>">
                            </form>
                            <?php }?>
                        </td>
                        <td><?php echo $result['expires_at'];?></td>
                        <td>
                            <form action="<?php echo $print_voucher_page_url;?>" method = "POST">
                                <input type="hidden" name="voucher_id" value = "<?php echo $result['fd_voucher_id']?>">
                                <input type="submit" name = "print_voucher" style="background:transparent;border:none;cursor:pointer" value="Print">
                            </form>
                        </td>
                        
                        <td>
                            <form method = "POST">
                                <input type="hidden" name="voucher_id" value = "<?php echo $result['fd_voucher_id']?>">
                                <input type="submit" name = "email_voucher" style="background:transparent;border:none;cursor:pointer" value="Email">
                            </form>
                         </td>
                        <td>
                         <?php
                         if($result['status'] == "active"){
                         ?>  
                        <form method = "POST">
                              <input type="hidden" name="voucher_id" value = "<?php echo $result['fd_voucher_id']?>">
                              <input type="submit" name = "refund_voucher_request" style="background:transparent;border:none;cursor:pointer" value="Convert to credit">
                        </form>
                        <?php }//if status is active
                                elseif ($result['status'] == "credit_transferred") {
                                    echo "<p class = 'text-success'>Credit Transfered</p>";
                                }
                        ?>
                        </td>
                    </tr>

                <?php }?>
            </tbody>
        </table>

    <?php endif;
    }//is_user_logged_in
    ?>

</div>