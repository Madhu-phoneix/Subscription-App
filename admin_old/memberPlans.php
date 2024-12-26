<?php
include_once("header.php");
if(!isset($_GET['code'])){
  $pageType = 'after_installation';
  $code = '';
}else{
   $pageType = 'on_installation';
   $code = $_GET['code'];
}
   $recurringBillingValues = array(
      "sd_trialDays" => '3',
      "pageType"=> $pageType,
      "sd_code"=> $code,
      "sd_planName"=> 'Free',
      "sd_configureTheme"=> 'Yes',
      "store" => $store
   );
  $app_installation = $mainobj->recurringBillingCharge($recurringBillingValues);
  $app_installation =  json_decode($app_installation);
  echo "<script>window.top.location.href='".$app_installation->confirmationUrl."'</script>";
?>
<!-- <div class="Polaris-Layout">
<?php
//  $configuretheme = 'Yes';
//  $all_member_plans = $mainobj->customQuery("SELECT memberPlanDetails.id as id,memberPlanDetails.planName as member_planName,memberPlanDetails.id as member_plan_id,memberPlanDetails.subscriptionPlans as member_subscriptionPlans,memberPlanDetails.subscriptionContracts as member_subscriptionContracts,memberPlanDetails.price as member_price,memberPlanDetails.status as member_status,memberPlanDetails.trial as member_trial,storeInstallOffers.store_id as install_store_id,storeInstallOffers.plan_id as install_plan_id,storeInstallOffers.subscriptionPlans as install_subscriptionPlans,storeInstallOffers.planName as install_planName,storeInstallOffers.subscriptionContracts as install_subscriptionContracts,storeInstallOffers.price as install_price,storeInstallOffers.trial as install_trial,storeInstallOffers.status as install_status FROM memberPlanDetails LEFT JOIN storeInstallOffers ON store_id = '$mainobj->store_id'  AND storeInstallOffers.status = '1' AND memberPlanDetails.id = storeInstallOffers.plan_id  ORDER BY memberPlanDetails.id DESC");

// if(!empty($mainobj->store_id)){
//    if (in_array($mainobj->store_id, array_column($all_member_plans, 'install_store_id'))){ // check if entry exist in install and storeinstallOffers table
//       include_once("../assets/navigation.php");
//    }
// }
// $key = array_search('1', array_column($all_member_plans, 'install_status'));
// $current_subscription_price =  $all_member_plans[$key]['install_price'];

?>
<input type = "hidden" value="<?php //echo $pageType; ?>" id="sd_pageType">
<input type = "hidden" value="<?php //echo $code; ?>" id="sd_code">
<div class="tabBox">
<div class="plan-header">
   <h3>Upgrade a Plan</h3>
   <p>If you need more info, please check <b>Pricing Guidelines.</b></p>
</div>

