window.addEventListener( 'DOMContentLoaded', function(){
    let fdCustomFieldsWrapper = document.querySelector('#fd_wc_voucher_options');
    if( fdCustomFieldsWrapper !== null ){
        
        //hides selling fast fields on page load if condition is true
        let sellingFastField        = fdCustomFieldsWrapper.querySelector('#fd_wc_corner_banner');
        let sellingFastTitle        = fdCustomFieldsWrapper.querySelector('.fd_wc_corner_banner_title_field');
        let sellingFastHeading      = fdCustomFieldsWrapper.querySelector('.fd_wc_corner_banner_headind_field');
        
        if(!sellingFastField.checked == true){
            sellingFastTitle.style.display = 'none';
            sellingFastHeading.style.display = 'none';
        }

        //hide voucher expiry field on page load
        let voucherExpiryField = fdCustomFieldsWrapper.querySelector('#fd_wc_voucher_expiry');
        let voucherUseGlobalSettings = fdCustomFieldsWrapper.querySelector('.fd_wc_voucher_use_global_expiry_field');
        let voucherUseGlobalSettingsField = voucherUseGlobalSettings.querySelector('#fd_wc_voucher_use_global_expiry');
        let voucherExpiryDuration = fdCustomFieldsWrapper.querySelector('.fd_wc_voucher_expiry_date_field');

        if(!voucherExpiryField.checked == true){
            voucherUseGlobalSettings.style.display = 'none';
            voucherExpiryDuration.style.display = 'none';
        }

        if(voucherUseGlobalSettingsField.checked == true || !voucherExpiryField.checked == true){
            voucherExpiryDuration.style.display = 'none';
        }

        fdCustomFieldsWrapper.addEventListener('click', function(){
            if(!sellingFastField.checked == true){
                sellingFastTitle.style.display = 'none';
                sellingFastHeading.style.display = 'none';
            }else{
                sellingFastTitle.style.display = 'block';
                sellingFastHeading.style.display = 'block';
            }

            if(!voucherExpiryField.checked == true){
                voucherUseGlobalSettings.style.display = 'none';
                voucherExpiryDuration.style.display = 'none';
            }else{
                voucherUseGlobalSettings.style.display = 'block';
                voucherExpiryDuration.style.display = 'block';
            }
            
            if( (voucherUseGlobalSettingsField.checked == true) || (!voucherExpiryField.checked == true) ){
                voucherExpiryDuration.style.display = 'none';
            }else{
                voucherExpiryDuration.style.display = 'block';
            }
        }, false);


    }
}, false );