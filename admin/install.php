<?php
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);
    // include("../application/controller/Mainfunction.php");
    include("../application/library/config.php");
    // $mainobj = new MainFunction($store);
    if(isset($_GET['shop'])){
      $store_name = strtok($_GET['shop'], '.');
      $store = $_GET['shop'];
    }
    //check that the store entry exist in both install and storeInstallOffer table

    $query = $db->prepare("SELECT * FROM `InstallAndStoreinstalloffer` WHERE store='$store'");
    $query->execute();
    $row_count = $query->rowCount();

    //if entry not exist in any of the two table then redirect it in the auth screen to generate access token
    if($row_count){
        $query = "SELECT * FROM `InstallAndStoreinstalloffer` WHERE store = '$store'";
        $result = $db->query($query);
        $row_data = $result->fetch(PDO::FETCH_ASSOC);
        if($row_data['store_id'] == ''){
            $install_action_url = "/admin/memberPlans.php";
        }else{
            $install_action_url = "/admin/dashboard.php";
        }
        echo "<script>open('".$install_action_url."?shop=".$store."', '_self'); </script>";
    }else{
        $install_action_url = "https://{$store}/admin/oauth/authorize?client_id={$SHOPIFY_APIKEY}&scope={$SHOPIFY_SCOPES}&redirect_uri={$SHOPIFY_REDIRECT_URI}"; // oath Screen
        echo $install_action_url;
        header("Location: ".$install_action_url);
        // echo "<script>open('".$install_action_url."', '_open'); </script>";

    }


