<?php

interface PaymentGatewayEventsInterface {

    //  Merchange Info Changed Event
    public function onMerchantInfoChanged();

    //  Card Info Changed Event
    public function onCardInfoChanged();

    //  Environment Changed Event
    public function onEnvironmentChanged();

    //  Reset Everything Event
    public function onResetEverything();

    //  Transaction Started Event
    public function onTransactionStart();

    //  Transaction Processing Event
    public function onTransactionProcessing();

    //  Transaction Ended Event
    public function onTransactionEnd();
}