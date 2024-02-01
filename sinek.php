<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function sinek_config(){
    $configarray = array(
        "FriendlyName" => array(
            "Type" => "System",
            "Value" => "Sinek Payment Gateway"
        ),
        'title' => array(
            'FriendlyName' => 'Title',
            'Type' => 'text',
            'Size' => '50',
            'Default' => 'Your Gateway Name',
            'Description' => 'Enter the title for your gateway module',
        ),
        'payment_domain' => array(
            'FriendlyName' => 'Payment Domain',
            'Type' => 'text',
            'Size' => '500',
            'Description' => 'Enter the payment domain url',
        ),
        'secret_key' => array(
            'FriendlyName' => 'Secret Key',
            'Type' => 'text',
            'Size' => '50',
            'Description' => 'Enter your secret key',
        ),
    );

    return $configarray;
}

function sinek_link($params){
    global $CONFIG;

    $ipn_url = $CONFIG['Domain'].'/modules/gateways/callback/sinek.php';
    
    $parameters = array(
        'currency' => strtoupper($params['currency']),
        'amount' => $params['amount'],
        'details' => $params['description'],
        'ipn_url' => $ipn_url,
        'failed_url' => $params['cancel_url'],
        'redirect_url' => $CONFIG['Domain'].'/modules/gateways/sinek_session.php?redirect_url='.$params['returnurl'].'&paymentsuccess=true',
        'firstname' => $params['clientdetails']['firstname'],
        'lastname' => $params['clientdetails']['lastname'],
        'email' => $params['clientdetails']['email'],
        'custom'=>$params['invoiceid'],
        'company'      => 'N/A',
        'address'      => 'N/A',
        'city'         => 'N/A',
        'state'        => 'N/A',
        'zip'          => 'N/A',
        'country'      => 'N/A',
    );
    
    $url = $params['payment_domain']."/api/payment/initiate";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    
    if ($result) {
        $responseData = json_decode($result, true);
    
        if (isset($responseData['redirect_url'])) {
            header("Location: " . $responseData['redirect_url']);
            exit;
            $_SESSION['cart'] = array();
        } else {
            $errorMessage = "Payment URL initiation failed";
        }
    } else {
        $errorMessage = "Payment initiation failed";
    }
    
    if (isset($errorMessage)) {
        $homePageURL = $CONFIG['Domain']; 
    
        echo '<div style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100vh;">';
        echo '<p style="font-size: 18px; color: red;">' . $errorMessage . '</p>';
        echo '<a href="' . $homePageURL . '" style="text-decoration: none; background-color: #007bff; color: white; padding: 8px 16px; border-radius: 5px; margin-top: 10px;">Go to Home</a>';
        echo '</div>';
    }
    
    exit;
}

function sinek_callback(){
    $file = 'sinek_log.txt';
    $content = 'IPN';
    file_put_contents($file, $content);
}

function dd(...$args) {
    echo '<pre>';
    foreach ($args as $arg) {
        var_dump($arg);
    }
    echo '</pre>';
    die();
}