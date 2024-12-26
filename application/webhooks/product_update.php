<?php
use PHPShopify\ShopifySDK;
$dirPath = dirname(dirname(__DIR__));
include $dirPath."/application/library/config.php";
require($dirPath."/PHPMailer/src/PHPMailer.php");
require($dirPath."/PHPMailer/src/SMTP.php");
require($dirPath."/PHPMailer/src/Exception.php");
include $dirPath."/graphLoad/autoload.php";
$store = $_SERVER['HTTP_X_SHOPIFY_SHOP_DOMAIN'];
if($store == '83839d.myshopify.com'){
	file_put_contents($dirPath . "/application/assets/txt/webhooks/product_update_error.txt", $json_str);
}
$json_str = file_get_contents('php://input');
file_put_contents($dirPath . "/application/assets/txt/webhooks/product_update.txt", $json_str);
$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
function verify_webhook($data, $hmac_header, $API_SECRET_KEY) {
	$calculated_hmac = base64_encode(hash_hmac('sha256', $data, $API_SECRET_KEY, true));
	return hash_equals($hmac_header, $calculated_hmac);
}
$verified = verify_webhook($json_str, $hmac_header, $API_SECRET_KEY);
if ($verified) {
	$json_obj = json_decode($json_str, true);
	$existing_product_variant_ids = array_column($json_obj['variants'], 'id');
	$product_id = $json_obj['id'];
	$existing_variants_array = array_column($json_obj['variants'], 'id');
	$deleted_variants_array = [];
	$whereCondition = array(
		'product_id' => $product_id
	);
	// get data from subscriptionPlanGroupsProducts
	$get_updated_product_variants = table_row_value('subscriptionPlanGroupsProducts', 'all', $whereCondition, 'and', '', $db); // Get data from the database that product is updated
	$store_install_query = $db->query("Select access_token, id FROM install  WHERE store = '$store'");
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

	if (!empty($get_updated_product_variants)) {
		// if data exist in the db
		$db_variant_ids = array_column($get_updated_product_variants,'variant_id');
		$existing_variant_ids = array_intersect(array_unique($db_variant_ids),$existing_variants_array);
		$product_title = $json_obj['title'];
		foreach ($existing_variant_ids as $key => $variant_id) {
		//    echo 'variant id '.$variant_id.'<br>';
		   $found_key = array_search($variant_id, array_column($json_obj['variants'], 'id'));
		   $variant_title = $json_obj['variants'][$found_key]['title'];
		//    echo 'variant title = '.$variant_title.'<br>';
		//    echo 'image id = '.$json_obj['variants'][$found_key]['image_id'];
		   if($json_obj['variants'][$found_key]['image_id'] != null){
		      $variant_image_index =  array_search($json_obj['variants'][$found_key]['image_id'], array_column($json_obj['images'], 'id'));
		    //   echo 'variant image index = '.$variant_image_index;
		      $variant_image =  $json_obj['images'][$variant_image_index]['src'];
		   }else if($json_obj['image'] != null){
		    $variant_image = $json_obj['image']['src'];
		   }else{
		      $variant_image = 'https://your-domain.com/application/assets/images/no-image.png';
		   }
		//    echo 'variant image = '.$variant_image;
		   $fields = array(
		      'product_name' => $product_title,
		      'variant_name' => $variant_title,
		      'Imagepath'=> $variant_image
		   );
		   $whereCondition = array(
		      'variant_id' => $variant_id
		   );
		   $updateSubscriptionContract = update_row('subscriptionPlanGroupsProducts', $fields, $whereCondition, 'and',$db);
		//    echo '++++++++++++++++++++++++++++++++++++++++++++++++++++++++';
		//    echo '<pre>';
		//    print_r($updateSubscriptionContract);
		}


		$prv_subscription_plan_group_id = $get_updated_product_variants[0]['subscription_plan_group_id']; //set intitial previous group id
		$removeproductvariantssarray = "[";
		// echo 'previous plan group id = '.$prv_subscription_plan_group_id;
		$i = 1;
		foreach ($get_updated_product_variants as $key => $prdVrt) {
			if ($prv_subscription_plan_group_id != $prdVrt['subscription_plan_group_id']) {
				echo 'previous not matched = '.$prdVrt['subscription_plan_group_id'];
				if ($removeproductvariantssarray != '[') {
					$removeproductvariantssarray .= "]";
					// remove_subscription_productVariants($removeproductvariantssarray, 'gid://shopify/SellingPlanGroup/' . $prdVrt['subscription_plan_group_id']);

					//Remove product variants
					try{
						$graphQL_sellingPlanGroupRemoveProducts = 'mutation {
							sellingPlanGroupRemoveProductVariants(
							id: "gid://shopify/SellingPlanGroup/' . $prdVrt['subscription_plan_group_id'].'"
							productVariantIds: '.$removeproductvariantssarray.'
							){
								userErrors {
									field
									message
								}
							}
						}';
						$sellingPlanGroupRemoveProductsapi_execution = $shopifies->GraphQL->post($graphQL_sellingPlanGroupRemoveProducts);
						$sellingPlanGroupRemoveProductsapi_error = $sellingPlanGroupRemoveProductsapi_execution['data'];['sellingPlanGroupRemoveProductVariants']['userErrors'];
						if(!count($sellingPlanGroupRemoveProductsapi_error)){
						   echo 'succsess'; // return json
						}else{
							// return json_encode(array("status"=>false,'error'=>$sellingPlanGroupRemoveProductsapi_error));
							echo $sellingPlanGroupRemoveProductsapi_error;
						}
					}catch(Exception $e) {
							// return json_encode(array("status"=>false,'error'=>$e->getMessage())); // return json
							print_r($e->getMessage());
					}

					$prv_subscription_plan_group_id = $prdVrt['subscription_plan_group_id'];
					$removeproductvariantssarray = "[";
					$i = 1;
				}
			}
			if (!in_array($prdVrt['variant_id'], $existing_variants_array)) {
				echo 'deleted variant id = '.$prdVrt['variant_id'];
				array_push($deleted_variants_array, $prdVrt['variant_id']); // if any variants deleted then push variant id into the deleted variant array
				if ($i > 1) {
					$removeproductvariantssarray .=  ",";
				}
				$removeproductvariantssarray .=  "gid://shopify/ProductVariant/" . $prdVrt['variant_id'];
				$i++;
                echo $removeproductvariantssarray;
			}
		}
		if ($removeproductvariantssarray != '[') {
			$removeproductvariantssarray .= ']';
			// remove_subscription_productVariants($removeproductvariantssarray, 'gid://shopify/SellingPlanGroup/' . $prdVrt['subscription_plan_group_id']);
				//Remove product variants
				try{
					$graphQL_sellingPlanGroupRemoveProducts = 'mutation {
						sellingPlanGroupRemoveProductVariants(
						id: "gid://shopify/SellingPlanGroup/' . $prdVrt['subscription_plan_group_id'].'"
						productVariantIds: '.$removeproductvariantssarray.'
						){
							userErrors {
								field
								message
							}
						}
					}';
					$sellingPlanGroupRemoveProductsapi_execution = $shopifies->GraphQL->post($graphQL_sellingPlanGroupRemoveProducts);
					$sellingPlanGroupRemoveProductsapi_error = $sellingPlanGroupRemoveProductsapi_execution['data']['sellingPlanGroupRemoveProductVariants']['userErrors'];
					if(!count($sellingPlanGroupRemoveProductsapi_error)){
					   echo 'succsess'; // return json
					}else{
						// return json_encode(array("status"=>false,'error'=>$sellingPlanGroupRemoveProductsapi_error));
						echo $sellingPlanGroupRemoveProductsapi_error;
					}
				}catch(Exception $e) {
						// return json_encode(array("status"=>false,'error'=>$e->getMessage())); // return json
						print_r($e->getMessage());
				}
		}
		if (!empty($deleted_variants_array)) { //  if any variant deleted then delete from the database
			$whereCondition = array(
				'variant_id' => implode(",", $deleted_variants_array)
			);
			try {
				delete_row('subscriptionPlanGroupsProducts', $whereCondition, 'IN', $db);
			} catch (Exception $e) {
				echo 'subscriptionPlanGroupsDetails delete frequency plans error'; // return json
			}
		}
	}

	$whereCondition = array(
		'product_id' => $product_id,
		'product_contract_status' => '1',
		'product_shopify_status' => 'Active'
	);
	$fields = array('contract_id', 'variant_id', 'product_id', 'contract_line_item_id', 'product_contract_status', 'product_name', 'variant_name');
	// get general setting data form the db table
	$installStoreSettingData_query = $db->query("SELECT d.store_email,d.owner_name,g.remove_product,g.after_product_delete_contract FROM store_details as d, contract_setting as g WHERE d.store_id = '$store_id' and g.store_id = '$store_id'");
	$installStoreSettingData = $installStoreSettingData_query->fetch(PDO::FETCH_ASSOC);
	$get_contract_product_variants = table_row_value('subscritionOrderContractProductDetails', $fields, $whereCondition, 'and', '', $db);
	if ($get_contract_product_variants) {
		$contract_variant_ids = array_column($get_contract_product_variants, 'variant_id');  //get all the variants of this updated product that are used in the contract
		$deleted_variant_ids = array_diff($contract_variant_ids, $existing_product_variant_ids); //get the variant ids that are deleted from the product
		$unique_deleted_variant_ids = array_unique($deleted_variant_ids); // unique the variant ids if duplicate
		if (!empty($unique_deleted_variant_ids)) { //  if any variant deleted then delete from the database
			if ($installStoreSettingData['remove_product'] == 'No') {
				$remove_product = 'not remove';
			}else {
				$remove_product = 'removed';
			}
			//get all contract ids where line item is not already removed from the contract and check that the contract contains single or multiple products
			$existingProductContracts = [];
			$deleted_contract_array = [];
			//check the contract settings
			foreach ($get_contract_product_variants as $val) {
				if (in_array($val['variant_id'], $unique_deleted_variant_ids)) {
					$contractId = $val['contract_id'];
					$lineId = $val['contract_line_item_id'];
					$product_variant_name = $val['product_name'] . '-' . $val['variant_name'];
					$whereCondition = array(
						'contract_id' => $contractId,
						'product_contract_status' => '1'
					);
					$fields = ['contract_id'];
					$get_contract_products = table_row_value('subscritionOrderContractProductDetails', $fields, $whereCondition, 'and', '', $db);
					if ($installStoreSettingData['after_product_delete_contract'] == 'Delete') {
						$updateContractStatus = 'EXPIRED';
						$updatedbStatus = 'C';
					}else if ((count($get_contract_products) == 1 && $installStoreSettingData['remove_product'] == 'Yes') || ($installStoreSettingData['after_product_delete_contract'] == 'Pause')) {
						$updateContractStatus = 'PAUSED';
						$updatedbStatus = 'P';
					}else {
						$updateContractStatus = 'ACTIVE';
						$updatedbStatus = 'A';
					}
					//query to get customer email for sending mail to the customer
					$selectCustomerIds_query = $db->query("SELECT  contract_id,email,name FROM subscriptionOrderContract INNER JOIN customers ON subscriptionOrderContract.shopify_customer_id = customers.shopify_customer_id WHERE subscriptionOrderContract.contract_id = '$contractId'");
					$selectCustomerIds = $selectCustomerIds_query->fetchAll(PDO::FETCH_ASSOC);
					$customerEmail =  $selectCustomerIds[0]['email'];
					$customerName = $selectCustomerIds[0]['name'];

					if (!in_array($contractId, $deleted_contract_array)) {
						// get contract draft id
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
						  echo 'error';
						}
                        // update contract status
						// $updateStatus =  $mainobj->updateSubscriptionStatus($subscription_draft_contract_id, $updateContractStatus);
						try{
							$updteContractStatus_mutation = 'mutation {
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
							$updateContractStatus_execution = $shopifies->GraphQL->post($updteContractStatus_mutation);
							$updateContractStatus_execution_error = $updateContractStatus_execution['data']['subscriptionDraftUpdate']['userErrors'];
							if(!count($updateContractStatus_execution_error)){
								echo 'Success';
							}else{
								echo 'error';
							}
						}catch(Exception $e){
							echo 'error';
						}

						//commit contract changes
						// $commitContractChanges = $mainobj->commitContractDraft($subscription_draft_contract_id);
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
							if(!count($commitContractStatus_execution_error)){
								$updateStatus = 'Success';
							}else{
								echo 'commiterror';
							}
						}catch(Exception $e){
							echo 'commiterror';
						}

						if ($updateStatus == 'Success') {
							$fields = array(
								'contract_status' => $updatedbStatus
							);
							$whereCondition = array(
								'contract_id' => $contractId
							);
							if ($updatedbStatus == 'C') {
								$updateSubscriptionContract = delete_row('subscriptionOrderContract', $whereCondition, 'and', $db);
							}else {
								$updateSubscriptionContract = update_row('subscriptionOrderContract', $fields, $whereCondition, 'and', $db);
							}
						}
						//send mail to the customer
						$merchantMailTemplate = 'Hello ' . $customerName . '<br> Product "' . $product_variant_name . '" is deleted from store "' . $store . '" , as a result of which subscription(s) #"' . $contractId . '"  is "' . $updateContractStatus . '" now.';

						$merchntMailTemplate = '<table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;" data-muid="2f94ef24-a0d9-4e6f-be94-d2d1257946b0" data-mc-module-version="2019-10-22">
              				<tbody>
              					<tr>
                  					<td style="padding:18px 50px 18px 50px; line-height:22px; text-align:inherit; background-color:#dde6de;" height="100%" valign="top" bgcolor="#dde6de" role="module-content"><div><div style="font-family: inherit; text-align: center"><span style="font-size: 16px; font-family: inherit">' . $merchantMailTemplate . '</span></div><div></div></div></td>
              					</tr>
              				</tbody>
          				</table>';
						$sendMailArray = array(
							'sendTo' =>  $customerEmail,
							'subject' => 'Product Delete Mail',
							'mailBody' => $merchntMailTemplate,
							'mailHeading' => 'Product Delete Mail'
						);
						sendMail($sendMailArray, 'false',$store_id,$db,$store);
					}
					// remove the product from the contracts if 'yes' in general settings
					if ($installStoreSettingData['remove_product'] == 'Yes') {
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
								  echo 'error';
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
									if($commitContractChanges != 'error'){
									$fields = array(
										'product_contract_status' => '0',
										'variant_image' => $SHOPIFY_DOMAIN_URL."/application/assets/images/no-image.png",
									);
									$whereCondition = array(
										'contract_line_item_id' => $lineId
									);
									update_row('subscritionOrderContractProductDetails',$fields,$whereCondition,'and',$db);
									// send mail to the customer or admin
									$product_deleted_mail = getSettingData('email_notification_setting','product_deleted',$db,$store_id);
									if($product_deleted_mail == '1' && $deleted_from != ''){
										$mailBody = '<table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;" data-muid="2f94ef24-a0d9-4e6f-be94-d2d1257946b0" data-mc-module-version="2019-10-22">
										<tbody>
										  <tr>
											<td style="padding:18px 50px 18px 50px; line-height:22px; text-align:inherit; background-color:#FFFFFF;" height="100%" valign="top" bgcolor="#FFFFFF" role="module-content"><div><div style="font-family: inherit; text-align: center"><span style="font-size: 16px; font-family: inherit">A Product is deleted From the Subscription with id '.$contractId.' by the '.$deleted_from.'</span></div><div></div></div></td>
										  </tr>
										</tbody>
									  </table>';
										$sendMailArray = array(
											'sendTo' =>  $sendMailTo,
											'subject' => 'Product Deleted',
											'mailBody' => $mailBody,
											'mailHeading' => 'Product Deleted',
										);
										sendMail($sendMailArray,'false',$store_id,$db,$store);
									}
									echo 'Product Removed Successfully'; // return json
								  }else{
									echo $removeProduct_error; // return json
								 }
								}else{
								   echo $removeProduct_error; // return json
								}
					}
					if ($updatedbStatus != 'C') {
						$fields = array(
							'product_shopify_status' => 'Deleted',
							'variant_image' => $SHOPIFY_DOMAIN_URL."/application/assets/images/no-image.png",
						);
						$whereCondition = array(
							'variant_id' => $val['variant_id']
						);
						$updateSubscriptionContract = update_row('subscritionOrderContractProductDetails', $fields, $whereCondition, 'and',$db);
					}
					if ($val['product_contract_status'] == '1') {
						array_push($existingProductContracts, '#' . $contractId);
					}
					array_push($deleted_contract_array, $contractId);
				}
			}

			if (!empty($existingProductContracts)) {
				//send mail to the admin
				$contract_string = implode(',', $existingProductContracts);
				$merchantMailTemplate = 'Hello ' . $installStoreSettingData['owner_name'] . '<br> Product "' . $product_variant_name . '" is deleted from store "' . $store . '" , as a result of which your subscription(s) #"' . $contract_string . '"  is "' . $updateContractStatus .

				$merchntMailTemplate = '<table class="module" role="module" data-type="text" border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;" data-muid="2f94ef24-a0d9-4e6f-be94-d2d1257946b0" data-mc-module-version="2019-10-22">
            		<tbody>
            			<tr>
                			<td style="padding:18px 50px 18px 50px; line-height:22px; text-align:inherit; background-color:#dde6de;" height="100%" valign="top" bgcolor="#dde6de" role="module-content"><div><div style="font-family: inherit; text-align: center"><span style="font-size: 16px; font-family: inherit">' . $merchantMailTemplate . 'You can contact the store admin or visit your Subscription Account Page</span></div><div></div></div></td>
            			</tr>
            		</tbody>
        		</table>';
				$sendMailArray = array(
					'sendTo' =>  $installStoreSettingData['store_email'],
					'subject' => 'Product Delete Mail',
					'mailBody' => $merchntMailTemplate,
					'mailHeading' => 'Product Delete Mail'
				);
				sendMail($sendMailArray, 'false',$store_id,$db,$store);
			}
		}
	}
	$db = null;
}else {
	http_response_code(401);
}

