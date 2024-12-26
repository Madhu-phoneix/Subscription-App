<?php
/* This file contains all basic db functions of insert , update , delete , duplicate & store install data*/
$dirPath =dirname(dirname(__DIR__));
require($dirPath."/PHPMailer/src/PHPMailer.php");
require($dirPath."/PHPMailer/src/SMTP.php");
require($dirPath."/PHPMailer/src/Exception.php");

require_once($dirPath."/application/library/keys.php");
use PHPShopify\ShopifySDK;
class CommonModal {

	Public $store , $access_token , $app_status, $store_id, $shopify_graphql_object,$currency,$currency_code,$shop_timezone,$shop_plan,$subscription_plans_view;
	public function __construct($store) {
		$this->MYSQL_HOST = "localhost";
		$this->MYSQL_DB = "DATABASE_NAME";
		$this->MYSQL_USER = "MYSQL_USER";
		$this->MYSQL_PASS = "MYSQL_PASSWORD";
		$this->SHOPIFY_APIKEY = "shopify_api_key";
		$this->SHOPIFY_SECRET = "shopify_secret_key";
		$this->SHOPIFY_API_VERSION = "2024-04";
		$this->app_extension_id = "app_extension_id";
		$this->theme_block_name = 'subscription_block';
        $this->app_name = 'your_app_name';
		$this->created_at = date('Y-m-d H:i:s');
		$this->image_folder = "https://your-domain.com/application/assets/images/";
		$this->SHOPIFY_DOMAIN_URL = "https://your-domain.com";
		$this->db = $this->connection();
		$this->storeInstallDetails($store,'set');
		$this->init_GraphQL_Object();
	}

	public function storeInstallDetails($store,$type){
		$whereCondition = array(
			"store" => $store
		);
		$fields = "all";

		$store_install_data = $this->customQuery("Select access_token,store,store_id,shop_timezone,currency,currencyCode,shop_plan,subscription_plans_view FROM install LEFT JOIN store_details ON  install.id = store_details.store_id WHERE install.store = '$store'");
		if(count($store_install_data) != 0){
			if($type == 'set'){
				$this->access_token = $store_install_data[0]['access_token'];
				$this->store  = $store_install_data[0]['store'];
				$this->store_id = $store_install_data[0]['store_id'];
				$this->currency = $store_install_data[0]['currency'];
				$this->currency_code = $store_install_data[0]['currencyCode'];
				$this->shop_timezone = $store_install_data[0]['shop_timezone'];
				$this->shop_plan = $store_install_data[0]['shop_plan'];
				$this->subscription_plans_view = $store_install_data[0]['subscription_plans_view'];
			}else if($type == 'return'){
				return json_encode($store_install_data);
			}
		}
	}

	public function init_GraphQL_Object(){
		$config = array(
			'ShopUrl' => $this->store,
			'AccessToken' => $this->access_token,
		);
		$this->shopify_graphql_object = new ShopifySDK($config);
	}

	/* To Check If Rows Exist Start */
	public function table_row_check($tableName,$whereCondition,$whereMode){
		$where = "";
		if(is_array($whereCondition)){
			$keys = array_keys( $whereCondition );
			$size = sizeof($whereCondition);
			for($x = 0; $x < $size; $x++ )
			{
				if($x > 0){
					$where .= $whereMode;
				}
				$where .= " LOWER(".$keys[$x].") = LOWER('".$whereCondition[$keys[$x]]."')";

			}
		}else if(is_string($whereCondition)){
			$where = $whereCondition;
		}

		  $query = $this->db->prepare("SELECT * FROM $tableName WHERE $where");
		  $query->execute();
		  $row_count = $query->rowCount();
		  return $row_count; // return integer
	}
	/* To Check If Rows Exist End */

	public function single_row_value($tableName,$fields,$whereCondition,$whereMode,$limit){
		$where = "";
		$keys = array_keys($whereCondition );
		$size = sizeof($whereCondition);
		for($x = 0; $x < $size; $x++ )
		{
			if($x > 0){
				$where .= $whereMode;
			}
			$where .= " ".$keys[$x]." = '".$whereCondition[$keys[$x]]."' ";
		}
		if(is_array($fields))
		{
			$field=implode(",",$fields);
		}
		if(is_string($fields))
		{
			$field = "*";
		}
		$check_entry  = $this->table_row_check($tableName,$whereCondition,$whereMode);
		if($check_entry){
	        $query = "SELECT $field FROM $tableName WHERE $where ORDER BY id DESC $limit";
			$result = $this->db->query($query);
			$row_data = $result->fetch(PDO::FETCH_ASSOC);
			return $row_data;	// return associative array
		}else{
			return $check_entry;
		}
	}

	/* To Get Fields Values From Table Start */
	public function table_row_value($tableName,$fields,$whereCondition,$whereMode,$limit){
		$where = "";
		$keys = array_keys($whereCondition );
		$size = sizeof($whereCondition);
		for($x = 0; $x < $size; $x++ )
		{
			if($x > 0){
				$where .= $whereMode;
			}
			$where .= " ".$keys[$x]." = '".$whereCondition[$keys[$x]]."' ";

		}
		if(is_array($fields))
		{
			$field=implode(",",$fields);
		}
		if(is_string($fields))
		{
			$field = "*";
		}
		$check_entry  = $this->table_row_check($tableName,$whereCondition,$whereMode);
		if($check_entry){
	        $query = "SELECT $field FROM $tableName WHERE $where ORDER BY id DESC $limit";
			$result = $this->db->query($query);
			$row_data = $result->fetchAll(PDO::FETCH_ASSOC);
			return $row_data;	// return associative array
		}else{
			return $check_entry;
		}
	}
	/* To Get Fields Values From Table End */

	public function customQuery($query){
		$result = $this->db->query($query);
		$row_data = $result->fetchAll(PDO::FETCH_ASSOC);
		return $row_data;	// return associative array
	}

	public function connection(){
		try {
			$result= new PDO("mysql:host=$this->MYSQL_HOST;dbname=$this->MYSQL_DB", $this->MYSQL_USER, $this->MYSQL_PASS);
		} catch (PDOException $pe) {
			die("Could not connect to the database $MYSQL_DB :" . $pe->getMessage());
		}
		return $result;
	}

	public function insert_row($tableName,$fields){

		$where = ""; $status=''; $message = ''; $last_inserted_id = '';
		$values = array();
		foreach ($fields as $k => $val) {
			array_push($values, $val);
		}
		$columns = implode(", ", array_keys($fields));
		// $values  = "'" . implode("','", $values) . "'";
		$values  = '"' . implode('","', $values) . '"';
		$sql_query_insert = "INSERT INTO $tableName ($columns) VALUES ($values)";
		$ins = $this->db->exec($sql_query_insert);
		if ($ins) {
			$status=true;
			$last_inserted_id = $this->db->lastInsertId();
			$message="Saved Successfully";
		} else {
			$status=false;
			$last_inserted_id = '';
			$message="Error: " . $this->db->rollback();
		}
		return json_encode(array("status"=>$status,'message'=>$message, 'id'=>$last_inserted_id)); // return json
	}

	// for single and multiple rows : insert and update
	public function multiple_insert_update_row($tableName,$fields,$fields_values){
	    $where = ""; $status=''; $message = '';
		$values = "";
		$result = array();
		foreach ($fields_values as $row) {
			$resultRow = array();
			for ($i = 0; $i < count($fields); $i++) {
				$resultRow[$fields[$i]] = $row[$i];
			}
			$result[] = $resultRow;
		}
		$update_string_value = '';
		$modifiedColumns = array();
		foreach($fields as $key=>$value){
			if($key != 0){
				$update_string_value .=  ', '.$value.' = VALUES('.$value.')';
			}else{
				$update_string_value .=  $value.' = VALUES('.$value.')';
			}
			$modifiedColumns[] = ":" . $value;
		}
		$columns = implode(", ", $fields);

		// Implode the modified column names back into a string
		$modifiedColumnsString = implode(", ", $modifiedColumns);
		// code for inderting all special character in the database
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt = $this->db->prepare("INSERT INTO $tableName ($columns) VALUES ($modifiedColumnsString) ON DUPLICATE KEY UPDATE $update_string_value");
		foreach ($result as $record) {
			if ($stmt->execute($record)) {
				$upd = true;
			} else {
				$upd = false;
			}
		}
		if ($upd) {
			$status=true;
			$message="Updated Successfully";
			$last_inserted_id = $this->db->lastInsertId();
		} else {
			$status=false;
			$message="Error: " . $this->db->rollback();
		}
		// return json_encode(array("status"=>$status,'message'=>$message, 'id' => $last_inserted_id)); // return json
	}


	public function multiple_insert_row($tableName,$fields,$fields_values){
		$where = ""; $status=''; $message = ''; $last_inserted_id = '';
		$values = "";
		foreach ($fields_values as $k => $val) {
			if($k != 0){
				$values .= "," ;
			}
			$values .= "(" ;
			$values .= "'".implode("','", $val)."'";
			$values .= ")" ;
		}
		$columns = implode(", ", $fields);

     	$sql_query_insert = "INSERT INTO $tableName ($columns) VALUES $values";
		$ins = $this->db->exec($sql_query_insert);
		if ($ins) {
			$status=true;
			$last_inserted_id = $this->db->lastInsertId();
			$message="Saved Successfully";
		} else {
			$status=false;
			$last_inserted_id = '';
			$message="Error: " . $this->db->rollback();
		}
		return json_encode(array("status"=>$status,'message'=>$message, 'id'=>$last_inserted_id)); // return json
	}

	public function insertupdateajax($tableName,$fields,$whereCondition,$whereMode){
	   $check_entry = $this->table_row_check($tableName,$whereCondition,$whereMode);
	   if($check_entry){
		  return $this->update_row($tableName,$fields,$whereCondition,$whereMode);
	   }else{
		  return $this->insert_row($tableName,$fields);
	   }
	}

	public function update_row($tableName,$fields,$whereCondition,$whereMode){
		$where = ""; $status=''; $message = '';
		if(is_array($whereCondition)){
		$keys = array_keys($whereCondition );
		$size = sizeof($whereCondition);
			for($x = 0; $x < $size; $x++ )
			{
				if($x > 0){
					$where .= $whereMode;
				}
				$where .= " ".$keys[$x]." = '".$whereCondition[$keys[$x]]."' ";

			}
		}

		$valueSets = array();

		foreach($fields as $key => $value) {
			$valueSets[] = $key . "= '" . $value . "'";
		}
		$sql_update = "UPDATE $tableName SET ".join(",",$valueSets). " WHERE $where";
		$upd =  $this->db->query($sql_update);
		if ($upd) {
			$status=true;
			$message= "Updated Successfully";
		} else {
			$status=false;
			$message="Error: " . $this->db->rollback();
		}
		return json_encode(array("status"=>$status,'message'=>$message)); // return json
	}

	/* To delete row start*/
	public function delete_row($tableName,$whereCondition,$whereMode){
		$where = "";
		$keys = array_keys( $whereCondition );
		$size = sizeof($whereCondition);
		if($whereMode == 'IN'){
			$where = $keys[0]." IN ( ".$whereCondition[$keys[0]]." ) ";
		}else{
			for($x = 0; $x < $size; $x++ )
			{
				if($x > 0){
					$where .= $whereMode;
				}
				$where .= " ".$keys[$x]." = '".$whereCondition[$keys[$x]]."' ";

			}
		}
		try{
		$query = "Delete FROM $tableName WHERE $where";
		  $result =  $this->db->exec($query);
		  if ($result) {
				$status=true;
				$message="Delete Successfully";
			} else {
				$status=false;
				$message="Error: " . $this->db->rollback();
			}
		  return json_encode(array("status"=>$status,'message'=>$message)); // return json
		}catch(Exception $e) {
			return json_encode(array("status"=>true,'message'=>'No data found')); // return json
	    }
	}
	/* To delete row end */

	public function duplicate_row($tableName,$fields,$whereCondition,$whereMode,$copytocolumn){
		$columns = implode(",",$fields);
		$where = "";
		$keys = array_keys( $whereCondition );
		$size = sizeof($whereCondition);
		for($x = 0; $x < $size; $x++ )
		{
			if($x > 0){
				$where .= $whereMode;
			}
			$where .= " ".$keys[$x]." = '".$whereCondition[$keys[$x]]."' ";

		}
		  $query  = "INSERT INTO $tableName ( $columns ) SELECT $columns from  $tableName where $where";
		  $result =  $this->db->exec($query);
		  $id     =  $this->db->lastInsertId();
		  $copyquery = "UPDATE $tableName  SET plan_name=CONCAT('Copy Of ',plan_name) WHERE id= $id";
		  $copycolumn_result =  $this->db->exec($copyquery);
			if ($copycolumn_result) {
				$status=true;
				$message="Duplicate Successfully";
			} else {
				$status=false;
				$message="Error: " . $this->db->rollback();
			}
    	return json_encode(array("status"=>$status,'message'=>$message)); // return json

	}

	public function graphqlQuery($query,$parm1,$parm2,$parm3){
		$resultArray =$this->shopify_graphql_object->GraphQL->post($query,$parm1,$parm2,$parm3);
        return  $resultArray;
	}

