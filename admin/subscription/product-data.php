
<?php
include(dirname(dirname(__file__)) . "/../application/controller/Mainfunction.php");


$store = $_POST['store'];
$mainobj = new MainFunction($store);

$all_contractIds = $_POST['all_contractIds'];
$store_id = $_POST['store_id'];

$all_productIds = get_product_ids($all_contractIds, $store_id, $mainobj);
echo json_encode($all_productIds);

function get_product_ids($all_contractIds, $store_id, $mainobj)
{
    $store_all_subscription_plans = [];
    $getContractDataArray = [];
    
    foreach ($all_contractIds as $contractId) {
        $whereCondition = array(
            'contract_id' => $contractId,
            'store_id' => $store_id
        );
        $fields = array(
            'contract_id','contract_line_item_id','product_shopify_status'
        );
        $result = $mainobj->table_row_value('subscritionOrderContractProductDetails', $fields, $whereCondition, 'and', '');
        // foreach ($store_all_subscription_plans as $subscription) {
        //     if (!in_array($subscription['product_id'], $all_productIds)) {
        //         array_push($all_productIds, $subscription['product_id']);
        //     }
        // }



        //get payment method token
        $contractMethodToken = $mainobj->getContractPaymentToken($contractId);
        $customerPaymentMethod = $contractMethodToken['data']['subscriptionContract']['customerPaymentMethod'];
        $paymentMethodToken =  substr($customerPaymentMethod['id'], strrpos($customerPaymentMethod['id'], '/') + 1);

        // echo $paymentMethodToken; die;
        $getContractData_qry = "SELECT p.payment_instrument_value,p.payment_method_token,o.after_cycle,o.order_currency,o.after_cycle_update,o.min_cycle,o.discount_type,o.discount_value,o.recurring_discount_type,o.recurring_discount_value,o.contract_id,o.next_billing_date,o.order_no,o.order_id,o.created_at,o.updated_at,o.contract_status,o.contract_products,o.delivery_policy_value,o.billing_policy_value,o.delivery_billing_type,a.first_name as shipping_first_name,a.last_name as shipping_last_name,a.address1 as shipping_address1,a.address2 as shipping_address2,a.city as shipping_city,a.province as shipping_province,a.country as shipping_country,a.company as shipping_company,a.phone as shipping_phone,a.province_code as shipping_province_code,a.country_code as shipping_country_code,a.zip as shipping_zip,a.delivery_method as shipping_delivery_method,a.delivery_price as shipping_delivery_price,b.first_name as billing_first_name,b.last_name as billing_last_name,b.address1 as billing_address1,b.address2 as billing_address2,b.city as billing_city,b.province as billing_province,b.country as billing_country,b.company as billing_company,b.phone as billing_phone,b.province_code as billing_province_code,b.country_code as billing_country_code,b.zip as billing_zip,d.store_email,d.shop_timezone,c.name,c.email,c.shopify_customer_id,cs.cancel_subscription,cs.edit_product_price,cs.skip_upcoming_order,cs.skip_upcoming_fulfillment,cs.pause_resume_subscription,cs.add_subscription_product,cs.add_out_of_stock_product,cs.edit_product_quantity,cs.edit_out_of_stock_product_quantity,cs.attempt_billing,cs.delete_product,cs.edit_shipping_address from subscriptionOrderContract as o
        INNER JOIN contract_setting as contract_settng ON o.store_id = contract_settng.store_id
        INNER JOIN subscriptionContractShippingAddress as a ON o.contract_id = a.contract_id
        INNER JOIN subscriptionContractBillingAddress as b ON o.contract_id = b.contract_id
        INNER JOIN customers as c ON c.shopify_customer_id = o.shopify_customer_id
        INNER JOIN store_details as d ON d.store_id = a.store_id
        INNER JOIN customerContractPaymentmethod AS p ON p.store_id = o.store_id
        INNER JOIN customer_settings as cs ON cs.store_id = o.store_id
        where o.contract_id = '$contractId' and p.payment_method_token = '$paymentMethodToken'";
        
        $getContractData = $mainobj->customQuery($getContractData_qry);

        array_push($store_all_subscription_plans,$result);
        array_push($getContractDataArray,$getContractData);
    }

    // return $all_productIds;
    return array('store_all_subscription_plans'=>$store_all_subscription_plans,'getContractDataArray'=>$getContractDataArray);
}






//oringinal codee/........


// function get_product_ids($all_contractIds, $store_id, $mainobj)
// {
//     $store_all_subscription_plans = [];
    
//     foreach ($all_contractIds as $contractId) {
//         $whereCondition = array(
//             'contract_id' => $contractId,
//             'store_id' => $store_id
//         );
//         $fields = array(
//             'contract_id','contract_line_item_id','product_shopify_status'
//         );
//         $result = $mainobj->table_row_value('subscritionOrderContractProductDetails', $fields, $whereCondition, 'and', '');
//         // foreach ($store_all_subscription_plans as $subscription) {
//         //     if (!in_array($subscription['product_id'], $all_productIds)) {
//         //         array_push($all_productIds, $subscription['product_id']);
//         //     }
//         // }
//         array_push($store_all_subscription_plans,$result);
//     }

//     // return $all_productIds;
//     return $store_all_subscription_plans;
// }

?>

