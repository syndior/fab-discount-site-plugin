<?php if ( ! defined( 'ABSPATH' ) ) exit;

    $user = wp_get_current_user();
    $users_viewed_products = get_user_meta($user->ID, 'fd_viewed_products', true);

    $viewed_products = array();
    if( !empty( $users_viewed_products ) ){
        foreach( $users_viewed_products as $product_id ){
            $product_id = (int)$product_id;
            $product = wc_get_product( $product_id );
            $viewed_products[] = $product;
        }
    }

    $viewed_items_exists = ( !empty($viewed_products) ? true : false);


?>

<div class="fd-wc-account-my-viewed-items-tab-content">

    <div class="fd-wc-account-my-viewed-items-tab-header">
        <h3>My Previously Viewed Items</h3>
    </div>

    <?php if(!$viewed_items_exists == true): ?>
    
    <div class="fd-wc-account-my-viewed-items-tab-notice">
        <p>You haven't viewed any items yet.</p>
    </div>
    
    <?php else: ?>
    
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $viewed_products as $product ):?>
                    <tr>
                        <td>
                            <a href="<?php echo get_permalink( $product->get_id() );?>" class="fd_viewed_item_wrapper">
                            <?php $product_img_url = ( wp_get_attachment_url($product->get_image_id()) ) ? wp_get_attachment_url($product->get_image_id()) : wc_placeholder_img_src() ; ?>
                                <div class="fd_viewed_item_img" style="background-image: url('<?php echo $product_img_url; ?>');"></div>
                                <div class="fd_viewed_item_title">
                                    <p><?php echo $product->get_name();?></p>
                                </div>
                            </a>
                        </td>
                        <td>
                            <p class="fd_viewed_item_price"><?php echo wc_price( $product->get_price() ); ?></p>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>

    <?php endif; ?>

</div>