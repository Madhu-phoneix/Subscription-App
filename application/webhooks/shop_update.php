<?php
use PHPShopify\ShopifySDK;
$dirPath = dirname(dirname(__DIR__));
include $dirPath."/application/library/config.php";
$store = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
$json_str = file_get_contents('php://input');
file_put_contents($dirPath."/application/assets/txt/webhooks/shop_update.txt",$json_str);
$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
function verify_webhook($data, $hmac_header, $API_SECRET_KEY) {
	$calculated_hmac = base64_encode(hash_hmac('sha256', $data, $API_SECRET_KEY, true));
	return hash_equals($hmac_header, $calculated_hmac);
}
$verified = verify_webhook($json_str, $hmac_header, $API_SECRET_KEY);
if($verified){
  $json_obj = json_decode($json_str,true);
  $currencyCode1 = str_replace("{{amount}}","",$json_obj['money_in_emails_format']);
  $currencyCode = str_replace("{{amount_with_comma_separator}}","",$currencyCode1);
  $store_install_query = $db->query("SELECT id from install where store = '$store'");
  $store_install_data = $store_install_query->fetch(PDO::FETCH_ASSOC);
  $store_id = $store_install_data['id'];
  $currency = $json_obj['currency'];
  $store_email = $json_obj['email'];
  $owner_name = $json_obj['shop_owner'];
  $shop_name = $json_obj['name'];
  $shop_timezone = $json_obj['iana_timezone'];
  $currencyCode = $currencyCode;
  $shop_plan = $json_obj['plan_name'];
  $db->query("UPDATE store_details SET currency = '$currency', store_email='$store_email', owner_name = '$owner_name', shop_name = '$shop_name', currencyCode = '$currencyCode', shop_timezone = '$shop_timezone', shop_plan = '$shop_plan' WHERE store_id = '$store_id'");
  $db = null;
}else{
  http_response_code(401);
}