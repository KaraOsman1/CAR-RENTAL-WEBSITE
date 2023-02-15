<?php

// Get the form data
$phone = $_POST['phone'];
$amount = $_POST['amount'];

// Generate an authentication token
// You'll need to replace 'YOUR_CONSUMER_KEY' and 'YOUR_CONSUMER_SECRET' with your own credentials from the M-PESA API
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "Authorization: Basic ".base64_encode("YOUR_CONSUMER_KEY:YOUR_CONSUMER_SECRET"),
        "Cache-Control: no-cache",
    ),
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
if ($err) {
  echo "cURL Error #:" . $err;
  exit;
}
$access_token = json_decode($response)->access_token;

// Initiate a new payment transaction
// You'll need to replace 'YOUR_INITIATOR_NAME', 'YOUR_SECURITY_CREDENTIAL', 'YOUR_SHORTCODE' and 'YOUR_CALLBACK_URL' with your own credentials from the M-PESA API
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode(array(
        "BusinessShortCode" => "YOUR_SHORTCODE",
        "Password" => base64_encode("YOUR_SHORTCODE".date("YmdHis").date("YmdHis")."YOUR_SECURITY_CREDENTIAL"),
        "Timestamp" => date("YmdHis"),
        "TransactionType" => "CustomerPayBillOnline",
        "Amount" => $amount,
        "PartyA" => $phone,
        "PartyB" => "YOUR_SHORTCODE",
        "PhoneNumber" => $phone,
        "CallBackURL" => "YOUR_CALLBACK_URL",
        "AccountReference" => "Test Payment",
        "TransactionDesc" => "Test Payment Description"
    )),
    CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer $access_token",
        "Content-Type: application/json",
        "Cache-Control: no-cache",
    ),
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
if ($err) {
  echo "cURL Error #:" . $err;
  exit;
}

// Store the payment details in your local database
// You'll need to replace 'YOUR_DB_HOST', 'YOUR_DB_USERNAME', 'YOUR_DB_PASSWORD', and 'YOUR_DB_NAME' with your own database credentials
$mysqli = new mysqli('YOUR_DB_HOST', 'YOUR_DB_USERNAME', 'YOUR_DB_PASSWORD', 'YOUR_DB_NAME');
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
$mysqli->query("INSERT INTO payments (phone, amount, status) VALUES ('$phone', '$amount', 'Pending')");

// Show a confirmation page to the customer
echo "Thank you for
