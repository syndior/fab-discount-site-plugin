//JS for after the page has loaded
window.addEventListener('DOMContentLoaded', function(){

    /**
     * Order completed / Claim offer section
     */

     let purchasedOfferItems = document.querySelectorAll('.fd_offer_details_wrapper');
     if( purchasedOfferItems.length > 0 ){

        purchasedOfferItems.forEach( function(offerItem){
            let claimOfferBtn = offerItem.querySelector('.fd_claim_offer_btn');
            let voucherKeyWrapper = offerItem.querySelector('.fd_offer_voucher_key_wrapper');

            if( claimOfferBtn !== null && voucherKeyWrapper !== null){
                claimOfferBtn.addEventListener( 'click', function(){
                    if( !voucherKeyWrapper.classList.contains('fd_offer_voucher_key_wrapper_show') ){
                        voucherKeyWrapper.style.display = 'block';
                        setTimeout( function(){
                            voucherKeyWrapper.classList.add('fd_offer_voucher_key_wrapper_show');
                        },10);
                    }else{
                        voucherKeyWrapper.classList.remove('fd_offer_voucher_key_wrapper_show');
                        setTimeout( function(){
                            voucherKeyWrapper.style.display = 'none';
                        },500);
                    }
                }, false );
            }

        } );

     }
    



    let uploadBtns = document.querySelectorAll('.fd_upload_btn');
    let identity_success_msg = document.getElementById('identity_success_msg');
    let others_doc_success_msg = document.getElementById('others_doc_success_msg');
    let proof_success_msg = document.getElementById('proof_success_msg');
    let video_success_msg = document.getElementById('video_success_msg');
    
    if( uploadBtns.length > 0 ){
        uploadBtns.forEach( function(btn){
            btn.addEventListener( 'click', function(e){
                e.preventDefault();
                let button = jQuery(this);
                custom_uploader = wp.media({
                    title: 'Insert image',
                    multiple: false,
                    library : {
                        // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
                        type : 'media'
                    },
                    button: {
                        text: 'Use this image' // button label text
                    },
                    multiple: false
                }).on('select', function() { // it also has "open" and "close" events
                    let message = 'File Selected';
                    let hiidenInput = document.querySelector(`input[name="${btn.dataset.inputName}"]`);
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    hiidenInput.value = attachment.id;
                    if(hiidenInput.value != ""){
                        // console.log(btn.dataset.inputName);
                        if(btn.dataset.inputName == "identity_doc"){
                            identity_success_msg.innerHTML = message;
                        }else if(btn.dataset.inputName == "others_doc"){
                            others_doc_success_msg.innerHTML = message;
                        }else if(btn.dataset.inputName == "fd_product_proof_of_stock"){
                            proof_success_msg.innerHTML = message;
                        }else if(btn.dataset.inputName == "fd_product_video"){
                            video_success_msg.innerHTML = message;
                        }
                    }

                }).open();
            }, false );
        } );
    }


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



    /**
     * Hook eventlistener for claim voucher form
     */
    let claimVoucherForm = document.querySelector('#fd_claim_voucher_form');
    if( claimVoucherForm !== null ){
        let submitBtn = claimVoucherForm.querySelector('#fd_claim_voucher_submit');
        submitBtn.addEventListener( 'click', function(e){
            e.preventDefault();
            
            let voucherKey = claimVoucherForm.querySelector('#fd_voucher_key').value;

            let data = new FormData();
            data.append('action', 'claim_voucher_ajax_request_handler');
            data.append('security', fd_ajax_obj.nonce);
            data.append('voucher_key', voucherKey);

            fetch(fd_ajax_obj.ajax_url, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                console.log(data);

                if( data.data.type === 'success' ){

                    let response = data.data;
                    if( response.voucher_status !== false ){

                        let voucherResultWrapper = document.querySelector('.fd_claim_result_wrapper');
                        let resultsWrapper = voucherResultWrapper.querySelector('.fd_claim_results');

                        let voucherIsUnique = false;

                        let vouchers = resultsWrapper.dataset.activeVouchers !== '' ? JSON.parse(resultsWrapper.dataset.activeVouchers) : {voucher_ids : []};
                        if( vouchers.voucher_ids.indexOf(response.voucher_id) === -1 ){
                            vouchers.voucher_ids.push(response.voucher_id);
                            resultsWrapper.dataset.activeVouchers = JSON.stringify(vouchers);
                            voucherIsUnique = true;
                        }else{
                            alert('This voucher already exists');
                            voucherIsUnique = false;
                        }

                        if( voucherIsUnique == true ){
                            let voucherResultsHTML = '';
                            voucherResultsHTML += '<div class="fd_claim_voucher_result_item">';
                            
                            voucherResultsHTML += `<input type="hidden" name="fd_voucher_ids[]" value="${response.voucher_id}">`;
    
                            voucherResultsHTML += '<div class="fd_claim_voucher_result_item_img">';
                            voucherResultsHTML += `<img src="${ response.product_img }">`;
                            voucherResultsHTML += '</div>';
    
                            voucherResultsHTML += '<div class="fd_claim_voucher_result_item_info">';
    
                            voucherResultsHTML += `<p class="fd_claim_voucher_result_item_title">${ response.product_name }</p>`;
                            
                            voucherResultsHTML += '<table class="fd_claim_voucher_result_item_data">';

                            voucherResultsHTML += '<tr>';
                            voucherResultsHTML += '<th>Status:</th>';
                            voucherResultsHTML += `<td>${ response.voucher_status }</td>`;
                            voucherResultsHTML += '</tr>';
                            
                            voucherResultsHTML += '<tr>';
                            voucherResultsHTML += '<th>Amount:</th>';
                            voucherResultsHTML += `<td>${ response.voucher_amount }</td>`;
                            voucherResultsHTML += '</tr>';
                            
                            voucherResultsHTML += '<tr>';
                            voucherResultsHTML += '<th>Key:</th>';
                            voucherResultsHTML += `<td>${ response.voucher_key }</td>`;
                            voucherResultsHTML += '</tr>';

                            voucherResultsHTML += '</table>';
                            
                            voucherResultsHTML += '</div>';
    
                            voucherResultsHTML += '</div>';
    
                            resultsWrapper.innerHTML += voucherResultsHTML;
                        }

                    }else if( response.voucher_status == false ){
                        alert('Invalid Voucher Key');
                    }


                }

            });

        }, false );
    }


    /**
     * User Account Dropdown Logic
     */
    let userDropDown = document.querySelector( '.fd_account_dropdown' );
    let dropDownElement = document.querySelector( '.fd_account_dropdown_element' );
    if( userDropDown !== null && dropDownElement !== null ){

        dropDownElement.addEventListener( 'click', function(e){
            e.stopPropagation();
        }, false );

        userDropDown.addEventListener( 'click', function(){
            
            if( dropDownElement.classList.contains('fd_account_dropdown_element_active') ){
                dropDownElement.classList.remove('fd_account_dropdown_element_active');
                setTimeout( function(){
                    dropDownElement.style.display = 'none';
                }, 300 );
            }else{
                dropDownElement.style.display = 'block';
                setTimeout( function(){
                    dropDownElement.classList.add('fd_account_dropdown_element_active');
                }, 10 );
            }

            //logic for arrow animation
            let dropdownArrow = userDropDown.querySelector( '.fd_dropdown_arrow' );
            dropdownArrow.classList.toggle('fd_dropdown_arrow_rotate');

        }, false );
    }
});


