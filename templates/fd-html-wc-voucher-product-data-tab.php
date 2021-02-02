<?php
    global $post;
    $product = wc_get_product( $post->ID );

    $fd_wc_corner_banner                = get_post_meta( $product->get_id(), 'fd_wc_corner_banner' )[0];
    $fd_wc_corner_banner_title          = get_post_meta( $product->get_id(), 'fd_wc_corner_banner_title' )[0];
    $fd_wc_corner_banner_headind        = get_post_meta( $product->get_id(), 'fd_wc_corner_banner_headind' )[0];
    
    $fd_wc_voucher_expiry               = get_post_meta( $product->get_id(), 'fd_wc_voucher_expiry' )[0];
    $fd_wc_voucher_use_global_expiry    = get_post_meta( $product->get_id(), 'fd_wc_voucher_use_global_expiry' )[0];
    
    $fd_wc_voucher_expiry_date          = get_post_meta( $product->get_id(), 'fd_wc_voucher_expiry_date' )[0];
?>
 <div id='fd_wc_voucher_options' class='panel woocommerce_options_panel'>
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
             * Ceckbox, if enabled voucher will expire after the defined date
             */
            $args = array(
                'id' => 'fd_wc_voucher_expiry',
                'label' => 'Enable Voucher Expiry: ',
                'value' => ( $fd_wc_voucher_expiry == 'fd_wc_voucher_expiry_enabled' ? $fd_wc_voucher_expiry : 'fd_wc_voucher_expiry_disabled' ),
                'cbvalue' => 'fd_wc_voucher_expiry_enabled',
                'desc_tip' => 'true',
                'description' => 'If this option is enabled, this voucher will expire after the below defined date (Will be converted into customer\'s wallet credit)'
            );
            woocommerce_wp_checkbox( $args );

        ?>

        <?php

        /**
         * Ceckbox, if enabled voucher will use global site default settings for expiry
         */
        $args = array(
            'id' => 'fd_wc_voucher_use_global_expiry',
            'label' => 'Use Global Site Settings: ',
            'value' => ( $fd_wc_voucher_use_global_expiry == 'fd_wc_voucher_use_global_expiry_enabled' ? $fd_wc_voucher_use_global_expiry : 'fd_wc_voucher_use_global_expiry_disabled' ),
            'cbvalue' => 'fd_wc_voucher_use_global_expiry_enabled',
            'desc_tip' => 'true',
            'description' => 'If this option is enabled, this voucher will use the global site settings for expiry duration calculations'
        );
        woocommerce_wp_checkbox( $args );

        ?>

        <?php

        /**
         * text input, for corner banner heading
         */
        $args = array(
            'id' => 'fd_wc_voucher_expiry_date',
            'label' => 'Voucher Expires in: ',
            'value' => ( isset( $fd_wc_voucher_expiry_date ) && $fd_wc_voucher_expiry_date > 0 ? $fd_wc_voucher_expiry_date : 0 ),
            'placeholder' => '',
            'desc_tip' => 'true',
            'description' => 'This Voucher will expire after this number of Days',
            'type' => 'number'
        );
        woocommerce_wp_text_input($args);

        ?>

    </div>
 </div>
 <?php