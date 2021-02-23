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
            ));

            $product = $query->get_products()[0];

            $product_name = $product->get_name();
            $product_description = $product->get_description();
            $product_img_url = ( wp_get_attachment_url($product->get_image_id()) ) ? wp_get_attachment_url($product->get_image_id()) : wc_placeholder_img_src() ;
            $product_actual_price = $product->get_price()?$product->get_price():10;
            $product_sale_price = $product->get_sale_price()?$product->get_sale_price():10;
            $currency_symbol = get_woocommerce_currency_symbol();
            $product_sale_price =$currency_symbol.$product_sale_price;
            $product_actual_price = $currency_symbol.$product_actual_price;
            $saving_percentage = 50;
            $product_url = get_permalink($product->get_id());
    
        } //if featured product
        else {
            $static_product_id = fdscf_get_acf_option("select_static_product_to_show_on_home_page");

            $product = wc_get_product($static_product_id);
                
            $product_name = $product->get_name();
            $product_description = $product->get_description();
            $product_img_url = ( wp_get_attachment_url($product->get_image_id()) ) ? wp_get_attachment_url($product->get_image_id()) : wc_placeholder_img_src() ;
            $product_actual_price = $product->get_price()?$product->get_price():10;
            $product_sale_price = $product->get_sale_price()?$product->get_sale_price():10;
            $currency_symbol = get_woocommerce_currency_symbol();
            $product_sale_price =$currency_symbol.$product_sale_price;
            $product_actual_price = $currency_symbol.$product_actual_price;
            $saving_percentage = 50;
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
