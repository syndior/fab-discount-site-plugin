<?php

    if(isset($_POST['voucher_id']) && $_POST['voucher_id'] !=="" && isset($_POST['print_voucher'])){
        
     if( is_user_logged_in() ){
        $voucher_id = $_POST['voucher_id'];
        $current_user_id = get_current_user_id();
        $voucher = new FD_Voucher( $voucher_id );
        if ($voucher->get_customer_id()==$current_user_id) {        
    
            $voucher_status = FD_Voucher::get_status_string($voucher->get_status());         
            $created_at = date("M-d-Y H:i:s",strtotime($voucher->get_created_date()));
            $expires_at = date("M-d-Y H:i:s",strtotime($voucher->get_expiry_date()));

    ?>

        <h3 class="fd_text_center fd_p20">Voucher Details</h3>
        <table id = "table_print_voucher" >
            <tr>
                <th>Voucher Key</th>
                <td><?=$voucher->get_key();?></td>
            </tr>

            <tr>
                <th>Voucher Amount</th>
                <td><?=wc_price($voucher->get_amount());?></td>
            </tr>

            <tr>
                <th>Voucher Status</th>
                <td><?=$voucher_status;?></td>
            </tr>

            <tr>
                <th>Order Number</th>
                <td><?=$voucher->get_order_id();?></td>
            </tr>

            <tr>
                <th>Creation Date</th>
                <td><?=$created_at;?></td>
            </tr>

            <tr>
                <th>Expiry Date</th>
                <td><?=$expires_at;?></td>
            </tr>

        </table> 

        <script>
            window.print();
        </script>
<?php
            }//if voucher is of current user
        }//is user logged in
    }//isset if
?>