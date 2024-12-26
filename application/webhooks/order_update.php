<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $dirPath = dirname(dirname(__DIR__));
    use PHPShopify\ShopifySDK;
    require($dirPath."/PHPMailer/src/PHPMailer.php");
    require($dirPath."/PHPMailer/src/SMTP.php");
    require($dirPath."/PHPMailer/src/Exception.php");
    include $dirPath."/application/library/config.php";
    include $dirPath."/graphLoad/autoload.php";
    require '../../vendor/autoload.php';
    $store = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
    $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
    // $store = 'mini-cart-development.myshopify.com';
    $json_str = file_get_contents('php://input');
    $json_obj = json_decode($json_str,true);
    $order_tags = $json_obj['tags'];
    $order_tags_array = explode(',',$order_tags);
    $order_tags_array = array_map('trim', $order_tags_array);
    file_put_contents($dirPath."/application/assets/txt/webhooks/order_update.txt",$json_str);
    // echo '<pre>';
    // print_r($order_tags_array);
    // die;
    function verify_webhook($data, $hmac_header, $API_SECRET_KEY) {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $API_SECRET_KEY, true));
        return hash_equals($hmac_header, $calculated_hmac);
    }
    $verified = verify_webhook($json_str, $hmac_header, $API_SECRET_KEY);
    if($verified){
    if(in_array("sd_send_order_invoice", $order_tags_array)){
    $store_install_query = $db->query("SELECT id,access_token from install where store = '$store'");
    $store_install_data = $store_install_query->fetch(PDO::FETCH_ASSOC);
    $store_id = $store_install_data['id'];
    $access_token = $store_install_data['access_token'];
    $config = array(
        'ShopUrl' => $store,
        'AccessToken' => $access_token
    );
    $shopifies = new ShopifySDK($config);
    $order_id = $json_obj['id'];
    $invoice_settings_query = $db->query("SELECT * from invoice_template_settings where store_id = '$store_id'");
    $invoice_settings = $invoice_settings_query->fetch(PDO::FETCH_ASSOC);
    $shop_settings_query = $db->query("SELECT * from store_details where store_id = '$store_id'");
    $shop_settings = $shop_settings_query->fetch(PDO::FETCH_ASSOC);
    if($invoice_settings){
        $auto_invoice = $invoice_settings['auto_invoice'];
            if($auto_invoice == '1'){
                $logo = $invoice_settings['logo'];
                $signature = $invoice_settings['signature'];
                $billing_information_text = $invoice_settings['billing_information_text'];
                $serial_number_text = $invoice_settings['serial_number_text'];
                $item_text = $invoice_settings['item_text'];
                $quantity_text = $invoice_settings['quantity_text'];
                $item_subtotal_text = $invoice_settings['item_subtotal_text'];
                $tax_text = $invoice_settings['tax_text'];
                $phone_number_text = $invoice_settings['phone_number_text'];
                $email_text = $invoice_settings['email_text'];
                $email_value = $invoice_settings['email_value'];
                $phone_number_value = $invoice_settings['phone_number_value'];
                $company_name = $invoice_settings['company_name'];
                $terms_conditions_text = $invoice_settings['terms_conditions_text'];
                $terms_conditions_value = $invoice_settings['terms_conditions_value'];
                $show_logo = $invoice_settings['show_logo'];
                $show_signature = $invoice_settings['show_signature'];
                $show_billing_information = $invoice_settings['show_billing_information'];
                $show_subtotal = $invoice_settings['show_subtotal'];
                $show_shipping = $invoice_settings['show_shipping'];
                $show_tax = $invoice_settings['show_tax'];
                $invoice_number_text = $invoice_settings['invoice_number_text'];
                $invoice_number_prefix = $invoice_settings['invoice_number_prefix'];
                $invoice_number_suffix = $invoice_settings['invoice_number_suffix'];
                $show_company_name = $invoice_settings['show_company_name'];
                $show_email = $invoice_settings['show_email'];
                $show_phone_number = $invoice_settings['show_phone_number'];
                $show_invoice_date = $invoice_settings['show_invoice_date'];
                $show_terms_conditions = $invoice_settings['show_terms_conditions'];
                $subtotal_text = $invoice_settings['subtotal_text'];
                $total_text = $invoice_settings['total_text'];
                $shipping_text = $invoice_settings['shipping_text'];
                $show_invoice_number = $invoice_settings['show_invoice_number'];
                $discount_text = $invoice_settings['discount_text'];
                $show_discount = $invoice_settings['show_discount'];

            $all_shopify_currencies = ['AED' => 'د.إ','AFN' => '؋','ALL' => 'L','AMD' => '֏','ANG' => 'ƒ','AOA' => 'Kz','ARS' => '$','AUD' => '$','AWG' => 'ƒ','AZN' => '₼','BAM' => 'KM','BBD' => '$','BDT' => '৳','BGN' => 'лв','BHD' => '.د.ب','BIF' => 'FBu','BMD' => '$','BND' => '$','BOB' => 'Bs.','BRL' => 'R$','BSD' => '$','BWP' => 'P','BZD' => '$',
            'CAD' => '$','CDF' => 'FC','CHF' => 'CHF','CLP' => '$','CNY' => '¥','COP' => '$','CRC' => '₡','CVE' => '$','CZK' => 'Kč','DJF' => 'Fdj','DKK' => 'kr','DOP' => '$','DZD' => 'د.ج','EGP' => 'E£','ERN' => 'Nfk','ETB' => 'Br','EUR' => '€','FJD' => '$','FKP' => '£','GBP' => '£','GEL' => '₾','GHS' => '₵','GIP' => '£','GMD' => 'D','GNF' => 'FG',
            'GTQ' => 'Q','GYD' => '$','HKD' => '$','HNL' => 'L','HRK' => 'kn','HTG' => 'G','HUF' => 'Ft','IDR' => 'Rp','ILS' => '₪','INR' => '₹','ISK' => 'kr','JMD' => '$','JOD' => 'د.ا','JPY' => '¥','KES' => 'KSh','KGS' => 'лв','KHR' => '៛','KMF' => 'CF','KRW' => '₩','KWD' => 'د.ك','KYD' => '$','KZT' => '₸','LAK' => '₭','LBP' => 'L£','USD' => '$','BTN' => 'Nu.','BYN' => 'Br','CUC' => '$','CUP' => '$'];

            $order_details_query = 'query {
                order(id:"gid://shopify/Order/'.$order_id.'"){
                        createdAt
                        name
                        customer{
                           email
                        }
                        currencyCode
                        totalShippingPriceSet{
                            presentmentMoney{
                                amount
                                currencyCode
                            }
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
                        currentSubtotalPriceSet{
                            presentmentMoney{
                                amount
                                currencyCode
                            }
                        }
                        totalTaxSet{
                            presentmentMoney{
                                amount
                            }
                        }
                        totalPriceSet{
                            presentmentMoney{
                                amount
                            }
                        }
                        totalDiscountsSet{
                            presentmentMoney{
                                amount
                            }
                        }
                        lineItems(first:50){
                            edges{
                                node{
                                    id
                                    quantity
                                    product{
                                    id
                                    featuredImage{
                                        url
                                    }
                                    }
                                    variant{
                                    id
                                    image{
                                        url
                                    }
                                    }
                                title
                                variantTitle
                                originalUnitPriceSet{
                                    presentmentMoney{
                                    amount
                                    }
                                }

                                }
                            }
                        }
                }
            }';
            $order_details = $shopifies->GraphQL->post($order_details_query,null,null,null);
            // echo '<pre>';
            // print_r($order_details);
            $order_details_data = $order_details['data']['order'];
            $billing_firstname = $order_details_data['billingAddress']['firstName'];
            $billing_lastname = $order_details_data['billingAddress']['lastName'];
            $billing_address1 = $order_details_data['billingAddress']['address1'];
            $customer_email = $order_details_data['customer']['email'];
            // echo $billing_address1;
            // die;
            $billing_city = $order_details_data['billingAddress']['city'];
            $billing_country = $order_details_data['billingAddress']['country'];
            $billing_province = $order_details_data['billingAddress']['province'];
            $billing_zip = $order_details_data['billingAddress']['zip'];
            $order_date = $order_details_data['createdAt'];
            $item_subtotal = $order_details_data['currentSubtotalPriceSet']['presentmentMoney']['amount'];
            $order_name = $order_details_data['name'];
            $currency_symbol = $all_shopify_currencies[$order_details_data['currentSubtotalPriceSet']['presentmentMoney']['currencyCode']];
            $shipping_price = $order_details_data['totalShippingPriceSet']['presentmentMoney']['amount'];
            $tax = $order_details_data['totalTaxSet']['presentmentMoney']['amount'];
            $order_number = preg_replace('/[^0-9]/', '', $order_name);
            $grand_total = $order_details_data['totalPriceSet']['presentmentMoney']['amount'];
            $total_discount = $order_details_data['totalDiscountsSet']['presentmentMoney']['amount'];
            $dateTime = date('Y-m-d H:i:s');
            $invoice_date =  date("d M Y", strtotime($dateTime));

           $invoice_template = '<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable">
            <tbody><tr>
                <td height="20"></td>
            </tr>
            <tr>
                <td>
                    <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff" style="">
                        <tbody>
                            <tr class="hiddenMobile">
                            <td height="40"></td>
                           </tr>
                        <tr class="visibleMobile">
                            <td height="30"></td>
                        </tr>


                        <tr>
                            <td>
                                <table width="550" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPaddingheader">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <table width="100%" border="0" cellpadding="0" cellspacing="0" align="left" class="col">
                                                    <tbody>';
                                                        if($show_logo == '1'){
                                                            $invoice_template .= '<tr>
                                                                <td align="left"> <img src="'.$SHOPIFY_DOMAIN_URL.'/application/assets/images/invoice/logo/'.$logo.'" width="170" height="50" alt="logo" border="0"></td>
                                                            </tr>
                                                            <tr class="hiddenMobile">
                                                                <td height="40"></td>
                                                            </tr>';
                                                       }
                                                       $invoice_template .= '<tr class="visibleMobile">
                                                            <td height="20"></td>
                                                        </tr>';
                                                        if($show_billing_information == '1'){
                                                            $invoice_template .= '<tr>
                                                                <td style="font-size: 11px; font-family: Poppins, sans-serif; color: #000; vertical-align: top; ">
                                                                    <strong><span class="editModeOff">'.$billing_information_text.'
                                                                    </strong>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="5"></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-size: 12px; font-family: Poppins, sans-serif; color: #5b5b5b; vertical-align: top; ">
                                                                       '.$billing_firstname.' '.$billing_lastname.'<br>'
                                                                         .$billing_address1.'<br>'
                                                                         .$billing_city.', '.$billing_province.'<br>'
                                                                         .$billing_zip.'<br>'
                                                                         .$billing_country.'<br>
                                                                </td>
                                                            </tr>';
                                                        }
                                                    $invoice_template .= '</tbody>
                                                </table>
                                                        </td>
                                                        <td>
                                                <table width="100%" border="0" cellpadding="0" cellspacing="0" align="right" class="col">
                                                    <tbody>
                                                        <tr class="visibleMobile">
                                                            <td height="20"></td>
                                                        </tr>
                                                        <tr>
                                                            <td height="5"></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-size: 12px; color: #5b5b5b; font-family: Poppins, sans-serif; vertical-align: top; text-align: right;">';
                                                                if($show_company_name == '1'){
                                                                    $invoice_template .= '<strong style="color: #000; ">'.$company_name.'</strong><br>';
                                                                }
                                                                if($show_email == '1'){
                                                                    $invoice_template .= '<strong style="color: #000;">'.$email_text.' :</strong>'.$email_value.'<br>';
                                                                }
                                                                if($show_phone_number == '1'){
                                                                    $invoice_template .= '<strong style="color: #000;">'.$phone_number_text.':</strong>'.$phone_number_value;
                                                                }
                                                            $invoice_template .= '</td>
                                                        </tr>
                                                        <tr class="hiddenMobile">
                                                            <td height="50"></td>
                                                        </tr>
                                                        <tr class="visibleMobile">
                                                            <td height="20"></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-size: 12px; color: #5b5b5b; font-family: Poppins, sans-serif; vertical-align: top; text-align: right;">';
                                                                if($show_invoice_number == '1'){
                                                                   $invoice_template .= '<small>'.$invoice_number_text.'</small>'.$invoice_number_prefix.''.$order_number.'<br>';
                                                                }
                                                                if($show_invoice_date == '1'){
                                                                    $invoice_template .= '<small>'.$invoice_date.'</small>';
                                                                }
                                                        $invoice_template .= '</td>
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
                </td>
            </tr>
        </tbody></table>
        <!-- /Header -->
        <!-- Order Details -->
        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable">
            <tbody>
                <tr>
                    <td>
                        <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff">
                            <tbody>
                                <tr>
                                </tr><tr class="hiddenMobile">
                                    <td height="30"></td>
                                </tr>
                                <tr class="visibleMobile">
                                    <td height="40"></td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="550" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                                            <tbody>
                                                <tr class="invoice-table-head" style="background:black;">
                                                    <th style="font-size: 12px; font-family: Poppins, sans-serif; color: #fff; font-weight: normal; line-height: 1; vertical-align: top; padding: 10px;" align="left">
                                                      '.$serial_number_text.'
                                                    </th>
                                                    <th style="font-size: 12px; font-family: Poppins, sans-serif; color: #fff; font-weight: normal; line-height: 1; vertical-align: top; padding: 10px;" align="left"  width="52%">
                                                        '.$item_text.'
                                                    </th>
                                                    <th style="font-size: 12px; font-family: Poppins, sans-serif; color: #fff; font-weight: normal; line-height: 1; vertical-align: top; padding: 10px 0 10px 0;" align="center">
                                                       '.$quantity_text.'
                                                    </th>
                                                    <th style="font-size: 12px; font-family: Poppins, sans-serif; color: #fff; font-weight: normal; line-height: 1; vertical-align: top; padding: 10px;" align="right">
                                                       '.$item_subtotal_text.'
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <td height="1" style="background: #bebebe;" colspan="4"></td>
                                                </tr>
                                                <tr>
                                                    <td height="10" colspan="4"></td>
                                                </tr>';
                                                foreach($order_details_data['lineItems']['edges'] as $key=>$value){
                                                    $variant_title = '';
                                                    if($value['node']['variantTitle']){
                                                       $variant_title = ' -'.$value['node']['variantTitle'];
                                                    }
                                                    $invoice_template .= '<tr>
                                                    <td style="font-size: 12px; font-family: Poppins, sans-serif; color: #000; line-height: 18px;  vertical-align: top; padding:10px;" class="article">
                                                       '.($key + 1).' </td>
                                                    <td style="font-size: 12px; font-family: Poppins, sans-serif; color: #000; font-weight: 600;  line-height: 18px;  vertical-align: top; padding:10px;" class="article">'.
                                                        $value['node']['title'].''.$variant_title.
                                                    '</td>
                                                    <td style="font-size: 12px; font-family: Poppins, sans-serif; color: #000; line-height: 18px;  vertical-align: top; padding:10px;" class="article">
                                                       '.$value['node']['quantity'].
                                                    '</td>
                                                    <td style="font-size: 12px; font-family: Poppins, sans-serif; color: #000; line-height: 18px;  vertical-align: top; padding:10px;" align="right" class="article">'.
                                                        $currency_symbol.''.$value['node']['originalUnitPriceSet']['presentmentMoney']['amount'].
                                                    '</td>
                                                    </tr>';
                                                 }
                                        $invoice_template .= '</tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20"></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- /Order Details -->
        <!-- Total -->
        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable">
            <tbody>
                <tr>
                    <td>
                        <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff">
                            <tbody>
                                <tr>
                                    <td>
                                        <!-- Table Total -->
                                        <table width="550" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                                            <tbody>';
                                                if($show_subtotal == '1'){
                                                    $invoice_template .= '<tr>
                                                        <td style="font-size: 12px; font-family: Poppins, sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                                        '.$subtotal_text.'
                                                        </td>
                                                        <td style="font-size: 12px; font-family: Poppins, sans-serif; color: #000; line-height: 22px; padding-right: 10px; vertical-align: top; text-align:right; white-space:nowrap;" width="80">'.
                                                        $currency_symbol.''.($item_subtotal + $total_discount).'
                                                        </td>
                                                    </tr>';
                                                }
                                                if($show_shipping == '1'){
                                                    $invoice_template .= '<tr>
                                                        <td style="font-size: 12px; font-family: Poppins, sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                                            '.$shipping_text.'
                                                        </td>
                                                        <td style="font-size: 12px; font-family: Poppins, sans-serif; padding-right: 10px; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                                            '.$currency_symbol.''.$shipping_price.'
                                                        </td>
                                                    </tr>';
                                                }
                                                if($show_tax == '1'){
                                                $invoice_template .= '<tr>
                                                    <td style="font-size: 12px;font-family: Poppins, sans-serif;color: #000000;line-height: 22px;vertical-align: top;text-align:right;">
                                                        <small>'.$tax_text.'</small></td>
                                                    <td style="font-size: 12px;font-family: Poppins, sans-serif;padding-right: 10px;color: #000000;line-height: 22px;vertical-align: top;text-align:right;">
                                                        <small>'.$currency_symbol.''.$tax.' </small>
                                                    </td>
                                                </tr>';
                                                }
                                                if($show_discount == '1'){
                                                $invoice_template .= '<tr>
                                                    <td style="font-size: 12px;font-family: Poppins, sans-serif;color: #000000;line-height: 22px;vertical-align: top;text-align:right;">
                                                        <small>'.$discount_text.'</small></td>
                                                    <td style="font-size: 12px;font-family: Poppins, sans-serif;padding-right: 10px;color: #000000;line-height: 22px;vertical-align: top;text-align:right;">
                                                        <small>'.$currency_symbol.''.$total_discount.'</small>
                                                    </td>
                                                </tr>';
                                               }
                                               $invoice_template .= '<tr>
                                                    <td style="font-size: 12px; font-family: Poppins, sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                                        <strong>'.$total_text.'</strong>
                                                    </td>
                                                    <td style="font-size: 12px; font-family: Poppins, sans-serif; padding-right: 10px; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                                        <strong>'.$currency_symbol.''.$grand_total.'</strong>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <!-- /Table Total -->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- /Total -->
        <!-- Information -->
        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable">
            <tbody>
                <tr>
                    <td>
                        <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff">
                            <tbody>
                                <tr>
                                </tr><tr class="hiddenMobile">
                                    <td height="30"></td>
                                </tr>
                                <tr class="visibleMobile">
                                    <td height="40"></td>
                                </tr>';
                                if($show_terms_conditions == '1'){
                                $invoice_template .= '<tr>
                                    <td>
                                        <table width="550" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <table width="270" border="0" cellpadding="0" cellspacing="0" align="left" class="col">

                                                            <tbody>
                                                                <tr>
                                                                    <td style="font-size: 11px; font-family: Poppins, sans-serif; color: #000; line-height: 1; vertical-align: top; ">
                                                                        <strong>'.$terms_conditions_text.'</strong>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="100%" height="10"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="font-size: 12px; font-family: Poppins, sans-serif; color: #5b5b5b; line-height: 20px; vertical-align: top; ">
                                                                    '.$terms_conditions_value.'
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>';
                                                    }
                                    if($show_signature == '1'){
                                                    $invoice_template .= '<td>
                                                        <table width="200" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <table width="270" border="0" cellpadding="0" cellspacing="0" align="right" class="col">
                                                                            <tbody>
                                                                                <tr class="hiddenMobile">
                                                                                    <td height="20"></td>
                                                                                </tr>
                                                                                <tr class="visibleMobile">
                                                                                    <td height="20"></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td style="font-size: 11px;font-family: Poppins, sans-serif;color: #000;line-height: 1;vertical-align: top;text-align: right;">
                                                                                        <img src="'.$SHOPIFY_DOMAIN_URL.'/application/assets/images/invoice/signature/'.$signature.'" width="170" height="50" alt="logo" border="0">
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
                                    </td>';
                                   }
                                $invoice_template .= '</tr>
                                <tr class="hiddenMobile">
                                    <td height="30"></td>
                                </tr>
                                <tr class="visibleMobile">
                                    <td height="30"></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>';
        echo $invoice_template;
        // die;

        function sendMail($sendMailArray, $testMode, $store_id, $db, $store){
            $email_configuration = 'false';
            $email_host = "your-email-host";
            $username = "apikey";
            $password = "email_password";
            $from_email = "your-from-email";
            $encryption = 'tls';
            $port_number = 587;
            //For pending mail
            if (array_key_exists("store_id", $sendMailArray)){
                $store_id = $sendMailArray['store_id'];
            }else{
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
                if($email_configuration_data) {
                    $query = "SELECT * FROM email_configuration WHERE store_id = '$store_id' ORDER BY id DESC";
                    $result = $db->query($query);
                    $email_configuration_data = $result->fetch(PDO::FETCH_ASSOC);
                }
                if($email_configuration_data){
                    if($email_configuration_data['email_enable'] == 'checked'){
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
            $mail->CharSet="UTF-8";
            $mail->Host = $email_host;
            $mail->SMTPDebug = 1;
            $mail->Port = $port_number ; //465 or 587
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
            if(is_array($sendTo)){
                $mail->AddAddress($sendTo[0]);
                $mail->AddAddress($sendTo[1]);
                $decrease_counter = 2;
            }else{
                $mail->AddAddress($sendTo);
                $decrease_counter = 1;
            }
            $mail->Subject = $subject;
            $mail->Body = $email_template_body;
            if($testMode == 'sendInvoice'){
                $mail->addAttachment($sendMailArray['mailAttachment']);
            }
            if(!$mail->Send()) {
                echo 'mail not sent';
            } else {
                echo 'mail sent successfully';
            }
        }


                $filename = 'Invoice-'.$order_id.'.pdf';
                $mpdfConfig = [
                    'tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf',
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'orientation' => 'P'
                ];
                $mpdf = new \Mpdf\Mpdf($mpdfConfig);
                $mpdf->SetMargins(10, 10, 10, 10);
                $mpdf->SetAutoPageBreak(true, 10);
                $mpdf->WriteHTML($invoice_template);
                $mpdf->Output($filename, 'F');

                $sendMailArray = array(
                    'sendTo' => $customer_email,
                    'subject' => 'Order Invoice',
                    'mailBody' => 'Please find the attachment below.',
                    'mailAttachment' => $filename,
                    'mailHeading' => '',
                    'ccc_email' => '',
                    'bcc_email' =>  '',
                    'reply_to' => ''
                );
                try{
                    $contract_deleted_mail = sendMail($sendMailArray,'sendInvoice', $store_id, $db, $store);
                    unlink($filename);
                    // remove 'sd_send_order_invoice' tags from the order
                    $new_order_tags = implode(",", array_diff($order_tags_array, array('sd_send_order_invoice')));
                    // echo '<pre>';
                    // print_r($new_order_tags);
                    // die;
                    //add tag to the order
                    try{
                        $addTagQuery = '   mutation orderUpdate($input: OrderInput!) {
                            orderUpdate(input: $input) {
                              order {
                                id
                              }
                              userErrors {
                                field
                                message
                              }
                            }
                        }';
                        $tagsParameters = [
                            "input" => [
                              "id" => "gid://shopify/Order/" . $order_id,
                              "tags" => [
                                $new_order_tags
                              ]
                            ]
                        ];

                        $tagQueryExecution = $shopifies->GraphQL->post($addTagQuery, null, null, $tagsParameters);
                        echo '<pre>';
                        print_r($tagQueryExecution);
                    }catch (Exception $e) {
                        echo '<pre>';
                        print_r($e);
                    }
                    $db = null;
                    echo $contract_deleted_mail;
                }catch(Exception $e) {
                    echo json_encode(array("status"=>false, "message"=>'Something went wrong'));
                }
       }
    }
}
}else{
    http_response_code(401);
}
?>