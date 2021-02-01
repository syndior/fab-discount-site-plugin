<?php
/**
 * Plugin Name:       Fab Discount Core Site Functionality
 * Plugin URI:        https://kristall.io/
 * Description:       Handles the Fab Discount site core functionality
 * Version:           1.0.0
 * Requires at least: 5.5
 * Requires PHP:      7.2
 * Author:            Kristall Studios
 * Author URI:        https://kristall.io/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

 /**
 * Direct access protection
 */
defined('ABSPATH') or die('This path is not accessible');

if( !class_exists('FD_SITE_CORE_FUNCTIONALITY') ){

    class FD_SITE_CORE_FUNCTIONALITY{

        public function __construct()
        {
            /**
             * Include js and css files
             */
            add_action( 'wp_enqueue_scripts', array($this, 'fdscf_includes_resources') );

            //*****adding extra fileds in dokan seller signup form start*****//   
    
            ////// for showing extra fields start //////
            add_action('dokan_seller_registration_field_after',array($this,'fdscf_dokan_seller_extra_fields'),10);

            ////// for checking extra fields whice are required start //////
            add_filter( 'dokan_seller_registration_required_fields', array($this,'fdscf_dokan_custom_seller_registration_required_fields'),10);

            ////// for inserting extra fields data in user_meta start //////
            add_action( 'dokan_new_seller_created', array($this,'fdscf_dokan_custom_new_seller_created'), 10, 2 );

            ////// Add custom profile fields (call in theme : echo $curauth->fieldname;) ////// 
            add_action( 'dokan_seller_meta_fields', array($this,'fdscf_show_extra_profile_fields')); 

            ////// updateing custom profile fields from admin //////
            add_action( 'personal_options_update', array($this,'fdscf_save_extra_profile_fields') );
            add_action( 'edit_user_profile_update', array($this,'fdscf_save_extra_profile_fields') );

            //*****adding extra fileds in dokan seller signup form end*****//   
            
            // *************************--------------------------***********************//
            
            //*****Extra field on the seller settings and show the value on the store banner -Dokan start*****//
    
            ////// Add extra field in seller settings //////
            add_filter( 'dokan_settings_form_bottom', array($this,'fdscf_extra_seller_setting_fields'), 10, 2);

            ////// save extra field in seller settings //////
            add_action( 'dokan_store_profile_saved',array($this,'fdscf_save_extra_seller_setting_fields'), 15 );

            //*****Extra field on the seller settings and show the value on the store banner -Dokan start*****//

            // *************************--------------------------***********************//

        }

        public function fdscf_includes_resources()
        {
            //plugin styles
            wp_enqueue_style( 'fdscf-styles', plugins_url( 'assets/css/main-styles.css', __FILE__ ),array(), '1.0.0');
            
            //plugin scripts
            wp_enqueue_script( 'fdscf-script', plugins_url( 'assets/js/main-scripts.js', __FILE__ ), array('jquery'),'1.0.0',true);
        }

//*****adding extra fileds in dokan seller signup form start*****//       
        // for showing extra fields start
        public function fdscf_dokan_seller_extra_fields(){
            $fields='';
            $value = '';
            // field 1 VAT //
            $fields.='<p class="form-row form-group form-row-wide">
            <label for="shop_vat_number">VAT Number<span class="required">*</span></label>';
            $fields.='<input type="number" class="input-text form-control" name="shop_vat_number" id="shop_vat_number"';
            if ( ! empty( $postdata["shop_vat_number"] ) ){
                $value = esc_attr($postdata["shop_vat_number"]);
            } 
            $fields.='value="'.$value.'" required="required" />
            </p>';

            ////// field 2 company reg number //////
            $fields.='<p class="form-row form-group form-row-wide">
            <label for="company_reg_number">Company registration number<span class="required">*</span></label>';
            $fields.='<input type="number" class="input-text form-control" name="company_reg_number" id="company_reg_number"';
            if ( ! empty( $postdata["company_reg_number"] ) ){
                $value = esc_attr($postdata["company_reg_number"]);
            } 
            $fields.='value="'.$value.'" required="required" />
            </p>';

            // field 3 company Website //
            $fields.='<p class="form-row form-group form-row-wide">
            <label for="company_website">Company website<span class="required">*</span></label>';
            $fields.='<input type="text" class="input-text form-control" name="company_website" id="company_website"';
            if ( ! empty( $postdata["company_website"] ) ){
                $value = esc_attr($postdata["company_website"]);
            } 
            $fields.='value="'.$value.'" required="required" />
            </p>';

            // field 4 document for proof on identity //
            $fields.='<p class="form-row form-group form-row-wide">
            <label for="identity_doc">Upload document for proof of identity</label>';
            $fields.='<input type="file" class="input-file form-control" name="identity_doc" id="identity_doc"';
            $fields.='"/>
            </p>';
            
        echo $fields;
        }
    

        ////// for checking extra fields whice are required start //////
        public function fdscf_dokan_custom_seller_registration_required_fields( $required_fields ) {
            $required_fields['shop_vat_number'] = __( 'Please enter your shop vat number', 'dokan-custom' );
            $required_fields['company_reg_number'] = __( 'Please enter your company reg number', 'dokan-custom' );
            $required_fields['company_website'] = __( 'Please enter your company website', 'dokan-custom' );
        
            return $required_fields;
        }
        

        
        ////// for inserting extra fields data in user_meta start //////
        public function fdscf_dokan_custom_new_seller_created( $vendor_id, $dokan_settings ) {
            $post_data = wp_unslash( $_POST );
        
            $shop_vat_number =  $post_data['shop_vat_number'];
            $company_reg_number =  $post_data['company_reg_number'];
            $company_website =  $post_data['company_website'];
            // $shop_vat_number =  $post_data['shop_vat_number'];
           
            update_user_meta( $vendor_id, 'dokan_seller_shop_vat_number', $shop_vat_number );
            update_user_meta( $vendor_id, 'dokan_seller_company_reg_number', $company_reg_number );
            update_user_meta( $vendor_id, 'dokan_seller_company_website', $company_website );
        }
        

        
        ////// Add custom profile fields in admin ////// 
        public function fdscf_show_extra_profile_fields( $user ) { ?>
        
            <?php if ( ! current_user_can( 'manage_woocommerce' ) ) {
                    return;
                }
                if ( ! user_can( $user, 'dokandar' ) ) {
                    return;
                }
                 $dokan_seller_shop_vat_number  = get_user_meta( $user->ID, 'dokan_seller_shop_vat_number', true );
                 $dokan_seller_company_reg_number  = get_user_meta( $user->ID, 'dokan_seller_company_reg_number', true );
                 $dokan_seller_company_website  = get_user_meta( $user->ID, 'dokan_seller_company_website', true );
             ?>
                 <tr>
                            <th><?php esc_html_e( 'VAT number', 'dokan-lite' ); ?></th>
                            <td>
                                <input type="text" name="shop_vat_number" class="regular-text" value="<?php echo esc_attr($dokan_seller_shop_vat_number); ?>"/>
                            </td>
                 </tr>

                 <tr>
                            <th><?php esc_html_e( 'Company reg number', 'dokan-lite' ); ?></th>
                            <td>
                                <input type="text" name="company_reg_number" class="regular-text" value="<?php echo esc_attr($dokan_seller_company_reg_number); ?>"/>
                            </td>
                 </tr>
                 <tr>
                            <th><?php esc_html_e( 'Company website', 'dokan-lite' ); ?></th>
                            <td>
                                <input type="text" name="company_website" class="regular-text" value="<?php echo esc_attr($dokan_seller_company_website); ?>"/>
                            </td>
                 </tr>

            <?php
         }
        
        ///// updateing custom profile fields from admin /////
        public function fdscf_save_extra_profile_fields( $user_id ) {
        
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
                    return;
                }
            update_usermeta( $user_id, 'dokan_seller_shop_vat_number', $_POST['shop_vat_number'] );
            update_usermeta( $user_id, 'dokan_seller_company_reg_number', $_POST['company_reg_number'] );
            update_usermeta( $user_id, 'dokan_seller_company_website', $_POST['company_website'] );
        }

//*****adding extra fileds in dokan seller signup form end*****//        


//*****Extra field on the seller settings and show the value on the store banner -Dokan start*****//

// Add extra field in seller settings
 public function fdscf_extra_seller_setting_fields( $user_ID, $profile_info ){
    //  var_dump($user);
    $shop_vat_number  = get_user_meta( $user_ID, 'dokan_seller_shop_vat_number', true );
    $company_reg_number  = get_user_meta( $user_ID, 'dokan_seller_company_reg_number', true );
    $company_website  = get_user_meta( $user_ID, 'dokan_seller_company_website', true );

//  $shop_vat_number= isset( $profile_info['dokan_seller_shop_vat_number'] ) ? $profile_info['dokan_seller_shop_vat_number'] : '';
//  $company_reg_number= isset( $profile_info['dokan_seller_shop_vat_number'] ) ? $profile_info['dokan_seller_shop_vat_number'] : '';
//  $company_website= isset( $profile_info['dokan_seller_shop_vat_number'] ) ? $profile_info['dokan_seller_shop_vat_number'] : '';
?>
 <div class="gregcustom dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="vat number">
            <?php _e( 'Company VAT Number', 'dokan' ); ?>
        </label>
        <div class="dokan-w5">
            <input type="text" class="dokan-form-control input-md valid" name="shop_vat_number" id="shop_vat_number" value="<?php echo $shop_vat_number; ?>" />
        </div>
    </div>
    <div class="gregcustom dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="reg number">
            <?php _e( 'Company Registration Number', 'dokan' ); ?>
        </label>
        <div class="dokan-w5">
            <input type="text" class="dokan-form-control input-md valid" name="company_reg_number" id="company_reg_number" value="<?php echo $company_reg_number; ?>" />
        </div>
    </div>

    <div class="gregcustom dokan-form-group">
        <label class="dokan-w3 dokan-control-label" for="website">
            <?php _e( 'Company Website', 'dokan' ); ?>
        </label>
        <div class="dokan-w5">
            <input type="text" class="dokan-form-control input-md valid" name="company_website" id="company_website" value="<?php echo $company_website; ?>" />
        </div>
    </div>
    <?php
}

    
//save the field value
function fdscf_save_extra_seller_setting_fields( $store_id ) {
    $dokan_settings = dokan_get_store_info($store_id);
    if ( isset( $_POST['seller_url'] ) ) {
        $dokan_settings['seller_url'] = $_POST['seller_url'];
    }
 update_user_meta( $store_id, 'dokan_profile_settings', $dokan_settings );
}

// show on the store page
// add_action( 'dokan_store_header_info_fields', 'save_seller_url', 10);

function save_seller_url($store_user){

    $store_info    = dokan_get_store_info( $store_user);

   ?>
        <?php if ( isset( $store_info['seller_url'] ) && !empty( $store_info['seller_url'] ) ) { ?>
            <i class="fa fa-globe"></i>
            <a href="<?php echo esc_html( $store_info['seller_url'] ); ?>"><?php echo esc_html( $store_info['seller_url'] ); ?></a>
    
    <?php } ?>
       
  <?php
}
//*****Extra field on the seller settings and show the value on the store banner -Dokan end*****//


    }//class end

}//if end (class exist)

/**
 * Main Plugin instance
 */
new FD_SITE_CORE_FUNCTIONALITY();