<?php
//  $pending_email_and_store_data = $mainobj->customQuery("SELECT * FROM email_counter,store_details where email_counter.store_id = '$mainobj->store_id' and store_details.store_id = '$mainobj->store_id'");
 $total_new_contracts = $mainobj->customQuery("SELECT COUNT(*) AS new_contracts FROM subscriptionOrderContract where store_id = '$mainobj->store_id' and new_contract = '1'");
//  echo "<script>jQuery('#sd_subscriptionLoader').remove();</script>";
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
                        <div class="Polaris-Banner Polaris-Banner--statusCritical Polaris-Banner--withinPage sd_backend_header <?php if($active_plan_id == 3){ echo 'sd_plan_upgraded'; }?>" tabindex="0" role="status" aria-live="polite" aria-labelledby="PolarisBanner8Heading" aria-describedby="PolarisBanner8Content">
                        <?php if($active_plan_id != 3){ ?>
                            <div class="sd_backend_header upgrade_plan_topbar"></div>
                        <?php } ?>
                            <!-- <div class="sd_backend_header">
                                <div class="Polaris-Banner__Ribbon">
                                    <span class="Polaris-Icon Polaris-Icon--colorCritical Polaris-Icon--applyColor">
                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                        <path d="M11.768.768a2.5 2.5 0 0 0-3.536 0L.768 8.232a2.5 2.5 0 0 0 0 3.536l7.464 7.464a2.5 2.5 0 0 0 3.536 0l7.464-7.464a2.5 2.5 0 0 0 0-3.536L11.768.768zM9 6a1 1 0 1 1 2 0v4a1 1 0 1 1-2 0V6zm2 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"></path>
                                    </svg>
                                    </span>
                                </div>
                                <div class="Polaris-Banner__ContentWrapper">
                                    <div class="Polaris-Banner__Content" id="PolarisBanner8Content">
                                        <p><b class="Polaris-Heading">Important!</b>Do you need any help or have any suggestion? <a style=" color: #ff0000; text-decoration: none; font-weight: bold; "data-redirect-link="contactUs.php" data-query-string="" class="navigate_element sd_edit_subscription <?php// if($current_page == 'contactUs.php'){ echo 'sd_selectedList'; }?>"  href="javascript:void(0)">contact us</a></p>
                                    </div>
                                </div>
                            </div> -->
                            <!-- <div class="sd_pendingMails">Pending Mails :- <?php// echo $pending_email_and_store_data[0]['pending_emails']; ?></div> -->
                           <?php if($app_status == 'false' || $check_selling_plan == '0' || $check_shopify_payment == '' ) { ?><button class="Polaris-Button Polaris-Button--plain sd_stepForm" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Configure App</span></span></button><?php } ?>
                           </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div aria-label="Navigation" class="Polaris-Frame__Navigation AppSdNavOuter" id="AppFrameNav" >
                <nav class="Polaris-Navigation">
                    <div class="Polaris-Navigation__LogoContainer" style="display:block;"><a attr-href="front" class="Polaris-Navigation__LogoLink redirect_link" href="javascript:void(0)" data-polaris-unstyled="true" style="width: 181px;"><img src="<?php echo $mainobj->image_folder; ?>logo.png" alt="Advanced Subscription"  class="Polaris-Navigation__Logo" style="width: 181px;"></a></div>
                    <div class="Polaris-Navigation__PrimaryNavigation Polaris-Scrollable Polaris-Scrollable--vertical" data-polaris-scrollable="true">
                        <ul class="Polaris-Navigation__Section Polaris-Navigation__Section--withSeparator">
                           <li class="Polaris-Navigation__SectionHeading">
                                <span class="Polaris-Navigation__Text">Main Menu</span>
                            </li>
                            <li class="Polaris-Navigation__ListItem">
                               <div class="Polaris-Navigation__ItemWrapper"><a class="Polaris-Navigation__Item navigate_element  <?php if($current_page == 'dashboard.php'){ echo 'sd_selectedList'; }?>" data-query-string="" data-redirect-link="dashboard.php" data-confirmbox="no" tabindex="0" href="javascript:void(0)" data-polaris-unstyled="true">
                                <div class="Polaris-Navigation__Icon"><span class="Polaris-Icon">  <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                    <path d="M18 7.261V17.5c0 .841-.672 1.5-1.5 1.5h-2c-.828 0-1.5-.659-1.5-1.5V13H7v4.477C7 18.318 6.328 19 5.5 19h-2c-.828 0-1.5-.682-1.5-1.523V7.261a1.5 1.5 0 0 1 .615-1.21l6.59-4.82a1.481 1.481 0 0 1 1.59 0l6.59 4.82A1.5 1.5 0 0 1 18 7.26z"></path>
                                </svg></span></div><span  class="Polaris-Navigation__Text">Dashboard</span>
                                </a></div>
                            </li>
                            <li class="Polaris-Navigation__ListItem">
                               <div class="Polaris-Navigation__ItemWrapper"><a class="Polaris-Navigation__Item navigate_element  <?php if($current_page == 'subscription_group.php'){ echo 'sd_selectedList'; }?>" data-query-string="" data-redirect-link="plans/subscription_group.php" data-confirmbox="no" tabindex="0" href="javascript:void(0)" data-polaris-unstyled="true">
                                <div class="Polaris-Navigation__Icon"><span class="Polaris-Icon"><svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M15.284 14.546A2.975 2.975 0 0117 14c1.654 0 3 1.346 3 3s-1.346 3-3 3-3-1.346-3-3c.004-.279.047-.555.129-.822l-1.575-1.125A3.964 3.964 0 0110 16a3.964 3.964 0 01-2.554-.947l-1.575 1.125c.076.262.129.535.129.822 0 1.654-1.346 3-3 3s-3-1.346-3-3 1.346-3 3-3c.615 0 1.214.191 1.716.546l1.56-1.114A3.97 3.97 0 016 12c0-1.858 1.28-3.411 3-3.858V5.815A2.993 2.993 0 017 3c0-1.654 1.346-3 3-3s3 1.346 3 3a2.996 2.996 0 01-2 2.816v2.326c1.72.447 3 2 3 3.858-.003.49-.096.976-.276 1.432l1.56 1.114zm1.037 3.146A1 1 0 0017 18a1 1 0 000-2 1 1 0 00-.679 1.692zm-14 0A1 1 0 003 18a1 1 0 000-2 1 1 0 00-.679 1.692zM11 3c0-.551-.449-1-1-1-.551 0-1 .449-1 1 0 .551.449 1 1 1 .551 0 1-.449 1-1z" fill="#5C5F62"/></svg></span></div><span  class="Polaris-Navigation__Text">My Plans</span>
                                </a></div>
                            </li>
                            <li class="Polaris-Navigation__ListItem">
                            <div class="Polaris-Navigation__ItemWrapper"><a class="Polaris-Navigation__Item navigate_element <?php if($current_page == 'subscriptions.php' || $current_page == 'subscriptionContract.php'){ echo 'sd_selectedList'; }?>" data-query-string="" data-redirect-link="subscription/subscriptions.php" data-confirmbox="no" tabindex="0" href="javascript:void(0)" data-polaris-unstyled="true">
                                <div class="Polaris-Navigation__Icon"><span class="Polaris-Icon"><svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M1 3.5A1.5 1.5 0 012.5 2h15A1.5 1.5 0 0119 3.5v2A1.5 1.5 0 0117.5 7h-15A1.5 1.5 0 011 5.5v-2zm3.5 1a1 1 0 11-2 0 1 1 0 012 0zM1 9.5A1.5 1.5 0 012.5 8h6.073a1.5 1.5 0 011.342 2.17l-1 2a1.5 1.5 0 01-1.342.83H2.5A1.5 1.5 0 011 11.5v-2zm3.5 1a1 1 0 11-2 0 1 1 0 012 0zM1 15.5A1.5 1.5 0 012.5 14h5.27a1.5 1.5 0 011.471 1.206l.4 2A1.5 1.5 0 018.171 19H2.5A1.5 1.5 0 011 17.5v-2zm3.5 1a1 1 0 11-2 0 1 1 0 012 0zM12.159 13.059l-.682-.429a.987.987 0 01-.452-.611.946.946 0 01.134-.742.983.983 0 01.639-.425 1.023 1.023 0 01.758.15l.682.427c.369-.31.8-.54 1.267-.676V9.97c0-.258.104-.504.291-.686.187-.182.44-.284.704-.284.264 0 .517.102.704.284a.957.957 0 01.291.686v.783c.472.138.903.37 1.267.676l.682-.429a1.02 1.02 0 01.735-.107c.25.058.467.208.606.419.14.21.19.465.141.71a.97.97 0 01-.403.608l-.682.429a3.296 3.296 0 010 1.882l.682.43a.987.987 0 01.452.611.946.946 0 01-.134.742.982.982 0 01-.639.425 1.02 1.02 0 01-.758-.15l-.682-.428c-.369.31-.8.54-1.267.676v.783a.957.957 0 01-.291.686A1.01 1.01 0 0115.5 19a1.01 1.01 0 01-.704-.284.957.957 0 01-.291-.686v-.783a3.503 3.503 0 01-1.267-.676l-.682.429a1.02 1.02 0 01-.75.132.999.999 0 01-.627-.421.949.949 0 01-.135-.73.97.97 0 01.434-.61l.68-.43a3.296 3.296 0 010-1.882zm3.341-.507c-.82 0-1.487.65-1.487 1.449s.667 1.448 1.487 1.448c.82 0 1.487-.65 1.487-1.448 0-.8-.667-1.45-1.487-1.45z" fill="#5C5F62"/></svg>
                                </span></div><span class=" Polaris-Navigation__Text">Subscriptions</span>
                                <?php if($total_new_contracts[0]['new_contracts'] > 0){?>
                                <div p-color-scheme="light"><span class="Polaris-Badge Polaris-Badge--statusAttention"><span class="Polaris-VisuallyHidden">Inactive</span><span><?php echo $total_new_contracts[0]['new_contracts']; ?>+</span></span>
                                <div id="PolarisPortalsContainer"></div>
                                </div>
                                <?php } ?>
                                </a></div>
                            </li>

            <li class="Polaris-Navigation__ListItem">
                <div class="Polaris-Navigation__ItemWrapper"><a class="Polaris-Navigation__Item navigate_element <?php if($current_page == 'subscriptionOrders.php'){ echo 'sd_selectedList'; }?>" data-query-string="" data-redirect-link="subscriptionOrders.php" data-confirmbox="no" tabindex="0" href="javascript:void(0)" data-polaris-unstyled="true">
                    <div class="Polaris-Navigation__Icon"><span class="Polaris-Icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.976 3.5a2.75 2.75 0 0 0-2.72 2.347l-.662 4.46a8.75 8.75 0 0 0-.094 1.282v1.661a3.25 3.25 0 0 0 3.25 3.25h6.5a3.25 3.25 0 0 0 3.25-3.25v-1.66c0-.43-.032-.858-.095-1.283l-.66-4.46a2.75 2.75 0 0 0-2.72-2.347h-6.05Zm-1.237 2.567a1.25 1.25 0 0 1 1.237-1.067h6.048c.62 0 1.146.454 1.237 1.067l.583 3.933h-2.484a1.25 1.25 0 0 0-1.185.855l-.159.474a.25.25 0 0 1-.237.171h-1.558a.25.25 0 0 1-.237-.17l-.159-.475a1.25 1.25 0 0 0-1.185-.855h-2.484l.583-3.933Zm-.738 5.433-.001.09v1.66c0 .966.784 1.75 1.75 1.75h6.5a1.75 1.75 0 0 0 1.75-1.75v-1.75h-2.46l-.1.303a1.75 1.75 0 0 1-1.66 1.197h-1.56a1.75 1.75 0 0 1-1.66-1.197l-.1-.303h-2.46Z" fill="#5C5F62"/></svg></span></div><span  class=" Polaris-Navigation__Text">Subscription Orders</span>
                    </a></div>
            </li>

        <li class="Polaris-Navigation__ListItem">
          <div class="Polaris-Navigation__ItemWrapper"><a class="Polaris-Navigation__Item navigate_element <?php if($current_page == 'setting.php'){ echo 'sd_selectedList'; }?>" data-query-string="" data-redirect-link="settings/setting.php" data-confirmbox="no" tabindex="0" href="javascript:void(0)" data-polaris-unstyled="true">
              <div class="Polaris-Navigation__Icon"><span class="Polaris-Icon"><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path fill-rule="evenodd" d="M9.027 0a1 1 0 0 0-.99.859l-.37 2.598A6.993 6.993 0 0 0 5.742 4.57l-2.437-.98a1 1 0 0 0-1.239.428L.934 5.981a1 1 0 0 0 .248 1.287l2.066 1.621a7.06 7.06 0 0 0 0 2.222l-2.066 1.621a1 1 0 0 0-.248 1.287l1.132 1.962a1 1 0 0 0 1.239.428l2.438-.979a6.995 6.995 0 0 0 1.923 1.113l.372 2.598a1 1 0 0 0 .99.859h2.265a1 1 0 0 0 .99-.859l.371-2.598a6.995 6.995 0 0 0 1.924-1.112l2.438.978a1 1 0 0 0 1.238-.428l1.133-1.962a1 1 0 0 0-.249-1.287l-2.065-1.621a7.063 7.063 0 0 0 0-2.222l2.065-1.621a1 1 0 0 0 .249-1.287l-1.133-1.962a1 1 0 0 0-1.239-.428l-2.437.979a6.994 6.994 0 0 0-1.924-1.113L12.283.86a1 1 0 0 0-.99-.859H9.027zm1.133 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"></path></svg></span></div><span  class=" Polaris-Navigation__Text">Settings</span>
            </a></div>
        </li>
        <li class="Polaris-Navigation__ListItem">
          <div class="Polaris-Navigation__ItemWrapper"><a class="Polaris-Navigation__Item navigate_element <?php if($current_page == 'analytics.php'){ echo 'sd_selectedList'; }?>" data-query-string="" data-redirect-link="analytics.php" data-confirmbox="no" tabindex="0" href="javascript:void(0)" data-polaris-unstyled="true">
              <div class="Polaris-Navigation__Icon"><span class="Polaris-Icon"><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
            <path d="M15.5 3A1.5 1.5 0 0014 4.5v12a1.5 1.5 0 001.5 1.5h1a1.5 1.5 0 001.5-1.5v-12A1.5 1.5 0 0016.5 3h-1zM8 8.5A1.5 1.5 0 019.5 7h1A1.5 1.5 0 0112 8.5v8a1.5 1.5 0 01-1.5 1.5h-1A1.5 1.5 0 018 16.5v-8zM2 12.5A1.5 1.5 0 013.5 11h1A1.5 1.5 0 016 12.5v4A1.5 1.5 0 014.5 18h-1A1.5 1.5 0 012 16.5v-4z"></path>
        </svg></span></div><span  class=" Polaris-Navigation__Text">Analytics</span>
            </a></div>
        </li>
                        </ul>
                        <ul class="Polaris-Navigation__Section Polaris-Navigation__Section--withSeparator">
                            <li class="Polaris-Navigation__SectionHeading">
                                <span class="Polaris-Navigation__Text">App Setup</span>
                            </li>
                            <li class="Polaris-Navigation__ListItem">
                        <div class="Polaris-Navigation__ItemWrapper"><a class="Polaris-Navigation__Item navigate_element <?php if($current_page == 'theme_integration.php'){ echo 'sd_selectedList'; }?>" data-query-string="" data-redirect-link="settings/theme_integration.php" data-confirmbox="no" tabindex="0" href="javascript:void(0)" data-polaris-unstyled="true">
                            <div class="Polaris-Navigation__Icon"><span class="Polaris-Icon"><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg_375hu" focusable="false" aria-hidden="true">
                            <path fill-rule="evenodd" d="M1.5 0A1.5 1.5 0 000 1.5v4A1.5 1.5 0 001.5 7H3v11.5A1.5 1.5 0 004.5 20H8a1 1 0 100-2H5V7h1.5A1.5 1.5 0 008 5.5V5h7a1 1 0 102 0v-.5A1.5 1.5 0 0015.5 3H8V1.5A1.5 1.5 0 006.5 0h-5zM2 2v3h4V2H2z"></path>
                            <path fill-rule="evenodd" d="M9 8a1 1 0 00-1 1v5a1 1 0 00.293.707l5 5a1 1 0 001.414 0l5-5a1 1 0 000-1.414l-5-5A1 1 0 0014 8H9zm4 4a1 1 0 11-2 0 1 1 0 012 0z"></path>
                        </svg></span></div>
                                <span class=" Polaris-Navigation__Text">Theme Integrate</span>
                            </a></div>
                        </li>
                        <li class="Polaris-Navigation__ListItem">
                        <div class="Polaris-Navigation__ItemWrapper"><a class="Polaris-Navigation__Item navigate_element <?php if($current_page == 'documentation.php'){ echo 'sd_selectedList'; }?>" data-query-string="" data-redirect-link="documentation.php" data-confirmbox="no" tabindex="0" href="javascript:void(0)" data-polaris-unstyled="true">
                            <div class="Polaris-Navigation__Icon"><span class="Polaris-Icon"><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg_375hu" focusable="false" aria-hidden="true">
                            <path fill-rule="evenodd" d="M1 2.5A1.5 1.5 0 012.5 1h15A1.5 1.5 0 0119 2.5v15a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 011 17.5v-15zM8 5h8v2H8V5zm8 4H8v2h8V9zm-8 4h8v2H8v-2zM5 7a1 1 0 100-2 1 1 0 000 2zm1 3a1 1 0 11-2 0 1 1 0 012 0zm-1 5a1 1 0 100-2 1 1 0 000 2z"></path></svg></span></div>
                            <span class=" Polaris-Navigation__Text">Documentation</span>
                            </a></div>
                        </li>
                        <li class="Polaris-Navigation__ListItem">
                        <div class="Polaris-Navigation__ItemWrapper"><a class="Polaris-Navigation__Item navigate_element <?php if($current_page == 'video_tutorials.php'){ echo 'sd_selectedList'; }?>" data-query-string="" data-redirect-link="video_tutorials.php" data-confirmbox="no" tabindex="0" href="javascript:void(0)" data-polaris-unstyled="true">
                            <div class="Polaris-Navigation__Icon"><span class="Polaris-Icon"><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg_375hu" focusable="false" aria-hidden="true">
                            <path fill-rule="evenodd" d="M1 2.5A1.5 1.5 0 012.5 1h15A1.5 1.5 0 0119 2.5v15a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 011 17.5v-15zM8 5h8v2H8V5zm8 4H8v2h8V9zm-8 4h8v2H8v-2zM5 7a1 1 0 100-2 1 1 0 000 2zm1 3a1 1 0 11-2 0 1 1 0 012 0zm-1 5a1 1 0 100-2 1 1 0 000 2z"></path></svg></span></div>
                            <span class=" Polaris-Navigation__Text">Video Tutorials</span>
                            </a></div>
                        </li>
                        </ul>
                        <ul class="Polaris-Navigation__Section Polaris-Navigation__Section--withSeparator">
                        <li class="Polaris-Navigation__SectionHeading">
                                <span class="Polaris-Navigation__Text">Others</span>
                        </li>
                        <li class="Polaris-Navigation__ListItem">
                        <div class="Polaris-Navigation__ItemWrapper"><a class="Polaris-Navigation__Item navigate_element <?php if($current_page == 'contactUs.php'){ echo 'sd_selectedList'; }?>" data-query-string="" data-redirect-link="contactUs.php" data-confirmbox="no" tabindex="0" href="javascript:void(0)" data-polaris-unstyled="true">
                            <div class="Polaris-Navigation__Icon"><span class="Polaris-Icon"><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg_375hu" focusable="false" aria-hidden="true">
                            <path d="M19.707 15.293l-3-3a1.001 1.001 0 00-1.414 1.414L16.586 15H13a1 1 0 000 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z"></path><path d="M18.5 1c.357 0 .686.125.943.334L10 6.842.557 1.334C.814 1.125 1.143 1 1.5 1h17zM0 13.5V3.324l9.496 5.54a1 1 0 001.008 0L20 3.324V10h-4a6.002 6.002 0 00-5.917 5H1.5A1.5 1.5 0 010 13.5z"></path>
                        </svg></span></div>
                                <span class=" Polaris-Navigation__Text">Contact Us</span>
                            </a></div>
                        </li>
                        <?php //if($mainobj->new_install == '0'){ ?>
                        <li class="Polaris-Navigation__ListItem">
                        <div class="Polaris-Navigation__ItemWrapper"><a class="Polaris-Navigation__Item navigate_element <?php if($current_page == 'app_plans.php'){ echo 'sd_selectedList'; }?>" data-query-string="" data-redirect-link="app_plans.php" data-confirmbox="no" tabindex="0" href="javascript:void(0)" data-polaris-unstyled="true">
                            <div class="Polaris-Navigation__Icon"><span class="Polaris-Icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M11 1a2 2 0 0 0-2 2v3H7a2 2 0 0 0-2 2v3H3a2 2 0 0 0-2 2v3c0 1.1.9 2 2 2h14a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-6Zm0 2h5v3h-5V3ZM7 8h5v3H7V8Zm-4 5h5v3H3v-3Z" fill="#5C5F62"/></svg></span></div>
                                <span class=" Polaris-Navigation__Text">App Plans</span>
                            </a></div>
                        </li>
                        <?php //} ?>
                        </ul>
                        <!-- <ul class="Polaris-Navigation__Section Polaris-Navigation__Section--withSeparator">
                            <li class="Polaris-Navigation__SectionHeading">
                                <span class="Polaris-Navigation__Text">LOGGED-IN USER</span>
                            </li>
                            <li class="Polaris-Navigation__ListItem">
                                <button type="button" class="Polaris-Navigation__Item">
                                    <div class="Polaris-Navigation__Icon">
                                        <span aria-label="Avatar with initials D" role="img" class="Polaris-Avatar Polaris-Avatar--sizeSmall Polaris-Avatar--styleFour">
                                            <span class="Polaris-Avatar__Initials">
                                                <svg class="Polaris-Avatar__Svg" viewBox="0 0 40 40">
                                                    <text x="50%" y="50%" dy="0.35em" fill="#202223" font-size="20" text-anchor="middle"><?php// echo $pending_email_and_store_data[0]['owner_name'][0]; ?></text>
                                                </svg>
                                            </span>
                                        </span>
                                    </div>
                                    <span class="Polaris-Navigation__Text">
                                        <p class="Polaris-TopBar-UserMenu__Name"><?php //echo $pending_email_and_store_data[0]['owner_name']; ?></p>
                                        <p class="Polaris-TopBar-UserMenu__Detail"><?php //echo $pending_email_and_store_data[0]['shop_name']; ?></p>
                                    </span>
                                </button>
                            </li>
                        </ul> -->
                        <ul class="Polaris-Navigation__Section Polaris-Navigation__Section--withSeparator sd-AppBottom-Enable-Disable sd-app-enabled <?php if($app_status == 'false'){ echo 'app_disabled'; }else{ echo 'app_enabled'; } ?>">
                            <li class="Polaris-Navigation__SectionHeading">
                            <a class="Polaris-Link" href="https://<?php echo $mainobj->store; ?>/admin/themes/current/editor?context=apps&activateAppId=<?php echo $mainobj->app_extension_id; ?>/<?php echo $mainobj->theme_block_name; ?>" data-polaris-unstyled="true" target="_blank"><span class="Polaris-Navigation__Text">Enable/Disable App</span></a>
                            </li>
                        </ul>
                        <ul class="Polaris-Navigation__Section Polaris-Navigation__Section--withSeparator">
                            <li class="Polaris-Navigation__ListItem">
                                <div class="Polaris-Navigation__ItemWrapper">
                                    <a class="Polaris-Navigation__Item navigate_element" href="https://<?php echo $store; ?>/admin/apps" data-polaris-unstyled="true">
                                        <div class="Polaris-Navigation__Icon">
                                            <span class="Polaris-Icon">
                                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M17 9H5.414l3.293-3.293a.999.999 0 1 0-1.414-1.414l-5 5a.999.999 0 0 0 0 1.414l5 5a.997.997 0 0 0 1.414 0 .999.999 0 0 0 0-1.414L5.414 11H17a1 1 0 1 0 0-2z"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <span class="Polaris-Navigation__Text">Back to Shopify</span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <button type="button" class="Polaris-Frame__NavigationDismiss" aria-hidden="true" aria-label="Close navigation" tabindex="-1">
                    <span class="Polaris-Icon">
                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                            <path d="M11.414 10l6.293-6.293a1 1 0 1 0-1.414-1.414L10 8.586 3.707 2.293a1 1 0 0 0-1.414 1.414L8.586 10l-6.293 6.293a1 1 0 1 0 1.414 1.414L10 11.414l6.293 6.293A.998.998 0 0 0 18 17a.999.999 0 0 0-.293-.707L11.414 10z"></path>
                        </svg>
                    </span>
                </button>
            </div>
        </div>
        <main class="Polaris-Frame__Main" id="AppFrameMain" data-has-global-ribbon="false" style="
    padding-left: calc(26rem + env(safe-area-inset-left));padding-top: 7.6rem;
">
            <div class="Polaris-Frame__Content">
                <div class="Polaris-Page sd-preorder-page-width">
                <!-- <span class="Polaris-Spinner Polaris-Spinner--sizeLarge" id="sd_subscriptionLoader">
                <svg viewBox="0 0 44 44" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15.542 1.487A21.507 21.507 0 00.5 22c0 11.874 9.626 21.5 21.5 21.5 9.847 0 18.364-6.675 20.809-16.072a1.5 1.5 0 00-2.904-.756C37.803 34.755 30.473 40.5 22 40.5 11.783 40.5 3.5 32.217 3.5 22c0-8.137 5.3-15.247 12.942-17.65a1.5 1.5 0 10-.9-2.863z">
                    </path>
                </svg> -->
                <!-- <div class="polaris-backdrop"></div> -->
                <!-- </span> -->

