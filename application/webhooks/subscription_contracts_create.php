<?php
$dirPath = dirname(dirname(__DIR__));
include $dirPath."/application/library/config.php";
$store = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
$json_str = file_get_contents('php://input');
file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n".$json_str, FILE_APPEND | LOCK_EX);
$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
function verify_webhook($data, $hmac_header, $API_SECRET_KEY) {
	$calculated_hmac = base64_encode(hash_hmac('sha256', $data, $API_SECRET_KEY, true));
	return hash_equals($hmac_header, $calculated_hmac);
}
$verified = verify_webhook($json_str, $hmac_header, $API_SECRET_KEY);

if($verified) {
	$json_obj = json_decode($json_str, true);
	$contract_id = $json_obj['id'];
	$order_id = $json_obj['origin_order_id'];

	$contract_cron_query = $db->query("Select * FROM contract_cron WHERE store = '$store' AND order_id = '$order_id'");
	$contract_cron_data = $contract_cron_query->fetch(PDO::FETCH_ASSOC);
	if($contract_cron_data){
		// insert into contract_details table
		contractDetails($db, $store, $order_id, $contract_id, $json_str);
	}else {
		// insert into contract_cron table
		$contract_insert_query = "INSERT INTO contract_cron (`store`, `order_id`) VALUES ('$store', '$order_id')";
		$db->exec($contract_insert_query);
		// insert into contract_details table
		contractDetails($db, $store, $order_id, $contract_id, $json_str);
	}
	$db = null;
}else {
	file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n Webhook not verified . ".$contract_id, FILE_APPEND | LOCK_EX);
	http_response_code(401);
}

function contractDetails($db, $store, $order_id, $contract_id, $json_str) {
	$contract_details_query = $db->query("Select * FROM contract_details WHERE store = '$store' AND order_id = '$order_id' AND contract_id = '$contract_id'");
	$contract_details_data = $contract_details_query->fetch(PDO::FETCH_ASSOC);
	if(empty($contract_details_data)) {
		$contract_details_insert_query = "INSERT INTO contract_details (`store`, `order_id`, `contract_id`, `contract_payload`) VALUES ('$store', '$order_id', '$contract_id', '$json_str')";
		$db->exec($contract_details_insert_query);
	}
}