function table_row_value($tableName, $fields, $whereCondition, $whereMode, $limit, $db) {
	$where = "";
	$keys = array_keys($whereCondition );
	$size = sizeof($whereCondition);
	for($x = 0; $x < $size; $x++ ) {
		if($x > 0) {
			$where .= $whereMode;
		}
		$where .= " ".$keys[$x]." = '".$whereCondition[$keys[$x]]."' ";
	}
	if(is_array($fields)) {
		$field=implode(",",$fields);
	}
	if(is_string($fields)) {
		$field = "*";
	}
	$check_entry  = table_row_check($tableName, $whereCondition, $whereMode, $db);
	if($check_entry) {
		$query = "SELECT $field FROM $tableName WHERE $where ORDER BY id DESC $limit";
		$result = $db->query($query);
		$row_data = $result->fetchAll(PDO::FETCH_ASSOC);
		return $row_data;	// return associative array
	}else {
		return $check_entry;
	}
}

function update_row($tableName,$fields,$whereCondition,$whereMode,$db){
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
	$upd =  $db->query($sql_update);
	if ($upd) {
		$status=true;
		$message= "Updated Successfully";
	} else {
		$status=false;
		$message="Error: " . $db->rollback();
	}
	return json_encode(array("status"=>$status,'message'=>$message)); // return json
}

