<?php
   include("header.php");

?>
<div class="">
<?php
    include("navigation.php");
    function getShopTimezoneDate($date,$shop_timezone){
      $dt = new DateTime($date);
      $tz = new DateTimeZone($shop_timezone); // or whatever zone you're after
      $dt->setTimezone($tz);
      $dateTime = $dt->format('Y-m-d H:i:s');
      $shopify_date = date("d M Y", strtotime($dateTime));
      return $shopify_date;
    }
   // get subscription orders
    $get_contract_orders_qry = $db->query("SELECT * FROM subscriptionOrderContract INNER JOIN customers ON subscriptionOrderContract.shopify_customer_id = customers.shopify_customer_id WHERE subscriptionOrderContract.store_id = '$store_id' group by subscriptionOrderContract.order_id order by subscriptionOrderContract.order_no desc");
    $get_contract_orders = $get_contract_orders_qry->fetchAll(PDO::FETCH_ASSOC);

    $get_billingAttempts_orders_qry = $db->query("SELECT * FROM billingAttempts WHERE billingAttempts.store_id = '$store_id' and billingAttempts.status = 'success' order by billingAttempts.order_no desc");
    $get_billingAttempts_orders = $get_billingAttempts_orders_qry->fetchAll(PDO::FETCH_ASSOC);

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
            if(isset($value['email'])){
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
              <td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric"><a class="Polaris-Navigation__Item"  href="https://<?php echo $store; ?>/admin/orders/<?php echo $value['order_id']; ?>" target="_blank" data-polaris-unstyled="true">#<?php echo $value['order_no']; ?></a></td>
              <td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric"><?php echo getShopTimezoneDate($value['created_at'],$shop_timezone); ?></td>
              <td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric">
              <a href="downloadInvoice.php?shop=<?php echo $store; ?>&order_id=<?php echo $value['order_id']; ?>" title="Print/Download"><button type="button" class="Polaris-Icon Polaris-Button" style=""><span class="Polaris-Button__Content"><span class="Polaris-Button__Text back__btn"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" viewBox="0 0 512 512" xml:space="preserve"><path style="fill:#8E959F;" d="M417.937,93.528c0-13.851-11.268-25.119-25.119-25.119H119.182  c-13.851,0-25.119,11.268-25.119,25.119v93.707h323.875V93.528z"/><path style="fill:#68727E;" d="M392.818,68.409H119.182c-0.18,0-0.356,0.01-0.534,0.014v118.812h274.171V68.409z"/><path style="fill:#F6F6F7;" d="M383.733,8.017c0-4.427-3.588-8.017-8.017-8.017H136.284c-4.428,0-8.017,3.589-8.017,8.017v179.218  h255.466V8.017z"/><path style="fill:#ECEDEF;" d="M173.35,0h-37.066c-4.428,0-8.017,3.589-8.017,8.017v179.218h167.219L173.35,0z"/><path style="fill:#8E959F;" d="M469.779,179.574H42.221C18.941,179.574,0,198.515,0,221.795v205.228  c0,23.28,18.941,42.221,42.221,42.221h427.557c23.28,0,42.221-18.941,42.221-42.221V221.795  C512,198.515,493.059,179.574,469.779,179.574z"/><g><rect y="314.789" style="fill:#68727E;" width="512" height="35.273"/><path style="fill:#68727E;" d="M409.921,384.802H102.079c-4.427,0-8.017,3.589-8.017,8.017v76.426h323.875v-76.426   C417.937,388.391,414.348,384.802,409.921,384.802z"/></g><path style="fill:#509FE8;" d="M469.779,179.574H42.221C18.903,179.574,0,198.477,0,221.795v110.986h512V221.795  C512,198.477,493.097,179.574,469.779,179.574z"/><path style="fill:#68727E;" d="M16.568,427.023V221.795c0-23.281,18.941-42.221,42.221-42.221H42.221  C18.941,179.574,0,198.515,0,221.795v205.228c0,23.28,18.941,42.221,42.221,42.221h16.568  C35.509,469.244,16.568,450.303,16.568,427.023z"/><path style="fill:#4D8CCF;" d="M58.789,179.574H42.221C18.903,179.574,0,198.477,0,221.795v110.986h16.568V221.795  C16.568,198.477,35.471,179.574,58.789,179.574z"/><path style="fill:#55606E;" d="M417.937,392.818c0-4.427-3.589-8.017-8.017-8.017H102.079c-4.427,0-8.017,3.589-8.017,8.017v17.637  h323.875V392.818z"/><path style="fill:#ECEDEF;" d="M383.733,384.802H128.267v119.182c0,4.427,3.589,8.017,8.017,8.017h239.432  c4.427,0,8.017-3.589,8.017-8.017V384.802z"/><rect x="128.267" y="384.802" style="fill:#D9DCDF;" width="255.466" height="25.653"/><path style="fill:#DF7A6E;" d="M452.676,230.881c-13.851,0-25.119,11.268-25.119,25.119c0,13.851,11.268,25.119,25.119,25.119  s25.119-11.268,25.119-25.119C477.795,242.149,466.527,230.881,452.676,230.881z"/><path style="fill:#FF8C78;" d="M452.676,238.898c-9.43,0-17.102,7.673-17.102,17.102s7.673,17.102,17.102,17.102  s17.102-7.673,17.102-17.102S462.106,238.898,452.676,238.898z"/><path style="fill:#AFCA62;" d="M392.818,230.881c-13.851,0-25.119,11.268-25.119,25.119c0,13.851,11.268,25.119,25.119,25.119  c13.851,0,25.119-11.268,25.119-25.119C417.937,242.149,406.669,230.881,392.818,230.881z"/><path style="fill:#C4DF64;" d="M392.818,238.898c-9.43,0-17.102,7.673-17.102,17.102s7.673,17.102,17.102,17.102  c9.43,0,17.102-7.673,17.102-17.102S402.248,238.898,392.818,238.898z"/><g><path style="fill:#B3B9BF;" d="M341.511,435.04H170.489c-4.428,0-8.017-3.589-8.017-8.017c0-4.427,3.588-8.017,8.017-8.017h171.023   c4.428,0,8.017,3.589,8.017,8.017C349.528,431.45,345.94,435.04,341.511,435.04z"/><path style="fill:#B3B9BF;" d="M298.756,469.244H170.489c-4.428,0-8.017-3.589-8.017-8.017s3.588-8.017,8.017-8.017h128.267   c4.428,0,8.017,3.589,8.017,8.017S303.184,469.244,298.756,469.244z"/></g><g><path style="fill:#4D8CCF;" d="M221.795,246.914H102.079c-4.428,0-8.017-3.589-8.017-8.017s3.588-8.017,8.017-8.017h119.716   c4.428,0,8.017,3.589,8.017,8.017S226.224,246.914,221.795,246.914z"/><path style="fill:#4D8CCF;" d="M264.551,246.914h-17.102c-4.428,0-8.017-3.589-8.017-8.017s3.588-8.017,8.017-8.017h17.102   c4.428,0,8.017,3.589,8.017,8.017S268.98,246.914,264.551,246.914z"/><path style="fill:#4D8CCF;" d="M76.426,246.914H59.324c-4.428,0-8.017-3.589-8.017-8.017s3.588-8.017,8.017-8.017h17.102   c4.428,0,8.017,3.589,8.017,8.017S80.854,246.914,76.426,246.914z"/><path style="fill:#4D8CCF;" d="M264.551,281.119H59.324c-4.428,0-8.017-3.589-8.017-8.017c0-4.427,3.588-8.017,8.017-8.017h205.228   c4.428,0,8.017,3.589,8.017,8.017C272.568,277.53,268.98,281.119,264.551,281.119z"/></g></svg></span></span></button></a>
              <a class="send_mail_invoice" href="#" data-customerEmail = "<?php echo $customer_email; ?>" data-orderId = "<?php echo $value['order_id']; ?>" title="Mail"><button type="button" class="Polaris-Icon Polaris-Button" style=""><span class="Polaris-Button__Content"><span class="Polaris-Button__Text back__btn"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="800px" height="800px" viewBox="0 0 500 500" enable-background="new 0 0 500 500" id="Layer_1" version="1.1" xml:space="preserve"><g><path d="M415.484,98.802c-32.513-34.721-79.476-7.76-80.147,35.672c-70.075,5.061-140.187,9.532-210.236,14.956   c-17.966,2.95-46.232-2.155-50.804,21.342c-1.24,60.734,2.622,123.439,6.731,184.645c3.626,53.981,13.508,66.33,68.775,60.224   c60.022-3.725,119.983-8.527,180.067-11.174c24.076-3.344,58.891,5.813,72.874-19.608c6.803-69.612-1.65-141.737-8.182-211.589   C422.206,159.684,437.387,125.278,415.484,98.802z" fill="#58454D"/><path d="M77.043,200.22c0,0-5.898-37.514,9.685-43.032c15.582-5.518,252.151-20.042,252.151-20.042   c4.725,25.336,29.182,35.889,51.801,32.422v10.905L290.435,263.59l103.159,113.475c0,0,5.993,5.305,4.421,9.628   c-1.572,4.323-3.34,9.825-30.064,11.986c-26.723,2.161-240.453,18.059-255.29,14.639s-21.01-17.39-21.01-17.39   c31.542-38.981,90.779-95.622,122.754-128.713L77.043,200.22z" fill="#F8C795"/><path d="M75.948,203.757c1.387,18.645,7.775,165.614,15.704,192.171c27.501-33.603,95.75-100.981,122.754-128.713   L82.009,202.643L75.948,203.757z" fill="#EEB378"/><path d="M390.679,180.473c3.311,9.848,11.742,179.35,11.263,196.789c0.003-0.001-3.927,9.431-3.927,9.431   l-2.812-7.957c-24.81-27.197-79.607-87.453-104.769-115.146L390.679,180.473z" fill="#E79959"/><path d="M78.493,207.617c14.01,11.338,97.763,32.809,86.448,70.775c-17.928,56.139-43.08,73.162-75.219,106.374   l2.5,10.278c27.972-35.156,90.453-95.77,121.743-128.044c-16.305-7.891-119.075-58.348-132.923-64.358   C81.042,202.643,78.322,204.844,78.493,207.617z" fill="#E79A59"/><path d="M300.817,264.081c17.167-21.182,69.385-53.491,72.724,16.452c3.397,37.389,5.486,55.366,1.619,55.317   C371.292,335.801,299.115,270.172,300.817,264.081z" fill="#EEB378"/><path d="M421.342,128.644c-0.903,52.885-79.569,52.877-80.464-0.002C341.781,75.759,420.447,75.767,421.342,128.644   z" fill="#EF7F5F"/><path d="M361.306,165.467c13.541-28.613,71.728-24.507,43.817-69.473   C446.673,125.25,405.473,189.332,361.306,165.467z" fill="#E86042"/><path d="M348.66,397.5c8.827-12.763,18.431-30.272,12.367-46.224c-2.145-5.547-7.057-10.448-7.144-16.586   C383.813,373.199,433.621,397.979,348.66,397.5z" fill="#F5B278"/><path d="M382.211,184.244c-23.481-1.585-49.898-19.176-53.68-43.572c-0.063-1.503-1.163-0.848-1.857-1.063   c2.548,0.281,10.199-2.208,10.858,0.681c4.542,18.066,20.276,32.047,38.92,33.776c3.01,1.401,13.335-1.867,13.486,0.914   C391.776,180.301,385.478,181.433,382.211,184.244z" fill="#F5B278"/><path d="M290.114,265.562c-43.59,29.075-16.187,30.127-68.243,6.095l0,0c0,0,0,0,0,0c0,0.001,0,0.002,0,0.003   c-1.939-1.313-4.193-1.915-6.217-3.038c-2.967-2.564-4.174,0.662-6.329,2.441c-3.573,3.393-7.276,6.795-10.67,10.423   c20.517-17.927,40.3,13.456,62.018,11.05c19.741-6.929,12.626-16.08,42.436-14.65C299.151,273.383,294.43,269.686,290.114,265.562z   " fill="#F5B278"/><path d="M80.44,208.491C80.44,208.491,80.44,208.491,80.44,208.491c0,0.01,0,0.019-0.001,0.029   c0.006-0.008,0.013-0.011,0.019-0.018C80.452,208.499,80.446,208.495,80.44,208.491z" fill="#58454D"/><path d="M415.484,98.802c-32.513-34.721-79.476-7.76-80.147,35.672c-70.075,5.061-140.187,9.532-210.236,14.956   c-17.966,2.95-46.232-2.155-50.804,21.342c-1.24,60.734,2.622,123.439,6.731,184.645c3.626,53.981,13.508,66.33,68.775,60.224   c60.022-3.725,119.983-8.527,180.067-11.174c24.076-3.344,58.891,5.813,72.874-19.608c6.803-69.612-1.65-141.737-8.182-211.589   C422.206,159.684,437.387,125.278,415.484,98.802z M79.602,198.39c-3.948-33.069,1.459-43.322,36.173-42.84   c73.673-3.927,147.24-10.014,220.881-14.472c16.587,47.003,52.439,26.129,51.346,37.332   c-44.135,32.568-85.588,74.396-129.913,105.57C198.67,255.861,137.943,227.114,79.602,198.39z M92.817,390.914   c-0.665-2.144-0.924-4.199-1.113-6.267c0,0,0,0,0,0c-0.044-0.179-0.088-0.358-0.132-0.537c0,0,0,0,0,0   c-7.701-58.881-8.004-118.475-11.479-177.698c2.571-3.48,30.416,12.844,128.814,59.878   C176.105,306.977,127.444,347.332,92.817,390.914z M394.985,386.587c-12.031,14.276-42.04,7.655-59.379,10.049   c-52.967,1.642-105.775,6.131-158.588,10.288c-22.794-0.661-67.227,14.091-80.923-7.96c-2.497-3.576-2.422-3.553,0.396-6.917   c36.736-41.096,75.699-80.383,114.851-119.111c2.091-1.724,3.251-4.844,6.127-2.363c1.959,1.087,4.142,1.67,6.019,2.941   c0-0.001,0-0.002,0-0.003c0,0,0,0,0,0l0,0c43.69,19.163,26.357,25.786,64.567-5.968c24.75,18.45,45.124,46.642,66.973,69.602v0   C362.693,347.944,395.317,376.665,394.985,386.587z M395.966,289.77c-0.871,30.102,4.838,60.521,2.202,90.333   c-33.718-39.761-68.33-78.598-104.74-115.865c21.112-20.767,48.658-40.084,71.795-60.488c3.874-2.268,21.703-21.151,23.512-17.51   C391.789,220.692,394.85,255.194,395.966,289.77z M419.126,128.862c-8.366,69.663-108.682,31.433-65.848-25.297   C375.498,77.122,420.717,94.233,419.126,128.862z" fill="#58454D"/></g></svg></span></span></button></a>
              <a href="invoiceDetails.php?shop=<?php echo $store; ?>&order_id=<?php echo $value['order_id']; ?>&customer_email=<?php echo $customer_email; ?>" title="View/Edit"><button type="button" class="Polaris-Icon Polaris-Button" style=""><span class="Polaris-Button__Content">
              <span class="Polaris-Button__Text back__btn"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" viewBox="0 0 512 512" xml:space="preserve"><path style="fill:#FFC1A6;" d="M257.923,0.011v350.84c95.531-0.911,179.12-70.251,229.174-175.42  C437.043,70.251,353.454,0.922,257.923,0.011z"/><path style="fill:#FFDBCC;" d="M257.923,0.011c81.463,1.08,152.737,70.364,195.437,175.42  c-42.699,105.045-113.974,174.34-195.437,175.42c-0.641,0.011-1.282,0.011-1.923,0.011c-96.33,0-180.705-69.554-231.097-175.431  C75.295,69.543,159.67,0,256,0C256.641,0,257.282,0,257.923,0.011z"/><path style="fill:#FFFFFF;" d="M256,53.656c-96.331,0-180.702,48.274-231.097,121.771C75.298,248.922,159.669,297.197,256,297.197  c96.33,0,180.702-48.276,231.097-121.771C436.702,101.932,352.33,53.656,256,53.656z"/><path style="fill:#42C8C6;" d="M256,53.417v244.029c67.383,0,122.015-54.631,122.015-122.015  C378.015,108.036,323.384,53.417,256,53.417z"/><path style="fill:#81E3E2;" d="M256,53.417c48.75,0,88.278,54.62,88.278,122.015c0,67.384-39.528,122.015-88.278,122.015  c-67.384,0-122.015-54.631-122.015-122.015C133.985,108.036,188.617,53.417,256,53.417z"/><path style="fill:#272727;" d="M256,117.516v115.829c31.982,0,57.915-25.932,57.915-57.915  C313.915,143.437,287.982,117.516,256,117.516z"/><path style="fill:#4D4D4D;" d="M256,117.516c13.348,0,24.178,25.921,24.178,57.915c0,31.982-10.829,57.915-24.178,57.915  c-31.982,0-57.915-25.932-57.915-57.915C198.085,143.437,224.018,117.516,256,117.516z"/><polygon style="fill:#272727;" points="74.586,452.961 52.095,481.659 0.163,452.961 "/><polygon style="fill:#4D4D4D;" points="52.095,424.262 74.586,444.763 52.095,465.263 0.163,452.961 "/><polygon style="fill:#FEA680;" points="129.487,465.263 106.996,512 52.095,481.659 52.095,465.263 "/><polygon style="fill:#FFC1A6;" points="106.996,478.263 52.095,465.263 52.095,424.262 106.996,393.921 129.487,436.092 "/><path style="fill:#FE5F1A;" d="M396.57,478.263L419.061,512h33.737c32.601,0,59.039-26.438,59.039-59.039L396.57,478.263z"/><path style="fill:#FE834D;" d="M452.798,393.921c32.612,0,59.039,26.427,59.039,59.039c0,13.978-26.438,25.303-59.039,25.303H396.57  l22.491-84.027v-0.315H452.798z"/><polygon style="fill:#DFC136;" points="407.815,455.772 385.324,512 106.996,512 106.996,478.263 "/><polygon style="fill:#FEDC3D;" points="407.815,430.47 385.324,478.263 106.996,478.263 106.996,452.961 "/><polygon style="fill:#FFE777;" points="385.324,394.236 407.815,452.961 106.996,452.961 106.996,393.921 385.324,393.921 "/><polygon style="fill:#B9B9B9;" points="385.324,478.263 385.324,512 419.061,512 419.061,478.263 402.193,455.772 "/><rect x="385.319" y="393.921" style="fill:#DCDCDC;" width="33.737" height="84.342"/></svg></span></span></button></a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>

 </div>
<?php
   include("footer.php");
?>
