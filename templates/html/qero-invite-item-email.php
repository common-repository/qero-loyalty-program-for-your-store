<?php
/**
 * Admin new order email
 */

$opening_paragraph = __( 'Hello %s, Become a loyal customer!', 'qero-for-wp' );
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<?php
$billing_first_name = $data->first_name;
if ( $order && $billing_first_name ) : ?>
    <p><?php printf( $opening_paragraph, $billing_first_name); ?></p>
<?php endif; ?>

    <p><?php _e( 'Along with Qero Loyalty Program, you can earn points and use them when shopping in our store!', 'qero-for-wp' ); ?></p>
    <p><?php _e( 'Shop in our store and enjoy the exclusive discounts that we offer you.', 'qero-for-wp' ); ?></p>
    <div style="text-align: center"><a href="<?php echo get_home_url(); ?>"><button class="qero-email-btn" ><?php _e( 'Visit our store >>','qero-for-wp' ); ?></button></a></div>

<?php do_action( 'woocommerce_email_footer' ); ?>