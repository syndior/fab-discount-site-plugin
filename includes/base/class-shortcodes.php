<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Shortcodes
{

    public function __construct()
    {
        // renders product slider
        add_shortcode( 'fd_product_slider', [ $this, 'fd_product_slider' ] );
        
        // redners product discount element
        add_shortcode( 'fd_product_discount', [ $this, 'fd_product_discount' ] );
        
        // renders product badges element
        add_shortcode( 'fd_product_badges', [ $this, 'fd_product_badges' ] );
    }

    public function fd_product_slider( $atts )
    {
        //atts defaults
        extract(shortcode_atts(array(
            'type'              => 'popular',
            'limit'             => 10,
            'columns'           => 4,
            'controls'          => true,
            'category'          => 0,
            'veiw_all_btn'      => true,
            'veiw_all_btn_txt'  => 'View All Products',
        ), $atts));

        //output variable
        $output = '';

        $args = array(
            'post_type'     => 'product',
            'numberposts'   => $limit,
            'post_status'   => 'publish',
            'orderby'       => 'date',
            'order'         => 'DESC',
        );

        if( $type == 'trending' ){

        }elseif( $type == 'popular' ){

        }elseif( $type == 'category' ){
            
        }elseif( $type == 'related' ){
            
        }

        $products_array = get_posts( $args );

        if( !empty($products_array) ){

            //set slider type class on parent element
            switch( $type ){
                case 'trending':
                    $slider_class = 'fd_slider_type_trending';
                break;

                case 'popular':
                    $slider_class = 'fd_slider_type_popular';
                break;

                case 'category':
                    $slider_class = 'fd_slider_type_category';
                break;

                case 'related':
                    $slider_class = 'fd_slider_type_related';
                break;
            }

            $output .= '<div class="fd_product_slider_wrapper '. $slider_class .'">';
            $output .= '<div class="splide">';
            $output .= '<div class="splide__track">';
            $output .= '<div class="splide__list">';
            foreach( $products_array as $product ){

                $product_id = '1';
                $product_url = '#';
                $product_featured = true;

                $output .= '<a class="splide__slide fd_slider_item" href="'.$product_url.'">';
            
                    $output .= '<div class="fd_slider_item_image">';
                        if( $product_featured == true ){
                            $output .= '<div class="fd_slider_item_featured_element"></div>';
                        }
                    $output .= '</div>';

                    if( $type == 'trending' ){
                        $output .= do_shortcode( '[fd_product_discount alignment="left" product_id="'.$product_id.'"]' );
                    }

                    if( $type == 'trending' ){
                        $title_alignment = 'text-align: left;';
                    }else{
                        $title_alignment = 'text-align: center;';
                    }

                    $output .= '<h3 class="fd_slider_item_title" style="'.$title_alignment.'">Product Title</h3>';

                    if( $type == 'trending' ){
                        $output .= '<p class="fd_slider_item_except">lorem ipsum placeholder text</p>';
                    }

                    if( $type !== 'trending' ){
                        $output .= do_shortcode( '[fd_product_discount show_percentage="false" product_id="'.$product_id.'"]' );
                    }

                    if( $type == 'trending' ){
                        $output .= do_shortcode( '[fd_product_badges show_borders="false" limit="3" product_id="'.$product_id.'"]' );
                    }

                    $output .= '<a class="fd_slider_item_btn" href="#">View Discount</a>';
                
                $output .= '</a>';

            }
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';

            if( $veiw_all_btn == true ){

                $view_all_link = '#';

                $output .= '<div class="fd_product_slider_button_wrapper">';
                    $output .= '<a class="fd_product_slider_button" href="'. $view_all_link .'">' . $veiw_all_btn_txt . '</a>';
                $output .= '</div>';

            }

            $output .= '</div>';

        }else{
            $output = '<p>Error: no product data avaialbe.</p>';
        }

        return $output;

    }


    public function fd_product_discount( $atts )
    {
        global $post;
        if( $post->post_type !== 'product' ){
            return;
        }

        //output variable
        $output = '';

        //atts defaults
        extract(shortcode_atts(array(
            'product_id'        => $post->ID,
            'show_percentage'   => true,
            'alignment'         => 'center',
        ), $atts));

        $product_current_price       = '100';
        $product_old_price           = '100';
        $product_discount_percentage = '100';
        $alignment_styles            = '';

        if( $alignment == 'center' ){
            $alignment_styles = 'justify-content: center;';
        }elseif( $alignment == 'left' ){
            $alignment_styles = 'justify-content: flex-start;';
        }
        
        $output .= '<div class="fd_product_discount" style="'. $alignment_styles .'">';
        
            $output .= '<div class="fd_current_amount">';
                $output .= '<p class="fd_amount_label">now</p>';
                $output .= '<p class="fd_amount_value">'. $product_current_price .'</p>';
            $output .= '</div>';

            $output .= '<div class="fd_before_amount">';
                $output .= '<p class="fd_amount_label">was</p>';
                $output .= '<p class="fd_amount_value">'. $product_old_price .'</p>';
            $output .= '</div>';

            if( $show_percentage == true ){

                $output .= '<div class="fd_discount_percentage">';
                    $output .= '<p class="fd_amount_label">you save</p>';
                    $output .= '<p class="fd_amount_value">'. $product_discount_percentage .'</p>';
                $output .= '</div>';

            }
        
        $output .= '</div>';

        return $output;
    }



    public function fd_product_badges( $atts )
    {
        global $post;
        if( $post->post_type !== 'product' ){
            return;
        }

        //output variable
        $output = '';

        //atts defaults
        extract(shortcode_atts(array(
            'product_id'    => $post->ID,
            'show_borders'  => true,
            'alignment'     => 'center',
            'limit'         => 0,
        ), $atts));

        //set element alignment styles
        if( $alignment == 'center' ){
            $alignment_styles = 'justify-content: center;';
        }elseif( $alignment == 'left' ){
            $alignment_styles = 'justify-content: flex-start;';
        }
        
        //set element item border class
        if( $show_borders == true ){
            $has_border = 'fd_badge_has_border';
        }else{
            $has_border = '';
        }

        //badges array
        $product_badges = array();

        //badges conditionals
        $is_selling_fast    = true;
        $is_ending_soon     = true;
        $is_stock_enabled   = true;
        $is_low_in_stock    = true;
        $is_has_discount    = true;

        if( $is_selling_fast == true ){
            $badge = array(
                'badge_icon_class'  => 'fd_product_badge_selling_fast',
                'badge_text'        => 'Selling Fast',
            );
            $product_badges[] = $badge;
        }

        if( $is_ending_soon == true ){
            $badge = array(
                'badge_icon_class'  => 'fd_product_badge_ending_soon',
                'badge_text'        => 'Ending Soon',
            );
            $product_badges[] = $badge;
        }

        if( $is_stock_enabled == true ){
            $badge = array(
                'badge_icon_class'  => 'fd_product_badge_sold_count',
                'badge_text'        => '162 Sold',
            );
            $product_badges[] = $badge;
        }

        if( $is_low_in_stock == true ){
            $badge = array(
                'badge_icon_class'  => 'fd_product_badge_low_stock',
                'badge_text'        => 'Low Stock',
            );
            $product_badges[] = $badge;
        }
        
        if( $is_has_discount == true ){
            $badge = array(
                'badge_icon_class'  => 'fd_product_badge_discount',
                'badge_text'        => '50% Saving',
            );
            $product_badges[] = $badge;
        }


        //if limit is set remove extra elements
        if( !empty($product_badges) && $limit > 0 ){
            if( count($product_badges) > $limit ){
                $product_badges = array_slice( $product_badges, 0, $limit );
            }
        }


        if( !empty($product_badges) ){

            $output .= '<div class="fd_product_badges_wrapper" style="'.$alignment_styles.'">';
        
            foreach( $product_badges as $badges ){

                $output .= '<div class="fd_product_badge_item '. $has_border .'">';
                    $output .= '<div class="fd_product_badge_icon '. $badges['badge_icon_class'] .'"></div>';
                    $output .= '<p class="fd_product_badge_text">'. $badges['badge_text'] .'</p>';
                $output .= '</div>';

            }
            
            $output .= '</div>';

        }


        return $output;

    }

}

new FD_Shortcodes();