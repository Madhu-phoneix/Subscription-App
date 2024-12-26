<?php 
    use PHPShopify\ShopifySDK;
    include("../header.php");
    include $dirPath."/graphLoad/autoload.php";
    $pageCSS = '';
    ?>
<div class="Polaris-Layout">
    <?php
      function getShopTimezoneDate($date,$shop_timezone){
         $dt = new DateTime($date);
         $tz = new DateTimeZone($shop_timezone); // or whatever zone you're after
         $dt->setTimezone($tz);
         $dateTime = $dt->format('Y-m-d H:i:s');
         $shopify_date = date("d M Y", strtotime($dateTime));
         return $shopify_date;
      }
      function getShopTimezoneDateFormat($date,$shop_timezone){
         $dt = new DateTime($date);
         $tz = new DateTimeZone($shop_timezone); // or whatever zone you're after
         $dt->setTimezone($tz);
         $dateTime = $dt->format('Y-m-d');
         return $dateTime; //use in analytics
      }
        if($store == 'thegivenget.myshopify.com') {
           // echo $logged_in_customer_id;
           echo '<p style="margin:40px;">'.$logged_in_customer_id.'</p>';
           echo 'idj';
        }
        if($logged_in_customer_id == ''){
           $whereCustomerCondition  = '';
           include("../navigation.php");
           $empty_contract_redirect_page = $SHOPIFY_DOMAIN_URL .'/admin/subscription/subscriptions.php?shop='. $store;
           $parent_class = 'sd_backend_subscription';
           // $empty_contract_redirect_page = '';
        }else{
          header("Content-Type: application/liquid");
          $pageCSS = 'style="padding: 28px 66px 50px 63px;"';
          $whereCustomerCondition  =  " and o.shopify_customer_id = '$logged_in_customer_id'";
          $empty_contract_redirect_page = 'https://'.$store.'/apps/your-subscriptions';
          $parent_class = 'sd_frontend_subscription';
        }

        if(isset($_GET['contract_id']) && ($_GET['contract_id'] != '')){
           $contractId = $_GET['contract_id'];
            $whereCondition = array(
             'contract_id'=>$contractId,
             'store_id' => $store_id
            );
            $fields = array(
              'new_contract' => '0'
            );
           //  $mainobj->update_row('subscriptionOrderContract',$fields,$whereCondition,'and');
           $sql_update = $db->query("UPDATE `subscriptionOrderContract` SET new_contract = '0' WHERE contract_id = '$contractId' AND store_id = '$store_id'");


           $config = array(
              'ShopUrl' => $store,
              'AccessToken' => $access_token,
          );
          $shopify_graphql_object = new ShopifySDK($config);

           //get payment method token
           // $contractMethodToken = $mainobj->getContractPaymentToken($contractId);
           $get_customer_payment_method = '{
              subscriptionContract(id: "gid://shopify/SubscriptionContract/'.trim($contractId).'"){
                 customerPaymentMethod{
                    id
                    ... on CustomerPaymentMethod{
                    id
                    instrument{
                       __typename
                       ... on CustomerCreditCard{
                       brand
                       expiryYear
                       expiryMonth
                       lastDigits
                       }
                       ... on CustomerShopPayAgreement{
                          expiryMonth
                          expiryYear
                          isRevocable
                          maskedNumber
                          lastDigits
                          name
                          inactive
                       }
                       ... on CustomerPaypalBillingAgreement{
                          billingAddress{
                          countryCode
                          country
                          province
                          }
                          paypalAccountEmail
                       }
                    }
                    }
                 }
              }
           }';
           $contractMethodToken = $shopify_graphql_object->GraphQL->post($get_customer_payment_method,null,null,null);

          
           $customerPaymentMethod = $contractMethodToken['data']['subscriptionContract']['customerPaymentMethod'];
           $paymentMethodToken =  substr($customerPaymentMethod['id'], strrpos($customerPaymentMethod['id'], '/') + 1);

           // echo $paymentMethodToken; die;
           $getContractData_qry = $db->query("SELECT p.payment_instrument_value,p.payment_method_token,o.after_cycle,o.order_currency,o.after_cycle_update,o.min_cycle,o.discount_type,o.discount_value,o.recurring_discount_type,o.recurring_discount_value,o.next_billing_date,o.order_no,o.order_id,o.created_at,o.updated_at,o.contract_status,o.contract_products,o.delivery_policy_value,o.billing_policy_value,o.delivery_billing_type,a.first_name as shipping_first_name,a.last_name as shipping_last_name,a.address1 as shipping_address1,a.address2 as shipping_address2,a.city as shipping_city,a.province as shipping_province,a.country as shipping_country,a.company as shipping_company,a.phone as shipping_phone,a.province_code as shipping_province_code,a.country_code as shipping_country_code,a.zip as shipping_zip,a.delivery_method as shipping_delivery_method,a.delivery_price as shipping_delivery_price,b.first_name as billing_first_name,b.last_name as billing_last_name,b.address1 as billing_address1,b.address2 as billing_address2,b.city as billing_city,b.province as billing_province,b.country as billing_country,b.company as billing_company,b.phone as billing_phone,b.province_code as billing_province_code,b.country_code as billing_country_code,b.zip as billing_zip,d.store_email,d.shop_timezone,c.name,c.email,c.shopify_customer_id,cs.cancel_subscription,cs.edit_product_price,cs.skip_upcoming_order,cs.skip_upcoming_fulfillment,cs.pause_resume_subscription,cs.add_subscription_product,cs.add_out_of_stock_product,cs.edit_product_quantity,cs.edit_out_of_stock_product_quantity,cs.attempt_billing,cs.delete_product,cs.edit_shipping_address from subscriptionOrderContract as o
           INNER JOIN contract_setting as contract_settng ON o.store_id = contract_settng.store_id
           INNER JOIN subscriptionContractShippingAddress as a ON o.contract_id = a.contract_id
           INNER JOIN subscriptionContractBillingAddress as b ON o.contract_id = b.contract_id
           INNER JOIN customers as c ON c.shopify_customer_id = o.shopify_customer_id
           INNER JOIN store_details as d ON d.store_id = a.store_id
           INNER JOIN customerContractPaymentmethod AS p ON p.store_id = o.store_id
           INNER JOIN customer_settings as cs ON cs.store_id = o.store_id
           where o.contract_id = '$contractId' and p.payment_method_token = '$paymentMethodToken'");
           $getContractData = $getContractData_qry->fetchAll(PDO::FETCH_ASSOC);
          

           if (empty($getContractData)) {
              echo "<script>window.top.location.href='" .   $empty_contract_redirect_page  . "'</script>";
              die;
           }
           $currency_list = array(
              "AFA" => "؋","ALL" => "Lek","DZD" => "دج","AOA" => "Kz","ARS" => "$","AMD" => "֏","AWG" => "ƒ","AUD" => "$","AZN" => "m","BSD" => "B$","BHD" => ".د.ب","BDT" => "৳","BBD" => "Bds$","BYR" => "Br","BEF" => "fr","BZD" => "$","BMD" => "$","BTN" => "Nu.","BTC" => "฿","BOB" => "Bs.","BAM" => "KM","BWP" => "P","BRL" => "R$","GBP" => "£","BND" => "B$","BGN" => "Лв.","BIF" => "FBu","KHR" => "KHR","CAD" => "$","CVE" => "$","KYD" => "$","XOF" => "CFA","XAF" => "FCFA","XPF" => "₣","CLP" => "$","CNY" => "¥","COP" => "$","KMF" => "CF","CDF" => "FC","CRC" => "₡","HRK" => "kn","CUC" => "$, CUC","CZK" => "Kč","DKK" => "Kr.","DJF" => "Fdj","DOP" => "$","XCD" => "$",
              "EGP" => "ج.م","ERN" => "Nfk","EEK" => "kr","ETB" => "Nkf","EUR" => "€","FKP" => "£","FJD" => "FJ$","GMD" => "D","GEL" => "ლ","DEM" => "DM","GHS" => "GH₵","GIP" => "£","GRD" => "₯, Δρχ, Δρ","GTQ" => "Q","GNF" => "FG","GYD" => "$","HTG" => "G","HNL" => "L","HKD" => "$","HUF" => "Ft","ISK" => "kr","INR" => "Rs.","IDR" => "Rp","IRR" => "﷼","IQD" => "د.ع","ILS" => "₪","ITL" => "L,£","JMD" => "J$","JPY" => "¥","JOD" => "ا.د","KZT" => "лв","KES" => "KSh","KWD" => "ك.د","KGS" => "лв","LAK" => "₭",
              "LVL" => "Ls","LBP" => "£","LSL" => "L","LRD" => "$","LYD" => "د.ل","LTL" => "Lt","MOP" => "$","MKD" => "ден", "MGA" => "Ar", "MWK" => "MK", "MYR" => "RM", "MVR" => "Rf", "MRO" => "MRU", "MUR" => "₨", "MXN" => "$", "MDL" => "L", "MNT" => "₮", "MAD" => "MAD", "MZM" => "MT", "MMK" => "K", "NAD" => "$", "NPR" => "₨", "ANG" => "ƒ", "TWD" => "$", "NZD" => "$", "NIO" => "C$", "NGN" => "₦", "KPW" => "₩", "NOK" => "kr", "OMR" => ".ع.ر", "PKR" => "₨", "PAB" => "B/.", "PGK" => "K", "PYG" => "₲", "PEN" => "S/.", "PHP" => "₱", "PLN" => "zł", "QAR" => "ق.ر", "RON" => "lei", "RUB" => "₽", "RWF" => "FRw", "SVC" => "₡", "WST" => "SAT", "SAR" => "﷼", "RSD" => "din", "SCR" => "SRe", "SLL" => "Le", "SGD" => "$", "SKK" => "Sk", "SBD" => "Si$", "SOS" => "Sh.so.", "ZAR" => "R", "KRW" => "₩", "XDR" => "SDR", "LKR" => "Rs", "SHP" => "£", "SDG" => ".س.ج", "SRD" => "$", "SZL" => "E", "SEK" => "kr", "CHF" => "CHf", "SYP" => "LS", "STD" => "Db", "TJS" => "SM", "TZS" => "TSh", "THB" => "฿", "TOP" => "$", "TTD" => "$", "TND" => "ت.د", "TRY" => "₺", "TMT" => "T", "UGX" => "USh", "UAH" => "₴", "AED" => "إ.د", "UYU" => "$", "USD" => "$", "UZS" => "лв", "VUV" => "VT", "VEF" => "Bs", "VND" => "₫", "YER" => "﷼", "ZMK" => "ZK"
           );
           $order_currency = $currency_list[$getContractData[0]['order_currency']];
           $updated_at_date = $getContractData[0]['updated_at'];
           $date_time_array = explode(' ', $updated_at_date);
           $shop_timezone = $getContractData[0]['shop_timezone'];
           $orderContractProducts = $getContractData[0]['contract_products'];
           $orderContractProducts_array = explode(',', $orderContractProducts);
           $lastOrderId = $getContractData[0]['order_id'];
           //get billing Attempts data from billingAttempts table of current contract
           // $whereCondition = array(
           //    'contract_id' => $contractId
           // );
           // $get_billing_Attempts = $mainobj->table_row_value('billingAttempts', 'all', $whereCondition, 'and', '');
           $get_billing_Attempts_qry = $db->query("SELECT * from `billingAttempts` WHERE contract_id = '$contractId'");
           $get_billing_Attempts = $get_billing_Attempts_qry->fetchAll(PDO::FETCH_ASSOC);

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

           // if($mainobj->store == 'boo-kay-nyc.myshopify.com'){
           $customer_payment_type = $customerPaymentMethod['instrument']['__typename'];
           $brand = ''; $lastDigits = ''; $expiryMonth = '';  $expiryYear = '';
           if($customer_payment_type == 'CustomerShopPayAgreement'){
              $lastDigits = $customerPaymentMethod['instrument']['lastDigits'];
              $expiryMonth = $customerPaymentMethod['instrument']['expiryMonth'];
              $expiryYear = $customerPaymentMethod['instrument']['expiryYear'];
           }else if($customer_payment_type == 'CustomerCreditCard'){
              $lastDigits = $customerPaymentMethod['instrument']['lastDigits'];
              $expiryMonth = $customerPaymentMethod['instrument']['expiryMonth'];
              $expiryYear = $customerPaymentMethod['instrument']['expiryYear'];
              $brand = $customerPaymentMethod['instrument']['brand'];
           }else if($customer_payment_type == 'CustomerPaypalBillingAgreement'){
              $brand = 'Paypal Billing';
              $lastDigits = 'XXXX';
           }
           // }
           // $payment_instrument_value = json_decode($getContractData[0]['payment_instrument_value']);
           // $dateObj   = DateTime::createFromFormat('!m', $expiryMonth);
           // $monthName = $dateObj->format('F'); // March

           $disable_customer_delete_contract = '';
            //check if min cycle is available or not
           if($logged_in_customer_id != ''){ // check if it is a customer account page
                 if($getContractData[0]['min_cycle'] != 0){  //disable delete contract button based upon the min cycle condition
                 // $total_contract_orders = $mainobj->totalContractOrders($contractId);
                 $get_contract_total_orders_qry =  $db->query("SELECT COUNT(*) as recurringOrderTotal FROM `billingAttempts` WHERE store_id = '$store_id' AND contract_id = '$contract_id' AND status = 'Success'");
                 $get_contract_total_orders = $get_contract_total_orders_qry->fetchAll(PDO::FETCH_ASSOC);
                 $total_contract_orders = ($get_contract_total_orders[0]['recurringOrderTotal'] + 1);
                 if($getContractData[0]['min_cycle'] > $total_contract_orders){
                    $disable_customer_delete_contract = 'disabled';
                 }
              }
           }

           if ($getContractData[0]['contract_status'] == 'A') {
              $buttonClass = 'Polaris-Button--destructive';
              $statusChangeTo = 'PAUSED';
              $buttonText = 'Pause Subscription';
              $currentStatus = 'Active';
              $next_bill_date = $getContractData[0]['next_billing_date'];
              $contract_status_info = 'statusSuccess';
              $addCircle = '<span class="Polaris-Badge__Pip"><span class="Polaris-VisuallyHidden"></span></span>';
           }  else if ($getContractData[0]['contract_status'] == 'P'){
              $buttonClass = 'Polaris-Button--primary';
              $statusChangeTo = 'ACTIVE';
              $buttonText = 'Activate Subscription';
              $currentStatus = 'Pause';
              $next_bill_date = '-';
              $contract_status_info = 'statusAttention';
              $addCircle = '';
           }elseif ($getContractData[0]['contract_status'] == 'C'){
              $buttonClass = 'Polaris-Button--primary';
              $statusChangeTo = 'ACTIVE';
              $buttonText = 'Activate Subscription';
              $currentStatus = 'Cancelled';
              $next_bill_date = '-';
              $contract_status_info = 'statusAttention';
              $addCircle = '';
           }
           $addedVariantsArray = [];
           // $whereCondition = array(
           //    'contract_id' => $contractId,
           //    'store_id' => $mainobj->store_id
           // );
           // $store_all_subscription_plans = $mainobj->table_row_value('subscritionOrderContractProductDetails', 'all', $whereCondition, 'and', '');
           $store_all_subscription_plans_qry = $db->query("SELECT * FROM `subscritionOrderContractProductDetails` WHERE contract_id = '$contractId' and store_id = '$store_id'");
           $store_all_subscription_plans = $store_all_subscription_plans_qry->fetchAll(PDO::FETCH_ASSOC);
           if($getContractData[0]['after_cycle_update'] == '1' && $getContractData[0]['after_cycle'] != 0){
              $get_subscription_price_column = 'recurring_computed_price';
              $get_dicount_type = 'recurring_';
           }else{
              $get_subscription_price_column = 'subscription_price';
              $get_dicount_type = '';
           }
           $subscription_price = [];
           $all_contract_products = [];
           $variant_string = '';
           $product_string = '';
           $coupon_applied = 'empty';
           $variant_ids_array = [];
           $pre_selected_products = [];
           foreach ($store_all_subscription_plans as $key => $value) {
              if (array_key_exists('gid://shopify/Product/'.$value['product_id'], $pre_selected_products)) {
              } else {
                 $pre_selected_products['gid://shopify/Product/'.$value['product_id']] = [];
              }
              if ($value['product_contract_status'] == '1') {
                 array_push($pre_selected_products['gid://shopify/Product/'.$value['product_id']],trim('gid://shopify/ProductVariant/'.$value['variant_id']));
                 if($coupon_applied == 'empty'){
                    $coupon_applied = $value['coupon_applied'];
                 }
                 $itemPrice = (($value[$get_subscription_price_column] * $value['quantity']) - ($value['coupon_value']));
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
              // $variantDetails = $mainobj->getVariantDetail($variantIds_string); // get variant inventory quantity from api
              $getSingleVariantData = '{
        			nodes(ids: '.$variantIds_string .') {
        			...on ProductVariant {
        				id
        				inventoryQuantity
        				availableForSale
        				inventoryPolicy
        				product{
        					onlineStorePreviewUrl
        				}
        			}
        			}
        		}';
              $variantDetails = $shopify_graphql_object->GraphQL->post($getSingleVariantData,null,null,null);
              $variant_ids_array = $variantDetails['data']['nodes'];
           }
           $addedVariantsArray = array_column($all_contract_products, 'variant_id');
           $total_subscription_product_price = array_sum($subscription_price);
           if (!empty($getContractData[0]['shipping_last_name'])) {
              $shipping_address_added = 1;
           }else{
              $shipping_address_added = 0;
           }
           echo "<script>shipping_address_added = ".$shipping_address_added.";already_added_products =" . json_encode($pre_selected_products) . ";specific_contract_data =" . json_encode($getContractData) . ";contract_existing_products =" . json_encode($all_contract_products) . ";disabled_product_variant_array =" . json_encode($addedVariantsArray) . ";</script>";
           $tickMarkSvg = '<svg version="1.2" viewBox="0 0 436 434"><path fill-rule="evenodd" class="s0" d="m371.5 63.5c40.7 40.7 63.6 95.9 63.6 153.5c0 42.9-12.8 84.9-36.6 120.6c-23.9 35.7-57.8 63.5-97.4 79.9c-39.7 16.5-83.3 20.8-125.4 12.4c-42.2-8.4-80.8-29-111.2-59.4c-30.3-30.4-51-69-59.4-111.1c-8.4-42.2-4.1-85.8 12.4-125.5c16.4-39.6 44.2-73.5 79.9-97.4c35.7-23.8 77.7-36.6 120.6-36.6c57.6 0.1 112.8 22.9 153.5 63.6zm-47.3 102.3c1-2.5 1.5-5.1 1.5-7.8c-0.1-2.7-0.7-5.3-1.8-7.8c-1.1-2.4-2.7-4.6-4.7-6.4c-3.7-3.7-8.7-5.7-13.9-5.7c-5.2 0-10.2 2-13.9 5.7l-103.2 103.4l-44.8-44.8c-3.7-3.7-8.7-5.7-13.9-5.7c-5.2 0-10.2 2-13.9 5.7c-1.9 1.8-3.3 4-4.3 6.4c-1 2.4-1.5 5-1.5 7.6c0 2.6 0.5 5.2 1.5 7.6c1 2.4 2.4 4.6 4.3 6.4l59.2 59.2c1.8 1.8 4 3.3 6.4 4.3c2.4 1 5 1.5 7.6 1.4c5.2 0 10.1-2 13.8-5.7l117.2-117.2c1.9-1.9 3.4-4.1 4.4-6.6z"/></svg>';

           ?>
    <div class="Polaris-Layout__Section sd-dashboard-page <?php echo  $parent_class; ?>" <?php echo $pageCSS; ?>>
        <input type="hidden" value="<?php echo $contractId; ?>" id="sd_contractId">
        <input type="hidden" value="<?php echo $all_contract_products[0]['contract_line_item_id'] ?>" id="contract_line_id">
        <div>
            <div class="Polaris-Page-Header Polaris-Page-Header--isSingleRow Polaris-Page-Header--mobileView Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
                <div class="Polaris-Page-Header__Row sd_contractNavigation">
                    <div class="Polaris-Page-Header__BreadcrumbWrapper sd_contractHeader">
                        <nav role="navigation">
                            <a class="Polaris-Breadcrumbs__Breadcrumb  navigate_element" data-usecase="subscription_form_leave" data-heading="Unsaved changes" data-query-string="" data-message="If you leave this page, any unsaved changes will be lost." data-acceptbuttontext="" data-rejectbuttontext="" data-confirmbox="" data-redirect-link="/admin/subscription/subscriptions.php?shop=<?php echo $store; ?>" data-polaris-unstyled="true" href="javascript:void(0)">
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
                            <?php
                                if($disable_customer_delete_contract == 'disabled'){ ?>
                            <div class="Polaris-Banner Polaris-Banner--statusWarning Polaris-Banner--withinPage" tabindex="0" role="status" aria-live="polite" aria-labelledby="PolarisBanner18Heading" aria-describedby="PolarisBanner18Content">
                                <div class="Polaris-Banner__ContentWrapper">
                                    <div class="Polaris-Banner__Heading" id="PolarisBanner18Heading">
                                        <p class="Polaris-Heading">Subscription cannot be paused/deleted until <?php echo $getContractData[0]['min_cycle']; ?> orders have completed.</p>
                                    </div>
                                </div>
                            </div>
                            <?php }
                                ?>
                            <div class="Polaris-Banner Polaris-Banner--statusSuccess Polaris-Banner--hasDismiss Polaris-Banner--withinPage sd_contractStatus" tabindex="0" role="status" aria-live="polite" aria-labelledby="PolarisBanner18Heading" aria-describedby="PolarisBanner18Content">
                                <div class="Polaris-Banner__ContentWrapper">
                                    <div class="Polaris-Banner__Heading" id="PolarisBanner18Heading">
                                        <p class="Polaris-Heading">Your Subscription is on <?php echo $currentStatus; ?> Mode</p>
                                        <?php if($logged_in_customer_id == '' || ($logged_in_customer_id != '' && $getContractData[0]['pause_resume_subscription'] == '1')){
                                            ?>
                                        <?php if($disable_customer_delete_contract == 'disabled'){ ?>
                                        <button class="Polaris-Button Polaris-Button--destructive remove-btn" <?php echo $disable_customer_delete_contract; ?>>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true" class="as-h-4 as-w-4 as-text-white as-mr-2" style="margin-right: 5px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                            <?php echo $buttonText; ?>
                                        </button>
                                        <?php }else{?>
                                        <button class="Polaris-Button sd_updateSubscriptionStatus <?php echo $buttonClass; ?>" id="sd_activePauseSubscription" data-nextBillingDate = "<?php echo $getContractData[0]['next_billing_date'] ?>" data-deliveryType = "<?php echo $getContractData[0]['delivery_billing_type']; ?>" data-billingValue = "<?php echo $getContractData[0]['billing_policy_value']; ?>" data-subscriptionStatus="<?php echo $statusChangeTo; ?>" data-buttonText="<?php echo $buttonText; ?>"  type="button"><?php echo $buttonText; ?></button>
                                        <?php } } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if((($logged_in_customer_id != '') && ($getContractData[0]['edit_product_price'] == '1')) || ($logged_in_customer_id == '') ){ ?>
            <div id="polaris-example" class="PolarisExampleWrapper_Example__NK9R0">
                <div class="Polaris-Banner Polaris-Banner--statusCritical Polaris-Banner--hasDismiss Polaris-Banner--withinPage" tabindex="0" role="alert" aria-live="polite" aria-labelledby="PolarisBanner1Heading" aria-describedby="PolarisBanner1Content">
                    <div class="Polaris-Banner__Ribbon">
                        <span class="Polaris-Icon Polaris-Icon--colorCritical Polaris-Icon--applyColor">
                            <span class="Polaris-Text--root Polaris-Text--bodySm Polaris-Text--regular Polaris-Text--visuallyHidden"></span>
                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                <path d="M11.768.768a2.5 2.5 0 0 0-3.536 0l-7.464 7.464a2.5 2.5 0 0 0 0 3.536l7.464 7.464a2.5 2.5 0 0 0 3.536 0l7.464-7.464a2.5 2.5 0 0 0 0-3.536l-7.464-7.464zm-2.768 5.232a1 1 0 1 1 2 0v4a1 1 0 1 1-2 0v-4zm2 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"></path>
                            </svg>
                        </span>
                    </div>
                    <div class="Polaris-Banner__ContentWrapper">
                        <div class="Polaris-Banner__Heading" id="PolarisBanner1Heading">
                            <p class="Polaris-Text--root Polaris-Text--headingMd Polaris-Text--semibold">If you edit the subscription product price then all the existing discount will be removed if applied.</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php if($logged_in_customer_id == '') { ?>
            <div id="polaris-example" class="PolarisExampleWrapper_Example__NK9R0 sd_funds_note">
                <div class="Polaris-Banner Polaris-Banner--statusInfo Polaris-Banner--hasDismiss Polaris-Banner--withinPage" tabindex="0" role="status" aria-live="polite" aria-labelledby="PolarisBanner1Heading" aria-describedby="PolarisBanner1Content">
                    <div class="Polaris-Banner__Ribbon">
                        <span class="Polaris-Icon Polaris-Icon--colorHighlight Polaris-Icon--applyColor">
                            <span class="Polaris-Text--root Polaris-Text--bodySm Polaris-Text--regular Polaris-Text--visuallyHidden"></span>
                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 20c5.514 0 10-4.486 10-10s-4.486-10-10-10-10 4.486-10 10 4.486 10 10 10zm1-6a1 1 0 1 1-2 0v-4a1 1 0 1 1 2 0v4zm-1-9a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"></path>
                            </svg>
                        </span>
                    </div>
                    <div class="Polaris-Banner__ContentWrapper">
                        <div class="Polaris-Banner__Heading" id="PolarisBanner1Heading">
                            <p class="Polaris-Text--root Polaris-Text--headingMd Polaris-Text--semibold">As per the shopify policy, you can't receive funds if your payout is below $1, £1, or €1, depending on which currency you use in your store. The funds are added to your next payout that meets this requirement.</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php if (!empty($getContractData)) {
                if($logged_in_customer_id == ''){
                   $customer_name = '<a href="https://'.$store.'/admin/customers/'.$getContractData[0]['shopify_customer_id'].'" target="_blank">'.$getContractData[0]['name'].'</a>';
                }else{
                   $customer_name = $getContractData[0]['name'];
                }
                ?>
            <div class="Polaris-Page__Content">
                <div class="Polaris-Layout">
                    <div class="Polaris-Layout__Section sd_contracts" style="display: flex;">
                        <!-- <div class="Polaris-Layout__Section Polaris-Layout__Section--secondary">  -->
                        <div class="sd_contracts-info">
                            <div class="Polaris-Card">
                                <div class="Polaris-Card__Header">
                                    <h2 class="Polaris-Heading">Subscription #<?php echo $contractId; ?></h2>
                                    <div class="sd_customerDetail">
                                        <div>
                                            <span class="Polaris-Badge Polaris-Badge--<?php echo $contract_status_info; ?> Polaris-Badge--progressComplete"><?php echo $addCircle . '' . $currentStatus; ?></span>
                                            <div id="PolarisPortalsContainer"></div>
                                        </div>
                                        <p class="cust-name"><?php echo $customer_name; ?></p>
                                        <p class="price-text"><?php echo $order_currency . '' . number_format($total_subscription_product_price, 2); ?></p>
                                        <p class="date">Created At <?php echo getShopTimezoneDate($getContractData[0]['created_at'], $shop_timezone); ?></p>
                                        <div class="sd_subscriptionPrc">
                                            Recurring total
                                            <div class="sd_recurringMsg" onmouseover="show_title(this)" onmouseout="hide_title(this)">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20px">
                                                    <path fill-rule="evenodd" d="M11 11H9v-.148c0-.876.306-1.499 1-1.852.385-.195 1-.568 1-1a1.001 1.001 0 00-2 0H7c0-1.654 1.346-3 3-3s3 1 3 3-2 2.165-2 3zm-2 4h2v-2H9v2zm1-13a8 8 0 100 16 8 8 0 000-16z" fill="#5C5F62" />
                                                </svg>
                                            </div>
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
                                        <p><?php echo $tickMarkSvg; ?>Delivery Period </p>
                                        <b><?php echo $getContractData[0]['delivery_policy_value'] . ' ' . $getContractData[0]['delivery_billing_type']; ?></b>
                                    </div>
                                    <div class="sd_orderDetail">
                                        <p><?php echo $tickMarkSvg; ?>Billing Period </p>
                                        <?php if($logged_in_customer_id == '' && ($getContractData[0]['billing_policy_value'] == $getContractData[0]['delivery_policy_value'])){  ?>
                                        <span class="update_delivery_billing_frequency">
                                            <b class="billing_period_frequency"><?php echo $getContractData[0]['billing_policy_value'] . ' ' . $getContractData[0]['delivery_billing_type']; ?></b>
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M17.3191 19.9994H2.55117C1.19208 19.9994 0.0898438 18.8972 0.0898438 17.536V2.75795C0.0898438 1.39886 1.20826 0.179321 2.56735 0.179321H12.4774V1.41909H2.56735C1.88781 1.41909 1.32152 2.08043 1.32152 2.75997V17.5381C1.32152 18.2176 1.87163 18.7697 2.55117 18.7697H17.3191C17.9987 18.7697 18.6722 18.2034 18.6722 17.5239V7.61185H19.9099V17.5239C19.9099 18.883 18.6803 19.9994 17.3191 19.9994ZM9.96348 14.0311C9.78348 14.2111 9.55898 14.3001 9.3264 14.3466L3.93656 17.0122C3.32982 17.2933 2.84443 16.7493 3.0669 16.1426L5.7325 10.7527C5.77699 10.5181 5.868 10.2936 6.04598 10.1136L15.6183 0.535273C16.0996 0.0559507 16.8762 0.0559507 17.3596 0.535273L19.5358 2.71144C20.0151 3.19278 20.0151 3.97345 19.5358 4.45277L9.96348 14.0311ZM4.24801 15.3983C4.10644 15.6814 4.37947 15.9727 4.68284 15.8331L8.00978 13.8188L6.26036 12.0693L4.24801 15.3983ZM7.35248 11.4201L8.65899 12.7266C8.89764 12.9673 8.27877 12.3464 9.09382 13.1635L16.0551 6.19815L13.8648 4.03412L6.91766 10.9853C7.14822 11.2159 7.11181 11.1795 7.35248 11.4201ZM18.2313 3.14626L16.9248 1.83976C16.6841 1.59908 16.2938 1.59908 16.0531 1.83976L14.781 3.1139L16.9268 5.32243L18.2333 4.01592C18.4719 3.77727 18.4719 3.38694 18.2313 3.14626Z" fill="black"></path>
                                            </svg>
                                        </span>
                                        <?php }else{ ?>
                                        <b class="billing_period_frequency"><?php echo $getContractData[0]['billing_policy_value'] . ' ' . $getContractData[0]['delivery_billing_type']; ?></b>
                                        <?php } ?>
                                        <span class="show_delivery_billing_selection show_delivery_billing_selection display-hide-label">
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField sd-small-textfield">
                                                        <input name="per_delivery_order_frequency_value" id="sd_per_delivery_order_frequency_value" class="Polaris-TextField__Input preview_plan" min="1" onkeypress="return event.charCode >= 48 &amp;&amp; event.charCode <= 57" type="number" aria-labelledby="PolarisTextField7Label" aria-invalid="false" autocomplete="off" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); this.value =
                                                            !!this.value &amp;&amp; Math.abs(this.value) > 0 ? Math.abs(this.value) : null" value="<?php echo $getContractData[0]['billing_policy_value']; ?>" maxlength="4">
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                        <div class="Polaris-Select sd-small-select">
                                                            <select id="sd_pay_per_delivery_billing_type" name="sd_pay_per_delivery_billing_type" class="Polaris-Select__Input" aria-invalid="false">
                                                                <option value="DAY" <?php if(strtoupper($getContractData[0]['delivery_billing_type']) == 'DAY'){ echo 'selected'; } ?>>DAY</option>
                                                                <option value="WEEK" <?php if(strtoupper($getContractData[0]['delivery_billing_type']) == 'WEEK'){ echo 'selected'; } ?>>WEEK</option>
                                                                <option value="MONTH" <?php if(strtoupper($getContractData[0]['delivery_billing_type']) == 'MONTH'){ echo 'selected'; } ?>>MONTH</option>
                                                                <option value="YEAR" <?php if(strtoupper($getContractData[0]['delivery_billing_type']) == 'YEAR'){ echo 'selected'; } ?>>YEAR</option>
                                                            </select>
                                                            <div class="Polaris-Select__Content" aria-hidden="true">
                                                                <span class="Polaris-Select__SelectedOption"  id="sd_prepaid_billing_type_Selected_value"><?php echo strtoupper($getContractData[0]['delivery_billing_type']); ?></span>
                                                                <span class="Polaris-Select__Icon">
                                                                    <span class="Polaris-Icon">
                                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                            <path d="m10 16-4-4h8l-4 4zm0-12 4 4H6l4-4z"></path>
                                                                        </svg>
                                                                    </span>
                                                                </span>
                                                            </div>
                                                            <div class="Polaris-Select__Backdrop"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </span>
                                    </div>
                                    <div class="sd_orderDetail show_delivery_billing_selection display-hide-label">
                                        <div class="">
                                            <button class="Polaris-Button Polaris-Button--primary update_delivery_billing_period" type="button"  data-contract_id="<?php echo $contractId; ?>">Save</button>
                                            <button class="Polaris-Button Polaris-Button--destructive cancel_update" data-id="update_delivery_billing_frequency" type="button">Cancel</button>
                                        </div>
                                    </div>
                                    <div class="sd_orderDetail">
                                        <p><?php echo $tickMarkSvg; ?>Plan Type </p>
                                        <b> <?php if ($getContractData[0]['delivery_policy_value'] == $getContractData[0]['billing_policy_value']) {
                                            echo "Pay Per Delivery";
                                            } else {
                                            echo "Prepaid";
                                            } ?></b>
                                    </div>
                                    <div class="sd_orderDetail sd_next_order_date">
                                        <p><?php echo $tickMarkSvg; ?>Next Order Date </p>
                                        <?php if($logged_in_customer_id == '' && $currentStatus == 'Active'){ ?>
                                        <span class="update_next_billing_date">
                                            <b class="sd_next_billing_date">
                                            <?php if ($currentStatus == 'Active') {
                                                $database_date = $next_bill_date;
                                                echo getShopTimezoneDate(($next_bill_date . ' ' . $date_time_array[1]), $shop_timezone);
                                                $shop_timezone_date = getShopTimezoneDateFormat(($next_bill_date . ' ' . $date_time_array[1]), $shop_timezone);
                                                $date1 = new DateTime($next_bill_date);
                                                $date2 = new DateTime($shop_timezone_date);
                                                $interval = $date1->diff($date2);
                                                $interval_days = $interval->days;
                                                if($next_bill_date > $shop_timezone_date){
                                                   $date_value_change = 'increase';
                                                }else if($shop_timezone_date > $next_bill_date){
                                                   $date_value_change = 'decrease';
                                                }else if($shop_timezone_date == $next_bill_date){
                                                   $date_value_change = 'no_change';
                                                }
                                                } else {
                                                echo '-';
                                                }
                                                if ($getContractData[0]['delivery_policy_value'] == $getContractData[0]['billing_policy_value']) {
                                                   $shop_timezone_date = getShopTimezoneDateFormat(date('Y-m-d H:i:s'), $shop_timezone);
                                                }
                                                ?>
                                            </b>
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M17.3191 19.9994H2.55117C1.19208 19.9994 0.0898438 18.8972 0.0898438 17.536V2.75795C0.0898438 1.39886 1.20826 0.179321 2.56735 0.179321H12.4774V1.41909H2.56735C1.88781 1.41909 1.32152 2.08043 1.32152 2.75997V17.5381C1.32152 18.2176 1.87163 18.7697 2.55117 18.7697H17.3191C17.9987 18.7697 18.6722 18.2034 18.6722 17.5239V7.61185H19.9099V17.5239C19.9099 18.883 18.6803 19.9994 17.3191 19.9994ZM9.96348 14.0311C9.78348 14.2111 9.55898 14.3001 9.3264 14.3466L3.93656 17.0122C3.32982 17.2933 2.84443 16.7493 3.0669 16.1426L5.7325 10.7527C5.77699 10.5181 5.868 10.2936 6.04598 10.1136L15.6183 0.535273C16.0996 0.0559507 16.8762 0.0559507 17.3596 0.535273L19.5358 2.71144C20.0151 3.19278 20.0151 3.97345 19.5358 4.45277L9.96348 14.0311ZM4.24801 15.3983C4.10644 15.6814 4.37947 15.9727 4.68284 15.8331L8.00978 13.8188L6.26036 12.0693L4.24801 15.3983ZM7.35248 11.4201L8.65899 12.7266C8.89764 12.9673 8.27877 12.3464 9.09382 13.1635L16.0551 6.19815L13.8648 4.03412L6.91766 10.9853C7.14822 11.2159 7.11181 11.1795 7.35248 11.4201ZM18.2313 3.14626L16.9248 1.83976C16.6841 1.59908 16.2938 1.59908 16.0531 1.83976L14.781 3.1139L16.9268 5.32243L18.2333 4.01592C18.4719 3.77727 18.4719 3.38694 18.2313 3.14626Z" fill="black"></path>
                                            </svg>
                                        </span>
                                        <span class="show_date_selection show_date_selection display-hide-label">
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField Polaris-TextField--hasValue">
                                                        <div class="Polaris-TextField__Prefix" id="select_next_billing_date-Prefix">
                                                            <svg viewBox="0 0 20 20" style="height: 18px;">
                                                                <path fill-rule="evenodd" d="M17.5 2H15V1a1 1 0 10-2 0v1H6V1a1 1 0 00-2 0v1H2.5C1.7 2 1 2.7 1 3.5v15c0 .8.7 1.5 1.5 1.5h15c.8 0 1.5-.7 1.5-1.5v-15c0-.8-.7-1.5-1.5-1.5zM3 18h14V8H3v10z" fill="#5C5F62"></path>
                                                            </svg>
                                                        </div>
                                                        <input id="select_next_billing_date" autocomplete="off" class="Polaris-TextField__Input" next-billing-date = "<?php echo getShopTimezoneDateFormat(($next_bill_date . ' ' . $date_time_array[1]), $shop_timezone); ?>" type="text" value="<?php echo $shop_timezone_date; ?>" readonly>
                                                        <div class="Polaris-TextField__Backdrop"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </span>
                                        <?php }else{
                                             if ($currentStatus == 'Active') {
                                                echo getShopTimezoneDate(($next_bill_date . ' ' . $date_time_array[1]), $shop_timezone);
                                             } else {
                                                echo '-';
                                             }
                                            } ?>
                                    </div>
                                    <div class="sd_orderDetail show_date_selection display-hide-label">
                                        <div class="">
                                            <button class="Polaris-Button Polaris-Button--primary update_next_date" data-contract_id="<?php echo $contractId; ?>" data-interval_value = "<?php echo $interval_days; ?>" value-change = "<?php echo $date_value_change; ?>" current-timezonedate = "<?php echo $shop_timezone_date; ?>" type="button">Save</button>
                                            <button class="Polaris-Button Polaris-Button--destructive cancel_update" data-id="update_next_billing_date" type="button">Cancel</button>
                                        </div>
                                    </div>
                                    <?php if($coupon_applied != 'empty'){ ?>
                                    <div class="sd_orderDetail">
                                        <p><?php echo $tickMarkSvg; ?>Coupon Applied </p>
                                        <b>#<?php echo $coupon_applied; ?></b>
                                    </div>
                                    <?php } ?>
                                    <div class="sd_orderDetail">
                                        <p><?php echo $tickMarkSvg; ?>Initial Order No. </p>
                                        <b>#<?php echo $getContractData[0]['order_no']; ?></b>
                                    </div>
                                    <?php if(($logged_in_customer_id == '') || (($logged_in_customer_id != '') && ($getContractData[0]['cancel_subscription'] == '1'))){ ?>
                                    <div class="sd_pauseCancelBtn">
                                        <?php if($disable_customer_delete_contract == 'disabled'){ ?>
                                        <button class="Polaris-Button Polaris-Button--destructive remove-btn" <?php echo $disable_customer_delete_contract; ?>>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true" class="as-h-4 as-w-4 as-text-white as-mr-2" style="margin-right: 5px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                            Cancel
                                        </button>
                                        <?php }else if($currentStatus != 'Cancelled'){ ?>
                                        <button class="Polaris-Button Polaris-Button--destructive sd_updateSubscriptionStatus remove-btn" type="button" id="sd_cancelSubscription" data-subscriptionstatus='CANCELLED' data-buttonText="Cancel Subscription" data-deliveryType = "<?php echo $getContractData[0]['delivery_billing_type']; ?>" data-billingValue = "<?php echo $getContractData[0]['billing_policy_value']; ?>" data-billingPolicy="" data-billingType="">Cancel</button>
                                        <div id="PolarisPortalsContainer"></div>
                                        <?php }?>
                                    </div>
                                    <?php }?>
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
                                    <div class="sd_subscription_tab sd_contractTabs-title sd_Tabs Polaris-Tabs__Tab--selected" group="sd_contractTabs" id="sd_contractProducts" target-tab="sd_contractProducts_content" role="tabpanel" tab-title="Products">
                                        <div class="sd_contractTabs Polaris-Tabs__Panel" id="sd_contractProducts_content">
                                            <!-- Product Tab content start here -->
                                            <div class="Polaris-Card__Section sd_productListing">
                                                <?php if($logged_in_customer_id == ''){?>
                                                <button class="Polaris-Button Polaris-Button--primary add_newProducts" data-out_of_stock ="<?php echo $getContractData[0]['add_out_of_stock_product']; ?>" type="button" product-display-style="prdouct-box" parent-id=''>
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                        <path d="M15 10a1 1 0 01-1 1h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 012 0v3h3a1 1 0 011 1zm-5-8a8 8 0 100 16 8 8 0 000-16z" fill="#5C5F62"></path>
                                                    </svg>
                                                    Add Products
                                                </button>
                                                <?php }else if($logged_in_customer_id != '' && $getContractData[0]['add_subscription_product'] == '1'){ ?>
                                                <button class="Polaris-Button Polaris-Button--primary add_newProducts_customer" data-out_of_stock ="<?php echo $getContractData[0]['add_out_of_stock_product']; ?>" type="button" product-display-style="prdouct-box" parent-id=''>
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                        <path d="M15 10a1 1 0 01-1 1h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 012 0v3h3a1 1 0 011 1zm-5-8a8 8 0 100 16 8 8 0 000-16z" fill="#5C5F62"></path>
                                                    </svg>
                                                    Add Products
                                                </button>
                                                <?php } ?>
                                                <div>
                                                    <div class="Polaris-Card">
                                                        <div id="sd_slider_1" class="sd_main_Slider">
                                                            <?php if (!empty($all_contract_products)) {
                                                                $slider_number = 1;
                                                                foreach ($all_contract_products as $key => $prdValue) {

                                                                   $edit_product_quantity = '';$add_stock_link = '';$edit_product_price='';
                                                                   $variant_id = $prdValue['variant_id'];
                                                                   $found_variant_key = array_search('gid://shopify/ProductVariant/'.$variant_id, array_column($variant_ids_array, 'id'));
                                                                   if($logged_in_customer_id == ''){
                                                                      $product_handle = '<a href="https://'.$store.'/admin/products/'.$prdValue['product_id'].'" target="_blank">'.$prdValue['product_name'].'</a>';
                                                                   }else{
                                                                      if(is_int($found_variant_key) && (strlen((string)$found_variant_key) > 0) && (!empty($variant_ids_array[$found_variant_key]))){
                                                                         $product_handle = '<a href="'.$variant_ids_array[$found_variant_key]['product']['onlineStorePreviewUrl'].'" target="_blank">'.$prdValue['product_name'].'</a>';
                                                                      }else{
                                                                         $product_handle = $prdValue['product_name'];
                                                                      }
                                                                   }

                                                                   if(is_int($found_variant_key) && (strlen((string)$found_variant_key) > 0) && (!empty($variant_ids_array[$found_variant_key]))){
                                                                         if((($logged_in_customer_id == '') && ($variant_ids_array[$found_variant_key]['inventoryQuantity'] > 0)) || (($logged_in_customer_id != '') && ($getContractData[0]['edit_product_quantity'] == '1'))){
                                                                            if((($logged_in_customer_id != '') && ($getContractData[0]['edit_out_of_stock_product_quantity'] == '1')) || (($logged_in_customer_id != '') && ($variant_ids_array[$found_variant_key]['inventoryQuantity'] > 0)) || ($logged_in_customer_id == '')){
                                                                               $edit_product_quantity = '<span class="update_prdQuantity" data-product_id="'.$prdValue['variant_id'].'"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                               <path d="M17.3191 19.9994H2.55117C1.19208 19.9994 0.0898438 18.8972 0.0898438 17.536V2.75795C0.0898438 1.39886 1.20826 0.179321 2.56735 0.179321H12.4774V1.41909H2.56735C1.88781 1.41909 1.32152 2.08043 1.32152 2.75997V17.5381C1.32152 18.2176 1.87163 18.7697 2.55117 18.7697H17.3191C17.9987 18.7697 18.6722 18.2034 18.6722 17.5239V7.61185H19.9099V17.5239C19.9099 18.883 18.6803 19.9994 17.3191 19.9994ZM9.96348 14.0311C9.78348 14.2111 9.55898 14.3001 9.3264 14.3466L3.93656 17.0122C3.32982 17.2933 2.84443 16.7493 3.0669 16.1426L5.7325 10.7527C5.77699 10.5181 5.868 10.2936 6.04598 10.1136L15.6183 0.535273C16.0996 0.0559507 16.8762 0.0559507 17.3596 0.535273L19.5358 2.71144C20.0151 3.19278 20.0151 3.97345 19.5358 4.45277L9.96348 14.0311ZM4.24801 15.3983C4.10644 15.6814 4.37947 15.9727 4.68284 15.8331L8.00978 13.8188L6.26036 12.0693L4.24801 15.3983ZM7.35248 11.4201L8.65899 12.7266C8.89764 12.9673 8.27877 12.3464 9.09382 13.1635L16.0551 6.19815L13.8648 4.03412L6.91766 10.9853C7.14822 11.2159 7.11181 11.1795 7.35248 11.4201ZM18.2313 3.14626L16.9248 1.83976C16.6841 1.59908 16.2938 1.59908 16.0531 1.83976L14.781 3.1139L16.9268 5.32243L18.2333 4.01592C18.4719 3.77727 18.4719 3.38694 18.2313 3.14626Z" fill="black"/>
                                                                               </svg>
                                                                               </span>';
                                                                            }
                                                                         }
                                                                         if((($logged_in_customer_id != '') && ($getContractData[0]['edit_product_price'] == '1')) || ($logged_in_customer_id == '') ){
                                                                            $edit_product_price = '<span class="update_prdPrice" data-product_id="'.$prdValue['variant_id'].'"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M17.3191 19.9994H2.55117C1.19208 19.9994 0.0898438 18.8972 0.0898438 17.536V2.75795C0.0898438 1.39886 1.20826 0.179321 2.56735 0.179321H12.4774V1.41909H2.56735C1.88781 1.41909 1.32152 2.08043 1.32152 2.75997V17.5381C1.32152 18.2176 1.87163 18.7697 2.55117 18.7697H17.3191C17.9987 18.7697 18.6722 18.2034 18.6722 17.5239V7.61185H19.9099V17.5239C19.9099 18.883 18.6803 19.9994 17.3191 19.9994ZM9.96348 14.0311C9.78348 14.2111 9.55898 14.3001 9.3264 14.3466L3.93656 17.0122C3.32982 17.2933 2.84443 16.7493 3.0669 16.1426L5.7325 10.7527C5.77699 10.5181 5.868 10.2936 6.04598 10.1136L15.6183 0.535273C16.0996 0.0559507 16.8762 0.0559507 17.3596 0.535273L19.5358 2.71144C20.0151 3.19278 20.0151 3.97345 19.5358 4.45277L9.96348 14.0311ZM4.24801 15.3983C4.10644 15.6814 4.37947 15.9727 4.68284 15.8331L8.00978 13.8188L6.26036 12.0693L4.24801 15.3983ZM7.35248 11.4201L8.65899 12.7266C8.89764 12.9673 8.27877 12.3464 9.09382 13.1635L16.0551 6.19815L13.8648 4.03412L6.91766 10.9853C7.14822 11.2159 7.11181 11.1795 7.35248 11.4201ZM18.2313 3.14626L16.9248 1.83976C16.6841 1.59908 16.2938 1.59908 16.0531 1.83976L14.781 3.1139L16.9268 5.32243L18.2333 4.01592C18.4719 3.77727 18.4719 3.38694 18.2313 3.14626Z" fill="black"/>
                                                                            </svg>
                                                                            </span>';
                                                                          }
                                                                         if($logged_in_customer_id == '' && ($variant_ids_array[$found_variant_key]['inventoryQuantity'] <= 0)){
                                                                            $add_stock_link = '<span class="sd_totalInventory sd_out_of_stock">Out Of Stock</span><span class="Polaris-TextStyle--variationStrong"><a href="https://'.$store.'/admin/products/'.$prdValue['product_id'].'/variants/'.$prdValue['variant_id'].'" target="_blank"><span class="Polaris-Badge Polaris-Badge--statusAttention Polaris-Badge--progressComplete">Add Stock</span></a></span>';
                                                                         }
                                                                   }
                                                                   if ($prdValue['variant_image'] == '0' || $prdValue['variant_image'] == '') {
                                                                      $imageSrc = $image_folder.'no-image.png';
                                                                   } else {
                                                                      $imageSrc = $prdValue['variant_image'];
                                                                   }
                                                                   if ($prdValue['variant_name'] == 'Default Title' || $prdValue['variant_name'] == '') {
                                                                      $variant_title = '';
                                                                   } else {
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
                                                                                    <h2 class="Polaris-Heading"><span class="Polaris-TextStyle--variationStrong"><?php echo $product_handle; ?></span></h2>
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
                                                                                <div class="sd_prdQuantity">
                                                                                    Quantity : <span class="sd_oldQuantity_<?php echo $prdValue['variant_id'] ?>" data-oldQty="<?php echo $prdValue['quantity']; ?>"><?php echo $prdValue['quantity']; ?><?php echo $edit_product_quantity; ?> </span>
                                                                                    <span class="sd_newQuantity_<?php echo $prdValue['variant_id'] ?> sd_newQuantity display-hide-label">
                                                                                        <button type="button" class="sd_updateQuantity sd_qtyMinus" data-id="<?php echo $prdValue['variant_id'] ?>">
                                                                                            <svg viewBox="0 0 20 20">
                                                                                                <path d="M15 9H5a1 1 0 100 2h10a1 1 0 100-2z" fill="#5C5F62" />
                                                                                            </svg>
                                                                                        </button>
                                                                                        <input type="number" min="1" value="<?php echo $prdValue['quantity']; ?>" class="product_qty" data-productName="<?php echo $prdValue['product_name'] . ' ' . $variant_title; ?>" id="product_qty_<?php echo $prdValue['variant_id'] ?>" width="50px;">
                                                                                        <button type="button" class="sd_updateQuantity sd_qtyPlus" data-id="<?php echo $prdValue['variant_id'] ?>">
                                                                                            <svg width="10" viewBox="0 0 12 12">
                                                                                                <path d="M11 5H7V1a1 1 0 00-2 0v4H1a1 1 0 000 2h4v4a1 1 0 002 0V7h4a1 1 0 000-2z" fill="currentColor" fill-rule="nonzero"></path>
                                                                                            </svg>
                                                                                        </button>
                                                                                    </span>
                                                                                    <?php echo $add_stock_link; ?>
                                                                                </div>
                                                                                <div class="subscription_prdPrice">Unit Price:
                                                                                    <?php if($prdValue['coupon_applied'] == 'empty'){
                                                                                        $product_price = $prdValue[$get_subscription_price_column];
                                                                                        ?>
                                                                                    <span class="sd_oldPrice_<?php echo $prdValue['variant_id'] ?>" data-oldPrice="<?php echo $prdValue[$get_subscription_price_column]; ?>"><?php echo  $order_currency.''.$prdValue[$get_subscription_price_column].' '.$edit_product_price; ?></span><span class="sd_newPrice_<?php echo $prdValue['variant_id']; ?> sd_newPrice display-hide-label"><input type="number" value="<?php echo $prdValue[$get_subscription_price_column]; ?>" id="sd_productPrc_<?php echo $prdValue['variant_id']; ?>" class="sd_product_price"></span>
                                                                                    <?php }else{
                                                                                        $product_price = (($prdValue[$get_subscription_price_column] * $prdValue['quantity']) - $prdValue['coupon_value'])/($prdValue['quantity']);
                                                                                        ?>
                                                                                    <span class="sd_oldPrice_<?php echo $prdValue['variant_id'] ?>" data-oldPrice="<?php echo $prdValue[$get_subscription_price_column]; ?>"><?php echo  $order_currency.'<span style="text-decoration:line-through;margin-right:5px;">'.$prdValue[$get_subscription_price_column].'</span>'.$product_price.' '.$edit_product_price; ?></span><span class="sd_newPrice_<?php echo $prdValue['variant_id']; ?> sd_newPrice display-hide-label"><input type="number" value="<?php echo $prdValue[$get_subscription_price_column]; ?>" id="sd_productPrc_<?php echo $prdValue['variant_id']; ?>" class="sd_product_price"></span>
                                                                                    <?php } ?>
                                                                                </div>
                                                                                <p>Total Amount : <?php echo $order_currency.''.($product_price * $prdValue['quantity']); ?></p>
                                                                            </div>
                                                                        </div>
                                                                        <!-- show discount div here -->
                                                                        <?php
                                                                            if($getContractData[0][$get_dicount_type.'discount_value'] == 0 || $getContractData[0][$get_dicount_type.'discount_value'] == '' || $getContractData[0][$get_dicount_type.'discount_value'] == '0') { ?> <?php }else{?>
                                                                        <div p-color-scheme="light">
                                                                            <span class="Polaris-Badge Polaris-Badge--statusInfo"><span class="Polaris-VisuallyHidden">Info </span>Saved <?php if($getContractData[0][$get_dicount_type.'discount_type'] == 'A'){ echo $order_currency; } echo $getContractData[0][$get_dicount_type.'discount_value'].' ';  if($getContractData[0][$get_dicount_type.'discount_type'] == 'P'){echo "%"; }?></span>
                                                                            <div id="PolarisPortalsContainer"></div>
                                                                        </div>
                                                                        <?php }?>
                                                                        <!-- show discount div ends here -->
                                                                    </div>
                                                                    <div class="sd_editProduct sd_updateProductButton_<?php echo $prdValue['variant_id'] ?> <?php if($edit_product_quantity == ''){ echo 'no_update_button';}?>">
                                                                        <?php
                                                                            if (count($all_contract_products) > 1) {
                                                                              if($logged_in_customer_id == '' || ($logged_in_customer_id != '' && $getContractData[0]['delete_product'] == '1')){
                                                                               ?>
                                                                        <div class="Polaris-Button remove_product" data-line_id="<?php echo $prdValue['contract_line_item_id']; ?>" data-product_id="<?php echo $prdValue['variant_id'] ?>">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="15px">
                                                                                <path fill-rule="evenodd" d="M14 4h3a1 1 0 011 1v1H2V5a1 1 0 011-1h3V1.5A1.5 1.5 0 017.5 0h5A1.5 1.5 0 0114 1.5V4zM8 2v2h4V2H8zM3 8h14v10.5a1.5 1.5 0 01-1.5 1.5h-11A1.5 1.5 0 013 18.5V8zm4 3H5v6h2v-6zm4 0H9v6h2v-6zm2 0h2v6h-2v-6z" fill="#5C5F62" />
                                                                            </svg>
                                                                        </div>
                                                                        <?php } }?>
                                                                    </div>
                                                                    <div class="sd_editProduct updateCancelBtn_<?php echo $prdValue['variant_id'] ?> display-hide-label">
                                                                        <button class="Polaris-Button Polaris-Button--primary update_prdQtyPrice" data-line_id="<?php echo $prdValue['contract_line_item_id']; ?>" data-product_id="<?php echo $prdValue['variant_id'] ?>" type="button">Save</button>
                                                                        <button class="Polaris-Button Polaris-Button--destructive cancel_prdUpdate" type="button" data-product_id="<?php echo $prdValue['variant_id'] ?>">Cancel</button>
                                                                    </div>
                                                                    <?php if($prdValue['product_shopify_status'] == 'Deleted'){ ?>
                                                                    <div class="sd_product_delete">
                                                                        <p> Product has been removed !</p>
                                                                    </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                            <!-- wrapper end -->
                                                            <?php }
                                                                } ?>
                                                        </div>
                                                        <?php if (count($subscription_price) > 6) { ?>
                                                        <div class="sd_slider_controls_wrapper">
                                                            <span class="sd_slider_controls" parent-slider="sd_slider_1" attr-type="prev">
                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="25px">
                                                                    <path d="M12 16a.997.997 0 01-.707-.293l-5-5a.999.999 0 010-1.414l5-5a.999.999 0 111.414 1.414L8.414 10l4.293 4.293A.999.999 0 0112 16z" fill="#5C5F62"></path>
                                                                </svg>
                                                            </span>
                                                            <span><b id="sd_sliderNumber">1 </b> of <?php echo $slider_number; ?></span>
                                                            <span class="sd_slider_controls" parent-slider="sd_slider_1" attr-type="next">
                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="25px">
                                                                    <path d="M8 16a.999.999 0 01-.707-1.707L11.586 10 7.293 5.707a.999.999 0 111.414-1.414l5 5a.999.999 0 010 1.414l-5 5A.997.997 0 018 16z" fill="#5C5F62" />
                                                                </svg>
                                                            </span>
                                                        </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--Product Tab content ends here-->
                                <div class="sd_subscription_tab sd_contractTabs-title sd_Tabs" id="sd_contractShippingAddress" group="sd_contractTabs" target-tab="sd_contractShippingAddress_content"  role="tabpanel" tab-title="Shipping and Billing Address">
                                    <div class="sd_contractTabs Polaris-Tabs__Panel Polaris-Tabs__Panel--hidden sd_contractShipAddress" id="sd_contractShippingAddress_content" >
                                        <!-- shipping address start -->
                                        <div class="sd_shippingAndBilling">
                                            <div class="sd_contractBillingShipping">
                                                <div class="Polaris-Card__Section">
                                                    <div class="sd_billingHeading">
                                                        <h2 class="Polaris-Heading">Shipping Address</h2>
                                                        <div id="PolarisPortalsContainer"></div>
                                                    </div>
                                                    <div class="sd_shippingAddressData" id="sd_shippingAddressData">
                                                        <?php if (!empty($getContractData[0]['shipping_last_name'])) { ?>
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
                                                                <?php if($logged_in_customer_id == '' || ($logged_in_customer_id != '' && $getContractData[0]['edit_shipping_address'] == '1')){
                                                                    if (!empty($getContractData[0]['shipping_last_name'])) {
                                                                    ?>
                                                                <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain sd_shippingEditBtn"><button title="Edit" type="button" data-redirect-link="" data-query-string="" class="Polaris-Button sd_updateShippingAddress" id="sd_updateShippingAddress" data-contract_id="<?php echo $prdValue['contract_id'] ?>">Update</button></div>
                                                                <?php }else{ ?>
                                                                <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain sd_shippingEditBtn"><button title="Edit" type="button" data-redirect-link="" data-query-string="" class="Polaris-Button sd_updateShippingAddress" id="sd_updateShippingAddress" data-contract_id="<?php echo $prdValue['contract_id'] ?>">Add</button></div>
                                                                <?php } } ?>
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
                                                    <form class="form-horizontal formShippingAddressSave" method="post" name="shippingAddressSave" id="shippingAddressSave" autocomplete="off">
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
                                                                    <div class="Polaris-FormLayout__Item">
                                                                        <div class="">
                                                                            <div class="Polaris-Labelled__LabelWrapper">
                                                                                <div class="Polaris-Label"><label id="shipPriceLabel" for="shipPrice" class="Polaris-Label__Text">Delivery Price</label></div>
                                                                            </div>
                                                                            <div class="Polaris-Connected">
                                                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                                    <div class="Polaris-TextField">
                                                                                        <?php if($logged_in_customer_id == ''){ ?>
                                                                                        <input id="shipDeliveryPrice" autocomplete="off" class="Polaris-TextField__Input" type="number" aria-labelledby="PolarisTextField7Label" aria-invalid="false" data-text="delivery_price" value="<?php echo $getContractData[0]['shipping_delivery_price']; ?>" name="delivery_price" maxlength="4">
                                                                                        <?php }else{ ?>
                                                                                        <input id="" autocomplete="off" class="Polaris-TextField__Input" type="number" aria-labelledby="PolarisTextField7Label" aria-invalid="false" data-text="Zip" value="<?php echo $getContractData[0]['shipping_delivery_price']; ?>" name="" maxlength="4" disabled>
                                                                                        <?php } ?>
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
                                        </div>
                                        <!--Shipping Address Tab content ends here-->
                                    </div>
                                </div>
                                <div id="PolarisPortalsContainer">
                                    <div data-portal-id="popover-Polarisportal1"></div>
                                </div>
                                <div class="sd_subscription_tab sd_contractTabs-title sd_Tabs" role="tabpanel" group="sd_contractTabs" tab-title="Payment Details" id="sd_contractPaymentDetails" target-tab="sd_contractPaymentDetails_content">
                                    <div class="sd_contractTabs Polaris-Tabs__Panel Polaris-Tabs__Panel--hidden" id="sd_contractPaymentDetails_content">
                                        <div class="sd_paymentDetail">
                                            <div class="Polaris-Card__Section">
                                                <div class="sd_contractPaymentDetails">
                                                    <div class="center">
                                                        <div class="card">
                                                            <div class="flip">
                                                                <div class="front">
                                                                    <div class="strip-bottom"></div>
                                                                    <div class="strip-top"></div>
                                                                    <div class="investor"><?php echo $brand; ?></div>
                                                                    <div class="card-number">
                                                                        <div class="section">XXXX</div>
                                                                        <div class="section">XXXX</div>
                                                                        <div class="section">XXXX</div>
                                                                        <div class="section"><?php echo $lastDigits; ?></div>
                                                                    </div>
                                                                    <?php if($customer_payment_type != 'CustomerPaypalBillingAgreement'){ ?>
                                                                    <div class="end"><span class="end-text">exp. end:</span><span class="end-date"><?php echo $expiryMonth. '/' . $expiryYear ?></span></div>
                                                                    <?php } ?>
                                                                    <div class="card-holder"><?php echo $getContractData[0]['name']; ?></div>
                                                                    <div class="master">
                                                                        <div class="circle master-red"></div>
                                                                        <div class="circle master-yellow"></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                </div>
                                <!-- payment details tab content ends here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="Polaris-Layout__Section sd_contracts sd_contractDetails">
                    <div>
                        <div class="Polaris-Card">
                            <div>
                                <div class="Polaris-Tabs__Wrapper">
                                    <ul role="tablist" class="Polaris-Tabs">
                                        <li class="Polaris-Tabs__TabContainer" role="presentation"><button group="sd_orderTabs" role="tab" type="button" tabindex="0" class="sd_orderTabs-title Polaris-Tabs__Tab Polaris-Tabs__Tab--selected sd_Tabs" aria-selected="true" target-tab="sd_pastOrders_content" aria-label="All customers"><span class="Polaris-Tabs__Title">Past Orders</span></button></li>
                                        <?php if ($getContractData[0]['contract_status'] == 'A' || ($getContractData[0]['delivery_policy_value'] != $getContractData[0]['billing_policy_value'])) { ?>
                                        <li class="Polaris-Tabs__TabContainer" role="presentation"><button group="sd_orderTabs" role="tab" type="button" tabindex="-1" class="sd_orderTabs-title Polaris-Tabs__Tab sd_Tabs" aria-selected="false" target-tab="sd_upcomingFulfillments_content"><span class="Polaris-Tabs__Title"><?php if ($getContractData[0]['delivery_policy_value'] == $getContractData[0]['billing_policy_value']) {                                                                                                    echo "Upcoming Orders";
                                            } else {
                                               echo "Upcoming Fulfillments";
                                            } ?></span></button></li>
                                        <?php } ?>
                                        <li class="Polaris-Tabs__TabContainer" role="presentation"><button group="sd_orderTabs" role="tab" type="button" tabindex="-1" class="sd_orderTabs-title Polaris-Tabs__Tab sd_Tabs" aria-selected="false" target-tab="sd_pendingOrders_content"><span class="Polaris-Tabs__Title">Pending Orders</span></button></li>
                                        <li class="Polaris-Tabs__TabContainer" role="presentation"><button group="sd_orderTabs" role="tab" type="button" tabindex="-1" class="sd_orderTabs-title Polaris-Tabs__Tab sd_Tabs" aria-selected="false" target-tab="sd_failureOrders_content"><span class="Polaris-Tabs__Title">Failure Orders</span></button></li>
                                        <li class="Polaris-Tabs__TabContainer" role="presentation"><button group="sd_orderTabs" role="tab" type="button" tabindex="-1" class="sd_orderTabs-title Polaris-Tabs__Tab sd_Tabs" aria-selected="false" target-tab="sd_skipOrders_content"><span class="Polaris-Tabs__Title"><?php if ($getContractData[0]['delivery_policy_value'] == $getContractData[0]['billing_policy_value']) {
                                            echo " Skip Orders";
                                            } else {
                                               echo "Rescheduled Fulfillments of current cycle";
                                            } ?></span></button></li>
                                    </ul>
                                </div>
                                <div class="sd_subscription_tab sd_orderTabs-title sd_Tabs Polaris-Tabs__Tab--selected" group="sd_orderTabs" target-tab="sd_pastOrders_content"  role="tabpanel" tab-title="Past Orders">
                                    <div class="sd_orderTabs Polaris-Tabs__Panel" role="tabpanel" id="sd_pastOrders_content" aria-labelledby="all-customers-1" tabindex="-1">
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
                                                                                  $productImage = $SHOPIFY_DOMAIN_URL . '/backend/assets/images/no-image.png';
                                                                               }
                                                                               if($store_all_subscription_plans[$item_index]['variant_name'] != 'Default Title'){
                                                                                  $variantTitle = '-' . $store_all_subscription_plans[$item_index]['variant_name'];
                                                                               }
                                                                                  if(trim($variantTitle == '-')){
                                                                                     $variantTitle = '';
                                                                                  }
                                                                               if($logged_in_customer_id == ''){
                                                                                 $order_number = '<a href="https://'. $store.'/admin/orders/'.$itemValue['order_id'].'" target="_blank">#'.$itemValue['order_no'].'</a>';
                                                                               }else{
                                                                                  $order_number = '#'.$itemValue['order_no'];
                                                                               }
                                                                               $variantHtml .= '<span class="Polaris-Tag" style="margin-right:10px; margin-top: 10px;"><img src="' . $productImage . '" width="20" height="20" style="margin-right: 10px;"><span>' . $store_all_subscription_plans[$item_index]['product_name'] . '' . $variantTitle . '</span></span>';
                                                                            } ?>
                                                                        <div id="sd_display" style="display: flex;">
                                                                            <p><span>Order Number</span><?php echo $order_number; ?></p>
                                                                            <p><span>Order Date</span><?php echo getShopTimezoneDate(($itemValue['updated_at']), $shop_timezone); ?></p>
                                                                        </div>
                                                                    </div>
                                                                    <?php if($logged_in_customer_id == ''){?>
                                                                    <div class="Polaris-Stack__Item">
                                                                        <div class="Polaris-ButtonGroup">
                                                                            <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain">
                                                                                <a class="Polaris-Navigation__Item arrow-icon no-focus"  tabindex="0" data-polaris-unstyled="true" href="https://<?php echo $store; ?>/admin/orders/<?php echo $itemValue['order_id']; ?>" target="_blank">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15px" height="20px" padding="10px" viewBox="0 0 20 20">
                                                                                        <path fill-rule="evenodd" d="M19.928 9.629c-2.137-5.343-6.247-7.779-10.355-7.565-4.06.21-7.892 3.002-9.516 7.603l-.118.333.118.333c1.624 4.601 5.455 7.393 9.516 7.603 4.108.213 8.218-2.222 10.355-7.565l.149-.371-.149-.371zm-9.928 5.371a5 5 0 1 0 0-10 5 5 0 0 0 0 10z"></path>
                                                                                        <circle cx="10" cy="10" r="3"></circle>
                                                                                    </svg>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php } ?>
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
                                                              $productImage = $SHOPIFY_DOMAIN_URL . '/backend/assets/images/no-image.png';
                                                           }
                                                           if($store_all_subscription_plans[$item_index]['variant_name'] != 'Default Title'){
                                                              $variantTitle = '-' . $store_all_subscription_plans[$item_index]['variant_name'];
                                                           }
                                                           if(trim($variantTitle == '-')){
                                                              $variantTitle = '';
                                                           }
                                                           if($logged_in_customer_id == ''){
                                                             $order_number = '<a href="https://'.$store.'/admin/orders/'.$getContractData[0]['order_id'].'" target="_blank">#'.$getContractData[0]['order_no'].'</a>';
                                                           }else{
                                                              $order_number = '#'.$getContractData[0]['order_no'];
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
                                                                            <p><span class="sd_upcomingOrderDate">Order Number</span><?php echo $order_number; ?></p>
                                                                            <p><span class="sd_upcomingOrderDate">Order Date</span><?php echo getShopTimezoneDate($getContractData[0]['created_at'], $shop_timezone);  ?></p>
                                                                        </div>
                                                                    </div>
                                                                    <?php if($logged_in_customer_id == ''){ ?>
                                                                    <div class="Polaris-Stack__Item">
                                                                        <div class="Polaris-ButtonGroup">
                                                                            <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain">
                                                                                <a class="Polaris-Navigation__Item arrow-icon no-focus" data-query-string="4222320878" tabindex="0" data-polaris-unstyled="true" href="https://<?php echo $store; ?>/admin/orders/<?php echo $getContractData[0]['order_id']; ?>" target="_blank">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="15px" padding="20px" viewBox="0 0 20 20">
                                                                                        <path fill-rule="evenodd" d="M19.928 9.629c-2.137-5.343-6.247-7.779-10.355-7.565-4.06.21-7.892 3.002-9.516 7.603l-.118.333.118.333c1.624 4.601 5.455 7.393 9.516 7.603 4.108.213 8.218-2.222 10.355-7.565l.149-.371-.149-.371zm-9.928 5.371a5 5 0 1 0 0-10 5 5 0 0 0 0 10z"></path>
                                                                                        <circle cx="10" cy="10" r="3"></circle>
                                                                                    </svg>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php } ?>
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
                                <div class="sd_subscription_tab sd_orderTabs-title sd_Tabs" group="sd_orderTabs" target-tab="sd_upcomingFulfillments_content"  role="tabpanel" tab-title="<?php if ($getContractData[0]['delivery_policy_value'] == $getContractData[0]['billing_policy_value']){echo "Upcoming Orders"; }else{echo "Upcoming Fulfillments";}?>">
                                    <div class="sd_orderTabs Polaris-Tabs__Panel Polaris-Tabs__Panel--hidden" id="sd_upcomingFulfillments_content" role="tabpanel" aria-labelledby="accepts-marketing-1" tabindex="-1">
                                        <div class="Polaris-Card__Section">
                                            <!--Upcoming Orders or Fulfillments List here1234-->
                                            <div class="Polaris-ResourceList__ResourceListWrapper">
                                                <ul class="Polaris-ResourceList" aria-live="polite">
                                                    <?php
                                                        if ($getContractData[0]['delivery_policy_value'] == $getContractData[0]['billing_policy_value']) {
                                                           if ($getContractData[0]['contract_status'] == 'A') {
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
                                                                                     $productImage = $SHOPIFY_DOMAIN_URL . '/backend/assets/images/no-image.png';
                                                                                  }
                                                                                  $variantHtml .= '<span class="Polaris-Tag" style="margin-right:10px; margin-top: 10px;"><img src="' . $productImage . '" width="20" height="20" style="margin-right: 10px;"><span>' . $prdValue['product_name'] . '' . $variantTitle . '</span></span>';
                                                                               }
                                                                            } ?>
                                                                        <div class="sd_upcomingDate">
                                                                            <span class="sd_upcomingOrderDate">Order Date</span>
                                                                            <p><?php
                                                                                // echo $next_billing_date;
                                                                                echo getShopTimezoneDate(($next_billing_date . ' ' . $date_time_array[1]), $shop_timezone);
                                                                                ?></p>
                                                                        </div>
                                                                        <div class="sd_upcomingFulfillment">
                                                                            <span class="sd_upcomingOrderDate">Status:</span><span class="Polaris-Badge Polaris-Badge--statusAttention Polaris-Badge--progressPartiallyComplete"><span class="Polaris-VisuallyHidden">Info </span>Queued</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="sd_billingAttempt">
                                                                        <div class="sd_attemptBillingAndSkip">
                                                                            <?php
                                                                                if ($attemptBilling == true) {
                                                                                   if(($logged_in_customer_id == '') ||  ($logged_in_customer_id != '' && $getContractData[0]['attempt_billing'] == '1')){
                                                                                   ?>
                                                                            <button class="Polaris-Button Polaris-Button--primary sd_attemptBilling" data-billingPolicy="<?php echo $getContractData[0]['billing_policy_value'];  ?>" data-billing_date="<?php echo $next_billing_date; ?>" type="button">Attempt Billing</button>
                                                                            <?php } if($logged_in_customer_id == '' || ($logged_in_customer_id != '' && $getContractData[0]['skip_upcoming_order'] == '1')) {?>
                                                                            <button class="Polaris-Button Polaris-Button--primary sd_skipOrder" data-billingPolicy="<?php echo $getContractData[0]['billing_policy_value'];  ?>" data-billingType="<?php echo $getContractData[0]['delivery_billing_type']; ?>" data-billing_date="<?php echo $next_billing_date; ?>" type="button">Skip Order</button>
                                                                            <?php $attemptBilling = false; } }?>
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
                                                        }
                                                        }

                                                        else {
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
                                                        $get_fulfillment_orders = '
                                                        {
                                                        subscriptionContract(id : "gid://shopify/SubscriptionContract/'.$contract_id.'"){
                                                           createdAt
                                                           orders(first : 1, reverse:true){
                                                              edges{
                                                              cursor
                                                                 node{
                                                                    id
                                                                    name
                                                                 }
                                                              }
                                                           }
                                                        }
                                                        }';
                                                        $contract_detail = $shopify_graphql_object->GraphQL->post($get_fulfillment_orders,null,null,null);
                                                        $graphql_order_id = $contract_detail['data']['subscriptionContract']['orders']['edges'][0]['node']['id'];

                                                        $all_contract_line_items = [];
                                                        $orders_contracts_lineItems = '{
                                                        order(id:"'.$graphql_order_id.'"){
                                                          id
                                                          name
                                                           lineItems(first:15){
                                                              edges{
                                                                 node{
                                                                    id
                                                                    contract{
                                                                    id
                                                                    }
                                                                 }
                                                              }
                                                           }
                                                        }
                                                        }';
                                                        $line_items_with_contractId = $shopify_graphql_object->GraphQL->post($orders_contracts_lineItems,null,null,null);
                                                        foreach($line_items_with_contractId['data']['order']['lineItems']['edges'] as $key=>$value){
                                                        $contract_id = substr($value['node']['contract']['id'], strrpos($value['node']['contract']['id'], '/') + 1);
                                                        $all_contract_line_items[$contract_id][] = substr($value['node']['id'], strrpos($value['node']['id'], '/') + 1);
                                                        }
                                                        $rest_order_id = substr($graphql_order_id, strrpos($graphql_order_id, '/') + 1);
                                                        $order_upcoming_fulfillments = PostPutApi('https://'. $store.'/admin/api/'.$SHOPIFY_API_VERSION.'/orders/'.$rest_order_id.'/fulfillment_orders.json?status=scheduled','GET',$this->access_token,'');
                                                        // $get_upcoming_fulfillments_data = $mainobj->getUpcomingFulfillments(trim($contractId));
                                                        $order_name = $contract_detail['data']['subscriptionContract']['orders']['edges'][0]['node']['name'];
                                                        $all_contract_line_items_array = $all_contract_line_items;
                                                        $all_contract_line_items = ($all_contract_line_items_array[trim($contractId)]);
                                                        foreach($order_upcoming_fulfillments['fulfillment_orders'] as $key => $value) {
                                                        $line_items_array = array_column($value['line_items'], 'line_item_id');
                                                        $check_contract_fulfillment = array_intersect($all_contract_line_items,$line_items_array);
                                                        if(!empty($check_contract_fulfillment)){
                                                        $complete_fulfillment_date = $value['fulfill_at'];
                                                        $fulfill_date = getShopTimezoneDate(($complete_fulfillment_date), $shop_timezone);
                                                        $actualFulfillmentDate = strtok($complete_fulfillment_date, 'T');
                                                        ?>
                                                    <li class="Polaris-ResourceItem__ListItem">
                                                        <div class="Polaris-ResourceItem__ItemWrapper">
                                                            <div class="Polaris-ResourceItem">
                                                                <a class="Polaris-ResourceItem__Link" id="PolarisResourceListItemOverlay4" data-polaris-unstyled="true"></a>
                                                                <div class="Polaris-ResourceItem__Container" id="145">
                                                                    <div class="Polaris-ResourceItem__Content sd_fulfillments">
                                                                        <p><span>Fulfill Date</span> <?php echo getShopTimezoneDate(($complete_fulfillment_date), $shop_timezone);  ?></p>
                                                                        <p><span class="sd_upcomingOrderDate">Status</span><span class="Polaris-Badge Polaris-Badge--statusAttention Polaris-Badge--progressPartiallyComplete"><span class="Polaris-VisuallyHidden">Info </span>SCHEDULED</span></p>
                                                                    </div>
                                                                    <div>
                                                                        <?php if($logged_in_customer_id == '' || ($logged_in_customer_id != '' && $getContractData[0]['skip_upcoming_fulfillment'] == '1')) {?>
                                                                        <span class="reschedule">
                                                                        <button class="Polaris-Button Polaris-Button--primary sd_skipFulfillment" data-fulfillOrderId="gid://shopify/FulfillmentOrder/<?php echo $value['id'] ?>" data-orderName="<?php echo $order_name;  ?>" data-billingCycle="<?php echo $getContractData[0]['billing_policy_value'] ?>" data-deliveryCycle="<?php echo $getContractData[0]['delivery_policy_value'] ?>" data-delivery_billing_type="<?php echo $getContractData[0]['delivery_billing_type']; ?>" data-actualOrderDate="<?php echo $actualFulfillmentDate; ?>" data-orderId="gid://shopify/Order/<?php echo $value['order_id'];  ?>" type="button">Skip Fulfillment</button>
                                                                        </span>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <?php
                                                        }
                                                        }
                                                        ?>
                                                    <?php }  ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="sd_subscription_tab sd_orderTabs-title sd_Tabs" group="sd_orderTabs" target-tab="sd_pendingOrders_content"  role="tabpanel" tab-title="Pending Orders">
                                    <div class="sd_orderTabs Polaris-Tabs__Panel Polaris-Tabs__Panel--hidden" id="sd_pendingOrders_content" role="tabpanel" aria-labelledby="repeat-customers-1" tabindex="-1">
                                        <div class="Polaris-Card__Section">
                                            <!--Pending Orders List here-->
                                            <div class="Polaris-ResourceList__ResourceListWrapper">
                                                <ul class="Polaris-ResourceList" aria-live="polite">
                                                    <?php if (!empty($pendingStatusArray)) {
                                                        foreach ($pendingStatusArray as $value) {
                                                            $pending_date_time_array = explode(' ', $value['updated_at']);
                                                           ?>
                                                    <li class="Polaris-ResourceItem__ListItem">
                                                        <div class="Polaris-ResourceItem__ItemWrapper">
                                                            <div class="Polaris-ResourceItem">
                                                                <a class="Polaris-ResourceItem__Link" id="PolarisResourceListItemOverlay4" data-polaris-unstyled="true"></a>
                                                                <div class="Polaris-ResourceItem__Container" id="145">
                                                                    <div class="Polaris-ResourceItem__Content sd_pendingOrders">
                                                                        <p><span>Order Number</span>Pending</p>
                                                                        <p><span>Order Date</span><?php echo getShopTimezoneDate(($value['renewal_date'] . ' ' . $pending_date_time_array[1]), $shop_timezone); ?></p>
                                                                        <p><span>Status</span><span class="Polaris-Badge Polaris-Badge--statusAttention Polaris-Badge--progressPartiallyComplete"><span class="Polaris-VisuallyHidden">Info </span><?php echo $value['status']; ?></span></p>
                                                                        <p><span>Order Attempt Date</span><?php echo getShopTimezoneDate(($value['billing_attempt_date'] . ' ' . $pending_date_time_array[1]), $shop_timezone); ?></p>
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
                                </div>
                                <div class="sd_subscription_tab sd_orderTabs-title sd_Tabs" group="sd_orderTabs" target-tab="sd_failureOrders_content"  role="tabpanel" tab-title="Failure Orders">
                                    <div class="sd_orderTabs Polaris-Tabs__Panel Polaris-Tabs__Panel--hidden" id="sd_failureOrders_content" role="tabpanel" aria-labelledby="prospects-1" tabindex="-1">
                                        <div class="Polaris-Card__Section">
                                            <div class="Polaris-ResourceList__ResourceListWrapper">
                                                <ul class="Polaris-ResourceList" aria-live="polite">
                                                    <?php if (!empty($failureStatusArray)) {
                                                        foreach ($failureStatusArray as $value) {
                                                           $failure_date_time_array = explode(' ', $value['updated_at']);
                                                           $failure_created_time_array = explode(' ', $value['created_at']);
                                                           ?>
                                                    <li class="Polaris-ResourceItem__ListItem">
                                                        <div class="Polaris-ResourceItem__ItemWrapper">
                                                            <div class="Polaris-ResourceItem">
                                                                <a class="Polaris-ResourceItem__Link" id="PolarisResourceListItemOverlay4" data-polaris-unstyled="true"></a>
                                                                <div class="Polaris-ResourceItem__Container" id="145">
                                                                    <div class="Polaris-ResourceItem__Content sd_failureOrders">
                                                                        <p><span>Order Number</span>Failure</p>
                                                                        <p><span>Order Date</span><?php echo getShopTimezoneDate(($value['renewal_date'] . ' ' . $failure_date_time_array[1]), $shop_timezone); ?></p>
                                                                        <p><span>Status</span><span class="Polaris-Badge Polaris-Badge--statusAttention Polaris-Badge--progressPartiallyComplete"><span class="Polaris-VisuallyHidden">Info </span><?php echo $value['status']; ?></span></p>
                                                                        <p><span>Order Attempt Date</span><?php echo getShopTimezoneDate(($value['billing_attempt_date'] . ' ' . $failure_created_time_array[1]), $shop_timezone); ?></p>
                                                                        <p><span>Order Failure Date</span><?php echo getShopTimezoneDate(($value['billingAttemptResponseDate'] . ' ' . $failure_date_time_array[1]), $shop_timezone); ?>
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
                                </div>
                                <div class="sd_subscription_tab sd_orderTabs-title sd_Tabs" group="sd_orderTabs" target-tab="sd_skipOrders_content"  role="tabpanel" tab-title="<?php if ($getContractData[0]['delivery_policy_value'] == $getContractData[0]['billing_policy_value']) { echo " Skip Orders"; } else { echo "Rescheduled Fulfillments of current cycle";
                                    } ?>">
                                    <div class="sd_orderTabs Polaris-Tabs__Panel Polaris-Tabs__Panel--hidden" id="sd_skipOrders_content" role="tabpanel" aria-labelledby="prospects-1" tabindex="-1">
                                        <div class="Polaris-Card__Section">
                                            <div class="Polaris-ResourceList__ResourceListWrapper">
                                                <ul class="Polaris-ResourceList" aria-live="polite" id="upcoming_order_fulfillment">
                                                    <?php if ($getContractData[0]['delivery_policy_value'] == $getContractData[0]['billing_policy_value']) {
                                                        if (!empty($skipStatusArray)) {
                                                           foreach ($skipStatusArray as $value) {
                                                              $skip_date_time_array =explode(' ', $value['updated_at']);
                                                              ?>
                                                    <li class="Polaris-ResourceItem__ListItem">
                                                        <div class="Polaris-ResourceItem__ItemWrapper">
                                                            <div class="Polaris-ResourceItem">
                                                                <a class="Polaris-ResourceItem__Link" id="PolarisResourceListItemOverlay4" data-polaris-unstyled="true"></a>
                                                                <div class="Polaris-ResourceItem__Container" id="145">
                                                                    <div class="Polaris-ResourceItem__Content">
                                                                        <div class="Polaris-ResourceItem__Content sd_skipOrders">
                                                                            <p><span>Order Number</span>Skipped</p>
                                                                            <p><span>Order Date</span><?php echo getShopTimezoneDate(($value['renewal_date'] . ' ' . $date_time_array[1]), $shop_timezone); ?></p>
                                                                            <p><span>Status</span><span class="Polaris-Badge Polaris-Badge--statusAttention Polaris-Badge--progressPartiallyComplete"><span class="Polaris-VisuallyHidden">Info </span>Skipped</span></p>
                                                                            <p><span>Order Skipped Date</span><?php echo getShopTimezoneDate(($value['billing_attempt_date'] . ' ' . $skip_date_time_array[1]), $shop_timezone); ?></p>
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
                                                        $order_id = $order_upcoming_fulfillments['fulfillment_orders'][0]['order_id'];
                                                        // $whereCondition = array(
                                                        //    'order_id' => $get_upcoming_fulfillments_data['order_upcoming_fulfillments']['fulfillment_orders'][0]['order_id'],
                                                        //    'contract_id' =>  $contractId
                                                        // );
                                                        // $get_rescheduled_data = $mainobj->table_row_value('reschedule_fulfillment', 'all', $whereCondition, 'and', '');
                                                        $get_rescheduled_data_qry = $db->query("SELECT * FROM 'reschedule_fulfillment' WHERE order_id = '$order_id' and contract_id = '$contract_id'");
                                                        $get_rescheduled_data = $get_rescheduled_data_qry->fetchAll(PDO::FETCH_ASSOC);
                                                        if (!empty($get_rescheduled_data)) {
                                                           foreach ($get_rescheduled_data as $value) {
                                                              $schedule_date_time_array = explode(' ', $value['updated_at']);
                                                           ?>
                                                    <li class="Polaris-ResourceItem__ListItem">
                                                        <div class="Polaris-ResourceItem__ItemWrapper">
                                                            <div class="Polaris-ResourceItem">
                                                                <a class="Polaris-ResourceItem__Link" id="PolarisResourceListItemOverlay4" data-polaris-unstyled="true"></a>
                                                                <div class="Polaris-ResourceItem__Container" id="145">
                                                                    <div class="Polaris-ResourceItem__Content">
                                                                        <div class="Polaris-ResourceItem__Content sd_rescheduleOrders">
                                                                            <p><span>Order Number</span>#<?php echo $value['order_no']; ?></p>
                                                                            <p><span>Actual Fulfillment Date</span><?php echo getShopTimezoneDate(($value['actual_fulfillment_date'] . ' ' . $date_time_array[1]), $shop_timezone); ?></p>
                                                                            <p><span>Rescheduled Date</span><?php echo getShopTimezoneDate(($value['new_fulfillment_date'] . ' ' . $schedule_date_time_array[1]), $shop_timezone);   ?></p>
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
                        </div>
                        <div id="PolarisPortalsContainer">
                            <div data-portal-id="popover-Polarisportal1"></div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <?php //} ?>
            </div>
        </div>
    </div>
    <?php
        }else{
        $getContractData_qry = $db->query("SELECT o.id,o.store_id,o.order_id,o.contract_id,
        o.billing_policy_value,o.delivery_policy_value,
        o.delivery_billing_type,o.contract_status,
        o.order_token,o.order_no,o.created_at,o.updated_at,o.new_contract,
        o.next_billing_date,
        c.name,c.email,c.shopify_customer_id,d.shop_timezone FROM `subscriptionOrderContract` as o, `customers` as c, `store_details` as d  WHERE o.store_id = '$store_id' and o.shopify_customer_id =c.shopify_customer_id and d.store_id = o.store_id $whereCustomerCondition ORDER BY o.id DESC");
        $getContractData = $getContractData_qry->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($getContractData)){
          $shop_timezone = $getContractData[0]['shop_timezone'];
        }
        ?>
    <div class="Polaris-Layout__Section sd-dashboard-page  <?php echo  $parent_class; ?>">
        <div>
            <div class="Polaris-Card__Header sd_contractHeader">
                <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                    <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                        <h2 class="Polaris-Heading">Subscriptions</h2>
                    </div>
                    <?php if($logged_in_customer_id == ''){ ?>
                    <div class="Polaris-Stack__Item">
                        <div class="Polaris-ButtonGroup">
                            <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain">
                                <button class="Polaris-Button Polaris-Button--primary sd_selectSetting" data-popup="contract_setting" type="button">
                                    <svg viewBox="0 0 20 20">
                                        <path d="M18.414 1.586a2 2 0 010 2.828l-3 3-1.115 1.115-2.828-2.828 1.232-1.233.015-.015 2.868-2.867a2 2 0 012.828 0zm-8.47 11.299l-2.788-2.787a4.67 4.67 0 01-5.919-5.901L3.76 6.719a1.5 1.5 0 002.121 0l.84-.84a1.5 1.5 0 000-2.12L4.197 1.236a4.67 4.67 0 015.9 5.919l2.787 2.787 5.506 5.506a2.08 2.08 0 01-2.942 2.942l-5.506-5.506zm-1.415 1.414l-3.287 3.287L1 19l1.414-4.243 3.287-3.286 2.828 2.828z" fill="#929eab"></path>
                                    </svg>
                                    <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Subscripiton Setting</span></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="Polaris-Page__Content">
                <div class="">
                    <div class="">
                        <div class="Polaris-DataTable__Navigation">
                            <button class="Polaris-Button Polaris-Button--disabled Polaris-Button--plain Polaris-Button--iconOnly" aria-label="Scroll table left one column" type="button" disabled="">
                                <span class="Polaris-Button__Content">
                                    <span class="Polaris-Button__Icon">
                                        <span class="Polaris-Icon">
                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                <path d="M12 16a.997.997 0 0 1-.707-.293l-5-5a.999.999 0 0 1 0-1.414l5-5a.999.999 0 1 1 1.414 1.414L8.414 10l4.293 4.293A.999.999 0 0 1 12 16z"></path>
                                            </svg>
                                        </span>
                                    </span>
                                </span>
                            </button>
                            <button class="Polaris-Button Polaris-Button--plain Polaris-Button--iconOnly" aria-label="Scroll table right one column" type="button">
                                <span class="Polaris-Button__Content">
                                    <span class="Polaris-Button__Icon">
                                        <span class="Polaris-Icon">
                                            <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                <path d="M8 16a.999.999 0 0 1-.707-1.707L11.586 10 7.293 5.707a.999.999 0 1 1 1.414-1.414l5 5a.999.999 0 0 1 0 1.414l-5 5A.997.997 0 0 1 8 16z"></path>
                                            </svg>
                                        </span>
                                    </span>
                                </span>
                            </button>
                        </div>
                        <div class="Polaris-DataTable sd_common_datatable">
                            <div class="Polaris-DataTable__ScrollContainer">
                                <table class="Polaris-DataTable__Table" id="subscriptionTable">
                                    <thead>
                                        <tr>
                                            <th data-polaris-header-cell="true" class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--firstColumn Polaris-DataTable__Cell--header" scope="col" aria-sort="none">Customer</th>
                                            <th data-polaris-header-cell="true" class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--firstColumn Polaris-DataTable__Cell--header" scope="col" aria-sort="none">Subscription ID</th>
                                            <th data-polaris-header-cell="true" class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--header Polaris-DataTable__Cell--numeric" scope="col" aria-sort="none">Order No.</th>
                                            <th data-polaris-header-cell="true" class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--header Polaris-DataTable__Cell--numeric" scope="col" aria-sort="none">Plan Type</th>
                                            <th data-polaris-header-cell="true" class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--header Polaris-DataTable__Cell--numeric" scope="col" aria-sort="none">Order Date</th>
                                            <th data-polaris-header-cell="true" class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--header Polaris-DataTable__Cell--numeric" scope="col" aria-sort="none">Renewal On</th>
                                            <th data-polaris-header-cell="true" class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--header Polaris-DataTable__Cell--numeric" scope="col" aria-sort="none">Status</th>
                                            <th data-polaris-header-cell="true" class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--header Polaris-DataTable__Cell--numeric" scope="col" aria-sort="none">View</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            if(!empty($getContractData)){
                                              foreach ($getContractData as $value) {
                                                if($logged_in_customer_id == ''){
                                                   $orderLink = 'https://'.$store.'/admin/orders/'.$value['order_id'];
                                                }else{
                                                   $orderLink = 'https://'.$store.'/account/orders/'.$value['order_token'];
                                                }
                                                $unread_contract = '';
                                                $add_new_class = '';
                                                $customer_name = $value['name'];
                                                $updated_at_date = $value['updated_at'];
                                                $date_time_array = explode(' ', $updated_at_date);
                                                if (isset($customer_name) && is_string($customer_name) && !empty($customer_name)) {
                                                   $initials = implode('', array_map(function($name) {
                                                      if (!empty($name)) {
                                                         return strtoupper($name[0]);
                                                      } else {
                                                         return ''; // Return empty string if $name is empty
                                                      }
                                                   }, explode(' ', $customer_name)));
                                                } else {
                                                   $initials = $customer_name;
                                                }
                                                if($value['delivery_policy_value'] == $value['billing_policy_value']){ $planType = "Pay Per Delivery"; }else{ $planType = "Prepaid"; }
                                                if($value['contract_status'] == 'A'){ $contract_status = "Active"; $contract_statusClass = "statusSuccess"; $addCircle='<span class="Polaris-Badge__Pip"><span class="Polaris-VisuallyHidden"></span></span>';}else if($value['contract_status'] == 'P'){ $contract_status = "Paused"; $contract_statusClass = "statusAttention"; $addCircle = '';}else if($value['contract_status'] == 'C'){ $contract_status = "Cancelled"; $contract_statusClass = "statusInfo"; $addCircle = '';}
                                                if($value['new_contract'] == '1'){
                                                  $unread_contract = '<div p-color-scheme="light" class="sd_contract_unread"><span class="Polaris-Badge Polaris-Badge--statusAttention"><span class="Polaris-VisuallyHidden">New</span><span>New</span></span><div id="PolarisPortalsContainer"></div></div>';
                                                  $add_new_class = 'sd_new_contract';
                                                }
                                                echo '<tr class="Polaris-DataTable__TableRow Polaris-DataTable--hoverable '.$add_new_class.'">';
                                                echo '<td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric"><div class="sd_customerData"><span class="user-name">'.$initials.'</span><div class="sd_customerDetails"><strong>'.$value['name'].'</strong><br><span>'.$value['email'].'</div></span></div></td>';
                                                echo '<td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric">'.$unread_contract.'<a class="Polaris-Navigation__Item navigate_contract_detail" onmouseover="show_title(this)" onmouseout="hide_title(this)" data-query-string="'.$value['contract_id'].'" data-confirmbox="no" tabindex="0" href="javascript:void(0)" data-polaris-unstyled="true">#'.$value['contract_id'].'</a><div class="Polaris-PositionedOverlay display-hide-label"><div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                                <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">View</div></div></div></td>';
                                                echo '<td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric"><a class="Polaris-Navigation__Item"  href="'.$orderLink.'" target="_blank" data-polaris-unstyled="true">#'.$value['order_no'].'</a></td>';
                                                echo '<td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric">'.$planType.'</td>';
                                                echo '<td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric">'.getShopTimezoneDate($value['created_at'],$shop_timezone).'</td>';
                                                echo '<td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric">'.getShopTimezoneDate(($value['next_billing_date']. ' ' . $date_time_array[1]),$shop_timezone).'</td>';
                                                echo '<td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric"><div><span class="Polaris-Badge Polaris-Badge--'.$contract_statusClass.' Polaris-Badge--progressComplete">'.$addCircle.''.$contract_status.'</span>
                                                <div id="PolarisPortalsContainer"></div>
                                                </div></td>';
                                                echo '<td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric"><a class="Polaris-Navigation__Item sd_view_subscription navigate_contract_detail" data-query-string="'.$value['contract_id'].'" tabindex="0" href="javascript:void(0)" data-polaris-unstyled="true"><svg xmlns="http://www.w3.org/2000/svg" width="15px" height="20px" padding="10px" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M19.928 9.629c-2.137-5.343-6.247-7.779-10.355-7.565-4.06.21-7.892 3.002-9.516 7.603l-.118.333.118.333c1.624 4.601 5.455 7.393 9.516 7.603 4.108.213 8.218-2.222 10.355-7.565l.149-.371-.149-.371zm-9.928 5.371a5 5 0 1 0 0-10 5 5 0 0 0 0 10z"></path><circle cx="10" cy="10" r="3"></circle></svg></a></td>';
                                                echo '</tr>';
                                              }
                                            }
                                            ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="PolarisPortalsContainer"></div>
        </div>
    </div>
</div>
<?php
   }
   include("../footer.php");
?>