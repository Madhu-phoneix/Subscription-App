<html>

<!-- ========= Include this file where backend page needs to be built ====== -->
<?php
// session_start();
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
$current_page = basename($_SERVER['PHP_SELF']);
/* ==============  Headers ==============*/
header("Access-Control-Allow-Origin: *");
header('Frame-Ancestors: ALLOWALL');
header('X-Frame-Options: ALLOWALL');
header('Set-Cookie: cross-site-cookie=bar; SameSite=None; Secure');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
/* ==============  Headers ==============*/
  include(dirname(dirname(__file__))."/application/controller/Mainfunction.php");
  //include(dirname(dirname(__file__))."/modal/RitikaMainfunction.php");
$mainobj = new MainFunction($store);
$redirectPage = "https://".$store."/admin/apps";
if(!isset($_GET['code'])){
   if(strlen($mainobj->store_id) == 0){
    echo "<script>window.top.location.href='".$redirectPage."'</script>";
   }
  $app_status  = $mainobj->checkAppStatus(); //check customizer app status
  $whereCondition = array(
    'store_id' => $mainobj->store_id
  );
  $check_selling_plan = $mainobj->table_row_check('subscriptionPlanGroupsDetails',$whereCondition,'and');
  // check shopify payment criteria
  $check_shopify_payment = $mainobj->checkShopifyPaymentRequirement();
}
if(isset($_GET['themeConfiguration']) && ($_GET['themeConfiguration'] == 'Yes')){
  $mainobj->themeConfiguration();
}

$add_development_stores = array('mini-cart-development.myshopify.com','ritikatest-predectivesearch.myshopify.com','subscription-store-two.myshopify.com','prakriti-advanced-option-pro.myshopify.com','predictive-search.myshopify.com','simar-test.myshopify.com','mytab-shinedezign.myshopify.com','shineinfotest.myshopify.com','advanced-subscriptionpro.myshopify.com','magiktheme.myshopify.com','robins-store1.myshopify.com','pragati-test-store.myshopify.com');
if(in_array($mainobj->store, $add_development_stores)){
}else if($mainobj->shop_plan == "trial" || $mainobj->shop_plan == "partner_test" || $mainobj->shop_plan == "affiliate"){
  echo 'You are been automatically redirected...';
  $url_redir = $mainobj->SHOPIFY_DOMAIN_URL."/admin/not_allowed.php?shop={$mainobj->store}";
  echo '<script type="text/javascript">window.top.location.href = "' . $url_redir . '" </script>';
}

if(isset($_GET['logged_in_customer_id'])){ //It means app proxy url hit
    $logged_in_customer_id = $_GET['logged_in_customer_id'];
    $sd_add_class = 'sd_app_frontend';
    if(($_GET['logged_in_customer_id'] == '')){ //show subscription page only if the customer is logged else redirect to account page
      $redirectPage = "https://".$store."/account";
      echo "<script>window.top.location.href='".$redirectPage."'</script>";
    }
}else{
  $logged_in_customer_id = '';
  $sd_add_class = 'sd_app_backend';
  //if app is  uninstalled and on refreshing the page check install table entry
  // if(isset($_SESSION['app_verification']) && $_SESSION['app_verification'] == 'Yes'){
  // }else{
  //   //check weather the app is enabled or disabled from the customizer
  //   echo "<script>window.top.location.href='".$redirectPage."'</script>";
  // }
}

// if($mainobj->store == 'ca-thebetterchocolate.myshopify.com'){
//   $metafield_data = $mainobj->add_custom_metafields();
//   echo '<pre>';
//   print_r($metafield_data);

// }

// check store total sale(restrict all the pages if sale is greater than 500)
$total_subscription_order_sale_query = $mainobj->db->query("SELECT total_sale,contract_currency,contract_id FROM contract_sale WHERE store_id = '$mainobj->store_id'");
$get_billing_attempt_orders_sale = $total_subscription_order_sale_query->fetchAll(PDO::FETCH_ASSOC);
echo "<script>contract_sale =" . json_encode($get_billing_attempt_orders_sale) . ";</script>";
$total_subscription_order_sale = array_sum(array_column($get_billing_attempt_orders_sale,'total_sale'));

