let picker_selection_checkboxes = {};
let before_updating_checkbox_data_picker_selection = {}; // this array is used for checking validatiion on subscription edit page that whether the checked product added in the group or not when click on the back button
let picker_display_wrapper = '';
let picker_display_style = '';
let add_product = '';
let add_out_of_stock_product = '';
let add_shipping_address = 'No';
	if(typeof shipping_address_added === 'undefined'){
		shipping_address_added = '';
	}
	function add_class(elements_array){
		let class_elements = elements_array.class_elements;
		class_elements.forEach(function(value, index, array) {
			sd_className = document.getElementsByClassName(value.name);
			for (let i = 0; i < sd_className.length; i++) {
				sd_className[i].classList.add(value.classname);
			}
		});
	}

	function remove_class(elements_array){
		let class_elements = elements_array.class_elements;
		class_elements.forEach(function(value, index, array) {
			sd_className = document.getElementsByClassName(value.name);
			for (let i = 0; i < sd_className.length; i++) {
				sd_className[i].classList.remove(value.classname);
			}
		});
	}

	function getVariantHtml(variantsArray,product_title,product_id,product_Image){
			variantHtml = '',variants_view_more='',add_main_checkbox='No';
		    variantsArray['edges'].forEach(function(variantItem){
				variantCursor = variantItem['cursor'];
				checkboxValue = '';
				variant_id = variantItem['node']['id'].replace( /[^\d.]/g, '' );
				console.log('shipping address added2 =',shipping_address_added);
                if(shipping_address_added == 0 && variantItem['node']['requiresShipping'] == true){
					add_shipping_address = 'Yes';
					console.log('add_shipping_address1',add_shipping_address);
				}
				if(Object.keys(picker_selection_checkboxes).length > 0){
					if (picker_selection_checkboxes.hasOwnProperty(variant_id)) {
						checkboxValue = 'checked';
						total_selected_variants++;
					}else{
						allSelectedVariants = "";
					}
				}else{
					allSelectedVariants = "";
				}

			  // disbale the products that exist in the contract
				if(disabled_product_variant_array.includes(variant_id)){
					disableAddedProducts = 'added_contract_product';
					checkboxValue = 'disabled';
					added_variant_count++;
			   }else{
				   disableAddedProducts = '';
				   product_disable = '';
			   }
			   // disbale the products that exist in the contract end
				if(variantItem['node']['image']){
					variantImage = variantItem['node']['image']['originalSrc'];
				}else{
					variantImage = product_Image;
				}
                variant_inventory = variantItem['node']['inventoryQuantity'];
				variant_title = variantItem['node']['title'].replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;");
				if(AjaxCallFrom == 'frontendAjaxCall' && add_out_of_stock_product == '1'){
					add_product_variant = 'Yes';
				}else if(AjaxCallFrom == 'frontendAjaxCall' && add_out_of_stock_product == '0'){
					if((variant_inventory > 0)){
						add_product_variant = 'Yes';
					}else{
						add_product_variant = 'No';
					}
				}else if(AjaxCallFrom == 'backendAjaxCall'){
					add_product_variant = 'Yes';
				}else{
					add_product_variant = 'No';
				}

                if(add_product_variant == 'Yes'){
					if(AjaxCallFrom == 'backendAjaxCall'){
						  show_product_checkbox =  `<div class="Polaris-ResourceItem__CheckboxWrapper"><div><label class="Polaris-Choice Polaris-Choice--labelHidden" for="PolarisResourceListItemCheckbox7"><span class="Polaris-Choice__Control"></span><span class="Polaris-Checkbox"><input id="productSelected_`+variant_id+`" data-productimage="`+variantImage+`" data-productHandle = "`+product_handle+`" class="productSelected variantSelected ${disableAddedProducts}" name="sd_addNewProducts[]" data-productid="`+product_id+`" data-producttitle="`+product_title+`" data-subscriptionprice="`+variantItem['node']['price']+`"  data-varianttitle="`+variant_title+`" value="`+variant_id+`" type="checkbox" aria-invalid="false" role="checkbox" aria-checked="false" ${checkboxValue}  ></span></label></div></div>`;
						  link_to_update_product_inventory = '';
						  add_main_checkbox='Yes';
						if((variant_inventory > 0) || (variantItem['node']['inventoryPolicy'] == 'CONTINUE')){
							available_stock = ``;
						}else{
							available_stock = `<span class="sd_totalInventory sd_out_of_stock">Out Of Stock</span>`;
						}
					}else{
						show_product_checkbox =  `<div class="Polaris-ResourceItem__CheckboxWrapper"><div><label class="Polaris-Choice Polaris-Choice--labelHidden" for="PolarisResourceListItemCheckbox7"><span class="Polaris-Choice__Control"></span><span class="Polaris-Checkbox"><input id="productSelected_`+variant_id+`" data-productimage="`+variantImage+`" data-productHandle = "`+product_handle+`" class="productSelected variantSelected ${disableAddedProducts}" name="sd_addNewProducts[]" data-productid="`+product_id+`" data-producttitle="`+product_title+`" data-subscriptionprice="`+variantItem['node']['price']+`"  data-varianttitle="`+variant_title+`" value="`+variant_id+`" type="checkbox" aria-invalid="false" role="checkbox" aria-checked="false" ${checkboxValue}  ></span></label></div></div>`;
						link_to_update_product_inventory = '';
						available_stock = '';
					}
					variantHtml +=  `<div class="Polaris-ResourceItem__Container"><div class="Polaris-ResourceItem__Owned sd_click_checkbox" data-attr = "productSelected_`+variant_id+`"><div class="Polaris-ResourceItem__Handle sd_click_checkbox" data-attr = "productSelected_`+variant_id+`">${show_product_checkbox}</div><div class="Polaris-ResourceItem__Media"><span role="img" class="Polaris-Avatar Polaris-Avatar--sizeMedium Polaris-Avatar--hasImage"><img src="`+variantImage+`" class="Polaris-Avatar__Image" alt="" role="presentation"></span></div></div><div class="Polaris-ResourceItem__Content"><h3><span class="Polaris-TextStyle--variationStrong">`+variant_title+`</span></h3><div class="sd_prdPrice">`+variantItem['node']['price']+`</div><div class="sd_add_inventory">${available_stock+''+link_to_update_product_inventory}</div></div></div>`;
					add_product = 'Yes'
				}
			});
			if(variantsArray['pageInfo']['hasNextPage']){
				variants_view_more = `<div><button class="Polaris-Button Polaris-Button--plain variant_view_more" id="variant_view_more_${product_id}" type="button" data-product_id = "${product_id}" data-product_title = "${product_title}" data-variant_cursor = "${variantCursor}" data-variant_title = "${product_title}" data-product_image = "${productImage}">+more</button>
				<div id="PolarisPortalsContainer"></div></div>`;
			}
			link_to_update_product_inventory = '';
			variantHtml += variants_view_more;
			return variantHtml;
    }

	// get array of ten products using graphql
	async function add_newProducts(cursor,searchValue){
		add_shipping_address = 'No';
		var nextCursorId = 'No';
		let productsArray = [];
		let ajaxResult = '';
			let ajaxParameters = {
				method:"POST",
				dataValues:{
					action:"getAllProducts",
					cursor:cursor,
					query:searchValue
				}
			};
			try{
				ajaxResult = await AjaxCall(ajaxParameters);
			}catch(e){
				return false;
			}

		 productsArray = ajaxResult['allProductsArray']['data']['products']['edges'];
		 let allProductsHtml='',lastCursorId,checkboxValue,searchDiv,product_variant_id;
		 if(productsArray.length > 0){
		 cursor = ajaxResult['allProductsArray']['data']['products']['edges'][0]['cursor'];
			productsArray.forEach(function(item,keyq){
				add_product = 'No';
				total_selected_variants = 0;
				lastCursorId = item['cursor'];
				product_handle = item['node']['handle'];
				total_inventory = item['node']['totalInventory'];
				variant_id = item['node']['variants']['edges'][0]['node']['id'].replace( /[^\d.]/g, '' );
				// to remove products duplicate issue in product picker.
				if(searchValue != ''){
				    searchDiv = 'sd_searchList';
				}else{
					searchDiv = 'sd_newProductsList';
				}
				if(searchValue == ''){
					if(document.getElementById(searchDiv).contains(document.getElementById('productSelected_'+variant_id))){
						return  true;
					}
		     	}
                added_variant_count = 0;
				//let preselect_checkbox = '';
				checkboxValue = '';
				variantList = '';
				selected_count = '';
				let TotalVariants  = item['node']['variants']['edges'].length;
				console.log('TotalVariants =',TotalVariants);
				product_id = item['node']['id'].replace( /[^\d.]/g, '' );
					if(item['node']['featuredImage']){
						productImage = item['node']['featuredImage']['originalSrc'];
					}else{
						productImage = SHOPIFY_DOMAIN_URL+'/application/assets/images/no-image.png';
					}
					link_to_update_product_inventory = '';
					if(TotalVariants == 1){
						console.log('shipping address added1 =',shipping_address_added);
						if(shipping_address_added == 0 && item['node']['variants']['edges'][0]['node']['requiresShipping'] == true){
							add_shipping_address = 'Yes';
							console.log('add_shipping_address1',add_shipping_address);
						}
						if(AjaxCallFrom == 'frontendAjaxCall' && add_out_of_stock_product == '1'){
							add_product = 'Yes';
						}else{
								if(item['node']['variants']['edges'][0]['node']['inventoryQuantity'] > 0){
								add_product = 'Yes';
							}
						}
						show_variant_btn = ''; // if variant is default then show variant button will not visible
						if(Object.keys(picker_selection_checkboxes).length > 0){
							if (picker_selection_checkboxes.hasOwnProperty(variant_id)) {
								checkboxValue = 'checked';
							}
						}
						if(disabled_product_variant_array.includes(variant_id)){
							disableAddedProducts = 'sd_alreadyAdded';
							checkboxValue = 'disabled';
						}else{
							disableAddedProducts = '';
						}
						product_title = item['node']['title'].replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;");
						variant_title = item['node']['variants']['edges'][0]['node']['title'].replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;");
							productCheckbox =  '<label class="Polaris-Choice Polaris-Choice--labelHidden" for="PolarisResourceListItemCheckbox7"><span class="Polaris-Choice__Control"></span><span class="Polaris-Checkbox"><input id="productSelected_'+variant_id+'"  '+checkboxValue+' data-productImage = "'+productImage+'" data-productHandle = "'+product_handle+'" class="productSelected variantSelected productSelected_'+product_id+' '+disableAddedProducts+'"  name="sd_addNewProducts[]" data-productId="'+product_id+'" data-productTitle="'+product_title+'" data-subscriptionPrice="'+item['node']['variants']['edges'][0]['node']['price']+'" data-variantTitle="'+variant_title+'" data-totalVariants = "'+TotalVariants+'" value="'+variant_id+'" type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value=""></label>';
						productPrice = item['node']['variants']['edges'][0]['node']['price'];
						product_variant_id = variant_id;
					}else{
						show_variant_btn = `<button class="Polaris-Button Polaris-Button--plain show_all_variants" data-product_id = "${product_id}" type="button">
						<svg viewBox="0 0 20 20" width="25">
						   <path d="M10 14a.997.997 0 01-.707-.293l-5-5a.999.999 0 111.414-1.414L10 11.586l4.293-4.293a.999.999 0 111.414 1.414l-5 5A.997.997 0 0110 14z" fill="#5C5F62"></path>
						</svg>
					    </button>`;
						allSelectedVariants = "checked";
						product_disable = 'disabled';
						variantsArray = item['node']['variants'];
						product_id = item['node']['id'].replace( /[^\d.]/g, '' );
						product_title = item['node']['title'].replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;");
						variantList += getVariantHtml(variantsArray,product_title,product_id,productImage);
						product_selected = '';
						if(total_selected_variants > 0){
						    product_selected = 'checked';
						}
						if(document.getElementsByClassName('add_newProducts_customer')[0].getAttribute('parent-id') == ''){
							selected_text = 'added';
						}else{
							selected_text = 'selected';
						}
						if((added_variant_count > 0) && (TotalVariants == added_variant_count)){
							selected_count = 'All Variants '+selected_text;
						}else if((added_variant_count > 0) && (TotalVariants != added_variant_count)){
							selected_count = added_variant_count+' Variants '+selected_text;
						}
                        if(add_main_checkbox == 'Yes' || AjaxCallFrom == 'frontendAjaxCall'){
							productCheckbox = '<label class="Polaris-Choice Polaris-Choice--labelHidden" for="PolarisResourceListItemCheckbox7"><span class="Polaris-Choice__Control"></span><span class="Polaris-Checkbox"><input  data-productid="'+product_id+'" id="productSelected_'+product_id+'" '+allSelectedVariants+' class="productSelected sd_mainProduct productSelected_'+product_id+'" '+product_disable+' '+product_selected+' type="checkbox" class="Polaris-Checkbox__Input" aria-invalid="false" role="checkbox" aria-checked="false" value="'+product_id+'" data-totalVariants = "'+TotalVariants+'"></label>';
						}else{
							productCheckbox = '';
						}
						productPrice = '';
						product_variant_id = product_id;
					}
						if(AjaxCallFrom == 'backendAjaxCall'){
							if((item['node']['variants']['edges'][0]['node']['inventoryQuantity'] > 0) || (item['node']['variants']['edges'][0]['node']['inventoryPolicy'] == 'CONTINUE')){
							      available_stock = '';
					     	}else{
								available_stock = '<span class="sd_totalInventory sd_out_of_stock">Out Of Stock</span>';
							}
						}else{
							available_stock = '';
						}
						if(AjaxCallFrom == 'backendAjaxCall' || add_product == 'Yes'){
							allProductsHtml += '<li class="Polaris-ResourceItem__ListItem"><div class="Polaris-ResourceItem__ItemWrapper"><div class="Polaris-ResourceItem Polaris-ResourceItem--selectable"   id="sd_selectable_'+product_id+'" data-added_variantcount="'+added_variant_count+'"><div class="Polaris-ResourceItem__Container"><div class="Polaris-ResourceItem__Owned sd_click_checkbox" data-attr = "productSelected_'+product_variant_id+'"><div class="Polaris-ResourceItem__Handle  sd_click_checkbox" data-attr = "productSelected_'+product_variant_id+'"><div class="Polaris-ResourceItem__CheckboxWrapper  sd_click_checkbox" data-attr = "productSelected_'+product_variant_id+'"><div>'+productCheckbox+'</div></div></div><div class="Polaris-ResourceItem__Media"><span role="img" class="Polaris-Avatar Polaris-Avatar--sizeMedium Polaris-Avatar--hasImage"><img src="'+productImage+'" class="Polaris-Avatar__Image" alt="" role="presentation"></span></div></div><div class="Polaris-ResourceItem__Content"><h3><span class="Polaris-TextStyle--variationStrong show_all_variants" data-product_id = "'+product_id+'" type="button">'+item['node']['title']+'</span></h3><div></div><div class="sd_prdPrice">'+productPrice+'</div><div class="sd_add_inventory">'+available_stock+''+link_to_update_product_inventory+'</div><div id="all_variant_selected_'+product_id+'" class="sd_variant_selected">'+selected_count+'</div></div>'+show_variant_btn+'</div><div class="sd_variantDiv display-hide-label" id="sd_productVariant_'+product_id+'">'+variantList+'</div>';
							allProductsHtml += '</div></div></li>';
						}


			});
			if(ajaxResult['allProductsArray']['data']['products']['pageInfo']['hasNextPage']){
				nextCursorId = lastCursorId;
			}else{
				nextCursorId = 'No';
			}
		 }
		 var productArray = []; // Creating a new array object
		 productArray['productsList'] = allProductsHtml; // Setting the attribute a to 200
		 productArray['nextCursorId'] = nextCursorId;
		return  productArray;
	}

		function confirmBoxModal(usecase,usecaseid,heading,message,acceptbuttontext,acceptbuttontype,rejectbuttontext){
			var confirmBoxHtml = `
							<div class="Polaris-Modal-Header">
							  <div id="Polarismodal-header26" class="Polaris-Modal-Header__Title">
								<h2 class="Polaris-DisplayText Polaris-DisplayText--sizeSmall">`+heading+`</h2>
							  </div><button class="Polaris-Modal-CloseButton" aria-label="Close"><span class="Polaris-Icon Polaris-Icon--colorBase Polaris-Icon--applyColor"><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true" width="10px">
									<path d="m11.414 10 6.293-6.293a1 1 0 1 0-1.414-1.414L10 8.586 3.707 2.293a1 1 0 0 0-1.414 1.414L8.586 10l-6.293 6.293a1 1 0 1 0 1.414 1.414L10 11.414l6.293 6.293A.998.998 0 0 0 18 17a.999.999 0 0 0-.293-.707L11.414 10z"></path>
								  </svg></span></button>
							</div>
							<div class="Polaris-Modal__BodyWrapper">
							  <div class="Polaris-Modal__Body Polaris-Scrollable Polaris-Scrollable--vertical" data-polaris-scrollable="true">
								<section class="Polaris-Modal-Section">
								  <div class="Polaris-TextContainer">
									<p>`+message+`</p>
								  </div>
								</section>
							  </div>
							</div>
							<div class="Polaris-Modal-Footer">
							  <div class="Polaris-Modal-Footer__FooterContent">
								<div class="Polaris-Stack Polaris-Stack--alignmentCenter">
								  <div class="Polaris-Stack__Item Polaris-Stack__Item--fill"></div>
								  <div class="Polaris-Stack__Item">
									<div class="Polaris-ButtonGroup">
									  <div class="Polaris-ButtonGroup__Item"><button attr-type="false" attr-usecase="`+usecase+`" attr-id="`+usecaseid+`" class="sd_confirm_button Polaris-Button confirm" type="button">`+rejectbuttontext+`</button></div>
									  <div class="Polaris-ButtonGroup__Item"><button attr-type="true" attr-usecase="`+usecase+`" attr-id="`+usecaseid+`" class="sd_confirm_button Polaris-Button `+acceptbuttontype+`" type="button">`+acceptbuttontext+`</button></div>
									</div>
								  </div>
								</div>
							  </div>
							</div> `;
			document.getElementById('sd_popup').classList.remove('display-hide-label');
			return confirmBoxHtml;
		}

		document.addEventListener('change',async function(e){
			let className = e.target.className;
			let idName = e.target.id;
			let classNameArray = className.split(" ");
			if(classNameArray.includes('sd_select')){
				let discount_selected = e.target.options[e.target.selectedIndex].getAttribute('discount');
				let select_type = e.target.options[e.target.selectedIndex].getAttribute('attr-type');
				let quantity_price = e.target.options[e.target.selectedIndex].getAttribute('per-quantity-price');
				let cut_original_price = e.target.options[e.target.selectedIndex].getAttribute('per-original-price');
				let selected_selling_plan_id = e.target.options[e.target.selectedIndex].value;
				if(hidden_selling_plan_name == 'Yes'){
					get_all_selling_plan_name = document.body.querySelectorAll('form[action="/cart/add"] input[name="selling_plan"]');
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
					document.getElementById('ppd_discount').innerHTML = discount_selected;
					document.getElementById('ppd_discount_price').innerHTML = quantity_price;
					document.getElementById('ppd_cut_original_price').innerHTML = show_cut_original_price;

				}else{
					document.getElementById('prepaid_discount').innerHTML = discount_selected;
					document.getElementById('prepaid_discount_price').innerHTML = quantity_price;
					document.getElementById('prepaid_cut_original_price').innerHTML = show_cut_original_price;
				}
			}else if(idName == 'subscription_discount_status'){
               if(e.target.checked){
				   let remove_class_params = {
					class_elements:[
						{
							name:"sd_subscription_discount_offer_wrapper",
							classname:"display-hide-label"
						}
						// CHANGES MADE ON 10 MAY FOR HIDING THE DISCOUNT AFTER CYCLE FUNCTIONALITY
					]
				};
				remove_class(remove_class_params);
			   }else{
				   document.querySelector('#subscription_discount_value').value = ' ';
				  // subscription discount after cycle changes
				let add_class_params = {
					class_elements:[
						{
							name:"sd_subscription_discount_offer_wrapper",
							classname:"display-hide-label"
						}
					]
				};
				add_class(add_class_params);
			   }
			}
		});

		// close the popup when click outside to it
		window.addEventListener('mouseup',function(event){
			var sd_productPickerPanel = document.getElementById('sd_productPicker');
			if(event.target != sd_productPickerPanel && event.target.parentNode != sd_productPickerPanel){
			}else{
				document.getElementById('sd_productPicker').classList.add('display-hide-label');
			}

			var sd_productPickerPanel = document.getElementById('sd_contractSetting');
			if(event.target != sd_productPickerPanel && event.target.parentNode != sd_productPickerPanel){
			}else{
				document.getElementById('sd_contractSetting').classList.add('display-hide-label');
			}

	    });

	   //email template customization
		async function send_test_mail(template_type,send_to_email){ // new code added on 17 march
		    console.log(template_type);
			let ajaxParameters = {
				method:"POST",
				dataValues:{
					'send_to_email':send_to_email,
					'template_type':template_type,
					action:"send_test_email"
				}
			};
			try{
				let result = await AjaxCall(ajaxParameters);
				if(result['status'] == true){
					displayMessage(result['message'],'success');
				}else{
					displayMessage(result['message'],'error');
				}
				console.log(result);
			}catch(e){
				console.log(e);
			}
		}

	document.addEventListener('click',async function(e){
		let className = e.target.className;
		let idName = e.target.id;
		let classNameArray = className.split(" ");
	   if(classNameArray.includes('sd_confirm_button')){
		    e.target.classList.add('btn-disable-loader');
			let usecase = e.target.getAttribute('attr-usecase');
			let usecaseid = e.target.getAttribute('attr-id');
			let confirmed = e.target.getAttribute('attr-type');
			switch(usecase) {
				case 'open_shipping_address_tab':
					document.getElementById("sd_contractShippingAddress").click();
				break;
				case 'send_email_template':
					if(confirmed == 'true'){
						var template_type = usecaseid+'_template';
						var send_to_email = document.getElementById('send_test_email').value;
						if(send_to_email == ''){
							console.log('enter email');
							document.getElementById('test_email_error').innerText = 'Enter send to email';
							return false;
						}else{
							send_test_mail(template_type,send_to_email);
						}
						console.log('true');
					}else{
						console.log('false');
					}
				break;
			  case 'app_disable':
				if(confirmed == 'true'){
					changeAppStatus(0,"install");
				}else{
					document.getElementById("app_mode").checked = true;
				}
			  break;

			//   case 'contractStatusUpdate':
			// 	  if(confirmed == 'true'){
			// 		redirect_link_global = "memberPlans.php";
			// 		redirectlink();
			// 	  }

			  case 'subscription_form_leave':
				// code block
				if(confirmed == 'true'){
					list_subscription_mode();
					reset_plan_name_change();
					remove_all_error_messages();
				}
				break;

			 case 'subscription_plan_delete':
				// code block
				if(confirmed == 'true'){
					deleteSubscriptionPlan(usecaseid,"subscriptionPlanGroups");
				}else{
					var buttons = document.querySelectorAll('.delete_subscription_plan');
					for (var i = 0; i < buttons.length; i++) {
					  var button = buttons[i];
					  if (button.getAttribute('subscription-group-id') === usecaseid) {
						var row = button.closest('tr'); // Get the closest <tr> element
						if (row) {
						  row.classList.remove('display-hide-label'); // Replace with the class you want to remove
						}
					  }
					}
				}
				break;

				case 'sd_reset_template':
                if(confirmed == 'true'){
					let whereconditionvalues = {
						"store_id": store_id
					}
					let ajaxParameters = {
						method: "POST",
						dataValues: {
							table: usecaseid,
							wherecondition: whereconditionvalues,
							wheremode : 'and',
							action: "reset_email_template"
						}
					};
					try {
						let result = await AjaxCall(ajaxParameters);
						if (result['status'] == true) {
							displayMessage(result['message'], 'success');
							location.reload();
						}else{
							displayMessage(result['message'], 'error');
						}
					} catch (e) {
						displayMessage(e, 'error');
					}
				}

				case 'contract_order_skip':
					if(confirmed == 'true'){
						displayMessage('Updating','waiting');
						document.getElementById('sd_subscriptionLoader').classList.remove('display-hide-label');
                        document.getElementsByClassName('sd_billingAttempt')[0].innerHTML = '';
						var contract_id = document.getElementById('sd_contractId').value;
						skip_order_data = usecaseid.split('_');
					   skipContractOrder(skip_order_data[0],skip_order_data[1],skip_order_data[2],contract_id);
					}
				break;

		    	case 'delete_subscription_contract':
				if(confirmed == 'true'){
					document.getElementById('sd_subscriptionLoader').classList.remove('display-hide-label');
					let statusUpdateArray = usecaseid.split("#");
					console.log('usecaseid', usecaseid);
					activePauseCancelSubscription(statusUpdateArray);

				}
				break;

				case 'update_contract_products':
					if(confirmed == 'true'){
						document.getElementById('sd_subscriptionLoader').classList.remove('display-hide-label');
						displayMessage('Product Adding','waiting');
						let adminEmail = document.getElementById('sendMailToAdmin').value;
	                 	let customerEmail = document.getElementById('sendMailToCustomer').value;
						let contract_id = document.getElementById("sd_contractId").value;
							let ajaxParameters = {
								method:"POST",
								dataValues:{
									'contract_id':contract_id,
									'newProductsArray':picker_selection_checkboxes,
									'AjaxCallFrom':AjaxCallFrom,
									'adminEmail':adminEmail,
									'customerEmail':customerEmail,
									'specific_contract_data':specific_contract_data,
								    'contract_product_details':contract_existing_products,
									action:"newAddedProducts"
								}
							};
							try{
								let result = await AjaxCall(ajaxParameters);
								document.getElementById('sd_subscriptionLoader').classList.add('display-hide-label');
								if(result['status'] == true){
									displayMessage(result['message'],'success');
									location.reload();
								}else{
									displayMessage(result['message'],'error');
									location.reload();
								}
							}catch(e){
								document.getElementById('sd_subscriptionLoader').classList.add('display-hide-label');
								displayMessage(e,'error');
							}
					}
				break;
                case 'sd_attempt_billing':
					if(confirmed == 'true'){
						document.getElementById('sd_subscriptionLoader').classList.remove('display-hide-label');
						displayMessage('Updating','waiting');
						let billingAttemptArray = usecaseid.split("_");
							let ajaxParameters = {
								method:"POST",
								dataValues:{
									"actualAttemptDate" : billingAttemptArray[0],
									"contract_id":billingAttemptArray[1],
									"billingPolicy" : billingAttemptArray[2],
									"adminEmail" : billingAttemptArray[3],
									"customerEmail" :billingAttemptArray[4],
									action:"manualBillingAttempt"
								}
							};
							try{
								let result = await AjaxCall(ajaxParameters);
								if(result['status'] == true){
									document.getElementsByClassName('sd_attemptBillingAndSkip')[0].innerHTML = '';
									setInterval(function(){
										displayMessage(result['message'],'success');
										location.reload();
								    }, 10000);
								}else{
									displayMessage(result['message'],'error');
									location.reload();
								}
							}catch(e){
								document.getElementById('sd_subscriptionLoader').classList.add('display-hide-label');
								displayMessage(e,'error');
							}
						// }
					}
					break;
				case 'delete_subscription_product':
					if(confirmed == 'true'){
					document.getElementById('sd_subscriptionLoader').classList.remove('display-hide-label');
					displayMessage('Deleting','waiting');
				   //   remove products from the contract
				   let productDataArray = usecaseid.split("_");
					var product_id = productDataArray[0];
					var line_id = productDataArray[1];
					var contract_id = document.getElementById("sd_contractId").value;
					var adminEmail = document.getElementById('sendMailToAdmin').value;
					var customerEmail = document.getElementById('sendMailToCustomer').value;
						if(AjaxCallFrom == 'backendAjaxCall'){
							var deletd_by = 'Admin';
						}else{
							var deletd_by = 'Customer';
						}
							let ajaxParameters = {
								method:"POST",
								dataValues:{
									"product_id":product_id,
									"contract_id":contract_id,
									"line_id" : line_id,
									'deleted_by' : deletd_by,
									'adminEmail' : adminEmail,
									'customerEmail' : customerEmail,
									'specific_contract_data':specific_contract_data,
								    'contract_product_details':contract_existing_products,
									action:"removeContractProduct"
								}
							};
							try{
								let result = await AjaxCall(ajaxParameters);
								document.getElementById('sd_subscriptionLoader').classList.add('display-hide-label');
								if(result['status'] == true){
									displayMessage(result['message'],'success');
									location.reload();
								}else{
									displayMessage(result['message'],'error');
									location.reload();
								}
							}catch(e){
								document.getElementById('sd_subscriptionLoader').classList.add('display-hide-label');
							}
					}
				break;
				default:
				// code block
			}
			close_sd_popup();
			}else if(classNameArray.includes('sd_click_checkbox')){
			   let checkbox_id =  e.target.getAttribute('data-attr');
			   document.getElementById(checkbox_id).click();
			}else if(classNameArray.includes('hide_all_variants')){
				let sd_parentDivId = e.target.closest('ul').id;
				 e.target.classList.remove('hide_all_variants');
				 var product_id = e.target.getAttribute('data-product_id');
				document.querySelector('#'+sd_parentDivId +' #sd_productVariant_'+product_id).classList.add('display-hide-label');
				document.querySelector('#'+sd_parentDivId +' #sd_productVariant_'+product_id).classList.remove('sd_variant_open');
			}else if(classNameArray.includes('show_all_variants')){
				document.getElementById('sd_productLoader').classList.remove('display-hide-label');
				let sd_parentDivId = e.target.closest('ul').id;
	            e.target.classList.add('hide_all_variants');
			    var product_id = e.target.getAttribute('data-product_id');
			    document.querySelector('#'+sd_parentDivId +' #sd_productVariant_'+product_id).classList.remove('display-hide-label');
			    document.querySelector('#'+sd_parentDivId +' #sd_productVariant_'+product_id).classList.add('sd_variant_open');
			    var view_more_btn_exist = document.getElementById('variant_view_more_'+product_id); //check if variant view more button exist or not
				if(view_more_btn_exist)
				{
					view_more_btn_exist.click();
				}else{
					document.getElementById('sd_productLoader').classList.add('display-hide-label');
				}
			}else if(classNameArray.includes('variant_view_more')){
				document.getElementById('sd_productLoader').classList.remove('display-hide-label');
				let search_value = document.getElementById('sd_searchProduct').value;
				// e.target.remove();
				variant_spinner = `<span class="Polaris-Spinner Polaris-Spinner--sizeSmall"><svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
				<path d="M7.229 1.173a9.25 9.25 0 1011.655 11.412 1.25 1.25 0 10-2.4-.698 6.75 6.75 0 11-8.506-8.329 1.25 1.25 0 10-.75-2.385z"></path>
				</svg></span><span role="status"><span class="Polaris-VisuallyHidden">Small spinner example</span></span>`;
				e.target.innerHTML = variant_spinner;
			   var sd_variant_cursor = e.target.getAttribute('data-variant_cursor');
			   var product_id = e.target.getAttribute('data-product_id');
			   var product_title = e.target.getAttribute('data-product_title').replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;");
			   var product_image = e.target.getAttribute('data-product_image');
					let ajaxParameters = {
						method:"POST",
						dataValues:{
							action:"getProductVariant",
							product_id : product_id,
							cursor:sd_variant_cursor,
						}
					};
					try{
						ajaxResult = await AjaxCall(ajaxParameters);
					}catch(e){
						return false;
					}
				variantsArray = ajaxResult['allVariantsArray']['data']['product']['variants'];
				nextVariantList = getVariantHtml(variantsArray,product_title,product_id,product_image);
				if(search_value.length > 0 ){
					product_variant_id =  document.querySelector('#sd_searchList #sd_productVariant_'+product_id);
					if(document.querySelector('#sd_searchList #variant_view_more_'+product_id)){
					   document.querySelector('#sd_searchList #variant_view_more_'+product_id).remove();
					}
				}else{
					product_variant_id =  document.querySelector('#sd_newProductsList #sd_productVariant_'+product_id);
					if(document.querySelector('#sd_newProductsList #variant_view_more_'+product_id)){
					  document.querySelector('#sd_newProductsList #variant_view_more_'+product_id).remove();
					}
				}
				product_variant_id.insertAdjacentHTML('beforeend', nextVariantList);
				document.getElementById('sd_productLoader').classList.add('display-hide-label');
			}else if(classNameArray.includes('close_product_picker')){
				close_product_picker();
			}else if(classNameArray.includes('sd_saveProducts')){
				setTimeout(function() {
			     sd_saveProducts();
			    }, 500);
			}else if(classNameArray.includes('updatePaymentMethod')){
				e.target.classList.add('btn-disable-loader');
				updatePaymentMethod();
				e.target.classList.remove('btn-disable-loader');
			}else if(classNameArray.includes('Polaris-Frame-Toast__CloseButton')){
              close_toast();
			}else if(classNameArray.includes('sd_updateQuantity')){
				this_variantId = e.target.getAttribute('data-id');
				input_value = document.getElementById('product_qty_'+this_variantId).value;
                if(classNameArray.includes('sd_qtyPlus')){
					input_value++;
				}else if(classNameArray.includes('sd_qtyMinus')){
					if(input_value > 1){
					  input_value--;
					}else{
						input_value = 1;
					}
				}
				document.getElementById('product_qty_'+this_variantId).value = input_value;
			}else if(classNameArray.includes('open_mini_action')){
				var subscription_group_id = e.target.getAttribute('subscription-group-id');
				var page_type = e.target.getAttribute('page-type');
				if(document.getElementById('mini_'+subscription_group_id).classList.contains('display-hide-label')){
				    list_close_all_mini();
					if(page_type == 'subscription_plan'){
					   reset_plan_name_change();
					}
					document.getElementById('mini_'+subscription_group_id).classList.remove('display-hide-label');
				}else{
					document.getElementById('mini_'+subscription_group_id).classList.add('display-hide-label');
				}
			}else if(classNameArray.includes('sd_slider_controls')){ //product slider on contract page js start
				var current_slide_number = document.getElementById("sd_slider_1").getElementsByClassName('sd_active_slider')[0].getAttribute('sd-slide-number');
				var total_slides = document.querySelectorAll('#sd_slider_1 .sd_slide_wrapper').length;
				let remove_class_params = {
					class_elements:[
						{
							name:"sd_slide_wrapper",
							classname:"sd_active_slider"
						}
					]
				};
				remove_class(remove_class_params);
				//find the next & prev Case
				check_number_div = document.getElementById('sd_sliderNumber');
				var control_type =  e.target.getAttribute('attr-type');
				if(control_type == 'next'){
					next_slide_number = parseInt(current_slide_number)+1;
					if(next_slide_number > total_slides){
						next_slide_number = '1';
					}
					if(check_number_div){
					 document.getElementById('sd_sliderNumber').innerHTML = next_slide_number;
					}
					document.querySelector("#sd_slider_1 [sd-slide-number='"+next_slide_number+"']").classList.add('sd_active_slider');
				}
				if(control_type == 'prev'){
					var prev_slide_number = parseInt(current_slide_number)-1;
					if(prev_slide_number < 1){
						prev_slide_number = total_slides;
					}
					if(check_number_div){
					document.getElementById('sd_sliderNumber').innerHTML = prev_slide_number;
					}
					document.querySelector("#sd_slider_1 [sd-slide-number='"+prev_slide_number+"']").classList.add('sd_active_slider');
				}
			}else if(classNameArray.includes('sd_page_slider_controls')){
				var current_slide_number = document.getElementById("sd_page_slider_1").getElementsByClassName('sd_active_page_slider')[0].getAttribute('sd-slide-number');
				var total_slides = document.querySelectorAll('#sd_page_slider_1 .sd_table_wrapper').length;
				let add_class_params = {
					class_elements:[
						{
							name:"sd_table_wrapper",
							classname:"sd_hide_slider"
						}
					]
				};
				add_class(add_class_params);
				let remove_class_params = {
					class_elements:[
						{
							name:"sd_table_wrapper",
							classname:"sd_active_page_slider"
						}
					]
				};

				remove_class(remove_class_params);
				check_number_div = document.getElementById('sd_page_sliderNumber');
				var control_type =  e.target.getAttribute('attr-type');
				if(control_type == 'next'){
					next_slide_number = parseInt(current_slide_number)+1;
					if(next_slide_number > total_slides){
						next_slide_number = '1';
					}
					if(check_number_div){
					 document.getElementById('sd_page_sliderNumber').innerHTML = next_slide_number;
					}
					document.querySelector("#sd_page_slider_1 [sd-slide-number='"+next_slide_number+"']").classList.remove('sd_hide_slider');
					document.querySelector("#sd_page_slider_1 [sd-slide-number='"+next_slide_number+"']").classList.add('sd_active_page_slider');
				}
				if(control_type == 'prev'){
					var prev_slide_number = parseInt(current_slide_number)-1;
					if(prev_slide_number < 1){
						prev_slide_number = total_slides;
					}
					if(check_number_div){
					document.getElementById('sd_page_sliderNumber').innerHTML = prev_slide_number;
					}
					document.querySelector("#sd_page_slider_1 [sd-slide-number='"+prev_slide_number+"']").classList.remove('sd_hide_slider');
					document.querySelector("#sd_page_slider_1 [sd-slide-number='"+prev_slide_number+"']").classList.add('sd_active_page_slider');
				}
			}
			else if(classNameArray.includes('sd_cancelShippingAddress')){
				let remove_class_params = {
					class_elements:[
						{
							name : 'sd_shippingAndBilling',
							classname : 'sd_updateShipping'
						}
					]
				}
				remove_class(remove_class_params);
				hideUpdateShipping("sd_shippingForm","sd_shippingSaveCancel","sd_shippingAndBilling");
				showUpdateShipping("sd_shippingEditBtn","sd_shippingAddressData","sd_shippingAndBilling");
				setCountryState();
				getStates();
			}else if(classNameArray.includes('sd_updateShippingAddress')){
				document.getElementById("sd_subscriptionLoader").classList.remove('display-hide-label');
				displayMessage('Please Wait...','waiting');
				e.target.classList.add('btn-disable-loader');
				let ajaxParameters = {
					method:"POST",
					dataValues:{
						action:"get_shipping_counteries"
					}
				};
				try{
					 result = await AjaxCall(ajaxParameters);
					 if(result['status'] == true){
						var j = result['shipping_counteries'];
					if(j.includes('Rest of World')){
						var j = new Array("Select Country","Afghanistan","Aland Islands","Albania","Algeria","Andorra","Angola","Anguilla","Antigua And Barbuda","Argentina","Armenia","Aruba","Ascension Island","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bermuda","Bhutan","Bolivia","Bosnia And Herzegovina","Botswana","Bouvet Island","Brazil","British Indian Ocean Territory","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Caribbean Netherlands","Cayman Islands","Central African Republic","Chad","Chile","China","Christmas Island","Cocos (Keeling) Islands","Colombia","Comoros","Congo","Congo, The Democratic Republic Of The","Cook Islands","Costa Rica","Croatia","Cuba","Curaçao","Cyprus","Czech Republic","Côte d'Ivoire","Denmark","Djibouti","Dominica","Dominican Republic","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Eswatini","Ethiopia","Falkland Islands","Faroe Islands","Fiji","Finland","France","French Guiana","French Polynesia","French Southern Territories","Gabon","Gambia","Georgia","Germany","Ghana","Gibraltar","Greece","Greenland","Grenada","Guadeloupe","Guatemala","Guernsey","Guinea","Guinea Bissau","Guyana","Haiti","Hong Kong SAR","Hungary","Iceland","India","Indonesia","Iraq","Ireland","Isle Of Man","Israel","Italy","Jamaica","Japan","Jersey","Jordan","Kazakhstan","Kenya","Kiribati","Kosovo","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macao SAR","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Martinique","Mauritania","Mauritius","Mayotte","Mexico","Moldova","Monaco","Mongolia","Montenegro","Montserrat","Morocco","Mozambique","Myanmar (Burma)","Namibia","Nauru","Nepal","Netherlands","Netherlands Antilles","New Caledonia","New Zealand","Nicaragua","Niger","Nigeria","Niue","Norfolk Island","North Macedonia","Norway","Oman","Pakistan","Palestinian Territories","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Pitcairn Islands","Poland","Portugal","Qatar","Reunion","Romania","Russia","Rwanda","Samoa","San Marino","Sao Tome And Principe","Saudi Arabia","Senegal","Serbia","Seychelles","Sierra Leone","Singapore","Sint Maarten","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","South Georgia And The South Sandwich Islands","South Korea","South Sudan","Spain","Sri Lanka","St. Barthélemy","St. Helena","St. Kitts &; Nevis","St. Lucia","St. Martin","St. Pierre & Miquelon","St. Vincent & Grenadines","Sudan","Suriname","Svalbard And Jan Mayen","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania, United Republic Of","Thailand","Timor Leste","Togo","Tokelau","Tonga","Trinidad and Tobago","Tristan da Cunha","Tunisia","Turkey","Turkmenistan","Turks and Caicos Islands","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","United States Minor Outlying Islands","Uruguay","Uzbekistan","Vanuatu","Venezuela","Vietnam","Wallis And Futuna","Western Sahara","Yemen","Zambia","Zimbabwe");
						}
							var options = '';
							let selectedCountry = document.getElementById("shipCountry").getAttribute("data-country");
							for (var i = 0; i < j.length; i++) {
								if(j[i] == selectedCountry){
								options += '<option value="' + j[i]+ '" selected>' + j[i] + '</option>';
								}else{
									options += '<option value="' + j[i]+ '">' + j[i] + '</option>';
								}
							}
							document.getElementById("shipCountry").innerHTML = options;
							getStates();
							let add_class_params = {
								class_elements:[
									{
										name : 'sd_shippingAndBilling',
										classname : 'sd_updateShipping'
									}
								]
							}
							add_class(add_class_params);
							showUpdateShipping("sd_shippingForm","sd_shippingSaveCancel","sd_shippingAndBilling");
							hideUpdateShipping("sd_shippingEditBtn","sd_shippingAddressData","sd_shippingAndBilling");
							e.target.classList.remove('btn-disable-loader');
							hide_toast_message();
							document.getElementById("sd_subscriptionLoader").classList.add('display-hide-label');
					}else{
						e.target.classList.remove('btn-disable-loader');
						displayMessage(result['message'],'error');
					}
				}catch(e){
					displayMessage(e,'error');
				}
				// getStates();
			}else if(classNameArray.includes('Polaris-Modal-CloseButton')){
				close_sd_popup();
			}else if(classNameArray.includes('sd_saveShippingAddress')){
				document.getElementById("sd_subscriptionLoader").classList.remove('display-hide-label');
				e.target.classList.add('btn-disable-loader');
					var shippingAddressFormElements_array = document.querySelectorAll('#shippingAddressSave input[type="text"]');
					var add_status = true;
					 var check_form_validation = await shipping_address_validation(shippingAddressFormElements_array);
					if(check_form_validation == true){
						displayMessage('Updating','waiting');
						let updateShippingData = form_serializeObject("shippingAddressSave");
						specific_contract_data[0].shipping_first_name = updateShippingData.first_name;
						specific_contract_data[0].shipping_last_name = updateShippingData.last_name;
						specific_contract_data[0].shipping_phone = updateShippingData.phone;
						specific_contract_data[0].shipping_province = updateShippingData.province;
						specific_contract_data[0].shipping_province_code = updateShippingData.province_code;
						specific_contract_data[0].shipping_zip = updateShippingData.zip;
						specific_contract_data[0].shipping_country = updateShippingData.country;
						specific_contract_data[0].shipping_company = updateShippingData.company;
						specific_contract_data[0].shipping_city = updateShippingData.city;
						specific_contract_data[0].shipping_address1 = updateShippingData.address1;
						specific_contract_data[0].shipping_address2 = updateShippingData.address2;
						specific_contract_data[0].shipping_delivery_price = updateShippingData.delivery_price;

						let contract_id = e.target.getAttribute('data-contract_id');
							let wheremodevalue = "and";
							let ajaxParameters = {
								method:"POST",
								dataValues:{
									action:"updateShippingAddress",
									"store_id":store_id,
									"contract_id":contract_id,
									"ajaxCallFrom":AjaxCallFrom,
									"shippingDataValues":updateShippingData,
									'specific_contract_data':specific_contract_data,
									'contract_product_details':contract_existing_products
								}
							};
							try{
								let result = await AjaxCall(ajaxParameters);
								document.getElementById("sd_subscriptionLoader").classList.add('display-hide-label');
								if(result['status'] == true){
									let shipping_address_content = '<p class="sd_shippingName">'+updateShippingData.first_name+' '+updateShippingData.last_name+'</p><p class="sd_shippingAddress">'+updateShippingData.address1+'</p><p class="sd_shippingCity">'+updateShippingData.country+' '+updateShippingData.city+' '+updateShippingData.province+'-'+updateShippingData.zip+'</p>';
									document.getElementById('sd_shippingAddressData').innerHTML = shipping_address_content;
									displayMessage(result['message'],'success');
									document.querySelector('.sd_cancelShippingAddress').click();
									e.target.classList.remove('btn-disable-loader');
									document.getElementById('shipCountry').setAttribute('data-country',updateShippingData.country);
									document.getElementById('shipProvince').setAttribute('data-province',updateShippingData.province);
									shipping_address_added = 1;
								}else{
									if(result['message'] == 'Delivery method shipping address zip is invalid'){
										displayMessage('Postal Code is invalid','error');
									}else{
										displayMessage(result['message'],'error');
									}
									e.target.classList.remove('btn-disable-loader');
								}
							}catch(e){
								displayMessage(e,'error');
								e.target.classList.remove('btn-disable-loader');
							}
					}else{
						document.getElementById("sd_subscriptionLoader").classList.add('display-hide-label');
						e.target.classList.remove('btn-disable-loader');
					}
			}else if(classNameArray.includes('add_newProducts_customer')){
				add_out_of_stock_product = e.target.getAttribute('data-out_of_stock');
				picker_display_wrapper = e.target.getAttribute('parent-id');
				picker_display_style = e.target.getAttribute('product-display-style');
				await add_newProductClick();
				// change add product text to save text of product picker
				if(form_type == 'edit'){
					document.getElementById('sd_saveProducts').innerHTML = 'Save';
				}else{
					document.getElementById('sd_saveProducts').innerHTML = 'Add Product';
				}
				let totalProductList = document.querySelectorAll('#sd_allProducts ul li').length;
				let nextcursorId = document.getElementById("sd_viewNextProducts").value;
				while(totalProductList < 10 && nextcursorId !='No'){
					await add_newProductClick();
					totalProductList = document.querySelectorAll('#sd_allProducts ul li').length;
				}
			}else if(classNameArray.includes('add_newProducts')){
				picker_display_wrapper = e.target.getAttribute('parent-id');
				console.log('picker_display_wrapper',picker_display_wrapper);
				console.log(typeof already_added_products);
				picker_display_style = e.target.getAttribute('product-display-style');
				if(typeof already_added_products !== 'undefined' && picker_display_wrapper != 'create-section-products-show'){
					console.log('pre_selected_products is already added');
					pre_selected_products = {};
					pre_selected_products = already_added_products;
				}else{
					console.log('pre_selected_products is already added plan added page');
				}
				allProductsHtml = '';
				html_tag = '';
				productWithVariantsSelected = [];
				proids = [];
				console.log('pre_selected_products length', Object.keys(pre_selected_products).length);
				if(Object.keys(pre_selected_products).length > 0){
					if(picker_display_wrapper != 'create-section-products-show'){
					    already_added_products = pre_selected_products;
					}
					$.each(pre_selected_products,function(pop_key, pop_value) {
						selected_variant_ids_array = [];
						jQuery.each(pop_value, function(vpop_key, vpop_value) {
							var variant_key = {
								id:  vpop_value,
							};
							selected_variant_ids_array.push(variant_key);
						});
						productWithVariantsSelected = {
							id: pop_key,
							variants: selected_variant_ids_array
						};
						proids.push(productWithVariantsSelected);
					});
				}
				const products_selection = await shopify.resourcePicker({type: 'product', multiple:true, selectionIds:proids});
				if (products_selection) {
					pre_selected_products = {};
					(products_selection.selection).forEach(function(item,keyq){
						pre_selected_products[item.id] = [];
						console.log('Product array value = ', item);
						item['variants'].forEach(function(varItem,keyv){
						    console.log('variant array value = ', varItem);
							if(shipping_address_added == 0 && varItem.requiresShipping == true){
								add_shipping_address = 'Yes';
								console.log('add_shipping_address1',add_shipping_address);
							}
							pre_selected_products[item.id].push(varItem.id);
							product_id = item['id'].replace( /[^\d.]/g, '' );
							product_variant_id = varItem['id'].replace( /[^\d.]/g, '' );
							productCheckbox = '';
							if(typeof varItem['image'] !== 'undefined'){
								productImage = varItem['image']['originalSrc'];
							}else if(item['images'].length > 0){
								productImage = item['images'][0]['originalSrc'];
							}else{
								productImage = SHOPIFY_DOMAIN_URL+'/application/assets/images/no-image.png';
							}
							link_to_update_product_inventory = '';
							html_tag += `<li class="Polaris-ResourceItem__ListItem" id="display_product_`+product_variant_id.trim()+`"><div class="Polaris-ResourceItem__ItemWrapper"><div class="Polaris-ResourceItem Polaris-ResourceItem--selectable"><div class="Polaris-ResourceItem__Container"><div class="Polaris-ResourceItem__Owned"><div class="Polaris-ResourceItem__Media"><span role="img" class="Polaris-Avatar Polaris-Avatar--sizeMedium Polaris-Avatar--hasImage"><img src="${productImage}" class="Polaris-Avatar__Image" alt="" role="presentation"></span></div></div><div class="Polaris-ResourceItem__Content"><h3><span class="Polaris-TextStyle--variationStrong"> `+item['title']+' '+varItem['title']+`</span></h3><div></div></div></div></div><button type="button" variant-id='`+product_variant_id+`' product-id='`+product_id+`' class="Polaris-Tag__Button display_remove_variant"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="m11.414 10 4.293-4.293a.999.999 0 1 0-1.414-1.414L10 8.586 5.707 4.293a.999.999 0 1 0-1.414 1.414L8.586 10l-4.293 4.293a.999.999 0 1 0 1.414 1.414L10 11.414l4.293 4.293a.997.997 0 0 0 1.414 0 .999.999 0 0 0 0-1.414L11.414 10z"></path></svg></span></div></li>`;

							// if(picker_display_style == 'prdouct-box'){
								if(typeof already_added_products !== 'undefined'){
									if (already_added_products.hasOwnProperty(item.id)) { // check if the selected product id already
										if ((already_added_products[item.id]).includes(varItem['id'])) {
										} else {
											var product_obj = {
												variant_id : product_variant_id,
												product_id : product_id,
												product_title : item['title'],
												variant_title : varItem['title'],
												product_handle : item['handle'],
												image : productImage,
												price : varItem['price'],
												quantity : 1,
											};
											picker_selection_checkboxes[product_variant_id] = product_obj;
										}
									} else {
										var product_obj = {
											variant_id : product_variant_id,
											product_id : product_id,
											product_title : item['title'],
											variant_title : varItem['title'],
											product_handle : item['handle'],
											image : productImage,
											price : varItem['price'],
											quantity : 1,
										};
										picker_selection_checkboxes[product_variant_id] = product_obj;
									}
								}else{
									var product_obj = {
										variant_id : product_variant_id,
										product_id : product_id,
										product_title : item['title'],
										variant_title : varItem['title'],
										product_handle : item['handle'],
										image : productImage,
										price : varItem['price'],
										quantity : 1,
									};
									picker_selection_checkboxes[product_variant_id] = product_obj;
								}
							// }
							// compare two arrays end

						});
					});
					if(Object.keys(picker_selection_checkboxes).length > 0){
						Object.entries(picker_selection_checkboxes).forEach(([key, value]) => {
							if (pre_selected_products.hasOwnProperty("gid://shopify/Product/"+value.product_id)) {
								if (pre_selected_products["gid://shopify/Product/"+value.product_id].includes("gid://shopify/ProductVariant/"+key)) {
								} else {
									delete picker_selection_checkboxes[key];
								}
							} else {
								delete picker_selection_checkboxes[key];
							}
						});
					}

					if(picker_display_wrapper == 'subscription_edit_prodcts'){
					   already_added_products = pre_selected_products;
					}
					if(picker_display_style == 'prdouct-box'){
                   		if(add_shipping_address == 'Yes'){
							content_html = `In order to include a physical product, it is necessary to provide the shipping address within the subscription, which is currently absent. Kindly ensure that the shipping address is added to this subscription for seamless processing.`;
							document.getElementById("sd_global_modal_container").innerHTML = confirmBoxModal("open_shipping_address_tab",'','Add Shipping Address',content_html,'Add Shipping Address',"Polaris-Button--primary","Cancel");
						}else{
							content_html = `<div>
							<h2 class="Polaris-Heading">Are you sure you want to add the Products in the subscription ? This will immediately update your subscription</h2></div>`;
							document.getElementById("sd_global_modal_container").innerHTML = confirmBoxModal("update_contract_products",'','Add Products in the Subscription',content_html,'Add',"Polaris-Button--primary","Cancel");
						}
					}else{
						console.log('picker display box');
						document.getElementById(picker_display_wrapper).innerHTML = html_tag;
						if(picker_display_wrapper == 'subscription_edit_prodcts'){
							document.querySelector('#subscription_edit_products .show_selected_products').classList.remove('display-hide-label');
						}
						let show_save_bar = check_subscription_whole_form_change();
						if(show_save_bar){
							show_save_banner();
						}else{
							hide_save_banner();
						}
						document.querySelector('.subscription-create-step1 .show_selected_products').classList.remove('display-hide-label');
					}
				}
			}else if(classNameArray.includes('sd_skipFulfillment')){
				e.target.classList.add('btn-disable-loader');
				displayMessage('Updating','waiting');
				var fulfillmentOrderId = e.target.getAttribute('data-fulfillOrderId');
				// var actualFulfillmentDate = e.target.getAttribute('data-actualOrderDate');
				var actualFulfillmentDate = document.querySelectorAll(".sd_skipFulfillment")[(document.querySelectorAll(".sd_skipFulfillment")).length -1].getAttribute('data-actualorderdate');
				var contract_id = document.getElementById("sd_contractId").value;
				var order_id =e.target.getAttribute('data-orderId');
				var order_no = e.target.getAttribute('data-orderName');
				var billingCycle = e.target.getAttribute('data-billingCycle');
				var deliveryCycle = e.target.getAttribute('data-deliveryCycle');
				var delivery_billing_type = e.target.getAttribute('data-delivery_billing_type');
				var adminEmail = document.getElementById('sendMailToAdmin').value;
				var customerEmail = document.getElementById('sendMailToCustomer').value;
				if(AjaxCallFrom == 'backendAjaxCall'){
					var scheduled_by = 'Admin';
				}else{
					var scheduled_by = 'Customer';
				}
					let ajaxParameters = {
						method:"POST",
						dataValues:{
							actualFulfillmentDate :actualFulfillmentDate,
							fulfillmentOrderId:fulfillmentOrderId,
							contract_id:contract_id,
							order_id:order_id,
							order_no:order_no,
							deliveryCycle : deliveryCycle,
							billingCycle : billingCycle,
							delivery_billing_type : delivery_billing_type,
							rescheduled_by : scheduled_by,
							customerEmail : customerEmail,
							adminEmail : adminEmail,
							'specific_contract_data':specific_contract_data,
							'contract_product_details':contract_existing_products,
							action:"rescheduleOrder"
						}
					};
					try{
						let result = await AjaxCall(ajaxParameters);
						if(result['status'] == true){
							displayMessage(result['message'],'success');
							location.reload();
						}else{
							displayMessage(result['message'],'error');
							location.reload();
						}
					}catch(e){
						displayMessage(e,'error');
						e.target.classList.remove('btn-disable-loader');
					}
			}else if(classNameArray.includes('cancel_prdUpdate')){
				var productId = e.target.getAttribute('data-product_id');
				cancel_product_update(productId);
		}else if(classNameArray.includes('update_prdQuantity')){
			var productId = e.target.getAttribute('data-product_id');
			let remove_class_params = {
				class_elements:[
					{
						name:"updateCancelBtn_"+productId,
						classname:"display-hide-label"
					},{
						name:"sd_newQuantity_"+productId,
						classname:"display-hide-label"
					}
				]
			};
			remove_class(remove_class_params);
			let add_class_params = {
				class_elements:[
					{
						name:"sd_updateProductButton_"+productId,
						classname:"display-hide-label"
					},{
						name:"sd_oldQuantity_"+productId,
						classname:"display-hide-label"
					}
				]
			};
		   add_class(add_class_params);
		}else if(classNameArray.includes('update_prdPrice')){
			var productId = e.target.getAttribute('data-product_id');
			console.log("sd_newPrice_"+productId);
			let remove_class_params = {
				class_elements:[
					{
						name:"updateCancelBtn_"+productId,
						classname:"display-hide-label"
					},{
						name: "sd_newPrice_"+productId,
						classname: "display-hide-label"
					}
				]
			};
			remove_class(remove_class_params);
			let add_class_params = {
				class_elements:[
					{
						name:"sd_updateProductButton_"+productId,
						classname:"display-hide-label"
					},{
						name: "sd_oldPrice_"+productId,
						classname: "display-hide-label"
					}
				]
			};
		   add_class(add_class_params);
		}else if(classNameArray.includes('update_prdQtyPrice')){
				// var data_to_update = e.target.getAttribute('data-update');
				// console.log(data_to_update);
				var adminEmail = document.getElementById('sendMailToAdmin').value;
				 var customerEmail = document.getElementById('sendMailToCustomer').value;
				var product_id = e.target.getAttribute('data-product_id');
				var contract_id = document.getElementById("sd_contractId").value;
				var prd_qty = document.getElementById("product_qty_"+product_id).value;
				var prd_price = document.getElementById("sd_productPrc_"+product_id).value;
				var old_price = document.querySelector(".sd_oldPrice_"+product_id).getAttribute('data-oldPrice');
				var old_quantity = document.querySelector(".sd_oldQuantity_"+product_id).getAttribute('data-oldQty');
				if(prd_qty.trim() == old_quantity.trim() && prd_price.trim() == old_price.trim()){
					cancel_product_update(product_id);
				}else{
				document.getElementById('sd_subscriptionLoader').classList.remove('display-hide-label');
				displayMessage('Product Updating','waiting');
				e.target.classList.add('btn-disable-loader');
				var product_title = document.getElementById("product_qty_"+product_id).getAttribute('data-productName');
				var line_id = e.target.getAttribute('data-line_id');
					if(AjaxCallFrom == 'backendAjaxCall'){
						var updatd_by = 'Admin';
					}else{
						var updatd_by = 'Customer';
					}
					let ajaxParameters = {
						method:"POST",
						dataValues:{
							// 'data_to_update':data_to_update,
							'product_id':product_id,
							'prd_qty':prd_qty,
							'prd_old_qty':old_quantity,
							'prd_old_price':old_price,
							'prd_price':prd_price,
							'contract_id':contract_id,
							'line_id' : line_id,
							'updated_by' : updatd_by,
							'adminEmail' : adminEmail,
							'customerEmail' : customerEmail,
							'product_title' :product_title,
							'specific_contract_data':specific_contract_data,
							'contract_product_details':contract_existing_products,
							action:"updatePrdQtyPrice"
						}
					};
					try{
						let result = await AjaxCall(ajaxParameters);
						document.getElementById('sd_subscriptionLoader').classList.add('display-hide-label');
						if(result['status'] == true){
							displayMessage(result['message'],false);
							location.reload();
						}else{
							displayMessage(result['message'],error);
							location.reload();
						}
					}catch(e){
						e.target.classList.remove('btn-disable-loader');
						displayMessage(e,true);
						document.getElementById('sd_subscriptionLoader').classList.add('display-hide-label');
					}
				}
		}else if(classNameArray.includes('sd_attemptBilling')){
				e.target.classList.add('btn-disable-loader');
				var adminEmail = document.getElementById('sendMailToAdmin').value;
	         	var customerEmail = document.getElementById('sendMailToCustomer').value;
				var actualAttemptDate = e.target.getAttribute('data-billing_date');
				var contract_id = document.getElementById('sd_contractId').value;
				var billingPolicy = e.target.getAttribute('data-billingPolicy');
				attempt_billing_confirm_box = `Are you sure to attempt the billing and create the order earlier than the actual order Date. This can't be undone`;
				document.getElementById("sd_global_modal_container").innerHTML = confirmBoxModal("sd_attempt_billing",actualAttemptDate+'_'+contract_id+'_'+billingPolicy+'_'+adminEmail+'_'+customerEmail,"Warning",attempt_billing_confirm_box,"Attempt Billing","Polaris-Button--primary","Cancel");
			}else if(classNameArray.includes('sd_skipOrder')){
				e.target.classList.add('btn-disable-loader');
				var skipDate = e.target.getAttribute('data-billing_date');
				var billing_policy = e.target.getAttribute('data-billingPolicy');
				var delivery_billing_type = e.target.getAttribute('data-billingType');
				var order_skip_confirm_box = "Are you sure You want to skip the order, this can't be undone";
				document.getElementById("sd_global_modal_container").innerHTML = confirmBoxModal("contract_order_skip",skipDate+'_'+billing_policy+'_'+delivery_billing_type,"Warning",order_skip_confirm_box,"Skip","Polaris-Button--primary","Cancel");
			}else if(classNameArray.includes('sd_updateSubscriptionStatus')){
				var adminEmail = document.getElementById('sendMailToAdmin').value;
				var customerEmail = document.getElementById('sendMailToCustomer').value;
				e.target.classList.add('btn-disable-loader');
				var buttonText = e.target.getAttribute('data-buttonText');
				var statusChangeTo = e.target.getAttribute('data-subscriptionstatus');
				var nextBillingDate = e.target.getAttribute('data-nextBillingDate');
				var billing_policy_value = e.target.getAttribute('data-billingValue');
				var delivery_billing_type = e.target.getAttribute('data-deliveryType');
				if(statusChangeTo == 'EXPIRED'){
					message = 'Are you sure, you want to cancel this subscription? This cannot be undone. All data related to the subcription will also deleted.';
				}else{
					message = 'Are you sure, you want to '+buttonText+'?';
				}
				contract_id = document.getElementById("sd_contractId").value;
				document.getElementById("sd_global_modal_container").innerHTML = confirmBoxModal('delete_subscription_contract',statusChangeTo+'#'+contract_id+'#'+adminEmail+'#'+customerEmail+'#'+nextBillingDate+'#'+delivery_billing_type+'#'+billing_policy_value,buttonText,message,'Yes',"Polaris-Button--destructive",'Cancel');
			}else if(classNameArray.includes('productSelected')){
				let sd_parentDivId = e.target.closest('ul').id;
				before_updating_checkbox_data_picker_selection = JSON.parse(JSON.stringify(picker_selection_checkboxes));
				let product_id = e.target.getAttribute('data-productid');
				let totalProductVariants = document.getElementsByClassName('productSelected_'+product_id)[0].getAttribute('data-totalVariants');
				var view_more_btn_exist = document.querySelector('#'+sd_parentDivId+ ' #'+ 'variant_view_more_'+product_id);
				if(view_more_btn_exist)
				{
					// document.getElementById('sd_productLoader').classList.remove('display-hide-label');
					view_more_btn_exist.click();
					// document.getElementById('sd_saveProducts').classList.add('Polaris-Button--disabled');
					document.getElementById('sd_productLoader').classList.remove('display-hide-label');
				}
				if(document.querySelector("#"+sd_parentDivId+" #"+e.target.id).checked){
					checkCheckboxes =true;
				}else{
					checkCheckboxes = false;
				}

				if(view_more_btn_exist && classNameArray.includes('sd_mainProduct')){
					setTimeoutInterval = 2500;
				}else{
					setTimeoutInterval = 0;
				}
				setTimeout(function() {
				if(classNameArray.includes('sd_mainProduct')){
				all_values = document.querySelectorAll("#"+sd_parentDivId+" #sd_productVariant_"+product_id+" [data-productId='"+product_id+"']");
				all_values.forEach(function(item, index) {
					checkbox_id = document.querySelectorAll("#"+sd_parentDivId+" [data-productId='"+product_id+"']")[index+1].id;
					variant_id = document.getElementById(checkbox_id).value;
					if(document.querySelector("#"+sd_parentDivId+" #"+checkbox_id).disabled == false){ // if checkboxes are not diabled
						document.querySelector("#"+sd_parentDivId+" #"+checkbox_id).checked = checkCheckboxes;
					}
					if(checkCheckboxes == false){
						delete picker_selection_checkboxes[variant_id];
					}else{
						if(checkCheckboxes == true){
							var product_obj = {
							variant_id : variant_id,
							product_id : document.getElementById(checkbox_id).getAttribute('data-productid'),
							product_title : document.getElementById(checkbox_id).getAttribute('data-producttitle'),
							variant_title : document.getElementById(checkbox_id).getAttribute('data-varianttitle'),
							product_handle : document.getElementById(checkbox_id).getAttribute('data-producthandle'),
							image : document.getElementById(checkbox_id).getAttribute('data-productimage'),
							price : document.getElementById(checkbox_id).getAttribute('data-subscriptionprice'),
							quantity : 1,
						};

						picker_selection_checkboxes[variant_id] = product_obj;
						}
					}
				});
			}else{
				variant_id = e.target.value;
                if(checkCheckboxes == true){
					var product_obj = {
						variant_id : variant_id,
						product_id : e.target.getAttribute('data-productid'),
						product_title : e.target.getAttribute('data-producttitle'),
						variant_title : e.target.getAttribute('data-varianttitle'),
						product_handle : e.target.getAttribute('data-producthandle'),
						image : e.target.getAttribute('data-productimage'),
						price : e.target.getAttribute('data-subscriptionprice'),
						quantity : 1
					};
					picker_selection_checkboxes[variant_id] = product_obj;
				}else if(checkCheckboxes == false){
					delete picker_selection_checkboxes[variant_id];
				}
			}
			if(totalProductVariants > 1){
				total_variant_count = document.querySelectorAll('#sd_productVariant_'+product_id+' input[type="checkbox"]').length;
				selected_variant_count = document.querySelectorAll('#sd_productVariant_'+product_id+' input[type="checkbox"]:checked').length;
				if((selected_variant_count > 0) && (selected_variant_count < total_variant_count)){
					document.querySelectorAll('#'+sd_parentDivId+' #all_variant_selected_'+product_id).innerHTML = selected_variant_count+' Variant Selected';
					document.querySelectorAll('#'+sd_parentDivId+' .productSelected_'+product_id)[0].classList.add('sd_someVariantSelected');
					document.querySelectorAll('#'+sd_parentDivId+' .productSelected_'+product_id)[0].checked = true;
				}else if(selected_variant_count == 0 ){
					disabled_variant_count =  document.querySelectorAll('#sd_productVariant_'+product_id+' input[type="checkbox"]:disabled').length;
					if(disabled_variant_count > 0){
						document.querySelectorAll('#'+sd_parentDivId+' #all_variant_selected_'+product_id).innerHTML = disabled_variant_count + ' variants added';
					}else{
						document.querySelectorAll('#'+sd_parentDivId+' #all_variant_selected_'+product_id).innerHTML = '';
					}
					document.querySelectorAll('#'+sd_parentDivId+ ' .productSelected_'+product_id)[0].classList.remove('sd_someVariantSelected');
					document.querySelectorAll('#'+sd_parentDivId+ ' .productSelected_'+product_id)[0].checked = false;
				}else if(selected_variant_count == total_variant_count){
					document.querySelectorAll('#'+sd_parentDivId+ ' .productSelected_'+product_id)[0].checked = true;
					document.querySelectorAll('#'+sd_parentDivId+' #all_variant_selected_'+product_id).innerHTML = 'All Variant Selected';
					document.querySelectorAll('#'+sd_parentDivId+ ' .productSelected_'+product_id)[0].classList.remove('sd_someVariantSelected');
				}
			}
			// document.getElementById('sd_saveProducts').classList.remove('Polaris-Button--disabled');
			document.getElementById('sd_productLoader').classList.add('display-hide-label');
			document.querySelector("#"+sd_parentDivId+" #"+e.target.id).checked = checkCheckboxes;
		}, setTimeoutInterval);

		}else if(classNameArray.includes('remove_product')){
				var product_id = e.target.getAttribute('data-product_id');
			    var line_id = e.target.getAttribute('data-line_id');
			   message = 'Are you sure you want to delete the product from the subscription';
			   document.getElementById("sd_global_modal_container").innerHTML = confirmBoxModal('delete_subscription_product',product_id+'_'+line_id,'Delete Subscription',message,'Yes',"Polaris-Button--destructive",'Cancel');
		}else if(classNameArray.includes('display_remove_variant')){
			let variant_id = e.target.getAttribute("variant-id");
			let product_id = e.target.getAttribute('product-id');
			let remove_variant = document.querySelectorAll('#'+form_type+'_subscription_wrapper #display_product_'+variant_id);
			remove_variant.forEach(e => e.remove());
			delete picker_selection_checkboxes[variant_id];
			var variant_checkbox = document.getElementById('productSelected_'+variant_id); //check  i checkbox html exist in the product picker
			if(variant_checkbox){
				variant_checkbox.checked = false;  // uncheck  the deleted variant checkbox from the product picker html
				check_product_id = document.querySelectorAll('.show_selected_products [product-id="'+product_id+'"]');
				if(check_product_id.length > 0){ //if all variant deleted then uncheck the main product checkbox too
					document.getElementById('productSelected_'+product_id).checked = false;
				}
			}
			let show_save_bar = check_subscription_whole_form_change();
			if(show_save_bar){
				show_save_banner();
			}else{
				hide_save_banner();
			}
			// remove variant from the product
			productKeyToDelete = 'gid://shopify/Product/'+product_id;
			variantIdToDelete = 'gid://shopify/ProductVariant/'+variant_id;
			console.log('pre_selected_products__before', pre_selected_products);
			if (pre_selected_products.hasOwnProperty(productKeyToDelete)) {
				console.log('productKeyToDelete', productKeyToDelete);
				console.log('variantIdToDelete', variantIdToDelete);
				var variants = pre_selected_products[productKeyToDelete];
				console.log('variants', variants);
				var indexToDelete = variants.findIndex(function(variant) {
					return variant === variantIdToDelete;
				});
				console.log('indexToDelete', indexToDelete);
				if (indexToDelete !== -1) {
					variants.splice(indexToDelete, 1);
					if (variants.length === 0) {
						delete pre_selected_products[productKeyToDelete];
					}
				}
			}
			console.log('pre_selected_products___after', pre_selected_products);

			// remove variant from the product end
			if(document.querySelectorAll('#'+form_type+'_subscription_wrapper .show_selected_products li').length == 0){
				document.querySelector('#'+form_type+'_subscription_wrapper .show_selected_products').classList.add('display-hide-label');
			}
			before_updating_checkbox_data_picker_selection = JSON.parse(JSON.stringify(picker_selection_checkboxes));
		}else if(classNameArray.includes('sd_Tabs')){
			var show_custom_template_preview = '{}', hide_default_template_preview = {};
			let target_tab = e.target.getAttribute('target-tab');
			let data_group = e.target.getAttribute('group');
			if(target_tab == 'edit_custom_template'){
				var active_plan_name = e.target.getAttribute('active-planname');
				console.log(active_plan_name);
				if(active_plan_name != 'Basic' && active_plan_name != 'Custom'){
					// console.log('Free');
					// redirect_link_global = 'app_plans.php';
					// redirectlink();
					open('/admin/app_plans.php?shop='+store, '_self')
				}else{
					show_custom_template_preview = {
						name : 'sd_custom_template_preview',
						classname : 'display-hide-label'
					};
					hide_default_template_preview = {
						name : 'sd_default_template_preview',
						classname : 'display-hide-label'
					};
				}
			}else if(target_tab == 'edit_default_template'){
				hide_default_template_preview = {
					name : 'sd_custom_template_preview',
					classname : 'display-hide-label'
				};
				show_custom_template_preview = {
					name : 'sd_default_template_preview',
					classname : 'display-hide-label'
				};
			}
			if((target_tab == 'edit_custom_template' && active_plan_name == 'Basic') || (target_tab == 'edit_custom_template' && active_plan_name == 'Custom') || target_tab != 'edit_custom_template' ){
				console.log('data group = ', target_tab);
				console.log('data geoup if be vrery fast',data_group);
				let remove_class_params = {
					class_elements:[
						{
							name:data_group+'-title',
							classname:"Polaris-Tabs__Tab--selected"
						},
						show_custom_template_preview,
						// hide_default_template_preview
					]
				};
				remove_class(remove_class_params);
				let add_class_params = {
					class_elements:[
						{
							name:data_group,
							classname:"Polaris-Tabs__Panel--hidden"
						},
						// show_custom_template_preview,
						hide_default_template_preview

					]
				};
				add_class(add_class_params);
				sd_attributeName = document.querySelectorAll('[target-tab="'+target_tab+'"]');
				for (let i = 0; i < sd_attributeName.length; i++) {
					sd_attributeName[i].classList.add('Polaris-Tabs__Tab--selected');
				}
				(document.getElementById(target_tab)).classList.remove('Polaris-Tabs__Panel--hidden');
			}
		}
	});


	function shipping_address_validation(shippingAddressFormElements_array){
		let add_class_params = {
			class_elements:[
				{
					name:"shipping_address_error",
					classname:"display-hide-label"
				}
			]
		};
		add_class(add_class_params);
		let add_status = true;
		Array.from(shippingAddressFormElements_array).forEach((shippingAddressFormElements,index) =>
		{
			let shippingAddressValue = shippingAddressFormElements.value;
			let data_text = shippingAddressFormElements.getAttribute('data-text');
			let data_id = shippingAddressFormElements.getAttribute('id');
			if(shippingAddressValue.length == 0){
				if(data_id == 'shipAddressTwo' || data_id == 'shipCompany'){
				}else{
						if(data_text){
						document.getElementById(data_id+'_error').classList.remove('display-hide-label');
						add_status = false;
						document.getElementById(data_id+'_error').innerHTML = data_text+' cannot be empty';

						}
				}
			}
		});
		if(add_status == true){
			return true;
		}else{
			return false;
		}
	}

	function cancel_product_update(productId){
		let add_class_params = {
			class_elements:[
				{
					name:"updateCancelBtn_"+productId,
					classname:"display-hide-label"
				},{
					name:"sd_newQuantity_"+productId,
					classname:"display-hide-label"
				},{
				   name: "sd_newPrice_"+productId,
				   classname: "display-hide-label"
				}
			]
		};
		add_class(add_class_params);
		let remove_class_params = {
			class_elements:[
				{
					name:"sd_updateProductButton_"+productId,
					classname:"display-hide-label"
				},{
					name:"sd_oldQuantity_"+productId,
					classname:"display-hide-label"
				},{
					name:"sd_oldPrice_"+productId,
					classname:"display-hide-label"
				}
			]
		};
	   remove_class(remove_class_params);
	}

	function reset_product_picker(){
		picker_selection_checkboxes = {};
		before_updating_checkbox_data_picker_selection = {};
		picker_display_wrapper = '';
		picker_display_style = '';
	}

	function reset_product_picker_html(){
		document.getElementById('sd_newProductsList').innerHTML = '';
	   document.getElementById('sd_viewNextProducts').value = '';
	}

	function close_product_picker(){
		document.getElementById("sd_searchProduct").value = '';
		let add_class_params = {
			class_elements:[
				{
					name:"sd_productPanel",
					classname:"display-hide-label"
				}
			]
		};
	   add_class(add_class_params);
	   document.getElementById('sd_newProductsList').classList.remove('display-hide-label');
	   document.getElementById('sd_searchList').innerHTML = '';
	}

	async function sd_viewMoreProducts(){
		let listData,cusrsor_id_getFrom;
		var element = document.getElementById('sd_allProducts_wrapper');
		if(element.offsetHeight + element.scrollTop >= element.scrollHeight)
		{
			var sd_productSpinner = document.getElementById("sd_productSpinner");
			sd_productSpinner.classList.remove("display-hide-label");
			let searchQuery = (document.getElementById("sd_searchProduct").value).trim();
			if(searchQuery != ''){
				cusrsor_id_getFrom = document.getElementById("sd_viewNextSearchProducts");
				cursorId = cusrsor_id_getFrom.value;
				listData =  document.getElementById('sd_searchList');
			}else{
				listData =  document.getElementById('sd_newProductsList');
				cusrsor_id_getFrom = document.getElementById("sd_viewNextProducts");
				cursorId = cusrsor_id_getFrom.value;
			}
			if(cursorId != 'No'){
				let sd_productsHtmlArray = await add_newProducts(cursorId,searchQuery);
				listData.insertAdjacentHTML('beforeend', sd_productsHtmlArray.productsList);
				cusrsor_id_getFrom.value = sd_productsHtmlArray.nextCursorId;
				var sd_productSpinner = document.getElementById("sd_productSpinner");
			sd_productSpinner.classList.add("display-hide-label");
			}else{
				var sd_productSpinner = document.getElementById("sd_productSpinner");
				sd_productSpinner.classList.add("display-hide-label");
			}
		}
	}

	function clear_product_picker_selection(parent_Id){
		document.getElementById(parent_Id).innerHTML = '';

	}

	// add new products button click
	async function add_newProductClick(){
		let remove_class_params = {
			class_elements:[
				{
					name:"sd_productPanel",
					classname:"display-hide-label"
				},{
					name : "sd_productSpinner",
					className :"display-hide-label"
				}
			]
		};
		remove_class(remove_class_params);
		let cursorId = document.getElementById("sd_viewNextProducts").value;
		if(cursorId != 'No'){
			let sd_productsHtmlArray = await add_newProducts(cursorId,'');
			if(sd_productsHtmlArray){
				document.getElementById("sd_viewNextProducts").value='';
				if(((sd_productsHtmlArray.productsList).length == 0) && (jQuery('#sd_newProductsList  li').length == 0)){
                     product_list = 'No Product Exist in your store please create one <div p-color-scheme="light"><a class="Polaris-Link" href="https://'+store+'/admin/products" target="__blank" data-polaris-unstyled="true">click here</a><div id="PolarisPortalsContainer"></div></div>';
				}else{
					product_list = sd_productsHtmlArray.productsList;
				}
				document.getElementById("sd_newProductsList").insertAdjacentHTML('beforeend', product_list);
				document.getElementById("sd_viewNextProducts").value= sd_productsHtmlArray.nextCursorId;
				let sd_productSpinner = document.getElementById("sd_productSpinner");
				sd_productSpinner.classList.add("display-hide-label");
			}else{
				document.getElementById("sd_newProductsList").innerHTML = 'No Product Found';
			}
	    }
	}

	// on keyup searchbar
	var searchProducts = document.getElementById("sd_searchProduct");
	var debounce = (func, delay) => {
		let debounceTimer

		return function() {
			const context = this
			const args = arguments
				clearTimeout(debounceTimer)
					debounceTimer
				= setTimeout(() => func.apply(context, args), delay)
		}
	}
    if(searchProducts){
		searchProducts.addEventListener('keyup', debounce(async function() {
			var sd_productSpinner = document.getElementById("sd_productSpinner");
			sd_productSpinner.classList.remove("display-hide-label");
			var searchValue = searchProducts.value;
			if(searchValue != ''){
				document.getElementById("sd_newProductsList").classList.add('display-hide-label');
				var viewMoreButton = document.getElementById("sd_viewNextSearchProducts");
				searchProductsHtml = await add_newProducts('',searchValue);
				if(searchProductsHtml.productsList == ''){
					document.getElementById('sd_searchList').innerHTML = 'No Product Found';
				}else{
					document.getElementById('sd_searchList').innerHTML = searchProductsHtml.productsList;
					viewMoreButton.value = searchProductsHtml['nextCursorId'];
				}
				sd_productSpinner.classList.add("display-hide-label");
			}else{
				document.querySelectorAll('.productSelected').forEach(el => el.checked = false);
				if(Object.keys(picker_selection_checkboxes).length > 0){
					Object.keys(picker_selection_checkboxes).forEach(function (value,key) {
						if(document.getElementById('productSelected_'+value)){
						   document.getElementById('productSelected_'+value).checked = true;
						}
						if(document.getElementById('productSelected_'+picker_selection_checkboxes[value].product_id)){
					    	document.getElementById('productSelected_'+picker_selection_checkboxes[value].product_id).checked = true;
						}else if(document.getElementById('productSelected_'+picker_selection_checkboxes[value].variant_id)){
							document.getElementById('productSelected_'+picker_selection_checkboxes[value].variant_id).checked = true;
						}
					});
				}
				document.getElementById("sd_newProductsList").classList.remove('display-hide-label');
				document.getElementById('sd_searchList').innerHTML = '';
				sd_productSpinner.classList.add("display-hide-label");
			}
		}, 1000));
	}
  // on key up search bar end


  		 //on click add products
  		function sd_saveProducts(){
			console.log('sd_saveProducts');
			console.log('picker_display_wrapper',picker_display_wrapper);
			before_updating_checkbox_data_picker_selection = JSON.parse(JSON.stringify(picker_selection_checkboxes));
			let html_tag = '';
			let variant_title,parent_Id;
			if(picker_display_style == 'tag'){
				if(picker_display_wrapper == 'subscription_edit_prodcts'){
					// remove_class(remove_class_params);
					let show_save_bar = check_subscription_whole_form_change();
					if(show_save_bar){
						show_save_banner();
					}else{
						hide_save_banner();
					}
				    clear_product_picker_selection(picker_display_wrapper);
				}
			}
			if(Object.keys(picker_selection_checkboxes).length > 0){
				for (let key in picker_selection_checkboxes) {
					if(picker_selection_checkboxes[key]['variant_title'] == 'Default Title'){
						variant_title = '';
					}else{
						variant_title = picker_selection_checkboxes[key]['variant_title'];
					}
					if(picker_display_style == 'tag'){
					html_tag += picker_tag_html(picker_selection_checkboxes[key]['product_id'],picker_selection_checkboxes[key]['variant_id'],picker_selection_checkboxes[key]['product_title'],variant_title,picker_selection_checkboxes[key]['image']);
					displayMessage('Product Updated', 'success');
					if(form_type == 'edit'){
						document.querySelector('#subscription_edit_products .show_selected_products').classList.remove('display-hide-label');
						document.querySelector('#subscription_edit_products .show_selected_products').classList.add('products_updated');
					}
					}else if(picker_display_style == 'prdouct-box'){
						console.log('Add shipping address = ',add_shipping_address);
						if(add_shipping_address == 'Yes'){
							content_html = `In order to include a physical product, it is necessary to provide the shipping address within the subscription, which is currently absent. Kindly ensure that the shipping address is added to this subscription for seamless processing.`;
							document.getElementById("sd_global_modal_container").innerHTML = confirmBoxModal("open_shipping_address_tab",'','Add Shipping Address',content_html,'Add Shipping Address',"Polaris-Button--primary","Cancel");
						}else{
							content_html = `<div>
							<h2 class="Polaris-Heading">Are you sure you want to add the Products in the subscription ? This will immediately update your subscription</h2></div>`;
							document.getElementById("sd_global_modal_container").innerHTML = confirmBoxModal("update_contract_products",'','Add Products in the Subscription',content_html,'Add',"Polaris-Button--primary","Cancel");
						}
					}
				}
			if(document.getElementById(picker_display_wrapper)){
			   document.getElementById(picker_display_wrapper).innerHTML = html_tag;
			   document.querySelector('.subscription-create-step1 .show_selected_products').classList.remove('display-hide-label');
			}
			let add_class_params = {
				class_elements:[
					{
						name:'subscription_add_product_error',
						classname:"display-hide-label"
					}
				]
			};
			add_class(add_class_params);
			close_product_picker();
			}else{
				displayMessage('Select Any Product','error');
			}
		}

	function picker_tag_html(product_id,variant_id,product_name,variant_name,imagepath){
		html_tag =  `<li class="Polaris-ResourceItem__ListItem" id="display_product_`+variant_id.trim()+`"><div class="Polaris-ResourceItem__ItemWrapper"><div class="Polaris-ResourceItem Polaris-ResourceItem--selectable"><div class="Polaris-ResourceItem__Container"><div class="Polaris-ResourceItem__Owned"><div class="Polaris-ResourceItem__Media"><span role="img" class="Polaris-Avatar Polaris-Avatar--sizeMedium Polaris-Avatar--hasImage"><img src="${imagepath}" class="Polaris-Avatar__Image" alt="" role="presentation"></span></div></div><div class="Polaris-ResourceItem__Content"><h3><span class="Polaris-TextStyle--variationStrong"> `+product_name+' '+variant_name+`</span></h3><div></div></div></div></div><button type="button" variant-id='`+variant_id+`' product-id='`+product_id+`' class="Polaris-Tag__Button display_remove_variant"><span class="Polaris-Icon"><span class="Polaris-VisuallyHidden"></span><svg viewBox="0 0 20 20" class="Polaris-Icon__Svg" focusable="false" aria-hidden="true"><path d="m11.414 10 4.293-4.293a.999.999 0 1 0-1.414-1.414L10 8.586 5.707 4.293a.999.999 0 1 0-1.414 1.414L8.586 10l-4.293 4.293a.999.999 0 1 0 1.414 1.414L10 11.414l4.293 4.293a.997.997 0 0 0 1.414 0 .999.999 0 0 0 0-1.414L11.414 10z"></path></svg></span></div></li>`;
		return html_tag;
	}

	function displayInlineMessages(message,id,type){
	  document.getElementById(id).innerHTML = message;
	  if(type == 'success'){
		document.getElementById(id).style.color = "green";
	  }else{
		document.getElementById(id).style.color = "red";
	  }
	}

	function hide_toast_message(){
		let add_class_params = {
			class_elements:[
				{
					name:'Polaris-Frame-Toast',
					classname:"display-hide-label"
				}
			]
		};
		add_class(add_class_params);
	}

	function displayMessage(message,type) {
		if(window.hasOwnProperty('shopify')){
			if(type == 'error'){
				shopify.toast.show(message, {isError: true});
			}else{
				shopify.toast.show(message, {isError: false});
			}
		}else{
			let add_class_params = {
				class_elements:[
					{
						name:'Polaris-Frame-Toast',
						classname:"display-hide-label"
					}
				]
			};
			add_class(add_class_params);
			if(type == 'waiting'){
				let remove_class_params = {
					class_elements:[
						{
							name:'WaitingMessage',
							classname:"display-hide-label"
						}
					]
				};
				remove_class(remove_class_params);
				document.getElementById("WaitingMessage").innerHTML = message;
			}else if(type == 'error'){
				let remove_class_params = {
					class_elements:[
						{
							name:'ErrorMessage',
							classname:"display-hide-label"
						}
					]
				};
				remove_class(remove_class_params);
				document.getElementById("ErrorMessage").innerHTML = message;
			}if(type == 'success'){
				let remove_class_params = {
					class_elements:[
						{
							name:'SuccessMessage',
							classname:"display-hide-label"
						}
					]
				};
				remove_class(remove_class_params);
				document.getElementById("SuccessMessage").innerHTML = message;
			}
			if(type != 'waiting'){
				setTimeout(function(){
					let add_class_params = {
						class_elements:[
							{
								name:'Polaris-Frame-Toast',
								classname:"display-hide-label"
							}
						]
					};
					add_class(add_class_params);
				}, 3000);
			}
		}
    }

	// function displayMessage(message,type) {
	// 	let add_class_params = {
	// 		class_elements:[
	// 			{
	// 				name:'Polaris-Frame-Toast',
	// 				classname:"display-hide-label"
	// 			}
	// 		]
	// 	};
	// 	add_class(add_class_params);
	// 	if(type == 'waiting'){
	// 		let remove_class_params = {
	// 			class_elements:[
	// 				{
	// 					name:'WaitingMessage',
	// 					classname:"display-hide-label"
	// 				}
	// 			]
	// 		};
	// 		remove_class(remove_class_params);
	// 		document.getElementById("WaitingMessage").innerHTML = message;
	// 	}else if(type == 'error'){
	// 		let remove_class_params = {
	// 			class_elements:[
	// 				{
	// 					name:'ErrorMessage',
	// 					classname:"display-hide-label"
	// 				}
	// 			]
	// 		};
	// 		remove_class(remove_class_params);
	// 		document.getElementById("ErrorMessage").innerHTML = message;
	// 	}if(type == 'success'){
	// 		let remove_class_params = {
	// 			class_elements:[
	// 				{
	// 					name:'SuccessMessage',
	// 					classname:"display-hide-label"
	// 				}
	// 			]
	// 		};
	// 		remove_class(remove_class_params);
	// 		document.getElementById("SuccessMessage").innerHTML = message;
	// 	}
	// 	if(type != 'waiting'){
	// 		setTimeout(function(){
	// 			let add_class_params = {
	// 				class_elements:[
	// 					{
	// 						name:'Polaris-Frame-Toast',
	// 						classname:"display-hide-label"
	// 					}
	// 				]
	// 			};
	// 			add_class(add_class_params);
	// 		}, 3000);
	//     }
    // }


	async function setCountryState(){
		let ajaxParameters = {
			method:"POST",
			dataValues:{
				action:"get_shipping_counteries"
			}
		};
		try{
			 result = await AjaxCall(ajaxParameters);
			 if(result['status'] == true){
				var j = result['shipping_counteries'];
			if(j.includes('Rest of World')){
				var j = new Array("Select Country","Afghanistan","Aland Islands","Albania","Algeria","Andorra","Angola","Anguilla","Antigua And Barbuda","Argentina","Armenia","Aruba","Ascension Island","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bermuda","Bhutan","Bolivia","Bosnia And Herzegovina","Botswana","Bouvet Island","Brazil","British Indian Ocean Territory","Brunei","Bulgaria","Burkina Faso","Burundi","Cambodia","Cameroon","Canada","Cape Verde","Caribbean Netherlands","Cayman Islands","Central African Republic","Chad","Chile","China","Christmas Island","Cocos (Keeling) Islands","Colombia","Comoros","Congo","Congo, The Democratic Republic Of The","Cook Islands","Costa Rica","Croatia","Cuba","Curaçao","Cyprus","Czech Republic","Côte d'Ivoire","Denmark","Djibouti","Dominica","Dominican Republic","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Eswatini","Ethiopia","Falkland Islands","Faroe Islands","Fiji","Finland","France","French Guiana","French Polynesia","French Southern Territories","Gabon","Gambia","Georgia","Germany","Ghana","Gibraltar","Greece","Greenland","Grenada","Guadeloupe","Guatemala","Guernsey","Guinea","Guinea Bissau","Guyana","Haiti","Hong Kong SAR","Hungary","Iceland","India","Indonesia","Iraq","Ireland","Isle Of Man","Israel","Italy","Jamaica","Japan","Jersey","Jordan","Kazakhstan","Kenya","Kiribati","Kosovo","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Macao SAR","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Martinique","Mauritania","Mauritius","Mayotte","Mexico","Moldova","Monaco","Mongolia","Montenegro","Montserrat","Morocco","Mozambique","Myanmar (Burma)","Namibia","Nauru","Nepal","Netherlands","Netherlands Antilles","New Caledonia","New Zealand","Nicaragua","Niger","Nigeria","Niue","Norfolk Island","North Macedonia","Norway","Oman","Pakistan","Palestinian Territories","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Pitcairn Islands","Poland","Portugal","Qatar","Reunion","Romania","Russia","Rwanda","Samoa","San Marino","Sao Tome And Principe","Saudi Arabia","Senegal","Serbia","Seychelles","Sierra Leone","Singapore","Sint Maarten","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","South Georgia And The South Sandwich Islands","South Korea","South Sudan","Spain","Sri Lanka","St. Barthélemy","St. Helena","St. Kitts &; Nevis","St. Lucia","St. Martin","St. Pierre & Miquelon","St. Vincent & Grenadines","Sudan","Suriname","Svalbard And Jan Mayen","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania, United Republic Of","Thailand","Timor Leste","Togo","Tokelau","Tonga","Trinidad and Tobago","Tristan da Cunha","Tunisia","Turkey","Turkmenistan","Turks and Caicos Islands","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","United States Minor Outlying Islands","Uruguay","Uzbekistan","Vanuatu","Venezuela","Vietnam","Wallis And Futuna","Western Sahara","Yemen","Zambia","Zimbabwe");
				}
					var options = '';
					let selectedCountry = document.getElementById("shipCountry").getAttribute("data-country");
					for (var i = 0; i < j.length; i++) {
						if(j[i] == selectedCountry){
						options += '<option value="' + j[i]+ '" selected>' + j[i] + '</option>';
						}else{
							options += '<option value="' + j[i]+ '">' + j[i] + '</option>';
						}
					}
					document.getElementById("shipCountry").innerHTML = options;

			}else{
				displayMessage(result['message'],'error');
			}
		}catch(e){
			displayMessage(e,'error');
		}

	}

    function getStates() {
		var countryState = [
		[
			'Afghanistan', [ //1
			['', 'State/Province'],
	    ]],
	   [
				'Aland Islands', [ //2
				['', 'State/Province'],
		]],
		[
			'Albania', [ //3
			['', 'State/Province'],
		], ],
		[
					'Algeria', [  //4
					['', 'State/Province'],
			]],	[
				'Andorra', [ //5
				['', 'State/Province'],
		], ],
		[
				'Angola', [ //6
				['', 'State/Province'],
		]],
		[
			'Anguilla', [  //7
			['', 'State/Province'],
		], ],
		[
			'Antigua And Barbuda', [  //8
			['', 'State/Province'],
		]],
		[
			'Argentina', [  //9
			['', 'Buenos Aires'],['', 'Catamarca'],['', 'Chaco'],['', 'Chubut'],['', 'Ciudad Autónoma de Buenos Aires'],['', 'Córdoba'],['', 'Corrientes'],['', 'Entre Ríos'],['', 'Formosa'],['', 'Jujuy'],['', 'La Pampa'],['', 'La Rioja'],['', 'Mendoza'],['', 'Misiones'],['', 'Neuquén'],['', 'Río Negro'],['', 'Salta'],['', 'San Juan'],['', 'San Luis'],['', 'Santa Cruz'],['', 'Santa Fe'],['', 'Santiago Del Estero'],['', 'Tierra Del Fuego'],['', 'Tucumán']
		], ],
		[
			'Armenia', [  //10
			['', 'State/Province'],
		]],
		[
			'Aruba', [ //11
			['', 'State/Province'],
		]],
[
	'Ascension Island', [  //12
	['', 'State/Province']
], ],
[
	'Australia', [ //13
	['', 'State/Province'],['', 'Australian Capital Territory'],['', 'New South Wales'],['', 'Northern Territory'],['', 'Queensland'],['', 'South Australia'],['', 'Tasmania'],['', 'Victoria'],['', 'Western Australia']
	]],	[
		'Austria', [ //14
		['', 'State/Province'],
	], ],
	[
		'Azerbaijan', [ //15
		['', 'State/Province'],
	]],	[
		'Bahamas', [  //16
		['', 'State/Province'],
	], ],
	[
		'Bahrain', [ //17
		['', 'State/Province'],
	]],	[
		'Bangladesh', [  //18
		['', 'State/Province'],
	], ],
	[
		'Barbados', [   //19
		['', 'State/Province'],
	]],	[
		'Belarus', [    //20
		['', 'State/Province'],
	], ],
	[
		'Belgium', [    //21
		['', 'State/Province'],
	]],	[
		'Belize', [    //22
		['', 'State/Province'],
	], ],
	[
		'Benin', [  //23
		['', 'State/Province'],
	]],	[
		'Bermuda', [    //24
		['', 'State/Province'],
	], ],
	[
		'Bhutan', [      //25
		['', 'State/Province'],
	]],	[
		'Bolivia', [     //26
		['', 'State/Province'],
	], ],
	[
		'Bosnia And Herzegovina', [     //27
		['', 'State/Province'],
	]],	[
		'Botswana', [      //28
		['', 'State/Province'],
	], ],
	[
		'Bouvet Island', [  //29
		['', 'State/Province'],
	]],	[
		'Brazil', [  //30
		['', 'Acre'],['', 'Alagoas'],['', 'Amapá'],['', 'Amazonas'],['', 'Bahia'],['', 'Ceará'],['', 'Distrito Federal'],['', 'Espírito Santo'],['', 'Goiás'],['', 'Maranhão'],['', 'Mato Grosso'],['', 'Mato Grosso do Sul'],['', 'Minas Gerais'],['', 'Pará'],['', 'Paraíba'],['', 'Paraná'],['', 'Pernambuco'],['', 'Piauí'],['', 'Rio de Janeiro'],['', 'Rio Grande do Norte'],['', 'Rio Grande do Sul'],['', 'Rondônia'],['', 'Roraima'],['', 'Santa Catarina'],['', 'São Paulo'],['', 'Sergipe'],['', 'Tocantins']
	], ],
	[
		'British Indian Ocean Territory', [ //31
		['', 'State/Province'],
	]],	[
		'Brunei', [ //32
		['', 'State/Province'],
	], ],
	[
		'Bulgaria', [  //33
		['', 'State/Province'],
	]],	[
		'Burkina Faso', [   //34
		['', 'State/Province'],
	], ],
[
	'Burundi', [    //35
	['', 'State/Province'],
]],	[
	'Cambodia', [      //36
	['', 'State/Province'],
], ],[
	'Cameroon', [      //36
	['', 'State/Province'],
], ],
[
	'Canada', [     //37
	['', 'Alberta'],['', 'British Columbia'],['', 'Manitoba'],['', 'New Brunswick'],['', 'Newfoundland and Labrador'],['', 'Northwest Territories'],['', 'Nova Scotia'],['', 'Nunavut'],['', 'Ontario'],['', 'Prince Edward Island'],['', 'Quebec'],['', 'Saskatchewan'],['', 'Yukon']
]],	[
	'Cape Verde', [      //38
	['', 'State/Province'],
], ],	[
	'Caribbean Netherlands', [      //39
	['', 'State/Province'],
], ],	[
	'Cayman Islands', [      //40
	['', 'State/Province'],
], ],	[
	'Central African Republic', [      //41
	['', 'State/Province'],
], ],	[
	'Chad', [      //42
	['', 'State/Province'],
], ],	[
	'Chile', [      //43
	['', 'Antofagasta'],['', 'Araucanía'],['', 'Arica and Parinacota'],['', 'Atacama'],['', 'Aysén'],['', 'Biobío'],['', 'Coquimbo'],['', 'Los Lagos'],['', 'Los Ríos'],['', 'Magallanes'],['', 'Maule'],['', 'Ñuble'],['', 'O Higgins'],['', 'Santiago'],['', 'Tarapacá'],['', 'Valparaíso']
], ],[
	'China', [      //44
	['', 'Anhui'],['', 'Beijing'],['', 'Chongqing'],['', 'Fujian'],['', 'Gansu'],['', 'Guangdong'],['', 'Guangxi'],['', 'Guizhou'],['', 'Hainan'],['', 'Hebei'],['', 'Heilongjiang'],['', 'Henan'],['', 'Hubei'],['', 'Hunan'],['', 'Inner Mongolia'],['', 'Jiangsu'],['', 'Jiangxi'],['', 'Jilin'],['', 'Liaoning'],['', 'Ningxia'],['', 'Qinghai'],['', 'Shaanxi'],['', 'Shandong'],['', 'Shanghai'],['', 'Shanxi'],['', 'Sichuan'],['', 'Tianjin'],['', 'Xinjiang'],['', 'Xizang'],['', 'Yunnan'],['', 'Zhejiang'],
], ],	[
	'Christmas Island', [      //45
	['', 'State/Province'],
], ],	[
	'Cocos (Keeling) Islands', [      //46
	['', 'State/Province'],
], ],	[
	'Colombia', [      //47
		['', 'Amazonas'],['', 'Antioquia'],['', 'Arauca'],['', 'Atlántico'],['', 'Bogotá, D.C.'],['', 'Bolívar'],['', 'Boyacá'],['', 'Caldas'],['', 'Caquetá'],['', 'Casanare'],['', 'Cauca'],['', 'Cesar'],['', 'Chocó'],['', 'Córdoba'],['', 'Cundinamarca'],['', 'Guainía'],['', 'Guaviare'],['', 'Huila'],['', 'La Guajira'],['', 'Magdalena'],['', 'Meta'],['', 'Nariño'],['', 'Norte de Santander'],['', 'Putumayo'],['', 'Quindío'],['', 'Risaralda'],['', 'San Andrés, Providencia y Santa Catalina'],['', 'Santander'],['', 'Sucre'],['', 'Tolima'],['', 'Valle del Cauca'],['', 'Vaupés'],['', 'Vichada']
], ],	[
	'Comoros', [      //48
		['', 'State/Province'],
], ],	[
	'Congo', [      //49
	['', 'State/Province'],
], ],	[
	'Congo, The Democratic Republic Of The', [      //50
	['', 'State/Province'],
], ],	[
	'Cook Islands', [      //51
	['', 'State/Province'],
], ],	[
	'Costa Rica', [      //52
	['', 'State/Province'],
], ],	[
	'Croatia', [      //53
	['', 'State/Province'],
], ],	[
	'Cuba', [      //54
	['', 'State/Province'],
], ],	[
	'Curaçao', [      //55
	['', 'State/Province'],
], ],	[
	'Cyprus', [      //56
	['', 'State/Province'],
], ],	[
	'Czech Republic', [      //57
	['', 'State/Province'],
], ],	[
	"Côte d'Ivoire", [      //58
	['', 'State/Province'],
], ],	[
	'Denmark', [      //59
	['', 'State/Province'],
], ],	[
	'Djibouti', [      //60
	['', 'State/Province'],
], ],	[
	'Dominica', [      //61
	['', 'State/Province'],
], ],	[
	'Dominican Republic', [      //62
	['', 'State/Province'],
], ],	[
	'Ecuador', [      //63
	['', 'State/Province'],
], ],	[
	'Egypt', [      //64
		['', '6th of October'],['', 'Al Sharqia'],['', 'Alexandria'],['', 'Aswan'],['', 'Asyut'],['', 'Beheira'],['', 'Beni Suef'],['', 'Cairo'],['', 'Dakahlia'],['', 'Damietta'],['', 'Faiyum'],['', 'Gharbia']['', 'Giza'],['', 'Helwan'],['', 'Ismailia'],['', 'Kafr el-Sheikh'],['', 'Luxor'],['', 'Matrouh'],['', 'Minya'],['', 'Monufia'],['', 'New Valley'],['', 'North Sinai'],['', 'Port Said'],['', 'Qalyubia']['', 'Qena'],['', 'Red Sea'],['', 'Sohag'],['', 'South Sinai'],['', 'Suez']
], ],	[
	'El Salvador', [      //65
		['', 'State/Province'],
], ],[
	'Equatorial Guinea', [      //66
	['', 'State/Province'],
], ],[
	'Eritrea', [      //67
	['', 'State/Province'],
], ],[
	'Estonia', [      //68
	['', 'State/Province'],
], ],[
	'Eswatini', [      //69
	['', 'State/Province'],
], ],[
	'Ethiopia', [      //70
	['', 'State/Province'],
], ],[
	'Falkland Islands', [      //71
	['', 'State/Province'],
], ],[
	'Faroe Islands', [      //72
	['', 'State/Province'],
], ],[
	'Fiji', [      //73
	['', 'State/Province'],
], ],[
	'Finland', [      //74
	['', 'State/Province'],
], ],[
	'France', [      //75
	['', 'State/Province'],
], ],[
	'French Guiana', [      //76
	['', 'State/Province'],
], ],[
	'French Polynesia', [      //77
	['', 'State/Province'],
], ],[
	'French Southern Territories', [      //78
	['', 'State/Province'],
], ],[
	'Gabon', [      //79
	['', 'State/Province'],
], ],[
	'Gambia', [      //80
	['', 'State/Province'],
], ],[
	'Georgia', [      //81
	['', 'State/Province'],
], ],[
	'Germany', [      //82
	['', 'State/Province'],
], ],[
	'Ghana', [      //83
	['', 'State/Province'],
], ],[
	'Gibraltar', [      //84
	['', 'State/Province'],
], ],[
	'Greece', [      //85
	['', 'State/Province'],
], ],[
	'Greenland', [      //86
	['', 'State/Province'],
], ],[
	'Grenada', [      //87
	['', 'State/Province'],
], ],[
	'Guadeloupe', [      //88
	['', 'State/Province'],
], ],[
	'Guatemala', [      //89
			['', 'Alta Verapaz'],['', 'Baja Verapaz'],['', 'Chimaltenango'],['', 'Chiquimula'],['', 'El Progreso'],['', 'Escuintla'],['', 'Guatemala'],['', 'Huehuetenango'],['', 'Izabal'],['', 'Jalapa'],['', 'Jutiapa'],['', 'Petén'],['', 'Quetzaltenango'],['', 'Quiché'],['', 'Retalhuleu'],['', 'Sacatepéquez'],['', 'San Marcos'],['', 'Santa Rosa'],['', 'Sololá'],['', 'Suchitepéquez'],['', 'Totonicapán'],['', 'Zacapa']
], ],[
	'Guernsey', [      //90
	['', 'State/Province'],
], ],[
	'Guinea', [      //91
	['', 'State/Province'],
], ],[
	'Guinea Bissau', [      //92
	['', 'State/Province'],
], ],[
	'Guyana', [      //93
	['', 'State/Province'],
], ],[
	'Haiti', [      //94
	['', 'State/Province'],
], ],[
	'Honduras', [      //97
	['', 'State/Province'],
], ],[
	'Hong Kong SAR', [      //98
	['', 'Hong Kong Island'],['', 'Kowloon'],['', 'New Territories']
], ],[
	'Hungary', [      //99
	['', 'State/Province'],
], ],[
	'Iceland', [      //100
	['', 'State/Province'],
], ],[
	'India', [      //101
	['', 'Andaman and Nicobar Islands'],['', 'Andhra Pradesh'],['', 'Arunachal Pradesh'],['', 'Assam'],['', 'Bihar'],['', 'Chandigarh'],['', 'Chhattisgarh'],['', 'Dadra and Nagar Haveli'],['', 'Daman and Diu'],['', 'Delhi'],['', 'Goa'],['', 'Gujarat'],['', 'Haryana'],['', 'Himachal Pradesh'],['', 'Jammu and Kashmir'],['', 'Jharkhand'],['', 'Karnataka'],['', 'Kerala'],['', 'Ladakh'],['', 'Lakshadweep'],['', 'Madhya Pradesh'],['', 'Maharashtra'],['', 'Manipur'],['', 'Meghalaya'],['', 'Mizoram'],['', 'Nagaland'],['', 'Odisha'],['', 'Puducherry'],['', 'Punjab'],['', 'Rajasthan'],['', 'Sikkim'],['', 'Tamil Nadu'],['', 'Telangana'],['', 'Tripura'],['', 'Uttar Pradesh'],['', 'Uttarakhand'],['', 'West Bengal'],
], ],[
	'Indonesia', [      //102
	['', 'Aceh'],['', 'Bali'],['', 'Bangka Belitung'],['', 'Banten'],['', 'Bengkulu'],['', 'Gorontalo'],['', 'Jakarta'],['', 'Jambi'],['', 'Jawa Barat'],['', 'Jawa Tengah'],['', 'Jawa Timur'],['', 'Kalimantan Barat'],['', 'Kalimantan Selatan'],['', 'Kalimantan Tengah'],['', 'Kalimantan Timur'],['', 'Kalimantan Utara'],['', 'Kepulauan Riau'],['', 'Lampung'],['', 'Maluku'],['', 'Maluku Utara'],['', 'North Sumatra'],['', 'Nusa Tenggara Barat'],['', 'Nusa Tenggara Timur'],['', 'Papua'],['', 'Papua Barat'],['', 'Riau'],['', 'South Sumatra'],['', 'Sulawesi Barat'],['', 'Sulawesi Selatan'],['', 'Sulawesi Tengah'],['', 'Sulawesi Tenggara'],['', 'Sulawesi Utara'],['', 'West Sumatra'],['', 'Yogyakarta'],
], ],[
	'Iraq', [      //104
	['', 'State/Province'],
], ],[
	'Ireland', [      //105
	['', 'Carlow'],['', 'Cavan'],['', 'Clare'],['', 'Cork'],['', 'Donegal'],['', 'Dublin'],['', 'Galway'],['', 'Kerry'],['', 'Kildare'],['', 'Kilkenny'],['', 'Laois'],['', 'Leitrim'],['', 'Limerick'],['', 'Longford'],['', 'Louth'],['', 'Mayo'],['', 'Meath'],['', 'Monaghan'],['', 'Offaly'],['', 'Roscommon'],['', 'Sligo'],['', 'Tipperary'],['', 'Waterford'],['', 'Westmeath'],['', 'Wexford'],['', 'Wicklow'],
], ],[
	'Isle Of Man', [      //106
	['', 'State/Province'],
], ],[
	'Israel', [      //107
	['', 'State/Province'],
], ],[
	'Italy', [      //108
	['', 'Agrigento'],['', 'Alessandria'],['', 'Ancona'],['', 'Aosta'],['', 'Arezzo'],['', 'Ascoli Piceno'],['', 'Asti'],['', 'Avellino'],['', 'Bari'],['', 'Barletta-Andria-Trani'],['', 'Belluno'],['', 'Benevento'],['', 'Bergamo'],['', 'Biella'],['', 'Bologna'],['', 'Bolzano'],['', 'Brescia'],['', 'Brindisi'],['', 'Cagliari'],['', 'Caltanissetta'],['', 'Campobasso'],['', 'Carbonia-Iglesias'],['', 'Caserta'],['', 'Catania'],['', 'Catanzaro'],['', 'Chieti'],['', 'Como'],['', 'Cosenza'],['', 'Cremona'],['', 'Crotone'],['', 'Cuneo'],['', 'Enna'],['', 'Fermo'],['', 'Ferrara'],['', 'Firenze'],['', 'Foggia'],['', 'Forlì-Cesena'],['', 'Frosinone'],['', 'Genova'],['', 'Gorizia']['', 'Grosseto'],['', 'Imperia'],['', 'Isernia'],['', "L'Aquila"],['', 'La Spezia'],['', 'Latina'],['', 'Lecce|Lecco'],['', 'Livorno'],['', 'Lodi'],['', 'Lucca'],['', 'Macerata'],['', 'Mantova'],['', 'Massa-Carrara'],['', 'Matera'],['', 'Medio Campidano'],['', 'Messina'],['', 'Milano|Modena'],['', 'Monza e Brianza'],['', 'Napoli'],['', 'Novara'],['', 'Nuoro'],['', 'Ogliastra'],['', 'Olbia-Tempio'],['', 'Oristano'],['', 'Padova'],['', 'Palermo'],['', 'Parma'],['', 'Pavia|Perugia|Pesaro e Urbino'],['', 'Pescara'],['', 'Piacenza'],['', 'Pisa'],['', 'Pistoia'],['', 'Pordenone'],['', 'Potenza'],['', 'Prato'],['', 'Ragusa'],['', 'Ravenna'],['', 'Reggio Calabria'],['', 'Reggio Emilia'],['', 'Rieti'],['', 'Rimini'],['', 'Roma'],['', 'Rovigo'],['', 'Salerno'],['', 'Sassari'],['', 'Savona'],['', 'Siena'],['', 'Siracusa'],['', 'Sondrio'],['', 'Taranto'],['', 'Teramo'],['', 'Terni'],['', 'Torino'],['', 'Trapani'],['', 'Trento'],['', 'Treviso'],['', 'Trieste'],['', 'Udine'],['', 'Varese'],['', 'Venezia'],['', 'Verbano-Cusio-Ossola'],['', 'Vercelli'],['', 'Verona'],['', 'Vibo Valentia'],['', 'Vicenza'],['', 'Viterbo']
], ],[
	'Jamaica', [      //109
	['', 'State/Province'],
], ],[
	'Japan', [      //110
	['', 'Aichi'],['', 'Akita'],['', 'Aomori'],['', 'Chiba'],['', 'Ehime'],['', 'Fukui'],['', 'Fukuoka'],['', 'Fukushima'],['', 'Gifu'],['', 'Gunma'],['', 'Hiroshima'],['', 'Hokkaidō'],['', 'Hyōgo'],['', 'Ibaraki'],['', 'Ishikawa'],['', 'Iwate'],['', 'Kagawa'],['', 'Kagoshima'],['', 'Kanagawa'],['', 'Kōchi'],['', 'Kumamoto'],['', 'Kyōto'],['', 'Mie'],['', 'Miyagi'],['', 'Miyazaki'],['', 'Nagano'],['', 'Nagasaki'],['', 'Nara'],['', 'Niigata'],['', 'Ōita'],['', 'Okayama'],['', 'Okinawa'],['', 'Ōsaka'],['', 'Saga'],['', 'Saitama'],['', 'Shiga'],['', 'Shimane'],['', 'Shizuoka'],['', 'Tochigi'],['', 'Tokushima'],['', 'Tōkyō'],['', 'Tottori'],['', 'Toyama'],['', 'Wakayama'],['', 'Yamagata'],['', 'Yamaguchi'],['', 'Yamanashi'],
], ],[
	'Jersey', [      //111
	['', 'State/Province'],
], ],[
	'Jordan', [      //112
	['', 'State/Province'],
], ],[
	'Kazakhstan', [      //113
	['', 'State/Province'],
], ],[
	'Kenya', [      //114
	['', 'State/Province'],
], ],[
	'Kiribati', [      //115
	['', 'State/Province'],
], ],[
	'Kosovo', [      //117
	['', 'State/Province'],
], ],[
	'Kuwait', [      //118
	['', 'State/Province'],
], ],[
	'Kyrgyzstan', [      //119
	['', 'State/Province'],
], ],[
	"Laos", [      //120
	['', 'State/Province'],
], ],[
	'Latvia', [      //121
	['', 'State/Province'],
], ],[
	'Lebanon', [      //122
	['', 'State/Province'],
], ],[
	'Lesotho', [      //123
	['', 'State/Province'],
], ],[
	'Liberia', [      //124
	['', 'State/Province'],
], ],[
	'Libya', [      //125
	['', 'State/Province'],
], ],[
	'Liechtenstein', [      //126
	['', 'State/Province'],
], ],[
	'Lithuania', [      //127
	['', 'State/Province'],
], ],[
	'Luxembourg', [      //128
	['', 'State/Province'],
], ],[
	'Macao', [      //129
	['', 'State/Province'],
], ],[
	'Madagascar', [      //130
	['', 'State/Province'],
], ],[
	'Malawi', [      //131
	['', 'State/Province'],
], ],[
	'Malaysia', [      //132
	['', 'Johor'],['', 'Kedah'],['', 'Kelantan'],['', 'Kuala Lumpur'],['', 'Labuan'],['', 'Melaka'],['', 'Negeri Sembilan'],['', 'Pahang'],['', 'Penang'],['', 'Perak'],['', 'Perlis'],['', 'Putrajaya'],['', 'Sabah'],['', 'Sarawak'],['', 'Selangor'],['', 'Terengganu'],
], ],[
	'Maldives', [      //133
	['', 'State/Province'],
], ],[
	'Mali', [      //134
	['', 'State/Province'],
], ],[
	'Malta', [      //135
	['', 'State/Province'],
], ],[
	'Martinique', [      //136
	['', 'State/Province'],
], ],[
	'Mauritania', [      //137
	['', 'State/Province'],
], ],[
	'Mauritius', [      //138
	['', 'State/Province'],
], ],[
	'Mayotte', [      //139
	['', 'State/Province'],
], ],[
	'Mexico', [      //140
	['', 'State/Province'],
	['', 'Aguascalientes'],['', 'Baja California'],['', 'Baja California Sur'],['', 'Campeche'],['', 'Chiapas'],['', 'Chihuahua'],['', 'Ciudad de México'],['', 'Coahuila'],['', 'Colima'],['', 'Durango'],['', 'Guanajuato'],['', 'Guerrero'],['', 'Hidalgo'],['', 'Jalisco'],['', 'México'],['', 'Michoacán'],['', 'Morelos'],['', 'Nayarit'],['', 'Nuevo León'],['', 'Oaxaca'],['', 'Puebla'],['', 'Querétaro'],['', 'Quintana Roo'],['', 'San Luis Potosí'],['', 'Sinaloa'],['', 'Sonora'],['', 'Tabasco'],['', 'Tamaulipas'],['', 'Tlaxcala'],['', 'Veracruz'],['', 'Yucatán'],['', 'Zacatecas'],
], ],[
	'Moldova, Republic of', [      //141
	['', 'State/Province'],
], ],[
	'Monaco', [      //142
	['', 'State/Province'],
], ],[
	'Mongolia', [      //143
	['', 'State/Province'],
], ],[
	'Montenegro', [      //144
	['', 'State/Province'],
], ],[
	'Montserrat', [      //145
	['', 'State/Province'],
], ],[
	'Morocco', [      //146
	['', 'State/Province'],
], ],[
	'Mozambique', [      //147
	['', 'State/Province'],
], ],[
	'Myanmar (Burma)', [      //148
	['', 'State/Province'],
], ],[
	'Namibia', [      //149
	['', 'State/Province'],
], ],[
	'Nauru', [      //150
	['', 'State/Province'],
], ],[
	'Nepal', [      //151
	['', 'State/Province'],
], ],[
	'Netherlands', [      //152
	['', 'State/Province'],
], ],[
	'Netherlands Antilles', [      //153
	['', 'State/Province'],
], ],[
	'New Caledonia', [      //154
	['', 'State/Province'],
], ],[
	'New Zealand', [      //155
	['', 'Auckland'],['', 'Bay of Plenty'],['', 'Canterbury'],['', 'Chatham Islands'],['', 'Gisborne'],['', "Hawke's Bay"],['', 'Manawatu-Wanganui'],['', 'Marlborough'],['', 'Nelson'],['', 'Northland'],['', 'Otago'],['', 'Southland'],['', 'Taranaki'],['', 'Tasman'],['', 'Waikato'],['', 'Wellington'],['', 'West Coast'],
], ],[
	'Nicaragua', [      //156
	['', 'State/Province'],
], ],[
	'Niger', [      //157
	['', 'State/Province'],
], ],[
	'Nigeria', [      //158
	['', 'State/Province'],
	['', 'Abia'],['', 'Abuja Federal Capital Territory'],['', 'Adamawa'],['', 'Akwa Ibom'],['', 'Anambra'],['', 'Bauchi'],['', 'Bayelsa'],['', 'Benue'],['', 'Borno'],['', 'Cross River'],['', 'Delta'],['', 'Ebonyi'],['', 'Edo'],['', 'Ekiti'],['', 'Enugu'],['', 'Gombe'],['', 'Imo'],['', 'Jigawa'],['', 'Kaduna'],['', 'Kano'],['', 'Katsina'],['', 'Kebbi'],['', 'Kogi'],['', 'Kwara'],['', 'Lagos'],['', 'Nasarawa'],['', 'Niger'],['', 'Ogun'],['', 'Ondo'],['', 'Osun'],['', 'Oyo'],['', 'Plateau'],['', 'Rivers'],['', 'Sokoto'],['', 'Taraba'],['', 'Yobe'],['', 'Zamfara'],
], ],[
	'Niue', [      //159
	['', 'State/Province'],
], ],[
	'Norfolk Island', [      //160
	['', 'State/Province'],
], ],[
	'North Macedonia', [      //161
	['', 'State/Province'],
], ],[
	'Norway', [      //162
	['', 'State/Province'],
], ],[
	'Oman', [      //163
	['', 'State/Province'],
], ],[
	'Pakistan', [      //164
	['', 'State/Province'],
], ],[
	'Palestinian Territories', [      //165
	['', 'State/Province'],
], ],[
	'Panama', [      //166
	['', 'State/Province'],
	['', 'Bocas del Toro'],['', 'Chiriquí'],['', 'Coclé'],['', 'Colón'],['', 'Darién'],['', 'Emberá'],['', 'Herrera'],['', 'Kuna Yala'],['', 'Los Santos'],['', 'Ngöbe-Buglé'],['', 'Panamá'],['', 'Panamá Oeste'],['', 'Veraguas'],
], ],[
	'Papua New Guinea', [      //167
	['', 'State/Province'],
], ],[
	'Paraguay', [      //168
	['', 'State/Province'],
], ],[
	'Peru', [      //169
	['','Amazonas'],['','Áncash'],['','Apurímac'],['','Arequipa'],['','Ayacucho'],['','Cajamarca'],['','Callao'],['','Cuzco'],['','Huancavelica'],['','Huánuco'],['','Ica'],['','Junín'],['','La Libertad'],['','Lambayeque'],['','Lima (departamento)'],['','Lima (provincia)'],['','Loreto'],['','Madre de Dios'],['','Moquegua'],['','Pasco'],['','Piura'],['','Puno'],['','San Martín'],['','Tacna'],['','Tumbes'],['','Ucayali']

], ],[
	'Philippines', [      //170
	['', 'State/Province'],
	['', 'Abra'],['', 'Agusan del Norte'],['', 'Agusan del Sur'],['', 'Aklan'],['', 'Albay'],['', 'Antique'],['', 'Apayao'],['', 'Aurora'],['', 'Basilan'],['', 'Bataan'],['', 'Batanes'],['', 'Batangas'],['', 'Benguet'],['', 'Biliran'],['', 'Bohol'],['', 'Bukidnon'],['', 'Bulacan'],['', 'Cagayan'],['', 'Camarines Norte'],['', 'Camarines Sur'],['', 'Camiguin'],['', 'Capiz'],['', 'Catanduanes'],['', 'Cavite'],['', 'Cebu'],['', 'Cotabato'],['', 'Davao de Oro'],['', 'Davao del Norte'],['', 'Davao del Sur'],['', 'Davao Occidental'],['', 'Davao Oriental'],['', 'Dinagat Islands'],['', 'Eastern Samar'],['', 'Guimaras'],['', 'Ifugao'],['', 'Ilocos Norte'],['', 'Ilocos Sur'],['', 'Iloilo'],['', 'Isabela'],['', 'Kalinga'],['', 'La Union'],['', 'Laguna'],['', 'Lanao del Norte'],['', 'Lanao del Sur'],['', 'Leyte'],['', 'Maguindanao'],['', 'Marinduque'],['', 'Masbate'],['', 'Metro Manila'],['', 'Misamis Occidental'],['', 'Misamis Oriental'],['', 'Mountain Province'],['', 'Negros Occidental'],['', 'Negros Oriental'],['', 'Northern Samar'],['', 'Nueva Ecija'],['', 'Nueva Vizcaya'],['', 'Occidental Mindoro'],['', 'Oriental Mindoro'],['', 'Palawan'],['', 'Pampanga'],['', 'Pangasinan'],['', 'Quezon'],['', 'Quirino'],['', 'Rizal'],['', 'Romblon'],['', 'Samar'],['', 'Sarangani'],['', 'Siquijor'],['', 'Sorsogon'],['', 'South Cotabato'],['', 'Southern Leyte'],['', 'Sultan Kudarat'],['', 'Sulu'],['', 'Surigao del Norte'],['', 'Surigao del Sur'],['', 'Tarlac'],['', 'Tawi-Tawi'],['', 'Zambales'],['', 'Zamboanga del Norte'],['', 'Zamboanga del Sur'],['', 'Zamboanga Sibugay'],
], ],[
	'Pitcairn Islands', [      //171
	['', 'State/Province'],
], ],[
	'Poland', [      //172
	['', 'State/Province'],
], ],[
	'Portugal', [      //173
	['', 'State/Province'],
	['', 'Açores'],['', 'Aveiro'],['', 'Beja'],['', 'Braga'],['', 'Bragança'],['', 'Castelo Branco'],['', 'Coimbra'],['', 'Évora'],['', 'Faro'],['', 'Guarda'],['', 'Leiria'],['', 'Lisboa'],['', 'Madeira'],['', 'Portalegre'],['', 'Porto'],['', 'Santarém'],['', 'Setúbal'],['', 'Viana do Castelo'],['', 'Vila Real'],['', 'Viseu'],
], ],[
	'Qatar', [      //174
	['', 'State/Province'],
], ],[
	'Reunion', [      //176
	['', 'State/Province'],
], ],[
	'Romania', [      //177
	['', 'State/Province'],
	['', 'Alba'],['', 'Arad'],['', 'Argeș'],['', 'Bacău'],['', 'Bihor'],['', 'Bistrița-Năsăud'],['', 'Botoșani'],['', 'Brăila'],['', 'Brașov'],['', 'București'],['', 'Buzău'],['', 'Călărași'],['', 'Caraș-Severin'],['', 'Cluj'],['', 'Constanța'],['', 'Covasna'],['', 'Dâmbovița'],['', 'Dolj'],['', 'Galați'],['', 'Giurgiu'],['', 'Gorj'],['', 'Harghita'],['', 'Hunedoara'],['', 'Ialomița'],['', 'Iași'],['', 'Ilfov'],['', 'Maramureș'],['', 'Mehedinți'],['', 'Mureș'],['', 'Neamț'],['', 'Olt'],['', 'Prahova'],['', 'Sălaj'],['', 'Satu Mare'],['', 'Sibiu'],['', 'Suceava'],['', 'Teleorman'],['', 'Timiș'],['', 'Tulcea'],['', 'Vâlcea'],['', 'Vaslui'],['', 'Vrancea'],
], ],[
	'Russia', [      //178
	['', 'Altai Krai'],['', 'Altai Republic'],['', 'Amur Oblast'],['', 'Arkhangelsk Oblast'],['', 'Astrakhan Oblast'],['', 'Belgorod Oblast'],['', 'Bryansk Oblast'],['', 'Chechen Republic'],['', 'Chelyabinsk Oblast'],['', 'Chukotka Autonomous Okrug'],['', 'Chuvash Republic'],['', 'Irkutsk Oblast'],['', 'Ivanovo Oblast'],['', 'Jewish Autonomous Oblast'],['', 'Kabardino-Balkarian Republic'],['', 'Kaliningrad Oblast'],['', 'Kaluga Oblast'],['', 'Kamchatka Krai'],['', 'Karachay–Cherkess Republic'],['', 'Kemerovo Oblast'],['', 'Khabarovsk Krai'],['', 'Khanty-Mansi Autonomous Okrug'],['', 'Kirov Oblast'],['', 'Komi Republic'],['', 'Kostroma Oblast'],['', 'Krasnodar Krai'],['', 'Krasnoyarsk Krai'],['', 'Kurgan Oblast'],['', 'Kursk Oblast'],['', 'Leningrad Oblast'],['', 'Lipetsk Oblast'],['', 'Magadan Oblast'],['', 'Mari El Republic|Moscow'],['', 'Moscow Oblast'],['', 'Murmansk Oblast'],['', 'Nizhny Novgorod Oblast'],['', 'Novgorod Oblast'],['', 'Novosibirsk Oblast'],['', 'Omsk Oblast'],['', 'Orenburg Oblast'],['', 'Oryol Oblast'],['', 'Penza Oblast'],['', 'Perm Krai'],['', 'Primorsky Krai'],['', 'Pskov Oblast'],['', 'Republic of Adygeya'],['', 'Republic of Bashkortostan'],['', 'Republic of Buryatia'],['', 'Republic of Dagestan'],['', 'Republic of Ingushetia'],['', 'Republic of Kalmykia'],['', 'Republic of Karelia'],['', 'Republic of Khakassia'],['', 'Republic of Mordovia'],['', 'Republic of North Ossetia–Alania'],['', 'Republic of Tatarstan'],['', 'Rostov Oblast'],['', 'Ryazan Oblast'],['', 'Saint Petersburg'],['', 'Sakha Republic (Yakutia)'],['', 'Sakhalin Oblast'],['', 'Samara Oblast'],['', 'Saratov Oblast'],['', 'Smolensk Oblast'],['', 'Stavropol Krai'],['', 'Sverdlovsk Oblast'],['', 'Tambov Oblast'],['', 'Tomsk Oblast'],['', 'Tula Oblast'],['', 'Tver Oblast'],['', 'Tyumen Oblast'],['', 'Tyva Republic'],['', 'Udmurtia'],['', 'Ulyanovsk Oblast'],['', 'Vladimir Oblast'],['', 'Volgograd Oblast'],['', 'Vologda Oblast'],['', 'Voronezh Oblast'],['', 'Yamalo-Nenets Autonomous Okrug'],['', 'Yaroslavl Oblast'],['', 'Zabaykalsky Krai'],
], ],[
	'Rwanda', [      //179
	['', 'State/Province'],
], ],[
	'Samoa', [      //186
	['', 'State/Province'],
], ],[
	'San Marino', [      //187
	['', 'State/Province'],
], ],[
	'Sao Tome And Principe', [      //188
	['', 'State/Province'],
], ],[
	'Saudi Arabia', [      //189
	['', 'State/Province'],
], ],[
	'Senegal', [      //190
	['', 'State/Province'],
], ],[
	'Serbia', [      //191
	['', 'State/Province'],
], ],[
	'Seychelles', [      //192
	['', 'State/Province'],
], ],[
	'Sierra Leone', [      //193
	['', 'State/Province'],
], ],[
	'Singapore', [      //194
	['', 'State/Province'],
], ],[
	'Sint Maarten', [      //195
	['', 'State/Province'],
], ],[
	'Slovakia', [      //196
	['', 'State/Province'],
], ],[
	'Slovenia', [      //197
	['', 'State/Province'],
], ],[
	'Solomon Islands', [      //198
	['', 'State/Province'],
], ],[
	'Somalia', [      //199
	['', 'State/Province'],
], ],[
	'South Africa', [      //200
	['', 'Eastern Cape'],['', 'Free State'],['', 'Gauteng'],['', 'KwaZulu-Natal'],['', 'Limpopo'],['', 'Mpumalanga'],['', 'North West'],['', 'Northern Cape'],['', 'Western Cape'],
], ],[
	'South Georgia And The South Sandwich Islands', [      //201
	['', 'State/Province'],
], ],[
	'South Korea', [      //202
	['', 'State/Province'],
	['', 'Busan'],['', 'Chungbuk'],['', 'Chungnam'],['', 'Daegu'],['', 'Daejeon'],['', 'Gangwon'],['', 'Gwangju'],['', 'Gyeongbuk'],['', 'Gyeonggi'],['', 'Gyeongnam'],['', 'Incheon'],['', 'Jeju'],['', 'Jeonbuk'],['', 'Jeonnam'],['', 'Sejong'],['', 'Seoul'],['', 'Ulsan'],
], ],[
	'South Sudan', [      //203
	['', 'State/Province'],
], ],[
	'Spain', [      //204
	['', 'A Coruña'],['', 'Álava'],['', 'Albacete'],['', 'Alicante'],['', 'Almería'],['', 'Asturias'],['', 'Ávila'],['', 'Badajoz'],['', 'Balears'],['', 'Barcelona'],['', 'Burgos'],['', 'Cáceres'],['', 'Cádiz'],['', 'Cantabria'],['', 'Castellón'],['', 'Ceuta'],['', 'Ciudad Real'],['', 'Córdoba'],['', 'Cuenca'],['', 'Girona'],['', 'Granada'],['', 'Guadalajara'],['', 'Guipúzcoa'],['', 'Huelva'],['', 'Huesca'],['', 'Jaén'],['', 'La Rioja'],['', 'Las Palmas'],['', 'León'],['', 'Lleida'],['', 'Lugo'],['', 'Madrid'],['', 'Málaga'],['', 'Melilla'],['', 'Murcia'],['', 'Navarra'],['', 'Ourense'],['', 'Palencia'],['', 'Pontevedra'],['', 'Salamanca'],['', 'Santa Cruz de Tenerife'],['', 'Segovia'],['', 'Sevilla'],['', 'Soria'],['', 'Tarragona'],['', 'Teruel'],['', 'Toledo'],['', 'Valencia'],['', 'Valladolid'],['', 'Vizcaya'],['', 'Zamora']['', 'Zaragoza'],
], ],[
	'Sri Lanka', [      //205
	['', 'State/Province'],
], ],
[
	'St. Barthélemy', [      //205
	['', 'State/Province'],
], ],
[
	'St. Helena', [      //205
	['', 'State/Province'],
], ],
[
	'St. Kitts & Nevis', [      //205
	['', 'State/Province'],
], ],
[
	'St. Lucia', [      //205
	['', 'State/Province'],
], ],
[
	'St. Martin', [      //205
	['', 'State/Province'],
], ],
[
	'St. Pierre &amp; Miquelon', [      //205
	['', 'State/Province'],
], ],
[
	'St. Vincent & Grenadines', [      //206
	['', 'State/Province'],
], ],[
	'Sudan', [      //207
	['', 'State/Province'],
], ],[
	'Suriname', [      //208
	['', 'State/Province'],
], ],[
	'Svalbard And Jan Mayen', [      //209
	['', 'State/Province'],
], ],[
	'Sweden', [      //210
	['', 'State/Province'],
], ],[
	'Switzerland', [      //211
	['', 'State/Province'],
], ],[
	'Syria', [      //212
	['', 'State/Province'],
], ],[
	'Taiwan', [      //213
	['', 'State/Province'],
], ],[
	'Tajikistan', [      //214
	['', 'State/Province'],
], ],[
	'Tanzania, United Republic Of', [      //215
	['', 'State/Province'],
], ],[
	'Thailand', [      //216
	['', 'Amnat Charoen'],['', 'Ang Thong'],['', 'Bangkok'],['', 'Bueng Kan'],['', 'Buriram'],['', 'Chachoengsao'],['', 'Chai Nat'],['', 'Chaiyaphum'],['', 'Chanthaburi'],['', 'Chiang Mai'],['', 'Chiang Rai'],['', 'Chon Buri'],['', 'Chumphon'],['', 'Kalasin'],['', 'Kamphaeng Phet'],['', 'Kanchanaburi'],['', 'Khon Kaen'],['', 'Krabi'],['', 'Lampang'],['', 'Lamphun'],['', 'Loei'],['', 'Lopburi'],['', 'Mae Hong Son'],['', 'Maha Sarakham'],['', 'Mukdahan'],['', 'Nakhon Nayok'],['', 'Nakhon Pathom'],['', 'Nakhon Phanom'],['', 'Nakhon Ratchasima']['', 'Nakhon Sawan'],['', 'Nakhon Si Thammarat'],['', 'Nan'],['', 'Narathiwat'],['', 'Nong Bua Lam Phu'],['', 'Nong Khai'],['', 'Nonthaburi'],['', 'Pathum Thani'],['', 'Pattani'],['', 'Pattaya'],['', 'Phangnga'],['', 'Phatthalung'],['', 'Phayao'],['', 'Phetchabun'],['', 'Phetchaburi'],['', 'Phichit'],['', 'Phitsanulok'],['', 'Phra Nakhon Si Ayutthaya'],['', 'Phrae'],['', 'Phuket|Prachin Buri'],['', 'Prachuap Khiri Khan'],['', 'Ranong'],['', 'Ratchaburi'],['', 'Rayong'],['', 'Roi Et'],['', 'Sa Kaeo'],['', 'Sakon Nakhon'],['', 'Samut Prakan'],['', 'Samut Sakhon'],['', 'Samut Songkhram'],['', 'Saraburi'],['', 'Satun'],['', 'Sing Buri'],['', 'Sisaket'],['', 'Songkhla'],['', 'Sukhothai'],['', 'Suphan Buri'],['', 'Surat Thani'],['', 'Surin'],['', 'Tak'],['', 'Trang'],['', 'Trat'],['', 'Ubon Ratchathani'],['', 'Udon Thani'],['', 'Uthai Thani'],['', 'Uttaradit'],['', 'Yala'],['', 'Yasothon'],
], ],[
	'Timor Leste', [      //217
	['', 'State/Province'],
], ],[
	'Togo', [      //218
	['', 'State/Province'],
], ],[
	'Tokelau', [      //219
	['', 'State/Province'],
], ],[
	'Tonga', [      //220
	['', 'State/Province'],
], ],[
	'Trinidad and Tobago', [      //221
	['', 'State/Province'],
], ],[
	'Tristan da Cunha', [      //222
	['', 'State/Province'],
], ],[
	'Tunisia', [      //223
	['', 'State/Province'],
], ],[
	'Turkey', [      //224
	['', 'State/Province'],
], ],[
	'Turkmenistan', [      //225
	['', 'State/Province'],
], ],[
	'Turks and Caicos Islands', [      //226
	['', 'State/Province'],
], ],[
	'Tuvalu', [      //227
	['', 'State/Province'],
], ],[
	'Uganda', [      //228
	['', 'State/Province'],
], ],[
	'Ukraine', [      //229
	['', 'State/Province'],
], ],[
	'United Arab Emirates', [      //230
	['', 'Abu Dhabi'],['', 'Ajman'],['', 'Dubai'],['', 'Fujairah'],['', 'Ras al-Khaimah'],['', 'Sharjah'],['', 'Umm al-Quwain'],
], ],[
	'United Kingdom', [      //231
	['', 'British Forces'],['', 'England'],['', 'Northern Ireland'],['', 'Scotland'],['', 'Wales'],
], ],[
	'United States', [      //232
	['', 'Alabama'],['', 'Alaska'],['', 'American Samoa'],['', 'Arizona'],['', 'Arkansas'],['', 'Armed Forces Americas'],['', 'Armed Forces Europe'],['', 'Armed Forces Pacific'],['', 'California'],['', 'Colorado'],['', 'Connecticut'],['', 'Delaware'],['', 'District of Columbia'],['', 'Federated States of Micronesia'],['', 'Florida'],['', 'Georgia'],['', 'Guam'],['', 'Hawaii'],['', 'Idaho'],['', 'Illinois'],['', 'Indiana'],['', 'Iowa'],['', 'Kansas'],['', 'Kentucky'],['', 'Louisiana'],['', 'Maine'],['', 'Marshall Islands'],['', 'Maryland'],['', 'Massachusetts'],['', 'Michigan'],['', 'Minnesota'],['', 'Mississippi'],['', 'Missouri'],['', 'Montana'],['', 'Nebraska'],['', 'Nevada'],['', 'New Hampshire'],['', 'New Jersey'],['', 'New Mexico'],['', 'New York'],['', 'North Carolina'],['', 'North Dakota'],['', 'Northern Mariana Islands'],['', 'Ohio'],['', 'Oklahoma'],['', 'Oregon'],['', 'Palau'],['', 'Pennsylvania'],['', 'Puerto Rico'],['', 'Rhode Island'],['', 'South Carolina'],['', 'South Dakota'],['', 'Tennessee'],['', 'Texas'],['', 'Utah'],['', 'Vermont'],['', 'Virgin Islands'],['', 'Virginia'],['', 'Washington'],['', 'West Virginia'],['', 'Wisconsin'],['', 'Wyoming'],
], ],[
	'United States Minor Outlying Islands', [      //233
	['', 'State/Province'],
], ],[
	'Uruguay', [      //234
	['', 'State/Province'],
], ],[
	'Uzbekistan', [      //235
	['', 'State/Province'],
], ],[
	'Vanuatu', [      //236
	['', 'State/Province'],
], ],[
	'Venezuela', [      //237
	['', 'State/Province'],
], ],[
	'Vietnam', [      //238
	['', 'State/Province'],
], ],[
	'Wallis And Futuna', [      //240
	['', 'State/Province'],
], ],[
	'Western Sahara', [      //241
	['', 'State/Province'],
], ],[
	'Yemen', [      //242
	['', 'State/Province'],
], ],[
	'Zambia', [      //243
	['', 'State/Province'],
], ],[
	'Zimbabwe', [      //244
	['', 'State/Province'],
], ]
	   ];

		var countryElement = document.getElementById('shipCountry');
		var stateElement = document.getElementById('shipProvince');
		var stateLabelElement = document.getElementById('shipProvinceLabel');
		let selectedProvince = document.getElementById("shipProvince").getAttribute("data-province");
	if (countryElement && stateElement) {
			var listOfState = [
				['XX', 'None']
			];

			var currentCountry = countryElement.options[countryElement.selectedIndex].value;
			for (var i = 0; i < countryState.length; i++) {
				if (currentCountry == countryState[i][0]) {
					listOfState = countryState[i][1];
				}
			}
			if (listOfState.length < 2){
				stateElement.style.display = 'none';
				stateLabelElement.style.display = 'none';
			}else{
				stateElement.style.display = 'inline';
				stateLabelElement.style.display = 'inline';
			}

			stateElement.options.length = 0;
			for (var i = 0; i < listOfState.length; i++) {
				stateElement.options[i] = new Option(listOfState[i][1], listOfState[i][1]);
				if (listOfState[i][1] == selectedProvince) {
					stateElement.options[i].selected = true;
				}
			}
		}
	}
  			function form_serializeObject(formid){
				var data = {};
				var dataArray = new FormData(document.getElementById(formid));
				for (var pair of dataArray.entries()) {
					data[pair[0]] =  pair[1];
				}
				return data;
			}
				//skip order start here
				async function skipContractOrder(skipDate,billing_policy_value,delivery_billing_type,contractId){
					var adminEmail = document.getElementById('sendMailToAdmin').value;
					var customerEmail = document.getElementById('sendMailToCustomer').value;
						let ajaxParameters = {
							method:"POST",
							dataValues:{
								contract_id:contractId,
								skipOrderDate:skipDate,
								billing_policy_value:billing_policy_value,
								delivery_billing_type:delivery_billing_type,
								customerEmail:customerEmail,
								adminEmail:adminEmail,
								skippedFrom:'Admin',
								'specific_contract_data':specific_contract_data,
								'contract_product_details':contract_existing_products,
								action:"skipOrder"
							}
						};
						try{
							 result = await AjaxCall(ajaxParameters);
							 document.getElementById('sd_subscriptionLoader').classList.add('display-hide-label');
							 if(result['status'] == true){
								displayMessage(result['message'],'success');
								location.reload();
							}else{
								displayMessage(result['message'],'error');
								location.reload();
							}
						}catch(e){
							document.getElementById('sd_subscriptionLoader').classList.add('display-hide-label');
							displayMessage(e,'error');
						}

				}

            // change contract status or delete contract
			    async function activePauseCancelSubscription(updateData){
					displayMessage('Subscription Updating','waiting');
					console.log('update_data', updateData);

						let ajaxParameters = {
							method:"POST",
							dataValues:{
								"statusChangeTo" : updateData[0],
								"contract_id":updateData[1],
								"adminEmail":updateData[2],
								"customerEmail":updateData[3],
								"next_billing_date" : updateData[4],
								"delivery_billing_type" : updateData[5],
								"billing_policy_value":updateData[6],
								"AjaxCallFrom":AjaxCallFrom,
								"specific_contract_data":specific_contract_data,
								'contract_product_details':contract_existing_products,
								action:"updateSubscriptionStatus"
							}
						};

						try{
							result = await AjaxCall(ajaxParameters);
							document.getElementById('sd_subscriptionLoader').classList.add('display-hide-label');
							if(result['status'] == true){
								if(updateData[0] == 'EXPIRED'){
									// redirect_link_global = "subscription/subscriptions.php";
									// redirectlink();
									// location.reload();
									open('/admin/subsrciption/subscriptions.php?shop='+store, '_self');
								}else{
									displayMessage(result['message'],'success');
									location.reload();
								}
							}else{
								displayMessage(result['message'],'error');
								location.reload();
							}
						}catch(e){
							document.getElementById('sd_subscriptionLoader').classList.add('display-hide-label');
							displayMessage(e,'error');
						}
				}

			//    update payment method of the customer
				async function updatePaymentMethod(){
				displayMessage('Mail Sending','waiting');
				var paymentMethodToken = document.getElementById('updatePaymentMethod').getAttribute('data-paymentToken');
				var sendMailToCustomer = document.getElementById('sendMailToCustomer').value;
					let ajaxParameters = {
						method:"POST",
						dataValues:{
							"token":paymentMethodToken,
						    "email":sendMailToCustomer,
							action:"updatePaymentMethod"
						}
					};
					try{
						result = await AjaxCall(ajaxParameters);
					}catch(e){
						displayMessage(e,'error');
					}
				if(result['status'] == true){
					let add_class_params = {
						class_elements:[
							{
								name:'Polaris-Frame-Toast',
								classname:"display-hide-label"
							}
						]
					};
					add_class(add_class_params);
					displayMessage(result['message'],'success');
					document.getElementById('updatePaymentMethod').remove();
					document.getElementById('updateMailSent').classList.remove('display-hide-label');
				}else{
					displayMessage(result['message'],'error');
				}
			}

			// update cancel shipping address
			function hideUpdateShipping(class1,class2,class3){
				let add_class_params = {
					class_elements:[
						{
							name:class1,
							classname:"display-hide-label"
						},{
							name:class2,
							classname:"display-hide-label"
						},{
							name:class3,
							classname:"display-hide-label"
						}
					]
				};
				add_class(add_class_params);
			}

			function showUpdateShipping(class1,class2,class3){
				let remove_class_params = {
					class_elements:[
						{
							name:class1,
							classname:"display-hide-label"
						},{
							name:class2,
							classname:"display-hide-label"
						},{
							name:class3,
							classname:"display-hide-label"
						}
					]
				};
				remove_class(remove_class_params);

			}

			function check_discount_value(discount_value,selected_option){
				if(discount_value == 0){
					displayMessage('Enter Discount Value','error');
					return false;
				}else if(selected_option == 'Percent Off(%)'){
                    if(discount_value > 100){
						displayMessage('Percentage Value cannot be greater than 100','error');
						return false;
					}
				}
               return true;
			}

