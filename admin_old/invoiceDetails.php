<?php
    $current_page = basename($_SERVER['PHP_SELF']);
    $dirPath = dirname(__DIR__);
    include($dirPath."/application/controller/Mainfunction.php");
    $mainobj = new MainFunction($store);
    $order_id = $_GET['order_id'];
    $invoice_settings = $mainobj->customQuery("SELECT * from invoice_template_settings where store_id = '$mainobj->store_id'");
    $shop_settings = $mainobj->customQuery("SELECT * from store_details where store_id = '$mainobj->store_id'");
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
        $email_value = $shop_settings[0]['store_email'];
        $phone_number_value = '';
        $company_name = $shop_settings[0]['shop_name'];
        $terms_conditions_text = 'Terms & Conditions';
        $terms_conditions_value = '';
        $show_logo = '1';
        $show_signature = '1';
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
        $discount_text = 'Discount';
        $show_discount = '1';

    }else{
        $logo = $invoice_settings[0]['logo'];
        $signature = $invoice_settings[0]['signature'];
        $billing_information_text = $invoice_settings[0]['billing_information_text'];
        $serial_number_text = $invoice_settings[0]['serial_number_text'];
        $item_text = $invoice_settings[0]['item_text'];
        $quantity_text = $invoice_settings[0]['quantity_text'];
        $item_subtotal_text = $invoice_settings[0]['item_subtotal_text'];
        $tax_text = $invoice_settings[0]['tax_text'];
        $phone_number_text = $invoice_settings[0]['phone_number_text'];
        $email_text = $invoice_settings[0]['email_text'];
        $email_value = $invoice_settings[0]['email_value'];
        $phone_number_value = $invoice_settings[0]['phone_number_value'];
        $company_name = $invoice_settings[0]['company_name'];
        $terms_conditions_text = $invoice_settings[0]['terms_conditions_text'];
        $terms_conditions_value = $invoice_settings[0]['terms_conditions_value'];
        $show_logo = $invoice_settings[0]['show_logo'];
        $show_signature = $invoice_settings[0]['show_signature'];
        $show_billing_information = $invoice_settings[0]['show_billing_information'];
        $show_subtotal = $invoice_settings[0]['show_subtotal'];
        $show_shipping = $invoice_settings[0]['show_shipping'];
        $show_tax = $invoice_settings[0]['show_tax'];
        $invoice_number_text = $invoice_settings[0]['invoice_number_text'];
        $invoice_number_prefix = $invoice_settings[0]['invoice_number_prefix'];
        $invoice_number_suffix = $invoice_settings[0]['invoice_number_suffix'];
        $show_company_name = $invoice_settings[0]['show_company_name'];
        $show_email = $invoice_settings[0]['show_email'];
        $show_phone_number = $invoice_settings[0]['show_phone_number'];
        $show_invoice_date = $invoice_settings[0]['show_invoice_date'];
        $show_terms_conditions = $invoice_settings[0]['show_terms_conditions'];
        $subtotal_text = $invoice_settings[0]['subtotal_text'];
        $total_text = $invoice_settings[0]['total_text'];
        $shipping_text = $invoice_settings[0]['shipping_text'];
        $show_invoice_number = $invoice_settings[0]['show_invoice_number'];
        $discount_text = $invoice_settings[0]['discount_text'];
        $show_discount = $invoice_settings[0]['show_discount'];
    }
    $all_shopify_currencies = ['AED' => 'د.إ','AFN' => '؋','ALL' => 'L','AMD' => '֏','ANG' => 'ƒ','AOA' => 'Kz','ARS' => '$','AUD' => '$','AWG' => 'ƒ','AZN' => '₼','BAM' => 'KM','BBD' => '$','BDT' => '৳','BGN' => 'лв','BHD' => '.د.ب','BIF' => 'FBu','BMD' => '$','BND' => '$','BOB' => 'Bs.','BRL' => 'R$','BSD' => '$','BWP' => 'P','BZD' => '$',
    'CAD' => '$','CDF' => 'FC','CHF' => 'CHF','CLP' => '$','CNY' => '¥','COP' => '$','CRC' => '₡','CVE' => '$','CZK' => 'Kč','DJF' => 'Fdj','DKK' => 'kr','DOP' => '$','DZD' => 'د.ج','EGP' => 'E£','ERN' => 'Nfk','ETB' => 'Br','EUR' => '€','FJD' => '$','FKP' => '£','GBP' => '£','GEL' => '₾','GHS' => '₵','GIP' => '£','GMD' => 'D','GNF' => 'FG',
    'GTQ' => 'Q','GYD' => '$','HKD' => '$','HNL' => 'L','HRK' => 'kn','HTG' => 'G','HUF' => 'Ft','IDR' => 'Rp','ILS' => '₪','INR' => '₹','ISK' => 'kr','JMD' => '$','JOD' => 'د.ا','JPY' => '¥','KES' => 'KSh','KGS' => 'лв','KHR' => '៛','KMF' => 'CF','KRW' => '₩','KWD' => 'د.ك','KYD' => '$','KZT' => '₸','LAK' => '₭','LBP' => 'L£','USD' => '$','BTN' => 'Nu.','BYN' => 'Br','CUC' => '$','CUP' => '$'];
    $order_details_query = 'query {
        order(id:"gid://shopify/Order/'.$order_id.'"){
                createdAt
                name
                currencyCode
                totalShippingPriceSet{
                    presentmentMoney{
                        amount
                        currencyCode
                    }
                }
                billingAddress{
                    firstName
                    lastName
                    address1
                    address2
                    phone
                    city
                    country
                    company
                    province
                    provinceCode
                    zip
                    countryCodeV2
                }
                currentSubtotalPriceSet{
                    presentmentMoney{
                        amount
                        currencyCode
                    }
                }
                totalTaxSet{
                    presentmentMoney{
                        amount
                    }
                }
                totalPriceSet{
                    presentmentMoney{
                        amount
                    }
                }
                totalDiscountsSet{
                    presentmentMoney{
                        amount
                    }
                }
                lineItems(first:50){
                    edges{
                        node{
                            id
                            quantity
                            product{
                              id
                              featuredImage{
                                url
                              }
                            }
                            variant{
                              id
                              image{
                                url
                              }
                            }
                           title
                          variantTitle
                          originalUnitPriceSet{
                            presentmentMoney{
                              amount
                            }
                          }

                        }
                    }
                }
        }
    }';
    $order_details = $mainobj->graphqlQuery($order_details_query,null,null,null);
    // if($mainobj->store == 'predictive-search.myshopify.com'){
    //     echo '<pre>';
    //     print_r($order_details);
    //     die;
    // }
    $order_details_data = $order_details['data']['order'];
    $billing_firstname = $order_details_data['billingAddress']['firstName'];
    $billing_lastname = $order_details_data['billingAddress']['lastName'];
    $billing_address1 = $order_details_data['billingAddress']['address1'];
    $billing_city = $order_details_data['billingAddress']['city'];
    $billing_country = $order_details_data['billingAddress']['country'];
    $billing_province = $order_details_data['billingAddress']['province'];
    $billing_zip = $order_details_data['billingAddress']['zip'];
    $order_date = $order_details_data['createdAt'];
    $item_subtotal = $order_details_data['currentSubtotalPriceSet']['presentmentMoney']['amount'];
    $order_name = $order_details_data['name'];
    $currency_symbol = $all_shopify_currencies[$order_details_data['currentSubtotalPriceSet']['presentmentMoney']['currencyCode']];
    $shipping_price = $order_details_data['totalShippingPriceSet']['presentmentMoney']['amount'];
    $tax = $order_details_data['totalTaxSet']['presentmentMoney']['amount'];
    $order_number = preg_replace('/[^0-9]/', '', $order_name);
    $grand_total = $order_details_data['totalPriceSet']['presentmentMoney']['amount'];
    $total_discount = $order_details_data['totalDiscountsSet']['presentmentMoney']['amount'];
    ?>
    <head>
    <link rel="stylesheet" href="https://unpkg.com/@shopify/polaris@7.3.1/build/esm/styles.css"/>
    <link href="<?php echo $mainobj->SHOPIFY_DOMAIN_URL ;?>/application/assets/css/style.css" rel="stylesheet">
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        @media print {
            * {
                -webkit-print-color-adjust: exact;
            }

            html {
                background: none;
                padding: 0;
            }

            body {
                box-shadow: none;
                margin: 0;
                height: 100%;
            }

            span:empty {
                display: none;
            }

            .add,
            .cut {
                display: none;
            }
        }

        @page {
            margin: 0;
            height: 100%;
            width:100%;
        }


        .main-invoice-table .visibleMobile {
            display: none;
        }

        .main-invoice-table .hiddenMobile {
            display: block;
        }

        .main-invoice-table .invoice-table-head {
            background: black;
        }

        @media only screen and (max-width: 600px) {


            .main-invoice-table   table[class=fullTable] {
                width: 96% !important;
                clear: both;
            }

            .main-invoice-table  table[class=fullPadding] {
                width: 85% !important;
                clear: both;
            }

            .main-invoice-table .fullPaddingheader {
                width: 85%;
            }

            .main-invoice-table  table[class=col] {
                width: 45% !important;
            }

            .main-invoice-table  .erase {
                display: none;
            }
        }

        @media only screen and (max-width: 420px) {
            .main-invoice-table table[class=fullTable] {
                width: 100% !important;
                clear: both;
            }

            .main-invoice-table table[class=fullPadding] {
                width: 85% !important;
                clear: both;
            }

            .main-invoice-table .fullPaddingheader {
                width: 85%;
            }

            .main-invoice-table  table[class=col] {
                width: 100% !important;
                clear: both;
            }

            .main-invoice-table table[class=col] td {
                text-align: left !important;
            }

            .main-invoice-table .erase {
                display: none;
                font-size: 0;
                max-height: 0;
                line-height: 0;
                padding: 0;
            }

            .main-invoice-table .visibleMobile {
                display: block !important;
            }

            .main-invoice-table  .hiddenMobile {
                display: none !important;
            }
        }
    </style>
    </head>
    <div
  style="color: rgb(32, 34, 35); --p-background:rgba(246, 246, 247, 1); --p-background-hovered:rgba(241, 242, 243, 1); --p-background-pressed:rgba(237, 238, 239, 1); --p-background-selected:rgba(237, 238, 239, 1); --p-surface:rgba(255, 255, 255, 1); --p-surface-neutral:rgba(228, 229, 231, 1); --p-surface-neutral-hovered:rgba(219, 221, 223, 1); --p-surface-neutral-pressed:rgba(201, 204, 208, 1); --p-surface-neutral-disabled:rgba(241, 242, 243, 1); --p-surface-neutral-subdued:rgba(246, 246, 247, 1); --p-surface-subdued:rgba(250, 251, 251, 1); --p-surface-disabled:rgba(250, 251, 251, 1); --p-surface-hovered:rgba(246, 246, 247, 1); --p-surface-pressed:rgba(241, 242, 243, 1); --p-surface-depressed:rgba(237, 238, 239, 1); --p-backdrop:rgba(0, 0, 0, 0.5); --p-overlay:rgba(255, 255, 255, 0.5); --p-shadow-from-dim-light:rgba(0, 0, 0, 0.2); --p-shadow-from-ambient-light:rgba(23, 24, 24, 0.05); --p-shadow-from-direct-light:rgba(0, 0, 0, 0.15); --p-hint-from-direct-light:rgba(0, 0, 0, 0.15); --p-surface-search-field:rgba(241, 242, 243, 1); --p-border:rgba(140, 145, 150, 1); --p-border-neutral-subdued:rgba(186, 191, 195, 1); --p-border-hovered:rgba(153, 158, 164, 1); --p-border-disabled:rgba(210, 213, 216, 1); --p-border-subdued:rgba(201, 204, 207, 1); --p-border-depressed:rgba(87, 89, 89, 1); --p-border-shadow:rgba(174, 180, 185, 1); --p-border-shadow-subdued:rgba(186, 191, 196, 1); --p-divider:rgba(225, 227, 229, 1); --p-icon:rgba(92, 95, 98, 1); --p-icon-hovered:rgba(26, 28, 29, 1); --p-icon-pressed:rgba(68, 71, 74, 1); --p-icon-disabled:rgba(186, 190, 195, 1); --p-icon-subdued:rgba(140, 145, 150, 1); --p-text:rgba(32, 34, 35, 1); --p-text-disabled:rgba(140, 145, 150, 1); --p-text-subdued:rgba(109, 113, 117, 1); --p-interactive:rgba(44, 110, 203, 1); --p-interactive-disabled:rgba(189, 193, 204, 1); --p-interactive-hovered:rgba(31, 81, 153, 1); --p-interactive-pressed:rgba(16, 50, 98, 1); --p-focused:rgba(69, 143, 255, 1); --p-surface-selected:rgba(242, 247, 254, 1); --p-surface-selected-hovered:rgba(237, 244, 254, 1); --p-surface-selected-pressed:rgba(229, 239, 253, 1); --p-icon-on-interactive:rgba(255, 255, 255, 1); --p-text-on-interactive:rgba(255, 255, 255, 1); --p-action-secondary:rgba(255, 255, 255, 1); --p-action-secondary-disabled:rgba(255, 255, 255, 1); --p-action-secondary-hovered:rgba(246, 246, 247, 1); --p-action-secondary-pressed:rgba(241, 242, 243, 1); --p-action-secondary-depressed:rgba(109, 113, 117, 1); --p-action-primary:rgba(0, 128, 96, 1); --p-action-primary-disabled:rgba(241, 241, 241, 1); --p-action-primary-hovered:rgba(0, 110, 82, 1); --p-action-primary-pressed:rgba(0, 94, 70, 1); --p-action-primary-depressed:rgba(0, 61, 44, 1); --p-icon-on-primary:rgba(255, 255, 255, 1); --p-text-on-primary:rgba(255, 255, 255, 1); --p-text-primary:rgba(0, 123, 92, 1); --p-text-primary-hovered:rgba(0, 108, 80, 1); --p-text-primary-pressed:rgba(0, 92, 68, 1); --p-surface-primary-selected:rgba(241, 248, 245, 1); --p-surface-primary-selected-hovered:rgba(179, 208, 195, 1); --p-surface-primary-selected-pressed:rgba(162, 188, 176, 1); --p-border-critical:rgba(253, 87, 73, 1); --p-border-critical-subdued:rgba(224, 179, 178, 1); --p-border-critical-disabled:rgba(255, 167, 163, 1); --p-icon-critical:rgba(215, 44, 13, 1); --p-surface-critical:rgba(254, 211, 209, 1); --p-surface-critical-subdued:rgba(255, 244, 244, 1); --p-surface-critical-subdued-hovered:rgba(255, 240, 240, 1); --p-surface-critical-subdued-pressed:rgba(255, 233, 232, 1); --p-surface-critical-subdued-depressed:rgba(254, 188, 185, 1); --p-text-critical:rgba(215, 44, 13, 1); --p-action-critical:rgba(216, 44, 13, 1); --p-action-critical-disabled:rgba(241, 241, 241, 1); --p-action-critical-hovered:rgba(188, 34, 0, 1); --p-action-critical-pressed:rgba(162, 27, 0, 1); --p-action-critical-depressed:rgba(108, 15, 0, 1); --p-icon-on-critical:rgba(255, 255, 255, 1); --p-text-on-critical:rgba(255, 255, 255, 1); --p-interactive-critical:rgba(216, 44, 13, 1); --p-interactive-critical-disabled:rgba(253, 147, 141, 1); --p-interactive-critical-hovered:rgba(205, 41, 12, 1); --p-interactive-critical-pressed:rgba(103, 15, 3, 1); --p-border-warning:rgba(185, 137, 0, 1); --p-border-warning-subdued:rgba(225, 184, 120, 1); --p-icon-warning:rgba(185, 137, 0, 1); --p-surface-warning:rgba(255, 215, 157, 1); --p-surface-warning-subdued:rgba(255, 245, 234, 1); --p-surface-warning-subdued-hovered:rgba(255, 242, 226, 1); --p-surface-warning-subdued-pressed:rgba(255, 235, 211, 1); --p-text-warning:rgba(145, 106, 0, 1); --p-border-highlight:rgba(68, 157, 167, 1); --p-border-highlight-subdued:rgba(152, 198, 205, 1); --p-icon-highlight:rgba(0, 160, 172, 1); --p-surface-highlight:rgba(164, 232, 242, 1); --p-surface-highlight-subdued:rgba(235, 249, 252, 1); --p-surface-highlight-subdued-hovered:rgba(228, 247, 250, 1); --p-surface-highlight-subdued-pressed:rgba(213, 243, 248, 1); --p-text-highlight:rgba(52, 124, 132, 1); --p-border-success:rgba(0, 164, 124, 1); --p-border-success-subdued:rgba(149, 201, 180, 1); --p-icon-success:rgba(0, 127, 95, 1); --p-surface-success:rgba(174, 233, 209, 1); --p-surface-success-subdued:rgba(241, 248, 245, 1); --p-surface-success-subdued-hovered:rgba(236, 246, 241, 1); --p-surface-success-subdued-pressed:rgba(226, 241, 234, 1); --p-text-success:rgba(0, 128, 96, 1); --p-decorative-one-icon:rgba(126, 87, 0, 1); --p-decorative-one-surface:rgba(255, 201, 107, 1); --p-decorative-one-text:rgba(61, 40, 0, 1); --p-decorative-two-icon:rgba(175, 41, 78, 1); --p-decorative-two-surface:rgba(255, 196, 176, 1); --p-decorative-two-text:rgba(73, 11, 28, 1); --p-decorative-three-icon:rgba(0, 109, 65, 1); --p-decorative-three-surface:rgba(146, 230, 181, 1); --p-decorative-three-text:rgba(0, 47, 25, 1); --p-decorative-four-icon:rgba(0, 106, 104, 1); --p-decorative-four-surface:rgba(145, 224, 214, 1); --p-decorative-four-text:rgba(0, 45, 45, 1); --p-decorative-five-icon:rgba(174, 43, 76, 1); --p-decorative-five-surface:rgba(253, 201, 208, 1); --p-decorative-five-text:rgba(79, 14, 31, 1); --p-border-radius-base:0.4rem; --p-border-radius-wide:0.8rem; --p-border-radius-full:50%; --p-card-shadow:0px 0px 5px var(--p-shadow-from-ambient-light), 0px 1px 2px var(--p-shadow-from-direct-light); --p-popover-shadow:-1px 0px 20px var(--p-shadow-from-ambient-light), 0px 1px 5px var(--p-shadow-from-direct-light); --p-modal-shadow:0px 26px 80px var(--p-shadow-from-dim-light), 0px 0px 1px var(--p-shadow-from-dim-light); --p-top-bar-shadow:0 2px 2px -1px var(--p-shadow-from-direct-light); --p-button-drop-shadow:0 1px 0 rgba(0, 0, 0, 0.05); --p-button-inner-shadow:inset 0 -1px 0 rgba(0, 0, 0, 0.2); --p-button-pressed-inner-shadow:inset 0 1px 0 rgba(0, 0, 0, 0.15); --p-override-none:none; --p-override-transparent:transparent; --p-override-one:1; --p-override-visible:visible; --p-override-zero:0; --p-override-loading-z-index:514; --p-button-font-weight:500; --p-non-null-content:''; --p-choice-size:2rem; --p-icon-size:1rem; --p-choice-margin:0.1rem; --p-control-border-width:0.2rem; --p-banner-border-default:inset 0 0.1rem 0 0 var(--p-border-neutral-subdued), inset 0 0 0 0.1rem var(--p-border-neutral-subdued); --p-banner-border-success:inset 0 0.1rem 0 0 var(--p-border-success-subdued), inset 0 0 0 0.1rem var(--p-border-success-subdued); --p-banner-border-highlight:inset 0 0.1rem 0 0 var(--p-border-highlight-subdued), inset 0 0 0 0.1rem var(--p-border-highlight-subdued); --p-banner-border-warning:inset 0 0.1rem 0 0 var(--p-border-warning-subdued), inset 0 0 0 0.1rem var(--p-border-warning-subdued); --p-banner-border-critical:inset 0 0.1rem 0 0 var(--p-border-critical-subdued), inset 0 0 0 0.1rem var(--p-border-critical-subdued); --p-badge-mix-blend-mode:luminosity; --p-thin-border-subdued:0.1rem solid var(--p-border-subdued); --p-text-field-spinner-offset:0.2rem; --p-text-field-focus-ring-offset:-0.4rem; --p-text-field-focus-ring-border-radius:0.7rem; --p-button-group-item-spacing:-0.1rem; --p-duration-1-0-0:100ms; --p-duration-1-5-0:150ms; --p-ease-in:cubic-bezier(0.5, 0.1, 1, 1); --p-ease:cubic-bezier(0.4, 0.22, 0.28, 1); --p-range-slider-thumb-size-base:1.6rem; --p-range-slider-thumb-size-active:2.4rem; --p-range-slider-thumb-scale:1.5; --p-badge-font-weight:400; --p-frame-offset:0px;"
