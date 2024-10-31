<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://qero.io/pt/fidelizacao-qero/
 * @since      1.0.0
 *
 * @package    Qero_For_Wp
 * @subpackage Qero_For_Wp/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Qero_For_Wp
 * @subpackage Qero_For_Wp/admin
 * @author     Qero <Lda.>
 */


include_once Qero_For_Wp::get_plugin_path().'includes/class-qero-for-wp-api-wrap.php';
include_once Qero_For_Wp::get_plugin_path().'includes/class-qero-for-wp-admin-bo.php';


class Qero_For_Wp_Admin{

    const TABLE_NAME    = 'qero_association';
    const DISCOUNT_NAME = 'qero_discount_points';
    const LOGS          = 'qero_logs';
    const BYPASS_CLASS  = 'qero-clean';

    /**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;
    private $parent_plugin_name = 'egoi-for-wp';
    protected $apiClient = false;
    protected $errors;

    /**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        if(!empty(self::getApiKey())){
            $this->apiClient = new QEROAPI(self::getApiKey());
        }
        
        $this->errors = [
            'invalid_apikey'    => __('Missing Egoi\'s ApiKey!','qero-for-wp'),
            'invalid_store'     => __('Store id is not valid!','qero-for-wp'),
        ];
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook) {
        wp_enqueue_style( $this->plugin_name.'-all', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), $this->version, 'all' );

        if(strpos($hook, 'qero_page_') === false){
            return;
        }

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/qero-for-wp-admin.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name.'-bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );

    }

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook) {

	    if(strpos($hook, 'qero_page_') === false){
	        return;
        }

        wp_enqueue_script( $this->plugin_name.'-common', plugin_dir_url( __FILE__ ) . 'js/qero-for-wp-common.js', array( 'jquery' ), $this->version, false );

        $pages = [
          'dashboard'   => 'qero-for-wp-dashboard',
          'account'     => 'qero-for-wp-account'
        ];

        wp_enqueue_script('jquery');
        wp_localize_script('jquery', 'qero_ajax_object', array(
                'ajax_url'       => admin_url( 'admin-ajax.php' ),
                'ajax_nonce'     => wp_create_nonce( 'admin_actions' ))
        );

	    //shared js
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/qero-for-wp-admin.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( $this->plugin_name.'-bootstrap', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( $this->plugin_name.'-simplepagination', plugin_dir_url( __FILE__ ) . 'js/jquery.simplePagination.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( $this->plugin_name.'-fa', plugin_dir_url( __FILE__ ) . 'js/fontawesome.js', array( 'jquery' ), $this->version, false );

        if(strpos($hook, $pages['account']) !== false){//shared from account
            wp_enqueue_script( $this->plugin_name.$pages['account'], plugin_dir_url( __FILE__ ) . 'js/admin/account/qero-for-wp-config.js', array( 'jquery' ), null, true);
        }

        if(strpos($hook, $pages['dashboard']) !== false){
            wp_enqueue_script( $this->plugin_name.$pages['dashboard'], plugin_dir_url( __FILE__ ) . 'js/admin/dashboard/qero-for-wp-dashboard.js', array( 'jquery' ), null, true);
            wp_enqueue_script( $this->plugin_name.'-chartjs',  plugin_dir_url( __FILE__ ) . 'js/chart-2.8.0.js', array( 'jquery' ), $this->version, false );

        }


	}

    public function qero_add_options_page() {

        add_menu_page( 'Qero - Main Page', 'Qero', 'Qero Plugin', $this->plugin_name, array($this, 'qero_display_config'), plugin_dir_url( __FILE__ ).'assets/_icon_qero.svg');

        $capability = 'manage_options';

        add_submenu_page($this->plugin_name, __('Account', 'qero-for-wp'), '<i class="far fa-user-circle"></i> '.__('Account', 'qero-for-wp'), $capability, 'qero-for-wp-account', array($this, 'qero_display_account'));

        if(self::getConfiguredStatus()) {
            add_submenu_page($this->plugin_name, __('Dashboard', 'qero-for-wp'), '<i class="fas fa-chart-line"></i> ' . __('Dashboard', 'qero-for-wp'), $capability, 'qero-for-wp-dashboard', array($this, 'qero_display_dashboard'));
        }

    }

    /**
     * Render the options page for plugin
     *
     * @since  1.0.0
     */
    public function qero_display_account() {
        include_once 'partials/qero-for-wp-header.php';

        $apiKey = self::getApiKey();
        update_option('qero_api_key', $apiKey);
        if(!empty($apiKey)){
            $this->apiClient = new QEROAPI(self::getApiKey());
            $this->apiClient->companyAssociation();
        }
        echo '<div class="'.self::BYPASS_CLASS.'">';
        wc_get_template(
            'account/qero-for-wp-config.php',
            array(
                'stores'    => !empty($this->apiClient)?$this->apiClient->getStores():false,
                'apikey'    => Qero_For_Wp_Admin::getApiKey(),
            ),
            '',
            Qero_For_Wp::get_admin_templates_path()
        );
        echo '<div>';
    }

