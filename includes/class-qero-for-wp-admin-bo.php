<?php
/**
 * Created by PhpStorm.
 * User: tmota
 * Date: 07/08/2019
 * Time: 10:03
 */


class QeroLogic {

    const LIMIT_PAGE = 5;

    /** @var QEROAPI */
    protected $apiClient;
    protected $email;

    /**
     * QeroLogic constructor.
     * @param $email
     */
    public function __construct($email=null)
    {
        if(!empty($email))
            $this->email = $email;

        $this->apiClient = new QEROAPI(Qero_For_Wp_Admin::getApiKey());
    }

    /**
     * @return bool
     */
    public function isLoyal(){
        global $wpdb;

        $table   = $wpdb->prefix . Qero_For_Wp_Admin::TABLE_NAME;
        $association = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE email = %s;", $this->email ) );
        if(empty($association))
            return false;
        return true;
    }

    /**
     * @param $mobile_number
     * @return bool
     */
    public function checkMobilePhoneIsAlreadyQeroSubscriber($mobile_number){
        return !empty($this->apiClient->getClients($mobile_number));
    }

    /**
     * @param $data
     * @return bool|string
     */
    public function createNewSubscriberQero($data){

        if(empty($data['cellphone']))
            return false;


        if($this->checkMobilePhoneIsAlreadyQeroSubscriber($data['cellphone']))
            return true;

        return $this->apiClient->registerNewClient([
            'secondary_id'  => $data['cellphone'],
            'email'         => $data['email'],
            'name'          => $data['name']
        ]);
    }

    /**
     * @param $mobile_number
     * @return bool
     */
    public function associateUser($mobile_number){
        $data = $this->apiClient->getClients($mobile_number);

        if($data === false || empty($data[0]['client_id']))
            return false;

        if($this->checkIfAlreadyInUse($mobile_number)){
            return false;
        }

        return $this->saveAssociationDB([
            'email'         => $this->email,
            'client_id'     => $data[0]['client_id'],
            'secondary_id'  => $mobile_number
        ]);
    }

    public function getCampaigns(){
        $campaigns = $this->apiClient->getCampaigns();
        $available = [];
        if(!is_array($campaigns))
            return [];
        foreach ($campaigns as $campaign){
            if(empty($campaign['end']))
                continue;
            if(new DateTime($campaign['end']) > new DateTime()){
                $available[] = $campaign;
            }
        }
        return $available;
    }

    /**
     * @param int $limit
     * @param int $page
     * @return array|bool|string
     */
    public function getLastClientMovement($limit = self::LIMIT_PAGE, $page = 0){

        if(false == $client_id = $this->getInternalClientId())
            return [];

        $resp = $this->apiClient->getAccountMovements($client_id, $limit, $this->convertMovementPageToLimitOffset($page));

        return is_array($resp)?$resp:[];

    }

    /**
     * @param $order_id
     * @param string $type
     * @return bool|string
     */
    public function makeAccountMovements($order_id, $type = 'BUY', $credit_out = 0, $client_id = 0){
        $cli = !empty($client_id);
        if(empty($client_id) && false == $client_id = $this->getInternalClientId())
            return false;

        $order = wc_get_order( $order_id );
        if(empty($order))
            return false;

        global $wpdb;

        $items =  $this->convertWCProductsToQero($order->get_items());

        return $this->apiClient->makeAccountMovements($client_id, [
            "secondary_id"      => $wpdb->prefix.md5($order_id.$client_id),
            "type"              => $type,
            "external_date"     => $order->get_date_created()->getOffsetTimestamp(),
            "amount_gross"      => ((float) wc_format_decimal($order->get_total(), 2)) * 100,
            "amount_net"        => ((float) wc_format_decimal($order->get_total(), 2) - wc_format_decimal($order->get_total_tax(), 2)) *100,
            "hash"              => $cli?$this->getAuthHashMovementFromClientId($client_id):$this->getAuthHashMovement(),
            "amount_promotional"=> 0,
            "products"          => $items,
            "credit_out"        => $credit_out,
            "identity_name"     => get_option('qero_app_name')
        ]);
    }

    public function getMovementsPages(){
        if(false == $client_id = $this->getInternalClientId())
            return false;

        $count = $this->apiClient->getMovementsCount($client_id);

        if(empty($count))
            return 0;

        return ceil($count/self::LIMIT_PAGE);
    }

