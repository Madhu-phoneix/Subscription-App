<?php
use PHPShopify\ShopifySDK;
$dirPath = dirname(dirname(__DIR__));
include $dirPath . "/application/library/config.php";
include $dirPath . "/graphLoad/autoload.php";
$currentDate = date('Y-m-d');
$beforeOneDayDate = date('Y-m-d', strtotime('-1 days', strtotime($currentDate)));
$BillingAttemptData_query =  $db->query("SELECT  e.payment_failed, b.id, b.contract_id,b.status, b.billing_attempt_date,s.shopify_customer_id, i.access_token, i.store, c.name, c.email  from billingAttempts as b INNER JOIN (SELECT MAX(id) as id FROM billingAttempts WHERE billing_attempt_date = '$beforeOneDayDate' GROUP BY contract_id) as t2 ON b.id = t2.id INNER JOIN subscriptionOrderContract AS s ON s.contract_id = b.contract_id INNER JOIN install AS  i  ON i.id =  s.store_id INNER JOIN customers AS c ON c.shopify_customer_id =  s.shopify_customer_id INNER JOIN email_notification_setting AS e ON e.store_id = s.store_id WHERE b.status = 'Pending' and s.contract_status = 'A' and billing_attempt_date = '$beforeOneDayDate' GROUP BY b.contract_id ORDER by b.id DESC");
$row_count = $BillingAttemptData_query->rowCount();
if ($row_count > 0) {
	$BillingAttemptData = $BillingAttemptData_query->fetchAll(PDO::FETCH_ASSOC);
	foreach ($BillingAttemptData as $value) {
		$config = array(
			'ShopUrl' => $rowVal['store'],
			'AccessToken' => $rowVal['access_token']
		);
		$shopifies = new ShopifySDK($config);
		$contract_id = $value['contract_id'];
		try {
			$getContractDraft = 'mutation {
				subscriptionContractUpdate(
					contractId: "gid://shopify/SubscriptionContract/' . $contract_id . '"
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
			if (!count($draftContract_execution_error)) {
				$contractDraftid = $contractDraftArray['data']['subscriptionContractUpdate']['draft']['id'];
			}
		} catch (Exception $e) {
			$contractDraftid = '';
		}

		if ($contractDraftid != '') {
			try {
				$updateContractStatus = 'mutation {
					subscriptionDraftUpdate(
						draftId: "' . $contractDraftid . '"
						input: { status : PAUSED }
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
				$updateContractStatus_execution = $shopifies->GraphQL->post($updateContractStatus);
				$updateContractStatus_execution_error = $updateContractStatus_execution['data']['subscriptionDraftUpdate']['userErrors'];
				if (!count($updateContractStatus_execution_error)) {
					$updateContractStatus = 'Success';
				} else {
					$updateContractStatus = 'error';
				}
			} catch (Exception $e) {
				$updateContractStatus = 'error';
			}

			if ($updateContractStatus == 'Success') {
				try {
					$updateContractStatus_query = 'mutation {
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
					$commitContractStatus_execution = $shopifies->GraphQL->post($updateContractStatus_query);
					$commitContractStatus_execution_error = $commitContractStatus_execution['data']['subscriptionDraftCommit']['userErrors'];
					if (!count($commitContractStatus_execution_error)) {
						$updatable = $db->query("UPDATE subscriptionOrderContract SET contract_status = 'P' where contract_id = '" . $value['contract_id'] . "'");
					}
				} catch (Exception $e) {
				}
			}
		}
	}
}
$db = null;
