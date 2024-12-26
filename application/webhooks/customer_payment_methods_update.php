<?php
$dirPath = dirname(dirname(__DIR__));
include $dirPath."/application/library/config.php";
$store = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
$json_str = file_get_contents('php://input');
file_put_contents($dirPath."/application/assets/txt/webhooks/customer_payment_methods_update.txt",$json_str);
$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
function verify_webhook($data, $hmac_header, $API_SECRET_KEY) {
	$calculated_hmac = base64_encode(hash_hmac('sha256', $data, $API_SECRET_KEY, true));
	return hash_equals($hmac_header, $calculated_hmac);
}
$verified = verify_webhook($json_str, $hmac_header, $API_SECRET_KEY);
if($verified){
    $json_obj = json_decode($json_str,true);
    $token = $json_obj['token'];
    $customer_id =  $json_obj['customer_id'];
    $instrument_type = $json_obj['instrument_type'];
    $payment_instrument = json_encode($json_obj['payment_instrument']);
    $query = $db->query("SELECT id FROM customerContractPaymentmethod WHERE shopify_customer_id = '$customer_id' and payment_method_token = '$token'");
    $query->execute();
    $row_count = $query->rowCount();
    if($row_count > 0){
      $update_query = $db->query("UPDATE customerContractPaymentmethod set payment_instrument_type = '$instrument_type' , payment_instrument_value = '$payment_instrument' WHERE shopify_customer_id = '$customer_id' and payment_method_token = '$token'");
    }else{
        $select_store_id_query = $db->query("SELECT id from install where store = '$store'");
        $select_store_id_data =  $select_store_id_query->fetch(PDO::FETCH_ASSOC);
        $store_id =  $select_store_id_data['id'];
        $insert_query = $db->query("INSERT INTO customerContractPaymentmethod (store_id,shopify_customer_id,payment_method_token,payment_instrument_type,payment_instrument_value) VALUES ('$store_id','$customer_id','$token','$instrument_type','$payment_instrument')");
    }
    $db = null;
}else{
    http_response_code(401);
}