    /**
     * @return array|bool|string
     */
    public function getHoldAccountMovementOnCart($amount_gross, $amount_net, $items, $client_id = ''){
        $cli = !empty($client_id);
        if(empty($client_id) && false == $client_id = $this->getInternalClientId())
            return false;

        global $wpdb;

        $move = $this->apiClient->applyCampaigns($client_id, [
            "secondary_id"      => $wpdb->prefix.md5(time()),
            "type"              => "HOLD",
            "external_date"     => time(),
            "amount_gross"      => $amount_gross,
            "amount_net"        => $amount_net,
            "hash"              => $cli?$this->getAuthHashMovementFromClientId($client_id):$this->getAuthHashMovement(),
            "products"          => $items,
            "amount_promotional"=> 0,
            "credit_out"        => 0,
            "identity_name"     => get_option('qero_app_name')
        ]);

        return $move;
    }

    public function getHoldAccountMovementOnOrder($order, $points = 0, $client_id=''){
        $cli = !empty($client_id);
        if(empty($client_id) && false == $client_id = $this->getInternalClientId())
            return false;

        $items =  $this->convertWCProductsToQero($order->get_items());
        global $wpdb;

        $move = $this->apiClient->makeAccountMovementHold($client_id, [
            "secondary_id"      => $wpdb->prefix.md5($order->get_id().$client_id),
            "type"              => "HOLD",
            "external_date"     => time(),
            "amount_gross"      => ((float) WC()->cart->get_subtotal()) *100,
            "amount_net"        => ((float) WC()->cart->get_subtotal_tax() ) *100,
            "hash"              => $cli?$this->getAuthHashMovementFromClientId($client_id):$this->getAuthHashMovement(),
            "products"          => $items,
            "amount_promotional"=> 0,
            "credit_out"        => $points,
            "identity_name"     => get_option('qero_app_name') //getAppName
        ]);
        if($move !== false){//impossible to
            $this->deleteDiscount();
        }
        return $move;
    }

    public static function getCountriesAndIndicative(){
        $countries = QEROAPI::configNumbers();

        $allowed  = ['name', 'code', 'regex'];
        foreach ($countries as &$country){
            $country = array_filter(
                $country,
                function ($key) use ($allowed) {
                    return in_array($key, $allowed);
                },
                ARRAY_FILTER_USE_KEY
            );
        }

        return $countries;

    }

    public function getAuthHashMovement(){
        return $this->getBalance()['hash'];
    }

    public function getAuthHashMovementFromClientId($client_id){
        $res = $this->apiClient->getClientBalance(null,$client_id);
        return isset($res['hash'])?$res['hash']:0;
    }

    public function getLastApiError(){
        return $this->apiClient->getLastError();
    }


    /**
     * @return array|bool
     */
    public function getBalance(){
        $infos = $this->apiClient->getClientBalance($this->getMobileNumber());
        $onHold = $this->getDiscount();
        if(!empty(WC()->cart) && !empty(WC()->cart->get_subtotal())){
            $infos['max_points'] = round(((float) WC()->cart->get_subtotal() * (float) $infos['max_points_use_percentage']),2);
        }
        if(!empty($onHold)){
            if(empty($infos['max_points']) || $onHold > $infos['max_points']){
                $this->deleteDiscount();
                return $this->getBalance();
            }
            $infos['available'] -= (float) $onHold;
            $infos['points'] -= (float) $onHold;
            $infos['points_on_hold'] = (float) $onHold;
        }
        return $infos;
    }

    /**
     * @param $id
     * @return bool|array
     */
    public function getSubscriberByClientId($id){
        global $wpdb;

        $table   = $wpdb->prefix . Qero_For_Wp_Admin::TABLE_NAME;
        $user = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE client_id = %s;", $id ) );
        if(empty($user))
            return false;
        return $user->secondary_id;
    }

    public function storeAssociation($store_name){
        $data = ['store_type' => $store_name];
        return $this->apiClient->storeAssociation($data);
    }

    /**
     * Apply a discount to the cart user has
     * @param $points
     * @return bool
     */
    public function applyDiscount($points){
        $secondary_id = $this->getMobileNumber();

        if(empty($secondary_id))
            return false;

        global $wpdb;

        $table  = $wpdb->prefix . Qero_For_Wp_Admin::DISCOUNT_NAME;

        $wpdb->delete( $table, array( 'secondary_id' => $secondary_id ) );

        $data = [
            'secondary_id'     => $secondary_id,
            'qero_points'   => $points
        ];

        $data = wp_parse_args( $data );

        $result = $wpdb->insert(
            $table,
            $data,
            array( '%s', '%s' )
        );

        return false !== $result;
    }