$wherePlanCondition = array(
  'store_id' => $mainobj->store_id,
  'status' => '1',
);
$plan_fields = array('plan_id','appSubscriptionPlanId','planName');
$active_member_data = $mainobj->single_row_value('storeInstallOffers',$plan_fields,$wherePlanCondition,'and','');
// if($this->store == 'predictive-search.myshopify.com'){
//   echo 'helloooooooo';
//   echo '<pre>';
//   print_r($active_member_data);
//   die;
// }
$active_plan_name = $active_member_data['planName'];
$active_plan_id = $active_member_data['plan_id'];
$active_member_plan_status = 'ACTIVE';
// if($mainobj->new_install == '0'){
    if(($active_plan_name == 'Free_old' && $mainobj->store == 'boo-kay-nyc.myshopify.com') || ($active_plan_name == 'Free_old' && $mainobj->store == 'advanced-subscriptionpro.myshopify.com') ){
      $amount_exceed = 1800;
    }else if($active_plan_name == 'Free_old'){
      $amount_exceed = 700;
    }else{
      $amount_exceed = 500;
    }
    //CHECK app billing status and if expired then redirect to the payment pending page
    if($total_subscription_order_sale > $amount_exceed && $logged_in_customer_id == ''){
      // $redirectPage = $mainobj->SHOPIFY_DOMAIN_URL."/admin/app_plans.php?shop=".$mainobj->store;
      $store_name = strtok($mainobj->store, '.');
      $redirectPage = '/subscription/admin/app_plans.php?shop='.$mainobj->store;
     if($active_member_data['plan_id'] != 3 ){
        $active_member_plan_status = 'INACTIVE';
        if($current_page != 'app_plans.php' && $current_page != 'dashboard.php' && $current_page != 'contactUs.php'){
          echo "<script type='text/javascript'>open('".$redirectPage."', '_self'); </script>";
        }
      }
    }
// }
//CHECK app billing status and if expired then redirect to the payment pending page
// if(!isset($_GET['billingStatus'])){
//   $billingStatus = $mainobj->getBillingStatus();
//     if($billingStatus != 'ACTIVE' && $billingStatus != 'free_plan_skip'){
//       $redirectPage = "https://".$mainobj->store."/admin/apps/".$mainobj->SHOPIFY_APIKEY."/".$mainobj->SHOPIFY_DIRECTORY_NAME."/admin/errors/paymentExpired.php?billingStatus = ".$billingStatus;
//       echo "<script>window.top.location.href='".$redirectPage."'</script>";
//     }
// }


