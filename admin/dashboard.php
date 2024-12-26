<?php
    include("header.php");
    include $dirPath."/graphLoad/autoload.php";
    use PHPShopify\ShopifySDK;
    $get_webhooks_count = PostPutApi('https://'. $store.'/admin/api/'.$SHOPIFY_API_VERSION.'/webhooks/count.json','GET',$access_token,'');
    if($get_webhooks_count['count'] < 15){
        $create_webhooks = array(
            "APP_UNINSTALLED" => $SHOPIFY_DOMAIN_URL."/application/webhooks/app_uninstall.php",
            "PRODUCTS_DELETE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/product_delete.php",
            "PRODUCTS_UPDATE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/product_update.php",
            "CUSTOMER_PAYMENT_METHODS_UPDATE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/customer_payment_methods_update.php",
            "CUSTOMER_PAYMENT_METHODS_CREATE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/customer_payment_methods_create.php",
            "CUSTOMER_PAYMENT_METHODS_REVOKE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/customer_payment_methods_revoke.php",
            "CUSTOMERS_UPDATE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/customers_update.php",
            "SUBSCRIPTION_CONTRACTS_CREATE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/subscription_contracts_create.php",
            "SUBSCRIPTION_CONTRACTS_UPDATE" =>  $SHOPIFY_DOMAIN_URL."/application/webhooks/subscription_contracts_update.php",
            "SUBSCRIPTION_BILLING_ATTEMPTS_SUCCESS" => $SHOPIFY_DOMAIN_URL."/application/webhooks/subscription_billing_attempts_success.php",
            "SUBSCRIPTION_BILLING_ATTEMPTS_FAILURE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/subscription_billing_attempts_failure.php",
            "SUBSCRIPTION_BILLING_ATTEMPTS_CHALLENGED"=>$SHOPIFY_DOMAIN_URL."/application/webhooks/subscription_billing_attempts_challenged.php",
            "SHOP_UPDATE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/shop_update.php",
            "THEMES_UPDATE" => $SHOPIFY_DOMAIN_URL."/application/webhooks/theme_update.php",
            "ORDERS_UPDATED" => $SHOPIFY_DOMAIN_URL."/application/webhooks/order_update.php",
        );
        $config = array(
            'ShopUrl' => $store,
            'AccessToken' => $access_token,
        );
        $shopify_graphql_object = new ShopifySDK($config);
        foreach($create_webhooks as $key=>$val){
            try{
                $createWebhook =  'mutation webhookSubscriptionCreate($topic: WebhookSubscriptionTopic!, $webhookSubscription: WebhookSubscriptionInput!) {
                    webhookSubscriptionCreate(topic: $topic, webhookSubscription: $webhookSubscription) {
                    userErrors {
                        field
                        message
                    }
                    webhookSubscription {
                        id
                    }
                    }
                }';
                $webhookParameters = [
                    "topic"=> $key,
                    "webhookSubscription" => [
                        "callbackUrl"=> $val,
                        "format"=> "JSON"
                    ]
                ];
                $createWebhookGet = $shopify_graphql_object->GraphQL->post($createWebhook,null,null,$webhookParameters);
            }catch(Exception $e) {
                echo ($e->getMessage());
            }
        }
    }

    function PostPutApi($url, $action, $access_token, $arrayfield){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $action,
            CURLOPT_POSTFIELDS => $arrayfield,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "x-shopify-access-token:{$access_token}"
            ),
        ));

		$response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            return json_decode($response, true);
        }
	}
?>
<div class="Polaris-Layout">
<?php
include("navigation.php");
?>
   <div class="Polaris-Frame__TopBar" data-polaris-layer="true" data-polaris-top-bar="true" id="">
        <div class="Polaris-TopBar">
            <button type="button" class="Polaris-TopBar__NavigationIcon" aria-label="Toggle menu">
                <span class="Polaris-Icon">
                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                        <path d="M19 11H1a1 1 0 0 1 0-2h18a1 1 0 1 1 0 2zm0-7H1a1 1 0 0 1 0-2h18a1 1 0 1 1 0 2zm0 14H1a1 1 0 0 1 0-2h18a1 1 0 0 1 0 2z"></path>
                    </svg>
                </span>
            </button>
            <div class="Polaris-TopBar__Contents">
                <div class="sd-import-update">
                    <div class="Polaris-Banner Polaris-Banner--statusCritical Polaris-Banner--withinPage sd_backend_header <?php if($active_plan_id <= 3){ echo 'sd_plan_upgraded'; }?>" tabindex="0" role="status" aria-live="polite" aria-labelledby="PolarisBanner8Heading" aria-describedby="PolarisBanner8Content">
                        <?php if($active_plan_id <= 2){ ?>
                            <div class="sd_backend_header upgrade_plan_topbar"></div>
                        <?php } ?>
                       <button class="Polaris-Button Polaris-Button--plain sd_stepForm" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Configure App</span></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="Polaris-Layout__Section sd-dashboard-page">
 <div>
