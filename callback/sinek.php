<?php

require_once __DIR__ . '/../../../init.php';
App::load_function('gateway');
App::load_function('invoice');

$gatewayModuleName = basename(__FILE__, '.php');

$gatewayParams = getGatewayVariables($gatewayModuleName);

if (!$gatewayParams['type']) {
    die("Module Not Activated");
}

$myIdentifier = checkCbInvoiceID($identifier, $gatewayModuleName);


$signature = $_POST['signature'];
$custom = $_POST['custom'];
$trx = $_POST['trx'];
$amount = $_POST['amount'];
$currency = $_POST['currency'];

$customKey = $amount.$currency.$custom.$trx;
$secret = $gatewayParams['secret_key'];
$mySignature = strtoupper(hash_hmac('sha256', $customKey , $secret));

if($signature == $mySignature){
    addInvoicePayment($identifier, $data['payment_trx'], $data['amount'], 0, $gatewayModuleName);
    callback3DSecureRedirect($identifier, true);
}
