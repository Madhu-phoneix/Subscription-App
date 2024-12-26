<?php
// include_once("header.php");
use PHPShopify\ShopifySDK;
$dirPath =dirname(__DIR__);
require_once($dirPath."/application/library/shopify.php");
require_once($dirPath."/graphLoad/autoload.php");
include($dirPath."/application/library/config.php");
$store = $_GET['shop'];
if(!isset($_GET['code'])){
  $pageType = 'after_installation';
  $code = '';
}else{
   $pageType = 'on_installation';
   $code = $_GET['code'];
}
function PostPutApi($url, $action, $access_token, $arrayfield){
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

// function start here
 function recurringBillingCharge($billingData){
   global $SHOPIFY_APIKEY,$SHOPIFY_SECRET,$SHOPIFY_DOMAIN_URL,$db,$SHOPIFY_API_VERSION;
   $store = $billingData['store'];
   $getCurrentUtcTime = gmdate("Y-m-d H:i:s");
   $wherestoreCondition = array(
      'store' => $billingData['store']
   );
   $store_id='';
    if($billingData['pageType'] == 'on_installation'){
      //get access token
      $shopifyClient = new ShopifyClient($billingData['store'], "", $SHOPIFY_APIKEY, $SHOPIFY_SECRET);
      $access_token = $shopifyClient->getAccessToken($billingData['sd_code']);
      //save data in the intall table
      if($access_token != ''){
         //check entry in uninstall table
         $fields = array('store_id');
         $uninstall_details_query = "SELECT store_id FROM `uninstalls` WHERE store = '$store'";
         $result = $db->query($uninstall_details_query);
         $uninstall_details = $result->fetch(PDO::FETCH_ASSOC);
         if($uninstall_details){ // it means the app is reinstalls within 48 hours then insert the previous store_id in the install table
            $store_id = $uninstall_details['store_id'];
            $sql_query_insert = "INSERT INTO install (id,store,access_token,app_status,created_at) VALUES ('$store_id','$store','$access_token','1','$getCurrentUtcTime')";
            $saveInstallData = $db->exec($sql_query_insert);
            $store_id = $db->lastInsertId();
         }else{ // insert entry with new store_id
            $sql_query_insert = "INSERT INTO install (store,access_token,app_status,created_at) VALUES ('$store','$access_token','1','$getCurrentUtcTime')";
            $saveInstallData = $db->exec($sql_query_insert);
            $store_id = $db->lastInsertId();
         }

         $config = array(
            'ShopUrl' => $store,
            'AccessToken' => $access_token,
         );
         $shopify_graphql_object = new ShopifySDK($config);

         $graphQL = "query{
				translatableResources(first:1, resourceType:ONLINE_STORE_THEME ){
				edges{
					node{
					resourceId
					}
				}
				}
			}
			";
			$activeThemeArray = $shopify_graphql_object->GraphQL->post($graphQL,null,null,null);
			$ThemeGraphqlId = $activeThemeArray["data"]["translatableResources"]["edges"][0]["node"]["resourceId"];
			$theme_id = substr($ThemeGraphqlId, strrpos($ThemeGraphqlId, "/") + 1);
         // public function addAppMetafields($theme_id){
            $app_installation_query = '{
               appInstallation{
                  id
               }
            }';
            $app_installation_execution = $shopify_graphql_object->GraphQL->post($app_installation_query, null, null, null);
            $app_installation_id = $app_installation_execution['data']['appInstallation']['id'];
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
                  $AppOwnedMetafieldGet = $shopify_graphql_object->GraphQL->post($createAppOwnedMetafield,null,null,$webhookParameters);
                  $AppOwnedMetafield_error = $AppOwnedMetafieldGet['data']['metafieldsSet']['userErrors'];
                  if(!count($AppOwnedMetafield_error)){
                     // return true;
                  }else{
                     // return false;
                  }
              }catch(Exception $e) {
               // return false;
              }
         //add uninstall webhook
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
                "topic"=> 'APP_UNINSTALLED',
                "webhookSubscription" => [
                    "callbackUrl"=> $SHOPIFY_DOMAIN_URL."/application/webhooks/app_uninstall.php",
                    "format"=> "JSON"
                ]
            ];
            $createWebhookGet = $shopify_graphql_object->GraphQL->post($createWebhook,null,null,$webhookParameters);
         }catch(Exception $e) {
               echo ($e->getMessage());
         }
         $storeData = PostPutApi('https://'. $store.'/admin/api/'.$SHOPIFY_API_VERSION.'/shop.json','GET',$access_token,'');
         $storeEmail =  $storeData['shop']['email'];
         $currency = $storeData['shop']['currency'];
         $shopFormat = $storeData['shop']['money_in_emails_format'];
         $currencyCode1 = str_replace("{{amount}}","",$shopFormat);
         $currencyCode = str_replace("{{amount_with_comma_separator}}","",$currencyCode1);
         $shop_timezone = $storeData['shop']['iana_timezone'];
         $shop_owner = $storeData['shop']['shop_owner'];
         $shop_name = $storeData['shop']['name'];
         $shop_plan = $storeData['shop']['plan_name'];
         if($storeData['shop']['plan_display_name'] == 'Shopify Plus'){
            $plusPlan = '1';
         }else{
            $plusPlan = '0';
         }
         if($saveInstallData){
            $store_details_query = "SELECT * FROM `store_details` WHERE store_id = '$store_id'";
            $result = $db->query($store_details_query);
            $store_details = $result->fetch(PDO::FETCH_ASSOC);
            if($store_details){ // it means the app is reinstalls within 48 hours then insert the previous store_id in the install table
               $store_details_update = "UPDATE `store_details` SET store_id = '$store_id', store_email = '$storeEmail', shopify_plus = '$plusPlan', currency = '$currency', currencyCode = '$currencyCode', shop_timezone = '$shop_timezone', owner_name = '$shop_owner', shop_name = '$shop_name', shop_plan = '$shop_plan'";
               $saveInstallData = $db->exec($store_details_update);
            }else{ // insert entry with new store_id
               $store_details_insert = "INSERT INTO store_details (store_id,store_email,shopify_plus,currency,currencyCode,shop_timezone,owner_name,shop_name,shop_plan) VALUES ('$store_id','$storeEmail','$plusPlan','$currency','$currencyCode','$shop_timezone','$shop_owner','$shop_name','$shop_plan')";
               $saveInstallData = $db->exec($store_details_insert);
            }
         }
      }
   }
   $updateInstallData_qry = "UPDATE `storeInstallOffers` SET status = '0' WHERE store_id = '$store_id' AND status = '1'";
   $saveInstallData = $db->exec($updateInstallData_qry);
   $created_at = date('Y-m-d H:i:s');
   $trial_days =  $billingData['sd_trialDays'];
   $planName = $billingData['sd_planName'];
   $saveInstallData_qry = "INSERT INTO storeInstallOffers (plan_id,store_id,planName,subscriptionPlans,subscriptionContracts,price,trial,subscription_emails,created_at) VALUES ('1','$store_id','$planName','-1','-1','0','$trial_days','10000','$created_at')";
   $saveInstallData = $db->exec($saveInstallData_qry);
   $install_action_url = $SHOPIFY_DOMAIN_URL.'/admin/dashboard.php?themeConfiguration='.$billingData['sd_configureTheme'].'&shop='.$store;
   return $install_action_url;
}
// function ends here
   $recurringBillingValues = array(
      "sd_trialDays" => '3',
      "pageType"=> $pageType,
      "sd_code"=> $code,
      "sd_planName"=> 'Free',
      "sd_configureTheme"=> 'Yes',
      "store" => $store
   );
  $app_installation = recurringBillingCharge($recurringBillingValues);
//   echo $app_installation;
  header("Location: ".$app_installation);
//   echo "<script>open('".$app_installation."', '_open'); </script>";
?>

<?php //require_once("footer.php");   ?>

