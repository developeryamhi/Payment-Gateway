<?php namespace Developeryamhi\PaymentGateway\Gateway;

class BrainTreeGateway extends PaymentGateway {

    //  Set Gateway Key, Name
    public $key = 'braintree';
    public $name = 'BrainTree';


    //  Run Transaction
    public function _runTransaction(Closure $closure = null) {

        //  Set Properties
        Braintree_Configuration::environment($this->getEnvironment());
        Braintree_Configuration::merchantId($this->getMerchantInfoVal('merchant_id'));
        Braintree_Configuration::publicKey($this->getMerchantInfoVal('public_key'));
        Braintree_Configuration::privateKey($this->getMerchantInfoVal('private_key'));

        //  Get Card Info
        $cardInfo = $this->getCardInfo();

        //  Trigger Processed Events
        $this->_fireEvent('transaction.processing');
        $this->_fireInternalEvent('onTransactionProcessing');

        //  Try/Catch
        try {

            //  Make Sale
            $result = Braintree_Transaction::sale(array(
                'amount' => $this->getAmount(),
                'creditCard' => array(
                    'number' => $cardInfo['number'],
                    'expirationMonth' => $cardInfo['exp_month'],
                    'expirationYear' => $cardInfo['exp_year'],
                    'cvv' => $cardInfo['cvc']
                )
            ));

            //  Store Response
            $this->response = $result;

            //  Check for Success
            if ($result->success) {

                //  Set Success
                $this->tranSuccess = true;

                //  Set Transaction ID
                $this->transID = $result->transaction->id;

                //  Set Message
                $this->transMessage = 'Transaction Completed';

            } else {

                //  Set Error
                $this->tranSuccess = false;

                //  Set Error Message
                $this->transError = $result->message;
            }

        } catch(Exception $ex) {

            //  Store Response
            $this->response = $ex;

            //  Set Failed
            $this->tranSuccess = false;

            //  Set Transaction Error
            $this->transError = $ex->getMessage();
        }

        //  Parent
        parent::_runTransaction($closure);
    }
}