<?php

/**
 * Fired during plugin activation
 *
 * @link       https://qero.io/pt/fidelizacao-qero/
 * @since      1.0.0
 *
 * @package    Qero_For_Wp
 * @subpackage Qero_For_Wp/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Qero_For_Wp
 * @subpackage Qero_For_Wp/includes
 * @author     Qero <Lda.>
 */
class Qero_For_Wp_Activator {

	/**
	 * Tables for user association and tracking of spent points
	 *
	 * Users are associated to a qero subscriber account (to a already existing one or creating a subscription)
     * Discounts used are connected to a cart, once the cart is paid it's converted in a order which has the original cart_hash
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        // Ensure that dbDelta() is defined.
        if ( ! function_exists( 'dbDelta' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        }

        $a = get_option(QERO_LAST_CRON_EXEC);
        if(empty($a))//set last cron to block sending to all in the first try
            add_option(QERO_LAST_CRON_EXEC,time());

        if ( ! wp_next_scheduled( 'qero_shot_email_invite_action' ) ) {
            wp_schedule_event( time(), QERO_EMAIL_CRON, 'qero_shot_email_invite_action' );
        }

        self::qero_create_table(Qero_For_Wp_Admin::TABLE_NAME, "
            id INT(11) NOT NULL AUTO_INCREMENT, 
            email VARCHAR(255) NOT NULL, 
            client_id VARCHAR(255) NOT NULL, 
            secondary_id VARCHAR(255) NOT NULL, 
            date DATETIME NOT NULL, 
            PRIMARY KEY (id)"
        );

        self::qero_create_table(Qero_For_Wp_Admin::DISCOUNT_NAME, "
            id INT(11) NOT NULL AUTO_INCREMENT, 
            secondary_id VARCHAR(255) NOT NULL, 
            qero_points VARCHAR(255) NOT NULL, 
            PRIMARY KEY (id)"
        );

        self::qero_create_table(Qero_For_Wp_Admin::LOGS, "
            id INT(11) NOT NULL AUTO_INCREMENT, 
            client_id VARCHAR(255) NOT NULL, 
            log BLOB NULL, 
            date DATETIME NOT NULL, 
            PRIMARY KEY (id)"
        );
	}

    public static function qero_create_table($table, $fields) {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix.$table." (".$fields.") ".$charset_collate."; ";

        dbDelta($sql);

    }

}
