<?php
use PHPShopify\ShopifySDK;
$dirPath = dirname(dirname(__DIR__));
include $dirPath."/application/library/config.php";
include $dirPath."/graphLoad/autoload.php";
$store = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
$json_str = file_get_contents('php://input');
file_put_contents($dirPath."/application/assets/txt/webhooks/theme_update.txt",$json_str);
$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
function verify_webhook($data, $hmac_header, $API_SECRET_KEY) {
	$calculated_hmac = base64_encode(hash_hmac('sha256', $data, $API_SECRET_KEY, true));
	return hash_equals($hmac_header, $calculated_hmac);
}
$verified = verify_webhook($json_str, $hmac_header, $API_SECRET_KEY);
if($verified){
    $json_obj = json_decode($json_str,true);
    $theme_id = $json_obj['id'];
    $graphql_theme_id = $json_obj['admin_graphql_api_id'];
    $store_install_query = $db->query("Select access_token FROM install WHERE store = '$store'");
    $store_install_data = $store_install_query->fetch(PDO::FETCH_ASSOC);
    $access_token = $store_install_data['access_token'];
    $config = array(
        'ShopUrl' => $store,
        'AccessToken' => $access_token
    );
    $shopifies = new ShopifySDK($config);
    $app_installation_query = '{
        appInstallation{
           id
        }
    }';
    $app_installation_execution = $shopifies->GraphQL->post($app_installation_query, null, null, null);
    $app_installation_id = $app_installation_execution['data']['appInstallation']['id'];

    //get active theme id
    $activeThemeid_query = "query{
        translatableResources(first:1, resourceType:ONLINE_STORE_THEME ){
            edges{
                node{
                  resourceId
                }
            }
        }
    }
    ";
    $activeThemeArray = $shopifies->GraphQL->post($activeThemeid_query,null,null,null);
    $ThemeGraphqlId = $activeThemeArray["data"]["translatableResources"]["edges"][0]["node"]["resourceId"];
    $theme_id = substr($ThemeGraphqlId, strrpos($ThemeGraphqlId, "/") + 1);
    // echo $theme_id; die;
    //check if theme_support 2.0
    try{
      $get_theme_support = PostPutApi('https://'. $store.'/admin/api/'.$SHOPIFY_API_VERSION.'/themes/'.$theme_id.'/assets.json?asset[key]=templates/product.json','GET',$access_token,'');
      if($get_theme_support){
        $theme_block_support = 'support_theme_block';
        $theme_block_support_not = 'support_theme_block_not';
        $meta_field_value_not = "false";
        $meta_field_value = "true";
      }else{
        $theme_block_support = 'support_theme_block';
        $meta_field_value = "false";
        $theme_block_support_not = 'support_theme_block_not';
        $meta_field_value_not = "true";
      }
    }catch(Exception $e) {
        $theme_block_support = 'support_theme_block';
        $meta_field_value = "false";
        $theme_block_support_not = 'support_theme_block_not';
        $meta_field_value_not = "true";
    }

    try{
        $createAppOwnedMetafield =  'mutation CreateAppOwnedMetafield($metafieldsSetInput: [MetafieldsSetInput!]!) {
            metafieldsSet(metafields: $metafieldsSetInput) {
              metafields {
                id
                namespace
                key
                type
                value
              }
              userErrors {
                field
                message
              }
            }
        }';

        $webhookParameters = [
            "metafieldsSetInput"=> [
                [
                    "namespace"=> "theme_support",
                    "key"=> $theme_block_support,
                    "type"=> "boolean",
                    "value"=> $meta_field_value,
                    "ownerId"=> $app_installation_id
                ],
                [
                    "namespace"=> "theme_not_support",
                    "key"=> $theme_block_support_not,
                    "type"=> "boolean",
                    "value"=> $meta_field_value_not,
                    "ownerId"=> $app_installation_id
                ]
            ]
        ];
        $AppOwnedMetafieldGet = $shopifies->GraphQL->post($createAppOwnedMetafield,null,null,$webhookParameters);
    }catch(Exception $e) {
    }
    $db = null;
}else{
    http_response_code(401);
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