//AJAX request function
function makeAjaxRequest(requestObject) {
    let data = new FormData();
    data.append('action', 'fd_log_user_viewed_product');
    data.append('security', fd_ajax_obj.nonce);
    data.append('request_type', requestObject.requestType);
    data.append('product_id', requestObject.productId);

    fetch(fd_ajax_obj.ajax_url, {
        method: "POST",
        credentials: 'same-origin',
        body: data
    }).then(function (response) {
        return response.json();
    }).then(function (data) {
        console.log(data);
    });
}


/**
* scrip to handle regisration form of vendor 
 */
const enable_or_disable_register_button= ()=>{
    let radio_inputs = document.getElementsByName('role');
    let register_button = document.getElementsByName('register')[0];
    let saveButton = `<button type="button" class="woocommerce-Button woocommerce-button button" id = "register_save_button" onclick="validatorVatCampanyNumber()">Save Info</button>
    <a href = "#" class="woocommerce-Button woocommerce-button button in_active_button" id = "register_contact_button">Contact Us</a>
    `;
    let privacy_area = document.getElementsByClassName('woocommerce-privacy-policy-text')[0];
    radio_inputs.forEach(element => {
        element.addEventListener('change',()=>{
            // console.log(element.value);
            let register_save_button = document.getElementById('register_save_button'); 

            if(element.value == "seller"){
                register_button.classList.add('in_active_button');
                if(register_save_button === null){
                    privacy_area.innerHTML+=saveButton;
                }else{
                    register_save_button.classList.remove('in_active_button');
                }
                
            }else if(element.value == "customer"){
                register_button.classList.remove('in_active_button');
                if(register_save_button === null){
                    privacy_area.innerHTML+=saveButton;
                }else{
                    register_save_button.classList.add('in_active_button');
                }

            }
        });//eventlistener        
    });
}

const validatorVatCampanyNumber= ()=>{
    let shop_vat_number = document.getElementById('shop_vat_number');
    let company_reg_number = document.getElementById('company_reg_number');
    let contact_link = document.getElementById('register_contact_button');
    let register_save_button = document.getElementById('register_save_button'); 
    let register_button = document.getElementsByName('register')[0];
    if((shop_vat_number.value == "" || shop_vat_number.value == null || shop_vat_number.value == undefined) || (company_reg_number.value == "" || company_reg_number.value == null || company_reg_number.value == undefined)){
        contact_link.classList.remove('in_active_button');
    }else{
        register_button.classList.remove('in_active_button');
        contact_link.classList.add('in_active_button');
        register_save_button.classList.add('in_active_button');
    }
    
}

