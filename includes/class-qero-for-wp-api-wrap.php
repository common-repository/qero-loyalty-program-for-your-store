<?php
/**
 * Created by PhpStorm.
 * User: tmota
 * Date: 06/08/2019
 * Time: 13:09
 */

class QEROAPI {
    const MESSAGES = [
        'invalid_data'          => 'Invalid input data!',
    ];
    const ENDPOINT = 'http://dev-qero-ecommerce.e-goi.com/v1';
    const _EGOI    = 'https://api.egoiapp.com';
    const PKEY     = 'f0312392ad15165818cf54033af25bfd';

    const APIURLS  = [
        'getClientBalance'          => '/client/balance',
        'companyAssociation'        => '/company/apikey',
        'getClients'                => '/client',
        'registerNewClient'         => '/client',
        'getAccountMovements'       => '/client/{client_id}/movement',
        'makeAccountMovements'      => '/client/{client_id}/movement',
        'getMovementsCount'         => '/client/{client_id}/movement/count',
        'applyCampaigns'            => '/clients/movements/apply/campaigns/{client_id}',
        'getStores'                 => '/store',
        'createStores'              => '/store',
        'cancelHoldMovement'        => '/clients/movements/hold/cancel/{client_id}',
        'makeAccountMovementHold'   => '/clients/movements/hold/{client_id}',
        'getCampaigns'              => '/campaign',
        'configNumbers'             => '/config/internationalnumbers',
        'dashboardSales'            => '/dashboard/sales',
        'dashboardClientCreation'   => '/dashboard/client/creation',
        'dashboardClientSales'      => '/dashboard/client/sales',
        'dashboardCampaigns'        => '/dashboard/campaign/sales',
        'dashboardSalesCount'       => '/dashboard/client/sales/count',
        'dashboardCampaignsCount'   => '/dashboard/campaign/sales/count',
        'dashboardInfo'             => '/dashboard',
        'ping'                      => '/ping',
        'storeAssociation'          => '/store/associate'

    ];
    protected $apiKey;
    protected $headers;
    protected $lastError;

    /**
     * QEROAPI constructor.
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey       = $apiKey;
        $this->headers      = ['APIKEY: '.$this->apiKey,'Content-Type: application/json'];
        $this->lastError    = false;
    }

    public function cancelHoldMovement($client_id, $data){
        $path = self::ENDPOINT.$this->replaceUrl(self::APIURLS[__FUNCTION__],'{client_id}', $client_id);

        $client = new QeroClientHttp(
            $path,
            'POST',
            $this->headers,
            $data
        );

        $resp = $this->errorParser($client);
        QeroClientLogger::log(['function'=> __FUNCTION__,'request' => $data, 'response' => $resp, 'code'=>$client->getCode()], $client_id);

        switch ($client->getCode()){
            case 200:
            default:
                return $resp;
                break;
        }
    }

    public function storeAssociation($data){
        $path = self::ENDPOINT.self::APIURLS[__FUNCTION__];
        $client = new QeroClientHttp(
            $path,
            'POST',
            $this->headers,
            $data
        );

        $resp = $this->errorParser($client);

        switch ($client->getCode()){
            case 200:
            default:
                return $resp;
                break;
        }
    }

    /**
     * Ping
     * @return bool
     */
    public function ping(){
        $path = self::_EGOI.self::APIURLS[__FUNCTION__];

        $client = new QeroClientHttp(
            $path,
            'POST',
            ['ApiKey: '.$this->apiKey,'PluginKey: '.self::PKEY,'Content-Type: application/json'],
            []
        );

        return true;
    }

    /**
     * get Dashboard Info
     * @return bool
     */
    public function dashboardInfo(){
        $path = self::ENDPOINT.self::APIURLS[__FUNCTION__];

        $client = new QeroClientHttp(
            $path,
            'GET',
            $this->headers
        );
        $resp = $this->errorParser($client);

        switch ($client->getCode()){
            case 200:
            default:
                return $resp;
                break;
        }
    }

    /**
     * get Campaigns count
     * @return bool
     */
    public function dashboardCampaignsCount(){
        $path = self::ENDPOINT.self::APIURLS[__FUNCTION__];

        $client = new QeroClientHttp(
            $path,
            'GET',
            $this->headers
        );

        $resp = $this->errorParser($client);

        switch ($client->getCode()){
            case 200:
            default:
                return $resp;
                break;
        }
    }

    /**
     * get Sales count
     * @return bool
     */
    public function dashboardSalesCount(){
        $path = self::ENDPOINT.self::APIURLS[__FUNCTION__];

        $client = new QeroClientHttp(
            $path,
            'GET',
            $this->headers
        );

        $resp = $this->errorParser($client);

        switch ($client->getCode()){
            case 200:
            default:
                return $resp;
                break;
        }
    }

