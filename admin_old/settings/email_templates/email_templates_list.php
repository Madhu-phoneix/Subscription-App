<?php
  include("../../header.php");
  ?>
  <div class="Polaris-Layout">
  <?php
  include("../../navigation.php");
  // ini_set('display_errors', 1);
  // ini_set('display_startup_errors', 1);
  // error_reporting(E_ALL);

  $whereCondition = array(
    'store_id' => $mainobj->store_id
  );
  $email_notification = $mainobj->single_row_value('email_notification_setting','all',$whereCondition,'and','');
  // $new_template = '';
  // if($mainobj->store == 'predictive-search.myshopify.com'){
  //     $new_template = "new =>";
  // }

  $email_notification_array = array(
    'Subscription Purchase' => 'subscription_purchase',
    'Subscription Cancelled' => 'subscription_status_cancelled',
    'Subscription status paused' => 'subscription_status_paused',
    'Subscription status resumed' => 'subscription_status_resumed',
    'Subscription product added' => 'product_added',
    'Subscription product removed' =>  'product_removed',
    'Subscription product updated' => 'product_updated',
    'Order Skipped' => 'skip_order',
    'Fulfillment rescheduled' => 'reschedule_fulfillment',
    'Shipping address updated' => 'shipping_address_update',
    'Billing attempted' => 'billing_attempted',
    'Payment Failed' => 'payment_failed',
    'Payment Declined' => 'payment_declined',
    'Payment failure/declined subscription paused' => 'payment_pending',
    'Subscription Renewal Date Update' => 'subscription_renewal_date_update',
    'Upcoming Orders' => 'upcoming_orders',
  );
 ?>

<div class="Polaris-Layout__Section sd-email_template_list-page">
      <div class="Polaris-Page-Header Polaris-Page-Header--isSingleRow Polaris-Page-Header--mobileView Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
        <div class="search-form">
            <div id="subscription-list-search">
                <div class="Polaris-Page-Header__BreadcrumbWrapper">
                  <nav role="navigation">
                    <a class="Polaris-Breadcrumbs__Breadcrumb" href="../setting.php?shop=<?php echo $mainobj->store; ?>">
                        <span class="Polaris-Breadcrumbs__ContentWrapper">
                        <span class="Polaris-Breadcrumbs__Icon">
                            <span class="Polaris-Icon">
                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                    <path d="M17 9H5.414l3.293-3.293a.999.999 0 1 0-1.414-1.414l-5 5a.999.999 0 0 0 0 1.414l5 5a.997.997 0 0 0 1.414 0 .999.999 0 0 0 0-1.414L5.414 11H17a1 1 0 1 0 0-2z"></path>
                                </svg>
                            </span>
                        </span>
                        </span>
                    </a>
                  </nav>
                </div>
                <div class="formbox">
                    <label>Email Templates</label>
                </div>
                <button class="Polaris-Button Polaris-Button--primary sd_selectSetting sd_button" type="button" data-popup="email_configuration"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Email Configuration</span></span></button>
            </div>
        </div>
      </div>
      <div class="Polaris-Page__Content">
         <div>
                     <div class="Polaris-Card">
                        <div class="Polaris-LegacyCard">
                        <div class="Polaris-IndexTable">
                            <div class="Polaris-IndexTable__IndexTableWrapper">
                            <div class="Polaris-IndexTable-ScrollContainer">
                                <table class="Polaris-IndexTable__Table Polaris-IndexTable__Table--unselectable Polaris-IndexTable__Table--sticky">
                                <thead>
                                    <tr>
                                    <th class="Polaris-IndexTable__TableHeading Polaris-IndexTable__TableHeading--unselectable" data-index-table-heading="true">Name</th>
                                    <th class="Polaris-IndexTable__TableHeading Polaris-IndexTable__TableHeading--second Polaris-IndexTable__TableHeading--unselectable" data-index-table-heading="true">Send to Admin</th>
                                    <th class="Polaris-IndexTable__TableHeading Polaris-IndexTable__TableHeading--second Polaris-IndexTable__TableHeading--unselectable" data-index-table-heading="true">Send to Customer</th>
                                    <th class="Polaris-IndexTable__TableHeading Polaris-IndexTable__TableHeading--unselectable" data-index-table-heading="true"></th>
                                    <th class="Polaris-IndexTable__TableHeading Polaris-IndexTable__TableHeading--last Polaris-IndexTable__TableHeading--unselectable" data-index-table-heading="true">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php foreach($email_notification_array as $key=>$value){ ?>
                                      <tr class="Polaris-IndexTable__TableRow Polaris-IndexTable__TableRow--unclickable">
                                      <td class="Polaris-IndexTable__TableCell"><?php echo $key; ?></td>
                                      <td class="Polaris-IndexTable__TableCell">
                                      <label class="switch">
                                        <input type="checkbox" class="email_template_notification" data-table = "email_notification_setting" data-field="<?php echo 'admin_'.$value; ?>" <?php if($email_notification['admin_'.$value] == '1'){ echo 'checked'; } ?>>
                                        <span class="slider round"></span>
                                      </label>
                                      </td>
                                      <td class="Polaris-IndexTable__TableCell">
                                      <label class="switch">
                                        <input type="checkbox" class="email_template_notification" data-table = "email_notification_setting" data-field="<?php echo 'customer_'.$value; ?>" <?php if($email_notification['customer_'.$value] == '1'){ echo 'checked'; } ?>>
                                        <span class="slider round"></span>
                                      </label>
                                      </td>
                                      <td class="Polaris-IndexTable__TableCell"></td>
                                      <td class="Polaris-IndexTable__TableCell">
                                      <span onmouseover="show_title(this)" onmouseout="hide_title(this)">
                                          <button class="Polaris-Button edit-template" type="button" data-template = "<?php echo $value; ?>"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" height="20px" width="20px"><path d="M14.846 1.403l3.752 3.753.625-.626A2.653 2.653 0 0015.471.778l-.625.625zm2.029 5.472l-3.752-3.753L1.218 15.028 0 19.998l4.97-1.217L16.875 6.875z" fill="#5C5F62"></path></svg></button>
                                      </span>
                                      <span class="Polaris-PositionedOverlay display-hide-label">
                                            <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                              <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">Manage Template</div>
                                            </div>
                                      </span>
                                      <span onmouseover="show_title(this)" onmouseout="hide_title(this)">
                                        <button class="Polaris-Button send_test_email" data-template = "<?php echo $value; ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" height="20px" width="20px"><path d="M3.415.189a1 1 0 011.1-.046l15 9a1 1 0 010 1.714l-15 9a1 1 0 01-1.491-1.074L4.754 11H10a1 1 0 100-2H4.753l-1.73-7.783A1 1 0 013.416.189z" fill="#5C5F62"/></svg></button>
                                      </span>
                                      <span class="Polaris-PositionedOverlay display-hide-label">
                                          <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                            <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">Send Test Mail</div>
                                          </div>
                                      </span>
                                    </td>
                                      </tr>
                                   <?php } ?>
                                </tbody>
                                </table>
                            </div>
                            </div>
                            <div>
                            </div>
                        </div>
                        <div class="Polaris-IndexTable__ScrollBarContainer Polaris-IndexTable--scrollBarContainerHidden">
                            <div class="Polaris-IndexTable__ScrollBar" style="--pc-index-table-scroll-bar-content-width:828px;">
                            <div class="Polaris-IndexTable__ScrollBarContent">
                            </div>
                            </div>
                        </div>
                        </div>
                        </div>
                        </div>
            <div id="PolarisPortalsContainer"></div>
         </div>
   </div>
</div>
<?php include('../../footer.php'); ?>