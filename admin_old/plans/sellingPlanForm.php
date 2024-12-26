<?php $tooltip_html = '<div class="sd_recurringMsg" onmouseover="show_title(this)" onmouseout="hide_title(this)"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20px"><path fill-rule="evenodd" d="M11 11H9v-.148c0-.876.306-1.499 1-1.852.385-.195 1-.568 1-1a1.001 1.001 0 00-2 0H7c0-1.654 1.346-3 3-3s3 1 3 3-2 2.165-2 3zm-2 4h2v-2H9v2zm1-13a8 8 0 100 16 8 8 0 000-16z" fill="#5C5F62"></path></svg></div>'; ?>
<div class="Polaris-Layout__AnnotationWrapper create-selling-plan-form">
   <div class="Polaris-Layout__AnnotationContent">
      <div class="Polaris-Banner display-hide-label frequency-plan-error add-10only-frequency-error Polaris-Banner--statusCritical Polaris-Banner--withinPage" tabindex="0" role="alert" aria-live="polite" aria-labelledby="PolarisBanner18Heading" aria-describedby="PolarisBanner18Content">
         <div class="Polaris-Banner__Ribbon">
            <span class="Polaris-Icon Polaris-Icon--colorCritical Polaris-Icon--applyColor">
               <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                  <path d="M11.768.768a2.5 2.5 0 0 0-3.536 0L.768 8.232a2.5 2.5 0 0 0 0 3.536l7.464 7.464a2.5 2.5 0 0 0 3.536 0l7.464-7.464a2.5 2.5 0 0 0 0-3.536L11.768.768zM9 6a1 1 0 1 1 2 0v4a1 1 0 1 1-2 0V6zm2 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"></path>
               </svg>
            </span>
         </div>
         <div class="Polaris-Banner__ContentWrapper">
            <div class="Polaris-Banner__Content" id="PolarisBanner18Content">
               <p>Only 20 Selling Plans can be added in a single Plan</p>
            </div>
         </div>
      </div>
      <div class="Polaris-Card">
         <div class="Polaris-Card__Section">
            <div class="Polaris-FormLayout">
               <div class="Polaris-FormLayout__Item">
                  <div class="Polaris-TextContainer subscription-step-heading">
                     <h2 class="Polaris-Heading">Add selling plan</h2>
                     <p>
                  </div>
               </div>
               <div class="sd_selling_form">
                  <div class="Polaris-FormLayout__Items">
                     <div class="Polaris-FormLayout__Item ">
                        <div class="">
                           <div class="Polaris-Labelled__LabelWrapper">
                              <div class="Polaris-Label"><label id="PolarisSelect2Label" for="sd_subscriptionPlan" class="Polaris-Label__Text">Plan type</label></div>
                              <div class="sd_planType sd_sellingPlans"><?php echo $tooltip_html; ?>
                              <div class="Polaris-PositionedOverlay display-hide-label">
                                 <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                    <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content"><p>We support two types of plan</p><p><b>Pay per delivery</b> :- This plan type charges the customer just for the 'immediately next' recurring order or delivery.</p><p><b>Prepaid</b> :- This plan type allows you to receive the payment for multiple periods of future orders at the same time.</p></div>
                                 </div>
                              </div>
                              </div>
                           </div>
                           <div class="Polaris-Select">
                              <select id="frequency_plan_type" class="Polaris-Select__Input frequency_plan_type" aria-invalid="false" name="frequency_plan_type">
                                 <option value="Pay Per Delivery">Pay Per Delivery</option>
                                 <option value="Prepaid">Prepaid</option>
                              </select>
                              <div class="Polaris-Select__Content" aria-hidden="true">
                                 <span id="frequency_plan_type_selected_value" class="Polaris-Select__SelectedOption">Pay per delivery</span>
                                 <span class="Polaris-Select__Icon">
                                    <span class="Polaris-Icon">
                                       <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                          <path d="M10 16l-4-4h8l-4 4zm0-12l4 4H6l4-4z"></path>
                                       </svg>
                                    </span>
                                 </span>
                              </div>
                              <div class="Polaris-Select__Backdrop"></div>
                           </div>
                        </div>
                        <div id="PolarisPortalsContainer"></div>
                     </div>
                     <div class="Polaris-FormLayout__Item">
                        <div class="">
                           <div class="Polaris-Labelled__LabelWrapper">
                              <div class="Polaris-Label">
                                 <label id="TextField2Label" for="TextField2" class="Polaris-Label__Text">Selling plan name</label>
                              </div>
                              <div class="sd_planName sd_sellingPlans"><?php echo $tooltip_html; ?>
                              <div class="Polaris-PositionedOverlay display-hide-label">
                                 <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                    <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">The selling plan name will be visible to the customer on the cart page.</div>
                                 </div>
                              </div>
                              </div>
                           </div>
                           <div class="Polaris-Connected">
                              <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                 <div class="Polaris-TextField">
                                    <input type="hidden" id="edit_frequency_card_serial_no" value="">
                                    <input type="hidden" id="sellingplanid" name="sellingplanid" value="">
                                    <input type="hidden" id="new_card_no" value="">
                                    <input placeholder = "eg. delivery every day/week/month/year" id="frequency_plan_name" type="text" value="" class="Polaris-TextField__Input restrict_input_single_quote" maxlength="<?php echo $mainobj->input_max_character; ?>" name="frequency_plan_name" aria-labelledby="TextField6Label" aria-invalid="false" autocomplete="off">
                                    <div class="Polaris-TextField__Backdrop"></div>
                                 </div>
                              </div>
                           </div>
                           <?php echo $mainobj->sellingPlanFormError('frequency_plan_name_error','Selling plan name is required.'); ?>
                           <?php echo $mainobj->sellingPlanFormError('same_frequency_plan_name_error','Subscription Plan cant have selling plans with same name.'); ?>
                        </div>
                     </div>
                  </div>
                  <div class="Polaris-FormLayout__Items " style="display: flex;">
                     <div class="Polaris-FormLayout__Item">
                        <div class="">
                           <div class="Polaris-Labelled__LabelWrapper">
                              <div class="Polaris-Label"><label id="sd_order_fulfillment_frequency" for="PolarisTextField7" class="Polaris-Label__Text">Customer facing description(optional)</label></div>
                           </div>
                        </div>
                        <div class="Polaris-Connected">
                           <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                              <div class="Polaris-TextField sd-small-textfield">
                                 <!-- <textarea placeholder="Enter Description here..." name="sd_description" id="sd_description" class="Polaris-TextField__Input preview_plan sd_validate_expration" min="1" type="number" aria-labelledby="PolarisTextField7Label" aria-invalid="false" autocomplete="off"  value=""></textarea> -->
                                 <input id="sd_description" placeholder = "eg. Get product every week with 10% discount." type="text" value="" class="Polaris-TextField__Input" name="sd_description" aria-labelledby="TextField6Label" aria-invalid="false" autocomplete="off">
                                 <div class="Polaris-TextField__Backdrop"></div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- sd_per_delivery_fields-->
                  <div class="Polaris-FormLayout__Items " style="display: flex;">
                     <div class="Polaris-FormLayout__Item">
                        <div class="">
                           <div class="Polaris-Labelled__LabelWrapper">
                              <div class="Polaris-Label"><label id="sd_order_fulfillment_frequency" for="PolarisTextField7" class="Polaris-Label__Text">Delivery period</label></div>
                              <div class="sd_planType sd_sellingPlans"><?php echo $tooltip_html; ?>
                              <div class="Polaris-PositionedOverlay display-hide-label">
                                 <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                    <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">The time gap between two order deliveries within the subscription plan. The number should be a minimum of 1, and the timeline can be days/weeks/months/years. For instance, if we have a subscription plan setup for 1 week and the first order is placed today, the next order will be automatically placed exactly after a week. The cycle will continue every week.</div>
                                 </div>
                              </div>
                              </div>
                           </div>
                           <div class="Polaris-Connected">
                              <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                 <div class="Polaris-TextField sd-small-textfield">
                                    <span class="sd-text-label">Every</span><input placeholder="eg. 1" name="per_delivery_order_frequency_value" id="sd_per_delivery_order_frequency_value" class="Polaris-TextField__Input preview_plan" min="1" onkeypress="return event.charCode >= 48 &amp;&amp; event.charCode <= 57" type="number" aria-labelledby="PolarisTextField7Label" aria-invalid="false" autocomplete="off" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); this.value =
                                          !!this.value &amp;&amp; Math.abs(this.value) > 0 ? Math.abs(this.value) : null" value="" maxlength="4">
                                    <div class="Polaris-TextField__Backdrop"></div>
                                    <div class="Polaris-Select sd-small-select">
                                       <select id="sd_per_delivery_order_frequency_type" name="per_delivery_order_frequency_type" class="Polaris-Select__Input" aria-invalid="false">
                                          <option value="DAY">DAY</option>
                                          <option value="WEEK">WEEK</option>
                                          <option value="MONTH">MONTH</option>
                                          <option value="YEAR">YEAR</option>
                                       </select>
                                       <div class="Polaris-Select__Content" aria-hidden="true">
                                          <span class="Polaris-Select__SelectedOption">DAY</span>
                                          <span class="Polaris-Select__Icon">
                                             <span class="Polaris-Icon">
                                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                   <path d="m10 16-4-4h8l-4 4zm0-12 4 4H6l4-4z"></path>
                                                </svg>
                                             </span>
                                          </span>
                                       </div>
                                       <div class="Polaris-Select__Backdrop"></div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <?php echo $mainobj->sellingPlanFormError('per_delivery_order_frequency_value_error','Delivery period is required.'); ?>
                           <?php echo $mainobj->sellingPlanFormError('selling_plan_same_error','No two plan can have same value of delivery and billing policy.'); ?>
                           <?php echo $mainobj->sellingPlanFormError('prepaid_billing_value_zero_error','Billing period value should be greater than zero.'); ?>
                        </div>
                     </div>
                     <div class="Polaris-FormLayout__Item sd_prepaid_fields display-hide-label">
                        <div class="">
                           <div class="Polaris-Labelled__LabelWrapper">
                              <div class="Polaris-Label"><label id="PolarisTextField7Label" for="PolarisTextField7" class="Polaris-Label__Text">Billing period</label></div>
                              <div class="sd_planType sd_sellingPlans"><?php echo $tooltip_html; ?>
                              <div class="Polaris-PositionedOverlay display-hide-label">
                                 <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                    <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">This is the number of deliveries that the customers can pay in advance for. For example, if you set the delivery frequency as 1 week, and if the billing timeframe is 12 weeks, and if , your customers will be charged for 12 weeks, but receive the deliveries once a week, for 12 weeks.</div>
                                 </div>
                              </div>
                              </div>
                           </div>
                           <div class="Polaris-Connected">
                              <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                 <div class="Polaris-TextField sd-small-textfield">
                                    <input placeholder="eg. 5"  name="prepaid_billing_value"  id="sd_prepaid_billing_value" class="Polaris-TextField__Input preview_plan" min="1" autocomplete="off" onkeypress="return event.charCode >= 48 &amp;&amp; event.charCode <= 57" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); this.value =
                                          !!this.value &amp;&amp; Math.abs(this.value) > 0 ? Math.abs(this.value) : null" maxlength="4" type="number" aria-labelledby="PolarisTextField7Label" aria-invalid="false" value="">
                                    <div class="Polaris-TextField__Backdrop"></div>
                                    <div class="Polaris-Select sd-small-select">
                                       <select disabled="" id="sd_prepaid_billing_type" name="prepaid_billing_type" class="Polaris-Select__Input" aria-invalid="false">
                                          <option value="DAY">DAY</option>
                                          <option value="WEEK">WEEK</option>
                                          <option value="MONTH">MONTH</option>
                                          <option value="YEAR">YEAR</option>
                                       </select>
                                       <div class="Polaris-Select__Content" aria-hidden="true">
                                          <span id="sd_prepaid_billing_type_Selected_value" class="Polaris-Select__SelectedOption">DAY</span>
                                          <span class="Polaris-Select__Icon">
                                             <span class="Polaris-Icon">
                                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                   <path d="m10 16-4-4h8l-4 4zm0-12 4 4H6l4-4z"></path>
                                                </svg>
                                             </span>
                                          </span>
                                       </div>
                                       <div class="Polaris-Select__Backdrop"></div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <?php echo $mainobj->sellingPlanFormError('prepaid_billing_value_error','Billing period is required.'); ?>
                           <?php echo $mainobj->sellingPlanFormError('prepaid_billing_value_rule_error','Billing Period Value should be greater than delivery period value.'); ?>
                           <?php echo $mainobj->sellingPlanFormError('prepaid_billing_value_multiple_error','Billing Period Value should be multiple of  delivery period value.'); ?>
                        </div>
                     </div>
                  </div>
                  <div class="Polaris-FormLayout__Item sd_set_anchor_date display-hide-label">
                     <div class="enable_copyright_box">
                        <div class="Polaris-Labelled__LabelWrapper">
                           <div class="Polaris-Label">
                              <label id="TextField1Label3" for="TextField3" class="Polaris-Label__Text">Set order date(optional)</label>
                           </div>
                        </div>
                        <div class="Polaris-Connected">
                           <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                              <div class="Polaris-TextField">
                                 <label class="switch">
                                 <input type="checkbox" name="sd_set_anchor_date" id="sd_set_anchor_date">
                                 <span class="slider round"></span>
                                 </label>
                                 <div class="sd_planName sd_sellingPlans"><?php echo $tooltip_html; ?>
                              <div class="Polaris-PositionedOverlay display-hide-label">
                                 <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                    <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">The date preset by the merchant, on which the order will be placed.</div>
                                 </div>
                              </div>
                              </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="Polaris-FormLayout__Items">
                   <div class="Polaris-FormLayout__Item sd_anchor_option display-hide-label">
                           <div class="">
                              <div class="Polaris-Labelled__LabelWrapper">
                                 <div class="Polaris-Label"><label id="PolarisTextField7Label" for="PolarisTextField7" class="Polaris-Label__Text">Select option</label></div>
                              </div>
                              <div class="Polaris-Connected">
                                 <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                    <div class="Polaris-Select">
                                       <select id="sd_anchor_option" name="sd_anchor_option" class="Polaris-Select__Input" aria-invalid="false">
                                          <option value="On Purchase Day">On purchase day</option>
                                          <option value="On Specific Day">On specific day</option>
                                       </select>
                                       <div class="Polaris-Select__Content" aria-hidden="true">
                                          <span class="Polaris-Select__SelectedOption">On purchase day</span>
                                          <span class="Polaris-Select__Icon">
                                             <span class="Polaris-Icon">
                                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                   <path d="m10 16-4-4h8l-4 4zm0-12 4 4H6l4-4z"></path>
                                                </svg>
                                             </span>
                                          </span>
                                       </div>
                                       <div class="Polaris-Select__Backdrop"></div>
                                    </div>
                                 </div>
                              </div>
                              <?php echo $mainobj->sellingPlanFormError('subscription_discount_value_greater_error','Discount Percentage value should not be greater than 100.'); ?>
                           </div>
                        </div>
                        <div class="Polaris-FormLayout__Item sd_anchor_week_day display-hide-label">
                           <div class="">
                              <div class="Polaris-Labelled__LabelWrapper">
                                 <div class="Polaris-Label"><label id="PolarisTextField7Label" for="PolarisTextField7" class="Polaris-Label__Text">Select day</label></div>
                              </div>
                              <div class="Polaris-Connected">
                                 <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                    <div class="Polaris-Select">
                                       <select id="sd_anchor_week_day" name="sd_anchor_week_day" class="Polaris-Select__Input" aria-invalid="false">
                                          <option value="Sunday">Sunday</option>
                                          <option value="Monday">Monday</option>
                                          <option value="Tuesday">Tuesday</option>
                                          <option value="Wednesday">Wednesday</option>
                                          <option value="Thursday">Thursday</option>
                                          <option value="Friday">Friday</option>
                                          <option value="Saturday">Saturday</option>
                                       </select>
                                       <div class="Polaris-Select__Content" aria-hidden="true">
                                          <span class="Polaris-Select__SelectedOption">Sunday</span>
                                          <span class="Polaris-Select__Icon">
                                             <span class="Polaris-Icon">
                                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                   <path d="m10 16-4-4h8l-4 4zm0-12 4 4H6l4-4z"></path>
                                                </svg>
                                             </span>
                                          </span>
                                       </div>
                                       <div class="Polaris-Select__Backdrop"></div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="Polaris-FormLayout__Item display-hide-label sd_anchor_month_day">
                        <div class="">
                           <div class="Polaris-Labelled__LabelWrapper">
                              <div class="Polaris-Label"><label id="PolarisSelect2Label" for="sd_subscriptionPlan" class="Polaris-Label__Text">Month date</label></div>
                              <div class="sd_minimum_cycle sd_sellingPlans"><?php echo $tooltip_html; ?>
                              <div class="Polaris-PositionedOverlay display-hide-label">
                                 <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                    <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">On which date of the month you want to place the order.</div>
                                 </div>
                              </div>
                           </div>
                           </div>
                           <div class="Polaris-Connected">
                              <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                 <div class="Polaris-TextField">
                                    <input placeholder="eg. 1" id="sd_anchor_month_day" min="1" onkeypress="return event.charCode >= 48 &amp;&amp; event.charCode <= 57" type="number" aria-labelledby="PolarisTextField7Label" aria-invalid="false" autocomplete="off" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); this.value =
                                          !!this.value &amp;&amp; Math.abs(this.value) > 0 ? Math.abs(this.value) : null" value="" maxlength="2" class="Polaris-TextField__Input" name="sd_anchor_month_day">
                                    <div class="Polaris-TextField__Suffix" id="PolarisTextField1-Suffix">Day</div>
                                    <div class="Polaris-TextField__Backdrop"></div>
                                 </div>
                              </div>
                              <div id="PolarisPortalsContainer"></div>
                           </div>
                           <?php echo $mainobj->sellingPlanFormError('sd_anchor_month_day_error','Month date is required'); ?>
                           <?php echo $mainobj->sellingPlanFormError('invalid_month_anchor_error','Month date should be between 1 to 31.'); ?>
                        </div>
                     </div>
                  </div>
                  <!-- cut off day start -->
                  <div class="Polaris-FormLayout__Item cut_off_days display-hide-label">
                        <div class="">
                           <div class="Polaris-Labelled__LabelWrapper">
                              <div class="Polaris-Label"><label id="PolarisSelect2Label" for="sd_subscriptionPlan" class="Polaris-Label__Text">Cutt off day(optional)</label></div>
                              <div class="sd_cutOff_days sd_sellingPlans"><?php echo $tooltip_html; ?>
                              <div class="Polaris-PositionedOverlay display-hide-label">
                                 <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                    <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">The cutoff indicates how many days in advance the order would need to be placed in order to qualify for the upcoming order cycle.</div>
                                 </div>
                              </div>
                           </div>
                           </div>
                           <div class="Polaris-Connected">
                              <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                 <div class="Polaris-TextField">
                                    <input placeholder="eg. 1" id="cut_off_days"  min="1" onkeypress="return event.charCode >= 48 &amp;&amp; event.charCode <= 57" type="number" aria-labelledby="PolarisTextField7Label" aria-invalid="false" autocomplete="off" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); this.value =
                                          !!this.value &amp;&amp; Math.abs(this.value) > 0 ? Math.abs(this.value) : null" value="" maxlength="4" class="Polaris-TextField__Input" name="cut_off_days" aria-labelledby="TextField6Label" aria-invalid="false" autocomplete="off">
                                    <div class="Polaris-TextField__Backdrop"></div>
                                 </div>
                              </div>
                              <div id="PolarisPortalsContainer"></div>
                           </div>
                           <?php echo $mainobj->sellingPlanFormError('sd_cut_off_month_error','Cutoff day cannot be greater than 30'); ?>
                           <?php echo $mainobj->sellingPlanFormError('sd_cut_off_week_error','Cutoff day cannot be greater than 7'); ?>
                        </div>
                     </div>
                   <!-- (13-7-2022) minimum and maximum cycle -->
                   <div class="Polaris-FormLayout__Items">
                     <div class="Polaris-FormLayout__Item ">
                        <div class="">
                           <div class="Polaris-Labelled__LabelWrapper">
                              <div class="Polaris-Label"><label id="PolarisSelect2Label" for="sd_subscriptionPlan" class="Polaris-Label__Text">Minimum number of cycle(optional)</label></div>
                              <div class="sd_minimumCycle sd_sellingPlans"><?php echo $tooltip_html; ?>
                              <div class="Polaris-PositionedOverlay display-hide-label">
                                 <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                    <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">Minimum number of billing iteration you want to bind your customers with, before they can cancel their subscription. Default value is one (the very first billing iteration).</div>
                                 </div>
                              </div>
                              </div>
                           </div>
                           <div class="Polaris-Connected">
                              <div class="Polaris-Connected__Item Polaris-Connected__Item--primary sd_zindex">
                                 <div class="Polaris-TextField">
                                    <input placeholder="eg. 5" id="minimum_number_cycle"  min="1" onkeypress="return event.charCode >= 48 &amp;&amp; event.charCode <= 57" type="number" aria-labelledby="PolarisTextField7Label" aria-invalid="false" autocomplete="off" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); this.value =
                                          !!this.value &amp;&amp; Math.abs(this.value) > 0 ? Math.abs(this.value) : null" value="" maxlength="4" class="Polaris-TextField__Input" name="minimum_number_cycle" aria-labelledby="TextField6Label" aria-invalid="false" autocomplete="off">
                                    <div class="Polaris-TextField__Backdrop"></div>
                                 </div>
                              </div>
                              <div id="PolarisPortalsContainer"></div>
                           </div>
                        </div>
                     </div>
                     <div class="Polaris-FormLayout__Item">
                        <div class="">
                           <div class="Polaris-Labelled__LabelWrapper">
                              <div class="Polaris-Label">
                                 <label id="TextField2Label" for="TextField2" class="Polaris-Label__Text">Maximum number of cycle(optional)</label>
                              </div>
                              <div class="sd_maximum_cycle sd_sellingPlans"><?php echo $tooltip_html; ?>
                              <div class="Polaris-PositionedOverlay display-hide-label">
                                 <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                    <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">Maximum number of billing iteration that will be fulfilled as a part of the subscription plan, after which it will automatically pause.</div>
                                 </div>
                              </div>
                              </div>
                           </div>
                           <div class="Polaris-Connected">
                              <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                 <div class="Polaris-TextField">
                                    <input placeholder="eg. 50" id="maximum_number_cycle"  min="1" onkeypress="return event.charCode >= 48 &amp;&amp; event.charCode <= 57" type="number" aria-labelledby="PolarisTextField7Label" aria-invalid="false" autocomplete="off" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); this.value =
                                          !!this.value &amp;&amp; Math.abs(this.value) > 0 ? Math.abs(this.value) : null" value="" maxlength="4" class="Polaris-TextField__Input" name="maximum_number_cycle" aria-labelledby="TextField6Label" aria-invalid="false" autocomplete="off">
                                    <div class="Polaris-TextField__Backdrop"></div>
                                 </div>
                              </div>
                           </div>
                           <?php echo $mainobj->sellingPlanFormError('maximum_number_cycle_error','Maximum number of cycle value is required.'); ?>
                           <?php echo $mainobj->sellingPlanFormError('maximum_number_cycle_validation_error','The maximum number of cycles must be greater than the minimum.'); ?>
                        </div>
                     </div>
                  </div>
                  <!-- minimum and maximum iteration cycle -->
                  <div class="Polaris-FormLayout__Item">
                     <div class="enable_copyright_box">
                        <div class="Polaris-Labelled__LabelWrapper">
                           <div class="Polaris-Label">
                              <label id="TextField1Label3" for="TextField3" class="Polaris-Label__Text">Offer discount(optional)</label>
                           </div>
                        </div>
                        <div class="Polaris-Connected">
                           <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                              <div class="Polaris-TextField">
                                 <label class="switch">
                                 <input type="checkbox" name="subscription_discount" id="subscription_discount_status">
                                 <span class="slider round"></span>
                                 </label>
                              </div>
                           </div>
                        </div>
                        <div class="sd_offer_discount sd_sellingPlans"><?php echo $tooltip_html; ?>
                        <div class="Polaris-PositionedOverlay display-hide-label">
                           <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                              <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">Discount will be applied on customer subscription when this plan is purchased. After that it cannot be disabled or changed in subscriptions, if the recurring discount is not set.</div>
                           </div>
                        </div>
                        </div>
                     </div>
                  </div>
                  <div class="sd_subscription_discount_offer_wrapper display-hide-label">
                     <div class="Polaris-FormLayout__Items">
                        <div class="Polaris-FormLayout__Item">
                           <div class="">
                              <div class="Polaris-Labelled__LabelWrapper">
                                 <div class="Polaris-Label"><label id="PolarisTextField7Label" for="PolarisTextField7" class="Polaris-Label__Text">Discount value</label></div>
                              </div>
                              <div class="Polaris-Connected">
                                 <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                    <div class="Polaris-TextField">
                                       <input placeholder="eg. 10" min="1" name="subscription_discount_value" id="subscription_discount_value" autocomplete="off" class="Polaris-TextField__Input preview_plan" aria-labelledby="PolarisTextField7Label" aria-invalid="false" value="" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); this.value =
                                          !!this.value &amp;&amp; Math.abs(this.value) >= 0 ? Math.abs(this.value) : null" type="number" maxlength="4">
                                       <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                 </div>
                              </div>
                              <?php echo $mainobj->sellingPlanFormError('subscription_discount_value_error','Discount Offer is required.'); ?>
                              <?php echo $mainobj->sellingPlanFormError('subscription_discount_value_greater_error','Discount Percentage value should not be greater than 100.'); ?>
                           </div>
                        </div>
                        <div class="Polaris-FormLayout__Item">
                           <div class="">
                              <div class="Polaris-Labelled__LabelWrapper hide-label">
                                 <div class="Polaris-Label"><label id="PolarisTextField8Label" for="PolarisTextField8" class="Polaris-Label__Text">hidden</label></div>
                              </div>
                              <div class="Polaris-Connected">
                                 <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                    <div class="Polaris-Select">
                                       <select id="subscription_discount_type" name="subscription_discount_type" class="Polaris-Select__Input" aria-invalid="false">
                                          <option value="Percent Off(%)">Percent Off(%)</option>
                                          <option value="Discount Off">Discount Off</option>
                                       </select>
                                       <div class="Polaris-Select__Content" aria-hidden="true">
                                          <span class="Polaris-Select__SelectedOption">Percent Off(%)</span>
                                          <span class="Polaris-Select__Icon">
                                             <span class="Polaris-Icon">
                                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                   <path d="m10 16-4-4h8l-4 4zm0-12 4 4H6l4-4z"></path>
                                                </svg>
                                             </span>
                                          </span>
                                       </div>
                                       <div class="Polaris-Select__Backdrop"></div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- change discount after start -->
                  <div class="sd_change_discount_after">
                     <div class="Polaris-FormLayout__Item" >
                        <div class="enable_copyright_box">
                           <div class="Polaris-Labelled__LabelWrapper">
                              <div class="Polaris-Label">
                                 <label id="TextField1Label3" for="TextField3" class="Polaris-Label__Text">Change discount after(optional)</label>
                              </div>
                           </div>
                           <div class="Polaris-Connected">
                              <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                 <div class="Polaris-TextField">
                                    <label class="switch">
                                    <input type="checkbox" name="subscription_discount_after" id="subscription_discount_after_status">
                                    <span class="slider round"></span>
                                    </label>
                                 </div>
                              </div>
                           </div>
                           <div class="sd_offer_discount sd_sellingPlans"><?php echo $tooltip_html; ?>
                           <div class="Polaris-PositionedOverlay display-hide-label">
                              <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                 <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">Customer subscription discount will be changed after the order cycle you mentioned, after that discount will not be disabled or changed.</div>
                              </div>
                           </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="sd_subscription_discount_offer_after_wrapper display-hide-label">
                  <div class="sd_subscription_discount_offer_after_wrapper display-hide-label">
                     <div class="Polaris-FormLayout__Items">
                        <div class="Polaris-FormLayout__Item">
                           <div class="">
                              <div class="Polaris-Labelled__LabelWrapper">
                                 <div class="Polaris-Label">
                                    <label id="PolarisTextField7Label" for="PolarisTextField7" class="Polaris-Label__Text">Change discount after cycle</label>
                                 </div>
                              </div>
                        <div class="Polaris-Connected">
                                 <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                    <div class="Polaris-TextField">
                                       <input placeholder="eg. 4"  name="change_discount_after_cycle"  id="change_discount_after_cycle" class="Polaris-TextField__Input preview_plan" min="1" type="number" autocomplete="off" aria-labelledby="PolarisTextField7Label" aria-invalid="false" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); this.value =
                                          !!this.value &amp;&amp; Math.abs(this.value) > 0 ? Math.abs(this.value) : null" type="number" maxlength="4"  value="">
                                       <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                 </div>
                              </div>
                             <?php echo $mainobj->sellingPlanFormError('change_discount_after_cycle_error','Enter discount after cycle.'); ?>
                           </div>
                        </div>
                     </div>
                        </div>
                     <div class="Polaris-FormLayout__Items">
                        <div class="Polaris-FormLayout__Item">
                           <div class="">
                              <div class="Polaris-Labelled__LabelWrapper">
                                 <div class="Polaris-Label"><label id="PolarisTextField7Label" for="PolarisTextField7" class="Polaris-Label__Text">Discount value</label></div>
                              </div>
                              <div class="Polaris-Connected">
                                 <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                    <div class="Polaris-TextField">
                                       <input placeholder="eg. 5" min="1" name="discount_value_after" id="discount_value_after" class="Polaris-TextField__Input preview_plan" aria-labelledby="PolarisTextField7Label" autocomplete="off" aria-invalid="false" value="" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); this.value =
                                          !!this.value &amp;&amp; Math.abs(this.value) >= 0 ? Math.abs(this.value) : null" type="number" maxlength="4">
                                       <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                 </div>
                              </div>
                              <?php echo $mainobj->sellingPlanFormError('discount_value_after_error','Discount Offer is required.'); ?>
                              <?php echo $mainobj->sellingPlanFormError('subscription_discount_value_greater_after_error','Discount Percentage value should not be greater than 100.'); ?>
                              <?php echo $mainobj->sellingPlanFormError('discount_value_same_after_error','After Discount Value cannot be same as discount value.'); ?>
                           </div>
                        </div>
                        <div class="Polaris-FormLayout__Item">
                           <div class="">
                              <div class="Polaris-Labelled__LabelWrapper hide-label">
                                 <div class="Polaris-Label"><label id="PolarisTextField8Label" for="PolarisTextField8" class="Polaris-Label__Text">hidden</label></div>
                              </div>
                              <div class="Polaris-Connected">
                                 <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                    <div class="Polaris-Select">
                                       <select id="subscription_discount_type_after" name="subscription_discount_type_after" class="Polaris-Select__Input" aria-invalid="false">
                                          <option value="Percent Off(%)">Percent Off(%)</option>
                                          <option value="Discount Off">Discount Off</option>
                                       </select>
                                       <div class="Polaris-Select__Content" aria-hidden="true">
                                          <span class="Polaris-Select__SelectedOption">Percent Off(%)</span>
                                          <span class="Polaris-Select__Icon">
                                             <span class="Polaris-Icon">
                                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                   <path d="m10 16-4-4h8l-4 4zm0-12 4 4H6l4-4z"></path>
                                                </svg>
                                             </span>
                                          </span>
                                       </div>
                                       <div class="Polaris-Select__Backdrop"></div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- change discount after end-->
               </div>
            </div>
            <div class="right-button-groups">
               <div class="Polaris-ButtonGroup ">
                  <div class="create-subscription-buttons">
                     <div class="Polaris-ButtonGroup__Item step2_button_cancel display-hide-label"><button class="Polaris-Button go-to-step3" type="button">Cancel</button></div>
                     <div class="Polaris-ButtonGroup__Item step2_button_previous"><button class="Polaris-Button go-to-step1" type="button">
                        Previous</button>
                     </div>
                  </div>
                  <div class="edit-subscription-buttons display-hide-label">
                     <div class="Polaris-ButtonGroup__Item  edit-cancel "><button class="Polaris-Modal-CloseButton copy_sellingform_to_create Polaris-Button" type="button">Cancel</button></div>
                  </div>
                  <div class="Polaris-ButtonGroup__Item edit-create-add-btn"><button id="sd_add_frequency" class="step2_button_submit Polaris-Button Polaris-Button--primary subscription_validate_step2" type="button">Add</button></div>
               </div>
               <div id="PolarisPortalsContainer"></div>
           </div>
         </div>
      </div>
   </div>
</div>