?>
<head>
<?php if($logged_in_customer_id == ''){?>
<title>Advanced Subscription Pro</title>
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $mainobj->image_folder; ?>favicon.ico">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php } ?>
<input type="hidden" value="<?php echo $store ?>" id="store">
<input type="hidden" value="<?php echo $mainobj->store_id; ?>" id="store_id">
<?php if($logged_in_customer_id == ''){
   echo "<script> const AjaxCallFrom = 'backendAjaxCall';</script>";
  ?>
<input type="hidden" value="<?php echo $app_status ?>" id="theme_customizer_app">
<input type="hidden" value="<?php echo $mainobj->currency; ?>" id="SHOPIFY_CURRENCY" />
<input type="hidden" value="<?php echo $mainobj->currency_code; ?>" id="SHOPIFY_CURRENCY_CODE" />
<input type="hidden" value="<?php echo $mainobj->theme_block_name; ?>" id="THEME_BLOCK_NAME" />
<input type="hidden" value="<?php echo $mainobj->app_extension_id; ?>" id="APP_EXTENSION_ID" />
<input type="hidden" value="<?php echo $check_selling_plan; ?>" id="CHECK_SELLING_PLAN" />
<input type="hidden" value="<?php echo $check_shopify_payment; ?>" id="SHOPIFY_PAYMENT_REQUIREMENT" />
<input type="hidden" value="<?php echo $amount_exceed; ?>" id="sd_amount_exceed" />
<?php }else{
    echo "<script> const AjaxCallFrom = 'frontendAjaxCall';</script>";
} ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/@shopify/polaris@7.3.1/build/esm/styles.css"/>

<?php if($logged_in_customer_id == ''){?>
  <link href="<?php echo $mainobj->SHOPIFY_DOMAIN_URL ;?>/application/assets/css/font_family.css" rel="stylesheet">
  <meta name="shopify-api-key" content="<?php echo $mainobj->SHOPIFY_APIKEY; ?>" />
  <script src="https://cdn.shopify.com/shopifycloud/app-bridge.js"></script>
  <ui-nav-menu>
    <a href="/" rel="home">Dashboard</a>
    <a href="/admin/plans/subscription_group.php">My Plans</a>
    <a href="/admin/subscription/subscriptions.php">Subscriptions</a>
    <a href="/admin/subscriptionOrders.php">Subscription orders</a>
    <a href="/admin/settings/setting.php">Settings</a>
    <a href="/admin/analytics.php">Analytics</a>
    <a href="/admin/documentation.php">Documentation</a>
    <a href="/admin/video_tutorials.php">Video Tutorials</a>
    <a href="/admin/contactUs.php">Contact Us</a>
    <a href="/admin/app_plans.php">App Plans</a>
  </ui-nav-menu>
<?php } ?>
<link href="<?php echo $mainobj->SHOPIFY_DOMAIN_URL ;?>/application/assets/css/style.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Viga&display=swap" rel="stylesheet"> <!-- script for email template -->
</head>
<body>
<!-- <img src="https://your-domain.com/application/assets/images/subscription_loader.gif" id="sd_subscriptionLoader"> -->
<div class="Polaris-Spinner Polaris-Spinner--sizeLarge sd_subscriptionLoader" id = "sd_subscriptionLoader">
  <div class="circle-container">
    <div class="circle-progress"></div>
  </div>
<style>
.circle-container::after {
    position: fixed;
    content: "";
    width: 100%;
    height: 100%;
    background: #00000059;
    z-index: 999;
    top: 0;
    left: 0;
}

.circle-progress {
    position: absolute;
    height: 40px;
    width: 40px;
    border-radius: 50%;
    border: 5px solid #dadadb;
    border-radius: 50%;
    top: 50%;
    left: 50%;
    z-index: 9999;
}
.circle-progress::before {
  content: "";
  position: absolute;
  height: 40px;
  width: 40px;
  border-radius: 50%;
  border: 5px solid transparent;
  border-top-color: #2c0069;
  top: -5px;
  left: -5px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}
</style>
</div>

<?php if($logged_in_customer_id != ''){ ?>
  <div class = "page-width">
<?php } ?>
<div
  style="background-color: #f0f2f5; color: rgb(32, 34, 35); --p-background:rgba(246, 246, 247, 1); --p-background-hovered:rgba(241, 242, 243, 1); --p-background-pressed:rgba(237, 238, 239, 1); --p-background-selected:rgba(237, 238, 239, 1); --p-surface:rgba(255, 255, 255, 1); --p-surface-neutral:rgba(228, 229, 231, 1); --p-surface-neutral-hovered:rgba(219, 221, 223, 1); --p-surface-neutral-pressed:rgba(201, 204, 208, 1); --p-surface-neutral-disabled:rgba(241, 242, 243, 1); --p-surface-neutral-subdued:rgba(246, 246, 247, 1); --p-surface-subdued:rgba(250, 251, 251, 1); --p-surface-disabled:rgba(250, 251, 251, 1); --p-surface-hovered:rgba(246, 246, 247, 1); --p-surface-pressed:rgba(241, 242, 243, 1); --p-surface-depressed:rgba(237, 238, 239, 1); --p-backdrop:rgba(0, 0, 0, 0.5); --p-overlay:rgba(255, 255, 255, 0.5); --p-shadow-from-dim-light:rgba(0, 0, 0, 0.2); --p-shadow-from-ambient-light:rgba(23, 24, 24, 0.05); --p-shadow-from-direct-light:rgba(0, 0, 0, 0.15); --p-hint-from-direct-light:rgba(0, 0, 0, 0.15); --p-surface-search-field:rgba(241, 242, 243, 1); --p-border:rgba(140, 145, 150, 1); --p-border-neutral-subdued:rgba(186, 191, 195, 1); --p-border-hovered:rgba(153, 158, 164, 1); --p-border-disabled:rgba(210, 213, 216, 1); --p-border-subdued:rgba(201, 204, 207, 1); --p-border-depressed:rgba(87, 89, 89, 1); --p-border-shadow:rgba(174, 180, 185, 1); --p-border-shadow-subdued:rgba(186, 191, 196, 1); --p-divider:rgba(225, 227, 229, 1); --p-icon:rgba(92, 95, 98, 1); --p-icon-hovered:rgba(26, 28, 29, 1); --p-icon-pressed:rgba(68, 71, 74, 1); --p-icon-disabled:rgba(186, 190, 195, 1); --p-icon-subdued:rgba(140, 145, 150, 1); --p-text:rgba(32, 34, 35, 1); --p-text-disabled:rgba(140, 145, 150, 1); --p-text-subdued:rgba(109, 113, 117, 1); --p-interactive:rgba(44, 110, 203, 1); --p-interactive-disabled:rgba(189, 193, 204, 1); --p-interactive-hovered:rgba(31, 81, 153, 1); --p-interactive-pressed:rgba(16, 50, 98, 1); --p-focused:rgba(69, 143, 255, 1); --p-surface-selected:rgba(242, 247, 254, 1); --p-surface-selected-hovered:rgba(237, 244, 254, 1); --p-surface-selected-pressed:rgba(229, 239, 253, 1); --p-icon-on-interactive:rgba(255, 255, 255, 1); --p-text-on-interactive:rgba(255, 255, 255, 1); --p-action-secondary:rgba(255, 255, 255, 1); --p-action-secondary-disabled:rgba(255, 255, 255, 1); --p-action-secondary-hovered:rgba(246, 246, 247, 1); --p-action-secondary-pressed:rgba(241, 242, 243, 1); --p-action-secondary-depressed:rgba(109, 113, 117, 1); --p-action-primary:rgba(0, 128, 96, 1); --p-action-primary-disabled:rgba(241, 241, 241, 1); --p-action-primary-hovered:rgba(0, 110, 82, 1); --p-action-primary-pressed:rgba(0, 94, 70, 1); --p-action-primary-depressed:rgba(0, 61, 44, 1); --p-icon-on-primary:rgba(255, 255, 255, 1); --p-text-on-primary:rgba(255, 255, 255, 1); --p-text-primary:rgba(0, 123, 92, 1); --p-text-primary-hovered:rgba(0, 108, 80, 1); --p-text-primary-pressed:rgba(0, 92, 68, 1); --p-surface-primary-selected:rgba(241, 248, 245, 1); --p-surface-primary-selected-hovered:rgba(179, 208, 195, 1); --p-surface-primary-selected-pressed:rgba(162, 188, 176, 1); --p-border-critical:rgba(253, 87, 73, 1); --p-border-critical-subdued:rgba(224, 179, 178, 1); --p-border-critical-disabled:rgba(255, 167, 163, 1); --p-icon-critical:rgba(215, 44, 13, 1); --p-surface-critical:rgba(254, 211, 209, 1); --p-surface-critical-subdued:rgba(255, 244, 244, 1); --p-surface-critical-subdued-hovered:rgba(255, 240, 240, 1); --p-surface-critical-subdued-pressed:rgba(255, 233, 232, 1); --p-surface-critical-subdued-depressed:rgba(254, 188, 185, 1); --p-text-critical:rgba(215, 44, 13, 1); --p-action-critical:rgba(216, 44, 13, 1); --p-action-critical-disabled:rgba(241, 241, 241, 1); --p-action-critical-hovered:rgba(188, 34, 0, 1); --p-action-critical-pressed:rgba(162, 27, 0, 1); --p-action-critical-depressed:rgba(108, 15, 0, 1); --p-icon-on-critical:rgba(255, 255, 255, 1); --p-text-on-critical:rgba(255, 255, 255, 1); --p-interactive-critical:rgba(216, 44, 13, 1); --p-interactive-critical-disabled:rgba(253, 147, 141, 1); --p-interactive-critical-hovered:rgba(205, 41, 12, 1); --p-interactive-critical-pressed:rgba(103, 15, 3, 1); --p-border-warning:rgba(185, 137, 0, 1); --p-border-warning-subdued:rgba(225, 184, 120, 1); --p-icon-warning:rgba(185, 137, 0, 1); --p-surface-warning:rgba(255, 215, 157, 1); --p-surface-warning-subdued:rgba(255, 245, 234, 1); --p-surface-warning-subdued-hovered:rgba(255, 242, 226, 1); --p-surface-warning-subdued-pressed:rgba(255, 235, 211, 1); --p-text-warning:rgba(145, 106, 0, 1); --p-border-highlight:rgba(68, 157, 167, 1); --p-border-highlight-subdued:rgba(152, 198, 205, 1); --p-icon-highlight:rgba(0, 160, 172, 1); --p-surface-highlight:rgba(164, 232, 242, 1); --p-surface-highlight-subdued:rgba(235, 249, 252, 1); --p-surface-highlight-subdued-hovered:rgba(228, 247, 250, 1); --p-surface-highlight-subdued-pressed:rgba(213, 243, 248, 1); --p-text-highlight:rgba(52, 124, 132, 1); --p-border-success:rgba(0, 164, 124, 1); --p-border-success-subdued:rgba(149, 201, 180, 1); --p-icon-success:rgba(0, 127, 95, 1); --p-surface-success:rgba(174, 233, 209, 1); --p-surface-success-subdued:rgba(241, 248, 245, 1); --p-surface-success-subdued-hovered:rgba(236, 246, 241, 1); --p-surface-success-subdued-pressed:rgba(226, 241, 234, 1); --p-text-success:rgba(0, 128, 96, 1); --p-decorative-one-icon:rgba(126, 87, 0, 1); --p-decorative-one-surface:rgba(255, 201, 107, 1); --p-decorative-one-text:rgba(61, 40, 0, 1); --p-decorative-two-icon:rgba(175, 41, 78, 1); --p-decorative-two-surface:rgba(255, 196, 176, 1); --p-decorative-two-text:rgba(73, 11, 28, 1); --p-decorative-three-icon:rgba(0, 109, 65, 1); --p-decorative-three-surface:rgba(146, 230, 181, 1); --p-decorative-three-text:rgba(0, 47, 25, 1); --p-decorative-four-icon:rgba(0, 106, 104, 1); --p-decorative-four-surface:rgba(145, 224, 214, 1); --p-decorative-four-text:rgba(0, 45, 45, 1); --p-decorative-five-icon:rgba(174, 43, 76, 1); --p-decorative-five-surface:rgba(253, 201, 208, 1); --p-decorative-five-text:rgba(79, 14, 31, 1); --p-border-radius-base:0.4rem; --p-border-radius-wide:0.8rem; --p-border-radius-full:50%; --p-card-shadow:0px 0px 5px var(--p-shadow-from-ambient-light), 0px 1px 2px var(--p-shadow-from-direct-light); --p-popover-shadow:-1px 0px 20px var(--p-shadow-from-ambient-light), 0px 1px 5px var(--p-shadow-from-direct-light); --p-modal-shadow:0px 26px 80px var(--p-shadow-from-dim-light), 0px 0px 1px var(--p-shadow-from-dim-light); --p-top-bar-shadow:0 2px 2px -1px var(--p-shadow-from-direct-light); --p-button-drop-shadow:0 1px 0 rgba(0, 0, 0, 0.05); --p-button-inner-shadow:inset 0 -1px 0 rgba(0, 0, 0, 0.2); --p-button-pressed-inner-shadow:inset 0 1px 0 rgba(0, 0, 0, 0.15); --p-override-none:none; --p-override-transparent:transparent; --p-override-one:1; --p-override-visible:visible; --p-override-zero:0; --p-override-loading-z-index:514; --p-button-font-weight:500; --p-non-null-content:''; --p-choice-size:2rem; --p-icon-size:1rem; --p-choice-margin:0.1rem; --p-control-border-width:0.2rem; --p-banner-border-default:inset 0 0.1rem 0 0 var(--p-border-neutral-subdued), inset 0 0 0 0.1rem var(--p-border-neutral-subdued); --p-banner-border-success:inset 0 0.1rem 0 0 var(--p-border-success-subdued), inset 0 0 0 0.1rem var(--p-border-success-subdued); --p-banner-border-highlight:inset 0 0.1rem 0 0 var(--p-border-highlight-subdued), inset 0 0 0 0.1rem var(--p-border-highlight-subdued); --p-banner-border-warning:inset 0 0.1rem 0 0 var(--p-border-warning-subdued), inset 0 0 0 0.1rem var(--p-border-warning-subdued); --p-banner-border-critical:inset 0 0.1rem 0 0 var(--p-border-critical-subdued), inset 0 0 0 0.1rem var(--p-border-critical-subdued); --p-badge-mix-blend-mode:luminosity; --p-thin-border-subdued:0.1rem solid var(--p-border-subdued); --p-text-field-spinner-offset:0.2rem; --p-text-field-focus-ring-offset:-0.4rem; --p-text-field-focus-ring-border-radius:0.7rem; --p-button-group-item-spacing:-0.1rem; --p-duration-1-0-0:100ms; --p-duration-1-5-0:150ms; --p-ease-in:cubic-bezier(0.5, 0.1, 1, 1); --p-ease:cubic-bezier(0.4, 0.22, 0.28, 1); --p-range-slider-thumb-size-base:1.6rem; --p-range-slider-thumb-size-active:2.4rem; --p-range-slider-thumb-scale:1.5; --p-badge-font-weight:400; --p-frame-offset:0px;"
>

<?php
