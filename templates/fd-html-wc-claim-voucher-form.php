<?php if ( ! defined( 'ABSPATH' ) ) exit;
    ob_start();
?>
<div class="fd_claim_voucher_form_wrapper">
    <form action="" id="fd_claim_voucher_form">
        <input type="text" name="fd_voucher_key" id="fd_voucher_key">
        <input type="submit" value="Check Voucher" class="fd_claim_voucher_btn" id="fd_claim_voucher_submit">
    </form>
    <div class="fd_claim_result_wrapper">
        <div class="fd_claim_results" data-active-vouchers="">
        </div>
        <input type="button" value="Claim Active Vouchers" class="fd_claim_voucher_btn">
    </div>
</div>
<?php
    $template = ob_get_clean();
?>
