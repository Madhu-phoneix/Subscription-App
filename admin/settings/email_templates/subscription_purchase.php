<?php
   include("../../header.php");
?>
<div class="Polaris-Layout">
<?php
  include("../../navigation.php");
  $where_condition = array(
    'store_id' => $mainobj->store_id
  );
  $template_data = $mainobj->single_row_value('subscription_purchase_template','All',$where_condition,'and','');
  $whereStoreCondition = array(
    'store_id' => $mainobj->store_id,
    'status' => '1'
  );
  $fields = array('plan_id','planName','price');
  $active_plan_data = $mainobj->single_row_value('storeInstallOffers',$fields,$whereStoreCondition,'and','');
  $plan_name = $active_plan_data['planName'];

$json_data = json_encode($template_data);
    $template_type_array = array(
       'template_type' => 'subscription_purchase_template'
    );
    if(!empty($template_data)){
        $show_currency = $template_data['show_currency'];
        $show_shipping_address = $template_data['show_shipping_address'];
        $show_billing_address = $template_data['show_billing_address'];
        $show_line_items = $template_data['show_line_items'];
        $show_payment_method = $template_data['show_payment_method'];
        $custom_template = $template_data['custom_template'];
        $show_order_number = $template_data['show_order_number'];
        $email_template_settings_array=array(
            'subject' => array('Subject',utf8_encode($template_data['subject'])),
            // 'from_email' => array('From Email',$template_data['from_email']),
            'ccc_email' => array('CCC Email',$template_data['ccc_email']),
            'bcc_email' => array('BCC Email',$template_data['bcc_email']),
            'reply_to' => array('Reply To',$template_data['reply_to']),
        );

        $default_template_array = array(
            'logo' => array('Logo',$template_data['logo']),
            'logo_height' => array('Logo Height',$template_data['logo_height']),
            'logo_width' => array('Logo Width',$template_data['logo_width']),
            'logo_alignment' => array('Logo Alignment',$template_data['logo_alignment']),
            'thanks_img' => array('Thanks Image',$template_data['thanks_img']),
            'thanks_img_height' => array('Thanks Image Height',$template_data['thanks_img_height']),
            'thanks_img_width' => array('Thanks Image Width',$template_data['thanks_img_width']),
            'thanks_img_alignment' => array('Thanks Image Alignment',$template_data['thanks_img_alignment']),
            'heading_text' => array('Heading Text',$template_data['heading_text']),
            'heading_text_color' => array('Heading Text Color',$template_data['heading_text_color']),
            'content_text' => array('Content Text',$template_data['content_text']),
            'text_color' => array('Text Color',$template_data['text_color']),
            'manage_subscription_txt' => array('Manage Subscription Text',$template_data['manage_subscription_txt']),
            'manage_subscription_url' => array('Manage Subscription Url',$template_data['manage_subscription_url']),
            'manage_button_text_color' => array('Manage Subscription Text Color',$template_data['manage_button_text_color']),
            'manage_button_background' => array('Manage Subscription Button Background',$template_data['manage_button_background']),
            'shipping_address_text' => array('Shipping Address Text',$template_data['shipping_address_text']),
            'shipping_address' => array('Shipping Address',$template_data['shipping_address']),
            'billing_address_text' => array('Billing Address Text',$template_data['billing_address_text']),
            'billing_address' => array('Billing Address',$template_data['billing_address']),
            'delivery_every_text' => array('Delivery every text',$template_data['delivery_every_text']),
            'next_charge_date_text' => array('Next billing date text',$template_data['next_charge_date_text']),
            'order_number_text' => array('Order number text',$template_data['order_number_text']),
            // 'next_renewal_date_text' => array('Next Billing Date Text',$template_data['next_renewal_date_text']),
            'payment_method_text' => array('Payment Method Text',$template_data['payment_method_text']),
            'ending_in_text' => array('Ending in Text',$template_data['ending_in_text']),
            // 'qty_text' => array('Quantity Text',$template_data['qty_text']),
            'footer_text' => array('Footer Text',$template_data['footer_text']),
            // 'show_currency' => array('Show Currency', $template_data['show_currency']),
            // 'show_shipping_address' => array('Show shipping address', $template_data['show_shipping_address']),
            // 'show_billing_address' => array('Show billing address', $template_data['show_billing_address']),
            // 'show_payment_method' => array('Show payment method', $template_data['show_payment_method']),
        );

    }else{
        $show_currency = '1';
        $show_shipping_address = '1';
        $show_billing_address = '1';
        $show_line_items = '1';
        $show_payment_method = '1';
        $custom_template = '';
        $show_order_number = '1';
        $show_line_items = '1';
        $email_template_settings_array=array(
            'subject' => array('Subject',utf8_encode('Your recurring order purchase confirmation')),
            // 'from_email' => array('From Email',$template_data['from_email']),
            'ccc_email' => array('CCC Email',''),
            'bcc_email' => array('BCC Email',''),
            'reply_to' => array('Reply To',''),
        );

        $default_template_array = array(
            'logo' => array('Logo',$mainobj->SHOPIFY_DOMAIN_URL.'/application/assets/images/logo.png'),
            'logo_height' => array('Logo Height','63'),
            'logo_width' => array('Logo Width','166'),
            'logo_alignment' => array('Logo Alignment','center'),
            'thanks_img' => array('Thanks Image',$mainobj->SHOPIFY_DOMAIN_URL.'/application/assets/images/thank_you.jpg'),
            'thanks_img_height' => array('Thanks Image Height','63'),
            'thanks_img_width' => array('Thanks Image Width','166'),
            'thanks_img_alignment' => array('Thanks Image Alignment','center'),
            'heading_text' => array('Heading Text','Welcome!'),
            'heading_text_color' => array('Heading Text Color',	'#495661'),
            'content_text' => array('Content Text','<h2 style="font-weight:normal;font-size:24px;margin:0 0 10px">Hi {{customer_name}}</h2><h2 style="font-weight:normal;font-size:24px;margin:0 0 10px">Thank you for your purchase!</h2> <p style="line-height:150%;font-size:16px;margin:0">We are getting your order ready to be shipped. We will notify you when it has been sent.</p>'),
            'text_color' => array('Text Color',	'#000000'),
            'manage_subscription_txt' => array('Manage Subscription Text','Manage Subscription'),
            'manage_subscription_url' => array('Manage Subscription Url',''),
            'manage_button_text_color' => array('Manage Subscription Text Color','#ffffff'),
            'manage_button_background' => array('Manage Subscription Button Background','#337ab7'),
            'shipping_address_text' => array('Shipping Address Text','Shipping address'),
            'shipping_address' => array('Shipping Address',	'<p>{{shipping_full_name}}</p><p>{{shipping_address1}}</p><p>{{shipping_city}},{{shipping_province_code}} - {{shipping_zip}}</p>'),
            'billing_address_text' => array('Billing Address Text',	'Billing address'),
            'billing_address' => array('Billing Address','<p>{{billing_full_name}}</p><p>{{billing_address1}}</p><p>{{billing_city}},{{billing_province_code}} - {{billing_zip}}</p>'),
            'delivery_every_text' => array('Delivery every text','Delivery every'),
            'next_charge_date_text' => array('Next billing date text',	'Next billing date'),
            'order_number_text' => array('Order number text','Order No.'),
            // 'next_renewal_date_text' => array('Next Billing Date Text',$template_data['next_renewal_date_text']),
            'payment_method_text' => array('Payment Method Text','Payment method'),
            'ending_in_text' => array('Ending in Text','Ending with'),
            // 'qty_text' => array('Quantity Text',$template_data['qty_text']),
            'footer_text' => array('Footer Text','<p style="line-height:150%;font-size:14px;margin:0">Thank You</p>'),
        );
    }
    $show_hide_fields = array(
        'show_currency' => array('Show Currency', $show_currency),
        'show_shipping_address' => array('Show shipping address', $show_shipping_address),
        'show_billing_address' => array('Show billing address', $show_billing_address),
        'show_payment_method' => array('Show payment method', $show_payment_method),
        'show_order_number' => array('show_order_number', $show_order_number),
        'show_line_items' => array('show_line_items', $show_line_items),
    );
    $subscription_purchase_variables_array=array(
        '{{order_number}}' => 'Order number',
        // '{{subscription_contract_id}}' => 'Subscription Id',
        '{{customer_email}}' => 'Customer email',
        '{{customer_name }}' => 'Customer name',
        '{{customer_id}}' => 'Customer Id',
        // '{{next_order_date}}' => 'Next billing date',
        '{{selling_plan_name}}' => 'Selling Plan name',
        "{{shipping_full_name}}" => 'Shipping Full name',
        "{{shipping_address1}}" => 'Shipping address1',
        "{{shipping_company}}" => 'Shipping company',
        "{{shipping_city}}" => 'Shipping city',
        "{{shipping_province}}" => 'Shipping Province',
        "{{shipping_province_code}}" => 'Shipping Province Code',
        "{{shipping_zip}}" => 'Shipping zip',
        "{{billing_full_name}}" => 'Billing full name',
        "{{billing_address1}}" => 'Billing full name',
        "{{billing_city}}" => 'Billing full name',
        "{{billing_province}}" => 'Billing Province',
        "{{billing_province_code}}" => 'Billing province code',
        "{{billing_zip}}" => 'Billing zip',
        "{{footer_text}}" => 'Email footer content',
        // "{{product_quantity}}" => 'Product quantity',
        "{{subscription_line_items}}" => 'Subscription line items',
        "{{card_brand}}" => 'Card Brand Name',
        "{{last_four_digits}}" => 'Last four digits',
        "{{card_expire_month}}" => 'Card expire in month',
        "{{card_expire_year}}" => 'Card expire in year',
        "{{shop_name}}" => 'Shop name',
        "{{shop_email}}" => 'Shop email',
        "{shop_domain}}" => 'Shop domain',
        "{{manage_subscription_link}}" => 'Manage Subscription Link',
        // "{{delivery_cycle}}" => 'Delivery Period of the subscription',
        // "{{billing_cycle}}" => 'Billing Period of the subscription',
        "{{email_subject}}" => 'Email Subject',
        "{{header_text_color}}" => 'Header text color',
        "{{text_color}}" => 'Text color',
        "{{footer_text_color}}" => 'Footer text color',
        "{{heading_text}}" => 'Heading text',
        "{{logo_image}}" => 'Logo image',
        "{{text_image_url}}" => 'Text image url',
        "{{manage_subscription_button_color}}" => 'Manage subscription button color',
        "{{manage_subscription_button_text}}"   => 'Manage subscription button text',
        "{{manage_subscription_button_text_color}}" => 'Manage subscription button text color',
        "{{shipping_address_text}}" => 'Shipping address text',
        "{{billing_address_text}}" => 'Billing address text',
        "{{payment_method_text}}" => 'Payment method text',
        "{{ending_in_text}}" => 'Ending in text',
        // "{{quantity_text}}" => 'Quantity text',
        "{{logo_height}}" => 'Logo height',
        "{{logo_width}}" => 'Logo width',
        "{{thanks_image}}" => 'Thanks Image',
        "{{thanks_image_height}}" => 'Thanks image height',
        "{{thanks_image_width}}" => 'Thanks image width',
        "{{logo_alignment}}" => 'Logo alignment',
        "{{thanks_image_alignment}}" => 'Thanks image alignment'
    );
