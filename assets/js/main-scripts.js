//JS for after the page has loaded
window.addEventListener('DOMContentLoaded', function(){

    /**
     * Order completed / Claim offer section
     */
    let claimOfferBtn = document.querySelector('.fd_claim_offer_btn');
    let voucherKeyWrapper = document.querySelector('.fd_offer_voucher_key_wrapper');
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

});


//AJAX request function
function makeAjaxRequest(requestObject) {
    var data = new FormData();
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


    /**
    * Enable "offer Scheduling"
    */
    let fd_wc_offer_schedule = document.getElementById('fd_wc_offer_schedule');
    let schedule_date = document.getElementById('schedule_date');
    let schedule_time = document.getElementById('schedule_time');
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


    /**
    * Enable "offer expiry"
    */
   let fd_wc_offer_expiry = document.getElementById('fd_wc_offer_expiry');
   let global_expiry = document.getElementById('global_expiry');
   let local_expiry = document.getElementById('local_expiry');
   fd_wc_offer_expiry.addEventListener('change',()=>{
       if(fd_wc_offer_expiry.checked==true){
           global_expiry.style.display = "block";
           local_expiry.style.display = "block";
       }else{
           global_expiry.style.display = "none";
           local_expiry.style.display = "none";
       }
   });//event listener for offer expiry
   

    /**
    * Enable "Voucher expiry"
    */
   let fd_wc_offer_voucher_expiry = document.getElementById('fd_wc_offer_voucher_expiry');
   let global_voucher_expiry = document.getElementById('global_voucher_expiry');
   let local_voucher_expiry = document.getElementById('local_voucher_expiry');
   fd_wc_offer_voucher_expiry.addEventListener('change',()=>{
       if(fd_wc_offer_voucher_expiry.checked==true){
           global_voucher_expiry.style.display = "block";
           local_voucher_expiry.style.display = "block";
       }else{
           global_voucher_expiry.style.display = "none";
           local_voucher_expiry.style.display = "none";
       }
   });//event listener for Voucher expiry


   let product_type = document.getElementById('product_type');
   let fd_wc_offer_options = document.getElementById('fd_wc_offer_options');

   product_type.addEventListener('change',()=>{
    if(product_type.value == "fd_wc_offer" || product_type.value == "fd_wc_offer_variable"){
        fd_wc_offer_options.style.display = "block";
    }else{
        fd_wc_offer_options.style.display = "none";
    }
   });


   

}

window.onload = setOfferOptionsInVendor();