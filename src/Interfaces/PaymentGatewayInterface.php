<?php namespace Developeryamhi\PaymentGateway\Interfaces;

interface PaymentGatewayInterface {

    //  Init Function
    public function _init();

    //  Get Settings Manipulator
    public function settings();

    //  Set Gateway Merchant Informations
    public function setMerchantInfo($info);

    //  Get Gateway Merchant Informations
    public function getMerchantInfo();

    //  Set Merchange Info Value
    public function setMerchantInfoVal($key, $val);

    //  Get Merchant Info Value
    public function getMerchantInfoVal($key, $def = null);

    //  Set Gateway Environment
    public function setEnvironment($env);

    //  Get Gateway Environment
    public function getEnvironment();

    //  Check for Sandbox
    public function isSandbox();

    //  Check for Live
    public function isLive();

    //  Set Currency
    public function setCurrency($cur);

    //  Get Currency
    public function getCurrency();

    //  Set Property
    public function setProperty($key, $value);

    //  Get Property
    public function getProperty($key, $def = null);

    //  Check Has Property
    public function hasProperty($key);

    //  Remove Property
    public function removeProperty($key);

    //  Set Post Field
    public function setPostField($key, $value);

    //  Get Post Field
    public function getPostField($key, $def = null);

    //  Check Has Post Field
    public function hasPostField($key);

    //  Remove Post Field
    public function removePostField($key);

    //  Set Amount
    public function setAmount($amount);

    //  Get Amount
    public function getAmount();

    //  Set Transaction Description
    public function setTransactionDetail($details);

    //  Get Transaction Description
    public function getTransactionDetail();

    //  Set Card Info
    public function setCardInfo($number, $exp_month, $exp_year, $cvc = null);

    //  Get Card Info
    public function getCardInfo();

    //  Process Transaction
    public function process(\Closure $closure = null);

    //  Make Transaction
    public function _runTransaction(\Closure $closure = null);

    //  Get Transaction Response
    public function getResponse();

    //  Get Response Property
    public function getResponseProperty($key);

    //  Get Transaction ID
    public function transactionID();

    //  Check for Successful Transaction
    public function transWasSuccessful();

    //  Check for Transaction Error
    public function transHasError();

    //  Get Transaction Message
    public function getTransactionMessage();

    //  Get Transaction Error
    public function getTransactionError();

    //  Reset Everything
    public function resetEverything();
}
