<?php
 include("../header.php");
 ?>
 <div class="Polaris-Layout">
 <?php
 include("../navigation.php");
 $getAllthemes = $mainobj->getAllThemes();
 ?>
<div class="Polaris-Layout__Section sd-dashboard-page">
      <div class="Polaris-Page-Header Polaris-Page-Header--isSingleRow Polaris-Page-Header--mobileView Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
         <div class="Polaris-Page-Header__Row">
            <div class="Polaris-Page-Header__TitleWrapper">
               <div>
                  <div class="Polaris-Header-Title__TitleAndSubtitleWrapper">
                     <h1 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">Theme Integration</h1>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="Polaris-Page__Content">
         <div>
            <div class="Polaris-Layout">
               <div class="Polaris-Layout__AnnotatedSection">
                  <div class="Polaris-Layout__AnnotationWrapper">
                     <div class="Polaris-Layout__Annotation">
                        <div class="Polaris-TextContainer">
                           <h2 class="Polaris-Heading" id="storeDetails">Select Theme</h2>
                        </div>
                     </div>
                     <div class="Polaris-Layout__AnnotationContent">
                        <div class="Polaris-Card">
                           <div class="Polaris-Card__Section">
                              <div class="Polaris-FormLayout">
                                 <div class="Polaris-FormLayout__Item">
                                    <div>
                                       <div class="">
                                          <div class="Polaris-Labelled__LabelWrapper">
                                             <div class="Polaris-Label"><label id="PolarisSelect2Label" for="sd_selectTheme" class="Polaris-Label__Text">Select theme from dropdown to configure the theme code</label></div>
                                          </div>
                                          <div class="Polaris-Select">
                                             <select id="sd_selectTheme" class="Polaris-Select__Input" aria-invalid="false">
                                                <?php foreach($getAllthemes as $themeVal){ ?>
                                                  <option value="<?php echo $themeVal['name'] ?>" data-id="<?php echo $themeVal['id'] ?>"><?php echo $themeVal['name'] ?></option>
                                                <?php } ?>
                                             </select>
                                             <div class="Polaris-Select__Content" aria-hidden="true">
                                                <span class="Polaris-Select__SelectedOption"><?php echo $getAllthemes[0]['name'] ?></span>
                                                <span class="Polaris-Select__Icon">
                                                   <span class="Polaris-Icon">
                                                      <span class="Polaris-VisuallyHidden"></span>
                                                      <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                         <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014L10.531 5.24a.708.708 0 0 0-1.062 0L7.145 7.986C6.798 8.397 7.113 9 7.676 9zm4.648 2H7.676c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014z"></path>
                                                      </svg>
                                                   </span>
                                                </span>
                                             </div>
                                             <div class="Polaris-Select__Backdrop"></div>
                                          </div>
                                       </div>
                                       <div id="PolarisPortalsContainer"></div>
                                    </div>
                                 </div>
                                 <div class="Polaris-FormLayout__Items">
                                    <div class="Polaris-FormLayout__Item">
                                       <div class="Polaris-Stack">
                                          <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                                          <a class="Polaris-Link Polaris-Button sd_theme_integrate" href="https://<?php echo $mainobj->store; ?>/admin/themes/current/editor?context=apps&activateAppId=<?php echo $mainobj->app_extension_id; ?>/<?php echo $mainobj->theme_block_name; ?>" target ="__blank" data-polaris-unstyled="true">Enable/Disable App</a>
                                          </div>
                                          </div>
                                       </div>
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
</div>
<?php include('../footer.php'); ?>