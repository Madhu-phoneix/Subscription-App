<?php
//  ini_set('display_errors', 1);
//  ini_set('display_startup_errors', 1);
//  error_reporting(E_ALL);
include "../library/commonmodal.php";
$store = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
// $store = 'testing-neha-subscription.myshopify.com';
$mainobj = new Commonmodal($store);
$API_SECRET_KEY = $mainobj->SHOPIFY_SECRET;
$json_str = file_get_contents('php://input');
function verify_webhook($data, $hmac_header, $API_SECRET_KEY)
{
    $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $API_SECRET_KEY, true));
    return hash_equals($hmac_header, $calculated_hmac);
}
$verified = verify_webhook($json_str, $hmac_header, $API_SECRET_KEY);
?>
