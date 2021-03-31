<?php if (!defined('ABSPATH')) exit;

function fdscf_get_acf_option(string $field_key = '')
{
    return get_field($field_key, 'option');
}

function fdscf_get_product_meta(string $meta_key = ""){
    global $post;
    $post_id = $post->ID;
    $value = get_post_meta($post_id,$meta_key,true);
    return $value; 
}
function fdscf_set_option_for_hero_section()
{

    $section_enabled = fdscf_get_acf_option("enable_hero_section_for_featured_or_static_product");
    $featured_or_static = fdscf_get_acf_option("static_or_featured_product");
    
    if ((int)$section_enabled == 1) {
        if ($featured_or_static == "featured") {

            // Get 10 most recent product IDs in date descending order //
            $query = new WC_Product_Query(array(
                'limit' => 1,
                'orderby' => 'date',
                'order' => 'DESC',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_type',
                        'field'    => 'slug',
                        'terms'    => 'fd_wc_offer',
                    ),
                ),  
            ));

            $product = $query->get_products()[0];

            // changings
            $product_id = $product->get_id();
            $product_created_at = $product->get_date_created();
            
            $offer_expiry = get_post_meta($product_id,'fd_wc_offer_expiry',true);
            $global_expiry = get_post_meta($product_id,'fd_wc_offer_use_global_expiry',true);
            $expiry_date = "";
            if($offer_expiry == "fd_wc_offer_expiry_enabled"){
                $offer_expiry = "fd_wc_offer_expiry_enabled";
                if($global_expiry == "fd_wc_offer_use_global_expiry_enabled"){
                
                    $expiry_days = round(get_field('global_offer_expiry','options'));
                    
                }else{
    
                    $expiry_days = get_post_meta($product_id,'fd_wc_offer_expiry_date',true);
                
                }
                $plus_days = " +".$expiry_days." day";
                $expiry_date = strtotime(date("Y-m-d H:i:s", strtotime($product_created_at)) . $plus_days);
                $expiry_date = date('Y-m-d H:i:s',$expiry_date);
    
            }//offer Expiry
            // changings
            
            
            //product initials
            $product_img_url = ( wp_get_attachment_url($product->get_image_id()) ) ? wp_get_attachment_url($product->get_image_id()) : wc_placeholder_img_src() ;
            $product_description = $product->get_description();
            $currency_symbol = get_woocommerce_currency_symbol();
            $product_url = get_permalink($product->get_id());
            $product_name = $product->get_name();
            $sold_qty = $product->get_sold_individually();
            
            if($sold_qty == ""){
                $sold_qty=0;
            }
            //checking stock
            $stock_status = "In Stock";
            if($product->managing_stock()){
               $low_stock_amount = (int)wc_get_low_stock_amount( $product );
               $stock_amount = (int)$product->get_stock_quantity();
               if($stock_amount>$low_stock_amount){
                   $stock_status = "In Stock";
               }else if($stock_amount<=0){
                   $stock_status = "Out Of Stock";
               }else{
                   $stock_status = "Ending Soon";
               }//status msg if else
            }//if managing stock
            //checking stock

            if($product->is_type('fd_wc_offer')){

                $product_actual_price = $product->get_price()?$product->get_price():'';
                $product_sale_price = $product->get_sale_price()?$product->get_sale_price():'';
                if($product_actual_price != "" && $product_sale_price!=""){
                    $saving_percentage = (int)(100-(($product_sale_price/$product_actual_price)*100));    
                }else {
                    $saving_percentage = 0;
                    $product_actual_price = 0;
                    $product_sale_price = 0;

                }
                $product_sale_price =$currency_symbol.$product_sale_price;
                $product_actual_price = $currency_symbol.$product_actual_price;

            }
    
        } //if featured product
        else {
            $product_id = fdscf_get_acf_option("select_static_product_to_show_on_home_page");

            $product = wc_get_product($product_id);
    
            
            // changings
            $product_created_at = $product->get_date_created();
            
            $offer_expiry = get_post_meta($product_id,'fd_wc_offer_expiry',true);
            $global_expiry = get_post_meta($product_id,'fd_wc_offer_use_global_expiry',true);
            $expiry_date = "";
            if($offer_expiry == "fd_wc_offer_expiry_enabled"){
                
                if($global_expiry == "fd_wc_offer_use_global_expiry_enabled"){
                
                    $expiry_days = round(get_field('global_offer_expiry','options'));
                    
                }else{
    
                    $expiry_days = get_post_meta($product_id,'fd_wc_offer_expiry_date',true);
                
                }
                $plus_days = " +".$expiry_days." day";
                $expiry_date = strtotime(date("Y-m-d H:i:s", strtotime($product_created_at)) . $plus_days);
                $expiry_date = date('Y-m-d H:i:s',$expiry_date);
    
            }//offer Expiry
            // changings


            //product initials
            $product_img_url = ( wp_get_attachment_url($product->get_image_id()) ) ? wp_get_attachment_url($product->get_image_id()) : wc_placeholder_img_src() ;
            $product_description = $product->get_description();
            $currency_symbol = get_woocommerce_currency_symbol();
            $product_url = get_permalink($product->get_id());
            $product_name = $product->get_name();

            $sold_qty = $product->get_sold_individually();
            if($sold_qty == ""){
                $sold_qty=0;
            }

            //checking stock
            $stock_status = "In Stock";
            if($product->managing_stock()){
               $low_stock_amount = (int)wc_get_low_stock_amount( $product );
               $stock_amount = (int)$product->get_stock_quantity();
               if($stock_amount>$low_stock_amount){
                   $stock_status = "In Stock";
               }else if($stock_amount<=0){
                   $stock_status = "Out Of Stock";
               }else{
                   $stock_status = "Ending Soon";
               }//status msg if else
            }//if managing stock
            //checking stock


            if($product->is_type('fd_wc_offer')){

                $product_actual_price = $product->get_price()?$product->get_price():'';
                $product_sale_price = $product->get_sale_price()?$product->get_sale_price():'';
                if($product_actual_price != "" && $product_sale_price!=""){
                    $saving_percentage = (int)(100-(($product_sale_price/$product_actual_price)*100));    
                }else {
                    $saving_percentage = 0;
                    $product_actual_price = 0;
                    $product_sale_price = 0;
                }
                $product_sale_price =$currency_symbol.$product_sale_price;
                $product_actual_price = $currency_symbol.$product_actual_price;

            }


        } //if static product

        update_option('fdscf_hero_product_url', $product_url);
        update_option('fdscf_hero_product_price', $product_actual_price);
        update_option('fdscf_hero_product_saving_per', $saving_percentage);
        update_option('fdscf_hero_product_sale_price', $product_sale_price);
        update_option('fdscf_hero_product_image', $product_img_url);
        update_option('fdscf_hero_product_title', $product_name);
        update_option('fdscf_hero_product_description', $product_description);
        update_option('fdscf_hero_product_sold_count', $sold_qty);
        update_option('fdscf_hero_product_stock_status', $stock_status);
        update_option('fdscf_hero_product_expiry_date', $expiry_date);
        update_option('fdscf_hero_product_offer_expiry', $offer_expiry);

    } //if section enabled

}
// add_action('wp_head', 'fdscf_set_option_for_hero_section');
add_action('init', 'fdscf_set_option_for_hero_section');