    /**
     * @return bool
     */
    public function deleteDiscount(){
        $secondary_id = $this->getMobileNumber();

        if(empty($secondary_id))
            return false;

        global $wpdb;

        $table  = $wpdb->prefix . Qero_For_Wp_Admin::DISCOUNT_NAME;

        $result = $wpdb->delete( $table, array( 'secondary_id' => $secondary_id ) );

        return false !== $result;
    }

    /**
     * GetDiscount to apply
     * @return bool
     */
    public function getDiscount(){

        $secondary_id = $this->getMobileNumber();

        if(empty($secondary_id))
            return false;

        global $wpdb;

        $table   = $wpdb->prefix . Qero_For_Wp_Admin::DISCOUNT_NAME;
        $discount = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE secondary_id = %s;", $secondary_id ) );
        if(empty($discount))
            return 0;

        return $discount->qero_points;
    }

    public function getMaxPoints($percent){
        return round((float) WC()->cart->get_subtotal() * 0.01 * (float) $percent,2);
    }

    public static function productViewConverter(){
        global $product;
        if($product instanceof WC_Product_Variable){
            //var_dump($product->get_children());//TODO:work out a logic for view variable products
        }
        if($product instanceof WC_Product_Simple) {
            return self::convertWCProductsToQero([$product]);
        }
    }

    public static function convertWCProductsToQero($items){

        $converted = [];
        foreach($items as $item => $values) {
            if($values instanceof WC_Order_Item_Product){
                $converted[] = self::convertWC_Order_Item_Product($values);
                continue;
            }

            if($values instanceof WC_Product_Simple || $values instanceof WC_Product_Variable || (! empty($values['data']) && $values['data'] instanceof WC_Product_Simple)){
                $quantity = 1;
                if(!$values instanceof WC_Product_Simple && !$values instanceof WC_Product_Variable && ! empty($values['data'])){
                    $quantity = $values['quantity'];
                    $values = $values['data'];
                }

                $tax_percent = WC_Tax::get_rates( $values->get_tax_class() )['rate'];
                $converted[] = [
                    'quantity'      => $quantity,
                    'id'            => $values->get_id(),
                    'separator'     => ';',
                    'amount_unit'   => [
                        'gross' => $values->get_price() * 100,
                        'net'   => wc_get_price_excluding_tax( $values ) * 100,
                    ],
                    'iva' => !empty($tax_percent)?$tax_percent:0,
                ];
                continue;
            }

        }
        return $converted;
    }

    public function dashboardClientCreation(){
        $data = $this->apiClient->dashboardClientCreation();

        if(empty($data) || !is_array($data)){
            return [];
        }

        $response = [
            'datasets'  => [
                [
                    'label'             => __('Number of new clients', 'qero-for-wp'),
                    'data'              => [],
                ]
            ],
            'labels'    => []
        ];
        foreach ($data as $day => $tot){
            $response['labels'][] = $day;
            $response['datasets'][0]['data'][] = $tot['count'];
        }
        return $response;
    }

    public function dashboardSales(){
        $data = $this->apiClient->dashboardSales();

        if(empty($data) || !is_array($data)){
            return [];
        }

        $response = [
            'datasets'  => [
                [
                    'label'             => __('Number of sells','qero-for-wp'),
                    'data'              => [],
                ],
                [
                    'label'             => __('Sales Value','qero-for-wp'),
                    'data'              => [],
                ]
            ],
            'labels'    => []
        ];
        foreach ($data as $day => $tot){
            $response['labels'][] = $day;
            $response['datasets'][0]['data'][] = $tot['count'];
            $response['datasets'][1]['data'][] = $tot['sum'];
        }
        return $response;
    }

    public function dashboardClientSales($page = 1){
        if($page < 1){
            $page = 1;
        }
        $rep = $this->apiClient->dashboardClientSales(self::LIMIT_PAGE,($page-1)*self::LIMIT_PAGE);
        if($rep === false || !is_array($rep))
            return $this->apiClient->getLastError();

        $response = [];
        foreach ($rep as $client){//remap keys for table
            $response[] = [
                'terciary_id'   => $client['terciary_id'],
                'name'          => $client['name'],
                'secondary_id'  => $client['secondary_id'],
                'sales'         => number_format($client['sales'],2)
            ];
        }

        return $response;
    }

