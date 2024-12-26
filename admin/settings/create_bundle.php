<?php
  include("../header.php");
?>
  <div class="Polaris-Layout">
<?php
    include("../navigation.php");
?>

<div class="Polaris-Layout__Section  sd-bundle_list-page">
    <div>
                <div class="Polaris-Card__Header sd_contractHeader">
                    <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                        <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                            <h2 class="Polaris-Heading">Create Bundle</h2>
                        </div>
                    </div>
                </div>
                <div class="Polaris-Page__Content">
                    <div>


                    <div class="Polaris-Card">
                <div class="Polaris-Card__Section">
                    <div class="Polaris-FormLayout">
                    <div class="Polaris-BlockStack" style="--pc-block-stack-order:column;--pc-block-stack-gap-xs:var(--p-space-400)">
                        <div class="Polaris-BlockStack" style="--pc-block-stack-order:column;--pc-block-stack-gap-xs:var(--p-space-200)" role="group">
                            <div class="Polaris-InlineStack" style="--pc-inline-stack-wrap:wrap;--pc-inline-stack-gap-xs:var(--p-space-300);display:flex;">
                                <div class="Polaris-FormLayout__Item Polaris-FormLayout--grouped">
                                    <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label">
                                        <label id=":R1mq6:Label" for=":R1mq6:" class="Polaris-Label__Text">Bundle name</label>
                                        </div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField">
                                            <input id="bundle_name" name="bundle_name" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby=":R1mq6:Label" aria-invalid="false" data-1p-ignore="true" data-lpignore="true" data-form-type="other" value="">
                                            <div class="Polaris-TextField__Backdrop">
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="Polaris-FormLayout__Item Polaris-FormLayout--grouped">
                                    <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label">
                                        <label id=":R2mq6:Label" for=":R2mq6:" class="Polaris-Label__Text">Action Button Text</label>
                                        </div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField">
                                            <input id="action_button_text" name="action_button_text" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby=":R2mq6:Label" aria-invalid="false" data-1p-ignore="true" data-lpignore="true" data-form-type="other" value="">
                                            <div class="Polaris-TextField__Backdrop">
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="Polaris-InlineStack" style="--pc-inline-stack-wrap:wrap;--pc-inline-stack-gap-xs:var(--p-space-300);display:flex;">
                                <div class="Polaris-FormLayout__Item Polaris-FormLayout--grouped">
                                    <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label">
                                            <label id=":R1mq6:Label" for=":R1mq6:" class="Polaris-Label__Text">Text under button</label>
                                            </div>
                                        </div>
                                        <div class="Polaris-Connected">
                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField">
                                                <input id="text_under_button" name="text_under_button" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby=":R1mq6:Label" aria-invalid="false" data-1p-ignore="true" data-lpignore="true" data-form-type="other" value="">
                                                <div class="Polaris-TextField__Backdrop">
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="Polaris-FormLayout__Item">
                                    <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisSelect2Label" for="bundle_status" class="Polaris-Label__Text">Status</label></div>
                                    </div>
                                    <div class="Polaris-Select">
                                        <select id="bundle_status" class="Polaris-Select__Input bundle_type" aria-invalid="false" name="bundle_status">
                                            <option value="Active">Active</option>
                                            <option value="Pause">Pause</option>
                                        </select>
                                        <div class="Polaris-Select__Content" aria-hidden="true">
                                            <span id="bundle_status_selected_value" class="Polaris-Select__SelectedOption">Active</span>
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
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="Polaris-Card">
                <div class="Polaris-Card__Section">
                    <div class="Polaris-FormLayout">
                    <div class="Polaris-BlockStack" style="--pc-block-stack-order:column;--pc-block-stack-gap-xs:var(--p-space-400)">
                        <div class="Polaris-BlockStack" style="--pc-block-stack-order:column;--pc-block-stack-gap-xs:var(--p-space-200)" role="group">
                            <div class="Polaris-InlineStack" style="--pc-inline-stack-wrap:wrap;--pc-inline-stack-gap-xs:var(--p-space-300);display:flex;">
                                <div class="Polaris-FormLayout__Item ">
                                    <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisSelect2Label" for="sd_subscriptionPlan" class="Polaris-Label__Text">Bundle Type</label></div>
                                    </div>
                                    <div class="Polaris-Select">
                                        <select id="bundle_plan_type" class="Polaris-Select__Input bundle_plan_type" aria-invalid="false" name="bundle_plan_type">
                                            <option value="Classic" data-value="classic">Classic</option>
                                            <option value="Mix and Match" data-value="mix_and_match">Mix and Match</option>
                                        </select>
                                        <div class="Polaris-Select__Content" aria-hidden="true">
                                            <span id="bundle_plan_type_selected_value" class="Polaris-Select__SelectedOption">Classic</span>
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
                            </div>
                            <div class="Polaris-InlineStack display-hide-label min_max_items" style="--pc-inline-stack-wrap:wrap;--pc-inline-stack-gap-xs:var(--p-space-300);display:flex;">
                                <div class="Polaris-FormLayout__Item Polaris-FormLayout--grouped">
                                    <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label">
                                        <label id="min_item_required_label" for="min_item_required" class="Polaris-Label__Text">Minimum number of items required.</label>
                                        </div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField">
                                            <input id="min_item_required" name="min_item_required" autocomplete="off" class="Polaris-TextField__Input" type="number" aria-labelledby=":R1mq6:Label" aria-invalid="false" data-1p-ignore="true" data-lpignore="true" data-form-type="other" value="">
                                            <div class="Polaris-TextField__Backdrop">
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="Polaris-FormLayout__Item Polaris-FormLayout--grouped">
                                    <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label">
                                            <label id="min_item_requiredLabel" for="min_item_required" class="Polaris-Label__Text">Maximum number of items required.</label>
                                        </div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField">
                                            <input id="min_item_required" name="min_item_required" autocomplete="off" class="Polaris-TextField__Input" type="number" aria-labelledby=":R2mq6:Label" aria-invalid="false" data-1p-ignore="true" data-lpignore="true" data-form-type="other" value="">
                                            <div class="Polaris-TextField__Backdrop">
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="Polaris-InlineStack" style="--pc-inline-stack-wrap:wrap;--pc-inline-stack-gap-xs:var(--p-space-300);display:flex;">
                                <div class="Polaris-FormLayout__Item Polaris-FormLayout--grouped">
                                    <label class="Polaris-Choice Polaris-Checkbox__ChoiceLabel" for="default">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                            <input id="default" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="">
                                            <span class="Polaris-Checkbox__Backdrop">
                                            </span>
                                            <span class="Polaris-Checkbox__Icon Polaris-Checkbox--animated">
                                                <svg viewBox="0 0 16 16" shape-rendering="geometricPrecision" text-rendering="geometricPrecision">
                                                <path class="" d="M1.5,5.5L3.44655,8.22517C3.72862,8.62007,4.30578,8.64717,4.62362,8.28044L10.5,1.5" transform="translate(2 2.980376)" opacity="0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" pathLength="1">
                                                </path>
                                                </svg>
                                            </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">
                                            <span>Select subscription by default</span>
                                        </span>
                                    </label>
                                </div>
                                <div class="Polaris-FormLayout__Item Polaris-FormLayout--grouped">
                                    <label class="Polaris-Choice Polaris-Checkbox__ChoiceLabel" for="combined">
                                        <span class="Polaris-Choice__Control">
                                            <span class="Polaris-Checkbox">
                                            <input id="combined" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="">
                                            <span class="Polaris-Checkbox__Backdrop">
                                            </span>
                                            <span class="Polaris-Checkbox__Icon Polaris-Checkbox--animated">
                                                <svg viewBox="0 0 16 16" shape-rendering="geometricPrecision" text-rendering="geometricPrecision">
                                                <path class="" d="M1.5,5.5L3.44655,8.22517C3.72862,8.62007,4.30578,8.64717,4.62362,8.28044L10.5,1.5" transform="translate(2 2.980376)" opacity="0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" pathLength="1">
                                                </path>
                                                </svg>
                                            </span>
                                            </span>
                                        </span>
                                        <span class="Polaris-Choice__Label">
                                            <span>Show combined selling plan</span>
                                        </span>
                                    </label>
                                </div>

                            </div>

                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="Polaris-Card">
                <div class="Polaris-Card__Section">
                    <div class="Polaris-FormLayout">
                        <div class="Polaris-BlockStack" style="--pc-block-stack-order:column;--pc-block-stack-gap-xs:var(--p-space-400)">
                            <div class="Polaris-BlockStack" style="--pc-block-stack-order:column;--pc-block-stack-gap-xs:var(--p-space-200)" role="group">
                                <div class="Polaris-InlineStack" style="--pc-inline-stack-wrap:wrap;--pc-inline-stack-gap-xs:var(--p-space-300);display:flex;">
                                    <div class="Polaris-FormLayout__Item Polaris-FormLayout--grouped">
                                        <div class="">
                                            <div class="Polaris-Labelled__LabelWrapper">
                                                <div class="Polaris-Label">
                                                <label id=":R1mq6:Label" for=":R1mq6:" class="Polaris-Label__Text">Discount</label>
                                                </div>
                                            </div>
                                            <div class="Polaris-Connected">
                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                    <div class="Polaris-TextField">
                                                        <input id="bundle_discount" name="bundle_discount" autocomplete="off" class="Polaris-TextField__Input" type="number" aria-labelledby=":R1mq6:Label" aria-invalid="false" data-1p-ignore="true" data-lpignore="true" data-form-type="other" value="">
                                                        <div class="Polaris-TextField__Backdrop">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="Polaris-FormLayout__Item">
                                        <div class="">
                                        <div class="Polaris-Labelled__LabelWrapper">
                                            <div class="Polaris-Label"><label id="PolarisSelect2Label" for="bundle_status" class="Polaris-Label__Text">Type</label></div>
                                        </div>
                                        <div class="Polaris-Select">
                                            <select id="bundle_status" class="Polaris-Select__Input bundle_type" aria-invalid="false" name="bundle_status">
                                                <option value="Percentage" data-value="percentage">Percentage</option>
                                                <option value="Fixed Amount" data-value="fixed_amount">Fixed Amount</option>
                                            </select>
                                            <div class="Polaris-Select__Content" aria-hidden="true">
                                                <span id="bundle_status_selected_value" class="Polaris-Select__SelectedOption">Percentage</span>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="Polaris-Card">
                <div class="Polaris-Card__Header sd_contractHeader">
                    <div class="Polaris-Stack Polaris-Stack--alignmentBaseline">
                        <div class="Polaris-Stack__Item Polaris-Stack__Item--fill">
                            <h2 class="Polaris-Heading">Select bundle products</h2>
                        </div>
                    </div>
                </div>
                <div class="Polaris-Card__Section">
                    <div class="Polaris-FormLayout">
                        <div class="Polaris-BlockStack" style="--pc-block-stack-order:column;--pc-block-stack-gap-xs:var(--p-space-400)">
                            <div class="Polaris-BlockStack" style="--pc-block-stack-order:column;--pc-block-stack-gap-xs:var(--p-space-200)" role="group">
                            <!-- <div class="Polaris-InlineStack" style="--pc-inline-stack-wrap:wrap;--pc-inline-stack-gap-xs:var(--p-space-300);display:flex;">
                                <div class="Polaris-FormLayout__Item Polaris-FormLayout--grouped">
                                <div class="Polaris-LegacyStack Polaris-LegacyStack--vertical">
                                    <div class="Polaris-LegacyStack__Item">
                                        <div>
                                        <label class="Polaris-Choice Polaris-RadioButton__ChoiceLabel" for="level_product">
                                            <span class="Polaris-Choice__Control">
                                            <span class="Polaris-RadioButton">
                                                <input id="level_product" name="bundle_level" type="radio" class="Polaris-RadioButton__Input" aria-describedby="disabledHelpText" checked="" value="">
                                                <span class="Polaris-RadioButton__Backdrop">
                                                </span>
                                            </span>
                                            </span>
                                            <span class="Polaris-Choice__Label">
                                            <span>Products</span>
                                            </span>
                                        </label>
                                        <div class="Polaris-Choice__Descriptions">
                                            <div class="Polaris-Choice__HelpText" id="disabledHelpText">
                                            <span class="Polaris-Text--root Polaris-Text--subdued">Bundle will be applied on the product.</span>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <div class="Polaris-FormLayout__Item Polaris-FormLayout--grouped">
                                <div class="Polaris-LegacyStack Polaris-LegacyStack--vertical">
                                    <div class="Polaris-LegacyStack__Item">
                                        <div>
                                        <label class="Polaris-Choice Polaris-RadioButton__ChoiceLabel" for="level_variant">
                                            <span class="Polaris-Choice__Control">
                                            <span class="Polaris-RadioButton">
                                                <input id="level_variant" name="bundle_level" type="radio" class="Polaris-RadioButton__Input" aria-describedby="disabledHelpText" value="">
                                                <span class="Polaris-RadioButton__Backdrop">
                                                </span>
                                            </span>
                                            </span>
                                            <span class="Polaris-Choice__Label">
                                            <span>Variant</span>
                                            </span>
                                        </label>
                                        <div class="Polaris-Choice__Descriptions">
                                            <div class="Polaris-Choice__HelpText" id="disabledHelpText">
                                            <span class="Polaris-Text--root Polaris-Text--subdued">Bundle will be applied on the variants.</span>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div> -->
                            <div class="Polaris-InlineStack" style="--pc-inline-stack-wrap:wrap;--pc-inline-stack-gap-xs:var(--p-space-300);">
                                <div class="Polaris-FormLayout__Item Polaris-FormLayout--grouped">
                                    <div class="Polaris-LegacyStack Polaris-LegacyStack--vertical">
                                        <div class="Polaris-InlineStack" style="--pc-inline-stack-align: center; --pc-inline-stack-wrap: wrap; --pc-inline-stack-gap-xs: var(--p-space-200);">
                                            <button class="Polaris-Button Polaris-Button--variantPrimary select_product_btn" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Select Products</span></span></button>
                                            <button class="Polaris-Button Polaris-Button--variantPrimary select_variant_btn display-hide-label" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Select Variants</span></span></button>
                                        </div>
                                    </div>
                                    <div class="Polaris-Stack Polaris-Stack--spacingTight show_selected_products display-hide-label">
                                        <ul class="Polaris-ResourceList" id="bundle_selected_prodcts"></ul>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <div class="Polaris-Card">
                <Text variant="headingMd" as="h2">Select bundle level</Text>
                <div class="Polaris-Card__Section">
                    <div class="Polaris-FormLayout">
                        <div class="Polaris-ResourceList__ResourceListWrapper">
                            <div class="Polaris-Box" style="--pc-box-padding-block-start-xs: var(--p-space-500); --pc-box-padding-block-end-xs: var(--p-space-1600); --pc-box-padding-inline-start-xs: var(--p-space-0); --pc-box-padding-inline-end-xs: var(--p-space-0);">
                                <div class="Polaris-BlockStack" style="--pc-block-stack-inline-align: center; --pc-block-stack-order: column;">
                                    <img alt="" src="https://cdn.shopify.com/s/files/1/2376/3301/products/emptystate-files.png" class="" role="presentation">
                                    <div class="Polaris-Box" style="--pc-box-max-width: 400px;">
                                        <div class="Polaris-BlockStack" style="--pc-block-stack-inline-align: center; --pc-block-stack-order: column;">
                                            <div class="Polaris-Box" style="--pc-box-padding-block-end-xs: var(--p-space-400);">
                                                <div class="Polaris-Box" style="--pc-box-padding-block-end-xs: var(--p-space-150);">
                                                    <p class="Polaris-Text--root Polaris-Text--headingMd Polaris-Text--block Polaris-Text--center">Select variants to get started</p>
                                                </div>
                                            </div>
                                            <div class="Polaris-InlineStack" style="--pc-inline-stack-align: center; --pc-inline-stack-wrap: wrap; --pc-inline-stack-gap-xs: var(--p-space-200);"><button class="Polaris-Button Polaris-Button--variantPrimary" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Select Variants</span></span></button></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->


            <div class="right-button-groups">
                <div class="Polaris-ButtonGroup step1_button_group">
                    <div class="Polaris-ButtonGroup__Item"><button class="Polaris-Button Polaris-Button--primary save-bundle-data" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Save</span></span></button></div>
                </div>
                <div id="PolarisPortalsContainer"></div>
            </div>

</div>


                    </div>
                </div>
            </div>
        </div>
<?php
  include("../footer.php");
?>
