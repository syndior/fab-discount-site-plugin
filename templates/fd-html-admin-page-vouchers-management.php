<?php if ( ! defined( 'ABSPATH' ) ) exit;
    /**
     * Handle form submitions / CRUD Operations
     */
    if( isset( $_POST['fd_wp_nonce'] ) && isset( $_POST['fd_voucher_id'] ) && isset( $_POST['fd_update_type'] ) ){

        if( wp_verify_nonce( $_POST['fd_wp_nonce'], 'fd_voucher_mngmnt' ) ){

            $voucher_id = (int)$_POST['fd_voucher_id'];
            $voucher = new FD_Voucher( $voucher_id );
    
            if( $_POST['fd_update_type'] == 'fd_status_update' ){
    
                if( isset( $_POST['fd_voucher_status'] ) ){
                    $status = $_POST['fd_voucher_status'];
    
                    switch ($status) {
                        case 'active':
                        case 'redeemed':
                        case 'blocked':
                        case 'expired':
                            if( !( $voucher->update_status( $status ) !== false ) ){
                                echo "<script>alert('An Error occured while performing this action');</script>";
                            }
                            break;
                        case 'credit_transferred':
                            $wallet = new FD_Wallet( $voucher->get_customer_id() );
                            if( $wallet->convert_voucher_to_credit( $voucher->get_ID() ) == false ){
                                echo "<script>alert('An Error occured while performing this action');</script>";
                            }
                            break;
                        
                        default:
                            echo "<script>alert('An Error occured while performing this action');</script>";
                            break;
                    }
    
                }
    
            }elseif ( $_POST['fd_update_type'] == 'fd_set_to_expire' ) {
                if( isset( $_POST['fd_voucher_expiry_date'] ) ){
    
                    $expiry_date = $_POST['fd_voucher_expiry_date'];
                    $set_expire = true;
                    if( $voucher->set_to_expire( $set_expire, $expiry_date ) !== false ){
                        echo "<script>alert('Voucher Updated!');</script>";
                    }else{
                        echo "<script>alert('An Error occured while performing this action');</script>";
                    }
                }else{
                    echo "<script>alert('An Error occured while performing this action');</script>";
                }
            }

        }

    }

    /**
     * Get vouchers from database
     */
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


    /**
     * Handle Voucher Filters
     */
    if( isset( $_POST['fd_wp_nonce'] ) && isset( $_POST['fd_request_type'] ) && $_POST['fd_request_type'] == 'filter' ){

        if( wp_verify_nonce( $_POST['fd_wp_nonce'], 'fd_voucher_filter' ) ){

            if( $_POST['submit'] == 'fd_add_filter' ){

                $filter         = true;
                $filter_args    = array(
                    'voucher_id'        => ( isset( $_POST['voucher_id'] )              && $_POST['voucher_id'] !== ''              ) ? $_POST['voucher_id']              : NULL ,
                    'customer_id'       => ( isset( $_POST['customer_id'] )             && $_POST['customer_id'] !== ''             ) ? $_POST['customer_id']             : NULL ,
                    'vendor_id'         => ( isset( $_POST['vendor_id'] )               && $_POST['vendor_id'] !== ''               ) ? $_POST['vendor_id']               : NULL ,
                    'product_id'        => ( isset( $_POST['product_id'] )              && $_POST['product_id'] !== ''              ) ? $_POST['product_id']              : NULL ,
                    'order_id'          => ( isset( $_POST['order_id'] )                && $_POST['order_id'] !== ''                ) ? $_POST['order_id']                : NULL ,
                    'voucher_key'       => ( isset( $_POST['voucher_key'] )             && $_POST['voucher_key'] !== ''             ) ? $_POST['voucher_key']             : NULL ,
                    'voucher_amount'    => ( isset( $_POST['voucher_amount'] )          && $_POST['voucher_amount'] !== ''          ) ? $_POST['voucher_amount']          : NULL ,
                    'voucher_status'    => ( isset( $_POST['voucher_status'] )          && $_POST['voucher_status'] !== ''          ) ? $_POST['voucher_status']          : NULL ,
                    'expiry_start'      => ( isset( $_POST['voucher_expiry_min'] )      && $_POST['voucher_expiry_min'] !== ''      ) ? $_POST['voucher_expiry_min']      : NULL ,
                    'expiry_end'        => ( isset( $_POST['voucher_expiry_max'] )      && $_POST['voucher_expiry_max'] !== ''      ) ? $_POST['voucher_expiry_max']      : NULL ,
                    'created_start'     => ( isset( $_POST['voucher_created_at_min'] )  && $_POST['voucher_created_at_min'] !== ''  ) ? $_POST['voucher_created_at_min']  : NULL ,
                    'created_end'       => ( isset( $_POST['voucher_created_at_max'] )  && $_POST['voucher_created_at_max'] !== ''  ) ? $_POST['voucher_created_at_max']  : NULL ,
                    'updated_start'     => ( isset( $_POST['voucher_last_upated_min'] ) && $_POST['voucher_last_upated_min'] !== '' ) ? $_POST['voucher_last_upated_min'] : NULL ,
                    'updated_end'       => ( isset( $_POST['voucher_last_upated_max'] ) && $_POST['voucher_last_upated_max'] !== '' ) ? $_POST['voucher_last_upated_max'] : NULL ,
                );
    
                $results = FD_Voucher::get_vouchers( $page_no, $item_per_page, $filter, $filter_args );
                $vouchers_exists = false;
                $vouchers = null;
                $pagination = null;
                
                if( $results !== false && !empty($results['vouchers']) ){
                    $vouchers_exists    = true;
                    $vouchers           = $results['vouchers'];
                    $pagination         = $results['pagination'];
                }

            }

        }

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
                    <th>Voucher Key</th>
                    <th>Amount</th>
                    <th>Voucher Status</th>
                    <th>Expiry Status</th>
                    <th>Creation Date</th>
                    <th>Last Updates</th>
                    <th>Actions</th>
                </tr>

                <tr>
                    <form method="POST" id="fd_filter_form">
                        <input form="fd_filter_form" type="hidden" name="fd_wp_nonce" value="<?=wp_create_nonce('fd_voucher_filter')?>">
                        <input form="fd_filter_form" type="hidden" name="fd_request_type" value="filter">
                    </form>
                    <td>
                        <input form="fd_filter_form" type="number" min="1" name="voucher_id" id="" style="width:70px;">
                    </td>
                    <td>
                        <input form="fd_filter_form" type="number" min="1" name="customer_id" id="" style="width:70px;">
                    </td>
                    <td>
                        <input form="fd_filter_form" type="number" min="1" name="vendor_id" id="" style="width:70px;">
                    </td>
                    <td>
                        <input form="fd_filter_form" type="number" min="1" name="product_id" id="" style="width:70px;">
                    </td>
                    <td>
                        <input form="fd_filter_form" type="number" min="1" name="order_id" id="" style="width:70px;">
                    </td>
                    <td>
                        <input form="fd_filter_form" type="text" name="voucher_key" id="">
                    </td>
                    <td>
                        <input form="fd_filter_form" type="number" min="0" name="voucher_amount" id="" style="width:70px;">
                    </td>
                    <td>
                        <select form="fd_filter_form" name="voucher_status" id="">
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="credit_transferred">Convert to Store Credit</option>
                            <option value="redeemed">Redeemed</option>
                            <option value="blocked">Blocked</option>
                            <option value="expired">Expired</option>
                        </select>
                    </td>
                    <td>
                        <div style="display: inline-flex; flex-direction: column; width: 49%;">
                            <label for="">Start</label>
                            <input form="fd_filter_form" type="date" name="voucher_expiry_min" id="">
                        </div>
                        
                        <div style="display: inline-flex; flex-direction: column; width: 49%;">
                            <label for="">End</label>
                            <input form="fd_filter_form" type="date" name="voucher_expiry_max" id="">
                        </div>
                    </td>
                    <td>
                        <div style="display: inline-flex; flex-direction: column; width: 49%;">
                            <label for="">Start</label>
                            <input form="fd_filter_form" type="date" name="voucher_created_at_min" id="">
                        </div>
                        
                        <div style="display: inline-flex; flex-direction: column; width: 49%;">
                            <label for="">End</label>
                            <input form="fd_filter_form" type="date" name="voucher_created_at_max" id="">
                        </div>
                    </td>
                    <td>
                        <div style="display: inline-flex; flex-direction: column; width: 49%;">
                            <label for="">Start</label>
                            <input form="fd_filter_form" type="date" name="voucher_last_upated_min" id="">
                        </div>
                        
                        <div style="display: inline-flex; flex-direction: column; width: 49%;">
                            <label for="">End</label>
                            <input form="fd_filter_form" type="date" name="voucher_last_upated_max" id="">
                        </div>
                    </td>
                    <td>
                        <div style="display:flex; flex-direction: row; align-items: stretch;">
                            <button form="fd_filter_form" type="submit" name="submit" value="fd_add_filter" style="width: 78%;margin: auto; padding: 10px 16px;">Search</button>
                        </div>
                    </td>
                </tr>

                <tbody>
                    <?php  foreach( $vouchers as $voucher ): ?>
                        <tr>
                            <td><?php echo $voucher->get_ID(); ?></td>
                            <td><?php echo $voucher->get_customer_id(); ?></td>
                            <td><?php echo $voucher->get_vendor_id(); ?></td>
                            <td><?php echo $voucher->get_product_id(); ?></td>
                            <td><?php echo $voucher->get_order_id(); ?></td>
                            <td><?php echo $voucher->get_key(); ?></td>
                            <td><?php echo  get_woocommerce_currency_symbol() . round( $voucher->get_amount(), 2 ); ?></td>
                            <td>
                                <?php
                                    $status = $voucher->get_status();
                                    switch ($status) {
                                        case 'active':
                                            echo 'Active';
                                            break;
                                        case 'redeemed':
                                            echo 'Redeemed';
                                            break;
                                        case 'credit_transferred':
                                            echo 'Converted to store credit';
                                            break;
                                        case 'expired':
                                            echo 'Expired';
                                            break;
                                        case 'blocked':
                                            echo 'Blocked';
                                            break;
                                    }
                                ?>
                            </td>
                            <td>
                                <div class="fd_voucher_mngmnt_expiry">
                                    <?php if($voucher->get_status() == "active"): ?>
                                        <?php if( $voucher->is_set_to_expire() ):?>
                                            <div class="fd_expiry_text"><span><?=$voucher->get_expiry_date()?></span></div>
                                        <?php endif;?>
                                        <form method="POST" class="fd_set_expiry_form">
                                            <input 
                                            type="date" 
                                            id="fd_voucher_expiry_date" 
                                            name="fd_voucher_expiry_date"
                                            value="<?= $voucher->is_set_to_expire() ? date('Y-m-d', strtotime( $voucher->get_expiry_date() )) : date('Y-m-d', strtotime(' +1 day'))?>"
                                            min="<?=date("Y-m-d")?>">
                                            <input type="hidden" name="fd_update_type" value="fd_set_to_expire">
                                            <input type="hidden" name="fd_voucher_id" value="<?=$voucher->get_ID()?>">
                                            <input type="hidden" name="fd_wp_nonce" value="<?=wp_create_nonce('fd_voucher_mngmnt')?>">
                                            <input type="submit" value="Save">
                                        </form>

                                        <?php $btn_text = ( $voucher->is_set_to_expire() == true ) ? 'Update Expiry' : 'Set Expiry' ;?>
                                        <input type="button" data-btn-text="<?=$btn_text?>" value="<?=$btn_text?>" class="fd_set_expiry_form_toggle">

                                    <?php else:?>
                                        <div><span>Can't set expiry for vouchers with this status</span></div>
                                    <?php endif;?>
                                </div>
                            </td>
                            <td><?php echo $voucher->get_created_date(); ?></td>
                            <td><?php echo $voucher->get_updated_date(); ?></td>
                            <td>
                                <?php if($voucher->get_status() == 'credit_transferred' || $voucher->get_status() == 'redeemed' || $voucher->get_status() == 'expired'): ?>
                                    <div><span>No actions are available for this voucher</span></div>
                                <?php else:?>
                                <form method="POST" class="fd_voucher_mngmnt_actions">
                                    <select name="fd_voucher_status" id="">
                                        <option  <?=( $voucher->get_status() == "active" ) ? 'selected' : '';?> value="active">Active</option>
                                        <option  <?=( $voucher->get_status() == "credit_transferred" ) ? 'selected' : '';?> value="credit_transferred">Convert to Store Credit</option>
                                        <option  <?=( $voucher->get_status() == "redeemed" ) ? 'selected' : '';?> value="redeemed">Redeemed</option>
                                        <option  <?=( $voucher->get_status() == "blocked" ) ? 'selected' : '';?> value="blocked">Blocked</option>
                                        <option  <?=( $voucher->get_status() == "expired" ) ? 'selected' : '';?> value="expired">Expired</option>
                                    </select>
                                    <input type="hidden" name="fd_update_type" value="fd_status_update">
                                    <input type="hidden" name="fd_voucher_id" value="<?=$voucher->get_ID()?>">
                                    <input type="hidden" name="fd_wp_nonce" value="<?=wp_create_nonce('fd_voucher_mngmnt')?>">
                                    <input type="submit" value="Update" class="fd_voucher_mngmnt_submit">
                                </form>
                                <?php endif;?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
            <div class="fd_admin_table_pagination">
                <div class="fd_admin_table_pagination_info"><p><?php echo "Viewing Page: {$pagination['page_no']} of {$pagination['total_pages']} Pages | Total Vouchers: {$pagination['total_vouchers']}";?></p></div>
                <div class="fd_admin_table_pagination_btns">
                    <a href="<?=$_SERVER['REQUEST_URI']?>&fd_page_no=1" class="fd_pagination_btn">First</a>
                    <a href="<?=($pagination['page_no'] <= 1) ? '#' : $_SERVER['REQUEST_URI'].'&fd_page_no='. ($pagination['page_no'] - 1)?>" class="fd_pagination_btn <?=($pagination['page_no'] <= 1) ? 'fd_pagination_disabled' : ''?>">Prev</a>
                    <a href="<?=($pagination['page_no']  >= $pagination['total_pages']) ? '#' : $_SERVER['REQUEST_URI'].'&fd_page_no='. ($pagination['page_no'] + 1)?>" class="fd_pagination_btn <?=($pagination['page_no']  >= $pagination['total_pages']) ? 'fd_pagination_disabled' : ''?>">Next</a>
                    <a href="<?=$_SERVER['REQUEST_URI']?>&fd_page_no=<?=$pagination['total_pages']?>" class="fd_pagination_btn">Last</a>
                </div>
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