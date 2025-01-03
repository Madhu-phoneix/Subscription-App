<?php
// header("Content-Type: application/liquid");
include("../header.php");
$pageCSS = '';
?>
<div class="Polaris-Layout">
   <?php
   if($logged_in_customer_id == ''){
      $whereCustomerCondition  = '';
      include("../navigation.php");
    }else{
      header("Content-Type: application/liquid");
      $pageCSS = 'style="padding: 28px 66px 50px 63px;"';
    }
    $contractId = $_GET['contract_id'];
    $whereCondition = array(
     'contract_id'=>$contractId,
     'store_id' => $mainobj->store_id
    );
    $fields = array(
      'new_contract' => '0'
    );
    $mainobj->update_row('subscriptionOrderContract',$fields,$whereCondition,'and');
   include("../navigation.php");
   //get payment method token
   $contractMethodToken = $mainobj->getContractPaymentToken($contractId);
   $paymentMethodToken =  substr($contractMethodToken['data']['subscriptionContract']['customerPaymentMethod']['id'], strrpos($contractMethodToken['data']['subscriptionContract']['customerPaymentMethod']['id'], '/') + 1);

   // echo $paymentMethodToken; die;
   $getContractData_qry = "SELECT p.payment_instrument_value,p.payment_method_token,o.next_billing_date,o.order_no,o.order_id,o.created_at,o.updated_at,o.contract_status,o.contract_products,o.delivery_policy_value,o.billing_policy_value,o.delivery_billing_type,a.first_name as shipping_first_name,a.last_name as shipping_last_name,a.address1 as shipping_address1,a.address2 as shipping_address2,a.city as shipping_city,a.province as shipping_province,a.country as shipping_country,a.company as shipping_company,a.phone as shipping_phone,a.province_code as shipping_province_code,a.country_code as shipping_country_code,a.zip as shipping_zip,a.delivery_method as shipping_delivery_method,a.delivery_price as shipping_delivery_price,b.first_name as billing_first_name,b.last_name as billing_last_name,b.address1 as billing_address1,b.address2 as billing_address2,b.city as billing_city,b.province as billing_province,b.country as billing_country,b.company as billing_company,b.phone as billing_phone,b.province_code as billing_province_code,b.country_code as billing_country_code,b.zip as billing_zip,d.currency,d.currencyCode,d.store_email,d.shop_timezone,c.name,c.email,c.shopify_customer_id from subscriptionOrderContract as o
   INNER JOIN contract_setting as contract_settng ON o.store_id = contract_settng.store_id
   INNER JOIN subscriptionContractShippingAddress as a ON o.contract_id = a.contract_id
   INNER JOIN subscriptionContractBillingAddress as b ON o.contract_id = b.contract_id
   INNER JOIN customers as c ON c.shopify_customer_id = o.shopify_customer_id
   INNER JOIN store_details as d ON d.store_id = a.store_id
   INNER JOIN customerContractPaymentmethod AS p ON p.store_id = o.store_id where o.contract_id = '$contractId' and p.payment_method_token = '$paymentMethodToken'";
   // die;
   $getContractData = $mainobj->customQuery($getContractData_qry);
   //  echo "<pre>";
   //  print_r($getContractData);
   // die;
   if (empty($getContractData)) {
      $redirectPage = $mainobj->SHOPIFY_DOMAIN_URL . "/admin/subscription/subscriptions.php?shop=" . $mainobj->store;
      echo "<script>window.top.location.href='" . $redirectPage . "'</script>";
      die;
   }
   $updated_at_date = $getContractData[0]['updated_at'];
   $date_time_array = explode(' ', $updated_at_date);
   //  if($mainobj->store == 'testing-neha-subscription.myshopify.com'){
   //    echo '<pre>';
   //    print_r($date_time_array);
   //    die;
   //   }

   $shop_timezone = $getContractData[0]['shop_timezone'];
   $orderContractProducts = $getContractData[0]['contract_products'];
   $orderContractProducts_array = explode(',', $orderContractProducts);
   $lastOrderId = $getContractData[0]['order_id'];
   //get billing Attempts data from billingAttempts table of current contract
   $whereCondition = array(
      'contract_id' => $contractId
   );
   $get_billing_Attempts = $mainobj->table_row_value('billingAttempts', 'all', $whereCondition, 'and', '');

   if (!empty($get_billing_Attempts)) {
      // pending order array start
      $pendingStatus = 'Pending';
      $pendingStatusArray = array_filter($get_billing_Attempts, function ($item) use ($pendingStatus) {
         if ($item['status'] == $pendingStatus) {
            return true;
         }
         return false;
      });

      // failure order array start
      $failureStatus = 'Failure';
      $failureStatusArray = array_filter($get_billing_Attempts, function ($item) use ($failureStatus) {
         if ($item['status'] == $failureStatus) {
            return true;
         }
         return false;
      });

      // skip order array start
      $skipStatus = 'Skip';
      $skipStatusArray = array_values(array_filter($get_billing_Attempts, function ($item) use ($skipStatus) {
         if ($item['status'] == $skipStatus) {
            return true;
         }
         return false;
      }));

      // Success order array start
      $successStatus = 'Success';
      $successStatusArray = array_values(array_filter($get_billing_Attempts, function ($item) use ($successStatus) {
         if ($item['status'] == $successStatus) {
            return true;
         }
         return false;
      }));
      if (!empty($successStatusArray)) {
         $lastOrderId = $successStatusArray[0]['order_id'];
      }
   }

   $payment_instrument_value = json_decode($getContractData[0]['payment_instrument_value']);
   $dateObj   = DateTime::createFromFormat('!m', $payment_instrument_value->month);
   $monthName = $dateObj->format('F'); // March

   if ($getContractData[0]['contract_status'] == 'A') {
      $buttonClass = 'Polaris-Button--destructive';
      $statusChangeTo = 'PAUSED';
      $buttonText = 'Pause';
      $currentStatus = 'Active';
      $next_bill_date = $getContractData[0]['next_billing_date'];
      $contract_status_info = 'statusSuccess';
      $addCircle = '<span class="Polaris-Badge__Pip"><span class="Polaris-VisuallyHidden"></span></span>';
   } else {
      $buttonClass = 'Polaris-Button--primary';
      $statusChangeTo = 'ACTIVE';
      $buttonText = 'Active';
      $currentStatus = 'Pause';
      $next_bill_date = '-';
      $contract_status_info = 'statusAttention';
      $addCircle = '';
   }
   $addedVariantsArray = [];
   $whereCondition = array(
      'contract_id' => $contractId,
      'store_id' => $mainobj->store_id
   );
   $store_all_subscription_plans = $mainobj->table_row_value('subscritionOrderContractProductDetails', 'all', $whereCondition, 'and', '');
   //   echo $mainobj->store_id;

   //   echo "<pre>";
   //   print_r($store_all_subscription_plans);
   //   die;
   $subscription_price = [];
   $all_contract_products = [];
   $variant_string = '';
   $variant_ids_array = [];
   foreach ($store_all_subscription_plans as $key => $value) {
      if ($value['product_contract_status'] == '1') {
         $itemPrice = ($value['subscription_price'] * $value['quantity']);
         array_push($subscription_price, $itemPrice);
         array_push($all_contract_products, $value);
         if($value['product_shopify_status'] == 'Active'){
             $variant_string .= ',"gid://shopify/ProductVariant/'.$value['variant_id'].'"';
         }
      }
   }
   if($variant_string != ''){
      $variant_string = substr($variant_string, 1); // remove leading ","
      $variantIds_string = '['.$variant_string.']';
      // if($mainobj->store == 'testing-neha-subscription.myshopify.com'){
      //    echo $variantIds_string;
      // }
      $variantDetails = $mainobj->getVariantDetail($variantIds_string); // get variant inventory quantity from api
      $variant_ids_array = $variantDetails['data']['nodes'];
   }

   $addedVariantsArray = array_column($all_contract_products, 'variant_id');
   $total_subscription_product_price = array_sum($subscription_price);
   $total_subscription_price = $total_subscription_product_price * ($getContractData[0]['billing_policy_value'] / $getContractData[0]['delivery_policy_value']);
   echo "<script>disabled_product_variant_array =" . json_encode($addedVariantsArray) . ";</script>";
   $tickMarkSvg = '<svg version="1.2" viewBox="0 0 436 434"><path fill-rule="evenodd" class="s0" d="m371.5 63.5c40.7 40.7 63.6 95.9 63.6 153.5c0 42.9-12.8 84.9-36.6 120.6c-23.9 35.7-57.8 63.5-97.4 79.9c-39.7 16.5-83.3 20.8-125.4 12.4c-42.2-8.4-80.8-29-111.2-59.4c-30.3-30.4-51-69-59.4-111.1c-8.4-42.2-4.1-85.8 12.4-125.5c16.4-39.6 44.2-73.5 79.9-97.4c35.7-23.8 77.7-36.6 120.6-36.6c57.6 0.1 112.8 22.9 153.5 63.6zm-47.3 102.3c1-2.5 1.5-5.1 1.5-7.8c-0.1-2.7-0.7-5.3-1.8-7.8c-1.1-2.4-2.7-4.6-4.7-6.4c-3.7-3.7-8.7-5.7-13.9-5.7c-5.2 0-10.2 2-13.9 5.7l-103.2 103.4l-44.8-44.8c-3.7-3.7-8.7-5.7-13.9-5.7c-5.2 0-10.2 2-13.9 5.7c-1.9 1.8-3.3 4-4.3 6.4c-1 2.4-1.5 5-1.5 7.6c0 2.6 0.5 5.2 1.5 7.6c1 2.4 2.4 4.6 4.3 6.4l59.2 59.2c1.8 1.8 4 3.3 6.4 4.3c2.4 1 5 1.5 7.6 1.4c5.2 0 10.1-2 13.8-5.7l117.2-117.2c1.9-1.9 3.4-4.1 4.4-6.6z"/></svg>';
   ?>
   <div class="Polaris-Layout__Section sd-dashboard-page" <?php echo $pageCSS; ?>>
      <input type="hidden" value="<?php echo $contractId; ?>" id="sd_contractId">
      <input type="hidden" value='<?php echo $getContractData[0]['discount'] . '_' . $getContractData[0]['discount_type']; ?>' id="sd_productDiscount">
      <input type="hidden" value="<?php echo $getContractData[0]['billing_policy_value']; ?>" id="contract_billingCycle">
      <input type="hidden" value="<?php echo $getContractData[0]['delivery_policy_value']; ?>" id="contract_deliveryCycle">
      <!-- <input type="hidden" value="<?php //echo $store_all_subscription_plans[0]['selling_plan_id']; ?>" id="sd_sellingPlanId"> -->
      <div>
         <div class="Polaris-Page-Header Polaris-Page-Header--isSingleRow Polaris-Page-Header--mobileView Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
            <div class="Polaris-Page-Header__Row sd_contractNavigation">
               <div class="Polaris-Page-Header__BreadcrumbWrapper sd_contractHeader">
                  <nav role="navigation">
                     <a class="Polaris-Breadcrumbs__Breadcrumb  navigate_element" data-usecase="" data-heading="Unsaved changes" data-query-string="" data-message="If you leave this page, any unsaved changes will be lost." data-acceptbuttontext="" data-rejectbuttontext="" data-confirmbox="" data-redirect-link="subscription/subscriptionContract.php" href="javascript:void(0)" data-polaris-unstyled="true">
                        <span class="Polaris-Breadcrumbs__ContentWrapper">
                           <span class="Polaris-Breadcrumbs__Icon">
                              <span class="Polaris-Icon">
                                 <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                    <path d="M17 9H5.414l3.293-3.293a.999.999 0 1 0-1.414-1.414l-5 5a.999.999 0 0 0 0 1.414l5 5a.997.997 0 0 0 1.414 0 .999.999 0 0 0 0-1.414L5.414 11H17a1 1 0 1 0 0-2z"></path>
                                 </svg>
                              </span>
                           </span>
                        </span>
                     </a>
                  </nav>
               </div>
               <div class="Polaris-Page-Header__TitleWrapper sd_contractBar">
                  <div>
                     <div class="Polaris-Banner Polaris-Banner--statusSuccess Polaris-Banner--hasDismiss Polaris-Banner--withinPage sd_contractStatus" tabindex="0" role="status" aria-live="polite" aria-labelledby="PolarisBanner18Heading" aria-describedby="PolarisBanner18Content">
                        <div class="Polaris-Banner__ContentWrapper">
                           <div class="Polaris-Banner__Heading" id="PolarisBanner18Heading">
                              <p class="Polaris-Heading">Your Subscription is on <?php echo $currentStatus; ?> Mode</p> <button class="Polaris-Button sd_updateSubscriptionStatus <?php echo $buttonClass; ?>" id="sd_activePauseSubscription" data-nextBillingDate = "<?php echo $getContractData[0]['next_billing_date'] ?>" data-subscriptionStatus="<?php echo $statusChangeTo; ?>" data-buttonText="<?php echo $buttonText; ?>"  type="button"><?php echo $buttonText; ?></button>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <?php if (!empty($getContractData)) { ?>
            <div class="Polaris-Page__Content">

               <div class="Polaris-Layout">
                  <div class="Polaris-Layout__Section sd_contracts" style="display: flex;">
                     <!-- <div class="Polaris-Layout__Section Polaris-Layout__Section--secondary">  -->
                     <div class="sd_contracts-info">
                        <div class="Polaris-Card">
                           <div class="Polaris-Card__Header">
                              <h2 class="Polaris-Heading">Subscription #<?php echo $contractId; ?></h2>
                              <div class="sd_customerDetail">
                                 <div><span class="Polaris-Badge Polaris-Badge--<?php echo $contract_status_info; ?> Polaris-Badge--progressComplete"><?php echo $addCircle . '' . $currentStatus; ?></span>
                                    <div id="PolarisPortalsContainer"></div>
                                 </div>
                                 <p class="cust-name"><a href="https://<?php echo $mainobj->store; ?>/admin/customers/<?php echo $getContractData[0]['shopify_customer_id']; ?>" target="_blank"><?php echo $getContractData[0]['name']; ?></a></p>
                                 <p class="price-text"><?php echo $getContractData[0]['currencyCode'] . ' ' . number_format($total_subscription_price, 2); ?></p>
                                 <p class="date">Created At <?php echo $mainobj->getShopTimezoneDate($getContractData[0]['created_at'], $shop_timezone); ?></p>
                                 <div class="sd_subscriptionPrc">Recurring total<div class="sd_recurringMsg" onmouseover="show_title(this)" onmouseout="hide_title(this)"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20px">
                                          <path fill-rule="evenodd" d="M11 11H9v-.148c0-.876.306-1.499 1-1.852.385-.195 1-.568 1-1a1.001 1.001 0 00-2 0H7c0-1.654 1.346-3 3-3s3 1 3 3-2 2.165-2 3zm-2 4h2v-2H9v2zm1-13a8 8 0 100 16 8 8 0 000-16z" fill="#5C5F62" /></svg></div>
                                    <div class="Polaris-PositionedOverlay display-hide-label">
                                       <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                          <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">Does not include shipping,tax,duties or any applicable discount</div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="Polaris-Card__Section">
                              <div class="sd_orderDetail">
                                 <p><?php echo $tickMarkSvg; ?>Delivery Frequency </p><b><?php echo $getContractData[0]['delivery_policy_value'] . ' ' . $getContractData[0]['delivery_billing_type']; ?></b>
                              </div>
                              <div class="sd_orderDetail">
                                 <p><?php echo $tickMarkSvg; ?>Billing Frequency </p><b><?php echo $getContractData[0]['billing_policy_value'] . ' ' . $getContractData[0]['delivery_billing_type']; ?></b>
                              </div>
                              <div class="sd_orderDetail">
                                 <p><?php echo $tickMarkSvg; ?>Plan Type </p><b> <?php if ($getContractData[0]['delivery_policy_value'] == $getContractData[0]['billing_policy_value']) {
                                    echo "Pay Per Delivery";
                                 } else {
                                    echo "Prepaid";
                                 } ?></b>
                              </div>
                              <div class="sd_orderDetail">
                                 <p><?php echo $tickMarkSvg; ?>Next Order Date </p><b><?php if ($currentStatus == 'Active') {
                                    echo $mainobj->getShopTimezoneDate(($next_bill_date . ' ' . $date_time_array[1]), $shop_timezone);
                                 // echo $next_bill_date;
                                 } else {
                                    echo '-';
                                 } ?></b>
                              </div>
                              <div class="sd_orderDetail">
                                 <p><?php echo $tickMarkSvg; ?>Order No. </p><b>#<?php echo $getContractData[0]['order_no']; ?></b>
                              </div>
                              <div class="sd_pauseCancelBtn">
                                 <button class="Polaris-Button Polaris-Button--destructive sd_updateSubscriptionStatus remove-btn" type="button" id="sd_cancelSubscription" data-subscriptionstatus='EXPIRED' data-buttonText="<?php echo $buttonText; ?>" data-billingPolicy="" data-billingType="">Delete</button>
                                 <div id="PolarisPortalsContainer"></div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="sd_contractdetail">
                        <div class="Polaris-Card sd_contractCard">
                           <div>
                              <div class="Polaris-Tabs__Wrapper">
                                 <ul role="tablist" class="Polaris-Tabs">
                                    <li class="Polaris-Tabs__TabContainer" role="presentation"><button group="sd_contractTabs" role="tab" type="button" tabindex="0" class="sd_contractTabs-title Polaris-Tabs__Tab sd_Tabs Polaris-Tabs__Tab--selected" aria-selected="true" id="sd_contractProducts" target-tab="sd_contractProducts_content"><span class="Polaris-Tabs__Title">Products</span></button></li>
                                    <li class="Polaris-Tabs__TabContainer" role="presentation"><button id="sd_contractShippingAddress" group="sd_contractTabs" role="tab" type="button" tabindex="-1" class="sd_contractTabs-title Polaris-Tabs__Tab sd_Tabs" aria-selected="false" target-tab="sd_contractShippingAddress_content"><span class="Polaris-Tabs__Title">Shipping and Billing Address</span></button></li>
                                    <li class="Polaris-Tabs__TabContainer" role="presentation"><button id="sd_contractPaymentDetails" group="sd_contractTabs" role="tab" type="button" tabindex="-1" class="sd_contractTabs-title Polaris-Tabs__Tab sd_Tabs" aria-selected="false" target-tab="sd_contractPaymentDetails_content"><span class="Polaris-Tabs__Title">Payment Details</span></button></li>
                                    </li>
                                 </ul>
                              </div>
                              <div class="sd_contractTabs Polaris-Tabs__Panel" id="sd_contractProducts_content" role="tabpanel">
                                 <!-- Product Tab content start here -->
                                 <div class="Polaris-Card__Section sd_productListing">
                                    <button class="Polaris-Button Polaris-Button--primary add_newProducts" type="button" product-display-style="prdouct-box" parent-id=''><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                          <path d="M15 10a1 1 0 01-1 1h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 012 0v3h3a1 1 0 011 1zm-5-8a8 8 0 100 16 8 8 0 000-16z" fill="#5C5F62"></path>
                                       </svg>Add Products</button>
                                    <div>
                                       <div class="Polaris-Card">
                                          <div id="sd_slider_1" class="sd_main_Slider">
                                             <?php if (!empty($all_contract_products)) {

                                                $slider_number = 1;
                                                foreach ($all_contract_products as $key => $prdValue) {

                                                   $edit_product_quantity = '';$add_stock_link = '';
                                                   $variant_id = $prdValue['variant_id'];
                                                   $found_variant_key = array_search('gid://shopify/ProductVariant/'.$variant_id, array_column($variant_ids_array, 'id'));

                                                   // if($mainobj->store == 'testing-neha-subscription.myshopify.com'){
                                                   //    echo '============search variant id===============';
                                                   //    echo $variant_id;
                                                   //    echo '============search variant id===============<br>';
                                                   //     echo "=========variant_ids_array===============";
                                                   //     echo '<pre>';
                                                   //     print_r($variant_ids_array);
                                                   //     echo "=========variant_ids_array end===============";

                                                   //    if(is_int($found_variant_key) && (strlen((string)$found_variant_key) > 0)){
                                                   //      echo 'found Key ='.$found_variant_key;
                                                   //    }
                                                   // }

                                                   if(is_int($found_variant_key) && (strlen((string)$found_variant_key) > 0) && (!empty($variant_ids_array[$found_variant_key]))){
                                                     $available_quantity = 'Stock '.$variant_ids_array[$found_variant_key]['inventoryQuantity'];
                                                      if($variant_ids_array[$found_variant_key]['inventoryQuantity'] > 0 || $variant_ids_array[$found_variant_key]['inventoryPolicy'] == 'CONTINUE'){
                                                      $edit_product_quantity = '<div class="Polaris-Button update_prdQuantity" data-product_id="'.$prdValue['variant_id'].'">
                                                      Edit</div>';
                                                     }else{
                                                      $add_stock_link = '<span class="Polaris-TextStyle--variationStrong"><a href="https://'.$mainobj->store.'/admin/products/'.$prdValue['product_id'].'/variants/'.$prdValue['variant_id'].'" target="_blank">Add Stock</a></span>';
                                                     }
                                                   }else{
                                                      $available_quantity = '';
                                                   }
                                                   if ($prdValue['variant_image'] == '0' || $prdValue['variant_image'] == '') {
                                                      $imageSrc = $mainobj->image_folder.'no-image.png';
                                                   } else {
                                                      $imageSrc = $prdValue['variant_image'];
                                                   }
                                                   if ($prdValue['variant_name'] == 'Default Title' || $prdValue['variant_name'] == '') {
                                                      // if($mainobj->store == 'testing-neha-subscription.myshopify.com'){
                                                      //    echo $prdValue['variant_name'].'<br>';
                                                      // }
                                                      $variant_title = '';
                                                   } else {
                                                      // if($mainobj->store == 'testing-neha-subscription.myshopify.com'){
                                                      //    echo $prdValue['variant_name'].'<br>';
                                                      // }
                                                      $variant_title = $prdValue['variant_name'];
                                                   }

                                                   if ($key == 0) {
                                                      echo "<div parent-slider='sd_slider_" . $slider_number . "' sd-slide-number='" . $slider_number . "' class='sd_slide_wrapper sd_active_slider'>";
                                                   } else  if ($key % 6 == 0) {
                                                      $slider_number++;
                                                      echo "</div><div parent-slider='sd_slider_" . $slider_number . "' sd-slide-number='" . $slider_number . "' class='sd_slide_wrapper'>";
                                                   }
                                             ?>
                                                   <div class="product_main_box_inner">
                                                      <?php if($prdValue['product_shopify_status'] == 'Deleted'){ echo 'Product Deleted from Shopify Store'; } ?>
                                                      <div class="product_main_box">
                                                         <div class="product_main_outer">
                                                            <div class="Polaris-Card__Header product_imgbox">
                                                               <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                                                                  <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                                                                     <div class="Polaris-ResourceItem__Media"><span role="img" class="Polaris-Avatar Polaris-Avatar--sizeMedium Polaris-Avatar--hasImage"><img src="<?php echo $imageSrc; ?>" class="Polaris-Avatar__Image" alt="" role="presentation"></span></div>
                                                                  </div>
                                                               </div>
                                                            </div>
                                                            <div class="Polaris-Card__Section">
                                                               <div class="sd_product-content">
                                                                  <div class="sd_productText" onmouseover="show_title(this)" onmouseout="hide_title(this)">
                                                                     <h2 class="Polaris-Heading"><span class="Polaris-TextStyle--variationStrong"><a href="https://<?php echo $store; ?>/admin/products/<?php echo $prdValue['product_id']; ?>" target="_blank"><?php echo $prdValue['product_name']; ?></a></span></h2>
                                                                  </div>
                                                                  <div class="Polaris-PositionedOverlay display-hide-label">
                                                                     <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                                                        <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content"><?php echo $prdValue['product_name']; ?></div>
                                                                     </div>
                                                                  </div>
                                                                  <div class="sd_productText" onmouseover="show_title(this)" onmouseout="hide_title(this)">
                                                                     <span><?php echo $variant_title; ?></span>
                                                                  </div>
                                                                  <div class="Polaris-PositionedOverlay display-hide-label">
                                                                     <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                                                        <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content"><?php echo $variant_title; ?></div>
                                                                     </div>
                                                                  </div>
                                                                  <div class="sd_prdQuantity">Quantity : <span class="sd_oldQuantity_<?php echo $prdValue['variant_id'] ?>"><?php echo $prdValue['quantity']; ?></span>
                                                                     <span class="sd_newQuantity_<?php echo $prdValue['variant_id'] ?> sd_newQuantity display-hide-label"><button type="button" class="sd_updateQuantity sd_qtyMinus" data-id="<?php echo $prdValue['variant_id'] ?>"><svg viewBox="0 0 20 20">
                                                                              <path d="M15 9H5a1 1 0 100 2h10a1 1 0 100-2z" fill="#5C5F62" /></svg></button><input type="number" min="1" value="<?php echo $prdValue['quantity']; ?>" class="product_qty" data-productName="<?php echo $prdValue['product_name'] . ' ' . $variant_title; ?>" id="product_qty_<?php echo $prdValue['variant_id'] ?>" width="50px;"><button type="button" class="sd_updateQuantity sd_qtyPlus" data-id="<?php echo $prdValue['variant_id'] ?>"><svg width="10" viewBox="0 0 12 12">
                                                                              <path d="M11 5H7V1a1 1 0 00-2 0v4H1a1 1 0 000 2h4v4a1 1 0 002 0V7h4a1 1 0 000-2z" fill="currentColor" fill-rule="nonzero"></path>
                                                                           </svg></button></span>
                                                                           <?php echo $available_quantity.' '.$add_stock_link; ?>
                                                                  </div>
                                                                  <p>Amount :<?php echo ($prdValue['subscription_price'] * $prdValue['quantity']) . ' ' . $getContractData[0]['currency']; ?></p>
                                                               </div>
                                                            </div>
                                                            <!-- show discount div here -->
                                                            <?php
                                                            if($prdValue['discount_value'] == 0 || $prdValue['discount_value'] == '' || $prdValue['discount_value'] == '0') { ?> <?php }else{?><div p-color-scheme="light"><span class="Polaris-Badge Polaris-Badge--statusInfo"><span class="Polaris-VisuallyHidden">Info </span>Saved <?php echo $prdValue['discount_value'].' ';  if($prdValue['discount_type'] == 'A'){echo $mainobj->currency; }else{echo "%"; }?></span>
                                                            <div id="PolarisPortalsContainer"></div>
                                                            </div><?php }?>
                                                             <!-- show discount div ends here -->
                                                         </div>
                                                         <div class="sd_editProduct sd_updateProductButton_<?php echo $prdValue['variant_id'] ?>">
                                                            <?php
                                                            echo $edit_product_quantity;
                                                             if (count($all_contract_products) > 1) { ?>
                                                               <div class="Polaris-Button remove_product" data-line_id="<?php echo $prdValue['contract_line_item_id']; ?>" data-product_id="<?php echo $prdValue['variant_id'] ?>">
                                                                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="15px">
                                                                     <path fill-rule="evenodd" d="M14 4h3a1 1 0 011 1v1H2V5a1 1 0 011-1h3V1.5A1.5 1.5 0 017.5 0h5A1.5 1.5 0 0114 1.5V4zM8 2v2h4V2H8zM3 8h14v10.5a1.5 1.5 0 01-1.5 1.5h-11A1.5 1.5 0 013 18.5V8zm4 3H5v6h2v-6zm4 0H9v6h2v-6zm2 0h2v6h-2v-6z" fill="#5C5F62" /></svg>
                                                               </div>
                                                            <?php } ?>
                                                         </div>
                                                         <div class="sd_editProduct updateCancelBtn_<?php echo $prdValue['variant_id'] ?> display-hide-label">
                                                            <button class="Polaris-Button Polaris-Button--primary update_prdQty" data-line_id="<?php echo $prdValue['contract_line_item_id']; ?>" data-product_id="<?php echo $prdValue['variant_id'] ?>" type="button">Save</button>
                                                            <button class="Polaris-Button Polaris-Button--destructive cancel_prdUpdate" type="button" data-product_id="<?php echo $prdValue['variant_id'] ?>">Cancel</button>
                                                         </div>
                                                      </div>
                                                   </div><!-- wrapper end -->
                                             <?php }
                                             } ?>
                                          </div>
                                          <?php if (count($subscription_price) > 6) { ?>
                                             <div class="sd_slider_controls_wrapper">
                                                <span class="sd_slider_controls" parent-slider="sd_slider_1" attr-type="prev"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="25px">
                                                      <path d="M12 16a.997.997 0 01-.707-.293l-5-5a.999.999 0 010-1.414l5-5a.999.999 0 111.414 1.414L8.414 10l4.293 4.293A.999.999 0 0112 16z" fill="#5C5F62"></path>
                                                   </svg></span>
                                                <span><b id="sd_sliderNumber">1 </b> of <?php echo $slider_number; ?></span>
                                                <span class="sd_slider_controls" parent-slider="sd_slider_1" attr-type="next"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="25px">
                                                      <path d="M8 16a.999.999 0 01-.707-1.707L11.586 10 7.293 5.707a.999.999 0 111.414-1.414l5 5a.999.999 0 010 1.414l-5 5A.997.997 0 018 16z" fill="#5C5F62" /></svg></span>
                                             </div>
                                          <?php } ?>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <!--Product Tab content ends here-->
                           <div class="sd_contractTabs Polaris-Tabs__Panel Polaris-Tabs__Panel--hidden sd_contractShipAddress" id="sd_contractShippingAddress_content" role="tabpanel">
                              <!-- shipping address start -->
                              <div class="sd_shippingAndBilling">
                                 <div class="sd_contractBillingShipping">
                                    <div class="Polaris-Card__Section">
                                       <div class="sd_billingHeading">
                                          <h2 class="Polaris-Heading">Shipping Address</h2>
                                          <div id="PolarisPortalsContainer"></div>
                                       </div>
                                       <div class="sd_shippingAddressData" id="sd_shippingAddressData">
                                          <?php if (!empty($getContractData[0]['shipping_first_name'])) { ?>
                                             <p class="sd_shippingName"><?php echo $getContractData[0]['shipping_first_name'] . ' ' . $getContractData[0]['shipping_last_name']; ?></p>
                                             <p class="sd_shippingAddress"><?php echo $getContractData[0]['shipping_address1']; ?></p>
                                             <p class="sd_shippingCity"><?php echo $getContractData[0]['shipping_country'] . ' ' . ' ' . $getContractData[0]['shipping_city'] . ' ' . $getContractData[0]['shipping_province'] . '-' . $getContractData[0]['shipping_zip']; ?></p>
                                          <?php } else {
                                             echo 'No Shipping Address';
                                          } ?>
                                       </div>
                                    </div>
                                    <div class="Polaris-Card__Footer sd_updatePayment">
                                       <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                                          <div class="Polaris-Stack__Item">
                                             <div class="Polaris-ButtonGroup">
                                                <?php if (!empty($getContractData[0]['shipping_first_name'])) { ?>
                                                   <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain sd_shippingEditBtn"><button title="Edit" type="button" data-redirect-link="" data-query-string="" class="Polaris-Button sd_updateShippingAddress" id="sd_updateShippingAddress" data-contract_id="<?php echo $prdValue['contract_id'] ?>">Update</button></div>
                                                <?php } ?>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>

                                 <!-- billing address start -->
                                 <div class="sd_contractBillingShipping">
                                    <div class="Polaris-Card__Section">
                                       <div class="sd_billingHeading">
                                          <h2 class="Polaris-Heading">Billing Address</h2>
                                          <div id="PolarisPortalsContainer"></div>
                                       </div>
                                       <div class="sd_shippingAddressData">
                                          <p class="sd_shippingName"><?php echo $getContractData[0]['billing_first_name'] . ' ' . $getContractData[0]['billing_last_name']; ?></p>
                                          <p class="sd_shippingAddress"><?php echo $getContractData[0]['billing_address1']; ?></p>
                                          <p class="sd_shippingCity"><?php echo $getContractData[0]['billing_country'] . ' ' . ' ' . $getContractData[0]['billing_city'] . ' ' . $getContractData[0]['billing_province'] . '-' . $getContractData[0]['billing_zip']; ?></p>
                                       </div>
                                    </div>
                                 </div>
                              </div>

                              <div class="sd_shippingForm display-hide-label">
                                 <div class="Polaris-Card">
                                    <div class="Polaris-Card__Section">
                                       <form class="form-horizontal formShippingAddressSave" method="post" name="shippingAddressSave" id="shippingAddressSave">
                                          <input type="hidden" value="<?php echo $getContractData[0]['email']; ?>" name="sendMailToCustomer" id="sendMailToCustomer">
                                          <input type="hidden" value="<?php echo $getContractData[0]['store_email']; ?>" name="sendMailToAdmin" id="sendMailToAdmin">
                                          <div class="Polaris-FormLayout">
                                             <div role="group" class="Polaris-FormLayout--grouped">
                                                <div class="Polaris-FormLayout__Items">
                                                   <div class="Polaris-FormLayout__Item">
                                                      <div class="">
                                                         <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label"><label id="shipFirstNameLabel" for="shipFirstName" class="Polaris-Label__Text">First Name*</label></div>
                                                         </div>
                                                         <div class="Polaris-Connected">
                                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                               <div class="Polaris-TextField">
                                                                  <input id="shipFirstName" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField7Label" aria-invalid="false" data-text="First Name" value="<?php echo $getContractData[0]['shipping_first_name']; ?>" name="first_name" maxlength="255">
                                                                  <div class="Polaris-TextField__Backdrop"></div>
                                                               </div>
                                                               <span id="shipFirstName_error" class="shipping_address_error display-hide-label" style="color:red;"></span>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                   <div class="Polaris-FormLayout__Item">
                                                      <div class="">
                                                         <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label"><label id="shipLastNameLabel" for="shipLastName" class="Polaris-Label__Text">Last Name*</label></div>
                                                         </div>
                                                         <div class="Polaris-Connected">
                                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                               <div class="Polaris-TextField">
                                                                  <input id="shipLastName" data-text="Last Name" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField7Label" aria-invalid="false" value="<?php echo $getContractData[0]['shipping_last_name']; ?>" name="last_name" maxlength="255">
                                                                  <div class="Polaris-TextField__Backdrop"></div>
                                                               </div>
                                                            </div>
                                                         </div>
                                                         <span id="shipLastName_error" class="shipping_address_error display-hide-label" style="color:red;"></span>
                                                      </div>
                                                   </div>
                                                </div>
                                                <div class="Polaris-FormLayout__Items">
                                                   <div class="Polaris-FormLayout__Item">
                                                      <div class="">
                                                         <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label"><label id="shipPhoneLabel" for="shipPhone" class="Polaris-Label__Text">Phone No.*</label></div>
                                                         </div>
                                                         <div class="Polaris-Connected">
                                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                               <div class="Polaris-TextField">
                                                                  <input id="shipPhone" data-text="Phone Number" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField7Label" aria-invalid="false" value="<?php echo $getContractData[0]['shipping_phone']; ?>" name="phone" maxlength="255">
                                                                  <div class="Polaris-TextField__Backdrop"></div>
                                                               </div>
                                                            </div>
                                                         </div>
                                                         <span id="shipPhone_error" class="shipping_address_error display-hide-label" style="color:red;"></span>
                                                      </div>
                                                   </div>
                                                   <div class="Polaris-FormLayout__Item">
                                                      <div class="">
                                                         <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label"><label id="shipCityLabel" for="shipCity" class="Polaris-Label__Text">City*</label></div>
                                                         </div>
                                                         <div class="Polaris-Connected">
                                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                               <div class="Polaris-TextField">
                                                                  <input id="shipCity" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField7Label" aria-invalid="false" data-text="City" value="<?php echo $getContractData[0]['shipping_city']; ?>" name="city" maxlength="255">
                                                                  <div class="Polaris-TextField__Backdrop"></div>
                                                               </div>
                                                            </div>
                                                         </div>
                                                         <span id="shipCity_error" class="shipping_address_error display-hide-label" style="color:red;"></span>
                                                      </div>
                                                   </div>
                                                </div>
                                                <div class="Polaris-FormLayout__Items">
                                                   <div class="Polaris-FormLayout__Item">
                                                      <div class="">
                                                         <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label"><label id="shipAddressOneLabel" for="shipAddressOne" class="Polaris-Label__Text">Address 1*</label></div>
                                                         </div>
                                                         <div class="Polaris-Connected">
                                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                               <div class="Polaris-TextField">
                                                                  <input id="shipAddressOne" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField7Label" aria-invalid="false" data-text="Address1" value="<?php echo $getContractData[0]['shipping_address1']; ?>" name="address1" maxlength="255">
                                                                  <div class="Polaris-TextField__Backdrop"></div>
                                                               </div>
                                                            </div>
                                                         </div>
                                                         <span id="shipAddressOne_error" class="shipping_address_error display-hide-label" style="color:red;"></span>
                                                      </div>
                                                   </div>
                                                   <div class="Polaris-FormLayout__Item">
                                                      <div class="">
                                                         <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label"><label id="shipAddressTwoLabel" for="shipAddressTwo" class="Polaris-Label__Text">Address2</label></div>
                                                         </div>
                                                         <div class="Polaris-Connected">
                                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                               <div class="Polaris-TextField">
                                                                  <input id="shipAddressTwo" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField7Label" aria-invalid="false" value="<?php echo $getContractData[0]['shipping_address2']; ?>" name="address2" maxlength="255">
                                                                  <div class="Polaris-TextField__Backdrop"></div>
                                                               </div>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                </div>
                                                <div class="Polaris-FormLayout__Items">
                                                   <div class="Polaris-FormLayout__Item">
                                                      <div class="">
                                                         <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label"><label id="shipCountryLabel" for="shipCountry" class="Polaris-Label__Text">Country*</label></div>
                                                         </div>
                                                         <div class="Polaris-Connected"><select name="country" class="sd_shipCountry" onchange="getStates()" id="shipCountry" data-country="<?php echo  $getContractData[0]['shipping_country']; ?>"></select></div>
                                                      </div>
                                                   </div>
                                                   <div class="Polaris-FormLayout__Item">
                                                      <div class="">
                                                         <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label"><label id="shipProvinceLabel" for="shipProvince" class="Polaris-Label__Text">Province*</label></div>
                                                         </div>
                                                         <div class="Polaris-Connected"><select name="province" id="shipProvince" class="sd_shipProvince" data-province="<?php echo  $getContractData[0]['shipping_province']; ?>" maxlength="255"></select></div>
                                                      </div>
                                                   </div>
                                                </div>
                                                <div class="Polaris-FormLayout__Items">
                                                   <div class="Polaris-FormLayout__Item">
                                                      <div class="">
                                                         <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label"><label id="shipCompanyLabel" for="shipCompany" class="Polaris-Label__Text">Company</label></div>
                                                         </div>
                                                         <div class="Polaris-Connected">
                                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                               <div class="Polaris-TextField">
                                                                  <input id="shipCompany" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField7Label" aria-invalid="false" value="<?php echo $getContractData[0]['shipping_company']; ?>" name="company" maxlength="255">
                                                                  <div class="Polaris-TextField__Backdrop"></div>
                                                               </div>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                   <div class="Polaris-FormLayout__Item">
                                                      <div class="">
                                                         <div class="Polaris-Labelled__LabelWrapper">
                                                            <div class="Polaris-Label"><label id="shipZipLabel" for="shipZip" class="Polaris-Label__Text">Postal Code*</label></div>
                                                         </div>
                                                         <div class="Polaris-Connected">
                                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                               <div class="Polaris-TextField">
                                                                  <input id="shipZip" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField7Label" aria-invalid="false" data-text="Zip" value="<?php echo $getContractData[0]['shipping_zip']; ?>" name="zip" maxlength="255">
                                                                  <div class="Polaris-TextField__Backdrop"></div>
                                                               </div>
                                                            </div>
                                                         </div>
                                                         <span id="shipZip_error" class="shipping_address_error display-hide-label" style="color:red;"></span>
                                                      </div>
                                                   </div>
                                                </div>
                                       </form>
                                    </div>
                                 </div>
                                 <div class="Polaris-Card__Footer sd_updatePayment">
                                    <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                                       <div class="Polaris-Stack__Item">
                                          <div class="Polaris-ButtonGroup">
                                             <div class="sd_shippingSaveCancel display-hide-label">
                                                <button class="Polaris-Button Polaris-Button--primary sd_saveShippingAddress" id="sd_saveShippingAddress" data-contract_id="<?php echo $prdValue['contract_id'] ?>" type="button">Save</button>
                                                <button class="Polaris-Button sd_cancelShippingAddress" type="button">Cancel</button>
                                                <div id="PolarisPortalsContainer"></div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <!--Shipping Address Tab content ends here-->
                        </div>
                     </div>
                     <div id="PolarisPortalsContainer">
                        <div data-portal-id="popover-Polarisportal1"></div>
                     </div>
                     <div class="sd_contractTabs Polaris-Tabs__Panel Polaris-Tabs__Panel--hidden" id="sd_contractPaymentDetails_content" role="tabpanel">
                        <div class="sd_paymentDetail">
                           <div class="Polaris-Card__Section">
                              <div class="sd_contractPaymentDetails">
                                 <p><b><?php echo $getContractData[0]['name']; ?></b></p>
                                 <p><?php echo $payment_instrument_value->brand; ?></p>
                                 <p>Ending With <?php echo $payment_instrument_value->last_digits; ?></p>
                                 <p><?php echo $monthName . ' ' . $payment_instrument_value->year ?></p>
                              </div>
                           </div>
                           <div class="Polaris-Card__Footer sd_updatePayment">
                              <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                                 <div class="Polaris-Stack__Item">
                                    <div class="Polaris-ButtonGroup">
                                       <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain"><button title="Edit" type="button" class="Polaris-Button updatePaymentMethod" data-paymentToken="<?php echo $getContractData[0]['payment_method_token'] ?>" id="updatePaymentMethod">Update Details</button><button title="Edit" type="button" class="Polaris-Button display-hide-label" id="updateMailSent">Mail Sent</button></div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <!-- <div id="sd_payment_mail_status"></div> -->
                        </div>

                     </div>
                     <!-- payment details tab content ends here -->
                  </div>
               </div>
            </div>
      </div>

      <div class="Polaris-Layout__Section sd_contracts sd_contractDetails">
         <?php if ($getContractData[0]['contract_status'] == 'A') { ?>
            <div>
               <div class="Polaris-Card">
                  <div>
                     <div class="Polaris-Tabs__Wrapper">
                        <ul role="tablist" class="Polaris-Tabs">
                           <li class="Polaris-Tabs__TabContainer" role="presentation"><button group="sd_orderTabs" role="tab" type="button" tabindex="0" class="sd_orderTabs-title Polaris-Tabs__Tab Polaris-Tabs__Tab--selected sd_Tabs" aria-selected="true" target-tab="sd_pastOrders_content" aria-label="All customers"><span class="Polaris-Tabs__Title">Past Orders</span></button></li>
                           <li class="Polaris-Tabs__TabContainer" role="presentation"><button group="sd_orderTabs" role="tab" type="button" tabindex="-1" class="sd_orderTabs-title Polaris-Tabs__Tab sd_Tabs" aria-selected="false" target-tab="sd_upcomingFulfillments_content"><span class="Polaris-Tabs__Title"><?php if ($getContractData[0]['delivery_policy_value'] == $getContractData[0]['billing_policy_value']) {                                                                                                    echo "Upcoming Orders";
                           } else {
                              echo "Upcoming Fulfillments";
                           } ?></span></button></li>
                           <li class="Polaris-Tabs__TabContainer" role="presentation"><button group="sd_orderTabs" role="tab" type="button" tabindex="-1" class="sd_orderTabs-title Polaris-Tabs__Tab sd_Tabs" aria-selected="false" target-tab="sd_pendingOrders_content"><span class="Polaris-Tabs__Title">Pending Orders</span></button></li>
                           <li class="Polaris-Tabs__TabContainer" role="presentation"><button group="sd_orderTabs" role="tab" type="button" tabindex="-1" class="sd_orderTabs-title Polaris-Tabs__Tab sd_Tabs" aria-selected="false" target-tab="sd_failureOrders_content"><span class="Polaris-Tabs__Title">Failure Orders</span></button></li>
                           <li class="Polaris-Tabs__TabContainer" role="presentation"><button group="sd_orderTabs" role="tab" type="button" tabindex="-1" class="sd_orderTabs-title Polaris-Tabs__Tab sd_Tabs" aria-selected="false" target-tab="sd_skipOrders_content"><span class="Polaris-Tabs__Title"><?php if ($getContractData[0]['delivery_policy_value'] == $getContractData[0]['billing_policy_value']) {
                              echo " Skip Orders";
                              } else {
                                 echo "Rescheduled Fulfillments of current cycle";
                              } ?></span></button></li>
                        </ul>
                     </div>
                     <div class="sd_orderTabs Polaris-Tabs__Panel" id="sd_pastOrders_content" role="tabpanel" aria-labelledby="all-customers-1" tabindex="-1">
                        <div class="Polaris-Card__Section">
                           <!--Upcoming Orders or Fulfillments List here-->
                           <div class="Polaris-ResourceList__ResourceListWrapper">
                              <ul class="Polaris-ResourceList" aria-live="polite">
                                 <?php if (!empty($successStatusArray)) {
                                    foreach ($successStatusArray as $key => $itemValue) { ?>
                                       <li class="Polaris-ResourceItem__ListItem">
                                          <div class="Polaris-ResourceItem__ItemWrapper">
                                             <div class="Polaris-ResourceItem">
                                                <a class="Polaris-ResourceItem__Link" id="PolarisResourceListItemOverlay4" data-polaris-unstyled="true"></a>
                                                <div class="Polaris-ResourceItem__Container" id="145">
                                                   <div class="Polaris-ResourceItem__Content">
                                                      <?php $all_products_array = explode(",", $itemValue['contract_products']);
                                                      $variantHtml = '';
                                                      foreach ($all_products_array as $key => $prdValue) {
                                                         $variantTitle = '';
                                                         $item_index = array_search($prdValue, array_column($store_all_subscription_plans, 'variant_id'));
                                                         if (!empty($store_all_subscription_plans[$item_index]['variant_image'])) {
                                                            $productImage = $store_all_subscription_plans[$item_index]['variant_image'];
                                                         } else {
                                                            $productImage = $mainobj->SHOPIFY_DOMAIN_URL . '/backend/assets/images/no-image.png';
                                                         }
                                                         if($store_all_subscription_plans[$item_index]['variant_name'] != 'Default Title'){
                                                            $variantTitle = '-' . $store_all_subscription_plans[$item_index]['variant_name'];
                                                         }
                                                            if(trim($variantTitle == '-')){
                                                               $variantTitle = '';
                                                            }

                                                         $variantHtml .= '<span class="Polaris-Tag" style="margin-right:10px; margin-top: 10px;"><img src="' . $productImage . '" width="20" height="20" style="margin-right: 10px;"><span>' . $store_all_subscription_plans[$item_index]['product_name'] . '' . $variantTitle . '</span></span>';
                                                      } ?>
                                                      <div id="sd_display" style="display: flex;">
                                                         <p><span>Order Number</span><a href="https://<?php echo $mainobj->store; ?>/admin/orders/<?php echo $itemValue['order_id']; ?>" target="_blank">#<?php echo $itemValue['order_no'] ?></a></p>
                                                         <p><span>Order Date</span><?php echo   $mainobj->getShopTimezoneDate(($itemValue['created_at']), $shop_timezone); ?></p>
                                                      </div>
                                                   </div>
                                                   <div class="Polaris-Stack__Item">
                                                      <div class="Polaris-ButtonGroup">
                                                         <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain">
                                                            <button subscription-group-id="<?php echo $itemValue['order_id']; ?>" page-type="subscription_contract" class="open_mini_action Polaris-Button arrow-icon no-focus" type="button">
                                                               <svg viewBox="0 0 20 20" width="25">
                                                                  <path d="M10 14a.997.997 0 01-.707-.293l-5-5a.999.999 0 111.414-1.414L10 11.586l4.293-4.293a.999.999 0 111.414 1.414l-5 5A.997.997 0 0110 14z" fill="#5C5F62"></path>
                                                               </svg>
                                                            </button>
                                                         </div>
                                                      </div>
                                                   </div>
                                                </div>
                                                <div id="mini_<?php echo $itemValue['order_id']; ?>" class="subscription_mini_inner_wrapper display-hide-label">
                                                   <div class="Polaris-Card__Section inner-box-cont">
                                                      <div class="Polaris-ResourceList__ResourceListWrapper">
                                                         <?php echo $variantHtml; ?>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </li>
                                 <?php }
                                 }
                                 $orderHtml = '';
                                 $variantHtml = '';
                                 foreach ($orderContractProducts_array as $key => $itemValue) {
                                    $item_index = array_search($itemValue, array_column($store_all_subscription_plans, 'variant_id')); //
                                    $variantTitle = '';
                                    if (!empty($store_all_subscription_plans[$item_index]['variant_image'])) {
                                       $productImage = $store_all_subscription_plans[$item_index]['variant_image'];
                                    } else {
                                       $productImage = $mainobj->SHOPIFY_DOMAIN_URL . '/backend/assets/images/no-image.png';
                                    }

                                    if($store_all_subscription_plans[$item_index]['variant_name'] != 'Default Title'){
                                       $variantTitle = '-' . $store_all_subscription_plans[$item_index]['variant_name'];
                                    }
                                       if(trim($variantTitle == '-')){
                                          $variantTitle = '';
                                       }
                                    $variantHtml .= '<span class="Polaris-Tag" style="margin-right:10px; margin-top: 10px;" data-id="6610063098030"><img src="' . $productImage . '" width="20" height="20" style="margin-right: 10px;"><span>' . $store_all_subscription_plans[$item_index]['product_name'] . '' . $variantTitle . '</span></span>';
                                 }
                                 ?>
                                 <li class="Polaris-ResourceItem__ListItem">
                                    <div class="Polaris-ResourceItem__ItemWrapper">
                                       <div class="Polaris-ResourceItem">
                                          <a class="Polaris-ResourceItem__Link" id="PolarisResourceListItemOverlay4" data-polaris-unstyled="true"></a>
                                          <div class="Polaris-ResourceItem__Container" id="145">
                                             <div class="Polaris-ResourceItem__Content">
                                                <div id="sd_display" style="display: flex;">
                                                   <p><span class="sd_upcomingOrderDate">Order Number</span><a href="https://<?php echo $mainobj->store; ?>/admin/orders/<?php echo $getContractData[0]['order_id']; ?>" target="_blank">#<?php echo $getContractData[0]['order_no'] ?></a></p>
                                                   <p><span class="sd_upcomingOrderDate">Order Date</span><?php echo $mainobj->getShopTimezoneDate($getContractData[0]['created_at'], $shop_timezone);  ?></p>
                                                </div>
                                             </div>
                                             <div class="Polaris-Stack__Item">
                                                <div class="Polaris-ButtonGroup">
                                                   <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain">
                                                      <button subscription-group-id="<?php echo $getContractData[0]['order_id']; ?>" page-type="subscription_contract" class="open_mini_action Polaris-Button arrow-icon no-focus" type="button">
                                                         <svg viewBox="0 0 20 20" width="25">
                                                            <path d="M10 14a.997.997 0 01-.707-.293l-5-5a.999.999 0 111.414-1.414L10 11.586l4.293-4.293a.999.999 0 111.414 1.414l-5 5A.997.997 0 0110 14z" fill="#5C5F62"></path>
                                                         </svg>
                                                      </button>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                          <div id="mini_<?php echo $getContractData[0]['order_id']; ?>" class="subscription_mini_inner_wrapper display-hide-label">
                                             <div class="Polaris-Card__Section inner-box-cont">
                                                <div class="Polaris-ResourceList__ResourceListWrapper">
                                                   <?php echo $variantHtml; ?>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </li>
                              </ul>
                           </div>
                        </div>
                     </div>
                     <!--Past Orders List ends here-->
                  </div>
                  <div class="sd_orderTabs Polaris-Tabs__Panel Polaris-Tabs__Panel--hidden" id="sd_upcomingFulfillments_content" role="tabpanel" aria-labelledby="accepts-marketing-1" tabindex="-1">
                     <div class="Polaris-Card__Section">
                        <!--Upcoming Orders or Fulfillments List here1234-->
                        <div class="Polaris-ResourceList__ResourceListWrapper">
                           <ul class="Polaris-ResourceList" aria-live="polite">
                              <?php
                              if ($getContractData[0]['delivery_policy_value'] == $getContractData[0]['billing_policy_value']) {
                                 $next_billing_date = $getContractData[0]['next_billing_date'];
                                 $attemptBilling = true;
                                 for ($i = 1; $i < 6; $i++) {
                              ?>
                                    <li class="Polaris-ResourceItem__ListItem" id="upcoming_order_<?php echo $next_billing_date; ?>">
                                       <div class="Polaris-ResourceItem__ItemWrapper">
                                          <div class="Polaris-ResourceItem">
                                             <a class="Polaris-ResourceItem__Link" id="PolarisResourceListItemOverlay4" data-polaris-unstyled="true"></a>
                                             <div class="Polaris-ResourceItem__Container" id="145">
                                                <div class="Polaris-ResourceItem__Content">
                                                   <?php
                                                   $variantHtml = '';
                                                   foreach ($store_all_subscription_plans as $key => $prdValue) {
                                                      if ($prdValue['product_contract_status'] == '1') {
                                                         if ($prdValue['variant_name'] == '-' || $prdValue['variant_name'] == '-Default Title' || $prdValue['variant_name'] == 'Default Title' || $prdValue['variant_name'] == '') {
                                                            $variantTitle = '';
                                                         } else {
                                                            $variantTitle =  ' - ' . $prdValue['variant_name'];
                                                         }
                                                         if ($prdValue['variant_image'] != '0' || $prdValue['variant_image'] != '') {
                                                            $productImage = $prdValue['variant_image'];
                                                         } else {
                                                            $productImage = $mainobj->SHOPIFY_DOMAIN_URL . '/backend/assets/images/no-image.png';
                                                         }
                                                         $variantHtml .= '<span class="Polaris-Tag" style="margin-right:10px; margin-top: 10px;"><img src="' . $productImage . '" width="20" height="20" style="margin-right: 10px;"><span>' . $prdValue['product_name'] . '' . $variantTitle . '</span></span>';
                                                      }
                                                   } ?>
                                                   <div class="sd_upcomingDate">
                                                      <span class="sd_upcomingOrderDate">Order Date</span>
                                                      <p><?php
                                                         // echo $next_billing_date;
                                                         echo $mainobj->getShopTimezoneDate(($next_billing_date . ' ' . $date_time_array[1]), $shop_timezone);
                                                         ?></p>
                                                   </div>
                                                   <div class="sd_upcomingFulfillment">
                                                      <span class="sd_upcomingOrderDate">Status:</span><span class="Polaris-Badge Polaris-Badge--statusAttention Polaris-Badge--progressPartiallyComplete"><span class="Polaris-VisuallyHidden">Info </span>Queued</span>
                                                   </div>
                                                </div>
                                                <div class="sd_billingAttempt">
                                                   <div class="sd_attemptBillingAndSkip">
                                                      <?php
                                                      if ($attemptBilling == true) { ?>
                                                         <button class="Polaris-Button Polaris-Button--primary sd_attemptBilling" data-billingPolicy="<?php echo $getContractData[0]['billing_policy_value'];  ?>" data-billing_date="<?php echo $next_billing_date; ?>" type="button">Attempt Billing</button>
                                                         <button class="Polaris-Button Polaris-Button--primary sd_skipOrder" data-billingPolicy="<?php echo $getContractData[0]['billing_policy_value'];  ?>" data-billingType="<?php echo $getContractData[0]['delivery_billing_type']; ?>" data-billing_date="<?php echo $next_billing_date; ?>" type="button">Skip Order</button>
                                                      <?php $attemptBilling = false;
                                                      } ?>
                                                   </div>
                                                   <div class="Polaris-Stack__Item">
                                                      <div class="Polaris-ButtonGroup">
                                                         <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain">
                                                            <button subscription-group-id="<?php echo $next_billing_date . '_' . $i; ?>" page-type="subscription_contract" class="open_mini_action Polaris-Button arrow-icon no-focus" type="button">
                                                               <svg viewBox="0 0 20 20" width="25">
                                                                  <path d="M10 14a.997.997 0 01-.707-.293l-5-5a.999.999 0 111.414-1.414L10 11.586l4.293-4.293a.999.999 0 111.414 1.414l-5 5A.997.997 0 0110 14z" fill="#5C5F62"></path>
                                                               </svg>
                                                            </button>
                                                         </div>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                             <div id="mini_<?php echo $next_billing_date . '_' . $i; ?>" class="subscription_mini_inner_wrapper display-hide-label">
                                                <div class="Polaris-Card__Section inner-box-cont">
                                                   <div class="Polaris-ResourceList__ResourceListWrapper">
                                                      <?php echo $variantHtml; ?>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </li>
                                 <?php
                                    $next_billing_date = date('Y-m-d', strtotime('+' . $getContractData[0]['billing_policy_value'] . ' ' . $getContractData[0]['delivery_billing_type'], strtotime($next_billing_date)));
                                 }
                              } else {
                                 // echo $lastOrderId;
                                 $getContractOrders = $mainobj->getContractOrders($contractId, '');
                                 // echo '<pre>';
                                 // print_r($getContractOrders);
                                 $all_fulfillmentDates = [];
                                 foreach ($getContractOrders['fulfillmentOrders']['edges'] as $key => $ordData) {
                                    $all_fulfillmentDates[$ordData['node']['fulfillAt']] = $ordData['node']['id'];
                                 }
                                 // if($mainobj->store == 'dev-nisha-subscription.myshopify.com'){
                                 //     print_r($all_fulfillmentDates);
                                 // }
                                 $sorted_date_array = array_keys($all_fulfillmentDates);
                                 function date_sort($a, $b)
                                 {
                                    return strtotime($a) - strtotime($b);
                                 }
                                 usort($sorted_date_array, "date_sort");
                                 foreach ($sorted_date_array as $key => $value) {
                                    //    if($mainobj->store == 'dev-nisha-subscription.myshopify.com'){
                                    //       echo $value;
                                    //   }
                                    $actualFulfillmentDate = strtok($value, 'T');
                                 ?>
                                    <li class="Polaris-ResourceItem__ListItem">
                                       <div class="Polaris-ResourceItem__ItemWrapper">
                                          <div class="Polaris-ResourceItem">
                                             <a class="Polaris-ResourceItem__Link" id="PolarisResourceListItemOverlay4" data-polaris-unstyled="true"></a>
                                             <div class="Polaris-ResourceItem__Container" id="145">
                                                <div class="Polaris-ResourceItem__Content sd_fulfillments">
                                                   <p><span>Fulfill Date</span> <?php echo $mainobj->getShopTimezoneDate(($value), $shop_timezone);  ?></p>
                                                   <p><span class="sd_upcomingOrderDate">Status</span><span class="Polaris-Badge Polaris-Badge--statusAttention Polaris-Badge--progressPartiallyComplete"><span class="Polaris-VisuallyHidden">Info </span>SCHEDULED</span></p>
                                                </div>
                                                <div>
                                                   <span class="reschedule">
                                                      <button class="Polaris-Button Polaris-Button--primary sd_skipFulfillment" data-fulfillOrderId="<?php echo $all_fulfillmentDates[$value] ?>" data-orderName="<?php echo $getContractOrders['name'];  ?>" data-billingCycle="<?php echo $getContractData[0]['billing_policy_value'] ?>" data-deliveryCycle="<?php echo $getContractData[0]['delivery_policy_value'] ?>" data-delivery_billing_type="<?php echo $getContractData[0]['delivery_billing_type']; ?>" data-actualOrderDate="<?php echo $actualFulfillmentDate; ?>" data-orderId="<?php echo $getContractOrders['id'];  ?>" type="button">Skip Fulfillment</button>
                                                   </span>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </li>
                                 <?php } ?>
                                 <!-- <input type="hidden" data-lastFulfillmentDate="<?php //echo max($sorted_date_array);
                                                                                       ?>" id="lastFulfillAt"> -->
                              <?php } ?>
                           </ul>
                        </div>
                     </div>
                  </div>
                  <div class="sd_orderTabs Polaris-Tabs__Panel Polaris-Tabs__Panel--hidden" id="sd_pendingOrders_content" role="tabpanel" aria-labelledby="repeat-customers-1" tabindex="-1">
                     <div class="Polaris-Card__Section">
                        <!--Pending Orders List here-->
                        <div class="Polaris-ResourceList__ResourceListWrapper">
                           <ul class="Polaris-ResourceList" aria-live="polite">
                              <?php if (!empty($pendingStatusArray)) {
                                 foreach ($pendingStatusArray as $value) { ?>
                                    <li class="Polaris-ResourceItem__ListItem">
                                       <div class="Polaris-ResourceItem__ItemWrapper">
                                          <div class="Polaris-ResourceItem">
                                             <a class="Polaris-ResourceItem__Link" id="PolarisResourceListItemOverlay4" data-polaris-unstyled="true"></a>
                                             <div class="Polaris-ResourceItem__Container" id="145">
                                                <div class="Polaris-ResourceItem__Content sd_pendingOrders">
                                                   <p><span>Order Number</span>Pending</p>
                                                   <p><span>Order Date</span><?php echo $mainobj->getShopTimezoneDate(($value['renewal_date'] . ' ' . $date_time_array[1]), $shop_timezone); ?></p>
                                                   <p><span>Status</span><span class="Polaris-Badge Polaris-Badge--statusAttention Polaris-Badge--progressPartiallyComplete"><span class="Polaris-VisuallyHidden">Info </span><?php echo $value['status']; ?></span></p>
                                                   <p><span>Order Attempt Date</span><?php echo $mainobj->getShopTimezoneDate(($value['billing_attempt_date'] . ' ' . $date_time_array[1]), $shop_timezone); ?></p>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </li>
                              <?php }
                              } else {
                                 echo "<div class='no_orders'>No any Pending Order of this Subscription yet</div>";
                              } ?>
                           </ul>
                        </div>
                     </div>
                  </div>
                  <div class="sd_orderTabs Polaris-Tabs__Panel Polaris-Tabs__Panel--hidden" id="sd_failureOrders_content" role="tabpanel" aria-labelledby="prospects-1" tabindex="-1">
                     <div class="Polaris-Card__Section">
                        <div class="Polaris-ResourceList__ResourceListWrapper">
                           <ul class="Polaris-ResourceList" aria-live="polite">
                              <?php if (!empty($failureStatusArray)) {
                                 foreach ($failureStatusArray as $value) { ?>
                                    <li class="Polaris-ResourceItem__ListItem">
                                       <div class="Polaris-ResourceItem__ItemWrapper">
                                          <div class="Polaris-ResourceItem">
                                             <a class="Polaris-ResourceItem__Link" id="PolarisResourceListItemOverlay4" data-polaris-unstyled="true"></a>
                                             <div class="Polaris-ResourceItem__Container" id="145">
                                                <div class="Polaris-ResourceItem__Content sd_failureOrders">
                                                   <p><span>Order Number</span>Failure</p>
                                                   <p><span>Order Date</span><?php echo $mainobj->getShopTimezoneDate(($value['renewal_date'] . ' ' . $date_time_array[1]), $shop_timezone); ?></p>
                                                   <p><span>Status</span><span class="Polaris-Badge Polaris-Badge--statusAttention Polaris-Badge--progressPartiallyComplete"><span class="Polaris-VisuallyHidden">Info </span><?php echo $value['status']; ?></span></p>
                                                   <p><span>Order Attempt Date</span><?php echo $mainobj->getShopTimezoneDate(($value['billing_attempt_date'] . ' ' . $date_time_array[1]), $shop_timezone); ?></p>
                                                   <p><span>Order Failure Date</span><?php echo $mainobj->getShopTimezoneDate(($value['billingAttemptResponseDate'] . ' ' . $date_time_array[1]), $shop_timezone); ?>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </li>
                              <?php }
                              } else {
                                 echo "<div class='no_orders'>No any Failure Order of this Subscription yet</div>";
                              } ?>
                           </ul>
                        </div>
                     </div>
                  </div>
                  <div class="sd_orderTabs Polaris-Tabs__Panel Polaris-Tabs__Panel--hidden" id="sd_skipOrders_content" role="tabpanel" aria-labelledby="prospects-1" tabindex="-1">
                     <div class="Polaris-Card__Section">
                        <div class="Polaris-ResourceList__ResourceListWrapper">
                           <ul class="Polaris-ResourceList" aria-live="polite" id="upcoming_order_fulfillment">
                              <?php if ($getContractData[0]['delivery_policy_value'] == $getContractData[0]['billing_policy_value']) {
                                 if (!empty($skipStatusArray)) {
                                    foreach ($skipStatusArray as $value) { ?>
                                       <li class="Polaris-ResourceItem__ListItem">
                                          <div class="Polaris-ResourceItem__ItemWrapper">
                                             <div class="Polaris-ResourceItem">
                                                <a class="Polaris-ResourceItem__Link" id="PolarisResourceListItemOverlay4" data-polaris-unstyled="true"></a>
                                                <div class="Polaris-ResourceItem__Container" id="145">
                                                   <div class="Polaris-ResourceItem__Content">
                                                      <div class="Polaris-ResourceItem__Content sd_skipOrders">
                                                         <p><span>Order Number</span>Skipped</p>
                                                         <p><span>Order Date</span><?php echo $mainobj->getShopTimezoneDate(($value['renewal_date'] . ' ' . $date_time_array[1]), $shop_timezone); ?></p>
                                                         <p><span>Status</span><span class="Polaris-Badge Polaris-Badge--statusAttention Polaris-Badge--progressPartiallyComplete"><span class="Polaris-VisuallyHidden">Info </span>Skipped</span></p>
                                                         <p><span>Order Skipped Date</span><?php echo $mainobj->getShopTimezoneDate(($value['billing_attempt_date'] . ' ' . $date_time_array[1]), $shop_timezone); ?></p>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </li>
                                    <?php }
                                 } else {
                                    echo "<div class='no_orders'>No any Skip Order of this Subscription yet</div>";
                                 }
                              } else {
                                 $whereCondition = array(
                                    'order_id' => substr($getContractOrders['id'], strrpos($getContractOrders['id'], '/') + 1),
                                    'contract_id' =>  $contractId
                                 );
                                 $get_rescheduled_data = $mainobj->table_row_value('reschedule_fulfillment', 'all', $whereCondition, 'and', '');
                                 if (!empty($get_rescheduled_data)) {
                                    foreach ($get_rescheduled_data as $value) {
                                    ?>
                                       <li class="Polaris-ResourceItem__ListItem">
                                          <div class="Polaris-ResourceItem__ItemWrapper">
                                             <div class="Polaris-ResourceItem">
                                                <a class="Polaris-ResourceItem__Link" id="PolarisResourceListItemOverlay4" data-polaris-unstyled="true"></a>
                                                <div class="Polaris-ResourceItem__Container" id="145">
                                                   <div class="Polaris-ResourceItem__Content">
                                                      <div class="Polaris-ResourceItem__Content sd_rescheduleOrders">
                                                         <p><span>Order Number</span>#<?php echo $value['order_no']; ?></p>
                                                         <p><span>Actual Fulfillment Date</span><?php echo $mainobj->getShopTimezoneDate(($value['actual_fulfillment_date'] . ' ' . $date_time_array[1]), $shop_timezone); ?></p>
                                                         <p><span>Rescheduled Date</span><?php echo  $mainobj->getShopTimezoneDate(($value['new_fulfillment_date'] . ' ' . $date_time_array[1]), $shop_timezone);   ?></p>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </li>
                              <?php }
                                 } else {
                                    echo "<div class='no_orders'>No any Reschedule Fulfillment of the  current order yet</div>";
                                 }
                              } ?>
                           </ul>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div id="PolarisPortalsContainer">
               <div data-portal-id="popover-Polarisportal1"></div>
            </div>
      </div>
   <?php } ?>
   </div>
<?php } ?>
</div>
</div>
</div>
<?php
include("../footer.php");
?>