<?php
    // ini_set('display_startup_errors', 1);
    // ini_set('display_errors', 1);
    // error_reporting(-1);
    $dirPath = dirname(dirname(__DIR__));
    $current_date = date('Y-m-d');
    file_put_contents($dirPath . "/application/assets/txt/webhooks/upcoming_orders.txt", 'Upcoming order mail'.$current_date);

    use PHPShopify\ShopifySDK;
    require($dirPath."/PHPMailer/src/PHPMailer.php");
    require($dirPath."/PHPMailer/src/SMTP.php");
    require($dirPath."/PHPMailer/src/Exception.php");
    include $dirPath."/application/library/config.php";
    $upcoming_order_query = $db->query("SELECT * FROM upcoming_orders_template");
    $upcoming_order_data = $upcoming_order_query->fetchAll(PDO::FETCH_ASSOC);

    // echo "========================================<br>";
    $date=date_create($current_date);
    date_add($date,date_interval_create_from_date_string("6 days"));
    $next_date = date_format($date,"Y-m-d");

    $subscription_contract_data_query = $db->query("SELECT * FROM subscriptionOrderContract WHERE next_billing_date > '$current_date' AND next_billing_date < '$next_date' AND contract_status = 'A'");
    $check_row_count = $subscription_contract_data_query->rowCount();
    if($check_row_count > 0){
        $subscription_contract_data = $subscription_contract_data_query->fetchAll(PDO::FETCH_ASSOC);
        // echo "============= subscription order contract ends ==================<br>";

        foreach($subscription_contract_data as $key=>$value){
            $store_id = $value['store_id'];
            $next_billing_date = $value['next_billing_date'];
            $found_key = array_search($store_id, array_column($upcoming_order_data, 'store_id'));
            if($found_key !== false){
                $send_before_days = $upcoming_order_data[$found_key]['send_mails_before_days'];
                $upcoming_template_data = $upcoming_order_data[$found_key];
            }else{
               $send_before_days = 1;
               $upcoming_template_data = [];
            }

            $originalDate = new DateTime($next_billing_date);
            $newDate = $originalDate->sub(new DateInterval("P" . $send_before_days . "D"));

            $newDateString = $newDate->format("Y-m-d");
            $contractId = $value['contract_id'];
            if($current_date == $newDateString){
                send_upcoming_order_mail($upcoming_template_data,$store_id,$db,$SHOPIFY_DOMAIN_URL,$contractId,$value);
            }
        }
        // echo '<pre>';
        // print_r($newArray);
    }

    function send_upcoming_order_mail($template_data,$store_id,$db,$SHOPIFY_DOMAIN_URL,$contractId,$data){
        // echo '<br>';
        $email_notification_query = $db->query("SELECT admin_upcoming_orders,customer_upcoming_orders,store,shop_name,store_email,currency FROM email_notification_setting
        INNER JOIN install ON email_notification_setting.store_id = install.id
        INNER JOIN store_details ON store_details.store_id = install.id where install.id = $store_id");
        $email_notification_data = $email_notification_query->fetch(PDO::FETCH_ASSOC);
        $send_to_admin = $email_notification_data['admin_upcoming_orders'];
        $send_to_customer = $email_notification_data['customer_upcoming_orders'];
        $shop_name = $email_notification_data['shop_name'];
        $shop_email = $email_notification_data['store_email'];
        $shop_domain = $email_notification_data['store'];
        $currency = $email_notification_data['currency'];
        if($send_to_admin == '1' || $send_to_customer == '1'){
            if(empty($template_data)){
                echo 'template data empty';
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
                $manage_subscription_url = 'https://'.$shop_domain.'/account';
                $manage_button_text_color = '#ffffff';
                $manage_button_background = '#337ab7';
                $shipping_address_text = 'Shipping address';
                $shipping_address = '<p>{{shipping_full_name}}</p><p>{{shipping_address1}}</p><p>{{shipping_city}},{{shipping_province_code}} - {{shipping_zip}}</p>';
                $billing_address_text = 'Billing address';
                $billing_address = '<p>{{billing_full_name}}</p><p>{{billing_address1}}</p><p>{{billing_city}},{{billing_province_code}} - {{billing_zip}}</p>';
                $next_renewal_date_text = 'Next billing date';
                $payment_method_text = 'Payment method';
                $ending_in_text = 'Ending with ';
                $footer_text = '<p style="line-height:150%;font-size:14px;margin:0">Thank You</p>';
                $next_charge_date_text = 'Next billing date';
                $delivery_every_text = 'Delivery every';
                $order_number_text = 'Order No.';
                $email_subject = 'Upcoming Order';
                $show_currency = '0';
                $show_shipping_address = '1';
                $show_billing_address = '1';
                $show_line_items = '1';
                $show_payment_method = '1';
                $custom_template = '';
                $show_order_number = '1';
                $content_text = '<h2 style="font-weight:normal;font-size:24px;margin:0 0 10px">Hi {{customer_name}}</h2><h2 style="font-weight:normal;font-size:24px;margin:0 0 10px">We wanted to remind you about your upcoming subscription order scheduled for {{renewal_date}}</h2> <p style="line-height:150%;font-size:16px;margin:0">Please visit manage subscription portal to confirm.</p>';
             }else{
                echo 'template data not empty';
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
                    $manage_subscription_url = 'https://'.$email_notification_data['store'].'/account';
                }
                $manage_button_text_color = $template_data['manage_button_text_color'];
                $manage_button_background = $template_data['manage_button_background'];
                $shipping_address_text = $template_data['shipping_address_text'];
                $shipping_address = $template_data['shipping_address'];
                $billing_address = $template_data['billing_address'];
                $billing_address_text = $template_data['billing_address_text'];
                $next_renewal_date_text = $template_data['next_renewal_date_text'];
                $payment_method_text = $template_data['payment_method_text'];
                $ending_in_text = $template_data['ending_in_text'];
                $qty_text = $template_data['qty_text'];
                $footer_text = $template_data['footer_text'];
                $currency = '';
                if($template_data['show_currency'] == '1'){
                    $currency = $email_notification_data['currency'];
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
                $show_order_number = $template_data['show_order_number'];
        }

        $getContractData_qry = $db->query("SELECT p.payment_instrument_value,p.payment_method_token,o.after_cycle,o.contract_id,o.order_currency,o.after_cycle_update,o.min_cycle,o.discount_type,o.discount_value,o.recurring_discount_type,o.recurring_discount_value,o.next_billing_date,o.order_no,o.order_id,o.created_at,o.updated_at,o.contract_status,o.contract_products,o.delivery_policy_value,o.billing_policy_value,o.delivery_billing_type,a.first_name as shipping_first_name,a.last_name as shipping_last_name,a.address1 as shipping_address1,a.address2 as shipping_address2,a.city as shipping_city,a.province as shipping_province,a.country as shipping_country,a.company as shipping_company,a.phone as shipping_phone,a.province_code as shipping_province_code,a.country_code as shipping_country_code,a.zip as shipping_zip,a.delivery_method as shipping_delivery_method,a.delivery_price as shipping_delivery_price,b.first_name as billing_first_name,b.last_name as billing_last_name,b.address1 as billing_address1,b.address2 as billing_address2,b.city as billing_city,b.province as billing_province,b.country as billing_country,b.company as billing_company,b.phone as billing_phone,b.province_code as billing_province_code,b.country_code as billing_country_code,b.zip as billing_zip,d.store_email,d.shop_timezone,c.name,c.email,c.shopify_customer_id,cs.cancel_subscription,cs.edit_product_price,cs.skip_upcoming_order,cs.skip_upcoming_fulfillment,cs.pause_resume_subscription,cs.add_subscription_product,cs.add_out_of_stock_product,cs.edit_product_quantity,cs.edit_out_of_stock_product_quantity,cs.attempt_billing,cs.delete_product,cs.edit_shipping_address from subscriptionOrderContract as o
        INNER JOIN contract_setting as contract_settng ON o.store_id = contract_settng.store_id
        INNER JOIN subscriptionContractShippingAddress as a ON o.contract_id = a.contract_id
        INNER JOIN subscriptionContractBillingAddress as b ON o.contract_id = b.contract_id
        INNER JOIN customers as c ON c.shopify_customer_id = o.shopify_customer_id
        INNER JOIN store_details as d ON d.store_id = a.store_id
        INNER JOIN customerContractPaymentmethod AS p ON p.store_id = o.store_id
        INNER JOIN customer_settings as cs ON cs.store_id = o.store_id
        where o.contract_id = '$contractId'");
        $check_row_count = $getContractData_qry->rowCount();

        if($check_row_count > 0){
            $get_customer_data = $getContractData_qry->fetch(PDO::FETCH_ASSOC);
            $customer_name = $get_customer_data['name'];
            $customer_email = $get_customer_data['email'];
            $customer_id = $get_customer_data['shopify_customer_id'];
            $subscription_contract_id = '#'.$get_customer_data['contract_id'];
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

            $contract_product_query = $db->query("SELECT * FROM subscritionOrderContractProductDetails WHERE store_id = '$store_id' and contract_id = '$contractId'");
            $contract_product_details = $contract_product_query->fetchAll(PDO::FETCH_ASSOC);
			$currency_code = getCurrencySymbol($get_customer_data['order_currency']);
			$updated_at_date = $get_customer_data['updated_at'];
			$date_time_array = explode(' ', $updated_at_date);
	     	$next_billing_date = getShopTimezoneDate(($get_customer_data['next_billing_date'] . ' ' . $date_time_array[1]), $get_customer_data['shop_timezone']);
			if($data['after_cycle_update'] == '1' && $data['after_cycle'] != 0){
				$get_subscription_price_column = 'recurring_computed_price';
			}else{
				$get_subscription_price_column = 'subscription_price';
            }
			foreach($contract_product_details as $key=>$prdVal){
				$product_quantity = $prdVal['quantity'];
                $product_price = $prdVal[$get_subscription_price_column] * $product_quantity;

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
                                                        </td>';
                                                        if($show_order_number == '1'){
                                                           $default_email_template .= '<td class="m_-1845756208323497270order-number__cell" style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;text-transform:uppercase;font-size:14px;color:#999" align="right">
                                                           <table style="width:100%;text-align:right;">
                                                               <tbody>
                                                                  <tr> <td> <span style="font-size:13px,font-weight:600;color:'.$text_color.'" class="sd_order_number_text_view sd_text_color_view"><b>'.$order_number_text.'</b></span> </td> </tr>
                                                                  <tr> <td> <span class="sd_text_color_view" style="font-size:16px;color:'.$text_color.'"> '.$order_number.' </span> </td> </tr>
                                                               </tbody>
                                                           </table>
                                                           </td>';
                                                        }
                                                        $default_email_template .='</tr>
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
														$default_email_template .= '<td class="sd_show_shipping_address" style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;width:50%" valign="top">
															<h4 style="font-weight:500;font-size:16px;color:'.$heading_text_color.';margin:0 0 5px" class="sd_heading_text_color_view sd_shipping_address_text_view">'.$shipping_address_text.'</h4>
															<div class="sd_shipping_address_view sd_text_color_view" style="color:'.$text_color.';">'.$shipping_address.'</div>
                                                        </td>';
                                                    }
                                                    if($show_billing_address == '1'){
														$default_email_template .= '<td class="sd_show_billing_address" style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;width:50%" valign="top">
															<h4 style="font-weight:500;font-size:16px;color:'.$heading_text_color.';margin:0 0 5px" class="sd_heading_text_color_view sd_billing_address_text_view">'.$billing_address_text.'</h4>
															<div class="sd_billing_address_view sd_text_color_view" style="color:'.$text_color.';">'.$billing_address.'</div>
                                                        </td>';
                                                    }
													$default_email_template .= '</tr>
												</tbody>
                                            </table>';
                                            if($show_payment_method == '1'){
                                                $default_email_template .= '<div class="sd_show_payment_method">
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
                                                </div>';
                                            }
                                    $default_email_template .= '</td>
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

		if($custom_template != '' && $custom_template != '<br>'){
			$template_data = $custom_template;
		}else{
			$doc = new DOMDocument();
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
			array('{{subscription_contract_id}}','{{customer_email}}','{{customer_name}}','{{customer_id}}','{{shipping_full_name}}','{{shipping_address1}}','{{shipping_company}}','{{shipping_city}}','{{shipping_province}}','{{shipping_province_code}}','{{shipping_zip}}','{{billing_full_name}}','{{billing_address1}}','{{billing_city}}','{{billing_province}}','{{billing_province_code}}','{{billing_zip}}','{{product_quantity}}','{{subscription_line_items}}','{{last_four_digits}}','{{card_expire_month}}','{{card_expire_year}}','{{shop_name}}','{{shop_email}}','{{shop_domain}}','{{manage_subscription_url}}','{{email_subject}}','{{header_text_color}}','{{text_color}}','{{heading_text}}','{{logo_image}}','{{manage_subscription_button_color}}','{{manage_subscription_button_text}}','{{manage_subscription_button_text_color}}','{{shipping_address_text}}','{{billing_address_text}}','{{payment_method_text}}','{{ending_in_text}}','{{logo_height}}','{{logo_width}}','{{thanks_image}}','{{thanks_image_height}}','{{thanks_image_width}}','{{logo_alignment}}','{{thanks_image_alignment}}','{{card_brand}}','{{order_number}}','{{renewal_date}}'),
			array($subscription_contract_id,$customer_email,$customer_name,$customer_id,$shipping_full_name,$shipping_address1,$shipping_company,$shipping_city,$shipping_province,$shipping_province_code,$shipping_zip,$billing_full_name,$billing_address1,$billing_city,$billing_province,$billing_province_code,$billing_zip,$product_quantity,$subscription_line_items,$last_four_digits,$card_expire_month,$card_expire_year,$shop_name,$shop_email,$shop_domain,$manage_subscription_url,$email_subject,$heading_text_color,$text_color,$heading_text,$logo,$manage_button_background,$manage_subscription_txt,$manage_button_text_color,$shipping_address_text,$billing_address_text,$payment_method_text,$ending_in_text,$logo_height,$logo_width,$thanks_img,$thanks_img_height,$thanks_img_width,$logo_alignment,$thanks_img_alignment,$card_brand,$order_number,$next_billing_date),
			$template_data,
			$count
		);

        // if($shop_domain == 'mytab-shinedezign.myshopify.com' || $shop_domain == 'predictive-search.myshopify.com'){
            if($send_to_admin == '1' && $send_to_customer == '1'){
                $sendMailTo = array($customer_email, $shop_email);
            }else if($send_to_customer != '1' && $send_to_admin == '1'){
                $sendMailTo = $shop_email;
            }else if($send_to_customer == '1' && $send_to_admin != '1'){
                $sendMailTo = $customer_email;
            }
            $sendMailArray = array(
                'sendTo' =>  $sendMailTo,
                'subject' => $email_subject,
                'mailBody' => $result,
                'mailHeading' => '',
                'ccc_email' => $ccc_email,
                'bcc_email' =>  $bcc_email,
                'reply_to' => $reply_to
            );
            // echo '<pre>';
            // print_r($sendMailArray);
            // die;
            try{
                $contract_deleted_mail = sendMail($sendMailArray,$store_id,$db);
            }catch(Exception $e) {
            }
        // }
   }
}

    function getCurrencySymbol($currencyCode){
        $currency_list = array(
            "AFA" => "؋","ALL" => "Lek","DZD" => "دج","AOA" => "Kz","ARS" => "$","AMD" => "֏","AWG" => "ƒ","AUD" => "$","AZN" => "m","BSD" => "B$","BHD" => ".د.ب","BDT" => "৳","BBD" => "Bds$","BYR" => "Br","BEF" => "fr","BZD" => "$","BMD" => "$","BTN" => "Nu.","BTC" => "฿","BOB" => "Bs.","BAM" => "KM","BWP" => "P","BRL" => "R$","GBP" => "£","BND" => "B$","BGN" => "Лв.","BIF" => "FBu","KHR" => "KHR","CAD" => "$","CVE" => "$","KYD" => "$","XOF" => "CFA","XAF" => "FCFA","XPF" => "₣","CLP" => "$","CNY" => "¥","COP" => "$","KMF" => "CF","CDF" => "FC","CRC" => "₡","HRK" => "kn","CUC" => "$, CUC","CZK" => "Kč","DKK" => "Kr.","DJF" => "Fdj","DOP" => "$","XCD" => "$",
            "EGP" => "ج.م","ERN" => "Nfk","EEK" => "kr","ETB" => "Nkf","EUR" => "€","FKP" => "£","FJD" => "FJ$","GMD" => "D","GEL" => "ლ","DEM" => "DM","GHS" => "GH₵","GIP" => "£","GRD" => "₯, Δρχ, Δρ","GTQ" => "Q","GNF" => "FG","GYD" => "$","HTG" => "G","HNL" => "L","HKD" => "$","HUF" => "Ft","ISK" => "kr","INR" => "Rs.","IDR" => "Rp","IRR" => "﷼","IQD" => "د.ع","ILS" => "₪","ITL" => "L,£","JMD" => "J$","JPY" => "¥","JOD" => "ا.د","KZT" => "лв","KES" => "KSh","KWD" => "ك.د","KGS" => "лв","LAK" => "₭",
            "LVL" => "Ls","LBP" => "£","LSL" => "L","LRD" => "$","LYD" => "د.ل","LTL" => "Lt","MOP" => "$","MKD" => "ден", "MGA" => "Ar", "MWK" => "MK", "MYR" => "RM", "MVR" => "Rf", "MRO" => "MRU", "MUR" => "₨", "MXN" => "$", "MDL" => "L", "MNT" => "₮", "MAD" => "MAD", "MZM" => "MT", "MMK" => "K", "NAD" => "$", "NPR" => "₨", "ANG" => "ƒ", "TWD" => "$", "NZD" => "$", "NIO" => "C$", "NGN" => "₦", "KPW" => "₩", "NOK" => "kr", "OMR" => ".ع.ر", "PKR" => "₨", "PAB" => "B/.", "PGK" => "K", "PYG" => "₲", "PEN" => "S/.", "PHP" => "₱", "PLN" => "zł", "QAR" => "ق.ر", "RON" => "lei", "RUB" => "₽", "RWF" => "FRw", "SVC" => "₡", "WST" => "SAT", "SAR" => "﷼", "RSD" => "din", "SCR" => "SRe", "SLL" => "Le", "SGD" => "$", "SKK" => "Sk", "SBD" => "Si$", "SOS" => "Sh.so.", "ZAR" => "R", "KRW" => "₩", "XDR" => "SDR", "LKR" => "Rs", "SHP" => "£", "SDG" => ".س.ج", "SRD" => "$", "SZL" => "E", "SEK" => "kr", "CHF" => "CHf", "SYP" => "LS", "STD" => "Db", "TJS" => "SM", "TZS" => "TSh", "THB" => "฿", "TOP" => "$", "TTD" => "$", "TND" => "ت.د", "TRY" => "₺", "TMT" => "T", "UGX" => "USh", "UAH" => "₴", "AED" => "إ.د", "UYU" => "$", "USD" => "$", "UZS" => "лв", "VUV" => "VT", "VEF" => "Bs", "VND" => "₫", "YER" => "﷼", "ZMK" => "ZK"
        );
        return $currency_list[$currencyCode];
    }

    function getShopTimezoneDate($date,$shop_timezone){
		$dt = new DateTime($date);
		$tz = new DateTimeZone($shop_timezone); // or whatever zone you're after
		$dt->setTimezone($tz);
		$dateTime = $dt->format('Y-m-d H:i:s');
		$shopify_date =  date("d M Y", strtotime($dateTime));
		return $shopify_date;
	}

    function sendMail($sendMailArray,$store_id, $db){
        //general mail configuration
        $email_configuration = 'false';
        $email_host = "your-email-host";
        $username = "apikey";
        $password = "your-email-password";
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
?>