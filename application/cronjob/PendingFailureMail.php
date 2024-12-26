<?php
$dirPath =dirname(dirname(__DIR__));
include $dirPath . "/application/library/config.php";
require ($dirPath . "/PHPMailer/src/PHPMailer.php");
require ($dirPath . "/PHPMailer/src/SMTP.php");
require ($dirPath . "/PHPMailer/src/Exception.php");
$currentDate = date('Y-m-d');
$beforeOneDayDate = date('Y-m-d', strtotime('-1 days', strtotime($currentDate)));
$beforeSixDayDate = date('Y-m-d', strtotime('-7 days', strtotime($currentDate)));
$failurePendingBilling_query =  $db->query("SELECT b.id, b.contract_id,b.status, b.billing_attempt_date,s.store_id,s.shopify_customer_id,s.delivery_policy_value,s.billing_policy_value,s.delivery_billing_type,s.recurring_discount_type,s.recurring_discount_value,s.next_billing_date,s.after_cycle_update,s.after_cycle,i.store,d.store_email,d.shop_name,c.name,c.email,sa.first_name as shipping_first_name, sa.last_name as shipping_last_name, sa.address1 as shipping_address1, sa.city as shipping_city, sa.province as shipping_province, sa.province_code as shipping_province_code, sa.zip  as shipping_zip, sa.company as shipping_company,ba.first_name as billing_first_name, ba.last_name as billing_last_name, ba.address1 as billing_address1, ba.city as billing_city, ba.province as billing_province, ba.province_code as billing_province_code, ba.zip as billing_zip, ba.company as billing_company, pm.payment_instrument_value, e.customer_payment_pending,e.admin_payment_pending from billingAttempts as b INNER JOIN (SELECT MAX(id) as id FROM billingAttempts GROUP BY contract_id) as t2 ON b.id = t2.id INNER JOIN subscriptionOrderContract AS s ON s.contract_id = b.contract_id INNER JOIN email_notification_setting AS e INNER JOIN install AS i ON i.id = s.store_id INNER JOIN store_details as d ON d.store_id = s.store_id INNER JOIN customers AS c ON c.shopify_customer_id = s.shopify_customer_id INNER JOIN subscriptionContractShippingAddress AS sa on sa.contract_id = s.contract_id INNER JOIN subscriptionContractBillingAddress AS ba on ba.contract_id = s.contract_id INNER JOIN  customerContractPaymentmethod as pm on pm.shopify_customer_id = s.shopify_customer_id  WHERE e.store_id = s.store_id and b.status = 'Pending' and s.contract_status = 'P' and b.billing_attempt_date BETWEEN '$beforeSixDayDate' AND '$beforeOneDayDate' GROUP BY b.contract_id ORDER by b.id DESC");
$row_count = $failurePendingBilling_query->rowCount();
if($row_count > 0){
    $failurePendingBilling = $failurePendingBilling_query->fetchAll(PDO::FETCH_ASSOC);
    // echo '<pre>';
    // print_r($failurePendingBilling);
    // die;
    foreach($failurePendingBilling as $value){
        $send_mail_to = '';
        if($value['customer_payment_pending'] == '1' && $value['admin_payment_pending'] == '1'){
            $send_mail_to = array($value['email'],$value['store_email']);
        }else if($value['customer_payment_pending'] != '1' && $value['admin_payment_pending'] == '1'){
            $send_mail_to = $value['store_email'];
        }else if($value['customer_payment_pending'] == '1' && $value['admin_payment_pending'] != '1'){
            $send_mail_to = $value['email'];
        }
        if($send_mail_to != ''){
            $store = $value['store'];
            $store_id = $value['store_id'];
            $contract_id = $value['contract_id'];
            $card_expire_month = '';
            $payment_instrument_value = json_decode($value['payment_instrument_value']);
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

            $email_template_table_name = 'payment_pending_template';

            //get email template data
            $email_template_query = $db->query("SELECT * FROM $email_template_table_name WHERE store_id = '$store_id'");
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
                $logo = '<img border="0" style="display:'.($template_data['logo'] == '' ? 'none' : 'block').';color:#000000;text-decoration:none;font-family:Helvetica,arial,sans-serif;font-size:16px;float:'.$logo_alignment.'" width="'.$logo_width.'" src="'.$template_data['logo'].'" height="'.$logo_height.'" data-bit="iit">';
                $thanks_img_width = $template_data['thanks_img_width'];
                $thanks_img_height = $template_data['thanks_img_height'];
                $thanks_img_alignment = $template_data['thanks_img_alignment'];
                $thanks_img = '<img border="0" style="display:'.($template_data['thanks_img'] == '' ? 'none' : 'block').';color:#000000;text-decoration:none;font-family:Helvetica,arial,sans-serif;font-size:16px;float:'.$thanks_img_alignment.'" width="'.$thanks_img_width.'" src="'.$template_data['thanks_img'].'" height="'.$thanks_img_height.'" data-bit="iit">';
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
                $template_type = $template_data['template_type'];
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
                $email_subject = 'Subscription order payment pending!';
                $logo = '<img border="0" style="color:#000000;text-decoration:none;font-family:Helvetica,arial,sans-serif;font-size:16px;float:'.$logo_alignment.'" width="'.$logo_width.'" src="'.$SHOPIFY_DOMAIN_URL.'/application/assets/images/logo.png" height="'.$logo_height.'" data-bit="iit">';
                $thanks_img = '<img border="0" style="color:#000000;text-decoration:none;font-family:Helvetica,arial,sans-serif;font-size:16px;float:'.$thanks_img_alignment.'" width="'.$thanks_img_width.'" src="'.$SHOPIFY_DOMAIN_URL.'/application/assets/images/thank_you.jpg" height="'.$thanks_img_height.'" data-bit="iit">';
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
                $show_currency = '0';
                $show_shipping_address = '0';
                $show_billing_address = '0';
                $show_line_items = '0';
                $show_payment_method = '0';
                $custom_template = '';
                $show_order_number = '0';
                $content_heading = 'Subscription with id {{subscription_contract_id}} has been paused due to the pending confirmation of the previous order.You can change the status of the contract from the contract listing page';
                $content_text = '<h2 style="font-weight:normal;font-size:24px;margin:0 0 10px">Hi {{customer_name}}</h2><h2 style="font-weight:normal;font-size:24px;margin:0 0 10px">'.$content_heading.'</h2> <p style="line-height:150%;font-size:16px;margin:0">Please visit manage subscription portal to confirm.</p>';
            }
            if($value['after_cycle_update'] == '1' && $value['after_cycle'] != 0){
                $product_price_column = 'recurring_computed_price';
            }else{
                $product_price_column = 'subscription_price';
            }
            $product_price_column =
            $subscription_line_items = '';
            if($show_line_items == '1'){
                $subscription_line_items = '<table style="width:100%;border-spacing:0;border-collapse:collapse">
                <tbody>
                    <tr>
                        <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;">
                            <center>
                                <table style="width:560px;text-align:left;border-spacing:0;border-collapse:collapse;margin:0 auto">
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
                        $subscription_line_items .= '<tr style="border-bottom: 1px solid #f3f3f3;">
                        <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                            <img src="'.$prdVal['variant_image'].'" align="left" width="60" height="60" style="margin-right:15px;border-radius:8px;border:1px solid #e5e5e5" data-bit="iit">
                        </td>
                        <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;width:100%">
                            <span style="font-size:16px;font-weight:600;line-height:1.4;color:'.$heading_text_color.'">'.$prdVal['product_name'].' '.$prdVal['variant_name'].' x '.$prdVal['quantity'].'</span><br>
                            <span style="font-size:14px;color:'.$text_color.'"><span>'.$delivery_every_text.'</span> : '.$value['delivery_policy_value'].' '.$value['delivery_billing_type'].'</span><br>
                            <span style="font-size:14px;color:'.$text_color.'">'.$next_charge_date_text.' : '.date('d M Y', strtotime($value['next_billing_date'])).'</span><br>
                        </td>
                        <td style="padding:15px 0px;font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;white-space:nowrap">
                            <p style="color:'.$text_color.';line-height:150%;font-size:16px;font-weight:600;margin:0 0 0 15px" align="right">
                               '.$all_shopify_currencies[$order_currency].''.$prdVal[$product_price_column].' '.($show_currency == '1' ? $order_currency : '').'</span>
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
            $email_template = '';
            if($template_type == 'default'){
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
                        <table style="width:100%;border-spacing:0;border-collapse:collapse;margin:40px 0 20px">
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
                                <table style="width:560px;text-align:left;border-spacing:0;border-collapse:collapse;margin:0 auto">
                                    <tbody>
                                        <tr>
                                            <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                                                <table style="width:100%;border-spacing:0;border-collapse:collapse">
                                                    <tbody>
                                                        <tr>
                                                            <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                                                             <div style="color:'.$heading_text_color.'">
                                                               <h1 style="font-weight:normal;font-size:30px;margin:0">
                                                                '.$heading_text.'
                                                                </h1>
                                                             </div>
                                                            </td>';
                                                            if($show_order_number == '1'){
                                                              $email_template .= '<td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;text-transform:uppercase;font-size:14px;color:#999" align="right">
                                                                <table style="width:100%;text-align:right;">
                                                                    <tbody>
                                                                    <tr> <td> <span style="font-size:13px,font-weight:600;color:'.$text_color.'"><b>'.$order_number_text.'</b></span> </td> </tr>
                                                                    <tr> <td> <span style="font-size:16px;color:'.$text_color.'"> '.'#'.$order_no.' </span> </td> </tr>
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
                                <table style="width:560px;text-align:left;border-spacing:0;border-collapse:collapse;margin:0 auto">
                                    <tbody>
                                        <tr>
                                            <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                                                <div style="color:'.$text_color.';">
                                                    '.$content_text.'
                                                </div>
                                                <table style="width:100%;border-spacing:0;border-collapse:collapse;margin-top:20px">
                                                    <tbody>
                                                        <tr>
                                                            <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;line-height:0em">&nbsp;</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                                                                <table style="border-spacing:0;border-collapse:collapse;float:left;margin-right:15px">
                                                                    <tbody>
                                                                        <tr>
                                                                        <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;border-radius:4px" align="center" class="sd_manage_button_background_view"  bgcolor="'.$manage_button_background.'"><a href="{{manage_subscription_url}}" style="font-size:16px;text-decoration:none;display:block;color:'.$manage_button_text_color.';padding:20px 25px">'.$manage_subscription_txt.'</a></td>
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
                                <table style="width:560px;text-align:left;border-spacing:0;border-collapse:collapse;margin:0 auto">
                                    <tbody>
                                        <tr>
                                            <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                                                <table style="width:100%;border-spacing:0;border-collapse:collapse">
                                                    <tbody>
                                                        <tr>';
                                                        if($show_shipping_address == '1'){
                                                            $email_template .= '<td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;width:50%" valign="top">
                                                                <h4 style="font-weight:500;font-size:16px;color:'.$heading_text_color.';margin:0 0 5px">'.$shipping_address_text.'</h4>
                                                                <div style="color:'.$text_color.';">'.$shipping_address.'</div>
                                                            </td>';
                                                        }
                                                        if($show_billing_address == '1'){
                                                            $email_template .= '<td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;width:50%" valign="top">
                                                                <h4 style="font-weight:500;font-size:16px;color:'.$heading_text_color.';margin:0 0 5px">'.$billing_address_text.'</h4>
                                                                <div style="color:'.$text_color.';">'.$billing_address.'</div>
                                                            </td>';
                                                        }
                                                        $email_template .= '</tr>
                                                    </tbody>
                                                </table>';
                                                if($show_payment_method == '1'){
                                                $email_template .= '<div>
                                                <table style="width:100%;border-spacing:0;border-collapse:collapse">
                                                    <tbody>
                                                        <tr>
                                                            <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;width:50%" valign="top">
                                                                <h4 style="font-weight:500;font-size:16px;color:'.$heading_text_color.';margin:0 0 5px">'.$payment_method_text.'</h4>
                                                                <p style="color:'.$text_color.';line-height:150%;font-size:16px;margin:0">
                                                                    {{card_brand}}
                                                                    <span style="font-size:16px;color:'.$text_color.'">'.$ending_in_text.'{{last_four_digits}}</span><br>
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
                                <table style="width:560px;text-align:left;border-spacing:0;border-collapse:collapse;margin:0 auto">
                                    <tbody>
                                        <tr>
                                            <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                                            <div>'.$footer_text.'</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </center>
                        </td>
                    </tr>
                </tbody>
            </table>
            <img src="https://ci4.googleusercontent.com/proxy/C5WAwfRu-nhYYB726ZtDmBBZxH2ZQQgtpxwmJT5KONtMOVp6k7laRdD7JghQXsHLcYM4veQr436syfT22M4kVYeof9oM4TIq5I7li0_YUjrim2hpHv5dYG7V9z9OmFYRRwYK3KgYIf0ck0d_WTq1EjhX_DpBFoi4n20fTmcCfJxl76PIrL1HodOHxbkR8PrieSaJX9F3tcNZb-9L3JTm7_owWlAKVQ64kFMBmJHwK7I=s0-d-e1-ft#https://cdn.shopify.com/shopifycloud/shopify/assets/themes_support/notifications/spacer-1a26dfd5c56b21ac888f9f1610ef81191b571603cb207c6c0f564148473cab3c.png" height="1" style="min-width:600px;height:0" data-bit="iit">
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
        }else if($template_type == 'custom' && $custom_template != '' && $custom_template != '<br>'){
            $email_template = $custom_template;
        }
            if($email_template != '' && $template_type != 'none'){
                $count = -1;
                $result = str_replace(
                    array('{{subscription_contract_id}}','{{customer_email}}','{{customer_name}}','{{customer_id}}','{{next_order_date}}','{{shipping_full_name}}','{{shipping_address1}}','{{shipping_company}}','{{shipping_city}}','{{shipping_province}}','{{shipping_province_code}}','{{shipping_zip}}','{{billing_full_name}}','{{billing_address1}}','{{billing_city}}','{{billing_province}}','{{billing_province_code}}','{{billing_zip}}','{{subscription_line_items}}','{{last_four_digits}}','{{card_expire_month}}','{{card_expire_year}}','{{shop_name}}','{{shop_email}}','{{shop_domain}}','{{manage_subscription_url}}','{{delivery_cycle}}','{{billing_cycle}}','{{email_subject}}','{{header_text_color}}','{{text_color}}','{{heading_text}}','{{logo_image}}','{{manage_subscription_button_color}}','{{manage_subscription_button_text}}','{{manage_subscription_button_text_color}}','{{shipping_address_text}}','{{billing_address_text}}','{{payment_method_text}}','{{ending_in_text}}','{{logo_height}}','{{logo_width}}','{{thanks_image}}','{{thanks_image_height}}','{{thanks_image_width}}','{{logo_alignment}}','{{thanks_image_alignment}}','{{card_brand}}','{{order_number}}'),
                    array('#'.$contract_id,$value['email'],$value['name'],$value['shopify_customer_id'],$value['next_billing_date'],$value['shipping_first_name'].' '.$value['shipping_last_name'],$value['shipping_address1'],$value['shipping_company'],$value['shipping_city'],$value['shipping_province'],$value['shipping_province_code'],$value['shipping_zip'],$value['billing_first_name'].' '.$value['billing_last_name'],$value['billing_address1'],$value['billing_city'],$value['billing_province'],$value['billing_province_code'],$value['billing_zip'],$subscription_line_items,$last_four_digits,$card_expire_month,$card_expire_year,$value['shop_name'],$value['store_email'],$store,$manage_subscription_url,$value['delivery_policy_value'],$value['billing_policy_value'],$email_subject,$heading_text_color,$text_color,$heading_text,$logo,$manage_button_background,$manage_subscription_txt,$manage_button_text_color,$shipping_address_text,$billing_address_text,$payment_method_text,$ending_in_text,$logo_height,$logo_width,$thanks_img,$thanks_img_height,$thanks_img_width,$logo_alignment,$thanks_img_alignment,$card_brand,''),
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
        }
    }
}
$db = null;
function sendMail($sendMailArray, $testMode, $store_id, $db, $store){
    //general mail configuration
    $email_configuration = 'false';
    $email_host = "your-mail-host";
    $username = "apikey";
    $password = "email-password";
    $from_email = "your-From-email";
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
