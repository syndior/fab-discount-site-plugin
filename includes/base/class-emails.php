<?php  if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Emails
{
    public function __construct(){

    }//constructor

    public function customer_mail_voucher(){

        if(isset($_POST['voucher_id']) && $_POST['voucher_id'] !=="" && isset($_POST['email_voucher'])){
        
        $current_user_id = get_current_user_id();
        $voucher_id = $_POST['voucher_id'];
        $voucher = new FD_Voucher( $voucher_id );

        if ($voucher->get_customer_id()==$current_user_id) {

            $voucher_status = FD_Voucher::get_status_string($voucher->get_status());         
            $created_at = date("M-d-Y H:i:s",strtotime($voucher->get_created_date()));
            $expires_at = date("M-d-Y H:i:s",strtotime($voucher->get_expiry_date()));

            $claim_guide = "";
            $claim_guide_enabled = get_field('claim_voucher_steps','option');
            if($claim_guide_enabled){
                $claim_guide = get_field("enter_steps_to_claim_voucher","option");
            }

            $template = '

            <h3 class="fd_text_center fd_p20">Voucher Details</h3>
            <p class = "claim_guide">
                '.$claim_guide.'
            </p>


            <table id = "table_print_voucher">
                <tr>
                    <th>Voucher Key</th>
                    <td>'.$voucher->get_key().'</td>
                </tr>
    
                <tr>
                    <th>Voucher Amount</th>
                    <td>'.wc_price($voucher->get_amount()).'</td>
                </tr>
    
                <tr>
                    <th>Voucher Status</th>
                    <td>'.$voucher_status.'</td>
                </tr>
    
                <tr>
                    <th>Order Number</th>
                    <td>'.$voucher->get_order_id().'</td>
                </tr>
    
                <tr>
                    <th>Creation Date</th>
                    <td>'.$created_at.'</td>
                </tr>
    
                <tr>
                    <th>Expiry Date</th>
                    <td>'.$expires_at.'</td>
                </tr>
    
            </table> 
    

            ';

            $user = wp_get_current_user();
            $to = $user->user_email;
            $subject = 'Fab Discount Voucher Details';
            $body = $template;
            $headers = array('Content-Type: text/html; charset=UTF-8');
            
            if(wp_mail($to, $subject, $body, $headers)){
                echo "<h3 class = 'fd_alert_success'>Mail Has Been Sent</h3>";
            }else{
                echo "<h3 class = 'fd_alert_danger'>Something Went Wrong</h3>";
            }
            
        }
    }//isset post if
    }//function


    public function customer_refund_voucher_request_mail(){

        if(isset($_POST['voucher_id']) && $_POST['voucher_id'] !=="" && isset($_POST['refund_voucher_request'])){
        
        $current_user_id = get_current_user_id();
        $voucher_id = $_POST['voucher_id'];
        $voucher = new FD_Voucher( $voucher_id );

        if ($voucher->get_customer_id()==$current_user_id) {

            $user = wp_get_current_user();
            
            $voucher_status = FD_Voucher::get_status_string($voucher->get_status());         
            $created_at = date("M-d-Y H:i:s",strtotime($voucher->get_created_date()));
            $expires_at = date("M-d-Y H:i:s",strtotime($voucher->get_expiry_date()));


            $template = '

            <h3 class="fd_text_center fd_p20">Refund Request</h3>
            <table id = "table_print_voucher">

                 <tr>
                    <th>Username</th>
                    <td>'.$user->user_login.'</td>
                </tr>

                <tr>
                    <th>User Email</th>
                    <td>'.$user->user_email.'</td>
                </tr>

                <tr>
                    <th>Voucher Key</th>
                    <td>'.$voucher->get_key().'</td>
                </tr>
    
                <tr>
                    <th>Voucher Amount</th>
                    <td>'.wc_price($voucher->get_amount()).'</td>
                </tr>
    
                <tr>
                    <th>Voucher Status</th>
                    <td>'.$voucher_status.'</td>
                </tr>
    
                <tr>
                    <th>Order Number</th>
                    <td>'.$voucher->get_order_id().'</td>
                </tr>
    
                <tr>
                    <th>Creation Date</th>
                    <td>'.$created_at.'</td>
                </tr>
    
                <tr>
                    <th>Expiry Date</th>
                    <td>'.$expires_at.'</td>
                </tr>
    
            </table> 
    

            ';

            $admin_email = get_field("acf_main_admin_email","options");

            $to = $admin_email;
            $subject = 'Voucher Refund Request';
            $body = $template;
            $headers = array('Content-Type: text/html; charset=UTF-8');
            
            if(wp_mail($to, $subject, $body, $headers)){
                if($voucher->update_status('refund_request') !== false){
                    echo "<h3 class = 'fd_alert_success'>Request Has Been Sent</h3>";
                }else{
                    echo "<h3 class = 'fd_alert_danger'>Something Went Wrong status not updated</h3>";
                }
            }else{
                echo "<h3 class = 'fd_alert_danger'>Something Went Wrong</h3>";
            }
            
        }
    }//isset post if
    }//function


}//class

?>