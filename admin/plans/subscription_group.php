<?php
   header("Access-Control-Allow-Origin: *");
   header('Frame-Ancestors: ALLOWALL');
   header('X-Frame-Options: ALLOWALL');
   header('Set-Cookie: cross-site-cookie=bar; SameSite=None; Secure');
   header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
   header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);
   require_once("../header.php");
   require_once("../navigation.php");
   echo "<script>pre_selected_products ={};</script>";
   function sellingPlanFormError($error_name,$plan_error){
      return '<div class="Polaris-Labelled__Error display-hide-label frequency-plan-error '.$error_name.'">
         <div id="PolarisTextField4Error" class="Polaris-InlineError">
         <div class="Polaris-InlineError__Icon">
            <span class="Polaris-Icon">
               <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                  <path d="M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16zM9 9a1 1 0 0 0 2 0V7a1 1 0 1 0-2 0v2zm0 4a1 1 0 1 0 2 0 1 1 0 0 0-2 0z"></path>
               </svg>
            </span>
         </div>
         '.$plan_error.'
         </div>
      </div>';
   }
?>
<script> let disabled_product_variant_array = []; </script>
<!-- <div id="PolarisPortalsContainer" class="t-right top-banner-create-subscription">
   <button class="Polaris-Button Polaris-Button--primary CreateSubscriptipnGroup sd_button" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Create Plan</span></span></button>
</div> -->
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