<html>

<!-- ========= Include this file where backend page needs to be built ====== -->
<?php
// session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
$dirPath =dirname(__DIR__);
include($dirPath."/application/library/config.php");
if(isset($_REQUEST['shop'])){
  $store = $_REQUEST['shop'];
}

$redirectPage = "https://".$store."/admin/apps";
$query = ("SELECT storeInstallOffers.store_id,plan_id,planName,access_token,shop_timezone,store_email,shop_name,currency,currencyCode,shop_plan,subscription_plans_view FROM install INNER JOIN storeInstallOffers ON install.id = storeInstallOffers.store_id INNER JOIN store_details ON install.id = store_details.store_id WHERE install.store = '$store' AND storeInstallOffers.status = '1'");
$result = $db->query($query);
$active_member_data = $result->fetch(PDO::FETCH_ASSOC);
$store_id = $active_member_data['store_id'];
$active_plan_name = $active_member_data['planName'];
$active_plan_id = $active_member_data['plan_id'];
$currency = $active_member_data['currency'];
$currency_code = $active_member_data['currencyCode'];
$shop_timezone = $active_member_data['shop_timezone'];
$shop_plan = $active_member_data['shop_plan'];
$shop_name = $active_member_data['shop_name'];
$shop_email = $active_member_data['store_email'];
$subscription_plans_view = $active_member_data['subscription_plans_view'];
$access_token = $active_member_data['access_token'];

if(!isset($_GET['code'])){
    if(strlen($store_id) == 0){
      echo "<script>window.top.location.href='".$redirectPage."'</script>";
    }
}
  if(isset($_GET['themeConfiguration']) && ($_GET['themeConfiguration'] == 'Yes')){
    //insert data into customer_settings table start
    $customer_settings_query = $db->prepare("SELECT * FROM `customer_settings` WHERE store_id = $store_id");
    $customer_settings_query->execute();
    $customer_settings_row_count = $customer_settings_query->rowCount();
    if($customer_settings_row_count <= 0){
      $sql_query_insert = "INSERT INTO customer_settings (store_id) VALUES ($store_id)";
      $ins = $db->exec($sql_query_insert);
    }
    //insert data into customer_settings table end
    // insert data into contract setting table start
    $contract_settings_query = $db->prepare("SELECT * FROM `contract_setting` WHERE store_id = $store_id");
    $contract_settings_query->execute();
    $contract_settings_row_count = $customer_settings_query->rowCount();
    $contract_setting_fields = array(
      'store_id' => $store_id,
      'afterBillingAttemptFail'=> 'Active',
      'remove_product' => 'Yes',
      'after_product_delete_contract' => 'Pause'
    );
    if($contract_settings_row_count <= 0){
      $sql_query_insert = "INSERT INTO 'contract_setting'(store_id,afterBillingAttemptFail,remove_product,after_product_delete_contract) VALUES ($store_id,'Active','Yes','Pause')";
      $ins = $db->exec($sql_query_insert);
    }
    //insert data into email setting table end
    $email_notification_setting_query = $db->prepare("SELECT * FROM `email_notification_setting` WHERE store_id = '$store_id'");
    $email_notification_setting_query->execute();
    $email_notification_setting_row_count = $email_notification_setting_query->rowCount();
    if($email_notification_setting_row_count > 0){
      $sql_query_insert = "INSERT INTO `email_notification_setting` (store_id) VALUES ('$store_id')";
      $ins = $db->exec($sql_query_insert);
    }
  }
$add_development_stores = array('mini-cart-development.myshopify.com','ritikatest-predectivesearch.myshopify.com','subscription-store-two.myshopify.com','prakriti-advanced-option-pro.myshopify.com','predictive-search.myshopify.com','simar-test.myshopify.com','mytab-shinedezign.myshopify.com','shineinfotest.myshopify.com','advanced-subscriptionpro.myshopify.com','magiktheme.myshopify.com','robins-store1.myshopify.com','pragati-test-store.myshopify.com','sanitizer-shopping.myshopify.com','king-development.myshopify.com','inder-store-credit.myshopify.com', 'shineinfotest.myshopify.com');
if(in_array($store, $add_development_stores)){
}else if($shop_plan == "trial" || $shop_plan == "partner_test" || $shop_plan == "affiliate"){
  echo 'You are been automatically redirected...';
  $url_redir = $SHOPIFY_DOMAIN_URL."/admin/not_allowed.php?shop={$store}";
  echo "<script>open('/admin/not_allowed.php?shop=".$store."', '_self'); </script>";
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
}


