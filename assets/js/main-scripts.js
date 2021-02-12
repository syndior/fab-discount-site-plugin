//JS for after the page has loaded
window.addEventListener('DOMContentLoaded', function(){

    // Test code
    let testBtn = document.querySelector('.fd_test_btn');
    if( testBtn !== null ){
        testBtn.addEventListener('click', function(){
            var data = new FormData();
            data.append('action', 'fd_create_voucher_ajax');
            data.append('security', fd_ajax_obj.nonce);

            fetch(fd_ajax_obj.ajax_url, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                console.log(data);
            });
            
        });
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

//scrip to handle regisration form of vendor
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