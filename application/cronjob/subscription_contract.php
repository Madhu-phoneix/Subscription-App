<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$dirPath = dirname(dirname(__DIR__));

use PHPShopify\ShopifySDK;

require($dirPath . "/PHPMailer/src/PHPMailer.php");
require($dirPath . "/PHPMailer/src/SMTP.php");
require($dirPath . "/PHPMailer/src/Exception.php");
include $dirPath . "/graphLoad/autoload.php";
include $dirPath . "/application/library/config.php";
$contract_cron_query = $db->query("Select * FROM contract_cron WHERE status = 'pending'");
$contract_cron_data = $contract_cron_query->fetch(PDO::FETCH_ASSOC);

if ($contract_cron_data) {
    $store = $contract_cron_data['store'];
    $order_id = $contract_cron_data['order_id'];
    $processDataParameters = array(
        'store' => $store,
        'order_id' => $order_id,
        'db' => $db,
        'dirPath' => $dirPath,
        'SHOPIFY_DOMAIN_URL' => $SHOPIFY_DOMAIN_URL,
        'SHOPIFY_API_VERSION' => $SHOPIFY_API_VERSION
    );
    $contract_html = ''; //mail content variable defined
    $merchant_subscription_title = '';
    $mail_count = 1;
    $store_id = '';
    $customerEmail = '';
    $order_token = '';
    $contract_id = '';
    $payment_instrument_type = '';
    $customerId = '';
    $email_template_data = '';
    $replace_with_value = '';
    $subscription_line_items = '';
    $orderTagsArray = array();
    $customerTagsArray = array();
    $billingAddressAry = array(
        'firstName' => '',
        'lastName' => '',
        'address1' => '',
        'address2' => '',
        'phone' => ''
    );
    $shopifies = array();
    processContracts($processDataParameters, $contract_html, $merchant_subscription_title, $mail_count, $store_id, $customerEmail, $order_token, $contract_id, $payment_instrument_type, $customerId, $customerTagsArray, $orderTagsArray, $billingAddressAry, $shopifies, $email_template_data, $replace_with_value, $subscription_line_items);
}

