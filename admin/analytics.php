<?php
include("header.php");
// echo "<script> const AjaxCallFrom = 'backendAjaxCall';</script>";
$current_date = gmdate("Y-m-d");  //get utc current date
$dt = new DateTime($current_date);
$tz = new DateTimeZone($shop_timezone); // or whatever zone you're after
$dt->setTimezone($tz);
$shop_current_date = $dt->format('Y-m-d');
$last_date = $shop_current_date;
//GET STORE DETAILS DATA
// $where_condition = array(
//   'store_id' => $mainobj->store_id
// );
// $fields = array('currency','currencyCode');
// $store_details_data = $mainobj->single_row_value('store_details',$fields,$where_condition,'and','');

$store_details_data_qry = $db->query("SELECT currency,currencyCode FROM `store_details` WHERE store_id = '$store_id'");
$store_details_data = $store_details_data_qry->fetch(PDO::FETCH_ASSOC);


$currency = $store_details_data['currency'];
$currencyCode = $store_details_data['currencyCode'];
$start_date = date('Y-m-d', strtotime('-7 days', strtotime($last_date)));

if(isset($_GET['start_date']) && isset($_GET['last_date'])){
    $start_date = $_GET['start_date'];
    $last_date =  $_GET['last_date'];
    if($start_date != '' && $last_date == ''){
        $last_date = date('Y-m-d', strtotime('+7 days', strtotime($start_date)));
    }else if($start_date == '' && $last_date != ''){
      $start_date = date('Y-m-d', strtotime('-7 days', strtotime($last_date)));
    }else if($start_date == '' && $last_date == ''){
      $last_date = $current_date;
      $start_date = date('Y-m-d', strtotime('-7 days', strtotime($last_date)));
    }
}
$query_last_date =  date('Y-m-d', strtotime('+1 days', strtotime($last_date)));

//MAY NEED TO BE CHANGE IN THE FUTUTRE DUE TO THE LARGE DB HITS
$get_shop_analytics_data_qry = $db->query("SELECT subscriptionPlanGroups.store_id, (SELECT COUNT(*) FROM subscriptionPlanGroups as g WHERE g.store_id='$store_id' ) as total_subscription_group,(SELECT COUNT(*) FROM subscriptionOrderContract as s WHERE s.contract_status = 'A' and s.store_id='$store_id') as total_active_subscription_contract,(SELECT COUNT(*) FROM subscriptionOrderContract as s WHERE s.contract_status = 'P' and s.store_id='$store_id') as total_pause_subscription_contract FROM subscriptionPlanGroups ,subscriptionOrderContract where subscriptionPlanGroups.store_id = '$store_id' GROUP BY subscriptionPlanGroups.store_id");
$get_shop_analytics_data = $get_shop_analytics_data_qry->fetch(PDO::FETCH_ASSOC);

$selling_plan_count_data_qry = $db->query("SELECT selling_plan_id, COUNT(*) AS total_orders FROM subscriptionOrderContract  where subscriptionOrderContract.store_id = '$store_id' GROUP BY selling_plan_id  order by COUNT(*) desc, id desc limit 7");
$selling_plan_count_data = $selling_plan_count_data_qry->fetchAll(PDO::FETCH_ASSOC);

if(!empty($selling_plan_count_data)){
  $selling_plan_id_array = array_column($selling_plan_count_data, 'selling_plan_id');
  $selling_plan_id_array = array_filter($selling_plan_id_array, fn($value) => !is_null($value) && $value !== '');
  $selling_plan_id = implode(',', $selling_plan_id_array);
  $get_selling_and_group_name_qry = $db->query("SELECT subscriptionPlanGroupsDetails.plan_name,subscriptionPlanGroupsDetails.selling_plan_id, subscriptionPlanGroups.plan_name as selling_plan_group_name FROM subscriptionPlanGroupsDetails INNER JOIN subscriptionPlanGroups ON subscriptionPlanGroups.subscription_plangroup_id = subscriptionPlanGroupsDetails.subscription_plan_group_id WHERE subscriptionPlanGroupsDetails.selling_plan_id IN ($selling_plan_id)");
  $get_selling_and_group_name = $get_selling_and_group_name_qry->fetchAll(PDO::FETCH_ASSOC);
}
$date_range_sale_from_billingAttempts_qry = $db->query("SELECT BA.created_at, BA.order_total,O.order_currency,O.contract_id FROM billingAttempts AS BA, subscriptionOrderContract as O where BA.store_id = '$store_id' and BA.status = 'Success' and BA.contract_id = O.contract_id  and BA.created_at BETWEEN '$start_date' AND '$query_last_date'");
$date_range_sale_from_billingAttempts = $date_range_sale_from_billingAttempts_qry->fetchAll(PDO::FETCH_ASSOC);
$total_date_price_array = $date_range_sale_from_billingAttempts;

