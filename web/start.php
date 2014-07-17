<?php

//  Listen Exception Shutdown
set_exception_handler(function(Exception $ex) {

    //  Check Current Output Level
    if(ob_get_level() > 0) {

        //  Loop Level
        for($i=0; $i<ob_get_length(); $i++)
            @ob_clean();
    }

    //  Print Error
    echo '<p style="color:red;"><strong>#' . $ex->getCode() . '</strong> [' . $ex->getMessage() . '] on line ' . $ex->getLine() . ' on file ' . $ex->getFile() . '</p>';
});

//  Listen Paypal Gateway Create
Developeryamhi\PaymentGateway\GatewayHelper::event_on('gateway.created.paypal', function (\Developeryamhi\PaymentGateway\Gateway\PaypalGateway $paypal) {

    //  Set Transaction Environment
    $paypal->setEnvironment(PP_TRANSACTION_MODE);

    //  Set Merchant Info
    $paypal->setMerchantInfo(array(
        'client_id' => PP_CLIENT_ID,
        'client_secret' => PP_CLIENT_SECRET
    ));
});

//  Listen BrainTree Gateway Create
Developeryamhi\PaymentGateway\GatewayHelper::event_on('gateway.created.braintree', function (\Developeryamhi\PaymentGateway\Gateway\BrainTreeGateway $brainTree) {

    //  Set Transaction Environment
    $brainTree->setEnvironment(BT_TRANSACTION_MODE);

    //  Set Merchant Info
    $brainTree->setMerchantInfo(array(
        'merchant_id' => BT_MERCHANT_ID,
        'public_key' => BT_PUBLIC_KEY,
        'private_key' => BT_PRIVATE_KEY
    ));
});

//  Listen Transaction Start
Developeryamhi\PaymentGateway\GatewayHelper::event_on('gateway.transaction.start', function (\Developeryamhi\PaymentGateway\PaymentGateway $gateway) {

    //  Insert Transaction Row to Table
    getDBConn()->transaction()->insert_update(array(
        'amount' => $gateway->getAmount(),
        'description' => $gateway->getTransactionDetail(),
        'currency' => $gateway->getCurrency(),
        'gateway' => $gateway->getName(),
        'environment' => $gateway->getEnvironment()
    ), array());

    //  Get Insert ID
    $insert_id = getDBConn()->transactions()->insert_id();

    //  Set Property
    $gateway->setProperty('db_id', $insert_id);
});

//  Listen Transaction End
Developeryamhi\PaymentGateway\GatewayHelper::event_on('gateway.transaction.end', function (\Developeryamhi\PaymentGateway\PaymentGateway $gateway) {

    //  Row ID
    $rowid = $gateway->getProperty('db_id');

    //  Check
    if($rowid) {

        //  Find the Transaction Row
        $row = getDBConn()->transaction[$rowid];

        //  Check
        if($row) {

            //  Update
            getDBConn()->transaction()->insert_update(array(
                'id' => $rowid
            ), array(), array(
                'trans_id' => $gateway->transactionID(),
                'status' => ($gateway->transWasSuccessful() ? 'successful' : 'failed'),
                'response_txt' => ($gateway->transWasSuccessful() ? $gateway->getTransactionMessage() : $gateway->getTransactionError())
            ));
        }
    }
});