function processContracts($processDataParameters, $contract_html, $merchant_subscription_title, $mail_count, $store_id, $customerEmail, $order_token, $contract_id, $payment_instrument_type, $customerId, $customerTagsArray, $orderTagsArray, $billingAddressAry, $shopifies, $email_template_data, $replace_with_value, $subscription_line_items)
{
    $all_shopify_currencies = [
        'AED' => 'د.إ', 'AFN' => '؋', 'ALL' => 'L', 'AMD' => '֏', 'ANG' => 'ƒ', 'AOA' => 'Kz', 'ARS' => '$', 'AUD' => '$', 'AWG' => 'ƒ', 'AZN' => '₼', 'BAM' => 'KM', 'BBD' => '$', 'BDT' => '৳', 'BGN' => 'лв', 'BHD' => '.د.ب', 'BIF' => 'FBu', 'BMD' => '$', 'BND' => '$', 'BOB' => 'Bs.', 'BRL' => 'R$', 'BSD' => '$', 'BWP' => 'P', 'BZD' => '$',
        'CAD' => '$', 'CDF' => 'FC', 'CHF' => 'CHF', 'CLP' => '$', 'CNY' => '¥', 'COP' => '$', 'CRC' => '₡', 'CVE' => '$', 'CZK' => 'Kč', 'DJF' => 'Fdj', 'DKK' => 'kr', 'DOP' => '$', 'DZD' => 'د.ج', 'EGP' => 'E£', 'ERN' => 'Nfk', 'ETB' => 'Br', 'EUR' => '€', 'FJD' => '$', 'FKP' => '£', 'GBP' => '£', 'GEL' => '₾', 'GHS' => '₵', 'GIP' => '£', 'GMD' => 'D', 'GNF' => 'FG',
        'GTQ' => 'Q', 'GYD' => '$', 'HKD' => '$', 'HNL' => 'L', 'HRK' => 'kn', 'HTG' => 'G', 'HUF' => 'Ft', 'IDR' => 'Rp', 'ILS' => '₪', 'INR' => '₹', 'ISK' => 'kr', 'JMD' => '$', 'JOD' => 'د.ا', 'JPY' => '¥', 'KES' => 'KSh', 'KGS' => 'лв', 'KHR' => '៛', 'KMF' => 'CF', 'KRW' => '₩', 'KWD' => 'د.ك', 'KYD' => '$', 'KZT' => '₸', 'LAK' => '₭', 'LBP' => 'L£', 'USD' => '$', 'BTN' => 'Nu.', 'BYN' => 'Br', 'CUC' => '$', 'CUP' => '$'
    ];
    $after_cycle_update = '0';
    $store = $processDataParameters['store'];
    $order_id = $processDataParameters['order_id'];
    $db = $processDataParameters['db'];
    $dirPath = $processDataParameters['dirPath'];
    $SHOPIFY_DOMAIN_URL = $processDataParameters['SHOPIFY_DOMAIN_URL'];
    $SHOPIFY_API_VERSION = $processDataParameters['SHOPIFY_API_VERSION'];
    $total_subscription_price = 0;
    // get store access token and store id
    $store_install_query = $db->query("Select access_token, store_id, shop_timezone, shop_name, store_email FROM install LEFT JOIN store_details ON install.id = store_details.store_id WHERE install.store = '$store'");
    $store_install_data = $store_install_query->fetch(PDO::FETCH_ASSOC);
    if ($store_install_data) {
        $access_token = $store_install_data['access_token'];
        $store_id = $store_install_data['store_id'];
        $shop_timezone = $store_install_data['shop_timezone'];
    } else {
        file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n store_install_data not available. " . $contract_id, FILE_APPEND | LOCK_EX);
        die("Something went wrong. Pease try again later.");
    }
    //get email template data
    $email_temlate_query = $db->query("Select * FROM subscription_purchase_template WHERE store_id = '$store_id'");
    $email_template_data = $email_temlate_query->fetch(PDO::FETCH_ASSOC);
    $manage_subscription_url = 'https://' . $store . '/account';
    if (!empty($email_template_data)) {
        $template_type = $email_template_data['template_type'];
        $show_order_number = $email_template_data['show_order_number'];
        $show_shipping_address = $email_template_data['show_shipping_address'];
        $show_billing_address = $email_template_data['show_billing_address'];
        $show_payment_method = $email_template_data['show_payment_method'];
        $show_line_items = $email_template_data['show_line_items'];
        $show_currency = $email_template_data['show_currency'];
        $email_subject = $email_template_data['subject'];
        $from_email = $email_template_data['from_email'];
        $logo = $email_template_data['logo'];
        if ($email_template_data['logo'] != '') {
            $logo = '<img border="0" style="display:block;color:#000000;text-decoration:none;font-family:Helvetica,arial,sans-serif;font-size:16px;" width="' . $email_template_data['logo_width'] . '" alt="" class="sd_logo_view" src="' . $email_template_data['logo'] . '" height="' . $email_template_data['logo_height'] . '" class="CToWUd" data-bit="iit">';
        }
        $ccc_email = $email_template_data['ccc_email'];
        $bcc_email = $email_template_data['bcc_email'];
        $reply_to = $email_template_data['bcc_email'];
        $logo_height = $email_template_data['logo_height'];
        $logo_width = $email_template_data['logo_width'];
        $logo_alignment = $email_template_data['logo_alignment'];
        $thanks_img = $email_template_data['thanks_img'];
        if ($thanks_img != '') {
            $thanks_img = '<img border="0" style="display:block;color:#000000;text-decoration:none;font-family:Helvetica,arial,sans-serif;font-size:16px;" width="' . $email_template_data['thanks_img_width'] . '" alt="" class="sd_thanks_img_view" src="' . $email_template_data['thanks_img'] . '" height="' . $email_template_data['thanks_img_height'] . '" class="CToWUd" data-bit="iit">';
        }
        $thanks_img_width = $email_template_data['thanks_img_width'];
        $thanks_img_height = $email_template_data['thanks_img_height'];
        $thanks_img_alignment = $email_template_data['thanks_img_alignment'];
        $heading_text = $email_template_data['heading_text'];
        $heading_text_color = $email_template_data['heading_text_color'];
        $content_text = $email_template_data['content_text'];
        $text_color = $email_template_data['text_color'];
        $manage_subscription_txt = $email_template_data['manage_subscription_txt'];
        if ($email_template_data['manage_subscription_url'] != '') {
            $manage_subscription_url = $email_template_data['manage_subscription_url'];
        }
        $manage_button_text_color = $email_template_data['manage_button_text_color'];
        $manage_button_background = $email_template_data['manage_button_background'];
        $shipping_address_text = $email_template_data['shipping_address_text'];
        $shipping_address = $email_template_data['shipping_address'];
        $billing_address = $email_template_data['billing_address'];
        $billing_address_text = $email_template_data['billing_address_text'];
        $next_charge_date_text = $email_template_data['next_charge_date_text'];
        $payment_method_text = $email_template_data['payment_method_text'];
        $ending_in_text = $email_template_data['ending_in_text'];
        $qty_text = $email_template_data['qty_text'];
        $footer_text = $email_template_data['footer_text'];
        $custom_template = $email_template_data['custom_template'];
        $delivery_every_text = $email_template_data['delivery_every_text'];
        $order_number_text = $email_template_data['order_number_text'];
    } else {
        $template_type = 'default';
        $email_subject = 'Your recurring order purchase confirmation';
        $ccc_email = '';
        $bcc_email = '';
        $reply_to = '';
        $logo = '<img src="https://www.your-domain.com/subscription/application/assets/images/logo.png" height="63"  width="166">';
        $thanks_img = '<img src="https://www.your-domain.com/subscription/application/assets/images/thank_you.jpg" height="63"  width="166">';
        $logo_height = '63';
        $logo_width = '166';
        $logo_alignment = 'center';
        $thanks_img_width = '166';
        $thanks_img_height = '63';
        $thanks_img_alignment = 'center';
        $heading_text = 'Welcome';
        $heading_text_color = '#495661';
        $content_text = '<h2 style="font-weight:normal;font-size:24px;margin:0 0 10px">Hi {{customer_name}}</h2><h2 style="font-weight:normal;font-size:24px;margin:0 0 10px">Thank you for your purchase!</h2> <p style="line-height:150%;font-size:16px;margin:0">We are getting your order ready to be shipped. We will notify you when it has been sent.</p>';
        $text_color = '#000000';
        $manage_subscription_txt = 'Manage Subscription';
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
        $currency = '';
        $show_currency = '1';
        $show_order_number = '1';
        $show_shipping_address = '1';
        $show_billing_address = '1';
        $show_payment_method = '1';
        $show_line_items = '1';
        $next_charge_date_text = 'Next billing date';
        $delivery_every_text = 'Delivery every';
        $custom_template = '';
        $order_number_text = 'Order No.';
        $qty_text = 'Quantity';
    }

    $contract_details_query = $db->query("Select * FROM contract_details WHERE store = '$store' AND order_id = '$order_id' AND status = 'pending'");
    $contract_details_data = $contract_details_query->fetch(PDO::FETCH_ASSOC);
    if ($contract_details_data) {
        $contract_id = $contract_details_data['contract_id'];
        $contract_payload_json = $contract_details_data['contract_payload'];
        $contract_payload = json_decode($contract_payload_json, true);
        /***** From Webhook File *****/

        // load graphql
        $config = array(
            'ShopUrl' => $store,
            'AccessToken' => $access_token
        );
        $shopifies = new ShopifySDK($config);
        $get_contract_lines = '{
            subscriptionContract(id :"gid://shopify/SubscriptionContract/' . $contract_id . '") {
                originOrder{
                    name
                    tags
                    customerLocale
                    createdAt
                    shippingLine{
                        code
                        originalPriceSet{
                            shopMoney{
                                amount
                                currencyCode
                            }
                        }
                    }
                    shippingAddress{
                        firstName
                        lastName
                        address1
                        address2
                        phone
                        city
                        country
                        company
                        province
                        provinceCode
                        zip
                        countryCodeV2
                    }
                    billingAddress{
                        firstName
                        lastName
                        address1
                        address2
                        phone
                        city
                        country
                        company
                        province
                        provinceCode
                        zip
                        countryCodeV2
                    }
                }
                customer{
                    displayName
                    email
                    state
                    tags
                }
                customerPaymentMethod{
                    id
                    instrument{
                        __typename
                        ... on CustomerCreditCard {
                            brand
                            expiresSoon
                            expiryMonth
                            expiryYear
                            firstDigits
                            lastDigits
                            name
                        }
                        ... on CustomerShopPayAgreement {
                            expiresSoon
                            expiryMonth
                            expiryYear
                            lastDigits
                            name
                        }
                        ... on CustomerPaypalBillingAgreement{
                            paypalAccountEmail
                        }
                    }
                }
                nextBillingDate
                billingPolicy{
                    anchors{
                        cutoffDay
                        day
                        month
                        type
                    }
                }
                lines(first:50){
                    edges{
                        node{
                            customAttributes{
                                key
                                value
                            }
                            id
                            quantity
                            sellingPlanId
                            productId
                            variantId
                            variantTitle
                            title
                            sku
                            variantImage{
                                src
                            }
                            lineDiscountedPrice{
                                amount
                            }
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
                            pricingPolicy {
                                basePrice{
                                    amount
                                }
                                cycleDiscounts {
                                    adjustmentType
                                    afterCycle
                                    computedPrice{
                                        amount
                                    }
                                    adjustmentValue{
                                        ... on MoneyV2{
                                            amount
                                        }
                                        ... on SellingPlanPricingPolicyPercentageValue{
                                            percentage
                                        }
                                    }
                                }

                            }
                            currentPrice{
                                amount
                                currencyCode
                            }
                        }
                    }
                }
            }
        }';
        try {
            $contract_detail = $shopifies->GraphQL->post($get_contract_lines);
            $proceed_contract = 'Yes';
            // echo 'contract details :-<br>';
            // echo '<pre>';
            // print_r($contract_detail);
            // die;

            //code updated on 07 march 2024
            // die;
            //  Bonne custom work  

            $customAttributes_custom = [];
            if (isset($contract_detail['data']['subscriptionContract']['lines']['edges'])) {
                foreach ($contract_detail['data']['subscriptionContract']['lines']['edges'] as $line) {
                    $node = $line['node'];

                    if (isset($node['customAttributes'])) {
                        foreach ($node['customAttributes'] as $attribute) {
                            $key = $attribute['key'];
                            $value = $attribute['value'];
                            $customAttributes_custom[$key] = $value;
                        }
                    }
                }
            }
            $sd_gender = $customAttributes_custom['_gender'] ?? '';
            $sd_productType = $customAttributes_custom['_productType'] ?? '';
            $sd_size = $customAttributes_custom['_itemSize'] ?? '';

            //  Bonne custom work end //

            $order_details_data = $contract_detail['data']['subscriptionContract']['originOrder'];
            $currentUtcDate = gmdate("Y-m-d H:i:s");
            // $currentUtcDate = $contract_detail['data']['subscriptionContract']['originOrder']['createdAt'];
            //customer data
            $customerId = $contract_payload['customer_id'];
            $customerEmail = $contract_detail['data']['subscriptionContract']['customer']['email'];
            $customerName = $contract_detail['data']['subscriptionContract']['customer']['displayName'];
            $customerAccountState = $contract_detail['data']['subscriptionContract']['customer']['state'];
            $customerLocale = $order_details_data['customerLocale'];
            //send account activation mail to the customer if customer account is not activated
            if ($customerAccountState == 'DISABLED') {
                try {
                    PostPutApi('https://' . $store . '/admin/api/' . $SHOPIFY_API_VERSION . '/customers/' . $customerId . '/send_invite.json', 'POST', $access_token, '');
                } catch (Exception $e) {
                    file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n Customer account activationg failed. " . $contract_id . " " . $e->getMessage(), FILE_APPEND | LOCK_EX);
                }
            }
            $shippingAddressAry = $order_details_data['shippingAddress'];
            $shippingLineAry =  $order_details_data['shippingLine'];
            $order_number = $order_details_data['name'];
            $order_no = (int) filter_var($order_details_data['name'], FILTER_SANITIZE_NUMBER_INT);

            if ($mail_count == 1) {
                $customerTagsArray = $contract_detail['data']['subscriptionContract']['customer']['tags'];
                $orderTagsArray = $order_details_data['tags'];
                array_push($orderTagsArray, 'sd_subscription_order', 'sd_send_order_invoice');
                array_push($customerTagsArray, "sd_subscription_customer");
                if ($custom_template != '' && $custom_template != '<br>'  && $template_type == 'custom') {
                    $contract_html .= '';
                    $background_image = '';
                } else if ($template_type == 'default') {
                    $background_image = 'background-image: url(' . $SHOPIFY_DOMAIN_URL . '/application/assets/images/default_template_background.jpg);';
                    $contract_html .= '<div style="background-color:#efefef" bgcolor="#efefef">
                <table role="presentation" cellpadding="0" cellspacing="0" style="border-spacing:0!important;border-collapse:collapse;margin:0;padding:0;width:100%!important;min-width:320px!important;height:100%!important;' . $background_image . 'background-repeat:no-repeat;background-size:100% 100%;background-position:center" width="100%" height="100%">
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
                                                        <tbody>';
                    if ($logo != '') {
                        $contract_html .= '<tr>
                                                                    <td style="font-size:6px;line-height:10px;padding:0px 0px 0px 0px" valign="top" align="center">
                                                                      ' . $logo . '
                                                                    </td>
                                                                </tr>';
                    }
                    if ($thanks_img != '') {
                        $contract_html .= '<tr>
                                                                    <td style="font-size:6px;line-height:10px;padding:0px 0px 0px 0px" valign="top" align="center">
                                                                      ' . $thanks_img . '
                                                                    </td>
                                                                </tr>';
                    }
                    $contract_html .= '</tbody>
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
                                                             <div class="sd_heading_text_color_view" style="color:' . $heading_text_color . '">
                                                               <h1 style="font-weight:normal;font-size:30px;margin:0" class="sd_heading_text_view">
                                                                ' . $heading_text . '
                                                                </h1>
                                                             </div>
                                                            </td>';
                    if ($show_order_number == '1') {
                        $contract_html .= '<td class="m_-1845756208323497270order-number__cell" style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;text-transform:uppercase;font-size:14px;color:' . $text_color . '" align="right">
                                                                <table style="width:100%;text-align:right;">
                                                                <tbody>
                                                                <tr> <td> <span style="font-size:13px,font-weight:600;color:' . $text_color . '"> <b>' . $order_number_text . '</b> </span> </td> </tr>
                                                                <tr> <td> <span style="font-size:16px;color:' . $text_color . '"> {{order_number}} </span> </td> </tr>
                                                                </tbody>
                                                                </table>
                                                             </td>';
                    }
                    $contract_html .= '
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
                                                <div class="sd_content_text_view sd_text_color_view" style="color:' . $text_color . ';">
                                                    ' . $content_text . '
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
                                                                            <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;border-radius:4px" align="center" class="sd_manage_button_background_view"  bgcolor="' . $manage_button_background . '"><a href="' . $manage_subscription_url . '" style="font-size:16px;text-decoration:none;display:block;color:' . $manage_button_text_color . ';padding:20px 25px">
                                                                          <div class="sd_manage_subscription_txt_view">' . $manage_subscription_txt . '</div></a></td>
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
            </table>';
                }
            }

            $billingPolicyValue = $contract_payload['billing_policy']['interval_count'];
            $payment_method_token  =  substr($contract_detail['data']['subscriptionContract']['customerPaymentMethod']['id'], strrpos($contract_detail['data']['subscriptionContract']['customerPaymentMethod']['id'], '/') + 1);
            $payment_instrument_type = $contract_detail['data']['subscriptionContract']['customerPaymentMethod']['instrument']['__typename'];
            if ($payment_instrument_type == 'CustomerCreditCard') {
                $customerPaymentMethodArray = array(
                    'last_digits' => $contract_detail['data']['subscriptionContract']['customerPaymentMethod']['instrument']['lastDigits'],
                    'brand' => $contract_detail['data']['subscriptionContract']['customerPaymentMethod']['instrument']['brand'],
                    'name' => $contract_detail['data']['subscriptionContract']['customerPaymentMethod']['instrument']['name'],
                    'month' => $contract_detail['data']['subscriptionContract']['customerPaymentMethod']['instrument']['expiryMonth'],
                    'year' => $contract_detail['data']['subscriptionContract']['customerPaymentMethod']['instrument']['expiryYear']
                );
            } else if ($payment_instrument_type == 'CustomerShopPayAgreement') {
                $customerPaymentMethodArray = array(
                    'last_digits' => $contract_detail['data']['subscriptionContract']['customerPaymentMethod']['instrument']['lastDigits'],
                    'brand' => '',
                    'name' => $contract_detail['data']['subscriptionContract']['customerPaymentMethod']['instrument']['name'],
                    'month' => $contract_detail['data']['subscriptionContract']['customerPaymentMethod']['instrument']['expiryMonth'],
                    'year' => $contract_detail['data']['subscriptionContract']['customerPaymentMethod']['instrument']['expiryYear']
                );
            } else if ($payment_instrument_type == 'CustomerPaypalBillingAgreement') {
                $customerPaymentMethodArray = array(
                    'last_digits' => '',
                    'brand' => '',
                    'name' => $contract_detail['data']['subscriptionContract']['customerPaymentMethod']['instrument']['paypalAccountEmail'],
                    'month' => '',
                    'year' => ''
                );
            }

            $deliveryPolicyValue = $contract_payload['delivery_policy']['interval_count'];
            $deliveryBillingType = $contract_payload['delivery_policy']['interval'];
            $nextBillingDate = $contract_detail['data']['subscriptionContract']['nextBillingDate'];
            $renewalDate = strtok($nextBillingDate, 'T');
            $dt = new DateTime($nextBillingDate);
            $tz = new DateTimeZone($shop_timezone); // or whatever zone you're after
            $dt->setTimezone($tz);
            $dateTime = $dt->format('Y-m-d H:i:s');
            $shop_renewalDate =  date("d M Y", strtotime($dateTime));
            $cut_off_days = 0;
            $min_cycle = 1;
            $anchor_day = 0;

            if ($contract_payload['billing_policy']['max_cycles']) {
                $max_cycle = $contract_payload['billing_policy']['max_cycles'];
                $max_cycle_text = '<div style="font-family: inherit; text-align: inherit">Expires after : ' . $max_cycle . ' Cycle</div>';
            } else {
                $max_cycle = 0;
                $max_cycle_text = '';
            }

            if ($max_cycle == 1) {
                $contract_status = 'P';
            } else {
                $contract_status = 'A';
            }
            if (!empty($contract_payload['billing_policy']['min_cycles'])) {
                $min_cycle = $contract_payload['billing_policy']['min_cycles'];
            }
            if (!empty($contract_detail['data']['subscriptionContract']['billingPolicy']['anchors'])) {
                if (!empty($contract_detail['data']['subscriptionContract']['billingPolicy']['anchors'][0]['cutoffDay'])) {
                    $cut_off_days = $contract_detail['data']['subscriptionContract']['billingPolicy']['anchors'][0]['cutoffDay'];
                }
                if (!empty($contract_detail['data']['subscriptionContract']['billingPolicy']['anchors'][0]['day'])) {
                    $anchor_day = $contract_detail['data']['subscriptionContract']['billingPolicy']['anchors'][0]['day'];
                }
            }

            try {
                $getContractDraft = 'mutation {
                subscriptionContractUpdate(
                    contractId: "gid://shopify/SubscriptionContract/' . $contract_id . '"
                ) {
                    draft {
                        id
                        deliveryPrice{
                            amount
                        }
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
                    $ship_delivery_price = $contractDraftArray['data']['subscriptionContractUpdate']['draft']['deliveryPrice']['amount'];
                }
            } catch (Exception $e) {
                // return 'error';
            }

            $contract_variant_ids =  array();
            $sub_count = 1;

            try {
                if ($mail_count == 1) {
                    $subscription_line_items = '<table style="width:100%;border-spacing:0;border-collapse:collapse">
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
                                                            <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;">
                                                                <table style="border-spacing:0;border-collapse:collapse">
                                                                <tbody>';
                }

                $email_order_currency = '';
                foreach ($contract_detail['data']['subscriptionContract']['lines']['edges'] as $contractData) { // array to get all selling plan ids added with the product in order
                    if ($show_currency == '1') {
                        $email_order_currency = $contractData['node']['currentPrice']['currencyCode'];
                    }
                    $order_currency = $contractData['node']['currentPrice']['currencyCode'];
                    $currency_code = $all_shopify_currencies[$contractData['node']['currentPrice']['currencyCode']];
                    $discount_type = 'P';
                    $discount_value = 0;
                    $recurring_discount_type = 'P';
                    $recurring_discount_value = 0;
                    $after_cycle = 0;
                    $recurring_computed_price = 0;
                    $coupon_name = 'empty';
                    $discounted_value = 0;
                    $selling_plan_id = substr($contractData['node']['sellingPlanId'], strrpos($contractData['node']['sellingPlanId'], '/') + 1);
                    $contract_line_id =  substr($contractData['node']['id'], strrpos($contractData['node']['id'], '/') + 1);
                    if ($contractData['node']['discountAllocations']) {
                        $coupon_name = $contractData['node']['discountAllocations'][0]['discount']['title'];
                        $discounted_value = $contractData['node']['discountAllocations'][0]['amount']['amount'];
                    }
                    if (!empty($contractData['node']['pricingPolicy'])) {
                        if ($contractData['node']['pricingPolicy']['cycleDiscounts'][0]['adjustmentType'] == 'PERCENTAGE') {
                            $discount_type = 'P';
                            $discount_value = $contractData['node']['pricingPolicy']['cycleDiscounts'][0]['adjustmentValue']['percentage'];
                        } else {
                            $discount_type = 'A';
                            $discount_value = $contractData['node']['pricingPolicy']['cycleDiscounts'][0]['adjustmentValue']['amount'];
                        }

                        //recurring discount data start here
                        if (count($contractData['node']['pricingPolicy']['cycleDiscounts']) > 1) {
                            $after_cycle = $contractData['node']['pricingPolicy']['cycleDiscounts'][1]['afterCycle'];
                            if ($contractData['node']['pricingPolicy']['cycleDiscounts'][1]['adjustmentType'] == 'PERCENTAGE') {
                                $recurring_discount_type = 'P';
                                $recurring_discount_value = $contractData['node']['pricingPolicy']['cycleDiscounts'][1]['adjustmentValue']['percentage'];
                            } else {
                                $recurring_discount_type = 'A';
                                $recurring_discount_value = $contractData['node']['pricingPolicy']['cycleDiscounts'][1]['adjustmentValue']['amount'];
                            }
                            $recurring_computed_price = $contractData['node']['pricingPolicy']['cycleDiscounts'][1]['computedPrice']['amount'];
                            if ($after_cycle == 1) {
                                //update the discount for next cycle if after cycle is 1
                                if (!count($draftContract_execution_error)) {
                                    try {
                                        $updateContractLineItemPrice = 'mutation {
                                        subscriptionDraftLineUpdate(
                                            draftId: "' . $contractDraftid . '"
                                            lineId: "' . $contractData['node']['id'] . '"
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
                            }
                        }
                    }

                    $productName = $contractData['node']['title'];
                    $variantName = $contractData['node']['variantTitle'];
                    if ($variantName == '-' || $variantName == 'Default Title') {
                        $variantName = '';
                    }
                    $productId = substr($contractData['node']['productId'], strrpos($contractData['node']['productId'], '/') + 1);

                    $variantId = substr($contractData['node']['variantId'], strrpos($contractData['node']['variantId'], '/') + 1);
                    if (!empty($contractData['node']['variantImage'])) {
                        $variantImage = $contractData['node']['variantImage']['src'];
                    } else {
                        $variantImage = $SHOPIFY_DOMAIN_URL . '/application/assets/images/no-image.png';
                    }
                    $quantity = $contractData['node']['quantity'];
                    $subscriptionPrice = $contractData['node']['currentPrice']['amount'];
                    array_push($contract_variant_ids, $variantId);
                    $subscription_line_items .= '<tr style="border-bottom: 1px solid #f3f3f3;">
                            <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                                <img src="' . $variantImage . '" align="left" width="60" height="60" style="margin-right:15px;border-radius:8px;border:1px solid #e5e5e5" class="CToWUd" data-bit="iit">
                            </td>
                            <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;width:100%">
                                <span style="font-size:16px;font-weight:600;line-height:1.4;color:' . $heading_text_color . '" class="sd_text_color_view"> ' . $productName . '-' . $variantName . ' x ' . $quantity . '</span><br>
                                <span style="font-size:14px;color:' . $text_color . '">' . $delivery_every_text . ' : ' . $deliveryPolicyValue . ' ' . $deliveryBillingType . '</span><br>
                                <span style="font-size:14px;color:' . $text_color . '">' . $next_charge_date_text . ' : ' . $shop_renewalDate . ' </span><br>
                            </span></td>
                            <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;white-space:nowrap">
                                <p class="sd_text_color_view" style="color:' . $text_color . ';line-height:150%;font-size:16px;font-weight:600;margin:0 0 0 15px" align="right">
                                ' . $currency_code . '' . $subscriptionPrice * $quantity . ' ' . $email_order_currency . '
                                </p>
                            </td>
                        </tr>&nbsp;';
                    try {
                        $product_name = str_replace("'", "", $productName);
                        $variant_name = htmlspecialchars($variantName, ENT_QUOTES);
                        $variant_image = htmlspecialchars($variantImage, ENT_QUOTES);
                        $total_subscription_price += $subscriptionPrice * $quantity;
                        $sql_ordercontract_query = "INSERT INTO subscritionOrderContractProductDetails (`store_id`, `contract_id`, `product_id`, `variant_id`, `product_name`, `variant_name`, `variant_image`, `recurring_computed_price`, `quantity`, `subscription_price`, `contract_line_item_id`,`coupon_applied`,`coupon_value`,`created_at`) VALUES ('$store_id', '$contract_id', '$productId', '$variantId', '$product_name', '$variant_name', '$variant_image', '$recurring_computed_price', '$quantity', '$subscriptionPrice', '$contract_line_id','$coupon_name','$discounted_value','$currentUtcDate')";
                        // echo $sql_ordercontract_query.'<br>';
                        // die;
                        $db->exec($sql_ordercontract_query);
                    } catch (Exception $e) {
                        $proceed_contract = 'No';
                        file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n multiple_insert_update_row subscritionOrderContractProductDetails 2 failed. loop " . $sub_count . "____" . $contract_id . " " . $e->getMessage(), FILE_APPEND | LOCK_EX);
                    }
                    $sub_count++;
                }
                // $contract_html .= $subscription_line_items;
            } catch (Exception $e) {
                file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n Error in forloops . " . $contract_id . " " . $e->getMessage(), FILE_APPEND | LOCK_EX);
            }

            //commit contract draft
            if (!count($draftContract_execution_error)) {
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
            }

            try {
                $customerPaymentMethodArray = json_encode($customerPaymentMethodArray);
                $check_entry = $db->prepare("SELECT * FROM customerContractPaymentmethod WHERE LOWER(store_id) = LOWER('$store_id') AND LOWER(shopify_customer_id) = LOWER('$customerId') AND LOWER(payment_method_token) = LOWER('$payment_method_token')");
                $check_entry->execute();
                $entry_count = $check_entry->rowCount();
                if ($entry_count) {
                    $update_payment_method = $db->prepare("UPDATE customerContractPaymentmethod SET payment_instrument_type = '$payment_instrument_type', payment_instrument_value = '$customerPaymentMethodArray' WHERE store_id = '$store_id' AND shopify_customer_id = '$customerId' AND payment_method_token = '$payment_method_token'");
                    $update_payment_method->execute();
                } else {
                    $insert_payment_method = $db->prepare("INSERT INTO customerContractPaymentmethod (store_id, shopify_customer_id, payment_method_token, payment_instrument_type, payment_instrument_value, created_at) VALUES ('$store_id', '$customerId', '$payment_method_token', '$payment_instrument_type', '$customerPaymentMethodArray', '$currentUtcDate')");
                    $insert_payment_method->execute();
                }
            } catch (Exception $e) {
                // echo '<pre>';
                // print_r($e->getMessage());
                // die;'
                $proceed_contract = 'No';
                file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n insert update into customerContractPaymentmethod failed. " . $contract_id . " " . $e->getMessage(), FILE_APPEND | LOCK_EX);
            }
            $contract_productid_string = implode(',', $contract_variant_ids);

            try {
                $sd_size = strtoupper($sd_size);
                $sd_productType = strtoupper($sd_productType);
                $sd_gender = strtoupper($sd_gender);

                if ($store == 'c401e3-2.myshopify.com' || $store == 'mini-cart-development.myshopify.com') {
                    $renewalDate = modifyDate($renewalDate);
                }

                $sql_ordercontract_query = "INSERT INTO subscriptionOrderContract (`store_id`, `contract_id`, `contract_products`, `order_id`, `order_no`, `shopify_customer_id`, `billing_policy_value`, `delivery_policy_value`, `delivery_billing_type`, `min_cycle`, `max_cycle`, `anchor_day`, `cut_off_days`, `after_cycle`, `after_cycle_update`,`selling_plan_id`, `discount_type`, `discount_value`, `recurring_discount_type`, `recurring_discount_value`, `next_billing_date`, `contract_status`, `firstRenewal_dateTime`,`created_at`,`order_currency`,`updated_at`,`sd_gender`,`sd_product_type`,`sd_size`) VALUES ('$store_id', '$contract_id', '$contract_productid_string', '$order_id', '$order_no', '$customerId', '$billingPolicyValue', '$deliveryPolicyValue', '$deliveryBillingType', '$min_cycle', '$max_cycle', '$anchor_day', '$cut_off_days', '$after_cycle','$after_cycle_update','$selling_plan_id', '$discount_type', '$discount_value', '$recurring_discount_type', '$recurring_discount_value', '$renewalDate', '$contract_status', '$nextBillingDate','$currentUtcDate','$order_currency','$currentUtcDate','$sd_gender','$sd_productType','$sd_size')";
                $db->exec($sql_ordercontract_query);
            } catch (Exception $e) {
                // echo '<pre>';
                // print_r($e->getMessage());
                // die;
                $proceed_contract = 'No';
                file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n multiple_insert_update_row subscriptionOrderContract 1 failed. " . $contract_id . " " . $e->getMessage(), FILE_APPEND | LOCK_EX);
            }
            // insert data into contract_sale
            try {
                $insert_contract_sale_query = "INSERT INTO contract_sale (`store_id`,`contract_id`,`total_sale`,`contract_currency`) VALUES('$store_id','$contract_id', '$total_subscription_price','$order_currency')";
                $db->exec($insert_contract_sale_query);
            } catch (Exception $e) {
                $proceed_contract = 'No';
                file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n multiple_insert_update_row subscriptionOrderContract 1 failed. " . $contract_id . " " . $e->getMessage(), FILE_APPEND | LOCK_EX);
            }
            // insert customer values
            try {
                $sql_customer_query = "INSERT INTO customers (`store_id`, `shopify_customer_id`, `email`, `name`, `created_at`) VALUES ('$store_id', '$customerId', '$customerEmail', '$customerName', '$currentUtcDate')";
                $db->exec($sql_customer_query);
            } catch (Exception $e) {
                $proceed_contract = 'No';
                file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n multiple_insert_update_row single customers 3 failed. " . $contract_id . " " . $e->getMessage(), FILE_APPEND | LOCK_EX);
            }
            if (!empty($shippingAddressAry)) {
                $ship_first_name = $shippingAddressAry['firstName'];
                $ship_last_name = $shippingAddressAry['lastName'];
                $ship_address1 = $shippingAddressAry['address1'];
                $ship_address2 = $shippingAddressAry['address2'];
                $ship_city = $shippingAddressAry['city'];
                $ship_province = $shippingAddressAry['province'];
                $ship_country = $shippingAddressAry['country'];
                $ship_company = $shippingAddressAry['company'];
                $ship_phone = $shippingAddressAry['phone'];
                $ship_province_code = $shippingAddressAry['provinceCode'];
                $ship_country_code = $shippingAddressAry['countryCodeV2'];
                $ship_delivery_method = $shippingLineAry['code'];
                // $ship_delivery_price = $shippingLineAry['originalPriceSet']['shopMoney']['amount'];
                $ship_zip = $shippingAddressAry['zip'];
                try {
                    $check_entry_subcontract_ship = $db->prepare("SELECT * FROM subscriptionContractShippingAddress WHERE LOWER(store_id) = LOWER('$store_id') AND LOWER(contract_id) = LOWER('$contract_id')");
                    $check_entry_subcontract_ship->execute();
                    $subcontract_entry_count = $check_entry_subcontract_ship->rowCount();
                    if ($subcontract_entry_count) {
                        $update_subcontract_ship = $db->prepare("UPDATE subscriptionContractShippingAddress SET first_name = '$ship_first_name', last_name = '$ship_last_name', address1 = '$ship_address1', address2 = '$ship_address2', city = '$ship_city', province = '$ship_province', country = '$ship_country', company = '$ship_company', phone = '$ship_phone', province_code = '$ship_province_code', country_code = '$ship_country_code', delivery_method = '$ship_delivery_method', delivery_price = '$ship_delivery_price', zip = '$ship_zip' WHERE store_id = '$store_id' AND contract_id = '$contract_id'");
                        $update_subcontract_ship->execute();
                    } else {
                        $insert_subcontract_ship = $db->prepare("INSERT INTO subscriptionContractShippingAddress (store_id, contract_id, first_name, last_name, address1, address2, city, province, country, company, phone, province_code, country_code, delivery_method, delivery_price, zip, created_at) VALUES ('$store_id', '$contract_id', '$ship_first_name', '$ship_last_name', '$ship_address1', '$ship_address2', '$ship_city', '$ship_province', '$ship_country', '$ship_company', '$ship_phone', '$ship_province_code', '$ship_country_code', '$ship_delivery_method', '$ship_delivery_price', '$ship_zip', '$currentUtcDate')");
                        $insert_subcontract_ship->execute();
                    }
                } catch (Exception $e) {
                    $proceed_contract = 'No';
                    file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n insert update into subscriptionContractShippingAddress if failed. " . $contract_id . " " . $e->getMessage(), FILE_APPEND | LOCK_EX);
                }
            } else {
                try {
                    $check_entry_subcontract_ship = $db->prepare("SELECT * FROM subscriptionContractShippingAddress WHERE LOWER(store_id) = LOWER('$store_id') AND LOWER(contract_id) = LOWER('$contract_id')");
                    $check_entry_subcontract_ship->execute();
                    $subcontract_entry_count = $check_entry_subcontract_ship->rowCount();
                    if (!$subcontract_entry_count) {
                        $insert_subcontract_ship = $db->prepare("INSERT INTO subscriptionContractShippingAddress (store_id, contract_id, created_at) VALUES ('$store_id', '$contract_id', '$currentUtcDate')");
                        $insert_subcontract_ship->execute();
                    }
                } catch (Exception $e) {
                    $proceed_contract = 'No';
                    file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n insert update into subscriptionContractShippingAddress else failed. " . $contract_id . " " . $e->getMessage(), FILE_APPEND | LOCK_EX);
                }
            }
            //save contract Billing address
            $billingAddressAry = $contract_detail['data']['subscriptionContract']['originOrder']['billingAddress'];
            if (!empty($billingAddressAry)) {
                $bill_first_name = $billingAddressAry['firstName'];
                $bill_last_name = $billingAddressAry['lastName'];
                $bill_address1 = $billingAddressAry['address1'];
                $bill_address2 = $billingAddressAry['address2'];
                $bill_city = $billingAddressAry['city'];
                $bill_province = $billingAddressAry['province'];
                $bill_country = $billingAddressAry['country'];
                $bill_company = $billingAddressAry['company'];
                $bill_phone = $billingAddressAry['phone'];
                $bill_province_code = $billingAddressAry['provinceCode'];
                $bill_country_code = $billingAddressAry['countryCodeV2'];
                $bill_zip = $billingAddressAry['zip'];
                try {
                    $check_entry_subcontract_bill = $db->prepare("SELECT * FROM subscriptionContractBillingAddress WHERE LOWER(store_id) = LOWER('$store_id') AND LOWER(contract_id) = LOWER('$contract_id')");
                    $check_entry_subcontract_bill->execute();
                    $subcontract_bill_entry_count = $check_entry_subcontract_bill->rowCount();
                    if ($subcontract_bill_entry_count) {
                        $update_subcontract_bill = $db->prepare("UPDATE subscriptionContractBillingAddress SET first_name = '$bill_first_name', last_name = '$bill_last_name', address1 = '$bill_address1', address2 = '$bill_address2', city = '$bill_city', province = '$bill_province', country = '$bill_country', company = '$bill_company', phone = '$bill_phone', province_code = '$bill_province_code', country_code = '$bill_country_code', zip = '$bill_zip' WHERE store_id = '$store_id' AND contract_id = '$contract_id'");
                        $update_subcontract_bill->execute();
                    } else {
                        $insert_subcontract_bill = $db->prepare("INSERT INTO subscriptionContractBillingAddress (store_id, contract_id, first_name, last_name, address1, address2, city, province, country, company, phone, province_code, country_code, zip, created_at) VALUES ('$store_id', '$contract_id', '$bill_first_name', '$bill_last_name', '$bill_address1', '$bill_address2', '$bill_city', '$bill_province', '$bill_country', '$bill_company', '$bill_phone', '$bill_province_code', '$bill_country_code', '$bill_zip', '$currentUtcDate')");
                        $insert_subcontract_bill->execute();
                    }
                } catch (Exception $e) {
                    $proceed_contract = 'No';
                    file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n insert update into subscriptionContractBillingAddress if failed. " . $contract_id . " " . $e->getMessage(), FILE_APPEND | LOCK_EX);
                }
            } else {
                try {
                    $check_entry_subcontract_bill = $db->prepare("SELECT * FROM subscriptionContractBillingAddress WHERE LOWER(store_id) = LOWER('$store_id') AND LOWER(contract_id) = LOWER('$contract_id')");
                    $check_entry_subcontract_bill->execute();
                    $subcontract_bill_entry_count = $check_entry_subcontract_bill->rowCount();
                    if (!$subcontract_bill_entry_count) {
                        $insert_subcontract_bill = $db->prepare("INSERT INTO subscriptionContractBillingAddress (store_id, contract_id, created_at) VALUES ('$store_id', '$contract_id', '$currentUtcDate')");
                        $insert_subcontract_bill->execute();
                    }
                } catch (Exception $e) {
                    $proceed_contract = 'No';
                    file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n insert update into subscriptionContractBillingAddress else failed. " . $contract_id . " " . $e->getMessage(), FILE_APPEND | LOCK_EX);
                }
            }

            $last_four_digits = $contract_detail['data']['subscriptionContract']['customerPaymentMethod']['instrument']['lastDigits'];
            $card_brand = $contract_detail['data']['subscriptionContract']['customerPaymentMethod']['instrument']['brand'];
            $card_name = $contract_detail['data']['subscriptionContract']['customerPaymentMethod']['instrument']['name'];
            $card_expire_month = $contract_detail['data']['subscriptionContract']['customerPaymentMethod']['instrument']['expiryMonth'];
            $card_expire_year = $contract_detail['data']['subscriptionContract']['customerPaymentMethod']['instrument']['expiryYear'];

            $replace_with_value = array('contract_id' => $contract_id, 'customerEmail' => $customerEmail, 'customerName' => $customerName, 'customerId' => $customerId, 'nextBillingDate' => $nextBillingDate, 'ship_first_name' => $ship_first_name . ' ' . $ship_last_name, 'ship_address1' => $ship_address1, 'ship_company' => $ship_company, 'ship_city' => $ship_city, 'ship_province' => $ship_province, 'ship_province_code' => $ship_province_code, 'ship_zip' => $ship_zip, 'bill_first_name' => $bill_first_name . ' ' . $bill_last_name, 'bill_address1' => $bill_address1, 'bill_city' => $bill_city, 'bill_province_code' => $bill_province_code, 'bill_zip' => $bill_zip, 'footer_text' => $footer_text, 'last_four_digits' => $last_four_digits, 'card_expire_month' => $card_expire_month, 'card_expire_year' => $card_expire_year, 'shop_name' => $store_install_data['shop_name'], 'store_email' => $store_install_data['store_email'], 'store' => $store, 'manage_subscription_url' => $manage_subscription_url, 'email_subject' => $email_subject, 'heading_text_color' => $heading_text_color, 'text_color' => $text_color, 'heading_text' => $heading_text, 'logo_image' => $logo, 'manage_button_background' => $manage_button_background, 'manage_subscription_txt' => $manage_subscription_txt, 'manage_button_text_color' => $manage_button_text_color, 'shipping_address_text' => $shipping_address_text, 'billing_address_text' => $billing_address_text, 'payment_method_text' => $payment_method_text, 'ending_in_text' => $ending_in_text, 'qty_text' => $qty_text, 'logo_height' => $logo_height, 'logo_width' => $logo_width, 'thanks_image' => $thanks_img, 'thanks_image_height' => $thanks_img_height, 'thanks_image_width' => $thanks_img_width, 'logo_alignment' => $logo_alignment, 'thanks_image_alignment' => $thanks_img_alignment, 'card_brand' => $card_brand, 'order_number' => $order_number, 'shipping_address' => $shipping_address, 'billing_address' => $billing_address, 'custom_template' => $custom_template, 'ccc_email' => $ccc_email, 'bcc_email' => $bcc_email, 'reply_to' => $reply_to);
            //save order token and checkout token in subscriptionOrderContract table
            if ($mail_count == 1) {
                try {
                    $getOrderData = PostPutApi('https://' . $store . '/admin/api/' . $SHOPIFY_API_VERSION . '/orders/' . $order_id . '.json?fields=token', 'GET', $access_token, '');
                } catch (Exception $e) {
                    $proceed_contract = 'No';
                    file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n getOrderData PostPutApi failed. " . $contract_id . " " . $e->getMessage(), FILE_APPEND | LOCK_EX);
                }
                $order_token = $getOrderData['order']['token'];
            }

            try {
                $update_suborder_contract = $db->prepare("UPDATE subscriptionOrderContract SET order_token = '$order_token' WHERE store_id = '$store_id' AND order_id = '$order_id'");
                $update_suborder_contract->execute();
            } catch (Exception $e) {
                file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n update subscriptionOrderContract failed. " . $contract_id . " " . $e->getMessage(), FILE_APPEND | LOCK_EX);
            }
            /***** From Webhook File *****/
            if ($proceed_contract == 'Yes') {
                $contract_details_query = "Update contract_details SET status = 'completed' WHERE store = '$store' AND order_id = '$order_id' AND contract_id = '$contract_id'";
                $contract_details_data = $db->exec($contract_details_query);
                $mail_count++;
                processContracts($processDataParameters, $contract_html, $merchant_subscription_title, $mail_count, $store_id, $customerEmail, $order_token, $contract_id, $payment_instrument_type, $customerId, $customerTagsArray, $orderTagsArray, $billingAddressAry, $shopifies, $email_template_data, $replace_with_value, $subscription_line_items);
            } else {
                $sendMailArray = array(
                    'sendTo' =>  'neha.bhagat@shinedezign.com',
                    'subject' => 'Contract not proceeded',
                    'mailBody' => 'Please check the contract id ' . $contract_id . ' of store ' . $store,
                    'mailHeading' => '',
                    'ccc_email' => 'nehaa.shinedezign@gmail.com',
                    'bcc_email' => '',
                    'reply_to' => '',
                );
                try {
                    sendMail($sendMailArray, 'false', $store_id, $db, $store);
                } catch (Exception $e) {
                    file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n sendMail() function 1 failed. " . $e->getMessage(), FILE_APPEND | LOCK_EX);
                }
            }


            //code updated end on 07 march 2024

        } catch (Exception $e) {
            $proceed_contract = 'No';
            // echo 'contract details error:-<br>';
            // echo '<pre>';
            // print_r($e->getMessage());
            file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n Grphql failed. " . $contract_id . " " . $e->getMessage(), FILE_APPEND | LOCK_EX);
        }
    } else {
        /***** From Webhook File *****/
        // get store detail and email notification setting
        try {
            $result = $db->query("SELECT store_email, customer_subscription_purchase, admin_subscription_purchase FROM store_details, email_notification_setting WHERE store_details.store_id = '$store_id' and email_notification_setting.store_id = '$store_id'");
            $get_store_details = $result->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n Select store_email, new_subscription_purchase failed. " . $e->getMessage(), FILE_APPEND | LOCK_EX);
        }
        // add tag to customer
        $allSubscriptionCustomerTags = implode(",", $customerTagsArray);
        $allSubscriptionOrderTags = implode(",", $orderTagsArray);
        //add tag to the order
        $addTagQuery = 'mutation tagsAdd($id: ID!, $tags: [String!]!) {
            tagsAdd(id: $id, tags: $tags) {
                userErrors {
                    field
                    message
                }
            }
        }';
        $orderTagsParameters = [
            "id" => "gid://shopify/Order/$order_id",
            "tags" => $allSubscriptionOrderTags
        ];
        try {
            $shopifies->GraphQL->post($addTagQuery, null, null, $orderTagsParameters);
        } catch (Exception $e) {
            file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n add tag to custoemer graphql failed. " . $contract_id . " " . $e->getMessage(), FILE_APPEND | LOCK_EX);
        }
        $addTagQuery = 'mutation tagsAdd($id: ID!, $tags: [String!]!) {
            tagsAdd(id: $id, tags: $tags) {
                userErrors {
                    field
                    message
                }
            }
        }';
        $tagsParameters = [
            "id" => "gid://shopify/Customer/$customerId",
            "tags" => $allSubscriptionCustomerTags
        ];
        // if($allSubscriptionCustomerTags != '' && $customerId != '') {
        try {
            $shopifies->GraphQL->post($addTagQuery, null, null, $tagsParameters);
        } catch (Exception $e) {
            file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n add tag to custoemer graphql failed. " . $contract_id . " " . $e->getMessage(), FILE_APPEND | LOCK_EX);
        }
        // }
        //send mail to the customer and admin if setting is on
        if ($get_store_details) {
            $customer_subscription_purchase = $get_store_details['customer_subscription_purchase'];
            $admin_subscription_purchase = $get_store_details['admin_subscription_purchase'];
            $send_to_email = '';
            if ($replace_with_value['custom_template'] != '' && $replace_with_value['custom_template'] != '<br>'  && $template_type == 'custom') {
                $subscription_line_items .=
                    '             </tbody>
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
              </div>
              </div>
          </td>
      </tr>
  </tbody>
</table>
</div>

              ';
                $contract_html = str_replace("{{subscription_line_items}}", $subscription_line_items, $replace_with_value['custom_template']);
            } else if ($template_type == 'default') {
                if ($show_line_items == '1') {
                    $contract_html .= $subscription_line_items . '</tbody>
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
                $contract_html .= '<table style="width:100%;border-spacing:0;border-collapse:collapse">
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
                if ($show_shipping_address == '1') {
                    if (!empty(trim($replace_with_value['ship_first_name']))) {
                        $shipping_address = $replace_with_value['shipping_address'];
                    } else {
                        $shipping_address = 'No shipping address.';
                    }


                    $contract_html .= '
                                                                        <td class="m_-1845756208323497270customer-info__item" style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;width:50%" valign="top">
                                                                            <h4 style="font-weight:500;font-size:16px;color:' . $replace_with_value['heading_text_color'] . ';margin:0 0 5px" class="sd_heading_text_color_view sd_shipping_address_text_view">' . $replace_with_value['shipping_address_text'] . '</h4>
                                                                            <div class="sd_shipping_address_view sd_text_color_view" style="color:' . $replace_with_value['text_color'] . ';"><p style="color:' . $replace_with_value['text_color'] . ';line-height:150%;font-size:16px;margin:0">' . $shipping_address . '</div>
                                                                        </td>';
                }
                if ($show_billing_address == '1') {
                    $contract_html .= '<td class="m_-1845756208323497270customer-info__item" style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;width:50%" valign="top">
                                                                            <h4 style="font-weight:500;font-size:16px;color:' . $replace_with_value['heading_text_color'] . ';margin:0 0 5px" class="sd_heading_text_color_view sd_billing_address_text_view">' . $replace_with_value['billing_address_text'] . '</h4>
                                                                            <div class="sd_billing_address_view sd_text_color_view" style="color:' . $replace_with_value['text_color'] . ';"><p style="color:' . $replace_with_value['text_color'] . ';line-height:150%;font-size:16px;margin:0">' . $replace_with_value['billing_address'] . '</p></div>
                                                                        </td>';
                }
                $contract_html .= '</tr>
                                                                </tbody>
                                                            </table>';
                if ($show_payment_method == '1') {
                    $contract_html .= '<table style="width:100%;border-spacing:0;border-collapse:collapse">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="m_-1845756208323497270customer-info__item" style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;width:50%" valign="top">
                                                                                <h4 style="font-weight:500;font-size:16px;color:' . $replace_with_value['heading_text_color'] . ';margin:0 0 5px" class="sd_heading_text_color_view sd_payment_method_text_view">' . $replace_with_value['payment_method_text'] . '</h4>
                                                                                <p style="color:' . $replace_with_value['text_color'] . ';line-height:150%;font-size:16px;margin:0">
                                                                                    {{card_brand}}
                                                                                    <span style="font-size:16px;color:' . $replace_with_value['text_color'] . '" class="sd_text_color_view sd_ending_in_text_view">' . $replace_with_value['ending_in_text'] . ' {{last_four_digits}}</span><br>
                                                                                </p>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>';
                }
                $contract_html .= '</td>
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
                                                        <div class="sd_footer_text_view">' . $replace_with_value['footer_text'] . '</div>
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
        </div>
    ';
            }
            $html_result = str_replace(
                array(
                    '{{subscription_contract_id}}', '{{customer_email}}', '{{customer_name}}', '{{customer_id}}', '{{next_order_date}}', '{{shipping_full_name}}', '{{shipping_address1}}', '{{shipping_company}}', '{{shipping_city}}', '{{shipping_province}}', '{{shipping_province_code}}', '{{shipping_zip}}', '{{billing_full_name}}', '{{billing_address1}}', '{{billing_city}}', '{{billing_province_code}}', '{{billing_zip}}', '{{footer_text}}', '{{last_four_digits}}', '{{card_expire_month}}', '{{card_expire_year}}', '{{shop_name}}', '{{shop_email}}', '{{shop_domain}}', '{{manage_subscription_url}}', '{{email_subject}}', '{{header_text_color}}', '{{text_color}}', '{{heading_text}}', '{{logo_image}}', '{{manage_subscription_button_color}}', '{{manage_subscription_button_text}}', '{{manage_subscription_button_text_color}}', '{{shipping_address_text}}', '{{billing_address_text}}', '{{payment_method_text}}', '{{ending_in_text}}', '{{quantity_text}}', '{{logo_height}}', '{{logo_width}}', '{{thanks_image}}', '{{thanks_image_height}}', '{{thanks_image_width}}', '{{logo_alignment}}', '{{thanks_image_alignment}}', '{{card_brand}}', '{{order_number}}', '{{shipping_address}}', '{{billing_address}}', '{{custom_template}}', '{{ccc_email}}', '{{bcc_email}}', '{{reply_to}}'
                ),
                array_values($replace_with_value),
                $contract_html
            );

            echo $html_result;
            // die;
            // die;
            $send_to_email = '';
            $store_email = $get_store_details['store_email'];
            if ($customer_subscription_purchase == '1' && $admin_subscription_purchase == '1') {
                $send_to_email = array($customerEmail, $store_email);
            } else if ($customer_subscription_purchase == '1' && $admin_subscription_purchase != '1') {
                $send_to_email = $customerEmail;
            } else if ($customer_subscription_purchase != '1' && $admin_subscription_purchase == '1') {
                $send_to_email = $store_email;
            }

            if ($send_to_email != '' && $template_type != 'none' && strlen(preg_replace('/[\x00-\x1F\x7F]/', '', $html_result)) != 0) {
                $sendMailArray = array(
                    'sendTo' =>  $send_to_email,
                    'subject' => $replace_with_value['email_subject'],
                    'mailBody' => $html_result,
                    'mailHeading' => '',
                    'ccc_email' => $replace_with_value['ccc_email'],
                    'bcc_email' => $replace_with_value['bcc_email'],
                    'reply_to' => $replace_with_value['reply_to'],
                );
                try {
                    sendMail($sendMailArray, 'false', $store_id, $db, $store);
                } catch (Exception $e) {
                    file_put_contents($dirPath . "/application/assets/txt/webhooks/subscription_contracts_create.txt", "\n\n sendMail() function 1 failed. " . $e->getMessage(), FILE_APPEND | LOCK_EX);
                }
            }
        }
        /***** From Webhook File *****/

        $contract_cron_update_query = "Update contract_cron SET status = 'completed' WHERE store = '$store' AND order_id = '$order_id'";
        $db->exec($contract_cron_update_query);
        exit;
    }
    $db = null;
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

function sendMail($sendMailArray, $testMode, $store_id, $db, $store)
{
    $email_configuration = 'false';
    $email_host =  "your-email-host";
    $username = "apikey";
    $password = "your-email-password";
    $from_email = "your-from-email";
    $encryption = 'tls';
    $port_number = 587;
    //For pending mail
    if (array_key_exists("store_id", $sendMailArray)) {
        $store_id = $sendMailArray['store_id'];
    } else {
        $store_id = $store_id;
    }
    $store_detail_query = $db->query("SELECT pending_emails, store_email,shop_name FROM email_counter, store_details WHERE email_counter.store_id = '$store_id' AND store_details.store_id = '$store_id'");
    $store_detail = $store_detail_query->fetch(PDO::FETCH_ASSOC);

    $subject = $sendMailArray['subject'];
    $sendTo = $sendMailArray['sendTo'];
    $email_template_body = $sendMailArray['mailBody'];
    $mailHeading = $sendMailArray['mailHeading'];

    $check_entry = $db->prepare("SELECT * FROM email_configuration WHERE LOWER(store_id) = LOWER('$store_id')");
    $check_entry->execute();
    $email_configuration_data = $check_entry->rowCount();
    if ($email_configuration_data) {
        $query = "SELECT * FROM email_configuration WHERE store_id = '$store_id' ORDER BY id DESC";
        $result = $db->query($query);
        $email_configuration_data = $result->fetch(PDO::FETCH_ASSOC);
    }
    if ($email_configuration_data) {
        if ($email_configuration_data['email_enable'] == 'checked') {
            $email_host = $email_configuration_data['email_host'];
            $username = $email_configuration_data['username'];
            $password = $email_configuration_data['password'];
            $from_email = $email_configuration_data['from_email'];
            $encryption = $email_configuration_data['encryption'];
            $port_number = $email_configuration_data['port_number'];
            $email_configuration = 'true';
        }
    }
    $pending_emails = $store_detail['pending_emails'];

    $mail =  new PHPMailer\PHPMailer\PHPMailer();
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
    //Set Params
    $mail->addReplyTo($sendMailArray['reply_to']);
    $mail->SetFrom($from_email);
    if (is_array($sendTo)) {
        $mail->AddAddress($sendTo[0]);
        $mail->AddAddress($sendTo[1]);
        $decrease_counter = 2;
    } else {
        $mail->AddAddress($sendTo);
        $decrease_counter = 1;
    }
    $mail->Subject = $subject;
    $mail->Body = $email_template_body;
    if (!$mail->Send()) {
        // return json_encode(array("status"=>false, "message"=>$mail->ErrorInfo));
        echo 'mail not sent';
    } else {
        // if($email_configuration == 'false'){
        //     $pending_emails = ($pending_emails - $decrease_counter);
        //     $update_suborder_contract = $db->prepare("UPDATE email_counter SET pending_emails = '$pending_emails' WHERE store_id = '$store_id'");
        //     $update_suborder_contract->execute();
        // }
        // return json_encode(array("status"=>true, "message"=>'Email Sent Successfully'));
        echo 'mail sent successfully';
    }
}

function modifyDate($date) {
    $timestamp = strtotime($date);
    $day = date('d', $timestamp);
    $month = date('m', $timestamp);
    $target_day = 20;
    $target_month = $month;
    
    if ($day >= $target_day) {
        $target_month++;
    }
    
    $target_month_name = date('F', mktime(0, 0, 0, $target_month, 1));
    $modified_date = $target_day . ' ' . $target_month_name . ' ' . date('Y', $timestamp);
   
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        $modified_date = date('Y-m-d', strtotime($modified_date));
    }
    return $modified_date;
}
