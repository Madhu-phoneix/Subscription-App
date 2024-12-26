<?php
use PHPShopify\ShopifySDK;
$dirPath = dirname(dirname(__DIR__));
include $dirPath."/application/library/config.php";
include $dirPath."/graphLoad/autoload.php";
 if(isset($_POST['submit'])){
    $store_name = $_POST['sname'];
    $get_access_token_query = $db->query("SELECT access_token from install where store = '$store_name'");
    $access_token_data = $get_access_token_query->fetch(PDO::FETCH_ASSOC);
    $access_token = $access_token_data['access_token'];
    $config = array(
        'ShopUrl' => $store_name,
        'AccessToken' => $access_token
    );
    $shopifies = new ShopifySDK($config);

    $get_webhooks_count = PostPutApi('https://'. $store_name.'/admin/api/'.$SHOPIFY_API_VERSION.'/webhooks/count.json','GET',$access_token,'');
    if($get_webhooks_count['count'] < 14){
        $create_webhooks = array(
			"APP_UNINSTALLED" => $SHOPIFY_DOMAIN_URL."/application/webhooks/app_uninstall.php",
			"PRODUCTS_DELETE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/product_delete.php",
			"PRODUCTS_UPDATE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/product_update.php",
			"CUSTOMER_PAYMENT_METHODS_UPDATE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/customer_payment_methods_update.php",
			"CUSTOMER_PAYMENT_METHODS_CREATE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/customer_payment_methods_create.php",
			"CUSTOMER_PAYMENT_METHODS_REVOKE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/customer_payment_methods_revoke.php",
			"CUSTOMERS_UPDATE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/customers_update.php",
			"SUBSCRIPTION_CONTRACTS_CREATE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/subscription_contracts_create.php",
			"SUBSCRIPTION_CONTRACTS_UPDATE" =>  $SHOPIFY_DOMAIN_URL."/application/webhooks/subscription_contracts_update.php",
			"SUBSCRIPTION_BILLING_ATTEMPTS_SUCCESS" =>  $SHOPIFY_DOMAIN_URL."/application/webhooks/subscription_billing_attempts_success.php",
			"SUBSCRIPTION_BILLING_ATTEMPTS_FAILURE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/subscription_billing_attempts_failure.php",
			"SUBSCRIPTION_BILLING_ATTEMPTS_CHALLENGED"=>$SHOPIFY_DOMAIN_URL."/application/webhooks/subscription_billing_attempts_challenged.php",
			"SHOP_UPDATE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/shop_update.php",
			"THEMES_UPDATE" =>  $SHOPIFY_DOMAIN_URL."/application/webhooks/theme_update.php",
		);
        createWebhooks($create_webhooks,$shopifies);

        echo 'All webhooks are :- <br>';
        $new_all_webhooks = PostPutApi('https://'. $store_name.'/admin/api/'.$SHOPIFY_API_VERSION.'/webhooks.json','GET',$access_token,'');
        echo '<pre>';
        print_r($new_all_webhooks);

    }else{
        echo 'Total webhooks count = '.$get_webhooks_count['count'];
    }
 }

 function PostPutApi($url, $action, $access_token, $arrayfield)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $action,
            CURLOPT_POSTFIELDS => $arrayfield,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "x-shopify-access-token:{$access_token}"
            ),
        ));

		$response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }
    }

    function createWebhooks($create_webhooks,$shopifies){
		foreach($create_webhooks as $key=>$val){
			try{
				$createWebhook =  'mutation webhookSubscriptionCreate($topic: WebhookSubscriptionTopic!, $webhookSubscription: WebhookSubscriptionInput!) {
					webhookSubscriptionCreate(topic: $topic, webhookSubscription: $webhookSubscription) {
					userErrors {
						field
						message
					}
					webhookSubscription {
						id
					}
					}
				}';
				$webhookParameters = [
					"topic"=> $key,
					"webhookSubscription" => [
					"callbackUrl"=> $val,
					"format"=> "JSON"
					]
				];
				$createWebhookGet = $shopifies->GraphQL->post($createWebhook,null,null,$webhookParameters);
			}catch(Exception $e) {
				echo $e;
			}
		}
	}

?>
<form action="" method="post">
  <label for="fname">Store name:</label><br>
  <input type="text" id="sname" name="sname" value=""><br>
  <input type="submit" value="Submit" name="submit">
</form>
