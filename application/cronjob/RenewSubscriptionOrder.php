<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
use PHPShopify\ShopifySDK;
$dirPath = dirname(dirname(__DIR__));
include $dirPath . "/application/library/config.php";
$current_date = gmdate("Y-m-d H:i:s");
$SERVER_ADDR = $_SERVER['SERVER_ADDR'];
$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
file_put_contents($dirPath . "/application/assets/txt/webhooks/renew_cronjob_hit.txt", "\n\n SERVER_ADDR = ".$SERVER_ADDR." and REMOTE_ADDR =".$REMOTE_ADDR." cron job hit time ".$current_date, FILE_APPEND | LOCK_EX);
$currentDate = strtok(gmdate("Y-m-d H:i:s"), " ");
file_put_contents($dirPath . "/application/assets/txt/webhooks/cronjob.txt", $currentDate, FILE_APPEND | LOCK_EX);
include $dirPath . "/graphLoad/autoload.php";
$after_cycle_update = '0';
$row_data_query = $db->query("SELECT s.store,e.after_cycle_update,e.after_cycle,e.recurring_discount_value,e.contract_id,e.store_id,e.next_billing_date,s.access_token from subscriptionOrderContract e, install s, billingAttempts as b where e.store_id = s.id AND e.next_billing_date = '$currentDate' AND  e.contract_status = 'A' AND contract_inprocess = 'no' AND e.next_billing_date NOT IN (SELECT b.renewal_date FROM billingAttempts b WHERE e.contract_id = b.contract_id AND b.status = 'Skip' AND b.renewal_date = '$currentDate') GROUP BY e.contract_id limit 0,10");
$row_count = $row_data_query->rowCount();
//get contract active products
if ($row_count > 0) {
	$row_data = $row_data_query->fetchAll(PDO::FETCH_ASSOC);
	echo '<pre>';
	print_r($row_data);
	$contracts_in_process = (implode(",",(array_column($row_data,'contract_id'))));
	file_put_contents($dirPath . "/application/assets/txt/webhooks/RenewSubscriptionOrder.txt", "\n\n contract order data. ".json_encode($row_data)." current date".$current_date, FILE_APPEND | LOCK_EX);
	foreach ($row_data as $rowVal) {
		$store_id = $rowVal['store_id'];
		$contract_id = $rowVal['contract_id'];
		$execute_cron = 'Yes';
		$update_contract_process_status = $db->query("UPDATE subscriptionOrderContract SET contract_inprocess = 'yes' where contract_id = $contract_id");
		if($store_id == '112' || $store_id == '289'){
			$current_timestamp = strtotime($current_date);
			$get_pst_time = $current_timestamp - (8 * 60 * 60);
			$pst_datetime = date("Y-m-d H:i:s", $get_pst_time);
			$pst_time = date("H:i:s",strtotime($pst_datetime));
			$exact_time_match = '05:00:00';
			if(substr($pst_time, 0, 2) == substr($exact_time_match, 0, 2)){
				$execute_cron = 'Yes';
				file_put_contents($dirPath . "/application/assets/txt/webhooks/RenewSubscriptionOrder.txt", "\n\n contract pst Time matches. ".$contract_id." pst_datetime date".$pst_datetime, FILE_APPEND | LOCK_EX);
			}else{
				$execute_cron = 'No';
				file_put_contents($dirPath . "/application/assets/txt/webhooks/RenewSubscriptionOrder.txt", "\n\n contract pst Time not matches. ".$contract_id." pst_datetime date".$pst_datetime, FILE_APPEND | LOCK_EX);
				$update_contract_process = "UPDATE subscriptionOrderContract SET contract_inprocess = 'no' where contract_id = '$contract_id'";
				$update_contract_queryResult = $db->query($update_contract_process);
			}
		}
		try{
			file_put_contents($dirPath . "/application/assets/txt/webhooks/RenewSubscriptionOrder.txt", "\n\n contract in process. ".$contract_id." current date".$current_date, FILE_APPEND | LOCK_EX);
		}catch (Exception $e) {
		}

		// $update_contract_processStatus = "UPDATE subscriptionOrderContract SET contract_inprocess = 'yes' where contract_id = '$contract_id' and store_id = '$store_id'";
		// $update_contract_processStatus_Result = $db->query($update_contract_processStatus);

		if($execute_cron == 'Yes'){
		$config = array(
			'ShopUrl' => $rowVal['store'],
			'AccessToken' => $rowVal['access_token']
		);
		$shopifies = new ShopifySDK($config);
		//get contract products
		$get_contract_products_query = $db->query("SELECT variant_id,contract_line_item_id,recurring_computed_price FROM subscritionOrderContractProductDetails WHERE contract_id = '$contract_id' AND product_contract_status = '1'");
		$contract_products_array = $get_contract_products_query->fetchAll(PDO::FETCH_ASSOC);
		$contract_products = implode(',',array_column($contract_products_array, 'variant_id'));
		$idempotencyKey = uniqid();
		try {
			$graphQL_billingAttemptCreate = 'mutation {
			   	subscriptionBillingAttemptCreate(
					subscriptionContractId: "gid://shopify/SubscriptionContract/' . $contract_id . '"
					subscriptionBillingAttemptInput: {idempotencyKey: "' . $idempotencyKey . '"}
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
			$billingAttemptCreateApi_execution = $shopifies->GraphQL->post($graphQL_billingAttemptCreate);
			echo '<pre>';
			print_r($billingAttemptCreateApi_execution);
			// die;
			$billingAttemptCreateApi_error = $billingAttemptCreateApi_execution['data']['subscriptionBillingAttemptCreate']['userErrors'];
			if (!count($billingAttemptCreateApi_error)) {
				$billingAttemptId = $billingAttemptCreateApi_execution['data']['subscriptionBillingAttemptCreate']['subscriptionBillingAttempt']['id'];
				$billingAttempt_id = substr($billingAttemptId, strrpos($billingAttemptId, '/') + 1);
				$insertbillingattempt = "INSERT INTO billingAttempts (store_id,contract_id,contract_products,billingAttemptId,billing_attempt_date,status,renewal_date,updated_at) VALUES ('$store_id','$contract_id','$contract_products','$billingAttempt_id','$currentDate','Pending','$currentDate','$current_date')";
				$queryResult = $db->query($insertbillingattempt);
			    // if value is active in general setting table
				$intervalCount = $billingAttemptCreateApi_execution['data']['subscriptionBillingAttemptCreate']['subscriptionBillingAttempt']['subscriptionContract']['billingPolicy']['intervalCount'];
				$billingType = $billingAttemptCreateApi_execution['data']['subscriptionBillingAttemptCreate']['subscriptionBillingAttempt']['subscriptionContract']['billingPolicy']['interval'];
				if ($billingType == 'DAY') {
					$TotalIntervalCount =  $intervalCount;
				} else if ($billingType == 'WEEK') {
					$TotalIntervalCount = (7 *  $intervalCount);
				} else if ($billingType == 'MONTH') {
					$TotalIntervalCount = (30 *  $intervalCount);
				} else if ($billingType == 'YEAR') {
					$TotalIntervalCount = (365 *  $intervalCount);
				}
				$newRenewalDate = date('Y-m-d', strtotime('+' . $TotalIntervalCount . ' day', strtotime($currentDate)));
				$updatable = "UPDATE subscriptionOrderContract SET updated_at = '$current_date', next_billing_date = '$newRenewalDate' where contract_id = '$contract_id'";
				$queryResult = $db->query($updatable);
			}
		}catch (Exception $e) {
			echo '<pre>';
			print_r($e->getMessage());
			// die;
			// set contract status in process no
			file_put_contents($dirPath . "/application/assets/txt/webhooks/renew_cronjob_hit.txt", "\n\n cron job catch error contract id = ".$contract_id." and ". $e->getMessage(), FILE_APPEND | LOCK_EX);
			$update_contract_processStatus = "UPDATE subscriptionOrderContract SET contract_inprocess = 'no' where contract_id = '$contract_id' and store_id = '$store_id'";
			$update_contract_processStatus_Result = $db->query($update_contract_processStatus);
		}
	}
}
}
$db = null;