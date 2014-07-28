<?php namespace Developeryamhi\PaymentGateway\Object;

class ValidationResponse extends PropertyCollection {

    //  Constructor
    public function __construct() {

        //  Add Property Items
        $this->addItem('messages', new Property());
        $this->addItem('errors', new Property());
    }

    //  Run Validation
    public function validate(\Developeryamhi\PaymentGateway\Interfaces\PaymentGatewayInterface $gateway) {

        dump_exit($gateway->getCardInfo());
    }
}