?>

<div class="Polaris-Layout__Section sd-email-template-page">
    <div id="PolarisPortalsContainer" class="t-right top-banner-create-memberPlan">
    </div>
      <div class="Polaris-Page-Header Polaris-Page-Header--isSingleRow Polaris-Page-Header--mobileView Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
        <div class="search-form">
            <div id="subscription-list-search">
                <div class="formbox">
                    <label>Email Template Settings</label>
                </div>
                <button class="Polaris-Button Polaris-Button--primary sd_save_email_template sd_button" type="button" data-id="subscription_purchase"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Save</span></span></button>
            </div>
        </div>
      </div>
      <div class="Polaris-Page__Content">
          <form method="post" id="sd_subscription_purchase_template">
          <input type="hidden" value="<?php echo $mainobj->store_id; ?>" name="store_id">
         <div>
     <div class="Polaris-Card">
     <div class="Polaris-Page Polaris-Page--fullWidth">
    <div class="Polaris-Page__Content">
        <div class="Polaris-Layout">
            <div class="Polaris-Layout__Section Polaris-Layout__Section--secondary">
            <div class="Polaris-LegacyCard">
              <div>
                <div class="Polaris-Box" style="--pc-box-border-block-end:var(--p-border-divider);--pc-box-padding-inline-start-xs:var(--p-space-2);--pc-box-padding-inline-end-xs:var(--p-space-2)">
                <ul role="tablist" class="Polaris-Tabs">
                    <li class="Polaris-Tabs__TabContainer" role="presentation"><button group="subscription-edit-tabs" target-tab="edit_default_template" role="tab" type="button" class="sd_Tabs subscription-edit-tabs-title Polaris-Tabs__Tab Polaris-Tabs__Tab--selected"><span class="Polaris-Tabs__Title">Default</span></button></li>
                    <li class="Polaris-Tabs__TabContainer" role="presentation"><button group="subscription-edit-tabs" target-tab="edit_custom_template" active-planname ="<?php echo $plan_name; ?>" type="button" class="sd_Tabs subscription-edit-tabs-title Polaris-Tabs__Tab"><span class="Polaris-Tabs__Title">Custom</span></button></li>
                    <li class="Polaris-Tabs__TabContainer" role="presentation"><button group="subscription-edit-tabs" target-tab="edit_email_template" role="tab" type="button" class="sd_Tabs subscription-edit-tabs-title Polaris-Tabs__Tab"><span class="Polaris-Tabs__Title">Email Setting</span></button></li>
                    <li class="Polaris-Tabs__TabContainer" role="presentation"><button group="subscription-edit-tabs" target-tab="add_dynamic_variables" role="tab" type="button" class="sd_Tabs subscription-edit-tabs-title Polaris-Tabs__Tab"><span class="Polaris-Tabs__Title">Dynamic Variables</span></button></li>
                  </ul>
                </div>

                <div class="Polaris-Tabs__Panel subscription-edit-tabs Polaris-Tabs__Panel--hidden" id="add_dynamic_variables" role="tabpanel" aria-labelledby="all-customers-1" tabindex="-1">
                  <!-- all -->
                  <div class="Polaris-Card__Section">
                    <div class="Polaris-FormLayout">
                        <div class="Polaris-FormLayout__Item">
                            <div class="Polaris-LegacyCard__Header">
                                <h2 class="Polaris-Text--root Polaris-Text--headingMd">Dynamic Variables</h2>
                            </div>
                            <?php foreach($subscription_purchase_variables_array as $key=>$value){?>

                                <div class="Polaris-Labelled__LabelWrapper">
                                    <div class="Polaris-Label">
                                    <label id="PolarisTextField1Label" for="PolarisTextField1" class="Polaris-Label__Text"><?php echo $value; ?></label>
                                    </div>
                                    <div class="sd_dynamic_variables">
                                        <span class="sd_copy_element" data-value="<?php echo $key; ?>" onmouseover="show_title(this)" onmouseout="hide_title(this)">
                                            <button class="Polaris-Button Polaris-Button--plain" type="button">
                                                <span class="Polaris-Button__Content">
                                                <span class="Polaris-Button__Text"><?php echo $key; ?></span>
                                                </span>
                                            </button>
                                        </span>
                                        <span class="Polaris-PositionedOverlay display-hide-label">
                                            <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                              <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">copy <?php echo $value; ?></div>
                                            </div>
                                        </span>
                                        <span class="sd_copy_element" data-value="<?php echo $key; ?>" onmouseover="show_title(this)" onmouseout="hide_title(this)">
                                            <svg viewBox="0 0 20 20" height="17px" width="20px" class="Polaris-Icon__Svg_375hu" focusable="false" aria-hidden="true"><path d="M8 3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1s0 .997.996 1h2.004a2 2 0 0 1 2 2v7.586a2 2 0 0 1-.586 1.414l-2.414 2.414a2 2 0 0 1-1.414.586h-7.586a2 2 0 0 1-2-2v-10a2 2 0 0 1 2-2h2.004c.996-.003.996-1 .996-1zm5 3v1a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1v-1h-2v10h7v-2a1 1 0 0 1 1-1h2v-7h-2z"></path></svg>
                                        </span>
                                        <span class="Polaris-PositionedOverlay display-hide-label">
                                            <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                                              <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">copy <?php echo $value; ?></div>
                                            </div>
                                        </span>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                  </div>
                </div>

                <div class="Polaris-Tabs__Panel subscription-edit-tabs Polaris-Tabs__Panel--hidden" id="edit_email_template" role="tabpanel" aria-labelledby="all-customers-1" tabindex="-1">
                  <!-- all -->
                  <div class="Polaris-Card__Section">
                    <div class="Polaris-FormLayout">
                    <?php foreach($email_template_settings_array as $key=>$value){ ?>
                        <div class="Polaris-FormLayout__Item">
                        <div class="input_border">
                        <div class="Polaris-Labelled__LabelWrapper">
                            <div class="Polaris-Label">
                            <label id="PolarisTextField1Label" for="PolarisTextField1" class="Polaris-Label__Text">
                                <span class="Polaris-Text--root Polaris-Text--bodyMd Polaris-Text--regular"><?php echo $value[0]; ?></span>
                            </label>
                            </div>
                        </div>
                        <div class="Polaris-Connected">
                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                            <div class="Polaris-TextField Polaris-TextField--hasValue">
                                <input id="<?php echo $key; ?>" name="<?php echo $key; ?>" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField1Label" aria-invalid="false" value="<?php echo $value[1]; ?>">
                                <div class="Polaris-TextField__Backdrop">
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
                        <div id="PolarisPortalsContainer"></div>
                    </div>
                    <?php } ?>
                    </div>
                 </div>
                </div>
                <div class="Polaris-Tabs__Panel subscription-edit-tabs Polaris-Tabs__Panel--hidden" id="edit_custom_template" role="tabpanel" aria-labelledby="accepts-marketing-1" tabindex="-1">
                    <!-- <div id="myMonacoEditor" style="width:800px;height:600px;"></div> -->
                    <div class="Polaris-Card__Section">
                        <div class="Polaris-FormLayout">
                            <div class="Polaris-FormLayout__Item sd_custom_template">
                                <div class="Polaris-Connected Polaris-Connected--newDesignLanguage">
                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <textarea class="content customeditor" id="custom_template" name="custom_template"><?php if($template_data['custom_template'] != '') { echo  htmlspecialchars_decode($template_data['custom_template'], ENT_QUOTES); } ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="Polaris-Tabs__Panel subscription-edit-tabs" id="edit_default_template" role="tabpanel" aria-labelledby="accepts-marketing-1" tabindex="-1">
                    <div class="Polaris-Card__Section">
                    <div class="Polaris-FormLayout">
                    <?php foreach($default_template_array as $key=>$value){
                          $split_string = explode('_',$key);
                        if($key == 'content_text' || $key == 'footer_text' || $key == 'shipping_address' || $key == 'billing_address'){ ?>
                            <div class="Polaris-FormLayout__Item sd_<?php echo $key; ?>">
                            <div class="Polaris-Labelled__LabelWrapper">
                                <div class="Polaris-Label"><label id="PolarisSelect1Label" for="<?php echo $key; ?>" class="Polaris-Label__Text"><span class="Polaris-Text--root Polaris-Text--bodyMd Polaris-Text--regular"><?php echo $value[0]; ?></span></label></div>
                                </div>
                                <div class="Polaris-Connected Polaris-Connected--newDesignLanguage">
                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <textarea class="content customeditor sd_<?php echo $key; ?>_view" id="<?php echo $key; ?>" name="<?php echo $key; ?>"><?php echo $value[1]; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        <?php }else if($key == 'logo_alignment' || $key == 'thanks_img_alignment'){
                             if($key == 'logo_alignment'){
                                 $sd_add_view_class = 'sd_logo_view';
                             }else{
                                $sd_add_view_class = 'sd_thanks_img_view';
                             }
                             $data_style = 'float';
                            ?>
                            <div class="Polaris-FormLayout__Item">
                            <div class="input_border">
                                <div class="Polaris-Labelled__LabelWrapper">
                                    <div class="Polaris-Label"><label id="PolarisSelect1Label" for="<?php echo $key; ?>" class="Polaris-Label__Text"><span class="Polaris-Text--root Polaris-Text--bodyMd Polaris-Text--regular"><?php echo $value[0]; ?></span></label></div>
                                </div>
                                <div class="Polaris-Select">
                                    <select id="<?php echo $key; ?>" name="<?php echo $key; ?>" data-style = "<?php echo $data_style; ?>" class="Polaris-Select__Input sd_default_template_text_fields sd_add_style" data-id="<?php echo $sd_add_view_class; ?>" aria-invalid="false">
                                        <option value="Center" <?php if( $value[1] == 'Center'){ echo 'selected'; } ?>>Center</option>
                                        <option value="Left"  <?php if( $value[1] == 'Left'){ echo 'selected'; } ?>>Left</option>
                                        <option value="Right" <?php if( $value[1] == 'Right'){ echo 'selected'; } ?>>Right</option>
                                    </select>
                                    <div class="Polaris-Select__Content" aria-hidden="true">
                                        <span class="Polaris-Select__SelectedOption"><?php echo $value[1]; ?></span>
                                        <span class="Polaris-Select__Icon">
                                            <span class="Polaris-Icon">
                                                <span class="Polaris-Text--root Polaris-Text--bodySm Polaris-Text--regular Polaris-Text--visuallyHidden"></span>
                                                <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                    <path d="M7.676 9h4.648c.563 0 .879-.603.53-1.014l-2.323-2.746a.708.708 0 0 0-1.062 0l-2.324 2.746c-.347.411-.032 1.014.531 1.014Zm4.648 2h-4.648c-.563 0-.878.603-.53 1.014l2.323 2.746c.27.32.792.32 1.062 0l2.323-2.746c.349-.411.033-1.014-.53-1.014Z"></path>
                                                </svg>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="Polaris-Select__Backdrop"></div>
                                </div>
                            </div>
                            </div>
                        <?php }else{
                            $placeholder = '';
                            $data_style = end($split_string);
                            $color_fields_array = array('heading_text_color','text_color','manage_button_text_color','manage_button_background');
                            $add_color_class = ''; $sd_add_style_class='';$sd_pixel_text = '';$data_type = 'text';
                            $sd_add_view_class = 'sd_'.$key.'_view';
                            if(in_array($key, $color_fields_array)){
                                $add_color_class = 'jscolor sd_add_style';
                            }
                            $values_in_px_array = array('logo_height','logo_width','thanks_img_height','thanks_img_width');
                            if(in_array($key, $values_in_px_array)){
                                $sd_add_style_class =  'sd_add_style';
                                $data_type = 'number';
                                $sd_pixel_text = '<div class="Polaris-TextField__Spinner" aria-hidden="true">px</div>';
                                if($key == 'logo_height' || $key == 'logo_width'){
                                    $sd_add_view_class = 'sd_logo_view';
                                }else{
                                    $sd_add_view_class = 'sd_thanks_img_view';
                                }
                            }
                            if($key == 'manage_subscription_url'){
                                $placeholder = 'By default, it will be customer subscriptions page link';
                            }
                        ?>
                        <div class="Polaris-FormLayout__Item">
                        <div class="input_border">
                        <div class="Polaris-Labelled__LabelWrapper">
                            <div class="Polaris-Label">
                            <label id="PolarisTextField1Label" for="PolarisTextField1" class="Polaris-Label__Text">
                                <span class="Polaris-Text--root Polaris-Text--bodyMd Polaris-Text--regular"><?php echo $value[0]; ?></span>
                            </label>
                            </div>
                            <?php

                            if($key == 'logo' || $key == 'thanks_img'){
                                $add_color_class = 'sd_add_style';
                                $data_style = "src";
                                ?>
                                <div class="Polaris-Labelled__Action">
                                    <a target="_blank" class="Polaris-Link" href="https://<?php echo $mainobj->store; ?>/admin/settings/files" rel="noopener noreferrer" data-polaris-unstyled="true">Don't have url? Click here</a>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="Polaris-Connected">
                            <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                            <div class="Polaris-TextField Polaris-TextField--hasValue">
                                <input id="<?php echo $key; ?>" name="<?php echo $key; ?>" data-style = "<?php echo $data_style; ?>" autocomplete="off" class="Polaris-TextField__Input sd_default_template_text_fields <?php echo $add_color_class.' '.$sd_add_style_class; ?> " data-id="<?php echo $sd_add_view_class; ?>" type="<?php echo $data_type;?>" placeholder="<?php echo $placeholder; ?>" aria-labelledby="PolarisTextField1Label" aria-invalid="false" value="<?php echo $value[1]; ?>">
                                <?php echo $sd_pixel_text; ?>
                                <div class="Polaris-TextField__Backdrop">
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
                        <div id="PolarisPortalsContainer"></div>
                    </div>
                    <?php } }
                    foreach($show_hide_fields as $key=>$value){
                    ?>
                    <div class="Polaris-FormLayout__Item">
                        <div class="Polaris-Labelled__LabelWrapper">
                            <div class="Polaris-Label">
                                <label id="PolarisTextField1Label" for="PolarisTextField1" class="Polaris-Label__Text"><?php echo $value[0]; ?></label>
                            </div>
                            <div class="sd_dynamic_variables">
                                <label class="switch">
                                    <input type="checkbox" class="email_template_notification" data-table="subscription_purchase_template" data-field="<?php echo $key; ?>" <?php if($value[1] == '1'){ echo "checked"; } ?>>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    </div>
                    </div>
                </div>
              </div>
            </div>
            </div>
            <div class="Polaris-Layout__Section" style="flex: 1 1 46rem;">
                <div class="Polaris-LegacyCard">
                    <div class="Polaris-LegacyCard__Header" style="border-bottom:1px solid #d4d4d4;">
                        <h2 class="Polaris-Text--root Polaris-Text--headingMd Polaris-Text--semibold" style="font-size: 17px;margin: 15px 0px 16px 0px;">Preview</h2>
                    </div>
                    <div class="Polaris-LegacyCard__Header"><a target="_blank" class="Polaris-Link" href="<?php echo $mainobj->SHOPIFY_DOMAIN_URL; ?>/admin/customize-email-template" rel="noopener noreferrer" data-polaris-unstyled="true">How to customize</a></div>
                    <div class="Polaris-LegacyCard__Section sd_default_template_preview">
                    <div class="sd_template_view" style="background-color:#efefef" bgcolor="#efefef">
                        <?php
                            $result_template_data = $mainobj->email_template($template_type_array,'app_template_view');
                            echo $result_template_data['default_email_template'];
                        ?>
	                </div>
                    </div>

                    <div class="Polaris-LegacyCard__Section sd_custom_template_preview display-hide-label">
                        <div class="sd_custom_template_view"><?php if($custom_template != ''){ echo htmlspecialchars_decode($custom_template, ENT_QUOTES); }?></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
      </div>
        </div>
            <div id="PolarisPortalsContainer"></div>
            </form>
         </div>
   </div>
</div>
<?php include('../../footer.php'); ?>

<script>
    // add editor and the value to the fields
    var add_editor_to_fields = [];
    add_editor_to_fields['content_text'] = `<?php echo ($default_template_array['content_text'][1]); ?>`;
    add_editor_to_fields['footer_text'] = `<?php echo ($default_template_array['footer_text'][1]); ?>`;
    add_editor_to_fields['shipping_address'] = `<?php echo ($default_template_array['shipping_address'][1]); ?>`;
    add_editor_to_fields['billing_address'] = `<?php echo ($default_template_array['billing_address'][1]); ?>`;
    add_editor_to_fields['custom_template'] = `<?php if(empty($custom_template)){ echo 'empty'; }else{ echo ($custom_template); } ?>`;
    console.log(add_editor_to_fields);

    for (var key in add_editor_to_fields) {
        jQuery("#"+key).Editor({'insert_img':false,'block_quote':false,'fonts':false,'undo':false,'redo':false,'strikeout':false,'hr_line':false,'print':false,'togglescreen':false,'splchars':false, 'unlink':false, 'styles':false, 'insert_table':false, 'indent':false, 'outdent':false, 'rm_format':false, 'select_all':false, 'ol':false, 'ul':false });
        $('.subscription-edit-tabs .sd_'+key+' .Editor-editor').attr('data-id','sd_'+key+'_view');
        if(add_editor_to_fields[key] != 'empty'){
           $('.subscription-edit-tabs .sd_'+key+' .Editor-editor').html(add_editor_to_fields[key]);
        }
    }
</script>