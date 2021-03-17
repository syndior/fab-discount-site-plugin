<?php if ( ! defined( 'ABSPATH' ) ) exit;
    global $post;
    $product = wc_get_product( $post->ID );

    //banner
    $fd_wc_corner_banner                    = get_post_meta( $product->get_id(), 'fd_wc_corner_banner', true );
    $fd_wc_corner_banner_title              = get_post_meta( $product->get_id(), 'fd_wc_corner_banner_title', true );
    $fd_wc_corner_banner_headind            = get_post_meta( $product->get_id(), 'fd_wc_corner_banner_headind', true );
    
    //scheduling 
    $fd_wc_offer_schedule                   = get_post_meta( $product->get_id(), 'fd_wc_offer_schedule', true );
    $fd_wc_offer_schedule_date              = get_post_meta( $product->get_id(), 'fd_wc_offer_schedule_date', true );
    $fd_wc_offer_schedule_time              = get_post_meta( $product->get_id(), 'fd_wc_offer_schedule_time', true );
    //expiry
    $fd_wc_offer_expiry                     = get_post_meta( $product->get_id(), 'fd_wc_offer_expiry', true );
    $fd_wc_offer_use_global_expiry          = get_post_meta( $product->get_id(), 'fd_wc_offer_use_global_expiry', true );
    $fd_wc_offer_expiry_date                = get_post_meta( $product->get_id(), 'fd_wc_offer_expiry_date', true );
    $fd_offer_linked_product                = get_post_meta( $product->get_id(), 'fd_offer_linked_product', true );
    $fd_offer_linked_product_variation      = get_post_meta( $product->get_id(), 'fd_offer_linked_product_variation', true );
    $fd_wc_offer_voucher_expiry             = get_post_meta( $product->get_id(), 'fd_wc_offer_voucher_expiry', true );
    $fd_wc_offer_voucher_use_global_expiry  = get_post_meta( $product->get_id(), 'fd_wc_offer_voucher_use_global_expiry', true );
    $fd_wc_offer_voucher_expiry_date        = get_post_meta( $product->get_id(), 'fd_wc_offer_voucher_expiry_date', true );

    $current_date = date('Y-m-d');

    $times = array(1,2,3,4,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24);

