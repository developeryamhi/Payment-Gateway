<?php namespace Developeryamhi\PaymentGateway;

abstract class PaymentGateway implements PaymentGatewayInterface {

    //  Gateway Key, Name
    public $key;
    public $name;

    //  Transaction Environment
    protected $environment;

    //  Merchant Info
    protected $merchantInfo = array();

    //  Properties Holder
    protected $properties = array();

    //  Post Fields
    protected $post_fields = array();

    //  Transaction ID
    protected $transID = null;

    //  Transaction Status
    protected $tranSuccess = false;

    //  Transaction Message
    protected $transMessage = null;

    //  Transaction Error
    protected $transError = null;

    //  Response
    protected $response = null;


    //  Construct
    public function __construct() {

        //  Fire Event
        $this->_fireEvent('created');
    }

    //  Fire External Event
    protected function _fireEvent($event) {

        //  Emit Gateway Object Created Event
        event()->emit('gateway.' . $event, array($this));

        //  Check for Key and Emit Event
        if($this->key != '')
            event()->emit('gateway.' . $event . '.' . $this->key, array($this));
    }

    //  Fire Internal Event
    protected function _fireInternalEvent($event) {

        //  Check & Fire
        if($this instanceof PaymentGatewayEventsInterface || method_exists($this, $event))
            call_user_func_array(array($this, $event), array($this));
    }

    //  Set Currency
    public function setCurrency($cur) {

        //  Set Property
        $this->setProperty('currency', $cur);
    }

    //  Get Currency
    public function getCurrency() {
        return $this->getProperty('currency');
    }

    //  Set Transaction Description
    public function setTransactionDetail($details) {

        //  Store Details
        $this->setProperty('trans_details', $details);
    }

    //  Get Transaction Description
    public function getTransactionDetail() {
        return $this->getProperty('trans_details');
    }

    //  Set Property
    public function setProperty($key, $value) {

        //  Store
        $this->properties[$key] = $value;
    }

    //  Get Property
    public function getProperty($key, $def = null) {

        //  Check for Property
        if($this->hasProperty($key)) {

            //  Return
            return $this->properties[$key];
        }

        //  Return Default
        return $def;
    }

    //  Check Property Exists
    public function hasProperty($key) {
        return (isset($this->properties[$key]));
    }

    //  Remove Property
    public function removeProperty($key) {

        //  Check for Property
        if($this->hasProperty($key)) {

            //  Unset
            unset($this->properties[$key]);
        }
    }

    //  Set Post Field
    public function setPostField($key, $value) {

        //  Store
        $this->post_fields[$key] = $value;
    }

    //  Get Post Field
    public function getPostField($key, $def = null) {

        //  Check for Post Field
        if($this->hasPostField($key)) {

            //  Return
            return $this->post_fields[$key];
        }

        //  Return Default
        return $def;
    }

    //  Check Post Field Exists
    public function hasPostField($key) {
        return (isset($this->post_fields[$key]));
    }

    //  Remove Post Field
    public function removePostField($key) {

        //  Check for Post Field
        if($this->hasPostField($key)) {

            //  Unset
            unset($this->post_fields[$key]);
        }
    }

    //  Get Amount
    public function getAmount() {
        return $this->getProperty('amount');
    }

    //  Get Environment
    public function getEnvironment() {
        return $this->environment;
    }

    //  Get Merchant Info
    public function getMerchantInfo() {
        return $this->merchantInfo;
    }

    //  Get Merchant Info Value
    public function getMerchantInfoVal($key, $def = null) {

        //  Check
        if(isset($this->merchantInfo[$key])) {

            //  Return
            return $this->merchantInfo[$key];
        }

        //  Return Def
        return $def;
    }

    //  Get Card Info
    public function getCardInfo() {

        //  Card Year
        $cardYear = $this->getProperty('cc_exp_year');

        //  Card Year Minimal
        $yearMin = (strlen($cardYear) == 2 ? $cardYear : substr($cardYear, -2));
        $yearFull = (strlen($cardYear) == 4 ? $cardYear : substr(date('Y'), 0, 2) . $cardYear);

        //  Get Info
        return array(
            'number' => $this->getProperty('cc_number'),
            'exp_month' => $this->getProperty('cc_exp_month'),
            'exp_year' => $yearMin,
            'exp_year_full' => $yearFull,
            'cvc' => $this->getProperty('cc_cvc'),
        );
    }

    //  Se Amount
    public function setAmount($amount) {

        //  Set Property
        $this->setProperty('amount', $amount);
    }

    //  Set Card Info
    public function setCardInfo($number, $exp_month, $exp_year, $cvc = null) {

        //  Set Properties
        $this->setProperty('cc_number', $number);
        $this->setProperty('cc_exp_month', $exp_month);
        $this->setProperty('cc_exp_year', $exp_year);
        if($cvc)    $this->setProperty('cc_cvc', $cvc);

        //  Fire External Event
        $this->_fireInternalEvent('onCardInfoChanged');
    }

    //  Set Environment
    public function setEnvironment($env) {

        //  Set Property
        $this->environment = $env;

        //  Fire External Event
        $this->_fireInternalEvent('onEnvironmentChanged');
    }

    //  Set Merchant Info
    public function setMerchantInfo($info) {

        //  Store
        $this->merchantInfo = $info;

        //  Fire External Event
        $this->_fireInternalEvent('onMerchantInfoChanged');
    }

    //  Set Merchange Info Value
    public function setMerchantInfoVal($key, $val) {

        //  Set
        $this->merchantInfo[$key] = $val;

        //  Fire External Event
        $this->_fireInternalEvent('onMerchantInfoChanged');
    }

    //  Check for Live Mode
    public function isLive() {
        return (!$this->isSandbox());
    }

    //  Check for Sandbox Mode
    public function isSandbox() {
        return ($this->environment == 'sandbox' || $this->environment == 'test');
    }

    //  Process
    public function process(Closure $closure = null) {

        //  Trigger Start Events
        $this->_fireEvent('transaction.start');
        $this->_fireInternalEvent('onTransactionStart');

        //  Process
        $this->_runTransaction($closure);
    }

    //  Run Transaction
    public function _runTransaction(Closure $closure = null) {

        //  Trigger End Events
        $this->_fireEvent('transaction.end');
        $this->_fireInternalEvent('onTransactionEnd');

        //  Check for Callback Closure
        if($closure) {

            //  Do Callback
            call_user_func_array($closure, array($this->tranSuccess, $this->response, $this));
        }
    }

    //  Get Response
    public function getResponse() {
        return $this->response;
    }

    //  Get Transaction ID
    public function transactionID() {
        return $this->transID;
    }

    //  Check for Successful Transaction
    public function transWasSuccessful() {
        return $this->tranSuccess;
    }

    //  Check for Transaction Error
    public function transHasError() {
        return (!$this->transWasSuccessful());
    }

    //  Get Transaction Message
    public function getTransactionMessage() {
        return $this->transMessage;
    }

    //  Get Transaction Error
    public function getTransactionError() {
        return $this->transError;
    }

    //  Get Response Property
    public function getResponseProperty($key) {
        return null;
    }

    //  Reset Everything
    public function resetEverything() {

        //  Fire Internal Event
        $this->_fireInternalEvent('onResetEverything');
    }
}