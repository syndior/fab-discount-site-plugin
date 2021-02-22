<?php if (!defined('ABSPATH')) exit;

function fdscf_get_acf_option(string $field_key = '')
{
    return get_field($field_key, 'option');
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
                'type' => 'fd_wc_offer',
                'type' => 'fd_wc_offer_variable',
            ));

            $product = $query->get_products()[0];

            $product_name = $product->get_name();
            $product_description = $product->get_description();
            $product_img_url = ( wp_get_attachment_url($product->get_image_id()) ) ? wp_get_attachment_url($product->get_image_id()) : wc_placeholder_img_src() ;
            $product_url = get_permalink($product->get_id());
            $currency_symbol = get_woocommerce_currency_symbol();
            $product_actual_price = $product->get_price()?$product->get_price():'';
            $product_sale_price = $product->get_sale_price()?$product->get_sale_price():'';
            $saving_percentage = (int)(100-(($product_sale_price/$product_actual_price)*100));
            $product_sale_price =$currency_symbol.$product_sale_price;
            $product_actual_price = $currency_symbol.$product_actual_price;

            // if($product->is_type('fd_wc_offer')){
            //     $product_actual_price = $product->get_price()?$product->get_price():'';
            //     $product_sale_price = $product->get_sale_price()?$product->get_sale_price():'';
            //     $saving_percentage = (int)(100-(($product_sale_price/$product_actual_price)*100));
            //     $product_sale_price =$currency_symbol.$product_sale_price;
            //     $product_actual_price = $currency_symbol.$product_actual_price;
            // }elseif($product->is_type('fd_wc_offer_variable')) {
            //     //goal is that we have to filter max saving percentage values
            //     $product_actual_price_array = array();// (1) in this we will save all all actual prices
            //     $product_sale_prices_array = array();// (2) in this we will save all all sales prices
            //     $savings_array = array();// (3) in this we will save all saving percentages

            //     $product_ids = $product->get_children();
            //     foreach ($product_ids as $key => $product_id) {
            //         $variab_product = wc_get_product($product_id);
            //         $regular_price = $variab_product->get_regular_price();
            //         $sale_price = $variab_product->get_sale_price();
                    
            //         $product_actual_price_array[$key] = $regular_price;//saving actual prices in this array related to (1)
            //         $product_sale_prices_array[$key] = $regular_price;//saving sales prices in this array related to (2)

            //         //checking whether both prices are inserted or not
            //         if($regular_price != "" && $sale_price!=""){
            //             $savings_array[$key] = (int)(100-(($sale_price/$regular_price)*100));//saving off percentage in this array related to (3)
            //         }else{
            //             $savings_array[$key] = 0;//saving off percentage in this array related to (3)     
            //         }//if els for calculating percentage of saving
                
            //     }//foreach for variations

            //     //cehcking if saving array has some values
            //     if(sizeof($savings_array)>0){
            //         $saving_percentage = max($savings_array)//(3);
            //         $saving_percentage_key = array_search($saving_percentage,$savings_array)//(3)getting index of that highest savings for sale and actual price  
            //         $product_sale_price = $product_sale_prices_array[$saving_percentage_key]//(2)getting value of that sale price which is having highest percentage of saving  
            //         $product_actual_price = $product_actual_price_array[$saving_percentage_key]//(2)getting value of that sale price which is having highest percentage of saving  
                    
            //     }else {
            //         $saving_percentage=0;
            //         $product_sale_price='';
            //         $product_actual_price='';
            //     }
            // }//main else to process fd_wc_offer_variable
            
    
        } //if featured product
        else {
            $static_product_id = fdscf_get_acf_option("select_static_product_to_show_on_home_page");

            $product = wc_get_product($static_product_id);
                
            $product_name = $product->get_name();
            $product_description = $product->get_description();
            $product_img_url = ( wp_get_attachment_url($product->get_image_id()) ) ? wp_get_attachment_url($product->get_image_id()) : wc_placeholder_img_src() ;
            $product_actual_price = $product->get_price()?$product->get_price():10;
            $product_sale_price = $product->get_sale_price()?$product->get_sale_price():10;
            $saving_percentage = ($product_sale_price/$product_actual_price)*100;

            $currency_symbol = get_woocommerce_currency_symbol();
            $product_sale_price =$currency_symbol.$product_sale_price;
            $product_actual_price = $currency_symbol.$product_actual_price;
            $product_url = get_permalink($product->get_id());
        } //if static product

        update_option('fdscf_hero_product_url', $product_url);
        update_option('fdscf_hero_product_price', $product_actual_price);
        update_option('fdscf_hero_product_saving_per', $saving_percentage);
        update_option('fdscf_hero_product_sale_price', $product_sale_price);
        update_option('fdscf_hero_product_image', $product_img_url);
        update_option('fdscf_hero_product_title', $product_name);
        update_option('fdscf_hero_product_description', $product_description);

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

// fd_wc_offer_variable
// fd_wc_offer
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
            }//if els for calculating percentage of saving
        
        }//foreach for variations

        if(sizeof($savings_array)>0){
            $savings = max($savings_array);
        }else {
            $savings=0;
        }

    }
    echo $savings;
}