function table_row_check($tableName, $whereCondition, $whereMode, $db) {
	$where = "";
	if(is_array($whereCondition)) {
		$keys = array_keys( $whereCondition );
		$size = sizeof($whereCondition);
		for($x = 0; $x < $size; $x++ ) {
			if($x > 0) {
				$where .= $whereMode;
			}
			$where .= " LOWER(".$keys[$x].") = LOWER('".$whereCondition[$keys[$x]]."')";
		}
	}else if(is_string($whereCondition)) {
		$where = $whereCondition;
	}
	$query = $db->prepare("SELECT * FROM $tableName WHERE $where");
	$query->execute();
	$row_count = $query->rowCount();
	return $row_count; // return integer
}


function getSettingData($tableName,$column_name,$db,$store_id){
	$whereStoreCondition = array(
		'store_id' => $store_id
	);
	$fields = array($column_name);
	$get_setting_data = table_row_value($tableName,$fields,$whereStoreCondition,'and','',$db);
	return $get_setting_data[0][$column_name];
}


//pending
function remove_subscription_productVariants($removeproducts, $subscription_plan_id){
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

function delete_row($tableName, $whereCondition, $whereMode, $db) {
	$where = "";
	$keys = array_keys( $whereCondition );
	$size = sizeof($whereCondition);
	if($whereMode == 'IN') {
		$where = $keys[0]." IN ( ".$whereCondition[$keys[0]]." ) ";
	}else {
		for($x = 0; $x < $size; $x++ ) {
			if($x > 0){
				$where .= $whereMode;
			}
			$where .= " ".$keys[$x]." = '".$whereCondition[$keys[$x]]."' ";
		}
	}
	$query = "Delete FROM $tableName WHERE $where";
	$result =  $db->exec($query);
	if ($result) {
		$status=true;
		$message="Delete Successfully";
	}else {
		$status=false;
		$message="Error: " . $db->rollback();
	}
	return json_encode(array("status"=>$status,'message'=>$message)); // return json
}

function email_templates($email_body, $email_heading, $store_id, $db, $store){
	//get data from email settings table
	$whereStoreCondition = array(
		'store_id' => $store_id
	);
	$email_Settings_data = table_row_value('email_settings', 'all', $whereStoreCondition, 'and', '', $db);

	$image_folder = "https://your-domain.com/application/assets/images/";
	$logo_url = $image_folder.'logo.png';
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
																	'.$email_body.'
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

function sendMail($sendMailArray, $testMode, $store_id, $db, $store){
	//general mail configuration
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
	$store_detail_query = $db->query("SELECT pending_emails,store_email FROM email_counter,store_details WHERE email_counter.store_id='$store_id' and store_details.store_id = '$store_id'");
	$store_detail = $store_detail_query->fetchAll(PDO::FETCH_ASSOC);

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
	}else{ // check if the email configuration setting exist and email enable is checked
		$subject = $sendMailArray['subject'];
		$sendTo = $sendMailArray['sendTo'];
		$mailBody = $sendMailArray['mailBody'];
		$mailHeading = $sendMailArray['mailHeading'];
		$whereCondition = array(
			'store_id' => $store_id
		);
		$email_configuration_data = table_row_value('email_configuration', 'all', $whereCondition, 'and', '', $db);
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
	}

	$pending_emails = $store_detail[0]['pending_emails'];

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
	if(($email_configuration_data) && ($email_configuration_data[0]['email_enable'] == 'checked')){
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
		if($email_configuration == 'false'){
			$whereCondition = array('store_id'=>$store_id);
			$pending_emails = ($pending_emails - $decrease_counter);
			$fields = array(
			 'pending_emails' => $pending_emails
			);
			update_row('email_counter', $fields, $whereCondition, 'and', $db);
		}
		return json_encode(array("status"=>true, "message"=>'Email Sent Successfully'));
	}
}