//total subscription orders
$get_total_subscription_orders_qry = $db->query("SELECT COUNT(distinct(order_no)) as total_contract_orders from subscriptionOrderContract where  store_id = '$store_id'");
$get_total_subscription_orders = $get_total_subscription_orders_qry->fetch(PDO::FETCH_ASSOC);

$get_billing_attempt_orders_qry = $db->query("SELECT COUNT(distinct(order_no)) as total_billing_attempt_orders from billingAttempts where  store_id = '$store_id'");
$get_billing_attempt_orders = $get_billing_attempt_orders_qry->fetch(PDO::FETCH_ASSOC);

$total_orders = $get_total_subscription_orders['total_contract_orders'] + $get_billing_attempt_orders['total_billing_attempt_orders'];

//total order sale from contract_sale table
$fields = array('total_sale','contract_currency','contract_id');
// $get_billing_attempt_orders_sale = $mainobj->table_row_value('contract_sale',$fields,$where_condition,'and','');
$get_billing_attempt_orders_sale_qry = $db->query("SELECT total_sale,contract_currency,contract_id FROM `contract_sale` WHERE store_id='$store_id'");
$get_billing_attempt_orders_sale = $get_billing_attempt_orders_sale_qry->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="Polaris-Layout">
<?php
include("navigation.php");
?>
<style>
  #sd_subscription_graph ul li .highcharts-a11y-proxy-button{
    pointer-events: none;
  }
</style>
<!-- <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script> -->
  <script src="https://code.highcharts.com/highcharts.js"></script>
  <script src="https://code.highcharts.com/modules/exporting.js"></script>
  <script src="https://code.highcharts.com/modules/export-data.js"></script>
  <script src="https://code.highcharts.com/modules/no-data-to-display.js"></script>
  <script src="https://code.highcharts.com/modules/accessibility.js"></script>
