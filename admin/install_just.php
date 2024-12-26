
<?php
// ini_set("display_errors", 1);
// error_reporting(E_ALL);
// echo 'hello';
include("../application/controller/Mainfunction.php");
$mainobj = new MainFunction($store);
$store_name = strtok($store, '.');
?>
<head>
  <script src="https://unpkg.com/@shopify/app-bridge@3"></script>
  <!-- <meta name="shopify-api-key" content="<?php //echo $mainobj->SHOPIFY_APIKEY; ?>" />
  <script src="https://cdn.shopify.com/shopifycloud/app-bridge.js"></script> -->
</head>
<script>
			var AppBridge = window['app-bridge'];
			var createApp = AppBridge.createApp;
			var app = createApp({
				apiKey: '32df8dd1bd8f9fb6669a8c4698464d54',
				host: new URLSearchParams(location.search).get("host"),
			});
			var actions = AppBridge.actions;
			var Redirect = actions.Redirect;
			var redirect = Redirect.create(app);
			console.log(redirect);
</script>
<?php
session_start();
// $_SESSION["app_verification"] = "Yes";
//check that the store entry exist in both install and storeInstallOffer table
$whereCondition = array(
  'store' => $store
);
// echo '<pre>';
// print_r($whereCondition);
$checkInstallOffer = $mainobj->table_row_value('InstallAndStoreinstalloffer','All',$whereCondition,'and',''); //CALLING A VIEW
// echo '<pre>';
// print_r($checkInstallOffer);
// die;
//if entry not exist in any of the two table then redirect it in the auth screen to generate access token
if(empty($checkInstallOffer)){
  // echo 'hello3';
  // die;
  $install_action_url = "https://{$store}/admin/oauth/authorize?client_id={$mainobj->SHOPIFY_APIKEY}&scope={$mainobj->SHOPIFY_SCOPES}&redirect_uri={$mainobj->SHOPIFY_REDIRECT_URI}"; // oath Screen
  echo "<script>window.top.location.href='".$install_action_url."'</script>";
}else{
  //if entry exist in install table but not in the storeInstallOffers it means user declined or leaves the billing screen then show the member plans
  if($checkInstallOffer[0]['store_id'] == ''){
    // echo 'hello1';
    // die;
    //   $install_action_url = $mainobj->SHOPIFY_DOMAIN_URL."/admin/memberPlans.php?billingStatus=unattempted&shop={$store}";
    $install_action_url = 'https://admin.shopify.com/store/'.$store_name.'/apps/'.$mainobj->SHOPIFY_APIKEY.'/subscription/admin/memberPlans.php?shop='.$store;
  }else{
    // echo 'hello2';
    // die;
    //if store entry exist in both tables then redirect user to the app dashboard
    //   $install_action_url = $mainobj->SHOPIFY_DOMAIN_URL."/admin/dashboard.php?shop={$store}";
    $install_action_url = 'https://admin.shopify.com/store/'.$store_name.'/apps/'.$mainobj->SHOPIFY_APIKEY.'/subscription/admin/dashboard.php?shop='.$store;
  }
  echo '<script type="text/javascript">redirect.dispatch(Redirect.Action.APP, "/subscription/admin/dashboard.php"); </script>';
  // echo "<script type='text/javascript'>open('/subscription/admin/dashboard.php', '_self'); </script>";
}