<div class="contentbox_main">
   <div class="tabs-leftside">
      <ul class="tabs">
      <?php //foreach($all_member_plans as $key=>$value){
            // if($value['member_status'] == '1'){  // it means the plan is active currently
            // if(empty($value['install_store_id'])){
            //    $tableType = 'member_';
            //    $liClass = '';
            //    if($value[$tableType.'price'] > $current_subscription_price){
            //       $activateButton = 'upgrade';
            //       $buttonClass = ' up-text Polaris-Button--primary';
            //    }else{
            //       $activateButton = 'downgrade';
            //       $buttonClass = ' down-text Polaris-Button--primary';
            //    }
            // }else{
            //    $tableType = 'install_';
            //    $activateButton = 'Active';
            //    $buttonClass = 'badge active-text Polaris-Button--disabled';
            //    $configuretheme = 'No';
            //    $liClass = 'active';
            // }
            ?>
         <li class="<?php //echo $liClass; ?>">
            <a href="#tab<?php //echo $key; ?>">
               <div class="title-box">
               <h3 aria-label="Contact Information" class="Polaris-Subheading" id="sd_planName_<?php //echo $value[$tableType.'plan_id']; ?>" data-value="<?php //echo  $value[$tableType.'planName']; ?>">
                  <?php //echo $value[$tableType.'planName']; ?>
               </h3>
                 <button class="Polaris-Button badge <?php //echo $buttonClass; ?> sd_selectedPlan" id="<?php //echo $value[$tableType.'plan_id']; ?>" type="button"><span class="Polaris-Button__Content"><span data-query-string="" class="Polaris-Button__Text"></span><?php //echo $activateButton; ?></span></button>
               </div>
               <div class="price-box">
                  <span>$</span>
                  <span class="f-bolder"><?php //echo $value[$tableType.'price']; ?></span><span><small>/month</small></span>
               </div>
            </a>
         </li>
         <?php //} }?>
      </ul>
   </div>

   <div class="tabs-rightcontent">
      <div class="tabContainer">
         <?php
            // foreach($all_member_plans as $key=>$value){
            // if($value['member_status'] == '1'){  // it means the plan is active currently
            // if(empty($value['install_store_id'])){
            //    $tableType = 'member_';
            //    $liClass = '';
            //    if($value[$tableType.'price'] > $current_subscription_price){
            //       $activateButton = 'upgrade';
            //       $buttonClass = ' up-text Polaris-Button--primary';
            //    }else{
            //       $activateButton = 'downgrade';
            //       $buttonClass = ' down-text Polaris-Button--primary';
            //    }
            // }else{
            //    $tableType = 'install_';
            //    $activateButton = 'Active';
            //    $buttonClass = 'badge active-text Polaris-Button--disabled';
            //    $configuretheme = 'No';
            // }
         ?>
         <div id="tab<?php //echo $key; ?>" class="tabContent" style="display:<?php //if($activateButton == 'Active'){ echo "block"; }else{echo "none";} ?>">
            <div class="inner-text-item">
               <h4>What’s in <?php //echo  $value[$tableType.'planName']; ?> Plan?</h4>
              <p>Optimal for 10+ team size and new startup</p>
               <ul class="pricing-listing">
                  <li id= "sd_subscriptionPlans_<?php// echo $value[$tableType.'plan_id']; ?>" data-value = "<?php //echo $value[$tableType.'subscriptionPlans']; ?>">
                     <span>Subscripition Plans</span>
                     <span class="svg-icon svg-icon-1 svg-icon-success">
                        <?php //echo $value[$tableType.'subscriptionPlans']; ?>
                     </span>
                  </li>
                  <li id= "sd_subscriptionContract_<?php //echo $value[$tableType.'plan_id']; ?>" data-value = "<?php //echo $value[$tableType.'subscriptionContracts']; ?>">
                     <span>Subscripition Contracts</span>
                     <span class="svg-icon svg-icon-1 svg-icon-success">
                     <?php //echo $value[$tableType.'subscriptionContracts']; ?>
                     </span>
                  </li>
                  <li id = "sd_planPrice_<?php //echo $value[$tableType.'plan_id']; ?>" data-value = "<?php //echo $value[$tableType.'price']; ?>">
                     <span>Subscription Price</span>
                     <span class="svg-icon svg-icon-1 svg-icon-success">
                     <?php //echo $value[$tableType.'price']; ?> USD
                     </span>
                  </li>
                  <li id= "sd_trialDays_<?php //echo $value[$tableType.'plan_id']; ?>" data-value = "<?php //echo $value[$tableType.'trial']; ?>">
                     <span>Trial</span>
                     <span class="svg-icon svg-icon-1 svg-icon-success">
                     <?php //echo $value[$tableType.'trial']; ?> Days
                     </span>
                  </li>
               </ul>
               <div class="sd_updatePlan">
                 <button class="Polaris-Button badge <?php //echo $buttonClass; ?> sd_selectedPlan" id="<?php //echo $value[$tableType.'plan_id']; ?>" type="button"><span class="Polaris-Button__Content"><span data-query-string="" class="Polaris-Button__Text"></span><?php //echo $activateButton; ?></span></button>
               </div>
            </div>
         </div>
         <?php //} }?>
         <input type="hidden" value="<?php //echo $configuretheme; ?>" id="configureTheme">
      </div>
   </div>
</div>
</div> -->
<?php require_once("footer.php");   ?>

