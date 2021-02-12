window.addEventListener('DOMContentLoaded', function () {
    let fdCustomFieldsWrapper = document.querySelector('#fd_wc_offer_options');
    if (fdCustomFieldsWrapper !== null) {

        //hides selling fast fields on page load if condition is true
        let sellingFastField = fdCustomFieldsWrapper.querySelector('#fd_wc_corner_banner');
        let sellingFastTitle = fdCustomFieldsWrapper.querySelector('.fd_wc_corner_banner_title_field');
        let sellingFastHeading = fdCustomFieldsWrapper.querySelector('.fd_wc_corner_banner_headind_field');

        if (!sellingFastField.checked == true) {
            sellingFastTitle.style.display = 'none';
            sellingFastHeading.style.display = 'none';
        }

        //hide offer expiry field on page load
        let offerExpiryField = fdCustomFieldsWrapper.querySelector('#fd_wc_offer_expiry');
        let offerUseGlobalSettings = fdCustomFieldsWrapper.querySelector('.fd_wc_offer_use_global_expiry_field');
        let offerUseGlobalSettingsField = offerUseGlobalSettings.querySelector('#fd_wc_offer_use_global_expiry');
        let offerExpiryDuration = fdCustomFieldsWrapper.querySelector('.fd_wc_offer_expiry_date_field');

        if (!offerExpiryField.checked == true) {
            offerUseGlobalSettings.style.display = 'none';
            offerExpiryDuration.style.display = 'none';
        }

        if (offerUseGlobalSettingsField.checked == true || !offerExpiryField.checked == true) {
            offerExpiryDuration.style.display = 'none';
        }

        //gide voucher expiry field in page load
        let voucherExpiryField = fdCustomFieldsWrapper.querySelector('#fd_wc_offer_voucher_expiry');
        let voucherUseGlobalSettings = fdCustomFieldsWrapper.querySelector('.fd_wc_offer_voucher_use_global_expiry_field');
        let voucherUseGlobalSettingsField = voucherUseGlobalSettings.querySelector('#fd_wc_offer_voucher_use_global_expiry');
        let voucherExpiryDuration = fdCustomFieldsWrapper.querySelector('.fd_wc_offer_voucher_expiry_date_field');

        if (!voucherExpiryField.checked == true) {
            voucherUseGlobalSettings.style.display = 'none';
            voucherExpiryDuration.style.display = 'none';
        }

        if (voucherUseGlobalSettingsField.checked == true || !voucherExpiryField.checked == true) {
            voucherExpiryDuration.style.display = 'none';
        }

        fdCustomFieldsWrapper.addEventListener('click', function () {
            if (!sellingFastField.checked == true) {
                sellingFastTitle.style.display = 'none';
                sellingFastHeading.style.display = 'none';
            } else {
                sellingFastTitle.style.display = 'block';
                sellingFastHeading.style.display = 'block';
            }

            //offer expiry field click logic
            if (!offerExpiryField.checked == true) {
                offerUseGlobalSettings.style.display = 'none';
                offerExpiryDuration.style.display = 'none';
            } else {
                offerUseGlobalSettings.style.display = 'block';
                offerExpiryDuration.style.display = 'block';
            }

            if ((offerUseGlobalSettingsField.checked == true) || (!offerExpiryField.checked == true)) {
                offerExpiryDuration.style.display = 'none';
            } else {
                offerExpiryDuration.style.display = 'block';
            }

            //voucher expiry click logic
            if (!voucherExpiryField.checked == true) {
                voucherUseGlobalSettings.style.display = 'none';
                voucherExpiryDuration.style.display = 'none';
            }else{
                voucherUseGlobalSettings.style.display = 'block';
                voucherExpiryDuration.style.display = 'block';
            }

            if ((voucherUseGlobalSettingsField.checked == true) || (!voucherExpiryField.checked == true)) {
                voucherExpiryDuration.style.display = 'none';
            } else {
                voucherExpiryDuration.style.display = 'block';
            }

        }, false);


        /**
         * Loads variations data in the admin product option offer linked product dropdown
         */
        let selectedProduct = document.querySelector('#fd_offer_linked_product');
        let selectedVariationOptions = document.querySelector('#fd_offer_linked_product_variation_wrapper');
        if (selectedProduct !== null && selectedVariationOptions !== null) {

            /**
             * Loads in variations on pageload
             */
            let selectedElement = selectedProduct.options[selectedProduct.selectedIndex];
            if (selectedElement.dataset.productType == 'variable') {
                let productId = selectedElement.value;
                getVariationOptionsAjax(productId).then(function (variations) {
                    let variationsDropdown = selectedVariationOptions.querySelector('#fd_offer_linked_product_variation');
                    variationsDropdown.innerHTML = '';
                    let defaultElement = `<option>Select a value</option>`;

                    variationsDropdown.innerHTML += defaultElement;
                    variations.forEach(function (option) {
                        let selected = (variationsDropdown.dataset.currentValue == option.product_id) ? 'selected' : '';
                        let optionELement = `<option value="${option.product_id}" ${selected}>${option.product_title}</option>`;
                        variationsDropdown.innerHTML += optionELement;
                    });
                    selectedVariationOptions.style.display = 'block';
                }, function (error) {
                    console.log(error);
                });

            }

            /**
             * Makes ajax call on input change event
             */
            selectedProduct.addEventListener('change', function () {
                let selectedElement = selectedProduct.options[selectedProduct.selectedIndex];
                if (selectedElement.dataset.productType == 'variable') {

                    let productId = selectedElement.value;
                    getVariationOptionsAjax(productId).then(function (variations) {
                        let variationsDropdown = selectedVariationOptions.querySelector('#fd_offer_linked_product_variation');
                        variationsDropdown.innerHTML = '';
                        let defaultElement = `<option>Select a value</option>`;

                        variationsDropdown.innerHTML += defaultElement;
                        variations.forEach(function (option) {
                            let selected = (variationsDropdown.dataset.currentValue == option.product_id) ? 'selected' : '';
                            let optionELement = `<option value="${option.product_id}" ${selected}>${option.product_title}</option>`;
                            variationsDropdown.innerHTML += optionELement;
                        });
                        selectedVariationOptions.style.display = 'block';
                    }, function (error) {
                        console.log(error);
                    });

                } else {
                    selectedVariationOptions.style.display = 'none';
                }

            }, false);
        }
    }


    /**
     * toggle voucher expiry date form on the vouchers management page
     */
    let voucherDateToggleBtns = document.querySelectorAll('.fd_set_expiry_form_toggle');
    if( voucherDateToggleBtns.length > 0){

        voucherDateToggleBtns.forEach( function( btn ){
            btn.addEventListener('click', function(){
                btn.parentElement.classList.toggle('fd_show_voucher_date_form');
                let btnVal = btn.dataset.btnText;
                if( btn.value == 'Set Expiry' ||  btn.value == 'Update Expiry' ){
                    btn.value = 'Cancel';
                }else{
                    btn.value = btnVal
                }
            }, false);
        } );

    }

    /**
     * Enable "Use for Variation checkbox for custom product type"
     */
    let useForVariationOption = document.querySelector('.enable_variation.show_if_variable');
    if( useForVariationOption !== null ){
        console.log(useForVariationOption);
    }

}, false);


/**
 * Helper function - gets variation with ajax
 */
let getVariationOptionsAjax = function (productId) {
    return new Promise(function (resolve, reject) {
        let data = new FormData();
        data.append('action', 'fd_wc_get_linked_variations');
        data.append('security', fd_admin_ajax_obj.nonce);
        data.append('product_id', productId);

        fetch(fd_admin_ajax_obj.ajax_url, {
            method: "POST",
            credentials: 'same-origin',
            body: data
        }).then(function (response) {
            return response.json();
        }).then(function (data) {
            if (data.data.type == 'success') {
                let variations = data.data.variations;
                resolve(variations);
            } else {
                reject(false);
            }
        });
    });
}