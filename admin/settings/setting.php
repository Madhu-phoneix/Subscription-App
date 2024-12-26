<?php
include("../header.php");
?>
<div class="Polaris-Layout">
<?php
include("../navigation.php");
?>
  <div class="Polaris-Layout__Section sd-dashboard-page">
      <div>

    <div class="Polaris-Page-Header Polaris-Page-Header--isSingleRow Polaris-Page-Header--mobileView Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
      <div class="Polaris-Page-Header__Row">
        <div class="Polaris-Page-Header__TitleWrapper">
          <div>
            <div class="Polaris-Header-Title__TitleAndSubtitleWrapper">
              <h1 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">Settings</h1>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="Polaris-Page__Content">

    <div class="Polaris-Page__Content">
            <div class="">
                <ul class="generalSetting_list">
                    <li>
                    <div class="sd_susbscriptionSetting">
                    <div class="Polaris-FormLayout">
                      <div role="group" class="Polaris-FormLayout--grouped">
                        <div class="Polaris-FormLayout__Items">
                          <div class="Polaris-FormLayout__Item sd_customerAccountSetting">
                            <div class="Polaris-Card">
                              <div class="Polaris-iconbox">
                                <svg viewBox="0 0 15.2 17.45"><defs><style>.cls-1{fill:#929eab;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><path class="cls-1" d="M7.61,7.7A3.85,3.85,0,1,0,3.75,3.86,3.86,3.86,0,0,0,7.61,7.7Z"></path><path class="cls-1" d="M11,7.92a.44.44,0,0,0-.33.13,4.37,4.37,0,0,1-6.06,0,.48.48,0,0,0-.33-.13A4.61,4.61,0,0,0,0,12.5v2.11a2.83,2.83,0,0,0,2.83,2.84h9.53a2.84,2.84,0,0,0,2.84-2.84V12.5A4.57,4.57,0,0,0,11,7.92ZM5.8,14.09a.41.41,0,0,1,0,.61.43.43,0,0,1-.3.13.43.43,0,0,1-.31-.13L4.08,13.59a.41.41,0,0,1,0-.61l1.11-1.12a.43.43,0,0,1,.61,0,.45.45,0,0,1,0,.64L5,13.3Zm2.92-2.84L7.5,15.59a.42.42,0,0,1-.41.33.26.26,0,0,1-.11,0,.45.45,0,0,1-.31-.55L7.89,11a.44.44,0,0,1,.55-.3A.43.43,0,0,1,8.72,11.25Zm2.42,2.36L10,14.72a.43.43,0,0,1-.3.13.44.44,0,0,1-.31-.74l.81-.81-.81-.83a.43.43,0,1,1,.61-.61L11.14,13A.41.41,0,0,1,11.14,13.61Z"></path></g></g></svg>
                              </div>
                              <div class="sd_settingPage">
                              <div class="Polaris-Card__Header">
                                <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                                  <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                                    <h2 class="Polaris-Heading">Customer account page</h2>
                                  </div>
                                </div>
                              </div>
                              <div class="Polaris-Card__Section"><span class="Polaris-TextStyle--variationSubdued">Manage customer subscriptions control here</span>
                              <div class="Polaris-Stack__Item">
                                <div class="Polaris-ButtonGroup">
                                  <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain"><button class="Polaris-Button Polaris-Button--plain sd_selectSetting" data-query-string="" type="button" data-popup="customer_settings" ><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Manage</span></span></button></div>
                                </div>
                              </div>
                            </div>
                            </div>
                            </div>
                          </div>
                      </div>
                    </div>
                    <div id="PolarisPortalsContainer"></div>
                  </div>
                  </div>
                    </li>
                    <?php if($store == 'renbe-uk.myshopify.com') { ?>
                      <li>
                        <div class="sd_susbscriptionSetting">
                          <div class="Polaris-FormLayout">
                            <div role="group" class="Polaris-FormLayout--grouped">
                              <div class="Polaris-FormLayout__Items">
                                <div class="Polaris-FormLayout__Item sd_customerAccountSetting">
                                  <div class="Polaris-Card">
                                    <div class="Polaris-iconbox">
                                    <svg viewBox="0 0 20 20" class="Icon_Icon__Dm3QW" style="width: 20px; height: 20px;"><path d="M0 5.324v10.176a1.5 1.5 0 0 0 1.5 1.5h17a1.5 1.5 0 0 0 1.5-1.5v-10.176l-9.496 5.54a1 1 0 0 1-1.008 0l-9.496-5.54z"></path><path d="M19.443 3.334a1.494 1.494 0 0 0-.943-.334h-17a1.49 1.49 0 0 0-.943.334l9.443 5.508 9.443-5.508z"></path></svg>
                                    </div>
                                    <div class="sd_settingPage">
                                    <div class="Polaris-Card__Header">
                                      <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                                        <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                                          <h2 class="Polaris-Heading">Email Template Settings</h2>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="Polaris-Card__Section"><span class="Polaris-TextStyle--variationSubdued">Manage Email Template Settings</span>
                                    <div class="Polaris-Stack__Item">
                                      <div class="Polaris-ButtonGroup">
                                        <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain"><a class="Polaris-Button Polaris-Button--plain navigate_element sd_select_setting" data-query-string="" data-redirect-link="/admin/settings/email_templates/email_matrix.php?shop=<?php echo $store; ?>" data-confirmbox="no" tabindex="0" href="javascript:void(0)" data-polaris-unstyled="true">Manage</a></div>
                                      </div>
                                    </div>
                                  </div>
                                  </div>
                                  </div>
                                </div>
                            </div>
                          </div>
                          <div id="PolarisPortalsContainer"></div>
                        </div>
                        </div>
                      </li>
                    <?php
                      }
                    ?>
                    <li>
                    <div class="sd_susbscriptionSetting">
                    <div class="Polaris-FormLayout">
                      <div role="group" class="Polaris-FormLayout--grouped">
                        <div class="Polaris-FormLayout__Items">
                          <div class="Polaris-FormLayout__Item sd_customerAccountSetting">
                            <div class="Polaris-Card">
                              <div class="Polaris-iconbox">
                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 1000 1000" enable-background="new 0 0 1000 1000" xml:space="preserve">
                              <metadata> Svg Vector Icons : http://www.onlinewebfonts.com/icon </metadata>
                              <g><g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)"><path d="M405.7,4993.2c-91.9-32.5-206.7-135.9-256.5-227.8c-42.1-80.4-42.1-82.3-42.1-1056.5c0-962.7,0-978.1,42.1-1052.7c47.9-90,122.5-162.7,216.3-212.5c63.2-34.4,128.2-36.4,1043.1-36.4c962.7,0,978,0,1052.7,42.1c90,47.8,162.7,122.5,212.5,216.3c34.4,63.2,36.4,128.2,36.4,1043.1c0,970.4,0,976.1-42.1,1054.6c-53.6,101.4-174.2,204.8-271.8,233.5C2291,5029.6,495.7,5025.8,405.7,4993.2z M2371.4,3714.7l-5.7-962.7h-957h-957l-5.7,962.7l-3.8,960.8h966.6h966.6L2371.4,3714.7z"></path><path d="M3994.4,4991.3c-105.3-36.4-204.8-122.5-254.6-222c-42.1-84.2-42.1-84.2-44-1052.7V2750l49.8-99.5c57.4-116.8,141.6-187.6,268-225.9c137.8-40.2,1862.3-40.2,1981,1.9c120.6,44,199.1,111,254.6,223.9l49.8,99.5v962.7v964.6l-47.9,93.8c-51.7,103.3-143.6,181.8-252.7,222C5891.2,5031.5,4101.6,5029.6,3994.4,4991.3z M5954.3,3708.9v-966.6h-957h-957v966.6v966.6h957h957V3708.9z"></path><path d="M7581.2,4995.1c-99.5-32.5-254.6-195.2-285.2-300.5c-17.2-65.1-21-319.6-17.2-1024c5.7-918.7,5.7-939.8,47.9-1014.4c47.8-90,122.5-162.7,216.3-212.5c63.2-34.4,128.2-36.4,1043.1-36.4c962.7,0,978,0,1052.7,42.1c90,47.8,162.7,122.5,212.4,216.3c34.5,63.2,36.4,128.2,36.4,1043.1c0,970.4,0,976.1-42.1,1054.6c-53.6,101.4-174.2,204.8-271.8,233.5C9474.1,5027.7,7676.9,5025.8,7581.2,4995.1z M9548.8,3714.7L9543,2752h-957h-957l-5.7,962.7l-3.8,960.8h966.6h966.6L9548.8,3714.7z"></path><path d="M333,1364.3c-109.1-63.2-181.8-155-216.3-277.5c-15.3-53.6-19.1-354.1-15.3-1016.3l5.7-939.8l57.4-93.8c40.2-63.2,91.9-114.8,162.7-155l103.4-63.2h978h978l103.4,63.2c70.8,40.2,122.5,91.9,162.7,155l57.4,93.8l5.7,945.5c3.8,606.7-1.9,970.4-15.3,1018.2c-30.6,112.9-122.5,225.9-229.7,279.5l-93.8,47.8h-972.3H432.5L333,1364.3z M2375.2,120.2v-957h-966.6H442.1v957v957h966.6h966.6V120.2z"></path><path d="M3954.2,1385.4c-42.1-19.1-103.4-65.1-137.8-99.5c-122.5-130.1-120.6-109.1-120.6-1169.4c0-1073.7-1.9-1045,135.9-1177.1c130.1-122.5,109.1-120.6,1169.4-120.6c1073.7,0,1045-1.9,1177.1,135.9c122.5,130.1,120.6,109.1,120.6,1165.6c0,1056.5,1.9,1035.5-120.6,1165.6c-132.1,137.8-101.4,135.9-1180.9,135.9C4095.8,1421.7,4025,1417.9,3954.2,1385.4z M5954.3,120.2v-957h-957h-957v957v957h957h957V120.2z"></path><path d="M7523.8,1375.8c-99.5-49.8-201-172.3-229.7-275.6c-30.6-112.9-24.9-1889.1,7.7-1981c40.2-109.1,118.7-201,222-252.6l93.8-47.9h972.3h972.3l99.5,57.4c109.1,63.2,164.6,130.1,206.7,250.7c42.1,118.7,42.1,1868.1,0,1986.7c-42.1,120.6-97.6,187.6-206.7,250.7l-99.5,57.4h-976.1l-976.1-1.9L7523.8,1375.8z M9552.6,120.2v-957h-966.6h-966.6v957v957h966.6h966.6V120.2z"></path><path d="M442.1-2174.6c-132.1-42.1-231.6-124.4-298.6-250.7c-34.4-63.2-36.4-128.2-36.4-1043.1c0-970.4,0-976.1,42.1-1054.6c53.6-101.5,174.2-204.8,271.8-233.5c105.3-32.5,1871.9-32.5,1981,0c49.8,15.3,114.8,59.3,176.1,120.6c145.5,145.5,145.5,143.6,137.8,1225c-5.7,853.6-7.7,924.4-42.1,985.7c-49.8,93.8-122.5,168.4-212.5,216.3c-74.6,42.1-93.8,42.1-1024,45.9C916.7-2161.2,468.9-2167,442.1-2174.6z M2371.4-3472.3l3.8-962.7h-966.6H442.1v953.2c0,524.4,5.7,960.8,13.4,966.6c5.7,7.7,440.2,11.5,960.8,9.6l949.3-5.7L2371.4-3472.3z"></path><path d="M4000.2-2184.2c-111-38.3-185.6-103.3-246.9-208.6l-57.4-99.5v-972.3V-4437l47.9-93.8c51.7-103.4,143.5-181.8,252.6-222c105.3-36.4,1896.7-36.4,2002,0c109.1,40.2,201,118.7,252.7,222l47.9,93.8v964.6v962.7l-49.8,99.5c-26.8,55.5-78.5,122.5-111,147.4c-135.9,103.3-151.2,105.3-1148.4,105.3C4287.3-2159.3,4051.8-2165.1,4000.2-2184.2z M5954.3-3468.5V-4435h-957h-957v966.6v966.6h957h957V-3468.5z"></path><path d="M7619.5-2174.6c-132.1-42.1-231.6-124.4-298.6-250.7c-34.5-63.2-36.4-132.1-42.1-1004.8c-3.8-704.3,0-958.9,17.2-1024c32.5-112.9,185.7-269.9,296.7-302.4c109.1-32.5,1875.7-32.5,1981,0c97.6,28.7,218.2,132.1,271.8,233.5c42.1,78.5,42.1,84.2,42.1,1054.6c0,914.9-1.9,980-36.4,1043.1c-49.7,93.8-122.5,168.4-212.4,216.3c-74.6,42.1-93.8,42.1-1024,45.9C8094.2-2161.2,7646.3-2167,7619.5-2174.6z M9543-3468.5v-957l-960.8-5.7l-962.7-3.8v953.2c0,524.4,5.7,960.8,13.4,966.6c5.7,7.7,440.2,11.5,960.8,9.6l949.3-5.7V-3468.5z"></path></g></g>
                            </svg>
                              </div>
                              <div class="sd_settingPage">
                              <div class="Polaris-Card__Header">
                                <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                                  <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                                    <h2 class="Polaris-Heading">Widget Settings</h2>
                                  </div>
                                </div>
                              </div>
                              <div class="Polaris-Card__Section"><span class="Polaris-TextStyle--variationSubdued">Manage widget settings control here</span>
                                <div class="Polaris-Stack__Item">
                                  <div class="Polaris-ButtonGroup">
                                    <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain">
                                      <a class="Polaris-Button Polaris-Button--plain navigate_element sd_select_setting" data-query-string="" data-redirect-link="/admin/settings/widget_setting.php?shop=<?php echo $store; ?>" data-confirmbox="no" tabindex="0" href="javascript:void(0)" data-polaris-unstyled="true">Manage</a>
                                    </div>
                                </div>
                            </div>
                            </div>
                            </div>
                          </div>
                      </div>
                    </div>
                    <div id="PolarisPortalsContainer"></div>
                  </div>
                  </div>
                    </li>
                    <li>
                    <div class="sd_susbscriptionSetting">
                    <div class="Polaris-FormLayout">
                      <div role="group" class="Polaris-FormLayout--grouped">
                        <div class="Polaris-FormLayout__Items">
                          <div class="Polaris-FormLayout__Item sd_customerAccountSetting">
                            <div class="Polaris-Card">
                              <div class="Polaris-iconbox">
                              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M7.75 5a.75.75 0 0 0 0 1.5h4.5a.75.75 0 1 0 0-1.5h-4.5Z" fill="#5C5F62"/><path d="M7 8.75a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5a.75.75 0 0 1-.75-.75Z" fill="#5C5F62"/><path d="M7.75 11a.75.75 0 0 0 0 1.5h1.5a.75.75 0 0 0 0-1.5h-1.5Z" fill="#5C5F62"/><path d="M11 8.75a.75.75 0 0 1 .75-.75h.5a.75.75 0 0 1 0 1.5h-.5a.75.75 0 0 1-.75-.75Z" fill="#5C5F62"/><path d="M11.75 11a.75.75 0 0 0 0 1.5h.5a.75.75 0 0 0 0-1.5h-.5Z" fill="#5C5F62"/><path fill-rule="evenodd" d="M4 16a1.5 1.5 0 0 0 2.615 1.003l1.135-1.26 1.135 1.26a1.5 1.5 0 0 0 2.23 0l1.135-1.26 1.135 1.26a1.5 1.5 0 0 0 2.615-1.003v-11a2.5 2.5 0 0 0-2.5-2.5h-7a2.5 2.5 0 0 0-2.5 2.5v11Zm2.5-12a1 1 0 0 0-1 1v11l1.507-1.674a1 1 0 0 1 1.486 0l1.507 1.674 1.507-1.674a1 1 0 0 1 1.486 0l1.507 1.674v-11a1 1 0 0 0-1-1h-7Z" fill="#5C5F62"/></svg>
                              </div>
                              <div class="sd_settingPage">
                              <div class="Polaris-Card__Header">
                                <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                                  <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                                    <h2 class="Polaris-Heading">Invoice Settings</h2>
                                  </div>
                                </div>
                              </div>
                              <div class="Polaris-Card__Section"><span class="Polaris-TextStyle--variationSubdued">Manage invoice settings control here</span>
                              <div class="Polaris-Stack__Item">
                                <div class="Polaris-ButtonGroup">
                                  <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain">
                                    <a class="Polaris-Button Polaris-Button--plain navigate_element sd_select_setting" data-query-string="" data-redirect-link="/admin/settings/invoiceSettings.php?shop=<?php echo $store; ?>" data-confirmbox="no" tabindex="0" href="javascript:void(0)" data-polaris-unstyled="true"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Manage</span></span></a>
                                </div>
                                </div>
                              </div>
                            </div>
                            </div>
                            </div>
                          </div>
                      </div>
                    </div>
                    <div id="PolarisPortalsContainer"></div>
                  </div>
                  </div>
                    </li>
                  
                  </ul>
            </div>
        </div>
</div>
</div>


<?php
include("../footer.php");
?>
</script>
