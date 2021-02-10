<?php if ( ! defined( 'ABSPATH' ) ) exit;
    /**
     * Get vouchers from database
     */

     var_dump( $_SERVER['REQUEST_URI'] );

    $page_no = isset( $_GET['fd_page_no'] ) ? $_GET['fd_page_no'] : 1;
    $item_per_page = isset( $_GET['fd_item_per_page'] ) ? $_GET['fd_item_per_page'] : 10;

    $results = FD_Voucher::get_vouchers( $page_no, $item_per_page );
    $vouchers_exists = false;
    $vouchers = null;
    $pagination = null;
    
    if( $results !== false && !empty($results['vouchers']) ){
        $vouchers_exists    = true;
        $vouchers           = $results['vouchers'];
        $pagination         = $results['pagination'];
    }

?>
<div class="wrap">
    <div class="fd_admin_management_wrapper">
        <div class="fd_admin_management_title">
            <h3>Vouchers Management</h3>
        </div>

        <div class="fd_admin_management_table_wrapper">
            <?php if( $vouchers_exists == true ):?>
            <table class="fd_admin_table" border="1">
                <tr>
                    <th>Voucher ID</th>
                    <th>Customer ID</th>
                    <th>Vendor ID</th>
                    <th>Product ID</th>
                    <th>Order ID</th>
                    <th>Amount</th>
                    <th>Expiry Status</th>
                    <th>Creation Date</th>
                    <th>Last Updates</th>
                    <th>Actions</th>
                </tr>

                <tbody>
                    <?php  foreach( $vouchers as $voucher ): ?>
                        <tr>
                            <td><?php echo $voucher->get_ID(); ?></td>
                            <td><?php echo $voucher->get_customer_id(); ?></td>
                            <td><?php echo $voucher->get_vendor_id(); ?></td>
                            <td><?php echo $voucher->get_product_id(); ?></td>
                            <td><?php echo $voucher->get_order_id(); ?></td>
                            <td><?php echo $voucher->get_amount(); ?></td>
                            <td>
                                <div class="fd_voucher_mngmnt_expiry">
                                    <?php if( $voucher->is_set_to_expire() ):?>
                                    <div>2021-02-09 12:45:40</div>
                                    <?php else:?>
                                    <div>
                                        <input type="button" value="Set Expiry">
                                    </div>
                                    <?php endif;?>
                                </div>
                            </td>
                            <td><?php echo $voucher->get_created_date(); ?></td>
                            <td><?php echo $voucher->get_updated_date(); ?></td>
                            <td>
                                <form action="/" method="POST" class="fd_voucher_mngmnt_actions">
                                    <select name="fd_voucher_status" id="">
                                        <option value="active">Active</option>
                                        <option value="credit_transferred">Convert to Store Credit</option>
                                        <option value="redeemed">Redeemed</option>
                                        <option value="block">Block</option>
                                        <option value="expired">Expired</option>
                                    </select>
                                    <input type="submit" value="Update" class="fd_voucher_mngmnt_submit">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
            <div class="fd_admin_table_pagination">
                <a href="<?=$_SERVER['REQUEST_URI']?>&fd_page_no=1" class="fd_pagination_btn">First</a>
                <a href="<?=($pagination['page_no'] <= 1) ? '#' : $_SERVER['REQUEST_URI'].'&fd_page_no='. ($pagination['page_no'] - 1)?>" class="fd_pagination_btn <?=($pagination['page_no'] <= 1) ? 'fd_pagination_disabled' : ''?>">Prev</a>
                <a href="<?=($pagination['page_no']  >= $pagination['total_pages']) ? '#' : $_SERVER['REQUEST_URI'].'&fd_page_no='. ($pagination['page_no'] + 1)?>" class="fd_pagination_btn <?=($pagination['page_no']  >= $pagination['total_pages']) ? 'fd_pagination_disabled' : ''?>">Next</a>
                <a href="<?=$_SERVER['REQUEST_URI']?>&fd_page_no=<?=$pagination['total_pages']?>" class="fd_pagination_btn">Last</a>
            </div>
            <?php else:?>
                <div>
                    <p>No voucher data available.</p>
                </div>
            <?php endif;?>
        </div>
    </div>
</div>
<?php?>