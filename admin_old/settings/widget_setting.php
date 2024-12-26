<?php
   include("../header.php");
    if($active_plan_name == 'Free' || $active_plan_name == 'Free_old'){
        $url_redir = $mainobj->SHOPIFY_DOMAIN_URL."/admin/app_plans.php?shop={$mainobj->store}";
        echo '<script type="text/javascript">window.top.location.href = "' . $url_redir . '" </script>';
        die;
    }
    $whereCondition = array(
      'store_id' => $mainobj->store_id
    );
    $widget_setting_data = $mainobj->single_row_value('widget_settings','All',$whereCondition,'and','');

    if(empty($widget_setting_data)){
        $widget_settings_array = array(
            'one_time_purchase_text' => array('One time purchase text','One Time Purchase'),
            'purchase_options_text' => array('Purchase options text','Purchase Options'),
            'subscription_options_text' => array('Subscription options text','Subscribe and Save'),
            'prepaid_options_text' => array('Prepaid options text','Prepaid'),
            'ppd_options_text' => array('Pay per delivery options text','Pay per delivery'),
            'month_frequency_text' => array('Month(s) frequency text','Month(s)'),
            'day_frequency_text' => array('Day(s) frequency text','Day(s)'),
            'week_frequency_text' => array('Week(s) frequency text','Week(s)'),
            'year_frequency_text' => array('Year frequency text','Year(s)'),
            'save_text' => array('Save text','Save'),
            'delivery_every_text' => array('Delivery every text','Delivery every'),
            'pre_pay_for_text' => array('Pre-Pay for text','Pre-pay for'),
            'each_text' => array('Each text','each'),
            'description_text' => array('Description text','Description'),
            'one_time_purchase_description' => array('one_time_purchase_description','One time purchase product'),
            'subscription_detail_text' => array('Subscription detail text','Subscription detail'),
            'additional_subscription_detail' => array('Additional subscription detail','Have complete control of your subscritpion Skip, reschedule, edit or cancel deliveries any time based on your needs.'),
            'total_payable_amount_text' => array('Total payable amount text','Total payable'),
            'total_saved_discount_text' => array('Total saved discount text','Total saved'),
            'border_style' => array('Border style','solid'),
            'on_first_text' => array('On first text','on first'),
            'orders_text' => array('Order(s) text','order(s)'),
            'then_text' => array('Then text','then'),
            'text_color' => array('Text color','#1D1D1D'),
            'price_color' => array('Price color','#5F5F5F'),
            'discounted_price_color' => array('Discounted Price color','#33353E'),
            'discount_label_color' => array('Discount label color','#F94556'),
            // 'active_otpion_circle_color' => array('Active option circle color', 'none'),
            'border_color' => array('Border color','#09080D'),
            'widget_bg_color' => array('Widget Background Color','#FFFFFF'),
            // 'active_option_box_bg_color' => array('Active Option background color','#F0F2F5'),
            'discount_bg_color' => array('Discount Label background color','none'),
            'options_bg_color1' => array('Active Option background color', '#C4C4C4'),
            'options_bg_color2' => array('Active Option background color', '#F0F0F0')
        );
        $widget_arrangement = explode(',','One Time Purchase,Pay Per Delivery,Prepaid');
        $selected_widget_template = '1';
        $default_selected_option = '1';
    }else{
        $widget_settings_array = array(
            'one_time_purchase_text' => array('One time purchase text',$widget_setting_data['one_time_purchase_text']),
            'purchase_options_text' => array('Purchase options text',$widget_setting_data['purchase_options_text']),
            'subscription_options_text' => array('Subscription options text',$widget_setting_data['subscription_options_text']),
            'prepaid_options_text' => array('Prepaid options text',$widget_setting_data['prepaid_options_text']),
            'ppd_options_text' => array('Pay per delivery options text',$widget_setting_data['ppd_options_text']),
            'month_frequency_text' => array('Month(s) frequency text',$widget_setting_data['month_frequency_text']),
            'day_frequency_text' => array('Day(s) frequency text',$widget_setting_data['day_frequency_text']),
            'week_frequency_text' => array('Week(s) frequency text',$widget_setting_data['week_frequency_text']),
            'year_frequency_text' => array('Year frequency text',$widget_setting_data['year_frequency_text']),
            'save_text' => array('Save text',$widget_setting_data['save_text']),
            'delivery_every_text' => array('Delivery every text',$widget_setting_data['delivery_every_text']),
            'pre_pay_for_text' => array('Pre-Pay for text',$widget_setting_data['pre_pay_for_text']),
            'each_text' => array('Each text',$widget_setting_data['each_text']),
            'description_text' => array('Description text',$widget_setting_data['description_text']),
            'one_time_purchase_description' => array('One Time Purchase Description',$widget_setting_data['one_time_purchase_description']),
            'subscription_detail_text' => array('Subscription detail text',$widget_setting_data['subscription_detail_text']),
            'additional_subscription_detail' => array('Additional subscription detail',$widget_setting_data['additional_subscription_detail']),
            'total_payable_amount_text' => array('Total payable amount text',$widget_setting_data['total_payable_amount_text']),
            'total_saved_discount_text' => array('Total saved discount text',$widget_setting_data['total_saved_discount_text']),
            'border_style' => array('Border style',$widget_setting_data['border_style']),
            'on_first_text' => array('On first text',$widget_setting_data['on_first_text']),
            'orders_text' => array('Order(s) text',$widget_setting_data['orders_text']),
            'then_text' => array('Then text',$widget_setting_data['then_text']),
            'text_color' => array('Text color',$widget_setting_data['text_color']),
            'price_color' => array('Price color',$widget_setting_data['price_color']),
            'discounted_price_color' => array('Discounted Price color',$widget_setting_data['discounted_price_color']),
            'discount_label_color' => array('Discount label color',$widget_setting_data['discount_label_color']),
            // 'active_otpion_circle_color' => array('Active option circle color', $widget_setting_data['active_otpion_circle_color']),
            'border_color' => array('Border color',$widget_setting_data['border_color']),
            'widget_bg_color' => array('Widget Background Color',$widget_setting_data['widget_bg_color']),
            // 'active_option_box_bg_color' => array('Active Option background color',$widget_setting_data['active_option_box_bg_color']),
            'discount_bg_color' => array('Discount Label background color',$widget_setting_data['discount_bg_color']),
            'options_bg_color1' => array('Active Option background color', $widget_setting_data['options_bg_color1']),
            'options_bg_color2' => array('Active Option background color', $widget_setting_data['options_bg_color2'])
        );
      $widget_arrangement = explode(',',$widget_setting_data['widget_arrange_label']);
      $selected_widget_template = $widget_setting_data['widget_template'];
      $default_selected_option = $widget_setting_data['default_selected_option'];
    }

    $color_fields_array = array('text_color','price_color','discounted_price_color','discount_label_color','active_otpion_circle_color','border_color','widget_bg_color','discount_bg_color','options_bg_color1','options_bg_color2');
    // echo '<pre>';
    // print_r($widget_settings_array);
    // die;

