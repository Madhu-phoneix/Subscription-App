<?php
    include("../header.php");
    $invoice_settings_qry = $db->query("SELECT * from invoice_template_settings where store_id = '$store_id'");
    $invoice_settings = $invoice_settings_qry->fetch(PDO::FETCH_ASSOC);

    $shop_settings_qry = $db->query("SELECT * from store_details where store_id = '$store_id'");
    $shop_settings = $shop_settings_qry->fetch(PDO::FETCH_ASSOC);
    if(empty($invoice_settings)){
        $logo = 'invoice_logo.png';
        $signature = 'signature.png';
        $billing_information_text = 'Billing Information';
        $serial_number_text = 'Sr. No.';
        $item_text = 'Item';
        $quantity_text = 'Quantity';
        $item_subtotal_text = 'Subtotal';
        $tax_text = 'Tax';
        $phone_number_text = 'Phone No.';
        $email_text = 'Email';
        $email_value = $shop_settings['store_email'];
        $phone_number_value = '';
        $company_name = $shop_settings['shop_name'];
        $terms_conditions_text = 'Terms & Conditions';
        $terms_conditions_value = '';
        $show_logo = '1';
        $show_signature = '0';
        $show_billing_information = '1';
        $show_subtotal = '1';
        $show_shipping = '1';
        $show_tax = '1';
        $invoice_number_text = 'Invoice No.';
        $invoice_number_prefix = '#';
        $invoice_number_suffix = '';
        $show_company_name = '1';
        $show_email = '1';
        $show_phone_number = '0';
        $show_invoice_date = '1';
        $show_terms_conditions = '0';
        $subtotal_text = 'Subtotal';
        $total_text = 'Total';
        $shipping_text = 'Shipping';
        $show_invoice_number = '1';
        $order_date_text = 'Date';
        $discount_text = 'Discount';
        $show_discount = '1';
        $auto_invoice = '0';
    }else{
        $logo = $invoice_settings['logo'];
        $signature = $invoice_settings['signature'];
        $billing_information_text = $invoice_settings['billing_information_text'];
        $serial_number_text = $invoice_settings['serial_number_text'];
        $item_text = $invoice_settings['item_text'];
        $quantity_text = $invoice_settings['quantity_text'];
        $item_subtotal_text = $invoice_settings['item_subtotal_text'];
        $tax_text = $invoice_settings['tax_text'];
        $phone_number_text = $invoice_settings['phone_number_text'];
        $email_text = $invoice_settings['email_text'];
        $email_value = $invoice_settings['email_value'];
        $phone_number_value = $invoice_settings['phone_number_value'];
        $company_name = $invoice_settings['company_name'];
        $terms_conditions_text = $invoice_settings['terms_conditions_text'];
        $terms_conditions_value = $invoice_settings['terms_conditions_value'];
        $show_logo = $invoice_settings['show_logo'];
        $show_signature = $invoice_settings['show_signature'];
        $show_billing_information = $invoice_settings['show_billing_information'];
        $show_subtotal = $invoice_settings['show_subtotal'];
        $show_shipping = $invoice_settings['show_shipping'];
        $show_tax = $invoice_settings['show_tax'];
        $invoice_number_text = $invoice_settings['invoice_number_text'];
        $invoice_number_prefix = $invoice_settings['invoice_number_prefix'];
        $invoice_number_suffix = $invoice_settings['invoice_number_suffix'];
        $show_company_name = $invoice_settings['show_company_name'];
        $show_email = $invoice_settings['show_email'];
        $show_phone_number = $invoice_settings['show_phone_number'];
        $show_invoice_date = $invoice_settings['show_invoice_date'];
        $show_terms_conditions = $invoice_settings['show_terms_conditions'];
        $subtotal_text = $invoice_settings['subtotal_text'];
        $total_text = $invoice_settings['total_text'];
        $shipping_text = $invoice_settings['shipping_text'];
        $show_invoice_number = $invoice_settings['show_invoice_number'];
        $order_date_text = $invoice_settings['order_date_text'];
        $discount_text = $invoice_settings['discount_text'];
        $show_discount = $invoice_settings['show_discount'];
        $auto_invoice = $invoice_settings['auto_invoice'];
    }