	// remove subscription product variants
	public function remove_subscription_productVariants($removeproducts,$subscription_plan_id){
		try{
			$graphQL_sellingPlanGroupRemoveProducts = 'mutation {
				sellingPlanGroupRemoveProductVariants(
				id: "'.$subscription_plan_id.'"
				productVariantIds: '.$removeproducts.'
				){
					userErrors {
					field
					message
					}
				}
			}';
			$sellingPlanGroupRemoveProductsapi_execution = $this->shopify_graphql_object->GraphQL->post($graphQL_sellingPlanGroupRemoveProducts);
			$sellingPlanGroupRemoveProductsapi_error = $sellingPlanGroupRemoveProductsapi_execution['data']['sellingPlanGroupRemoveProductVariants']['userErrors'];
			if(!count($sellingPlanGroupRemoveProductsapi_error)){
			   return json_encode(array("status"=>true,'error'=>'')); // return json
			}else{
				return json_encode(array("status"=>false,'error'=>$sellingPlanGroupRemoveProductsapi_error));
			}
		}
		catch(Exception $e) {
				return json_encode(array("status"=>false,'error'=>$e->getMessage())); // return json
		}
	}


	public function getRecurringBillingStatus($billingId){
		$getBillingStatus = 'query {
			node(id: "gid://shopify/AppSubscription/'.$billingId.'") {
			  ...on AppSubscription {
				status
				name
				trialDays
				lineItems {
				  plan {
					pricingDetails {
					  ...on AppRecurringPricing {
						interval
					  }
					}
				  }
				}
			  }
			}
		}';
		return	$graphQL_getBillingStatus = $this->graphqlQuery($getBillingStatus,null,null,null);
	}
	public function getContractDraftId($contractId){
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
			$contractDraftArray = $this->graphqlQuery($getContractDraft,null,null,null);
			$draftContract_execution_error = $contractDraftArray['data']['subscriptionContractUpdate']['userErrors'];
			if(!count($draftContract_execution_error)){
			 return $contractDraftArray['data']['subscriptionContractUpdate']['draft']['id'];
			}
		}catch(Exception $e) {
		  return 'error';
		}
	}

	public function checkShopifyPaymentRequirement(){
		$check_payment_requirement = '{
			shop {
			  features {
				eligibleForSubscriptions
			  }
			}
		}';
		$graphql_check_payment_response = $this->graphqlQuery($check_payment_requirement,null,null,null);
		return  $graphql_check_payment_response['data']['shop']['features']['eligibleForSubscriptions'];
	}

     public function billingAttemptApi($contractId){
		$idempotencyKey = uniqid();
		$currentDate = new DateTime();
        $formattedDate = $currentDate->format('Y-m-d\TH:i:s\Z');
		try{
				$graphQL_billingAttemptCreate = 'mutation {
					subscriptionBillingAttemptCreate(
						subscriptionContractId: "gid://shopify/SubscriptionContract/'.$contractId.'"
						subscriptionBillingAttemptInput: {idempotencyKey: "'.$idempotencyKey.'", originTime: "'.$formattedDate.'" }
					) {
						subscriptionBillingAttempt  {
						id
						subscriptionContract
						{
							nextBillingDate
							billingPolicy{
							interval
								intervalCount
								maxCycles
								minCycles
								anchors{
								day
								type
								month
								}

						}
						deliveryPolicy{
							interval
								intervalCount
								anchors{
								day
								type
								month
								}
						}
					}
					}
					userErrors {
						field
						message
					}
					}
				}';
			$billingAttemptCreateApi_execution = $this->graphqlQuery($graphQL_billingAttemptCreate,null,null,null);
			// if($this->store == 'predictive-search.myshopify.com'){
			//    echo '<pre>';
			//    print_r($billingAttemptCreateApi_execution);
			//    die;
			// }
		    $billingAttemptCreateApi_error = $billingAttemptCreateApi_execution['data']['subscriptionBillingAttemptCreate']['userErrors'];
			if(!count($billingAttemptCreateApi_error)){
				return  $billingAttemptCreateApi_execution;
			}else{
				return 'error';
			}
		}catch(Exception $e){
			return 'error';
		}
	 }
	 public function getActiveSubscriptions($customer_id) {
		// echo $customer_id;
		return $this->customQuery("SELECT COUNT(*) as total FROM subscriptionOrderContract WHERE store_id = '$this->store_id' AND shopify_customer_id = '$customer_id' AND contract_status = 'A'");
	}
	public function customerTagUpdate($status, $customer_id) {
		// echo $customer_id;die;
		$activeSubscription = $this->getActiveSubscriptions($customer_id);
		$totalActiveSubscriptions = $activeSubscription[0]['total'];
		if($totalActiveSubscriptions == 0) {
			$updateCustomerTag = 'mutation {
				tagsRemove(id: "gid://shopify/Customer/'.$customer_id.'", tags: ["sd_subscription_customer"]) {
					node {
						id
						}
					}
				}';
					
		}else {
			$updateCustomerTag = 'mutation {
				tagsAdd(id: "gid://shopify/Customer/'.$customer_id.'", tags: ["sd_subscription_customer"]) {
					node {
						id
					}
				}
			}';
		}
		$updateContractStatus_execution = $this->graphqlQuery($updateCustomerTag, null, null, null);
		// print_r($updateContractStatus_execution);
	}
    public function updateSubscriptionStatusCommit($contractId,$status,$adminEmail,$customerEmail,$ajaxCallFrom,$next_billing_date,$delivery_billing_type,$billing_policy_value,$contract_details,$contract_product_details){
		//check if setting changes or not
		if($status == 'CANCELLED'){
			$column_name = 'cancel_subscription';
		}else{
			$column_name = 'pause_resume_subscription';
		}
		$check_contract_update_setting = $this->getSettingData('customer_settings',$column_name);
		if($check_contract_update_setting[$column_name] == '1' || $ajaxCallFrom == 'backendAjaxCall'){
	    $subscription_draft_contract_id = $this->getContractDraftId($contractId);
		$changeStatus = $this->updateSubscriptionStatus($subscription_draft_contract_id,$status);
		if($changeStatus == 'Success'){
			$commitChanges =	$this->commitContractDraft($subscription_draft_contract_id);
			if($commitChanges == 'Success'){
				$whereCondition = array(
					'contract_id' => $contractId
				);
               if($status == 'ACTIVE'){
				   $changedContractStatus = 'Resumed';
					 $currentDate = date('Y-m-d');
						if($currentDate > $next_billing_date){
							$newRenewalDate = $next_billing_date;
							if($delivery_billing_type == 'DAY'){
								$TotalIntervalCount =  $billing_policy_value;
							}else if($delivery_billing_type == 'WEEK'){
								$TotalIntervalCount = (7 *  $billing_policy_value);
							}else if($delivery_billing_type == 'MONTH'){
								$TotalIntervalCount = (30 *  $billing_policy_value);
							}else if($delivery_billing_type == 'YEAR'){
								$TotalIntervalCount = (365 *  $billing_policy_value);
							}
						    $TotalIntervalCount = $this->getBillingRenewalDays(strtoupper($delivery_billing_type),$billing_policy_value);
							while($newRenewalDate <= $currentDate) {
								$newRenewalDate = date('Y-m-d', strtotime('+' . $TotalIntervalCount . ' day', strtotime($next_billing_date)));
								$next_billing_date = $newRenewalDate;
							}
						}else{
							$newRenewalDate = $next_billing_date;
						}
                        try{
							$fields = array(
								'contract_status' => $status[0],
								'next_billing_date'	=> $newRenewalDate,
								'updated_at' => gmdate('Y-m-d H:i:s')
							);
							$this->update_row('subscriptionOrderContract',$fields,$whereCondition,'and');
					    }catch(Exception $e){
						}
				}else if($status == 'CANCELLED'){
					$changedContractStatus = 'Cancelled';
				}else{
    				$changedContractStatus = 'Paused';
				}
                if($status == 'CANCELLED' || $status == 'PAUSED'){
					try{
						$fields = array(
							'contract_status' => $status[0],
							'updated_at' => gmdate('Y-m-d H:i:s')
						);
						$this->update_row('subscriptionOrderContract',$fields,$whereCondition,'and');
					}catch(Exception $e){
					}
				}
				$get_fields = 'customer_subscription_status_'.strtolower($changedContractStatus).',admin_subscription_status_'.strtolower($changedContractStatus);
				$contract_deleted_mail = $this->getSettingData('email_notification_setting',$get_fields);
				$email_send_to = '';
                if($contract_deleted_mail['customer_subscription_status_'.strtolower($changedContractStatus)] == '1' && $contract_deleted_mail['admin_subscription_status_'.strtolower($changedContractStatus)] == '1'){
                   $email_send_to = array($customerEmail,$adminEmail);
				}else if($contract_deleted_mail['customer_subscription_status_'.strtolower($changedContractStatus)] != '1' && $contract_deleted_mail['admin_subscription_status_'.strtolower($changedContractStatus)] == '1'){
					$email_send_to = $adminEmail;
				}else if($contract_deleted_mail['customer_subscription_status_'.strtolower($changedContractStatus)] == '1' && $contract_deleted_mail['admin_subscription_status_'.strtolower($changedContractStatus)] != '1'){
					$email_send_to = $customerEmail;
				}

				if($email_send_to != ''){
					$data = array(
						'template_type' => 'subscription_status_'.strtolower($changedContractStatus).'_template',
						'contract_id' => $contractId,
						'contract_details' => $contract_details[0],
						'contract_product_details' => $contract_product_details
					);
					$email_template_data = $this->email_template($data,'send_dynamic_email');
					if($email_template_data['template_type'] != 'none'){
						$sendMailArray = array(
							'sendTo' =>  $email_send_to,
							'subject' => $email_template_data['email_subject'],
							'mailBody' => $email_template_data['test_email_content'],
							'mailHeading' => '',
							'ccc_email' => $email_template_data['ccc_email'],
							'bcc_email' =>  $email_template_data['bcc_email'],
							// 'from_email' =>  $from_email,
							'reply_to' => $email_template_data['reply_to']
						);
						try{
							$contract_deleted_mail = $this->sendMail($sendMailArray,'false');
						}catch(Exception $e) {
						}
					}
			    }
				$this->customerTagUpdate($status, $contract_details[0]['shopify_customer_id']);
				return json_encode(array("status"=>true,'message'=>'Subscription Updated Successfully'));
			}else{
				return json_encode(array("status"=>false,'message'=>'Error Occured'));
			}

		}else{
			return json_encode(array("status"=>false,'message'=>'Error Occured'));
		}
	}else{
		return json_encode(array("status"=>false,'message'=>'Subscription Update setting has been disabled'));
	}
	}

	public function updateSubscriptionStatus($subscription_draft_contract_id,$contractStatus){
		try{
			$updateContractStatus = 'mutation {
			subscriptionDraftUpdate(
				draftId: "'.$subscription_draft_contract_id.'"
				input: { status : '.$contractStatus.' }
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
			$updateContractStatus_execution = $this->graphqlQuery($updateContractStatus,null,null,null);
			$updateContractStatus_execution_error = $updateContractStatus_execution['data']['subscriptionDraftUpdate']['userErrors'];
			if(!count($updateContractStatus_execution_error)){
				return 'Success';
			}
		}catch(Exception $e){
			return 'error';
		}
	}

	public function update_deliveryBilling_frequency($contract_draft_id,$delivery_billing_frequencies){
		try{
			$updateDeliveryBillingFrequency = '
			mutation {
				subscriptionDraftUpdate(
				  draftId: "'.$contract_draft_id.'"
				  input: {

					deliveryPolicy: {
						interval: '.$delivery_billing_frequencies['delivery_billing_type'].',
						intervalCount: '.$delivery_billing_frequencies['delivery_billing_frequency'].'
					},
					billingPolicy: {
						interval: '.$delivery_billing_frequencies['delivery_billing_type'].',
						intervalCount: '.$delivery_billing_frequencies['delivery_billing_frequency'].'
					},
				  }
				) {
					draft {
						deliveryPolicy{
							interval
							intervalCount
						}
						billingPolicy{
							interval
							intervalCount
						}
					}
					userErrors {
						field
						message
					}
				}
			  }
			';
			$updateDeliveryBillingFrequency_execution = $this->graphqlQuery($updateDeliveryBillingFrequency,null,null,null);
			$updateDeliveryBillingFrequency_error = $updateDeliveryBillingFrequency_execution['data']['subscriptionDraftUpdate']['userErrors'];
			if(!count($updateDeliveryBillingFrequency_error)){
				return array("status" => true, "delivery_billing_data" => $updateDeliveryBillingFrequency_execution);
			}else{
				return array("status"=>false, "message"=>$updateDeliveryBillingFrequency_error[0]['message']);
			}
		}catch(Exception $e){
				return array("status" => false, "message" => $e->getMessage());
		}
	}

	public function commitContractDraft($subscription_draft_contract_id){
		try{
			$updateContractStatus = 'mutation {
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
			$commitContractStatus_execution = $this->graphqlQuery($updateContractStatus,null,null,null);
			$commitContractStatus_execution_error = $commitContractStatus_execution['data']['subscriptionDraftCommit']['userErrors'];
			if(!count($commitContractStatus_execution_error)){
				return 'Success';
			}else{
				return 'error';
			}
		}catch(Exception $e){
			return 'error';
		}
	}

	public function getBillingStatus(){
		$whereCondition = array(
			'store_id' => $this->store_id,
			'status' => '1'
		);
		$currentPlanDetails = $this->table_row_value('storeInstallOffers','All',$whereCondition,'and','');
		$planName = $currentPlanDetails[0]['planName'];
		$plan_id = $currentPlanDetails[0]['plan_id'];
		if($plan_id != '1'){
			return 'ACTIVE';
		}else{
		    return "FREE";
		}
	}
	 // get total contract orders
	public function totalContractOrders($contract_id){
		$get_contract_total_orders =  $this->customQuery("SELECT COUNT(*) as recurringOrderTotal FROM billingAttempts WHERE store_id = '$this->store_id' AND contract_id = '$contract_id' AND status = 'Success'");
		return $total_contract_orders = ($get_contract_total_orders[0]['recurringOrderTotal'] + 1);
	 }
		    public function addNewContractProducts($newProductsAry,$productDetail,$added_by,$contract_details,$contract_product_details){
				$check_customer_setting = $this->getSettingData('customer_settings','add_subscription_product');
				$all_new_contract_products = array();
				if($check_customer_setting['add_subscription_product'] == '1' || $added_by == 'Admin'){
					$draft_id = $this->getContractDraftId($productDetail['contract_id']);
					foreach($newProductsAry as $value){
						//add new line in the contract mutation
						$whereCondition = array(
							'contract_id' => $productDetail['contract_id']
						);
						$fields = array('discount_type','discount_value','recurring_discount_type','recurring_discount_value','selling_plan_id','after_cycle','billing_policy_value','delivery_policy_value','after_cycle_update');
						$get_contract_data = $this->single_row_value('subscriptionOrderContract',$fields,$whereCondition,'and','');
				        $price = $value['price'];
						$price = ($price*($get_contract_data['billing_policy_value']/$get_contract_data['delivery_policy_value']));
						$computedPrice = 0;
						if($get_contract_data['discount_value'] != 0 || $get_contract_data['recurring_discount_value'] != 0){
							if($get_contract_data['discount_type'] == 'A'){
								if($price > $get_contract_data['discount_value']){
								   $price = $price-$get_contract_data['discount_value'];
								}else{
									$price = 0;
								}
							}else if($get_contract_data['discount_type'] == 'P'){
								$price = $price-($price*($get_contract_data['discount_value']/100));
							}

							// if recurring discount is applied
							if($get_contract_data['recurring_discount_value'] != 0){
								if($get_contract_data['recurring_discount_type'] == 'A'){
									$adjustmentType = 'FIXED_AMOUNT';
									$fixedorpercntValue = "fixedValue:".$get_contract_data['recurring_discount_value'];
									if($value['price'] > $get_contract_data['recurring_discount_value']){
									  $computedPrice = $value['price']-$get_contract_data['recurring_discount_value'];
									}else{
										$computedPrice = 0;
									}
								}else{
									$adjustmentType = 'PERCENTAGE';
									$fixedorpercntValue = "percentage:".$get_contract_data['recurring_discount_value'];
									$computedPrice =$value['price']-($value['price']*($get_contract_data['recurring_discount_value']/100));
								}
                                		$cycleDiscount = 'cycleDiscounts : {
											adjustmentType: '.$adjustmentType.'
											adjustmentValue :  {
												'.$fixedorpercntValue.'
											}
											afterCycle: '.$get_contract_data['after_cycle'].'
											computedPrice:'.$computedPrice.'
										}';
										$pricingPolicy = 'pricingPolicy :{
											basePrice : '.$price.'
											'.$cycleDiscount.'
										}';
										if($get_contract_data['after_cycle_update'] == '1'){
										   $price = $computedPrice;
										}
							}else{
								$pricingPolicy = '';
							}

						}else{
							$pricingPolicy = '';
						}
					try{
						$addNewLineItem = 'mutation{
							subscriptionDraftLineAdd(
							draftId: "'.$draft_id.'"
							input: {
								productVariantId: "gid://shopify/ProductVariant/'.$value['variant_id'].'"
								quantity: '.$value['quantity'].'
								currentPrice: '.number_format((float)$price, 2, '.', '').'
								'.$pricingPolicy.'
							}
							) {
								lineAdded {
									id
								}
								userErrors {
									field
									message
								}
							}
						}';
						$addNewLineItem_execution = $this->graphqlQuery($addNewLineItem,null,null,null);
						$addNewLineItem_error = $addNewLineItem_execution['data']['subscriptionDraftLineAdd']['userErrors'];
					}catch(Exception $e) {
						return json_encode(array("status"=>false,'message'=>'Something went wrong')); // return json
					}
					if(!count($addNewLineItem_error)){
						$AddLineItemId = substr($addNewLineItem_execution['data']['subscriptionDraftLineAdd']['lineAdded']['id'], strrpos($addNewLineItem_execution['data']['subscriptionDraftLineAdd']['lineAdded']['id'], '/') + 1);
						$fields = array(
							"store_id" => $this->store_id,
							"contract_id" =>$productDetail['contract_id'],
							"product_id" => $value['product_id'],
							"variant_id" =>  $value['variant_id'],
							"product_name" => $value['product_title'],
							// "variant_name" =>  $value['variant_title'],
							"variant_name" => str_replace('"', '',$value['variant_title']),
							"subscription_price" => number_format((float)$price, 2, '.', ''),
							'quantity' => $value['quantity'],
							"contract_line_item_id" => $AddLineItemId,
							"recurring_computed_price"=>number_format((float)$computedPrice, 2, '.', ''),
							"variant_image" => $value['image'],
						);
						$insert_row = $this->insert_row('subscritionOrderContractProductDetails',$fields);
						
						array_push($all_new_contract_products,$fields);
					}else{
						$addNewLineItem_execution_error  = $addNewLineItem_execution['data']['subscriptionDraftLineAdd']['userErrors'];
					}
					}

				$newProductsAdded =	$this->commitContractDraft($draft_id);
				if($newProductsAdded == 'Success'){
					$send_mail_to = '';
					$product_added_mail = $this->getSettingData('email_notification_setting','customer_product_added,admin_product_added');
			        if($product_added_mail['customer_product_added'] == '1' && $product_added_mail['admin_product_added'] == '1'){
                        $send_mail_to = array($productDetail['customerEmail'],$productDetail['adminEmail']);
					}else if($product_added_mail['customer_product_added'] == '1' && $product_added_mail['admin_product_added'] != '1'){
                        $send_mail_to = $productDetail['customerEmail'];
					}else if($product_added_mail['customer_product_added'] != '1' && $product_added_mail['admin_product_added'] == '1'){
                        $send_mail_to = $productDetail['adminEmail'];
					}
					if($send_mail_to != ''){
						$data = array(
							'template_type' => 'product_added_template',
							'contract_id' => $productDetail['contract_id'],
							'contract_details' => $contract_details[0],
							'contract_product_details' => $contract_product_details,
							'new_added_products' => $all_new_contract_products
						);
						$email_template_data = $this->email_template($data,'send_dynamic_email');
						if($email_template_data['template_type'] != 'none'){
							$sendMailArray = array(
								'sendTo' =>  $send_mail_to,
								'subject' => $email_template_data['email_subject'],
								'mailBody' => $email_template_data['test_email_content'],
								'mailHeading' => '',
								'ccc_email' => $email_template_data['ccc_email'],
								'bcc_email' =>  $email_template_data['bcc_email'],
								// 'from_email' =>  $from_email,
								'reply_to' => $email_template_data['reply_to']
							);
							try{
								$contract_deleted_mail = $this->sendMail($sendMailArray,'false');
							}catch(Exception $e) {
							}
						}
					// 	$mailBody = '<table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;" data-muid="2f94ef24-a0d9-4e6f-be94-d2d1257946b0" data-mc-module-version="2019-10-22">
					// 	<tbody>
					// 	  <tr>
					// 		<td style="padding:18px 50px 18px 50px; line-height:22px; text-align:inherit; background-color:#FFFFFF;" height="100%" valign="top" bgcolor="#FFFFFF" role="module-content"><div><div style="font-family: inherit; text-align: center"><span style="font-size: 16px; font-family: inherit">New Product(s) has been added in the Subscription with  Id #'.$productDetail['contract_id'].'</span></div><div></div></div></td>
					// 	  </tr>
					// 	</tbody>
					//   </table>';
					// 	$sendMailArray = array(
					// 		'sendTo' =>  $send_mail_to,
					// 		'subject' => 'New Product Added in the Subscription',
					// 		'mailBody' => $mailBody,
					// 		'mailHeading' => 'Product Added in the Subscription'
					// 	);
					// 	$this->sendMail($sendMailArray,'false');
		     	    }
			        return json_encode(array("status"=>true,'message'=>'Product(s) Added Successfully')); // return json
				}else{
				    return json_encode(array("status"=>false,'message'=>'Something went wrong')); // return json
				}
			}else{
				return json_encode(array("status"=>false,'message'=>'Add Product Setting has been disabled')); // return json
			}
	        }

	public function getCurrencySymbol($currencyCode){
        $currency_list = array(
			"AFA" => "؋","ALL" => "Lek","DZD" => "دج","AOA" => "Kz","ARS" => "$","AMD" => "֏","AWG" => "ƒ","AUD" => "$","AZN" => "m","BSD" => "B$","BHD" => ".د.ب","BDT" => "৳","BBD" => "Bds$","BYR" => "Br","BEF" => "fr","BZD" => "$","BMD" => "$","BTN" => "Nu.","BTC" => "฿","BOB" => "Bs.","BAM" => "KM","BWP" => "P","BRL" => "R$","GBP" => "£","BND" => "B$","BGN" => "Лв.","BIF" => "FBu","KHR" => "KHR","CAD" => "$","CVE" => "$","KYD" => "$","XOF" => "CFA","XAF" => "FCFA","XPF" => "₣","CLP" => "$","CNY" => "¥","COP" => "$","KMF" => "CF","CDF" => "FC","CRC" => "₡","HRK" => "kn","CUC" => "$, CUC","CZK" => "Kč","DKK" => "Kr.","DJF" => "Fdj","DOP" => "$","XCD" => "$",
			"EGP" => "ج.م","ERN" => "Nfk","EEK" => "kr","ETB" => "Nkf","EUR" => "€","FKP" => "£","FJD" => "FJ$","GMD" => "D","GEL" => "ლ","DEM" => "DM","GHS" => "GH₵","GIP" => "£","GRD" => "₯, Δρχ, Δρ","GTQ" => "Q","GNF" => "FG","GYD" => "$","HTG" => "G","HNL" => "L","HKD" => "$","HUF" => "Ft","ISK" => "kr","INR" => "Rs.","IDR" => "Rp","IRR" => "﷼","IQD" => "د.ع","ILS" => "₪","ITL" => "L,£","JMD" => "J$","JPY" => "¥","JOD" => "ا.د","KZT" => "лв","KES" => "KSh","KWD" => "ك.د","KGS" => "лв","LAK" => "₭",
			"LVL" => "Ls","LBP" => "£","LSL" => "L","LRD" => "$","LYD" => "د.ل","LTL" => "Lt","MOP" => "$","MKD" => "ден", "MGA" => "Ar", "MWK" => "MK", "MYR" => "RM", "MVR" => "Rf", "MRO" => "MRU", "MUR" => "₨", "MXN" => "$", "MDL" => "L", "MNT" => "₮", "MAD" => "MAD", "MZM" => "MT", "MMK" => "K", "NAD" => "$", "NPR" => "₨", "ANG" => "ƒ", "TWD" => "$", "NZD" => "$", "NIO" => "C$", "NGN" => "₦", "KPW" => "₩", "NOK" => "kr", "OMR" => ".ع.ر", "PKR" => "₨", "PAB" => "B/.", "PGK" => "K", "PYG" => "₲", "PEN" => "S/.", "PHP" => "₱", "PLN" => "zł", "QAR" => "ق.ر", "RON" => "lei", "RUB" => "₽", "RWF" => "FRw", "SVC" => "₡", "WST" => "SAT", "SAR" => "﷼", "RSD" => "din", "SCR" => "SRe", "SLL" => "Le", "SGD" => "$", "SKK" => "Sk", "SBD" => "Si$", "SOS" => "Sh.so.", "ZAR" => "R", "KRW" => "₩", "XDR" => "SDR", "LKR" => "Rs", "SHP" => "£", "SDG" => ".س.ج", "SRD" => "$", "SZL" => "E", "SEK" => "kr", "CHF" => "CHf", "SYP" => "LS", "STD" => "Db", "TJS" => "SM", "TZS" => "TSh", "THB" => "฿", "TOP" => "$", "TTD" => "$", "TND" => "ت.د", "TRY" => "₺", "TMT" => "T", "UGX" => "USh", "UAH" => "₴", "AED" => "إ.د", "UYU" => "$", "USD" => "$", "UZS" => "лв", "VUV" => "VT", "VEF" => "Bs", "VND" => "₫", "YER" => "﷼", "ZMK" => "ZK"
		);
		return $currency_list[$currencyCode];
	}

	// send mail to customer to update payment details
	public function sendPaymentUpdateMail($paymentUpdateToken,$emailSendTo){
		try{
			$updatePayment =  'mutation customerPaymentMethodSendUpdateEmail($customerPaymentMethodId: ID!) {
				customerPaymentMethodSendUpdateEmail(customerPaymentMethodId: $customerPaymentMethodId) {
				  customer {
					id
				  }
				  userErrors {
					field
					message
				  }
				}
			  }';
			$updatePaymentParameters = [
				"customerPaymentMethodId"=> "gid://shopify/CustomerPaymentMethod/".$paymentUpdateToken,
				"email" => [
					"bcc"=> "vipingarg.shinedezign@gmail",
					"body"=> "Update Payment Method",
					"customMessage" => "payment update",
					"from" => "vipingarg.shinedezign@gmail",
					"subject" => "Update Payment Method",
					"to" => $emailSendTo
				]
			];
		 $updatePaymentGet = $this->graphqlQuery($updatePayment,null,null,$updatePaymentParameters);
		 $updatePaymentGet_error = $updatePaymentGet['data']['customerPaymentMethodSendUpdateEmail']['userErrors'];
		}catch(Exception $e) {
			return json_encode(array("status"=>false,'message'=>'Error occured')); // return json
		}
		if(!count($updatePaymentGet_error)){
		    return json_encode(array("status"=>true,'message'=>'Payment Method Update Request has been sent to the Customer mail')); // return json
		}else{
			return json_encode(array("status"=>true,'message'=>$updatePaymentGet_error[0]['message'])); // return json
		}
	}

	public function getProductData($productIdsArray){
		if(is_array($productIdsArray)){
            $getVariant = 30;
		}else{
			$getVariant = 100;
		}
		try{
	    $getProductData = '{
			nodes(ids: '.$productIdsArray.') {
			  ...on Product {
				title
				id
				featuredImage {
				  originalSrc
				}
				variants(first:'.$getVariant.'){
					edges{
					  node{
						id
						title
						image{
							originalSrc
						  }
					  }
					}
				}
			  }
			}
		  }';
		return $this->graphqlQuery($getProductData,null,null,null);
		}catch(Exception $e) {
			return json_encode(array("status"=>false,'message'=>'Error occured')); // return json
		}
	}

	public function getGroupProductVariant($sellingPlanGroupId,$product_id){
		$get_groupProduct = 'query{
			sellingPlanGroup(id : "'.$sellingPlanGroupId.'") {
				productCount
				productVariantCount
				appliesToProduct(productId : "'.$product_id.'")
			}
		}';
		$graphQL_get_groupProduct = $this->graphqlQuery($get_groupProduct,null,null,null);
		return $graphQL_get_groupProduct;
	}


	// public function getVariantData
	public function getProductVariant($product_id){
		$getVariantData = '{
			product(id:"'.$product_id.'") {
				variants(first: 100){
					edges{
						node{
							id
						}
					}
				}
			}
		}';
		$variantData_execution =  $this->graphqlQuery($getVariantData,null,null,null);
		return $variantData_execution;
	}

	public function getVariantData($cursor,$product_id){
		if($cursor != ''){
			$after_cursor = ', after : "'.$cursor.'"';
		}else{
			$after_cursor = '';
		}
		$getVariantData = '{
			product(id:"gid://shopify/Product/'.$product_id.'") {
				handle
			variants(first: 80 '.$after_cursor.'){
				pageInfo {
					hasNextPage
					hasPreviousPage
				}
				edges{
				  cursor
				  node{
					id
					title
					price
					inventoryQuantity
					image{
						originalSrc
					}
				  }
				}
			}
			}
		}';
		$variantData_execution =  $this->graphqlQuery($getVariantData,null,null,null);
		return $variantData_execution;
	}

	public function getVariantDetail($variant_id){
		$getSingleVariantData = '{
			nodes(ids: '.$variant_id.') {
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
		$getSingleVariantData_execution =  $this->graphqlQuery($getSingleVariantData,null,null,null);
		return $getSingleVariantData_execution;
	}

	public function getAllProducts($cursor,$query){
		if($cursor == ''){
			$get_products_after = '';
		}else{
			$get_products_after = 'after : "'.$cursor.'",';
		}
        if($query == ''){
			$get_search_products = ', query: "status:ACTIVE"';
		}else{
			$get_search_products = ',  query: "title:*'.$query.'* AND status:ACTIVE"';
		}
		try{
			$getProductData = '{
				products(first: 10, '.$get_products_after.'  '.$get_search_products.', reverse:true) {
					pageInfo {
						hasNextPage
						hasPreviousPage
					}
				edges {
					cursor
					node {
						title
						id
						handle
						featuredImage {
						originalSrc
						}
						totalInventory
						totalVariants
						variants(first:20){
							pageInfo {
								hasNextPage
								hasPreviousPage
							}
							edges{
							cursor
							node{
								id
								title
								price
								inventoryQuantity
								inventoryPolicy
								requiresShipping
								image{
									originalSrc
								}
							}
							}
						}
					}
				}
				}
			}';
			$prouductData_execution = $this->graphqlQuery($getProductData,null,null,null);
		}catch(Exception $e) {
			$prouductData_execution = ''; // return json
		}
		return $prouductData_execution;

	}

	public function getShopData($store){
	  $getShopData = $this->PostPutApi('https://'. $this->store.'/admin/api/'.$this->SHOPIFY_API_VERSION.'/shop.json','GET',$this->access_token,'');
	  return $getShopData;
	}

	public function update_selling_planName($contract_draft_id,$line_item_id,$selling_plan_name){
		try{
		$update_selling_plan_name_mutation = 'mutation {
			subscriptionDraftLineUpdate(
			draftId: "'.$contract_draft_id.'"
			lineId: "gid://shopify/SubscriptionLine/'.$line_item_id.'"
			input: {
				sellingPlanName : "'.$selling_plan_name.'"
			}
			) {
				lineUpdated {
					id
					quantity
					currentPrice{
						amount
					}
					productId
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

		$productUpdateData_execution = $this->graphqlQuery($update_selling_plan_name_mutation,null,null,null);
		$productUpdateDataapi_error = $productUpdateData_execution['data']['subscriptionDraftLineUpdate']['userErrors'];
		if(!count($productUpdateDataapi_error)){
			return true;
		}else{
			return false;
		}
		}catch(Exception $e) {
			return array("status"=>false,'error'=>$e->getMessage(),'message'=>'Quantity Upate error'); // return json
		}
	}

	//   update quantity of product subscription
	public function updateSubscriptionProductQty($data){
		$get_fields = 'edit_product_quantity,edit_product_price';
		$check_customer_update = $this->getSettingData('customer_settings',$get_fields);
		if( $data['updated_by'] == 'Admin'){
			$update_product = 'true';
			$update_data = '{ quantity: '.$data['prd_qty'].', currentPrice : '.$data['prd_price'].'}';
		}else if($data['prd_old_price'] != $data['prd_price'] && $data['prd_old_qty'] != $data['prd_qty'] && $check_customer_update['edit_product_price'] == '1' && $check_customer_update['edit_product_quantity'] == '1'){
			$update_product = 'true';
			$update_data = '{ quantity: '.$data['prd_qty'].', currentPrice : '.$data['prd_price'].'}';
		}else if($data['prd_old_price'] == $data['prd_price'] && $data['prd_old_qty'] != $data['prd_qty'] && $check_customer_update['edit_product_quantity'] == '1'){
			$update_product = 'true';
			$update_data = '{ quantity: '.$data['prd_qty'].'}';
		}else if($data['prd_old_qty'] == $data['prd_qty'] && $data['prd_old_price'] != $data['prd_price'] && $check_customer_update['edit_product_price'] == '1'){
			$update_product = 'true';
			$update_data = '{ currentPrice: '.$data['prd_price'].'}';
		}else{
			$update_product = 'false';
		}
		if($update_product == 'true'){
		$contract_draftId = $this->getContractDraftId(trim($data['contract_id']));
		try{
			$productUpdateData =   'mutation {
				subscriptionDraftLineUpdate(
				draftId: "'.$contract_draftId.'"
				lineId: "gid://shopify/SubscriptionLine/'.$data['line_id'].'"
				input: '.$update_data.'
				) {
					lineUpdated {
						id
						quantity
						currentPrice{
							amount
						}
						productId
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
		 $productUpdateData_execution = $this->graphqlQuery($productUpdateData,null,null,null);
		 $productUpdateDataapi_error = $productUpdateData_execution['data']['subscriptionDraftLineUpdate']['userErrors'];
		}catch(Exception $e) {
			return array("status"=>false,'error'=>$e->getMessage(),'message'=>'Quantity Upate error'); // return json
		}
		if(!count($productUpdateDataapi_error)){
			$this->commitContractDraft($contract_draftId);
			$updatedPrdQty = $productUpdateData_execution['data']['subscriptionDraftLineUpdate']['lineUpdated']['quantity'];
			$updatedPrdPrice = $productUpdateData_execution['data']['subscriptionDraftLineUpdate']['lineUpdated']['currentPrice']['amount'];
			$fields = array(
				'quantity' => $updatedPrdQty,
				'subscription_price' => $updatedPrdPrice
			);
			$whereCondition = array(
                'contract_line_item_id' => $data['line_id']
			);
			$this->update_row('subscritionOrderContractProductDetails',$fields,$whereCondition,'and');
            if($data['prd_old_price'] != $data['prd_price']){ // remove all the discount applied if price edited
				$contract_fields = array(
					'discount_value' => 0,
					'recurring_discount_value' => 0,
					'after_cycle_update'=> '0',
					'after_cycle'=>0
				);
				$whereContractCondition = array(
					'contract_id' => $data['contract_id']
				);
                $this->update_row('subscriptionOrderContract',$contract_fields,$whereContractCondition,'and');
			}
			//send product updation mail to customer and admin
			$get_fields = 'customer_product_updated,admin_product_updated';
			$quantity_updated = $this->getSettingData('email_notification_setting',$get_fields);
			$send_mail_to = '';
			if($quantity_updated['customer_product_updated'] == '1' && $quantity_updated['admin_product_updated'] == '1'){
				$send_mail_to = array($data['customerEmail'],$data['adminEmail']);
			}else if($quantity_updated['customer_product_updated'] == '1' && $quantity_updated['admin_product_updated'] != '1'){
				$send_mail_to = $data['customerEmail'];
			}else if($quantity_updated['customer_product_updated'] != '1' && $quantity_updated['admin_product_updated'] == '1'){
				$send_mail_to = $data['adminEmail'];
			}
			if($send_mail_to != ''){
				$data = array(
					'template_type' => 'product_updated_template',
					'contract_id' => $data['contract_id'],
					'contract_details' => $data['specific_contract_data'][0],
					'contract_product_details' => $data['contract_product_details'],
					'updated_product' => $data
				);
				$email_template_data = $this->email_template($data,'send_dynamic_email');
				if($email_template_data['template_type'] != 'none'){
					$sendMailArray = array(
						'sendTo' =>  $send_mail_to,
						'subject' => $email_template_data['email_subject'],
						'mailBody' => $email_template_data['test_email_content'],
						'mailHeading' => '',
						'ccc_email' => $email_template_data['ccc_email'],
						'bcc_email' =>  $email_template_data['bcc_email'],
						'reply_to' => $email_template_data['reply_to']
					);
					try{
						$contract_deleted_mail = $this->sendMail($sendMailArray,'false');
					}catch(Exception $e) {
					}
				}
				// $mailBody = '<table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;" data-muid="2f94ef24-a0d9-4e6f-be94-d2d1257946b0" data-mc-module-version="2019-10-22">
				// 	<tbody>
				// 	  <tr>
				// 		<td style="padding:18px 50px 18px 50px; line-height:22px; text-align:inherit; background-color:#FFFFFF;" height="100%" valign="top" bgcolor="#FFFFFF" role="module-content"><div><div style="font-family: inherit; text-align: center"><span style="font-size: 16px; font-family: inherit">Product "'.$data['product_title'].'" has been updated in Subscription with Id '.$data['contract_id'].', now the product quantity is '.$updatedPrdQty.' and the product price is '.$updatedPrdPrice.'.</span></div><div></div></div></td>
				// 	  </tr>
				// 	</tbody>
				//   </table>';
				// $sendMailArray = array(
				// 	'sendTo' =>  $send_mail_to,
				// 	'subject' => 'Product Update',
				// 	'mailBody' => $mailBody,
				// 	'mailHeading' => 'Product has been Updated of the Subscription #'.$data['contract_id']
				// );
				// $this->sendMail($sendMailArray,'false');
		    }
	     	$result = array('status'=>true,'message'=>"Product Updated Successfully");
			return json_encode($result); // return json
		}else{
			return json_encode(array("status"=>false,'error'=>$productUpdateDataapi_error)); // return json
		}
	}else{
	    return json_encode(array("status"=>false, 'message'=> 'Update Product Setting has been disabled')); // return json
    }
	}

    public function getShippingRates($draftId,$shippingDataValues){
		try{
		$shippingAddressRate = '{
			subscriptionDraft(id: "'.$draftId.'") {
				shippingOptions(
				deliveryAddress: {
					address1: "'.$shippingDataValues['address1'].'"
					address2: "'.$shippingDataValues['address2'].'"
					company: "'.$shippingDataValues['company'].'"
					city: "'.$shippingDataValues['city'].'"
					country: "'.$shippingDataValues['country'].'"
					province: "'.$shippingDataValues['province'].'"
					zip: "'.$shippingDataValues['zip'].'"
					firstName: "'.$shippingDataValues['first_name'].'"
					lastName: "'.$shippingDataValues['last_name'].'"
					phone: "'.$shippingDataValues['phone'].'"
				}
				) {
				__typename
				... on SubscriptionShippingOptionResultSuccess {
					shippingOptions {
						title
						description
						presentmentTitle
						code
						price {
						amount
						currencyCode
						}
					}
				}
				... on SubscriptionShippingOptionResultFailure {
					message
				}
				}
			}
		}';
	 $shippingData = $this->graphqlQuery($shippingAddressRate,null,null,null);
	 return $shippingData;
	}catch(Exception $e){
		return 'error';
	}
	}

	public function updateShippingAddressAndRates($draftId,$shippingDataValues){
	    if(isset($shippingDataValues['delivery_price'])){
            $delivery_price_value = 'deliveryPrice : "'.$shippingDataValues['delivery_price'].'"';
		}else{
			$delivery_price_value = '';
		}
		 try{
			$updateShippingAddress = '
			mutation {
				subscriptionDraftUpdate(
				  draftId: "'.$draftId.'"
				  input: {
					deliveryMethod: {
					  shipping: {
						address: {
							address1: "'.$shippingDataValues['address1'].'"
							address2: "'.$shippingDataValues['address2'].'"
							company: "'.$shippingDataValues['company'].'"
							city: "'.$shippingDataValues['city'].'"
							country: "'.$shippingDataValues['country'].'"
							province: "'.$shippingDataValues['province'].'"
							zip: "'.$shippingDataValues['zip'].'"
							firstName: "'.$shippingDataValues['first_name'].'"
							lastName: "'.$shippingDataValues['last_name'].'"
							phone: "'.$shippingDataValues['phone'].'"
						}
					  }
					}
					'.$delivery_price_value.'
				  }
				) {
				  draft {
					deliveryPrice {
					  amount
					  currencyCode
					}
					deliveryMethod {
					  __typename

					  ... on SubscriptionDeliveryMethodShipping {
						address {
						  address1
						  city
						  provinceCode
						  countryCode
						}
						shippingOption {
						  title
						  presentmentTitle
						  code
						  description
						}
					  }
					}
				  }
				  userErrors {
					field
					message
				  }
				}
			  }
			';
			$updateShippingAddress_execution = $this->graphqlQuery($updateShippingAddress,null,null,null);
			$updateShippingAddress_error = $updateShippingAddress_execution['data']['subscriptionDraftUpdate']['userErrors'];
			// if($this->store == 'mytab-shinedezign.myshopify.com'){
			// 	echo '<pre>';
			// 	print_r($updateShippingAddress_execution);
			// 	die;
			// }
			if(!count($updateShippingAddress_error)){
				return array("status" => true, "shipping_data" => $updateShippingAddress_execution);
			}else{
				return array("status"=>false, "message"=>$updateShippingAddress_error[0]['message']);
			}
		}catch(Exception $e){
			// if($this->store == 'mytab-shinedezign.myshopify.com'){
			// 	echo '<pre>';
			// 	print_r($e->getMessage());
			// 	die;
			// }
			return array("status" => false, "message" => $e->getMessage());
		}
	}



	public function updateShippingAddress($contractId,$shippingDataValues,$ajaxCallFrom,$contract_details,$contract_product_details){
		if(is_object($shippingDataValues)){
			$shippingDataValues = (array) $shippingDataValues;
		}
		$check_customer_setting = $this->getSettingData('customer_settings', 'edit_shipping_address');
		$customer_email = $shippingDataValues['sendMailToCustomer'];
		$get_store_detail = $this->customQuery("SELECT currencyCode,name FROM customers,store_details WHERE store_details.store_id = '$this->store_id' and customers.store_id = '$this->store_id' and customers.email = '$customer_email'");
		$shop_currency_code = $get_store_detail[0]['currencyCode'];
		if($get_store_detail[0]['name'] != ''){
			$customer_name = $get_store_detail[0]['name'];
		}else{
			$customer_name = $shippingDataValues['sendMailToCustomer'];
		}
        if($check_customer_setting['edit_shipping_address'] == '1' || $ajaxCallFrom == 'backendAjaxCall'){
		    $draftId =  $this->getContractDraftId($contractId);
			$shippingAddresses = $this->updateShippingAddressAndRates($draftId,$shippingDataValues);
	    	if($shippingAddresses['status'] == true){
			  $addressUpdated =	$this->commitContractDraft($draftId);
				if($addressUpdated != 'error'){
					$fields = array(
						'first_name' => htmlspecialchars($shippingDataValues['first_name'], ENT_QUOTES),
						'last_name' => htmlspecialchars($shippingDataValues['last_name'], ENT_QUOTES),
						'company' => htmlspecialchars($shippingDataValues['company'], ENT_QUOTES),
						'city' => htmlspecialchars($shippingDataValues['city'], ENT_QUOTES),
						'address1' => htmlspecialchars($shippingDataValues['address1'], ENT_QUOTES),
						'address2' => htmlspecialchars($shippingDataValues['address2'], ENT_QUOTES),
						'country' => htmlspecialchars($shippingDataValues['country'], ENT_QUOTES),
						'province' => htmlspecialchars($shippingDataValues['province'], ENT_QUOTES),
						'phone' => htmlspecialchars($shippingDataValues['phone'], ENT_QUOTES),
						'country_code' => htmlspecialchars($shippingAddresses['shipping_data']['data']['subscriptionDraftUpdate']['draft']['deliveryMethod']['address']['countryCode'], ENT_QUOTES),
						'zip' => htmlspecialchars($shippingDataValues['zip'], ENT_QUOTES),
					);
					if(isset($shippingDataValues['delivery_price'])){
						$fields['delivery_price'] = htmlspecialchars($shippingDataValues['delivery_price'], ENT_QUOTES);
					}
					$whereCondition = array(
					'contract_id' => $contractId
					);
				  $updateAddress =	json_decode($this->update_row('subscriptionContractShippingAddress',$fields,$whereCondition,'and'));
					if($updateAddress->status == 1){
						$get_fields = 'customer_shipping_address_update,admin_shipping_address_update';
						$shipping_Address_change = $this->getSettingData('email_notification_setting',$get_fields);
						$sendMailTo = '';
						if($shipping_Address_change['customer_shipping_address_update'] == '1' && $shipping_Address_change['admin_shipping_address_update'] == '1'){
							$sendMailTo = array($shippingDataValues['sendMailToAdmin'], $shippingDataValues['sendMailToCustomer']);
						}else if($shipping_Address_change['customer_shipping_address_update'] != '1' && $shipping_Address_change['admin_shipping_address_update'] == '1'){
							$sendMailTo = $shippingDataValues['sendMailToAdmin'];
						}else if($shipping_Address_change['customer_shipping_address_update'] == '1' && $shipping_Address_change['admin_shipping_address_update'] != '1'){
							$sendMailTo = $shippingDataValues['sendMailToCustomer'];
						}
						if($sendMailTo != ''){
							$data = array(
								'template_type' => 'shipping_address_update_template',
								'contract_id' => $contractId,
								'contract_details' => $contract_details[0],
								'contract_product_details' => $contract_product_details,
								'shipping_data' => $shippingDataValues,
							);
							$email_template_data = $this->email_template($data,'send_dynamic_email');
							if($email_template_data['template_type'] != 'none'){
								$sendMailArray = array(
									'sendTo' =>  $sendMailTo,
									'subject' => $email_template_data['email_subject'],
									'mailBody' => $email_template_data['test_email_content'],
									'mailHeading' => '',
									'ccc_email' => $email_template_data['ccc_email'],
									'bcc_email' =>  $email_template_data['bcc_email'],
									'reply_to' => $email_template_data['reply_to']
								);
								try{
									$contract_deleted_mail = $this->sendMail($sendMailArray,'false');
								}catch(Exception $e) {
								}
							}
						}
					}
					return json_encode(array("status"=>true,'message'=>'Shipping Address Updated Successfully'));
				}else{
					return json_encode(array("status"=>false,'message'=>'Update Shipping Address error'));
				}
			}else{
				return json_encode(array("status"=>false,'message'=>$shippingAddresses['message']));
			}
		}else{
			return json_encode(array("status"=>false,'message'=>'Enter Valid Shipping Address'));
		}
    }



	public function removeSubscriptionProduct($contractId,$lineId,$deleted_from,$customerEmail,$adminEmail,$contract_details,$contract_product_details){
		//check if customer can delete product or not
		$check_customer_setting =  $this->getSettingData('customer_settings','delete_product');
		if($check_customer_setting['delete_product'] == '1' || $deleted_from == 'Admin'){
		$contract_draftId = $this->getContractDraftId($contractId);
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
				$removeProduct_execution = $this->graphqlQuery($removeProduct,null,null,null);

				$removeProduct_error = $removeProduct_execution['data']['subscriptionDraftLineRemove']['userErrors'];
			}catch(Exception $e) {
				return json_encode(array("status"=>false,'error'=>$e->getMessage(),'message'=>'Product Removed error')); // return json
			}
			if(!count($removeProduct_error)){
				$commitContractChanges = $this->commitContractDraft($contract_draftId);
                if($commitContractChanges != 'error'){
				$fields = array(
					'product_contract_status' => '0'
				);
				$whereCondition = array(
					'contract_line_item_id' => $lineId
				);
				$this->update_row('subscritionOrderContractProductDetails',$fields,$whereCondition,'and');
				// send mail to the customer or admin
				$get_fields = 'customer_product_removed,admin_product_removed';
				$product_deleted_mail = $this->getSettingData('email_notification_setting',$get_fields);
				$sendMailTo = '';
				if($product_deleted_mail['customer_product_removed'] == '1' && $product_deleted_mail['admin_product_removed'] == '1'){
				    $sendMailTo = array($customerEmail,$adminEmail);
				}else if($product_deleted_mail['customer_product_removed'] != '1' && $product_deleted_mail['admin_product_removed'] == '1'){
				    $sendMailTo = $adminEmail;
				}else if($product_deleted_mail['customer_product_removed'] == '1' && $product_deleted_mail['admin_product_removed'] != '1'){
				    $sendMailTo = $customerEmail;
				}
				if($sendMailTo != ''){
					$data = array(
						'template_type' => 'product_removed_template',
						'contract_id' => $contractId,
						'contract_details' => $contract_details[0],
						'contract_product_details' => $contract_product_details,
						'deleted_product_line_id' => $lineId
					);
					$email_template_data = $this->email_template($data,'send_dynamic_email');
                    // if($this->store == 'predictive-search.myshopify.com'){
					//   echo '<pre>';
					//   print_r($email_template_data);
					//   die;
					// }
					if($email_template_data['template_type'] != 'none'){
						$sendMailArray = array(
							'sendTo' =>  $sendMailTo,
							'subject' => $email_template_data['email_subject'],
							'mailBody' => $email_template_data['test_email_content'],
							'mailHeading' => '',
							'ccc_email' => $email_template_data['ccc_email'],
							'bcc_email' =>  $email_template_data['bcc_email'],
							'reply_to' => $email_template_data['reply_to']
						);
						try{
							$contract_deleted_mail = $this->sendMail($sendMailArray,'false');
						}catch(Exception $e) {
						}
					}
				}
				return json_encode(array("status"=>true,'message'=>'Product Removed Successfully')); // return json
			  }else{
				return json_encode(array("status"=>false,'message'=>$removeProduct_error[0]['message'])); // return json
			 }
			}else{
			   return json_encode(array("status"=>false,'message'=>$removeProduct_error[0]['message'])); // return json
			}
		}else{
			return json_encode(array("status"=>false,'message'=>'Delete Product Setting has been disabled')); // return json
		}

	}

		//this function used in both manaully billing attempt and contract renewal cron job
		public function getContractProducts($contract_id){
			$get_contract_products = $this->customQuery("SELECT variant_id,contract_line_item_id,recurring_computed_price FROM subscritionOrderContractProductDetails WHERE contract_id = '$contract_id' AND product_contract_status = '1'");
			return $get_contract_products;
		}
		public function updateLineItemPrice($updateLineItemArray,$contract_id){
			$contractDraftid =  $this->getContractDraftId($contract_id);
			$change_after_cycle_update = '0';
             foreach($updateLineItemArray as $key=>$value){
				$lineItemId = $value['contract_line_item_id'];
				$recurring_computed_price = $value['recurring_computed_price'];
				  try{
				  $updateContractLineItemPrice = 'mutation {
					subscriptionDraftLineUpdate(
					  draftId: "'.$contractDraftid.'"
					  lineId: "gid://shopify/SubscriptionLine/'.$lineItemId.'"
					  input: { currentPrice: '.$recurring_computed_price.' }
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
				  $updateContractLine_execution = $this->graphqlQuery($updateContractLineItemPrice,null,null,null);
				  $updateContractLine_execution_error = $updateContractLine_execution['data']['subscriptionDraftLineUpdate']['userErrors'];
					if(!count($updateContractLine_execution_error)){
						$change_after_cycle_update = '1';
					}
				}catch(Exception $e) {
				  return 'error';
				}
			}
			$this->commitContractDraft($contractDraftid);
			return $change_after_cycle_update;
		}

        public function manualBillingAttempt($contractData){
			$billingRenewalDate = $contractData['actualAttemptDate'];
			$whereCondition = array(
				'store_id' => $this->store_id,
				'contract_id' => $contractData['contract_id'],
				'renewal_date' => $billingRenewalDate,
			);
			$check_skip_status = $this->table_row_check('billingAttempts',$whereCondition,'and');

			// get contract data to check the recurring discount applied or not
			$whereContractCondition = array(
				'contract_id' => $contractData['contract_id']
			);
			$fields = array('after_cycle_update','recurring_discount_value','after_cycle','contract_status');
			$get_contract_data = $this->single_row_value('subscriptionOrderContract',$fields,$whereContractCondition,'and','');
            if($check_skip_status == 0 && (!empty($get_contract_data)) && $get_contract_data['contract_status'] == 'A'){
				$contract_product_data = $this->getContractProducts($contractData['contract_id']);
				$contract_products = implode(',',array_column($contract_product_data,'variant_id'));
				$after_cycle_update = '0';
				if($get_contract_data['after_cycle_update'] != '1' && $get_contract_data['recurring_discount_value'] != 0){
					$total_contract_orders = $this->totalContractOrders($contractData['contract_id']);
					if( $total_contract_orders >= $get_contract_data['after_cycle']){
					$after_cycle_update_status = $this->updateLineItemPrice($contract_product_data,$contractData['contract_id']);
						if($after_cycle_update_status != 'error'){
							$after_cycle_update = $after_cycle_update_status;
						}
			    	}
				}else{
					$after_cycle_update = '1';
				}
				$billingAttemptResult =  $this->billingAttemptApi($contractData['contract_id']);
				if($billingAttemptResult != 'error'){
					$currentDate  = date('Y-m-d');
					$created_at = date('Y-m-d H:i:s');
					$contract_id = $contractData['contract_id'];
					$billingAttemptId = $billingAttemptResult['data']['subscriptionBillingAttemptCreate']['subscriptionBillingAttempt']['id'];
					$billingAttempt_id = substr($billingAttemptId, strrpos($billingAttemptId, '/') + 1);

					$fields = array(
						'contract_id' => $contract_id,
						'store_id' => $this->store_id,
						'billingAttemptId' => $billingAttempt_id,
						'billing_attempt_date' => $currentDate,
						'contract_products' => $contract_products,
						'status'=>'Pending',
						'renewal_date'=>$billingRenewalDate,
						'updated_at' => gmdate('Y-m-d H:i:s')
					);
					$this->insert_row('billingAttempts',$fields);

					// if value is active in general setting table NEHAA
					$billingType = $billingAttemptResult['data']['subscriptionBillingAttemptCreate']['subscriptionBillingAttempt']['subscriptionContract']['billingPolicy']['interval'];
					$billingPolicyValue = $billingAttemptResult['data']['subscriptionBillingAttemptCreate']['subscriptionBillingAttempt']['subscriptionContract']['billingPolicy']['intervalCount'];
					$TotalIntervalCount = $this->getBillingRenewalDays(strtoupper($billingType), $billingPolicyValue);
					$newRenewalDate = date('Y-m-d', strtotime('+'.$TotalIntervalCount.' day', strtotime($billingRenewalDate)));

					//update renewal date in subscriptionOrderContract table
					$whereCondition = array(
						'contract_id' => $contract_id,
						'store_id' => $this->store_id
					);
					$fields = array(
						'next_billing_date' => $newRenewalDate,
						'after_cycle_update' => $after_cycle_update,
						'updated_at' => gmdate('Y-m-d H:i:s')
					);
					$update_contract_Renewal = $this->update_row('subscriptionOrderContract',$fields,$whereCondition,'and');
					return json_encode(array("status"=>true,'message'=>'Billing Attempt Created Successfully')); // return json
				}else{
					return json_encode(array("status"=>false,'message'=>'Error in Creating Billing Attempt')); // return json
				}
			}else{
				return json_encode(array("status"=>false,'message'=>"Can't attempt billing")); // return json
			}
		}

		public function getUpcomingFulfillments($contract_id){
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
			$contract_detail = $this->graphqlQuery($get_fulfillment_orders,null,null,null);
			$graphql_order_id = $contract_detail['data']['subscriptionContract']['orders']['edges'][0]['node']['id'];
			$order_name = $contract_detail['data']['subscriptionContract']['orders']['edges'][0]['node']['name'];

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
			$line_items_with_contractId = $this->graphqlQuery($orders_contracts_lineItems,null,null,null);
			foreach($line_items_with_contractId['data']['order']['lineItems']['edges'] as $key=>$value){
				$contract_id = substr($value['node']['contract']['id'], strrpos($value['node']['contract']['id'], '/') + 1);
				$all_contract_line_items[$contract_id][] = substr($value['node']['id'], strrpos($value['node']['id'], '/') + 1);
			}
            $rest_order_id = substr($graphql_order_id, strrpos($graphql_order_id, '/') + 1);
			$order_upcoming_fulfillments = $this->PostPutApi('https://'. $this->store.'/admin/api/'.$this->SHOPIFY_API_VERSION.'/orders/'.$rest_order_id.'/fulfillment_orders.json?status=scheduled','GET',$this->access_token,'');
		    return(array('all_contract_line_items'=>$all_contract_line_items, 'order_upcoming_fulfillments' => $order_upcoming_fulfillments, 'order_name' => $order_name));
		}

     	// get contract orders
		public function getContractOrders($contractId,$cursor){
			if($cursor == ''){
               $get_orders_after = '';
			}else{
               $get_orders_after = 'after : "'.$cursor.'"';
			}
			$getSubscriptionData = 'query {
				subscriptionContract(id : "gid://shopify/SubscriptionContract/'.$contractId.'"){
					createdAt
					orders(first : 1, reverse:true){
						edges{
						cursor
							node{
								id
								name
								fulfillmentOrders(first : 20 query: "status:SCHEDULED"){
									edges{
										node{
											id
											status
											fulfillAt
										}
									}
								}
							}
						}
					}
				}
			}';
			try{
			$getContractOrders = $this->graphqlQuery($getSubscriptionData,null,null,null);
			$fulfillments_array  = $getContractOrders['data']['subscriptionContract']['orders']['edges'][0]['node'];
			return $fulfillments_array;
			}catch(Exception $e) {
				print_r($e->getMessage());
			}
		}

		//get last fulfillment data
		public function getLastFulfillment($orderId){
            $getLastFulfillments = 'query {
					order(id:"'.$orderId.'"){
								id
								name
					fulfillmentOrders(first : 20 reverse : true){
						edges{
							node{
							id
							status
							fulfillAt
							}
						}
				}
			}
			}';
			$graphQL_getLastFulfillments = $this->graphqlQuery($getLastFulfillments,null,null,null);
			return $graphQL_getLastFulfillments['data']['order']['fulfillmentOrders']['edges'][0]['node']['fulfillAt'];

		}

		//  Reschedule orders
		public function rescheduleOrder($scheduleData){
			if(is_object($scheduleData)){
				$scheduleData = (array) $scheduleData;
			}
			$check_customer_Setting = $this->getSettingData('customer_settings', 'skip_upcoming_fulfillment');
			if($check_customer_Setting['skip_upcoming_fulfillment'] == '1' || $scheduleData['rescheduled_by'] == 'Admin'){
			// check if the fulfillment already rescheduled or not
			$whereFulfillmentCondition = array(
			  'actual_fulfillment_date' => strtok($scheduleData['actualFulfillmentDate'], 'T'),
			  'contract_id' => $scheduleData['contract_id']
			);
			$check_reschedule_entry = $this->table_row_check('reschedule_fulfillment',$whereFulfillmentCondition,'and');
			$whereContractid = array(
				'contract_id' => $scheduleData['contract_id']
			);
			$check_contract_exist = $this->table_row_check('subscriptionOrderContract',$whereContractid,'and');
            if($check_reschedule_entry || (!($check_contract_exist))){
				echo json_encode(array("status"=>false,'message'=>'Something went wrong'));
			}else{
			 $TotalIntervalCount = $this->getBillingRenewalDays(strtoupper($scheduleData['delivery_billing_type']), $scheduleData['deliveryCycle']);
			$whereCondition = array(
              'contract_id' => $scheduleData['contract_id']
			);
			$fields = array('next_billing_date');
			$order_renewal_date = $this->table_row_value('subscriptionOrderContract',$fields,$whereCondition,'and','');
			$next_schedule_date = $order_renewal_date[0]['next_billing_date'];

			$updated_renewal_date = date('Y-m-d', strtotime('+'.$TotalIntervalCount.' day', strtotime($next_schedule_date)));
			try{
				$rescheduleFulfillments = 'mutation {
					fulfillmentOrderReschedule(
					id: "'.$scheduleData['fulfillmentOrderId'].'",
					fulfillAt: "'.$next_schedule_date.'"
					)
					{
					fulfillmentOrder {
						fulfillAt
					}
					userErrors {
						field
						message
					}
					}
				}';
				$graphQL_rescheduleFulfillments = $this->graphqlQuery($rescheduleFulfillments,null,null,null);
				$rescheduleFulfillments_error = $graphQL_rescheduleFulfillments['data']['fulfillmentOrderReschedule']['userErrors'];
					if(!count($rescheduleFulfillments_error)){
						//save fulfillment data in db table reschedule_fulfillment
						$whereCondition = array(
						'contract_id' => $scheduleData['contract_id'],
						'fulfillment_orderId' =>  substr($scheduleData['fulfillmentOrderId'], strrpos($scheduleData['fulfillmentOrderId'], '/') + 1),
						'order_id' => substr($scheduleData['order_id'], strrpos($scheduleData['order_id'], '/') + 1)
						);
						$check_entry = $this->table_row_check('reschedule_fulfillment',$whereCondition,'and');
						if($check_entry){
							$fields = array(
								'new_fulfillment_date' => $next_schedule_date,
							);
						$rescheduled_fulfillment =	$this->update_row('reschedule_fulfillment',$fields,$whereCondition,'and');
						}else{
								$fields = array(
									'store_id' => $this->store_id,
									'contract_id' => $scheduleData['contract_id'],
									'fulfillment_orderId' => substr($scheduleData['fulfillmentOrderId'], strrpos($scheduleData['fulfillmentOrderId'], '/') + 1),
									'order_id' => substr($scheduleData['order_id'], strrpos($scheduleData['order_id'], '/') + 1),
									'order_no' => (int) filter_var($scheduleData['order_no'], FILTER_SANITIZE_NUMBER_INT),
									'actual_fulfillment_date' =>  strtok($scheduleData['actualFulfillmentDate'], 'T'),
									'new_fulfillment_date' => $next_schedule_date
								);
								$this->insert_row('reschedule_fulfillment',$fields);
								$rescheduled_fulfillment =	$this->update_row('reschedule_fulfillment',$fields,$whereCondition,'and');
							}
							if($rescheduled_fulfillment){
								$update_renewal_date = $this->setRenewalDate($scheduleData['contract_id'],$updated_renewal_date);
								if($update_renewal_date['status'] == false){
									echo json_encode(array("status"=>false,'message'=>'Fulfillment is Rescheduled but renewal date is not updated'));
								}else{
									$fields = array('shop_timezone');
									$where_store_condition = array('store_id' => $this->store_id);
									$get_store_timezone = $this->table_row_value('store_details',$fields,$where_store_condition,'and','');
									$shop_timezone = $get_store_timezone[0]['shop_timezone'];
									$get_fields = 'admin_reschedule_fulfillment,customer_reschedule_fulfillment';
									$reschedule_fulfillment = $this->getSettingData('email_notification_setting',$get_fields);
									$sendMailTo = '';
									if($reschedule_fulfillment['admin_reschedule_fulfillment'] == '1' && $reschedule_fulfillment['customer_reschedule_fulfillment'] == '1'){
                                        $sendMailTo = array($scheduleData['customerEmail'],$scheduleData['adminEmail']);
									}else if($reschedule_fulfillment['admin_reschedule_fulfillment'] != '1' && $reschedule_fulfillment['customer_reschedule_fulfillment'] == '1'){
                                        $sendMailTo = $scheduleData['customerEmail'];
									}else if($reschedule_fulfillment['admin_reschedule_fulfillment'] == '1' && $reschedule_fulfillment['customer_reschedule_fulfillment'] != '1'){
										$sendMailTo = $scheduleData['adminEmail'];
									}
									if($sendMailTo != ''){
										$data = array(
											'template_type' => 'reschedule_fulfillment_template',
											'contract_id' => $scheduleData['contract_id'],
											'contract_details' => $scheduleData['specific_contract_data'][0],
											'contract_product_details' => $scheduleData['contract_product_details'],
											'actual_fulfillment_date' =>   date('d M Y', strtotime(strtok($scheduleData['actualFulfillmentDate'], 'T'))),
											'new_scheduled_date' => $this->getShopTimezoneDate($next_schedule_date,$shop_timezone),
										);
										$email_template_data = $this->email_template($data,'send_dynamic_email');
										if($email_template_data['template_type'] != 'none'){
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
												$contract_deleted_mail = $this->sendMail($sendMailArray,'false');
											}catch(Exception $e) {
												if($this->store == 'predictive-search.myshopify.com'){
													echo 'mail is not sent.';
												}
											}
										}
									}
								}
							}
							echo json_encode(array("status"=>true,'message'=>'Fulfillment is Rescheduled'));
					}
				}catch(Exception $e) {
					echo json_encode(array("status"=>false,'message'=>$e->getMessage()));
				}
			}
		}else{
			echo json_encode(array("status"=>false,'message'=>'Rescheduled setting has been disabled'));
		}
		}

        public function setRenewalDate($contract_id,$renewal_date){
			$graphQL_rescheduleFulfillments= 'mutation {
				subscriptionContractSetNextBillingDate(
				contractId: "gid://shopify/SubscriptionContract/'.$contract_id.'"
				date: "'.$renewal_date.'"
				) {
				contract {
					nextBillingDate
				}
				userErrors {
					field
					message
				}
				}
			}';
			$graphQL_rescheduleFulfillments_execution = $this->graphqlQuery($graphQL_rescheduleFulfillments,null,null,null);
			$reschedule_error = $graphQL_rescheduleFulfillments_execution['data']['subscriptionContractSetNextBillingDate']['userErrors'];
			if(!count($reschedule_error)){
				$whereCondition = array(
					'contract_id' => $contract_id
				);
                 $fields = array(
                    'next_billing_date' => $renewal_date,
					'updated_at' => gmdate('Y-m-d H:i:s')
				 );
				$rescheduled_fulfillment =	$this->update_row('subscriptionOrderContract',$fields,$whereCondition,'and');
                return array("status"=>true,'message'=>'Renewal Date Updated');
			}else{
				return array("status"=>false,'message'=>'Error in updating renewal date');
			}
		}

		// skip contract order
		public function skipContractOrder($contractId,$skipOrderDate,$billingPolicy,$delivery_billingType,$customerEmail,$adminEmail,$skippedFrom,$contract_details,$contract_product_details){
		//check if setting changed by the admin for customer contract page
		$check_skip_setting = $this->getSettingData('customer_settings','skip_upcoming_order');
		if($skippedFrom == 'Admin' || $check_skip_setting['skip_upcoming_order'] == '1'){
        $where_condition = array(
			'contract_id'=>$contractId,
			'store_id' => $this->store_id,
			'renewal_date' => $skipOrderDate,
		);
		$check_attempt_entry =  $this->table_row_check('billingAttempts',$where_condition,'and');
        if($check_attempt_entry == 0){
         $fields = array(
			'contract_id' => $contractId,
			'store_id' => $this->store_id,
			'billing_attempt_date' => date('Y-m-d'),
			'renewal_date' => $skipOrderDate,
			'status' => 'Skip',
			'updated_at' => gmdate('Y-m-d H:i:s')
		 );
		 $skipOrderData = $this->insert_row('billingAttempts',$fields);
		 $next_billing_date = date('Y-m-d', strtotime('+'.$billingPolicy.' '.$delivery_billingType, strtotime($skipOrderDate)));
		 $whereCondition = array(
			 'contract_id'=>$contractId
		 );
		 $fields = array(
			'next_billing_date'	=> $next_billing_date,
			'updated_at' => gmdate('Y-m-d H:i:s')
		 );
		 $update_next_billing_date = $this->update_row('subscriptionOrderContract',$fields,$whereCondition,'and');

		 if($skipOrderData && $update_next_billing_date){
			//send mail to customer and admin
			//send mail to customer and admin
			$get_fields = 'customer_skip_order,admin_skip_order';
			$skip_order_mail = $this->getSettingData('email_notification_setting',$get_fields);
			$send_mail_to = '';
			if($skip_order_mail['customer_skip_order'] == '1' && $skip_order_mail['admin_skip_order'] == '1'){
               $send_mail_to = array($customerEmail,$adminEmail);
			}else if($skip_order_mail['customer_skip_order'] != '1' && $skip_order_mail['admin_skip_order'] == '1'){
				$send_mail_to = $adminEmail;
			}else if($skip_order_mail['customer_skip_order'] == '1' && $skip_order_mail['admin_skip_order'] != '1'){
				$send_mail_to = $customerEmail;
			}
			if($send_mail_to != ''){
				$data = array(
					'template_type' => 'skip_order_template',
					'contract_id' => $contractId,
					'contract_details' => $contract_details[0],
					'contract_product_details' => $contract_product_details,
					'skipped_order_date' => $skipOrderDate
				);
				$email_template_data = $this->email_template($data,'send_dynamic_email');
				if($email_template_data['template_type'] != 'none'){
					$sendMailArray = array(
						'sendTo' =>  $send_mail_to,
						'subject' => $email_template_data['email_subject'],
						'mailBody' => $email_template_data['test_email_content'],
						'mailHeading' => '',
						'ccc_email' => $email_template_data['ccc_email'],
						'bcc_email' =>  $email_template_data['bcc_email'],
						// 'from_email' =>  $from_email,
						'reply_to' => $email_template_data['reply_to']
					);
					try{
						$order_skipped_mail = $this->sendMail($sendMailArray,'false');
					}catch(Exception $e) {
					}
				}
		    }
            return json_encode(array("status"=>true,'message'=>'Order is skipped'));
		 }else{
            return json_encode(array("status"=>false,'message'=>'something went wrong'));
		 }
		}else{
			return json_encode(array("status"=>false,'message'=>'something went wrong'));
		}
	}else{
		return json_encode(array("status"=>false,'message'=>'Setting for skip upcoming order has been disabled'));
	}
		}

    public function getContractPaymentToken($contract_id){
		$get_customer_payment_method = '{
			subscriptionContract(id: "gid://shopify/SubscriptionContract/'.trim($contract_id).'"){

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
		$get_contract_payment_execution =  $this->graphqlQuery($get_customer_payment_method,null,null,null);
		return $get_contract_payment_execution;

	}

		//get customer contract using contract_id
		public function getContractPaymentToken_old($contract_id){
	   $get_contract_payment = '{
			subscriptionContract(id: "gid://shopify/SubscriptionContract/'.trim($contract_id).'"){
			customerPaymentMethod{
				id
			}
			}
		}';
		$get_contract_payment_execution =  $this->graphqlQuery($get_contract_payment,null,null,null);
		return $get_contract_payment_execution;
		}

		public function getCurrentUtcTime(){
			return gmdate("Y-m-d H:i:s");
		}


		public function getBillingRenewalDays($billingType, $billingPolicyValue){
			if($billingType == 'DAY'){
			  $TotalIntervalCount =  $billingPolicyValue;
			  }else if($billingType == 'WEEK'){
				$TotalIntervalCount = (7 *  $billingPolicyValue);
			  }else if($billingType == 'MONTH'){
				$TotalIntervalCount = (30 *  $billingPolicyValue);
			  }else if($billingType == 'YEAR'){
				$TotalIntervalCount = (365 *  $billingPolicyValue);
			  }
			  return $TotalIntervalCount;
		}

		//get active theme id
		public function getActiveTheme(){
			$graphQL = "query{
				translatableResources(first:1, resourceType:ONLINE_STORE_THEME ){
				edges{
					node{
					resourceId
					}
				}
				}
			}
			";
			$activeThemeArray = $this->graphqlQuery($graphQL,null,null,null);
			$ThemeGraphqlId = $activeThemeArray["data"]["translatableResources"]["edges"][0]["node"]["resourceId"];
			return substr($ThemeGraphqlId, strrpos($ThemeGraphqlId, "/") + 1);
		}

		//check weather the app is enabled or disabled from the customizer
		public function checkAppStatus(){
			$app_status = 'false';
			$themeId = $this->getActiveTheme();
			$get_theme_files = $this->PostPutApi('https://'. $this->store.'/admin/api/'.$this->SHOPIFY_API_VERSION.'/themes/'.$themeId.'/assets.json?asset[key]=config/settings_data.json','GET',$this->access_token,'');
			$theme_extension_block = json_decode($get_theme_files['asset']['value']);
			if($theme_extension_block->current){
				if($theme_extension_block->current->blocks){
					$current_blocks = $theme_extension_block->current->blocks;
					$searchedValue = 'shopify://apps/'.$this->app_name.'/blocks/'.$this->theme_block_name.'/'.$this->app_extension_id;
					foreach($current_blocks as $app_block){
						if($app_block->type == $searchedValue){
							$app_status =  $app_block->disabled;
						}
					}
					if($app_status){
				    	return 'false';
					}else{
				    	return 'true';
					}
				}else{
					return 'false';
				}
		   }else{
			return 'false';
		   }
		}

		public function testMailIssue(){
			$dirPath =dirname(dirname(__DIR__));
			$message = "The message..." . "\n";
			$myfile="txtFiles/testMail.txt";
			file_put_contents($myfile, $message, FILE_APPEND | LOCK_EX);
		}

		// this function using in  subscription_billing_attempt_success webhook
		public function getOrderData($order_id){
			$get_order_number = '{
			   order(id: "gid://shopify/Order/'.$order_id.'"){
				 name
				 subtotalPriceSet{
				   shopMoney{
					 amount
				   }
				 }
			   }
			}';
			$get_order_number_execution = $this->graphqlQuery($get_order_number,null,null,null);
			return $get_order_number_execution;
		}

    // send mail function
	public function sendMail($sendMailArray,$testMode){
		//general mail configuration
        $email_configuration = 'false';
        $email_host = "your-email-host";
        $username = "apikey";
        $password = "email_password";
        $from_email = "your-from-email";
        $encryption = 'tls';
        $port_number = 587;

		//For pending mail
		if (array_key_exists("store_id",$sendMailArray)){
			$store_id = $sendMailArray['store_id'];
		}else{
			$store_id = $this->store_id;
		}
		$store_detail = $this->customQuery("SELECT store_email,shop_name FROM store_details WHERE store_id = '$store_id'");

		 if($testMode == 'true'){ // it testing the mail configuration setting
			$email_host = $sendMailArray['dataValues']['email_host'];
			$username = $sendMailArray['dataValues']['username'];
			$password = $sendMailArray['dataValues']['password'];
			$from_email = $sendMailArray['dataValues']['from_email'];
			$encryption = $sendMailArray['dataValues']['encryption'];
			$port_number = $sendMailArray['dataValues']['port_number'];
			$subject = $sendMailArray['dataValues']['subject'];
			$sendTo = $sendMailArray['dataValues']['sendTo'];
			$mailBody = $sendMailArray['dataValues']['mailBody'];
			$mailHeading = '';
			$email_template_body = $mailBody;
		}else{ // check if the email configuration setting exist and email enable is checked
			$subject = $sendMailArray['subject'];
			$sendTo = $sendMailArray['sendTo'];
			$mailBody = $sendMailArray['mailBody'];
			$mailHeading = $sendMailArray['mailHeading'];
			$whereCondition = array(
				'store_id' => $this->store_id
			);
			$email_configuration_data = $this->table_row_value('email_configuration','all',$whereCondition,'and','');
			if($email_configuration_data){
				if($email_configuration_data[0]['email_enable'] == 'checked'){
					$email_host = $email_configuration_data[0]['email_host'];
					$username = $email_configuration_data[0]['username'];
					$password = $email_configuration_data[0]['password'];
					$from_email = $email_configuration_data[0]['from_email'];
					$encryption = $email_configuration_data[0]['encryption'];
					$port_number = $email_configuration_data[0]['port_number'];
					$email_configuration = 'true';
				}
			}
			if($mailHeading == ''){
				$email_template_body = $mailBody;
			}else{
			    $email_template_body = $this->email_templates($mailBody,$mailHeading);
			}
		}

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
		if($sendMailArray['ccc_email']){
			$mail->addCC($sendMailArray['ccc_email']);
		}
		if($sendMailArray['bcc_email']){
			$mail->addBCC($sendMailArray['bcc_email']);
		}

		if((($email_configuration_data) && ($email_configuration_data[0]['email_enable'] == 'checked')) || $testMode == 'true'){
			$mail->SetFrom($username,$from_email);
		}else{
			$mail->SetFrom($from_email);
		}
		//Set Params
		if($sendMailArray['reply_to']){
			$mail->addReplyTo($sendMailArray['reply_to']);
		}else{
			$mail->addReplyTo($store_detail[0]['store_email'], $store_detail[0]['shop_name']);
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
		if($testMode == 'sendInvoice'){
			$mail->addAttachment($sendMailArray['mailAttachment']);
		}
		if(!$mail->Send()) {
			return json_encode(array("status"=>false, "message"=>$mail->ErrorInfo));
		} else {
			// if($email_configuration == 'false'){
			// 	$whereCondition = array('store_id'=>$store_id);
			// 	$pending_emails = ($pending_emails - $decrease_counter);
			// 	$fields = array(
            //      'pending_emails' => $pending_emails
			// 	);
			// 	$this->update_row('email_counter',$fields,$whereCondition,'and');
			// }
			return json_encode(array("status"=>true, "message"=>'Email Sent Successfully'));
		}
	}

    public function email_templates($email_body,$email_heading){
		//get data from email settings table
		$whereStoreCondition = array(
			'store_id' => $this->store_id
		);
		$email_Settings_data = $this->table_row_value('email_settings','all',$whereStoreCondition,'and','');
			$logo_url = $this->image_folder.'logo.png';
			$footer_text = 'Thank You';
			$social_link_html = '';

			if(!empty($email_Settings_data)){
                if($email_Settings_data[0]['footer_text'] != ''){
					$footer_text = $email_Settings_data[0]['footer_text'];
				}
				if($email_Settings_data[0]['logo_url'] != ''){
					$logo_url = $email_Settings_data[0]['logo_url'];
				}
			   	if($email_Settings_data[0]['enable_social_link'] == '1'){
					if($email_Settings_data[0]['facebook_link'] != ''){
						$social_link_html .= '<li class="fb_contents" style="list-style: none;margin: 0 10px;display:inline-block;"><a href="'.$email_Settings_data[0]['facebook_link'].'" target="_blank" style="color:#3c5996;background: #f6f9ff;border: 1px solid #f2f2f2;border-radius: 50%;width: 35px;height: 35px;display: inline-block;text-align: center;line-height:40px;"><img width="15px" height="15px" style="margin-top:6px;" src="https://lh3.googleusercontent.com/-e2x3nBYmZfk/X7dtRSyslDI/AAAAAAAAB0M/KTW6KLFg6eEzbpKaZXcXAvhjiIJoOBJUQCK8BGAsYHg/s64/2020-11-19.png" class="uqvYjb KgFPz" alt="" aria-label="Picture. Press Enter to open it in a new page."></i></a></li>';
					}
					if($email_Settings_data[0]['twitter_link'] != ''){
						$social_link_html .= '<li class="tw_contents" style="list-style: none;margin: 0 10px;display:inline-block;"><a href="'.$email_Settings_data[0]['twitter_link'].'" target="_blank" style="color:#58acec;background: #f6f9ff;border: 1px solid #f2f2f2;border-radius: 50%;width: 35px;height: 35px;display: inline-block;text-align: center;line-height:40px;"><img width="15px" height="15px" style="margin-top:6px;" src="https://lh3.googleusercontent.com/-hmg-zXw5RG0/X7dtSbfpWcI/AAAAAAAAB0Q/3twwSmDKpMsqDo8eSKAID8X8k4olFidsACK8BGAsYHg/s64/2020-11-19.png" class="uqvYjb KgFPz" alt="" aria-label="Picture. Press Enter to open it in a new page."></a></li>';
					}
					if($email_Settings_data[0]['instagram_link'] != ''){
						$social_link_html .= '<li class="ins_contents" style="list-style: none;margin: 0 10px;display:inline-block;"><a href="'.$email_Settings_data[0]['instagram_link'].'" target="_blank" style="color:#db4d45;background: #f6f9ff;border: 1px solid #f2f2f2;border-radius: 50%;width: 35px;height: 35px;display: inline-block;text-align: center;line-height:40px;"><img width="15px" height="15px" style="margin-top:6px;" src="https://lh3.googleusercontent.com/-DdrdoKZW5dA/X7dtTOJjmZI/AAAAAAAAB0U/jxIyk80qIG81JptOG_c9zHF7MgIrPpGrQCK8BGAsYHg/s64/2020-11-19.png" class="uqvYjb KgFPz" alt="" aria-label="Picture. Press Enter to open it in a new page."></i></a></li>';
					}
					if($email_Settings_data[0]['linkedin_link'] != ''){
						$social_link_html .= '<li class="linkedin_contents" style="list-style: none;margin: 0 10px; display:inline-block;"><a href="'.$email_Settings_data[0]['linkedin_link'].'" target="_blank" style="color:#0e7ab7;background: #f6f9ff;border: 1px solid #f2f2f2;border-radius: 50%;width: 35px;height: 35px;display: inline-block;text-align: center;line-height:40px;"><img width="15px"style="margin-top:6px;" height="15px" src="https://lh3.googleusercontent.com/-7Fkye-Jqt-c/X7dtT-C4GFI/AAAAAAAAB0Y/OEf5Fp97T6AO-v8sRbs7cpF-p5l_C_RAACK8BGAsYHg/s64/2020-11-19.png" class="uqvYjb KgFPz" alt="" aria-label="Picture. Press Enter to open it in a new page."></i></a></li>';
					}
				}
			}
		// }
		$email_template_body = '<center class="wrapper" data-link-color="#1188E6" data-body-style="font-size:14px; font-family:inherit; color:#000000; background-color:#f3f3f3;">
		<div class="webkit">
		   <table cellpadding="0" cellspacing="0" border="0" width="100%" class="wrapper" bgcolor="#f3f3f3">
			  <tbody><tr>
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
                                                            '.$email_body.'
														   <div data-role="module-unsubscribe" class="module" role="module" data-type="unsubscribe" style="color:#444444; font-size:12px; line-height:20px; padding:16px 16px 16px 16px; text-align:Center;" data-muid="4e838cf3-9892-4a6d-94d6-170e474d21e5">
															  <p style="font-size:12px; line-height:20px;"><a href="https://'.$this->store.'" target="_blank" class="Unsubscribe--unsubscribePreferences" style="">'.$this->store.'</a></p>
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
		   </tbody></table>
		</div>
	 </center>';
	 return $email_template_body;
	}

	public function delete_subscription($ids){
		$sellingPlanGroupId = $ids['subscription_plangroup_id'];
		try{
		$graphQL_sellingPlanGroupDelete = 'mutation {
			sellingPlanGroupDelete(id: "gid://shopify/SellingPlanGroup/'.$sellingPlanGroupId.'") {
			deletedSellingPlanGroupId
				userErrors {
					code
					field
					message
				}
			}
		}';
		$sellingPlanGroupDeleteapi_execution = $this->graphqlQuery($graphQL_sellingPlanGroupDelete,null,null,null);
		}
		catch(Exception $e) {
		return json_encode(array("status"=>false,'error'=>$e->getMessage())); // return json
		}
		$sellingPlanGroupDeleteapi_error = $sellingPlanGroupDeleteapi_execution['data']['sellingPlanGroupDelete']['userErrors'];
		if(!count($sellingPlanGroupDeleteapi_error)){
			$response = json_decode($this->delete_row($_REQUEST['table'],$_REQUEST['wherecondition'],$_REQUEST['wheremode']));
			if($response->status == true){
			return json_encode(array('status'=>true,'message'=>"Deleted Successfully")); // return json
			}else{
			return json_encode(array('status'=>false,'message'=>"Database delete query error")); // return json
			}
		}else{
			if($sellingPlanGroupDeleteapi_error[0]['code'] == 'GROUP_DOES_NOT_EXIST'){
				try{
					$response = json_decode($this->delete_row($_REQUEST['table'],$_REQUEST['wherecondition'],$_REQUEST['wheremode']));
					if($response->status == true){
						return json_encode(array('status'=>true,'message'=>"Deleted Successfully")); // return json
					}else{
						return json_encode(array("status"=>false,'error'=>$sellingPlanGroupDeleteapi_error,'message'=>'404')); // return json
					}
			    }catch(Exception $e){
					return json_encode(array("status"=>false,'error'=>$sellingPlanGroupDeleteapi_error,'message'=>'404'));
				}
			}else{
				return json_encode(array("status"=>false,'error'=>$sellingPlanGroupDeleteapi_error,'message'=>"Delete mutation error")); // return json
			}
		}
	}

    // public function getSettingData($tableName,$column_name){
	// 	$whereStoreCondition = array(
	// 		'store_id' => $this->store_id
	// 	);
	// 	$fields = array($column_name);
	// 	$get_setting_data = $this->table_row_value($tableName,$fields,$whereStoreCondition,'and','');
	// 	return $get_setting_data[0][$column_name];
	// }
	public function getSettingData($tableName,$column_name){
		$whereStoreCondition = array(
			'store_id' => $this->store_id
		);
		$fields = array($column_name);
		$get_setting_data = $this->single_row_value($tableName,$fields,$whereStoreCondition,'and','');
		// return $get_setting_data[0][$column_name];
		return $get_setting_data;
	}


	public function PostPutApi($url, $action, $access_token, $arrayfield)
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

	public function getShopTimezoneDateFormat($date,$shop_timezone){
		$dt = new DateTime($date);
		$tz = new DateTimeZone($shop_timezone); // or whatever zone you're after
		$dt->setTimezone($tz);
		$dateTime = $dt->format('Y-m-d');
		return $dateTime; //use in analytics
	}

	public function getShopTimezoneDate($date,$shop_timezone){
		$dt = new DateTime($date);
		$tz = new DateTimeZone($shop_timezone); // or whatever zone you're after
		$dt->setTimezone($tz);
		$dateTime = $dt->format('Y-m-d H:i:s');
		$shopify_date =  date("d M Y", strtotime($dateTime));
		return $shopify_date;
	}

	//   bone custom work function     

	public function getReplaceAllProducts($newProductObj, $oldData, $all_contractIds, $contract_details)
	{
		// print_r($contract_details);
		$added_by = $deleted_from = 'Admin';
		$check_customer_setting = $this->getSettingData('customer_settings', 'add_subscription_product');
		$all_new_contract_products = [];
		$error_Count =0;
		if ($check_customer_setting['add_subscription_product'] == '1' || $added_by == 'Admin') {
			foreach ($all_contractIds as $c_id) {
			
				$draft_id = $this->getContractDraftId($c_id);
				$error_Count = 0;

				$value = $newProductObj[0];
				// foreach ($newProductObj as $value) {
					$whereCondition = ['contract_id' => $c_id];
					$get_contract_data = $this->single_row_value('subscriptionOrderContract', ['discount_type', 'discount_value', 'recurring_discount_type', 'recurring_discount_value', 'selling_plan_id', 'after_cycle', 'billing_policy_value', 'delivery_policy_value', 'after_cycle_update'], $whereCondition, 'and', '');

					$price = $value['price'] * ($get_contract_data['billing_policy_value'] / $get_contract_data['delivery_policy_value']);
					$computedPrice = 0;

					if ($get_contract_data['discount_value'] != 0 || $get_contract_data['recurring_discount_value'] != 0) {
						if ($get_contract_data['discount_type'] == 'A') {
							$price = max(0, $price - $get_contract_data['discount_value']);
						} else if ($get_contract_data['discount_type'] == 'P') {
							$price = $price - ($price * ($get_contract_data['discount_value'] / 100));
						}

						if ($get_contract_data['recurring_discount_value'] != 0) {
							if ($get_contract_data['recurring_discount_type'] == 'A') {
								$adjustmentType = 'FIXED_AMOUNT';
								$fixedorpercntValue = "fixedValue:" . $get_contract_data['recurring_discount_value'];
								$computedPrice = max(0, $value['price'] - $get_contract_data['recurring_discount_value']);
							} else {
								$adjustmentType = 'PERCENTAGE';
								$fixedorpercntValue = "percentage:" . $get_contract_data['recurring_discount_value'];
								$computedPrice = $value['price'] - ($value['price'] * ($get_contract_data['recurring_discount_value'] / 100));
							}
							$cycleDiscount = 'cycleDiscounts : {
													 adjustmentType: ' . $adjustmentType . ',
													 adjustmentValue:  {
														 ' . $fixedorpercntValue . '
													 },
													 afterCycle: ' . $get_contract_data['after_cycle'] . ',
													 computedPrice:' . $computedPrice . '
												 }';
							$pricingPolicy = 'pricingPolicy :{
													 basePrice : ' . $price . ',
													 ' . $cycleDiscount . '
												 }';
							if ($get_contract_data['after_cycle_update'] == '1') {
								$price = $computedPrice;
							}
						} else {
							$pricingPolicy = '';
						}
					} else {
						$pricingPolicy = '';
					}

					try {
						$addNewLineItem = 'mutation{
												 subscriptionDraftLineAdd(
													 draftId: "' . $draft_id . '",
													 input: {
														 productVariantId: "gid://shopify/ProductVariant/' . $value['variant_id'] . '",
														 quantity: ' . $value['quantity'] . ',
														 currentPrice: ' . number_format((float)$price, 2, '.', '') . ',
														 ' . $pricingPolicy . '
													 }
												 ) {
													 lineAdded {
														 id
													 },
													 userErrors {
														 field
														 message
													 }
												 }
											 }';

						$addNewLineItem_execution = $this->graphqlQuery($addNewLineItem, null, null, null);
						$addNewLineItem_error = $addNewLineItem_execution['data']['subscriptionDraftLineAdd']['userErrors'];
					} catch (Exception $e) {
						return json_encode(["status" => false, 'message' => 'Something went wrong']);
					}
					// print_r($addNewLineItem_error);

					try{
						if (!count($addNewLineItem_error)) {
							$AddLineItemId = substr($addNewLineItem_execution['data']['subscriptionDraftLineAdd']['lineAdded']['id'], strrpos($addNewLineItem_execution['data']['subscriptionDraftLineAdd']['lineAdded']['id'], '/') + 1);
							$fields = [
								"store_id" => $this->store_id,
								"contract_id" => $c_id,
								"product_id" => $value['product_id'],
								"variant_id" => $value['variant_id'],
								"product_name" => $value['product_title'],
								"variant_name" => str_replace('"', '',$value['variant_title']),
								"subscription_price" => number_format((float)$price, 2, '.', ''),
								'quantity' => $value['quantity'],
								"contract_line_item_id" => $AddLineItemId,
								"recurring_computed_price" => number_format((float)$computedPrice, 2, '.', ''),
								"variant_image" => $value['image'],
								"variant_sku" => $value['sku'],
							];
							$insert_row = $this->insert_row('subscritionOrderContractProductDetails', $fields);
							
							array_push($all_new_contract_products, $fields);
						} else {
							$addNewLineItem_execution_error = $addNewLineItem_execution['data']['subscriptionDraftLineAdd']['userErrors'];
							echo 'Error in --> $addNewLineItem_execution_error';
							$error_Count=1;
						}
					} catch (Exception $e) {
						echo $e->getMessage();
						return json_encode(["status" => false, 'message' => 'Something went wrong']);
					}


					$oldDataByContactId = $oldData[$c_id];
					foreach ($oldDataByContactId as $data) {
						$lineId = $data['contract_line_item_id'];
						$oldProductsdelete = $this->bonneRemoveSubscriptionProduct($draft_id, $lineId, $deleted_from);
					}
					$updateNextBillingDate = $this->updateNextBillingDate($draft_id);
					$newProductsAdded = $this->bonneCommitContractDraft($draft_id);
					$contract_fields = array(
						'next_billing_date' => $this->boneCycleCustomDate(),
					);
					$whereContractCondition = array(
						'contract_id' => $c_id
					);
					$this->update_row('subscriptionOrderContract', $contract_fields, $whereContractCondition, 'and');
					if ($newProductsAdded == 'Success') {
						$send_mail_to = '';
						$product_added_mail = $this->getSettingData('email_notification_setting', 'customer_product_added,admin_product_added');
						// print_r($product_added_mail,'1');
						if ($product_added_mail['customer_product_added'] == '1' && $product_added_mail['admin_product_added'] == '1') {
							$send_mail_to = array($contract_details[$c_id][0]['email'], $contract_details[$c_id][0]['store_email']);
						} else if ($product_added_mail['customer_product_added'] == '1' && $product_added_mail['admin_product_added'] != '1') {
							$send_mail_to = $contract_details[$c_id][0]['email'];
						} else if ($product_added_mail['customer_product_added'] != '1' && $product_added_mail['admin_product_added'] == '1') {
							$send_mail_to = $contract_details[$c_id][0]['store_email'];
						}
						if ($send_mail_to != '') {
							$data = array(
								'template_type' => 'product_added_template',
								'contract_id' => $c_id,
								'contract_details' => $contract_details[$c_id][0],
								'contract_product_details' => '',
								'new_added_products' => $all_new_contract_products
							);
							$email_template_data = $this->email_template($data, 'send_dynamic_email');

							// just for testing........

							// $send_mail_to = array('kawal13@yopmail.com', $contract_details[$c_id][0]['store_email']);

							////////////////////////////


							if ($email_template_data['template_type'] != 'none') {
								$sendMailArray = array(
									'sendTo' =>  $send_mail_to,
									'subject' => $email_template_data['email_subject'],
									'mailBody' => $email_template_data['test_email_content'],
									'mailHeading' => '',
									'ccc_email' => $email_template_data['ccc_email'],
									'bcc_email' =>  $email_template_data['bcc_email'],
									// 'from_email' =>  $from_email,
									'reply_to' => $email_template_data['reply_to']
								);
								try {
									// print_r($sendMailArray);
									$contract_deleted_mail = $this->sendMail($sendMailArray, 'false');
									// echo 'hello1<br>';
									// print_r($contract_deleted_mail);
								} catch (Exception $e) {
								}
							}
						}
					} 
			}

			if($newProductsAdded == 'Success'){
					return json_encode(["status" => true, 'message' => 'Product(s) Replace Successfully']);
			} else {
				return json_encode(["status" => false, 'message' => 'Something went wrong']);
			}
		} else {
			return json_encode(["status" => false, 'message' => 'Add Product Setting has been disabled']);
		}
	}
	public function bonneRemoveSubscriptionProduct($contract_draftId, $lineId, $deleted_from)
	{
		// Check if customer can delete product or not
		$check_customer_setting = $this->getSettingData('customer_settings', 'delete_product');

		if ($check_customer_setting['delete_product'] == '1' || $deleted_from == 'Admin') {
	
			// $contract_draftId = $this->getContractDraftId($contractId);
			try {
				$removeProduct = 'mutation {
                subscriptionDraftLineRemove(
                    draftId: "' . $contract_draftId . '"
                    lineId: "gid://shopify/SubscriptionLine/' . $lineId . '"
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

				$removeProduct_execution = $this->graphqlQuery($removeProduct, null, null, null);
				$removeProduct_error = $removeProduct_execution['data']['subscriptionDraftLineRemove']['userErrors'];
				// $commitContractChanges = $this->bonneCommitContractDraft($contract_draftId);
				$fields = array(
					'product_contract_status' => '0'
				);
				$whereCondition = array(
					'contract_line_item_id' => $lineId
				);
				$this->update_row('subscritionOrderContractProductDetails', $fields, $whereCondition, 'and');

			} catch (Exception $e) {
				return json_encode(["status" => false, 'error' => $e->getMessage(), 'message' => 'Product Removed error']);
			}

			if (!count($removeProduct_error)) {
				
			} else {
				// return json_encode(["status" => false, 'message' => $removeProduct_error[0]['message']]);
			}
		} else {
			return json_encode(["status" => false, 'message' => 'Delete Product Setting has been disabled']);
		}
	}
	public function bonneCommitContractDraft($subscription_draft_contract_id)
	{
		try {
			// Construct GraphQL mutation for committing contract draft
			$updateContractStatus = 'mutation {
				subscriptionDraftCommit(draftId: "' . $subscription_draft_contract_id . '") {
					contract {
						id
						status
						lines(first:50) {
							edges {
								node {
									id
									quantity
									title
									productId
									variantId
									variantTitle
									currentPrice {
										amount
									}
									discountAllocations {
										discount {
											__typename
										}
									}
								}
							}
						}
					}
					userErrors {
						field
						message
					}
				}
			}';

			// Execute GraphQL mutation
			$commitContractStatus_execution = $this->graphqlQuery($updateContractStatus, null, null, null);

			// Check for GraphQL user errors
			$commitContractStatus_execution_errors = $commitContractStatus_execution['data']['subscriptionDraftCommit']['userErrors'];

			if (!count($commitContractStatus_execution_errors)) {
				return 'Success';
			} else {
				// Errors occurred, handle appropriately
				return 'Error in this variable $commitContractStatus_execution_errors';
			}
		} catch (Exception $e) {
			return 'bonneCommitContractDraft function Error ----->' . $e->getMessage();
		}

	}
	public function updateNextBillingDate($draft_id){
		try {
			$next_billing_date = $this->boneCycleCustomDate();
			$input = [
				"nextBillingDate" => $next_billing_date,
			];

			$updateNextBilling = 'mutation($draftId: ID!, $input: SubscriptionDraftInput!) {
				subscriptionDraftUpdate(draftId: $draftId, input: $input) {
					draft {
						id
						nextBillingDate
					}
					userErrors {
						field
						message
					}
				}
			}';

			$variables = [
				'draftId' => $draft_id,
				'input' => $input
			];

			$updateNextBilling_execution = $this->graphqlQuery($updateNextBilling, null, null, $variables);
			$updateNextBilling_error = $updateNextBilling_execution['data']['subscriptionDraftUpdate']['userErrors'];
		} catch (Exception $e) {
			return json_encode(["status" => false, 'message' => 'Something went wrong']);
		}
	}

	function boneCycleCustomDate() {
		$starting_date = date('Y-m-d');
		$date = new DateTime($starting_date);
		if ($date->format('d') >= 20) {
			$date->modify('first day of next month');
		}
		$date->setDate($date->format('Y'), $date->format('m'), 20);
		return $date->format('Y-m-d');
	}
}