?>
<div class="Polaris-Layout">
<?php
   include("../navigation.php");
?>
<div class="Polaris-Frame__Content">
    <div class="Polaris-Page sd-preorder-page-width">
        <div class="Polaris-Layout__Section sd-email_template_list-page">
            <div class="Polaris-Page-Header Polaris-Page-Header--isSingleRow Polaris-Page-Header--mobileView Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
                <div class="search-form">
                    <div id="subscription-list-search">
                        <div class="Polaris-Page-Header__BreadcrumbWrapper">
                        <nav role="navigation">
                            <a class="Polaris-Breadcrumbs__Breadcrumb" href="setting.php?shop=<?php echo $mainobj->store; ?>">
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
                        <div class="formbox">
                            <label>Widget Settings</label>
                        </div>
                        <button class="Polaris-Button Polaris-Button--primary sd_select_setting sd_button" type="button" data-popup="email_configuration" id="save_widget_setting"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Save</span></span></button>
                    </div>
                </div>
            </div>
    <div style="height:200px">
        <div class="Polaris-Card">
            <div class="Polaris-Card__Section">
                <div class="Polaris-Stack Polaris-Stack--vertical">
                    <div class="Polaris-Stack__Item widget_setting_type">
                        <span class="Polaris-Button__Text">Select Widget Template</span>
                    </div>
                    <div class="Polaris-Stack__Item widget_setting_data">
                        <div class="container Polaris-Collapsible Polaris-Collapsible--expandOnPrint" data-id="arrange_widget_setting" style="display:flex;">
                            <div class="Polaris-Card select_widget_template <?php if($widget_setting_data['widget_template'] == '1'){ echo 'selected'; } ?>" data-id="1">
                                <h2>Template 1</h2>
                                <img src="<?php echo $mainobj->image_folder?>subscription_template_1.png" height="150" width="250">
                            </div>
                            <div class="Polaris-Card select_widget_template <?php if($widget_setting_data['widget_template'] == '2'){ echo 'selected'; } ?>" data-id="2">
                                <h2>Template 2</h2>
                                <img src="<?php echo $mainobj->image_folder?>subscription_template_2.png" height="150" width="250">
                            </div>
                            <div class="Polaris-Card select_widget_template <?php if($widget_setting_data['widget_template'] == '3'){ echo 'selected'; } ?>" data-id="3">
                                <h2>Template 3</h2>
                                <img src="<?php echo $mainobj->image_folder?>subscription_template_3.png" height="150" width="250">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="Polaris-Card sd_default_label" style="display:<?php if($selected_widget_template == '1' || $selected_widget_template == '2'){ echo  'block'; }else { echo 'none'; }?>">
            <div class="Polaris-Card__Section">
                <div class="Polaris-Stack Polaris-Stack--vertical">
                    <div class="Polaris-Stack__Item widget_setting_type">
                        <!-- <button class="Polaris-Button" type="button" aria-controls="basic-collapsible" aria-expanded="true" widget-data="arrange_widget_setting">
                            <span class="Polaris-Button__Content"> -->
                        <span class="Polaris-Button__Text">Default Label Select</span>
                        <!-- </span>
                            </button> -->
                    </div>
                    <div class="Polaris-Stack__Item widget_setting_data">
                        <div class="container Polaris-Collapsible Polaris-Collapsible--expandOnPrint" data-id="arrange_widget_setting">
                            <div class="Polaris-Stack Polaris-Stack--vertical">
                                <label class="Polaris-Choice" for="one_time_purchase">
                                <span class="Polaris-Choice__Control">
                                <span class="Polaris-RadioButton">
                                <input id="one_time_purchase" name="default_option" type="radio" class="Polaris-RadioButton__Input" aria-describedby="disabledHelpText" <?php if($default_selected_option == '1'){ echo "checked"; } ?> value="1">
                                <span class="Polaris-RadioButton__Backdrop">
                                </span>
                                </span>
                                </span>
                                <span class="Polaris-Choice__Label">
                                <span class="Polaris-Text--root Polaris-Text--bodyMd Polaris-Text--regular">One Time Purchase</span>
                                </span>
                                </label>
                                <label class="Polaris-Choice" for="pay_per_delivery">
                                <span class="Polaris-Choice__Control">
                                <span class="Polaris-RadioButton">
                                <input id="pay_per_delivery" name="default_option" type="radio" class="Polaris-RadioButton__Input" aria-describedby="optionalHelpText" value="2" <?php if($default_selected_option == '2'){ echo "checked"; } ?>>
                                <span class="Polaris-RadioButton__Backdrop">
                                </span>
                                </span>
                                </span>
                                <span class="Polaris-Choice__Label">
                                <span class="Polaris-Text--root Polaris-Text--bodyMd Polaris-Text--regular">Pay Per Delivery</span>
                                </span>
                                </label>
                                <label class="Polaris-Choice" for="prepaid">
                                <span class="Polaris-Choice__Control">
                                <span class="Polaris-RadioButton">
                                <input id="prepaid" name="default_option" type="radio" class="Polaris-RadioButton__Input" aria-describedby="optionalHelpText" value="3" <?php if($default_selected_option == '3'){ echo "checked"; } ?>>
                                <span class="Polaris-RadioButton__Backdrop">
                                </span>
                                </span>
                                </span>
                                <span class="Polaris-Choice__Label">
                                <span class="Polaris-Text--root Polaris-Text--bodyMd Polaris-Text--regular">Prepaid</span>
                                </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="Polaris-Card sd_arrange_options" style="display:<?php if($selected_widget_template == '1'){ echo  'block'; }else { echo 'none'; }?>">
            <div class="Polaris-Card__Section">
                <div class="Polaris-Stack Polaris-Stack--vertical">
                    <div class="Polaris-Stack__Item widget_setting_type">
                        <!-- <button class="Polaris-Button" type="button" aria-controls="basic-collapsible" aria-expanded="true" widget-data="arrange_widget_setting">
                            <span class="Polaris-Button__Content"> -->
                        <span class="Polaris-Button__Text">Rearrange Widget Labels</span>
                        <!-- </span>
                            </button> -->
                    </div>
                    <div class="Polaris-Stack__Item widget_setting_data">
                        <div class="container Polaris-Collapsible Polaris-Collapsible--expandOnPrint" data-id="arrange_widget_setting">
                            <ul tabindex="0" role="listbox" class="Polaris-Listbox reorderable-list" aria-label="Basic Listbox example" aria-busy="false" id="PolarisListbox1" aria-activedescendant="PolarisListboxOption1">
                                <li class="Polaris-Listbox-Option reorderable-list__item dropzone" draggable="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" height="20">
                                        <path d="M7 2a2 2 0 10.001 4.001A2 2 0 007 2zm0 6a2 2 0 10.001 4.001A2 2 0 007 8zm0 6a2 2 0 10.001 4.001A2 2 0 007 14zm6-8a2 2 0 10-.001-4.001A2 2 0 0013 6zm0 2a2 2 0 10.001 4.001A2 2 0 0013 8zm0 6a2 2 0 10.001 4.001A2 2 0 0013 14z" fill="#5C5F62"/>
                                    </svg>
                                    <?php echo $widget_arrangement[0]; ?>
                                </li>
                                <li class="Polaris-Listbox-Option reorderable-list__item dropzone" draggable="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" height="20">
                                        <path d="M7 2a2 2 0 10.001 4.001A2 2 0 007 2zm0 6a2 2 0 10.001 4.001A2 2 0 007 8zm0 6a2 2 0 10.001 4.001A2 2 0 007 14zm6-8a2 2 0 10-.001-4.001A2 2 0 0013 6zm0 2a2 2 0 10.001 4.001A2 2 0 0013 8zm0 6a2 2 0 10.001 4.001A2 2 0 0013 14z" fill="#5C5F62"/>
                                    </svg>
                                    <?php echo $widget_arrangement[1]; ?>
                                </li>
                                <li class="Polaris-Listbox-Option reorderable-list__item dropzone" draggable="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" height="20">
                                        <path d="M7 2a2 2 0 10.001 4.001A2 2 0 007 2zm0 6a2 2 0 10.001 4.001A2 2 0 007 8zm0 6a2 2 0 10.001 4.001A2 2 0 007 14zm6-8a2 2 0 10-.001-4.001A2 2 0 0013 6zm0 2a2 2 0 10.001 4.001A2 2 0 0013 8zm0 6a2 2 0 10.001 4.001A2 2 0 0013 14z" fill="#5C5F62"/>
                                    </svg>
                                    <?php echo $widget_arrangement[2]; ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- widget labels -->
        <!-- </div>
            </div> -->
        <!-- Widget labels ends here  -->
        <!-- </div> -->
        <!-- layout  start-->
        <div class="Polaris-Card">
            <div class="Polaris-Card__Section">
                <div class="">
                    <div class="Polaris-Layout">
                        <div class="Polaris-Layout__Section">
                            <div class="Polaris-FormLayout">
                              <form method="post" id="subscription_label_form">
                                  <div class="Polaris-LegacyCard__Header">
                                      <h2 class="Polaris-Text--root Polaris-Text--headingMd">Widget Labels</h2>
                                  </div>
                                  <div class="Polaris-LegacyCard__Section">
                                        <?php
                                            $i=0;
                                            foreach($widget_settings_array as $key=>$value){
                                            $add_color_class = '';
                                            $split_string = explode('_',$key);
                                            if (in_array("bg", $split_string)){
                                               $data_style = 'background';
                                            }else if($key == 'border_color'){
                                                $data_style = 'border-color';
                                            }else if($key == 'border_style'){
                                                $data_style = 'border-style';
                                                $add_color_class = 'sd_add_style';
                                            }else{
                                                $data_style = 'color';
                                            }

                                            if(in_array($key, $color_fields_array)){
                                                $add_color_class = 'jscolor sd_add_style';
                                            }
                                            // if(($key == 'prepaid_options_text' && $selected_widget_template == '2') || ($key == 'discounted_price_color' && $selected_widget_template == '2') || ($key == 'ppd_options_text' && $selected_widget_template == '2') || ($key == 'prepaid_options_text' && $selected_widget_template == '3') || ($key == 'prepaid_options_text' && $selected_widget_template == '3') || ($key == 'ppd_options_text' && $selected_widget_template == '3') || ($key == 'subscription_options_text' && $selected_widget_template == '1') || ($key == 'subscription_options_text' && $selected_widget_template == '3') || ($key == 'options_bg_color2')){
                                            //     // echo $key.'<br>';
                                            //     $add_color_class =  $add_color_class.' display-hide-label';
                                            // }
                                            if($key == 'options_bg_color2'){
                                                continue;
                                            }
                                            if($i%2 == 0){
                                        ?>
                                      <div class="Polaris-FormLayout__Items">
                                          <?php } ?>
                                            <div class="Polaris-FormLayout__Item">
                                                <div class="">
                                                    <div class="Polaris-Labelled__LabelWrapper">
                                                        <div class="Polaris-Label">
                                                            <label id="PolarisTextField1Label" for="PolarisTextField1" class="Polaris-Label__Text"><?php echo $value[0]; ?></label>
                                                        </div>
                                                    </div>
                                                    <?php if($key == 'border_style'){?>
                                                           <div class="Polaris-Select">
                                                            <select id="PolarisSelect1" class="Polaris-Select__Input sd_default_template_text_fields <?php echo $add_color_class; ?>" aria-invalid="false" name="<?php echo $key; ?>" data-style="<?php echo $data_style; ?>" data-id="<?php echo $key; ?>_preview">
                                                                <option value="solid" <?php if($value[1] == 'solid'){ echo 'selected'; } ?>>Solid</option>
                                                                <option value="dashed" <?php if($value[1] == 'dashed'){ echo 'selected'; } ?>>Dashed</option>
                                                                <option value="dotted" <?php if($value[1] == 'dotted'){ echo 'selected'; } ?>>Dotted</option>
                                                                <option value="double" <?php if($value[1] == 'double'){ echo 'selected'; } ?>>Double</option>
                                                                <option value="groove" <?php if($value[1] == 'groove'){ echo 'selected'; } ?>>Groove</option>
                                                                <option value="hidden" <?php if($value[1] == 'hidden'){ echo 'selected'; } ?>>Hidden</option>
                                                                <option value="inherit" <?php if($value[1] == 'inherit'){ echo 'selected'; } ?>>Inherit</option>
                                                                <option value="initial" <?php if($value[1] == 'initial'){ echo 'selected'; } ?>>Initial</option>
                                                                <option value="inset" <?php if($value[1] == 'inset'){ echo 'selected'; } ?>>Inset</option>
                                                                <option value="none" <?php if($value[1] == 'none'){ echo 'selected'; } ?>>None</option>
                                                                <option value="outset" <?php if($value[1] == 'outset'){ echo 'selected'; } ?>>Outset</option>
                                                                <option value="revert" <?php if($value[1] == 'revert'){ echo 'selected'; } ?>>Revert</option>
                                                                <option value="ridge" <?php if($value[1] == 'ridge'){ echo 'selected'; } ?>>Ridge</option>
                                                                <option value="unset" <?php if($value[1] == 'unset'){ echo 'selected'; } ?>>Unset</option>
                                                            </select>
                                                            <div class="Polaris-Select__Content" aria-hidden="true">
                                                            <span class="Polaris-Select__SelectedOption"><?php echo $value[1]; ?></span>
                                                            <span class="Polaris-Select__Icon">
                                                                <span class="Polaris-Icon">
                                                                <span class="Polaris-Text--root Polaris-Text--visuallyHidden">
                                                                </span>
                                                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                    <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z">
                                                                    </path>
                                                                </svg>
                                                                </span>
                                                            </span>
                                                            </div>
                                                            <div class="Polaris-Select__Backdrop">
                                                            </div>
                                                        </div>
                                                    <?php }else{ if($key == 'options_bg_color1'){ ?>
                                                        <div class="option_background_color">
                                                            <div class="Polaris-Connected">
                                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                    <div class="Polaris-TextField">
                                                                        <input id="PolarisTextField1" name="options_bg_color1" data-style="<?php echo $data_style; ?>" data-id="options_bg_color1_preview" autocomplete="off" class="Polaris-TextField__Input sd_default_template_text_fields <?php echo $add_color_class; ?>" type="text" aria-labelledby="PolarisTextField1Label" aria-invalid="false" value="<?php echo $widget_settings_array['options_bg_color1'][1]; ?>">
                                                                        <div class="Polaris-TextField__Backdrop">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="Polaris-Connected">
                                                                <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                    <div class="Polaris-TextField">
                                                                        <input id="PolarisTextField1" name="options_bg_color2" data-style="<?php echo $data_style; ?>" data-id="options_bg_color2_preview" autocomplete="off" class="Polaris-TextField__Input sd_default_template_text_fields <?php echo $add_color_class; ?>" type="text" aria-labelledby="PolarisTextField1Label" aria-invalid="false" value="<?php echo $widget_settings_array['options_bg_color2'][1]; ?>">
                                                                        <div class="Polaris-TextField__Backdrop">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php }else{ ?>
                                                        <div class="Polaris-Connected">
                                                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                                <div class="Polaris-TextField">
                                                                    <input id="PolarisTextField1" name="<?php echo $key; ?>" data-style="<?php echo $data_style; ?>" data-id="<?php echo $key; ?>_preview" autocomplete="off" class="Polaris-TextField__Input sd_default_template_text_fields <?php echo $add_color_class; ?>" type="text" aria-labelledby="PolarisTextField1Label" aria-invalid="false" value="<?php echo $value[1]; ?>">
                                                                    <div class="Polaris-TextField__Backdrop">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } }?>
                                                </div>
                                            </div>
                                          <?php if($i%2 != 0){ ?>
                                      </div>
                                      <?php }  $i++; }?>
                                  </div>
                              </form>
                            </div>
                        </div>
                    </div>

                    <div class="Polaris-Layout__Section Polaris-Layout__Section--secondary"  id="subscription_widget">
                        <div class="Polaris-LegacyCard">
                            <div class="Polaris-LegacyCard__Header">
                                <h2 class="Polaris-Text--root Polaris-Text--headingMd">Preview</h2>
                            </div>
                            <div class="Polaris-LegacyCard__Section">
                                <div class="sd_widget_mainwrapper widget_bg_color_preview border_color_preview border_style_preview widget_template_1 <?php if($selected_widget_template != '1') { echo 'display-hide-label'; } ?>" style="border:<?php echo $widget_settings_array['border_color'][1];?> 1px solid;background-color:<?php echo $widget_settings_array['widget_bg_color'][1];?>">
                                    <div class="sd_widget_title purchase_options_text_preview"><?php echo $widget_settings_array['purchase_options_text'][1];?></div>
                                    <div class="sd_subscription_wrapper widget_bg_color_preview" style="background-color:<?php echo $widget_settings_array['widget_bg_color'][1];?>">
                                        <div id="sd_one_time_purchase_wrapper" class="sd_subscription_wrapper_option" >
                                            <div class="sd_subscription_label_wrapper">
                                                <div class="radio_discount_wrapper">
                                                    <label for="sd_subscription_frequency_plans_radio" class="sd_radio_label one_time_purchase_text_preview">
                                                    <?php echo $widget_settings_array['one_time_purchase_text'][1];?>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="sd_pay_per_delivery_wrapper" class="sd_subscription_wrapper_option ">
                                           <div id="sd_pay_per_delivery_wrapper" class="sd_subscription_wrapper_option" >
                                            <div class="sd_subscription_label_wrapper text_color_preview active_option_bg_color"  style="background:linear-gradient(to right, <?php echo $widget_settings_array['options_bg_color1'][1];?> , <?php echo $widget_settings_array['options_bg_color2'][1];?>);color:<?php echo $widget_settings_array['text_color'][1];?>">
                                                <div class="radio_discount_wrapper">
                                                    <label for="sd_subscription_frequency_plans_radio" class="sd_radio_label">
                                                    <span class="ppd_options_text_preview"><?php echo $widget_settings_array['ppd_options_text'][1];?>
                                                </div>
                                                <div class="selected_price_wrapper">
                                                    <span id="ppd_discount_price" class="selected_price discounted_price_color_preview" style="color:<?php echo $widget_settings_array['discounted_price_color'][1]; ?>">$80</span>
                                                    <span id="ppd_cut_original_price" class="cut_original_price price_color_preview" style="color:<?php echo $widget_settings_array['price_color'][1]; ?>;text-decoration:line-through;">$100</span><span class="each_text_preview"> <?php echo $widget_settings_array['each_text'][1];?></span>
                                                </div>
                                            </div>
                                            <div id="sd_subscriptionOptionsWrapper" class="sd_frequency_plan_option_wrapper  display-none">
                                                <label for="sd_selling_plan_title" class="sd_select_label delivery_every_text_preview"><?php echo $widget_settings_array['delivery_every_text'][1]; ?></label>
                                                <p style="border:#000 1px solid;">1 <span class="day_frequency_text_preview"><?php echo $widget_settings_array['day_frequency_text'][1]; ?></span>(<span class="save_text_preview"><?php echo $widget_settings_array['save_text'][1];?></span> 20% <span class="on_first_text_preview"><?php echo $widget_settings_array['on_first_text'][1]; ?></span> <span class="orders_text_preview"><?php echo $widget_settings_array['orders_text'][1]; ?></span>, <span class="then_text_preview"><?php echo $widget_settings_array['then_text'][1]; ?></span> 10%)</p>
                                            </div>
                                        </div>
                                        <div id="sd_prepaid_delivery_wrapper" class="sd_subscription_wrapper_option">
                                            <div class="sd_subscription_label_wrapper">
                                                <div class="radio_discount_wrapper">
                                                    <label for="sd_subscription_frequency_plans_radio" class="sd_radio_label">
                                                    <span class="prepaid_options_text_preview"><?php echo $widget_settings_array['prepaid_options_text'][1]; ?></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="sd_totalPayableAmount"><span class="total_payable_amount_text_preview"><?php echo $widget_settings_array['total_payable_amount_text'][1]; ?> </span> $80.00 <span class="sd_discount_ribbon discount_label_color_preview discount_bg_color_preview" style="background-color:<?php echo $widget_settings_array['discount_bg_color'][1]; ?>; color:<?php echo $widget_settings_array['discount_label_color'][1]; ?>;display: inline-block;"><span class="total_saved_discount_text_preview"> <?php echo $widget_settings_array['total_saved_discount_text'][1]; ?></span> 20%</span></div>
                                    </div>
                                </div>
                                </div>
                            <div class="sd_widget_mainwrapper widget_bg_color_preview widget_template_2 <?php if($selected_widget_template != '2') { echo 'display-hide-label'; }?>" style="background-color:<?php echo $widget_settings_array['widget_bg_color'][1];?>;">
                                <div class="sd_widget_title purchase_options_text_preview"><?php echo $widget_settings_array['purchase_options_text'][1];?></div>
                                <div class="template_2" style="margin-top : 1px; margin-bottom : 10px;">
                                <div class="sd_subscription_wrapper"><div id="sd_subscription_frequency_plan_wrapper" class="sd_subscription_wrapper_option">
                                            <div class="sd_subscription_label_wrapper">
                                                <div class="sd_label_discount_wrapper border_color_preview border_style_preview" style="border: <?php echo $widget_settings_array['border_color'][1]; ?> 1px <?php echo $widget_settings_array['border_style'][1]; ?>;">
                                                    <div class="radio_discount_wrapper">
                                                        <input type="radio" class="" id="sd_subscription_frequency_plan_radio" name="purchase_option" value="Subscribe and save">
                                                        <label for="sd_subscription_frequency_plan_radio" id="label_one_time_purchase" class="one_time_purchase_text_preview">
                                                           <?php echo $widget_settings_array['one_time_purchase_text'][1];?>
                                                        </label>
                                                    </div>
                                                    <div class="selected_price_wrapper all_plans_selected_price">$100.00</div>
                                                </div>
                                            </div>
                                        </div>
                                    <div id="sd_all_plans_delivery_wrapper" class="sd_subscription_wrapper_option  activemethod">
                                    <div class="sd_subscription_label_wrapper">
                                        <div class="sd_label_discount_wrapper border_color_preview border_style_preview text_color_preview active_option_bg_color" style="border: <?php echo $widget_settings_array['border_color'][1]; ?> 1px <?php echo $widget_settings_array['border_style'][1]; ?>;background:linear-gradient(to right, <?php echo $widget_settings_array['options_bg_color1'][1];?> , <?php echo $widget_settings_array['options_bg_color2'][1];?>);color:<?php echo $widget_settings_array['text_color'][1];?>">
                                            <div class="radio_discount_wrapper">
                                                <input type="radio" class="" id="sd_subscription_frequency_plan_radio" name="purchase_option" value="Subscribe and save" checked="">
                                                <label for="sd_all_delivery_purchase_radio " id="label_all_plans"><span class="subscription_options_text_preview"><?php echo $widget_settings_array['subscription_options_text'][1];?></span> + <span class="save_text_preview"><?php echo $widget_settings_array['save_text'][1];?></span> 20%</label>
                                            </div>
                                            <div class="selected_price_wrapper all_plans_selected_price" id="all_plans_selected_price"><span id="all_plans_discount_price" class="selected_price price_color_preview" >$80.00</span></div>
                                        </div>
                                    </div>
                                    <div id="sd_subscriptionAllOptionsWrapper" class="sd_frequency_plan_option_wrapper"><p style="border-bottom:1px #000 solid"><span class="delivery_every_text_preview"><?php echo $widget_settings_array['delivery_every_text'][1];?></span> 1 <span class="day_frequency_text_preview"><?php echo $widget_settings_array['day_frequency_text'][1]; ?></span></p></div>
                                </div></div><!-- sd_subscription_wrapper -->
                                </div>
                                <div id="sd_totalPayableAmount">
                                    <span class="total_payable_amount_text_preview"><?php echo $widget_settings_array['total_payable_amount_text'][1]; ?> </span> $80.00 <span class="sd_discount_ribbon discount_label_color_preview discount_bg_color_preview" style="background-color:<?php echo $widget_settings_array['discount_bg_color'][1]; ?>; color:<?php echo $widget_settings_array['discount_label_color'][1]; ?>;display: inline-block;"><span class="total_saved_discount_text_preview"> <?php echo $widget_settings_array['total_saved_discount_text'][1]; ?></span> 100%</span>
                                </div>
                            </div></div>

                            <div class="sd_widget_mainwrapper widget_template_3 widget_bg_color_preview <?php if($selected_widget_template == '1' || $selected_widget_template == '2') { echo 'display-hide-label'; }?>" style="background-color:<?php echo $widget_settings_array['widget_bg_color'][1];?>">
                                    <div class="sd_widget_title purchase_options_text_preview"><?php echo $widget_settings_array['purchase_options_text'][1];?></div>
                                    <div class="widget_template_3" style="margin-top : 1px; margin-bottom : 1px;">
                                        <div class="sd_subscription_wrapper"><div class="sd_subscription_wrapper" id="sd_all_plans_delivery_wrapper" style="">
                                        <div class="innercards sd_subscription_wrapper_option">
                                    <div class="First">
                                        <label for="sd_subscription_frequency_plan_radio" id="label_one_time_purchase" class="sd_radio_label one_time_purchase_text_preview">
                                           <?php echo $widget_settings_array['one_time_purchase_text'][1];?>
                                        </label>
                                    </div>
                                <div class="offer_peice">
                                    <h2><div class="selected_price_wrapper">$100.00 USD</div></h2>
                                </div>
                               </div><div class="innercards sd_subscription_wrapper_option active_option_bg_color text_color_preview" style="background:linear-gradient(to right, <?php echo $widget_settings_array['options_bg_color1'][1];?> , <?php echo $widget_settings_array['options_bg_color2'][1];?>);color:<?php echo $widget_settings_array['text_color'][1];?>;">
                                    <div class="First">
                                        <label id="sd_all_plans_option" class="sd_radio_label"><span class="delivery_every_text_preview"><?php echo $widget_settings_array['delivery_every_text'][1];?></span> 1 <span class="day_frequency_text_preview"><?php echo $widget_settings_array['day_frequency_text'][1];?></label>
                                    </div>
                                    <div class="offer_peice price_color_preview" style="color:<?php echo $widget_settings_array['price_color'][1]; ?>">
                                        <h2>$80 USD</h2>
                                    </div>
                                </div><div class="innercards sd_subscription_wrapper_option">
                                    <div class="First">
                                        <label for="purchase_option_3714154749" id="sd_all_plans_option" class="sd_radio_label"><span class="pre_pay_for_text_preview"><?php echo $widget_settings_array['pre_pay_for_text'][1]; ?></span> 10 <span class="week_frequency_text_preview"> <?php echo $widget_settings_array['week_frequency_text'][1]; ?> </span>, <span class="delivery_every_text_preview"><?php echo $widget_settings_array['delivery_every_text'][1]; ?> </span> 1 <span class="week_frequency_text_preview"><?php echo $widget_settings_array['week_frequency_text'][1]; ?></span> + <span class="save_text_preview"><?php echo $widget_settings_array['save_text'][1]; ?></span> 10%</label>
                                    </div>
                                    <div class="offer_peice"><h2>$800 USD</h2></div>
                                </div>
                                <div id="sd_totalPayableAmount">
                                    <span class="total_payable_amount_text_preview"><?php echo $widget_settings_array['total_payable_amount_text'][1]; ?> </span> $80.00 <span class="sd_discount_ribbon discount_label_color_preview discount_bg_color_preview" style="background-color:<?php echo $widget_settings_array['discount_bg_color'][1]; ?>; color:<?php echo $widget_settings_array['discount_label_color'][1]; ?>;display: inline-block;"><span class="total_saved_discount_text_preview"> <?php echo $widget_settings_array['total_saved_discount_text'][1]; ?></span> 100%</span>
                                </div>
                            </div></div><!-- sd_subscription_wrapper -->
                         </div></div>

                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
    <!-- layout  end-->