<div class="Polaris-Layout__Section sd-dashboard-page">
  <input type="hidden" value="<?php echo $shop_current_date; ?>" id="shop_current_date">
  <div>
    <div class="Polaris-Page-Header Polaris-Page-Header--isSingleRow Polaris-Page-Header--mobileView Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
      <div class="Polaris-Page-Header__Row">
        <div class="Polaris-Page-Header__TitleWrapper">
          <div>
            <div class="Polaris-Header-Title__TitleAndSubtitleWrapper">
              <h1 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">Analytics</h1>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="Polaris-Page__Content">
  <div class="sd_susbscriptionAnalytics">
  <div class="Polaris-FormLayout">
    <div role="group" class="Polaris-FormLayout--grouped">
      <div class="Polaris-FormLayout__Items">
      <div class="Polaris-FormLayout__Item sd_productPageSetting">
          <div class="Polaris-Card">
              <div class="sd_settingPage">
            <div class="Polaris-Card__Header">
              <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                  <h2 class="Polaris-Heading">Total Sales</h2>
                </div>
              </div>
            </div>
            <div class="Polaris-Card__Section sd_analytic_badge "><span class="Polaris-TextStyle--variationSubdued sd_total_sales">

            </span>
          </div>
              </div>
          </div>
        </div>
      <div class="Polaris-FormLayout__Item sd_productPageSetting">
          <div class="Polaris-Card">
              <div class="sd_settingPage">
            <div class="Polaris-Card__Header">
              <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                  <h2 class="Polaris-Heading">Total Plans</h2>
                </div>
              </div>
            </div>
              <div class="Polaris-Card__Section sd_analytic_badge ">
              <a href="<?php echo $SHOPIFY_DOMAIN_URL; ?>/admin/plans/subscription_group.php?shop=<?php echo $store; ?>" target= "_blank">
                <span class="Polaris-TextStyle--variationSubdued"><?php if(!empty($get_shop_analytics_data['total_subscription_group'])){ echo $get_shop_analytics_data['total_subscription_group']; }else{ echo '0'; } ?></span>
                </a>
              </div>
        </div>
          </div>
        </div>
        <div class="Polaris-FormLayout__Item sd_productPageSetting">
          <div class="Polaris-Card">

              <div class="sd_settingPage">
            <div class="Polaris-Card__Header">
              <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                  <h2 class="Polaris-Heading">Total Active Subscriptions</h2>
                </div>
              </div>
            </div>
            <a href="<?php echo $SHOPIFY_DOMAIN_URL; ?>/admin/subscription/subscriptions.php?shop=<?php echo $store; ?>&search_contract=active" target= "_blank">
              <div class="Polaris-Card__Section sd_analytic_badge "><span class="Polaris-TextStyle--variationSubdued"><?php if(!empty($get_shop_analytics_data['total_subscription_group'])){  echo $get_shop_analytics_data['total_active_subscription_contract']; }else{ echo '0'; }?></span>
              </div>
            </a>
              </div>
          </div>
        </div>
        <div class="Polaris-FormLayout__Item sd_productPageSetting">
          <div class="Polaris-Card">
              <div class="sd_settingPage">
            <div class="Polaris-Card__Header">
              <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                  <h2 class="Polaris-Heading">Total Pause Subscriptions</h2>
                </div>
              </div>
            </div>
            <a href="<?php echo $SHOPIFY_DOMAIN_URL; ?>/admin/subscription/subscriptions.php?shop=<?php echo $store; ?>&search_contract=pause" target= "_blank">
            <div class="Polaris-Card__Section sd_analytic_badge "><span class="Polaris-TextStyle--variationSubdued"><?php if(!empty($get_shop_analytics_data['total_subscription_group'])){  echo $get_shop_analytics_data['total_pause_subscription_contract']; }else{ echo '0'; }?></span>
            </div>
            </a>
              </div>
          </div>
        </div>
        <div class="Polaris-FormLayout__Item sd_productPageSetting">
          <div class="Polaris-Card">
              <div class="sd_settingPage">
            <div class="Polaris-Card__Header">
              <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                  <h2 class="Polaris-Heading">Total Recurring Subscriptions Orders</h2>
                </div>
              </div>
            </div>
            <a href="https://<?php echo $store; ?>/admin/orders?inContextTimeframe=none&query=sd_recurring_subscription_order" target= "_blank">
            <div class="Polaris-Card__Section sd_analytic_badge"><span class="Polaris-TextStyle--variationSubdued"><?php echo $get_billing_attempt_orders['total_billing_attempt_orders']; ?></span>
            </div>
            </a>
              </div>
          </div>
        </div>
    </div>
  </div>
  <div id="PolarisPortalsContainer"></div>
