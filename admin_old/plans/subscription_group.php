<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
   require_once("../header.php");
   require_once("../navigation.php");
   echo "<script>pre_selected_products ={};</script>";
?>
<script> let disabled_product_variant_array = []; </script>
<div id="PolarisPortalsContainer" class="t-right top-banner-create-subscription">
   <button class="Polaris-Button Polaris-Button--primary CreateSubscriptipnGroup sd_button" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Create Plan</span></span></button>
</div>
<div id="create_subscription_wrapper" class="display-hide-label">
<?php require_once("subscription_create.php"); ?>
</div>
<div id="edit_subscription_wrapper" class="display-hide-label">
<?php require_once("subscription_edit.php"); ?>
</div>
<div id="list_subscription_wrapper" class="">
<?php require_once("subscription_list.php"); ?>
</div>
<?php require_once("../footer.php");   ?>