</div>
<?php
   include("../footer.php");
?>
<script src="<?php echo $mainobj->SHOPIFY_DOMAIN_URL ;?>/application/assets/js/jscolor.js"></script>
<script>
jQuery("body").on("click", "#save_widget_setting", async function() {
    var widget_setting_data = form_serializeObject("subscription_label_form");
    console.log(widget_setting_data);
    var arrange_widget = jQuery(".widget_setting_data .dropzone").map(function(){
      return $.trim($(this).text());
    }).get().join(',');
    var select_template = jQuery('.select_widget_template.selected').attr('data-id');
    var selected_default_option = document.querySelector('input[name="default_option"]:checked').value;
    let whereconditionvalues = {
        "store_id": store_id
    }
    widget_setting_data['widget_arrange_label'] = arrange_widget;
    widget_setting_data['widget_template'] = select_template;
    widget_setting_data['default_selected_option'] = selected_default_option;
    widget_setting_data['store_id'] = store_id;
    let ajaxParameters = {
      method: "POST",
      dataValues: {
        table: 'widget_settings',
        data_values: widget_setting_data,
        wherecondition: whereconditionvalues,
        wheremode: 'and',
        action: "insertupdateajax"
      }
    };
    try {
        let result = await AjaxCall(ajaxParameters);
        if (result['status'] == true) {
            displayMessage(result['message'], 'success');
        }
    } catch (e) {
        displayMessage(e, 'error');
    }
});