    public function dashboardCampaigns($page = 1){
        if($page < 1){
            $page = 1;
        }
        $rep = $this->apiClient->dashboardCampaigns(self::LIMIT_PAGE,($page-1)*self::LIMIT_PAGE);
        if($rep === false || !is_array($rep))
            return $this->apiClient->getLastError();

        $response = [];
        foreach ($rep as $client){//remap keys for table
            $response[] = [
                'title' => $client['title'],
                'start' => $client['start'],
                'end'   => $client['end'],
                'count' => $client['count'],
                'value' => number_format($client['value'],2)
            ];
        }

        return $response;
    }

    public function dashboardInfo(){
        $rep = $this->apiClient->dashboardInfo();
        if(!isset($rep['count']) || !isset($rep['sum'])){
            return [
                'count' => 0,
                'sum'   => 0
            ];
        }else{
            return [
                'count' => $rep['count'],
                'sum'   => number_format($rep['sum'],2)
            ];
        }
    }

    public function getCampaingPages(){
        $rep = $this->apiClient->dashboardCampaignsCount();
        if(empty($rep['count'])){
            return 1;
        }else{
            return $rep['count'];
        }
    }

    public function getClientsPages(){
        $rep = $this->apiClient->dashboardSalesCount();
        if(empty($rep['count'])){
            return 1;
        }else{
            return $rep['count'];
        }
    }

    /**
     * Consult client id with mobile id
     * @param $mobile_id
     * @return string
     */
    public function getClientIdFromMobile($mobile_id){
        $info = $this->apiClient->getClients($mobile_id);
        return !empty($info[0]['client_id'])?$info[0]['client_id']:'';
    }

    public function getStores(){
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
        return $stores;
    }

    private static function convertWC_Order_Item_Product($values){
        $tax_percent = WC_Tax::get_rates( $values->get_tax_class() )['rate'];
        return [
            'quantity'      => $values->get_quantity(),
            'id'            => $values->get_product_id(),
            'separator'     => ';',
            'amount_unit'   => [
                'gross' => $values->get_subtotal()/$values->get_quantity() * 100,
                'net'   => ($values->get_subtotal()) /$values->get_quantity() / (1+($tax_percent*0.01)) * 100
            ],
            'iva'           => !empty($tax_percent)?$tax_percent:0
        ];
    }

    private function convertMovementPageToLimitOffset($page){
        if(false == $client_id = $this->getInternalClientId())
            return 0;

        return $page*self::LIMIT_PAGE;
    }

    /**
     * @param $mobile_number
     * @return bool
     */
    private function checkIfAlreadyInUse($mobile_number){
        global $wpdb;

        $table   = $wpdb->prefix . Qero_For_Wp_Admin::TABLE_NAME;
        $association = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE secondary_id = %s;", $mobile_number ) );
        if(empty($association))
            return false;
        return true;
    }

    /**
     * @return bool
     */
    private function getInternalClientId(){
        global $wpdb;

        $table   = $wpdb->prefix . Qero_For_Wp_Admin::TABLE_NAME;
        $association = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE email = %s;", $this->email ) );
        if(!empty($association))
            return $association->client_id;
        return false;
    }

    /**
     * @return bool
     */
    private function getMobileNumber(){
        global $wpdb;

        $table   = $wpdb->prefix . Qero_For_Wp_Admin::TABLE_NAME;
        $association = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE email = %s;", $this->email ) );
        if(!empty($association))
            return $association->secondary_id;
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    private function saveAssociationDB($data){
        if(empty($data['email']) || empty($data['client_id']) || empty($data['secondary_id']))
            return false;

        global $wpdb;
        $data['date'] = date("Y-m-d H:i:s");
        $data = wp_parse_args( $data );

        $result = $wpdb->insert(
            $wpdb->prefix . Qero_For_Wp_Admin::TABLE_NAME,
            $data,
            array( '%s', '%s', '%s' , '%s' )
        ); // WPCS: db call ok, cache ok.

        return false !== $result;
    }

    public static function getUsersToInvite($before_date){
        global $wpdb;

        $query_users = "SELECT ID,user_registered FROM `". $wpdb->prefix."users`
                            WHERE user_registered > %s";
        return $wpdb->get_results( $wpdb->prepare( $query_users, $before_date ) );
    }

    public static function shotInviteEmailToUsers($users, $time=0){
        if(!is_array($users))
            return;

        if(count($users) > 0)
            new WC_Emails();

        foreach ($users as $user) {
            if(strtotime($user->user_registered) + $time < time()){
                do_action('qero_send_email_user_trigger',$user->ID);
            }
        }

        update_option(QERO_LAST_CRON_EXEC, time());
    }

}