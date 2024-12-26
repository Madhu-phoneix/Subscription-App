<?php
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);
    include("../application/controller/Mainfunction.php");
    $mainobj = new MainFunction($store);
    $store_name = strtok($store, '.');
?>

 <?php
    //check that the store entry exist in both install and storeInstallOffer table
    $whereCondition = array(
      'store' => $store
    );
    $checkInstallOffer = $mainobj->table_row_value('InstallAndStoreinstalloffer','All',$whereCondition,'and',''); //CALLING A VIEW
    //if entry not exist in any of the two table then redirect it in the auth screen to generate access token
    if(empty($checkInstallOffer)){
        $install_action_url = "https://{$store}/admin/oauth/authorize?client_id={$mainobj->SHOPIFY_APIKEY}&scope={$mainobj->SHOPIFY_SCOPES}&redirect_uri={$mainobj->SHOPIFY_REDIRECT_URI}"; // oath Screen
        header("Location: ".$install_action_url);
    }else{
        if($checkInstallOffer[0]['store_id'] == ''){
            $install_action_url = "/admin/memberPlans.php";
        }else{
            $install_action_url = "/admin/dashboard.php";
        }
        echo "<script>open('".$install_action_url."?shop=".$store."', '_self'); </script>";
    }

