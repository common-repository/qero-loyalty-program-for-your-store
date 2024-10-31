<?php
/**
 * Admin new order email
 */

echo "= " . $email_heading . " =\n\n";
$opening_paragraph = __( 'Hello %s, Become a loyal customer!', 'qero-for-wp' );
$billing_first_name = $data->first_name;
if ( $billing_first_name  ) {
    echo sprintf( $opening_paragraph, $billing_first_name ) . "\n\n";
}
echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo __( 'Along with Qero Loyalty Program, you can earn points and use them when shopping in our store!', 'qero-for-wp' ) . "\n";
echo __( 'Shop in our store and enjoy the exclusive discounts that we offer you.', 'qero-for-wp', 'qero-for-wp' ) . "\n";
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );