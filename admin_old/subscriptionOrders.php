<?php
   include("header.php");
?>
<div class="">
<?php
   include("navigation.php");
   // get subscription orders
   $get_contract_orders = $mainobj->customQuery("SELECT * FROM subscriptionOrderContract INNER JOIN customers ON subscriptionOrderContract.shopify_customer_id = customers.shopify_customer_id WHERE subscriptionOrderContract.store_id = '$mainobj->store_id' group by subscriptionOrderContract.order_id order by subscriptionOrderContract.order_no desc");
   $get_billingAttempts_orders = $mainobj->customQuery("SELECT * FROM billingAttempts WHERE billingAttempts.store_id = '$mainobj->store_id' and billingAttempts.status = 'success' order by billingAttempts.order_no desc");
   $combine_array = array_merge($get_contract_orders,$get_billingAttempts_orders);
   function sortByAge($a, $b) {
    return $b['order_no'] - $a['order_no'];
   }
  usort($combine_array, 'sortByAge');
?>
<div class="Polaris-Card__Header sd_contractHeader"><div class="Polaris-Stack Polaris-Stack--alignmentBaseline"><div class="Polaris-Stack__Item Polaris-Stack__Item--fill"><h2 class="Polaris-Heading">Subscription Orders</h2></div>
  </div></div>
 <div class="Polaris-Page__Content">
 <div class="Polaris-DataTable sd_common_datatable">
    <div class="Polaris-DataTable__ScrollContainer">
      <table class="Polaris-DataTable__Table" id="subscription_order_table">
        <thead>
          <tr>
            <th data-polaris-header-cell="true" class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--firstColumn Polaris-DataTable__Cell--header" scope="col" aria-sort="none">Customer</th>
            <!-- <th data-polaris-header-cell="true" class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--firstColumn Polaris-DataTable__Cell--header" scope="col" aria-sort="none">Subscription ID</th> -->
            <th data-polaris-header-cell="true" class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--header Polaris-DataTable__Cell--numeric" scope="col" aria-sort="none">Order No.</th>
            <th data-polaris-header-cell="true" class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--header Polaris-DataTable__Cell--numeric" scope="col" aria-sort="none">Order Date</th>
            <th data-polaris-header-cell="true" class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--header Polaris-DataTable__Cell--numeric" scope="col" aria-sort="none">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($combine_array as $key=>$value){
            if($value['email']){
              $customer_name = $value['name'];
              $customer_email = $value['email'];
            }else{
              $found_key = array_search($value['contract_id'], array_column($get_contract_orders, 'contract_id'));
              $customer_name = $get_contract_orders[$found_key]['name'];
              $customer_email = $get_contract_orders[$found_key]['email'];
            }
          ?>
            <tr>
              <td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric"><div class="sd_customerData"><span class="user-name"><?php echo implode('', array_map(function($customer_name) { if($customer_name){ return strtoupper($customer_name[0]); } }, explode(' ', $customer_name)))?></span><div class="sd_customerDetails"><strong><?php echo $customer_name; ?></strong><br><span><?php echo $customer_email; ?></div></span></div></td>
              <td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric"><a class="Polaris-Navigation__Item"  href="https://<?php echo $mainobj->store; ?>/admin/orders/<?php echo $value['order_id']; ?>" target="_blank" data-polaris-unstyled="true">#<?php echo $value['order_no']; ?></a></td>
              <td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric"><?php echo $mainobj->getShopTimezoneDate($value['created_at'],$mainobj->shop_timezone); ?></td>
              <td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric">
              <a href="downloadInvoice.php?shop=<?php echo $mainobj->store; ?>&order_id=<?php echo $value['order_id']; ?>" title="Print/Download"><button type="button" class="Polaris-Icon Polaris-Button" style=""><span class="Polaris-Button__Content"><span class="Polaris-Button__Text back__btn"><svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4 2.5A1.5 1.5 0 015.5 1h9A1.5 1.5 0 0116 2.5V6h1.5A1.5 1.5 0 0119 7.5v10a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 011 17.5v-10A1.5 1.5 0 012.5 6H4V2.5zM6 12h8V3H6v9zm-2 3a1 1 0 011-1h3a1 1 0 110 2H5a1 1 0 01-1-1zm11 1a1 1 0 100-2 1 1 0 000 2z" fill="#5C5F62"></path></svg></span></span></button></a>
              <a class="send_mail_invoice" href="#" data-customerEmail = "<?php echo $customer_email; ?>" data-orderId = "<?php echo $value['order_id']; ?>" title="Mail"><button type="button" class="Polaris-Icon Polaris-Button" style=""><span class="Polaris-Button__Content"><span class="Polaris-Button__Text back__btn"><svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M0 5.324V15.5A1.5 1.5 0 001.5 17h17a1.5 1.5 0 001.5-1.5V5.324l-9.496 5.54a1 1 0 01-1.008 0L0 5.324z" fill="#5C5F62"></path><path d="M19.443 3.334A1.494 1.494 0 0018.5 3h-17c-.357 0-.686.125-.943.334L10 8.842l9.443-5.508z" fill="#5C5F62"></path></svg></span></span></button></a>
              <a href="invoiceDetails.php?shop=<?php echo $mainobj->store; ?>&order_id=<?php echo $value['order_id']; ?>&customer_email=<?php echo $customer_email; ?>" title="View/Edit"><button type="button" class="Polaris-Icon Polaris-Button" style=""><span class="Polaris-Button__Content">
              <span class="Polaris-Button__Text back__btn"><svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill="#5C5F62" fill-rule="evenodd" d="M19.928 9.629C17.791 4.286 13.681 1.85 9.573 2.064c-4.06.21-7.892 3.002-9.516 7.603L-.061 10l.118.333c1.624 4.601 5.455 7.393 9.516 7.603 4.108.213 8.218-2.222 10.355-7.565l.149-.371-.149-.371zM10 15a5 5 0 100-10 5 5 0 000 10z"></path><circle fill="#5C5F62" cx="10" cy="10" r="3"></circle></svg></span></span></button></a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
 </div>
<?php
   include("footer.php");
?>