<div class="sd-updates-section">
<?php if($active_plan_id != 3){ ?>
    <div id="polaris-example" class="PolarisExampleWrapper_Example__NK9R0 sd_funds_note">
        <div class="Polaris-Banner Polaris-Banner--statusInfo Polaris-Banner--hasDismiss Polaris-Banner--withinPage" tabindex="0" role="status" aria-live="polite" aria-labelledby="PolarisBanner1Heading" aria-describedby="PolarisBanner1Content">
            <div class="Polaris-Banner__Ribbon">
                <span class="Polaris-Icon Polaris-Icon--colorHighlight Polaris-Icon--applyColor">
                    <span class="Polaris-Text--root Polaris-Text--bodySm Polaris-Text--regular Polaris-Text--visuallyHidden"></span>
                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 20c5.514 0 10-4.486 10-10s-4.486-10-10-10-10 4.486-10 10 4.486 10 10 10zm1-6a1 1 0 1 1-2 0v-4a1 1 0 1 1 2 0v4zm-1-9a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"></path>
                    </svg>
                </span>
            </div>

            <div class="Polaris-Banner__ContentWrapper">
                <div class="Polaris-Banner__Heading" id="PolarisBanner1Heading">
                    <p class="Polaris-Text--root Polaris-Text--headingMd Polaris-Text--semibold">You need to upgrade your plan, when you exceed <?php echo $currency_code.''.$amount_exceed; ?> in subscription sales. Zero transactions fees.</p>
                </div>
            </div>

        </div>
    </div>
    <?php } ?>
    <?php
    if($total_subscription_order_sale > $amount_exceed && $active_member_plan_status != 'ACTIVE'){ ?>
        <div class="Polaris-Banner Polaris-Banner--statusCritical Polaris-Banner--hasDismiss Polaris-Banner--withinPage sd_contractStatus" tabindex="0" role="status" aria-live="polite" aria-labelledby="PolarisBanner18Heading" aria-describedby="PolarisBanner18Content">
       <div class="Polaris-Banner__ContentWrapper">
        <div class="Polaris-Banner__Heading" id="PolarisBanner18Heading">
                <p class="Polaris-Heading">You've reached your  <?php echo $currency_code.''.$amount_exceed; ?> limit</p>
                <a class="Polaris-Button" href="<?php echo $SHOPIFY_DOMAIN_URL; ?>/admin/app_plans.php?shop=<?php echo $store; ?>" data-polaris-unstyled="true">Upgrade Basic Plan</a>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="Polaris-Page-Header Polaris-Page-Header--isSingleRow Polaris-Page-Header--mobileView Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
        <div class="Polaris-Page-Header__Row">
        <div p-color-scheme="light" class="sd_Suggestion"><a data-redirect-link="contactUs.php?shop=<?php echo $store; ?>" data-query-string="" class="navigate_element sd_Suggestion-btn" href="javascript:void(0)">Suggestion</a>
  <div id="PolarisPortalsContainer"></div>
</div>
        </div>
    </div>
    <div class="Polaris-Page__Content">
        <div class="Polaris-Layout">
            <div class="Polaris-Layout__Section Polaris-Layout__Section--oneHalf">
                <a href="https://apps.shopify.com/smart-monster" target="_blank"><img src="https://cdn.shopify.com/s/files/1/0614/2528/7356/files/728X90-1.jpg?v=1726136458" height="70" width="100%"></img></a>
                <div class="Polaris-Card">
                    <div class="Polaris-Card__Header">
                        <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                            <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                                <h2 class="Polaris-Heading">FAQ's</h2>
                            </div>
                        </div>
                    </div>
                    <div class="Polaris-Card__Section">
                        <div class="Polaris-Card__SectionHeader">
                            <h3 aria-label="Items" class="Polaris-Subheading">Why are my customers not able to see their subscription dashboard?</h3>
                        </div>
                        <div class="Polaris-TextContainer Polaris-TextContainer--spacingLoose" style="margin-bottom: 10px;">
                            <p style="margin: 0 0 10px;">Kindly make sure you have enabled customer accounts. Follow these steps in the Shopify Admin Panel for the same: - <i>Settings -> Checkout -> Customer accounts</i>, here you can either select  <b>“Accounts are optional”</b> or <b>“Accounts are required”</b></p>
<ul><li><strong>Accounts are required:</strong> If this is selected, the User while making checkout will be first prompted to signup/login to the site.</li>
<li><strong>Accounts are optional:</strong>  If this is selected, the User will not be prompted to signup/Login while they make a checkout but once checkout is completed & order is created, automatically customer account will be created with the email id used in the order & customer will receive an email to set the account password. </li>
                        </div>
                    </div>
                </div>
            </div>
            <div class="Polaris-Layout__Section">
                <div class="Polaris-Card">
                    <div class="Polaris-Card__Header">
                        <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                            <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                                <h2 class="Polaris-Heading">Important Notice</h2>
                            </div>
                        </div>
                    </div>
                    <div class="Polaris-Card__Section">
                        <div class="Polaris-Card__SectionHeader">
                            <h3 aria-label="Items" class="Polaris-Subheading"> Why My Plans are not showing on the product page?</h3>

                           <ul class="sd_important_notice">
                                <li> Make Sure you have enabled the app from Theme Customizer. <a class="Polaris-Link" href="https://<?php echo $store; ?>/admin/themes/current/editor?context=apps&activateAppId=<?php echo $app_extension_id; ?>/<?php echo $theme_block_name; ?>" target ="__blank" data-polaris-unstyled="true">Click Here</a> or Follow the Steps in the Shopify Admin Panel: -<br>

                                <p><b>1.</b> From your Shopify admin, go to <b>Online Store > Themes</b>.</p>
                                <p><b>2.</b> Find the theme that you want to edit, and then click <b>Customize</b>.</p>
                                <p><b>3.</b> Click <b>Theme settings</b>.</p>
                                <p><b>4.</b> Click the <b>App embeds </b>tab.</p>
                                <p><b>5.</b> Select the app embed that you want to activate or click the <b>Search</b> bar and enter a search term to search through your installed apps.</p>
                                <p><b>6.</b> Beside the app embed that you want to activate, click the toggle to activate it.</p>
                                </li>
                                <li> If you are using 2.O theme then make Sure you have added the Advanced Subscription app block . <a class="Polaris-Link" href="https://<?php echo $store; ?>/admin/themes/current/editor" target ="__blank" data-polaris-unstyled="true">Click Here</a> or Follow the Steps in the Shopify Admin Panel: -<br>
                                <p><b>1.</b> From your Shopify admin, go to <b>Online Store > Themes</b>.</p>
                                <p><b>2.</b> Find the theme that you want to edit, and then click <b>Customize</b>.</p>
                                <p><b>3.</b> Click <b>Theme settings</b>.</p>
                                <p><b>4.</b> Click the <b>Sections </b>tab.</p>
                                <p><b>5.</b> Add the Advanced Subscription app block.</p>
                                <p><b>6.</b>You can see the subscription widget on the product on which you have added the selling plan in preview.</p>
                                </li>
                                <li>Most importantly, you must activate Shopify payments in order for subscriptions to function on product pages according to Shopify default requirements. <b>(Settings->Payments)</b></li>
                                <li>Product Inventory needs to be more than zero.</li>
                            </ul>
                       <p>If still facing issues, Kindly contact us at <a herf="mailto:your-email.com" class="Polaris-Link sd_supportEmail">your-email.com</a></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="Polaris-Layout__Section" style="display:none;">
                <?php include('sd_otherApps.php') ?>
            </div>
        </div>
    </div>
</div>
 </div>
</div>
<?php
include("footer.php");
?>