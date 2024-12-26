<?php
   include("../../modal/Mainfunction.php");
   $mainobj = new MainFunction($store);
   $recurringBillingId =  $_GET['charge_id'];
   $configureTheme = $_GET['themeConfiguration'];
   $getBillingDetails = $mainobj->getRecurringBillingStatus($recurringBillingId);

   $billingStatus =  $getBillingDetails['data']['node']['status'];
   if($billingStatus == 'ACTIVE'){
      $trial_days = $getBillingDetails['data']['node']['trialDays'];


      $planName = $getBillingDetails['data']['node']['name'];

      //get memberPlanDetails
      $whereCondition = array(
         'planName' => $planName
      );
      $updateInstallData =  $mainobj->table_row_value('memberPlanDetails','all',$whereCondition,'and','');

      if($trial_days){
         $insertTrialData = array(
            'plan_id' => $updateInstallData[0]['id'],
            'store' => $store,
            'purchased_on' => date('Y-m-d')
         );
         $saveInstallData = $mainobj->insert_row('trial_used',$insertTrialData);
       }

      $whereStoreCondition = array(
         'store' => $mainobj->store
      );

      //set status 0 to the previous active plan
      $ActiveStoreidCondition = array(
         'store_id' => $mainobj->store_id,
         'status' => '1'
      );
      $fields = array(
         'status' => 0
      );
      $insertInstallData = $mainobj->update_row('storeInstallOffers',$fields,$ActiveStoreidCondition,'and');

      //save selected member plan detail into istallOffer table
      $fields = array(
         'plan_id' => $updateInstallData[0]['id'],
         'store_id' => $mainobj->store_id,
         'planName' => $updateInstallData[0]['planName'],
         'subscriptionPlans' => $updateInstallData[0]['subscriptionPlans'],
         'subscriptionContracts' => $updateInstallData[0]['subscriptionContracts'],
         'price' => $updateInstallData[0]['price'],
         'trial' => $trial_days,
         'created_at' => date('Y-m-d H:i:s'),
         'recurringBillingId' => $recurringBillingId
      );
      $saveInstallData = $mainobj->insert_row('storeInstallOffers',$fields);
      $redirectUrl =  "https://".$mainobj->store."/admin/apps/".$mainobj->SHOPIFY_APIKEY."/".$mainobj->SHOPIFY_DIRECTORY_NAME."/backend/view/dashboard.php?themeConfiguration=".$configureTheme."&shop=".$mainobj->store; // return json
      echo "<script>window.top.location.href='".$redirectUrl."'</script>";
   }else{
      $redirectUrl = "https://".$mainobj->store."/admin/apps/".$mainobj->SHOPIFY_APIKEY."/".$mainobj->SHOPIFY_DIRECTORY_NAME."/backend/view/memberPlans.php?shop=".$mainobj->store; // return json
      echo "<script>window.top.location.href='".$redirectUrl."'</script>";
   }
?>