    /**
     * Render the Dashboard page for plugin
     *
     * @since  1.0.0
     */
    public function qero_display_dashboard() {

        $qero = new QeroLogic();
        echo '<div class="'.self::BYPASS_CLASS.'">';
        wc_get_template(
            'dashboard/qero-for-wp-dashboard.php',
            array(
                'movement'  => $qero->dashboardInfo(),
                'pages'     => [
                    'campaigns' => $qero->getCampaingPages(),
                    'clients'   => $qero->getClientsPages()
                ]
            ),
            '',
            Qero_For_Wp::get_admin_templates_path()
        );
        echo '<div>';

    }

    public function qero_config_new_app(){
        check_ajax_referer( 'admin_actions', 'security' );
        $app_name = filter_var($_POST['app_name'], FILTER_SANITIZE_STRING);
        if(empty($app_name))
            wp_send_json_success(['ERROR' => __('Invalid store name.', 'qero-for-wp')]);


        global $wpdb;
        $store = $this->apiClient->createStores([
            'name'          => $app_name,
            'storeCode'     => $wpdb->prefix . strtolower(str_replace(' ', '_', $app_name)),
            'status'        => 1
        ]);

        wp_send_json_success(['status' => true]);
    }

    public function qero_config(){
        check_ajax_referer( 'admin_actions', 'security' );
        $apiKey = self::getApiKey();

        if(empty($apiKey) || empty($this->apiClient))
            wp_send_json_success(['ERROR' => $this->errors['invalid_apikey']]);

        $resp = $this->apiClient->companyAssociation();

        if($resp == false)
            wp_send_json_success(['ERROR' => $this->apiClient->getLastError()]);

        update_option('qero_api_key', $apiKey);

        $store = $this->apiClient->storeAssociation(['store_id' => filter_var($_POST['app_id'],FILTER_SANITIZE_STRING)]);

        if(empty($store['name']))
            wp_send_json_success(['ERROR' => $this->errors['invalid_store']]);

        update_option('qero_app_name', $store['name']);//TODO:validation

        wp_send_json_success(['status' => true]);
    }

    public function qero_get_stores(){
        check_ajax_referer( 'admin_actions', 'security' );

        $allowed = ['name', 'store_id', 'storeCode'];
        $stores = $this->apiClient->getStores();

        foreach ($stores as &$store){
            $store = array_filter(
                $store,
                function ($key) use ($allowed) {
                    return in_array($key, $allowed);
                },
                ARRAY_FILTER_USE_KEY
            );
        }

        wp_send_json_success(['data' =>  $stores ]);
    }

    public function qero_get_clients(){
        check_ajax_referer( 'admin_actions', 'security' );

        $apiKey = self::getApiKey();

        if(empty($apiKey) || empty($this->apiClient))
            wp_send_json_success(['ERROR' => $this->errors['invalid_apikey']]);

        $resp = $this->apiClient->getClients();

        if($resp == false)
            wp_send_json_success(['ERROR' => $this->apiClient->getLastError()]);

        wp_send_json_success(['data' => $resp]);
    }

