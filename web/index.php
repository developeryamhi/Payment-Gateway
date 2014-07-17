<?php

//  Load Config File
require_once 'config.php';

//  Messages to Display
$messages = array(
    'no_payment_data' => 'No Payment Info Submitted'
);

//  Get Action, Message
$action = (isset($_GET['action']) ? strtolower($_GET['action']) : null);
$message = (isset($_GET['msg']) ? strtolower($_GET['msg']) : null);
$message_type = (isset($_GET['msg_type']) ? strtolower($_GET['msg_type']) : 'info');

//  Check in Session
if(isset($_SESSION[$message])) {

    //  Store Message
    $messages[$message] = $_SESSION[$message];
    if(isset($_SESSION[$message . '_type']))
        $message_type = $_SESSION[$message . '_type'];
}

//  Direct Messages
$direct_messages = array();

//  Get The Params
$payment_info = (isset($_POST['payment']) ? $_POST['payment'] : null);

//  Switch Action
switch($action) {

    //  Case 'process_payment'
    case 'process_payment':

        //  Check
        if($payment_info) {

            //  Get Info
            $gatewayName = $payment_info['gateway'];
            $currency = $payment_info['currency'];
            $cc_number = str_ireplace(' ', '', $payment_info['cc_number']);
            $cc_cvc = str_ireplace(' ', '', $payment_info['cc_cvc']);
            $cc_expiry = str_ireplace(' ', '', $payment_info['cc_exp']);
            $expiry_split = explode('/', $cc_expiry);
            $amount = $payment_info['amount'];
            $card_type = Developeryamhi\PaymentGateway\CardHelper::getType($cc_number);

            //  Check for Auto-Detect Gateway
            if($gatewayName == '- Auto Detect -') {

                //  Check for Card Type
                if($card_type == 'amex' || in_array($currency, array('USD', 'EUR', 'AUD'))) {

                    //  Paypal Gateway
                    $gatewayName = 'Paypal';
                } else {

                    //  BrainTree Gateway
                    $gatewayName = 'BrainTree';
                }
            }

            //  Success
            $success = true;

            //  Validate Card Info
            if(!Developeryamhi\PaymentGateway\CardHelper::isValid($cc_number)) {

                //  Set Error
                $success = false;

                //  Add Message
                $direct_messages[] = 'Invalid Credit Card Number';
            }

            //  Validate Card Info
            if($cc_cvc != '' && !preg_match('/([0-9]+)/i', $cc_cvc)) {

                //  Set Error
                $success = false;

                //  Add Message
                $direct_messages[] = 'Invalid Credit Card CVC';
            }

            //  Validate Card Info
            if(!preg_match('/([0-9\/]+)/i', $cc_expiry) || strlen($expiry_split[0]) != 2
                    || sizeof($expiry_split) == 1 ||  (strlen($expiry_split[1]) != 2 && strlen($expiry_split[1]) != 4)) {

                //  Set Error
                $success = false;

                //  Add Message
                $direct_messages[] = 'Invalid Credit Card Expiry Date';
            }

            //  Validate Card Type & CUrrency
            if($card_type == 'amex' && $currency != 'USD') {

                //  Set Error
                $success = false;

                //  Add Message
                $direct_messages[] = 'Amex Card Supports USD Currency Only. Please use another card or select USD for currency';
            }

            //  Check Success
            if($success) {

                //  Create Gateway Object
                $gateway = \Developeryamhi\PaymentGateway\GatewayHelper::locateGateway($gatewayName);

                //  Set Transaction Detail
                $gateway->setTransactionDetail('This is test transaction');

                //  Set Currency & Amount
                $gateway->setCurrency($currency);
                $gateway->setAmount($amount);

                //  Set Card Informations
                $gateway->setCardInfo($cc_number, $expiry_split[0], $expiry_split[1]);

                //  Process Transaction
                $gateway->process(function($success, $result, Developeryamhi\PaymentGateway\PaymentGateway $pg) {

                    //  Message Key
                    $message_key = 'gateway_response';

                    //  Check for Success
                    if($success) {

                        //  Set Details
                        $_SESSION[$message_key] = $pg->getTransactionMessage();
                        $_SESSION[$message_key . '_type'] = 'success';
                    } else {

                        //  Set Details
                        $_SESSION[$message_key] = $pg->getTransactionError();
                        $_SESSION[$message_key . '_type'] = 'danger';
                    }

                    //  Redirect
                    doTheRedirect('?msg=' . $message_key);
                });
            }
        } else {

            //  Do the Redirect
            doTheRedirect('?msg=no_payment_data&msg_type=danger');
        }

        break;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Assignment: Payment Gateway Integration</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />

<link href="assets/css/bootstrap.min.css" rel="stylesheet" />
<link href="assets/css/style.css" rel="stylesheet" />
</head>

<body>

    <div class="container">
        <div class="page">

            <h1>Payment Gateway Example</h1>

            <?php if($message) { ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $messages[$message]; ?>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            </div>
            <?php } ?>

            <?php if($direct_messages) { ?>
            <div class="alert alert-danger">
                <?php echo implode('<br/>', $direct_messages); ?>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            </div>
            <?php } ?>

            <ul class="nav nav-tabs">
                <li class="active"><a href="#process-payment" data-toggle="tab">Process Payment</a></li>
                <li><a href="#transactions" data-toggle="tab">Transactions</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="process-payment">
                    <form action="?action=process_payment" method="post" class="paymentInput">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment Gateway</label>
                                    <select name="payment[gateway]" class="form-control">
                                        <?php $gateways = pds_gateway_lists(); ?>
                                        <?php foreach($gateways as $gKey => $gName) { ?>
                                        <option value="<?php echo $gKey; ?>" <?php echo ($payment_info && $gKey == $payment_info['gateway'] ? 'selected="selected"' : ''); ?>><?php echo $gName; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Translate Currency</label>
                                    <select name="payment[currency]" class="form-control" id="payment-currency">
                                        <?php $currencies = array('USD', 'EUR', 'AUD', 'THB', 'HKD', 'SGD'); ?>
                                        <?php foreach($currencies as $cName) { ?>
                                        <option value="<?php echo $cName; ?>" <?php echo ($payment_info && $cName == $payment_info['currency'] ? 'selected="selected"' : ''); ?>><?php echo $cName; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Card Number</label>
                                    <div class="cc-num__wrap">
                                        <input type="text" name="payment[cc_number]" class="form-control cc-num" placeholder="XXXX - XXXX - XXXX - XXXX" data-numeric="true" value="<?php echo ($payment_info ? $payment_info['cc_number'] : ''); ?>" required />
                                        <span class="card" aria-hidden="true"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Card CVC</label>
                                    <input type="text" name="payment[cc_cvc]" class="form-control cc-cvc" placeholder="XXX" data-numeric="true" value="<?php echo ($payment_info ? $payment_info['cc_cvc'] : ''); ?>" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Expiry Date</label>
                                    <input type="text" name="payment[cc_exp]" class="form-control cc-exp" placeholder="XX / XXXX" value="<?php echo ($payment_info ? $payment_info['cc_exp'] : ''); ?>" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">$</span>
                                        <input type="text" name="payment[amount]" class="form-control" id="payment-amount" placeholder="10" data-numeric="true" value="<?php echo ($payment_info ? $payment_info['amount'] : '15'); ?>" required />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="submit" name="makePayment" class="btn btn-primary btn-block" value="Process Payment" />
                    </form>
                </div>

                <div class="tab-pane" id="transactions">
                    <table class="table table-bordered table-striped table-responsive">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Trans ID</th>
                                <th>Gateway</th>
                                <th>Details</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach(getDBConn()->transaction() as $transaction) { ?>
                            <tr>
                                <td>#<?php echo $transaction['id']; ?></td>
                                <td><?php echo $transaction['trans_id']; ?></td>
                                <td><?php echo $transaction['gateway']; ?></td>
                                <td><?php echo $transaction['description']; ?></td>
                                <td><?php echo $transaction['amount'] . ' ' . $transaction['currency']; ?></td>
                                <td><?php echo ucfirst($transaction['status']); ?></td>
                                <td><?php echo $transaction['created_at']; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.payment.js"></script>
    <!--<script src="assets/js/money.min.js"></script>-->
    <script src="assets/js/script.js"></script>
</body>
</html>