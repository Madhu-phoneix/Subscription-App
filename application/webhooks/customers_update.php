<?php
$dirPath = dirname(dirname(__DIR__));
include $dirPath."/application/library/config.php";
file_put_contents($dirPath."/application/assets/txt/webhooks/customer_update.txt",$json_str);
$store = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
$json_str = file_get_contents('php://input');
$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
function verify_webhook($data, $hmac_header, $API_SECRET_KEY) {
	$calculated_hmac = base64_encode(hash_hmac('sha256', $data, $API_SECRET_KEY, true));
	return hash_equals($hmac_header, $calculated_hmac);
}
$verified = verify_webhook($json_str, $hmac_header, $API_SECRET_KEY);
if($verified){
    $json_obj = json_decode($json_str,true);
    $customerTagsArray = explode(", ",$json_obj['tags']);
    if (in_array("sd_subscription_customer", $customerTagsArray)){
        $customer_name = $json_obj['first_name'].' '.$json_obj['last_name'];
        $email = $json_obj['email'];
        $customer_id = $json_obj['id'];
        $query = $db->query("SELECT id FROM customers WHERE shopify_customer_id = '$customer_id'");
        $query->execute();
        $row_count = $query->rowCount();
        if($row_count > 0){
           $update_query = $db->query("UPDATE customers set name = '$customer_name' , email = '$email' WHERE shopify_customer_id = '$customer_id'");
        }else{
            $select_store_id_query = $db->query("SELECT id from install where store = '$store'");
            $select_store_id_data =  $select_store_id_query->fetch(PDO::FETCH_ASSOC);
            $store_id =  $select_store_id_data['id'];
            $insert_query = $db->query("INSERT INTO customers (store_id,name,email,shopify_customer_id) VALUES ('$store_id','$customer_name','$email','$customer_id')");
        }
    }
    http_response_code(200);
    $db = null;
}else{
    http_response_code(401);
}