>
<input type="hidden" value="<?php echo $store ?>" id="store">
<input type="hidden" value="<?php echo $mainobj->store_id; ?>" id="store_id">
    <div class="Polaris-Layout">
   <?php
      if($current_page == 'invoiceDetails.php'){
        include("navigation.php");
        echo "<script> const AjaxCallFrom = 'frontendAjaxCall';</script>";
   ?>
<div class="sd_invoice_header">
     <div class="">
    <div class="Polaris-Select">
    <select id="" class="Polaris-Select__Input sd_select_invoice_action" aria-invalid="false">
      <option value="More Actions">More Actions</option>
      <option value="Print">Print</option>
      <option value="Download" data-orderId="<?php echo $_GET['order_id']; ?>">Download</option>
      <option value="Mail" data-customerEmail="<?php echo $_GET['customer_email']; ?>" data-orderId="<?php echo $_GET['order_id']; ?>">Mail</option>
    </select>
    <div class="Polaris-Select__Content" aria-hidden="true">
      <span class="Polaris-Select__SelectedOption">More Actions</span>
      <span class="Polaris-Select__Icon">
        <span class="Polaris-Icon">
          <span class="Polaris-Text--root Polaris-Text--visuallyHidden">
          </span>
          <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
            <path d="M10.884 4.323a1.25 1.25 0 0 0-1.768 0l-2.646 2.647a.75.75 0 0 0 1.06 1.06l2.47-2.47 2.47 2.47a.75.75 0 1 0 1.06-1.06l-2.646-2.647Z">
            </path>
            <path d="m13.53 13.03-2.646 2.647a1.25 1.25 0 0 1-1.768 0l-2.646-2.647a.75.75 0 0 1 1.06-1.06l2.47 2.47 2.47-2.47a.75.75 0 0 1 1.06 1.06Z">
            </path>
          </svg>
        </span>
      </span>
    </div>
    <div class="Polaris-Select__Backdrop">
    </div>
  </div>
</div>
    <div class="formbox">
        <div class="plan-heading">
          <h3>Invoice</h3>
        </div>
    </div>
    <div class="Polaris-Page-Header__BreadcrumbWrapper">
        <nav role="navigation">
            <a class="Polaris-Breadcrumbs__Breadcrumb" href="subscriptionOrders.php?shop=<?php echo $mainobj->store; ?>">
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
<?php } ?>
<div class="main-invoice-table">
    <!-- Header -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable">
        <tbody><tr>
            <td height="20"></td>
        </tr>
        <tr>
            <td>
                <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff" style="">
                    <tbody>
                        <tr class="hiddenMobile">
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
                                        <td>
                                            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="left" class="col">
                                                <tbody>
                                                    <?php if($show_logo == '1'){ ?>
                                                        <tr>
                                                            <td align="left"> <img src="<?php echo $mainobj->SHOPIFY_DOMAIN_URL; ?>/application/assets/images/invoice/logo/<?php echo $logo; ?>" width="170" height="50" alt="logo" border="0"></td>
                                                        </tr>
                                                        <tr class="hiddenMobile">
                                                            <td height="40"></td>
                                                        </tr>
                                                    <?php } ?>
                                                    <tr class="visibleMobile">
                                                        <td height="20"></td>
                                                    </tr>
                                                    <?php if($show_billing_information == '1'){ ?>
                                                        <tr>
                                                            <td style="font-size: 11px; font-family: 'Poppins', sans-serif; color: #000; vertical-align: top; ">
                                                                <strong><span class="editModeOff"><?php echo $billing_information_text; ?></span>
                                                                </strong>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="100%" height="5"></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #5b5b5b; vertical-align: top; ">
                                                                <?php echo $billing_firstname.' '.$billing_lastname.'<br>';
                                                                    echo $billing_address1.'<br>';
                                                                    echo $billing_city.', '.$billing_province.'<br>';
                                                                    echo $billing_zip.'<br>';
                                                                    echo $billing_country.'<br>';
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                                    </td>
                                                    <td>
                                            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="right" class="col">
                                                <tbody>
                                                    <tr class="visibleMobile">
                                                        <td height="20"></td>
                                                    </tr>
                                                    <tr>
                                                        <td height="5"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-size: 12px; color: #5b5b5b; font-family: 'Poppins', sans-serif; vertical-align: top; text-align: right;">
                                                            <?php if($show_company_name == '1'){ ?><strong style="color: #000; "><?php echo $company_name; ?></strong><br><?php } ?>
                                                            <?php if($show_email == '1'){ ?><strong style="color: #000;"> <?php echo $email_text; ?> : </strong><?php echo $email_value; ?><br><?php } ?>
                                                            <?php if($show_phone_number == '1'){ ?><strong style="color: #000;"><?php echo $phone_number_text ?> : </strong><?php echo $phone_number_value; ?><?php } ?>
                                                        </td>
                                                    </tr>
                                                    <tr class="hiddenMobile">
                                                        <td height="50"></td>
                                                    </tr>
                                                    <tr class="visibleMobile">
                                                        <td height="20"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-size: 12px; color: #5b5b5b; font-family: 'Poppins', sans-serif; vertical-align: top; text-align: right;">
                                                            <?php if($show_invoice_number == '1'){ ?><small><?php echo $invoice_number_text ?> </small><?php echo $invoice_number_prefix.''.$order_number; ?><br><?php } ?>
                                                            <?php if($show_invoice_date == '1'){ ?><small><?php echo $mainobj->getShopTimezoneDate($order_date,$mainobj->shop_timezone); ?></small><?php } ?>
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
                </tbody>
               </table>
            </td>
        </tr>
    </tbody></table>
    <!-- /Header -->
    <!-- Order Details -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable">
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
                                                    <?php echo $serial_number_text; ?>
                                                </th>
                                                <th style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #fff; font-weight: normal; line-height: 1; vertical-align: top; padding: 10px;" align="left"  width="52%">
                                                    <small><?php echo $item_text; ?></small>
                                                </th>
                                                <th style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #fff; font-weight: normal; line-height: 1; vertical-align: top; padding: 10px 0 10px 0;" align="center">
                                                   <?php echo $quantity_text; ?>
                                                </th>
                                                <th style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #fff; font-weight: normal; line-height: 1; vertical-align: top; padding: 10px;" align="right">
                                                   <?php echo $item_subtotal_text; ?>
                                                </th>
                                            </tr>
                                            <tr>
                                                <td height="1" style="background: #bebebe;" colspan="4"></td>
                                            </tr>
                                            <tr>
                                                <td height="10" colspan="4"></td>
                                            </tr>
                                            <?php foreach($order_details_data['lineItems']['edges'] as $key=>$value){
                                                $variant_title = '';
                                                if($value['node']['variantTitle']){
                                                   $variant_title = ' -'.$value['node']['variantTitle'];
                                                }
                                                ?>
                                                <tr>
                                                <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000; line-height: 18px;  vertical-align: top; padding:10px;" class="article">
                                                   <?php echo $key + 1; ?>
                                                </td>
                                                <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000; font-weight: 600;  line-height: 18px;  vertical-align: top; padding:10px;" class="article">
                                                   <?php echo $value['node']['title'].''.$variant_title; ?>
                                                </td>
                                                <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000; line-height: 18px;  vertical-align: top; padding:10px;" class="article">
                                                   <?php echo $value['node']['quantity']; ?>
                                                </td>
                                                <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000; line-height: 18px;  vertical-align: top; padding:10px;" align="right" class="article">
                                                    <?php echo $currency_symbol.''.$value['node']['originalUnitPriceSet']['presentmentMoney']['amount']; ?>
                                                </td>
                                                </tr>
                                            <?php } ?>
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
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable">
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
                                            <?php if($show_subtotal == '1'){ ?>
                                                <tr>
                                                    <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                                    <?php echo $subtotal_text; ?>
                                                    </td>
                                                    <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000; line-height: 22px; padding-right: 10px; vertical-align: top; text-align:right; white-space:nowrap;" width="80">
                                                    <?php echo $currency_symbol.''.$item_subtotal; ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if($show_shipping == '1'){ ?>
                                                <tr>
                                                    <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                                        <?php echo $shipping_text; ?>
                                                    </td>
                                                    <td style="font-size: 12px; font-family: 'Poppins', sans-serif; padding-right: 10px; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                                        <?php echo $currency_symbol.''.$shipping_price; ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <?php if($show_tax == '1'){ ?>
                                            <tr>
                                                <td style="font-size: 12px;font-family: 'Poppins', sans-serif;color: #000000;line-height: 22px;vertical-align: top;text-align:right;">
                                                    <small><?php echo $tax_text; ?></small></td>
                                                <td style="font-size: 12px;font-family: 'Poppins', sans-serif;padding-right: 10px;color: #000000;line-height: 22px;vertical-align: top;text-align:right;">
                                                    <small> <?php echo $currency_symbol.''.$tax; ?></small>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            <?php if($show_discount == '1'){ ?>
                                            <tr>
                                                <td style="font-size: 12px;font-family: 'Poppins', sans-serif;color: #000000;line-height: 22px;vertical-align: top;text-align:right;">
                                                    <small><?php echo $discount_text; ?></small></td>
                                                <td style="font-size: 12px;font-family: 'Poppins', sans-serif;padding-right: 10px;color: #000000;line-height: 22px;vertical-align: top;text-align:right;">
                                                    <small> <?php echo $currency_symbol.''.$total_discount; ?></small>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            <tr>
                                                <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                                    <strong><?php echo $total_text; ?></strong>
                                                </td>
                                                <td style="font-size: 12px; font-family: 'Poppins', sans-serif; padding-right: 10px; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                                    <strong><?php echo $currency_symbol.''.$grand_total; ?></strong>
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
    <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable">
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
                            <?php if($show_terms_conditions == '1'){ ?>
                            <tr>
                                <td>
                                    <table width="550" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <table width="270" border="0" cellpadding="0" cellspacing="0" align="left" class="col">

                                                        <tbody>
                                                            <tr>
                                                                <td style="font-size: 11px; font-family: 'Poppins', sans-serif; color: #000; line-height: 1; vertical-align: top; ">
                                                                    <strong><?php echo $terms_conditions_text; ?></strong>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="100%" height="10"></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-size: 12px; font-family: 'Poppins', sans-serif; color: #5b5b5b; line-height: 20px; vertical-align: top; ">
                                                                <?php echo $terms_conditions_value; ?>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <?php } if($show_signature == '1'){ ?>
                                                <td>
                                                    <table width="200" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
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
                                                                                <td style="font-size: 11px;font-family: 'Poppins', sans-serif;color: #000;line-height: 1;vertical-align: top;text-align: right;">
                                                                                    <img src="<?php echo $mainobj->SHOPIFY_DOMAIN_URL; ?>/application/assets/images/invoice/signature/<?php echo $signature; ?>" width="170" height="50" alt="logo" border="0">
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
                                        </tbody>
                                    </table>
                                </td>
                                <?php } ?>
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

</div>
    <?php
      if($current_page == 'invoiceDetails.php'){
        include("footer.php");
      }
    ?>



