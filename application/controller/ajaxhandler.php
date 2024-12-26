
<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
include("Mainfunction.php");
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once('../../vendor/autoload.php');
if(isset($_REQUEST['store'])){
 $store = $_REQUEST['store'];
}
$mainobj = new MainFunction($store);
switch ($_REQUEST['action']) {
	case 'configure_step_form':
		$app_status  = $mainobj->checkAppStatus(); //check customizer app status
        $whereCondition = array(
           'store_id' => $mainobj->store_id
        );
		$check_selling_plan = $mainobj->table_row_check('subscriptionPlanGroupsDetails',$whereCondition,'and');
		// check shopify payment criteria
		$check_shopify_payment = $mainobj->checkShopifyPaymentRequirement();
		echo json_encode(array("status"=> true, "app_status"=>$app_status, 'selling_plan_status'=>$check_selling_plan, 'shopify_payment_status' => $check_shopify_payment));
	break;
	case 'product_app_extension_validate':
		$json_data = file_get_contents('php://input');
		$data_obj = json_decode($json_data,true);
		$secretKey  = $mainobj->API_SECRET_KEY;
		$data_token = [
			'token' => $data_obj['token']
		];
		$encode_token = JWT::encode(
			$data_token,
			$secretKey,
			'HS256'
		);
		$decoded_token = JWT::decode($encode_token, new Key($secretKey, 'HS256'));
		$get_token_data = JWT::decode($data_obj['token'], new Key($secretKey, 'HS256'));
		$store_name = str_replace("https://","",$get_token_data->dest);
		// echo $store_name;
		echo json_encode(array("status"=>true, 'decoded_token'=>($decoded_token->token), 'store_name' => $store_name));
	break;

    case 'product_app_extension_create':
		$json_data = file_get_contents('php://input');
		$data_obj = json_decode($json_data,true);
		$response = $mainobj->create_subscription($data_obj,'product_app_extension');
		echo $response;
	break;

	case 'product_app_extension_fetchExistingGroup':
	$whereCondition = array(
		'store_id' => $mainobj->store_id
	);
	$allSubscriptionGroups = $mainobj->table_row_value('subscriptionPlanGroups','All',$whereCondition,'and','');
	echo json_encode(array("status"=>true, 'subscription_plan_groups'=>$allSubscriptionGroups));
    break;

	case 'product_app_extension_addExistingGroup':
	$json_data = file_get_contents('php://input');
	$data_obj = json_decode($json_data,true);
	$response = $mainobj->addMultipleSubscriptionGroup($data_obj);
	echo $response;
	break;

	case 'product_app_extension_edit':
	$json_data = file_get_contents('php://input');
	$data_obj = json_decode($json_data,true);
	$response = $mainobj->edit_subscription($data_obj,'product_app_extension');
    echo  $response;
	break;

	case 'product_app_extension_fetchGroupDetail':
	$json_data = file_get_contents('php://input');
	$data_obj = json_decode($json_data,true);
	$selling_plan_group_id = str_replace("gid://shopify/SellingPlanGroup/","",$data_obj['sellingPlanGroupId']);
	try{
		$getSellingPlanGroupDetail = $mainobj->customQuery("SELECT g.plan_name, p.selling_plan_id as sellingplanid, p.plan_type as frequency_plan_type, p.plan_name as frequency_plan_name,p.plan_description as sd_description,p.delivery_policy as per_delivery_order_frequency_value,p.billing_policy as prepaid_billing_value, p.delivery_billing_type as per_delivery_order_frequency_type, p.discount_offer as subscription_discount,p.change_discount_after_cycle, p.recurring_discount_offer as subscription_discount_after,p.discount_type_after as subscription_discount_type_after,p.discount_value_after,p.min_cycle as minimum_number_cycle,p.max_cycle as maximum_number_cycle,p.discount_value as subscription_discount_value, p.discount_type as subscription_discount_type FROM subscriptionPlanGroups as g INNER JOIN subscriptionPlanGroupsDetails as p ON g.subscription_plangroup_id=p.subscription_plan_group_id where g.subscription_plangroup_id = '$selling_plan_group_id'");
		echo json_encode(array("status"=>true,'error'=>'', 'subscription_plan_group_detail'=>$getSellingPlanGroupDetail, 'shopCurrency'=>$mainobj->currency_code));
	}catch(Exception $e) {
		echo json_encode(array("status"=>false,'error'=>$e->getMessage(),'message'=>'subscription Plan Groups fetch error'));
	}
	break;

	case 'product_app_extension_deletePlan':
	$json_data = file_get_contents('php://input');
	$data_obj = json_decode($json_data,true);
	$response = $mainobj->delete_selling_plan($data_obj,'product_app_extension');
	echo $response;
	break;

	case 'update_delivery_billing_frequency':
		$response = $mainobj->update_delivery_billing_frequency($_REQUEST);
		echo $response;
	break;

	case 'delete_selling_plan':
	// print_r($_REQUEST);
	$response = $mainobj->delete_selling_plan($_REQUEST['data'],'app_backend');
	echo $response;
	break;

	case 'product_app_extension_remove':
	$json_data = file_get_contents('php://input');
	$data_obj = json_decode($json_data,true);
	$subscription_plan_id = $data_obj['sellingPlanGroupId'];
	$product_id = $data_obj['productId'];
		if(array_key_exists('variantId',$data_obj)){
		    $removeproducts = $data_obj['variantId'];
		}else{
			$removeproducts = implode('","', $data_obj['variantIds']);
		}
		$response = $mainobj->remove_subscription_productVariants('["'.$removeproducts.'"]',$subscription_plan_id);
		$get_group_data = $mainobj->getGroupProductVariant($subscription_plan_id,$product_id);
		//delete group or group products from db
	if($get_group_data['data']['sellingPlanGroup']['productCount'] > 0 || $get_group_data['data']['sellingPlanGroup']['productVariantCount'] > 0){
		try{
			if(array_key_exists('variantId',$data_obj)){
				$whereCondition = array(
					"store_id" => $mainobj->store_id,
					"variant_id" => str_replace("gid://shopify/ProductVariant/","",$data_obj['variantId']),
					"subscription_plan_group_id" => str_replace("gid://shopify/SellingPlanGroup/","",$subscription_plan_id)
				);
			}else{
				$whereCondition = array(
					"store_id" => $mainobj->store_id,
					"product_id" => str_replace("gid://shopify/Product/","",$product_id),
					"subscription_plan_group_id" => str_replace("gid://shopify/SellingPlanGroup/","",$subscription_plan_id)
				);
			}
			$mainobj->delete_row('subscriptionPlanGroupsProducts',$whereCondition,'and');
		}
		catch(Exception $e) {
			print_r($e);
		}
	}else{
		//delete group
		try{
			$whereCondition = array(
				"store_id" => $mainobj->store_id,
				"subscription_plangroup_id" => str_replace("gid://shopify/SellingPlanGroup/","",$subscription_plan_id)
			);
			$mainobj->delete_row('subscriptionPlanGroups',$whereCondition,'and');
		}
		catch(Exception $e) {
		}
	}
    echo ($response);
	break;

	case 'search_subscription_group':
		$response = $mainobj->getMoreGroups($_REQUEST,'search_group');
		echo $response;
	break;

	//get setting data
	case 'get_setting_data':
		$whereCondition = array(
			'store_id' => $mainobj->store_id,
		);
	if($_REQUEST['tablename'] == 'email_configuration'){
		$email_configuration_data = $mainobj->single_row_value('email_configuration','all',$whereCondition,'and','');
		// $email_settings = $mainobj->single_row_value('email_settings','all',$whereCondition,'and','');
		// $email_notification_setting = $mainobj->single_row_value('email_notification_setting','all',$whereCondition,'and','');
		echo json_encode(array('email_configuration'=>$email_configuration_data));
	}else{
		$response = $mainobj->single_row_value($_REQUEST['tablename'],'all',$whereCondition,'and','');
		echo json_encode($response);
    }
    break;
	// used when you need only to update when where condition met otherwise insert
	case "insert_update_ajax_single_row":
	 	$response = $mainobj->multiple_insert_update_row($_REQUEST['table'],$_REQUEST['fields_name'],$_REQUEST['fields_value']);
	    echo $response;
	break;

	case 'save_invoice_settings':
		$response = $mainobj->save_invoice_settings($_REQUEST);
		echo $response;
		die;
    break;

	case 'send_test_email':   //new update on 17 March 2023
		$response = $mainobj->send_test_email($_REQUEST);
		echo $response;
	break;

	case "insertupdateajax":
		$response = $mainobj->insertupdateajax($_REQUEST['table'],$_REQUEST['data_values'],$_REQUEST['wherecondition'],$_REQUEST['wheremode']);
		if($_REQUEST['update_renewal_date']){
			$get_fields = 'admin_subscription_renewal_date_update,customer_subscription_renewal_date_update';
			$reschedule_fulfillment = $mainobj->getSettingData('email_notification_setting',$get_fields);
			$sendMailTo = '';
			if($reschedule_fulfillment['admin_subscription_renewal_date_update'] == '1' && $reschedule_fulfillment['customer_subscription_renewal_date_update'] == '1'){
				$sendMailTo = array($_REQUEST['customerEmail'],$_REQUEST['adminEmail']);
			}else if($reschedule_fulfillment['admin_subscription_renewal_date_update'] != '1' && $reschedule_fulfillment['customer_subscription_renewal_date_update'] == '1'){
				$sendMailTo = $_REQUEST['customerEmail'];
			}else if($reschedule_fulfillment['admin_subscription_renewal_date_update'] == '1' && $reschedule_fulfillment['customer_subscription_renewal_date_update'] != '1'){
				$sendMailTo = $_REQUEST['adminEmail'];
			}
			if($sendMailTo != ''){
				$data = array(
					'template_type' => 'subscription_renewal_date_update_template',
					'contract_id' => $_REQUEST['wherecondition']['contract_id'],
					'contract_details' => $_REQUEST['specific_contract_data'][0],
					'contract_product_details' => $_REQUEST['contract_product_details'],
					'new_renewal_date' => $_REQUEST['new_renewal_date'],
				);
				$email_template_data = $mainobj->email_template($data,'send_dynamic_email');
				$sendMailArray = array(
					'sendTo' =>  $sendMailTo,
					'subject' => $email_template_data['email_subject'],
					'mailBody' => $email_template_data['test_email_content'],
					'mailHeading' => '',
					'ccc_email' => $email_template_data['ccc_email'],
					'bcc_email' =>  $email_template_data['bcc_email'],
					// 'from_email' =>  $from_email,
					'reply_to' => $email_template_data['reply_to']
				);
				try{
					$contract_deleted_mail = $mainobj->sendMail($sendMailArray,'false');
				}catch(Exception $e) {
				}
			}
		}
		echo ($response);
	break;
	//send test mail
	case "sendTestMail":
		$response = $mainobj->sendMail($_REQUEST,'true');
		echo ($response);
	break;

	//get products
	case "getAllProducts":
		$response = $mainobj->getAllProducts($_REQUEST['cursor'],$_REQUEST['query']);
		echo json_encode(array('allProductsArray'=>$response));
		break;

	case "getProductVariant":
		$response = $mainobj->getVariantData($_REQUEST['cursor'],$_REQUEST['product_id']);
		echo json_encode(array('allVariantsArray'=>$response));
	break;

	// for multiple rows & this is helpful when all columns need to insert/update are in the scene
	case "insert_update_ajax":
		$fields_name_text = $_REQUEST['table'].'_fields';
		$fields_value_text = $_REQUEST['table'].'_values';
		array_push($mainobj->$fields_value_text,$_REQUEST['fields_value']);
		$response = $mainobj->multiple_insert_update_row($_REQUEST['table'],$mainobj->$fields_name_text,$mainobj->$fields_value_text);
	    echo $response;
	break;

	// used when you need only to update
	case "updateajax":
		$response = $mainobj->update_row($_REQUEST['table'],$_REQUEST['insertdata'],$_REQUEST['wherecondition'],$_REQUEST['wheremode']);
        $responseArray = json_decode($response);
		if($response){
			echo json_encode(array("status"=>true, 'message' => 'Saved Successfully'));
		}else{
			echo json_encode(array("status"=>true, 'message' => 'Error'));
		}
	break;
	// used when you need only to update
	case "insertajax":
	    $response = $mainobj->insert_row($_REQUEST['table'],$_REQUEST['insertdata']);
	break;

	case "deleteajax":
		$response = $mainobj->delete_row($_REQUEST['table'],$_REQUEST['wherecondition'],$_REQUEST['wheremode']);
		echo $response;
	break;

	case "createsubscription":
		$response = $mainobj->create_subscription($_REQUEST['insertdata'],'backend_subscription');
		echo $response;
	break;

	case "editsubscription":
	echo $mainobj->edit_subscription($_REQUEST,'backend_subscription');
	break;

	case "deletesubscription":
		echo $mainobj->delete_subscription($_REQUEST['wherecondition']);
	break;

	//update product quantity in subscription contract
	case "updatePrdQtyPrice":
	$response = $mainobj->updateSubscriptionProductQty($_REQUEST);
	echo $response;
	break;

	// remove product from the contract
	case "removeContractProduct":
	$response = $mainobj->removeSubscriptionProduct($_REQUEST['contract_id'],$_REQUEST['line_id'],$_REQUEST['deleted_by'],$_REQUEST['customerEmail'],$_REQUEST['adminEmail'],$_REQUEST['specific_contract_data'],$_REQUEST['contract_product_details']);
	echo $response;
	break;

	case "updateShippingAddress":
	$shipping_address_array = $_REQUEST['shippingDataValues'];
	$response = $mainobj->updateShippingAddress($_REQUEST['contract_id'],$shipping_address_array,$_REQUEST['ajaxCallFrom'],$_REQUEST['specific_contract_data'],$_REQUEST['contract_product_details']);
	echo $response;
	break;

	case "updatePaymentMethod":
		$response = $mainobj->sendPaymentUpdateMail($_REQUEST['token'],$_REQUEST['email']);
		echo $response;
	break;

	case "newAddedProducts":
	if($_REQUEST['AjaxCallFrom'] == 'backendAjaxCall'){
		$added_by = 'Admin';
	}else if($_REQUEST['AjaxCallFrom'] == 'frontendAjaxCall'){
		$added_by = 'Customer';
	}
	$response = $mainobj->addNewContractProducts($_REQUEST['newProductsArray'],$_REQUEST,$added_by,$_REQUEST['specific_contract_data'],$_REQUEST['contract_product_details']);
	echo $response;
	break;

	case "updateSubscriptionStatus":
		// if($mainobj->shop == 'renbe-uk.myshopify.com'){
		//   echo '<pre>';
		//   print_r($_REQUEST);
		//   die;
		// }
	$response = $mainobj->updateSubscriptionStatusCommit($_REQUEST['contract_id'],$_REQUEST['statusChangeTo'],$_REQUEST['adminEmail'],$_REQUEST['customerEmail'],$_REQUEST['AjaxCallFrom'],$_REQUEST['next_billing_date'],$_REQUEST['delivery_billing_type'],$_REQUEST['billing_policy_value'],$_REQUEST['specific_contract_data'],$_REQUEST['contract_product_details']);
	echo $response;
	break;

	case "manualBillingAttempt":
	$response = $mainobj->manualBillingAttempt($_REQUEST);
	echo $response;
	break;

	case "getNextOrders":
	$response = $mainobj->getContractOrders($_REQUEST['contract_id'],$_REQUEST['cursorId'],'backend');
	if($response != ''){
	  echo json_encode($response);
	}
	break;

	case "rescheduleOrder":
	$response = $mainobj->rescheduleOrder($_REQUEST);
    echo $response;
	break;

	case "skipOrder":
	$response = $mainobj->skipContractOrder($_REQUEST['contract_id'],$_REQUEST['skipOrderDate'],$_REQUEST['billing_policy_value'],$_REQUEST['delivery_billing_type'],$_REQUEST['customerEmail'],$_REQUEST['adminEmail'],$_REQUEST['skippedFrom'],$_REQUEST['specific_contract_data'],$_REQUEST['contract_product_details']);
	echo $response;
	break;

	case "send_contactUS_mail":
        $mailBody = '<table style="background-color: #f2f3f8; max-width:670px;  margin:0 auto;" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tbody>
		<tr>
			<td>
			<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" role="module" data-type="columns" style="padding:30px 0px 30px 0px;" bgcolor="#fff" data-distribution="1">
					<tbody><tr>
						<td style="height:40px;">&nbsp;</td>
					</tr>
					<tr>
						<td style="padding:0 35px;">
							<h1 style="color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:"Rubik",sans-serif;">'.$_REQUEST['mailHeading'].'</h1>
							<p style="color:#455056; font-size:15px;line-height:24px; margin:0;">
								Shop Name :- '.$_REQUEST['sd_customerShop'].'
							<p style="color:#455056; font-size:15px;line-height:24px; margin:0;">
								Customer Email :-  '.$_REQUEST['sd_customerEmail'].'
							<p style="color:#455056; font-size:15px;line-height:24px; margin:0;">
								Customer Name :- '.$_REQUEST['sd_customerName'].'
							</p>
							</p>Customer Query :- '.$_REQUEST['sd_customerMsg'].'</p>
						</td>
					</tr>
					<tr>
						<td style="height:40px;">&nbsp;</td>
					</tr>
				</tbody></table>
			</td>
		</tr>
	</tbody></table>';

    	$sendMailArray = array(
		//  'sendTo' =>  'vikas.shinedezign@gmail.com',
		  "sendTo" => 'contact_us_mail',
		 'subject' => 'Subscription Contact Us Mail',
		 'mailBody' => $mailBody,
		 'mailHeading' => $_REQUEST['mailHeading'],
		);
		$sendContactUsMail = $mainobj->sendMail($sendMailArray,'false');
		if($sendContactUsMail){
			echo json_encode(array("status"=>true, 'message' => 'A mail has been sent to the support team.'));
		}else{
			echo json_encode(array("status"=>false, 'message' => 'Something went wrong in sending mail, Please try again later.'));
		}
	break;

	case "mini_subscription_planName_change":
	$response = $mainobj->mini_subscription_planName_change($_REQUEST);
	echo $response;
	break;

	case "planName_Duplicacy":
	$response = $mainobj->planName_Duplicacy($_REQUEST['planName']);
	echo $response;
	break;

	case "view_subscription_plan":
	$response = $mainobj->view_subscription_plan($_REQUEST['subscription_group_id']);
	echo $response;
	break;

	case "getCustomerSubscription":
	$customer_id = $_REQUEST['customer_id'];
	$getContractData = $mainobj->customQuery("SELECT o.id,o.store_id,o.order_id,o.contract_id,
	o.billing_policy_value,o.delivery_policy_value,
	o.delivery_billing_type,o.contract_status,
	o.order_token,o.order_no,o.created_at,o.updated_at,
	o.next_billing_date,
	c.name,c.email,c.shopify_customer_id,d.shop_timezone FROM `subscriptionOrderContract` as o, `customers` as c, `store_details` as d  WHERE o.store_id = '$mainobj->store_id' and o.shopify_customer_id =c.shopify_customer_id and o.shopify_customer_id = '$customer_id' and d.store_id = o.store_id ORDER BY o.id DESC");
	echo json_encode(array("customer_subscriptions"=>$getContractData));
	break;

   /************* frontend contract page  ********/
	case "getCustomerSubscriptionDetails":
		$response = $mainobj->getCustomerSubscriptionDetails($_REQUEST['contract_id'],$_REQUEST['store_id']);
		echo $response;
	break;

	case "checkCustomerSubscription":  // check if customer subscription exist if yes then get the page id from the asset table
		$whereCondition = array(
			'shopify_customer_id' => $_REQUEST['customer_id']
		);
		$accountPageHandle = '';
		$response = $mainobj->table_row_check('subscriptionOrderContract',$whereCondition,'');
     	if($response == true){
			// $whereStoreCondition = array(
			// 	'store_id' => $mainobj->store_id
			// );
			// $reschedule_fulfillment_data = $mainobj->table_row_value('theme_assets','all',$whereStoreCondition,'and','limit 0,1');
			// if(!empty($reschedule_fulfillment_data)){
			// 	$pageId = $reschedule_fulfillment_data[0]['asset_id'];
			// 	$accountPageHandle = $mainobj->getaccountPageHandle($pageId);
			// }
			echo json_encode(array("customer_exist"=>'yes', 'accountPageHandle' => $accountPageHandle));
		}else{
			echo json_encode(array("customer_exist"=>'no'));
		}
	break;

	case "checkCustomerContract":
		$whereCondition = array(
		  'store_id' => $mainobj->store_id,
		  'shopify_customer_id' => $_REQUEST['customer_id'],
		  'contract_id' => $_REQUEST['contract_id'],
		);
		$response = $mainobj->table_row_check('subscriptionOrderContract',$whereCondition,'and');
		if($response == true){
			echo json_encode(array("status"=>'true'));
		}else{
			echo json_encode(array("status"=>'false'));
		}
	break;

	/* --------- frontend product page -------------*/
	case "storeInstallInformation":
	$store = $_REQUEST['store'];
	$response = $mainobj->customQuery("SELECT id,app_status FROM install where store = '$store'");
	echo json_encode($response);
	break;

	case "productpagesetting":
	$response = $mainobj->productpagesetting($_REQUEST['store_id']); // return json
	echo $response;
	break;

	case "subscriptionPlanGroupsDetails":
    $response = $mainobj->subscriptionPlanGroupsDetails($_REQUEST); // return json
	echo $response;
	break;

	case "create_app_subscription":
	$response = $mainobj->create_app_subscription($_REQUEST);
	echo $response;
	break;

	case 'reset_email_template':
		$reset_template = json_decode($mainobj->delete_row($_REQUEST['table'],$_REQUEST['wherecondition'],$_REQUEST['wheremode']));
		if($reset_template->status == true){
			echo json_encode(array("status"=>true, 'message' => 'Saved Successfully'));
		}else{
			echo json_encode(array("status"=>true, 'message' => 'Something went wrong'));
		}
	break;

	case "get_shipping_counteries":
	$getShippingData = $mainobj->PostPutApi('https://'. $mainobj->store.'/admin/api/'.$mainobj->SHOPIFY_API_VERSION.'/shipping_zones.json','GET',$mainobj->access_token,'');
	$shippingCountryArray = [];
	if(!empty($getShippingData['shipping_zones'])){
		foreach($getShippingData['shipping_zones'] as $shippingData){
			if(!empty($shippingData['price_based_shipping_rates']) || !empty($shippingData['weight_based_shipping_rates'])){
				$all_country_array = array_merge($shippingCountryArray,array_column($shippingData['countries'],'name'));
				$shippingCountryArray = $all_country_array;
			}
		}
		if(!empty($shippingCountryArray)){
			echo json_encode(array("status"=>true, 'shipping_counteries' => $shippingCountryArray));
		}else{
			echo json_encode(array("status"=>false, 'message' => 'No Shipping Counteries Available'));
		}
	}else{
		echo json_encode(array("status"=>false, 'message' => 'No Shipping Counteries Available'));
	}
	break;
	case "getReplaceAllProducts":
		$response = $mainobj->getReplaceAllProducts($_REQUEST['newProductObj'],$_REQUEST['oldData'],$_REQUEST['all_contractIds'],$_REQUEST['contract_details']);
		echo json_encode(array('allProductsArray'=>$response));
	break;
}
