<?php
/**
 * Created by PhpStorm.
 * User: tmota
 * Date: 17/09/2019
 * Time: 09:57
 */

include_once 'qero-for-wp-checkout-header.php';
//TODO:FIX BACKGROUND COLOR
?>

<div class="shop_table qero-logged-not-loyal" style="background-color: #f8f8f8;">
    <p>
        <?=__('Become loyal today and get points with this purchase!','qero-for-wp');?>
    </p>
    <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ).'qero'; ?>" title="<?php __('Make me loyal!','qero-for-wp'); ?>">
        <span class="button alt qero-button-center" id="qero_redir_loyal" data-value="Make me loyal"><?=__('Qero!','qero-for-wp');?></span>
    </a>
</div>
