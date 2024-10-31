<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://qero.io/pt/fidelizacao-qero/
 * @since             1.0.0
 * @package           Qero_For_Wp
 *
 * @wordpress-plugin
 * Plugin Name:       Qero
 * Plugin URI:        https://qero.io/
 * Description:       Make customers loyal with Qero, let your customers accumulate points to redeem them for discounts in your store store.
 * Version:           1.1.1
 * Author:            E-goi
 * Author URI:        https://www.e-goi.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       qero-for-wp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'QERO_FOR_WP_VERSION', '1.1.1' );
define( 'QERO_FOR_WP_FILE', __FILE__ );
define( 'QERO_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );
define( 'QERO_EMAIL_CRON', 'qero_scheduler_email');

define( 'QERO_WAIT_TIME_OPTN', 'qero_wait_time_to_send_email');
define( 'QERO_LAST_CRON_EXEC', 'qero_last_event_done');

add_action( 'admin_init', 'qero_child_plugin_has_parent_plugin' );
function qero_child_plugin_has_parent_plugin() {
    $egoi_plugin = plugin_dir_path( __DIR__ ).'smart-marketing-for-wp';

    if (!is_dir($egoi_plugin)) {

        add_action( 'admin_notices', 'qero_parent_plugin_notice' );

        deactivate_plugins( plugin_basename( __FILE__ ) );

    } else if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'smart-marketing-for-wp/egoi-for-wp.php' ) ) {
        add_action( 'admin_notices', 'qero_child_plugin_notice' );

        deactivate_plugins( plugin_basename( __FILE__ ) );

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

function qero_parent_plugin_notice(){
    ?><div class="notice notice-error is-dismissible">
    <p>
        <?php _e('To use this plugin, you first need to install', 'qero-for-wp');?>
        <a href="https://wordpress.org/plugins/smart-marketing-for-wp/" target="_blank">Smart Marketing SMS and Newsletters Forms by E-goi</a>
    </p>
    </div><?php
}

function qero_child_plugin_notice(){
    ?><div class="notice notice-error is-dismissible">
    <p><?php _e('By removing this plugin, you will no longer be able to use the Qero plugin', 'qero-for-wp');?></p>
    </div><?php
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-qero-for-wp-activator.php
 */
function activate_qero_for_wp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-qero-for-wp-activator.php';
	Qero_For_Wp_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-qero-for-wp-deactivator.php
 */
function deactivate_qero_for_wp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-qero-for-wp-deactivator.php';
	Qero_For_Wp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_qero_for_wp' );
register_deactivation_hook( __FILE__, 'deactivate_qero_for_wp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-qero-for-wp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_qero_for_wp() {

	$plugin = new Qero_For_Wp();
	$plugin->run();

}
run_qero_for_wp();