    /**
     * get dashboard campaigns
     * @param int $limit
     * @param int $offset
     * @return bool
     */
    public function dashboardCampaigns($limit = 5, $offset = 0){
        $path = self::ENDPOINT.self::APIURLS[__FUNCTION__]
            .'?'
            .http_build_query([
                'limit'     => $limit,
                'offset'    => $offset
            ]);

        $client = new QeroClientHttp(
            $path,
            'GET',
            $this->headers
        );

        $resp = $this->errorParser($client);

        switch ($client->getCode()){
            case 200:
                return $resp;
                break;
            default:
                return [];
                break;
        }
    }

    public function dashboardClientSales($limit = 5, $offset = 0){
        $path = self::ENDPOINT.self::APIURLS[__FUNCTION__]
            .'?'
            .http_build_query([
                'limit'     => $limit,
                'offset'    => $offset
            ]);

        $client = new QeroClientHttp(
            $path,
            'GET',
            $this->headers
        );


        $resp = $this->errorParser($client);

        switch ($client->getCode()){
            case 200:
                return $resp;
                break;
            default:
                return [];
                break;
        }
    }

    public function dashboardClientCreation(){
        $path = self::ENDPOINT.self::APIURLS[__FUNCTION__];

        $client = new QeroClientHttp(
            $path,
            'GET',
            $this->headers
        );

        $resp = $this->errorParser($client);

        switch ($client->getCode()){
            case 200:
            default:
                return $resp;
                break;
        }
    }

    public function dashboardSales(){
        $path = self::ENDPOINT.self::APIURLS[__FUNCTION__];

        $client = new QeroClientHttp(
            $path,
            'GET',
            $this->headers
        );

        $resp = $this->errorParser($client);

        switch ($client->getCode()){
            case 200:
            default:
                return $resp;
                break;
        }
    }

    /**
     * @param $client_id
     * @return bool|string
     */
    public function makeAccountMovements($client_id, $data){

        if($this->makeAccountMovementsValidator($data) == false){
            $this->lastError = self::MESSAGES['invalid_data'];
            return false;
        }

        $this->cancelHoldMovement($client_id, $data);

        $path = self::ENDPOINT.$this->replaceUrl(self::APIURLS[__FUNCTION__],'{client_id}', $client_id);

        $client = new QeroClientHttp(
            $path,
            'POST',
            $this->headers,
            $data
        );

        $resp = $this->errorParser($client);
        QeroClientLogger::log(['function'=> __FUNCTION__,'request' => $data, 'response' => $resp, 'code'=>$client->getCode()], $client_id);
        $this->ping();

        switch ($client->getCode()){
            case 405:
                return $resp;
            case 200:
            default:
                return true;
                break;
        }
    }