</div>
</div>
</div>
<div p-color-scheme="light">
  <div class="Polaris-Layout">
    <div class="Polaris-Layout__Section">
      <div class="Polaris-Card">
        <div class="Polaris-Card__Header sd_graph_header">
          <h2 class="Polaris-Heading">Total Recurring Sale Growth</h2>
          <button title="Choose Date" class="Polaris-Button open__datePicker" id="chooseOrderDatePicker" type="button">
              <svg viewBox="0 0 20 20" style="height: 18px;">
                <path fill-rule="evenodd" d="M17.5 2H15V1a1 1 0 10-2 0v1H6V1a1 1 0 00-2 0v1H2.5C1.7 2 1 2.7 1 3.5v15c0 .8.7 1.5 1.5 1.5h15c.8 0 1.5-.7 1.5-1.5v-15c0-.8-.7-1.5-1.5-1.5zM3 18h14V8H3v10z" fill="#5C5F62"></path>
              </svg>
              <div class="date_range_inner"><?php echo date("d M Y", strtotime($start_date)).' - '.date("d M Y", strtotime($last_date)); ?></div>
          </button>
        </div>
        <!-- <div class="Polaris-Card__Section"> -->
        <!-- <div class="Polaris-Layout SD_Dashboard SD_Advanced_Front_Grids">
            <div class="Polaris-Layout__Section">
               <div class="Polaris-Card">
                  <div class="Polaris-Card__Section"> -->
                        <!-- date filter start here -->
                  <div class="Polaris-Header-Title__TitleAndSubtitleWrapper">
                      <div class="choose_dateReport" style="display:none;">
                            <div class="Polaris-Card__Section">
                              <div class="Polaris-FormLayout">
                                  <div class="Polaris-FormLayout">
                                    <div class="Polaris-FormLayout__Items">
                                        <div class="Polaris-FormLayout__Item">
                                          <div class="Polaris-Connected">
                                              <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                              <div class="Polaris-TextField">
                                              <input  type="text" id="startdateReport" class="Polaris-TextField__Input" value="<?php echo $start_date; ?>">
                                              <div class="Polaris-TextField__Backdrop"></div>
                                            </div>
                                              </div>
                                          </div>
                                        </div>
                                        <div class="Polaris-FormLayout__Item">
                                          <div class="Polaris-Connected">
                                              <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                              <div class="Polaris-TextField">
                                                <input autocomplete="off" type="text" name="enddateReport" id="enddateReport" class="Polaris-TextField__Input" value="<?php echo $last_date; ?>">
                                                <div class="Polaris-TextField__Backdrop"></div>
                                              </div>
                                              </div>
                                          </div>
                                        </div>
                                        <div class="Polaris-FormLayout__Item">
                                          <button class="Polaris-Button Polaris-Button--primary" type="button" data-type="orders" id="submitOrderdate">
                                          <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Apply</span></span>
                                          </button>
                                        </div>
                                    </div>
                                  </div>
                              </div>
                            <!-- </div>
                      </div>
                  </div> -->
                  <!-- date filter end here -->
                  </div>
               </div>
            <!-- </div>
         </div> -->
          <div id="sd_subscription_graph"></div>
        </div>
      </div>
    </div>
    <div class="Polaris-Layout__Section Polaris-Layout__Section--secondary sd_purchased_plans">
      <div class="Polaris-Card">
        <div class="Polaris-Card__Header">
          <h2 class="Polaris-Heading">7 most purchased Selling plans</h2>
        </div>
         <div class="">
          <div class="Polaris-DataTable__Navigation">
            <button class="Polaris-Button Polaris-Button--disabled Polaris-Button--plain Polaris-Button--iconOnly" aria-label="Scroll table left one column" type="button" disabled="">
                <span class="Polaris-Button__Content">
                  <span class="Polaris-Button__Icon">
                      <span class="Polaris-Icon">
                        <span class="Polaris-VisuallyHidden"></span>
                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                            <path d="M12 16a.997.997 0 0 1-.707-.293l-5-5a.999.999 0 0 1 0-1.414l5-5a.999.999 0 1 1 1.414 1.414L8.414 10l4.293 4.293A.999.999 0 0 1 12 16z"></path>
                        </svg>
                      </span>
                  </span>
                </span>
            </button>
            <button class="Polaris-Button Polaris-Button--plain Polaris-Button--iconOnly" aria-label="Scroll table right one column" type="button">
                <span class="Polaris-Button__Content">
                  <span class="Polaris-Button__Icon">
                      <span class="Polaris-Icon">
                        <span class="Polaris-VisuallyHidden"></span>
                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                            <path d="M8 16a.999.999 0 0 1-.707-1.707L11.586 10 7.293 5.707a.999.999 0 1 1 1.414-1.414l5 5a.999.999 0 0 1 0 1.414l-5 5A.997.997 0 0 1 8 16z"></path>
                        </svg>
                      </span>
                  </span>
                </span>
            </button>
          </div>
          <?php  if(!empty($get_selling_and_group_name)){ ?>
            <div class="Polaris-DataTable">
            <div class="Polaris-DataTable__ScrollContainer">
            <table class="Polaris-IndexTable__Table most_purchased_selling">
              <thead>
                  <tr>
                    <th class="Polaris-IndexTable__TableHeading" data-index-table-heading="true"><b>Plan</b></th>
                    <th class="Polaris-IndexTable__TableHeading" data-index-table-heading="true"><b>Selling Plan</b></th>
                    <th class="Polaris-IndexTable__TableHeading Polaris-IndexTable__TableHeading--last" data-index-table-heading="true"><b>Total Subscriptions</b></th>
                  </tr>
              </thead>
              <tbody>
                    <?php foreach($get_selling_and_group_name as $key=>$value){
                      $selling_plan_id_index =  array_search($value['selling_plan_id'], $selling_plan_id_array);
                      $selling_plan_count = $selling_plan_count_data[$selling_plan_id_index]['total_orders'];
                    ?>
                 <tr class="Polaris-DataTable__TableRow Polaris-DataTable--hoverable"><td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric"><a href="<?php echo $SHOPIFY_DOMAIN_URL; ?>/admin/plans/subscription_group.php?shop=<?php echo $store; ?>&search_subscription_group_name=<?php echo $value['selling_plan_group_name'];  ?>" target= "_blank"><?php echo $value['selling_plan_group_name']; ?></td>
                 <td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric"><?php echo $value['plan_name']; ?></a></td>
                 <td class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--numeric"><?php echo $selling_plan_count; ?></td>
                </tr>
                  <?php } ?>
              </tbody>
            </table>
        <?php }else{ ?>
         <div class="sd_no_sellingPlan">No data available.</div>
       <?php } ?>
      </div>
    </div>
    </div>
  </div>
  <div id="PolarisPortalsContainer"></div>
