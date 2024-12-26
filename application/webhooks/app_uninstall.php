<?php
$dirPath = dirname(dirname(__DIR__));
include $dirPath."/application/library/config.php";
require($dirPath."/PHPMailer/src/PHPMailer.php");
require($dirPath."/PHPMailer/src/SMTP.php");
require($dirPath."/PHPMailer/src/Exception.php");
include $dirPath."/graphLoad/autoload.php";
$store = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
$json_str = file_get_contents('php://input');
file_put_contents($dirPath . "/application/assets/txt/webhooks/app_uninstall.txt", $json_str, FILE_APPEND | LOCK_EX);
    $hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
    function verify_webhook($data, $hmac_header, $API_SECRET_KEY) {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $API_SECRET_KEY, true));
        return hash_equals($hmac_header, $calculated_hmac);
    }
    $verified = verify_webhook($json_str, $hmac_header, $API_SECRET_KEY);
    if ($verified) {
    $json_obj = json_decode($json_str,true);
    $store = $json_obj['myshopify_domain'];
    $store_detail_query = $db->query("SELECT id FROM install where  store = '$store'");
	$get_store_details = $store_detail_query->fetch(PDO::FETCH_ASSOC);
    $store_id = $get_store_details['id'];
   //send uninstallation mail to the store admin
    $store_email = $json_obj['email'];
    $mailBody = 'As you uninstall the app, the running subscriptions of your store will be paused and will be deleted after 48 hours. You can install the app within 48 hours if you want to continue the subscriptions';
    $sendMailArray = array(
        'sendTo' =>  $store_email,
        'subject' => 'Subscription Pause',
        'mailBody' => $mailBody,
        'mailHeading' => 'App Uninstall Mail'
    );
    sendMail($sendMailArray, 'false',$store_id,$db,$store);
   // delete data from install table and storeinstalloffer table
    try{
		$delete_install_entry = $db->query("DELETE FROM install where  store = '$store'");
	}catch(Exception $e) {
		file_put_contents($dirPath . "/application/assets/txt/webhooks/app_uninstall.txt", $e->getMessage(), FILE_APPEND | LOCK_EX);
	}
	try{
	  $delete_installoffer_entry = $db->query("DELETE FROM storeInstallOffers where  store_id = '$store_id'");
	}catch(Exception $e) {
	}

	// insert/update entry from uninstall table
	$check_entry_uninstalls = $db->prepare("SELECT id FROM uninstalls WHERE store = '$store'");
    $check_entry_uninstalls->execute();
    $uninstalls_entry_count = $check_entry_uninstalls->rowCount();
    if(!$uninstalls_entry_count) {
        $insert_uninstalls = $db->prepare("INSERT INTO uninstalls (store,store_id) VALUES ('$store','$store_id')");
        $insert_uninstalls->execute();
    }else{
       $update_uninstalls = $db->query("UPDATE uninstalls SET store_id = '$store_id' where store = '$store'");
	}
	$db = null;
}else{
	file_put_contents($dirPath . "/application/assets/txt/webhooks/app_uninstall.txt", 'Request was not from shopify', FILE_APPEND | LOCK_EX);
    http_response_code(401);
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
		$whereCondition = array(
			'store_id' => $store_id
		);
        $email_configuration_query =  $db->query("SELECT * FROM email_configuration WHERE store_id = '$store_id'");
        $email_configuration_data = $email_configuration_query->fetch(PDO::FETCH_ASSOC);
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
		$store_detail_query = $db->query("SELECT pending_emails, store_email,shop_name FROM email_counter, store_details WHERE email_counter.store_id = '$store_id' AND store_details.store_id = '$store_id'");
	$store_detail = $store_detail_query->fetch(PDO::FETCH_ASSOC);
	$email_template_body = email_templates($mailBody, $mailHeading, $store_id, $db, $store);
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
	$mail->addReplyTo($store_detail['store_email'], $store_detail['shop_name']);
	if(($email_configuration_data) && ($email_configuration_data['email_enable'] == 'checked')){
		$mail->SetFrom($username,$from_email);
	}else{
		$mail->SetFrom($from_email);
	}
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
	if(!$mail->Send()) {
		return json_encode(array("status"=>false, "message"=>$mail->ErrorInfo));
	} else {
		return json_encode(array("status"=>true, "message"=>'Email Sent Successfully'));
	}
}
function email_templates($email_body, $email_heading, $store_id, $db, $store){
	//get data from email settings table
    $email_setting_query = $db->query("SELECT * FROM email_settings WHERE store_id = '$store_id'");
    $email_Settings_data = $email_setting_query->fetch(PDO::FETCH_ASSOC);
	$image_folder = "https://your-domain.com/application/assets/images/";
	$logo_url = $image_folder.'logo.png';
	$footer_text = 'Thank You';
	$social_link_html = '';

	if(!empty($email_Settings_data)){
		if($email_Settings_data['footer_text'] != ''){
			$footer_text = $email_Settings_data['footer_text'];
		}
		if($email_Settings_data['logo_url'] != ''){
			$logo_url = $email_Settings_data[0]['logo_url'];
		}
			if($email_Settings_data['enable_social_link'] == '1'){
			if($email_Settings_data['facebook_link'] != ''){
				$social_link_html .= '<li class="fb_contents" style="list-style: none;margin: 0 10px;display:inline-block;"><a href="'.$email_Settings_data['facebook_link'].'" target="_blank" style="color:#3c5996;background: #f6f9ff;border: 1px solid #f2f2f2;border-radius: 50%;width: 35px;height: 35px;display: inline-block;text-align: center;line-height:40px;"><img width="15px" height="15px" style="margin-top:6px;" src="https://lh3.googleusercontent.com/-e2x3nBYmZfk/X7dtRSyslDI/AAAAAAAAB0M/KTW6KLFg6eEzbpKaZXcXAvhjiIJoOBJUQCK8BGAsYHg/s64/2020-11-19.png" class="uqvYjb KgFPz" alt="" aria-label="Picture. Press Enter to open it in a new page."></i></a></li>';
			}
			if($email_Settings_data['twitter_link'] != ''){
				$social_link_html .= '<li class="tw_contents" style="list-style: none;margin: 0 10px;display:inline-block;"><a href="'.$email_Settings_data[0]['twitter_link'].'" target="_blank" style="color:#58acec;background: #f6f9ff;border: 1px solid #f2f2f2;border-radius: 50%;width: 35px;height: 35px;display: inline-block;text-align: center;line-height:40px;"><img width="15px" height="15px" style="margin-top:6px;" src="https://lh3.googleusercontent.com/-hmg-zXw5RG0/X7dtSbfpWcI/AAAAAAAAB0Q/3twwSmDKpMsqDo8eSKAID8X8k4olFidsACK8BGAsYHg/s64/2020-11-19.png" class="uqvYjb KgFPz" alt="" aria-label="Picture. Press Enter to open it in a new page."></a></li>';
			}
			if($email_Settings_data['instagram_link'] != ''){
				$social_link_html .= '<li class="ins_contents" style="list-style: none;margin: 0 10px;display:inline-block;"><a href="'.$email_Settings_data['instagram_link'].'" target="_blank" style="color:#db4d45;background: #f6f9ff;border: 1px solid #f2f2f2;border-radius: 50%;width: 35px;height: 35px;display: inline-block;text-align: center;line-height:40px;"><img width="15px" height="15px" style="margin-top:6px;" src="https://lh3.googleusercontent.com/-DdrdoKZW5dA/X7dtTOJjmZI/AAAAAAAAB0U/jxIyk80qIG81JptOG_c9zHF7MgIrPpGrQCK8BGAsYHg/s64/2020-11-19.png" class="uqvYjb KgFPz" alt="" aria-label="Picture. Press Enter to open it in a new page."></i></a></li>';
			}
			if($email_Settings_data['linkedin_link'] != ''){
				$social_link_html .= '<li class="linkedin_contents" style="list-style: none;margin: 0 10px; display:inline-block;"><a href="'.$email_Settings_data['linkedin_link'].'" target="_blank" style="color:#0e7ab7;background: #f6f9ff;border: 1px solid #f2f2f2;border-radius: 50%;width: 35px;height: 35px;display: inline-block;text-align: center;line-height:40px;"><img width="15px"style="margin-top:6px;" height="15px" src="https://lh3.googleusercontent.com/-7Fkye-Jqt-c/X7dtT-C4GFI/AAAAAAAAB0Y/OEf5Fp97T6AO-v8sRbs7cpF-p5l_C_RAACK8BGAsYHg/s64/2020-11-19.png" class="uqvYjb KgFPz" alt="" aria-label="Picture. Press Enter to open it in a new page."></i></a></li>';
			}
		}
	}

	$email_template_body = '
	<center class="wrapper" data-link-color="#1188E6" data-body-style="font-size:14px; font-family:inherit; color:#000000; background-color:#f3f3f3;">
		<div class="webkit">
			<table cellpadding="0" cellspacing="0" border="0" width="100%" class="wrapper" bgcolor="#f3f3f3">
				<tbody>
					<tr>
						<td valign="top" bgcolor="#f3f3f3" width="100%" style="padding-top:50px;padding-bottom:50px;">
							<table width="100%" role="content-container" class="outer" align="center" cellpadding="0" cellspacing="0" border="0">
							<tbody><tr>
								<td width="100%">
									<table width="100%" cellpadding="0" cellspacing="0" border="0">
										<tbody><tr>
										<td>
											<!--[if mso]>
											<center>
												<table>
													<tr>
													<td width="600">
														<![endif]-->
														<table width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; max-width:600px;" align="center">
															<tbody><tr>
																<td role="modules-container" style="padding:0px 0px 0px 0px; color:#000000; text-align:left;" bgcolor="#FFFFFF" width="100%" align="left">
																<table class="module preheader preheader-hide" role="module" data-type="preheader" border="0" cellpadding="0" cellspacing="0" width="100%" style="display: none !important; mso-hide: all; visibility: hidden; opacity: 0; color: transparent; height: 0; width: 0;">
																	<tbody><tr>
																		<td role="module-content">
																			<p></p>
																		</td>
																	</tr>
																</tbody></table>
																<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" role="module" data-type="columns" style="padding:30px 0px 30px 0px;" bgcolor="#f2eefb" data-distribution="1">
																	<tbody>
																		<tr role="module-content">
																			<td height="100%" valign="top">
																			<table width="600" style="width:600px; border-spacing:0; border-collapse:collapse; margin:0px 0px 0px 0px;" cellpadding="0" cellspacing="0" align="left" border="0" bgcolor="" class="column column-0">
																				<tbody>
																					<tr>
																						<td style="padding:0px;margin:0px;border-spacing:0;">
																						<table class="wrapper" role="module" data-type="image" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;" data-muid="79178f70-3054-4e9f-9b29-edfe3988719e">
																							<tbody>
																								<tr>
																									<td style="font-size:6px; line-height:10px; padding:0px 0px 0px 0px;" valign="top" align="center">
																									<img class="max-width" border="0" style="display:block; color:#000000; text-decoration:none; font-family:Helvetica, arial, sans-serif; font-size:16px;height:63px;width:166px;" width="166" alt="" data-proportionally-constrained="true" data-responsive="false" src="'.$logo_url.'" height="63">
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
                                                                <table role="module" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
																	<tbody>
																		<tr>
																			<td style="padding:18px 50px 18px 50px;line-height:22px;text-align:inherit" height="100%" valign="top" role="module-content"><div><div style="font-family:inherit;text-align:center"><span style="font-size:16px;font-family:inherit">'.$email_body.'</span></div><div></div></div></td>
																		</tr>
																	</tbody>
															   </table>
																<div data-role="module-unsubscribe" class="module" role="module" data-type="unsubscribe" style="color:#444444; font-size:12px; line-height:20px; padding:16px 16px 16px 16px; text-align:Center;" data-muid="4e838cf3-9892-4a6d-94d6-170e474d21e5">
																	<p style="font-size:12px; line-height:20px;"><a href="https://'.$store.'" target="_blank" class="Unsubscribe--unsubscribePreferences" style="">'.$store.'</a></p>
																</div>
																<div data-role="module-unsubscribe" class="module" role="module" data-type="unsubscribe" style="color:#444444; font-size:12px; line-height:20px; padding:0px 16px 16px 16px; text-align:Center;" data-muid="4e838cf3-9892-4a6d-94d6-170e474d21e5">
																<p style="font-size:12px; line-height:20px;">'.$footer_text.'</p>
															    </div>
																<table border="0" cellpadding="0" cellspacing="0" class="module" data-role="module-button" data-type="button" role="module" style="table-layout:fixed;" width="100%" data-muid="de63a5a7-03eb-460a-97c7-d2535151ca0b">
																	<tbody>
																		<tr>
																			<td align="center" bgcolor="" class="outer-td" style="padding:0px 0px 20px 0px;">
																			<table border="0" cellpadding="0" cellspacing="0" class="wrapper-mobile" style="text-align:center;">
																				<tbody>
																					<tr>
																						<td align="center" bgcolor="#f5f8fd" class="inner-td" style="border-radius:6px; font-size:16px; text-align:center; background-color:inherit;">'.$social_link_html.'</td>
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
													</td>
													</tr>
												</table>
											</center>
										</td>
										</tr>
									</tbody></table>
								</td>
							</tr>
							</tbody></table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</center>';
 	return $email_template_body;
}