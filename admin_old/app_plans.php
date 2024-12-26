<?php
  include("header.php");
  if(isset($_GET['charge_id'])){
    $update_fields = array(
        'status' => '0',
    );
    $wherePlanCondition = array(
        'store_id' => $mainobj->store_id,
    );
    $mainobj->update_row('storeInstallOffers',$update_fields,$wherePlanCondition,'and');

    $update_fields = array(
        'status' => '1',
    );
    $wherePlanCondition = array(
        'store_id' => $mainobj->store_id,
        'appSubscriptionPlanId' => $_GET['charge_id'],
    );
   $mainobj->update_row('storeInstallOffers',$update_fields,$wherePlanCondition,'and');
  }
  //check if the user is old user or new user
$whereUserPlanCondition = array(
    'store_id' => $mainobj->store_id,
    'planName' => 'Free_old',
);
$check_entry  = $mainobj->table_row_check('storeInstallOffers',$whereUserPlanCondition,'and');
if($check_entry){
    $active_user_type = 'Free_old';
}else{
    $active_user_type = 'new_user';
}
$current_plan_id = '';
$all_member_plans_query = $mainobj->db->query("Select * from memberPlanDetails where status='1'");
$all_member_plans_data = $all_member_plans_query->fetchAll(PDO::FETCH_ASSOC);

  $whereStoreCondition = array(
    'store_id' => $mainobj->store_id,
    'status' => '1'
  );
  $fields = array('plan_id','planName','price');
  $active_plan_id_array = $mainobj->single_row_value('storeInstallOffers',$fields,$whereStoreCondition,'and','');
  if(!empty($active_plan_id_array)){
      $current_plan_id = $active_plan_id_array['plan_id'];
      //update mail send column in install table
      if(isset($_GET['charge_id'])){
        $where_install_condition = array(
          'id' => $mainobj->store_id,
        );
        $update_send_billing_mail = array(
          'send_update_billing_mail' => 'yes'
        );
        if($current_plan_id == '3'){
          $mainobj->update_row('install',$update_send_billing_mail,$where_install_condition,'and');
        }
    }
  }
//   echo '<pre>';
//   print_r($active_plan_id);
 ?>
<!-- <div class="Polaris-Layout"> -->
<?php
   include("navigation.php");
?>
        <div class="Polaris-Layout__AnnotatedSection">
            <div class="Polaris-Layout__AnnotationWrapper">
                <div class="Polaris-Layout__Annotation">
                    <div class="Polaris-TextContainer sd-active-plan-center">
                        <h1 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge" style="text-align:center">Plans And Packages</h1>
                        <!-- <h3 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge sd-active-plan" style="text-align:center">Beginner Plan Activated</h3> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="Polaris-Card sd_subscription_plan" style="width:100%;padding-bottom:15px;">
            <div class="Polaris-Card__Section">
                <div class="Pricing_plan" style="max-width: 850px;">
                        <?php foreach($all_member_plans_data as $key=>$val){
                            // if($mainobj->store == 'mytab-shinedezign.myshopify.com'){
                            //   echo '<pre>';
                            //   print_r($val);
                            //   die;
                            // }
                            $plan_active = '';
                            if($val['id'] == $active_plan_id_array['plan_id']){
                                $plan_active = 'activated';
                                $plan_array =  $active_plan_id_array;
                            }else{
                                $plan_array = $val;
                            }
                            if(($active_user_type == 'Free_old' && $mainobj->store == 'boo-kay-nyc.myshopify.com') || ($active_user_type == 'Free_old' && $mainobj->store == 'advanced-subscriptionpro.myshopify.com')){
                                $order_limit = 1800;
                            }else if($active_user_type == 'Free_old'){
                                $order_limit = 700;
                            }else{
                                $order_limit = 500;
                            }
                            if($val['id'] == '1'){
                               $plan_information = 'Free forever until you generate '.$mainobj->currency_code.'500 in subscriptions revenue from your store. 0% transaction fees';
                            }else if($val['id'] == '2'){
                               $plan_information = 'Accessible until you generate '.$mainobj->currency_code.'500 in subscriptions revenue from your store. 0% transaction fees';
                            }else if($val['id'] == '9'){
                                $plan_information = 'We need to check the feasiblity of the custom feature before considering an upgrade to the plan. Please reach out to us and provide details about the custom feature before proceeding with the plan upgrade.';
                            }else{
                                $plan_information = '0% transaction fees';
                            }
                            if($active_plan_id_array['plan_id'] <= $val['id']){
                            // if($val['id'] != 3 && $active_plan_id_array['planName'] == 'Free_old'){
                            // }else{
                        ?>
                        <ul class="pricing-item <?php echo $plan_active; ?>">
                            <li>
                          <?php if($plan_active == 'activated'){ ?><span class="sales_badge">Activated</span><?php } ?>$<?php echo $plan_array['price']; ?><h3><?php if($plan_array['planName'] == 'Free_old'){ echo 'Free'; }else{ echo $plan_array['planName']; } ?> Plan</h3>
                          <small><?php echo $plan_information; ?></small>
                            </li>
                            <?php if($val['id'] == 1){ ?>
                                <li>Subscription creation & management</li>
                                <li>Pay as you go & prepaid plans</li>
                                <li>Unlimited emails</li>
                                <li>Email Support</li>
                            <?php }else if($val['id'] == 2){ ?>
                                <li>All in free +</li>
                                <li>Remove branding</li>
                                <li>Multiple widget layouts</li>
                                <li>Email Support</li>
                            <?php }else if($val['id'] == 3){ ?>
                                <li>All in starter +</li>
                                <li>Remove restriction of <?php echo $mainobj->currency_code.''.$order_limit; ?> subscription reveune</li>
                                <li>Customized email templates</li>
                                <li>Live chat support</li>
                            <?php }else if($val['id'] == 9){ ?>
                                <li>All in Basic +</li>
                                <li>Custom Feature Support</li>
                            <?php } ?>
                           <li>
                            <?php if($plan_active == 'activated'){ ?>
                               <span class="Plan_button Polaris-Button--primary Polaris-Button" disabled>Activated</span>
                            <?php }else{ ?>
                                <button class="Plan_button Polaris-Button--primary Polaris-Button" id="sd_member_plan_activate" attr-plan-name="<?php echo $val['planName']; ?>" attr-plan-price = "<?php echo $val['price']; ?>" attr-plan-id="<?php echo $val['id']; ?>" attr-plan-name="<?php echo $val['planName']; ?>" attr-currentPlan-id="<?php echo $current_plan_id; ?>">SUBSCRIBE</button>
                            <?php } ?>
                            </li>
                        </ul>
                    <?php }
                            // }
                  }
                ?>
                </div>
            </div>
        </div>

<?php
  include("footer.php");
?>