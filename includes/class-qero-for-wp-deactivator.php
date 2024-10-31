<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://qero.io/pt/fidelizacao-qero/
 * @since      1.0.0
 *
 * @package    Qero_For_Wp
 * @subpackage Qero_For_Wp/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Qero_For_Wp
 * @subpackage Qero_For_Wp/includes
 * @author     Qero <Lda.>
 */
class Qero_For_Wp_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        wp_clear_scheduled_hook('qero_shot_email_invite_action');
	}

}
