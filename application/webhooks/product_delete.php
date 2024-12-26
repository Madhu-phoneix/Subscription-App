<?php
use PHPShopify\ShopifySDK;
$dirPath = dirname(dirname(__DIR__));
include $dirPath."/application/library/config.php";
require($dirPath."/PHPMailer/src/PHPMailer.php");
require($dirPath."/PHPMailer/src/SMTP.php");
require($dirPath."/PHPMailer/src/Exception.php");
include $dirPath."/graphLoad/autoload.php";
$store = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
$json_str = file_get_contents('php://input');
file_put_contents($dirPath."/application/assets/txt/webhooks/product_delete.txt",$json_str);
$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
function verify_webhook($data, $hmac_header, $API_SECRET_KEY) {
	$calculated_hmac = base64_encode(hash_hmac('sha256', $data, $API_SECRET_KEY, true));
	return hash_equals($hmac_header, $calculated_hmac);
}
$verified = verify_webhook($json_str, $hmac_header, $API_SECRET_KEY);
if($verified){
http_response_code(200);
$json_obj = json_decode($json_str,true);
// $product_name = $json_obj['title'];
$product_id = $json_obj['id'];
    // get access_token and store_id from install
    $store_install_query = $db->query("SELECT id,access_token FROM install WHERE store = '$store'");
	$store_install_data = $store_install_query->fetch(PDO::FETCH_ASSOC);
	if(count($store_install_data) != 0){
		$access_token = $store_install_data['access_token'];
		$store_id = $store_install_data['id'];
    }
    // load graphql
	$config = array(
		'ShopUrl' => $store,
		'AccessToken' => $access_token
	);
	$shopifies = new ShopifySDK($config);
    $subscription_plan_group_products_query = $db->query("SELECT subscription_plan_group_id,variant_id FROM subscriptionPlanGroupsProducts where store_id='$store_id' and product_id = '$product_id'");
    $subscription_plan_group_products = $subscription_plan_group_products_query->fetchAll(PDO::FETCH_ASSOC);
    if(!empty($subscription_plan_group_products)){
        $prev_plan_group_id = $subscription_plan_group_products[0]['subscription_plan_group_id'];
        $i = 1;
        $removeproductvariantssarray = "[";
        foreach($subscription_plan_group_products as $key=>$subVal){
            if($subVal['subscription_plan_group_id'] != $prev_plan_group_id){
                $removeproductvariantssarray .= "]";
                try{
                    $graphQL_sellingPlanGroupRemoveProducts = 'mutation {
                        sellingPlanGroupRemoveProductVariants(
                        id: "gid://shopify/SellingPlanGroup/'.$subVal['subscription_plan_group_id'].'"
                        productVariantIds: '.$removeproductvariantssarray.'
                        ){
                            userErrors {
                            field
                            message
                            }
                        }
                    }';
                    // echo $graphQL_sellingPlanGroupRemoveProducts;
                    $sellingPlanGroupRemoveProductsapi_execution = $shopifies->GraphQL->post($graphQL_sellingPlanGroupRemoveProducts);
                }
                catch(Exception $e) {
                    // return json
                }

                $prev_plan_group_id = $subVal['subscription_plan_group_id'];
                $removeproductvariantssarray = "[";
                $i = 1;
            }
            if($i > 1){
                $removeproductvariantssarray .= ",";
            }
            $removeproductvariantssarray .= "gid://shopify/ProductVariant/".$subVal['variant_id'];
            $i++;
        }
        if($removeproductvariantssarray != '['){
            $removeproductvariantssarray .= "]";
            try{
                $graphQL_sellingPlanGroupRemoveProducts = 'mutation {
                    sellingPlanGroupRemoveProductVariants(
                    id: "gid://shopify/SellingPlanGroup/'.$subVal['subscription_plan_group_id'].'"
                    productVariantIds: '.$removeproductvariantssarray.'
                    ){
                        userErrors {
                        field
                        message
                        }
                    }
                }';
                $sellingPlanGroupRemoveProductsapi_execution = $shopifies->GraphQL->post($graphQL_sellingPlanGroupRemoveProducts);
            }
            catch(Exception $e) {
                // return json
            }
        }
        try{
            $db->query("DELETE FROM subscriptionPlanGroupsProducts where store_id = '$store_id' and product_id ='$product_id'");
        }catch(Exception $e) {
        }
    }

  //get general setting data form the db table
   $installStoreSettingData_query = $db->query("SELECT d.store_email,d.owner_name,g.remove_product,g.after_product_delete_contract FROM store_details as d, contract_setting as g WHERE d.store_id = '$store_id' and g.store_id = '$store_id'");
   $installStoreSettingData = $installStoreSettingData_query->fetch(PDO::FETCH_ASSOC);
   if($installStoreSettingData['after_product_delete_contract'] == 'Cancel'){
    $contract_status = 'Deleted';
  }else{
    $contract_status = $installStoreSettingData['after_product_delete_contract'];
  }
    $get_contract_product_variants_query = $db->query("SELECT contract_id,product_name,variant_id,product_id,contract_line_item_id,product_contract_status FROM subscritionOrderContractProductDetails where product_id = '$product_id' and product_contract_status = '1' and product_shopify_status='Active'");
    $get_contract_product_variants =  $get_contract_product_variants_query->fetchAll(PDO::FETCH_ASSOC);
