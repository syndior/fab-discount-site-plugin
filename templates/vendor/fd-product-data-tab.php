<?php if ( ! defined( 'ABSPATH' ) ) exit;?>


<div class="dokan-product-inventory dokan-edit-row " id = "FD_vendor_offer">
    <div class="dokan-section-heading" data-togglehandler="dokan_product_inventory">
        <h2><i class="fa fa-cubes" aria-hidden="true"></i> Inventory</h2>
        <p>Manage inventory for this product.</p>
        <a href="#" class="dokan-section-toggle">
            <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true" style="margin-top: 9px;"></i>
        </a>
        <div class="dokan-clearfix"></div>
    </div>

    <div class="dokan-section-content">

        <div class="content-half-part dokan-form-group hide_if_variation">
            <label for="_sku" class="form-label"></span></label>
                        <input type="text" name="_sku" id="_sku" value="" class="dokan-form-control" placeholder="">
                    </div>

        <div class="content-half-part hide_if_variable">
            <label for="_stock_status" class="form-label">Stock Status</label>

                        <select name="_stock_status" id="_stock_status" class="dokan-form-control">
                                    <option value="instock" selected="selected">In Stock</option>
                                    <option value="outofstock">Out of Stock</option>
                                    <option value="onbackorder">On Backorder</option>
                            </select>

                    </div>

        <div class="dokan-clearfix"></div>

                <div class="dokan-form-group hide_if_variation hide_if_grouped">
            
            <label class="" for="_manage_stock">
                <input type="hidden" name="_manage_stock" value="no">
                <input name="_manage_stock" id="_manage_stock" value="yes" type="checkbox">
                Enable product stock management            </label>

                    </div>

        <div class="show_if_stock dokan-stock-management-wrapper dokan-form-group dokan-clearfix" style="display: none;">

            <div class="content-half-part hide_if_variation">
                <label for="_stock" class="form-label">Stock quantity</label>
                <input type="number" class="dokan-form-control" name="_stock" placeholder="" value="0" min="0" step="1">
            </div>

                        <div class="content-half-part hide_if_variation">
                <label for="_low_stock_amount" class="form-label">Low stock threshold</label>
                <input type="number" class="dokan-form-control" name="_low_stock_amount" placeholder="" value="0" min="0" step="1">
            </div>
            
            <div class="content-half-part hide_if_variation last-child">
                <label for="_backorders" class="form-label">Allow Backorders</label>

                            <select name="_backorders" id="_backorders" class="dokan-form-control">
                                    <option value="no" selected="selected">Do not allow</option>
                                    <option value="notify">Allow but notify customer</option>
                                    <option value="yes">Allow</option>
                            </select>

                        </div>
            <div class="dokan-clearfix"></div>
        </div><!-- .show_if_stock -->
        
        <div class="dokan-form-group hide_if_grouped">
            <label class="" for="_sold_individually">
                <input name="_sold_individually" id="_sold_individually" value="yes" type="checkbox">
                Allow only one quantity of this product to be bought in a single order            </label>
        </div>

                            
                        
    </div><!-- .dokan-side-right -->
</div>
