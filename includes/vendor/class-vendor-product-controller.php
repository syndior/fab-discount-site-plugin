<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Vendor_Product_Controller{
    public function __construct(){

        add_action( 'dokan_new_product_after_product_tags',array($this,'vendorProductExtraFields'),10 );
    }


    public function vendorProductExtraFields(){
        echo require_once ( fdscf_path . 'includes/vendor/templates/fd-product-data-tab.php' );
    }



}
new FD_Vendor_Product_Controller();

?>