if(!empty($get_contract_product_variants)){
    $no_image = $SHOPIFY_DOMAIN_URL."/application/assets/images/no-image.png";
    //get all contract ids where line item is not already removed from the contract and check that the contract contains single or multiple products
    $existingProductContracts = [];
    $deleted_contract_array = [];
    foreach($get_contract_product_variants as $val){
        $product_name = $val['product_name'];
        $contractId = $val['contract_id'];
        $lineId = $val['contract_line_item_id'];
        $query = $db->prepare("SELECT id FROM subscritionOrderContractProductDetails WHERE contract_id = '$contractId' and product_contract_status = '1' and product_shopify_status='Active'");
        $query->execute();
        $get_contract_products_count = $query->rowCount();
        if($installStoreSettingData['after_product_delete_contract'] == 'Delete'){
            $updateContractStatus = 'EXPIRED';
            $updatedbStatus = 'C';
        }else if(($get_contract_products_count == 1 && $installStoreSettingData['remove_product'] == 'Yes') || ($installStoreSettingData['after_product_delete_contract'] == 'Pause')){
            $updateContractStatus = 'PAUSED';
            $updatedbStatus = 'P';
        }else{
            $updateContractStatus = 'ACTIVE';
            $updatedbStatus = 'A';
        }
        if($installStoreSettingData['remove_product'] == 'No'){
            $remove_product = 'not remove';
        }else{
            $remove_product = 'removed';
        }
        //  query for send mail to the customer
        $selectCustomerIds_query = $db->query("SELECT  contract_id,email,name FROM subscriptionOrderContract INNER JOIN customers ON subscriptionOrderContract.shopify_customer_id = customers.shopify_customer_id WHERE subscriptionOrderContract.contract_id = '$contractId'");
        $selectCustomerIds = $selectCustomerIds_query->fetch(PDO::FETCH_ASSOC);
        $customerEmail =  $selectCustomerIds['email'];
        $customerName =  $selectCustomerIds['name'];
            if (!in_array($contractId, $deleted_contract_array)){
                try{
                    $getContractDraft = 'mutation {
                    subscriptionContractUpdate(
                        contractId: "gid://shopify/SubscriptionContract/'.$contractId.'"
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
                    if(!count($draftContract_execution_error)){
                        $subscription_draft_contract_id = $contractDraftArray['data']['subscriptionContractUpdate']['draft']['id'];
                    }
                }catch(Exception $e) {
                   return 'error';
                }

            try{
                $updateContractStatus_query = 'mutation {
                subscriptionDraftUpdate(
                    draftId: "'.$subscription_draft_contract_id.'"
                    input: { status : '.$updateContractStatus.' }
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
                $updateContractStatus_execution = $shopifies->GraphQL->post($updateContractStatus_query);
                $updateContractStatus_execution_error = $updateContractStatus_execution['data']['subscriptionDraftUpdate']['userErrors'];
                if(!count($updateContractStatus_execution_error)){
                    if($updatedbStatus == 'C'){
                        $updateSubscriptionContract = $db->query("DELETE FROM subscriptionOrderContract WHERE contract_id = '$contractId'");
                    }else{
                        $updateSubscriptionContract = $db->query("UPDATE subscriptionOrderContract SET contract_status = '$updatedbStatus' where contract_id = '$contractId'");
                    }
                    //send mail to the customer
                    if($installStoreSettingData['remove_product'] == 'Yes' ){
                        $customerMailTemplate = '<table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;" data-muid="2f94ef24-a0d9-4e6f-be94-d2d1257946b0" data-mc-module-version="2019-10-22">
                            <tbody>
                            <tr>
                                <td style="padding:18px 50px 18px 50px; line-height:22px; text-align:inherit;" height="100%" valign="top" role="module-content"><div><div style="font-family: inherit; text-align: center"><span style="font-size: 16px; font-family: inherit">Hello '.$customerName.'
                                Product "'.$product_name.'" is deleted from store "'.$store.'" , as a result of which your subscription(s) "'.$contractId.'" is "'.$updateContractStatus.'" now. Kindly contact store support or visit website for further queries.
                                </span></div><div></div></div></td>
                            </tr>
                            </tbody>
                        </table>';

                        $sendMailArray = array(
                        'sendTo' =>  $customerEmail,
                        'subject' => 'Product has been removed from subscription #'.$contractId,
                        'mailBody' => $customerMailTemplate,
                        'mailHeading' => 'Product has been removed from store'
                        );
                        sendMail($sendMailArray,'false',$store_id, $db, $store);
                    }
                }
            }catch(Exception $e){
            }

            try{
                $updateContractStatus_mutation = 'mutation {
                subscriptionDraftCommit(draftId: "'.$subscription_draft_contract_id.'") {
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
                $commitContractStatus_execution = $shopifies->GraphQL->post($updateContractStatus_mutation);
                $commitContractStatus_execution_error = $commitContractStatus_execution['data']['subscriptionDraftCommit']['userErrors'];
            }catch(Exception $e){
            }
            }
            //   remove the product from the contracts if 'yes' in contract settings
            if($installStoreSettingData['remove_product'] == 'Yes'){
                // $product_remove_response =  $mainobj->removeSubscriptionProduct($contractId,$lineId,'Admin','');
                try{
                    $getContractDraft = 'mutation {
                      subscriptionContractUpdate(
                        contractId: "gid://shopify/SubscriptionContract/'.$contractId.'"
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
                    if(!count($draftContract_execution_error)){
                        $contract_draftId = $contractDraftArray['data']['subscriptionContractUpdate']['draft']['id'];
                    }
                }catch(Exception $e) {
                  return 'error';
                }

                try{
                    $removeProduct = 'mutation {
                        subscriptionDraftLineRemove(
                        draftId: "'.$contract_draftId.'"
                        lineId: "gid://shopify/SubscriptionLine/'.$lineId.'"
                        ) {
                        lineRemoved {
                            id
                        }
                        draft {
                            id
                        }
                        userErrors {
                            field
                            message
                            code
                        }
                        }
                    }';
                    $removeProduct_execution = $shopifies->GraphQL->post($removeProduct);
                    $removeProduct_error = $removeProduct_execution['data']['subscriptionDraftLineRemove']['userErrors'];
                }catch(Exception $e) {
                    return json_encode(array("status"=>false,'error'=>$e->getMessage(),'message'=>'Product Removed error')); // return json
                }

                if(!count($removeProduct_error)){
                    try{
                        $updateContractStatus_mutation = 'mutation {
                        subscriptionDraftCommit(draftId: "'.$contract_draftId.'") {
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
                        $commitContractStatus_execution = $shopifies->GraphQL->post($updateContractStatus_mutation);
                        $commitContractStatus_execution_error = $commitContractStatus_execution['data']['subscriptionDraftCommit']['userErrors'];
                    }catch(Exception $e){
                    }
                    if(!count($commitContractStatus_execution_error)){
                    $db->query("UPDATE subscritionOrderContractProductDetails set product_contract_status = '0', variant_image = '$no_image'  WHERE contract_line_item_id = '$lineId'");
                }
            }
            }
            if($updatedbStatus != 'C'){
                $db->query("UPDATE subscritionOrderContractProductDetails set product_shopify_status = 'DELETED', variant_image = '$no_image' where product_id = '$product_id'");
            }
        if($val['product_contract_status'] == '1'){
            array_push($existingProductContracts,'#'.$contractId);
        }
        array_push($deleted_contract_array,$contractId);
    }

    if(!empty($existingProductContracts)){
        $allContractIds = implode(",",$existingProductContracts);
        $merchantMailTemplate = 'Hello '.$installStoreSettingData['owner_name'].'<br> Product "'.$product_name.'" is deleted from store "'.$store.'" , as a result of which your Subscription(s) "'.$allContractIds.'" is "'.$updateContractStatus.'"  now. Kindly contact store support or visit website for further queries.';

        $merchntMailTemplate = '<table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;" data-muid="2f94ef24-a0d9-4e6f-be94-d2d1257946b0" data-mc-module-version="2019-10-22">
                <tbody>
                <tr>
                    <td style="padding:18px 50px 18px 50px; line-height:22px; text-align:inherit;" height="100%" valign="top" role="module-content"><div><div style="font-family: inherit; text-align: center"><span style="font-size: 16px; font-family: inherit">'.$merchantMailTemplate.'</span></div><div></div></div></td>
                </tr>
                </tbody>
            </table>';

            $sendMailArray = array(
                'sendTo' =>  $installStoreSettingData['store_email'],
                'subject' => 'Product has been removed from subscription(s) '.$allContractIds,
                'mailBody' => $merchntMailTemplate,
                'mailHeading' => 'Product has been removed from the store'
            );
        sendMail($sendMailArray,'false',$store_id, $db, $store);
    }
}
$db = null;
}else{
    http_response_code(401);
}

function sendMail($sendMailArray, $testMode, $store_id, $db, $store)
{
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
    $store_detail_query = $db->query("SELECT pending_emails, store_email,shop_name FROM email_counter, store_details WHERE email_counter.store_id = '$store_id' AND store_details.store_id = '$store_id'");
	$store_detail = $store_detail_query->fetch(PDO::FETCH_ASSOC);
    $email_template_body = email_templates($mailBody, $mailHeading, $store_id, $db, $store);
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
    //Set Params
    $mail->addReplyTo($store_detail['store_email'], $store_detail['shop_name']);
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
    $mail->Body = $email_template_body;
    if (!$mail->Send())
    {
        return json_encode(array(
            "status" => false,
            "message" => $mail->ErrorInfo
        ));
    }
    else
    {
        return json_encode(array(
            "status" => true,
            "message" => 'Email Sent Successfully'
        ));
    }
}

function email_templates($email_body, $email_heading, $store_id, $db, $store)
{
    //get data from email settings table
    $email_setting_query = $db->query("Select * from email_settings WHERE store_id = '$store_id'");
    $email_Settings_data = $email_setting_query->fetch(PDO::FETCH_ASSOC);
    $image_folder = "https://your-domain.com/application/assets/images/";
    $logo_url = $image_folder . 'logo.png';
    $footer_text = 'Thank You';
    $social_link_html = '';

    if (!empty($email_Settings_data))
    {
        if ($email_Settings_data['footer_text'] != '')
        {
            $footer_text = $email_Settings_data['footer_text'];
        }
        if ($email_Settings_data['logo_url'] != '')
        {
            $logo_url = $email_Settings_data['logo_url'];
        }
        if ($email_Settings_data['enable_social_link'] == '1')
        {
            if ($email_Settings_data['facebook_link'] != '')
            {
                $social_link_html .= '<li class="fb_contents" style="list-style: none;margin: 0 10px;display:inline-block;"><a href="' . $email_Settings_data['facebook_link'] . '" target="_blank" style="color:#3c5996;background: #f6f9ff;border: 1px solid #f2f2f2;border-radius: 50%;width: 35px;height: 35px;display: inline-block;text-align: center;line-height:40px;"><img width="15px" height="15px" style="margin-top:6px;" src="https://lh3.googleusercontent.com/-e2x3nBYmZfk/X7dtRSyslDI/AAAAAAAAB0M/KTW6KLFg6eEzbpKaZXcXAvhjiIJoOBJUQCK8BGAsYHg/s64/2020-11-19.png" class="uqvYjb KgFPz" alt="" aria-label="Picture. Press Enter to open it in a new page."></i></a></li>';
            }
            if ($email_Settings_data['twitter_link'] != '')
            {
                $social_link_html .= '<li class="tw_contents" style="list-style: none;margin: 0 10px;display:inline-block;"><a href="' . $email_Settings_data['twitter_link'] . '" target="_blank" style="color:#58acec;background: #f6f9ff;border: 1px solid #f2f2f2;border-radius: 50%;width: 35px;height: 35px;display: inline-block;text-align: center;line-height:40px;"><img width="15px" height="15px" style="margin-top:6px;" src="https://lh3.googleusercontent.com/-hmg-zXw5RG0/X7dtSbfpWcI/AAAAAAAAB0Q/3twwSmDKpMsqDo8eSKAID8X8k4olFidsACK8BGAsYHg/s64/2020-11-19.png" class="uqvYjb KgFPz" alt="" aria-label="Picture. Press Enter to open it in a new page."></a></li>';
            }
            if ($email_Settings_data['instagram_link'] != '')
            {
                $social_link_html .= '<li class="ins_contents" style="list-style: none;margin: 0 10px;display:inline-block;"><a href="' . $email_Settings_data['instagram_link'] . '" target="_blank" style="color:#db4d45;background: #f6f9ff;border: 1px solid #f2f2f2;border-radius: 50%;width: 35px;height: 35px;display: inline-block;text-align: center;line-height:40px;"><img width="15px" height="15px" style="margin-top:6px;" src="https://lh3.googleusercontent.com/-DdrdoKZW5dA/X7dtTOJjmZI/AAAAAAAAB0U/jxIyk80qIG81JptOG_c9zHF7MgIrPpGrQCK8BGAsYHg/s64/2020-11-19.png" class="uqvYjb KgFPz" alt="" aria-label="Picture. Press Enter to open it in a new page."></i></a></li>';
            }
            if ($email_Settings_data['linkedin_link'] != '')
            {
                $social_link_html .= '<li class="linkedin_contents" style="list-style: none;margin: 0 10px; display:inline-block;"><a href="' . $email_Settings_data['linkedin_link'] . '" target="_blank" style="color:#0e7ab7;background: #f6f9ff;border: 1px solid #f2f2f2;border-radius: 50%;width: 35px;height: 35px;display: inline-block;text-align: center;line-height:40px;"><img width="15px"style="margin-top:6px;" height="15px" src="https://lh3.googleusercontent.com/-7Fkye-Jqt-c/X7dtT-C4GFI/AAAAAAAAB0Y/OEf5Fp97T6AO-v8sRbs7cpF-p5l_C_RAACK8BGAsYHg/s64/2020-11-19.png" class="uqvYjb KgFPz" alt="" aria-label="Picture. Press Enter to open it in a new page."></i></a></li>';
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
																									<img class="max-width" border="0" style="display:block; color:#000000; text-decoration:none; font-family:Helvetica, arial, sans-serif; font-size:16px;height:63px;width:166px;" width="166" alt="" data-proportionally-constrained="true" data-responsive="false" src="' . $logo_url . '" height="63">
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
																	' . $email_body . '
																<div data-role="module-unsubscribe" class="module" role="module" data-type="unsubscribe" style="color:#444444; font-size:12px; line-height:20px; padding:16px 16px 16px 16px; text-align:Center;" data-muid="4e838cf3-9892-4a6d-94d6-170e474d21e5">
																	<p style="font-size:12px; line-height:20px;"><a href="https://' . $store . '" target="_blank" class="Unsubscribe--unsubscribePreferences" style="">' . $store . '</a></p>
                                                                </div>
                                                                <div data-role="module-unsubscribe" class="module" role="module" data-type="unsubscribe" style="color:#444444; font-size:12px; line-height:20px; padding:16px 16px 16px 16px; text-align:Center;" data-muid="4e838cf3-9892-4a6d-94d6-170e474d21e5">
                                                                <p style="font-size:12px; line-height:20px;">' . $footer_text . '</p>
                                                                </div>
																<table border="0" cellpadding="0" cellspacing="0" class="module" data-role="module-button" data-type="button" role="module" style="table-layout:fixed;" width="100%" data-muid="de63a5a7-03eb-460a-97c7-d2535151ca0b">
																	<tbody>
																		<tr>
																			<td align="center" bgcolor="" class="outer-td" style="padding:0px 0px 20px 0px;">
																			<table border="0" cellpadding="0" cellspacing="0" class="wrapper-mobile" style="text-align:center;">
																				<tbody>
																					<tr>
																						<td align="center" bgcolor="#f5f8fd" class="inner-td" style="border-radius:6px; font-size:16px; text-align:center; background-color:inherit;">' . $social_link_html . '</td>
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
