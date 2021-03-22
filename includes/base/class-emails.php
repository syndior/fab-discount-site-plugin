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

            $template = '
            <h3 class="fd_text_center fd_p20">Voucher Details</h3>
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

}//class

?>