<?php namespace Developeryamhi\PaymentGateway;

class GatewayHelper {

    //  Init The Helper
    public static function init() {

        //  Load the Base Helper Functions
        require_once dirname(dirname(__FILE__)) . '/helper.php';

        //  Create Global Variable for Event Emitter
        global $event_emitter;

        //  Create Event Emitter Instance
        if(!$event_emitter)
            $event_emitter = new \Evenement\EventEmitter();

        //  Add the Default Gateways
        pds_gateway_support(Gateway\PaypalGateway::$name, 'PaypalGateway');
        pds_gateway_support(Gateway\BrainTreeGateway::$name, 'BrainTreeGateway');
    }

    //  Get the Event Object
    public static function event() {

        //  Get Global
        global $event_emitter;

        //  Return
        return $event_emitter;
    }

    //  Bind the Event
    public static function event_on($event, $listener) {

        //  Bind Event
        self::event()->on($event, $listener);
    }

    //  Unbind the Event
    public static function event_off($event, $listener) {

        //  Unbind Event
        self::event()->removeListener($event, $listener);
    }

    //  Trigger the Event
    public static function event_fire($event, $arguments = array()) {

        //  Trigger Event
        self::event()->emit($event, $arguments);
    }

    //  Create the Instance
    public static function locateGateway($gateway) {

        //  Create the Gateway Class Name
        $gatewayClass = pds_gateway_get($gateway);

        //  Check Class Exists
        if (!class_exists($gatewayClass))
            throw new \Exception("{$gatewayClass}: Payment Gateway '{$gateway}' does not exist.");

        //  Create the Gateway Class Instance
        $instance = new $gatewayClass();

        //  Check for Proper Class Defination
        if (!$instance instanceof PaymentGateway)
            throw new \Exception("Payment Gateway '{$gateway}' must extend class 'PaymentGateway'.");

        //  Return the Instance
        return $instance;
    }

}