</div>
</div>
<?php
include("footer.php");
?>
<script>
      $.getScript("https://cdn.shopify.com/s/javascripts/currencies.js", function(){
        return;
      }).then(()=>{
        // var contract_sale = '<?php //echo json_encode($get_billing_attempt_orders_sale); ?>';
        // var total_contract_sale = 0;
        // $.each(JSON.parse(contract_sale), function( key, value ) {
        //   var currency_amount = Currency.convert(value.total_sale,value.contract_currency,'<?php //echo $currency; ?>');
        //   console.log(currency_amount);
        //   total_contract_sale = total_contract_sale + currency_amount;
        // });
        // jQuery('.sd_total_sales').html('<?php //echo $mainobj->currency_code; ?>'+''+total_contract_sale.toFixed(2));


       // create arrays for chart
      var total_date_price_array = '<?php echo json_encode($total_date_price_array); ?>';
       let date_price_array = {};
      $.each(JSON.parse(total_date_price_array), function( key, value ) {
        var dtes = value['created_at'].substring(0, value['created_at'].indexOf(' '));
        var contract_amount = Currency.convert(value['order_total'],value['order_currency'],'<?php echo $currency; ?>');
        if(date_price_array[dtes]){
        }else{
          date_price_array[dtes] = [];
        }
        date_price_array[dtes].push(contract_amount);
      });

      var newDatapointspartlabel = [];
      var newDatapointsparthigh = [];
      $.each(date_price_array, function( key, value ) {
        newDatapointspartlabel.push(key);
        newDatapointsparthigh.push((value.reduce((partialSum, a) => partialSum + a, 0)).toFixed(2));
      });

      var chartPart = Highcharts.chart('sd_subscription_graph', {
        chartPart: {
          type: 'column'
        },
        title: {
          text: ''
        },
        subtitle: {
          text: ''
        },
        xAxis: {
          categories:newDatapointspartlabel,
          labels: {
            y: -10,
            text: ''
          }
        },
        yAxis: {
          allowDecimals: true,
          title: {
            text: "Sales in <?php echo $currency; ?>"
          }
        },
        series: [
        {
          name: 'Total sale '+((newDatapointsparthigh.map(Number)).reduce((partialSum, a) => partialSum + a, 0)),
          data: newDatapointsparthigh.map(Number),
          tooltip: {
            valueDecimals: 2,
            valuePrefiy: '<?php echo $currency; ?>',
            valueSuffiy: '<?php echo $currencyCode; ?>'
          }
        }],
        responsive: {
          rules: [{
            condition: {
              maxWidth: 500
            },
            chartOptions: {
              yAxis: {
                labels: {
                  align: 'left',
                  y: 0,
                  y: -5
                },
                title: {
                  text: null
                }
              },
              subtitle: {
                text: null
              },
              credits: {
                enabled: false
              }
            }
          }]
        }
        });
       });
      var Currency = {
        convert: function(amount, from, to) {
            return (amount * this.rates[from]) / this.rates[to];
            console.log('2');
        }
      }
      //analytics datepicker start

    //date filter click on analytic page
    let shop_current_date =   jQuery( "#shop_current_date" ).val();
      let check_start_date_input = document.getElementById('startdateReport');
      if(check_start_date_input){
          jQuery("#startdateReport,#enddateReport").datepicker({
              "dateFormat": "yy-mm-dd",
              maxDate: shop_current_date,
              onSelect: function(dateText) {
                  let start_date = jQuery('#startdateReport').val();
                  let  last_date = jQuery('#enddateReport').val();
                  if(start_date > last_date){
                      last_date = start_date;
                  }
                  if(last_date < start_date){
                      start_date = last_date;
                  }
                  $('#startdateReport').datepicker("setDate", start_date );
                  $('#enddateReport').datepicker("setDate", last_date );
              }
          });
      }
</script>
