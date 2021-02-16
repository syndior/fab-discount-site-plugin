<?php if ( ! defined( 'ABSPATH' ) ) exit;
    global $post;
    $product = wc_get_product( $post->ID );

    $fd_wc_corner_banner                    = get_post_meta( $product->get_id(), 'fd_wc_corner_banner', true );
    $fd_wc_corner_banner_title              = get_post_meta( $product->get_id(), 'fd_wc_corner_banner_title', true );
    $fd_wc_corner_banner_headind            = get_post_meta( $product->get_id(), 'fd_wc_corner_banner_headind', true );
    $fd_wc_offer_expiry                     = get_post_meta( $product->get_id(), 'fd_wc_offer_expiry', true );
    $fd_wc_offer_use_global_expiry          = get_post_meta( $product->get_id(), 'fd_wc_offer_use_global_expiry', true );
    $fd_wc_offer_expiry_date                = get_post_meta( $product->get_id(), 'fd_wc_offer_expiry_date', true );
    $fd_offer_linked_product                = get_post_meta( $product->get_id(), 'fd_offer_linked_product', true );
    $fd_offer_linked_product_variation      = get_post_meta( $product->get_id(), 'fd_offer_linked_product_variation', true );
    $fd_wc_offer_voucher_expiry             = get_post_meta( $product->get_id(), 'fd_wc_offer_voucher_expiry', true );
    $fd_wc_offer_voucher_use_global_expiry  = get_post_meta( $product->get_id(), 'fd_wc_offer_voucher_use_global_expiry', true );
    $fd_wc_offer_voucher_expiry_date        = get_post_meta( $product->get_id(), 'fd_wc_offer_voucher_expiry_date', true );
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
                'label' => 'Corner Banner Tille: ',
                'value' => ( isset( $fd_wc_corner_banner_title) ? $fd_wc_corner_banner_title : '' ),
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
            if( !$product->is_type( 'fd_wc_offer' ) && !$product->is_type( 'fd_wc_offer_variable' ) ){
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