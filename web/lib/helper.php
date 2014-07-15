<?php

/**
 * Dump and Exit
 */
function dump_exit($var, $exit = true) {
    if(!function_exists('xdebug_disable'))  echo "<pre>";
    var_dump($var);
    if(!function_exists('xdebug_disable'))  echo "</pre>";
    if($exit)   exit;
}

/**
 * Echo and Exit
 */
function echo_exit($var) {
    echo (string)$var;
    exit;
}

//  Clear All Output
function clearTheOutput() {
    if(ob_get_level() > 0) {
        for($i=0; $i<ob_get_length(); $i++)
            @ob_clean();
    }
}

//  Redirect
function doTheRedirect($url) {
    clearTheOutput();
    @header('Location: ' . $url);
    exit;
}


//  Create Global Variable to Store Database Connection
global $db_pdo, $db_conn;

//  PDO Database Connection
$db_pdo = new PDO("mysql:dbname=" . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS);

//  Create NotORM Connection
$db_conn = new NotORM($db_pdo);

//  Get PDO Connection
function getPDOConn() {
    global $db_pdo;
    return $db_pdo;
}

//  Get Database Connection
function getDBConn() {
    global $db_conn;
    return $db_conn;
}