// check store total sale(restrict all the pages if sale is greater than 500)
$total_subscription_order_sale_query = $db->query("SELECT total_sale,contract_currency,contract_id FROM contract_sale WHERE store_id = '$store_id'");
$get_billing_attempt_orders_sale = $total_subscription_order_sale_query->fetchAll(PDO::FETCH_ASSOC);
$install_data_query = $mainobj->db->query("SELECT user_type FROM install WHERE id = '{$mainobj->store_id}'");
$install_data = $install_data_query->fetch(PDO::FETCH_ASSOC); // Fetch a single row as an associative array
$user_type = $install_data['user_type']; // Print the value of 'user_type'
echo "<script>contract_sale =" . json_encode($get_billing_attempt_orders_sale) . ";</script>";
$total_subscription_order_sale = array_sum(array_column($get_billing_attempt_orders_sale,'total_sale'));
$active_plan_name = $active_member_data['planName'];
$redirectPage = '/admin/app_plans.php?shop='.$mainobj->store;
if($user_type == 'new' && $active_plan_name == 'Free' && $current_page != 'app_plans.php') {
  echo "<script type='text/javascript'>open('".$redirectPage."', '_self'); </script>";
}
$active_member_plan_status = 'ACTIVE';
    if(($active_plan_name == 'Free_old' && $store == 'boo-kay-nyc.myshopify.com') || ($active_plan_name == 'Free_old' && $store == 'advanced-subscriptionpro.myshopify.com') ){
      $amount_exceed = 1800;
    }else if($active_plan_name == 'Free_old'){
      $amount_exceed = 700;
    }else{
      $amount_exceed = 500;
    }
    //CHECK app billing status and if expired then redirect to the payment pending page
    if($total_subscription_order_sale > $amount_exceed && $logged_in_customer_id == ''){
      $store_name = strtok($store, '.');
      $redirectPage = '/admin/app_plans.php?shop='.$store;
      if($active_member_data['plan_id'] != 3 ){
        $active_member_plan_status = 'INACTIVE';
        if($current_page != 'app_plans.php' && $current_page != 'dashboard.php' && $current_page != 'contactUs.php'){
          echo "<script type='text/javascript'>open('".$redirectPage."', '_self'); </script>";
        }
      }
    }

    if(isset($_GET['template_name'])){
      function getCurrencySymbol($currencyCode){
        $currency_list = array(
          "AFA" => "؋","ALL" => "Lek","DZD" => "دج","AOA" => "Kz","ARS" => "$","AMD" => "֏","AWG" => "ƒ","AUD" => "$","AZN" => "m","BSD" => "B$","BHD" => ".د.ب","BDT" => "৳","BBD" => "Bds$","BYR" => "Br","BEF" => "fr","BZD" => "$","BMD" => "$","BTN" => "Nu.","BTC" => "฿","BOB" => "Bs.","BAM" => "KM","BWP" => "P","BRL" => "R$","GBP" => "£","BND" => "B$","BGN" => "Лв.","BIF" => "FBu","KHR" => "KHR","CAD" => "$","CVE" => "$","KYD" => "$","XOF" => "CFA","XAF" => "FCFA","XPF" => "₣","CLP" => "$","CNY" => "¥","COP" => "$","KMF" => "CF","CDF" => "FC","CRC" => "₡","HRK" => "kn","CUC" => "$, CUC","CZK" => "Kč","DKK" => "Kr.","DJF" => "Fdj","DOP" => "$","XCD" => "$",
          "EGP" => "ج.م","ERN" => "Nfk","EEK" => "kr","ETB" => "Nkf","EUR" => "€","FKP" => "£","FJD" => "FJ$","GMD" => "D","GEL" => "ლ","DEM" => "DM","GHS" => "GH₵","GIP" => "£","GRD" => "₯, Δρχ, Δρ","GTQ" => "Q","GNF" => "FG","GYD" => "$","HTG" => "G","HNL" => "L","HKD" => "$","HUF" => "Ft","ISK" => "kr","INR" => "Rs.","IDR" => "Rp","IRR" => "﷼","IQD" => "د.ع","ILS" => "₪","ITL" => "L,£","JMD" => "J$","JPY" => "¥","JOD" => "ا.د","KZT" => "лв","KES" => "KSh","KWD" => "ك.د","KGS" => "лв","LAK" => "₭",
          "LVL" => "Ls","LBP" => "£","LSL" => "L","LRD" => "$","LYD" => "د.ل","LTL" => "Lt","MOP" => "$","MKD" => "ден", "MGA" => "Ar", "MWK" => "MK", "MYR" => "RM", "MVR" => "Rf", "MRO" => "MRU", "MUR" => "₨", "MXN" => "$", "MDL" => "L", "MNT" => "₮", "MAD" => "MAD", "MZM" => "MT", "MMK" => "K", "NAD" => "$", "NPR" => "₨", "ANG" => "ƒ", "TWD" => "$", "NZD" => "$", "NIO" => "C$", "NGN" => "₦", "KPW" => "₩", "NOK" => "kr", "OMR" => ".ع.ر", "PKR" => "₨", "PAB" => "B/.", "PGK" => "K", "PYG" => "₲", "PEN" => "S/.", "PHP" => "₱", "PLN" => "zł", "QAR" => "ق.ر", "RON" => "lei", "RUB" => "₽", "RWF" => "FRw", "SVC" => "₡", "WST" => "SAT", "SAR" => "﷼", "RSD" => "din", "SCR" => "SRe", "SLL" => "Le", "SGD" => "$", "SKK" => "Sk", "SBD" => "Si$", "SOS" => "Sh.so.", "ZAR" => "R", "KRW" => "₩", "XDR" => "SDR", "LKR" => "Rs", "SHP" => "£", "SDG" => ".س.ج", "SRD" => "$", "SZL" => "E", "SEK" => "kr", "CHF" => "CHf", "SYP" => "LS", "STD" => "Db", "TJS" => "SM", "TZS" => "TSh", "THB" => "฿", "TOP" => "$", "TTD" => "$", "TND" => "ت.د", "TRY" => "₺", "TMT" => "T", "UGX" => "USh", "UAH" => "₴", "AED" => "إ.د", "UYU" => "$", "USD" => "$", "UZS" => "лв", "VUV" => "VT", "VEF" => "Bs", "VND" => "₫", "YER" => "﷼", "ZMK" => "ZK"
        );
        return $currency_list[$currencyCode];
	    }
      function email_template($data,$show_template_for){
        global $store,$SHOPIFY_DOMAIN_URL,$store_id,$db,$currency,$currency_code,$shop_name,$shop_email;
        $shop_domain = $store;
        $deleted_product = ' ';
        $new_added_products = ' ';
        $updated_product = ' ';
        $skipped_order_date = ' ';
        $actual_fulfillment_date = '';
        $new_scheduled_date = '';
        $new_shipping_price = ' ';
        $new_renewal_date = '';
        $selling_plan_name = 'Delivery Every Day';
        $order_number = '#1129';
        $next_order_date = date('Y-m-d H:i:s');
        $product_quantity = '1';$product_name = 'Sprouts';$variant_name = 'salad';$delivery_cycle = '2';$billing_cycle = '2';$thanks_img = '';$logo='';
        $product_image_url = $SHOPIFY_DOMAIN_URL.'/application/assets/images/sprouts-salad.jpg';
        // $whereCondition = array(
        //   "store_id" => $store_id,
        // );
        $template_name = $data['template_type'];
        $template_heading = ucwords(str_replace(array('_','status'),array(' ',''),$template_name));
        // $fields = array('shop_name','store_email');
        // $store_details_data = $this->single_row_value('store_details',$fields,$whereCondition,'and','');
        // $store_details_data_qry = $db->query("SELECT shop_name,store_email FROM `store_details` WHERE store_id='$store_id'");
        // $store_details_data = $store_details_data_qry->fetch(PDO::FETCH_ASSOC);
        // $shop_name = $store_details_data['shop_name'];
        // $shop_email = $store_details_data['store_email'];

        //set default value for the email template fields
        $customer_name = 'Honey Bailey';
        $customer_email = 'honey@gmail.com';
        $customer_id = '6578937358';
        $subscription_contract_id = '#757899098';
        $shipping_full_name = 'Honey Bailey';
        $shipping_company = 'Company name';
        $shipping_address1 = '215 Bell St';
        $shipping_address2 = '215 Bell St2';
        $shipping_city = 'Melbourne';
        $shipping_province = 'Victoria';
        $shipping_country = 'Australia';
        $shipping_phone = '(03) 9485 0100';
        $shipping_province_code = 'AUS';
        $shipping_country_code = '61';
        $shipping_zip = '3071';

        $billing_full_name = 'Honey Bailey';
        $billing_company = 'Company name';
        $billing_address1 = '215 Bell St';
        $billing_address2 = '215 Bell St2';
        $billing_city = 'Melbourne';
        $billing_province = 'Victoria';
        $billing_country = 'Australia';
        $billing_phone = '(03) 9485 0100';
        $billing_province_code = 'AUS';
        $billing_country_code = '61';
        $billing_zip = '3071';

        $delivery_method = 'Standard';
        $delivery_price = '100';
        $payment_method_token = '';
        $card_expire_month = 'March'; // March
        $card_expire_year = '2024';
        $last_four_digits = '4242';
        $card_brand = 'Visa';

        if($show_template_for == 'send_dynamic_email'){
          $province_code = '';
          $country_code = '';
          if(isset($data['shipping_data']['province_code'])){
             $province_code = $data['shipping_data']['province_code'];
          }
          if(isset($data['shipping_data']['country_code'])){
            $country_code = $data['shipping_data']['country_code'];
          }
          $get_customer_data = $data['contract_details'];
          $customer_name = $get_customer_data['name'];
          $customer_email = $get_customer_data['email'];
          $customer_id = $get_customer_data['shopify_customer_id'];
          $subscription_contract_id = '#'.$data['contract_id'];
          if($template_name == 'shipping_address_update_template'){
            $shipping_full_name = $data['shipping_data']['first_name'].' '.$data['shipping_data']['last_name'];
            $shipping_company = $data['shipping_data']['company'];
            $shipping_address1 = $data['shipping_data']['address1'];
            $shipping_address2 = $data['shipping_data']['address2'];
            $shipping_city = $data['shipping_data']['city'];
            $shipping_province = $data['shipping_data']['province'];
            $shipping_country = $data['shipping_data']['country'];
            $shipping_phone = $data['shipping_data']['phone'];
            $shipping_province_code = $province_code;
            $shipping_country_code = $country_code;
            $shipping_zip = $data['shipping_data']['zip'];
          }else{
            $shipping_full_name = $get_customer_data['shipping_first_name'].' '.$get_customer_data['shipping_last_name'];
            $shipping_company = $get_customer_data['shipping_company'];
            $shipping_address1 = $get_customer_data['shipping_address1'];
            $shipping_address2 = $get_customer_data['shipping_address2'];
            $shipping_city = $get_customer_data['shipping_city'];
            $shipping_province = $get_customer_data['shipping_province'];
            $shipping_country = $get_customer_data['shipping_country'];
            $shipping_phone = $get_customer_data['shipping_phone'];
            $shipping_province_code = $get_customer_data['shipping_province_code'];
            $shipping_country_code = $get_customer_data['shipping_country_code'];
            $shipping_zip = $get_customer_data['shipping_zip'];
          }
          $order_number = '#'.$get_customer_data['order_no'];
          $billing_full_name = $get_customer_data['billing_first_name'].' '.$get_customer_data['billing_last_name'];
          $billing_company = $get_customer_data['billing_company'];
          $billing_address1 = $get_customer_data['billing_address1'];
          $billing_address2 = $get_customer_data['billing_address2'];
          $billing_city = $get_customer_data['billing_city'];
          $billing_province = $get_customer_data['billing_province'];
          $billing_country = $get_customer_data['billing_country'];
          $billing_phone = $get_customer_data['billing_phone'];
          $billing_province_code = $get_customer_data['billing_province_code'];
          $billing_country_code = $get_customer_data['billing_country_code'];
          $billing_zip = $get_customer_data['billing_zip'];

          $delivery_method = $get_customer_data['shipping_delivery_method'];
          $delivery_price = $get_customer_data['shipping_delivery_price'];
          $payment_method_token = $get_customer_data['payment_method_token'];
          $payment_instrument_value = json_decode($get_customer_data['payment_instrument_value']);
          if($payment_instrument_value->month){
              $dateObj   = DateTime::createFromFormat('!m', $payment_instrument_value->month);
              $card_expire_month = $dateObj->format('F'); // March
          }else{
            $card_expire_month = $payment_instrument_value->month; // March
          }
          $card_expire_year = $payment_instrument_value->year;
          $last_four_digits = $payment_instrument_value->last_digits;
          $card_brand = $payment_instrument_value->brand;

        }
        // $template_data = $this->single_row_value($template_name,'all',$whereCondition,'and','');
        $template_data_qry = $db->query("SELECT * FROM `$template_name` WHERE store_id='$store_id'");
        $template_data = $template_data_qry->fetch(PDO::FETCH_ASSOC);
        if($template_name == 'subscription_purchase_template'){
          $content_text = '<h2 style="font-weight:normal;font-size:24px;margin:0 0 10px">Hi {{customer_name}}</h2><h2 style="font-weight:normal;font-size:24px;margin:0 0 10px">Thank you for your purchase!</h2> <p style="line-height:150%;font-size:16px;margin:0">We are getting your order ready to be shipped. We will notify you when it has been sent.</p>';
        }else{
          switch ($template_name) {
            case "subscription_status_cancelled_template":
              $content_heading = 'Subscription with id {{subscription_contract_id}} has been cancelled';
            break;
            case "subscription_status_resumed_template":
              $content_heading = 'Subscription with id {{subscription_contract_id}} has been resumed';
            break;
            case "subscription_status_paused_template":
              $content_heading = 'Subscription with id {{subscription_contract_id}} has been paused';
            break;
            case "product_added_template":
              $content_heading = 'Product(s) {{new_added_products}} has been added in the Subscription with id {{subscription_contract_id}}';
            break;
            case "product_removed_template":
              $content_heading = 'Product {{removed_product}} has been removed from the Subscription with id {{subscription_contract_id}}';
              if($show_template_for == 'send_dynamic_email'){
                $deleted_product_line_id = $data['deleted_product_line_id'];
                $filteredArray = array_filter($data['contract_product_details'], function($item) use ($deleted_product_line_id) {
                  return $item['contract_line_item_id'] === $deleted_product_line_id;
                });
                  $targetItem = array_shift($filteredArray);
                if ($targetItem) {
                  $deleted_product = $targetItem['product_name'].' '.$targetItem['variant_name'];
                }
              }
            break;
            case "product_updated_template":
              $content_heading = 'Subscription product {{updated_product}} has been updated in the subscription with id {{subscription_contract_id}}';
              if($show_template_for == 'send_dynamic_email'){
                $updated_product = $data['updated_product']['product_title'];
              }
            break;
            case "skip_order_template":
              $content_heading = 'Subscription order of the date {{skipped_order_date}} has been skipped in the subscription with id {{subscription_contract_id}}';
              if($show_template_for == 'send_dynamic_email'){
                $skipped_order_date = date('d M Y', strtotime($data['skipped_order_date']));
              }
            break;
            case "billing_attempted_template":
              $content_heading = 'Thanks for your order {{order_number}}!. We’ll get it to your doorstep as soon as possible! You will get a shipping notification once your order has left our shop and is on the way to you! ';
            break;
            case "reschedule_fulfillment_template":
              $content_heading = 'Subscription with id {{subscription_contract_id}} has been rescheduled the fulfillment of the date {{actual_fulfillment_date}} to the {{new_scheduled_date}}';
                if($show_template_for == 'send_dynamic_email'){
                 $actual_fulfillment_date = $data['actual_fulfillment_date'];
                 $new_scheduled_date = $data['new_scheduled_date'];
                }
            break;
            case "shipping_address_update_template":
               $content_heading = 'Shipping address of the subscription id {{subscription_contract_id}} has been changed and the shipping price is {{new_shipping_price}}';
               if($show_template_for == 'send_dynamic_email'){
              if(isset($data['shipping_data']['delivery_price'])){
                $new_shipping_price = getCurrencySymbol($get_customer_data['order_currency']).''.$data['shipping_data']['delivery_price'];
              }
               }
            break;
            case "payment_failed_template":
              $content_heading = 'Subscription order payment has been failed of the subscription with id {{subscription_contract_id}}';
            break;
            case "payment_declined_template":
               $content_heading = 'Subscription order payment has been declined of the subscription with id {{subscription_contract_id}}';
            break;
            case "payment_pending_template":
              $content_heading = 'Subscription order payment has been declined of the subscription with id {{subscription_contract_id}}';
            break;
            case "subscription_renewal_date_update_template":
              $content_heading = 'Renewal date of the subscription with Id {{subscription_contract_id}} has been updated. The new renewal date of the subscription is {{new_renewal_date}}';
              if($show_template_for == 'send_dynamic_email'){
                $new_renewal_date = $data['new_renewal_date'];
              }
            break;
            case "upcoming_orders_template":
                        $content_heading = 'We wanted to remind you about your upcoming subscription order scheduled for {{renewal_date}}';
              if($show_template_for == 'send_dynamic_email'){
                $new_renewal_date = $data['next_billing_date'];
              }
            break;
            default:
            $content_heading = '';
          }
          $content_text = '<h2 style="font-weight:normal;font-size:24px;margin:0 0 10px">Hi {{customer_name}}</h2><h2 style="font-weight:normal;font-size:24px;margin:0 0 10px">'.$content_heading.'</h2> <p style="line-height:150%;font-size:16px;margin:0">Please visit manage subscription portal to confirm.</p>';
        }

        if(!empty($template_data)){
          $template_type = $template_data['template_type'];
          $email_subject = $template_data['subject'];
          $ccc_email = $template_data['ccc_email'];
          $bcc_email = $template_data['bcc_email'];
          $from_email = $template_data['from_email'];
          $reply_to = $template_data['reply_to'];
          $logo_height = $template_data['logo_height'];
          $logo_width = $template_data['logo_width'];
          $logo_alignment = $template_data['logo_alignment'];
          $logo = '<img class="sd_logo_view" border="0" style="display:'.($template_data['logo'] == '' ? 'none' : 'block').';color:#000000;text-decoration:none;font-family:Helvetica,arial,sans-serif;font-size:16px;float:'.$logo_alignment.'" width="'.$logo_width.'" src="'.$template_data['logo'].'" height="'.$logo_height.'" data-bit="iit">';
          $thanks_img_width = $template_data['thanks_img_width'];
          $thanks_img_height = $template_data['thanks_img_height'];
          $thanks_img_alignment = $template_data['thanks_img_alignment'];
          $thanks_img = '<img class="sd_thanks_img_view" border="0" style="display:'.($template_data['thanks_img'] == '' ? 'none' : 'block').';color:#000000;text-decoration:none;font-family:Helvetica,arial,sans-serif;font-size:16px;float:'.$thanks_img_alignment.'" width="'.$thanks_img_width.'" src="'.$template_data['thanks_img'].'" height="'.$thanks_img_height.'" data-bit="iit">';
          $heading_text = $template_data['heading_text'];
          $heading_text_color = $template_data['heading_text_color'];
          $content_text = $template_data['content_text'];
          $text_color = $template_data['text_color'];
          $manage_subscription_txt = $template_data['manage_subscription_txt'];
          $manage_subscription_url = $template_data['manage_subscription_url'];
          if($manage_subscription_url == ''){
            $manage_subscription_url = 'https://'.$store.'/account';
          }
          $manage_button_text_color = $template_data['manage_button_text_color'];
          $manage_button_background = $template_data['manage_button_background'];
          $shipping_address_text = $template_data['shipping_address_text'];
          $shipping_address = $template_data['shipping_address'];
          $billing_address = $template_data['billing_address'];
          $billing_address_text = $template_data['billing_address_text'];
          if(isset($template_data['next_renewal_date_text'])){
            $next_renewal_date_text = $template_data['next_renewal_date_text'];
          }
          $payment_method_text = $template_data['payment_method_text'];
          $ending_in_text = $template_data['ending_in_text'];
          $qty_text = $template_data['qty_text'];
          $footer_text = $template_data['footer_text'];
          $show_currency = $template_data['show_currency'];
          if($template_data['show_currency'] == '0'){
            $currency = '';
          }
          $next_charge_date_text = $template_data['next_charge_date_text'];
          $delivery_every_text = $template_data['delivery_every_text'];
          $custom_template = $template_data['custom_template'];
          $order_number_text = $template_data['order_number_text'];

          $show_currency = $template_data['show_currency'];
          $show_shipping_address = $template_data['show_shipping_address'];
          $show_billing_address = $template_data['show_billing_address'];
          $show_line_items = $template_data['show_line_items'];
          $show_payment_method = $template_data['show_payment_method'];
          $custom_template = $template_data['custom_template'];
          $show_order_number = $template_data['show_order_number'];
        }else{
          $template_type = 'default';
          $ccc_email = '';
          $bcc_email = '';
          $reply_to = '';
          $logo_height = '63';
          $logo_width = '166';
          $logo_alignment = 'center';
          $thanks_img_width = '166';
          $thanks_img_height = '63';
          $thanks_img_alignment = 'center';
          $logo = '<img class="sd_logo_view" border="0" style="color:#000000;text-decoration:none;font-family:Helvetica,arial,sans-serif;font-size:16px;float:'.$logo_alignment.'" width="'.$logo_width.'" src="'.$SHOPIFY_DOMAIN_URL.'/application/assets/images/logo.png" height="'.$logo_height.'" data-bit="iit">';
          $thanks_img = '<img class="sd_thanks_img_view" border="0" style="color:#000000;text-decoration:none;font-family:Helvetica,arial,sans-serif;font-size:16px;float:'.$thanks_img_alignment.'" width="'.$thanks_img_width.'" src="'.$SHOPIFY_DOMAIN_URL.'/application/assets/images/thank_you.jpg" height="'.$thanks_img_height.'" data-bit="iit">';
          $heading_text = 'Welcome';
          $heading_text_color = '#495661';
          $text_color = '#000000';
          $manage_subscription_txt = 'Manage Subscription';
          $manage_subscription_url = 'https://'.$store.'/account';
          $manage_button_text_color = '#ffffff';
          $manage_button_background = '#337ab7';
          $shipping_address_text = 'Shipping address';
          $shipping_address = '<p>{{shipping_full_name}}</p><p>{{shipping_address1}}</p><p>{{shipping_city}},{{shipping_province_code}} - {{shipping_zip}}</p>';
          $billing_address_text = 'Billing address';
          $billing_address = '<p>{{billing_full_name}}</p><p>{{billing_address1}}</p><p>{{billing_city}},{{billing_province_code}} - {{billing_zip}}</p>';
          $next_renewal_date_text = 'Next billing date';
          $payment_method_text = 'Payment method';
          $ending_in_text = 'Ending with';
          $footer_text = '<p style="line-height:150%;font-size:14px;margin:0">Thank You</p>';
          $next_charge_date_text = 'Next billing date';
          $delivery_every_text = 'Delivery every';
          $order_number_text = 'Order No.';
          if($template_name == 'subscription_purchase_template' || $template_name == 'upcoming_orders_template'){
            $email_subject = 'Your recurring order purchase confirmation';
            $show_currency = '1';
            $show_shipping_address = '1';
            $show_billing_address = '1';
            $show_line_items = '1';
            $show_payment_method = '1';
            $custom_template = '';
            $show_order_number = '1';
          }else{
            $email_subject = str_replace('Template','',$template_heading);
            $show_currency = '0';
            $show_shipping_address = '0';
            $show_billing_address = '0';
            $show_line_items = '0';
            $show_payment_method = '0';
            $custom_template = '';
            $show_order_number = '0';
          }
        }

        // if($this->store == 'predictive-search.myshopify.com'){
        // 	echo $content_text;
        // }

        $subscription_line_items = '<table style="width:100%;border-spacing:0;border-collapse:collapse" class="sd_show_line_items '.($show_line_items == '0' ? 'display-hide-label' : '').'">
        <tbody>
          <tr>
            <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;">
              <center>
                <table class="m_-1845756208323497270container" style="width:560px;text-align:left;border-spacing:0;border-collapse:collapse;margin:0 auto">
                  <tbody>
                    <tr>
                      <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                        <table style="width:100%;border-spacing:0;border-collapse:collapse">
                          <tbody>
                            <tr style="width:100%">
                              <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:15px">
                                <table style="border-spacing:0;border-collapse:collapse">
                                  <tbody>';

        // echo $card_brand; die;
        if($show_template_for == 'send_dynamic_email'){
          $contract_product_details = $data['contract_product_details'];
          $currency_code = getCurrencySymbol($get_customer_data['order_currency']);
          $updated_at_date = $get_customer_data['updated_at'];
          $date_time_array = explode(' ', $updated_at_date);
          if($template_name == 'subscription_renewal_date_update_template'){
            $next_billing_date = $data['new_renewal_date'];
          }else{
            $next_billing_date = getShopTimezoneDate(($get_customer_data['next_billing_date'] . ' ' . $date_time_array[1]), $get_customer_data['shop_timezone']);
          }
          $next_billing_date = getShopTimezoneDate(($get_customer_data['next_billing_date'] . ' ' . $date_time_array[1]), $get_customer_data['shop_timezone']);
          if($data['contract_details']['after_cycle_update'] == '1' && $data['contract_details']['after_cycle'] != 0){
            $get_subscription_price_column = 'recurring_computed_price';
          }else{
            $get_subscription_price_column = 'subscription_price';
          }
          foreach($contract_product_details as $key=>$prdVal){
            $product_quantity = $prdVal['quantity'];
            $product_price = $prdVal[$get_subscription_price_column] * $product_quantity;
            if($template_name == 'product_updated_template'){
              $deleted_product_line_id = $data['updated_product']['line_id'];
              if($prdVal['contract_line_item_id'] == $deleted_product_line_id){
                $product_quantity = $data['updated_product']['prd_qty'];
                $product_price = $data['updated_product']['prd_price'] * $product_quantity;
              }
            }
            if($template_name == 'product_removed_template' && $data['deleted_product_line_id'] == $prdVal['contract_line_item_id']){
              continue;
            }
            $subscription_line_items .= '<tr style="border-bottom: 1px solid #f3f3f3;">
            <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
              <img src="'.$prdVal['variant_image'].'" align="left" width="60" height="60" style="margin-right:15px;border-radius:8px;border:1px solid #e5e5e5" class="CToWUd" data-bit="iit">
            </td>
            <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;width:100%">
              <span style="font-size:16px;font-weight:600;line-height:1.4;color:'.$heading_text_color.'" class="sd_heading_text_color_view">'.$prdVal['product_name'].' '.$prdVal['variant_name'].' x '.$product_quantity.'</span><br>
              <span class="sd_text_color_view" style="font-size:14px;color:'.$text_color.'"><span class = "sd_delivery_every_text_view">'.$delivery_every_text.'</span> : '.$get_customer_data['delivery_policy_value'].' '.$get_customer_data['delivery_billing_type'].'</span><br>
              <span style="font-size:14px;color:'.$text_color.'">'.$next_charge_date_text.' : '.$next_billing_date.'</span><br>
            </td>
            <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;white-space:nowrap">
              <p class="sd_text_color_view" style="color:'.$text_color.';line-height:150%;font-size:16px;font-weight:600;margin:0 0 0 15px" align="right">
                '.$currency_code.''.$product_price.' <span class="sd_show_currency '.($show_currency == '0' ? 'display-hide-label' : '').'">'.$get_customer_data['order_currency'].'</span>
              </p>
            </td>
              </tr>';
          }

          if($template_name = 'product_added_template'){
            $new_products = $data['new_added_products'];
            $productTitles = [];
            foreach ($new_products as $item) {
               $productTitles[] = $item['product_name'] . '-' . $item['variant_name'];
            }
            $new_added_products = implode(', ', $productTitles);

            foreach($new_products as $key=>$newPrd){
              if($get_customer_data['recurring_discount_value'] != 0 && $get_customer_data['after_cycle_update'] == '1'){
                $product_price = $newPrd['recurring_computed_price'];
              }else{
                $product_price = $newPrd['subscription_price'];
              }
              $subscription_line_items .= '<tr style="border-bottom: 1px solid #f3f3f3;">
              <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                <img src="'.$newPrd['variant_image'].'" align="left" width="60" height="60" style="margin-right:15px;border-radius:8px;border:1px solid #e5e5e5" class="CToWUd" data-bit="iit">
              </td>
              <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;width:100%">
                <span style="font-size:16px;font-weight:600;line-height:1.4;color:'.$heading_text_color.'" class="sd_heading_text_color_view">'.$newPrd['product_name'].' '.$newPrd['variant_name'].' x 1</span><br>
                <span class="sd_text_color_view" style="font-size:14px;color:'.$text_color.'"><span class = "sd_delivery_every_text_view">'.$delivery_every_text.'</span> : '.$get_customer_data['delivery_policy_value'].' '.$get_customer_data['delivery_billing_type'].'</span><br>
                <span style="font-size:14px;color:'.$text_color.'">'.$next_charge_date_text.' : '.$next_billing_date.'</span><br>
              </td>
              <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;white-space:nowrap">
                <p class="sd_text_color_view" style="color:'.$text_color.';line-height:150%;font-size:16px;font-weight:600;margin:0 0 0 15px" align="right">
                  '.$currency_code.''.$product_price.' <span class="sd_show_currency '.($show_currency == '0' ? 'display-hide-label' : '').'">'.$get_customer_data['order_currency'].'</span>
                </p>
              </td>
              </tr>';
            }
          }

        }else{
            $subscription_line_items .= '<tr style="border-bottom: 1px solid #f3f3f3;">
                      <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                        <img src="'.$product_image_url.'" align="left" width="60" height="60" style="margin-right:15px;border-radius:8px;border:1px solid #e5e5e5" class="CToWUd" data-bit="iit">
                      </td>
                      <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;width:100%">
                        <span style="font-size:16px;font-weight:600;line-height:1.4;color:'.$heading_text_color.'" class="sd_heading_text_color_view">'.$product_name.'-'.$variant_name.' x 2</span><br>
                        <span class="sd_text_color_view" style="font-size:14px;color:'.$text_color.'"><span class="sd_delivery_every_text_view">'.$delivery_every_text.'</span> : 1 month</span><br>
                        <span class="sd_text_color_view" style="font-size:14px;color:'.$text_color.'"><span class="sd_next_charge_date_text_view">'.$next_charge_date_text.'</span> : 5 May, 2023</span><br>
                      </td>
                      <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;white-space:nowrap">
                        <p class="sd_text_color_view" style="color:'.$text_color.';line-height:150%;font-size:16px;font-weight:600;margin:0 0 0 15px" align="right">
                          '.$currency_code.'100.00 <span class="sd_show_currency '.($show_currency == '0' ? 'display-hide-label' : '').'">'.$currency.'</span>
                        </p>
                      </td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f3f3f3;">
                    <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                      <img src="'.$product_image_url.'" align="left" width="60" height="60" style="margin-right:15px;border-radius:8px;border:1px solid #e5e5e5" class="CToWUd" data-bit="iit">
                    </td>
                    <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;width:100%">
                      <span style="font-size:16px;font-weight:600;line-height:1.4;color:'.$heading_text_color.'" class="sd_heading_text_color_view">'.$product_name.'-'.$variant_name.' x 2</span><br>
                      <span class="sd_text_color_view" style="font-size:14px;color:'.$text_color.'"><span class="sd_delivery_every_text_view">'.$delivery_every_text.'</span> : 1 month</span><br>
                      <span class="sd_text_color_view" style="font-size:14px;color:'.$text_color.'"><span class="sd_next_charge_date_text_view">'.$next_charge_date_text.'</span> : 5 May, 2023</span><br>
                      </td>
                    <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;white-space:nowrap">
                      <p class="sd_text_color_view" style="color:'.$text_color.';line-height:150%;font-size:16px;font-weight:600;margin:0 0 0 15px" align="right">
                        '.$currency_code.'100.00 <span class="sd_show_currency '.($show_currency == '0' ? 'display-hide-label' : '').'">'.$currency.'</span>
                      </p>
                    </td>
                  </tr>';

            }
            $subscription_line_items .= '</tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                    </table>
                    </td>
                    </tr>
                    </tbody>
                    </table>
                    </center>
                    </td>
                    </tr>
                    </tbody>
                    </table>';

          $default_email_template = '<div style="background-color:#efefef" bgcolor="#efefef">
          <table role="presentation" cellpadding="0" cellspacing="0" style="border-spacing:0!important;border-collapse:collapse;margin:0;padding:0;width:100%!important;min-width:320px!important;height:100%!important;background-image: url('.$SHOPIFY_DOMAIN_URL.'/application/assets/images/default_template_background.jpg);background-repeat:no-repeat;background-size:100% 100%;background-position:center" width="100%" height="100%">
            <tbody>
              <tr>
                <td valign="top" style="border-collapse:collapse;font-family:Arial,sans-serif;font-size:15px;color:#191d48;word-break:break-word;">
                  <div id="m_-5083759200921609693m_-526092176599779985hs_cos_wrapper_main" style="color:inherit;font-size:inherit;line-height:inherit">  <div id="m_-5083759200921609693m_-526092176599779985section-0" style="padding-top:20px;padding-bottom: 20px;">
                    <div style="max-width: 644px;width:100%;Margin-left:auto;Margin-right:auto;border-collapse:collapse;border-spacing:0;background-color:#ffffff;" bgcolor="#ffffff">
      <table style="height:100%!important;width:100%!important;border-spacing:0;border-collapse:collapse;">
        <tbody>
          <tr>
            <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
              <table class="m_-1845756208323497270header" style="width:100%;border-spacing:0;border-collapse:collapse;margin:40px 0 20px">
                <tbody>
                  <tr>
                    <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                      <center>
                      <table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" role="module" style="width:100%;border-spacing:0;border-collapse:collapse;>
                        <tbody>
                          <tr role="module-content">
                            <td height="100%" valign="top">
                            <table width="100%" style="width:100%;border-spacing:0;border-collapse:collapse;margin:0px 0px 0px 0px" cellpadding="0" cellspacing="0" align="left" border="0" bgcolor="">
                              <tbody>
                              <tr>
                            <td style="padding:0px;margin:0px;border-spacing:0">
                            <table role="module" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
                              <tbody>
                                <tr>
                                  <td style="font-size:6px;line-height:10px;padding:0px 0px 0px 0px" valign="top" align="center">
                                  '.$logo.'
                                  </td>
                                </tr>
                                <tr>
                                  <td style="font-size:6px;line-height:10px;padding:0px 0px 0px 0px" valign="top" align="center">
                                  '.$thanks_img.'
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      </td>
                    </tr>
                  </tbody>
                </table>
                  <table class="m_-1845756208323497270container" style="width:560px;text-align:left;border-spacing:0;border-collapse:collapse;margin:0 auto">
                    <tbody>
                      <tr>
                        <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                          <table style="width:100%;border-spacing:0;border-collapse:collapse">
                            <tbody>
                              <tr>
                                <td class="m_-1845756208323497270shop-name__cell" style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                                 <div class="sd_heading_text_color_view" style="color:'.$heading_text_color.'">
                                   <h1 style="font-weight:normal;font-size:30px;margin:0" class="sd_heading_text_view">
                                  '.$heading_text.'
                                  </h1>
                                 </div>
                                </td>
                                <td class="m_-1845756208323497270order-number__cell sd_show_order_number '.($show_order_number == '0' ?'display-hide-label' : '').'" style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;text-transform:uppercase;font-size:14px;color:#999" align="right">
                                <table style="width:100%;text-align:right;">
                                  <tbody>
                                     <tr> <td> <span style="font-size:13px,font-weight:600;color:'.$text_color.'" class="sd_order_number_text_view sd_text_color_view"><b>'.$order_number_text.'</b></span> </td> </tr>
                                     <tr> <td> <span class="sd_text_color_view" style="font-size:16px;color:'.$text_color.'"> '.$order_number.' </span> </td> </tr>
                                  </tbody>
                                </table>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </center>
              </td>
            </tr>
          </tbody>
        </table>
        <table style="width:100%;border-spacing:0;border-collapse:collapse">
          <tbody>
            <tr>
              <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;border-width:0">
                <center>
                  <table class="m_-1845756208323497270container" style="width:560px;text-align:left;border-spacing:0;border-collapse:collapse;margin:0 auto">
                    <tbody>
                      <tr>
                        <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                          <div class="sd_content_text_view sd_text_color_view" style="color:'.$text_color.';">
                            '.$content_text.'
                          </div>
                          <table style="width:100%;border-spacing:0;border-collapse:collapse;margin-top:20px">
                            <tbody>
                              <tr>
                                <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;line-height:0em">&nbsp;</td>
                              </tr>
                              <tr>
                                <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                                  <table class="m_-1845756208323497270button m_-1845756208323497270main-action-cell" style="border-spacing:0;border-collapse:collapse;float:left;margin-right:15px">
                                    <tbody>
                                      <tr>
                                        <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;border-radius:4px" align="center" class="sd_manage_button_background_view"  bgcolor="'.$manage_button_background.'"><a href="'.$manage_subscription_url.'" class="sd_manage_button_text_color_view sd_manage_subscription_txt_view sd_manage_subscription_url_view" target="_blank" style="font-size:16px;text-decoration:none;display:block;color:'.$manage_button_text_color.';padding:20px 25px">'.$manage_subscription_txt.'</a></td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </center>
              </td>
            </tr>
          </tbody>
        </table>
          '.$subscription_line_items.'
        <table style="width:100%;border-spacing:0;border-collapse:collapse">
          <tbody>
            <tr>
              <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding:40px 0">
                <center>
                  <table class="m_-1845756208323497270container" style="width:560px;text-align:left;border-spacing:0;border-collapse:collapse;margin:0 auto">
                    <tbody>
                      <tr>
                        <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                          <table style="width:100%;border-spacing:0;border-collapse:collapse">
                            <tbody>
                              <tr>
                                <td class="m_-1845756208323497270customer-info__item sd_show_shipping_address '.($show_shipping_address == '0' ?'display-hide-label' : '').'" style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;width:50%" valign="top">
                                  <h4 style="font-weight:500;font-size:16px;color:'.$heading_text_color.';margin:0 0 5px" class="sd_heading_text_color_view sd_shipping_address_text_view">'.$shipping_address_text.'</h4>
                                  <div class="sd_shipping_address_view sd_text_color_view" style="color:'.$text_color.';">'.$shipping_address.'</div>
                                </td>
                                <td class="m_-1845756208323497270customer-info__item sd_show_billing_address '.($show_billing_address == '0' ? 'display-hide-label' : '').'" style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;width:50%" valign="top">
                                  <h4 style="font-weight:500;font-size:16px;color:'.$heading_text_color.';margin:0 0 5px" class="sd_heading_text_color_view sd_billing_address_text_view">'.$billing_address_text.'</h4>
                                  <div class="sd_billing_address_view sd_text_color_view" style="color:'.$text_color.';">'.$billing_address.'</div>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                          <div class="sd_show_payment_method '.($show_payment_method == '0' ? 'display-hide-label' : '').'">
                          <table style="width:100%;border-spacing:0;border-collapse:collapse">
                            <tbody>
                              <tr>
                                <td class="m_-1845756208323497270customer-info__item" style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;width:50%" valign="top">
                                  <h4 style="font-weight:500;font-size:16px;color:'.$heading_text_color.';margin:0 0 5px" class="sd_heading_text_color_view sd_payment_method_text_view">'.$payment_method_text.'</h4>
                                  <p style="color:'.$text_color.';line-height:150%;font-size:16px;margin:0" class="sd_text_color_view">
                                    {{card_brand}}
                                    <span style="font-size:16px;color:'.$text_color.'" class="sd_text_color_view sd_ending_in_text_view">'.$ending_in_text.'{{last_four_digits}}</span><br>
                                  </p>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </center>
              </td>
            </tr>
          </tbody>
        </table>
        <table style="width:100%;border-spacing:0;border-collapse:collapse;border-top-width:1px;border-top-color:#e5e5e5;border-top-style:solid">
          <tbody>
            <tr>
              <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding:35px 0">
                <center>
                  <table class="m_-1845756208323497270container" style="width:560px;text-align:left;border-spacing:0;border-collapse:collapse;margin:0 auto">
                    <tbody>
                      <tr>
                        <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                        <div class="sd_footer_text_view">'.$footer_text.'</div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </center>
              </td>
            </tr>
          </tbody>
        </table>
        <img src="https://ci4.googleusercontent.com/proxy/C5WAwfRu-nhYYB726ZtDmBBZxH2ZQQgtpxwmJT5KONtMOVp6k7laRdD7JghQXsHLcYM4veQr436syfT22M4kVYeof9oM4TIq5I7li0_YUjrim2hpHv5dYG7V9z9OmFYRRwYK3KgYIf0ck0d_WTq1EjhX_DpBFoi4n20fTmcCfJxl76PIrL1HodOHxbkR8PrieSaJX9F3tcNZb-9L3JTm7_owWlAKVQ64kFMBmJHwK7I=s0-d-e1-ft#https://cdn.shopify.com/shopifycloud/shopify/assets/themes_support/notifications/spacer-1a26dfd5c56b21ac888f9f1610ef81191b571603cb207c6c0f564148473cab3c.png" class="m_-1845756208323497270spacer CToWUd" height="1" style="min-width:600px;height:0" data-bit="iit">
      </td>
    </tr>
    </tbody>
    </table>
          </div>
          </div>
          </div>
        </td>
      </tr>
    </tbody>
    </table>
    </div>';
        if($custom_template != '' && $custom_template != '<br>' && $template_type == 'custom'){
          $template_data = $custom_template;
        }else if($template_type == 'default'){
          $config = array(
            'indent'         => true,
            'output-xhtml'   => true,
            'wrap'           => 200
          );
          $doc = new DOMDocument();
          libxml_use_internal_errors(true);
          $doc->loadHTML('<?xml encoding="UTF-8">' . $default_email_template, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
          $xpath = new DOMXPath($doc);
          $elementsToRemove = $xpath->query("//*[contains(@class,'display-hide-label')]");
          foreach ($elementsToRemove as $element) {
            $element->parentNode->removeChild($element);
          }
          $modifiedHtml = $doc->saveHTML($doc->documentElement);
          $modifiedHtml = mb_convert_encoding($modifiedHtml, 'HTML-ENTITIES', 'UTF-8');
          $template_data = urldecode($modifiedHtml);
        }
        $count = -1;
        $result = str_replace(
          array('{{subscription_contract_id}}','{{customer_email}}','{{customer_name}}','{{customer_id}}','{{next_order_date}}','{{selling_plan_name}}','{{shipping_full_name}}','{{shipping_address1}}','{{shipping_company}}','{{shipping_city}}','{{shipping_province}}','{{shipping_province_code}}','{{shipping_zip}}','{{billing_full_name}}','{{billing_address1}}','{{billing_city}}','{{billing_province}}','{{billing_province_code}}','{{billing_zip}}','{{product_quantity}}','{{subscription_line_items}}','{{last_four_digits}}','{{card_expire_month}}','{{card_expire_year}}','{{shop_name}}','{{shop_email}}','{{shop_domain}}','{{manage_subscription_url}}','{{delivery_cycle}}','{{billing_cycle}}','{{email_subject}}','{{header_text_color}}','{{text_color}}','{{heading_text}}','{{logo_image}}','{{manage_subscription_button_color}}','{{manage_subscription_button_text}}','{{manage_subscription_button_text_color}}','{{shipping_address_text}}','{{billing_address_text}}','{{payment_method_text}}','{{ending_in_text}}','{{logo_height}}','{{logo_width}}','{{thanks_image}}','{{thanks_image_height}}','{{thanks_image_width}}','{{logo_alignment}}','{{thanks_image_alignment}}','{{card_brand}}','{{order_number}}','{{new_added_products}}','{{removed_product}}','{{updated_product}}','{{skipped_order_date}}','{{actual_fulfillment_date}}','{{new_scheduled_date}}','{{new_shipping_price}}','{{new_renewal_date}}','{{renewal_date}}'),
          array($subscription_contract_id,$customer_email,$customer_name,$customer_id,$next_order_date,$selling_plan_name,$shipping_full_name,$shipping_address1,$shipping_company,$shipping_city,$shipping_province,$shipping_province_code,$shipping_zip,$billing_full_name,$billing_address1,$billing_city,$billing_province,$billing_province_code,$billing_zip,$product_quantity,$subscription_line_items,$last_four_digits,$card_expire_month,$card_expire_year,$shop_name,$shop_email,$shop_domain,$manage_subscription_url,$delivery_cycle,$billing_cycle,$email_subject,$heading_text_color,$text_color,$heading_text,$logo,$manage_button_background,$manage_subscription_txt,$manage_button_text_color,$shipping_address_text,$billing_address_text,$payment_method_text,$ending_in_text,$logo_height,$logo_width,$thanks_img,$thanks_img_height,$thanks_img_width,$logo_alignment,$thanks_img_alignment,$card_brand,$order_number,$new_added_products,$deleted_product,$updated_product,$skipped_order_date,$actual_fulfillment_date,$new_scheduled_date,$new_shipping_price,$new_renewal_date,$new_renewal_date),
          $template_data,
          $count
        );

        $return_template_array = array(
          'test_email_content' => $result,
          'default_email_template' => $default_email_template,
          'email_subject' => $email_subject,
          'ccc_email' => $ccc_email,
          'bcc_email' => $bcc_email,
          'reply_to' => $reply_to,
          'template_type' =>$template_type,
        );
        return $return_template_array;
      }
    }



?>
<head>
<?php if($logged_in_customer_id == ''){?>
<title>Advanced Subscription Pro</title>
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $image_folder; ?>favicon.ico">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php } ?>
<input type="hidden" value="<?php echo $store ?>" id="store">
<input type="hidden" value="<?php echo $store_id; ?>" id="store_id">
<?php if($logged_in_customer_id == ''){
   echo "<script> const AjaxCallFrom = 'backendAjaxCall';</script>";
  ?>
<input type="hidden" value="<?php //echo $app_status ?>" id="theme_customizer_app">
<input type="hidden" value="<?php echo $currency; ?>" id="SHOPIFY_CURRENCY" />
<input type="hidden" value="<?php echo $currency_code; ?>" id="SHOPIFY_CURRENCY_CODE" />
<input type="hidden" value="<?php echo $theme_block_name; ?>" id="THEME_BLOCK_NAME" />
<input type="hidden" value="<?php echo $app_extension_id; ?>" id="APP_EXTENSION_ID" />
<input type="hidden" value="<?php //echo $check_selling_plan; ?>" id="CHECK_SELLING_PLAN" />
<input type="hidden" value="<?php //echo $check_shopify_payment; ?>" id="SHOPIFY_PAYMENT_REQUIREMENT" />
<input type="hidden" value="<?php //echo $amount_exceed; ?>" id="sd_amount_exceed" />
<?php }else{
    echo "<script> const AjaxCallFrom = 'frontendAjaxCall';</script>";
} ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/@shopify/polaris@7.3.1/build/esm/styles.css"/>

<?php if($logged_in_customer_id == ''){?>
  <link href="<?php echo $SHOPIFY_DOMAIN_URL ;?>/application/assets/css/font_family.css" rel="stylesheet">
  <meta name="shopify-api-key" content="<?php echo $SHOPIFY_APIKEY; ?>" />
  <script src="https://cdn.shopify.com/shopifycloud/app-bridge.js"></script>
  <ui-nav-menu>
    <a href="/" rel="home">Dashboard</a>
    <a href="/admin/plans/subscription_group.php">My Plans</a>


    <?php if($store == 'mini-cart-development.myshopify.com' || $store == 'c401e3-2.myshopify.com'){ ?>
      <a href="/admin/subscription/boneSubscriptions.php">Subscriptions</a>
    <?php } else{?>
      <a href="/admin/subscription/subscriptions.php">Subscriptions</a>
    <?php } ?>

    <a href="/admin/subscriptionOrders.php">Subscription orders</a>
    <a href="/admin/settings/setting.php">Settings</a>
    <a href="/admin/analytics.php">Analytics</a>
    <a href="/admin/documentation.php">Documentation</a>
    <a href="/admin/video_tutorials.php">Video Tutorials</a>
    <a href="/admin/contactUs.php">Contact Us</a>
    <a href="/admin/app_plans.php">App Plans</a>
  </ui-nav-menu>
<?php } ?>
<link href="<?php echo $SHOPIFY_DOMAIN_URL ;?>/application/assets/css/style.css" rel="stylesheet">
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