function fdscf_get_hero_product_option(string $field_key = '')
{
    return get_option($field_key);
}

function fd_product_stock_status(){
     global $post;
     $product = wc_get_product($post->ID);
     $print = "In Stock";
     if($product->managing_stock()){
        $low_stock_amount = (int)wc_get_low_stock_amount( $product );
        $stock_amount = (int)$product->get_stock_quantity();
        if($stock_amount>$low_stock_amount){
            $print = "In Stock";
        }else if($stock_amount<=0){
            $print = "Out Of Stock";
        }else{
            $print = "Ending Soon";
        }//status msg if else
     }//if managing stock
     echo $print; 
}

function fd_product_sold(){
    global $post;
    // $product = wc_get_product($post->ID);
    $sold = get_post_meta($post->ID,'_sold_individually',true);

    echo $sold;
    // _sold_individually
    // get_sold_individually()
}

function fd_product_saving_percentage(){
    global $post;
    $product = wc_get_product($post->ID);
    if($product->is_type('fd_wc_offer')){
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        if($regular_price != "" && $sale_price!=""){
            $savings = (int)(100-(($sale_price/$regular_price)*100));     
        }else{
            $savings = 0;
        }
    }elseif ($product->is_type('fd_wc_offer_variable')) {
        $savings_array = array();
        $product_ids = $product->get_children();
        foreach ($product_ids as $key => $product_id) {
            $variab_product = wc_get_product($product_id);
            $regular_price = $variab_product->get_regular_price();
            $sale_price = $variab_product->get_sale_price();
            if($regular_price != "" && $sale_price!=""){
                $savings_array[$key] = (int)(100-(($sale_price/$regular_price)*100));     
            }else{
                $savings_array[$key] = 0;     
            }
        }

        if(sizeof($savings_array)>0){
            $savings = max($savings_array);
        }else {
            $savings=0;
        }

    }
    echo $savings;
}