?>
 <div id='fd_wc_offer_options' class='panel woocommerce_options_panel'>
    <div class='options_group'>

        <?php

            /**
             * Ceckbox, if enable show top left banner on front-end
             */
            $args = array(
                'id' => 'fd_wc_corner_banner',
                'label' => 'Set product as "Selling Fast": ',
                'value' => ( $fd_wc_corner_banner == 'fd_wc_corner_banner_enabled' ? $fd_wc_corner_banner : 'fd_wc_corner_banner_disabled' ),
                'cbvalue' => 'fd_wc_corner_banner_enabled',
                'desc_tip' => 'true',
                'description' => 'Enable this option to display the top corner banner on the front-end of this product'
            );
            woocommerce_wp_checkbox( $args );

        ?>

        <?php

            /**
             * text input, for corner banner title
             */
            $args = array(
                'id' => 'fd_wc_corner_banner_title',
                'label' => 'Corner Banner TiTle: ',
                'value' => (isset( $fd_wc_corner_banner_title) ? $fd_wc_corner_banner_title : ''),
                'placeholder' => '',
                'desc_tip' => 'true',
                'description' => 'Displayed on top of the corner banner as a title',
                'type' => 'text'
            );
            woocommerce_wp_text_input($args);

        ?>

        <?php

            /**
             * text input, for corner banner heading
             */
            $args = array(
                'id' => 'fd_wc_corner_banner_headind',
                'label' => 'Corner Banner Heading: ',
                'value' => ( isset( $fd_wc_corner_banner_title) ? $fd_wc_corner_banner_title : '' ),
                'placeholder' => '',
                'desc_tip' => 'true',
                'description' => 'Displayed after the title of the corner banner as a heading',
                'type' => 'text'
            );
            woocommerce_wp_text_input($args);

        ?>
    
        <?php

            /**
             * schedule offer to go live
             */
            $args = array(
                'id' => 'fd_wc_offer_schedule',
                'label' => 'Enable Offer Scheduling: ',
                'desc_tip' => 'true',
                'description' => 'Live offer after specific interval.',
                'value' => ( $fd_wc_offer_schedule=='enabled'?'enabled':'disabled'),
                'cbvalue' => 'enabled',
            );
            woocommerce_wp_checkbox( $args );

            if($fd_wc_offer_schedule=='enabled'){
                $in_active_dom = 'display:';
            }else{
                $in_active_dom = 'display:none!important';
            }

            //schedule date     
            $value = isset($fd_wc_offer_schedule_date)?$fd_wc_offer_schedule_date:'';
            $date_field = '
            <p class="form-field" id = "schedule_date" style = "'.$in_active_dom.'">
            <label for="fd_wc_offer_schedule_date">Set Date for offer to go live: </label>
            <input type="date" class="short " style="" name="fd_wc_offer_schedule_date" id="fd_wc_offer_schedule_date" value="'.$value.'" min="'.$current_date.'">
            </p>    
            ';
             echo $date_field;   

            //schedule time     
            $select_options="<option value = 0>Select Time</option>";
            foreach($times as $time){
                if(isset($fd_wc_offer_schedule_time)){
                    $fd_wc_offer_schedule_time==$time?$select_options.='<option value='.$time.' selected>'.$time.'</option>':$select_options.='<option value='.$time.'>'.$time.'</option>';
                }else{
                    $select_options.='<option value='.$time.'>'.$time.'</option>';
                }
            }
            $value = isset($fd_wc_offer_schedule_time)?$fd_wc_offer_schedule_time:'';
            $time_field = '
            <p class="form-field" id = "schedule_time" style = "'.$in_active_dom.'">
            <label for="fd_wc_offer_schedule_time">Set time for offer to go live: </label>
            <input type="time" class="short " style="" name="fd_wc_offer_schedule_time" id="fd_wc_offer_schedule_time" value="'.$value.'">
            </p>    
            ';
             echo $time_field;   
            //  <select name = "fd_wc_offer_schedule_time" id = "fd_wc_offer_schedule_time">
            //  '.$select_options.'
            //  </select>
 
            ?>
        <?php

            /**
             * Ceckbox, if enabled offer will expire after the defined date
             */
            $args = array(
                'id' => 'fd_wc_offer_expiry',
                'label' => 'Enable Offer Expiry: ',
                'value' => ( $fd_wc_offer_expiry == 'fd_wc_offer_expiry_enabled' ? $fd_wc_offer_expiry : 'fd_wc_offer_expiry_disabled' ),
                'cbvalue' => 'fd_wc_offer_expiry_enabled',
                'desc_tip' => 'true',
                'description' => 'If this option is enabled, this offer will expire after the below defined date (won\'t be visiable on the store-front)'
            );
            woocommerce_wp_checkbox( $args );

        ?>

        <?php

        /**
         * Ceckbox, if enabled offer will use global site default settings for expiry
         */
        $args = array(
            'id' => 'fd_wc_offer_use_global_expiry',
            'label' => 'Use Global Site Settings: ',
            'value' => ( $fd_wc_offer_use_global_expiry == 'fd_wc_offer_use_global_expiry_enabled' ? $fd_wc_offer_use_global_expiry : 'fd_wc_offer_use_global_expiry_disabled' ),
            'cbvalue' => 'fd_wc_offer_use_global_expiry_enabled',
            'desc_tip' => 'true',
            'description' => 'If this option is enabled, this offer will use the global site settings for expiry duration calculations'
        );
        woocommerce_wp_checkbox( $args );

        ?>

        <?php

        /**
         * text input, for corner banner heading
         */
        $args = array(
            'id' => 'fd_wc_offer_expiry_date',
            'label' => 'Offer Expires in: ',
            'value' => ( isset( $fd_wc_offer_expiry_date ) && $fd_wc_offer_expiry_date > 0 ? $fd_wc_offer_expiry_date : 0 ),
            'placeholder' => '',
            'desc_tip' => 'true',
            'description' => 'This Offer will expire after this number of Days',
            'type' => 'number'
        );
        woocommerce_wp_text_input($args);

        ?>

        <?php

        /**
         * Linked product with this offer
         */
        $args = array(
            'post_type'      => 'product',
            'numberposts'    => -1,
        );

        $products = get_posts($args);
        $select_options = array();

        foreach( $products as $product ){
            $product = wc_get_product( $product->ID );
            if( !$product->is_type( 'fd_wc_offer' ) ){
                $option['product_id']           = $product->get_ID();
                $option['product_title']        = $product->get_title();
                $option['product_type']         = $product->get_type();
                $select_options[]               = $option;
            }
        }
        ?>
        <p class="form-field">
            <label for="fd_offer_linked_product">This Offer Applies to: </label>
          
            <select name="fd_offer_linked_product" id="fd_offer_linked_product" class="select short" required="required">
                <option>Select a value</option>

                <?php foreach( $select_options as $option ):?>
                <?php $selected = ( $fd_offer_linked_product == $option['product_id'] ) ? 'selected' : ''; ?>
                <option value="<?=$option['product_id']?>" data-product-type="<?=$option['product_type']?>" <?=$selected?> ><?=$option['product_title']?></option>
                <?php endforeach;?>

            </select>
        </p>


        <?php
            /**
             * Linked product selected variation
             */
        ?>
        <p id="fd_offer_linked_product_variation_wrapper" class="form-field" style="display: none;">
            <label for="fd_offer_linked_product_variation">Select Variation: </label>
            <select name="fd_offer_linked_product_variation" id="fd_offer_linked_product_variation" class="select short" required="required" data-current-value="<?=$fd_offer_linked_product_variation?>">
                <option>Select a value</option>
            </select>
        </p>

        <?php

            /**
             * Ceckbox, if enabled vouchers generated from this offer will expire after the defined duartion
             */
            $args = array(
                'id' => 'fd_wc_offer_voucher_expiry',
                'label' => 'Enable Voucher Expiry: ',
                'value' => ( $fd_wc_offer_voucher_expiry == 'fd_wc_offer_voucher_expiry_enabled' ? $fd_wc_offer_voucher_expiry : 'fd_wc_offer_voucher_expiry_disabled' ),
                'cbvalue' => 'fd_wc_offer_voucher_expiry_enabled',
                'desc_tip' => 'true',
                'description' => 'If this option is enabled, vouchers generated from this offer will expire after the below defined date (Will be converted into customer\'s wallet credit)'
            );
            woocommerce_wp_checkbox( $args );

        ?>

        <?php

        /**
         * Ceckbox, if enabled offer will use global site default settings for expiry
         */
        $args = array(
            'id' => 'fd_wc_offer_voucher_use_global_expiry',
            'label' => 'Use Global Site Settings: ',
            'value' => ( $fd_wc_offer_voucher_use_global_expiry == 'fd_wc_offer_voucher_use_global_expiry_enabled' ? $fd_wc_offer_voucher_use_global_expiry : 'fd_wc_offer_voucher_use_global_expiry_disabled' ),
            'cbvalue' => 'fd_wc_offer_voucher_use_global_expiry_enabled',
            'desc_tip' => 'true',
            'description' => 'If this option is enabled, vouchers generated from this offer will use the global site settings for expiry duration calculations'
        );
        woocommerce_wp_checkbox( $args );

        ?>

        <?php

        /**
         * text input, for corner banner heading
         */
        $args = array(
            'id' => 'fd_wc_offer_voucher_expiry_date',
            'label' => 'Vouchers generated from this offer Expires in: ',
            'value' => ( isset( $fd_wc_offer_voucher_expiry_date ) && $fd_wc_offer_voucher_expiry_date > 0 ? $fd_wc_offer_voucher_expiry_date : 0 ),
            'placeholder' => '',
            'desc_tip' => 'true',
            'description' => 'Vouchers generated from this offer will expire after this number of Days',
            'type' => 'number'
        );
        woocommerce_wp_text_input($args);

        ?>

    </div>
 </div>