window.onload = enable_or_disable_register_button();


const setOfferOptionsInVendor = ()=>{

    /**
     * Enable selling fast"
     */
    let corner_banner = document.getElementById('fd_wc_corner_banner');
    let selling_fast_banner_title = document.getElementById('selling_fast_banner_title');
    let selling_fast_banner_heading = document.getElementById('selling_fast_banner_heading');

    if( corner_banner !== null ){
        corner_banner.addEventListener('change',()=>{
            // var checkbox_schedule = document.getElementById('fd_wc_offer_schedule');
            if(corner_banner.checked==true){
                selling_fast_banner_title.style.display = "block";
                selling_fast_banner_heading.style.display = "block";
            }else{
                selling_fast_banner_title.style.display = "none";
                selling_fast_banner_heading.style.display = "none";
            }
        });//event listener for Enable selling fast
    }



    /**
    * Enable "offer Scheduling"
    */
    let fd_wc_offer_schedule = document.getElementById('fd_wc_offer_schedule');
    let schedule_date = document.getElementById('schedule_date');
    let schedule_time = document.getElementById('schedule_time');
    
    if( fd_wc_offer_schedule !== null ){
        fd_wc_offer_schedule.addEventListener('change',()=>{
            // var checkbox_schedule = document.getElementById('fd_wc_offer_schedule');
            if(fd_wc_offer_schedule.checked==true){
                schedule_date.style.display = "block";
                schedule_time.style.display = "block";
            }else{
                schedule_date.style.display = "none";
                schedule_time.style.display = "none";
            }
        });//event listener for offer Scheduling
    }


    /**
    * Enable "offer expiry"
    */
   let fd_wc_offer_expiry = document.getElementById('fd_wc_offer_expiry');
   let global_expiry = document.getElementById('global_expiry');
   let local_expiry = document.getElementById('local_expiry');

   if( fd_wc_offer_expiry !== null ){

    fd_wc_offer_expiry.addEventListener('change',()=>{
        if(fd_wc_offer_expiry.checked==true){
            global_expiry.style.display = "block";
            local_expiry.style.display = "block";
        }else{
            global_expiry.style.display = "none";
            local_expiry.style.display = "none";
        }
    });//event listener for offer expiry

   }
   

    /**
    * Enable "Voucher expiry"
    */
   let fd_wc_offer_voucher_expiry = document.getElementById('fd_wc_offer_voucher_expiry');
   let global_voucher_expiry = document.getElementById('global_voucher_expiry');
   let local_voucher_expiry = document.getElementById('local_voucher_expiry');

   if( fd_wc_offer_voucher_expiry !== null ){
    
    fd_wc_offer_voucher_expiry.addEventListener('change',()=>{
        if(fd_wc_offer_voucher_expiry.checked==true){
            global_voucher_expiry.style.display = "block";
            local_voucher_expiry.style.display = "block";
        }else{
            global_voucher_expiry.style.display = "none";
            local_voucher_expiry.style.display = "none";
        }
    });//event listener for Voucher expiry
    
   }


   let product_type = document.getElementById('product_type');
   let fd_wc_offer_options = document.getElementById('fd_wc_offer_options');

   if( product_type !== null){
        product_type.addEventListener('change',()=>{
            if(product_type.value == "fd_wc_offer"){
                fd_wc_offer_options.style.display = "block";
            }else{
                fd_wc_offer_options.style.display = "none";
            }
        });
   }


   

}
window.onload = setOfferOptionsInVendor();


/**
 * Helper function - gets variation with ajax
 */
let getVariationOptionsAjax = function (productId) {
    return new Promise(function (resolve, reject) {
        let data = new FormData();
        data.append('action', 'fd_wc_get_linked_variations');
        data.append('security', fd_ajax_obj.nonce);
        data.append('product_id', productId);

        fetch(fd_ajax_obj.ajax_url, {
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

jQuery(document).ready(function($){
    /**
     * Set nav menu as slider for mobile devices
     */
    console.log(document.documentElement.clientWidth);
    if( document.documentElement.clientWidth < 768 ){
        
        $('.fd_menu_item_slider .menu-header-menu-container > ul').slick({
            infinite: true,
            speed: 300,
            slidesToShow: 1,
            variableWidth: true
        });
    }
    
});

/**
 * Set loop item rows as sliders
 */
document.addEventListener( 'DOMContentLoaded', function () {

    var elms = document.getElementsByClassName( 'splide' );
    if( elms.length > 0 ){

        for ( var i = 0, len = elms.length; i < len; i++ ) {
            new Splide( elms[ i ], {
                type   : 'loop',
                pagination : false,
                perPage: 4,
                perMove: 1,
                gap: 30,
                padding: '1em',
                breakpoints: {
                    768: {
                        perPage: 1,
                    },
                }
        
            } ).mount();
        }

    }

} );