jQuery("body").on("click", ".select_widget_template", function() {
    jQuery('.select_widget_template').removeClass('selected');
    jQuery(this).addClass('selected');
    var select_template = jQuery('.select_widget_template.selected').attr('data-id');
    console.log(select_template);
    if(select_template == '1'){
        jQuery('.sd_arrange_options, .sd_default_label').show();
    }else if(select_template == '2'){
       jQuery('.sd_default_label').show();
       jQuery('.sd_arrange_options').hide();
    }else{
        jQuery('.sd_arrange_options, .sd_default_label').hide();
    }
    jQuery('.sd_widget_mainwrapper').addClass('display-hide-label');
    jQuery('.sd_widget_mainwrapper.widget_template_'+select_template).removeClass('display-hide-label');
});


let elementBeingDragged = null;
let draggables = document.querySelectorAll('.reorderable-list__item');
let dropzones = document.querySelectorAll('.dropzone');

/* Item-Being-Dragged Handlers */
let startDrag = (event) => {
  event.dataTransfer.effectAllowed = 'move';
  event.dataTransfer.setData('text/html', event.target.innerHTML);
  elementBeingDragged = event.target;
};

let stopDrag = (event) => {
  event.preventDefault();
  elementBeingDragged = null;
};

/* Dropzone Handlers */
let dragInto = (event) => {
  event.preventDefault();
  event.target.classList.add('-dropzone');
};

let dragOver = (event) => {
  event.preventDefault();
  event.dataTransfer.dropEffect = 'move';
}

let dragOut = (event) => {
  event.preventDefault();
  event.target.classList.remove('-dropzone');
};

let drop = (event) => {
  event.preventDefault();
  event.stopPropagation();
  event.target.classList.remove('-dropzone');
  elementBeingDragged.innerHTML = event.target.innerHTML;
  event.target.innerHTML = event.dataTransfer.getData('text/html');
};

Array.prototype.forEach.call(dropzones, (dropzone => {
  dropzone.addEventListener('dragenter', dragInto);
  dropzone.addEventListener('dragover', dragOver);
  dropzone.addEventListener('dragleave', dragOut);
  dropzone.addEventListener('drop', drop);
}));

Array.prototype.forEach.call(draggables, (item => {
  item.addEventListener('dragstart', startDrag);
  item.addEventListener('dragend', stopDrag);
}));
</script>