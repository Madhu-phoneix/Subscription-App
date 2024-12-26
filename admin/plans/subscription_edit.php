<div id="edit-plan-save-banner" class="Polaris-Frame-ContextualSaveBar  display-hide-label">
   <div class="Polaris-Frame-ContextualSaveBar__Contents" id="edit-plan-update">
      <h2 class="Polaris-Frame-ContextualSaveBar__Message">Unsaved changes</h2>
      <div class="sd_banner_buttons">
      <div class="Polaris-Frame-ContextualSaveBar__ActionContainer">
      <div p-color-scheme="light">
         <button class="Polaris-Button Polaris-Button--destructive back_button_subscription" data-usecase="subscription_form_leave" data-heading="Unsaved changes" data-query-string="" data-message="If you leave this page, any unsaved changes will be lost." data-acceptbuttontext="Leave" data-rejectbuttontext="Cancel" data-confirmbox="yes" data-redirect-link="dashboard.php" href="javascript:void(0)" data-polaris-unstyled="true" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Discard</span></span></button>
        <div id="PolarisPortalsContainer"></div>
      </div>
      </div>
      <div class="Polaris-Frame-ContextualSaveBar__ActionContainer">
         <div class="Polaris-Stack Polaris-Stack--spacingTight Polaris-Stack--noWrap">
            <div class="Polaris-Stack__Item"><button class="Polaris-Button Polaris-Button--primary plan-edit-save save-subscription-plan" type="button">Update</button></div>
         </div>
      </div>
      </div>
   </div>
</div>
<input type="hidden" id="db_edit_subscriptionplan_id" value="" />
   <div class="Polaris-Layout__Section sd-subscription-page-title">
      <div class="Polaris-Page-Header Polaris-Page-Header--isSingleRow Polaris-Page-Header--mobileView Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
         <div class="Polaris-Page-Header__Row">
            <div class="Polaris-Page-Header__BreadcrumbWrapper">
               <nav role="navigation">
                  <a class="Polaris-Breadcrumbs__Breadcrumb  back_button_subscription" data-usecase="subscription_form_leave" data-heading="Unsaved changes" data-query-string="" data-message="If you leave this page, any unsaved changes will be lost." data-acceptbuttontext="Leave" data-rejectbuttontext="Cancel" data-confirmbox="yes" data-redirect-link="dashboard.php" href="javascript:void(0)" data-polaris-unstyled="true">
                     <span class="Polaris-Breadcrumbs__ContentWrapper">
                        <span class="Polaris-Breadcrumbs__Icon">
                           <span class="Polaris-Icon">
                              <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                 <path d="M17 9H5.414l3.293-3.293a.999.999 0 1 0-1.414-1.414l-5 5a.999.999 0 0 0 0 1.414l5 5a.997.997 0 0 0 1.414 0 .999.999 0 0 0 0-1.414L5.414 11H17a1 1 0 1 0 0-2z"></path>
                              </svg>
                           </span>
                        </span>
                     </span>
                  </a>
               </nav>
            </div>
            <div class="Polaris-Page-Header__TitleWrapper">
               <div>
                  <div class="Polaris-Header-Title__TitleAndSubtitleWrapper">
                     <h1 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">Edit Plan</h1>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
