<?php
$dirPath = dirname(dirname(__DIR__));
include $dirPath."/library/config.php";
$store = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
$json_str = file_get_contents('php://input');
file_put_contents($dirPath."/assets/txt/gdpr/shop_redact.txt",$json_str);
$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
function verify_webhook($data, $hmac_header, $API_SECRET_KEY) {
    $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $API_SECRET_KEY, true));
    return hash_equals($hmac_header, $calculated_hmac);
}
$verified = verify_webhook($json_str, $hmac_header, $API_SECRET_KEY);
if ($verified) {
    $get_store_details_query = $db->query("SELECT store_id from uninstalls WHERE store = '$store'");
    $get_store_details = $get_store_details_query->fetch(PDO::FETCH_ASSOC);
    $store_id = $get_store_details['store_id'];
    $db->query("DELETE FROM store_details WHERE store_id = $store_id");
    $db = null;
}else{
  http_response_code(401);
}