?>
<div class="Polaris-Layout">
<?php
include("../navigation.php");
?>
<div class="sd_invoice_header">
     <div id="PolarisPortalsContainer" class="t-right">
    <button class="Polaris-Button Polaris-Button--primary save-invoice-settings sd_button" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Save</span></span></button>
    </div>
    <div class="formbox">
        <div class="plan-heading">
          <h3>Edit Invoice</h3>
        </div>
         <div class="Polaris-ButtonGroup Polaris-ButtonGroup--segmented" data-buttongroup-segmented="true">
            <label class="switch">
				<input type="checkbox" class="invoice_edit_on">
			    <span class="slider round"></span>
			</label>
        </div>
    </div>
    <div class="Polaris-Page-Header__BreadcrumbWrapper">
        <nav role="navigation">
            <a class="Polaris-Breadcrumbs__Breadcrumb" href="setting.php?shop=<?php echo $store; ?>">
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

</div>
    <div class="sd_invoice_template">
        <div class="sd-invoice-settings">
            <div class="Polaris-Card">
                <div class="Polaris-Card__Section">
                <div class="Polaris-Card__settings">
                  <div class="Polaris-ResourceList__ResourceListWrapper">
                    <ul class="Polaris-ResourceList" aria-live="polite">
                        <li class="Polaris-ResourceItem__ListItem">
                            <div class="Polaris-ResourceItem__ItemWrapper">
                            <div class="Polaris-ResourceItem">
                                <div class="Polaris-ResourceItem__Container">
                                <div class="Polaris-ResourceItem__Content">
                                    <h3><span class="Polaris-TextStyle--variationStrong">Show Invoice Logo</span></h3>
                                    <label class="switch">
                                        <input type="checkbox" class="show_logo invoice_settings_toggle" name="show_logo" <?php if($show_logo == '1'){echo 'checked'; } ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        <li class="Polaris-ResourceItem__ListItem">
                            <div class="Polaris-ResourceItem__ItemWrapper">
                            <div class="Polaris-ResourceItem">
                                <div class="Polaris-ResourceItem__Container">
                                <div class="Polaris-ResourceItem__Content">
                                    <h3><span class="Polaris-TextStyle--variationStrong">Show Company Name</span></h3>
                                    <label class="switch">
                                        <input type="checkbox" class="show_comapny_name invoice_settings_toggle" name="show_company_name" <?php if($show_company_name == '1'){echo 'checked'; } ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        <li class="Polaris-ResourceItem__ListItem">
                            <div class="Polaris-ResourceItem__ItemWrapper">
                            <div class="Polaris-ResourceItem">
                                <div class="Polaris-ResourceItem__Container">
                                <div class="Polaris-ResourceItem__Content">
                                    <h3><span class="Polaris-TextStyle--variationStrong">Show Email</span></h3>
                                    <label class="switch">
                                        <input type="checkbox" class="show_email invoice_settings_toggle" name="show_email" <?php if($show_email == '1'){echo 'checked'; } ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        <li class="Polaris-ResourceItem__ListItem">
                            <div class="Polaris-ResourceItem__ItemWrapper">
                            <div class="Polaris-ResourceItem">
                                <div class="Polaris-ResourceItem__Container">
                                <div class="Polaris-ResourceItem__Content">
                                    <h3><span class="Polaris-TextStyle--variationStrong">Show Phone Number</span></h3>
                                    <label class="switch">
                                        <input type="checkbox" class="show_phone_number invoice_settings_toggle" name="show_phone_number" <?php if($show_phone_number == '1'){ echo 'checked'; } ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        <li class="Polaris-ResourceItem__ListItem">
                            <div class="Polaris-ResourceItem__ItemWrapper">
                            <div class="Polaris-ResourceItem">
                                <div class="Polaris-ResourceItem__Container">
                                <div class="Polaris-ResourceItem__Content">
                                    <h3><span class="Polaris-TextStyle--variationStrong">Show Billing Information</span></h3>
                                    <label class="switch">
                                        <input type="checkbox" class="show_billing_information invoice_settings_toggle" name="show_billing_information" <?php if($show_billing_information == '1'){echo 'checked'; } ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        <li class="Polaris-ResourceItem__ListItem">
                            <div class="Polaris-ResourceItem__ItemWrapper">
                            <div class="Polaris-ResourceItem">
                                <div class="Polaris-ResourceItem__Container">
                                <div class="Polaris-ResourceItem__Content">
                                    <h3><span class="Polaris-TextStyle--variationStrong">Show Invoice Number</span></h3>
                                    <label class="switch">
                                        <input type="checkbox" class="show_invoice_number invoice_settings_toggle" name="show_invoice_number" <?php if($show_invoice_number == '1'){echo 'checked'; } ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        <li class="Polaris-ResourceItem__ListItem">
                            <div class="Polaris-ResourceItem__ItemWrapper">
                            <div class="Polaris-ResourceItem">
                                <div class="Polaris-ResourceItem__Container">
                                <div class="Polaris-ResourceItem__Content">
                                    <h3><span class="Polaris-TextStyle--variationStrong">Show Invoice Date</span></h3>
                                    <label class="switch">
                                        <input type="checkbox" class="show_invoice_date invoice_settings_toggle" name="show_invoice_date" <?php if($show_invoice_number == '1'){echo 'checked'; } ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        <li class="Polaris-ResourceItem__ListItem">
                            <div class="Polaris-ResourceItem__ItemWrapper">
                            <div class="Polaris-ResourceItem">
                                <div class="Polaris-ResourceItem__Container">
                                <div class="Polaris-ResourceItem__Content">
                                    <h3><span class="Polaris-TextStyle--variationStrong">Show Terms and Conditions</span></h3>
                                    <label class="switch">
                                        <input type="checkbox" class="show_invoice_date invoice_settings_toggle" name="show_terms_conditions" <?php if($show_terms_conditions == '1'){echo 'checked'; } ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        <li class="Polaris-ResourceItem__ListItem">
                            <div class="Polaris-ResourceItem__ItemWrapper">
                            <div class="Polaris-ResourceItem">
                                <div class="Polaris-ResourceItem__Container">
                                <div class="Polaris-ResourceItem__Content">
                                    <h3><span class="Polaris-TextStyle--variationStrong">Show Subtotal</span></h3>
                                    <label class="switch">
                                        <input type="checkbox" class="show_subtotal invoice_settings_toggle" name="show_subtotal" <?php if($show_subtotal == '1'){echo 'checked'; } ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        <li class="Polaris-ResourceItem__ListItem">
                            <div class="Polaris-ResourceItem__ItemWrapper">
                            <div class="Polaris-ResourceItem">
                                <div class="Polaris-ResourceItem__Container">
                                <div class="Polaris-ResourceItem__Content">
                                    <h3><span class="Polaris-TextStyle--variationStrong">Show Discount</span></h3>
                                    <label class="switch">
                                        <input type="checkbox" class="show_subtotal invoice_settings_toggle" name="show_discount" <?php if($show_discount == '1'){echo 'checked'; } ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        <li class="Polaris-ResourceItem__ListItem">
                            <div class="Polaris-ResourceItem__ItemWrapper">
                            <div class="Polaris-ResourceItem">
                                <div class="Polaris-ResourceItem__Container">
                                <div class="Polaris-ResourceItem__Content">
                                    <h3><span class="Polaris-TextStyle--variationStrong">Show Shipping</span></h3>
                                    <label class="switch">
                                        <input type="checkbox" class="show_shipping invoice_settings_toggle" name="show_shipping" <?php if($show_shipping == '1'){echo 'checked'; } ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        <li class="Polaris-ResourceItem__ListItem">
                            <div class="Polaris-ResourceItem__ItemWrapper">
                            <div class="Polaris-ResourceItem">
                                <div class="Polaris-ResourceItem__Container">
                                <div class="Polaris-ResourceItem__Content">
                                    <h3><span class="Polaris-TextStyle--variationStrong">Show Taxes</span></h3>
                                    <label class="switch">
                                        <input type="checkbox" class="show_tax invoice_settings_toggle" name="show_tax" <?php if($show_tax == '1'){echo 'checked'; } ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        <!-- <li class="Polaris-ResourceItem__ListItem">
                            <div class="Polaris-ResourceItem__ItemWrapper">
                            <div class="Polaris-ResourceItem">
                                <div class="Polaris-ResourceItem__Container">
                                <div class="Polaris-ResourceItem__Content">
                                    <h3><span class="Polaris-TextStyle--variationStrong">Show Total</span></h3>
                                    <label class="switch">
                                        <input type="checkbox" class="show_total invoice_settings_toggle" name="show_total" <?php //if($show_total == '1'){echo 'checked'; } ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li> -->
                        <li class="Polaris-ResourceItem__ListItem">
                            <div class="Polaris-ResourceItem__ItemWrapper">
                            <div class="Polaris-ResourceItem">
                                <div class="Polaris-ResourceItem__Container">
                                <div class="Polaris-ResourceItem__Content">
                                    <h3><span class="Polaris-TextStyle--variationStrong">Show Signature</span></h3>
                                    <label class="switch">
                                        <input type="checkbox" class="show_signature invoice_settings_toggle" name="show_signature" <?php if($show_signature == '1'){echo 'checked'; } ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                        <li class="Polaris-ResourceItem__ListItem">
                            <div class="Polaris-ResourceItem__ItemWrapper">
                            <div class="Polaris-ResourceItem">
                                <div class="Polaris-ResourceItem__Container">
                                <div class="Polaris-ResourceItem__Content">
                                    <h3><span class="Polaris-TextStyle--variationStrong">Send automaically when subscription order created.</span></h3>
                                    <label class="switch">
                                        <input type="checkbox" class="auto_invoice invoice_settings_toggle" name="auto_invoice" <?php if($auto_invoice == '1'){echo 'checked'; } ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                                </div>
                            </div>
                            </div>
                        </li>
                    </ul>
            </div>
        </div>
                </div>
            </div>
        </div>
        <form name="save_invoice_settings" id="save_invoice_settings">
        <div class="main-invoice-table">
            <!-- Header -->
            <input type="hidden" name="store_id" value="<?php echo $store_id; ?>">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#f0f2f5">
                <tbody><tr class="sd_invoice_none">
                    <td height="20"></td>
                </tr>
                <tr>
                    <td>
                        <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff" style="">
                            <tbody><tr class="hiddenMobile">
                                <td height="40"></td>
                            </tr>
                            <tr class="visibleMobile">
                                <td height="30"></td>
                            </tr>
                            <tr>
                                <td>
                                    <table width="550" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPaddingheader">
                                        <tbody>
                                            <tr>
                                            <tr>
                                                <td height="40"><span style="font-size:10px;color:green;">File dimensions should be below 270*200 and size 500kb and file extension should be .png, .jpeg, .jpg and .gif.</span></td>
                                            </tr>
                                                <td>
                                                    <table width="270" border="0" cellpadding="0" cellspacing="0" align="left" class="col">
                                                        <tbody>
                                                            <tr class="show_logo <?php if($show_logo == '0'){ echo 'display-hide-label'; } ?>">
                                                                <td align="left" class="empty">
                                                                <a href="JavaScript:void(0)" id="uploadlogoimg" class="uploadimg editModeOn empty <?php echo 'display-hide-label'; ?>" style="text-align: center;">
                                                                    <span class="hovertext enable_hover">Upload Logo Image</span>
                                                                    <img type="image" id="set_logo_img" class="showFiles" src="<?php echo $SHOPIFY_DOMAIN_URL; ?>/application/assets/images/invoice/logo/<?php echo $logo; ?>" alt="logo" width="170">
                                                                </a>
                                                                <input type="file" name="logo" id="imglogo" value="<?php echo $logo;?>" class="img_input" style="display: none;" accept=".png, .jpg, .jpeg">
                                                                <img class="editModeOff" src="<?php echo $SHOPIFY_DOMAIN_URL; ?>/application/assets/images/invoice/logo/<?php echo $logo;?>" width="170" alt="logo" border="0">
                                                            </td>
                                                            </tr>

                                                            <tr class="hiddenMobile">
                                                                <td height="40"></td>
                                                            </tr>
                                                            <tr class="visibleMobile">
                                                                <td height="20"></td>
                                                            </tr>
                                                            <tr class="show_billing_information <?php if($show_billing_information == '0'){ echo 'display-hide-label';}?>">
                                                                <td style="font-size: 11px; font-family: 'Poppins', sans-serif; color: #000; line-height: 1; vertical-align: top; ">
                                                                    <strong><span class="editModeOff"><?php echo $billing_information_text; ?></span>
                                                                        <input placeholder="Please enter Billing information label" maxlength="100" value="<?php echo $billing_information_text; ?>" name="billing_information_text" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 2px; border-radius: 5px; width: 50%; height: 30px;">
                                                                    </strong>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="5"></td>
                                                            </tr>
                                                            <tr class="show_billing_information <?php if($show_billing_information == '0'){ echo 'display-hide-label';}?>">
                                                                <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #5b5b5b; line-height: 20px; vertical-align: top; ">
                                                                    Philip Brooks<br> Public Wales, Somewhere<br> New
                                                                    York NY<br> 4468, United States<br> T: 202-555-0133
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <table width="270" border="0" cellpadding="0" cellspacing="0" align="right" class="col">
                                                        <tbody>
                                                            <tr class="visibleMobile">
                                                                <td height="20"></td>
                                                            </tr>
                                                            <tr>
                                                                <td height="5"></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-size: 12px; color: #5b5b5b; font-family: 'Poppins', sans-serif; line-height: 18px; vertical-align: top; text-align: right;">
                                                                    <div class="show_company_name <?php if($show_company_name == '0'){ echo 'display-hide-label'; }?>">
                                                                        <strong style="color: #000;" class ><span class="editModeOff"><?php echo $company_name; ?></span></strong>
                                                                        <input placeholder="Please enter Company name" maxlength="100" value="<?php echo $company_name ?>" name="company_name" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 7px; border-radius: 5px; width: 100%; height: 30px;">
                                                                        <br>
                                                                    </div>

                                                                    <div class="show_email <?php if($show_email == '0'){ echo 'display-hide-label'; }?>">
                                                                        <strong style="color: #000;"><span class="editModeOff"><?php echo $email_text; ?></span>
                                                                        <input placeholder="Please enter Company email text" maxlength="100" value="<?php echo $email_text; ?>" name="email_text" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 7px; border-radius: 5px; width: 29%; height: 30px;">:</strong>
                                                                        <span class="editModeOff"><?php echo $email_value ?></span>
                                                                        <input placeholder="Please enter Company email" maxlength="100" value="<?php echo $email_value ?>" name="email_value" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 2px; border-radius: 5px; width: 68%; height: 30px;"><br>
                                                                    </div>

                                                                    <div class="show_phone_number <?php if($show_phone_number == '0'){ echo 'display-hide-label'; }?>">
                                                                        <strong style="color: #000;"><span class="editModeOff"><?php echo $phone_number_text; ?></span>
                                                                        <input placeholder="Please enter phone number label" maxlength="100" value="<?php echo $phone_number_text; ?>" name="phone_number_text" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 7px; border-radius: 5px; width: 33%; height: 30px;">:</strong><span class="editModeOff"><?php echo $phone_number_value; ?></span>
                                                                        <input placeholder="Please enter phone number" maxlength="100" value="<?php echo $phone_number_value; ?>" name="phone_number_value" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 2px; border-radius: 5px; width: 63%; height: 30px;">
                                                                    </div>

                                                                </td>
                                                            </tr>
                                                            <tr>
                                                            </tr><tr class="hiddenMobile">
                                                                <td height="50"></td>
                                                            </tr>
                                                            <tr class="visibleMobile">
                                                                <td height="20"></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-size: 12px; color: #5b5b5b; font-family: 'Poppins', sans-serif; line-height: 18px; vertical-align: top; text-align: right;">
                                                                    <small class="show_invoice_number <?php if($show_invoice_number == '0'){ echo 'display-hide-label'; } ?>"><span class="editModeOff"><?php echo $invoice_number_text; ?></span>
                                                                    <input placeholder="Please enter Invoice number label" maxlength="100" value="<?php  echo $invoice_number_text; ?>" name="invoice_number_text" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 2px; border-radius: 5px; width: 50%; height: 30px;">:
                                                                    <input placeholder="Please enter Invoice number label" maxlength="100" value="<?php  echo $invoice_number_prefix; ?>" name="invoice_number_prefix" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin: 5px; border-radius: 5px; width: 34%; height: 30px;">
                                                                    <span class="editModeOff"><?php  echo $invoice_number_prefix; ?></span>1002</small> <br>

                                                                    <small class="show_invoice_date <?php if($show_invoice_date == '0'){ echo 'display-hide-label'; } ?>"><span class="editModeOff"><?php echo $order_date_text; ?><input placeholder="Please enter Invoice number label" maxlength="100" value="<?php  echo $order_date_text; ?>" name="order_date_text" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 2px; border-radius: 5px; width: 50%; height: 30px;">:</span>MARCH 4TH 2016</small>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody></table>
                    </td>
                </tr>
            </tbody></table>
            <!-- /Header -->
            <!-- Order Details -->
            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#f0f2f5">
                <tbody>
                    <tr>
                        <td>
                            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff">
                                <tbody>
                                    <tr>
                                    </tr><tr class="hiddenMobile">
                                        <td height="30"></td>
                                    </tr>
                                    <tr class="visibleMobile">
                                        <td height="40"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table width="550" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                                                <tbody>
                                                    <tr class="invoice-table-head">
                                                        <th style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #fff; font-weight: normal; line-height: 1; vertical-align: top; padding: 10px;" align="left">
                                                            <small><span class="editModeOff"><?php echo $serial_number_text; ?></span></small>
                                                            <input placeholder="Please enter Sr. No. label" maxlength="100" value="<?php echo $serial_number_text; ?>" name="serial_number_text" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 2px; border-radius: 5px; width: 100%; height: 30px;">
                                                        </th>
                                                        <th style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #fff; font-weight: normal; line-height: 1; vertical-align: top; padding: 10px;" width="52%" align="left"><span class="editModeOff"><?php echo $item_text; ?></span>
                                                        <input placeholder="Please enter item text" maxlength="100" value="<?php echo $item_text; ?>" name="item_text" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 2px; border-radius: 5px; width: 100%; height: 30px;">
                                                        </th>
                                                        <th style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #fff; font-weight: normal; line-height: 1; vertical-align: top; padding: 10px 0 10px 0;" align="center">
                                                            <span class="editModeOff"><?php echo $quantity_text; ?></span>
                                                            <input placeholder="Please enter quantity text" maxlength="100" value=" <?php echo $quantity_text; ?>" name="quantity_text" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 2px; border-radius: 5px; width: 100%; height: 30px;">
                                                        </th>
                                                        <th style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #fff; font-weight: normal; line-height: 1; vertical-align: top; padding: 10px;" align="right">
                                                            <span class="editModeOff"><?php echo $item_subtotal_text; ?></span>
                                                            <input placeholder="Please enter item subtotal text" maxlength="100" value=" <?php echo $item_subtotal_text; ?>" name="item_subtotal_text" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 2px; border-radius: 5px; width: 100%; height: 30px;">
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <td height="1" style="background: #bebebe;" colspan="4"></td>
                                                    </tr>
                                                    <tr>
                                                        <td height="10" colspan="4"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000;  line-height: 18px;  vertical-align: top; padding:10px;">
                                                            <small>1.</small></td>
                                                        <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000; font-weight: 600;  line-height: 18px;  vertical-align: top; padding:10px;" class="article">
                                                            Beats Studio Over-Ear Headphones
                                                        </td>
                                                        <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000;  line-height: 18px;  vertical-align: top; padding:10px 0;" align="center">1</td>
                                                        <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #1e2b33;  line-height: 18px;  vertical-align: top; padding: 10px 10px 0 0;" align="right">$299.95</td>
                                                    </tr>
                                                    <tr>
                                                        <td height="1" colspan="4" style="border-bottom:1px solid #e4e4e4"></td>
                                                    </tr>
                                                    <tr>
                                                    <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000;  line-height: 18px;  vertical-align: top; padding:10px;">
                                                            <small>2.</small></td>
                                                        <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000; font-weight: 600;  line-height: 18px;  vertical-align: top; padding:10px;" class="article">Beats RemoteTalk Cable</td>
                                                        <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000;  line-height: 18px;  vertical-align: top; padding:10px 0;" align="center">1</td>
                                                        <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #1e2b33;  line-height: 18px;  vertical-align: top;     padding: 10px 10px 0 0;" align="right">$29.95</td>
                                                    </tr>
                                                    <tr>
                                                        <td height="1" colspan="4" style="border-bottom:1px solid #e4e4e4"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="20"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- /Order Details -->
            <!-- Total -->
            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#f0f2f5">
                <tbody>
                    <tr>
                        <td>
                            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff">
                                <tbody>
                                    <tr>
                                        <td>

                                            <!-- Table Total -->
                                            <table width="550" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                                                <tbody>
                                                    <tr class="show_subtotal <?php if($show_subtotal == '0'){ echo 'display-hide-label'; } ?>">
                                                        <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                                            <span class="editModeOff"><?php echo $subtotal_text; ?></span>
                                                            <input placeholder="Please enter Subtotal label" maxlength="100" value="<?php echo $subtotal_text; ?>" name="subtotal_text" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 7px; border-radius: 5px; width: 30%; height: 30px;">
                                                        </td>
                                                        <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000; line-height: 22px; padding-right: 10px; vertical-align: top; text-align:right; white-space:nowrap;" width="80">
                                                            $329.90
                                                        </td>
                                                    </tr>
                                                    <tr class="show_discount <?php if($show_discount == '0'){ echo 'display-hide-label'; } ?>">
                                                        <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                                            <span class="editModeOff"><?php echo $discount_text; ?></span>
                                                            <input placeholder="Please enter Discount label" maxlength="100" value="<?php echo $discount_text; ?>" name="discount_text" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 7px; border-radius: 5px; width: 30%; height: 30px;">
                                                        </td>
                                                        <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000; line-height: 22px; padding-right: 10px; vertical-align: top; text-align:right; white-space:nowrap;" width="80">
                                                            $5.00
                                                        </td>
                                                    </tr>
                                                    <tr class="show_shipping <?php if($show_shipping == '0'){ echo 'display-hide-label'; } ?>">
                                                        <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                                        <span class="editModeOff"><?php echo $shipping_text; ?></span>
                                                        <input placeholder="Please enter Shipping label" maxlength="100" value="<?php echo $shipping_text; ?>" name="shipping_text" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 7px; border-radius: 5px; width: 30%; height: 30px;">
                                                        </td>
                                                        <td style="font-size: 12px; font-family: 'Poppins', sans-serif; padding-right: 10px; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                                            $5.00
                                                        </td>
                                                    </tr>
                                                    <tr class="show_tax <?php if($show_tax == '0'){ echo 'display-hide-label'; } ?>">
                                                        <td style="font-size: 12px;font-family: 'Poppins', sans-serif;color: #000000;line-height: 22px;vertical-align: top;text-align:right;">
                                                            <small><span class="editModeOff"><?php echo $tax_text; ?></span></small>
                                                            <input placeholder="Please enter Tax label" maxlength="100" value="<?php echo $tax_text; ?>" name="tax_text" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 7px; border-radius: 5px; width: 30%; height: 30px;">
                                                        </td>
                                                        <td style="font-size: 12px;font-family: 'Poppins', sans-serif;padding-right: 10px;color: #000000;line-height: 22px;vertical-align: top;text-align:right;">
                                                            <small>$5.00</small>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                                            <strong><span class="editModeOff"><?php echo $total_text; ?></span></strong>
                                                            <input placeholder="Please enter Total label" maxlength="100" value="<?php echo $total_text; ?>" name="total_text" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 2px; border-radius: 5px; width: 30%; height: 30px;">
                                                        </td>
                                                        <td style="font-size: 12px; font-family: 'Poppins', sans-serif; padding-right: 10px; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                                            <strong>$344.90</strong>
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                            <!-- /Table Total -->

                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- /Total -->
            <!-- Information -->
            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#f0f2f5">
                <tbody>
                    <tr>
                        <td>
                            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff">
                                <tbody>
                                    <tr>
                                    </tr><tr class="hiddenMobile">
                                        <td height="30"></td>
                                    </tr>
                                    <tr class="visibleMobile">
                                        <td height="40"></td>
                                    </tr>
                                    <tr class="show_terms_conditions <?php if($show_terms_conditions == '0'){ echo 'display-hide-label'; }?>">
                                        <td>
                                            <table width="550" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <table width="500" border="0" cellpadding="0" cellspacing="0" align="left" class="col">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="font-size: 11px; font-family: 'Poppins', sans-serif; color: #000; line-height: 1; vertical-align: top; ">
                                                                            <strong><span class="editModeOff"><?php echo $terms_conditions_text; ?></span></strong>
                                                                            <input placeholder="Please enter Term & conditions label" maxlength="100" value=" <?php echo $terms_conditions_text; ?>" name="terms_conditions_text" id="name" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 2px; border-radius: 5px; width: 100%; height: 30px;">
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td width="100%" height="10"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #5b5b5b; line-height: 20px; vertical-align: top; ">
                                                                            <span class="editModeOff"><?php echo $terms_conditions_value; ?></span>
                                                                            <textarea name="terms_conditions_value" class="editModeOn <?php echo 'display-hide-label'; ?>" style="font-size: 15px; font-weight: 500; border: 1px dashed rgb(72, 141, 255); margin-bottom: 2px; border-radius: 5px; width: 100%;"><?php echo $terms_conditions_value; ?></textarea>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr class="show_signature <?php if($show_signature == '0'){ echo 'display-hide-label'; } ?>">
                                        <td>
                                            <table width="550" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <table width="270" border="0" cellpadding="0" cellspacing="0" align="right" class="col">
                                                                <tbody>
                                                                    <tr class="hiddenMobile">
                                                                        <td height="20"></td>
                                                                    </tr>
                                                                    <tr class="visibleMobile">
                                                                        <td height="20"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="empty" style="font-size: 11px;font-family: 'Poppins', sans-serif;color: #000;line-height: 1;vertical-align: top;text-align: right;">
                                                                            <img class="editModeOff" src="<?php echo $SHOPIFY_DOMAIN_URL; ?>/application/assets/images/invoice/signature/<?php echo $signature; ?>" width="170" height="50" alt="logo" border="0">
                                                                            <a href="JavaScript:void(0)" id="uploadsignatureimg" class="uploadimg editModeOn empty <?php echo 'display-hide-label'; ?>" style="text-align: center;">
                                                                                <span class="hovertext enable_hover">Upload Logo Image</span>
                                                                                <img type="image" id="set_signature_img" class="showFiles" src="<?php echo $SHOPIFY_DOMAIN_URL; ?>/application/assets/images/invoice/signature/<?php echo $signature; ?>" width="170" alt="logo" border="0">
                                                                            </a>
                                                                            <input type="file" name="signature_img"  value="<?php echo $signature; ?>" id="signatureImg" class="img_input" style="display: none;" accept=".png, .jpg, .jpeg">
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr class="hiddenMobile">
                                        <td height="30"></td>
                                    </tr>
                                    <tr class="visibleMobile">
                                        <td height="30"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- /Information -->
            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#f0f2f5">

                <tbody><tr>
                    <td>
                        <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff" style="">
                            <tbody>
                            <tr class="spacer">
                                <td height="10"></td>
                            </tr>

                        </tbody></table>
                    </td>
                </tr>
                <tr class="sd_invoice_none">
                    <td height="20"></td>
                </tr>
            </tbody></table>
        </div>
        </form>
    </div>
<?php
include("../footer.php");
?>