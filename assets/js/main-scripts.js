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