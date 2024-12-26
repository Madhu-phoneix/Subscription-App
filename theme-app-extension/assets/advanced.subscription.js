if(!((window.location.pathname).includes('/your-subscriptions'))){
	var SHOPIFY_DOMAIN_URL = "https://your-domain.com";
	var random_number = (Math.random() + 1).toString(36).substring(7);
	var accountPageHandle,productpagesetting,app_status,ajaxResponseData,currentProductVariantsObj,currentProductID,CurrentVariantObjectIndex,CurrentVariant,selling_plans,variantID = "",timezone,form_type='create',widget_template,One_Time_Purchase_html,Pay_Per_Delivery_html,Prepaid_html,selling_planId_index,plan_type,show_cut_original_price = '',hidden_selling_plan_name = 'No',total_payable_text,total_discount_value='',discountRibbonStyle,per_quantityPrice='',billingCycle,deliveryCycle,total_saved_discount_value, loaded_variant = '',plan_description,planDescription,subscription_detail_content,theme_block_support,get_forms,active_purchase_option,secondary_purchase_option,all_plans_single_discount='',all_plans_options_list='',checkout_charge_amount,selected_plan_type,day_frequency_text,week_frequency_text,month_frequency_text,year_frequency_text,selected_background_color,total_payable_amount_text,discount_background_color,discount_text_color,widget_background_color,save_discount_label,total_saved_discount_label,widget_template,subscription_detail,orders_text,then_text,on_first_text,pd_selling_plans_label,subscription_options_text,primary_price_color,secondary_price_color,options_bg_color1,options_bg_color2,subscription_widget_block='';
	var theme_id = Shopify.theme.id;
	var sd_shopCurrency = Shopify.currency.active;
	var store = Shopify.shop;
	AjaxCallFrom = 'frontendAjaxCall';
	ajaxurl = SHOPIFY_DOMAIN_URL+"/application/controller/frontend_ajxhandler.php?store=" + store;
	shopifyLocale = Shopify.locale.split('-')[0];
	localeCountry = `${shopifyLocale}-${Shopify.country}`;
	params = new URLSearchParams(window.location.search);

	var priceSelectors = ['.product-price','.price.price--large','.product__price','.product__price--holder','.yv-product-price','.product-single__prices','.price-area'];
	get_forms = document.body.querySelectorAll('form[action*="/cart/add"]');
	/* ============= Store Install Information ================= */
	app_status = '1';
	function select_bydefault_radio(){ //new_code_updated
		if(document.getElementById('sd_subscription_frequency_plan_radio') && (store_widget_data.default_selected_option == '1')){
			document.getElementById('sd_subscription_frequency_plan_radio').click();
		}else if(document.getElementById('sd_pay_per_delivery_purchase_radio') && (store_widget_data.default_selected_option == '2')){
			document.getElementById('sd_pay_per_delivery_purchase_radio').click();
		}else if(document.getElementById("sd_prepaid_delivery_purchase_radio") && (store_widget_data.default_selected_option == '3')){
			document.getElementById("sd_prepaid_delivery_purchase_radio").click();
		}else if(document.getElementById('sd_subscription_frequency_plan_radio')){
			document.getElementById('sd_subscription_frequency_plan_radio').click();
		}else if(document.getElementById('sd_pay_per_delivery_purchase_radio')){
			document.getElementById('sd_pay_per_delivery_purchase_radio').click();
		}else if(document.getElementById("sd_prepaid_delivery_purchase_radio")){
			document.getElementById("sd_prepaid_delivery_purchase_radio").click();
		}else if(document.getElementById('sd_all_delivery_purchase_radio')){
			document.getElementById("sd_all_delivery_purchase_radio").click();
		}else if((widget_template == '3') && (document.getElementById('sd_all_plans_option'))){
			var selected_value = document.getElementsByClassName('sd_purchase_options')[0];
			selected_plan_type = selected_value;
			var all_active_class = document.querySelectorAll('.activemethod');
			all_active_class.forEach(function (element) {
				element.classList.remove('activemethod');
			});
			selected_value.parentElement.classList.add('activemethod');
			selected_selling_plan_id = selected_value.value;
			if(hidden_selling_plan_name == 'Yes'){
				get_all_selling_plan_name = document.body.querySelectorAll('form[action*="/cart/add"] input[name="selling_plan"]');
				get_all_selling_plan_name.forEach(function (element) {
					element.value = selected_selling_plan_id;
				});
			}

			selected_plan_type = selected_value;
			var all_active_class = document.querySelectorAll('.activemethod');
			all_active_class.forEach(function (element) {
				element.classList.remove('activemethod');
			});
			selected_value.parentElement.classList.add('activemethod');
			selected_selling_plan_id = selected_value.value;
			if(hidden_selling_plan_name == 'Yes'){
				get_all_selling_plan_name = document.body.querySelectorAll('form[action*="/cart/add"] input[name="selling_plan"]');
				get_all_selling_plan_name.forEach(function (element) {
					element.value = selected_selling_plan_id;
				});
			}

			deliveryCycle = selected_plan_type.getAttribute('attr-deliveryCycle');
			billingCycle = selected_plan_type.getAttribute('attr-billingCycle');
			total_saved_discount_value = selected_plan_type.getAttribute('discount_value');
			let total_payable_amt = selected_plan_type.getAttribute('total-payable-amount');
			checkoutChargeAmount =  selected_plan_type.getAttribute('checkout-charge-amount');
			product_input_quantity = 1;
			discountRibbonStyle = total_saved_discount_value.length > 0 ? 'display: inline-block;' : 'display: none;';
			let total_payable_amount = (product_input_quantity*checkoutChargeAmount);
			document.getElementById('sd_totalPayableAmount').innerHTML = total_payable_amount_text+' '+currencyES6Format(total_payable_amount)+' '+'<span class="sd_discount_ribbon discount2" style="'+discountRibbonStyle+'">'+total_saved_discount_value+'</span>';
			let discount_available = '';
			let discount_selected = selected_plan_type.getAttribute('discount');
			if(discount_selected.length > 0){
				discount_available = '<span class="sd_discount_ribbon discount3" style="background-color:'+discount_background_color+'; color:'+discount_text_color+'; display: inline-block;">'+discount_selected+'</span>';
			}else{
				discount_available = '';
			}
			let quantity_price = selected_plan_type.getAttribute('per-quantity-price');
			let cut_original_price = selected_plan_type.getAttribute('per-original-price');
			if(quantity_price != cut_original_price){
				show_cut_original_price = cut_original_price;
			}else{
				show_cut_original_price = '';
			}
			let discount_html = '<div class="selected_subscription_wrapper"><span class="selected_price">'+quantity_price+'</span><span class="cut_original_price">'+show_cut_original_price+'</span>'+discount_available+''+per_quantity_text+'</div>';
			let all_price_selectors = priceSelectors.join();
			document.querySelector(all_price_selectors).innerHTML = discount_html;
		}
		if(widget_template == '1'){
			active_purchase_option = document.querySelector('.sd_subscription_wrapper .activemethod .sd_subscription_label_wrapper');
			console.log('widget template = ', widget_template);
		}else if(widget_template == '2'){
			active_purchase_option = document.querySelector('.sd_subscription_wrapper .activemethod .sd_label_discount_wrapper');
		}else if(widget_template == '3'){
			active_purchase_option = document.querySelector('.sd_subscription_wrapper .activemethod ');
		}


		if(active_purchase_option){
		//    active_purchase_option.style.backgroundColor = add_block_settings.selected_background_color;
		   active_purchase_option.style.color = text_color;
		   active_purchase_option.style.background = "linear-gradient(to right," + options_bg_color1 + "," + options_bg_color2 + ")";
		}

		var abcDivs = document.querySelectorAll('.sd_cards_options');
		abcDivs.forEach(function(abcDiv) {
			abcDiv.addEventListener('click', function() {
				var radioBtn = abcDiv.querySelector('.sd_subscription_wrapper_option input[type="radio"]');
				if (radioBtn) {
				var radioBtnId = radioBtn.id;
				document.getElementById(radioBtnId).click();
				console.log('Dynamic Radio button ID:', radioBtnId);
				}
			});
		});
	}
	function MerchantAjaxCall(action,ajaxdata,asyncmode){
		const xmlhttp = new XMLHttpRequest();
			xmlhttp.onload = function() {
			ajaxResponseData = this.responseText;
		}
		xmlhttp.open(action,ajaxurl,asyncmode);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send(ajaxdata);
	}

	document.addEventListener("DOMContentLoaded", function(event) {
		if((window.location.pathname).includes('/products/') && app_status == 1 ){
			getVariant();
			checkWidgetCreate();
			document.addEventListener('change',function(e){
				if(Shopify.designMode) {
					changeVariantID = loaded_variant;
				}else{
					changeVariantID = window.ShopifyAnalytics.meta["selectedVariantId"];
				}
				if(variantID != changeVariantID){
					variantID = changeVariantID;
					checkWidgetCreate();
					select_bydefault_radio();
				}
				if(document.querySelector('input[name="quantity"]')){
					product_input_quantity = document.querySelector('input[name="quantity"]').value;
				}else{
					product_input_quantity = '';
				}

					var selected_selling_plan = document.getElementsByClassName('sd_purchase_options');
					for (var selected_value of selected_selling_plan)
					{
						if (selected_value.checked) {
							selected_selling_plan_type = selected_value.id;
							if(selected_selling_plan_type == 'sd_subscription_frequency_plan_radio'){
								document.getElementById('sd_totalPayableAmount').innerHTML = '';
								if(widget_template == '3'){
									var all_active_class = document.querySelectorAll('.activemethod');
									all_active_class.forEach(function (element) {
										element.classList.remove('activemethod');
									});
									selected_value.parentElement.classList.add('activemethod');
								}
							}else{
								if(selected_selling_plan_type == 'sd_pay_per_delivery_purchase_radio' || selected_selling_plan_type == 'sd_prepaid_delivery_purchase_radio' || widget_template == '2'){
									if(selected_selling_plan_type == 'sd_pay_per_delivery_purchase_radio'){
										selected_plan_type_id = 'sd_ppd_list';
									}else if(selected_selling_plan_type == 'sd_prepaid_delivery_purchase_radio'){
										selected_plan_type_id = 'sd_prepaid_list';
									}else if(widget_template == '2'){
										selected_plan_type_id = 'sd_all_plans_list';
									}
									if(document.querySelector("#sd_widget_wrapper .activemethod").querySelector("select")){
										selected_plan_type = document.getElementById(selected_plan_type_id).options[document.getElementById(selected_plan_type_id).selectedIndex];
									}else{
										selected_plan_type = document.getElementById(selected_plan_type_id);
									}
								}else if(widget_template == '3'){
									selected_plan_type = selected_value;
									var all_active_class = document.querySelectorAll('.activemethod');
									all_active_class.forEach(function (element) {
										element.classList.remove('activemethod');
									});
									selected_value.parentElement.classList.add('activemethod');
									selected_selling_plan_id = selected_value.value;
									if(hidden_selling_plan_name == 'Yes'){
										get_all_selling_plan_name = document.body.querySelectorAll('form[action*="/cart/add"] input[name="selling_plan"]');
										get_all_selling_plan_name.forEach(function (element) {
											element.value = selected_selling_plan_id;
										});
									}
								}
									deliveryCycle = selected_plan_type.getAttribute('attr-deliveryCycle');
									billingCycle = selected_plan_type.getAttribute('attr-billingCycle');
									total_saved_discount_value = selected_plan_type.getAttribute('discount_value');
									let total_payable_amt = selected_plan_type.getAttribute('total-payable-amount');
									checkoutChargeAmount =  selected_plan_type.getAttribute('checkout-charge-amount');
									if(product_input_quantity == ''){
										product_input_quantity = 1;
									}
									discountRibbonStyle = total_saved_discount_value.length > 0 ? 'display: inline-block;' : 'display: none;';
									let total_payable_amount = (product_input_quantity*checkoutChargeAmount);
									document.getElementById('sd_totalPayableAmount').innerHTML = total_payable_amount_text+' '+currencyES6Format(total_payable_amount)+' '+'<span class="sd_discount_ribbon discount4" style="' + discount_background_color + '; color:'+discount_text_color+';'+discountRibbonStyle+'">'+total_saved_discount_value+'</span>';
									if(widget_template == '3'){
										let discount_available = '';
										let discount_selected = selected_plan_type.getAttribute('discount');
										let planDescription = selected_plan_type.getAttribute('attr-plandescription');
										if(discount_selected.length > 0){
											discount_available = '<span class="sd_discount_ribbon discount5" style="background-color:'+discount_background_color+'; color:'+discount_text_color+'; display: inline-block;">'+discount_selected+'</span>';
										}else{
											discount_available = '';
										}
										let quantity_price = selected_plan_type.getAttribute('per-quantity-price');
										let cut_original_price = selected_plan_type.getAttribute('per-original-price');
										if(quantity_price != cut_original_price){
											show_cut_original_price = cut_original_price;
										}else{
											show_cut_original_price = '';
										}
										if(planDescription != '' && planDescription != null && planDescription != 'null'){
											document.getElementById('sd_plan_description').innerHTML = '<h1><span>'+description_title+'</h1><span>'+planDescription+'</span>';
										}
										let discount_html = '<div class="selected_subscription_wrapper"><span class="selected_price">'+quantity_price+'</span><span class="cut_original_price">'+show_cut_original_price+'</span>'+discount_available+''+per_quantity_text+'</div>';
												let all_price_selectors = priceSelectors.join();
										document.querySelector(all_price_selectors).innerHTML = discount_html;
									}
							}
						}
					}
					if(widget_template == '1'){
						active_purchase_option = document.querySelector('.sd_subscription_wrapper .activemethod .sd_subscription_label_wrapper');
						secondary_purchase_option = document.querySelectorAll('.sd_subscription_wrapper_option .sd_subscription_label_wrapper');
					}else if(widget_template == '2'){
						active_purchase_option = document.querySelector('.sd_subscription_wrapper .activemethod .sd_label_discount_wrapper');
						secondary_purchase_option = document.querySelectorAll('.sd_subscription_wrapper_option .sd_label_discount_wrapper');
					}else if(widget_template == '3'){
						active_purchase_option = document.querySelector('.sd_subscription_wrapper .sd_subscription_wrapper_option.activemethod ');
						secondary_purchase_option = document.querySelectorAll('.sd_subscription_wrapper_option ');
					}

					secondary_purchase_option.forEach(function (element) {
						element.style.color = '';
						// if(widget_template == '3'){
						// 	element.style.background = widget_background_color;
						// }else{
							element.style.background = "";
						// }
					});

					if(active_purchase_option){
						active_purchase_option.style.color = text_color;
					   active_purchase_option.style.background = "linear-gradient(to right," + options_bg_color1 + "," + options_bg_color2 + ")";
					}
				// }, 100);
			});
		}
	});
	//document ready end
		// customer account page start here

		if((window.location.pathname).includes('/account')){
		let customer_id = window.ShopifyAnalytics.meta['page']['customerId'];
			if (customer_id && (typeof customer_id !== 'undefined')){
				let btn = document.createElement("button");
				MerchantAjaxCall("POST","action=checkCustomerSubscription&customer_id="+customer_id,false);
				if(ajaxResponseData){
					let obj = JSON.parse(ajaxResponseData);
					if(obj.customer_exist == 'yes'){
						accountPageHandle = obj.accountPageHandle;
						if(store == 'iwara-shop.myshopify.com'){
							let getTable = document.querySelector(".account-history");
							if(getTable){
								getTable.insertAdjacentHTML('beforebegin','<button type="button" id="sd_manageSubscription">Manage Subscription</button>');
							}
						}else{
							let getTable = document.getElementsByTagName("table")[0];
							if(getTable){
								getTable.insertAdjacentHTML('beforebegin','<button type="button" id="sd_manageSubscription">Manage Subscription</button>');
							}
						}
					}
				}
			}
			if(document.getElementById("sd_manageSubscription")){
				document.getElementById("sd_manageSubscription").onclick = function(){//do something
					window.location = "http://"+Shopify.shop+"/apps/your-subscriptions";
				}
			}
		}


	function currencyES6Format(money){
		return new Intl.NumberFormat(localeCountry, {
			style: 'currency',
			currency: sd_shopCurrency
		}).format(money);
	}

	if((window.location.pathname).includes('/products/') && app_status == 1 ){
		function add_class(elements_array){
			let class_elements = elements_array.class_elements;
			class_elements.forEach(function(value, index, array) {
				sd_className = document.getElementsByClassName(value.name);
				for (let i = 0; i < sd_className.length; i++) {
					sd_className[i].classList.add(value.classname);
				}
			});
		}

		document.addEventListener('change',async function(e){
			let className = e.target.className;
			let idName = e.target.id;
			let classNameArray = className.split(" ");
			if(classNameArray.includes('sd_select')){
				let discount_available = '';
				let discount_selected = e.target.options[e.target.selectedIndex].getAttribute('discount');
				let select_type = e.target.options[e.target.selectedIndex].getAttribute('attr-type');
				let quantity_price = e.target.options[e.target.selectedIndex].getAttribute('per-quantity-price');
				let cut_original_price = e.target.options[e.target.selectedIndex].getAttribute('per-original-price');
				let selected_selling_plan_id = e.target.options[e.target.selectedIndex].value;
				let total_discount_value = e.target.options[e.target.selectedIndex].getAttribute('discount_value');
				planDescription = e.target.options[e.target.selectedIndex].getAttribute('attr-plandescription');
				var single_option_discount = e.target.options[e.target.selectedIndex].getAttribute('first-discount');

				 if(hidden_selling_plan_name == 'Yes'){
					get_all_selling_plan_name = document.body.querySelectorAll('form[action*="/cart/add"] input[name="selling_plan"]');
					get_all_selling_plan_name.forEach(function (element) {
						element.value = selected_selling_plan_id;
					});
				}
				if(quantity_price != cut_original_price){
					show_cut_original_price = cut_original_price;
				}else{
					show_cut_original_price = '';
				}
				if(select_type == "ppd"){
					let sd_price_text = document.getElementById('sd_ppd_price_text');
					if(discount_selected.length > 0){
						discount_available = '<span class="sd_discount_ribbon discount6" style="background-color:'+discount_background_color+'; color:'+discount_text_color+'; display: inline-block;">'+discount_selected+'</span>';
						discountRibbonStyle = 'display: inline-block;';
					}else{
						discount_available = '';
						discountRibbonStyle = 'display: none;';
					}
					document.getElementById('ppd_discount_price').innerHTML = quantity_price;
					document.getElementById('ppd_cut_original_price').innerHTML = show_cut_original_price;

				}else if(select_type == 'prepaid'){
					let sd_price_text = document.getElementById('sd_prepaid_price_text');
					if(discount_selected.length > 0){
						discount_available = '<span class="sd_discount_ribbon discount7" style="background-color:'+discount_background_color+'; color:'+discount_text_color+'; display: inline-block;">'+discount_selected+'</span>';
						discountRibbonStyle = 'display: inline-block;';
					 }else{
						discount_available = '';
						discountRibbonStyle = 'display: none;';
					}
					document.getElementById('prepaid_discount_price').innerHTML = quantity_price;
					document.getElementById('prepaid_cut_original_price').innerHTML = show_cut_original_price;
				}else{
					if(discount_selected.length > 0){
						discount_available = '<span class="sd_discount_ribbon discount8" style="background-color:'+discount_background_color+'; color:'+discount_text_color+'; display: inline-block;">'+discount_selected+'</span>';
						discountRibbonStyle = 'display: inline-block;';
					 }else{
						discount_available = '';
						discountRibbonStyle = 'display: none;';
					}
					document.getElementById('all_plans_discount_price').innerHTML = quantity_price;
					document.getElementById('label_all_plans').innerHTML = subscription_options_text+` `+single_option_discount;
				}
				if(planDescription != '' &&  planDescription != null && planDescription != 'null'){
					document.getElementById('sd_plan_description').innerHTML = '<h1><span>'+description_title+'</h1><span>'+planDescription+'</span>';
				}else{
					document.getElementById('sd_plan_description').innerHTML = '';
				}
				let discount_html = '<div class="selected_subscription_wrapper"><span class="selected_price">'+quantity_price+'</span><span class="cut_original_price">'+show_cut_original_price+'</span>'+discount_available+''+per_quantity_text+'</div>';
				let all_price_selectors = priceSelectors.join();
				document.querySelector(all_price_selectors).innerHTML = discount_html;
			}
		});

	function getVariant(){
		if (Shopify.designMode) {
			variantID = loaded_variant;
		}else{
			variantID = window.ShopifyAnalytics.meta["selectedVariantId"];
		}

		if(variantID == '' || variantID == undefined || variantID == null){
			let urlQuery = window.location.href;
			urlQuery = new URL(urlQuery);
			variantID = urlQuery.searchParams.get("variant");
			if(variantID == '' || variantID == undefined || variantID == null){
				if(document.getElementsByName("id").length){
						variantID = document.getElementsByName("id")[0].value;
				}else{
						 firstVariant = currentProductVariants[0];
						 variantID = firstVariant.id;
				}
			}else{
			}
		}
	}

	 function checkWidgetCreate(){
		CurrentVariantObjectIndex =  "VID_"+variantID;
		CurrentVariant =  SDSubscriptionConfig['variant'][CurrentVariantObjectIndex];
		if(typeof CurrentVariant !== "undefined" ){
			if(CurrentVariant.variant_available){
			   create_subscription_widget(CurrentVariant);
			}
		}else{
		}
	}

	function removeElement(id) {
		var selection = document.querySelector('#'+id) !== null;
		if (selection) {
			var elem = document.getElementById(id);
			return elem.parentNode.removeChild(elem);
		} else {
		}
	}

	function fill_widget_data(variant_priceallocations_selling_plans){
		let subscriptionPlanGroupsDetails = productpagesetting.subscriptionPlanGroupsDetails;
		var ppd_array_count = subscriptionPlanGroupsDetails.filter(item => item.plan_type === "1").length;
		var prepaid_array_count = subscriptionPlanGroupsDetails.filter(item => item.plan_type === "2").length;
		console.log('ppd_array_count',ppd_array_count);
		console.log('prepaid_array_count',prepaid_array_count);
		all_plans_options_list = '';
		for (var j = 0; j < subscriptionPlanGroupsDetails.length; j++){
				let plan = subscriptionPlanGroupsDetails[j];
				let select_html = '';
				let select_id = '';
				let display_discount_final = '';
				let display_type_text = '';
				total_discount_value = '';
				let total_discount_value_option = '';
				let total_payable_amount = '';
				if(store == 'h-strom-text-kultur.myshopify.com'){
					if(plan.delivery_billing_type == 'MONTH'){
						custom_delivery_billing_type = 'månad(er)';
					}else if(plan.delivery_billing_type == 'WEEK'){
						custom_delivery_billing_type = 'vecka(er)';
					}else if(plan.delivery_billing_type == 'YEAR'){
						custom_delivery_billing_type = 'år(er)';
					}else if(plan.delivery_billing_type == 'DAY'){
						custom_delivery_billing_type = 'dag(er)';
					}
				}else{
					if(plan.delivery_billing_type == 'MONTH'){
						custom_delivery_billing_type = month_frequency_text;
					}else if(plan.delivery_billing_type == 'WEEK'){
						custom_delivery_billing_type = week_frequency_text;
					}else if(plan.delivery_billing_type == 'YEAR'){
						custom_delivery_billing_type = year_frequency_text;
					}else if(plan.delivery_billing_type == 'DAY'){
						custom_delivery_billing_type = day_frequency_text;
					}
				}
				let delivery_every = plan.delivery_policy+` `+custom_delivery_billing_type;
				let billing_period = plan.billing_policy+` `+custom_delivery_billing_type;
				let after_cycle = '';
				selling_planId_index = 'ID_'+(plan.selling_plan_id);
				if(store == 'mevrouw-kraai.myshopify.com' || store == 'simar-test.myshopify.com'){
					if(plan.delivery_billing_type == 'MONTH'){
						var max_cycle_text = 'Maanden';
					}else if(plan.delivery_billing_type == 'WEEK'){
						var max_cycle_text = 'Weken';
					}else if(plan.delivery_billing_type == 'YEAR'){
						var max_cycle_text = 'Jaren';
					} if(plan.delivery_billing_type == 'DAY'){
						var max_cycle_text = 'Dagen';
					}
					if(plan.max_cycle != 0){
						total_discount_value_option = '(Termijn van '+plan.max_cycle+' '+max_cycle_text+')';
					}
				}else{
					if(Object.keys(selling_plans[selling_planId_index].price_adjustment).length > 0){
							if(selling_plans[selling_planId_index].price_adjustment.position_1.type == 'percentage'){
								display_discount_final = save_discount_label +' '+selling_plans[selling_planId_index].price_adjustment.position_1.value+'%';
								all_plans_single_discount = '+ '+display_discount_final;
								total_discount_value = total_saved_discount_label+' '+ selling_plans[selling_planId_index].price_adjustment.position_1.value+'%';
							}else{
								display_discount_final =  save_discount_label +' '+currencyES6Format((((selling_plans[selling_planId_index].price_adjustment.position_1.value)/100) /(plan.billing_policy/plan.delivery_policy)).toFixed(2));
								all_plans_single_discount = '+ '+display_discount_final;
								total_discount_value = total_saved_discount_label+' '+currencyES6Format((selling_plans[selling_planId_index].price_adjustment.position_1.value)/100);
							}
						if(selling_plans[selling_planId_index].price_adjustment.position_2){
							if(selling_plans[selling_planId_index].price_adjustment.position_2.type == 'percentage'){
								discount_value_after = selling_plans[selling_planId_index].price_adjustment.position_2.value+'%';
							}else{
								discount_value_after = currencyES6Format((((selling_plans[selling_planId_index].price_adjustment.position_2.value)/100) /(plan.billing_policy/plan.delivery_policy)).toFixed(2));
							}
							if(selling_plans[selling_planId_index].price_adjustment.position_1.order_count != 1){
								after_cycle = selling_plans[selling_planId_index].price_adjustment.position_1.order_count;
							}

							display_discount_final += ' '+on_first_text+' '+after_cycle+' '+orders_text+', '+then_text+' '+discount_value_after;

						}else if(selling_plans[selling_planId_index].price_adjustment.position_1.value == 0){
							display_discount_final = '';
							all_plans_single_discount = '';
						}
						if(display_discount_final != ''){
							total_discount_value_option = '('+display_discount_final+')';
						}
					}
				}

				    console.log('selling plan list');
				    console.log(CurrentVariant['allocations']['selling_plans']['list']);
					if(CurrentVariant['allocations']['selling_plans']['list'].hasOwnProperty(selling_planId_index)){
					per_quantity_price_html = CurrentVariant['allocations']['selling_plans']['list'][selling_planId_index]['pa_per_delivery_price_formatted'];
					total_payable_amount = CurrentVariant['allocations']['selling_plans']['list'][selling_planId_index]['pa_selling_plan_allocation_price'];
					per_quantity_price_html = per_quantity_price_html.replace('<span class="money">', "");
					per_quantity_price = per_quantity_price_html.replace('</span>', "");
					variant_original_price_html = CurrentVariant['allocations']['selling_plans']['list'][selling_planId_index]['price_formatted'];
					plan_description = escapeHtmlToCode(CurrentVariant['allocations']['selling_plans']['list'][selling_planId_index]['plan_description']);
					checkout_charge_amount = (CurrentVariant['allocations']['selling_plans']['list'][selling_planId_index]['checkout_charge_amount'])/100
					variant_original_price_html = variant_original_price_html.replace('<span class="money">', "");
					variant_original_price = variant_original_price_html.replace('</span>', "");
					if(widget_template == '3'){
						if(plan.plan_type == 1){
							attr_type = 'ppd';
							selling_plan_label = ppd_selling_plans_label+` `+delivery_every;
						}else if(plan.plan_type == 2){
							attr_type = 'prepaid';
							selling_plan_label = pd_selling_plans_label+` `+billing_period+` , `+ppd_selling_plans_label+` `+delivery_every;
						}
						select_html = `<div class="sd_cards_options" onmouseover="this.style.background =  'linear-gradient(to right,` + options_bg_color1 + `,` + options_bg_color2 + `)';" onmouseout="this.style.background =  '`+widget_background_color+`';">
						<div class="innercards sd_subscription_wrapper_option">
						<input type="radio" class="sd_purchase_options" id="purchase_option_${plan.selling_plan_id}" name="purchase_option" value="`+plan.selling_plan_id+`" attr-type="all_plans" discount_value = "`+total_discount_value+`" first-discount = "`+all_plans_single_discount+`" discount="`+display_discount_final+`"  per-original-price ="`+variant_original_price+`" per-quantity-price="`+per_quantity_price+`" attr-deliveryCycle="`+plan.delivery_policy+`" attr-billingCycle="`+plan.billing_policy+`" attr-planDescription = "`+plan_description+`" total-payable-amount = "`+total_payable_amount+`" checkout-charge-amount="`+checkout_charge_amount+`">
							<div class="First">
								<label for="purchase_option_${plan.selling_plan_id}" id="sd_all_plans_option" class="sd_radio_label">${selling_plan_label}</label>
								<span class="sd_discount_ribbon discount9" style="background-color:`+discount_background_color+`; `+discountRibbonStyle+` color:`+discount_text_color+`;">`+display_discount_final+`</span>
							</div>
							<div class="offer_price">
								<label for="purchase_option_${plan.selling_plan_id}"><h2 style="color:`+primary_price_color+`">${per_quantity_price}</h2></label>
							</div>
						</div>
						</div>`;
						all_plans_options_list += select_html;
					}else if(widget_template == '2'){
						if(plan.plan_type == 1){
							attr_type = 'ppd';
							selling_plan_label = ppd_selling_plans_label+` `+delivery_every;
						}else if(plan.plan_type == 2){
							attr_type = 'prepaid';
							selling_plan_label = pd_selling_plans_label+` `+billing_period+` , `+ppd_selling_plans_label+` `+delivery_every;
						}
						if(Object.keys(CurrentVariant['allocations']['selling_plans']['list']).length == 1){
							all_plans_options_list += `<label for="sd_all_plans_list" class="sd_radio_label">`+selling_plan_label+`</label><input style="display:none;" name = "selling_plan" id="sd_all_plans_list" class="sd_select" value="`+plan.selling_plan_id+`" attr-type="all_plans" discount_value = "`+total_discount_value+`" first-discount = "`+all_plans_single_discount+`" discount="`+display_discount_final+`"  per-original-price ="`+variant_original_price+`" per-quantity-price="`+per_quantity_price+`" attr-deliveryCycle="`+plan.delivery_policy+`" attr-billingCycle="`+plan.billing_policy+`" attr-planDescription = "`+plan_description+`" total-payable-amount = "`+total_payable_amount+`" checkout-charge-amount="`+checkout_charge_amount+`">`;
						}else{
							select_html = `<option value="`+plan.selling_plan_id+`" attr-type="all_plans" discount_value = "`+total_discount_value+`" first-discount = "`+all_plans_single_discount+`" discount="`+display_discount_final+`"  per-original-price ="`+variant_original_price+`" per-quantity-price="`+per_quantity_price+`" attr-deliveryCycle="`+plan.delivery_policy+`" attr-billingCycle="`+plan.billing_policy+`" attr-planDescription = "`+plan_description+`" total-payable-amount = "`+total_payable_amount+`" checkout-charge-amount="`+checkout_charge_amount+`">`+selling_plan_label+`</option>`;
							all_plans_options_list += select_html;
						}
					}else{
						if(plan.plan_type == 1) {
							if(ppd_array_count == 1){
								document.getElementById('sd_ppd_select_div').remove();
								select_id = "sd_ppd_list_div";
								select_html = `<span class="sd_hello">`+ppd_selling_plans_label+` `+delivery_every+` `+total_discount_value_option+`</span><input id="sd_ppd_list" style="display:none;" value="`+plan.selling_plan_id+`" attr-type="ppd" discount_value = "`+total_discount_value+`" discount="`+display_discount_final+`"  per-original-price ="`+variant_original_price+`" per-quantity-price="`+per_quantity_price+`" attr-deliveryCycle="`+plan.delivery_policy+`" attr-billingCycle="`+plan.billing_policy+`" attr-planDescription = "`+plan_description+`" total-payable-amount = "`+total_payable_amount+`" checkout-charge-amount="`+checkout_charge_amount+`">`;
								document.getElementById(select_id).innerHTML = select_html;
							}else{
								select_id = "sd_ppd_list";
								select_html = `<option value="`+plan.selling_plan_id+`" attr-type="ppd" discount_value = "`+total_discount_value+`" discount="`+display_discount_final+`"  per-original-price ="`+variant_original_price+`" per-quantity-price="`+per_quantity_price+`" attr-deliveryCycle="`+plan.delivery_policy+`" attr-billingCycle="`+plan.billing_policy+`" attr-planDescription = "`+plan_description+`" total-payable-amount = "`+total_payable_amount+`" checkout-charge-amount="`+checkout_charge_amount+`">`+delivery_every+` `+total_discount_value_option+`</option>`;
								document.getElementById(select_id).insertAdjacentHTML('beforeend',select_html);
							}
						} else if (plan.plan_type == 2) {
							if(prepaid_array_count == 1){
								document.getElementById('sd_prepaid_select_div').remove();
								select_id = "sd_prepaid_list_div";
								select_html = `<span class="sd_hello">`+pd_selling_plans_label+` `+billing_period+` , `+ppd_selling_plans_label+` `+delivery_every+` `+total_discount_value_option+`</span><input id="sd_prepaid_list" style="display:none;" value="`+plan.selling_plan_id+`" attr-type="ppd" discount_value = "`+total_discount_value+`" discount="`+display_discount_final+`"  per-original-price ="`+variant_original_price+`" per-quantity-price="`+per_quantity_price+`" attr-deliveryCycle="`+plan.delivery_policy+`" attr-billingCycle="`+plan.billing_policy+`" attr-planDescription = "`+plan_description+`" total-payable-amount = "`+total_payable_amount+`" checkout-charge-amount="`+checkout_charge_amount+`">`;
								document.getElementById(select_id).innerHTML = select_html;
							}else{
								select_id = "sd_prepaid_list";
								select_html = `<option value="`+plan.selling_plan_id+`" attr-type="prepaid" discount_value = "`+total_discount_value+`" discount="`+display_discount_final+`" per-original-price ="`+variant_original_price+`" per-quantity-price="`+per_quantity_price+`" attr-deliveryCycle="`+plan.delivery_policy+`" attr-billingCycle="`+plan.billing_policy+`" attr-planDescription = "`+plan_description+`" total-payable-amount = "`+total_payable_amount+`" checkout-charge-amount="`+checkout_charge_amount+`">`+billing_period+` , `+ppd_selling_plans_label+` `+delivery_every+` `+total_discount_value_option+`</option>`;
								document.getElementById(select_id).insertAdjacentHTML('beforeend',select_html);
							}
						}

					}
				}
		}
			if(widget_template == '3'){
				document.getElementById('sd_all_plans_delivery_wrapper').insertAdjacentHTML('beforeend',all_plans_options_list);
			}else if(widget_template == '2'){
				if(document.getElementById('sd_subscriptionAllOptionsWrapper')){
					if(Object.keys(CurrentVariant['allocations']['selling_plans']['list']).length == 1){
						all_selling_plan_options_html = all_plans_options_list;
					}else{
						all_selling_plan_options_html = `<select name="" id="sd_all_plans_list" class="sd_select">`+all_plans_options_list+`</select>`;
					}
					document.getElementById('sd_subscriptionAllOptionsWrapper').innerHTML = all_selling_plan_options_html;
				}
			}else if(widget_template == '1'){
				// let ppd_option_length = document.querySelector('#sd_ppd_list').length;
				// let prepaid_option_length = document.querySelector( '#sd_prepaid_list' ).length;
				if(ppd_array_count == 0){
					document.querySelector( '#sd_pay_per_delivery_wrapper' ).remove();
				}else if(prepaid_array_count == 0){
					document.querySelector( '#sd_prepaid_delivery_wrapper' ).remove();
				}
				// if(ppd_option_length == 1){
				// 	document.getElementById('sd_ppd_list').classList.add("sd_single_option");
				// }
				// if(prepaid_option_length == 1){
				// 	document.getElementById('sd_prepaid_list').classList.add("sd_single_option");
				// }
			}
			return Promise.resolve("success");
	}

	function escapeHtmlToCode(text) {
		if((text != '') && (typeof text !== 'undefined') && text != null){
			return text
				.replace(/&amp;/g, "&")
				.replace(/amp;/g, "&")
				.replace(/lt;/g, "<")
				.replace(/gt;/g, ">")
				.replace(/&quot;/g, '"')
				.replace(/quot;/g, '"')
				.replace(/&#039;/g,"'")
				.replace(/#039;/g, "'");
		}else{
			return text;
		}
	}

	function create_subscription_widget(CurrentVariant){
	  removeElement('sd_widget_wrapper');
	  removeElement('sd_subscriptionPrc');
	  if(document.getElementById('sd_widget_'+random_number)){
		removeElement('sd_widget_'+random_number);
	  }
	  let variant_priceallocations_selling_plans = CurrentVariant.allocations.selling_plans.list;
	  let show_one_time_purchase = SDSubscriptionConfig['product']['requires_selling_plan'];
	  if(Object.keys(variant_priceallocations_selling_plans).length == 0){
		get_forms.forEach(function (element) {
			element.classList.remove('sd_add_subscription_widget');
		});
		return;
	  }
	  selling_plans = SDSubscriptionConfig.selling_plans.list;
	  if (Shopify.designMode) {
		currentProductID = productId;
		currentProductVariantsObj = product_variants;
	  }else{
	  currentProductID = window.ShopifyAnalytics.meta['product'].id;
	  currentProductVariantsObj  = window.ShopifyAnalytics.meta['product'].variants;
	  }

	  MerchantAjaxCall("POST","action=subscriptionPlanGroupsDetails&store="+Shopify.shop+"&product_id="+currentProductID,false);
	  productpagesetting =  JSON.parse(ajaxResponseData);
	  store_widget_data = productpagesetting.store_widget_data; // new_code_updated
		if(store_widget_data){
			widget_template = store_widget_data.widget_template;
			widget_arrange_label = (store_widget_data.widget_arrange_label).split(',');
			one_time_purchase_option_label = store_widget_data.one_time_purchase_text;
			border_style = store_widget_data.border_style;
			border_color = store_widget_data.border_color;
			text_color = store_widget_data.text_color;
			primary_price_color = store_widget_data.price_color;
			secondary_price_color = store_widget_data.discounted_price_color;
			subscription_detail_title = store_widget_data.subscription_detail_text;
			widget_background_color = store_widget_data.widget_bg_color;
			widget_title = store_widget_data.purchase_options_text;
			selected_background_color = store_widget_data.active_option_box_bg_color;
			prepaid_option_label = store_widget_data.prepaid_options_text;
			description_title = store_widget_data.description_text;
			discount_background_color = store_widget_data.discount_bg_color;
			ppd_selling_plans_label = store_widget_data.delivery_every_text;
			pd_selling_plans_label = store_widget_data.pre_pay_for_text;
			pay_per_delivery_option_label = store_widget_data.ppd_options_text;
			one_time_purchase_description = store_widget_data.one_time_purchase_description;
			discount_text_color = store_widget_data.discount_label_color;
			per_quantity_text = store_widget_data.each_text;
			save_discount_label = store_widget_data.save_text;
			total_saved_discount_label = store_widget_data.total_saved_discount_text;
			subscription_detail = store_widget_data.additional_subscription_detail;
			total_payable_amount_text = store_widget_data.total_payable_amount_text;
			month_frequency_text = store_widget_data.month_frequency_text;
			year_frequency_text = store_widget_data.year_frequency_text;
			week_frequency_text = store_widget_data.week_frequency_text;
			day_frequency_text = store_widget_data.day_frequency_text;
			orders_text = store_widget_data.orders_text;
			then_text = store_widget_data.then_text;
			on_first_text = store_widget_data.on_first_text;
			subscription_options_text = store_widget_data.subscription_options_text;
			options_bg_color1 = store_widget_data.options_bg_color1;
			options_bg_color2 = store_widget_data.options_bg_color2;
		}else{
			widget_template = '1';
			widget_arrange_label = ('One Time Purchase,Pay Per Delivery,Prepaid').split(',');
			one_time_purchase_option_label = 'One Time Purchase';
			border_style = 'solid';
			border_color = '#09080D';
			text_color = '#1D1D1D';
			primary_price_color = '#5F5F5F';
			secondary_price_color = '#33353E';
			subscription_detail_title = 'Subscription detail';
			widget_background_color = '#FFFFFF';
			widget_title = 'Purchase options';
			selected_background_color = '#F0F2F5';
			prepaid_option_label = 'Prepaid';
			description_title = 'Description';
			discount_background_color = '#ffffff00';
			ppd_selling_plans_label = 'Delivery Every';
			pd_selling_plans_label = 'Pre-Pay for';
			pay_per_delivery_option_label = 'Pay Per Delivery';
			one_time_purchase_description = 'One time purchase product';
			discount_text_color = '#F94556';
			per_quantity_text = 'each';
			save_discount_label = 'Save'
			total_saved_discount_label = 'Total saved';
			subscription_detail = 'Additional subscription detail, Have complete control of your subscritpion Skip, reschedule, edit or cancel deliveries any time based on your needs.';
			total_payable_amount_text = 'Total payable';
			month_frequency_text = 'Month(s)';
			year_frequency_text = 'Year(s)';
			week_frequency_text = 'Week(s)';
			day_frequency_text = 'Day(s)';
			orders_text = 'order(s)';
			then_text = 'then';
			on_first_text = 'on first';
			subscription_options_text = 'Subscribe and Save';
			options_bg_color1 = '#C4C4C4';
			options_bg_color2 = '#F0F0F0';
		}

		if(store == 'ca-thebetterchocolate.myshopify.com'){
			theme_block_support = 'false';
		}else{
			theme_block_support = theme_block_supported;
		}

		if(show_one_time_purchase){
			One_Time_Purchase_html = '';
		}else{
			if(widget_template == '2' || widget_template == '3'){
			   var one_time_purchase_price = '<div class="selected_price_wrapper all_plans_selected_price" style="color:'+primary_price_color+'">'+CurrentVariant.onetimepurchaseprice+'</div>'
			   var border_type = 'border : 1px ';
			}else{
				var one_time_purchase_price = '';
				var border_type = '';
			}

			if(widget_template == '3'){
				One_Time_Purchase_html = `<div class="sd_cards_options" onmouseover="this.style.background =  'linear-gradient(to right,` + options_bg_color1 + `,` + options_bg_color2 + `)';" onmouseout="this.style.background =  '`+widget_background_color+`';">
				<div class="innercards activemethod sd_subscription_wrapper_option">
					<input type="radio" class="sd_purchase_options" id="sd_subscription_frequency_plan_radio" name="purchase_option" value="Subscribe and save">
					<div class="First">
					<label for="sd_subscription_frequency_plan_radio" id="label_one_time_purchase" class="sd_radio_label">
					`+escapeHtmlToCode(one_time_purchase_option_label)+`
					 </label>
					</div>
					<div class="offer_price">
					<label for="sd_subscription_frequency_plan_radio"><h2>${one_time_purchase_price}</h2></label>
					</div>
				</div>
				</div>`;
			}else{
				One_Time_Purchase_html = `<div id="sd_subscription_frequency_plan_wrapper" class="sd_subscription_wrapper_option" style="`+border_type+``+border_style+` `+border_color+`;" onmouseover="this.style.background =  'linear-gradient(to right,` + options_bg_color1 + `,` + options_bg_color2 + `)';" onmouseout="this.style.background =  '`+widget_background_color+`';">
					<div class="sd_subscription_label_wrapper">
						<div class="sd_label_discount_wrapper">
							<div class="radio_discount_wrapper"	>
								<input type="radio" class="sd_purchase_options" id="sd_subscription_frequency_plan_radio" name="purchase_option" value="Subscribe and save">
								<label for="sd_subscription_frequency_plan_radio" id="label_one_time_purchase" class="sd_radio_label">
							   `+escapeHtmlToCode(one_time_purchase_option_label)+`
								</label>
							</div>
							${one_time_purchase_price}
						</div>
					</div>
				</div>`;
			}
		}
		let widgetBody = '';

			subscription_widget_block = `<div class="sd_subscriptionPrc sd_description_tooltip" id="sd_subscriptionPrc">
				<div class="sd_subscription_privacy" id="sd_subscription_privacy">`;
				if(subscription_detail_title != '' && subscription_detail != ''){
					subscription_widget_block += `<div class="sd_recurringMsg">
					<img src="`+SHOPIFY_DOMAIN_URL+`/application/assets/images/subscription_detail.png">
					`+subscription_detail_title+`
					</div>
					<div class="Polaris-PositionedOverlay">
						<div class="Polaris-Tooltip-TooltipOverlay" data-polaris-layer="true">
							<div id="PolarisTooltipContent2" role="tooltip" class="Polaris-Tooltip-TooltipOverlay__Content"> `+subscription_detail+`</div>
						</div>
					</div>`;
				}
				subscription_widget_block += `</div>
			</div>`;

	if(widget_template == '3'){
		widgetBody += `<div id="sd_widget_wrapper" style="background-color:`+widget_background_color+`">
							<div class="sd_widget_title">`+widget_title+`</div>
							<div class="sd_widget_mainwrapper template_`+widget_template+`" style="margin-top : 1px; margin-bottom : 1px; background-color:`+widget_background_color+`;">
								 <div class="sd_subscription_wrapper"><div class="sd_subscription_wrapper" id="sd_all_plans_delivery_wrapper" class="sd_select">`;
							 widgetBody += One_Time_Purchase_html;
							widgetBody += `</div></div><!-- sd_subscription_wrapper -->
			<div id="sd_totalPayableAmount"></div>
			<div id="sd_plan_description" style="background-color:`+widget_background_color+`;"></div>`+subscription_widget_block+`
		</div>`;
	}else if(widget_template == '2'){
	   var option_html = `<div id="sd_all_plans_delivery_wrapper" class="sd_subscription_wrapper_option sd_all_subscription_plans">
			<div class="sd_subscription_label_wrapper" onmouseover="this.style.background =  'linear-gradient(to right,` + options_bg_color1 + `,` + options_bg_color2 + `)';" onmouseout="this.style.background =  '`+widget_background_color+`';">
				<div class="sd_label_discount_wrapper" style="border : 1px `+border_style+` `+border_color+`; border-radius:7px;">
					<div class="radio_discount_wrapper">
						<input type="radio" class="sd_purchase_options" id="sd_all_delivery_purchase_radio" name="purchase_option" value="Subscribe and save">
						<label for="sd_all_delivery_purchase_radio" id="label_all_plans" class="sd_radio_label">
						`+subscription_options_text+`
						</label>
					</div>
					<div class="selected_price_wrapper all_plans_selected_price" id="all_plans_selected_price" style="color:`+primary_price_color+`">

					</div>
				</div>
			</div>
			<div id="sd_subscriptionAllOptionsWrapper" class="sd_frequency_plan_option_wrapper display-hide-label"></div>
		</div>`;

	   widgetBody += `<div id="sd_widget_wrapper"  style="background:`+widget_background_color+`"><div class="sd_widget_title">`+widget_title+`</div>
		<div class="sd_widget_mainwrapper template_`+widget_template+`" style="margin-top : 1px; margin-bottom : 1px; background-color:`+widget_background_color+`;">
		<div class="sd_subscription_wrapper">`;
			widgetBody += One_Time_Purchase_html;
			widgetBody += option_html;
			widgetBody += `</div><!-- sd_subscription_wrapper -->
		</div>
		<div id="sd_totalPayableAmount"></div>
		<div id="sd_plan_description" style="background-color:`+widget_background_color+`;"></div>`+subscription_widget_block+`</div>`;
	}else{
		Pay_Per_Delivery_html = `<div id="sd_pay_per_delivery_wrapper" class="sd_subscription_wrapper_option" onmouseover="this.style.background =  'linear-gradient(to right,` + options_bg_color1 + `,` + options_bg_color2 + `)';" onmouseout="this.style.background =  '`+widget_background_color+`';">
			<div class="sd_subscription_label_wrapper">
				<div class="radio_discount_wrapper"	>
					<input type="radio" class="sd_purchase_options" id="sd_pay_per_delivery_purchase_radio" name="purchase_option" value="Subscribe and save">
					<label for="sd_pay_per_delivery_purchase_radio" id="label_pay_per_delivery" class="sd_radio_label">
				`+escapeHtmlToCode(pay_per_delivery_option_label)+`
					</label>
				</div>
				<div class="selected_price_wrapper pay_per_delivery_selected_price" id="pay_per_delivery_selected_price">
				</div>
				<div id="sd_subscriptionPayPerDeliveryOptionsWrapper" class="sd_frequency_plan_option_wrapper display-hide-label">
					<div id="sd_ppd_select_div">
						<label for="sd_selling_plan_title" class="sd_select_label"> `+ppd_selling_plans_label+`</label>
						<select name="" id="sd_ppd_list" class="sd_select">

						</select>
					</div>
					<div id="sd_ppd_list_div"></div>
				</div>
			</div>
		</div>`;

		Prepaid_html = `<div id="sd_prepaid_delivery_wrapper" class="sd_subscription_wrapper_option" onmouseover="this.style.background =  'linear-gradient(to right,` + options_bg_color1 + `,` + options_bg_color2 + `)';" onmouseout="this.style.background =  '`+widget_background_color+`';">
			<div class="sd_subscription_label_wrapper">
				<div class="radio_discount_wrapper"	>
					<input type="radio" class="sd_purchase_options" id="sd_prepaid_delivery_purchase_radio" name="purchase_option" value="Subscribe and save">
					<label for="sd_prepaid_delivery_purchase_radio" id="label_prepaid" class="sd_radio_label">
					`+prepaid_option_label+`
					</label>
				</div>
				<div class="selected_price_wrapper prepaid_selected_price" id="prepaid_selected_price">
				</div>
				<div id="sd_subscriptionPrepaidOptionsWrapper" class="sd_frequency_plan_option_wrapper display-hide-label">
					<div id="sd_prepaid_select_div">
						<label for="sd_selling_plan_title" class="sd_select_label"> `+pd_selling_plans_label+`</label>
						<select name=""  id="sd_prepaid_list" class="sd_select">

						</select>
					</div>
					<div id="sd_prepaid_list_div"></div>
				</div>
			</div>
		</div>`;
		widgetBody += `
		<div id="sd_widget_wrapper" class="sd_widget_mainwrapper template_1" style="margin-top : 1px; border : 1px `+border_color+`; border-style : `+border_style+`;background-color:`+widget_background_color+`">
		<div class="sd_widget_title" style="border-bottom : 1px `+border_style+` `+border_color+`;">`+widget_title+`</div>
		<div class="sd_subscription_wrapper">
			`+window[widget_arrange_label[0].replaceAll(' ', '_')+'_html']+`
			`+window[widget_arrange_label[1].replaceAll(' ', '_')+'_html']+`
			`+window[widget_arrange_label[2].replaceAll(' ', '_')+'_html']+`
			<div id="sd_plan_description"></div>
			<div id="sd_totalPayableAmount"></div>
		</div><!-- sd_subscription_wrapper -->
		</div>`+subscription_widget_block;
	}
	 //mainWrapperEnd
		let check_block_wrapper = document.getElementById('advanced-subscription-block-wrapper');
		if(theme_block_support == 'true' && store != 'iwara-shop.myshopify.com'){
			if(check_block_wrapper){
				check_block_wrapper.innerHTML = widgetBody;
				if(document.body.querySelector('form[action*="/cart/add"] .sd_widget_wrapper')){
				}else{
					get_forms.forEach(function (element) {
						element.classList.add('sd_add_subscription_widget');
						element.insertAdjacentHTML('afterbegin','<input type="hidden" name="selling_plan" value="">');
					});
					hidden_selling_plan_name = 'Yes';
				}
			}
		}else{
			get_forms.forEach(function (element) {
				element.classList.add('sd_add_subscription_widget');
				var add_element_selector = '';
				if(store == 'bread-beauty-supply.myshopify.com'){
					add_element_selector = ',.quantity-submit-row';
				}else if(store == 'bio-bod.myshopify.com'){
					add_element_selector = ',.ProductForm__AddToCart';
				}else if(store == 'iwara-shop.myshopify.com'){
					add_element_selector = ',.pro-detail-button';
				}else if(store == 'mevrouw-kraai.myshopify.com'){
					add_element_selector = ',.product-quantity-container';
				}
				let submit_button = element.querySelector("[name='add']"+add_element_selector);
				if(submit_button != null){
					submit_button.insertAdjacentHTML('beforebegin',widgetBody);
				}
			});
		}
		if(document.body.querySelector('form[action*="/cart/add"] .sd_widget_wrapper')){
		}else{
			get_forms.forEach(function (element) {
				element.insertAdjacentHTML('afterbegin','<input type="hidden" name="selling_plan" value="">');
			});
			hidden_selling_plan_name = 'Yes';
		}

	if(document.getElementById("sd_subscription_frequency_plan_radio")){
		document.getElementById("sd_subscription_frequency_plan_radio").onclick = function(){
			select_subscription_plan('sd_subscription_frequency_plan_wrapper');
		}
		if(document.getElementById("sd_subscription_frequency_plan_wrapper")){
			document.getElementById("sd_subscription_frequency_plan_wrapper").onclick = function(){
				document.getElementById("sd_subscription_frequency_plan_radio").click();
			}
		}
	}
    if(document.getElementById("sd_pay_per_delivery_purchase_radio")){
		document.getElementById("sd_pay_per_delivery_purchase_radio").onclick = function(){
			select_subscription_plan('sd_pay_per_delivery_wrapper');
		}
		if(document.querySelector("#sd_pay_per_delivery_wrapper .radio_discount_wrapper")){
			document.querySelector("#sd_pay_per_delivery_wrapper .radio_discount_wrapper").onclick = function(){
				document.getElementById("sd_pay_per_delivery_purchase_radio").click();
			}
		}
    }

	if(document.getElementById("sd_prepaid_delivery_purchase_radio")){
		document.getElementById("sd_prepaid_delivery_purchase_radio").onclick = function(){
			select_subscription_plan('sd_prepaid_delivery_wrapper');
		}
		if(document.querySelector("#sd_prepaid_delivery_wrapper .radio_discount_wrapper")){
			document.querySelector("#sd_prepaid_delivery_wrapper .radio_discount_wrapper").onclick = function(){
				document.getElementById("sd_prepaid_delivery_purchase_radio").click();
			}
		}
	}

	if(document.getElementById("sd_all_delivery_purchase_radio")){
		document.getElementById("sd_all_delivery_purchase_radio").onclick = function(){
			select_subscription_plan('sd_all_plans_delivery_wrapper');
		}
		if(document.querySelector("#sd_all_plans_delivery_wrapper .sd_subscription_label_wrapper")){
			document.querySelector("#sd_all_plans_delivery_wrapper .sd_subscription_label_wrapper").onclick = function(){
				document.getElementById("sd_all_delivery_purchase_radio").click();
			}
		}
    }

		function select_subscription_plan(wrapper_id){
			if(widget_template == '2' || widget_template == '3'){
				if(document.getElementById('label_all_plans')){
				   document.getElementById('label_all_plans').innerHTML = subscription_options_text;
				}
			}
			let discount_html = '';
			const discount_div = document.getElementsByClassName('sd_discount_ribbon');
			while(discount_div.length > 0){
				discount_div[0].parentNode.removeChild(discount_div[0]);
			}
			document.getElementById('sd_plan_description').innerHTML = '';
			document.querySelectorAll('#sd_widget_wrapper select').forEach(function(element) {
				element.setAttribute('name', '');
			});
			if(document.getElementById('pay_per_delivery_selected_price')){
			    document.getElementById('pay_per_delivery_selected_price').innerHTML = '';   //
			    if(document.getElementById("sd_ppd_list")){
			      document.getElementById("sd_ppd_list").selectedIndex = "0";
			    }
			    document.getElementById("sd_pay_per_delivery_wrapper").classList.remove('activemethod');
			}
			if(document.getElementById('prepaid_selected_price')){
			   document.getElementById('prepaid_selected_price').innerHTML = '';
			    if(document.getElementById("sd_prepaid_list")){
			      document.getElementById("sd_prepaid_list").selectedIndex = "0";
			    }
			   document.getElementById("sd_prepaid_delivery_wrapper").classList.remove('activemethod');
			}

			if(document.getElementById('all_plans_selected_price')){
				document.getElementById('all_plans_selected_price').innerHTML = '';
				if(document.getElementById("sd_all_plans_list")){
				 document.getElementById("sd_all_plans_list").selectedIndex = "0";
				}
				document.getElementById("sd_all_plans_delivery_wrapper").classList.remove('activemethod');
			}

			if(document.getElementById("sd_subscription_frequency_plan_wrapper")){
				document.getElementById("sd_subscription_frequency_plan_wrapper").classList.remove('activemethod');
			}

			if(widget_template == '1' || widget_template == '2'){
				document.getElementById(wrapper_id).classList.add('activemethod');
			}

			let add_class_params = {
				class_elements:[
					{
						name:"sd_frequency_plan_option_wrapper",
						classname:"display-hide-label"
					}
				]
			};
			add_class(add_class_params);
			if(document.querySelector('#'+wrapper_id+' select')){
				document.querySelector('#'+wrapper_id+' select').setAttribute('name', 'selling_plan');
			}
			if(wrapper_id == 'sd_prepaid_delivery_wrapper'){
				discount_type = 'prepaid';
			}else if(wrapper_id == 'sd_pay_per_delivery_wrapper'){
				discount_type = 'ppd';
			}else if(wrapper_id == 'sd_all_plans_delivery_wrapper'){
				discount_type = 'all_plans';
			}else{
				if(one_time_purchase_description){
				   document.getElementById('sd_plan_description').innerHTML = '<h1>'+description_title+'</h1><span >'+one_time_purchase_description+'</span>';
				}
			}
			if(wrapper_id == 'sd_prepaid_delivery_wrapper' || wrapper_id == 'sd_pay_per_delivery_wrapper' || wrapper_id == 'sd_all_plans_delivery_wrapper'){
				document.querySelector("#"+wrapper_id+" .sd_frequency_plan_option_wrapper").classList.remove('display-hide-label');
				if(document.querySelector("#sd_widget_wrapper .activemethod").querySelector("select")){
					selected_plan_type = document.getElementById('sd_'+discount_type+'_list').options[document.getElementById('sd_'+discount_type+'_list').selectedIndex];
					console.log('option exist yes');
				}else{
					selected_plan_type = document.getElementById('sd_'+discount_type+'_list');
					console.log('option exist no');
				}
				var discount_selected = selected_plan_type.getAttribute('discount');

				var quantity_price = selected_plan_type.getAttribute('per-quantity-price');
				deliveryCycle = selected_plan_type.getAttribute('attr-deliveryCycle');
				billingCycle = selected_plan_type.getAttribute('attr-billingCycle');
				total_saved_discount_value =  selected_plan_type.getAttribute('discount_value');
				checkout_charge_amount =  selected_plan_type.getAttribute('checkout-charge-amount');
				per_originial_price = selected_plan_type.getAttribute('per-original-price');
				planDescription = selected_plan_type.getAttribute('attr-planDescription');
				selected_selling_plan_id = selected_plan_type.value;

				if(hidden_selling_plan_name == 'Yes'){
					get_all_selling_plan_name = document.body.querySelectorAll('form[action*="/cart/add"] input[name="selling_plan"]');
					get_all_selling_plan_name.forEach(function (element) {
						element.value = selected_selling_plan_id;
					});
				}
				if(quantity_price != per_originial_price){
				   show_cut_original_price = per_originial_price;
				}else{
					show_cut_original_price = '';
				}
				discountRibbonStyle = discount_selected.length > 0 ? 'display: inline-block;' : 'display: none;';
				if(widget_template == '2' || widget_template == '3'){
					discount_price_html = `<span id="`+discount_type+`_discount_price" class="selected_price" style="color:`+primary_price_color+`;">${quantity_price}</span>`;
					if(document.querySelector("#sd_widget_wrapper .activemethod").querySelector("select")){
						var get_single_discount = document.getElementById('sd_'+discount_type+'_list').options[document.getElementById('sd_'+discount_type+'_list').selectedIndex].getAttribute('first-discount');
					}else{
						var get_single_discount = document.getElementById('sd_'+discount_type+'_list').getAttribute('first-discount');
					}
					if(document.getElementById('label_all_plans')){
					  document.getElementById('label_all_plans').innerHTML = subscription_options_text+` `+get_single_discount;
					}
				}else{
					discount_price_html = `<span id="`+discount_type+`_discount_price" class="selected_price" style="color:`+primary_price_color+`;">${quantity_price}</span><span id="`+discount_type+`_cut_original_price" class="cut_original_price"  style="color:`+secondary_price_color+`;">${show_cut_original_price}</span><span id="sd_`+discount_type+`_price_text" style="color:`+text_color+`">`+per_quantity_text+`</span>`;
				}
				document.querySelector('#'+wrapper_id+' .selected_price_wrapper').innerHTML = discount_price_html;
				// append new price
				discount_html = `<div class="selected_subscription_wrapper"><span class="selected_price">`+quantity_price+`</span><span class="cut_original_price">`+show_cut_original_price+`</span><span class="sd_discount_ribbon discount10" style="background-color:`+discount_background_color+`; `+discountRibbonStyle+` color:`+discount_text_color+`;">`+discount_selected+`</span><span style="color:`+text_color+`">`+per_quantity_text+`</span></div>`;
				if(planDescription != '' && planDescription != null && planDescription != 'null'){
					document.getElementById('sd_plan_description').innerHTML = '<h1><span>'+description_title+'</h1><span>'+planDescription+'</span>';
				}
			}else if(wrapper_id == 'sd_subscription_frequency_plan_wrapper'){
				if(hidden_selling_plan_name == 'Yes'){
					get_all_selling_plan_name = document.body.querySelectorAll('form[action*="/cart/add"] input[name="selling_plan"]');
					get_all_selling_plan_name.forEach(function (element) {
						element.value = '';
					});
				}
				discount_html = '<div class="selected_subscription_wrapper"><span class="selected_price">'+CurrentVariant.onetimepurchaseprice+'</span></div>';
			}
			let all_price_selectors = priceSelectors.join();
			if(document.querySelector(all_price_selectors)){
				document.querySelector(all_price_selectors).innerHTML = discount_html;
			}

			if(document.querySelector('input[name="quantity"]')){
				product_input_quantity = document.querySelector('input[name="quantity"]').value;
			}else{
				product_input_quantity = '1';
			}
			total_payable_amount = (product_input_quantity*checkout_charge_amount);
			if(wrapper_id != 'sd_subscription_frequency_plan_wrapper'){
				document.getElementById('sd_totalPayableAmount').innerHTML = total_payable_amount_text+' '+currencyES6Format(total_payable_amount)+' '+'<span class="sd_discount_ribbon discounttt" style="background:'+discountRibbonStyle+'">'+total_saved_discount_value+'</span>';
			}
		}
		fill_widget_data(variant_priceallocations_selling_plans);
		select_bydefault_radio();
		if(productpagesetting.app_subscription_status != 'ACTIVE' && store != 'bio-bod.myshopify.com'){
			document.getElementById('sd_subscription_privacy').insertAdjacentHTML('afterend','<div id="sd_widget_'+random_number+'" style="display:block!important; font-size:1.2rem;background-color: #000;color: #fff;padding: 3px 8px;visibility: visible!important;">Powered by Shine Dezign</div>');
		}
	}
	}//create_subscription_widget end
	}