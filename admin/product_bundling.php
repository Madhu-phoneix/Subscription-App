<?php
   include("header.php");
    $whereCondition = array(
        'store_id' => $mainobj->store_id
    );
    $widget_setting_data = $mainobj->single_row_value('product_bundles','All',$whereCondition,'and','');
    if($widget_setting_data){
        $bundle_name = $widget_setting_data['bundle_name'];
        $bundle_title = $widget_setting_data['bundle_title'];
        $description = $widget_setting_data['description'];
        $action_button_text = $widget_setting_data['action_button_text'];
        $bundle_discount_type = $widget_setting_data['bundle_discount_type'];
        $bundle_discount_value = $widget_setting_data['bundle_discount_value'];
        $under_button_text = $widget_setting_data['under_button_text'];
        $status = $widget_setting_data['status'];
        $bundle_type = $widget_setting_data['bundle_type'];
        $bundle_products = $widget_setting_data['bundle_products'];
        echo "<script>picker_selection_checkboxes = ".$widget_setting_data['bundle_products']."
        console.log('picker_selection_checkboxes',picker_selection_checkboxes);
        </script>";
    }else{
        $bundle_name = 'Create';
        $bundle_title = 'Create bundle';
        $description = '';
        $action_button_text = 'Checkout';
        $bundle_discount_type = 'Percentage';
        $bundle_discount_value = '10';
        $under_button_text = '';
        $status = 'Active';
        $bundle_type = 'Classic';
        $bundle_products = '';
    }

?>
<div class="Polaris-Layout">
<?php
  include("navigation.php");
?>

