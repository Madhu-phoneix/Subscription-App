<?php
$dirPath = dirname(dirname(__DIR__));
include $dirPath."/application/library/config.php";
file_put_contents($dirPath."/application/assets/txt/webhooks/customer_payment_methods_revoke.txt",$json_str);
$store = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
$json_str = file_get_contents('php://input');
$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
function verify_webhook($data, $hmac_header, $API_SECRET_KEY) {
	$calculated_hmac = base64_encode(hash_hmac('sha256', $data, $API_SECRET_KEY, true));
	return hash_equals($hmac_header, $calculated_hmac);
}
$verified = verify_webhook($json_str, $hmac_header, $API_SECRET_KEY);
if($verified){
    http_response_code(200);
    $db = null;
}else{
    http_response_code(401);
}