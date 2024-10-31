<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://qero.io/pt/fidelizacao-qero/
 * @since      1.0.0
 *
 * @package    Qero_For_Wp
 * @subpackage Qero_For_Wp/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Qero_For_Wp
 * @subpackage Qero_For_Wp/public
 * @author     Qero <Lda.>
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

include_once Qero_For_Wp::get_plugin_path().'includes/class-qero-for-wp-admin-bo.php';

class Qero_For_Wp_Public {

    const META_CREDIT_IN    = '_qero_points_in';
    const META_CREDIT_OUT   = '_qero_points_out';
    const META_CELLPHONE    = '_qero_cellphone';
    const META_CLIENT_ID    = '_qero_client_id';

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Qero_For_Wp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Qero_For_Wp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/qero-for-wp-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

        global $wp;
        $url = home_url( $wp->request );

        wp_enqueue_script('jquery');

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/qero-for-wp-public.js', array( 'jquery' ), $this->version, true );
        wp_enqueue_script( $this->plugin_name.'-fa', plugin_dir_url( __FILE__ ) . 'js/fontawesome.js', array( 'jquery' ), $this->version, false );

        if (strpos($url, "/qero") !== false){

            wp_localize_script('jquery', 'qero_ajax_my_account_cellphone', array(
                    'ajax_url'       => WC_AJAX::get_endpoint( 'qero_my_account' ),
                    'ajax_nonce'     => wp_create_nonce( 'public_actions' ))
            );

            wp_localize_script('jquery', 'qero_ajax_my_account_new_account', array(
                    'ajax_url'       => WC_AJAX::get_endpoint( 'qero_my_account_new_qero' ),
                    'ajax_nonce'     => wp_create_nonce( 'public_actions' ))
            );

            wp_localize_script('jquery', 'qero_ajax_get_movements', array(
                    'ajax_url'       => WC_AJAX::get_endpoint( 'qero_my_account_movements' ),
                )
            );

            wp_localize_script('jquery', 'qero_ajax_get_movements_count', array(
                    'ajax_url'       => WC_AJAX::get_endpoint( 'qero_my_account_movements_pages' ),
                )
            );

            wp_localize_script('jquery', 'qero_ajax_get_campaigns', array(
                    'ajax_url'       => WC_AJAX::get_endpoint( 'qero_my_account_campaigns' ),
                )
            );

        }

        if(is_checkout() || strpos($url, "/qero") !== false){

            wp_localize_script('jquery', 'qero_ajax_checkout_not_logged_cellphone', array(
                    'ajax_url'       => WC_AJAX::get_endpoint( 'qero_checkout_valid_cellphone' ),
                    'ajax_nonce'     => wp_create_nonce( 'public_actions' ))
            );

            wp_localize_script('jquery', 'qero_ajax_get_infos', array(
                    'ajax_url'       => WC_AJAX::get_endpoint( 'qero_my_account_infos' ),
                )
            );
        }

