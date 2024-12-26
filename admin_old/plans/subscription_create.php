<div class="create_plangroup_left">
<form method="post" id="subscription_plan_form">
   <input type="hidden" id="db_edit_subscriptionplan_id" value="">
   <div class="Polaris-Layout">
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
                        <h1 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">Create Plans</h1>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="Polaris-Layout__Section">
         <div class="Polaris-Page-Header Polaris-Page-Header--isSingleRow Polaris-Page-Header--mobileView Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
            <div class="Polaris-Page-Header__Row">
               <div class="Polaris-Page-Header__TitleWrapper">
                  <ul class="form-steps">
                     <li id="li_step1" class="active"><span class="step-tick display-hide-label"><img src="<?php echo $mainobj->image_folder; ?>TickMinor.svg"/></span><span class="step-number">1</span></li>
                     <li id="li_step2" class=""><span class="step-tick display-hide-label"><img src="<?php echo $mainobj->image_folder; ?>TickMinor.svg"/></span><span class="step-number">2</span></li>
                     <li id="li_step3" class=""><span class="step-tick display-hide-label"><img src="<?php echo $mainobj->image_folder; ?>TickMinor.svg"/></span><span class="step-number">3</span></li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
      <div><a target="_blank" class="Polaris-Link" href="<?php echo $mainobj->SHOPIFY_DOMAIN_URL; ?>/admin/create-subscription-plan" rel="noopener noreferrer" data-polaris-unstyled="true">How to create</a></div>
	  <!-- *******************************************************  STEP 1 ******************************************************-->
      <div class="Polaris-Layout__AnnotatedSection form-steps-tab subscription-create-step1">
         <div class="Polaris-Layout__AnnotationWrapper">
            <div class="Polaris-Layout__AnnotationContent">
               <div class="Polaris-Card">
                  <div class="Polaris-Card__Section">
                     <div class="Polaris-FormLayout">
                        <div class="sd-boxshadow">
                           <div class="Polaris-FormLayout__Item">
                              <div class="">
                                 <div class="Polaris-Labelled__LabelWrapper">
                                    <div class="Polaris-Label"><label id="PolarisTextField3Label" for="PolarisTextField3" class="Polaris-Label__Text">Plan Name</label></div>
                                    <div class="sd_planType sd_sellingPlans"><div class="sd_recurringMsg" onmouseover="show_title(this)" onmouseout="hide_title(this)"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20px"><path fill-rule="evenodd" d="M11 11H9v-.148c0-.876.306-1.499 1-1.852.385-.195 1-.568 1-1a1.001 1.001 0 00-2 0H7c0-1.654 1.346-3 3-3s3 1 3 3-2 2.165-2 3zm-2 4h2v-2H9v2zm1-13a8 8 0 100 16 8 8 0 000-16z" fill="#5C5F62"></path></svg></div>
                                    <div class="Polaris-PositionedOverlay display-hide-label">
                                       <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                          <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content"><p>Plan name will not be visible to the customer, only merchant can see this name.</p></div>
                                       </div>
                                    </div>
                                 </div>
                                 </div>
                                 <div class="Polaris-Connected">
                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                       <div class="Polaris-TextField">
                                          <input type="text"  name="subscription_plan_name" id="subscription_plan_name" class="Polaris-TextField__Input restrict_input_single_quote" aria-labelledby="PolarisTextField3Label" aria-invalid="false" value="" maxlength="50" autocomplete="off" placeholder="eg.Subscribe and save">
                                          <div class="Polaris-TextField__Backdrop"></div>
                                       </div>
                                    </div>
                                 </div>
                              <?php echo $mainobj->sellingPlanFormError('subscription_plan_name_error','Plan name is required.'); ?>
                              <?php echo $mainobj->sellingPlanFormError('subscription_plan_name_already_exist_error','Subscription with this plan name already exists.'); ?>
                              </div>
                           </div>
                           <div class="Polaris-FormLayout__Item" >
                              <div>
                                 <div class="Polaris-Stack Polaris-Stack--spacingTight">

										<div class="Polaris-Stack Polaris-Stack--spacingTight show_selected_products display-hide-label"  id="">
                              <ul class="Polaris-ResourceList" id="create-section-products-show" ></ul>
										</div>

                                 </div>
                                 <div id="PolarisPortalsContainer"></div>
                              </div>
                           </div>
                           <div class="Polaris-FormLayout__Item">
                              <div>
                        <button parent-id="create-section-products-show" id="" product-display-style='tag' class="add_newProducts Polaris-Button Polaris-Button--primary " type="button">Select Products</button>
                              <?php echo $mainobj->sellingPlanFormError('subscription_add_product_error','Products are required.'); ?>
                                 <div id="PolarisPortalsContainer"></div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="right-button-groups sd_float_right">
                  <div class="Polaris-ButtonGroup step1_button_group">
                     <div class="Polaris-ButtonGroup__Item"><button class="Polaris-Button Polaris-Button--primary subscription_validate_step1" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Next</span></span></button></div>
                  </div>
                  <div id="PolarisPortalsContainer"></div>
               </div>
            </div>
         </div>
      </div>
	<!-- *******************************************************  STEP 2 ******************************************************-->
	 <div class="Polaris-Layout__AnnotatedSection sd-subscription-frequency-form form-steps-tab subscription-create-step2 display-hide-label">
			<?php require_once("sellingPlanForm.php"); ?>
      </div>
	<!-- *******************************************************  STEP 3 ******************************************************-->
	<div class="Polaris-Layout__AnnotatedSection form-steps-tab subscription-create-step3 display-hide-label">
         <div class="Polaris-Layout__AnnotationWrapper sd-frequency-schemes Polaris-Card">
            <div class="Polaris-Layout__AnnotationContent sd-frequency-schemes-wrapper">
               <div class="Polaris-Page-Header Polaris-Page-Header--isSingleRow Polaris-Page-Header--mobileView Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
                  <div class="Polaris-Page-Header__Row">
                     <div class="Polaris-Page-Header__TitleWrapper">
                        <div>
                           <div class="Polaris-Header-Title__TitleAndSubtitleWrapper">
                              <h2 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">Selling Plans</h2>
                           </div>
                        </div>
                        <div class="Polaris-Stack__Item subscriptin-button-wrapper"><button show-update-button="true" attr-form-type="new" attr-form-id="" class="Polaris-Button Polaris-Button--primary  go-to-step2" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Add Selling Plan</span></span></button></div>
                     </div>
                  </div>
               </div>
               <div>
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
							<p>Please Add Atleast 1 Plan .<span class="sd_link go-to-step2">Click Here</a> </p>
						 </div>
					  </div>
				   </div>
                </div>

               </div>
            </div>
         </div>
         <div class="right-button-groups">
            <div class="Polaris-ButtonGroup step3_button_group">
			 <div class="Polaris-ButtonGroup__Item"><button class="Polaris-Button step3_previous" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Previous</span></span></button></div>
               <div class="Polaris-ButtonGroup__Item"><button class="save-subscription-plan Polaris-Button Polaris-Button--primary" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Finish</span></span></button></div>
            </div>
            <div id="PolarisPortalsContainer"></div>
         </div>
      </div>
  </div>
   <!-- Polaris-Layout -->
</form>
</div>
<div class="create_plangroup_right">
	<?php require_once("subscription_create_preview.php"); ?>
</div>