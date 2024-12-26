<?php
use PHPShopify\ShopifySDK;
$dirPath = dirname(dirname(__DIR__));
include $dirPath . "/application/library/config.php";
require ($dirPath . "/PHPMailer/src/PHPMailer.php");
require ($dirPath . "/PHPMailer/src/SMTP.php");
require ($dirPath . "/PHPMailer/src/Exception.php");
include $dirPath . "/graphLoad/autoload.php";
$store = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
$json_str = file_get_contents('php://input');
file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_billing_attempts_success.txt", $json_str, FILE_APPEND | LOCK_EX);
$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
function verify_webhook($data, $hmac_header, $API_SECRET_KEY)
{
    $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $API_SECRET_KEY, true));
    return hash_equals($hmac_header, $calculated_hmac);
}
$verified = verify_webhook($json_str, $hmac_header, $API_SECRET_KEY);
if ($verified)
{
    $max_cycle_update_fields = [];
    $after_cycle_update_fields = [];
    $json_obj = json_decode($json_str, true);
    $subscription_contract_id = $json_obj['subscription_contract_id'];
    $billingAttempt_id = $json_obj['id'];
    $order_id = $json_obj['order_id'];
    $currentDate = date('Y-m-d');
    $store_install_query = $db->query("Select access_token,id FROM install WHERE store = '$store'");
    $store_install_data = $store_install_query->fetch(PDO::FETCH_ASSOC);
    $access_token = $store_install_data['access_token'];
    $store_id = $store_install_data['id'];
    $config = array(
        'ShopUrl' => $store,
        'AccessToken' => $access_token
    );
    $shopifies = new ShopifySDK($config);
    $whereCondition = array(
        "contract_id" => $subscription_contract_id,
        "billingAttemptId" => $billingAttempt_id
    );

    // get order number by order id
    $get_order_number = '{
        order(id: "gid://shopify/Order/' . $order_id . '"){
        name
            subtotalPriceSet{
                shopMoney{
                    currencyCode
                    amount
                }
                presentmentMoney{
                    amount
                    currencyCode
                }
            }
        }
    }';
    $get_order_data = $shopifies->GraphQL->post($get_order_number, null, null, null);
    $order_no = (int)filter_var($get_order_data['data']['order']['name'], FILTER_SANITIZE_NUMBER_INT);
    $order_total = $get_order_data['data']['order']['subtotalPriceSet']['presentmentMoney']['amount'];
    $order_currency = $get_order_data['data']['order']['subtotalPriceSet']['presentmentMoney']['currencyCode'];
    //get database table id of billingAttempts table
    $billingAttemptFields = array(
        'status' => 'Success',
        'order_id' => $order_id,
        'order_no' => $order_no,
        'contract_id' => $subscription_contract_id,
        'billingAttemptResponseDate' => $currentDate,
        'order_total' => $order_total,
        'updated_at' => gmdate('Y-m-d H:i:s')
    );
    update_row('billingAttempts', $billingAttemptFields, $whereCondition, 'and', $db);
    $where_contract_condition = array(
       'contract_id' => $subscription_contract_id,
       'store_id' => $store_id
    );
    $subscription_order_inprocesss_status = array(
      'contract_inprocess' => 'no',
    );
    update_row('subscriptionOrderContract', $subscription_order_inprocesss_status, $where_contract_condition, 'and', $db);
    $recurring_order_subscription_tags = 'sd_send_order_invoice,sd_recurring_subscription_order,sd_subscription_contractId_' . $subscription_contract_id;
    // update total_sale table
    $check_entry_contract_sale = $db->prepare("SELECT * FROM contract_sale WHERE store_id = '$store_id' and contract_id = '$subscription_contract_id'");
    $check_entry_contract_sale->execute();
    $contract_sale_entry_count = $check_entry_contract_sale->rowCount();

    if(!$contract_sale_entry_count) {
        $insert_contract_sale = $db->prepare("INSERT INTO contract_sale (store_id, contract_id, total_sale,contract_currency) VALUES ('$store_id', '$subscription_contract_id', '$order_total','$order_currency')");
        $insert_contract_sale->execute();
    }else{
       $update_contract_sale = $db->query("UPDATE contract_sale SET total_sale = total_sale+$order_total WHERE store_id = '$store_id' and contract_id = '$subscription_contract_id'");
    }

    $whereContractCondition = array(
        'contract_id' => $subscription_contract_id
    );
    //get customer and admin email of the contract
    $get_contract_data_query = $db->query("SELECT s.store_email,s.shop_name,customer_billing_attempted,admin_billing_attempted,c.shopify_customer_id,c.email,c.name,o.delivery_policy_value,o.billing_policy_value,o.delivery_billing_type,o.recurring_discount_type,o.recurring_discount_value,o.max_cycle,o.max_cycle_update,o.next_billing_date,o.after_cycle_update,o.after_cycle, sa.first_name as shipping_first_name, sa.last_name as shipping_last_name, sa.address1 as shipping_address1, sa.city as shipping_city, sa.province as shipping_province, sa.province_code as shipping_province_code, sa.zip  as shipping_zip, sa.company as shipping_company,ba.first_name as billing_first_name, ba.last_name as billing_last_name, ba.address1 as billing_address1, ba.city as billing_city, ba.province as billing_province, ba.province_code as billing_province_code, ba.zip as billing_zip, ba.company as billing_company, pm.payment_instrument_value FROM  email_notification_setting, store_details as s, customers as c , subscriptionOrderContract as o, subscriptionContractShippingAddress as sa, subscriptionContractBillingAddress as ba, customerContractPaymentmethod as pm WHERE c.shopify_customer_id = o.shopify_customer_id and s.store_id = o.store_id and o.contract_id = '$subscription_contract_id' and email_notification_setting.store_id = o.store_id and ba.contract_id = o.contract_id and sa.contract_id = o.contract_id and pm.shopify_customer_id = o.shopify_customer_id");
    $get_contract_data = $get_contract_data_query->fetch(PDO::FETCH_ASSOC);
    $get_contract_total_orders_query = $db->query("SELECT COUNT(*) as recurringOrderTotal FROM billingAttempts WHERE store_id = '$store_id' AND contract_id = '$subscription_contract_id' AND status = 'Success'");
    $get_contract_total_orders = $get_contract_total_orders_query->fetch(PDO::FETCH_ASSOC);
    $total_contract_orders = ($get_contract_total_orders['recurringOrderTotal'] + 1);// first order entry is not inserting in billing attempt table that's why we are adding 1 here
    // check maxCycle condition meets then update the status if it is not updated before
    $max_cycle_update = $get_contract_data['max_cycle_update'];
    $max_cycle = $get_contract_data['max_cycle'];
    if ($max_cycle_update != '1' && $max_cycle != 0)
    {
        if ($total_contract_orders >= $get_contract_data['max_cycle'])
        {
            // update contract contract status and max_cycle_update based on the max cycle  at date
            $max_cycle_update_fields = array(
                'contract_status' => 'P',
                'max_cycle_update' => '1',
                'updated_at' => gmdate('Y-m-d H:i:s')
            );
        }
    }

    $get_contract_products_query = $db->query("SELECT variant_id,contract_line_item_id,subscription_price,recurring_computed_price,product_name,variant_name,quantity,variant_image FROM subscritionOrderContractProductDetails WHERE contract_id = '$subscription_contract_id' AND product_contract_status = '1'");
    $contract_products_array = $get_contract_products_query->fetchAll(PDO::FETCH_ASSOC);
    $product_price_column = 'subscription_price';
    $contract_products = implode(',',array_column($contract_products_array, 'variant_id'));
    if ($get_contract_data['after_cycle_update'] != '1' && $get_contract_data['after_cycle'] > 0) {
        if ($total_contract_orders >= $get_contract_data['after_cycle']) {
            // update recurring price
            $product_price_column = 'recurring_computed_price';
            try {
                $getContractDraft = 'mutation {
                    subscriptionContractUpdate(
                        contractId: "gid://shopify/SubscriptionContract/' . $subscription_contract_id . '"
                    ) {
                        draft {
                            id
                        }
                        userErrors {
                            field
                            message
                        }
                    }
                }';
                $contractDraftArray = $shopifies->GraphQL->post($getContractDraft);
                $draftContract_execution_error = $contractDraftArray['data']['subscriptionContractUpdate']['userErrors'];
                if (!count($draftContract_execution_error)) {
                    $contractDraftid = $contractDraftArray['data']['subscriptionContractUpdate']['draft']['id'];
                }
            } catch (Exception $e) {
                // return 'error';
            }
            if (!count($draftContract_execution_error)) {
                foreach ($contract_products_array as $key => $value) {
                    $lineItemId = $value['contract_line_item_id'];
                    $recurring_computed_price = $value['recurring_computed_price'];
                    try {
                        $updateContractLineItemPrice = 'mutation {
                            subscriptionDraftLineUpdate(
                                draftId: "' . $contractDraftid . '"
                                lineId: "gid://shopify/SubscriptionLine/' . $lineItemId . '"
                                input: { currentPrice: ' . $recurring_computed_price . ' }
                            ) {
                                lineUpdated {
                                        id
                                        currentPrice{
                                        amount
                                    }
                                }
                                userErrors {
                                    field
                                    message
                                    code
                                }
                            }
                        }';
                        $updateContractLine_execution = $shopifies->GraphQL->post($updateContractLineItemPrice);
                        $updateContractLine_execution_error = $updateContractLine_execution['data']['subscriptionDraftLineUpdate']['userErrors'];
                        if (!count($updateContractLine_execution_error)) {
                            $after_cycle_update = '1';
                        }
                    } catch (Exception $e) {
                        // return 'error';
                    }
                }
                try {
                    $updateContractStatus = 'mutation {
                        subscriptionDraftCommit(draftId: "' . $contractDraftid . '") {
                            contract {
                                id
                                status
                            }
                            userErrors {
                                field
                                message
                            }
                        }
                    }';
                    $commitContractStatus_execution = $shopifies->GraphQL->post($updateContractStatus);
                    $commitContractStatus_execution_error = $commitContractStatus_execution['data']['subscriptionDraftCommit']['userErrors'];
                } catch (Exception $e) {
                    // return 'error';
                }
                if (!count($updateContractLine_execution_error)) {
                    $after_cycle_update_fields = array(
                        'after_cycle_update' => '1'
                    );
                }

            }
        }
    }
    // update discount coupon if maximum cycle reached
    $get_contract_datas = '{
        subscriptionContract(id :"gid://shopify/SubscriptionContract/'.$subscription_contract_id.'") {
            id
            lines(first:50){
                    edges{
                        node{
                            id
                            discountAllocations{
                                amount{
                                    amount
                                }
                                discount{
                                __typename
                                    ... on SubscriptionManualDiscount{
                                    title
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }';
    $get_contract_lines_execution = $shopifies->GraphQL->post($get_contract_datas,null,null,null);
    $subscription_contract_products = $get_contract_lines_execution['data']['subscriptionContract']['lines']['edges'];
    foreach($subscription_contract_products as $key=>$value){
        if(empty($value['node']['discountAllocations'])){
            $contract_line_item_id = str_replace("gid://shopify/SubscriptionLine/","",$value['node']['id']);
            $update_product_details = "UPDATE subscritionOrderContractProductDetails SET coupon_applied = 'empty', coupon_value = '0' where contract_line_item_id = '$contract_line_item_id'";
            $queryResult = $db->query($update_product_details);
        }
    }
    // update discount coupon if maximum cycle reached end
    $update_contract_fields = array_merge($after_cycle_update_fields,$max_cycle_update_fields);
    if(!empty($update_contract_fields)){
        update_row('subscriptionOrderContract', $update_contract_fields, $whereContractCondition, 'and', $db);
    }
    //send mail to customer and customer
    $send_mail_to = '';
    $customerEmail = $get_contract_data['email'];
    $customerName = $get_contract_data['name'];
    $adminEmail = $get_contract_data['store_email'];
    $customer_id = $get_contract_data['shopify_customer_id'];
    if($get_contract_data['customer_billing_attempted'] == '1' && $get_contract_data['admin_billing_attempted'] == '1'){
        $send_mail_to = array($customerEmail,$adminEmail);
    }else if($get_contract_data['customer_billing_attempted'] != '1' && $get_contract_data['admin_billing_attempted'] == '1'){
        $send_mail_to = $adminEmail;
    }else if($get_contract_data['customer_billing_attempted'] == '1' && $get_contract_data['admin_billing_attempted'] != '1'){
        $send_mail_to = $customerEmail;
    }

    if ($send_mail_to != '')
    {
        $card_expire_month = '';
        $payment_instrument_value = json_decode($get_contract_data['payment_instrument_value']);
        if($payment_instrument_value->month){
            $dateObj   = DateTime::createFromFormat('!m', $payment_instrument_value->month);
            $card_expire_month = $dateObj->format('F'); // March
        }
        $card_expire_year = $payment_instrument_value->year;
        $last_four_digits = $payment_instrument_value->last_digits;
        $card_brand = $payment_instrument_value->brand;

        $all_shopify_currencies = ['AED' => 'د.إ','AFN' => '؋','ALL' => 'L','AMD' => '֏','ANG' => 'ƒ','AOA' => 'Kz','ARS' => '$','AUD' => '$','AWG' => 'ƒ','AZN' => '₼','BAM' => 'KM','BBD' => '$','BDT' => '৳','BGN' => 'лв','BHD' => '.د.ب','BIF' => 'FBu','BMD' => '$','BND' => '$','BOB' => 'Bs.','BRL' => 'R$','BSD' => '$','BWP' => 'P','BZD' => '$',
        'CAD' => '$','CDF' => 'FC','CHF' => 'CHF','CLP' => '$','CNY' => '¥','COP' => '$','CRC' => '₡','CVE' => '$','CZK' => 'Kč','DJF' => 'Fdj','DKK' => 'kr','DOP' => '$','DZD' => 'د.ج','EGP' => 'E£','ERN' => 'Nfk','ETB' => 'Br','EUR' => '€','FJD' => '$','FKP' => '£','GBP' => '£','GEL' => '₾','GHS' => '₵','GIP' => '£','GMD' => 'D','GNF' => 'FG',
        'GTQ' => 'Q','GYD' => '$','HKD' => '$','HNL' => 'L','HRK' => 'kn','HTG' => 'G','HUF' => 'Ft','IDR' => 'Rp','ILS' => '₪','INR' => '₹','ISK' => 'kr','JMD' => '$','JOD' => 'د.ا','JPY' => '¥','KES' => 'KSh','KGS' => 'лв','KHR' => '៛','KMF' => 'CF','KRW' => '₩','KWD' => 'د.ك','KYD' => '$','KZT' => '₸','LAK' => '₭','LBP' => 'L£','USD' => '$','BTN' => 'Nu.','BYN' => 'Br','CUC' => '$','CUP' => '$'];

        //get email template data
        $email_template_query = $db->query("SELECT * FROM billing_attempted_template WHERE store_id = '$store_id'");
        $template_data = $email_template_query->fetch(PDO::FETCH_ASSOC);
        if(!empty($template_data)){
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
			// $next_charge_date_text = $template_data['next_renewal_date_text'];
			$payment_method_text = $template_data['payment_method_text'];
			$ending_in_text = $template_data['ending_in_text'];
			$qty_text = $template_data['qty_text'];
			$footer_text = $template_data['footer_text'];
			$currency = '';
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
			$payment_method_text = 'Payment method';
			$ending_in_text = 'Ending with';
			$footer_text = '<p style="line-height:150%;font-size:14px;margin:0">Thank You</p>';
			$currency = '';
			$next_charge_date_text = 'Next billing date';
			$delivery_every_text = 'Delivery every';
			$order_number_text = 'Order No.';
            $email_subject = 'Billing Attempted';
            $show_currency = '0';
            $show_shipping_address = '0';
            $show_billing_address = '0';
            $show_line_items = '0';
            $show_payment_method = '0';
            $custom_template = '';
            $show_order_number = '0';
            $content_heading = 'Thanks for your order {{order_number}}! We’ll get it to your doorstep as soon as possible! You will get a shipping notification once your order has left our shop and is on the way to you! ';
            $content_text = '<h2 style="font-weight:normal;font-size:24px;margin:0 0 10px">Hi {{customer_name}}</h2><h2 style="font-weight:normal;font-size:24px;margin:0 0 10px">'.$content_heading.'</h2> <p style="line-height:150%;font-size:16px;margin:0">Please visit manage subscription portal to confirm.</p>';
        }

        $subscription_line_items = '';
        if($show_line_items == '1'){
            $subscription_line_items = '<table style="width:100%;border-spacing:0;border-collapse:collapse" class="sd_show_line_items">
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
                foreach ($contract_products_array as $key => $prdVal) {
                    $product_price = $prdVal[$product_price_column] * $prdVal['quantity'];
                    $subscription_line_items .= '<tr style="border-bottom: 1px solid #f3f3f3;">
                    <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                        <img src="'.$prdVal['variant_image'].'" align="left" width="60" height="60" style="margin-right:15px;border-radius:8px;border:1px solid #e5e5e5" class="CToWUd" data-bit="iit">
                    </td>
                    <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;width:100%">
                        <span style="font-size:16px;font-weight:600;line-height:1.4;color:'.$heading_text_color.'" class="sd_heading_text_color_view">'.$prdVal['product_name'].' '.$prdVal['variant_name'].' x '.$prdVal['quantity'].'</span><br>
                        <span class="sd_text_color_view" style="font-size:14px;color:'.$text_color.'"><span class = "sd_delivery_every_text_view">'.$delivery_every_text.'</span> : '.$get_contract_data['delivery_policy_value'].' '.$get_contract_data['delivery_billing_type'].'</span><br>
                        <span style="font-size:14px;color:'.$text_color.'">'.$next_charge_date_text.' : '.date('d M Y', strtotime($get_contract_data['next_billing_date'])).'</span><br>
                    </td>
                    <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;white-space:nowrap">
                        <p class="sd_text_color_view" style="color:'.$text_color.';line-height:150%;font-size:16px;font-weight:600;margin:0 0 0 15px" align="right">
                           '.$all_shopify_currencies[$order_currency].''.$product_price.' '.($show_currency == '1' ? $order_currency : '').'</span>
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
        }

        if($custom_template == ''){
        $email_template = '<div style="background-color:#efefef" bgcolor="#efefef">
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
                                                        </td>';
                                                        if($show_order_number == '1'){
														  $email_template .= '<td class="m_-1845756208323497270order-number__cell sd_show_order_number" style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;text-transform:uppercase;font-size:14px;color:#999" align="right">
														    <table style="width:100%;text-align:right;">
                                                                <tbody>
                                                                <tr> <td> <span style="font-size:13px,font-weight:600;color:'.$text_color.'" class="sd_order_number_text_view sd_text_color_view"><b>'.$order_number_text.'</b></span> </td> </tr>
                                                                <tr> <td> <span class="sd_text_color_view" style="font-size:16px;color:'.$text_color.'"> '.'#'.$order_no.' </span> </td> </tr>
                                                                </tbody>
														    </table>
                                                          </td>';
                                                        }
                                                $email_template .= '</tr>
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
																		<td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;border-radius:4px" align="center" class="sd_manage_button_background_view"  bgcolor="'.$manage_button_background.'"><a href="{{manage_subscription_url}}" class="sd_manage_button_text_color_view sd_manage_subscription_txt_view" style="font-size:16px;text-decoration:none;display:block;color:'.$manage_button_text_color.';padding:20px 25px">'.$manage_subscription_txt.'</a></td>
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
                                                    <tr>';
                                                    if($show_shipping_address == '1'){
                                                        if(!empty(trim($get_contract_data['shipping_last_name']))){
                                                            $shipping_address = $shipping_address;
                                                        }else{
                                                            $shipping_address = 'No shipping address.';
                                                        }
														$email_template .= '<td class="sd_show_shipping_address" style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;width:50%" valign="top">
															<h4 style="font-weight:500;font-size:16px;color:'.$heading_text_color.';margin:0 0 5px" class="sd_heading_text_color_view sd_shipping_address_text_view">'.$shipping_address_text.'</h4>
															<div class="sd_shipping_address_view sd_text_color_view" style="color:'.$text_color.';">'.$shipping_address.'</div>
                                                        </td>';
                                                    }
                                                    if($show_billing_address == '1'){
													    $email_template .= '<td class="sd_show_billing_address" style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;width:50%" valign="top">
															<h4 style="font-weight:500;font-size:16px;color:'.$heading_text_color.';margin:0 0 5px" class="sd_heading_text_color_view sd_billing_address_text_view">'.$billing_address_text.'</h4>
															<div class="sd_billing_address_view sd_text_color_view" style="color:'.$text_color.';">'.$billing_address.'</div>
                                                        </td>';
                                                    }
													$email_template .= '</tr>
												</tbody>
                                            </table>';
                                            if($show_payment_method == '1'){
                                            $email_template .= '<div class="sd_show_payment_method">
											<table style="width:100%;border-spacing:0;border-collapse:collapse">
												<tbody>
													<tr>
														<td class="" style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;width:50%" valign="top">
															<h4 style="font-weight:500;font-size:16px;color:'.$heading_text_color.';margin:0 0 5px" class="sd_heading_text_color_view sd_payment_method_text_view">'.$payment_method_text.'</h4>
															<p style="color:'.$text_color.';line-height:150%;font-size:16px;margin:0" class="sd_text_color_view">
																{{card_brand}}
																<span style="font-size:16px;color:'.$text_color.'" class="sd_text_color_view sd_ending_in_text_view">'.$ending_in_text.' {{last_four_digits}}</span><br>
															</p>
														</td>
													</tr>
												</tbody>
											</table>
                                            </div>';
                                            }
                                        $email_template .= '</td>
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
    }else{
        $email_template = $custom_template;
    }

    $count = -1;
    $result = str_replace(
        array('{{subscription_contract_id}}','{{customer_email}}','{{customer_name}}','{{customer_id}}','{{next_order_date}}','{{shipping_full_name}}','{{shipping_address1}}','{{shipping_company}}','{{shipping_city}}','{{shipping_province}}','{{shipping_province_code}}','{{shipping_zip}}','{{billing_full_name}}','{{billing_address1}}','{{billing_city}}','{{billing_province}}','{{billing_province_code}}','{{billing_zip}}','{{subscription_line_items}}','{{last_four_digits}}','{{card_expire_month}}','{{card_expire_year}}','{{shop_name}}','{{shop_email}}','{{shop_domain}}','{{manage_subscription_url}}','{{delivery_cycle}}','{{billing_cycle}}','{{email_subject}}','{{header_text_color}}','{{text_color}}','{{heading_text}}','{{logo_image}}','{{manage_subscription_button_color}}','{{manage_subscription_button_text}}','{{manage_subscription_button_text_color}}','{{shipping_address_text}}','{{billing_address_text}}','{{payment_method_text}}','{{ending_in_text}}','{{logo_height}}','{{logo_width}}','{{thanks_image}}','{{thanks_image_height}}','{{thanks_image_width}}','{{logo_alignment}}','{{thanks_image_alignment}}','{{card_brand}}','{{order_number}}'),
        array($subscription_contract_id,$customerEmail,$customerName,$customer_id,$get_contract_data['next_billing_date'],$get_contract_data['shipping_first_name'].' '.$get_contract_data['shipping_last_name'],$get_contract_data['shipping_address1'],$get_contract_data['shipping_company'],$get_contract_data['shipping_city'],$get_contract_data['shipping_province'],$get_contract_data['shipping_province_code'],$get_contract_data['shipping_zip'],$get_contract_data['billing_first_name'].' '.$get_contract_data['billing_last_name'],$get_contract_data['billing_address1'],$get_contract_data['billing_city'],$get_contract_data['billing_province'],$get_contract_data['billing_province_code'],$get_contract_data['billing_zip'],$subscription_line_items,$last_four_digits,$card_expire_month,$card_expire_year,$get_contract_data['shop_name'],$adminEmail,$store,$manage_subscription_url,$get_contract_data['delivery_policy_value'],$get_contract_data['billing_policy_value'],$email_subject,$heading_text_color,$text_color,$heading_text,$logo,$manage_button_background,$manage_subscription_txt,$manage_button_text_color,$shipping_address_text,$billing_address_text,$payment_method_text,$ending_in_text,$logo_height,$logo_width,$thanks_img,$thanks_img_height,$thanks_img_width,$logo_alignment,$thanks_img_alignment,$card_brand,'#'.$order_no),
        $email_template,
        $count
    );

        $sendMailArray = array(
            'sendTo' =>  $send_mail_to,
            'subject' => $email_subject,
            'mailBody' => $result,
            'mailHeading' => '',
            'ccc_email' => $ccc_email,
            'bcc_email' =>  $bcc_email,
            'reply_to' => $reply_to
        );
        try{
            sendMail($sendMailArray, 'false', $store_id, $db, $store);
        }catch(Exception $e) {
            return 'error';
        }
    }
    //send mail to customer and customer end
    //add tag to the order
    $addTagQuery = 'mutation tagsAdd($id: ID!, $tags: [String!]!) {
    tagsAdd(id: $id, tags: $tags) {
      userErrors {
        field
        message
      }
    }
  }';
    $tagsParameters = ["id" => "gid://shopify/Order/" . $order_id, "tags" => $recurring_order_subscription_tags];
    $tagQueryExecution = $shopifies->GraphQL->post($addTagQuery, null, null, $tagsParameters);
    $db = null;
}else{
    http_response_code(401);
}

function update_row($tableName, $fields, $whereCondition, $whereMode, $db)
{
    $where = "";
    $status = '';
    $message = '';
    if (is_array($whereCondition))
    {
        $keys = array_keys($whereCondition);
        $size = sizeof($whereCondition);
        for ($x = 0;$x < $size;$x++)
        {
            if ($x > 0)
            {
                $where .= $whereMode;
            }
            $where .= " " . $keys[$x] . " = '" . $whereCondition[$keys[$x]] . "' ";

        }
    }

    $valueSets = array();

    foreach ($fields as $key => $value)
    {
        $valueSets[] = $key . "= '" . $value . "'";
    }
    $sql_update = "UPDATE $tableName SET " . join(",", $valueSets) . " WHERE $where";
    // if($this->store == 'testing-neha-subscription.myshopify.com'){
    // 	echo $sql_update; die;
    //  }
    $upd = $db->query($sql_update);
    if ($upd)
    {
        $status = true;
        $message = "Updated Successfully";
    }
    else
    {
        $status = false;
        $message = "Error: " . $db->rollback();
    }
    return json_encode(array(
        "status" => $status,
        'message' => $message
    )); // return json

}

function sendMail($sendMailArray, $testMode, $store_id, $db, $store){
    //general mail configuration
    $email_configuration = 'false';
    $email_host = "your-email-host";
    $username = "apikey";
    $password = "email_password";
    $from_email = "your-from-email";
    $encryption = 'tls';
    $port_number = 587;

    // check if the email configuration setting exist and email enable is checked
    $subject = $sendMailArray['subject'];
    $sendTo = $sendMailArray['sendTo'];
    $mailBody = $sendMailArray['mailBody'];
    $mailHeading = $sendMailArray['mailHeading'];
    $email_configuration_query = $db->query("SELECT * FROM email_configuration WHERE store_id = '$store_id'");
    $email_configuration_data = $email_configuration_query->fetch(PDO::FETCH_ASSOC);
    if ($email_configuration_data)
    {
        if ($email_configuration_data['email_enable'] == 'checked')
        {
            $email_host = $email_configuration_data['email_host'];
            $username = $email_configuration_data['username'];
            $password = $email_configuration_data['password'];
            $from_email = $email_configuration_data['from_email'];
            $encryption = $email_configuration_data['encryption'];
            $port_number = $email_configuration_data['port_number'];
            $email_configuration = 'true';
        }
    }
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->IsSMTP();
    $mail->CharSet = "UTF-8";
    $mail->Host = $email_host;
    $mail->SMTPDebug = 1;
    $mail->Port = $port_number; //465 or 587
    $mail->SMTPDebug = false;
    $mail->SMTPSecure = $encryption;
    $mail->SMTPAuth = true;
    $mail->IsHTML(true);
    //Authentication
    $mail->Username = $username;
    $mail->Password = $password;
    if($sendMailArray['ccc_email']){
        $mail->addCC($sendMailArray['ccc_email']);
    }
    if($sendMailArray['bcc_email']){
        $mail->addBCC($sendMailArray['bcc_email']);
    }
    if($sendMailArray['reply_to']){
        $mail->addReplyTo($sendMailArray['reply_to']);
    }
    //Set Params
    if(($email_configuration_data) && ($email_configuration_data['email_enable'] == 'checked')){
        $mail->SetFrom($username,$from_email);
    }else{
        $mail->SetFrom($from_email);
    }
    if (is_array($sendTo))
    {
        $mail->AddAddress($sendTo[0]);
        $mail->AddAddress($sendTo[1]);
        $decrease_counter = 2;
    }
    else
    {
        $mail->AddAddress($sendTo);
        $decrease_counter = 1;
    }
    $mail->Subject = $subject;
    $mail->Body = $mailBody;
    if (!$mail->Send())
    {
        echo json_encode(array(
            "status" => false,
            "message" => $mail->ErrorInfo
        ));
    }
    else
    {
        echo json_encode(array(
            "status" => true,
            "message" => 'Email Sent Successfully'
        ));
    }
}

