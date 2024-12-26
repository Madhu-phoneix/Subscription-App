/**=======================================================  Global Variables define Start =====================================================**/
let sd_subscription_selected_products = [];
let sd_subscription_edit_case_already_selected_products_array = [];
let sd_frequency_card_serialno = 0;
let sd_subscription_edit_case_already_existing_plans_array = {};
let sd_subscription_edit_case_already_existing_plans_array_validation_check = {};
let sd_subscription_edit_case_already_selected_products = [];
let sd_subscription_edit_case_to_be_deleted_plans_array = [];
let sd_subscription_edit_case_to_be_added_new_plans_array = [];
var to_check_existing_plan_array;
let to_check_new_plan_array = [];
let sd_frequency_plans_array = [];
let db_edit_subscriptionplan_id,initial_group_name;
let redirect_link_global,currency,theme_customizer_enable,check_Selling_plan,SHOPIFY_PAYMENT_REQUIREMENT,href_link,weekDaysArray,sd_step_form_html;
let redirect_query_string = '';
let delete_button_disable = '';
let show_enteries = true;
// let restrict_format = /[!@#$%^*()+\=\[\]{};:"\\|,.<>\/?]+/;
let restrict_format = /[`~!@#$%^&*_|+\-=?;:",<>{}\[\]\\\/]/gi;
let params = new URLSearchParams(window.location.search);
SHOPIFY_DOMAIN_URL = 'https://your-domain.com';
form_type='',hidden_selling_plan_name = 'No';
// const SHOPIFY_SECRET = document.getElementById("SHOPIFY_SECRET").value;
const store_id = document.getElementById("store_id").value;
store = document.getElementById("store").value;

let ajaxurl = SHOPIFY_DOMAIN_URL+"/application/controller/ajaxhandler.php?store="+store;
if(AjaxCallFrom == 'backendAjaxCall'){
    currency = document.getElementById("SHOPIFY_CURRENCY").value;
    const currency_code = document.getElementById("SHOPIFY_CURRENCY_CODE").value;
    theme_customizer_enable = document.getElementById('theme_customizer_app').value;
    check_Selling_plan = document.getElementById('CHECK_SELLING_PLAN').value;
    SHOPIFY_PAYMENT_REQUIREMENT = document.getElementById('SHOPIFY_PAYMENT_REQUIREMENT').value;
    // limit_amount_exceed = document.getElementById('sd_amount_exceed').value;
    limit_amount_exceed = Number(document.getElementById('sd_amount_exceed').value) || 0;


    $.getScript("https://cdn.shopify.com/s/javascripts/currencies.js", function(){
       return;
    }).then(()=>{
        var total_contract_sale = 0;
        $.each(contract_sale, function( key, value ) {
            var currency_amount = Currency.convert(value.total_sale,value.contract_currency,currency);
            total_contract_sale = total_contract_sale + currency_amount;
        });

        // if(total_contract_sale >= limit_amount_exceed){ //old code condition
        if(total_contract_sale > limit_amount_exceed){
            var totl_contract_sale = 'You have reached your subscription sale limit';
            var add_progress_class = 'progress2';
            var progress_value = '100';
        }else{
            console.log('else')

            var totl_contract_sale = `Subscription Sale : <span class="sd_progress_amount"><span class="sd_total_sales">`+total_contract_sale+`</span>/`+limit_amount_exceed+'';
            var add_progress_class = 'progress1';
            var progress_value = total_contract_sale/limit_amount_exceed * 100;
        }
        var percentage_value = 300 * progress_value/100;

        var sd_top_sidebar = `<div class="progress-section" data-aos="fade-left" data-aos-once="true">
            <div class="task-progress">
                <p>`+totl_contract_sale+`</p>
                <div class="sd-progress-bar">
                    <span><progress class="progress `+add_progress_class+`" max="100" value="`+progress_value+`"></progress></span>
                    <span class="sd_upgrade_plan"><b><a href="`+SHOPIFY_DOMAIN_URL+`/admin/app_plans.php?shop=`+store+`" data-polaris-unstyled="true">Upgrade Plan</a></b></span>
                    <div class="progress-bar-anim" style="width:`+percentage_value+`px;"></div>
                </div>
            </div>
        </div>
        </div>`;
        jQuery('.upgrade_plan_topbar').html(sd_top_sidebar);
        jQuery('.sd_total_sales').html(currency_code+''+total_contract_sale.toFixed(2));
    });
    var Currency = {
        convert: function(amount, from, to) {
            return (amount * this.rates[from]) / this.rates[to];
        }
    }
    jQuery("body").on('click', ".select_plan_view", async function(e) {
        var selected_plan_view = jQuery(this).attr('plan-view');
        jQuery('.select_plan_view').removeClass('sd_selectedList');
        jQuery('#sd_'+selected_plan_view+'_view').addClass('sd_selectedList');
        if(selected_plan_view == 'grid'){
            jQuery('.sd_plan_grid_view').removeClass('display-hide-label');
            jQuery('.sd_plan_list_view').addClass('display-hide-label');
        }else if(selected_plan_view == 'list'){
          jQuery('.sd_plan_grid_view').addClass('display-hide-label');
          jQuery('.sd_plan_list_view').removeClass('display-hide-label');
        }
        let whereconditionvalues = {
            "store": store
            }
        subscription_plan_view = {
            'subscription_plans_view': selected_plan_view
        }
        let ajaxParameters = {
            method: "POST",
            dataValues: {
                table: 'install',
                data_values: subscription_plan_view,
                wherecondition: whereconditionvalues,
                wheremode: 'and',
                action: "insertupdateajax"
            }
        };
        try {
            let result = await AjaxCall(ajaxParameters);
        } catch (e) {
            console.log(e);
        }
    });

 href_link = 'https://' + store + '/admin/themes/current/editor?context=apps';
 weekDaysArray = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

 function sd_step_form_return(app_status,selling_plan_status,shopify_payment_status){
    sd_step_form_html = `
    <div class="sd_step_1">
        <div class="Polaris-Modal-Header">
            <div id="Polarismodal-header2" class="Polaris-Modal-Header__Title">
                <h2 class="Polaris-DisplayText Polaris-DisplayText--sizeSmall">Steps To Configure</h2>
            </div>
        </div>
        <div p-color-scheme="light" class="sd_step_form">
            <div class="Polaris-Card">
                <div class="Polaris-Card__Section">
                    <div class="Polaris-Layout" style="flex-wrap:initial;">
                        <div class="Polaris-Layout__Section">
                            <div class="">
                                <div class="Polaris-Card__Header">
                                    <h2 class="Polaris-Heading"><span style="color:red;">Step 1.</span>  Enable App</h2>
                                </div>
                                <div class="Polaris-Card__Section">
                                    `;
                                    if (app_status == 'true') {
                                    sd_step_form_html += `
                                    <div class="sd_done_step">
                                        <span class="step-tick"><img src="` + SHOPIFY_DOMAIN_URL + `/application/assets/images/TickMinor.svg"></span>
                                        <p class="step_completed">Setup Completed!!</p>
                                    </div>
                                    `;
                                    } else {
                                    sd_step_form_html += `
                                    <p>1. From your Shopify admin, go to <b>Online Store > Themes</b>.</p>
                                    <p>2. Find the theme that you want to edit, and then click <b>Customize</b>.</p>
                                    <p>3. Click <b>Theme settings</b>.</p>
                                    <p>4. Click the <b>App embeds</b> tab.</p>
                                    <p>5. Select the app embed that you want to activate or click the <b>Search</b> bar and enter a search term to search through your installed apps.</p>
                                    <p>6. Beside the app embed that you want to activate, click the toggle to activate it.</p>
                                    <span><a class="Polaris-Button Polaris-Button--primary" type="button" href="` + href_link + `">Enable</a></span>`;
                                    }
                                    sd_step_form_html += `
                                    <p style="color:red;">Note: If your theme is supported with edition 2.O, you must include a subscription widget block.</p>
                                    To learn how to add a block, <a href="` + SHOPIFY_DOMAIN_URL + `/admin/video_tutorials.php?shop=`+store+`" target="_blank">Click here</a>
                                </div>
                            </div>
                        </div>
                        <div class="Polaris-Layout__Section">
                            <div class="">
                                <div class="Polaris-Card__Section">
                                    <p><img src="` + SHOPIFY_DOMAIN_URL + `/application/assets/images/app_enable.png"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sd_step_required"><span style="color:red;">Note: This step is necessary to add the product widget</span>
                        <button id="skip_theme_enable_step" class="Polaris-Button Polaris-Button--plain skip_step sd_float_right" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">${app_status == 'true' ? 'Next' : 'Next'}</span></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sd_step_2 display-hide-label">
        <div class="Polaris-Modal-Header">
            <div id="Polarismodal-header2" class="Polaris-Modal-Header__Title">
                <h2 class="Polaris-DisplayText Polaris-DisplayText--sizeSmall">Steps To Configure</h2>
            </div>
        </div>
        <div p-color-scheme="light" class="sd_step_form">
            <div class="Polaris-Card">
                <div class="Polaris-Card__Section">
                    <div class="Polaris-Layout" style="flex-wrap:initial;">
                        <div class="Polaris-Layout__Section">
                            <div class="">
                                <div class="Polaris-Card__Header">
                                    <h2 class="Polaris-Heading"><span style="color:red;">Step 2.</span>Shopify Payments/Paypal/Stripe</h2>
                                </div>
                                <div class="Polaris-Card__Section">
                                    `;
                                    if (shopify_payment_status == 1) {
                                    sd_step_form_html += `<span class="step-tick"><img src="` + SHOPIFY_DOMAIN_URL + `/application/assets/images/TickMinor.svg"></span>
                                    <p class="step_completed">Setup Completed!! </p>
                                    `;
                                    } else {
                                    sd_step_form_html += `
                                    <p>Most importantly, you must activate Shopify payments in order for subscriptions to function on product pages according to Shopify default requirements.</p>
                                    <p>(Settings->Payments)</p>
                                    <span><a class="Polaris-Button Polaris-Button--primary" type="button" href="https://` + store + `/admin/settings/payments/shopify-payments">Enable</a></span>
                                    `;
                                    }
                                    sd_step_form_html += `
                                </div>
                            </div>
                        </div>
                        <div class="Polaris-Layout__Section">
                            <div class="">
                                <div class="Polaris-Card__Section">
                                    <p><img src="` + SHOPIFY_DOMAIN_URL + `/application/assets/images/shopify_payment.png"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sd_step_required"><span style="color:red;">Note: This step is necessary to add the product widget</span>
                        <button id="skip_payment_enable_step" class="Polaris-Button Polaris-Button--plain skip_step sd_float_right" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text">${shopify_payment_status == 1 ? 'Next' : 'Skip'}</span></span></button>
                        <button class="Polaris-Button Polaris-Button--plain sd_float_right prev_step" show-data="sd_step_1" hide-data="sd_step_2" type="button">Prev</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sd_step_3 display-hide-label">
        <div class="Polaris-Modal-Header">
            <div id="Polarismodal-header2" class="Polaris-Modal-Header__Title">
                <h2 class="Polaris-DisplayText Polaris-DisplayText--sizeSmall">Steps To Configure</h2>
            </div>
            <button class="Polaris-Modal-CloseButton" aria-label="Close">
                <span class="Polaris-Icon Polaris-Icon--colorBase Polaris-Icon--applyColor">
                    <span class="Polaris-VisuallyHidden"></span>
                    <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                        <path d="m11.414 10 6.293-6.293a1 1 0 1 0-1.414-1.414l-6.293 6.293-6.293-6.293a1 1 0 0 0-1.414 1.414l6.293 6.293-6.293 6.293a1 1 0 1 0 1.414 1.414l6.293-6.293 6.293 6.293a.998.998 0 0 0 1.707-.707.999.999 0 0 0-.293-.707l-6.293-6.293z"></path>
                    </svg>
                </span>
            </button>
        </div>
        <div p-color-scheme="light" class="sd_step_form">
            <div class="Polaris-Card">
                <div class="Polaris-Card__Section">
                    <div class="Polaris-Layout" style="flex-wrap:initial;">
                        <div class="Polaris-Layout__Section">
                            <div class="Polaris-Card__Header">
                                <h2 class="Polaris-Heading"><span style="color:red;">Step 3.</span>  Create Subscription Plan</h2>
                            </div>
                            <div class="">
                                <div class="Polaris-Card__Section">
                                    `;
                                    if (selling_plan_status != '0') {
                                    sd_step_form_html += `
                                    <div class="sd_done_step">
                                        <span class="step-tick"><img src="` + SHOPIFY_DOMAIN_URL + `/application/assets/images/TickMinor.svg"></span>
                                        <p class="step_completed">Setup Completed!!</p>
                                    </div>
                                    `;
                                    } else {
                                    sd_step_form_html += `
                                    <p>Add Selling plans to the product by creating the subscription plan.
                                    </p>
                                    <span><a class="Polaris-Button Polaris-Button--primary" type="button" href="` + SHOPIFY_DOMAIN_URL + `/admin/plans/subscription_group.php?shop=` + store + `">Create</a></span>`;
                                    }
                                    sd_step_form_html += `
                                </div>
                            </div>
                        </div>
                        <div class="Polaris-Layout__Section">
                            <div class="">
                                <div class="Polaris-Card__Section">
                                    <p><img src="` + SHOPIFY_DOMAIN_URL + `/application/assets/images/product_widget.png"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="sd_step_required">
                            <button class="Polaris-Button Polaris-Button--plain Polaris-Modal-CloseButton sd_float_right" aria-label="Close" type="button">Done</button>
                            <button class="Polaris-Button Polaris-Button--plain sd_float_right prev_step" show-data="sd_step_2" hide-data="sd_step_3" type="button">Prev</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `;
    return sd_step_form_html;
}
}
/**=======================================================  Global Variables define End =====================================================**/
function AjaxCall(data) {
    return new Promise(function(Resolve, Reject) {
        jQuery.ajax({
            url: ajaxurl,
            type: data.method,
            data: data.dataValues,
            dataType: "json",
            success: function(result) {
                let json = jQuery.parseJSON(JSON.stringify(result));
                Resolve(json);
            },
            error: function(response) {
            }
        });
    });
}

$(window).on('load', function() {
    //    jQuery("#sd_subscriptionLoader").html('<img src="'+SHOPIFY_DOMAIN_URL+'/application/assets/images/subscription_loader.gif">');
    /**======================================================= query string Start ==========================================================**/
    if (params.has('search_subscription_group_name') && params.get('search_subscription_group_name') != '') {
        let search_name = params.get('search_subscription_group_name');
        jQuery('#search-subscription-text').val(search_name);
        jQuery('#subscription-list-search').submit();
    }
    if (params.has('search_contract') && params.get('search_contract') != '') {
        let search_name = params.get('search_contract');
        jQuery('.dataTables_filter input').val(search_name);
        var table = $('#subscriptionTable').DataTable();
        table.search(search_name).draw();
    }
    if (params.has('themeConfiguration') && params.get('themeConfiguration') == 'Yes') {
        jQuery("#sd_global_modal_container").html(sd_step_form_html);
        jQuery("#sd_popup").removeClass("display-hide-label");
    }
});

if((window.location.pathname).includes('/apps/your-subscriptions')){
    document.body.classList.add('sd_frontend_subscriptions');
        show_enteries = false;
}

$(document).ready(function() {

    jQuery("#sd_subscriptionLoader").addClass('display-hide-label');

    /**===================================================== DataTable Initialization Start =====================================================**/
    const subscriptionTable = jQuery('#subscriptionTable').dataTable({
        "bSort": false,
        "bLengthChange": show_enteries,
    });
    var subscription_plan_table = jQuery('#subscription_plan_table').dataTable({
        "bSort": false,
        "bLengthChange": show_enteries,
    });
    jQuery('#subscriptionTable').wrap('<div class="sd_subscription_table"></div>');
    const customerContractCountTable = jQuery('#customerContractCountTable').dataTable({
        "bSort": false
    });
    const subscription_order_table = jQuery('#subscription_order_table').dataTable({
        "bSort": false,
        "bLengthChange": show_enteries,
    });

    const customerContractDetailTable = jQuery('#customerContractDetailTable').dataTable({
        "bSort": false
    });

    /**====================================================  DataTable Initialization End ===================================================**/

    /**====================================================  Error Messages Start ======================================================**/
    const app_disable_confirmbox_message = "Are you sure you want to Disable The app? This will stop the Subscription functionality in the storefront.";
    const subscription_delete_confirm_message = "This can't be undone . Are you sure you want to delete this plan?";
    /**================================================  Error Messages End =========================================================**/
    /**=======================================================  Functions Start ==========================================================**/

    // function to add small spinner
    function add_small_spinner() {
        small_spinner_html = '<span class="Polaris-Spinner Polaris-Spinner--sizeSmall"><svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M7.229 1.173a9.25 9.25 0 1011.655 11.412 1.25 1.25 0 10-2.4-.698 6.75 6.75 0 11-8.506-8.329 1.25 1.25 0 10-.75-2.385z"></path></svg></span><span role="status"><span class="Polaris-VisuallyHidden">Small spinner example</span></span>';
        return small_spinner_html;
    }

    //  function to check validation
    function validURL(str) {
        var urlregex = /^(https?|ftp):\/\/([a-zA-Z0-9.-]+(:[a-zA-Z0-9.&%$-]+)*@)*((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9][0-9]?)(\.(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}|([a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(:[0-9]+)*(\/($|[a-zA-Z0-9.,?'\\+&%$#=~_-]+))*$/;
        return urlregex.test(str);
    }


    function subscription_search() {
        reset_plan_name_change();
     //   list_close_all_mini();
        let search_keyword = (jQuery('#search-subscription-text').val()).trim().toLowerCase();
        if (search_keyword.length) {
            displayMessage('Searching', 'waiting');
            let matchCase = 0;
            jQuery(".subscription-list-card").each(function() {
                    search_type_data = (jQuery(this).attr("data-search-planname")).trim().toLowerCase();
                if (search_type_data.indexOf(search_keyword) != -1) {
                    matchCase++;
                    jQuery(this).removeClass('display-hide-label');
                } else {
                    jQuery(this).addClass('display-hide-label');
                }
            });
            close_toast();
            if ($("#plan-no-list")[0]){
                jQuery("#plan-no-list").addClass('display-hide-label');
            }else{
            }
            if (matchCase == 0) {
                //show no serach result
                jQuery("#no_search_result").removeClass('display-hide-label');

            }
        } else {
            //show All cards
            list_subscription_mode();
            if(params.has('search_subscription_group_name') && params.get('search_subscription_group_name') != ''){
                window.history.pushState({}, document.title, "/" + "Subscription/application/admin/SubscriptionPlan/subscription_group.php?shop="+store);
            }
            if ($("#plan-no-list")[0]){
               jQuery("#plan-no-list").removeClass('display-hide-label');
            }
        }
    }

    function delaySearch(callback, ms) {
        var timer = 0;
        return function() {
            var context = this,
                args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function() {
                callback.apply(context, args);
            }, ms || 0);
        };
    }

    function setLocalStorage(key, value) {
        localStorage.setItem(key, value);
    }


    // function check_subscription_whole_form_change() {
    //     if (form_type == 'edit') {
    //         delete picker_selection_checkboxes.product_handle;
    //         delete picker_selection_checkboxes.price;
    //         delete picker_selection_checkboxes.quantity;
    //         let checkproductupdate = JSON.stringify(sd_subscription_edit_case_already_selected_products) == JSON.stringify(picker_selection_checkboxes);
    //         let check_existing_plan_change = JSON.stringify(sd_subscription_edit_case_already_existing_plans_array) == JSON.stringify(sd_subscription_edit_case_already_existing_plans_array_validation_check);
    //         group_name = document.querySelectorAll('#edit_subscription_wrapper .subscription_plan_name')[0].value; //check if group name changed
    //         if (Object.keys(sd_subscription_edit_case_to_be_deleted_plans_array).length != 0 ||
    //             Object.keys(sd_subscription_edit_case_to_be_added_new_plans_array).length != 0 || checkproductupdate == false || check_existing_plan_change == false || (initial_group_name != group_name && group_name != '')) {
    //             return true;
    //         } else {
    //             return false;
    //         }
    //     }else if(form_type == 'create'){
    //         if (Object.keys(picker_selection_checkboxes).length != 0 || Object.keys(sd_frequency_plans_array).length != 0 || plan_group_name != ''){
    //             return true;
    //         } else {
    //             return false;
    //         }
    //     }
    // }

    function clear_edit_screen_subscriptionPlan() {
        jQuery('#edit_subscription_wrapper .sd_subscription_heading').html('');
        jQuery('#edit_subscription_wrapper .change_plan_name').attr("plan-name-value", '');
        jQuery('#edit_subscription_wrapper .change_plan_name').attr("subscription-group-id", '');
        jQuery('edit_subscription_wrapper .save_plan_name').attr("subscription-group-id", '');
        jQuery('.edit_total_products').html('');
        jQuery('.edit_total_selling_plans').html('');
        jQuery('.sd-frequency-plan-card-wrapper').html('');
        jQuery('#subscription_edit_prodcts').html('');
        jQuery("button[target-tab=subscription_edit_schemes]").addClass('Polaris-Tabs__Tab--selected');
        jQuery("#subscription_edit_schemes").removeClass('Polaris-Tabs__Panel--hidden');
        jQuery("button[target-tab=subscription_edit_products]").removeClass('Polaris-Tabs__Tab--selected');
        jQuery("#subscription_edit_products").addClass('Polaris-Tabs__Panel--hidden');
    }

    function step2_preps(caseType) {

        if (caseType == 'edit') {
            jQuery('.step2_button_submit').html('Update');
        } else if (caseType == 'add') {
            reset_form("sd-subscription-frequency-form"); // reset form for new entry
            jQuery('.step2_button_submit').html('Add');
        }

        if (jQuery('.sd-frequency-plan-card')[0]) {
            jQuery('.step2_button_cancel').removeClass('display-hide-label');
            jQuery('.step2_button_previous').addClass('display-hide-label');
        } else {
            jQuery('.step2_button_cancel').addClass('display-hide-label');
            jQuery('.step2_button_previous').removeClass('display-hide-label');
        }

        done_step('1');
        active_step('2');
        unfinshed_step('3');
        jQuery("#frequency_plan_name").focus();
    }

    function step3_preps() {
        done_step('1');
        done_step('2');
        active_step('3');
    }

    function active_step(step) {
        jQuery('#li_step' + step).addClass('active');
        jQuery('#li_step' + step).removeClass('done');
        jQuery('#li_step' + step + ' .step-tick').addClass('display-hide-label');
        jQuery('#li_step' + step + ' .step-number').removeClass('display-hide-label');
        //for tab content
        jQuery('.form-steps-tab').addClass('display-hide-label');
        jQuery('.subscription-create-step' + step).removeClass('display-hide-label');
    }

    function done_step(step) {
        jQuery('#li_step' + step).removeClass('active');
        jQuery('#li_step' + step).addClass('done');
        jQuery('#li_step' + step + ' .step-tick').removeClass('display-hide-label');
        jQuery('#li_step' + step + ' .step-number').addClass('display-hide-label');
    }

    function unfinshed_step(step) {
        jQuery('#li_step' + step).removeClass('active');
        jQuery('#li_step' + step).removeClass('done');
        jQuery('#li_step' + step + ' .step-tick').addClass('display-hide-label');
        jQuery('#li_step' + step + ' .step-number').removeClass('display-hide-label');
    }

    function edit_subscription_popup_close() {
        reset_form("create-selling-plan-form"); // reset form to empty fields
        jQuery(".subscription-create-step2").append(jQuery(".create-selling-plan-form"));
        close_sd_popup();
    }

    async function subscription_form_submission(submission_mode, edit_case_data) {
        let subscription_plan_name;
        if (form_type == 'edit') { //new added code
            subscription_plan_name = $('#edit_subscription_wrapper .sd_subscription_heading').text();
        } else {
            subscription_plan_name = $('#subscription_plan_name').val();
        }
        let insertdatavalues = {
            "store_id": store_id,
            "product_ids": JSON.stringify(picker_selection_checkboxes),
            "frequency_plan": JSON.stringify(sd_frequency_plans_array),
            "plan_name": subscription_plan_name,
            "table": "subscription_plan",
        }
        let ajaxParameters = {
            method: "POST",
            dataValues: {
                insertdata: insertdatavalues,
                action: submission_mode,
                edit_case_data: JSON.stringify(edit_case_data)
            }
        };
        try {
            let result = await AjaxCall(ajaxParameters);
            if (result['status'] == true) {
                displayMessage(result['message'], 'success');
                if (form_type == 'create') {
                    unfinshed_step('1');
                    unfinshed_step('2');
                    unfinshed_step('3');
                    var created_group_id = result['selling_plan_group_id'];
                    var table = jQuery('#subscription_plan_table').DataTable();
                    var newRow = table.row.add([result['selling_plan_list_card_array']['plan_name'],result['selling_plan_list_card_array']['total_selling_plan_count'],Object.keys(result['selling_plan_list_card_array']['product_id_array']).length,'<button subscription-group-id="'+result['selling_plan_list_card_array']['selling_plan_group_id']+'" class="Polaris-Button  edit-subscription-group" type="button" data-type="edit"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="10px"><path d="M14.846 1.403l3.752 3.753.625-.626A2.653 2.653 0 0015.471.778l-.625.625zm2.029 5.472l-3.752-3.753L1.218 15.028 0 19.998l4.97-1.217L16.875 6.875z" fill="#5C5F62"></path></svg></button><button class="Polaris-Button delete_subscription_plan"  subscription-group-id="'+result['selling_plan_list_card_array']['selling_plan_group_id']+'" type="button"><span class="Polaris-Button__Content"><span class="Polaris-Button__Text"><svg width="20" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M14 4h3a1 1 0 011 1v1H2V5a1 1 0 011-1h3V1.5A1.5 1.5 0 017.5 0h5A1.5 1.5 0 0114 1.5V4zM8 2v2h4V2H8zM3 8h14v10.5a1.5 1.5 0 01-1.5 1.5h-11A1.5 1.5 0 013 18.5V8zm4 3H5v6h2v-6zm4 0H9v6h2v-6zm2 0h2v6h-2v-6z" fill="#5C5F62"/></svg></span></span></button>']).draw(false);
                    var newRowNode = newRow.node();
                    jQuery(newRowNode).insertBefore(table.row(0).node());
                    jQuery('.subscription-list-start').prepend(result['list_card_html']);
                } else {
                    $("div#subscription_list_" + result['selling_plan_group_id']).replaceWith(result['list_card_html']);
                }
                reset_plan_name_change();
                list_subscription_mode();
                jQuery('#plan-no-list').addClass('display-hide-label');
                remove_all_error_messages();
                jQuery('#sd_productSpinner').removeClass('display-hide-label');
            } else {
                jQuery('.save-subscription-plan').removeClass('btn-disable-loader');
                if (result['message'] == 'Some of the selected product does not exist in the shopify store') {
                    let error_message = JSON.parse(result['error']);
                    $.each(error_message, function(index, value) {
                        let variant_id = value['message'].split('/').pop().split(' does not exist')[0];
                        document.getElementById("display_product_" + variant_id).style.border = "1px solid red";
                    });
                    $('.go-to-step1').click();
                    displayMessage(result['message'], 'error');
                } else if (result['message'] == 'mutation execution error') {
                    displayMessage('Product limit exceeded, remove some products', 'error');
                } else {
                    displayMessage(result['message'], 'error');
                    location.reload();
                }
            }
        } catch (e) {
            displayMessage(e, 'error');
        }
    }

    function form_serializeObject(formid) { //new code updated on 20 March 2
        var data = {};
        var dataArray = jQuery('body').find("#" + formid).serializeArray();
        for (var i = 0; i < dataArray.length; i++) {
            var pattern = /\{\{\s*(.*?)\s*\}\}/g;
            var new_value = (dataArray[i].value).replace(pattern, '{{$1}}');// for the email temmplates to remove the spaces between {{ }}
            if (dataArray[i].name != '') {
                if (formid == 'frmProductSave') {
                    data[dataArray[i].name] = dataArray[i].value;
                } else {
                    data[dataArray[i].name] = $.trim(escapeCodeToHtml_template(new_value));
                }
            }
        }
        return data;
    }

    function reset_form(parentclass) {
        jQuery("." + parentclass).find("input[type=text],input[type=hidden],input[type=number],textarea").val("");
        jQuery("." + parentclass).find("input[type=checkbox]").each(function() {
            if (this.checked) {
                // $("." + parentclass).find("input[type=checkbox]").click(); // Unchecks it
                $(this).prop('checked', false);
            }
        });
        change_select_html('#sd_per_delivery_order_frequency_type', 'DAY');
        change_select_html('#sd_prepaid_billing_type', 'DAY');
        change_select_html('#sd_anchor_option', 'On Purchase Day');
        jQuery('.Polaris-Labelled__Error,.sd_subscription_discount_offer_wrapper,.sd_subscription_discount_offer_after_wrapper,.sd_change_discount_after,.edit-subscription-buttons,.sd_anchor_option,.sd_anchor_week_day,.sd_anchor_month_day,.cut_off_days,.sd_set_anchor_date,.subscription-create-step1 .show_selected_products').addClass('display-hide-label');
    }

    function check_obj_key_value(object, name, type) {
        let edit_frequency_card_serial_no = jQuery('#edit_frequency_card_serial_no').val();
        let sellingplanid = jQuery('#sellingplanid').val();
        let selling_plan_same_name_exist = 'No';
        const {
            length
        } = object;
        const id = length + 1;
        const found = object.some(function(el, key) {
            search_selling_plan_name = 'true';
            if (edit_frequency_card_serial_no != '' && sellingplanid != '') {
                if (el.sellingplanid == sellingplanid) {
                    search_selling_plan_name = 'false';
                }
            } else if (edit_frequency_card_serial_no != '') {
                if (form_type == 'edit') {
                    index_value = jQuery('#sd_frequency_card_serialno_' + edit_frequency_card_serial_no).attr('attr-edit-case-new-plan-array-index');
                    if (key == parseInt(index_value)) {
                        search_selling_plan_name = 'false';
                    }
                } else if (form_type == 'create') {
                    if (key == parseInt(edit_frequency_card_serial_no)) {
                        search_selling_plan_name = 'false';
                    }
                }
            }
            if (search_selling_plan_name == 'true') {
                if ($.trim((el[type]).toLowerCase()) === $.trim(name.toLowerCase())) {
                    selling_plan_same_name_exist = 'Yes';
                }
            }
        });
        if (selling_plan_same_name_exist == 'Yes') {
            return false;
        } else {
            return true;
        }
    }

    function scrollToTop(scroollDivclassName) {
        jQuery(scroollDivclassName).animate({
            scrollTop: 0
        }, "slow");
    }

    function scrollToBottom(scroollDivclassName) {
        setTimeout(function() {
            $(scroollDivclassName).scrollTop($(scroollDivclassName)[0].scrollHeight);
        }, 50);
    }

    //to check if value of the key is already exist in array or not
    function userExists(array, keyValue, keyName) {
        return array.some(function(el) {
            return el[keyName] === keyValue;
        });
    }

    function subscription_frequency_validation() {
        let error_values = {};
        // first remove all error messages
        jQuery('body').find('.frequency-plan-error').addClass('display-hide-label');
        let frequency_plan_name = jQuery('#frequency_plan_name').val();
        if (form_type == 'edit') {
            form_id = 'subscription_plan_edit_form';
            var combine_array = Object.values(to_check_existing_plan_array).concat(to_check_new_plan_array);
            check_name_exist = userExists(combine_array, frequency_plan_name, 'frequency_plan_name'); // true
            if (check_name_exist) {
                error_values["same_frequency_plan_name"] = "My Plan can't have selling plans with same name";
                jQuery('.same_frequency_plan_name_error').removeClass('display-hide-label');
                scrollToTop('.sd_selling_form');
            }
        } else {
            form_id = 'subscription_plan_form';
            let check_name_exist = check_obj_key_value(sd_frequency_plans_array, frequency_plan_name, 'frequency_plan_name');
            if (!check_name_exist) {
                error_values["same_frequency_plan_name"] = "My Plan can't have selling plans with same name";
                jQuery('.same_frequency_plan_name_error').removeClass('display-hide-label');
                scrollToTop('.sd_selling_form');
            }
        }
        // return false;
        let subscription_plan_form_data = form_serializeObject(form_id); //pass only id in parameter
        //new selling plan name
        //selling plan name on cart page
        prepaid_delivery = '';
        if (subscription_plan_form_data.frequency_plan_type == "Prepaid") {
            prepaid_delivery = subscription_plan_form_data.prepaid_billing_value+ ' ' +  titleCase(subscription_plan_form_data.per_delivery_order_frequency_type) + '(s) prepaid subscription,';
        }
        delivery_every = titleCase(subscription_plan_form_data.per_delivery_order_frequency_type);
        if (subscription_plan_form_data.per_delivery_order_frequency_value != 1) {
            delivery_every = subscription_plan_form_data.per_delivery_order_frequency_value + ' ' + titleCase(subscription_plan_form_data.per_delivery_order_frequency_type);
        }
        selling_plan_name = $.trim(prepaid_delivery + ' Delivery Every ' + delivery_every);
        if (form_id == 'subscription_plan_edit_form') {
            check_name_exist = userExists(combine_array, selling_plan_name, 'selling_plan_name');
            if (check_name_exist) {
                error_values["selling_plan_same_error"] = "No two plan can have same value of delivery and billing policy";
                jQuery('.selling_plan_same_error').removeClass('display-hide-label');
                scrollToTop('.sd_selling_form');
            }
        } else if (form_id == 'subscription_plan_form') {
            let check_Selling_planName = check_obj_key_value(sd_frequency_plans_array, selling_plan_name, 'selling_plan_name');
            if (!check_Selling_planName) {
                error_values["selling_plan_same_error"] = "No two plan can have same value of delivery and billing policy";
                jQuery('.selling_plan_same_error').removeClass('display-hide-label');
                scrollToTop('.sd_selling_form');
            }
        }
        for (const [key, value] of Object.entries(subscription_plan_form_data)) {
            if (key == "subscription_plan_name" || key == "sellingplanid") {
                continue;
            }
            if (key == "prepaid_billing_value") {
                if (subscription_plan_form_data.frequency_plan_type == "Pay Per Delivery") {
                    continue;
                }
            }
            if (key == "subscription_discount_value") {
                if (!(subscription_plan_form_data.hasOwnProperty("subscription_discount"))) {
                    continue;
                }
                let selected_discount_value = $('#subscription_discount_type').find(":selected").text();
                if (($.trim(value) > 100) && selected_discount_value == 'Percent Off(%)') {
                    error_values["discount_value_greater"] = "Discount Value can't be greater than 100";
                    jQuery('.subscription_discount_value_greater_error').removeClass('display-hide-label');
                    scrollToBottom('.sd_selling_form');
                } else {
                    jQuery('.subscription_discount_value_greater_error').addClass('display-hide-label');
                }
            }
            if (key == 'sd_anchor_month_day') {
                if ((!(subscription_plan_form_data.hasOwnProperty("sd_set_anchor_date"))) || (subscription_plan_form_data.sd_anchor_option == 'On Purchase Day' || subscription_plan_form_data.per_delivery_order_frequency_type == 'DAY' || subscription_plan_form_data.per_delivery_order_frequency_type == 'WEEK')) {
                    continue;
                } else {
                    if (parseInt(subscription_plan_form_data.sd_anchor_month_day) > 31) {
                        jQuery('.invalid_month_anchor_error').removeClass('display-hide-label');
                        error_values['invalid_month_anchor_error'] = 'Month date should be between 1 to 31';
                    }
                }
            }
            // chnage discount after validation
            if (key == "discount_value_after") {
                if (!(subscription_plan_form_data.hasOwnProperty("subscription_discount_after"))) {
                    continue;
                }
                let selected_discount_value_after = $('#subscription_discount_type_after').find(":selected").text();
                if (($.trim(value) > 100) && selected_discount_value_after == 'Percent Off(%)') {
                    error_values["discount_value_greater_after"] = "Discount Value can't be greater than 100";
                    jQuery('.subscription_discount_value_greater_after_error').removeClass('display-hide-label');
                    scrollToBottom('.sd_selling_form');
                } else {
                    jQuery('.subscription_discount_value_greater_after_error').addClass('display-hide-label');
                }
                if ((subscription_plan_form_data.discount_value_after == subscription_plan_form_data.subscription_discount_value) && (subscription_plan_form_data.subscription_discount_type == subscription_plan_form_data.subscription_discount_type_after)) {
                    error_values["discount_value_same_after"] = "After Discount value can't be same as discount value";
                    jQuery('.discount_value_same_after_error').removeClass('display-hide-label');
                    scrollToBottom('.sd_selling_form');
                } else {
                    jQuery('.discount_value_same_after_error').addClass('display-hide-label');
                }
            }

            if (key == "change_discount_after_cycle") {
                if (!(subscription_plan_form_data.hasOwnProperty("subscription_discount_after"))) {
                    continue;
                }
                if ($.trim(value) == '') {
                    error_values["change_discount_after_cycle"] = "Enter discount change after cycle value";
                    jQuery('.change_discount_after_cycle_error').removeClass('display-hide-label');
                    scrollToBottom('.sd_selling_form');
                } else {
                    jQuery('.change_discount_after_cycle_error').addClass('display-hide-label');
                }
            }

            if (key == "per_delivery_order_frequency_value" || key == "prepaid_billing_value") {
                jQuery('.prepaid_billing_value_zero_error').addClass('display-hide-label');
                if (subscription_plan_form_data.per_delivery_order_frequency_value <= 0 && parseInt(subscription_plan_form_data.per_delivery_order_frequency_value.length) > 0) {
                    error_values["prepaid_billing_value_zero"] = "Order Frequency should be greater than zero";
                    jQuery('.prepaid_billing_value_zero_error').removeClass('display-hide-label');
                    scrollToTop('.sd_selling_form');
                } else if (parseInt(subscription_plan_form_data.prepaid_billing_value.length) > 0 && parseInt(subscription_plan_form_data.per_delivery_order_frequency_value.length) > 0) {
                    if (parseInt(subscription_plan_form_data.per_delivery_order_frequency_value) > parseInt(subscription_plan_form_data.prepaid_billing_value) || parseInt(subscription_plan_form_data.per_delivery_order_frequency_value) == parseInt(subscription_plan_form_data.prepaid_billing_value)) {
                        error_values["fullfillment_value_greater"] = "Fullfillment Value greater than billing value";
                        jQuery('.prepaid_billing_value_rule_error').removeClass('display-hide-label');
                        scrollToTop('.sd_selling_form');
                    } else {
                        jQuery('.prepaid_billing_value_rule_error').addClass('display-hide-label');
                    }

                    if (!((parseInt(subscription_plan_form_data.prepaid_billing_value) % parseInt(subscription_plan_form_data.per_delivery_order_frequency_value)) == 0)) {
                        error_values["fullfillment_value_greater"] = "Billing Value not multiple of fullfillment value";
                        jQuery('.prepaid_billing_value_multiple_error').removeClass('display-hide-label');
                        scrollToTop('.sd_selling_form');
                    } else {
                        jQuery('.prepaid_billing_value_multiple_error').addClass('display-hide-label');
                    }
                }
            }
            if (key == 'maximum_number_cycle') {
                if (parseInt(subscription_plan_form_data.maximum_number_cycle) < parseInt(subscription_plan_form_data.minimum_number_cycle)) {
                    error_values["maximum_number_cycle_validation_error"] = "The maximum number of cycles must be greater than the minimum.";
                    jQuery('.maximum_number_cycle_validation_error').removeClass('display-hide-label');
                    scrollToTop('.sd_selling_form');
                }
            }

            if (key == 'cut_off_days' && value == '') {
                continue;
            } else if (key == 'cut_off_days' && value != '') {
                if ((parseInt(value) > 7) && subscription_plan_form_data.per_delivery_order_frequency_type == 'WEEK') {
                    error_values['sd_cut_off_week_error'] = 'Week cut off value should not be greater than 7';
                    jQuery('.sd_cut_off_week_error').removeClass('display-hide-label');
                    scrollToTop('.sd_selling_form');
                } else if ((parseInt(value) > 30) && subscription_plan_form_data.per_delivery_order_frequency_type == 'MONTH') {
                    error_values['sd_cut_off_month_error'] = 'Month cut off value should not be greater than 30';
                    jQuery('.sd_cut_off_month_error').removeClass('display-hide-label');
                    scrollToTop('.sd_selling_form');
                }
            }

            if (key == 'minimum_number_cycle' || key == 'cut_off_days' || key == 'sd_description' || key == 'maximum_number_cycle') {} else {
                if (!($.trim(value).length)) {
                    error_values[key] = value;
                    if (key == 'frequency_plan_name' || key == 'per_delivery_order_frequency_value' || key == 'prepaid_billing_value') {
                        scrollToTop('.sd_selling_form');
                    } else {
                        scrollToBottom('.sd_selling_form');
                    }
                    jQuery('.' + key + "_error").removeClass('display-hide-label');
                } else {
                    jQuery('.' + key + "_error").addClass('display-hide-label');
                }
            }
        }
        if (Object.keys(error_values).length == 0) {
            return "true";
        } else {
            return "false";
        }
    }

    // delete of frequency plan for new and edit of whole subscription plan is handle through this function
   // delete of frequency plan for new and edit of whole subscription plan is handle through this function
   async function delete_frequency_card(card_serial_no, sellingplanid, new_card_no) {
    let delete_plan = 'No';
    // return false;
    let parent_element;
    if (db_edit_subscriptionplan_id.length) {
        parent_element = 'edit_subscription_wrapper';
        // edit whole subscription plan case
        if((Object.keys(sd_subscription_edit_case_already_existing_plans_array).length > 1) || (sd_subscription_edit_case_to_be_added_new_plans_array.length > 0)){
            delete_plan = 'Yes';
        if (sellingplanid.length) { // deleting one of the already existing
                displayMessage('Deleting...', 'waiting');
                let delete_selling_plan_array = {
                    'selling_plan_id' : sellingplanid,
                    'subscription_group_id' : db_edit_subscriptionplan_id
                };
                let ajaxParameters = {
                    method: "POST",
                    dataValues: {
                        data:delete_selling_plan_array,
                        action: "delete_selling_plan"
                    }
                };
                try {
                    let result = await AjaxCall(ajaxParameters);
                    if(result.status == true){
                        delete sd_subscription_edit_case_already_existing_plans_array[sellingplanid];
                        sd_subscription_edit_case_to_be_deleted_plans_array.push(sellingplanid);
                       displayMessage('Selling Plan deleted successfully','success');
                       delete_plan = 'Yes';
                    }else if(result.status == false){
                        if((result.message).includes('Selling plan') && (result.message).includes('does not exist')){
                            displayMessage('Selling Plan does not exist','error');
                        }else if(result.message == 'Atleast one plan should be in the group'){
                            displayMessage(result.message,'error');
                            location.reload();
                        }else{
                            displayMessage(result.message,'error');
                        }
                        delete_plan = 'No';
                    }
                } catch (e) {}
                jQuery("#sd_subscriptionLoader").addClass('display-hide-label');
            }else { // deleting one of the newly add
                sd_subscription_edit_case_to_be_added_new_plans_array.splice(new_card_no, 1);
                jQuery("#sd_subscriptionLoader").addClass('display-hide-label');
            }
        }
    } else {
        parent_element = 'create_subscription_wrapper';
        // add new whole subscription plan case
        let frequency_plan_type = sd_frequency_plans_array[card_serial_no].frequency_plan_type;
        let preview_select_id;
        if (frequency_plan_type == "Pay Per Delivery") {
            preview_select_id = "sd_ppd_list";
        } else if (frequency_plan_type == "Prepaid") {
            preview_select_id = "sd_prepaid_list";
        }
        sd_frequency_plans_array.splice(card_serial_no, 1);
        jQuery("#" + preview_select_id + " option[value='" + card_serial_no + "']").remove();
        jQuery("#sd_subscriptionLoader").addClass('display-hide-label');
    }
    if ((delete_plan == 'Yes') || (parent_element == 'create_subscription_wrapper') ){
        jQuery('#' + parent_element).find("#sd_frequency_card_serialno_" + card_serial_no).remove();
        sd_frequency_card_serialno--;
        create_plan_preview();
        let attr_edit_case_new_plan_array_index = 0;
        //after deletion re-arrange the cards - start
        jQuery('#' + parent_element).find('.sd-frequency-plan-card').each(function(i, obj) {
            if (jQuery(this).hasClass("edit-case-new-plan")) {
                // edit whole subscription plan case
                jQuery(this).attr("attr-edit-case-new-plan-array-index", attr_edit_case_new_plan_array_index);
                jQuery(this).find(".delete_frequency_card").attr("attr-edit-case-new-plan-array-index", attr_edit_case_new_plan_array_index);
                jQuery(this).find(".delete_frequency_card_cancel").attr("attr-edit-case-new-plan-array-index", attr_edit_case_new_plan_array_index);
                jQuery(this).find(".delete_frequency_card_ask_confirmation").attr("attr-edit-case-new-plan-array-index", attr_edit_case_new_plan_array_index);
                jQuery(this).find(".edit_frequency_card").attr("attr-edit-case-new-plan-array-index", attr_edit_case_new_plan_array_index);
                attr_edit_case_new_plan_array_index++;
            }
            jQuery(this).attr("id", "sd_frequency_card_serialno_" + i);
            jQuery(this).find(".delete_frequency_card").attr("attr-card-serial-no", i);
            jQuery(this).find(".delete_frequency_card_cancel").attr("attr-card-serial-no", i);
            jQuery(this).find(".delete_frequency_card_ask_confirmation").attr("attr-card-serial-no", i);
            jQuery(this).find(".edit_frequency_card").attr("attr-card-serial-no", i);
        });
        //after deletion re-arrange the cards - end

        reset_form("sd-subscription-frequency-form"); // reset form for new entry
        //jQuery(".edit-frequency-cancel-button").trigger('click'); // reset form for false entry like someone may first click edit & then delete same plan from frequency schemes display
    }else{
        $(".delete_frequency_card_ask_confirmation").prop('disabled', true);
        displayMessage('Atleast one plan should be in the group','error');
    }
}

    // edit & new creation of frequency plan for both new and edit of whole subscription plan is handle through this function
    function create_frequency_plan_card(edit_frequency_card_serial_no, db_edit_subscriptionplan_id, sellingplanid, new_card_no) {
        jQuery('.add-least-frequency-error').addClass('display-hide-label');
        let parent_element, frequencyType, card_class, subscription_plan_form_data, card_serial_no, case_type, edit_case_new_card = "attr-edit-case-new-plan-array-index='" + new_card_no + "'";
        if (db_edit_subscriptionplan_id.length) { // edit  whole subscription plan case
            parent_element = 'edit_subscription_wrapper';
            subscription_plan_form_data = form_serializeObject("subscription_plan_edit_form"); //pass only id in parameter
            if (sellingplanid.length) { //already existing edit frequency case
                card_class = "edit-case-already-plan";
                case_type = "update";
                checkexistingplanschange = true;
            } else {
                card_class = "edit-case-new-plan";
                if (edit_frequency_card_serial_no.length) { //new add edit frequency case
                    case_type = "update";
                } else { //new add  frequency case
                    case_type = "add";
                    edit_case_new_card = "attr-edit-case-new-plan-array-index='" + sd_subscription_edit_case_to_be_added_new_plans_array.length + "'";
                }
            }
        } else { // add whole subscription plan case
            card_class = "create-case-plan";
            parent_element = 'create_subscription_wrapper';
            subscription_plan_form_data = form_serializeObject("subscription_plan_form"); //pass only id in parameter
            if (edit_frequency_card_serial_no.length) { //edit frequency case
                case_type = "update";
                //card_class = "create-case-plan";
            } else { //new frequency case
                case_type = "add";
            }
        }

        if (case_type == "add") {
            card_serial_no = sd_frequency_card_serialno;
        } else {
            card_serial_no = edit_frequency_card_serial_no;
            jQuery("#sd_frequency_card_serialno_" + card_serial_no).remove(); // to remove in case of update only so that at same place new data can be inserted
        }
        let delivery_every = subscription_plan_form_data.per_delivery_order_frequency_value + ` ` + subscription_plan_form_data.per_delivery_order_frequency_type;
        let billing_period = subscription_plan_form_data.prepaid_billing_value + ` ` + subscription_plan_form_data.per_delivery_order_frequency_type;
        let discount = subscription_plan_form_data.subscription_discount_value + ` ` + subscription_plan_form_data.subscription_discount_type;
        let discount_after = subscription_plan_form_data.discount_value_after + ` ` + subscription_plan_form_data.subscription_discount_type_after;



        var html_frequency_plan_card = `<div ` + edit_case_new_card + ` id="sd_frequency_card_serialno_` + card_serial_no + `" class="Polaris-Layout__Section Polaris-Layout__Section--oneThird sd-frequency-plan-card ` + card_class + `">
                <div class="Polaris-Card1">
                     <div class="Polaris-Card__Section sd_frequency_section">
                        <div class="Polaris-Card__SectionHeader">
                           <div class="Polaris-Stack Polaris-Stack--alignmentBaseline sd_frequency_stack">
                              <div class="Polaris-Stack__Item Polaris-Stack__Item--fill sd_frequencyPlans">
                                 <h3 aria-label="Contact Information" class="Polaris-Subheading">` + subscription_plan_form_data.frequency_plan_type + `</h3>
                              </div>
                           </div>
                        </div>
                        <ul class="Polaris-List">
                           <li class="Polaris-List__Item"><b>Selling Plan Name</b>  ` + subscription_plan_form_data.frequency_plan_name + `</li>`;

        html_frequency_plan_card += `<li class="Polaris-List__Item"><b>Delivery Every</b> ` + delivery_every + `</li>`;
        prepaid_delivery = '';
        if (subscription_plan_form_data.frequency_plan_type == "Prepaid") {
            html_frequency_plan_card += `<li class="Polaris-List__Item"><b>Billing Period</b> ` + billing_period + `</li>`;
            prepaid_delivery =  subscription_plan_form_data.prepaid_billing_value + ' ' + titleCase(subscription_plan_form_data.per_delivery_order_frequency_type) + '(s) prepaid subscription,';
        }
        //selling plan name on cart page
        delivery_every = titleCase(subscription_plan_form_data.per_delivery_order_frequency_type);
        if (subscription_plan_form_data.per_delivery_order_frequency_value != 1) {
            delivery_every = subscription_plan_form_data.per_delivery_order_frequency_value + ' ' + titleCase(subscription_plan_form_data.per_delivery_order_frequency_type);
        }
        selling_plan_name = $.trim(prepaid_delivery + ' Delivery Every ' + delivery_every);
        if ((subscription_plan_form_data.hasOwnProperty("subscription_discount"))) {
            html_frequency_plan_card += `<li class="Polaris-List__Item"><b>Discount</b> ` + discount + `</li>`;
        }
        subscription_plan_form_data['selling_plan_name'] = selling_plan_name;
        //chnage discount after cycle
        if ((subscription_plan_form_data.hasOwnProperty("subscription_discount_after"))) {
            html_frequency_plan_card += `<li class="Polaris-List__Item"><b>After ` + subscription_plan_form_data.change_discount_after_cycle + ` cycle </b> ` + discount_after + `</li>`;
        }


        html_frequency_plan_card += `</ul></div><div class="Polaris-Stack__Item sd-frequency-plan-actions">
							   <div class="Polaris-ButtonGroup display-hide-label frequency-delete-confirmation">
									<span class="frequency_delete_confirm_message_span">Are you sure to delete?</span>
									<div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain">
									<button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain delete_frequency_card" type="button" ` + edit_case_new_card + ` attr-card-serial-no="` + card_serial_no + `" attr-id="` + sellingplanid + `" >Yes</button>
									</div>
									<div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain">
									<button class="Polaris-Button Polaris-Button--plain delete_frequency_card_cancel" type="button" ` + edit_case_new_card + ` attr-card-serial-no="` + card_serial_no + `" attr-id="` + sellingplanid + `" >No</button>
									</div>
                                 </div>
                                 <div class="Polaris-ButtonGroup frequency-card-actions">
                                    <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain"><button class="remove-btn Polaris-Button Polaris-Button--destructive Polaris-Button--plain delete_frequency_card_ask_confirmation" attr-card-serial-no="` + card_serial_no + `" attr-id="` + sellingplanid + `" type="button">Delete</button></div>
                                    <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain"><button class="update-btn edit_frequency_card Polaris-Button Polaris-Button--plain" type="button" ` + edit_case_new_card + ` attr-card-serial-no="` + card_serial_no + `" attr-id="` + sellingplanid + `">Edit</button></div></div>
                                 </div>
                              </div></div>`;
        let remove_class_params = {
            class_elements: [{
                name: "sd-frequency-schemes",
                classname: "display-hide-label"
            }]
        };
        remove_class(remove_class_params); // remove hide class from section:frequency card wrapper where all selling plans are listed.
        if (case_type == "add") { //new add frequency case
            jQuery('#' + parent_element).find('.sd-frequency-plan-card-wrapper').append(html_frequency_plan_card);
        } else {
            //edit frequency case
            jQuery('#' + parent_element).find('#sd_frequency_card_serialno_' + card_serial_no).remove();
            if (card_serial_no == 0) {
                jQuery('#' + parent_element).find('.sd-frequency-plan-card-wrapper').prepend(html_frequency_plan_card);
            } else {
                let before_elements = parseInt(card_serial_no) - parseInt(1);
                jQuery('#' + parent_element).find("#sd_frequency_card_serialno_" + before_elements).after(html_frequency_plan_card);
            }
        }

        if (db_edit_subscriptionplan_id.length) { // edit whole subscription plan case
            if (sellingplanid.length) { //already existing edit frequency case
                sd_subscription_edit_case_already_existing_plans_array[sellingplanid] = subscription_plan_form_data;
            } else {
                if (edit_frequency_card_serial_no.length) { // new add edit frequency case
                    //first delete index  & insert new at same index from object
                    sd_subscription_edit_case_to_be_added_new_plans_array[new_card_no] = subscription_plan_form_data;
                } else { // add new frequency case

                    sd_subscription_edit_case_to_be_added_new_plans_array.push(subscription_plan_form_data); // add object in whole array of frequency plans
                    sd_frequency_card_serialno++; // increment the number of card for the next move once current is done
                }
            }
            edit_subscription_popup_close();
        } else { // add new whole subscription plan case

            if (edit_frequency_card_serial_no.length) { // edit frequency case
                //first delete index  & insert new at same index from object  in case of edit
                sd_frequency_plans_array.splice(card_serial_no, 1, subscription_plan_form_data);
            } else { // add new frequency index in array
                sd_frequency_plans_array[card_serial_no] = subscription_plan_form_data; // add object in whole array of frequency plans
                sd_frequency_card_serialno++; // increment the number of card for the next move once current is done
            }
            create_plan_preview();
        }
        jQuery('#sd_add_frequency_span').html('Add Frequency');
        jQuery('#sd_add_frequency').removeClass('UpdateFrequency');
        jQuery('.edit_frequency_button_group').addClass('display-hide-label');
        reset_form("sd-subscription-frequency-form"); // reset form for new entry
    }

    function create_plan_preview() {
        // empty preview of both select
        jQuery('.sd_select').html('');
        jQuery('.sd_discount_ribbon').html('');
        jQuery.each(sd_frequency_plans_array, function(key, value) {
            let preview_select_html = '';
            let preview_select_id = '';
            let display_discount_final = '';
            let display_type_text = '';
            let delivery_every = value.per_delivery_order_frequency_value + ` ` + value.per_delivery_order_frequency_type;
            let billing_period = value.prepaid_billing_value + ` ` + value.per_delivery_order_frequency_type;
            if ((value.prepaid_billing_value).length > 0) {
                prepaid_billing_value = value.prepaid_billing_value;
            } else {
                prepaid_billing_value = value.per_delivery_order_frequency_value;
            }
            if (value.subscription_discount == 'on') {
                if (value.subscription_discount_type == 'Percent Off(%)') {
                    display_type_text = '%';
                    display_discount_final = `Save ` + (value.subscription_discount_value) + ` ` + display_type_text;
                } else {
                    display_type_text = currency;
                    display_discount_final = `Save ` + ((value.subscription_discount_value) / (prepaid_billing_value / value.per_delivery_order_frequency_value)) + ` ` + display_type_text;
                }
            }

            if (value.frequency_plan_type == "Pay Per Delivery") {
                preview_select_id = "sd_ppd_list";
                preview_select_html = `<option attr-type="ppd" discount="` + display_discount_final + `" per-quantity-price="$80">` + delivery_every + `</option>`;
            } else if (value.frequency_plan_type == "Prepaid") {
                preview_select_id = "sd_prepaid_list";
                preview_select_html = `<option attr-type="prepaid" discount="` + display_discount_final + `" per-quantity-price="$60">` + billing_period + ` , Delivery Every ` + delivery_every + `</option>`;
            }
            jQuery('#' + preview_select_id).append(preview_select_html);
        });

        let prepaid_discount_first_display = jQuery("#sd_prepaid_list option:first").attr('discount');
        let ppd_discount_first_display = jQuery("#sd_ppd_list option:first").attr('discount');
        jQuery('#ppd_discount').html(ppd_discount_first_display);
        jQuery('#prepaid_discount').html(prepaid_discount_first_display);
    }


    function subscription_wholeplan_validation(form_type) {
        let error_values = {},
            check_atleast = true;
        if (form_type == 'create') {
            if (sd_frequency_plans_array.length == 0) {
                check_atleast = false;
            }
        } else if (form_type == 'edit') {
            if (Object.keys(sd_subscription_edit_case_already_existing_plans_array) == 0 && sd_subscription_edit_case_to_be_added_new_plans_array.length == 0) {
                check_atleast = false;
            }
            if (Object.keys(picker_selection_checkboxes).length == 0) {
                error_values["subscription_add_product_error"] = "No Product Selected";
                jQuery(".subscription_add_product_error").removeClass('display-hide-label');
                if (form_type == 'edit') {
                    //open the product tab when no product added
                    jQuery('.Polaris-Tabs__Tab').removeClass('Polaris-Tabs__Tab--selected');
                    jQuery("[target-tab=subscription_edit_products]").addClass('Polaris-Tabs__Tab--selected');
                    jQuery('.subscription-edit-tabs').addClass('Polaris-Tabs__Panel--hidden');
                    jQuery('#subscription_edit_products').removeClass('Polaris-Tabs__Panel--hidden');
                }
            } else {
                jQuery(".subscription_add_product_error").addClass('display-hide-label');
            }
        }
        if (check_atleast) {
            jQuery('body').find('.add-least-frequency-error').addClass('display-hide-label');
            //jQuery('.subscription_validate_step2').addClass('display-hide-label');
            //jQuery('.subscription_cancel_step2').removeClass('display-hide-label');

        } else {
            error_values["frequency_plan_creation_error"] = "No plan created";
            //open the selling plan tab when no selling plan exist
            jQuery('.Polaris-Tabs__Tab').removeClass('Polaris-Tabs__Tab--selected');
            jQuery("[target-tab=subscription_edit_schemes]").addClass('Polaris-Tabs__Tab--selected');
            jQuery('.subscription-edit-tabs').addClass('Polaris-Tabs__Panel--hidden');
            jQuery('#subscription_edit_schemes').removeClass('Polaris-Tabs__Panel--hidden');
            jQuery('body').find('.add-least-frequency-error').removeClass('display-hide-label');

        }

        if (Object.keys(error_values).length == 0) {
            return "true";
        } else {
            return "false";
        }
    }

   //email template js start
    jQuery("body").on('click', '.edit-template', function(e) {  // new update in march 2023
        var redirect_to_file = jQuery(this).attr('data-template');
        // redirect_query_string = '&template_name='+redirect_to_file+'_template';
        // redirect_link_global = 'settings/email_templates/edit_email_template.php';
        // redirectlink();
        var fullURL = `/admin/settings/email_templates/edit_email_template.php?shop=`+store+`&template_name=`+redirect_to_file+`_template`;
        open(fullURL, '_self');
    });
    jQuery('body').on('input','#send_test_email', function(){ //new code updated on 16 march
        var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        var check_email = jQuery(this).val();
        if(check_email.match(mailformat)){
            jQuery('#test_email_error').text('');
            jQuery('.sd_confirm_button').removeClass('btn-disable-loader');
        }else{
            jQuery('#test_email_error').text('Enter Valid email');
            jQuery('.sd_confirm_button').addClass('btn-disable-loader');
        }
    });

    jQuery("body").on('click', '.send_test_email', function() {  // new update on 17 march 2023
        var usecase = 'send_email_template';
        var usecaseid = jQuery(this).attr('data-template');
        var heading = 'Send Test Mail';
        var message = `<div class="">
        <div class="Polaris-Labelled__LabelWrapper">
          <div class="Polaris-Label">
            <label id="PolarisTextField1Label" for="PolarisTextField1" class="Polaris-Label__Text">Send to email</label>
          </div>
        </div>
        <div class="Polaris-Connected">
          <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
            <div class="Polaris-TextField Polaris-TextField--hasValue">
              <input id="send_test_email" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField1Label" aria-invalid="false" value="">
              <div class="Polaris-TextField__Backdrop"></div>
            </div>
          </div>
        </div>
        <span class="Polaris-InlineError" id="test_email_error"></span>
      </div>`;
        var acceptbuttontext = 'Send';
        var acceptbuttontype = 'Polaris-Button--primary';
        var rejectbuttontext = 'Cancel';
        document.getElementById("sd_global_modal_container").innerHTML = confirmBoxModal(usecase,usecaseid,heading,message,acceptbuttontext,acceptbuttontype,rejectbuttontext)
    });

    jQuery('body').on('input','.Editor-editor', function(){     // new update in march 2023
        var content_div = jQuery(this).attr('data-id');
        var content_html = jQuery(this).html();
        var replace_values = {
            sd_:"",
            _view:"",
         };
        textarea_id = content_div.replace(/sd_|_view/gi, function(matched){
           return replace_values[matched];
        });
        if(content_html.includes('<pre contenteditable="true">')){
            content_html = (content_html.replace(/^<pre contenteditable="true">|<\/pre>$/g, ""));
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = "<div>"+content_html+"</div>";
            const divElement = tempDiv.firstChild;
            const h2Element = divElement.firstChild;
            if(h2Element){
                jQuery('.'+content_div+', #'+textarea_id).html(h2Element.textContent);
            }else{
                jQuery('.'+content_div+', #'+textarea_id).html('');
            }
        }else{
            jQuery('.'+content_div+', #'+textarea_id).html(content_html);
        }
    });


    jQuery('body').on('input','.sd_default_template_text_fields', function(){  // new update in march 2023
        console.log('1111111111');
        // jQuery('.sd_logo_view, .sd_thanks_img_view').show(); reason for why we are using this
        var content_div = jQuery(this).attr('data-id');
        if(content_div == 'options_bg_color1_preview' || content_div == 'options_bg_color2_preview'){
            var gradient_color1 = $("input[name=options_bg_color1]").val();
            var gradient_color2 = $("input[name=options_bg_color2]").val();
            jQuery('.active_option_bg_color').css('background', 'linear-gradient(to right, '+gradient_color1+', '+gradient_color2+')');
        }else{
            var content_value = jQuery(this).val();
            if(jQuery(this).hasClass('sd_add_style')){
                var data_style = jQuery(this).attr('data-style');
                console.log(data_style);
                if(data_style == 'url'){
                    console.log(content_div);
                    if($.trim(content_value) != ''){
                        jQuery('.'+content_div).attr("href", content_value);
                    }else{
                        jQuery('.'+content_div).attr("href", 'https://'+store+'/account');
                    }
                }if(data_style == 'color' || data_style == 'background' || data_style == 'float' || data_style == 'border-color' || data_style == 'border-style'){
                    if(content_value == 'Center'){
                        jQuery('.'+content_div).css(data_style, '');
                        jQuery('.'+content_div).css('align-items', content_value);
                    }else{
                        jQuery('.'+content_div).css(data_style, content_value);
                    }
                }else{
                    if(data_style == 'src' && content_value == ''){
                        jQuery('.'+content_div).hide();
                    }if(data_style == 'src' && content_value != ''){
                        jQuery('.'+content_div).show();
                        jQuery('.'+content_div).attr(data_style, content_value);
                    }else{
                    jQuery('.'+content_div).attr(data_style, content_value);
                    }
                }
            }else{
                jQuery('.'+content_div).html(content_value);
            }
        }
        // document.querySelector(".border_color_preview").style.borderColor = "green purple";
    });

        // to copy the text
    jQuery("body").on('click', '.sd_copy_element', function() {
        var text = jQuery(this).attr('data-value'); //getting the text from that particular Row
        console.log(text);
        if (window.clipboardData && window.clipboardData.setData) {
            return clipboardData.setData("Text", text);
            $('.respnse').html("COPIED!");
        } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
            var textarea = document.createElement("textarea");
            textarea.textContent = text;
            textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in MS Edge.
            document.body.appendChild(textarea);
            textarea.select();
            try {
                return document.execCommand("copy");  // Security exception may be thrown by some browsers.
            } catch (ex) {
                console.warn("Copy to clipboard failed.", ex);
                return false;
            } finally {
                document.body.removeChild(textarea);
                displayMessage('Copied successfully', 'success');
            }
        }
    });

    jQuery("body").on('click', '.sd_reset_email_template', async function(e) {
        var email_template = jQuery(this).attr('data-id');
        document.getElementById("sd_global_modal_container").innerHTML = confirmBoxModal("sd_reset_template", email_template, "Alert", 'All the changes will be lost. Are you sure?', "Yes", "Polaris-Button--primary", "Cancel");
    });

    jQuery("body").on('click', '.sd_save_email_template', async function(e) {
        var email_template = jQuery(this).attr('data-id');
        var email_template_data = form_serializeObject('sd_'+email_template);
        if (email_template_data.hasOwnProperty('template_type')) {
        } else {
            email_template_data['template_type'] = 'none';
        }
        let whereconditionvalues = {
            "store_id": store_id
        }
        const keysToCheck = ['content_text', 'custom_template'];

        // // Call the function with the object and the keys
        const result = checkSpecificLinks(email_template_data, keysToCheck);
        if(result == false) {

            shopify.toast.show('Error: Contains invalid link in field', {isError: true});
            return false;
        }
        let ajaxParameters = {
            method: "POST",
            dataValues: {
                table: email_template,
                data_values: email_template_data,
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
            $('.email_template_notification').prop('checked', if_failed);
        }
    });


    jQuery("body").on('change', '.email_template_notification', async function() { // new code added on 24 feb 2023
        console.log('email template');
        var template_field = jQuery(this).attr('data-field');
        var tablename = jQuery(this).attr('data-table');
        if (this.checked) {
           if(template_field == 'show_currency' || template_field == 'show_shipping_address' || template_field == 'show_billing_address' || template_field == 'show_payment_method' || template_field == 'show_order_number' || template_field == 'show_line_items'){
            jQuery('.sd_'+template_field).removeClass('display-hide-label');
           }
           var status = '1';
           var if_failed = false;
        } else {
            if(template_field == 'show_currency' || template_field == 'show_shipping_address' || template_field == 'show_billing_address' || template_field == 'show_payment_method' || template_field == 'show_order_number' || template_field == 'show_line_items'){
                jQuery('.sd_'+template_field).addClass('display-hide-label');
            }
           var status = '0';
           var if_failed = true;
        }
        let whereconditionvalues = {
            "store_id": store_id
        }
        emailnotificationData = {};
        emailnotificationData[template_field] = status;
        emailnotificationData['store_id'] = store_id;
        let ajaxParameters = {
            method: "POST",
            dataValues: {
                table: tablename,
                data_values: emailnotificationData,
                wherecondition: whereconditionvalues,
                wheremode: 'and',
                action: "insertupdateajax"
            }
        };
        try {
            let result = await AjaxCall(ajaxParameters);
            console.log('result', result);
            if (result['status'] == true) {
                console.log('Message', result['message']);
                displayMessage(result['message'], 'success');
            }else{
                console.log('error Message', result['message']);
            }
        } catch (e) {
            displayMessage(e, 'error');
            $('.email_template_notification').prop('checked', if_failed);
        }
    });
    // new email templates settings code ends here


    function change_select_html(elementvalue, newvalue) {
        //jQuery(elementvalue+"_selected_value").html(newvalue);
        jQuery(elementvalue).parent().find(".Polaris-Select__SelectedOption").html(newvalue);
        jQuery(elementvalue + ' option').prop('selected', false);
        jQuery(elementvalue + " option[value='" + newvalue + "']").prop('selected', true);
    }

    function getEmailConfigurationSetting(email_data) {
        email_configuartion_settings = email_data.email_configuration;
        // email_settings_data = email_data.email_settings;
        // email_notification = email_data.email_notification_setting;
        // email_notification_array = {
        //     'When subscription is created': ['new_subscription_purchase', email_notification['new_subscription_purchase']],
        //     // 'Credit Card Expiring' : ['credit_card_expiring', email_notification[0]['credit_card_expiring']],
        //     'When billing attempt is unattended': ['payment_failed', email_notification['payment_failed']],
        //     'When the user declined the payment(3D Secure)': ['payment_declined', email_notification['payment_declined']],
        //     'When subscription deleted': ['subscription_cancelled', email_notification['subscription_cancelled']],
        //     'When subscription status changed': ['contract_status_changed', email_notification['contract_status_changed']],
        //     'When product is added to the subscription': ['product_added', email_notification['product_added']],
        //     'When product deleted from the subscription': ['product_deleted', email_notification['product_deleted']],
        //     'When product quantity is updated': ['quantity_updated', email_notification['quantity_updated']],
        //     'When order is skipped': ['skip_order', email_notification['skip_order']],
        //     'When billing attempted': ['manual_billing_attempt', email_notification['manual_billing_attempt']],
        //     'When upcoming fulfillment is rescheduled': ['reschedule_fulfillment', email_notification['reschedule_fulfillment']],
        //     'When shipping address is updated': ['	shipping_Address_change', email_notification['shipping_Address_change']],
        // }
        //email configuration data start
        email_host = '', username = '', from_email = '', password = '', encryption = '', port_number = '', email_enable = '', ssl_selected = '', tls_selected = '', starttls_selected = '', none_selected = '';
        if (email_configuartion_settings) {
            email_host = email_configuartion_settings.email_host;
            username = email_configuartion_settings.username;
            from_email = email_configuartion_settings.from_email;
            password = email_configuartion_settings.password;
            encryption = email_configuartion_settings.encryption;
            port_number = email_configuartion_settings.port_number;
            email_enable = email_configuartion_settings.email_enable;

            if (encryption == 'ssl') {
                ssl_selected = 'selected';
            } else if (encryption == 'tls') {
                tls_selected = 'selected';
            } else if (encryption == 'starttls') {
                starttls_selected = 'selected';
            } else if (encryption == 'none') {
                none_selected = 'selected';
            }
        }
        //email configuration data end

        //email settings data start
        // footer_text = '', logo_url = '', enable_social_links = '', facebook_link = '', twitter_link = '', linkedin_link = '', instagram_link = '';
        // if (email_settings_data) {
        //     footer_text = email_settings_data.footer_text;
        //     logo_url = email_settings_data.logo_url;
        //     facebook_link = email_settings_data.facebook_link;
        //     twitter_link = email_settings_data.twitter_link;
        //     linkedin_link = email_settings_data.linkedin_link;
        //     instagram_link = email_settings_data.instagram_link;
        //     if (email_settings_data.enable_social_link == '1') {
        //         enable_social_links = 'checked';
        //     }
        // }
        //email settings data end

        //email notification setting start

        email_configuration_data = `
        <div class="Polaris-Layout__Section sd-dashboard-page">
        <div class="Polaris-Page">
         <div class="Polaris-Page-Header">
                      <div class="Polaris-Page-Header__MainContent">
                          <div class="Polaris-Page-Header__TitleActionMenuWrapper">
                              <div>
                                  <div class="Polaris-Header-Title__TitleAndSubtitleWrapper">
                                  <div class="Polaris-Header-Title">
                                      <h1 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">Email Configuration</h1>
                                  </div>
                              </div>
                          </div>
                      </div>
                      </div>
                  </div>
                  <!-- ========================== Email SMTP Configuration ============================= -->

                  <div class="Polaris-Page__Content">
                  <div class="Polaris-Card">
                  <div class="sd_emailConfigurationTabs Polaris-Tabs__Panel" id="sd_emailConfig_content" role="tabpanel" aria-labelledby="accepts-marketing-1" tabindex="-1">
                  <div class="scroll_setting_detail Polaris-Scrollable Polaris-Scrollable--vertical Polaris-Scrollable--hasBottomShadow Polaris-Scrollable--verticalHasScrolling" data-polaris-scrollable="true">
                  <form class="form-horizontal emailform_setting" action="" name="emailform_setting" id="emailform_setting">
                  <div class="Polaris-Layout__AnnotatedSection">
                      <div class="Polaris-Layout__AnnotationWrapper">
                          <div class="Polaris-Layout__Annotation">
                              <div class="Polaris-TextContainer sd_emailInfo">
                                  <h2 class="Polaris-Heading">Gmail SMTP Configuration</h2>
                                  <div class="Polaris-Layout__AnnotationDescription">
                                      <p>Add email configuration settings to send mail from your server</p>
                                      <p>If you are using Gmail then you can set SMTP using gmail app password by going through following URL: -</p>
                                         <a target="_blank" href="https://www.gmass.co/blog/gmail-smtp/" style="font-weight:700;">Email SMTP Settings</a>
                                  </div>
                              </div>
                          </div>
                          <div class="Polaris-Layout__AnnotationContent">
                              <div class="Polaris-Card">
                                  <div class="Polaris-Card__Section">
                                      <div class="Polaris-FormLayout">
                                          <div class="Polaris-FormLayout__Items">
                                              <div class="Polaris-FormLayout__Item">
                                                  <div class="Polaris-Labelled__LabelWrapper">
                                                      <div class="Polaris-Label">
                                                          <label id="PolarisTextFieldemail_enableLabel" for="PolarisTextFieldemail_enable" class="Polaris-Label__Text">Enable custom email configuration</label>
                                                      </div>
                                                  </div>
                                                  <div class="Polaris-Connected">
                                                      <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                          <div class="Polaris-TextField">
                                                              <label class="switch">
                                                                  <input type="checkbox" name="email_enable" id="email_enable" ${email_enable}>
                                                                  <span class="slider round"></span>
                                                              </label>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                              <div class="Polaris-FormLayout__Item">
                                                  <div class="">
                                                      <div class="Polaris-Labelled__LabelWrapper">
                                                          <div class="Polaris-Label">
                                                              <label id="PolarisTextFieldemail_hostLabel" for="PolarisTextFieldemail_host" class="Polaris-Label__Text">Email host</label>
                                                          </div>
                                                      </div>
                                                      <div class="Polaris-Connected">
                                                          <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                              <div class="Polaris-TextField">
                                                                  <input id="email_host" name="email_host" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextFieldemail_hostLabel" aria-invalid="false" aria-multiline="false" value="${email_host}">
                                                                  <div class="Polaris-TextField__Backdrop"></div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                              <div class="Polaris-FormLayout__Item">
                                                  <div class="">
                                                      <div class="Polaris-Labelled__LabelWrapper">
                                                          <div class="Polaris-Label">
                                                              <label id="PolarisTextFieldemail_usernameLabel" for="PolarisTextFieldemail_username" class="Polaris-Label__Text">User name</label>
                                                          </div>
                                                      </div>
                                                      <div class="Polaris-Connected">
                                                          <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                              <div class="Polaris-TextField">
                                                                  <input id="username" name="username" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextFieldemail_usernameLabel" aria-invalid="false" aria-multiline="false" value="${username}">
                                                                  <div class="Polaris-TextField__Backdrop"></div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                              <div class="Polaris-FormLayout__Item">
                                                  <div class="">
                                                      <div class="Polaris-Labelled__LabelWrapper">
                                                          <div class="Polaris-Label">
                                                              <label id="PolarisTextFieldemail_passLabel" for="PolarisTextFieldemail_pass" class="Polaris-Label__Text">Password</label>
                                                          </div>
                                                      </div>
                                                      <div class="Polaris-Connected">
                                                          <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                              <div class="Polaris-TextField">
                                                                  <input id="password" name="password" class="Polaris-TextField__Input" type="password" aria-labelledby="PolarisTextFieldemail_passLabel" aria-invalid="false" aria-multiline="false" value="${password}">
                                                                  <div class="Polaris-TextField__Backdrop"></div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                              <div class="Polaris-FormLayout__Item">
                                                  <div class="">
                                                      <div class="Polaris-Labelled__LabelWrapper">
                                                          <div class="Polaris-Label">
                                                              <label id="PolarisTextFieldemail_fromLabel" for="PolarisTextFieldemail_from" class="Polaris-Label__Text">From name</label>
                                                          </div>
                                                      </div>
                                                      <div class="Polaris-Connected">
                                                          <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                              <div class="Polaris-TextField">
                                                                  <input id="from_email" name="from_email" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextFieldemail_fromLabel" aria-invalid="false" aria-multiline="false" value="${from_email}">
                                                                  <div class="Polaris-TextField__Backdrop"></div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                              <div class="Polaris-FormLayout__Item">
                                                  <div class="">
                                                      <div class="Polaris-Labelled__LabelWrapper">
                                                          <div class="Polaris-Label">
                                                              <label id="PolarisTextFieldemail_encryptionLabel" for="PolarisTextFieldemail_encryption" class="Polaris-Label__Text">Encryption</label>
                                                          </div>
                                                      </div>
                                                      <div class="Polaris-Connected">
                                                          <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                              <div class="Polaris-TextField">
                                                                  <div class="Polaris-Select">
                                                                      <select class="Polaris-Select__Input" name="encryption" id="encryption" aria-invalid="false">
                                                                          <option value="" disabled="">Select Encryption</option>
                                                                          <option value="none" ${none_selected}>none</option>
                                                                          <option value="ssl" ${ssl_selected}>ssl</option>
                                                                          <option value="tls" ${tls_selected}>tls</option>
                                                                          <option value="starttls" ${starttls_selected}>starttls</option>
                                                                      </select>
                                                                      <div class="Polaris-Select__Content" aria-hidden="true"><span class="Polaris-Select__SelectedOption">${encryption}</span><span class="Polaris-Select__Icon"><span class="Polaris-Icon"><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                                          <path d="M13 8l-3-3-3 3h6zm-.1 4L10 14.9 7.1 12h5.8z" fill-rule="evenodd"></path>
                                                                      </svg></span></span></div>
                                                                      <div class="Polaris-Select__Backdrop"></div>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                              <div class="Polaris-FormLayout__Item">
                                                  <div class="">
                                                      <div class="Polaris-Labelled__LabelWrapper">
                                                          <div class="Polaris-Label">
                                                              <label id="PolarisTextFieldemail_portLabel" for="PolarisTextFieldemail_port" class="Polaris-Label__Text">Port number</label>
                                                          </div>
                                                      </div>
                                                      <div class="Polaris-Connected">
                                                          <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                              <div class="Polaris-TextField">
                                                                  <input id="port_number" name="port_number" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextFieldemail_portLabel" aria-invalid="false" aria-multiline="false" value="${port_number}">
                                                                  <div class="Polaris-TextField__Backdrop"></div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>

                                          <div class="Polaris-FormLayout__Items">
                                              <div class="Polaris-FormLayout__Item">
                                                  <div class="Polaris-Label">
                                                      <div class="formui-page-actions">
                                                          <button type="submit" name="sd_button_emailsetting" id="sd_button_emailsetting" class="Polaris-Button Polaris-Button--primary">
                                                              <span class="Polaris-Button__Content"><span class="Polaris-Button__Text">Save</span></span>
                                                          </button>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                            </div>
                          </div>
                  <!-- ========================== Email SMTP Configuration  ============================= -->
                  </form>
                  </div>

              <!-- ========================== Test Email SMTP Configuration ============================= -->
              <div class="Polaris-Layout__AnnotatedSection">
                  <div class="Polaris-Layout__AnnotationWrapper">
                      <div class="Polaris-Layout__Annotation">
                          <div class="Polaris-TextContainer">
                              <h2 class="Polaris-Heading">Test SMTP Mail</h2>
                              <div class="Polaris-Layout__AnnotationDescription">
                                  <p>Please test the SMTP settings by sending a test mail.</p>
                              </div>
                          </div>
                      </div>
                      <div class="Polaris-Layout__AnnotationContent">
                          <div class="Polaris-Card">
                              <div class="Polaris-Card__Section">
                                  <div class="Polaris-FormLayout">
                                      <div class="Polaris-FormLayout__Items">
                                          <div class="Polaris-FormLayout__Item">
                                              <div class="">
                                                  <div class="Polaris-Labelled__LabelWrapper">
                                                      <div class="Polaris-Label">
                                                          <label id="PolarisTextFieldemail_toLabel" for="PolarisTextFieldemail_to" class="Polaris-Label__Text">Test mail to</label>
                                                      </div>
                                                  </div>
                                                  <div class="Polaris-Connected">
                                                      <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                                          <div class="Polaris-TextField">
                                                              <input id="email_to" name="email_to" class="Polaris-TextField__Input" type="email" aria-labelledby="PolarisTextFieldemail_toLabel" aria-invalid="false" aria-multiline="false" value="">
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
                                                  <div class="Polaris-Label">
                                                      <div class="formui-page-actions">
                                                          <input type="button" class="Polaris-Button Polaris-Button--primary" value="Send Test Mail" name="sd_testmail" id="sd_testmail">
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
              </div>
              </div>
              <!--===========================Email Notification setting start ==================== -->
              </div>
          </div>`;
        document.getElementById('sd_addSettingsHtml').innerHTML = email_configuration_data;
    }

    function getContractSettingData(contract_settings) {
        after_billing_attempt_fail_active = '', after_billing_attempt_fail_pause = '', after_product_delete_contract_active = '', after_product_delete_contract_pause = '', after_product_delete_contract_delete = '', after_product_delete_contract_delete = '', remove_product_yes = '', remove_product_no = '';
        if (contract_settings.after_product_delete_contract == 'Active') {
            after_product_delete_contract_active = 'selected';
        }
        if (contract_settings.after_product_delete_contract == 'Pause') {
            after_product_delete_contract_pause = 'selected';
        }
        if (contract_settings.after_product_delete_contract == 'Delete') {
            after_product_delete_contract_delete = 'selected';
        }
        if (contract_settings.remove_product == 'No') {
            remove_product_no = 'selected';
        }
        if (contract_settings.remove_product == 'Yes') {
            remove_product_yes = 'selected';
        }
        var contract_setting_data = `<div class="Polaris-Layout__Section sd-general-setting">
        <div>
           <form class="form-horizontal frmGeneralSettingSave" method="post" name="frmGeneralSettingSave" id="frmGeneralSettingSave">
              <div class="Polaris-Page">
                 <div class="Polaris-Page-Header Polaris-Page-Header--isSingleRow Polaris-Page-Header--mobileView Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
                    <div class="Polaris-Page-Header__Row">
                       <div class="Polaris-Page-Header__TitleWrapper">
                          <div>
                             <div class="Polaris-Header-Title__TitleAndSubtitleWrapper">
                                <h1 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">Subscription Setting</h1>
                             </div>
                          </div>
                       </div>
                    </div>
                 </div>
                 <div class="Polaris-Page__Content">
                       <div class="scroll_setting_detail Polaris-Scrollable Polaris-Scrollable--vertical Polaris-Scrollable--hasBottomShadow Polaris-Scrollable--verticalHasScrolling" data-polaris-scrollable="true">
                          <div class="Polaris-Card">
                             <div class="Polaris-Card__Section">
                                <div class="Polaris-SettingAction">
                                   <div class="Polaris-SettingAction__Setting"><p class="sd_settingText">Remove Product From Subscription when it is deleted from<span class="Polaris-TextStyle--variationStrong"> Shopify Store :<br>(If there is only one product in a Subscription and selected setting is 'Enable', then Subscription status will be changed to pause and the item will not deleted.)</span></p></div>
                                   <div class="Polaris-SettingAction__Action">
                                   <label class="Polaris-Navigation__Action switch">
                                   <input type="checkbox" class="sd_removeProduct" id="sd_removeProduct" ${contract_settings.remove_product == 'Yes' ? 'checked=""' : ''}>
                                   <span class="slider round"></span>
                                   </label>
                                   </div>
                                </div>
                             </div>
                             <div class="Polaris-Card__Section">
                                <div class="Polaris-SettingAction">
                                   <div class="Polaris-SettingAction__Setting"><p class="sd_settingText">When Product Deleted From Shopify Store then <span class="Polaris-TextStyle--variationStrong">Subscription Status changed to :</span></p></div>
                                   <div class="Polaris-SettingAction__Action">
                                      <div class="Polaris-Select">
                                         <select id="sd_contractStatus" class="Polaris-Select__Input" aria-invalid="true" >
                                            <option value="Active" ${after_product_delete_contract_active}>Active</option>
                                            <option value="Pause" ${after_product_delete_contract_pause}>Pause</option>
                                            <option value="Delete" ${after_product_delete_contract_delete}>Delete</option>
                                         </select>
                                         <div class="Polaris-Select__Content" aria-hidden="true">
                                            <span class="Polaris-Select__SelectedOption">${contract_settings['after_product_delete_contract']}</span>
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
                                </div>
                             </div>
                            <div class="Polaris-Card__Section">
                                <div class="Polaris-SettingAction">
                                   <div class="Polaris-SettingAction__Setting"><p class="sd_settingText">Pause Subscription when Billing Attempt Failed by customer: <span class="Polaris-TextStyle--variationStrong"></span></p></div>
                                    <div class="Polaris-SettingAction__Action">
                                        <label class="Polaris-Navigation__Action switch">
                                            <input type="checkbox" class="sd_failed_billingAttempt" id="sd_failed_billingAttempt" ${contract_settings.afterBillingAttemptFail == 'Pause' ? 'checked=""' : ''}>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            </div>`;
        contract_setting_data += `</div>
                    <div class="Polaris-SettingAction__Action"><button class="Polaris-Button Polaris-Button--primary" id="save_contract_settings" type="button"><span class="Polaris-Button__Content">
                    <span data-query-string="" class="Polaris-Button__Text">Save</span></span></button>
                 </div>
                 </div>
              </div>
           </form>
        </div>
     </div>`;
        document.getElementById('sd_addSettingsHtml').innerHTML = contract_setting_data;
    }

    function getProductPageSetting(product_page_setting_data) {
        product_pagetooltip_message_yes = '', product_pagetooltip_message_no = '';
        if (product_page_setting_data['showPlanDescription'] == 'Yes') {
            product_pagetooltip_message_yes = 'selected';
        }
        if (product_page_setting_data['showPlanDescription'] == 'No') {
            product_pagetooltip_message_no = 'selected';
        }
        var product_page_setting = `<div class="Polaris-Layout__Section sd-dashboard-page">
       <form class="form-horizontal formProductSettingSave" method="post" name="frmProductSave" id="frmProductSave">
          <div class="Polaris-Page">
             <div class="Polaris-Page-Header Polaris-Page-Header--isSingleRow Polaris-Page-Header--mobileView Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
                <div class="Polaris-Page-Header__Row">
                   <div class="Polaris-Page-Header__TitleWrapper">
                      <div>
                         <div class="Polaris-Header-Title__TitleAndSubtitleWrapper">
                            <h1 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">Product Page Setting</h1>
                         </div>
                      </div>
                   </div>
                </div>
             </div>
             <div class="Polaris-Page__Content">
                <div class="scroll_setting_detail Polaris-Scrollable Polaris-Scrollable--vertical Polaris-Scrollable--hasBottomShadow Polaris-Scrollable--verticalHasScrolling" data-polaris-scrollable="true" > <!-- widget div start -->
                   <div class="">
                   <div class="Polaris-Tabs__Wrapper">
                      <ul role="tablist" class="Polaris-Tabs">
                         <li class="Polaris-Tabs__TabContainer" role="presentation"><button group="sd_productPageTabs" role="tab" type="button" tabindex="0" class="sd_productPageTabs-title Polaris-Tabs__Tab sd_Tabs Polaris-Tabs__Tab--selected" aria-selected="true" target-tab="sd_widgetLabels_content" aria-label="All customers"><span class="Polaris-Tabs__Title">Widget Labels</span></button></li>
                         <li class="Polaris-Tabs__TabContainer" role="presentation"><button group="sd_productPageTabs" role="tab" type="button" tabindex="-1" class="sd_productPageTabs-title Polaris-Tabs__Tab sd_Tabs" aria-selected="false" target-tab="sd_widgetAppearance_content"><span class="Polaris-Tabs__Title">Widget Appearance</span></button></li>
                      </ul>
                   </div>
                   <div class="sd_productPageTabs Polaris-Tabs__Panel" id="sd_widgetLabels_content" role="tabpanel" aria-labelledby="accepts-marketing-1" tabindex="-1">
                      <div class="Polaris-Card__Section">
                      <div>
                      <div class="Polaris-FormLayout">
                         <div role="group" class="Polaris-FormLayout--grouped">
                            <div class="Polaris-FormLayout__Items">
                               <div class="Polaris-FormLayout__Item">
                                  <div class="">
                                     <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisTextField47Label" for="widgetTitle" class="Polaris-Label__Text">Widget title</label></div>
                                     </div>
                                     <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                           <div class="Polaris-TextField">
                                              <input id="widgetTitle" name ="widgetTitle" autocomplete="off" class="Polaris-TextField__Input" aria-labelledby="PolarisTextField2Label" aria-invalid="false" value="${product_page_setting_data['widgetTitle']}">
                                              <div class="Polaris-TextField__Backdrop"></div>
                                           </div>
                                        </div>
                                     </div>
                                  </div>
                               </div>
                               <div class="Polaris-FormLayout__Item">
                                  <div class="">
                                     <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="oneTimePurchaseOptionHeading" for="PolarisTextField48" class="Polaris-Label__Text">One time purchase option heading</label></div>
                                     </div>
                                     <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                           <div class="Polaris-TextField">
                                              <input id="oneTimePurchaseOptionHeading" name="oneTimePurchaseOptionHeading" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField48Label" aria-invalid="false" value="${product_page_setting_data['oneTimePurchaseOptionHeading']}">
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
                                        <div class="Polaris-Label"><label id="PolarisTextField47Label" for="payperdeliveryOptionHeading" class="Polaris-Label__Text">Pay per delivery option heading</label></div>
                                     </div>
                                     <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                           <div class="Polaris-TextField">
                                              <input id="payperdeliveryOptionHeading" name="payperdeliveryOptionHeading" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField47Label" aria-invalid="false" value="${product_page_setting_data['payperdeliveryOptionHeading']}">
                                              <div class="Polaris-TextField__Backdrop"></div>
                                           </div>
                                        </div>
                                     </div>
                                  </div>
                               </div>
                               <div class="Polaris-FormLayout__Item">
                                  <div class="">
                                     <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisTextField48Label" for="prepaidOptionHeading" class="Polaris-Label__Text">Prepaid option heading</label></div>
                                     </div>
                                     <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                           <div class="Polaris-TextField">
                                              <input id="prepaidOptionHeading" name="prepaidOptionHeading" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField48Label" aria-invalid="false" value="${product_page_setting_data['prepaidOptionHeading']}">
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
                                     <div class="Polaris-Label"><label id="PolarisTextField47Label" for="pay_per_select_label" class="Polaris-Label__Text">Pay per delivery selling plans display heading</label></div>
                                  </div>
                                  <div class="Polaris-Connected">
                                     <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField">
                                           <input id="pay_per_select_label" name="pay_per_select_label" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField47Label" aria-invalid="false" value="${product_page_setting_data['pay_per_select_label']}">
                                           <div class="Polaris-TextField__Backdrop"></div>
                                        </div>
                                     </div>
                                  </div>
                               </div>
                            </div>
                            <div class="Polaris-FormLayout__Item">
                               <div class="">
                                  <div class="Polaris-Labelled__LabelWrapper">
                                     <div class="Polaris-Label"><label id="PolarisTextField48Label" for="prepaid_select_label" class="Polaris-Label__Text">Prepaid selling plans display heading</label></div>
                                  </div>
                                  <div class="Polaris-Connected">
                                     <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField">
                                           <input id="prepaid_select_label" name="prepaid_select_label" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField48Label" aria-invalid="false" value="${product_page_setting_data['prepaid_select_label']}">
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
                                        <div class="Polaris-Label"><label id="PolarisTextField47Label" for="price_text" class="Polaris-Label__Text">Selling plan title text</label></div>
                                     </div>
                                     <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                           <div class="Polaris-TextField">
                                              <input id="price_text" name="price_text" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField47Label" aria-invalid="false" value="${product_page_setting_data['price_text']}">
                                              <div class="Polaris-TextField__Backdrop"></div>
                                           </div>
                                        </div>
                                     </div>
                                  </div>
                               </div>
                               <div class="Polaris-FormLayout__Item">
                                  <div class="">
                                     <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisTextField48Label" for="showPlanDescription" class="Polaris-Label__Text">Show one time purchase description</label></div>
                                     </div>
                                     <div class="Polaris-Select">
                                    <select id="showPlanDescription" name="showPlanDescription" class="Polaris-Select__Input" aria-invalid="false">
                                        <option value="Yes" ${product_pagetooltip_message_yes}>Yes</option>
                                        <option value="No" ${product_pagetooltip_message_no}>No</option>
                                    </select>
                                    <div class="Polaris-Select__Content" aria-hidden="true">
                                        <span class="Polaris-Select__SelectedOption">${product_page_setting_data['showPlanDescription']}</span>
                                        <span class="Polaris-Select__Icon">
                                          <span class="Polaris-Icon">
                                              <span class="Polaris-VisuallyHidden"></span>
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
                            <div class="Polaris-FormLayout__Items">
                               <div class="Polaris-FormLayout__Item">
                                  <div class="">
                                     <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisTextField47Label" for="planDescriptionTitle" class="Polaris-Label__Text">Description title</label></div>
                                     </div>
                                     <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                           <div class="Polaris-TextField">
                                              <input id="planDescriptionTitle" name="planDescriptionTitle" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField47Label" aria-invalid="false" value="${product_page_setting_data['planDescriptionTitle']}">
                                              <div class="Polaris-TextField__Backdrop"></div>
                                           </div>
                                        </div>
                                     </div>
                                  </div>
                               </div>
                               <div class="Polaris-FormLayout__Item">
                                  <div class="">
                                     <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisTextField48Label" for="oneTimePurchaseDescription" class="Polaris-Label__Text">One time purchase desctiption</label></div>
                                     </div>
                                     <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                           <div class="Polaris-TextField">
                                              <input id="oneTimePurchaseDescription" name="oneTimePurchaseDescription" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField48Label" aria-invalid="false" value="${product_page_setting_data['oneTimePurchaseDescription']}">
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
                                     <div class="Polaris-Label"><label id="PolarisTextField48Label" for="discount_label" class="Polaris-Label__Text">'Save' discount label</label></div>
                                  </div>
                                  <div class="Polaris-Connected">
                                     <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField">
                                           <input id="discount_label" name="discount_label" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField48Label" aria-invalid="false" value="${product_page_setting_data['discount_label']}">
                                           <div class="Polaris-TextField__Backdrop"></div>
                                        </div>
                                     </div>
                                  </div>
                               </div>
                            </div>
                            <div class="Polaris-FormLayout__Item">
                                <div class="">
                                    <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisTextField48Label" for="total_payable_text" class="Polaris-Label__Text">Total payable amount text</label></div>
                                    </div>
                                    <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField">
                                                <input id="total_payable_text" name="total_payable_text" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField48Label" aria-invalid="false" value="${product_page_setting_data['total_payable_text']}">
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
                                    <div class="Polaris-Label"><label id="PolarisTextField48Label" for="total_saved_discount_label" class="Polaris-Label__Text">'Total saved' discount label</label></div>
                                </div>
                                <div class="Polaris-Connected">
                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField">
                                            <input id="total_saved_discount_label" name="total_saved_discount_label" autocomplete="off" class="Polaris-TextField__Input" type="text" aria-labelledby="PolarisTextField48Label" aria-invalid="false" value="${product_page_setting_data['total_saved_discount_label']}">
                                            <div class="Polaris-TextField__Backdrop"></div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                         </div>
                         </div>    <!-- form layout group end -->
                      </div> <!-- form layout end -->
                      <div id="PolarisPortalsContainer"></div>
                       </div>
                      </div>
                   </div><!--Widget Label content ends here-->
                   <div class="sd_productPageTabs Polaris-Tabs__Panel Polaris-Tabs__Panel--hidden" id="sd_widgetAppearance_content" role="tabpanel" aria-labelledby="accepts-marketing-1" tabindex="-1">
                      <div class="Polaris-Card__Section">
                      <div>
                      <div class="Polaris-FormLayout">
                         <div role="group" class="Polaris-FormLayout--grouped">
                            <div class="Polaris-FormLayout__Items">
                               <div class="Polaris-FormLayout__Item">
                                  <div class="">
                                     <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisTextField47Label" for="text_colour" class="Polaris-Label__Text">Text color</label></div>
                                     </div>
                                     <div class="Polaris-Connected">
                                      <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField">
                                            <label class="colorlabel" id="colorlabel_text_colour" style=""></label>
                                            <input type="text" class="Polaris-TextField__Input jscolor colorinput" name="text_colour" id="text_colour" value="${product_page_setting_data['text_colour']}" aria-labelledby="PolarisTextField20Label" aria-invalid="false" aria-multiline="false"  autocomplete="off">
                                          <div class="Polaris-TextField__Backdrop"></div>
                                          </div>
                                      </div>
                                     </div>
                                  </div>
                               </div>
                               <div class="Polaris-FormLayout__Item">
                               <div class="">
                                  <div class="Polaris-Labelled__LabelWrapper">
                                     <div class="Polaris-Label"><label id="PolarisTextField47Label" for="description_Text_Color" class="Polaris-Label__Text">Description text color</label></div>
                                  </div>
                                  <div class="Polaris-Connected">
                                     <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                     <div class="Polaris-TextField">
                                         <label class="colorlabel" id="colorlabel_radiobtn_colour" style=""></label>
                                         <input type="text" class="Polaris-TextField__Input jscolor colorinput" name="description_Text_Color" id="description_Text_Color" value="${product_page_setting_data['description_Text_Color']}" aria-labelledby="PolarisTextField20Label" aria-invalid="false" aria-multiline="false"  autocomplete="off">
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
                                        <div class="Polaris-Label"><label id="PolarisTextField47Label" for="border_colour" class="Polaris-Label__Text">Border colour</label></div>
                                     </div>
                                     <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField">
                                            <label class="colorlabel" id="colorlabel_border_colour" style=""></label>
                                            <input type="text" class="Polaris-TextField__Input jscolor colorinput" name="border_colour" id="border_colour" value="${product_page_setting_data['border_colour']}" aria-labelledby="PolarisTextField20Label" aria-invalid="false" aria-multiline="false"  autocomplete="off">
                                          <div class="Polaris-TextField__Backdrop"></div>
                                        </div>
                                        </div>
                                     </div>
                                  </div>
                               </div>
                               <div class="Polaris-FormLayout__Item">
                                  <div class="">
                                     <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="oneTimePurchaseOptionHeading" for="radio_button_colour" class="Polaris-Label__Text">Radio button colour</label></div>
                                     </div>
                                     <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField">
                                            <label class="colorlabel" id="colorlabel_radiobtn_colour" style=""></label>
                                            <input type="text" class="Polaris-TextField__Input jscolor colorinput" name="radio_button_colour" id="radio_button_colour" value="${product_page_setting_data['radio_button_colour']}" aria-labelledby="PolarisTextField20Label" aria-invalid="false" aria-multiline="false"  autocomplete="off">
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
                                     <div class="Polaris-Label"><label id="PolarisTextField47Label" for="discount_background" class="Polaris-Label__Text">Discount background colour</label></div>
                                  </div>
                                  <div class="Polaris-Connected">
                                     <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                     <div class="Polaris-TextField">
                                         <label class="colorlabel" id="discount_background" style=""></label>
                                         <input type="text" class="Polaris-TextField__Input jscolor colorinput" name="discount_background" id="discount_background" value="${product_page_setting_data['discount_background']}" aria-labelledby="PolarisTextField20Label" aria-invalid="false" aria-multiline="false"  autocomplete="off">
                                       <div class="Polaris-TextField__Backdrop"></div>
                                     </div>
                                     </div>
                                  </div>
                               </div>
                            </div>
                            <div class="Polaris-FormLayout__Item">
                                  <div class="">
                                     <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisTextField47Label" for="description_background_color" class="Polaris-Label__Text">Description background color</label></div>
                                     </div>
                                     <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField">
                                            <label class="colorlabel" id="colorlabel_radiobtn_colour" style=""></label>
                                            <input type="text" class="Polaris-TextField__Input jscolor colorinput" name="description_background_color" id="description_background_color" value="${product_page_setting_data['description_background_color']}" aria-labelledby="PolarisTextField20Label" aria-invalid="false" aria-multiline="false"  autocomplete="off">
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
                                    <div class="Polaris-Label"><label id="PolarisTextField48Label" for="border_style" class="Polaris-Label__Text">Border style</label></div>
                                </div>
                                <div class="Polaris-Select">
                                <select id="border_style" name="border_style" class="Polaris-Select__Input" aria-invalid="false">
                                    <option value="dashed" ${product_page_setting_data['border_style'] == 'dashed' ? 'selected' : ''}>dashed</option>
                                    <option value="dotted" ${product_page_setting_data['border_style'] == 'dotted' ? 'selected' : ''}>dotted</option>
                                    <option value="double" ${product_page_setting_data['border_style'] == 'double' ? 'selected' : ''}>double</option>
                                    <option value="groove" ${product_page_setting_data['border_style'] == 'groove' ? 'selected' : ''}>groove</option>
                                    <option value="hidden" ${product_page_setting_data['border_style'] == 'hidden' ? 'selected' : ''}>hidden</option>
                                    <option value="inherit" ${product_page_setting_data['border_style'] == 'inherit' ? 'selected' : ''}>inherit</option>
                                    <option value="initial" ${product_page_setting_data['border_style'] == 'initial' ? 'selected' : ''}>initial</option>
                                    <option value="inset" ${product_page_setting_data['border_style'] == 'inset' ? 'selected' : ''}>inset</option>
                                    <option value="none" ${product_page_setting_data['border_style'] == 'none' ? 'selected' : ''}>none</option>
                                    <option value="outset" ${product_page_setting_data['border_style'] == 'outset' ? 'selected' : ''}>outset</option>
                                    <option value="revert" ${product_page_setting_data['border_style'] == 'revert' ? 'selected' : ''}>revert</option>
                                    <option value="ridge" ${product_page_setting_data['border_style'] == 'ridge' ? 'selected' : ''}>ridge</option>
                                    <option value="solid" ${product_page_setting_data['border_style'] == 'solid' ? 'selected' : ''}>solid</option>
                                    <option value="unset" ${product_page_setting_data['border_style'] == 'unset' ? 'selected' : ''}>unset</option>
                                </select>
                                <div class="Polaris-Select__Content" aria-hidden="true">
                                    <span class="Polaris-Select__SelectedOption">${product_page_setting_data['border_style']}</span>
                                    <span class="Polaris-Select__Icon">
                                    <span class="Polaris-Icon">
                                        <span class="Polaris-VisuallyHidden"></span>
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
                               <div class="Polaris-FormLayout__Item">
                               <div class="">
                                  <div class="Polaris-Labelled__LabelWrapper">
                                     <div class="Polaris-Label"><label id="PolarisTextField47Label" for="border_radious" class="Polaris-Label__Text">Border radius</label></div>
                                  </div>
                                  <div class="Polaris-Connected">
                                     <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                     <div class="Polaris-TextField">
                                         <label class="colorlabel" id="colorlabel_radiobtn_colour" style=""></label>
                                         <input type="text" class="Polaris-TextField__Input" name="border_radious" id="border_radious" value="${product_page_setting_data['border_radious']}" aria-labelledby="PolarisTextField20Label" aria-invalid="false" aria-multiline="false"  autocomplete="off">
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
                                        <div class="Polaris-Label"><label id="PolarisTextField47Label" for="top_margin" class="Polaris-Label__Text">Top margin</label></div>
                                     </div>
                                     <div class="Polaris-Connected">
                                      <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                          <div class="Polaris-TextField Polaris-TextField--hasValue">
                                            <input id="top_margin" name="top_margin" autocomplete="off" class="Polaris-TextField__Input" type="number" aria-labelledby="PolarisTextField4Label" aria-invalid="false" value="${product_page_setting_data['top_margin']}">
                                            <div class="Polaris-TextField__Spinner" aria-hidden="true">
                                                <div role="button" class="Polaris-TextField__Segment sd_inc_dec_num" data-id="sd_increase_number"  tabindex="-1">
                                                  <div class="Polaris-TextField__SpinnerIcon">
                                                      <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="m15 12-5-5-5 5h10z"></path>
                                                        </svg>
                                                      </span>
                                                  </div>
                                                </div>
                                                <div role="button" class="Polaris-TextField__Segment sd_inc_dec_num" data-id="sd_decrease_number" tabindex="-1">
                                                  <div class="Polaris-TextField__SpinnerIcon">
                                                      <span class="Polaris-Icon">
                                                        <span class="Polaris-VisuallyHidden"></span>
                                                        <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                            <path d="m5 8 5 5 5-5H5z"></path>
                                                        </svg>
                                                      </span>
                                                  </div>
                                                </div>
                                            </div>
                                            <div class="Polaris-TextField__Backdrop"></div>
                                          </div>
                                      </div>
                                    </div>
                                  </div>
                               </div>
                               <div class="Polaris-FormLayout__Item">
                                  <div class="">
                                     <div class="Polaris-Labelled__LabelWrapper">
                                        <div class="Polaris-Label"><label id="PolarisTextField48Label" for="bottom_margin" class="Polaris-Label__Text">Bottom margin</label></div>
                                     </div>
                                     <div class="Polaris-Connected">
                                    <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                        <div class="Polaris-TextField Polaris-TextField--hasValue">
                                          <input id="bottom_margin" name="bottom_margin" autocomplete="off" class="Polaris-TextField__Input" type="number" aria-labelledby="PolarisTextField4Label" aria-invalid="false" value="${product_page_setting_data['bottom_margin']}">
                                          <div class="Polaris-TextField__Spinner" aria-hidden="true">
                                              <div role="button" class="Polaris-TextField__Segment sd_inc_dec_num" data-id="sd_increase_number" tabindex="-1">
                                                <div class="Polaris-TextField__SpinnerIcon">
                                                    <span class="Polaris-Icon">
                                                      <span class="Polaris-VisuallyHidden"></span>
                                                      <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                          <path d="m15 12-5-5-5 5h10z"></path>
                                                      </svg>
                                                    </span>
                                                </div>
                                              </div>
                                              <div role="button" class="Polaris-TextField__Segment sd_inc_dec_num" data-id="sd_decrease_number" tabindex="-1">
                                                <div class="Polaris-TextField__SpinnerIcon">
                                                    <span class="Polaris-Icon">
                                                      <span class="Polaris-VisuallyHidden"></span>
                                                      <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                          <path d="m5 8 5 5 5-5H5z"></path>
                                                      </svg>
                                                    </span>
                                                </div>
                                              </div>
                                          </div>
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
                                        <div class="Polaris-Label"><label id="PolarisTextField47Label" for="border_thickness" class="Polaris-Label__Text">Border thickness</label></div>
                                     </div>
                                     <div class="Polaris-Connected">
                                        <div class="Polaris-Connected__Item Polaris-Connected__Item--primary">
                                            <div class="Polaris-TextField Polaris-TextField--hasValue">
                                              <input id="border_thickness" name="border_thickness" autocomplete="off" class="Polaris-TextField__Input" type="number" aria-labelledby="PolarisTextField4Label" aria-invalid="false" value="${product_page_setting_data['border_thickness']}">
                                              <div class="Polaris-TextField__Spinner" aria-hidden="true">
                                                  <div role="button" class="Polaris-TextField__Segment sd_inc_dec_num" data-id="sd_increase_number" tabindex="-1">
                                                    <div class="Polaris-TextField__SpinnerIcon">
                                                        <span class="Polaris-Icon">
                                                          <span class="Polaris-VisuallyHidden"></span>
                                                          <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                              <path d="m15 12-5-5-5 5h10z"></path>
                                                          </svg>
                                                        </span>
                                                    </div>
                                                  </div>
                                                  <div role="button" class="Polaris-TextField__Segment sd_inc_dec_num" data-id="sd_decrease_number" tabindex="-1">
                                                    <div class="Polaris-TextField__SpinnerIcon">
                                                        <span class="Polaris-Icon">
                                                          <span class="Polaris-VisuallyHidden"></span>
                                                          <svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                                                              <path d="m5 8 5 5 5-5H5z"></path>
                                                          </svg>
                                                        </span>
                                                    </div>
                                                  </div>
                                              </div>
                                              <div class="Polaris-TextField__Backdrop"></div>
                                            </div>
                                        </div>
                                      </div>
                                  </div>
                               </div>
                            </div>
                         </div>    <!-- form layout group end -->
                      </div> <!-- form layout end -->
                      <div id="PolarisPortalsContainer"></div>
                   </div>
                      </div>
                   </div>  <!--Widget appearance content ends here-->

                 <!--Past Orders List ends here-->
                    </div>
                   </div><!-- polaris card end -->
				   <div class="Polaris-SettingAction__Action"><button class="Polaris-Button Polaris-Button--primary" type="submit"><span class="Polaris-Button__Content">
                      <span data-query-string="" class="Polaris-Button__Text">Save</span></span></button>
                   </div>
                </div>  <!-- widget  div end -->
             </div><!-- page content end -->
             </div><!-- page end -->
       </form>
       </div>
       `;
        document.getElementById('sd_addSettingsHtml').innerHTML = product_page_setting;
        // check selected option checked
        jQuery('#border_style option[value=' + product_page_setting_data['border_style'] + ']:selected');
    }
    //show tooltip on email notification setting
    function mail_sent_to_tooltip(mail_setting) {
        if (mail_setting == 'new_subscription_purchase' || mail_setting == 'manual_billing_attempt' || mail_setting == 'payment_declined') {
            tooltip_message = 'Mail will be sent to both the admin and customer';
        } else {
            tooltip_message = 'If changes made by customer then mail will be sent to admin and V/S';
        }
        mail_notify_messages = `<div class="sd_subscriptionPrc"><div class="sd_recurringMsg" onmouseover="show_title(this)" onmouseout="hide_title(this)"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20px">
            <path fill-rule="evenodd" d="M11 11H9v-.148c0-.876.306-1.499 1-1.852.385-.195 1-.568 1-1a1.001 1.001 0 00-2 0H7c0-1.654 1.346-3 3-3s3 1 3 3-2 2.165-2 3zm-2 4h2v-2H9v2zm1-13a8 8 0 100 16 8 8 0 000-16z" fill="#5C5F62"></path></svg></div>
            <div class="Polaris-PositionedOverlay display-hide-label">
                <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                    <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">${tooltip_message}</div>
                </div>
            </div>
            </div>`;
        return mail_notify_messages;
    }

        function getCustomerSettingData(customerSettingData) {
            var customer_Setting = `<div class="Polaris-Layout__Section sd-dashboard-page">
         <form class="form-horizontal frmCustomerSettingSave" method="post" name="frmCustomerSetting" id="frmCustomerSetting">
            <div class="Polaris-Page">
               <div class="Polaris-Page-Header Polaris-Page-Header--isSingleRow Polaris-Page-Header--mobileView Polaris-Page-Header--noBreadcrumbs Polaris-Page-Header--mediumTitle">
                  <div class="Polaris-Page-Header__Row">
                     <div class="Polaris-Page-Header__TitleWrapper">
                        <div>
                           <div class="Polaris-Header-Title__TitleAndSubtitleWrapper">
                              <h1 class="Polaris-DisplayText Polaris-DisplayText--sizeLarge">Customer portal setting</h1>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="Polaris-Page__Content">
               <div class="scroll_setting_detail Polaris-Scrollable Polaris-Scrollable--vertical Polaris-Scrollable--hasBottomShadow Polaris-Scrollable--verticalHasScrolling" data-polaris-scrollable="true">
                   <div class="customer-settings-wrapper">
                     <label class="Polaris-Choice" for="cancel_subscription"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="cancel_subscription" type="checkbox" name="cancel_subscription" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="" ${customerSettingData.cancel_subscription == '1' ? 'checked' : ''}><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                     <path d="m8.315 13.859-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.436.436 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                     </svg></span></span></span></span><span class="Polaris-Choice__Label">Customer can cancel subscription</span></label>
                     <div id="PolarisPortalsContainer"></div>
                   </div>
                   <div class="customer-settings-wrapper">
                       <label class="Polaris-Choice" for="attempt_billing"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="attempt_billing" type="checkbox" name="attempt_billing" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="" ${customerSettingData.attempt_billing == '1' ? 'checked' : ''}><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                       <path d="m8.315 13.859-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.436.436 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                       </svg></span></span></span></span><span class="Polaris-Choice__Label">Customer can attempt billing for pay per delivery orders</span></label>
                       <div id="PolarisPortalsContainer"></div>
                   </div>
                   <div class="customer-settings-wrapper">
                     <label class="Polaris-Choice" for="skip_upcoming_order"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="skip_upcoming_order" type="checkbox" name="skip_upcoming_order" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="" ${customerSettingData.skip_upcoming_order == '1' ? 'checked' : ''}><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                     <path d="m8.315 13.859-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.436.436 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                     </svg></span></span></span></span><span class="Polaris-Choice__Label">Customer can skip order</span></label>
                     <div id="PolarisPortalsContainer"></div>
                 </div>
                 <div class="customer-settings-wrapper">
                 <label class="Polaris-Choice" for="skip_upcoming_fulfillment"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="skip_upcoming_fulfillment" type="checkbox" name="skip_upcoming_fulfillment" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="" ${customerSettingData.skip_upcoming_fulfillment == '1' ? 'checked' : ''}><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                 <path d="m8.315 13.859-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.436.436 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                 </svg></span></span></span></span><span class="Polaris-Choice__Label">Customer can skip upcoming fulfillment in prepaid orders.</span></label>
                 <div id="PolarisPortalsContainer"></div>
             </div>
                   <div class="customer-settings-wrapper">
                     <label class="Polaris-Choice" for="pause_resume_subscription"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="pause_resume_subscription" type="checkbox" name="pause_resume_subscription" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="" ${customerSettingData.pause_resume_subscription == '1' ? 'checked' : ''}><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                     <path d="m8.315 13.859-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.436.436 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                     </svg></span></span></span></span><span class="Polaris-Choice__Label">Customer can pause/resume subscription</span></label>
                     <div id="PolarisPortalsContainer"></div>
                   </div>
                   <div class="customer-settings-wrapper">
                       <label class="Polaris-Choice" for="edit_product_price"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="edit_product_price" type="checkbox" name="edit_product_price" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="" ${customerSettingData.edit_product_price == '1' ? 'checked' : ''}><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                       <path d="m8.315 13.859-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.436.436 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                       </svg></span></span></span></span><span class="Polaris-Choice__Label">Customer can change product price</span></label>
                       <div id="PolarisPortalsContainer"></div>
                   </div>
                   <div class="customer-settings-wrapper">
                       <label class="Polaris-Choice" for="edit_product_quantity"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="edit_product_quantity" type="checkbox" name="edit_product_quantity" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="" ${customerSettingData.edit_product_quantity == '1' ? 'checked' : ''}><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                       <path d="m8.315 13.859-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.436.436 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                       </svg></span></span></span></span><span class="Polaris-Choice__Label">Customer can change product quantity</span></label>

                       <label class="Polaris-Choice ${customerSettingData.edit_product_quantity == '1' ? '' : 'display-hide-label'}" for="edit_out_of_stock_product_quantity" id="edit_out_of_stock_product_quantity_label"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="edit_out_of_stock_product_quantity" type="checkbox" name="edit_out_of_stock_product_quantity" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="" ${customerSettingData.edit_out_of_stock_product_quantity == '1' ? 'checked' : ''}><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                       <path d="m8.315 13.859-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.436.436 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                       </svg></span></span></span></span><span class="Polaris-Choice__Label">Customer can change out of stock product quantity</span></label>
                       <div id="PolarisPortalsContainer"></div>
                   </div>
                   <div class="customer-settings-wrapper">
                   <label class="Polaris-Choice" for="add_subscription_product"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="add_subscription_product" type="checkbox" name="add_subscription_product" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="" ${customerSettingData.add_subscription_product == '1' ? 'checked' : ''}><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                   <path d="m8.315 13.859-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.436.436 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                   </svg></span></span></span></span><span class="Polaris-Choice__Label">Customer Can add new Product in the Contract</span></label>
                   <label class="Polaris-Choice ${ customerSettingData.add_subscription_product == '1' ? '' : 'display-hide-label' }" id="add_out_of_stock_product_label" for="add_out_of_stock_product"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="add_out_of_stock_product" type="checkbox" name="add_out_of_stock_product" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="" ${customerSettingData.add_out_of_stock_product == '1' ? 'checked' : ''}><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                   <path d="m8.315 13.859-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.436.436 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                   </svg></span></span></span></span><span class="Polaris-Choice__Label">Customer Can add out of stock Product in Contract</span></label>
                   <div class="sd_subscriptionPrc"><div class="sd_recurringMsg" onmouseover="show_title(this)" onmouseout="hide_title(this)"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="20px">
                   <path fill-rule="evenodd" d="M11 11H9v-.148c0-.876.306-1.499 1-1.852.385-.195 1-.568 1-1a1.001 1.001 0 00-2 0H7c0-1.654 1.346-3 3-3s3 1 3 3-2 2.165-2 3zm-2 4h2v-2H9v2zm1-13a8 8 0 100 16 8 8 0 000-16z" fill="#5C5F62"></path></svg></div>
                   <div class="Polaris-PositionedOverlay display-hide-label">
                       <div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
                           <div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content">Product will be added in the currency in which the subscription were purchased without accounting for store currency and without currency conversion.</div>
                       </div>
                   </div>
                   </div>
                   <div id="PolarisPortalsContainer"></div>
                 </div>
                   <div class="customer-settings-wrapper">
                     <label class="Polaris-Choice" for="delete_product"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="delete_product" type="checkbox" name="delete_product" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="" ${customerSettingData.delete_product == '1' ? 'checked' : ''}><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                     <path d="m8.315 13.859-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.436.436 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                     </svg></span></span></span></span><span class="Polaris-Choice__Label">Customer can delete subscription product</span></label>
                     <div id="PolarisPortalsContainer"></div>
                 </div>
                          <div class="customer-settings-wrapper">
                     <label class="Polaris-Choice" for="edit_shipping_address"><span class="Polaris-Choice__Control"><span class="Polaris-Checkbox"><input id="edit_shipping_address" type="checkbox" name="edit_shipping_address" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="" ${customerSettingData.edit_shipping_address == '1' ? 'checked' : ''}><span class="Polaris-Checkbox__Backdrop"></span><span class="Polaris-Checkbox__Icon"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true">
                     <path d="m8.315 13.859-3.182-3.417a.506.506 0 0 1 0-.684l.643-.683a.437.437 0 0 1 .642 0l2.22 2.393 4.942-5.327a.436.436 0 0 1 .643 0l.643.684a.504.504 0 0 1 0 .683l-5.91 6.35a.437.437 0 0 1-.642 0"></path>
                     </svg></span></span></span></span><span class="Polaris-Choice__Label">Customer can change shipping address</span></label>
                     <div id="PolarisPortalsContainer"></div>
                 </div>
                 </div>
                 <div class="Polaris-SettingAction__Action"><button class="Polaris-Button Polaris-Button--primary" type="submit"><span class="Polaris-Button__Content">
                 <span data-query-string="" class="Polaris-Button__Text">Save</span></span></button>
            </div>
                        </div>
            </div>
         </form>
         </div>`;
               document.getElementById('sd_addSettingsHtml').innerHTML = customer_Setting;
           }

    function close_setting_picker() {
        jQuery('.sd_contractSetting').addClass('display-hide-label');
    }

    /**===========================================================  Functions End ============================================================**/
    // onchange event start
    jQuery("body").on("change", "select#sd_productDiscount", function() {
        if (this.value == 'Maximum' || this.value == 'Minimum') {
            jQuery('.sd_discount_type').removeClass('display-hide-label');
        } else {
            jQuery('.sd_discount_type').addClass('display-hide-label');
        }
    });
    // onchange event end


    /**===========================================================  Click Events Start ============================================================**/
        //invoice actions click for mail download and print the invoice
        jQuery('body').on('click', '.sd_select_invoice_action', function(e){
            var selected_option = jQuery('.sd_select_invoice_action option:selected').val();
            if(selected_option == 'Mail'){
                var order_id = jQuery('.sd_select_invoice_action option:selected').attr('data-orderId');
                var customerEmail = jQuery('.sd_select_invoice_action option:selected').attr('data-customerEmail');
               send_mail_invoice(order_id,customerEmail);
            }else if(selected_option == 'Print'){
                var printhtml = $(".main-invoice-table").html();
                printWindow = window.open('');
                printWindow.document.write(printhtml);
                printWindow.print();
                printWindow.close();
            }else if(selected_option == 'Download'){
                var order_id = jQuery('.sd_select_invoice_action option:selected').attr('data-orderId');
                var fullURL = `/admin/downloadInvoice.php?shop=`+store+`&order_id=`+order_id;
                open(fullURL,'_self');
            }
            jQuery(".sd_select_invoice_action").val('More Actions');
        });


        // invoice template settings start
        jQuery('body').on('change', '.invoice_edit_on', function(e){
            if(jQuery(this).prop('checked')) {
               jQuery('.editModeOff').addClass('display-hide-label');
               jQuery('.editModeOn').removeClass('display-hide-label');
            }else{
                jQuery('.editModeOff').removeClass('display-hide-label');
               jQuery('.editModeOn').addClass('display-hide-label');
            }
        });

        jQuery('body').on('change', '.invoice_settings_toggle', function(e){
            var updated_toggle = jQuery(this).attr('name');
            if(this.checked){
               jQuery('.'+updated_toggle).removeClass('display-hide-label');
            }else{
                jQuery('.'+updated_toggle).addClass('display-hide-label');
            }
        });

        jQuery('body').on('click', '.send_mail_invoice', function(e){
            var customer_email = jQuery(this).attr('data-customerEmail');
            var order_id = jQuery(this).attr('data-orderId');
            send_mail_invoice(order_id,customer_email);
        });

        function send_mail_invoice(order_id,customer_email){
            $.ajax({
                url: SHOPIFY_DOMAIN_URL+"/admin/mailInvoice.php?shop="+store+"&order_id="+order_id, // Replace with your API endpoint
                type: 'POST', // Or 'PUT' if you're updating data
                data: {
                    'customer_email' : customer_email,
                },
                success: function(response) {
                // Handle success response
                    let json = jQuery.parseJSON(response);
                    if(json.status == true){
                        displayMessage(json['message'], 'success');
                    }
                },
                error: function(xhr, status, error) {
                // Handle error
                }
            });
        }



        jQuery("body").on('change', '#imglogo', function (e) {
            var file = this.files[0];
            var reader = new FileReader();
            if(file){
                var fileSize = file.size;
                var maxSizeInBytes = fileSize/1024;
                if(maxSizeInBytes > 500){
                    displayMessage('File dimensions should be below 270*200 and size 500kb.', 'error');
                }else{
                    reader.onload = function(e) {
                        var img = new Image();
                        var width,height;
                        img.onload = async function() {
                            width = this.width;
                            height = this.height;
                            if(width <= 270 && height <= 200){
                                $('#set_logo_img').attr('src', img.src);
                            }else{
                                displayMessage('File dimensions should be below 270*200 and size 500kb.', 'error');
                            }
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        jQuery("body").on('click', '#uploadlogoimg', function () {
            $("#imglogo").click();
        });

        jQuery("body").on('change', '#signatureImg', function (e) {
            var file = this.files[0];
            var reader = new FileReader();
            if(file){
                var fileSize = file.size;
                var maxSizeInBytes = fileSize/1024;
                if(maxSizeInBytes > 500){
                    displayMessage('File dimensions should be below 270*200 and size 500kb.', 'error');
                }else{
                    reader.onload = function(e) {
                        var img = new Image();
                        var height,width;
                        img.onload = function() {
                            width = this.width;
                            height = this.height;
                            if(height <= 200 && width <= 270){
                                $('#set_signature_img').attr('src', img.src);
                            }else{
                                displayMessage('File dimensions should be below 270*200 and size 500kb.', 'error');
                            }
                        };
                        img.src = e.target.result;

                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        jQuery("body").on('click', '#uploadsignatureimg', function () {
            $("#signatureImg").click();
        });


        jQuery('body').on('click', '.save-invoice-settings', async function(e){
            var form = document.getElementById('save_invoice_settings');
            var formData = new FormData(form);
            var show_invoice_settings = {};
            let invoice_form_data = form_serializeObject("save_invoice_settings");

            $('.sd-invoice-settings input[type=checkbox]').each(function () {
                var column_name = jQuery(this).attr('name');
                if(this.checked){
                    formData.append(column_name, '1');
                }else{
                    formData.append(column_name, '0');
                }
            });

            all_invoice_settings = Object.assign(show_invoice_settings, invoice_form_data);
            $.ajax({
                url: SHOPIFY_DOMAIN_URL+"/application/controller/ajaxhandler.php?store="+store+"&action=save_invoice_settings", // Replace with your API endpoint
                type: 'POST', // Or 'PUT' if you're updating data
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting content type
                success: function(response) {
                  // Handle success response
                    let json = jQuery.parseJSON(response);
                    if(json.status == true){
                        displayMessage(json['message'], 'success');
                        location.reload();
                    }else{
                        displayMessage(json['message'], 'error');
                    }
                },
                error: function(xhr, status, error) {
                  // Handle error
                  displayMessage(error,'error');
                }
            });

        });

    jQuery("body").on('click', '.prev_step', async function(e) {
        let show_div = jQuery(this).attr('show-data');
        let hide_div = jQuery(this).attr('hide-data');
        jQuery('.' + show_div).removeClass('display-hide-label');
        jQuery('.' + hide_div).addClass('display-hide-label');
    });

    jQuery("body").on('click', '.skip_step', async function(e) {
        let step_number = jQuery(this).attr('id');
        if (step_number == 'skip_theme_enable_step') {
            jQuery('.sd_step_1').addClass('display-hide-label');
            jQuery('.sd_step_2').removeClass('display-hide-label');
        } else if (step_number == 'skip_payment_enable_step') {
            jQuery('.sd_step_2').addClass('display-hide-label');
            jQuery('.sd_step_3').removeClass('display-hide-label');
        }
    });

    // jQuery("body").on('click', '.sd_stepForm', function(e) {
    //     jQuery("#sd_global_modal_container").html(sd_step_form_html);
    //     jQuery("#sd_popup").removeClass("display-hide-label");
    // });
    jQuery("body").on('click', '.sd_stepForm', async function(e) {
        console.log('store id',store_id);
        jQuery('#sd_subscriptionLoader').removeClass('display-hide-label');
        let data_values = {
            "store_id": store_id
        }
        let ajaxParameters = {
            method: "POST",
            dataValues: {
                data_values: data_values,
                action: "configure_step_form"
            }
        };
        try {
            let result = await AjaxCall(ajaxParameters);
            jQuery('#sd_subscriptionLoader').addClass('display-hide-label');
            sd_step_form_value = sd_step_form_return(result['app_status'],result['selling_plan_status'],result['shopify_payment_status']);
            jQuery("#sd_global_modal_container").html(sd_step_form_value);
            jQuery("#sd_popup").removeClass("display-hide-label");
        } catch (e) {}
    });

    //step form click function start
    jQuery("body").on('click', '.sd_selectedStep', async function(e) {
        let selected_step = jQuery(this).attr('data-id');
        let whereconditionvalues = {
            "store_id": store_id
        }
        step_form_values = {
            'store_id': store_id,
            'step_form': selected_step
        }
        let ajaxParameters = {
            method: "POST",
            dataValues: {
                table: 'step_form',
                data_values: step_form_values,
                wherecondition: whereconditionvalues,
                wheremode: 'and',
                action: "insertupdateajax"
            }
        };
        try {
            let result = await AjaxCall(ajaxParameters);
        } catch (e) {}
    });

    //  increase  decrease number when click on the icons of number fields
    jQuery("body").on('click', '.sd_inc_dec_num', function(e) {
        let input_value = jQuery(this).parent().parent().find("input[type=number]").val();
        let selected_btn = jQuery(this).attr('data-id');
        if (selected_btn == 'sd_increase_number') {
            input_value++;
        } else if (selected_btn == 'sd_decrease_number') {
            input_value--
        }
        jQuery(this).parent().parent().find("input[type=number]").val(input_value);
    });

    // save email setting data into db
    jQuery("body").on('click', '#sd_email_setting', async function(e) {
        e.preventDefault();
        jQuery('#sd_email_setting').addClass('btn-disable-loader');
        jQuery('.email_url_error').html(''); //remove all errors
        checkStatus = 'true';
        emailSettingFormData = form_serializeObject('email_setting_form');
        $.each(emailSettingFormData, function(key, value) {
            if (value != '' && (key != 'footer_text') && (key != 'enable_social_link')) {
                if (validURL(value)) {} else {
                    jQuery('.' + key + '_error').html('Enter Valid Url');
                    checkStatus = 'false';
                }
            }
        });

        if ($("#enable_social_link").prop('checked') == true) {
            if ((jQuery.trim(jQuery('#facebook_link').val()) == '') && (jQuery.trim(jQuery('#twitter_link').val()) == '') && (jQuery.trim(jQuery('#instagram_link').val()) == '') && (jQuery.trim(jQuery('#linkedin_link').val()) == '')) {
                displayMessage('Since you have enabled social links , please enter at least one social link value', 'error');
                checkStatus = 'false';
            }
        }

        if (checkStatus == 'true') {
            // enable_social_links
            if ($("#enable_social_link").prop('checked') == true) {
                //do something
                emailSettingFormData['enable_social_link'] = '1';
            } else {
                emailSettingFormData['enable_social_link'] = '0';
            }
            emailSettingFormData['store_id'] = store_id;
            let whereconditionvalues = {
                "store_id": store_id
            }
            let ajaxParameters = {
                method: "POST",
                dataValues: {
                    table: 'email_settings',
                    data_values: emailSettingFormData,
                    wherecondition: whereconditionvalues,
                    wheremode: 'and',
                    action: "insertupdateajax"
                }
            };
            try {
                let result = await AjaxCall(ajaxParameters);
                if (result['status'] == true) {
                    displayMessage(result['message'], 'success');
                    jQuery('#sd_email_setting').removeClass('btn-disable-loader');
                    close_setting_picker();
                }
            } catch (e) {
                displayMessage(e, 'error');
                jQuery('#sd_email_setting').removeClass('btn-disable-loader');
            }
        } else {
            jQuery('#sd_email_setting').removeClass('btn-disable-loader');
        }
    });


    $("body").on('click', '.Polaris-TopBar__NavigationIcon', function() {
        $('#AppFrameNav').addClass('Polaris-Frame__Navigation--visible Polaris-Frame__Navigation--enterActive').after('<div class="Polaris-Backdrop Polaris-Backdrop--belowNavigation"></div>');
    });

    $("body").on('click', '.Polaris-Frame__NavigationDismiss', function() {
        $('#AppFrameNav').removeClass('Polaris-Frame__Navigation--visible Polaris-Frame__Navigation--enterActive');
        $('.Polaris-Backdrop.Polaris-Backdrop--belowNavigation').remove();
    });

    // customer account page  setting checkbox click start
    jQuery("body").on('click', '#frmCustomerSetting input[type="checkbox"]', async function(e) {
        if (this.id == 'add_subscription_product') {
            if (this.checked) {
                jQuery('#add_out_of_stock_product_label').removeClass('display-hide-label');
            } else {
                jQuery('#add_out_of_stock_product_label').addClass('display-hide-label');
                $("#add_out_of_stock_product").prop("checked", false);
            }
        } else if (this.id == 'edit_product_quantity') {
            if (this.checked) {
                jQuery('#edit_out_of_stock_product_quantity_label').removeClass('display-hide-label');
            } else {
                jQuery('#edit_out_of_stock_product_quantity_label').addClass('display-hide-label');
                $("#edit_out_of_stock_product_quantity").prop("checked", false);
            }
        }
    });

    jQuery("body").on('click', '#chooseOrderDatePicker', function(e) {
        jQuery('.choose_dateReport').toggle();
    });

    jQuery("body").on('click', '#submitOrderdate', function(e) {
        let start_date = jQuery('#startdateReport').val();
        let last_date = jQuery('#enddateReport').val();
        // redirect_link_global = 'analytics.php';
        // redirect_query_string = '&start_date=' + start_date + '&last_date=' + last_date;
        // redirectlink();
        var fullURL = `/admin/analytics.php?shop=`+store+`&start_date=`+start_date+'&last_date='+last_date;
        open(fullURL,'_self');
    });
    //analytics datepicker end

    //send test mail
    jQuery("body").on('click', '#sd_testmail', async function(e) {
        jQuery(this).addClass('btn-disable-loader');
        var sendMailTo = jQuery('#email_to').val();
        if (sendMailTo == '') {
            displayMessage('Enter Email', 'error');
            jQuery(this).removeClass('btn-disable-loader');
            return false;
        } else if (!isEmail(sendMailTo)) {
            displayMessage('Enter Valid Email', 'error');
            jQuery(this).removeClass('btn-disable-loader');
            return false;
        }
        var isTrue = checkEmailConfiguration("emailform_setting");
        if (isTrue) {
            displayMessage('Please Wait...', 'waiting');
            emailConfigurationData['sendTo'] = sendMailTo;
            emailConfigurationData['subject'] = 'test Mail';
            emailConfigurationData['mailBody'] = 'test Mail Sent Successfully';
            let ajaxParameters = {
                method: "POST",
                dataValues: {
                    dataValues: emailConfigurationData,
                    action: "sendTestMail"
                }
            };
            try {
                let result = await AjaxCall(ajaxParameters);
                if (result['status'] == true) {
                    displayMessage(result['message'], 'success');
                    jQuery(this).removeClass('btn-disable-loader');
                    close_setting_picker();
                } else {
                    displayMessage('Email configuration or test mail is incorrect', 'error');
                    jQuery(this).removeClass('btn-disable-loader');
                }
            } catch (e) {
                displayMessage(e, 'error');
                jQuery(this).removeClass('btn-disable-loader');
            }
        } else {
            jQuery(this).removeClass('btn-disable-loader');
        }
    });


    jQuery("body").on("click", "#sd_viewMoreOrders", async function() {
        var cursorId = jQuery(this).attr('data-cursorId');
        var contractId = jQuery("#sd_contractId").val();
        let ajaxParameters = {
            method: "POST",
            dataValues: {
                cursorId: cursorId,
                contract_id: contractId,
                action: "getNextOrders"
            }
        };
        try {
            let result = await AjaxCall(ajaxParameters);
            jQuery("#sd_orderListing").append(result['ListHtml']);
            jQuery(".sd_viewMoreButton").html(result['viewMoreButton']);
        } catch (e) {
            displayMessage(e, true);
        }
    });

    //  contact us page button start here
    jQuery("body").on("click", "#sd_contactUs", async function() {
        let mailHeading = $.trim($('#sd_query_type').find(":selected").text());
        let sd_customerName = $.trim(jQuery("#sd_customerName").val());
        let sd_customerEmail = $.trim(jQuery("#sd_customerEmail").val());
        let sd_customerShop = $.trim(jQuery("#sd_customerShop").val());
        let sd_customerMsg = $.trim(jQuery("#sd_customerMsg").val());
        if (sd_customerName == '' || sd_customerEmail == '' || sd_customerShop == '' || sd_customerMsg == '') {
            displayMessage('Fill All the Fields', 'error');
        } else if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(sd_customerEmail))) {
            displayMessage('Enter Valid Email', 'error');
        } else {
            let ajaxParameters = {
                method: "POST",
                dataValues: {
                    sd_customerName: sd_customerName,
                    sd_customerEmail: sd_customerEmail,
                    sd_customerShop: sd_customerShop,
                    sd_customerMsg: sd_customerMsg,
                    mailHeading: mailHeading,
                    action: "send_contactUS_mail"
                }
            };
            try {
                let result = await AjaxCall(ajaxParameters);
                if (result['status'] == true) {
                    displayMessage(result['message'], 'success');
                    location.reload();
                } else {
                    displayMessage(result['message'], 'error');
                }
            } catch (e) {
                displayMessage(e, 'error');
            }
        }
    });

    //update contract setting data in the db
    jQuery("body").on("click", "#save_contract_settings", async function() {
        if (document.getElementById('sd_removeProduct').checked) {
            remove_product_from_contract = 'Yes';
        } else {
            remove_product_from_contract = 'No';
        }
        let contract_after_product_delete = $('#sd_contractStatus').find(":selected").text();
        if (document.getElementById('sd_failed_billingAttempt').checked) {
            after_billing_attempt_failed = 'Pause';
        } else {
            after_billing_attempt_failed = 'Active';
        }

        // var discount_seleted = $('#sd_productDiscount').find(":selected").text();
        // var discount_type = $('#sd_discount_type').find(":selected").text();
        data_values = {
            'remove_product': remove_product_from_contract,
            'after_product_delete_contract': contract_after_product_delete,
            'afterBillingAttemptFail': after_billing_attempt_failed,
            //    'discount': discount_seleted,
            //    'discount_type':discount_type
        };
        let whereconditionvalues = {
            "store_id": store_id
        }
        let ajaxParameters = {
            method: "POST",
            dataValues: {
                table: 'contract_setting',
                insertdata: data_values,
                wherecondition: whereconditionvalues,
                wheremode: 'and',
                action: "updateajax"
            }
        };
        try {
            let result = await AjaxCall(ajaxParameters);
            if (result['status'] == true) {
                displayMessage(result['message'], 'success');
                close_setting_picker();
            }
        } catch (e) {
            displayMessage(e, 'error');
        }
    });

    //select theme for configuration
    // jQuery("body").on('click', '#add_theme_code', async function(e) {
    //     let selectedThemeId = $("#sd_selectTheme option:selected").attr("data-id");
    //     let ajaxParameters = {
    //         method: "POST",
    //         dataValues: {
    //             themeId: selectedThemeId,
    //             action: "addThemeCode"
    //         }
    //     };
    //     try {
    //         let result = await AjaxCall(ajaxParameters);
    //         // displayMessage('Theme Configured Successfully',false);
    //     } catch (e) {
    //         // displayMessage(e,true);
    //     }
    // });

    jQuery("body").on('click', '#remove_theme_code', async function(e) {
        let selectedThemeId = $("#sd_selectTheme option:selected").attr("data-id");
        let ajaxParameters = {
            method: "POST",
            dataValues: {
                themeId: selectedThemeId,
                action: "removeThemeCode"
            }
        };
        try {
            let result = await AjaxCall(ajaxParameters);
        } catch (e) {
            // displayMessage(e,true);
        }
    });

    jQuery("body").on('click', '.back_button_subscription', function() {
        jQuery('#sd_productSpinner').removeClass('display-hide-label');
        if (Object.keys(before_updating_checkbox_data_picker_selection).length > 0) { // to check that the selected products added in the group or not
            picker_selection_checkboxes = JSON.parse(JSON.stringify(before_updating_checkbox_data_picker_selection));
        }
        if (form_type == 'create') { // to check that the selected products added in the group or not on the create scree
            let selected_product_count = document.querySelectorAll("#create-section-products-show li").length //validation fron picker_Selection_checkboxes irregularities
            if (selected_product_count == 0) {
                picker_selection_checkboxes = {};
            }
        }
        let show_popup = check_subscription_whole_form_change();
        redirect_link_global = jQuery(this).attr("data-redirect-link");
        if (show_popup) {
            let usecase = jQuery(this).attr("data-usecase");
            let heading = jQuery(this).attr("data-heading");
            let message = jQuery(this).attr("data-message");
            let acceptbuttontext = jQuery(this).attr("data-acceptbuttontext");
            let rejectbuttontext = jQuery(this).attr("data-rejectbuttontext");
            document.getElementById("sd_global_modal_container").innerHTML = confirmBoxModal(usecase, "", heading, message, acceptbuttontext, "Polaris-Button--destructive", rejectbuttontext);
        } else {
            list_subscription_mode();
            reset_plan_name_change();
            remove_all_error_messages();
        }
    });

    jQuery("body").on('click', '.navigate_contract_detail', function() {
        jQuery('#sd_subscriptionLoader').removeClass('display-hide-label');
        let searchParams = new URLSearchParams(window.location.search);
        let contractId = jQuery(this).attr('data-query-string');
        searchParams.set("contract_id", contractId);
        // if(store == 'thegivenget.myshopify.com' && contractId == 11664752921) {
        //     console.log(searchParams.toString());
        //     return false;
        // }
        window.location.search = searchParams.toString();
    });

    jQuery("body").on('click', '.navigate_element', function() {
        jQuery('#sd_subscriptionLoader').removeClass('display-hide-label');
        redirect_link_global = jQuery(this).attr("data-redirect-link");
        let show_confrimbox_first = jQuery(this).attr("data-confirmbox");
        redirect_query_string = jQuery(this).attr("data-query-string");
        let usecase = jQuery(this).attr("data-usecase");
        if (usecase == 'subscription_form_leave') {
            open(redirect_link_global, '_self');
        } else {
            if (show_confrimbox_first == "yes") {
                console.log('navigate_element2');
                let heading = jQuery(this).attr("data-heading");
                let message = jQuery(this).attr("data-message");
                let acceptbuttontext = jQuery(this).attr("data-acceptbuttontext");
                let rejectbuttontext = jQuery(this).attr("data-rejectbuttontext");
                document.getElementById("sd_global_modal_container").innerHTML = confirmBoxModal(usecase, "", heading, message, acceptbuttontext, "Polaris-Button--destructive", rejectbuttontext);
            } else {
                open(redirect_link_global, '_self');
            }
        }
    });

    jQuery("body").on('click', '.delete_subscription_plan', function() {
        let db_id = $(this).attr("data-id");
        let subscription_group_id = $(this).attr("subscription-group-id");
        let usecaseid = subscription_group_id;
        document.getElementById("sd_global_modal_container").innerHTML = confirmBoxModal("subscription_plan_delete", usecaseid, "Alert", subscription_delete_confirm_message, "Yes", "Polaris-Button--primary", "No");
        $('#subscription_plan_table').DataTable().$("[subscription-group-id="+subscription_group_id+"]").parents('tr').addClass('display-hide-label');
        // $('#subscription_plan_table').DataTable().row($("[subscription-group-id="+subscription_group_id+"]").parents('tr')).remove().draw();
    });

    // jQuery("body").on('click', '.remove_subscription_product', function(e) {
    //     let product_id = $(this).attr('attr-product-id');
    //     let variant_id = $(this).attr('attr-variant-id');
    //     var product_index = sd_subscription_selected_products.findIndex(p => p.id == 'gid://shopify/Product/' + product_id);
    //     let variant_length = sd_subscription_selected_products[product_index].variants.length;
    //     if (variant_length > 1) {
    //         let newVariantList = sd_subscription_selected_products[product_index].variants.filter((item) => item.id !== 'gid://shopify/ProductVariant/' + variant_id);
    //         sd_subscription_selected_products[product_index].variants = newVariantList;
    //     } else {
    //         sd_subscription_selected_products = sd_subscription_selected_products.filter((item) => item.id !== 'gid://shopify/Product/' + product_id);
    //     }
    //     jQuery("#variant_tag_" + variant_id).remove();
    //     if (!($('.product_tags')[0])) {
    //         let add_class_params = {
    //             class_elements: [{
    //                 name: "section-layout-select-products-show",
    //                 classname: "display-hide-label"
    //             }]
    //         };
    //         add_class(add_class_params);
    //     }
    // });

    jQuery("body").on('click', '.delete_frequency_card', function() {
        jQuery('#sd_subscriptionLoader').removeClass('display-hide-label');
        let card_serial_no = jQuery(this).attr('attr-card-serial-no');
        let sellingplanid = jQuery(this).attr('attr-id');
        let new_card_no = jQuery(this).attr('attr-edit-case-new-plan-array-index');
        delete_frequency_card(card_serial_no, sellingplanid, new_card_no);
        //new added code
        // if (form_type == 'edit') {
        //     let show_save_bar = check_subscription_whole_form_change();
        //     if (show_save_bar) {
        //         show_save_banner();
        //         jQuery('.save-subscription-plan').removeClass('btn-disable-loader');
        //     }
        // }
        // To hide frequency schemes section if in case no frequency card are deleted
        if (!(jQuery('#' + form_type + '_subscription_wrapper .sd-frequency-plan-card')[0])) {
            jQuery('.add-least-frequency-error').removeClass('display-hide-label');
            //new coded added
            jQuery('#' + form_type + '_subscription_wrapper .step3_button_group  button.step3_previous').removeClass('go-to-step1');
            jQuery('#' + form_type + '_subscription_wrapper .step3_button_group  button.step3_previous').addClass('go-to-step2');
        }
    });

    jQuery("body").on('click', '.save-subscription-plan', function() {
        jQuery(this).addClass('btn-disable-loader');
        let subscription_wholeplan_validation_result = subscription_wholeplan_validation(form_type);
        let submission_mode = '',
            whereconditionvalues = '',
            form_id = '',
            updateproducts = 'no',
            wheremodevalue = '',
            removeproducts = [],
            subscription_plan_id = '';
        if (subscription_wholeplan_validation_result == "true") {
            displayMessage('Updating', 'waiting');
            if (form_type == 'edit') {
                subscription_plan_id = db_edit_subscriptionplan_id;
                submission_mode = 'editsubscription';
                // compare arrays
                // let checkproductupdate = JSON.stringify(sd_subscription_edit_case_already_selected_products) == JSON.stringify(picker_selection_checkboxes);
                //new added code
                Object.keys(picker_selection_checkboxes).forEach(function(key) {
                    delete picker_selection_checkboxes[key].product_handle;
                    delete picker_selection_checkboxes[key].price;
                    delete picker_selection_checkboxes[key].quantity;
                });

                let checkproductupdate = Object.keys(sd_subscription_edit_case_already_selected_products)
                    .filter(x => !(Object.keys(picker_selection_checkboxes)).includes(x))
                    .concat(Object.keys(picker_selection_checkboxes).filter(x => !(Object.keys(sd_subscription_edit_case_already_selected_products)).includes(x)));

                // if checkproductupdate is true
                if (checkproductupdate.length == 0) {
                    //The arrays have the same elements
                    updateproducts = false;
                } else {
                    //The arrays have different elements
                    updateproducts = true;
                    //removeproducts =  sd_subscription_edit_case_already_selected_products;
                }
            } else if (form_type == 'create') {
                submission_mode = 'createsubscription';
            }
            let subscription_plan_name = jQuery('.subscription_list_' + subscription_plan_id + ' .edit-subscription-left .sd_subscription_heading').text();
            let edit_case_data = {
                'sd_subscription_edit_case_already_existing_plans_array': sd_subscription_edit_case_already_existing_plans_array,
                'sd_subscription_edit_case_to_be_added_new_plans_array': sd_subscription_edit_case_to_be_added_new_plans_array,
                'sd_subscription_edit_case_to_be_deleted_plans_array': sd_subscription_edit_case_to_be_deleted_plans_array,
                'updateproducts': updateproducts,
                'removeproducts': sd_subscription_edit_case_already_selected_products,
                'subscription_plan_id': subscription_plan_id,
                'plan_name': subscription_plan_name
            }
            subscription_form_submission(submission_mode, edit_case_data);
        } else {
            jQuery(this).removeClass('btn-disable-loader');
            jQuery('Polaris-Frame-Toast').addClass('display-hide-label');
        }
    });

    jQuery("body").on('click', '.edit-frequency-cancel-button', function() {
        jQuery('#sd_add_frequency_span').html('Add Frequency');
        jQuery('#sd_add_frequency').removeClass('UpdateFrequency');
        jQuery('.edit_frequency_button_group').addClass('display-hide-label');
        jQuery('#edit_frequency_card_serial_no').val('');
        jQuery('#selling_plan_id').val('');
        change_select_html('#frequency_plan_type', 'Pay Per Delivery');
        jQuery('#frequency_plan_type').trigger("change");
        reset_form("sd-subscription-frequency-form"); // reset form for new entry
    });

    jQuery("body").on('click', '.delete_frequency_card_ask_confirmation', function(e) {
        if(jQuery('.edit-subscription-right .sd-frequency-plan-card').length  == 1){
          jQuery(this).prop('disabled', true);
        }else{
            let card_serial_no = $(this).attr('attr-card-serial-no');
            jQuery('#sd_frequency_card_serialno_' + card_serial_no + ' .frequency-delete-confirmation').removeClass('display-hide-label');
            jQuery('#sd_frequency_card_serialno_' + card_serial_no + ' .frequency-card-actions').addClass('display-hide-label');
        }
    });

    jQuery("body").on('click', '.delete_frequency_card_cancel', function() {
        let card_serial_no = $(this).attr('attr-card-serial-no');
        jQuery('#sd_frequency_card_serialno_' + card_serial_no + ' .frequency-delete-confirmation').addClass('display-hide-label');
        jQuery('#sd_frequency_card_serialno_' + card_serial_no + ' .frequency-card-actions').removeClass('display-hide-label');
        reset_form("create-selling-plan-form"); // reset form to empty fields

    });

    jQuery("body").on('click', '.copy_sellingform_to_create', function() {
        edit_subscription_popup_close();
    });

    jQuery("body").on('click', '.edit_subscription_add_new_selling_plan', function() {
        to_check_existing_plan_array = Object.assign({}, sd_subscription_edit_case_already_existing_plans_array);
        to_check_new_plan_array = sd_subscription_edit_case_to_be_added_new_plans_array;
        // show pop up with edit case cancel buttons & hide create case buttons
        reset_form("create-selling-plan-form"); // reset form to empty fields
        jQuery(".create-subscription-buttons").addClass("display-hide-label");
        jQuery(".edit-subscription-buttons").removeClass("display-hide-label");
        jQuery("#sd_global_modal_container").html(`<form method="post" id="subscription_plan_edit_form" ></form>`);
        jQuery("#subscription_plan_edit_form").append(jQuery(".create-selling-plan-form"));
        jQuery("#sd_popup").removeClass("display-hide-label");
        jQuery('#sd_add_frequency').html('Add');
        jQuery('#sd_add_frequency').removeClass('UpdateFrequency');

    });


    jQuery("body").on('click', '.edit_frequency_card', function() {
        reset_form("create-selling-plan-form"); // reset form to empty fields
        let card_serial_no = jQuery(this).attr('attr-card-serial-no');
        let sellingplanid = jQuery(this).attr('attr-id');
        let card_detail, new_card_no;
        to_check_existing_plan_array = Object.assign({}, sd_subscription_edit_case_already_existing_plans_array);
        to_check_new_plan_array = sd_subscription_edit_case_to_be_added_new_plans_array;
        if (db_edit_subscriptionplan_id.length) {
            //--------edit subscription case---------
            // show pop up with edit case cancel buttons & hide create case buttons
            jQuery(".create-subscription-buttons").addClass("display-hide-label");
            jQuery(".edit-subscription-buttons").removeClass("display-hide-label");
            jQuery("#sd_global_modal_container").html(`<form method="post" id="subscription_plan_edit_form" ></form>`);
            jQuery("#subscription_plan_edit_form").append(jQuery(".create-selling-plan-form"));
            jQuery("#sd_popup").removeClass("display-hide-label");
            if (sellingplanid.length) {
                card_detail = sd_subscription_edit_case_already_existing_plans_array[sellingplanid];
                delete to_check_existing_plan_array[sellingplanid];
            } else {
                new_card_no = jQuery(this).attr('attr-edit-case-new-plan-array-index');
                card_detail = sd_subscription_edit_case_to_be_added_new_plans_array[new_card_no];
                to_check_new_plan_array.splice(new_card_no, 1);
            }
        } else {
            //create subscription case
            card_detail = sd_frequency_plans_array[card_serial_no];
            // show pop up cancel buttons & hide create case buttons
            jQuery(".create-subscription-buttons").removeClass("display-hide-label");
            jQuery(".edit-subscription-buttons").addClass("display-hide-label");
        }
        let frequency_plan_type = card_detail.frequency_plan_type;

        jQuery('#sd_add_frequency').html('Update');
        jQuery('#sd_add_frequency').addClass('UpdateFrequency');
        //jQuery('.edit_frequency_button_group').removeClass('display-hide-label');
        jQuery('#edit_frequency_card_serial_no').val(card_serial_no);
        jQuery('#new_card_no').val(new_card_no);
        jQuery('#sellingplanid').val(card_detail.sellingplanid);
        jQuery('#minimum_number_cycle').val(card_detail.minimum_number_cycle);
        if(card_detail.maximum_number_cycle == 0){
            jQuery('#maximum_number_cycle').val('');
        }else{
           jQuery('#maximum_number_cycle').val(card_detail.maximum_number_cycle);
        }
        jQuery('#sd_description').val(card_detail.sd_description);
        change_select_html('#frequency_plan_type', frequency_plan_type);
        jQuery('#frequency_plan_type').trigger("change");
        jQuery('#frequency_plan_name').val(card_detail.frequency_plan_name);
        jQuery('#cut_off_days').val(card_detail.cut_off_days);
        // jQuery('#frequency_plan_name').val(card_detail.frequency_plan_name);
        change_select_html('#sd_per_delivery_order_frequency_type', card_detail.per_delivery_order_frequency_type);
        if (frequency_plan_type == 'Pay Per Delivery') {
            jQuery('#sd_per_delivery_order_frequency_value').val(card_detail.per_delivery_order_frequency_value);

        } else if (frequency_plan_type == 'Prepaid') {
            jQuery('#sd_per_delivery_order_frequency_value').val(card_detail.per_delivery_order_frequency_value); //per_delivery_order_frequency_value
            jQuery('#sd_prepaid_billing_value').val(card_detail.prepaid_billing_value);
            // change_select_html('#sd_prepaid_fullfillment_type',card_detail.prepaid_fullfillment_type);
            change_select_html('#sd_per_delivery_order_frequency_type', card_detail.per_delivery_order_frequency_type);
            change_select_html('#sd_prepaid_billing_type', card_detail.per_delivery_order_frequency_type);
        }

        if (card_detail.hasOwnProperty("subscription_discount")) {
            jQuery('#subscription_discount_status').prop('checked', true);
            jQuery('#subscription_discount_status').trigger("change");
            jQuery('#subscription_discount_value').val(card_detail.subscription_discount_value);
            jQuery('.sd_subscription_discount_offer_wrapper').removeClass('display-hide-label');
            if (card_detail.subscription_discount_type == 'P' || card_detail.subscription_discount_type == 'Percent Off(%)') {
                change_select_html('#subscription_discount_type', 'Percent Off(%)');
            } else if (card_detail.subscription_discount_type == 'A' || card_detail.subscription_discount_type == 'Discount Off') {
                change_select_html('#subscription_discount_type', 'Discount Off');
            }
        } else {
            jQuery('#subscription_discount_status').prop('checked', false);
            jQuery('#subscription_discount_status').trigger("change");
        }

        if (card_detail.per_delivery_order_frequency_type != 'DAY' && card_detail.per_delivery_order_frequency_type != 'YEAR') {
            jQuery('.sd_set_anchor_date').removeClass('display-hide-label');
        }
        //anchor data start
        if (card_detail.hasOwnProperty('sd_set_anchor_date')) {
            jQuery('#sd_set_anchor_date').prop('checked', true);
            jQuery('.sd_anchor_option').removeClass('display-hide-label');
            change_select_html('#sd_anchor_option', card_detail.sd_anchor_option);
            if (card_detail.sd_anchor_option == 'On Specific Day') {
                if (card_detail.hasOwnProperty('sd_anchor_month_day') && card_detail.per_delivery_order_frequency_type == 'MONTH') {
                    jQuery('.sd_anchor_month_day').removeClass('display-hide-label');
                    jQuery('#sd_anchor_month_day').val(card_detail.sd_anchor_month_day);
                } else {
                    jQuery('.sd_anchor_week_day').removeClass('display-hide-label');
                    change_select_html('#sd_anchor_week_day', card_detail.sd_anchor_week_day);
                }
                jQuery('.cut_off_days').removeClass('display-hide-label');
            }
        }
        //discount after cycle change
        if (card_detail.hasOwnProperty("subscription_discount_after")) {
            jQuery('#subscription_discount_after_status').prop('checked', true);
            jQuery('#subscription_discount_after_status').trigger("change");
            jQuery('#discount_value_after').val(card_detail.discount_value_after);
            jQuery('#change_discount_after_cycle').val(card_detail.change_discount_after_cycle);
            if (card_detail.subscription_discount_type_after == 'P' || card_detail.subscription_discount_type_after == 'Percent Off(%)') {
                change_select_html('#subscription_discount_type_after', 'Percent Off(%)');
            } else if (card_detail.subscription_discount_type_after == 'A' || card_detail.subscription_discount_type_after == 'Discount Off') {
                change_select_html('#subscription_discount_type_after', 'Discount Off');
            }
        } else {
            jQuery('#subscription_discount_after_status').prop('checked', false);
            jQuery('#subscription_discount_after_status').trigger("change");
        }

        active_step('2');
        step2_preps('edit');
        done_step('1');
        unfinshed_step('3');
        //jQuery('.step2_button_cancel').removeClass('display-hide-label');
        //jQuery('.step2_button_previous').addClass('display-hide-label');
        //jQuery('.step2_button_submit').html('Update');
    });

    // memberplan.php page
    $("ul.tabs li").click(function() {
        $("ul.tabs li").removeClass("active");
        $(this).addClass("active");
        $(".tabContent").hide();
        var activeTab = $(this).find("a").attr("href");
        $(activeTab).fadeIn();
        // return false;
    });

    // jQuery("body").on('click', ".sd_selectedPlan", async function() {
    //     jQuery(this).addClass('btn-disable-loader');
    //     var selectedPlanId = this.id;
    //     var sd_subscriptionPlans = $("#sd_subscriptionPlans_" + selectedPlanId).attr('data-value');
    //     var sd_subscriptionContract = $("#sd_subscriptionContract_" + selectedPlanId).attr('data-value');
    //     var sd_planPrice = $("#sd_planPrice_" + selectedPlanId).attr('data-value');
    //     var sd_trialDays = $("#sd_trialDays_" + selectedPlanId).attr('data-value');
    //     var pageType = $("#sd_pageType").val();
    //     var sd_code = $("#sd_code").val();
    //     var configureTheme = $("#configureTheme").val();
    //     var sd_planName = $("#sd_planName_" + selectedPlanId).attr('data-value');

    //     let recurringBillingValues = {
    //         "plan_id": selectedPlanId,
    //         "sd_subscriptionPlans": sd_subscriptionPlans,
    //         "sd_subscriptionContract": sd_subscriptionContract,
    //         "sd_planPrice": sd_planPrice,
    //         "sd_trialDays": sd_trialDays,
    //         "pageType": pageType,
    //         "sd_code": sd_code,
    //         "sd_planName": sd_planName,
    //         "sd_configureTheme": configureTheme
    //     }
    //     let ajaxParameters = {
    //         method: "POST",
    //         dataValues: {
    //             recurringBillingdata: recurringBillingValues,
    //             action: "recurringBilling"
    //         }
    //     };
    //     try {
    //         let result = await AjaxCall(ajaxParameters);
    //         if (result['status'] == true) {
    //             setLocalStorage('recurring_id', result['recurring_id']);
    //             window.top.location.href = result['confirmationUrl'];
    //         } else if (result['status'] == false && result['error'] == 'member_plan_error') {
    //             deleteContract = '', deleteSubscription = '', addand = '';
    //             memberPlanErrorPopup = 'The Plan you have selected allows to create ' + sd_subscriptionContract + ' Subscription Contract and ' + sd_subscriptionPlans + ' Subscription Plan group but you have already created total';
    //             if (result['createdExtraContract'] != 'no' && result['createdExtraSubscription'] != 'no') {
    //                 addand = ' and ';
    //             }
    //             if (result['createdExtraContract'] != 'no') {
    //                 memberPlanErrorPopup += (parseInt(sd_subscriptionContract) + parseInt(result['createdExtraContract'])) + ' subscription contract, ';
    //                 deleteContract = 'cancel ' + result['createdExtraContract'] + '<a target="_blank" href="' + SHOPIFY_DOMAIN_URL + '"/backend/view/subscriptions.php?shop=' + store + '"> subcription Contract</a>';
    //             }
    //             if (result['createdExtraSubscription'] != 'no') {
    //                 memberPlanErrorPopup += (parseInt(sd_subscriptionPlans) + parseInt(result['createdExtraSubscription'])) + ' subscription Plan Group, ';
    //                 deleteSubscription = 'delete ' + result['createdExtraSubscription'] + '<a target="_blank" href="' + SHOPIFY_DOMAIN_URL + '"/backend/view/dashboard.php?shop="' + store + '"> Subscription Plan Group</a>';
    //             }
    //             memberPlanErrorPopup += 'to update the plan to the selected plan you have to ' + deleteContract + addand + deleteSubscription;
    //             document.getElementById("sd_global_modal_container").innerHTML = confirmBoxModal("contractMemberPlanUpdate", '', "update Member Plan Error", memberPlanErrorPopup, "OK", "Polaris-Button--primary", "Cancel");
    //             jQuery(this).removeClass('btn-disable-loader');
    //         }
    //     } catch (e) {
    //         jQuery(this).removeClass('btn-disable-loader');
    //         displayMessage(e, true);
    //     }
    // });

    jQuery("body").on("click", ".go-to-step1", function() {
        jQuery('.show_selected_products').removeClass('display-hide-label');
        active_step('1');
        unfinshed_step('2');
        unfinshed_step('3');
    });

    jQuery("body").on("click", ".go-to-step3", function() {
        step3_preps();
    });

    jQuery("body").on("click", ".go-to-step2", function() {
        // new added code
        if (form_type == 'edit') {
            $('.edit_subscription_add_new_selling_plan').click();
        } else {
            step2_preps('add');
        }
    });

    //update next billing date start
    jQuery("body").on('click', '.update_next_billing_date', function(e) {
        const date = new Date();
        let currentDay= String(date.getDate()).padStart(2, '0');
        let currentMonth = String(date.getMonth()+1).padStart(2,"0");
        let currentYear = date.getFullYear();
        let currentDate = `${currentYear}-${currentMonth}-${currentDay}`;
        e.target.classList.add('display-hide-label');
        jQuery('.show_date_selection').removeClass('display-hide-label');
        var next_billing_date = jQuery('#select_next_billing_date').val();
        var selected_next_billing_date = jQuery('#select_next_billing_date').attr('next-billing-date');
        jQuery("#select_next_billing_date").datepicker({
            "dateFormat": "yy-mm-dd",
            minDate: next_billing_date,
        });
        jQuery("#select_next_billing_date").datepicker('setDate', selected_next_billing_date);
    });

    jQuery("body").on('click', '.update_delivery_billing_frequency', function(e) {
        jQuery('.update_delivery_billing_frequency').addClass('display-hide-label');
        jQuery('.show_delivery_billing_selection').removeClass('display-hide-label');
        var next_billing_date = jQuery('#select_next_billing_date').val();
    });

    jQuery('body').on('click','.update_delivery_billing_period', async function(e){
        var delivery_billing_frequency = jQuery('#sd_per_delivery_order_frequency_value').val();
        var delivery_billing_type = jQuery('#sd_pay_per_delivery_billing_type').val();
        var contract_id = jQuery(this).attr('data-contract_id');
        var line_item_id = jQuery("#contract_line_id").val();
        var changed_frequency = {
            'delivery_billing_frequency' : delivery_billing_frequency,
            'delivery_billing_type' : delivery_billing_type,
            'line_item_id' : line_item_id
        }
        let whereconditionvalues = {
            "store_id": store_id,
            'contract_id' : contract_id
        }
        let ajaxParameters = {
            method: "POST",
            dataValues: {
                data_values: changed_frequency,
                wherecondition: whereconditionvalues,
                action: "update_delivery_billing_frequency"
            }
        };
        try {
            let result = await AjaxCall(ajaxParameters);
            if (result['status'] == true) {
               displayMessage(result['message'], 'success');
               location.reload();
               displayMessage(result['message'], 'success');
            }
        } catch (e) {
            displayMessage(e, 'error');
        }
    });

    function hide_update(class_name){
        jQuery('.'+class_name).removeClass('display-hide-label');
        jQuery('.show_date_selection,.show_delivery_billing_selection').addClass('display-hide-label');
    }

    jQuery('body').on('click','.cancel_update', function(e){
        var update_class = jQuery(this).attr('data-id');
        hide_update(update_class);
    });

    jQuery('body').on('click','.update_next_date', async function(e){
        var previous_date = jQuery(this).attr('current-timezonedate');
        var contract_id = jQuery(this).attr('data-contract_id');
        var change_day_value = jQuery(this).attr('value-change');
        var changed_date = jQuery('#select_next_billing_date').val();
        var interval_days = jQuery(this).attr('data-interval_value');
        const date1 = new Date(previous_date);
        const date2 = new Date(changed_date);
        const diffTime = Math.abs(date2 - date1);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        // if(diffDays == 0){
        //     hide_date_update();
        // }else{
            if(change_day_value != 'no_change'){
                if(change_day_value == 'increase'){
                    var new_date_string = new Date(changed_date);
                    new_date_string.setDate(new_date_string.getDate() + parseInt(interval_days));
                }else if(change_day_value == 'decrease'){
                    var new_date_string = new Date(changed_date);
                    new_date_string.setDate(new_date_string.getDate() - parseInt(interval_days));
                }
            }else{
                new_date_string = new Date(changed_date);
            }
            var mnths = { Jan: "01", Feb: "02", Mar: "03", Apr: "04", May: "05", Jun: "06", Jul: "07", Aug: "08", Sep: "09", Oct: "10", Nov: "11", Dec: "12" },
            date = (new_date_string + "").split(" ");
            new_changed_date = [date[3], mnths[date[1]], date[2]].join("-");
            let whereconditionvalues = {
                "store_id": store_id,
                'contract_id' : contract_id
            }
            date_change_data = {
                'next_billing_date' : new_changed_date
            }
            var adminEmail = document.getElementById('sendMailToAdmin').value;
            var customerEmail = document.getElementById('sendMailToCustomer').value;
            let ajaxParameters = {
                method: "POST",
                dataValues: {
                    table: 'subscriptionOrderContract',
                    data_values: date_change_data,
                    specific_contract_data : specific_contract_data,
                    contract_product_details : contract_existing_products,
                    update_renewal_date : 'update_renewal_date',
                    new_renewal_date : ([date[2], date[1], date[3]].join(" ")),
                    adminEmail : adminEmail,
                    customerEmail : customerEmail,
                    wherecondition: whereconditionvalues,
                    wheremode: 'and',
                    action: "insertupdateajax"
                }
            };
            try {
                let result = await AjaxCall(ajaxParameters);
                if (result['status'] == true) {
                    displayMessage(result['message'], 'success');
                    location.reload();
                }
            } catch (e) {
                displayMessage(e, 'error');
            }
        // }
    });
    //update next billing end

    jQuery("body").on('change', 'input[name="template_type"]', async function() {
        var checked_value = $(this).val();
        if(this.checked){
            if(checked_value == 'default'){
                $('#sd_custom_template').prop('checked', false);
            }else if(checked_value == 'custom'){
                $('#sd_default_template').prop('checked', false);
            }
        }
    });


    jQuery("body").on("click", ".subscription_validate_step1", async function() {
        let error_values = {};
        jQuery(this).addClass('btn-disable-loader');
        let planName = $.trim($('#subscription_plan_name').val());
        if (planName.length == 0) {
            error_values["subscription_plan_name_error"] = "Empty Field";
            jQuery(".subscription_plan_name_error").removeClass('display-hide-label');
        } else {
            jQuery(".subscription_plan_name_error").addClass('display-hide-label');
        }
        let selected_product_count = document.querySelectorAll("#create-section-products-show li").length //validation fron picker_Selection_checkboxes irregularities
        if (selected_product_count == 0) {
            error_values["subscription_add_product_error"] = "No Product Selected";
            jQuery(".subscription_add_product_error").removeClass('display-hide-label');
        } else {
            jQuery(".subscription_add_product_error").addClass('display-hide-label');
        }
        let ajaxParameters = {
            method: "POST",
            dataValues: {
                action: "planName_Duplicacy",
                planName: planName
            }
        };
        try {
            let result = await AjaxCall(ajaxParameters);
            jQuery(this).removeClass('btn-disable-loader');
            if (result['status'] == false) {
                error_values["subscription_plan_name_already_exist_error"] = "Plan Name already exist";
                jQuery('.subscription_plan_name_already_exist_error').removeClass('display-hide-label');
            } else {
                jQuery('.subscription_plan_name_already_exist_error').addClass('display-hide-label');
            }
        } catch (e) {
            displayMessage(e, true);
        }

        if (Object.keys(error_values).length == 0) {
            if (jQuery('.sd-frequency-plan-card')[0]) {
                step3_preps();
            } else {
                step2_preps('add');
            }
        } else {

        }
    });

    jQuery("body").on('click', '#sd_add_frequency', function(e) { // add frequency button click
        let frequency_number_validation = true;
        if (!jQuery(this).hasClass("UpdateFrequency")) {
            if (db_edit_subscriptionplan_id.length) {
                //edit subcription case
                let check_total = (parseInt(Object.keys(sd_subscription_edit_case_to_be_added_new_plans_array).length) + parseInt(Object.keys(sd_subscription_edit_case_already_existing_plans_array).length)) - parseInt(Object.keys(sd_subscription_edit_case_to_be_deleted_plans_array).length);
                if (check_total >= 20) {
                    frequency_number_validation = false;
                }
            } else if (Object.keys(sd_frequency_plans_array).length >= 20) {
                //create subcription case
                frequency_number_validation = false;
            }
        }
        if (frequency_number_validation) {
            let subscription_validation_result = subscription_frequency_validation();
            let sellingplanid = jQuery('#sellingplanid').val();
            let new_card_no = jQuery('#new_card_no').val();
            if (subscription_validation_result == "true") {
                let edit_frequency_card_serial_no = jQuery('#edit_frequency_card_serial_no').val();
                create_frequency_plan_card(edit_frequency_card_serial_no, db_edit_subscriptionplan_id, sellingplanid, new_card_no);
                change_select_html('#frequency_plan_type', 'Pay Per Delivery');
                jQuery('#frequency_plan_type').trigger("change");
                done_step('1');
                done_step('2');
                active_step('3');
            }
            // new code added
            if (form_type == 'create') {
                jQuery('#' + form_type + '_subscription_wrapper .step3_button_group  button.step3_previous').addClass('go-to-step1');
                jQuery('#' + form_type + '_subscription_wrapper .step3_button_group  button.step3_previous').removeClass('go-to-step2');
            }
        } else {
            let remove_class_params = {
                class_elements: [{
                    name: "add-10only-frequency-error",
                    classname: "display-hide-label"
                }]
            };
            remove_class(remove_class_params);
        }

        let show_save_bar = check_subscription_whole_form_change();
        if (show_save_bar) {
            show_save_banner();
        } else {
            hide_save_banner();
        }
    });

    function titleCase(string){
        return string[0].toUpperCase() + string.slice(1).toLowerCase();
    }


    function decodeHtmlEntities(html) {
        var txt = document.createElement("textarea");
        txt.innerHTML = html;
        return txt.value;
    }

    //list view complete edit
    jQuery("body").on("click", ".edit-subscription-group", async function() {
        // var pre_selected_products = {};
        //to add loader when click on add button
        jQuery('#sd_subscriptionLoader,.show_selected_products').removeClass('display-hide-label');
        //   let spinner_html = add_small_spinner();
        // jQuery(this).html(spinner_html);
        let select_type = jQuery(this).attr('data-type');
        form_type = 'edit';
        jQuery(this).addClass('btn-disable-loader');
        clear_edit_screen_subscriptionPlan();
        db_edit_subscriptionplan_id = jQuery(this).attr('subscription-group-id');
        jQuery('#db_edit_subscriptionplan_id').val(db_edit_subscriptionplan_id);
        let ajaxParameters = {
            method: "POST",
            dataValues: {
                action: "view_subscription_plan",
                subscription_group_id: db_edit_subscriptionplan_id,
            }
        };
        try {
            let result = await AjaxCall(ajaxParameters);
            if (result['status'] == true) {
                edit_subscription_mode();
                sd_frequency_card_serialno = 0;
                // ----------------------------Set The whole Edit View ----------------------
                let all_products = result['subscription_products'];
                //add class to edit wrapper
                jQuery('#edit_subscription_wrapper').addClass('subscription_list_' + db_edit_subscriptionplan_id);
                if (select_type == 'products') {
                    jQuery("button[target-tab=subscription_edit_schemes]").removeClass('Polaris-Tabs__Tab--selected');
                    jQuery("#subscription_edit_schemes").addClass('Polaris-Tabs__Panel--hidden');
                    jQuery("button[target-tab=subscription_edit_products]").addClass('Polaris-Tabs__Tab--selected');
                    jQuery("#subscription_edit_products").removeClass('Polaris-Tabs__Panel--hidden');
                }
                //set delete button attribute
                if (all_products.length > 0) {
                    //show products tags & initialize picker global array variable
                    jQuery.each(all_products, function(key, value) {
                        if (pre_selected_products.hasOwnProperty("gid://shopify/Product/"+value.product_id)) {
                        } else {
                            pre_selected_products["gid://shopify/Product/"+value.product_id] = [];
                        }
                        pre_selected_products["gid://shopify/Product/"+value.product_id].push("gid://shopify/ProductVariant/"+value.variant_id);
                        if(value.variant_name == 'Default Title'){
                            variant_name = '';
                        }else{
                            variant_name = value.variant_name;
                        }
                        let html_tag = picker_tag_html(value.product_id, value.variant_id, value.product_name, variant_name, value.Imagepath);

                        jQuery('#subscription_edit_prodcts').append(html_tag);
                        newAddedProducts = {};
                        newAddedProducts['variant_id'] = value.variant_id;
                        newAddedProducts['product_id'] = value.product_id;
                        newAddedProducts['product_title'] = value.product_name; //to remove apostrophe
                        // newAddedProducts['variant_name'] = (value.variant_id).replace(/'/g, 'A'); //to remove apostrophe
                        newAddedProducts['variant_title'] = variant_name;
                        newAddedProducts['image'] = value.Imagepath;
                        picker_selection_checkboxes[value.variant_id] = {};
                        picker_selection_checkboxes[value.variant_id] = newAddedProducts;
                    });
                } else {
                    jQuery('.subscription_add_product_error').removeClass('display-hide-label');
                }
                console.log('pre_selected_products', pre_selected_products);
                sd_subscription_edit_case_already_selected_products = JSON.parse(JSON.stringify(picker_selection_checkboxes));
                //prepare mini open
                let existing_products_number = Object.keys(all_products).length;
                let existing_selling_plans_number = Object.keys(result['subscription_frequency_plans']).length;
                let planName = result['subscription_plan_name'];
                jQuery('#edit_subscription_wrapper').find('.sd_subscription_heading').html(planName);
                jQuery('#edit_subscription_wrapper').find('.change_plan_name').attr("plan-name-value", planName);
                jQuery('#edit_subscription_wrapper').find('.change_plan_name').attr("subscription-group-id", db_edit_subscriptionplan_id);
                jQuery('#edit_subscription_wrapper').find('.save_plan_name').attr("subscription-group-id", db_edit_subscriptionplan_id);
                jQuery('#edit_subscription_wrapper').find('.delete_subscription_plan').attr("subscription-group-id", db_edit_subscriptionplan_id);
                jQuery('#edit_subscription_wrapper').find('.edit_total_products').html(existing_products_number);
                jQuery('#edit_subscription_wrapper').find('.edit_total_selling_plans').html(existing_selling_plans_number);

                // prepare cards
                let htmlCard = '';
                jQuery.each(result['subscription_frequency_plans'], function(key, value) {
                    if(result['subscription_frequency_plans'].length == 1){
                       delete_button_disable = 'disabled';
                    }else if(result['subscription_frequency_plans'].length > 0){
                       delete_button_disable = '';
                    }
                    let plantype, liHtml, discounttype, discountstatus, discountstatus_after;
                    liHtml = `<li class="Polaris-List__Item"><b>Delivery Every</b>  ` + value.delivery_policy + ` ` + value.delivery_billing_type + `</li>`;
                    if (value.plan_type == 1) {
                        plantype = "Pay Per Delivery";
                        prepaid_delivery = '';
                    } else if (value.plan_type == 2) {
                        plantype = "Prepaid";
                        prepaid_delivery =  value.billing_policy+ ' ' + titleCase(value.delivery_billing_type) + '(s) prepaid subscription,';
                        liHtml += `<li class="Polaris-List__Item"><b>Billing Period</b> ` + value.billing_policy + ` ` + value.delivery_billing_type + `</li>`;
                    }
                    //new added code
                    if (value.billing_policy == 0) {
                        billing_policy_value = '';
                    } else {
                        billing_policy_value = value.billing_policy;
                    }
                    delivery_every = titleCase(value.delivery_billing_type);
                    if (value.delivery_policy != 1) {
                        delivery_every = value.delivery_policy + ' ' + titleCase(value.delivery_billing_type);
                    }
                    selling_plan_name = $.trim(prepaid_delivery + ' Delivery Every ' + delivery_every);
                    sd_subscription_edit_case_already_existing_plans_array[value.selling_plan_id] = {
                        frequency_plan_type: plantype,
                        sellingplanid: value.selling_plan_id,
                        frequency_plan_name: escapeHtmlToCode(value.plan_name),
                        per_delivery_order_frequency_value: value.delivery_policy,
                        per_delivery_order_frequency_type: value.delivery_billing_type,
                        prepaid_billing_value: billing_policy_value,
                        minimum_number_cycle: value.min_cycle,
                        maximum_number_cycle: value.max_cycle,
                        cut_off_days: value.cut_off_days,
                        selling_plan_name: $.trim(selling_plan_name),
                        sd_description:value.plan_description
                    };

                    if (value.discount_type == 'P') {
                        discounttype = "Percent Off(%)";
                    } else {
                        discounttype = "Discount Off";
                    }
                    if (value.anchor_type != '2') {
                        sd_subscription_edit_case_already_existing_plans_array[value.selling_plan_id]['sd_set_anchor_date'] = "on";
                        if (value.delivery_billing_type == 'WEEK') {
                            sd_subscription_edit_case_already_existing_plans_array[value.selling_plan_id]['sd_anchor_week_day'] = weekDaysArray[value.anchor_day];
                        } else {
                            sd_subscription_edit_case_already_existing_plans_array[value.selling_plan_id]['sd_anchor_month_day'] = value.anchor_day;
                        }
                        if (value.anchor_type == '0') {
                            sd_subscription_edit_case_already_existing_plans_array[value.selling_plan_id]['sd_anchor_option'] = "On Purchase Day";
                        } else if (value.anchor_type == '1') {
                            sd_subscription_edit_case_already_existing_plans_array[value.selling_plan_id]['sd_anchor_option'] = "On Specific Day";
                        }
                    }
                    if (value.discount_offer != 0) {
                        discountstatus = "on";
                        sd_subscription_edit_case_already_existing_plans_array[value.selling_plan_id]["subscription_discount"] = "on"
                        liHtml += `<li class="Polaris-List__Item"><b>Discount</b>  ` + value.discount_value + ` ` + discounttype + `</li>`;
                    }
                        discount_value = value.discount_value;

                    // recurring discount
                    if (value.discount_type_after == 'P') {
                        discounttype_after = "Percent Off(%)";
                    } else {
                        discounttype_after = "Discount Off";
                    }
                    if (value.recurring_discount_offer != 0) {
                        discountstatus_after = "on";
                        sd_subscription_edit_case_already_existing_plans_array[value.selling_plan_id]["subscription_discount_after"] = "on"
                        liHtml += `<li class="Polaris-List__Item"><b>After ` + value.change_discount_after_cycle + ` cycle</b>  ` + value.discount_value_after + ` ` + discounttype_after + `</li>`;
                    }
                        discount_value_after = value.discount_value_after;

                    sd_subscription_edit_case_already_existing_plans_array[value.selling_plan_id]['subscription_discount_value'] = discount_value;
                    sd_subscription_edit_case_already_existing_plans_array[value.selling_plan_id]['subscription_discount_type'] = discounttype;
                    sd_subscription_edit_case_already_existing_plans_array[value.selling_plan_id]['discount_value_after'] = discount_value_after;
                    sd_subscription_edit_case_already_existing_plans_array[value.selling_plan_id]['subscription_discount_type_after'] = discounttype_after;
                    sd_subscription_edit_case_already_existing_plans_array[value.selling_plan_id]['change_discount_after_cycle'] = value.change_discount_after_cycle;
                    htmlCard += `<div attr-edit-case-new-plan-array-index="" id="sd_frequency_card_serialno_` + key + `" class="Polaris-Layout__Section Polaris-Layout__Section--oneThird sd-frequency-plan-card">
                        <div class="Polaris-Card1">
                           <div class="Polaris-Card__Section sd_frequency_section">
                              <div class="Polaris-Card__SectionHeader">
                                 <div class="Polaris-Stack Polaris-Stack--alignmentBaseline sd_frequency_stack">
                                    <div class="Polaris-Stack__Item Polaris-Stack__Item--fill sd_frequencyPlans">
                                       <h3 aria-label="Contact Information" class="Polaris-Subheading">
                                          ` + plantype + `
                                       </h3>
                                    </div>

                                 </div>
                              </div>
                              <ul class="Polaris-List"><li class="Polaris-List__Item"><b>Selling Plan Name</b>  ` + escapeHtmlToCode(value.plan_name) + `</li>`;
                    htmlCard += liHtml + `
                              </ul>
                           </div>
						    <div class="Polaris-Stack__Item sd-frequency-plan-actions">
                                       <div class="Polaris-ButtonGroup display-hide-label frequency-delete-confirmation">
                                          <span class="frequency_delete_confirm_message_span">Are you sure to delete?</span>
                                          <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain">
                                             <button class="Polaris-Button Polaris-Button--destructive Polaris-Button--plain delete_frequency_card" type="button" attr-card-serial-no="` + key + `" attr-id="` + value.selling_plan_id + `">Yes</button>
                                          </div>
                                          <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain">
                                             <button class="Polaris-Button Polaris-Button--plain delete_frequency_card_cancel" type="button" attr-card-serial-no="` + key + `" attr-id="` + value.selling_plan_id + `">No</button>
                                          </div>
                                       </div>
                                       <div class="Polaris-ButtonGroup frequency-card-actions">
                                          <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain"><button class="remove-btn Polaris-Button Polaris-Button--destructive Polaris-Button--plain delete_frequency_card_ask_confirmation" attr-card-serial-no="` + key + `" attr-id="` + value.selling_plan_id + `" type="button" `+delete_button_disable+`>Delete</button></div>
                                          <div class="Polaris-ButtonGroup__Item Polaris-ButtonGroup__Item--plain"><button class="update-btn Polaris-Button Polaris-Button--plain edit_frequency_card" type="button" attr-card-serial-no="` + key + `" attr-id="` + value.selling_plan_id + `">Edit</button></div>
                                       </div>
                                    </div>
                        </div>
                     </div>`;
                    sd_frequency_card_serialno++;
                });
                sd_subscription_edit_case_already_existing_plans_array_validation_check = JSON.parse(JSON.stringify(sd_subscription_edit_case_already_existing_plans_array));
                jQuery('.sd-frequency-plan-card-wrapper').append(htmlCard);
                initial_group_name = document.querySelectorAll('#edit_subscription_wrapper .sd_subscription_heading')[0].textContent;
                jQuery(this).removeClass('btn-disable-loader');
                jQuery('#sd_subscriptionLoader').addClass('display-hide-label');
            } else {
                if (result['message'] == '404') {
                    shopify.toast.show('Plan does not exist', {isError: true});
                    location.reload();
                }
            }
        } catch (e) {
        }
    });

    jQuery("body").on("click", ".CreateSubscriptipnGroup", function() {
        pre_selected_products = {};
        jQuery('.create-subscription-buttons').removeClass("display-hide-label"); // new code added on 18 july
        jQuery('.save-subscription-plan').removeClass('btn-disable-loader');
        reset_form('subscription-create-step1');
        jQuery('#show_selected_products').html('');
        jQuery('.sd-frequency-plan-card').remove();
        sd_frequency_card_serialno = 0;
        active_step('1');
        create_subscription_mode();
        form_type = 'create';
        jQuery('#db_edit_subscriptionplan_id').val('');
        db_edit_subscriptionplan_id = jQuery('#db_edit_subscriptionplan_id').val();
    });

    jQuery("body").on('click', '#shipCountry', function(e) {
        getStates();
    });

    jQuery("body").on('click', '.open_mini_action', function(e) {
        var subscription_group_id = jQuery(this).attr('subscription-group-id');
        if (jQuery('#subscription_mini_' + subscription_group_id).hasClass("display-hide-label")) {
            //open case
            list_close_all_mini();
            reset_plan_name_change();
            jQuery('#subscription_mini_' + subscription_group_id).removeClass('display-hide-label');
        } else {
            //close case
            jQuery('#subscription_mini_' + subscription_group_id).addClass('display-hide-label');
        }

    });

    jQuery('body').on('click', '.cancel_plan_name', function() {
        reset_plan_name_change();
    });

    jQuery('body').on('click', '.save_plan_name', async function() {
        var subscription_group_id = jQuery(this).attr('subscription-group-id');
        let new_planName = jQuery.trim(jQuery('.subscription_list_' + subscription_group_id + ' .subscription_plan_name').val());
        if (new_planName.length > 0) {
            displayMessage('Updating', 'waiting');
            let ajaxParameters = {
                method: "POST",
                dataValues: {
                    action: "mini_subscription_planName_change",
                    subscription_group_id: subscription_group_id,
                    plan_name: new_planName,
                }
            };
            try {
                let result = await AjaxCall(ajaxParameters);
                if (result['status'] == true) {
                    displayMessage(result['message'], 'success');
                    jQuery('.subscription_list_' + subscription_group_id + ' .sd_subscription_heading .list_planname').html(new_planName);
                    jQuery('#edit_subscription_wrapper .sd_subscription_heading').html(new_planName);
                    jQuery('.subscription_list_' + subscription_group_id).attr("data-search-planname", new_planName);
                    jQuery('.subscription_list_' + subscription_group_id + ' .change_plan_name').attr("plan-name-value", new_planName);
                    reset_plan_name_change();
                } else if (result['status'] == 404) {
                    jQuery('.subscription_list_' + subscription_group_id).remove();
                    var subscription_list_card = document.getElementsByClassName('subscription-list-card');
                    if (subscription_list_card.length == 0) {
                        location.reload();
                    }
                } else {
                    displayMessage(result['message'], 'error');
                }
            } catch (e) {
            }
        } else {
            displayMessage('Plan Name cannot be empty', 'error');
        }

    });

    jQuery('body').on('click', '.change_plan_name', function() {
        reset_plan_name_change();
        let subscription_group_id = jQuery(this).attr('subscription-group-id');
        let plan_name_value = jQuery(this).attr('plan-name-value');
        //for others
        jQuery('.planname_input_wrapper').addClass('display-hide-label'); // hide others input fields
        jQuery('.change_plan_name').removeClass('display-hide-label'); //show change plan name text
        jQuery('.sd_subscription_heading').removeClass('display-hide-label'); // show others heading tag
        //for this particular
        jQuery('.subscription_list_' + subscription_group_id + ' .subscription_heading').addClass('display-hide-label'); //hide heading tag
        jQuery('.subscription_list_' + subscription_group_id + ' .planname_input_wrapper').removeClass('display-hide-label'); //show input fields
        jQuery('.subscription_plan_name').val(plan_name_value);
        jQuery(this).addClass('display-hide-label'); //hide change plan name text
    });

    // setting page click event start
    jQuery("body").on("click", ".sd_selectSetting", async function() {
        var sd_productSpinner = jQuery("#sd_productSpinner").html();
        jQuery('#sd_addSettingsHtml').html('<div id="sd_productSpinner" class="sd_productSpinner">' + sd_productSpinner + '</div>');
        jQuery('.sd_contractSetting').removeClass('display-hide-label');
        selected_setting = $(this).attr('data-popup');
        let ajaxParameters = {
            method: "POST",
            dataValues: {
                tablename: selected_setting,
                action: "get_setting_data"
            }
        };
        try {
            result = await AjaxCall(ajaxParameters);
        } catch (e) {}
        if (selected_setting == 'productPageSetting') {
            getProductPageSetting(result);
            jscolor.install();
        } else if (selected_setting == 'customer_settings') {
            getCustomerSettingData(result);
        } else if (selected_setting == 'contract_setting') {
            getContractSettingData(result);
        } else if (selected_setting == 'email_configuration') {
            getEmailConfigurationSetting(result);
        }
    });

    jQuery("body").on("click", ".close_setting_picker", async function() {
        close_setting_picker();
        document.getElementById('sd_addSettingsHtml').innerHTML = '';
    });


    /**===========================================================  Click Events End ============================================================**/

    /**============================================ On Change Events Start ===============================================**/

    // jQuery("body").on('change', '.app_mode', function() {
    //     if (this.checked) {
    //         changeAppStatus(1, "install");
    //     } else {
    //         document.getElementById("sd_global_modal_container").innerHTML = confirmBoxModal("app_disable", "", "Alert", app_disable_confirmbox_message, "Yes", "Polaris-Button--primary", "No");
    //     }
    // });

    // jQuery("body").on('change', '.sd_select', function() {
    // 	 let discount_selected = jQuery('option:selected', this).attr('discount');
    // 	 let select_type = jQuery('option:selected', this).attr('attr-type');
    // 	 if(select_type == "ppd"){
    // 		 jQuery('#ppd_discount').html(discount_selected);
    // 	 }else{
    // 		 jQuery('#prepaid_discount').html(discount_selected);
    // 	 }
    // });

    jQuery("body").on("change", ".Polaris-Select__Input", function() {
        var selected_option_value = jQuery(this).val();
        jQuery(this).parent().find(".Polaris-Select__SelectedOption").html(selected_option_value);
    });

    jQuery("body").on("change", "#frequency_plan_type", function() {
        var selectedPlan = jQuery('#frequency_plan_type').val();
        jQuery('.frequency-plan-error').addClass('display-hide-label');
        if (selectedPlan == "Prepaid") {
            jQuery('.sd_prepaid_fields').removeClass('display-hide-label');
        } else if (selectedPlan == "Pay Per Delivery") {
            jQuery('.sd_prepaid_fields').addClass('display-hide-label');
        }
    });

    jQuery("body").on("change", "#subscription_discount_status", function() {
        if (jQuery(this).prop("checked") == true) {
            jQuery('.sd_subscription_discount_offer_wrapper,.sd_change_discount_after').removeClass('display-hide-label');
        } else {
            jQuery('#subscription_discount_after_status').prop('checked', false);
            jQuery('#subscription_discount_value,#change_discount_after_cycle,#discount_value_after').val('');
            jQuery('.sd_subscription_discount_offer_wrapper,.sd_change_discount_after,.sd_subscription_discount_offer_after_wrapper').addClass('display-hide-label');
        }
    });

    //anchor change
    jQuery("body").on("change", "#sd_set_anchor_date", function() {
        if (jQuery(this).prop("checked") == true) {
            jQuery('.sd_anchor_option').removeClass('display-hide-label');
        } else {
            change_select_html('#sd_anchor_option', 'On Purchase Day');
            jQuery('.sd_anchor_option,.sd_anchor_month_day,.sd_anchor_week_day,.cut_off_days').addClass('display-hide-label');
        }
    });

    jQuery("body").on("change", "#sd_anchor_option", function() {
        if ((jQuery('#sd_anchor_option option:selected').val()) == 'On Purchase Day') {
            jQuery('.sd_anchor_week_day,.sd_anchor_month_day,.cut_off_days').addClass('display-hide-label');
        } else {
            let delivery_billing_type = jQuery('#sd_per_delivery_order_frequency_type option:selected').val();
            if (delivery_billing_type == 'WEEK') {
                jQuery('.sd_anchor_week_day,.cut_off_days').removeClass('display-hide-label');
            } else {
                jQuery('.sd_anchor_month_day,.cut_off_days').removeClass('display-hide-label');
            }
        }
    });

    jQuery("body").on("change", "#subscription_discount_after_status", function() {
        if ($(this).prop("checked") == true) {
            jQuery('.sd_subscription_discount_offer_after_wrapper').removeClass('display-hide-label');
            scrollToBottom('.sd_selling_form');
        } else {
            jQuery('#change_discount_after_cycle,#discount_value_after').val('');
            jQuery('.sd_subscription_discount_offer_after_wrapper').addClass('display-hide-label');
        }
    });


    jQuery("body").on('change', '#sd_per_delivery_order_frequency_type', function(e) { //sd_prepaid_fullfillment_type
        let selected_value = $(this).val();
        jQuery('#sd_prepaid_billing_type option:selected').removeAttr('selected');
        jQuery("#sd_prepaid_billing_type option[value='" + selected_value + "']").attr('selected', 'selected');
        jQuery('#sd_prepaid_billing_type_Selected_value').html(selected_value);
        jQuery('.sd_set_anchor_date,.sd_anchor_option,.sd_anchor_month_day,.sd_anchor_week_day,.cut_off_days').addClass('display-hide-label');
        jQuery('#sd_set_anchor_date').prop('checked', false);
        jQuery('.sd_anchor_option .Polaris-Select__SelectedOption').first().text('On Purchase Day');
        change_select_html('#sd_anchor_option', 'On Purchase Day');
        if (selected_value != 'DAY' && selected_value != 'YEAR') {
            jQuery('.sd_set_anchor_date').removeClass('display-hide-label');
        }
    });

    /**============================================ On KeyUP Events Start ===============================================**/
    jQuery("body").on("keyup", "#search-subscription-text", function() {
        subscription_search();
   });

    jQuery("body").on("keyup, change, input", ".sd_validate_expration", function(event) {
        $(this).val($(this).val().replace(restrict_format, ''));
    });

    $('body').on("keyup, change, input", ".restrict_input_single_quote", function(e) {
        var inputValue = $(this).val();
        if (inputValue.includes("'")) {
            inputValue = inputValue.replace(/'/g, '');
            $(this).val(inputValue);
        }
    });

    /**============================================ On KeyUP Events End ===============================================**/

    /**============================================ On Submit Events Start ===============================================**/
    var emailConfigurationData;
    // save email configuration setting in db
    jQuery("body").on('submit', '.emailform_setting', async function(e) {
        e.preventDefault();
        $('#sd_button_emailsetting').addClass('btn-disable-loader');
        var isTrue = checkEmailConfiguration("emailform_setting");
        if (isTrue) {
            if (jQuery('input[id=email_enable]').is(':checked')) {
                email_enable_checked = 'checked';
            } else {
                email_enable_checked = 'unchecked';
            }
            emailConfigurationData['email_enable'] = email_enable_checked;
            emailConfigurationData['store_id'] = store_id;
            let whereconditionvalues = {
                "store_id": store_id
            }
            let ajaxParameters = {
                method: "POST",
                dataValues: {
                    table: 'email_configuration',
                    data_values: emailConfigurationData,
                    wherecondition: whereconditionvalues,
                    wheremode: 'and',
                    action: "insertupdateajax"
                }
            };
            try {
                let result = await AjaxCall(ajaxParameters);
                if (result['status'] == true) {
                    displayMessage(result['message'], 'success');
                    $('#sd_button_emailsetting').removeClass('btn-disable-loader');
                    close_setting_picker();
                }
            } catch (e) {
                displayMessage(e, 'error');
                $('#sd_button_emailsetting').removeClass('btn-disable-loader');
            }
        } else {
            $('#sd_button_emailsetting').removeClass('btn-disable-loader');
        }
    });

    function isEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }

    function checkEmailConfiguration(formId) {
        emailConfigurationData = form_serializeObject(formId);
        checkStatus = 'true'
        $.each(emailConfigurationData, function(key, value) {
            if (value == '') {
                displayMessage('Enter ' + key, 'error');
                checkStatus = 'false';
                return false;
            }
        });
        if (checkStatus == 'false') {
            return false;
        } else {
            return true;
        }
    }

    jQuery("body").on('click', '#serach_subscription_group', async function(e) {
        let selected_search_type = jQuery('#subscription-search-option').find(":selected").text();
        var search_text = jQuery('#search-subscription-text').val();
        if(search_text == ''){
            displayMessage('Enter text to find the plan', 'error');
        }else{
        jQuery('#sd_subscriptionLoader').removeClass('display-hide-label');
        let ajaxParameters = {
            method: "POST",
            dataValues: {
                search_text :search_text,
                selected_search_type : selected_search_type,
                action: "search_subscription_group"
            }
        };
        try {
            let result = await AjaxCall(ajaxParameters);
            if(result.status == true){
              jQuery('.subscription-list-start').html(result['next_subscription_groups']);
              jQuery(this).removeClass('display-hide-label');
              jQuery('#sd_subscriptionLoader').addClass('display-hide-label');
            }else{
                jQuery('#sd_subscriptionLoader').addClass('display-hide-label');
                displayMessage(result['message'], 'error');
                jQuery(this).removeClass('display-hide-label');
            }
        } catch (e) {
        }
    }
    });
    //save emailnotification setting
    // frmEmailSettingSave
    jQuery("body").on('click', '#sd_emailNotifySetting', async function(e) {
        e.preventDefault();
        var allCheckboxValues = {};
        $('.frmEmailSettingSave input[type="checkbox"]').each(function() {
            if (this.checked) {
                checkboxValue = '1';
            } else {
                checkboxValue = '0';
            }
            if (this.name != '') {
                allCheckboxValues[this.name] = checkboxValue;
            }
        });
        let whereconditionvalues = {
            "store_id": store_id
        }
        allCheckboxValues['store_id'] = store_id;
        let ajaxParameters = {
            method: "POST",
            dataValues: {
                insertdata: allCheckboxValues,
                wherecondition: whereconditionvalues,
                wheremode: 'and',
                table: 'email_notification_setting',
                action: "updateajax"
            }
        };
        try {
            let result = await AjaxCall(ajaxParameters);
            displayMessage(result['message'], 'success');
            close_setting_picker();
        } catch (e) {
            displayMessage(e, true);
        }
    });

    jQuery("body").on("click", "#sd_member_plan_activate", async function() {
        var plan_price = jQuery(this).attr('attr-plan-price');
        var plan_id = jQuery(this).attr('attr-plan-id');
        var current_plan_id = jQuery(this).attr('attr-currentplan-id');
        var plan_name = jQuery(this).attr('attr-plan-name');
        let ajaxParameters = {
            method: "POST",
            dataValues: {
                action: "create_app_subscription",
                plan_price: plan_price,
                plan_id:plan_id,
                plan_name:plan_name,
                current_plan_id:current_plan_id,
            }
        };
        try {
            let result = await AjaxCall(ajaxParameters);
            if (result['status'] == true) {
                window.top.location.href = result['confirmationUrl'];
            }else{

            }
        } catch (e) {
        }
    });

    //   save product page setting in database and theme
    jQuery("body").on('submit', '.formProductSettingSave', async function(e) {
        e.preventDefault();
        let ProductSettingData = form_serializeObject("frmProductSave");
        let whereconditionvalues = {
            "store_id": store_id
        }
        wheremodevalue = "and";
        let ajaxParameters = {
            method: "POST",
            dataValues: {
                insertdata: ProductSettingData,
                action: "updateajax",
                table: 'productPageSetting',
                wheremode: wheremodevalue,
                wherecondition: whereconditionvalues
            }
        };
        try {
            let result = await AjaxCall(ajaxParameters);
            displayMessage(result['message'], 'success');
            close_setting_picker();
        } catch (e) {
            displayMessage(e, true);
        }
    });

    // save customer account setting in db
    jQuery("body").on('submit', '.frmCustomerSettingSave', async function(e) {
        e.preventDefault();
        var allCheckboxValues = {};
        $('input[type="checkbox"]').each(function() {
            if (this.checked) {
                checkboxValue = '1';
            } else {
                checkboxValue = '0';
            }
            if (this.name != '') {
                allCheckboxValues[this.name] = checkboxValue;
            }
        });
        let whereconditionvalues = {
            "store_id": store_id
        }
        allCheckboxValues['store_id'] = store_id;
        let ajaxParameters = {
            method: "POST",
            dataValues: {
                insertdata: allCheckboxValues,
                wherecondition: whereconditionvalues,
                wheremode: 'and',
                table: 'customer_settings',
                action: "updateajax"
            }
        };
        try {
            let result = await AjaxCall(ajaxParameters);
            displayMessage(result['message'], 'success');
            close_setting_picker();
        } catch (e) {
            displayMessage(e, true);
        }
    });
    /**============================================ On Submit Events End ===============================================**/
}); // document.ready end

function checkSpecificLinks(obj, keys) {
    // Define the list of valid domains
    const validDomains = [
        "facebook.com", "instagram.com", "youtube.com", "pinterest.com",
        "reddit.com", "whatsapp.com", "t.me", "skype.com", "xing.com",
        "getpocket.com", "linkedin.com", store
    ];

    // Convert the valid domains into a regular expression pattern
    const domainPattern = new RegExp(validDomains.join('|'), 'i');

    // Function to check if a string contains a link that doesn't have a valid domain
    function containsInvalidLink(str) {
        // Regex to find all href attributes
        const linkPattern = /href=["'](https?:\/\/[^"']+)["']/gi;
        let match;

        // Check all href links in the string
        while ((match = linkPattern.exec(str)) !== null) {
            const url = match[1];
            const domain = new URL(url).hostname;

            // If the domain is not in the valid domains list, return true
            if (!domainPattern.test(domain)) {
                return true;
            }
        }
        return false;
    }

    // Check the specific keys in the object
    for (const key of keys) {
        if (typeof obj[key] === 'string' && containsInvalidLink(obj[key])) {
            return false
        }
    }

    return true;
}