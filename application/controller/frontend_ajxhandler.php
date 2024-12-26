
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
require_once('../library/config.php');
switch ($_REQUEST['action']) {
    case "checkCustomerSubscription":
        $shopify_customer_id = $_REQUEST['customer_id'];
        $query = $db->prepare("SELECT * FROM subscriptionOrderContract WHERE shopify_customer_id ='$shopify_customer_id'");
        $query->execute();
        $row_count = $query->rowCount();
        if($row_count){
            $accountPageHandle = '';
            echo json_encode(array("customer_exist"=>'yes', 'accountPageHandle' => $accountPageHandle));
        }else{
            echo json_encode(array("customer_exist"=>'no'));
        }
    break;


    case "subscriptionPlanGroupsDetails":
        $store = $_REQUEST['store'];
        $product_id = $_REQUEST['product_id'];
        $store_query = $db->query("SELECT id from install where store = '$store'");
        $store_query_data = $store_query->fetch(PDO::FETCH_ASSOC);
        $store_id = $store_query_data['id'];

        $currentPlanDetails_query = $db->query("SELECT planName,plan_id from storeInstallOffers where store_id = '$store_id' and status = '1'");
        $store_install_data = $currentPlanDetails_query->fetch(PDO::FETCH_ASSOC);
		$planName = $store_install_data['planName'];
		$plan_id = $store_install_data['plan_id'];
		if($plan_id != '1'){
			$app_subscription_status = 'ACTIVE';
		}else{
		    $app_subscription_status = "FREE";
        }

        $currentPlanDetails_query = $db->query("SELECT subscription_plan_group_id from subscriptionPlanGroupsProducts where store_id = '$store_id' and product_id = '$product_id'");
        $subscription_plan_group_id = $currentPlanDetails_query->fetchAll(PDO::FETCH_ASSOC);
        if($subscription_plan_group_id){
		    $subscription_group_ids = array_column($subscription_plan_group_id, 'subscription_plan_group_id');
			$subscription_group_ids_string = implode(',',$subscription_group_ids);
            $subscriptionPlanGroupsDetails_query = $db->query("SELECT max_cycle,delivery_policy,delivery_billing_type,billing_policy,discount_value,selling_plan_id,plan_type,discount_type,discount_offer,recurring_discount_offer,change_discount_after_cycle,discount_type_after,discount_value_after FROM subscriptionPlanGroupsDetails WHERE store_id = '$store_id' and subscription_plan_group_id IN ($subscription_group_ids_string)");
            $subscriptionPlanGroupsDetails = $subscriptionPlanGroupsDetails_query->fetchAll(PDO::FETCH_ASSOC);
		}else{
			$subscriptionPlanGroupsDetails = '';
        }

        $store_widget_data_query = $db->query("SELECT * from widget_settings where store_id = '$store_id'");
        $store_widget_data = $store_widget_data_query->fetch(PDO::FETCH_ASSOC);
        echo json_encode(array('subscriptionPlanGroupsDetails'=>$subscriptionPlanGroupsDetails, 'app_subscription_status' => $app_subscription_status, 'store_widget_data' => $store_widget_data));
    break;
}