        if( is_checkout() ){

            wp_localize_script('jquery', 'qero_ajax_add_points_discount', array(
                    'ajax_url'       => WC_AJAX::get_endpoint( 'add_points_discount' ),
                    'ajax_nonce'     => wp_create_nonce( 'public_actions' ))
            );

            wp_localize_script('jquery', 'qero_ajax_remove_points_discount', array(
                    'ajax_url'       => WC_AJAX::get_endpoint( 'remove_points_discount' ),
                    'ajax_nonce'     => wp_create_nonce( 'public_actions' ))
            );

            wp_localize_script('jquery', 'qero_ajax_checkout_new_qero', array(
                    'ajax_url'       => WC_AJAX::get_endpoint( 'qero_checkout_new_qero' ),
                    'ajax_nonce'     => wp_create_nonce( 'public_actions' ))
            );

            wp_localize_script('jquery', 'qero_ajax_checkout_forget_qero_anon', array(
                    'ajax_url'       => WC_AJAX::get_endpoint( 'qero_checkout_forget_cellphone_anon' ),
                    'ajax_nonce'     => wp_create_nonce( 'public_actions' ))
            );

        }

	}


    /**
     * My Account Menu
     * @param $menu_links
     * @return array
     */
    public function qero_points_menu($menu_links)
    {
        $costume_menu =
            array( 'qero' => __('Qero Points', 'qero-for-wp') );

        return ! empty($menu_links)
            ? array_slice($menu_links, 0, 1, TRUE)
                + $costume_menu
                + array_slice($menu_links, 1, NULL, TRUE)
            : $costume_menu;
    }

    function qero_add_endpoint() {
        add_rewrite_endpoint( 'qero', EP_ROOT | EP_PAGES );
        if(!get_option('qero_endpoint_')){
            add_option('qero_endpoint_', true);
            $this->qero_flush_rewrite_rules();
        }
        //global $wp_filter;
        //die(print_r($wp_filter));
    }

    /**
     * My_Account splitter, Loyal, Not Loyal
     */
    function qero_my_account_endpoint_content() {

        $current_user = wp_get_current_user();
        if ( ! $current_user->exists() ) {
            return;
        }

        $qero = new QeroLogic($current_user->user_email);

        if($qero->isLoyal()){

            wp_enqueue_script( $this->plugin_name.'_myaccount', plugin_dir_url( __FILE__ ) . 'js/qero-for-wp-myaccount-loyal.js', array( 'jquery' ), $this->version, true );

            wc_get_template(
                'myaccount/qero-for-wp-loyal.php',
                array(
                    'qero' => $qero,
                ),
                '',
                Qero_For_Wp::get_templates_path()
            );
        }else{

            wp_enqueue_script( $this->plugin_name.'_myaccount', plugin_dir_url( __FILE__ ) . 'js/qero-for-wp-myaccount.js', array( 'jquery' ), $this->version, true );

            wc_get_template(
                'myaccount/qero-for-wp-not-loyal.php',
                array(
                    'qero' => $qero,
                    'indicatives' => QeroLogic::getCountriesAndIndicative(),
                ),
                '',
                Qero_For_Wp::get_templates_path()
            );
        }

    }

    /**
     * Checkout splitter, not logged, logged not loyal, logged loyal
     */
    function qero_checkout_page(){

        $current_user = wp_get_current_user();
        if ( $current_user->exists() ) {

            $qero = new QeroLogic($current_user->user_email);

            if($qero->isLoyal()){
                wp_enqueue_script( $this->plugin_name.'_checkout', plugin_dir_url( __FILE__ ) . 'js/checkout/qerp-for-wp-logged-loyal.js', array( 'jquery' ), $this->version, true );

                wc_get_template(
                    'checkout/qero-for-wp-logged-loyal.php',
                    array(
                        //'qero' => $qero,
                    ),
                    '',
                    Qero_For_Wp::get_templates_path()
                );
                return;
            }else{
                wc_get_template(
                    'checkout/qero-for-wp-logged-not-loyal.php',
                    array(
                        //'qero' => $qero,
                    ),
                    '',
                    Qero_For_Wp::get_templates_path()
                );
                return;
            }
        }else{
            wp_enqueue_script( $this->plugin_name.'_checkout', plugin_dir_url( __FILE__ ) . 'js/checkout/qero-for-wp-not-logged.js', array( 'jquery' ), $this->version, true );

            wc_get_template(
                'checkout/qero-for-wp-not-logged.php',
                array(
                    'indicatives' => QeroLogic::getCountriesAndIndicative(),
                ),
                '',
                Qero_For_Wp::get_templates_path()
            );
        }

    }

    /**
     * flush
     */
    function qero_flush_rewrite_rules() {
        flush_rewrite_rules();
    }

    /**
     * Ajax|internal validate account can be associate
     * @param null $post
     * @return bool
     */
    function qero_my_account($post=null){
        $_POST = !empty($post)?$post:$_POST;
        if(!empty($post))
            check_ajax_referer( 'public_actions', 'security' );

        $cellphone = trim(filter_var ( $_POST['cellphone'], FILTER_SANITIZE_NUMBER_INT));
        if(empty($cellphone))
            wp_send_json_success(['ERROR' => __('The field "cellphone" should not be empty!','qero-for-wp')]);

        $current_user = wp_get_current_user();
        if ( ! $current_user->exists() ) {
            wp_send_json_success(['ERROR' => __('User not authenticated!','qero-for-wp')]);
        }

        $qero = new QeroLogic($current_user->user_email);
        if($qero->isLoyal())
            wp_send_json_success(['ERROR' => __('User already registered!','qero-for-wp')]);

        if($qero->checkMobilePhoneIsAlreadyQeroSubscriber($cellphone) == true){
            wp_send_json_success(['data' => $qero->associateUser($cellphone)]);//TODO:2fa
        }

        if(empty($post))
            wp_send_json_success(['SHOW' => 'second_step']);
        return true;
    }

    /**
     * Ajax associate account
     */
    function qero_my_account_new_qero(){
        check_ajax_referer( 'public_actions', 'security' );

        $cellphone = filter_var($_POST['cellphone'],FILTER_SANITIZE_NUMBER_INT);
        $email = sanitize_email($_POST['email']);
        $name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);

        if(empty($cellphone) || empty($email) || empty($name))
            wp_send_json_success(['ERROR' => __('The field "cellphone" and "name" should not be empty!','qero-for-wp')]);


        $this->qero_my_account($_POST);

        $current_user = wp_get_current_user();
        $qero = new QeroLogic($current_user->user_email);
        if($qero->createNewSubscriberQero(
            [
                'cellphone' => $cellphone,
                'email'     => $email,
                'name'      => $name
            ]
        ) !== true){
            wp_send_json_success(['ERROR' => __('Please verify the inserted data.','qero-for-wp')]);
        }

        wp_send_json_success(['data' => $qero->associateUser($cellphone)]);
    }

    /**
     * Ajax create qero subscriber checkout
     */
    function qero_checkout_new_qero(){
        check_ajax_referer( 'public_actions', 'security' );

        $cellphone = filter_var($_POST['cellphone'],FILTER_SANITIZE_NUMBER_INT);
        $email = sanitize_email($_POST['email']);
        $name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);

        if(empty($cellphone))
            wp_send_json_success(['ERROR' => __('Cellphone is required','qero-for-wp')]);

        $qero = new QeroLogic();
        if($qero->createNewSubscriberQero(
            [
                'cellphone' => $cellphone,
                'email'     => $email,
                'name'      => $name
            ]
        ) !== true){
            wp_send_json_success(['ERROR' => __('Please verify the inserted data.','qero-for-wp')]);
        }

        $_SESSION['qero_cellphone'] = $cellphone;
        $_SESSION['qero_client_id'] = $qero->getClientIdFromMobile($cellphone);

        wp_send_json_success(['data' => true]);
    }

    /**
     * Ajax forget anon user
     */
    function qero_checkout_forget_cellphone_anon(){
        check_ajax_referer( 'public_actions', 'security' );
        unset($_SESSION['qero_cellphone']);
        unset($_SESSION['qero_client_id']);
        wp_send_json_success(['data' => true]);
    }

    function qero_my_account_movements_pages(){
        $current_user = wp_get_current_user();
        if ( ! $current_user->exists() ) {
            wp_send_json_success(['ERROR' => __('User not authenticated!','qero-for-wp')]);
        }
        $qero = new QeroLogic($current_user->user_email);
        wp_send_json_success(['data' => $qero->getMovementsPages()]);

    }

    /**
     * get Campaigns
     */
    function qero_my_account_campaigns(){
        $current_user = wp_get_current_user();
        if ( ! $current_user->exists() ) {
            wp_send_json_success(['ERROR' => __('User not authenticated!','qero-for-wp')]);
        }

        $qero = new QeroLogic($current_user->user_email);
        $resp = $qero->getCampaigns();
        wp_send_json_success(['data' => $resp]);
    }

    /**
     * Ajax give qero subscriber's movements
     */
    function qero_my_account_movements(){
        $since = null;$until = null;$page = 1;
        if(isset($_GET['since']))
            $since = filter_var($_GET['since'], FILTER_SANITIZE_NUMBER_INT);

        if(isset($_GET['until']))
            $until = filter_var($_GET['until'], FILTER_SANITIZE_NUMBER_INT);

        if(isset($_GET['page']))
            $page = filter_var($_GET['page'], FILTER_SANITIZE_NUMBER_INT);

        $current_user = wp_get_current_user();
        if ( ! $current_user->exists() ) {
            wp_send_json_success(['ERROR' => __('User not authenticated!','qero-for-wp')]);
        }

        $qero = new QeroLogic($current_user->user_email);

        wp_send_json_success(['data' => $qero->getLastClientMovement(QeroLogic::LIMIT_PAGE, $page-1)]);

    }

    /**
     * Ajax give qero subscriber's infos
     */
    function qero_my_account_infos(){
        $current_user = wp_get_current_user();
        if ( ! $current_user->exists() ) {
            wp_send_json_success(['ERROR' => __('User not authenticated!','qero-for-wp')]);
        }

        $qero = new QeroLogic($current_user->user_email);
        $balance = $qero->getBalance();

        if($balance == false)
            wp_send_json_success(['ERROR' => __('No information!','qero-for-wp')]);


        wp_send_json_success(['data' => $balance]);

    }

    /**
     * Ajax phone validator
     */
    function qero_checkout_valid_cellphone(){
        check_ajax_referer( 'public_actions', 'security' );

        unset($_SESSION['qero_client_id']);
        unset($_SESSION['qero_cellphone']);

        $cellphone = filter_var($_POST['cellphone'],FILTER_SANITIZE_NUMBER_INT);

        if(empty($cellphone))
            return;


        $qero = new QeroLogic();

        if(!$qero->checkMobilePhoneIsAlreadyQeroSubscriber($cellphone))
            wp_send_json_success(['data' =>  false]);

        $_SESSION['qero_cellphone'] = $cellphone;
        $_SESSION['qero_client_id'] = $qero->getClientIdFromMobile($cellphone);
        wp_send_json_success(['data' =>  true]);
    }

    /**
     * Communicate movement
     * @param $order_id
     * @return bool|string
     */
    function qero_communicate_movement($order_id){
        $user = get_userdata(
            get_post_meta( $order_id, '_customer_user', true )
        );

        if(!empty($user)){
            $qero = new QeroLogic($user->user_email);

            $qero->makeAccountMovements($order_id, 'BUY', get_post_meta( $order_id, self::META_CREDIT_OUT, true ));
        }else{
            $client_id = get_post_meta( $order_id, self::META_CLIENT_ID, true );

            if(empty($client_id))
                return false;

            $qero = new QeroLogic();
            $qero->makeAccountMovements($order_id,'BUY',0,$client_id);
        }
    }

    function qero_anon_product_view(){

        $qero = new QeroLogic();
        $movement = $qero->getHoldAccountMovementOnCart(
            WC()->cart->get_subtotal() * 100,
            WC()->cart->get_subtotal_tax() * 100,
            QeroLogic::productViewConverter(),
            $_SESSION['qero_client_id']
        );

        wc_get_template(
            'product/qero-for-wp-product-points-earn.php',
            array(
                'movement' => $movement,
            ),
            '',
            Qero_For_Wp::get_templates_path()
        );
        return true;
    }

    function qero_variation_product_view($a){
        global $product;
    }

    function qero_product_view(){
        $current_user = wp_get_current_user();
        if ( ! $current_user->exists() && empty($_SESSION['qero_client_id'])){
            return false;
        } else if (!empty($_SESSION['qero_client_id'])){
            return $this->qero_anon_product_view();
        }

        $qero = new QeroLogic($current_user->user_email);
        if(!$qero->isLoyal()){
            return false;
        }

        $movement = $qero->getHoldAccountMovementOnCart(
            WC()->cart->get_subtotal() * 100,
            WC()->cart->get_subtotal_tax() * 100,
            QeroLogic::productViewConverter()
        );

        wc_get_template(
            'product/qero-for-wp-product-points-earn.php',
            array(
                'movement' => $movement,
            ),
            '',
            Qero_For_Wp::get_templates_path()
        );

        return true;
    }

    /**
     * Add cellphone to the unlogged order, to know which number should receive the points
     * Register all points out and in in order
     * @param $order_id
     * @return bool
     */
    function qero_add_cellphone_to_order($order_id){
        return true;

        $order = wc_get_order( $order_id );

        if(empty($order))
            return false;

        $qero_cellphone_lock = filter_var($_POST ['qero_cellphone_lock'], FILTER_SANITIZE_NUMBER_INT);

        $current_user = wp_get_current_user();

        if (! empty($qero_cellphone_lock) && ! $current_user->exists()) {//guest accounts can get points
            $qero = new QeroLogic();
            $client_id = $qero->getClientIdFromMobile($qero_cellphone_lock);

            if(empty($client_id))
                return false;

            add_post_meta( $order_id, self::META_CELLPHONE, $qero_cellphone_lock);
            add_post_meta( $order_id, self::META_CLIENT_ID, filter_var($client_id, FILTER_SANITIZE_STRING) );
        }
    }

    function qero_add_notes_order($order_id){
        $order = new WC_Order( $order_id );

        $credit_in = $order->get_meta(self::META_CREDIT_IN);
        $credit_out = $order->get_meta(self::META_CREDIT_OUT);

        if(!empty($credit_out)){
            $order->add_order_note( sprintf( __( 'In this order were spent %s points!', 'qero-for-wp' ), $credit_out ) );
        }

        $trigger = get_option('qero_order_status_release');

        if(!empty($credit_in)){
            $order->add_order_note( sprintf( __( 'This order generated %s points! The Customer will receive them once this order goes to "%s".', 'qero-for-wp' ), $credit_in, ucfirst(empty($trigger)?'processing':$trigger) ) );
        }
    }

    function qero_filter_order($order){
        $current_user = wp_get_current_user();
        $qero_cellphone_lock = filter_var($_POST ['qero_cellphone_lock'],FILTER_SANITIZE_NUMBER_INT);
        if(! $current_user->exists() && empty($qero_cellphone_lock)){
            return $order;
        }else if(!empty($qero_cellphone_lock)){
            $qero = new QeroLogic();
            $client_id = $qero->getClientIdFromMobile($qero_cellphone_lock);

            if(empty($client_id))
                return false;

            $order->update_meta_data( self::META_CELLPHONE, $qero_cellphone_lock );
            $order->update_meta_data( self::META_CLIENT_ID, $client_id );

            $mov =  $qero->getHoldAccountMovementOnOrder($order, 0, $client_id);
        } else {
            $qero = new QeroLogic($current_user->user_email);
            if(!$qero->isLoyal()){
                return $order;
            }

            $mov =  $qero->getHoldAccountMovementOnOrder($order, $qero->getDiscount());
        }
        if($mov === false){
            //mov not possible
            return false;
        }

        $credit_in = $mov['credit_in'];
        $credit_out = $mov['credit_out'];

        $order->update_meta_data( self::META_CREDIT_IN, empty($credit_in)?0:$credit_in );
        $order->update_meta_data( self::META_CREDIT_OUT, empty($credit_out)?0:$credit_out );

        return $order;
    }

    /**
     * Show thank you message with points earns
     * @param $order
     */
    function qero_thankyou_page_info($order){//TODO: register earning points in products options

        if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }

        $points = get_post_meta( $order_id, self::META_CREDIT_IN, true );
        if(empty($points))
            return;
        wc_get_template(
            'thankyou/qero-for-wp-thankyou.php',
            array(
                'points' => $points,
            ),
            '',
            Qero_For_Wp::get_templates_path()
        );
    }

    function remove_points_discount(){
        check_ajax_referer( 'public_actions', 'security' );

        $current_user = wp_get_current_user();
        if ( ! $current_user->exists() )
            wp_send_json_success(['ERROR' => __('User not authenticated!','qero-for-wp')]);

        $qero = new QeroLogic($current_user->user_email);

        wp_send_json_success(['data' => $qero->deleteDiscount()]);

    }

    function add_points_discount(){
        check_ajax_referer( 'public_actions', 'security' );

        $current_user = wp_get_current_user();
        if ( ! $current_user->exists() )
            wp_send_json_success(['ERROR' => __('User not authenticated!','qero-for-wp')]);

        $qero = new QeroLogic($current_user->user_email);
        if(!$qero->isLoyal())
            wp_send_json_success(['ERROR' => __('Before spending points you need to have a loyal account! Go to "My Account".','qero-for-wp')]);

        $balance = $qero->getBalance();

        if($balance == false)
            wp_send_json_success(['ERROR' => __('No information!','qero-for-wp')]);

        if(//sanitizing verification
            empty($_POST['points'])
            || !( is_float($_POST['points']) || is_numeric($_POST['points']) )
            || !(((float) $balance['max_points'] >= (float) $_POST['points'])
                && (0<$_POST['points'])
                && ((float) $balance['available'] >= (float) $_POST['points'])
            )
        )
            wp_send_json_success(['ERROR' => __('Invalid points!','qero-for-wp')]);

        $qero->applyDiscount($_POST['points']);

        WC()->cart->calculate_fees();

        wp_send_json_success(['data' => true]);
    }

    function qero_checkout_totals($cart){

        $current_user = wp_get_current_user();
        if ( ! $current_user->exists() ){
            return;
        }

        $qero = new QeroLogic($current_user->user_email);
        if(!$qero->isLoyal())
            return;

        $discount = $qero->getDiscount();

        $balance = $qero->getBalance();

        if(empty($balance['max_points']) || empty($discount) || $balance['max_points'] < $discount)
            return;

        $cart->add_fee(sprintf( __('Discount %s points', 'qero-for-wp'), $discount), -$discount*0.01 );

    }

    function qero_hold_movement(){
        $current_user = wp_get_current_user();
        if ( ! $current_user->exists() )
            return $this->qero_anon_hold_movement();

        $qero = new QeroLogic($current_user->user_email);
        if(!$qero->isLoyal())
            return false;

        if(empty(WC()->cart))
            return false;

        $items = WC()->cart->get_cart();

        $items =  $qero->convertWCProductsToQero($items);
        $movement = $qero->getHoldAccountMovementOnCart(
            WC()->cart->get_subtotal() * 100,
            WC()->cart->get_subtotal_tax() * 100,
            $items
        );

        if(empty($movement['credit_in']))
            return false;

        wc_get_template(
            'checkout/qero-for-wp-cart-points.php',
            array(
                'movement' => $movement,
            ),
            '',
            Qero_For_Wp::get_templates_path()
        );

        return true;
    }

    function qero_anon_hold_movement(){
        if(empty($_SESSION['qero_client_id']))
            return false;

        if(empty(WC()->cart))
            return false;

        $items = WC()->cart->get_cart();

        $qero = new QeroLogic();

        $items =  $qero->convertWCProductsToQero($items);
        $movement = $qero->getHoldAccountMovementOnCart(
            WC()->cart->get_subtotal() * 100,
            WC()->cart->get_subtotal_tax() * 100,
            $items,
            $_SESSION['qero_client_id']
        );

        wc_get_template(
            'checkout/qero-for-wp-cart-points.php',
            array(
                'movement' => $movement,
            ),
            '',
            Qero_For_Wp::get_templates_path()
        );

        return true;
    }

}