<div class="cardboxes">
  <div class="Polaris-Layout">
  <!-- ****************** MINI Panel ***************************** --->
    <div class="Polaris-Layout__Section Polaris-Layout__Section--secondary edit-subscription-left">
         <div class="Polaris-Card">
		  <div class="sd-upper-wrapper">
            <div class="Polaris-Card__Header">
               <div class="Polaris-Stack Polaris-Stack--alignmentCenter">
                  <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                     <h2 class="Polaris-Heading subscription_heading"><span class="sd_subscription_heading"></span></h2>
					 <div class="planname_input_wrapper display-hide-label">
					 <input type="text" value="" name="subscription_heading" class="subscription_plan_name restrict_input_single_quote" maxlength="50">
                <img subscription-group-id=" " src="<?php echo $image_folder; ?>MobileCancelMajor.svg" class="plan_name_actions cancel_plan_name">
					 <img class="plan_name_actions save_plan_name" case-type="edit" src="<?php echo $image_folder; ?>MobileAcceptMajor.svg">
					 </div>
					 <div class="edit_text change_plan_name">
					 Edit
                     <span plan-name-value="" subscription-group-id=" " class=""><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="10px"><path d="M14.846 1.403l3.752 3.753.625-.626A2.653 2.653 0 0015.471.778l-.625.625zm2.029 5.472l-3.752-3.753L1.218 15.028 0 19.998l4.97-1.217L16.875 6.875z" fill="#5C5F62"></path></svg></span>
					 </div>
                  </div>

               </div>
            </div>
            <div id="" class="edit-case-left-product">
               <div class="Polaris-Card__Section inner-box-cont sd_Tabs subscription-edit-tabs-title" group="subscription-edit-tabs" target-tab="subscription_edit_products">
				   <h3 aria-label="Items" class="Polaris-Subheading" >Products</h3>
				   <div class="left-product-wrapper">
					   <span class="left-panel-add-product"><button  parent-id="subscription_edit_prodcts"  product-display-style='tag' id="" class="add_newProducts Polaris-Button" type="button">Add Products</button></span>
					   <span class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain edit_total_products"></span>
				   </div>
				  </div>

               <div class="Polaris-Card__Section inner-box-cont sd_Tabs subscription-edit-tabs-title" group="subscription-edit-tabs" target-tab="subscription_edit_schemes">
			    <h3 aria-label="Items"  class="Polaris-Subheading">Selling Plans</h3>
				<span class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain edit_total_selling_plans"></span>
               </div>

            </div>
			</div>
            <!-- content wrapper -->
            <div class="Polaris-Card__Footer">
               <div class="Polaris-Stack Polaris-Stack--alignmentCenter">
                  <div>
                     <div class="Polaris-ButtonGroup">
                        <div class="Polaris-ButtonGroup__Item">
                           <button class="Polaris-Button remove-btn delete_subscription_plan"  subscription-group-id=""  data-id="" type="button">
                                    <svg width="20" viewBox="0 0 20 20">
                                       <path fill-rule="evenodd" d="M14 4h3a1 1 0 011 1v1H2V5a1 1 0 011-1h3V1.5A1.5 1.5 0 017.5 0h5A1.5 1.5 0 0114 1.5V4zM8 2v2h4V2H8zM3 8h14v10.5a1.5 1.5 0 01-1.5 1.5h-11A1.5 1.5 0 013 18.5V8zm4 3H5v6h2v-6zm4 0H9v6h2v-6zm2 0h2v6h-2v-6z" fill="#5C5F62"></path>
                                    </svg>  Delete Group
                           </button>
                        </div>
                     </div>
                     <div id="PolarisPortalsContainer"></div>
                  </div>
               </div>
            </div>
         </div>
    </div>	<!-- ****************** Detail Edit Panel ***************************** --->
  <div class="Polaris-Layout__Section edit-subscription-right">
   <div>
      <div class="Polaris-Card">
         <div>
            <div class="Polaris-Tabs__Wrapper">
               <ul role="tablist" class="Polaris-Tabs">
                  <li class="Polaris-Tabs__TabContainer" role="presentation"><button group="subscription-edit-tabs" target-tab="subscription_edit_schemes" role="tab" type="button"  class="sd_Tabs subscription-edit-tabs-title Polaris-Tabs__Tab Polaris-Tabs__Tab--selected"><span class="Polaris-Tabs__Title">Selling Plans</span></button></li>
                  <li class="Polaris-Tabs__TabContainer" role="presentation"><button  group="subscription-edit-tabs" target-tab="subscription_edit_products" type="button" class="sd_Tabs subscription-edit-tabs-title Polaris-Tabs__Tab" ><span class="Polaris-Tabs__Title">Products</span></button></li>
               </ul>
            </div>
            <div class="Polaris-Tabs__Panel subscription-edit-tabs Polaris-Tabs__Panel--hidden" id="subscription_edit_products" role="tabpanel">
               <div class="Polaris-Card__Section">
                  <div class="Polaris-Card__SectionHeader">
                     <div>
                        <button  parent-id="subscription_edit_prodcts"  product-display-style='tag' id="" class="add_newProducts Polaris-Button" type="button">Add Products</button>
                        <div id="PolarisPortalsContainer"></div>
                     </div>
                  </div>
                  <div>
                     <div  class="Polaris-Stack Polaris-Stack--spacingTight show_selected_products">
                        <ul class="Polaris-ResourceList" id="subscription_edit_prodcts"></ul>
                     </div>
                     <div id="PolarisPortalsContainer"></div>
                  </div>
               <?php echo sellingPlanFormError('subscription_add_product_error','Products are required.'); ?>
               </div>
            </div>
            <div class="Polaris-Tabs__Panel subscription-edit-tabs" id="subscription_edit_schemes" role="tabpanel">
			 <div class="Polaris-Card__Section">
				<div class="Polaris-ButtonGroup">
				 <div class="Polaris-ButtonGroup__Item"><button id="" class="edit_subscription_add_new_selling_plan Polaris-Button Polaris-Button--primary" type="button">Add Selling Plan</button></div>
			  </div>
           <div class="sd_main_card_wrapper">
                <div class="Polaris-Layout sd-frequency-plan-card-wrapper">
                </div>
				      <div class="Polaris-Banner display-hide-label frequency-plan-error add-least-frequency-error Polaris-Banner--statusCritical Polaris-Banner--withinPage" tabindex="0" role="alert" aria-live="polite" aria-labelledby="PolarisBanner18Heading" aria-describedby="PolarisBanner18Content">
					  <div class="Polaris-Banner__Ribbon">
						 <span class="Polaris-Icon Polaris-Icon--colorCritical Polaris-Icon--applyColor">
							<svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
							   <path d="M11.768.768a2.5 2.5 0 0 0-3.536 0L.768 8.232a2.5 2.5 0 0 0 0 3.536l7.464 7.464a2.5 2.5 0 0 0 3.536 0l7.464-7.464a2.5 2.5 0 0 0 0-3.536L11.768.768zM9 6a1 1 0 1 1 2 0v4a1 1 0 1 1-2 0V6zm2 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"></path>
							</svg>
						 </span>
					  </div>
					  <div class="Polaris-Banner__ContentWrapper">
						 <div class="Polaris-Banner__Content" id="PolarisBanner18Content">
							<p>Please Add Atleast 1 Selling Plan .<span class="sd_link go-to-step2">Click Here</a> </p>
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
</form>
