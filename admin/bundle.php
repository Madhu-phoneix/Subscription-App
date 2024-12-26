
<?php
   include("header.php");
?>
<div class="Polaris-Layout">
<?php
   include("navigation.php");
   $get_contract_orders = $mainobj->customQuery("SELECT * FROM subscriptionOrderContract INNER JOIN customers ON subscriptionOrderContract.shopify_customer_id = customers.shopify_customer_id WHERE subscriptionOrderContract.store_id = '$mainobj->store_id' group by subscriptionOrderContract.order_id order by subscriptionOrderContract.order_no desc");
   $get_billingAttempts_orders = $mainobj->customQuery("SELECT * FROM billingAttempts WHERE billingAttempts.store_id = '$mainobj->store_id' and billingAttempts.status = 'success' order by billingAttempts.order_no desc");
   echo 'contract orders';
   echo '<pre>';
   print_r($get_contract_orders);
   echo 'billing attempt orders';
   echo '<pre>';
   print_r($get_billingAttempts_orders);
   die;
   include("footer.php");
?>