/* -------------------------- Only backend JS Functions Start ---------------------------*/
	function escapeHtmlToCode(text) {
		if(text){
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

	function escapeCodeToHtml(text) {
		return text
			.replace(/&/g, "amp;")
			.replace(/</g, "lt;")
			.replace(/>/g, "gt;")
			.replace(/"/g, "quot;")
			.replace(/'/g, "#039;");
	}
	function escapeCodeToHtml_template(text){
		return text
		.replace(/'/g, "#039;");
	}


	function remove_all_error_messages(){
		let add_class_params = {
			class_elements:[
				{
					name:"Polaris-Labelled__Error",
					classname:"display-hide-label"
				},
				{
					name:"frequency-plan-error",
					classname:"display-hide-label"
				}
			]
		};
		add_class(add_class_params);
	}

	function show_save_banner(){
		document.getElementById('edit-plan-save-banner').classList.remove('display-hide-label');
		document.querySelector('#edit-plan-save-banner .save-subscription-plan').classList.remove('btn-disable-loader');
	}

	function hide_save_banner(){
		document.getElementById('edit-plan-save-banner').classList.add('display-hide-label');
	}

	function check_subscription_whole_form_change() {
		if (form_type == 'edit') {
			Object.keys(picker_selection_checkboxes).forEach(function (key) {
				delete picker_selection_checkboxes[key].product_handle;
				delete picker_selection_checkboxes[key].price;
				delete picker_selection_checkboxes[key].quantity;
			});
			let checkproductupdate = Object.keys(sd_subscription_edit_case_already_selected_products)
                 .filter(x => !(Object.keys(picker_selection_checkboxes)).includes(x))
				 .concat(Object.keys(picker_selection_checkboxes).filter(x => !(Object.keys(sd_subscription_edit_case_already_selected_products)).includes(x)));
			let check_existing_plan_change = JSON.stringify(sd_subscription_edit_case_already_existing_plans_array) == JSON.stringify(sd_subscription_edit_case_already_existing_plans_array_validation_check);
			group_name = document.querySelectorAll('#edit_subscription_wrapper .subscription_plan_name')[0].value; //check if group name changed
			if (Object.keys(sd_subscription_edit_case_to_be_deleted_plans_array).length != 0 ||
				Object.keys(sd_subscription_edit_case_to_be_added_new_plans_array).length != 0 || checkproductupdate.length != 0 || check_existing_plan_change == false || (initial_group_name != group_name && group_name != '')) {
				return true;
			} else {
				return false;
			}
		}else if(form_type == 'create'){
			plan_group_name = document.getElementById('subscription_plan_name').value;
			if (Object.keys(picker_selection_checkboxes).length != 0 || Object.keys(sd_frequency_plans_array).length != 0 || plan_group_name != ''){
				return true;
			} else {
				return false;
			}
		}
	}

 	async function deleteSubscriptionPlan(ids,tablename){
		document.getElementById("sd_subscriptionLoader").classList.remove('display-hide-label');
		displayMessage('Deleting','waiting');
		let whereconditionvalues = {
			 "store_id":store_id,
			 "subscription_plangroup_id":ids
		}
		let wheremodevalue = "and";
		let ajaxParameters = {
			method:"POST",
			dataValues:{
				action:"deletesubscription",
				table:tablename,
				wheremode:wheremodevalue,
				wherecondition:whereconditionvalues
			}
		};
		try{
				let result = await AjaxCall(ajaxParameters);
				if(result['status'] == true){
					document.getElementById('subscription_list_'+ids).remove();
					var subscription_list_card = document.getElementsByClassName('subscription-list-card');
					if (subscription_list_card.length == 0) { // if no subscription plan group exist then show the
						location.reload();
					}
					displayMessage(result['message'],'sucess');
				}else{
					if(result['message'] == '404'){
						displayMessage('Plan does not exist', 'error');
						location.reload();
					}else{
						displayMessage(result['message'], 'error');
					}
				}
				list_subscription_mode();
				document.getElementById("sd_subscriptionLoader").classList.add('display-hide-label');
		}
		catch(e){
			document.getElementById("sd_subscriptionLoader").classList.add('display-hide-label');
				 displayMessage(e,'error');
		}
	}

	function list_subscription_mode(){
		reset_all_subscription_variables();
		close_product_picker();
		reset_product_picker();
		reset_product_picker_html();
		document.getElementById("create_subscription_wrapper").classList.add("display-hide-label");
		document.getElementById('edit_subscription_wrapper').className = '';
		document.getElementById("edit_subscription_wrapper").classList.add("display-hide-label");
		document.getElementById("list_subscription_wrapper").classList.remove("display-hide-label");
		document.getElementById("no_search_result").classList.add("display-hide-label");

		let remove_class_params = {
					class_elements:[
						{
							name:"subscription-list-card",
							classname:"display-hide-label"
						}
					]
		};
		remove_class(remove_class_params);
		document.getElementsByClassName("top-banner-create-subscription")[0].classList.remove("display-hide-label");
		if(picker_display_wrapper == 'subscription_edit_prodcts'){
		var back_button_subscription = document.getElementById("edit-plan-save-banner");
		back_button_subscription.classList.add("display-hide-label");
		}
		document.getElementById("search-subscription-text").value='';
	}

	function create_subscription_mode(){
		reset_all_subscription_variables();
		close_product_picker();
		reset_product_picker();
		// empty preview of both select
		document.getElementById('sd_prepaid_list').innerHTML = '';
		document.getElementById('sd_ppd_list').innerHTML = '';
		document.getElementById("create-section-products-show").innerHTML = '';
		document.getElementById("ppd_discount").innerHTML = '';
		document.getElementById("prepaid_discount").innerHTML = '';
		document.getElementById("create_subscription_wrapper").classList.remove("display-hide-label");
		document.getElementById('edit_subscription_wrapper').className = '';
		document.getElementById("edit_subscription_wrapper").classList.add("display-hide-label");
		document.getElementById("list_subscription_wrapper").classList.add("display-hide-label");
		document.getElementsByClassName("top-banner-create-subscription")[0].classList.add("display-hide-label");
		document.getElementById("no_search_result").classList.add("display-hide-label");
		if(picker_display_wrapper == 'subscription_edit_prodcts'){
		var back_button_subscription = document.getElementById("edit-plan-save-banner");
		back_button_subscription.classList.add("display-hide-label");
		}
	}

	function list_close_all_mini(){
		let add_class_params = {
			class_elements:[
				{
					name:"subscription_mini_inner_wrapper",
					classname:"display-hide-label"
				}
			]
		};
		add_class(add_class_params);
   }

   function reset_plan_name_change(){
		let add_class_params = {
			class_elements:[
				{
					name:"planname_input_wrapper",
					classname:"display-hide-label"
				}
			]
		};
		add_class(add_class_params);
		let remove_class_params = {
			class_elements:[
				{
					name:"change_plan_name",
					classname:"display-hide-label"
				},
				{
					name:"subscription_heading",
					classname:"display-hide-label"
				}
			]
		};
		remove_class(remove_class_params);
		document.getElementsByClassName('subscription_plan_name')[0].value = '';
	}

	function edit_subscription_mode(){
		reset_all_subscription_variables();
		reset_plan_name_change();
		close_product_picker();
		reset_product_picker();
		reset_product_picker_html();
		document.getElementById("create_subscription_wrapper").classList.add("display-hide-label");
		document.getElementById('edit_subscription_wrapper').className = '';
		document.getElementById("list_subscription_wrapper").classList.add("display-hide-label");
		document.getElementsByClassName("top-banner-create-subscription")[0].classList.add("display-hide-label");
		document.getElementById("no_search_result").classList.add("display-hide-label");
		var back_button_subscription = document.getElementById("edit-plan-save-banner");
		back_button_subscription.classList.add("display-hide-label");
	}

	function show_title(x){
		x.nextElementSibling.classList.remove('display-hide-label');
	}

	function hide_title(x){
		x.nextElementSibling.classList.add('display-hide-label');
	}
	function close_sd_popup(){
		document.getElementById('sd_popup').classList.add('display-hide-label');
		document.getElementById("sd_global_modal_container").innerHTML = '';
		remove_disable_class();
	}

	function remove_disable_class(){
		// Finding all elements of a class (creates an array of results)
		let x = document.getElementsByClassName('btn-disable-loader');
		// If it exists, remove it.
		if(x.length > 0) { x[0].classList.remove('btn-disable-loader'); }
	}

	function close_toast(){
		let add_class_params = {
			class_elements:[
				{
					name:"Polaris-Frame-Toast",
					classname:"display-hide-label"
				}
			]
		};
		add_class(add_class_params);
	}

	function reset_all_subscription_variables(){
		sd_subscription_selected_products=[];
		sd_subscription_edit_case_already_selected_products_array=[];
		sd_frequency_card_serialno = 0;
		sd_subscription_edit_case_already_existing_plans_array= {};
		sd_subscription_edit_case_already_selected_products = [];
		sd_subscription_edit_case_to_be_deleted_plans_array =[];
		sd_subscription_edit_case_to_be_added_new_plans_array = [];
		sd_frequency_plans_array = [];
		db_edit_subscriptionplan_id , form_type ;
	}

	// function redirectlink(){
	// 	window.top.location.href = SHOPIFY_DOMAIN_URL+ "/admin/"+redirect_link_global+"?shop="+store+redirect_query_string;
	// }

	function goBack(thisData){
		let redirect_contract_url = new URL(window.location.href);
		redirect_contract_url.searchParams.delete('sd_contract_id', '');
		window.location.replace(redirect_contract_url);
	}


	//  bonne custom work 

	jQuery('body').on('click', '.sd_custom-product-action', async function () {
		$('.sd-replace-btn-error').css({display: 'none'});
		let loopRun = 0;
		let all_contractIds = [];
	
		$('.sd_subscription-checkbox:checked').each(function(){
			loopRun++;
			all_contractIds.push($(this).attr('data-query-string'))
		});
	
		if (!loopRun) {
			$('.sd-replace-btn-error').css({display: 'block'}).text('Select any checkbox one of these');
			return false;
		}
		let storeValue = $('#store').val();
		$('.sd_bone_loader').css({display: 'flex'});
		$.ajax({
			type: "POST",
			url: 'product-data.php',
			data: {
				'all_contractIds': all_contractIds,
				'store_id': store_id,
				'store': storeValue
			},
			success: function(response) {
			$('.sd_bone_loader').css({display: 'none'});
				const responseObject = JSON.parse(response);
				let dataArray = responseObject.store_all_subscription_plans;
				let getContractDataArray = responseObject.getContractDataArray;
				let groupedData ={}
				let letBillingData ={}
				dataArray.forEach(subArray => {
					// Sub array ko iterate karein
					subArray.forEach(obj => {
						const contractId = obj.contract_id;
						if (!groupedData.hasOwnProperty(contractId)) {
							groupedData[contractId] = [];
						}
						groupedData[contractId].push(obj);
					});
				});
	
				getContractDataArray.forEach(subArray1 => {
					// Sub array ko iterate karein
					subArray1.forEach(obj => {
						const contractId = obj.contract_id;
						if (!letBillingData.hasOwnProperty(contractId)) {
							letBillingData[contractId] = [];
						}
						letBillingData[contractId].push(obj);
					});
				});
	
				// console.log('letBillingData',letBillingData);
				// console.log('groupedData',groupedData);
				// return false;
				
				product_resourcePicker(groupedData,letBillingData,all_contractIds);
			},
			error: function(xhr, status, error) {
			$('.sd_bone_loader').css({display: 'none'});
				console.error('AJAX request failed:', status, error);
			}
		});
	});
	
	async function product_resourcePicker(dataArray,letBillingData,all_contractIds){
		const selected =  await shopify.resourcePicker({
			type: 'variant',
			action: 'select',
			selectionIds: [],
			multiple : false,
			filter: {
				hidden: true,
				draft: true,
				archived:true
			},
		});
		if(selected.length > 0){
			console.log(selected);
			
			shopify.loading(true);
			let newProductObj = [];
			for (let a = 0; a < selected.length; a++) {
				let inventoryQuantity = selected[a]['inventoryQuantity'];
				if(inventoryQuantity < 1){
					displayMessage('Product is out of stock pick another one..', 'success');
					shopify.loading(false);
					return false;
				}
				let variant_id = selected[a]['id'];
				let variant_title = selected[a]['title'];
				let product_id = selected[a]['product']['id'];
				let product_title = selected[a]['displayName'];
				product_title = product_title.replace("- "+variant_title, "")
				let image = selected[a]['image'] ? selected[a]['image']['originalSrc'] : '';
				let variant_price = selected[a]['price'];
				let variant_quantity = selected[a]['inventoryQuantity'];
				let variant_sku = selected[a]['sku'];
				
				// Push values into newProductObj
				newProductObj.push({
					product_id: product_id.replace("gid://shopify/Product/", ""),
					product_title: product_title,
					variant_id: variant_id.replace("gid://shopify/ProductVariant/", ""),
					variant_title: variant_title,
					price: variant_price,
					image: image,
					sku: variant_sku,
					quantity: variant_quantity
				});	
			}
			displayMessage("Please wait a few seconds while updating...", 'productUpdate');
			let ajaxParameters = {
				method:"POST",
				dataValues:{
					action:"getReplaceAllProducts",
					newProductObj:newProductObj,
					oldData:dataArray,
					contract_details:letBillingData,
					all_contractIds : all_contractIds
				}
			};
			try{
				$('.sd_bone_loader').css({display: 'flex'});
				ajaxResult = await AjaxCall(ajaxParameters);
				shopify.loading(false);
				$('.sd_bone_loader').css({display: 'none'});
	
				shopify.toast.hide();
				let allProducts = JSON.parse(ajaxResult.allProductsArray);
				if (allProducts.status === true) {
					displayMessage(allProducts.message, 'success');
				} else {
					displayMessage(allProducts.message, 'error'); 
				}
				location.reload();
			}catch(e){
			('.sd_bone_loader').css({display: 'none'});
	
				shopify.loading(false);
				return false;
			}
		}
	}
	jQuery('body').on('change', '#sd_subscription-select-all', function() {
		
		// let dropdown = $("select[name='subscriptionTable_length']");
		// let selectedOption = dropdown.find("option:selected");
		// let selectedValue = parseInt(selectedOption.val());
	
		if(jQuery(this).prop('checked')) {
			// Check the first 10 checkboxes
			jQuery('.sd_subscription-checkbox:lt(10)').prop('checked', true);
			$('.sd-item-swipe-btn').show();
		} else {
			jQuery('.sd_subscription-checkbox').prop('checked', false);
			$('.sd-item-swipe-btn').hide();
		}
	});
/* -------------------------- Only backend JS Functions End ---------------------------*/
