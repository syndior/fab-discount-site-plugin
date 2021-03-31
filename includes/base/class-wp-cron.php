<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_WP_Cron
{
    public function __construct()
    {

    // add interval to wp cron
    add_filter( 'cron_schedules', array($this,'fd_add_cron_interval') );

    // activation of cron schdule
    add_action('wp',array($this,'fd_activate_cron_minute_job'));

    // functionality to do on cron
    add_action('fd_cron_minute_hook',array($this,'fd_remove_offer_after_time_expires'));
    add_action('fd_cron_minute_hook',array($this,'offer_code_expiry'));
    add_action('fd_cron_minute_hook',array($this,'schedule_vouchers_to_go_live'));
    

    }




    // add interval to wp cron
    public function fd_add_cron_interval( $schedules ) { 
        $schedules['minute'] = array(
            'interval' => 5,
            'display'  => esc_html__( 'Every Minute' ), );
        return $schedules;
    }
    
    // creation of hook, set schedule when to repeat it
    public function fd_activate_cron_minute_job(){
        if ( ! wp_next_scheduled( 'fd_cron_minute_hook' ) ) {
              wp_schedule_event( time(), 'minute', 'fd_cron_minute_hook' );
        }
    }


    
    // fd_offer voucher expiry functionality 
    public function fd_remove_offer_after_time_expires(){
        
        $query_args = array(
            'post_type' => 'product',
            'tax_query' => array(
                 array(
                     'taxonomy' => 'product_type',
                     'field'    => 'slug',
                     'terms'    => 'fd_wc_offer', 
                 ),
             ),                 
            'meta_query' => array(
                  array(
                     'key'       => 'fd_wc_offer_voucher_expiry',
                     'value'     => 'fd_wc_offer_voucher_expiry_enabled',
                     'compare'   => '=',
                 ),
             ),
          );
         
         $results = get_posts( $query_args );

         if(sizeof($results)>0){

            foreach ($results as $key => $result) {
                $product_id = $result->ID;
               
                //getting product creation date for 
                $product_creation_date = strtotime($result->post_date);
 
                //getting current date
                $current_date = time();

                $global_expiry = get_post_meta($product_id,'fd_wc_offer_voucher_use_global_expiry',true);

                //getting difference b/w dates in form of days
                $difference = $current_date - $product_creation_date;
                $difference = round($difference / (60 * 60 * 24));

                //checking if global expiry is enabled or not
                if($global_expiry == "fd_wc_offer_voucher_use_global_expiry_enabled"){
                    
                    $global_expiry_days = round(get_field('global_voucher_expiry','options'));

                    //CHECKKING IF DIFFERENCE BETWEEN dates are greater than global expiry set by admin 
                    if($current_date > $global_expiry_days){
                    
                        $this->product_is_not_purchaseable($product_id);                        
                    }

                }else{

                    $expiry_days = round( get_post_meta($product_id,'fd_wc_offer_voucher_expiry_date',true) );

                    //CHECKKING IF DIFFERENCE BETWEEN dates are greater than global expiry set by admin 
                    if($difference > $expiry_days){
                        
                        $this->product_is_not_purchaseable($product_id);

                    }

                }

       
            }

         }

    }//fd_remove_offer_after_time_expires

    //updating status of stock so that product/offer will not be purchaseabel
    public function product_is_not_purchaseable($product_id){

        $out_of_stock_staus = 'outofstock';

        // 1. Updating the stock quantity
        update_post_meta($product_id, '_stock', 0);

        // 2. Updating the stock quantity
        update_post_meta( $product_id, '_stock_status', wc_clean( $out_of_stock_staus ) );

        // 3. Updating post term relationship
        wp_set_post_terms( $product_id, 'outofstock', 'product_visibility', true );

        // And finally (optionally if needed)
        // wc_delete_product_transients( $product_id ); // Clear/refresh the variation cache


    }//product_is_not_purchasable


    //offer(code=>which is not redeemed expiry ) expiry
    public function offer_code_expiry(){

        //it will get all vouchers with status "active" and offer expiry enabled
        $vouchers = FD_Voucher::get_all_vouchers_wrt_status("active",1);

        //getting current date
        $current_date = strtotime(date('Y-m-d H:i:s'));
        
        //looping through all vouchers
        foreach ($vouchers as $key => $voucher) {

            //voucher expiry date
            $voucher_expiry_date = strtotime($voucher->expires_at);

   


            //comparing current date and voucher expiry date
            if($current_date>$voucher_expiry_date){

                $voucher_id = $voucher->fd_voucher_id;
                $customer_id = (int)$voucher->customer_id;
                $new_voucher = new FD_voucher($voucher_id);
                // $new_voucher->update_status( 'expired' );

                // getting customer wallet
                $wallet = new FD_Wallet( $customer_id );
                
                // converting voucher amount to wallet vredit
                $wallet->convert_voucher_to_credit( $voucher_id );
                
            }
        }


    } //offer_code_expiry

    public function schedule_vouchers_to_go_live(){

        $query_args = array(
            'post_type' => 'product',
            'tax_query' => array(
                 array(
                     'taxonomy' => 'product_type',
                     'field'    => 'slug',
                     'terms'    => 'fd_wc_offer', 
                 ),
             ),                 
            'meta_query' => array(
                  array(
                     'key'       => 'fd_wc_offer_schedule',
                     'value'     => 'enabled',
                     'compare'   => '=',
                 ),
             ),
          );
         
         $results = get_posts( $query_args );

         ob_start();
         var_dump($results);
         $log = ob_get_clean();
         error_log($log);

         if(sizeof($results)>0){

            foreach ($results as $key => $result) {
                $product_id = $result->ID;
               
                //getting product creation date for 
                $product_creation_date = strtotime($result->post_date);
 
                //getting current date and time
                $current_date_time = strtotime( date('Y-m-d H:i') );
                // $current_date_time = date('Y-m-d H:i');

                //getting schedule date
                $schedule_date = get_post_meta($product_id,'fd_wc_offer_schedule_date',true);

                //getting schedule time
                $schedule_time = get_post_meta($product_id,'fd_wc_offer_schedule_time',true);

                //getting schedule date and time
                $schedule_date_time =  strtotime( $schedule_date." ".$schedule_time );
                // $schedule_date_time =  $schedule_date." ".$schedule_time;                

                //comparing current date and voucher schedule date to make product live
                if($current_date_time>=$schedule_date_time){    

                    //disabling scheduling
                    update_post_meta( $product_id, 'fd_wc_offer_schedule', 'disabled');
                    
                    //offer/product will be visible after schedule time reached
                    $product = wc_get_product($product_id);
                    $product->set_catalog_visibility('visible');
                    $product->save();
                
                }//if

       
            }//loop

         }//if


    }//function


}//class

new FD_WP_Cron();