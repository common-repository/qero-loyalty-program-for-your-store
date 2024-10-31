<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://qero.io/pt/fidelizacao-qero/
 * @since      1.0.0
 *
 * @package    Qero_For_Wp
 * @subpackage Qero_For_Wp/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Qero_For_Wp
 * @subpackage Qero_For_Wp/includes
 * @author     Qero <Lda.>
 */
class Qero_For_Wp {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Qero_For_Wp_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'QERO_FOR_WP_VERSION' ) ) {
			$this->version = QERO_FOR_WP_VERSION;
		} else {
			$this->version = '1.1.1';
		}
		$this->plugin_name = 'qero-for-wp';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Qero_For_Wp_Loader. Orchestrates the hooks of the plugin.
	 * - Qero_For_Wp_i18n. Defines internationalization functionality.
	 * - Qero_For_Wp_Admin. Defines all hooks for the admin area.
	 * - Qero_For_Wp_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-qero-for-wp-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-qero-for-wp-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-qero-for-wp-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-qero-for-wp-public.php';

		$this->loader = new Qero_For_Wp_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Qero_For_Wp_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Qero_For_Wp_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Qero_For_Wp_Admin( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'admin_head', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'qero_add_options_page', 11 );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

        //config
        $this->loader->add_action('wp_ajax_qero_config', $plugin_admin, 'qero_config');
        $this->loader->add_action('wp_ajax_qero_config_new_app', $plugin_admin, 'qero_config_new_app');

        $this->loader->add_action('wp_ajax_qero_get_stores', $plugin_admin, 'qero_get_stores');
        $this->loader->add_action('wp_ajax_qero_get_clients', $plugin_admin, 'qero_get_clients');
        $this->loader->add_action('wp_ajax_qero_dashboard_sales',$plugin_admin,'qero_dashboard_sales');
        $this->loader->add_action('wp_ajax_qero_dashboard_client_creation',$plugin_admin,'qero_dashboard_client_creation');
        $this->loader->add_action('wp_ajax_qero_dashboard_campaigns',$plugin_admin,'qero_dashboard_campaigns');
        $this->loader->add_action('wp_ajax_qero_dashboard_client_sales',$plugin_admin,'qero_dashboard_client_sales');
        $this->loader->add_action('wp_ajax_qero_dashboard_info',$plugin_admin,'qero_dashboard_info');

        //Hooks events
        $this->loader->add_action('woocommerce_order_status_changed', $plugin_admin, 'qero_points_unlock', 10, 1);

        //email
        $this->loader->add_filter( 'woocommerce_email_classes', $plugin_admin, 'qero_email_invite', 10 , 1);
        $this->loader->add_action( 'qero_send_email_user_trigger', $plugin_admin, 'qero_trigger_email_invite', 10 );
        $this->loader->add_action( 'wp_ajax_qero_force_invite_all',$plugin_admin,'qero_force_invite_all');


        //user information
        $this->loader->add_filter('manage_users_columns', $plugin_admin, 'qero_add_extra_user_info', 10);
        $this->loader->add_filter('manage_users_custom_column', $plugin_admin, 'qero_add_extra_user_info_row', 1, 3);


        // Email Actions - Triggers
        $email_actions = array(
            'qero_invite_pending_email',
            'qero_invite_item_email',
        );

        foreach ( $email_actions as $action ) {
            add_action( $action, array( 'WC_Emails', 'send_transactional_email' ), 10, 10 );
        }

        //Email Cron
        $this->loader->add_filter( 'cron_schedules', $plugin_admin, 'qero_manage_invite_email');

        $this->loader->add_action( 'qero_shot_email_invite_action', $plugin_admin, 'qero_shot_email_invite', 11 );//action qero_shot_email_invite_action


    }

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Qero_For_Wp_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_filter( 'woocommerce_account_menu_items', $plugin_public,'qero_points_menu' );
        $this->loader->add_filter( 'woocommerce_order_details_after_order_table', $plugin_public, 'qero_thankyou_page_info', 10 , 1);

        $this->loader->add_action( 'init', $plugin_public, 'qero_add_endpoint' );
        $this->loader->add_action( 'after_switch_theme', $plugin_public, 'qero_flush_rewrite_rules' );
        $this->loader->add_action( 'wp_loaded', $plugin_public, 'qero_flush_rewrite_rules' );

        $this->loader->add_action( 'woocommerce_account_qero_endpoint', $plugin_public, 'qero_my_account_endpoint_content');

        $this->loader->add_action( 'wc_ajax_qero_my_account', $plugin_public, 'qero_my_account');
        $this->loader->add_action( 'wc_ajax_qero_my_account_new_qero', $plugin_public, 'qero_my_account_new_qero');
        $this->loader->add_action( 'wc_ajax_qero_my_account_movements', $plugin_public, 'qero_my_account_movements');
        $this->loader->add_action( 'wc_ajax_qero_my_account_campaigns', $plugin_public, 'qero_my_account_campaigns');
        $this->loader->add_action( 'wc_ajax_qero_my_account_movements_pages', $plugin_public, 'qero_my_account_movements_pages');
        $this->loader->add_action( 'wc_ajax_qero_my_account_infos', $plugin_public, 'qero_my_account_infos');
        $this->loader->add_action( 'wc_ajax_add_points_discount',$plugin_public,'add_points_discount');
        $this->loader->add_action( 'wc_ajax_remove_points_discount',$plugin_public,'remove_points_discount');
        $this->loader->add_action( 'wc_ajax_qero_checkout_valid_cellphone', $plugin_public, 'qero_checkout_valid_cellphone');
        $this->loader->add_action( 'wc_ajax_qero_checkout_forget_cellphone_anon', $plugin_public, 'qero_checkout_forget_cellphone_anon');
        $this->loader->add_action( 'wc_ajax_qero_checkout_new_qero', $plugin_public, 'qero_checkout_new_qero');

        $this->loader->add_action( 'woocommerce_review_order_before_payment', $plugin_public, 'qero_checkout_page');
        //$this->loader->add_action( 'woocommerce_checkout_update_order_meta', $plugin_public, 'qero_add_cellphone_to_order' ,10, 1);
        $this->loader->add_action( 'woocommerce_checkout_create_order', $plugin_public, 'qero_filter_order' ,10, 1);
        $this->loader->add_action( 'woocommerce_new_order', $plugin_public, 'qero_add_notes_order' ,10, 1);
        $this->loader->add_action( 'woocommerce_single_product_summary', $plugin_public, 'qero_product_view');
        $this->loader->add_action( 'woocommerce_before_add_to_cart_quantity', $plugin_public, 'qero_variation_product_view', 10 );

        $this->loader->add_action('woocommerce_cart_calculate_fees', $plugin_public,'qero_checkout_totals', 1);
        $this->loader->add_action('woocommerce_review_order_before_order_total', $plugin_public, 'qero_hold_movement');

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$trigger = get_option('qero_order_status_release');
		switch ($trigger){
            case 'completed':
                $this->loader->add_action( 'woocommerce_order_status_completed', $plugin_public, 'qero_communicate_movement');
                break;
		    case 'processing':
            default:
                $this->loader->add_action( 'woocommerce_order_status_processing', $plugin_public, 'qero_communicate_movement');
                break;
        }

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Qero_For_Wp_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

    /**
     * Get main file.
     *
     * @return string
     */
    public static function get_main_file() {
        return QERO_FOR_WP_FILE;
    }

    /**
     * Get plugin path.
     *
     * @return string
     */
    public static function get_plugin_path() {
        return plugin_dir_path( QERO_FOR_WP_FILE );
    }

    /**
     * Get templates path.
     *
     * @return string
     */
    public static function get_templates_path() {
        return self::get_plugin_path() . 'public/templates/';
    }

    /**
     * Get templates path.
     *
     * @return string
     */
    public static function get_admin_templates_path() {
        return self::get_plugin_path() . 'admin/templates/';
    }

}
