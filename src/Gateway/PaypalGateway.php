<?php namespace Developeryamhi\PaymentGateway\Gateway;

use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\CreditCard;
use PayPal\Api\Amount;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Transaction;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

class PaypalGateway extends PaymentGateway {

    //  Set Gateway Key, Name
    public $key = 'paypal';
    public $name = 'Paypal';


    //  Process Transaction
    public function _runTransaction(Closure $closure = null) {

        //  Set No Time Limit
        set_time_limit(0);

        //  Create Context
        $apiContext = new ApiContext(new OAuthTokenCredential($this->getMerchantInfoVal('client_id'), $this->getMerchantInfoVal('client_secret')));

        //  Get Card Info
        $cardInfo = $this->getCardInfo();

        //  Create Card
        $card = new CreditCard();
        $card->setNumber($cardInfo['number']);
        $card->setType(getCardType($cardInfo['number']));
        $card->setExpire_month($cardInfo['exp_month']);
        $card->setExpire_year($cardInfo['exp_year_full']);
        if($cardInfo['cvc'])    $card->setCvv2($cardInfo['cvc']);

        //  Funding Instrument
        $fi = new FundingInstrument();
        $fi->setCredit_card($card);

        //  Create Payer
        $payer = new Payer();
        $payer->setPayment_method('credit_card');
        $payer->setFunding_instruments(array($fi));

        //  Amount
        $amount = new Amount();
        $amount->setCurrency($this->getCurrency());
        $amount->setTotal($this->getAmount());

        //  Create Transaction
        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setDescription($this->getTransactionDetail());

        //  Create Payment
        $payment = new Payment();
        $payment->setIntent('sale');
        $payment->setPayer($payer);
        $payment->setTransactions(array($transaction));

        //  Store Every Instances
        $this->setProperty('ins_apicontext', $apiContext);
        $this->setProperty('ins_card', $card);
        $this->setProperty('ins_fi', $fi);
        $this->setProperty('ins_payer', $payer);
        $this->setProperty('ins_amount', $amount);
        $this->setProperty('ins_transaction', $transaction);

        //  Trigger Processed Events
        $this->_fireEvent('transaction.processing');
        $this->_fireInternalEvent('onTransactionProcessing');

        //  Try/Catch
        try {

            //  Create Payment
            $payment->create($apiContext);

            //  Store Response
            $this->response = $payment;

            //  Set Success
            $this->tranSuccess = true;

            //  Set Transaction ID
            $this->transID = $payment->id;

            //  Set Message
            $this->transMessage = 'Transaction Completed';

        } catch(Exception $ex) {

            //  Store Response
            $this->response = $ex;

            //  Set Failed
            $this->tranSuccess = false;

            //  Error Message
            $errorMessage = $ex->getMessage();

            //  Check for Exception Type
            if($ex instanceof PPConnectionException) {

                //  Error Messages
                $err_messages = array();

                //  Get Exception Data
                $data = json_decode($ex->getData());

                //  Check
                if(isset($data->error_description)) {

                    //  Set Error
                    $err_messages[] = $data->error_description;
                }
                else if(isset($data->name)) {

                    //  Set Error Type
                    $err_messages[] = 'Error: ' . $data->name . ' | ' . $data->message;
                    $err_messages[] = '';

                    //  Check for Details Available
                    if(isset($data->details)) {

                        //  Loop Details
                        foreach($data->details as $detail) {

                            //  Set Message
                            $err_messages[] = '<strong>' . $detail->field . ':</strong> ' . $detail->issue;
                        }

                        //  Add Break
                        $err_messages[] = '';
                    }

                    //  Set Link
                    $err_messages[] = 'For more informations, click <a href="' . $data->information_link . '" target="_blank">here</a>.';
                }

                //  Set Error Message
                $errorMessage = implode('<br/>', $err_messages);
            }

            //  Set Transaction Error
            $this->transError = $errorMessage;
        }

        //  Parent
        parent::_runTransaction($closure);
    }
}