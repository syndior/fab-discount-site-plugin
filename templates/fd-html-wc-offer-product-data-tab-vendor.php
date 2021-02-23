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
    // $curre_time = date('H');
    $times = array(1,2,3,4,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24);
    $display = "display:none";
    if($product->is_type('fd_wc_offer')){
        $display = "display:block";
    }
?>
 <div id='fd_wc_offer_options' style = "<?php echo $display?>">
    <div class='options_group'>
        <h3>Offer Options</h3>
        <?php

            /**
             * Ceckbox, if enable show top left banner on front-end
             */
            $value = $fd_wc_corner_banner == 'fd_wc_corner_banner_enabled' ? $fd_wc_corner_banner : 'fd_wc_corner_banner_disabled';
            $checked = $fd_wc_corner_banner == 'fd_wc_corner_banner_enabled' ? 'checked' : '';
            $field = '
            <div class="dokan-form-group">
                     <label class="form-label">Set product as Selling Fast: </label>
                     <input type="checkbox" name="fd_wc_corner_banner" id="fd_wc_corner_banner" value="'.$value.'" class="dokan-form-control" '.$checked.'>                                                                                    
            </div>
            ';
            echo $field; 
           /**
             * text input, for corner banner title
             */
            $value = isset( $fd_wc_corner_banner_title) ? $fd_wc_corner_banner_title : '';
            $checked = $fd_wc_corner_banner == 'fd_wc_corner_banner_enabled' ? 'display:block' : 'display:none';
            $field = '
            <div class="dokan-form-group" style = "'.$checked.'" id = "selling_fast_banner_title">
                     <label class="form-label">Corner Banner TiTle: </label>
                     <input type="text" name="fd_wc_corner_banner_title" id="fd_wc_corner_banner_title" value="'.$value.'" class="dokan-form-control" >                                                                                    
            </div>
            ';

            echo $field; 

            /**
             * text input, for corner banner heading
             */
            $value = isset( $fd_wc_corner_banner_headind) ? $fd_wc_corner_banner_headind : '';
            $checked = $fd_wc_corner_banner == 'fd_wc_corner_banner_enabled' ? 'display:block' : 'display:none';
            $field = '
            <div class="dokan-form-group" style = "'.$checked.'" id = "selling_fast_banner_heading">
                     <label class="form-label">Corner Banner Heading: </label>
                     <input type="text" name="fd_wc_corner_banner_headind" id="fd_wc_corner_banner_headind" value="'.$value.'" class="dokan-form-control">                                                                                    
            </div>
            ';
            echo $field; 


            ?>

    
        <?php

            /**
             * Ceckbox, if enable show top left banner on front-end
             */
            $value = $fd_wc_offer_schedule=='enabled'?'enabled':'disabled';
            $checked = $fd_wc_offer_schedule == 'banner_enabled' ? 'checked' : '';
            $field = '
            <div class="dokan-form-group">
                     <label class="form-label">Live offer after specific interval: </label>
                     <input type="checkbox" name="fd_wc_offer_schedule" id="fd_wc_offer_schedule" value="'.$value.'" class="dokan-form-control" '.$checked.'>                                                                                    
            </div>
            ';
            echo $field; 

            /**
             * schedule offer to go live
             */
            $value = isset($fd_wc_offer_schedule_date)?$fd_wc_offer_schedule_date:'';
            $checked = $fd_wc_offer_schedule == 'enabled' ? 'display:block' : 'display:none';
            $field = '
            <div class="dokan-form-group" style = "'.$checked.'" id = "schedule_date">
                     <label class="form-label">Set Date for offer to go live : </label>
                     <input type="date" name="fd_wc_offer_schedule_date" id="fd_wc_offer_schedule_date" value="'.$value.'" class="dokan-form-control">                                                                                    
            </div>
            ';
            echo $field; 


            //schedule time     
            $select_options="<option value = 0>Select Time</option>";
            foreach($times as $time){
                if(isset($fd_wc_offer_schedule_time)){
                    $fd_wc_offer_schedule_time==$time?$select_options.='<option value='.$time.' selected>'.$time.'</option>':$select_options.='<option value='.$time.'>'.$time.'</option>';
                }else{
                    $select_options.='<option value='.$time.'>'.$time.'</option>';
                }
            }
            $field = '
            <div class="dokan-form-group" style = "'.$checked.'" id = "schedule_time">
            <label class="form-label">Set time for offer to go live: </label>
            <select name = "fd_wc_offer_schedule_time" id = "fd_wc_offer_schedule_time">
            '.$select_options.'
            </select>
            </div>    
            ';
            echo $field;   

            ?>
       
        <?php

            /**
             * Ceckbox, if enabled offer will expire after the defined date
             */
            $value = $fd_wc_offer_expiry == 'fd_wc_offer_expiry_enabled' ? $fd_wc_offer_expiry : 'fd_wc_offer_expiry_disabled';
            $checked = $fd_wc_offer_expiry == 'fd_wc_offer_expiry_enabled' ? 'checked' : '';
            $field = '
            <div class="dokan-form-group">
                     <label class="form-label">Enable Offer Expiry : </label>
                     <input type="checkbox" name="fd_wc_offer_expiry" id="fd_wc_offer_expiry" value="'.$value.'" class="dokan-form-control" '.$checked.'>                                                                                    
            </div>
            ';
            echo $field; 

        /**
         * Ceckbox, if enabled offer will use global site default settings for expiry
         */
        $value = $fd_wc_offer_use_global_expiry == 'fd_wc_offer_use_global_expiry_enabled' ? $fd_wc_offer_use_global_expiry : 'fd_wc_offer_use_global_expiry_disabled';
        $checked = $fd_wc_offer_use_global_expiry == 'fd_wc_offer_use_global_expiry_enabled' ? 'checked' : '';
        $checked1 = $fd_wc_offer_expiry == 'fd_wc_offer_expiry_enabled' ? 'display:block' : 'display:none';
        $field = '
        <div class="dokan-form-group" style = "'.$checked1.'" id = "global_expiry">
                 <label class="form-label">Set global expiry which is of 28 days: </label>
                 <input type="checkbox" name="fd_wc_offer_use_global_expiry" id="fd_wc_offer_use_global_expiry" value="'.$value.'" class="dokan-form-control" '.$checked.'>                                                                                    
        </div>
        ';
        echo $field; 

        /**
         * insert expiry in days
         */
        $value = isset( $fd_wc_offer_expiry_date ) && $fd_wc_offer_expiry_date > 0 ? $fd_wc_offer_expiry_date : 0 ;
        $checked = $fd_wc_offer_use_global_expiry == 'fd_wc_offer_use_global_expiry_enabled' ? '' : 'yes';
        $checked1 = $fd_wc_offer_expiry == 'fd_wc_offer_expiry_enabled' ? 'yes' : '';
        $checked2 = $fd_wc_offer_use_global_expiry=="yes"&&$fd_wc_offer_expiry=="yes"?'display:block':'display:none';
        $field = '
        <div class="dokan-form-group" style = "'.$checked2.'" id = "local_expiry">
                 <label class="form-label">Offer Expires in : </label>
                 <input type="number" name="fd_wc_offer_expiry_date" id="fd_wc_offer_expiry_date" value="'.$value.'" class="dokan-form-control" '.$checked.'>                                                                                    
        </div>
        ';
        echo $field; 

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
        <div class="dokan-form-group">
            <label class= "form-label" for="fd_offer_linked_product">This Offer Applies to: </label>
          
            <select name="fd_offer_linked_product" id="fd_offer_linked_product" class="select short" required="required">
                <option>Select a value</option>

                <?php foreach( $select_options as $option ):?>
                <?php $selected = ( $fd_offer_linked_product == $option['product_id'] ) ? 'selected' : ''; ?>
                <option value="<?=$option['product_id']?>" data-product-type="<?=$option['product_type']?>" <?=$selected?> ><?=$option['product_title']?></option>
                <?php endforeach;?>

            </select>
        </div>


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
             * Ceckbox, if enabled voucher will expire after the defined date
             */
            $value = $fd_wc_offer_voucher_expiry == 'fd_wc_offer_voucher_expiry_enabled' ? $fd_wc_offer_voucher_expiry : 'fd_wc_offer_voucher_expiry_disabled';
            $checked = $fd_wc_offer_voucher_expiry == 'fd_wc_offer_voucher_expiry_enabled' ? 'checked' : '';
            $field = '
            <div class="dokan-form-group">
                     <label class="form-label">Enable Offer Expiry : </label>
                     <input type="checkbox" name="fd_wc_offer_voucher_expiry" id="fd_wc_offer_voucher_expiry" value="'.$value.'" class="dokan-form-control" '.$checked.'>                                                                                    
            </div>
            ';
            echo $field; 

        /**
         * Ceckbox, if enabled offer will use global site default settings for expiry
         */
        $value = $fd_wc_offer_voucher_use_global_expiry == 'fd_wc_offer_voucher_use_global_expiry_enabled' ? $fd_wc_offer_voucher_use_global_expiry : 'fd_wc_offer_voucher_use_global_expiry_disabled';
        $checked = $fd_wc_offer_voucher_use_global_expiry == 'fd_wc_offer_voucher_use_global_expiry_enabled' ? 'checked' : '';
        $checked1 = $fd_wc_offer_expiry == 'fd_wc_offer_expiry_enabled' ? 'display:block' : 'display:none';
        $field = '
        <div class="dokan-form-group" style = "'.$checked1.'" id = "global_voucher_expiry">
                 <label class="form-label">Set product as Selling Fast: </label>
                 <input type="checkbox" name="fd_wc_offer_voucher_use_global_expiry" id="fd_wc_offer_voucher_use_global_expiry" value="'.$value.'" class="dokan-form-control" '.$checked.'>                                                                                    
        </div>
        ';
        echo $field; 

        /**
         * insert expiry in days
         */
        $value = isset( $fd_wc_offer_voucher_expiry_date ) && $fd_wc_offer_voucher_expiry_date > 0 ? $fd_wc_offer_voucher_expiry_date : 0 ;
        $checked = $fd_wc_offer_use_global_expiry == 'fd_wc_offer_use_global_expiry_enabled' ? '' : 'yes';
        $checked1 = $fd_wc_offer_expiry == 'fd_wc_offer_expiry_enabled' ? 'yes' : '';
        $checked2 = $fd_wc_offer_use_global_expiry=="yes"&&$fd_wc_offer_expiry=="yes"?'display:block':'display:none';
        $field = '
        <div class="dokan-form-group" style = "'.$checked2.'" id = "local_voucher_expiry">
                 <label class="form-label">Voucher Expires in : </label>
                 <input type="number" name="fd_wc_offer_voucher_expiry_date" id="fd_wc_offer_voucher_expiry_date" value="'.$value.'" class="dokan-form-control" '.$checked.'>                                                                                    
        </div>
        ';
        echo $field;           
        ?>




    </div>
 </div>