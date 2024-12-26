<?php
    include("header.php");
    if(isset($_GET['charge_id'])){
        $db->query("UPDATE `storeInstallOffers` SET status='0' WHERE store_id='$store_id'");
        $charge_id =  $_GET['charge_id'];
        $db->query("UPDATE `storeInstallOffers` SET status='1' WHERE store_id='$store_id' AND appSubscriptionPlanId = '$charge_id'");
    }
    $query = $db->prepare("SELECT * FROM `storeInstallOffers` WHERE store_id='$store_id' and planName = 'Free_old'");
    $query->execute();
    $check_entry = $query->rowCount();
    if($check_entry){
        $active_user_type = 'Free_old';
    }else{
        $active_user_type = 'new_user';
    }
    $current_plan_id = '';
    $all_member_plans_query = $db->query("SELECT * FROM memberPlanDetails WHERE status='1'");
    $all_member_plans_data = $all_member_plans_query->fetchAll(PDO::FETCH_ASSOC);
    $active_plan_id_array_query = $db->query("SELECT * FROM `storeInstallOffers` WHERE status='1' and store_id = '$store_id'");
    $active_plan_id_array = $active_plan_id_array_query->fetch(PDO::FETCH_ASSOC);
    if(!empty($active_plan_id_array)){
        $current_plan_id = $active_plan_id_array['plan_id'];
        //update mail send column in install table
        if(isset($_GET['charge_id'])){
            if($current_plan_id == '3'){
                $db->query("UPDATE `install` SET send_update_billing_mail='yes' WHERE id = '$store_id'");
            }
        }
    }
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
                    </div>
                </div>
            </div>
        </div>
        <div class="Polaris-Card sd_subscription_plan" style="width:100%;padding-bottom:15px;">
            <div class="Polaris-Card__Section">
                <div class="Pricing_plan" style="max-width: 850px;">
                        <?php foreach($all_member_plans_data as $key=>$val){
                            $plan_active = '';
                            if($val['id'] == $active_plan_id_array['plan_id']){
                                $plan_active = 'activated';
                                $plan_array =  $active_plan_id_array;
                            }else{
                                $plan_array = $val;
                            }
                            if(($active_user_type == 'Free_old' && $store == 'boo-kay-nyc.myshopify.com') || ($active_user_type == 'Free_old' && $store == 'advanced-subscriptionpro.myshopify.com')){
                                $order_limit = 1800;
                            }else if($active_user_type == 'Free_old'){
                                $order_limit = 700;
                            }else{
                                $order_limit = 500;
                            }
                            if($val['id'] == '1'){
                               $plan_information = 'Free forever until you generate '.$currency_code.'500 in subscriptions revenue from your store. 0% transaction fees';
                            }else if($val['id'] == '2'){
                               $plan_information = 'Accessible until you generate '.$currency_code.'500 in subscriptions revenue from your store. 0% transaction fees';
                            }else if($val['id'] == '9'){
                                $plan_information = 'We need to check the feasiblity of the custom feature before considering an upgrade to the plan. Please reach out to us and provide details about the custom feature before proceeding with the plan upgrade.';
                            }else{
                                $plan_information = '0% transaction fees';
                            }
                            if($active_plan_id_array['plan_id'] <= $val['id']){
                        ?>
                        <ul class="pricing-item <?php echo $plan_active; ?>">
                            <li>
                          <?php if($plan_active == 'activated'){ ?><span class="sales_badge">Activated</span><?php } ?>$<?php if($store_id == '1069' && $plan_array['planName'] == 'Custom'){echo "75"; }else { echo $plan_array['price']; }?><h3><?php if($plan_array['planName'] == 'Free_old'){ echo 'Free'; }else{ echo $plan_array['planName']; } ?> Plan</h3> 
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
                                <li>Remove restriction of <?php echo $currency_code.''.$order_limit; ?> subscription reveune</li>
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
                    }
                ?>
                </div>
            </div>
        </div>

<?php
  include("footer.php");
?>