<form class="sd_product_bundle" id="sd_product_bundle" method="POST">
  <input type="hidden" name="store_id" value='<?php echo $mainobj->store_id; ?>'>
    <div class="sd_product_bundles">
        <div class="Polaris-Card">
            <div class="Polaris-Card__Section">
                <div class="Polaris-FormLayout">
                    <div class="Polaris-FormLayout__Item">
                        <div class="Polaris-TextContainer subscription-step-heading">
                            <h2 class="Polaris-Heading">Create Bundle</h2>
                        </div>
                    </div>
                    <div class="sd_selling_form">
                        <div class="Polaris-FormLayout__Items">
                            <div class="Polaris-FormLayout__Item ">
                                <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisSelect2Label" for="sd_subscriptionPlan" class="Polaris-Label__Text">Bundle Name</label></div>
                                    </div>
                                    <div class="Polaris-Select">
                                        <input placeholder="" id="bundle_name" type="text" value="<?php echo $bundle_name; ?>" class="Polaris-TextField__Input sd_validate_expration" name="bundle_name" aria-labelledby="TextField6Label" aria-invalid="false" autocomplete="off">
                                        <div class="Polaris-TextField__Backdrop"></div>
                                    </div>
                                    <span class="sd_bundle_name_error" style="color:red;"></span>
                                </div>
                                <div id="PolarisPortalsContainer"></div>
                            </div>
                            <div class="Polaris-FormLayout__Item">
                                <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label">
                                            <label id="TextField2Label" for="TextField2" class="Polaris-Label__Text">Title</label>
                                        </div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField">
                                                <input placeholder="" id="bundle_title" type="text" value="<?php echo $bundle_title; ?>" class="Polaris-TextField__Input sd_validate_expration" maxlength="50" name="bundle_title" aria-labelledby="TextField6Label" aria-invalid="false" autocomplete="off">
                                                <div class="Polaris-TextField__Backdrop"></div>
                                            </div>
                                            <span class="sd_bundle_title_error" style="color:red;"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="Polaris-FormLayout__Items " style="display: flex;">
                            <div class="Polaris-FormLayout__Item sd_prepaid_fields">
                                <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisTextField7Label" for="PolarisTextField7" class="Polaris-Label__Text">Description</label></div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField sd-small-textfield">
                                                <input placeholder="" id="description" type="text" value="<?php echo $description; ?>" class="Polaris-TextField__Input sd_validate_expration" name="description" aria-labelledby="TextField6Label" aria-invalid="false" autocomplete="off">
                                                <div class="Polaris-TextField__Backdrop"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="Polaris-FormLayout__Item">
                                <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="sd_order_fulfillment_frequency" for="PolarisTextField7" class="Polaris-Label__Text">Action Button Text</label></div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField sd-small-textfield">
                                                <input placeholder="" id="action_button_text" type="text" value="<?php echo $action_button_text; ?>" class="Polaris-TextField__Input sd_validate_expration" name="action_button_text" aria-labelledby="TextField6Label" aria-invalid="false" autocomplete="off">
                                                <div class="Polaris-TextField__Backdrop"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="Polaris-FormLayout__Items">
                            <div class="Polaris-FormLayout__Item">
                                <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisTextField7Label" for="PolarisTextField7" class="Polaris-Label__Text">Text under button</label></div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField sd-small-textfield">
                                                <input placeholder="" id="under_button_text" type="text" value="<?php echo $under_button_text; ?>" class="Polaris-TextField__Input sd_validate_expration" name="under_button_text" aria-labelledby="TextField6Label" aria-invalid="false" autocomplete="off">
                                                <div class="Polaris-TextField__Backdrop"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="Polaris-FormLayout__Item ">
                                <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisSelect2Label" for="sd_subscriptionPlan" class="Polaris-Label__Text">Status</label></div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-Select">
                                                <select id="subscription_discount_type" name="status" class="Polaris-Select__Input" aria-invalid="false">
                                                    <option value="Active" <?php if($status == 'Active'){ echo 'selected'; } ?>>Active</option>
                                                    <option value="Pause" <?php if($status == 'Pause'){ echo 'selected'; } ?>>Pause</option>
                                                </select>
                                                <div class="Polaris-Select__Content" aria-hidden="true">
                                                    <span class="Polaris-Select__SelectedOption"><?php echo $status; ?></span>
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
                </div>
            </div>
        </div>
        <div class="Polaris-Card">
            <div class="Polaris-Card__Section">
                <div class="Polaris-FormLayout__Items">
                    <div class="Polaris-FormLayout__Item ">
                        <div class="">
                            <div class="Polaris-Labelled__LabelWrapper">
                                <div class="Polaris-Label"><label id="PolarisSelect2Label" for="sd_subscriptionPlan" class="Polaris-Label__Text">Bundle Type</label></div>
                            </div>
                            <div class="Polaris-Connected">
                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                    <div class="Polaris-Select">
                                        <select id="bundle_type" name="bundle_type" class="Polaris-Select__Input" aria-invalid="false">
                                            <option value="Classic" <?php if($bundle_type == 'Classic'){ echo 'selected'; }?>>Classic</option>
                                            <option value="Mix and Match" <?php if($bundle_type == 'Mix and Match'){ echo 'selected'; }?>>Mix and Match</option>
                                        </select>
                                        <div class="Polaris-Select__Content" aria-hidden="true">
                                            <span class="Polaris-Select__SelectedOption"><?php echo $bundle_type; ?></span>
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
                    <div class="Polaris-FormLayout__Item">
                        <div class="">
                            <div class="Polaris-Labelled__LabelWrapper">
                                <div class="Polaris-Label"><label id="PolarisTextField7Label" for="PolarisTextField7" class="Polaris-Label__Text">Discount value</label></div>
                            </div>
                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                <div class="Polaris-TextField sd-small-textfield">
                                    <input placeholder="eg. 10" name="bundle_discount_value" id="bundle_discount_value" class="Polaris-TextField__Input preview_plan" min="1" autocomplete="off" type="number" aria-labelledby="PolarisTextField7Label" aria-invalid="false" value="<?php echo $bundle_discount_value; ?>">
                                    <div class="Polaris-TextField__Backdrop"></div>
                                    <div class="Polaris-Select sd-small-select">
                                        <select id="bundle_discount_type" name="bundle_discount_type" class="Polaris-Select__Input" aria-invalid="false">
                                            <option value="Percentage" <?php if($bundle_discount_type == 'Percentage'){ echo 'selected';} ?>>Percentage</option>
                                            <option value="Fixed Amount" <?php if($bundle_discount_type == 'Fixed Amount'){ echo 'selected';} ?>>Fixed Amount</option>
                                        </select>
                                        <div class="Polaris-Select__Content" aria-hidden="true">
                                            <span id="sd_prepaid_billing_type_Selected_value" class="Polaris-Select__SelectedOption"><?php echo $bundle_discount_type; ?></span>
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
        </div>
        <div class="Polaris-Card">
            <div class="Polaris-Card__Section">
                <div class="Polaris-Card__SectionHeader">
                    <div>
                        <button parent-id="subscription_edit_prodcts" product-display-style="bundle_products" id="add_products" class="add_newProducts Polaris-Button" type="button">Add Products</button>
                        <div id="PolarisPortalsContainer"></div>
                    </div>
                </div>
                <span class="sd_add_products_error" style="color:red;"></span>
                <div>
                    <div class="Polaris-Stack Polaris-Stack--spacingTight show_selected_products <?php if($widget_setting_data){}else { echo 'display-hide-label'; } ?>">
                        <ul class="Polaris-ResourceList" id="subscription_edit_prodcts">
                        <?php if($widget_setting_data){
                            foreach(json_decode($widget_setting_data['bundle_products']) as $key=>$products_data){
                        ?>
                        <li class="Polaris-ResourceItem__ListItem" id="display_product_<?php echo $products_data->variant_id; ?>">
                            <div class="Polaris-ResourceItem__ItemWrapper">
                                <div class="Polaris-ResourceItem Polaris-ResourceItem--selectable">
                                    <div class="Polaris-ResourceItem__Container">
                                        <div class="Polaris-ResourceItem__Owned">
                                            <div class="Polaris-ResourceItem__Media"><span role="img" class="Polaris-Avatar Polaris-Avatar--sizeMedium Polaris-Avatar--hasImage"><img src="<?php echo $products_data->image; ?>" class="Polaris-Avatar__Image" alt="" role="presentation"></span></div>
                                        </div>
                                        <div class="Polaris-ResourceItem__Content">
                                            <h3><span class="Polaris-TextStyle--variationStrong"><?php echo $products_data->product_title.'-'.$products_data->variant_title; ?></span></h3>
                                            <div></div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" variant-id='<?php echo $products_data->variant_id; ?>' product-id='<?php echo $products_data->product_id; ?>' class="Polaris-Tag__Button display_remove_variant">
                                <span class="Polaris-Icon">
                                    <span class="Polaris-VisuallyHidden"></span>
                                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                        <path d="m11.414 10 4.293-4.293a.999.999 0 1 0-1.414-1.414L10 8.586 5.707 4.293a.999.999 0 1 0-1.414 1.414L8.586 10l-4.293 4.293a.999.999 0 1 0 1.414 1.414L10 11.414l4.293 4.293a.997.997 0 0 0 1.414 0 .999.999 0 0 0 0-1.414L11.414 10z"></path>
                                    </svg>
                                </span>
                            </div>
                        </li>
                        <?php } } ?>
                        </ul>
                    </div>
                    <div id="PolarisPortalsContainer"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="right-button-groups">
        <div class="Polaris-ButtonGroup ">
            <div class="edit-subscription-buttons">
                <div class="Polaris-ButtonGroup__Item  edit-cancel "><button class="Polaris-Modal-CloseButton copy_sellingform_to_create Polaris-Button" type="button">Cancel</button></div>
            </div>
            <div class="Polaris-ButtonGroup__Item edit-create-add-btn"><button class="step2_button_submit Polaris-Button Polaris-Button--primary sd_add_bundle" type="button">Update</button></div>
        </div>
        <div id="PolarisPortalsContainer"></div>
    </div>
    </div>
    </div>
    </div>
    </div>
</form>
<?php
   include("footer.php");
?>
