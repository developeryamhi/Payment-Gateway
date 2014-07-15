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
    public static function locateGateway($gateway, $append = 'Gateway', $prepend = '') {

        //  Create the Gateway Class Name
        $gatewayClass = $prepend . $gateway . $append;

        //  Check Class Exists
        if (!class_exists($gatewayClass))
            throw new Exception("Payment Gateway '{$gateway}' does not exist.");

        //  Create the Gateway Class Instance
        $instance = new $gatewayClass();

        //  Check for Proper Class Defination
        if (!$instance instanceof PaymentGateway)
            throw new Exception("Payment Gateway '{$gateway}' must extend class 'PaymentGateway'.");

        //  Return the Instance
        return $instance;
    }

}
