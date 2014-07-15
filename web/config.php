<?php

//  Start Session
if(!session_id()) session_start();

//  Paths
define('DS', '/');
define('APP_PATH', str_ireplace('\\', DS, dirname(__FILE__)) . DS);
define('LIB_PATH', APP_PATH . 'lib' . DS);
define('LOG_PATH', APP_PATH . 'log' . DS);

//  Database Connection Details
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password');
define('DB_NAME', 'payment_gateway');

//  Paypal Payment Gateway Details
define('PP_TRANSACTION_MODE', 'sandbox');
define('PP_CONFIG_PATH', LIB_PATH . 'ini');
define('PP_CLIENT_ID', 'AZR1VRBs7O9kCuIIJlTEzGPeVHJNhVC4bAdDU-dzdmlGlsm7hBMuNcnI1FJI');
define('PP_CLIENT_SECRET', 'EHPloxCtPq1IEmlB6fuOHIijCsL82Vq4ofxB0kwjOYAVRWgVkJxYFpiHxncY');

//  BrainTree Payment Gateway Details
define('BT_TRANSACTION_MODE', 'sandbox');
define('BT_MERCHANT_ID', 'xvq3bjpdgk2pp2wy');
define('BT_PUBLIC_KEY', '62ygmtn3rb48ggp2');
define('BT_PRIVATE_KEY', 'e99c664c6cc3b70c1d49d127b87147ff');
define('BT_CSE_KEY', 'MIIBCgKCAQEAucq+8n5YQutSlJwKFcKrQ4TohxTHW73GQA/YvO7gD9Ea3mzGpGQwrvp731xHPVJEsa2fqFDttiK7QExGKBg8XR9AtNO28xXBabonjXRG+06+FYsJgSYa7E3jkbdmODCJ0mIkqdrCchIgX9aJMP05CBip59UImIbE1eKqkKOOYYhRfnxeBSuX9RDyYaRszAYXs10UNgTPrlMT57WbHkT5RviWoVXN7RR9JLdTPMTbh4fvqEnfMN2OoUhDTilXE/4zO0l627WgKoDpxyuN7RMLURVA+UM1XCoK0bSY2fYIeE4hnl2BoQf0Z/AHV2/FbsV4GjGNwyZO+2S63XSmVXefwwIDAQAB');


//  Load Autoload File
include_once '../vendor/autoload.php';

//  Load Helper File
include_once LIB_PATH . 'helper.php';

//  Init the Gateway Helper
\Developeryamhi\PaymentGateway\GatewayHelper::init();

//  Load Application Start File
include_once APP_PATH . 'start.php';