    /**
     * @param $client_id
     * @return bool|string
     */
    public function makeAccountMovementHold($client_id, $data){

        if($this->makeAccountMovementsValidator($data) == false){
            $this->lastError = self::MESSAGES['invalid_data'];
            return false;
        }

        $this->cancelHoldMovement($client_id, $data);

        $path = self::ENDPOINT.$this->replaceUrl(self::APIURLS[__FUNCTION__],'{client_id}', $client_id);

        $client = new QeroClientHttp(
            $path,
            'POST',
            $this->headers,
            $data
        );

        $resp = $this->errorParser($client);
        QeroClientLogger::log(['function'=> __FUNCTION__,'request' => $data, 'response' => $resp, 'code'=>$client->getCode()], $client_id);

        switch ($client->getCode()){
            case 200:
                return $resp;
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * @param $client_id
     * @param $data
     * @return bool
     */
    public function applyCampaigns($client_id, $data){
        if($this->makeAccountMovementsValidator($data) == false){
            $this->lastError = self::MESSAGES['invalid_data'];
            return false;
        }

        $path = self::ENDPOINT.$this->replaceUrl(self::APIURLS[__FUNCTION__],'{client_id}', $client_id);

        $client = new QeroClientHttp(
            $path,
            'POST',
            $this->headers,
            $data
        );

        $resp = $this->errorParser($client);
        switch ($client->getCode()){
            case 200:
                return $resp;
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * @return bool|array
     */
    function getCampaigns() {
        $path = self::ENDPOINT.self::APIURLS[__FUNCTION__];

        $client = new QeroClientHttp(
            $path,
            'GET',
            $this->headers
        );

        $resp = $this->errorParser($client);

        switch ($client->getCode()){
            case 200:
            default:
                return $resp;
                break;
        }
    }

    /**
     * @param $client_id
     * @param int $limit
     * @param null $offset
     * @return bool|string
     */
    public function getAccountMovements($client_id, $limit = 10, $offset= null){
        $path = self::ENDPOINT.$this->replaceUrl(self::APIURLS[__FUNCTION__],'{client_id}', $client_id);

        $path .= '?' . http_build_query([
            'limit'     => $limit,
            'offset'    => $offset
        ]);

        $client = new QeroClientHttp(
            $path,
            'GET',
            $this->headers
        );

        $resp = $this->errorParser($client);

        switch ($client->getCode()){
            case 200:
            default:
                return $resp;
                break;
        }
    }

    public function getMovementsCount($client_id){
        $path = self::ENDPOINT.$this->replaceUrl(self::APIURLS[__FUNCTION__],'{client_id}', $client_id);

        $client = new QeroClientHttp(
            $path,
            'GET',
            $this->headers
        );

        $resp = $this->errorParser($client);

        switch ($client->getCode()){
            case 200:
            default:
                return !empty($resp['count'])?$resp['count']:false;
                break;
        }

    }

    /**
     * @param null $mobile
     * @return bool|string
     */
    public function getClients($mobile = null){
        $path = self::ENDPOINT.self::APIURLS[__FUNCTION__];
        if(!empty($mobile))
            $path.="?mobile=$mobile";

        $client = new QeroClientHttp(
            $path,
            'GET',
            $this->headers
        );

        $resp = $this->errorParser($client);

        switch ($client->getCode()){
            case 200:
            default:
                return $resp;
                break;
        }
    }

    /**
     * @param $data
     * @return bool|string
     */
    public function registerNewClient($data){

        if(
            empty($data['secondary_id'])
        ){
            $this->lastError = self::MESSAGES['invalid_data'];
            return false;
        }

        $path = self::ENDPOINT.self::APIURLS[__FUNCTION__];

        $client = new QeroClientHttp(
            $path,
            'POST',
            $this->headers,
            $data
        );

        $resp = $this->errorParser($client);
        $this->ping();
        switch ($client->getCode()){
            case 200:
                return true;
            default:
                return false;
                break;
        }
    }

    /**
     * Makes the association between E-goi && Qero
     * @return bool|array
     */
    public function companyAssociation(){
        $path = self::ENDPOINT.self::APIURLS[__FUNCTION__];

        $payload = [
            'apikey'    => $this->apiKey
        ];

        $client = new QeroClientHttp(
            $path,
            'POST',
            $this->headers,
            $payload
        );

        $resp = $this->errorParser($client);

        $this->ping();

        switch ($client->getCode()){
            case 200:
            default:
                return $resp;
                break;
        }
    }

    /**
     * Get the balance and hash of the Subscriber
     * @param $mobile_number
     * @param null $client_id
     * @return bool|array
     */
    public function getClientBalance($mobile_number=null, $client_id=null){
        $path = self::ENDPOINT.self::APIURLS[__FUNCTION__];
        if(!empty($mobile_number)){
            $path .= '?'.http_build_query(['mobile' => $mobile_number]);
        }else if(!empty($client_id)){
            $path .= '?'.http_build_query(['client_id' => $client_id]);
        }


        $client = new QeroClientHttp(
            $path,
            'GET',
            $this->headers
        );

        $resp = $this->errorParser($client);

        switch ($client->getCode()){
            case 200:
            default:
                return $resp;
                break;
        }

    }

    /**
     * @return bool|array
     */
    public function getStores(){
        $path = self::ENDPOINT.self::APIURLS[__FUNCTION__];

        $client = new QeroClientHttp(
            $path,
            'GET',
            $this->headers
        );
        $resp = $this->errorParser($client);
        switch ($client->getCode()){
            case 409:
                return $resp['detail']['detail'];
            case 200:
            default:
                return $resp;
                break;
        }
    }

    /**
     * @param $payload
     * @return bool
     */
    public function createStores($payload){
        $path = self::ENDPOINT.self::APIURLS[__FUNCTION__];

        $client = new QeroClientHttp(
            $path,
            'POST',
            $this->headers,
            $payload
        );

        $resp = $this->errorParser($client);

        switch ($client->getCode()){
            case 200:
            default:
                return $resp;
                break;
        }
    }

    /**
     * @return array
     */
    public static function configNumbers(){
        $path = self::ENDPOINT.self::APIURLS[__FUNCTION__];

        $client = new QeroClientHttp(
            $path,
            'GET'
        );

        $resp = json_decode($client->getResponse(),true);

        switch ($client->getCode()){
            case 200:
            default:
                return empty($resp)?[]:$resp;
                break;
        }
    }

    /**
     * @return bool|string
     */
    public function getLastError(){
        return $this->lastError;
    }

    private function makeAccountMovementsValidator($data){
        if(
            !isset($data['secondary_id']) // internal_order_id
            || ( !isset($data['type']) || !in_array($data['type'], ['BUY','RETURN', 'HOLD']) )// BUY || RETURN
            || !isset($data['external_date'])
            || !isset($data['amount_gross'])
            || !isset($data['amount_net'])
            || !isset($data['credit_out'])
            || !isset($data['identity_name'])
            || !is_array($data['products'])
        ){
            return false;
        }

        if(empty($data['products']))
            return true;

        foreach ($data['products'] as $product){
            if( !isset($product['quantity']) || !isset($product['id']) || !isset($product['separator']) || !isset($product['amount_unit']) || !isset($product['iva']))
                return false;
        }

        return true;
    }

    /**
     * @param $client QeroClientHttp
     * @return bool
     */
    private function errorParser($client){
        if($client->success() !== true){
            $this->lastError = $client->getError();
            return false;
        }

        $resp = json_decode($client->getResponse(),true);
        if(json_last_error() !== JSON_ERROR_NONE){
            $this->lastError = $this->jsonErrorParser();
            return false;
        }

        return empty($resp)?false:$resp;
    }


    /**
     * @param $url
     * @param $search
     * @param $replace
     * @return null|string|string[]
     */
    protected function replaceUrl($url, $search, $replace){
        if(is_array($replace)){
            foreach ($replace as $key => $value){
                $url = $this->privReplaceUrl($url, $search[$key], $replace[$key]);
            }
            return $url;
        }else{
            return $this->privReplaceUrl($url, $search, $replace);
        }
    }

    /**
     * @param $url
     * @param $search
     * @param $replace
     * @return null|string|string[]
     */
    private function privReplaceUrl($url, $search, $replace){
        return preg_replace("/$search/", "$replace", $url );
    }

    /**
     * Returns a string by the name of the json decode error
     * @return string
     */
    private function jsonErrorParser(){
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return ' - No errors';
                break;
            case JSON_ERROR_DEPTH:
                return ' - Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                return ' - Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                return ' - Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                return ' - Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                return ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                return ' - Unknown error';
                break;
        }
    }
}

if(!class_exists('QeroClientHttp')) {
    class QeroClientHttp
    {

        protected $headers;
        protected $response;
        protected $err;
        protected $http_code;


        static function parseHeaders($headers){
            if(!is_array($headers))
                return [];

            $mapped = [];
            foreach ($headers as $header){
                $he = explode(':',$header,2);
                $mapped[$he[0]] = $he[1];
            }
            return $mapped;
        }

        /**
         * QeroClientHttp constructor.
         * @param $url
         * @param string $method
         * @param array $headers
         * @param string $body
         */
        public function __construct($url, $method = 'GET', $headers = ['Accept: application/json'], $body = '')
        {

            switch ($method){
                case 'GET':
                    $response = wp_remote_get( $url, ['headers' => self::parseHeaders($headers)]);
                    break;
                case 'POST':
                    $response = wp_remote_post( $url, [
                        'body'          => json_encode($body),
                        'timeout'       => '10',
                        'redirection'   => '10',
                        'httpversion'   => '1.1',
                        'blocking'      => true,
                        'headers'       => self::parseHeaders($headers),
                        'cookies'       => array()
                    ]);
                    break;
                default:
                    $response = wp_remote_request( $url, ['method' => $method, 'body' => json_encode($body), 'headers' => $headers] );
                    break;
            }
            if($response instanceof WP_Error){
                $this->err = $response->get_error_message();
                return;
            }
            $this->http_code = (int) filter_var($response['response']['code'], FILTER_SANITIZE_NUMBER_INT);

            $this->headers = $response['headers'];
            $this->response = $response['body'];

        }

        public function success()
        {
            if (empty($this->err))
                return true;
            return $this->err;
        }

        public function getError()
        {
            return $this->err;
        }

        public function getCode()
        {
            return $this->http_code;
        }

        public function getResponse()
        {
            return $this->response;
        }

        public function getHeaders()
        {
            return $this->headers;
        }

    }
}

if(!class_exists('QeroClientLogger')) {
    class QeroClientLogger {
        public static function log($obj, $secondary_id = 0){

            global $wpdb;
            $data = [
                'client_id'     => $secondary_id,
                'log'           => json_encode($obj),
                'date'          => date("Y-m-d H:i:s"),

            ];
            $data = wp_parse_args( $data );

            $wpdb->insert(
                $wpdb->prefix . Qero_For_Wp_Admin::LOGS,
                $data,
                array( '%s', '%s' , '%s' )
            ); // WPCS: db call ok, cache ok.
        }
    }
}