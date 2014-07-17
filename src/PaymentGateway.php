<?php namespace Developeryamhi\PaymentGateway;

abstract class PaymentGateway implements Interfaces\PaymentGatewayInterface {


    //  Transaction Environment
    protected $environment;

    //  Gateway Settings
    protected $settings = null;

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

        //  Create Settings Collection
        $this->settings = new Object\PropertyCollection();

        //  Create Items
        $this->settings->addItem('merchant', new Object\Property());
        $this->settings->addItem('properties', new Object\Property());
        $this->settings->addItem('post_fields', new Object\Property());

        //  Call Init
        $this->_init();

        //  Fire Event
        $this->_fireEvent('created');
    }

    //  Get Name
    public function getName() {

        //  Get Class
        $class = '\\' . get_class($this);

        //  Return
        return $class::$name;
    }

    //  Get Label
    public function getLabel() {

        //  Get Class
        $class = '\\' . get_class($this);

        //  Return
        return $class::$label;
    }

    //  Init Function
    public function _init() {
        //  Do Nothing Here
    }

    //  Settings Manipulator
    public function settings() {

        //  Return Settings Collection
        return $this->settings;
    }

    //  Fire External Event
    protected function _fireEvent($event) {

        //  Emit Gateway Object Created Event
        GatewayHelper::event_fire('gateway.' . $event, array($this));

        //  Check for Key and Emit Event
        GatewayHelper::event_fire('gateway.' . $event . '.' . $this->getName(), array($this));
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
        $this->settings()->properties_assign('currency', $cur);
    }

    //  Get Currency
    public function getCurrency() {
        return $this->settings()->properties_read('currency');
    }

    //  Set Transaction Description
    public function setTransactionDetail($details) {

        //  Store Details
        $this->settings()->properties_assign('trans_details', $details);
    }

    //  Get Transaction Description
    public function getTransactionDetail() {
        return $this->settings()->properties_read('trans_details');
    }

    //  Set Property
    public function setProperty($key, $value) {

        //  Store
        $this->settings()->properties_assign($key, $value);
    }

    //  Get Property
    public function getProperty($key, $def = null) {

        //  Read Property
        return $this->settings()->properties_read($key, $def);
    }

    //  Check Property Exists
    public function hasProperty($key) {
        return ($this->settings()->properties_exists($key));
    }

    //  Remove Property
    public function removeProperty($key) {

        //  Remove Property
        $this->settings()->properties_unassign($key);
    }

    //  Set Post Field
    public function setPostField($key, $value) {

        //  Store
        $this->settings()->post_fields_assign($key, $value);
    }

    //  Get Post Field
    public function getPostField($key, $def = null) {

        //  Read Post Field
        return $this->settings()->post_fields_read($key, $def);
    }

    //  Check Post Field Exists
    public function hasPostField($key) {
        return ($this->settings()->post_fields_exists($key));
    }

    //  Remove Post Field
    public function removePostField($key) {

        //  Remove Post Field
        $this->settings()->post_fields_unassign($key);
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
        return $this->settings()->getItem('merchant');
    }

    //  Get Merchant Info Value
    public function getMerchantInfoVal($key, $def = null) {

        //  Get Info
        return $this->settings()->merchant_read($key, $def);
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
        $this->settings()->merchant_copyArray($info);

        //  Fire External Event
        $this->_fireInternalEvent('onMerchantInfoChanged');
    }

    //  Set Merchange Info Value
    public function setMerchantInfoVal($key, $val) {

        //  Set
        $this->settings()->merchant_assign($key, $val);

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
    public function process(\Closure $closure = null) {

        //  Trigger Start Events
        $this->_fireEvent('transaction.start');
        $this->_fireInternalEvent('onTransactionStart');

        //  Process
        $this->_runTransaction($closure);
    }

    //  Run Transaction
    public function _runTransaction(\Closure $closure = null) {

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