    public static function getApiKey(){
        $apikey = get_option('egoi_api_key');
        if(isset($apikey['api_key']) && ($apikey['api_key'])) {
            return $apikey['api_key'];
        }

        return false;
    }

    public static function getConfiguredStatus(){
        $apiKey = get_option('qero_api_key');
        $qeroApp = get_option('qero_app_name');

        return (!empty($apiKey) && $apiKey == self::getApiKey() && !empty($qeroApp));
    }

    /**
     * Check if is time to give points to the subscriber
     * @param $order_id
     */
    function qero_points_unlock($order_id){ //TODO:ask wich
    }

    function qero_email_invite($emails){

        if ( ! isset( $emails[ 'Qero_Invite_Email' ] ) ) {
            $emails[ 'Qero_Invite_Email' ] = include_once(  untrailingslashit( plugin_dir_path( __FILE__ ) ).'/../emails/class-qero-invite-email.php' );
        }

        return $emails;
    }

    public function qero_add_extra_user_info($data){
        $data['qero'] = __('Loyal','qero-for-wp');
        return $data;
    }

    public function qero_add_extra_user_info_row($val, $column_name, $user_id){
        if($column_name !== 'qero')
            return $val;
        $user_info = get_userdata($user_id);
        $qero = new QeroLogic(!empty($user_info->user_email)?$user_info->user_email:'');

        return $qero->isLoyal()?'<i class="fas fa-check"></i>':'<i class="fas fa-times-circle"></i>';
    }

    public function qero_manage_invite_email($schedules){
        $schedules[QERO_EMAIL_CRON] = array(
            'interval' => 60*10,
            'display'  => __( 'Qero Event Cron' ),
        );
        return $schedules;
    }

    public function qero_shot_email_invite(){

        $time = get_option(QERO_WAIT_TIME_OPTN);
        if(empty($time) || ! is_numeric($time))
            $time = 60*60;//default 1 hour in seconds

        $last = get_option(QERO_LAST_CRON_EXEC);
        $last_date = date('Y-m-d H:i:s',$last == false?0:$last);

        $users = QeroLogic::getUsersToInvite($last_date);

        QeroLogic::shotInviteEmailToUsers($users, $time);
    }

    public function qero_trigger_email_invite($user_id){
        if ( isset( $user_id ) && 0 != $user_id ) {

            do_action( 'qero_invite_email_notification', $user_id );

        }
    }

    /**
     * Ajax request for dashboard
     */
    function qero_dashboard_sales(){
        check_ajax_referer( 'admin_actions', 'security' );
        $qero = new QeroLogic();

        wp_send_json_success(['data' => $qero->dashboardSales()]);
    }

    function qero_dashboard_client_creation(){
        check_ajax_referer( 'admin_actions', 'security' );
        $qero = new QeroLogic();

        wp_send_json_success(['data' => $qero->dashboardClientCreation()]);
    }

    function qero_dashboard_client_sales(){
        check_ajax_referer( 'admin_actions', 'security' );
        $qero = new QeroLogic();
        $page = filter_var($_POST['page'], FILTER_SANITIZE_NUMBER_INT);
        wp_send_json_success(['data' => $qero->dashboardClientSales(!empty($page)?$page:1)]);
    }

    function qero_dashboard_campaigns(){
        check_ajax_referer( 'admin_actions', 'security' );
        $qero = new QeroLogic();
        $page = filter_var($_POST['page'], FILTER_SANITIZE_NUMBER_INT);
        wp_send_json_success(['data' => $qero->dashboardCampaigns(!empty($page)?$page:1)]);
    }

    function qero_force_invite_all(){
        check_ajax_referer( 'admin_actions', 'security' );

        if(get_option(base64_encode(QERO_LAST_CRON_EXEC)) !== false)
            wp_send_json_error(__('This is one time use only.','qero-for-wp'));

        update_option(base64_encode(QERO_LAST_CRON_EXEC), 1);
        $users = QeroLogic::getUsersToInvite(date('Y-m-d H:i:s', 0));//get all users
        QeroLogic::shotInviteEmailToUsers($users);

        wp_send_json_success(true);

    }

}
