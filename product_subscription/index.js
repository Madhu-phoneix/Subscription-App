import {
  BlockStack,
  Button,
  Card,
  Checkbox,
  InlineStack,
  Text,
  TextBlock,
  TextField,
  extend,
  Select,
  Heading,
  ResourceItem,
  ResourceList,
  Banner,
  Modal,
  CardSection,
  Spinner,
  StackItem,
} from '@shopify/admin-ui-extensions';

const translations = {
  de: {
    hello: 'Guten Tag',
  },
  en: {
    hello: 'Hello',
  },
  fr: {
    hello: 'Bonjour',
  },
};
//global declaration of all the fields
let check_validation = true, planNameField, PayperFrequencyField, discount_textField, PrepaidFrequencyField, selling_plan_block_Stack,payper_prepaid_delivery_value,selectDelievryMethod,closeButton,delivery_frequency_type_options,billing_frequency_type,delivery_frequency_type,selling_plan_options,payper_prepaid_frequency_options,inlineStack3,selectFields,discountCheckbox,checkbox_component,DiscountOptions,discountOptions,offer_discount_stack,selectDiscountMethod,discount_field_and_type_stack,add_button_stack,add_selling_plan_Button,create_cancel_button_stack,textField,heading_component,cardComponent,planDetailsCard,create_text,block_Stack_component,selling_plan_fields_block,delete_plan_button_stack,allSellingPlanArray = [],selling_plan_index=0,group_name_textField,createSellingPlanObject = {},subscription_group = [],headingComponent,bannerText,store_name,base64JWT_to_string,parsed_JWT,all_subscription_plan_groups,selected_subscription_plan_group = [],headingText,group_name_stack,groupNameCard_stack,group_name_block_stack,groupNameCard,sd_subscription_edit_case_already_existing_plans_array = {},sd_subscription_edit_case_to_be_added_new_plans_array = [],sd_subscription_edit_case_to_be_deleted_plans_array = [],current_edit_selling_plan = {},cancelUpdatePlanButton,selling_plan_modal,update_button_stack,update_selling_plan_Button,selling_plan_object_index,check_duplicate_selling_plan,selling_plan_form_card,all_created_selling_plans,selling_plan_heading_stack,recurring_offer_discount_stack,recurringDiscountCheckbox,recurring_discount_field_and_type_stack,discount_textField_after,recurringSelectDiscountMethod,min_max_cycle_stack,minCycleField,maxCycleField,change_discount_after_cycle_textfield,after_cycle_stack,prepaid_delivery='',delivery_every,selling_plan_name,check_duplicate_selling_plan_name,shop_currency = '',discount_symbol,discount_symbol_after,discount_after,discount_applied,delete_selling_plan_modal,delete_selling_plan_form_card,cancelDeletePlanButton,confirm_delete_selling_plan_Button,confirm_button_stack,frequency_card_name,selling_plan_group_id,subscription_plan_group_detail,disable_delete_button = false,confirm_delete_plan = 'Yes',selling_plan_error,page_type,selling_plan_id,show_message,block_alignment;
let ajax_url = 'https://your-domain.com/application/controller/ajaxhandler.php';
// 'Add' mode should allow a user to add the current product to an existing selling plan
// [Shopify admin renders this mode inside a modal container]
async function Add(root, api) {
  const data = api.data;
  const locale = api.locale.initialValue;
  const localizedStrings = translations[locale] || translations.en;
  const sessionToken = api.sessionToken;
  const {close, done, setPrimaryAction, setSecondaryAction} = api.container;
  const token = await sessionToken.getSessionToken();
    validate_token(token).then(response => {
      if (response.ok) {
        response.json().then(json => {
          const inlineStack = root.createComponent(InlineStack);
          if(json.status == true){
            if(token == json.decoded_token){
                store_name = json.store_name;
                // get all the selling plan group from database
                fetch(ajax_url+'?action=product_app_extension_fetchExistingGroup&store='+store_name,{
                  method: 'GET',
                  headers: {Accept: 'application/json', },
                }).then(response => {
                  if (response.ok) {
                    response.json().then(json => {
                      all_subscription_plan_groups = json.subscription_plan_groups;
                        // Configure the extension container UI
                      setPrimaryAction({
                      content: 'Add to plan',
                      onAction: async () => {
                        // Get a fresh session token before every call to your app server.
                        let plan_group_data = {};
                        plan_group_data['product_id'] = data.productId;
                        plan_group_data['variant_id'] = data.variantId;
                        plan_group_data['group_id_array'] = selected_subscription_plan_group;
                        fetch(ajax_url+'?action=product_app_extension_addExistingGroup&store='+store_name,{
                          method: 'POST',
                          headers: {Accept: 'application/json', },
                          body : JSON.stringify(Object.assign({}, plan_group_data)),
                        }).then(response => {
                          if (response.ok) {
                            response.json().then(json => {
                                if(json.status == true){
                                  done();
                                }else{
                                  done();
                                }
                            });
                          }
                        });
                      },
                    });
                setSecondaryAction({
                  content: 'Cancel',
                  onAction: () => close(),
                });

                const localizedHelloText = root.createComponent(TextBlock, {
                  size: 'Large',
                });
                root.appendChild(localizedHelloText);
                const inlineStack = root.createComponent(InlineStack);
                  if(all_subscription_plan_groups.length > 0){
                    localizedHelloText.appendChild(root.createText('Please Select Below Plans to add in this Product'));
                    all_subscription_plan_groups.forEach((plan) => {
                        const checkbox = root.createComponent(Checkbox, {
                          label: plan.plan_name,
                          checked: false,
                          onChange: (checked) => {
                            checkbox.updateProps({
                              checked,
                            });
                            if(checked == true){
                              selected_subscription_plan_group.push("gid://shopify/SellingPlanGroup/"+(plan.subscription_plangroup_id));
                            }else{
                              selected_subscription_plan_group = selected_subscription_plan_group.filter(item => item !== "gid://shopify/SellingPlanGroup/"+(plan.subscription_plangroup_id))
                            }
                          },
                        });
                        inlineStack.appendChild(checkbox);
                      });
                      root.appendChild(inlineStack);
                    }else{
                      const no_plan_exist = root.createComponent(TextBlock, {
                        size: 'Large',
                      });
                      no_plan_exist.appendChild(root.createText('No Plan exists'));
                      inlineStack.appendChild(no_plan_exist);
                      root.appendChild(inlineStack);
                    }  //
                    root.mount();
                    });
                  }
                });
            }else{
              const no_plan_exist = root.createComponent(TextBlock, {
                size: 'Large',
              });
              no_plan_exist.appendChild(root.createText('Page not found'));
              inlineStack.appendChild(no_plan_exist);
              root.appendChild(inlineStack);
              root.mount();
            }
          }else{
            const no_plan_exist = root.createComponent(TextBlock, {
              size: 'Large',
            });
            no_plan_exist.appendChild(root.createText('Page not found'));
            inlineStack.appendChild(no_plan_exist);
            root.appendChild(inlineStack);
            root.mount();
          }
        });
      }
    });
}

function createStack(root,api,component,alignment){
  let inlineStack = root.createComponent(InlineStack, {
    inlineAlignment: alignment,
  });
  component.appendChild(inlineStack);
  return inlineStack;
}

function appendError(fieldName,errorText){
  fieldName.updateProps({
     error : errorText
  });
  check_validation = false;
}

 function checkValidation(root,api){
  check_validation = true;
  planNameField.updateProps({ error : '',}); // remove plan name error
  PayperFrequencyField.updateProps({ // remove pay per delivery value error
    error : '',
  });
  discount_textField.updateProps({ // remove discount field error
    error : '',
  });
  PrepaidFrequencyField.updateProps({ // remove prepaid field error
    error : '',
  });
  minCycleField.updateProps({ // remove min cycle field error
    error : '',
  });
  maxCycleField.updateProps({ // remove max cycle field error
    error : ''
  });
  change_discount_after_cycle_textfield.updateProps({ // remove after cycle field error
    error : ''
  });
   discount_textField_after.updateProps({// remove after cycle field error
    error : ''
  });

  delivery_every = titleCase(createSellingPlanObject['per_delivery_order_frequency_type']);
  if(createSellingPlanObject['per_delivery_order_frequency_value'] != 1){
    delivery_every = createSellingPlanObject['per_delivery_order_frequency_value']+' '+titleCase(createSellingPlanObject['per_delivery_order_frequency_type']);
  }
  prepaid_delivery = '';
  if(createSellingPlanObject.frequency_plan_type == 'Prepaid'){
    prepaid_delivery =  createSellingPlanObject['prepaid_billing_value']+' '+titleCase(createSellingPlanObject['per_delivery_order_frequency_type'])+'(s) prepaid subscription,';
  }
  selling_plan_name = (prepaid_delivery+' Delivery Every '+delivery_every).trim();
  createSellingPlanObject['selling_plan_name'] = selling_plan_name;
  // return false;
  //empty errors start here
  let special_character_format =  /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
  if(Object.entries(current_edit_selling_plan).length > 0){
    // condition specially for edit case
    if(current_edit_selling_plan['frequency_plan_name'] == createSellingPlanObject['frequency_plan_name']){
      check_duplicate_selling_plan = false;
    }else{
      check_duplicate_selling_plan = allSellingPlanArray.some(el => el.frequency_plan_name === createSellingPlanObject['frequency_plan_name']);
    }

    //same plan name error
    if(current_edit_selling_plan['selling_plan_name'] == createSellingPlanObject['selling_plan_name']){
      check_duplicate_selling_plan_name = false;
    }else{
      check_duplicate_selling_plan_name = allSellingPlanArray.some(el => el.selling_plan_name === createSellingPlanObject['selling_plan_name']);
    }
  }else{
    check_duplicate_selling_plan = allSellingPlanArray.some(el => el.frequency_plan_name === createSellingPlanObject['frequency_plan_name']);
    check_duplicate_selling_plan_name = allSellingPlanArray.some(el => el.selling_plan_name === createSellingPlanObject['selling_plan_name']);
  }

  if(check_duplicate_selling_plan){
    appendError(planNameField,'Selling plan with same name already exist');
  }else
   if((createSellingPlanObject.frequency_plan_name).trim() == ''){
    appendError(planNameField,'Selling plan name is required.');
  }else if(parseInt(createSellingPlanObject.frequency_plan_name).length > 50){
    appendError(planNameField,'Selling plan name value should not be greater than 50 character.');
  }

 if(createSellingPlanObject.per_delivery_order_frequency_value == ''){
    appendError(PayperFrequencyField,'Delivery Period is required.');
  }else if(/[a-zA-Z]/.test(createSellingPlanObject.per_delivery_order_frequency_value) || special_character_format.test(createSellingPlanObject.per_delivery_order_frequency_value)){
    appendError(PayperFrequencyField,'Delivery period value should only contain numbers.');
  }else if(check_duplicate_selling_plan_name){
    appendError(PayperFrequencyField,'No two plan can have same value of delivery and billing policy.');
  }

  else if(createSellingPlanObject.per_delivery_order_frequency_value.length > 4){
    appendError(PayperFrequencyField,'Delivery period value should not be greater than 4 digits.');
  }else if(parseInt(createSellingPlanObject.per_delivery_order_frequency_value) < 0){
    appendError(PayperFrequencyField,'Delivery period value should not be greater than 0.');
  }
  //max number cycle validations
  if(createSellingPlanObject.maximum_number_cycle == ''){
    appendError(maxCycleField,'Maximum number of cycle value is required.');
  }else if(createSellingPlanObject.maximum_number_cycle == 0){
    appendError(maxCycleField,'Maximum number value must be greater than 0.');
  }else if(/[a-zA-Z]/.test(createSellingPlanObject.maximum_number_cycle) || special_character_format.test(createSellingPlanObject.maximum_number_cycle)){
    appendError(maxCycleField,'Maximum number value should only contain numbers.');
  }else if((createSellingPlanObject.maximum_number_cycle).length > 4){
    appendError(maxCycleField,'Maximum number value should not be greater than 4 digits.');
  }else if(parseInt(createSellingPlanObject.maximum_number_cycle) < parseInt(createSellingPlanObject.minimum_number_cycle)){
    appendError(maxCycleField,'The maximum number of cycles must be greater than the minimum.');
  }

  if(createSellingPlanObject.maximum_number_cycle != ''){
    if(/[a-zA-Z]/.test(createSellingPlanObject.minimum_number_cycle) || special_character_format.test(createSellingPlanObject.minimum_number_cycle)){
      appendError(minCycleField,'Minimum number value should only contain numbers.');
    }
  }

 // recurring discount validation
  if(createSellingPlanObject.subscription_discount_after == 'on'){
    //after cycle condition
    if(createSellingPlanObject.change_discount_after_cycle == ''){
      appendError(change_discount_after_cycle_textfield,'After cycle value is required.');
    }else if(createSellingPlanObject.change_discount_after_cycle == 0 || createSellingPlanObject.change_discount_after_cycle == '0'){
      appendError(change_discount_after_cycle_textfield,'After cycle value should be greater than 0.');
    }else if((createSellingPlanObject.change_discount_after_cycle).length > 4){
      appendError(change_discount_after_cycle_textfield,'After cycle value should not be greater than 4 digits.');
    }else if(/[a-zA-Z]/.test(createSellingPlanObject.change_discount_after_cycle) || special_character_format.test(createSellingPlanObject.change_discount_after_cycle)){
      appendError(change_discount_after_cycle_textfield,'After cycle value should only contain numbers.');
    }

    if(createSellingPlanObject.discount_value_after == ''){
      appendError(discount_textField_after,'After Discount value required.');
    }else if((parseInt(createSellingPlanObject.discount_value_after) == parseInt(createSellingPlanObject.subscription_discount_value)) && (createSellingPlanObject.subscription_discount_type_after == createSellingPlanObject.subscription_discount_type)){
      appendError(discount_textField_after,"After Discount value can't be same as discount value.");
    }else if(/[a-zA-Z]/.test(createSellingPlanObject.discount_value_after) || special_character_format.test(createSellingPlanObject.discount_value_after)){
      appendError(discount_textField_after,'After discount value should only contain numbers.');
    }else if(createSellingPlanObject.subscription_discount_type_after == 'Percent Off(%)'){
      if(parseInt(createSellingPlanObject.discount_value_after) > 100){
        appendError(discount_textField_after,'After discount value cannot be greater than 100.');
      }
    }else if(createSellingPlanObject.subscription_discount_type_after == 'Discount Off'){
      if((createSellingPlanObject.discount_value_after).length > 4){
        appendError(discount_textField_after,'After discount value should not be greater than 4 digit.');
      }
    }
  }

  if(createSellingPlanObject.subscription_discount == 'on'){
    if(createSellingPlanObject.subscription_discount_value == ''){
      appendError(discount_textField,'Discount value required.');
    }else if(/[a-zA-Z]/.test(createSellingPlanObject.subscription_discount_value) || special_character_format.test(createSellingPlanObject.subscription_discount_value)){
      appendError(discount_textField,'Discount value should only contain numbers.');
    }else if(createSellingPlanObject.subscription_discount_type == 'Percent Off(%)'){
      if(parseInt(createSellingPlanObject.subscription_discount_value) > 100){
        appendError(discount_textField,'Discount value cannot be greater than 100.');
      }
    }else if(createSellingPlanObject.subscription_discount_type == 'Discount Off'){
      if((createSellingPlanObject.subscription_discount_value).length > 4){
        appendError(discount_textField,'Discount value should not be greater than 4 digit.');
      }
    }
  }
  if(createSellingPlanObject.frequency_plan_type == 'Prepaid'){
    if(createSellingPlanObject.prepaid_billing_value == ''){
      appendError(PrepaidFrequencyField,'Billing period is required.');
    }else if(/[a-zA-Z]/.test(createSellingPlanObject.prepaid_billing_value) || special_character_format.test(createSellingPlanObject.prepaid_billing_value)){
      appendError(PrepaidFrequencyField,'Billing Period value should only contain numbers.');
    }else if(parseInt(createSellingPlanObject.prepaid_billing_value) <= 0 || (parseInt(createSellingPlanObject.prepaid_billing_value) <=  parseInt(createSellingPlanObject.per_delivery_order_frequency_value))){
      appendError(PrepaidFrequencyField,'Billing Period Value should be greater than zero and Delivery Period value.');
    }else if(!((parseInt(createSellingPlanObject.prepaid_billing_value) % parseInt(createSellingPlanObject.per_delivery_order_frequency_value)) == 0)){
      appendError(PrepaidFrequencyField,'Billing Period Value should be multiple of Delivery Period value.');
    }else if((createSellingPlanObject.prepaid_billing_value).length > 4){
      appendError(PrepaidFrequencyField,'Billing Period Value should not be greater than 4 digits.');
    }
  }
  return check_validation;
 }

  function create_heading(root,api){
    heading_component = root.createComponent(Heading, {
      id: 'profile_heading',
      level: 3,
    });
    return heading_component;
  }

  function create_text_field(root,api,Label,Value,Type,Display,text_type){
    textField = root.createComponent(TextField, {
      label: Label,
      value: escapeHtmlToCode(Value),
      error:'',
      type : Type,
      display : Display,
      onChange(value) {
        if(text_type == 'plan_name'){
          subscription_group['plan_name'] =  escapeHtmlToCode(value);
        }else{
          createSellingPlanObject[text_type] = escapeHtmlToCode(value);
        }
        // else{
        //   createSellingPlanObject[text_type] = escapeHtmlToCode(value.replace(/^0+/, ''));
        // }
      },
    });
    return textField;
  }


  function select_fields(root,api,selling_plan_options,Value,Label,select_type){
     selectFields = root.createComponent(Select, {
      label : Label,
      options : selling_plan_options,
      value : Value,
      labelInline: false,
      onChange(value) {
        // selectFields.updateProps({
        //   value : value,
        // });
       if(select_type == 'delivery_method'){
        selectDelievryMethod.updateProps({
          value : value,
        });
        if(value == 'Prepaid'){
          payper_prepaid_delivery_value.appendChild(inlineStack3);
          inlineStack3.appendChild(billing_frequency_type);
          inlineStack3.appendChild(PrepaidFrequencyField);
        }else if(value == 'Pay Per Delivery'){
          payper_prepaid_delivery_value.removeChild(inlineStack3);
          inlineStack3.removeChild(billing_frequency_type);
          inlineStack3.removeChild(PrepaidFrequencyField);
        }
        createSellingPlanObject['frequency_plan_type'] = value;
        }else if(select_type == 'delivery_billing_type'){
          delivery_frequency_type.updateProps({
            value : value,
          });
          billing_frequency_type.updateProps({
            value : value,
          });
          createSellingPlanObject['per_delivery_order_frequency_type'] = value;
        }else if(select_type == 'discount_type'){
          selectDiscountMethod.updateProps({
            value : value,
          });
          createSellingPlanObject['subscription_discount_type'] = value;
        }else if(select_type == 'discount_type_after'){
          recurringSelectDiscountMethod.updateProps({
            value : value,
          });
          createSellingPlanObject['subscription_discount_type_after'] = value;
        }
      },
    });
    return selectFields;
  }

 function delivery_billing_frequency_options(){
    delivery_frequency_type_options = [
      {
        label: 'DAY',
        value: 'DAY',
      },
      {
        label: 'WEEK',
        value: 'WEEK',
      },
      {
        label: 'MONTH',
        value: 'MONTH',
      },
      {
        label: 'YEAR',
        value: 'YEAR',
      },
    ];
    return delivery_frequency_type_options;
  }

  function  discount_options (){
    discountOptions = [
        {
          label: 'Percent Off(%)',
          value: 'Percent Off(%)',
        },
        {
          label: 'Discount Off',
          value: 'Discount Off',
        }
      ];
    return discountOptions;
   }

   function selling_plans_options(){
    selling_plan_options = [
     {
       label: 'Pay Per Delivery',
       value: 'Pay Per Delivery',
     },
     {
       label: 'Prepaid',
       value: 'Prepaid',
     }
   ];
   return selling_plan_options;
 }

    //OFFER DISCOUNT CHECKBOX
    function checkBox(root,api,Value,Label){
      checkbox_component =  root.createComponent(Checkbox, {
        label: Label,
        value : Value,
        onChange(value) {
          if(Label == 'Offer Discount'){
            if(value == true){
              discount_field_and_type_stack.appendChild(discount_textField);
              discount_field_and_type_stack.appendChild(selectDiscountMethod);
              createSellingPlanObject['subscription_discount'] = 'on';
              recurring_offer_discount_stack.appendChild(recurringDiscountCheckbox);
            }else{
              discount_field_and_type_stack.removeChild(discount_textField);
              discount_field_and_type_stack.removeChild(selectDiscountMethod);
              createSellingPlanObject['subscription_discount'] = 'no';
              recurring_offer_discount_stack.removeChild(recurringDiscountCheckbox);
              after_cycle_stack.removeChild(change_discount_after_cycle_textfield);
              recurring_discount_field_and_type_stack.removeChild(discount_textField_after);
              recurring_discount_field_and_type_stack.removeChild(recurringSelectDiscountMethod);
            }
            discountCheckbox.updateProps({
              checked : value,
            });
            recurringDiscountCheckbox.updateProps({
              checked : false,
            });
          }else if(Label == 'Change Discount After'){
            if(value == true){
              after_cycle_stack.appendChild(change_discount_after_cycle_textfield);
              recurring_discount_field_and_type_stack.appendChild(discount_textField_after);
              recurring_discount_field_and_type_stack.appendChild(recurringSelectDiscountMethod);
              createSellingPlanObject['subscription_discount_after'] = 'on';
            }else{
              after_cycle_stack.removeChild(change_discount_after_cycle_textfield);
              recurring_discount_field_and_type_stack.removeChild(discount_textField_after);
              recurring_discount_field_and_type_stack.removeChild(recurringSelectDiscountMethod);
              createSellingPlanObject['subscription_discount_after'] = 'no';
            }
            recurringDiscountCheckbox.updateProps({
              checked : value,
            });
          }
        },
      });
      return checkbox_component;
    }

    //create card component
    function card_component(root,api){
      cardComponent = root.createComponent(Card, {
        sectioned: true,
        display : 'none',
      });
      return cardComponent;
    }

    function BlockStack_component(root,api,add_component,block_alignment){
      block_Stack_component = root.createComponent(BlockStack, {
        inlineAlignment : block_alignment,
        vertical: false,
        spacing: 'loose',
      });
      add_component.appendChild(block_Stack_component);
      return block_Stack_component;
    }

    function escapeHtmlToCode(text) {
      return text
        .replace(/amp;/g, "&")
        .replace(/lt;/g, "<")
        .replace(/gt;/g, ">")
        .replace(/quot;/g, '"')
        .replace(/&#039;/g, "'");
    }

    function createText_component(root,api,Appearance,Strong,Size){
      create_text = root.createComponent(Text, {
        appearance: Appearance,
        emphasized: true,
        size: Size,
        strong: Strong,
        error :'no selling plan is added',
      });
      return create_text;
    }
   //20
   // Creating a new array object
    function default_selling_plan_array(){
      createSellingPlanObject['frequency_plan_name'] = '';
      createSellingPlanObject['plan_name'] = '';
      createSellingPlanObject['sellingplanid'] = '';
      createSellingPlanObject['frequency_plan_type'] = 'Pay Per Delivery';
      createSellingPlanObject['subscription_discount'] = '';
      createSellingPlanObject['subscription_discount_after'] = '';
      createSellingPlanObject['sd_set_anchor_date'] = '';
      createSellingPlanObject['per_delivery_order_frequency_value'] = '';
      createSellingPlanObject['prepaid_billing_value'] = '';
      createSellingPlanObject['subscription_discount_value'] = '';
      createSellingPlanObject['per_delivery_order_frequency_type'] = 'DAY';
      createSellingPlanObject['subscription_discount_type'] = 'Percent Off(%)';
      createSellingPlanObject['change_discount_after_cycle'] = '';
      createSellingPlanObject['cut_off_days'] = '';
      createSellingPlanObject['discount_value_after'] = '';
      createSellingPlanObject['subscription_discount_type_after'] = 'Percent Off(%)';
      createSellingPlanObject['maximum_number_cycle'] = '';
      createSellingPlanObject['minimum_number_cycle'] = '';
      createSellingPlanObject['sd_anchor_month_day'] = '';
      createSellingPlanObject['sd_anchor_option'] = '';
      createSellingPlanObject['sd_anchor_week_day'] = '';
      createSellingPlanObject['selling_plan_name'] = '';
      createSellingPlanObject['sd_description'] = '';
      return createSellingPlanObject;
    }

    function group_name_field(root,api,group_name){
      let groupNameCard = card_component(root,api);
      root.appendChild(groupNameCard);
      let group_name_block_stack = BlockStack_component(root,api,groupNameCard,'leading');
      group_name_stack = createStack(root,api,group_name_block_stack,'');
      group_name_textField = create_text_field(root,api, 'Plan Name',group_name,'text','','plan_name');
      group_name_stack.appendChild(group_name_textField);
    }

// 'Create' mode should create a new selling plan, and add the current product to it
async function validate_token(token){
  subscription_group['token'] = token;
  let validate_token_response = fetch(ajax_url+'?action=product_app_extension_validate',{
    method: 'POST',
    headers: {Accept: 'application/json', },
    body : JSON.stringify(Object.assign({}, subscription_group)),
  });
  return validate_token_response;
}
// [Shopify admin renders this mode inside an app overlay container]
async function Create(root, api) {
  const data = api.data;
  const locale = api.locale.initialValue;
  const sessionToken = api.sessionToken;
  const {close, done} = api.container;
  const localizedStrings = translations[locale] || translations.en;
  const token = await sessionToken.getSessionToken();
  page_type = 'on_create_group';
  validate_token(token).then(response => {
    if (response.ok) {
      response.json().then(json => {
          if(json.status == true){
            if(token == json.decoded_token){ // if both tokens are identical
              default_selling_plan_array();
              heading_banner_component(root,api,'Add Plan');
              subscription_group['plan_name'] = '';
              const add_selling_plans = root.createComponent(Button, { // create process save button click
                title: 'Save',
                kind: 'primary',
                disabled :false,
                onPress: async () => {
                  group_name_textField.updateProps({
                    error : '',
                  });
                  // disable button until the process is completed
                 if(allSellingPlanArray.length > 0){
                  if(subscription_group['plan_name'] == ''){
                    group_name_textField.updateProps({
                      error : 'Plan Name field is required.',
                    });
                  }else{
                    // bannerText.updateProps({
                    //   title : json.message,
                    // });
                    add_selling_plans.updateProps({
                      disabled : true,
                    });
                    const token = await sessionToken.getSessionToken();
                    store_name = json.store_name;
                    subscription_group['product_ids'] = data.productId;
                    subscription_group['variant_ids'] = data.variantId,
                    subscription_group['frequency_plan'] = allSellingPlanArray;
                    subscription_group['table'] = 'subscription_plan';
                    const spinner = root.createComponent(Spinner);
                    headingComponent.appendChild(spinner);
                    // createSellingPlanObject['store'] = store_name;
                    fetch(ajax_url+'?action=product_app_extension_create&store='+store_name,{
                      method: 'POST',
                      headers: {Accept: 'application/json', },
                      body : JSON.stringify(Object.assign({}, subscription_group)),
                    }).then(response => {
                      headingComponent.removeChild(spinner);
                      if (response.ok) {
                        response.json().then(json => {
                            if(json.status == true){
                              done();
                            }else if(json.status == false){
                              add_selling_plans.updateProps({
                                disabled : false,
                              });
                                  if(!bannerText){
                                    bannerText = root.createComponent(Banner, {
                                      status: 'critical',
                                      title: json.message,
                                      display : 'block',
                                      onDismiss: () => headingComponent.removeChild(bannerText),
                                    });
                                    headingComponent.appendChild(bannerText);
                                  }else{
                                    bannerText.updateProps({
                                      title : json.message
                                    })
                                    headingComponent.appendChild(bannerText);
                                  }
                            }
                        });
                      }
                    });
                  }
                 }else{
                    if(!bannerText){
                      bannerText = root.createComponent(Banner, {
                        status: 'critical',
                        title: 'Add atleast one selling plan',
                        display : 'none',
                        onDismiss: () => headingComponent.removeChild(bannerText),
                      });
                      headingComponent.appendChild(bannerText);
                    }else{
                      bannerText.updateProps({
                        title : 'Add atleast one selling plan'
                      })
                      headingComponent.appendChild(bannerText);
                    }
                 }
              },
              });
                // CANCEL BUTTON
                const secondaryButton = root.createComponent(Button, {
                  title: 'Cancel',
                  onPress: () => close(),
                });

                // create selling plan and cancel button start
                const blockStack1 = root.createComponent(BlockStack, {
                  inlineAlignment: 'trailing',
                  spacing: 'loose',
                });
                root.appendChild(blockStack1);
                create_cancel_button_stack = root.createComponent(InlineStack, {
                  inlineAlignment: 'trailing',
                  fill: true
                });
                let add_space_stack = root.createComponent(InlineStack, {
                  inlineAlignment: 'leading',
                  fill: true
                });
                blockStack1.appendChild(create_cancel_button_stack);
                blockStack1.appendChild(add_space_stack);
                create_cancel_button_stack.appendChild(secondaryButton);
                create_cancel_button_stack.appendChild(add_selling_plans);

                // group name stack
                group_name_field(root,api,'');
                selling_plan_form(root,api);
            }else{
               not_found(root,api);
            }
          }else{
            not_found(root,api);
          }
          root.mount();
        });
      }
  });
}

function not_found(root,api){
  const card = root.createComponent(Card, {});
  root.appendChild(card);
  const cardSection = root.createComponent(CardSection, {
    title: 'Error 404',
  });
  cardSection.appendChild('Page not found.');
  card.appendChild(cardSection);
}

async function confirm_delete_selling_plan_modal(root,api,createdplanDetailsCard,selling_plan_object_index){
  const {close, done, setPrimaryAction, setSecondaryAction} = api.container;
   delete_selling_plan_modal =  root.createComponent(Modal, {
    title: 'Delete Selling Plan',
    onClose(){
      close_confirm_delete_selling_plan(root,api);
    },
    open: false,
  });
  root.appendChild(delete_selling_plan_modal);
  delete_selling_plan_form_card = BlockStack_component(root,api,delete_selling_plan_modal,'leading');
  delete_selling_plan_modal.appendChild(delete_selling_plan_form_card);
  let confirm_delete_text =  createText_component(root,api,'',true,'small');
  confirm_delete_text.appendChild('Are you sure you want to delete the Selling Plan');
  delete_selling_plan_form_card.appendChild(confirm_delete_text);

  confirm_delete_selling_plan_Button = root.createComponent(Button, {
    title: 'Yes',
    kind: 'primary',
    onPress: async () => {
      confirm_delete_selling_plan_Button.updateProps({
        disabled : true,
      });
      if((allSellingPlanArray[selling_plan_object_index].sellingplanid).length > 0){
        console.log('id exist');
        let delete_selling_plan_array = [];
        delete_selling_plan_array['selling_plan_id'] =  allSellingPlanArray[selling_plan_object_index].sellingplanid;
        delete_selling_plan_array['subscription_group_id'] = selling_plan_group_id;
        fetch(ajax_url+'?action=product_app_extension_deletePlan&store='+store_name,{
          method: 'POST',
          headers: {Accept: 'application/json', },
          body : JSON.stringify(Object.assign({}, delete_selling_plan_array)),
        }).then(response => {
          if (response.ok) {
            response.json().then(json => {
                // if(json.status == true){
                  all_created_selling_plans.removeChild(createdplanDetailsCard);
                  sd_subscription_edit_case_to_be_deleted_plans_array.push(allSellingPlanArray[selling_plan_object_index].sellingplanid);
                  allSellingPlanArray.splice(selling_plan_object_index, 1);
                  close_confirm_delete_selling_plan(root,api);
                // }else if(json.status == false){
                //   if((json.message).includes('Selling plan') && (json.message).includes('does not exist')){
                //     // bannerText =  createText_component(root,api,'critical',true,'small');
                //     // bannerText.appendChild(json.message);
                //     // headingComponent.appendChild(bannerText);
                //     bannerText = root.createComponent(Banner, {
                //       status: 'critical',
                //       title: json.message,
                //       display : 'none',
                //       onDismiss: () => headingComponent.removeChild(bannerText),
                //     });
                //     headingComponent.appendChild(bannerText);
                //     done();
                //   }else if(json.message == 'Atleast one plan should be in the group'){
                //     bannerText = root.createComponent(Banner, {
                //       status: 'critical',
                //       title: json.message,
                //       display : 'none',
                //       onDismiss: () => headingComponent.removeChild(bannerText),
                //     });
                //     headingComponent.appendChild(bannerText);
                //     done();
                //   }
                // }
            });
          }
        });
      }else{
        console.log('id not exist');
        all_created_selling_plans.removeChild(createdplanDetailsCard);
        allSellingPlanArray.splice(selling_plan_object_index, 1);
         close_confirm_delete_selling_plan(root,api);
      }
    },
  });
  //cancel button
  cancelDeletePlanButton = root.createComponent(Button, {
    title: 'Cancel',
    kind: 'secondary',
    onPress: async () => {
      close_confirm_delete_selling_plan(root,api);
    }
  });
confirm_button_stack = createStack(root,api,delete_selling_plan_modal,'trailing');
confirm_button_stack.appendChild(confirm_delete_selling_plan_Button);
confirm_button_stack.appendChild(cancelDeletePlanButton);
}

function selling_plan_modal_create(root,api,form_type){
    selling_plan_modal =  root.createComponent(Modal, {
      title: form_type+' Selling Plan',
      onClose(){
        update_array_value();
        close_selling_plan_modal(root,api);
        add_selling_plan_Button.updateProps({
          disabled : false,
        });
      },
      open: false,
    });
    root.appendChild(selling_plan_modal);
    selling_plan_form_card = BlockStack_component(root,api,selling_plan_modal,'leading');
    selling_plan_modal.appendChild(selling_plan_form_card);
         //update or create button of selling plan modal
         update_selling_plan_Button = root.createComponent(Button, {
          title: form_type,
          kind: 'primary',
          onPress: async () => {
            //remove the previous updated selling plan from array and previous card
            // update selling plan validations start
            checkValidation(root,api);
            if(check_validation == true){
              if(Object.entries(current_edit_selling_plan).length > 0){
              selling_plan_object_index = allSellingPlanArray.findIndex(x => x.frequency_plan_name === current_edit_selling_plan.frequency_plan_name);
              // allSellingPlanArray.splice(selling_plan_object_index, 1);
              // allSellingPlanArray.push(createSellingPlanObject);
              allSellingPlanArray[selling_plan_object_index] = createSellingPlanObject;
              root.removeChild(all_created_selling_plans);
              all_created_selling_plans = card_component(root,api);
              root.appendChild(all_created_selling_plans);
              allSellingPlanArray.forEach(value => {
                createSellingPlanObject = {};
                createSellingPlanObject = value;
                created_selling_plan_form(root,api,'edit');
              });
              }else{
                created_selling_plan_form(root,api,'create'); // call function to create detail card of the selling plan
                createSellingPlanObject = {}; //empty selling plan object to add new values
                default_selling_plan_array();
                current_edit_selling_plan = {};  //new edit(27_june)
              }
              update_array_value();
              close_selling_plan_modal(root,api);
              add_selling_plan_Button.updateProps({
                disabled : false,
              });
              // ++++++++++++++++++++++++++++++++++++++++//
            }else{
            }
          },
        });
        //cancel button
        cancelUpdatePlanButton = root.createComponent(Button, {
          title: 'Cancel',
          kind: 'secondary',
          onPress: async () => {
            add_selling_plan_Button.updateProps({
              disabled : false,
            });
            update_array_value();
            close_selling_plan_modal(root,api);
          }
        });
      update_button_stack = createStack(root,api,selling_plan_modal,'trailing');
      update_button_stack.appendChild(update_selling_plan_Button);
      update_button_stack.appendChild(cancelUpdatePlanButton);
}

function create_added_plan_card(root,api,block_stack,Label,type){
    // let plan_name_stack = createStack(root,api,block_stack,'');
    let plan_name_text =  createText_component(root,api,'',false,'small');
    if(Label == 'Delivery Every'){
      plan_name_text.appendChild(Label+' : '+createSellingPlanObject[type]+' '+createSellingPlanObject['per_delivery_order_frequency_type']);
    }else if(Label == 'Discount'){
      if(createSellingPlanObject[type] == 'on'){
        discount_after = ' ';
        if(createSellingPlanObject['subscription_discount_after'] == 'on'){
          if(createSellingPlanObject['subscription_discount_type_after'] ==  "Percent Off(%)"){
            discount_symbol_after = '%';
          }else{
            discount_symbol_after = ' '+shop_currency;
          }
          if(createSellingPlanObject['change_discount_after_cycle'] == 1){
            discount_after = 'on first order then '+createSellingPlanObject['discount_value_after']+' '+discount_symbol;
          }else{
            discount_after = 'on first '+createSellingPlanObject['change_discount_after_cycle']+' order then '+createSellingPlanObject['discount_value_after']+' '+discount_symbol_after;
          }
        }
        if(createSellingPlanObject['subscription_discount_type'] ==  "Percent Off(%)"){
          discount_symbol = '%';
        }else{
          discount_symbol = ' '+shop_currency;
        }
        discount_applied = createSellingPlanObject['subscription_discount_value']+''+discount_symbol+' '+discount_after;
        plan_name_text.appendChild(Label+' : '+discount_applied);
      }else{
        plan_name_text.appendChild('');
      }
    }else{
      plan_name_text.appendChild(Label+' : '+escapeHtmlToCode(createSellingPlanObject[type]));
    }
    return block_stack.appendChild(plan_name_text);
    // return plan_name_stack;
}

  function reset_form(root,api){
    planNameField.updateProps({
      value : '',
    });
    PayperFrequencyField.updateProps({
      value : '',
    });
    discount_textField.updateProps({
      value : '',
    });
    PrepaidFrequencyField.updateProps({
      value : '',
    });
    change_discount_after_cycle_textfield.updateProps({
      value : ''
    });
    discount_textField_after.updateProps({
      value : ''
    });
  }

  function close_selling_plan_modal(root,api){
    if(selling_plan_modal){
      selling_plan_modal.updateProps({
        open : false,
      });
    }
  }

  function open_selling_plan_modal(root,api){
    selling_plan_modal.updateProps({
      open : true,
    });
  }

  function open_confirm_delete_selling_plan(root,api){
    delete_selling_plan_modal.updateProps({
      open : true,
    });
  }

  function close_confirm_delete_selling_plan(root,api){
    if(delete_selling_plan_modal){
      delete_selling_plan_modal.updateProps({
        open : false,
      });
    }
  }

  function update_array_value(){
    createSellingPlanObject = {}; //empty selling plan object to add new values
    default_selling_plan_array();
    current_edit_selling_plan = {};
  }

  function created_selling_plan_form(root,api,from_type){
    if(from_type == 'create'){
      allSellingPlanArray.push(createSellingPlanObject);
    }
    // create added plans cards
    if(!all_created_selling_plans){
      all_created_selling_plans = card_component(root,api);
      root.appendChild(all_created_selling_plans);
    }
    const createdplanDetailsCard = root.createComponent(CardSection, {});
    all_created_selling_plans.appendChild(createdplanDetailsCard);
    const createdplanDetailsCard_new = root.createComponent(InlineStack, {
      blockAlignment : 'center',
      inlineAlignment: 'fill',
      spacing: 'none',
      fill:true,
    });
    createdplanDetailsCard.appendChild(createdplanDetailsCard_new);
    let frequency_card_name = escapeHtmlToCode(createSellingPlanObject['frequency_plan_name']);

    let delete_selling_plan_Button = root.createComponent(Button, {
      title: 'Delete',
      kind: 'secondary',
      inlineAlignment: 'trailing',
      disabled: disable_delete_button,
      accessibilityLabel:frequency_card_name,
      onPress: async () => {
        if(selling_plan_error){
          headingComponent.removeChild(selling_plan_error);
        }
        if(allSellingPlanArray.length > 1 || page_type == 'on_create_group'){
          close_confirm_delete_selling_plan(root,api);
          selling_plan_object_index = allSellingPlanArray.findIndex(x => x.frequency_plan_name === frequency_card_name);
          confirm_delete_selling_plan_modal(root,api,createdplanDetailsCard,selling_plan_object_index);
          open_confirm_delete_selling_plan(root,api);
        }else{
          delete_selling_plan_Button.updateProps({
            disabled : true,
          });
          if(!bannerText){
            bannerText = root.createComponent(Banner, {
              status: 'Atleast one plan should be in the group',
              title: json.message,
              display : 'none',
              onDismiss: () => headingComponent.removeChild(bannerText),
            });
            headingComponent.appendChild(bannerText);
          }else{
            bannerText.updateProps({
              title : 'Atleast one plan should be in the group',
            });
            headingComponent.appendChild(bannerText);
          }
        }
        // show confirmation modal
      },
    });

    let edit_selling_plan_Button = root.createComponent(Button, {
      title: 'Edit',
      kind: 'secondary',
      onPress: async () => {
        update_array_value();
        close_selling_plan_modal(root,api); // if another selling plan modal is open to update then close the previous pop up
        selling_plan_modal_create(root,api,'Update'); // create modal
        open_selling_plan_modal(root,api);
        selling_plan_object_index = allSellingPlanArray.findIndex(x => x.frequency_plan_name === frequency_card_name);
        selling_plan_form_fields(root,api,selling_plan_form_card,allSellingPlanArray[selling_plan_object_index]);
        current_edit_selling_plan = allSellingPlanArray[selling_plan_object_index];

        Object.keys(createSellingPlanObject).forEach(key => {
          if (key in allSellingPlanArray[selling_plan_object_index]) {
            createSellingPlanObject[key] = allSellingPlanArray[selling_plan_object_index][key];
          }
        });
      },
    });

    const add_button_stack = root.createComponent(InlineStack, {
      blockAlignment : 'center',
      inlineAlignment: 'leading',
      spacing: 'none',
    });
    createdplanDetailsCard_new.appendChild(add_button_stack);
    const createdplanDetailsCard1  = BlockStack_component(root,api,add_button_stack,'leading');
    create_added_plan_card(root,api,createdplanDetailsCard1,'Selling Plan Name','frequency_plan_name');
    create_added_plan_card(root,api,createdplanDetailsCard1,'Plan Type','frequency_plan_type');
    create_added_plan_card(root,api,createdplanDetailsCard1,'Delivery Every','per_delivery_order_frequency_value');
    create_added_plan_card(root,api,createdplanDetailsCard1,'Discount','subscription_discount');
    const add_button_stack2 = root.createComponent(InlineStack, {
      blockAlignment : 'center',
      inlineAlignment: 'trailing',
      spacing: 'loose',
    });
    createdplanDetailsCard_new.appendChild(add_button_stack2);
    const createdplanDetailsCard2  = BlockStack_component(root,api,add_button_stack2,'leading');
    createdplanDetailsCard2.appendChild(delete_selling_plan_Button);
    createdplanDetailsCard2.appendChild(edit_selling_plan_Button);
    disable_delete_button = false;
  }

  function heading_banner_component(root,api,heading_text){
    headingComponent = create_heading(root,api);
    root.appendChild(headingComponent);
    headingText = root.createText(heading_text);
    headingComponent.appendChild(headingText);
  }

function selling_plan_form(root,api){
  const sessionToken = api.sessionToken;
  const data = api.data;
    //ADD BUTTON
    let selling_plan_block_stack = BlockStack_component(root,api,root,'');
    let add_button_stack_empty = createStack(root,api,selling_plan_block_stack,'leading');
    add_selling_plan_Button = root.createComponent(Button, {
      title: 'Add New Selling Plan',
      kind: 'primary',
      id:'add_Selling_plan',
      onPress: async () => {
        if(allSellingPlanArray.length >= 20){
          if(!bannerText){
            bannerText = root.createComponent(Banner, {
              status: 'critical',
              title: 'Only 20 Selling Plans can be added in a single Plan.',
              display : 'none',
              onDismiss: () => headingComponent.removeChild(bannerText),
            });
            headingComponent.appendChild(bannerText);
          }else{
            bannerText.updateProps({
              title : 'Only 20 Selling Plans can be added in a single Plan.'
            })
            headingComponent.appendChild(bannerText);
          }
        }else{
          add_selling_plan_Button.updateProps({
            disabled : true,
          });
          selling_plan_modal_create(root,api,'Add');
          selling_plan_form_fields(root,api,selling_plan_form_card,createSellingPlanObject);
          open_selling_plan_modal(root,api);
        }
      },
    });
  //CREATE CARD
  add_button_stack = createStack(root,api,selling_plan_block_stack,'trailing');
  add_button_stack.appendChild(add_selling_plan_Button);
  selling_plan_heading_stack = createStack(root,api,selling_plan_block_stack,'');
  // heading_banner_component(root,api,'Selling Plans');
}

function selling_plan_form_fields(root,api,card,single_selling_plan_array){
  let frequency_plan_name = '',frequency_plan_type = '',prepaid_billing_value='',per_delivery_order_frequency_value='',per_delivery_order_frequency_type='',subscription_discount='',subscription_discount_value = '',subscription_discount_type='',subscription_discount_after='',discount_value_after='',subscription_discount_type_after='',minimum_number_cycle='',maximum_number_cycle='',change_discount_after_cycle = '';
  if(single_selling_plan_array == ''){
  }else{
    frequency_plan_name = escapeHtmlToCode(single_selling_plan_array.frequency_plan_name);
    frequency_plan_type = single_selling_plan_array.frequency_plan_type;
    prepaid_billing_value = single_selling_plan_array.prepaid_billing_value;
    per_delivery_order_frequency_value = single_selling_plan_array.per_delivery_order_frequency_value;
    per_delivery_order_frequency_type = single_selling_plan_array.per_delivery_order_frequency_type;
    subscription_discount = single_selling_plan_array.subscription_discount;
    subscription_discount_value = single_selling_plan_array.subscription_discount_value;
    subscription_discount_type = single_selling_plan_array.subscription_discount_type;
    minimum_number_cycle = single_selling_plan_array.minimum_number_cycle;
    maximum_number_cycle = single_selling_plan_array.maximum_number_cycle;
    subscription_discount_after = single_selling_plan_array.subscription_discount_after;
    discount_value_after = single_selling_plan_array.discount_value_after;
    subscription_discount_type_after = single_selling_plan_array.subscription_discount_type_after;
    change_discount_after_cycle = single_selling_plan_array.change_discount_after_cycle;
  }
  selling_plan_block_Stack = BlockStack_component(root,api,card,'leading');
  //=========== first stack of plan name and delivery method ==============//
  const plan_name_delivery_method_stack = createStack(root,api,selling_plan_block_Stack,''); // this function create stack
  //Selling PLAN NAME FIELD

  planNameField = create_text_field(root,api, 'Selling Plan Name',frequency_plan_name,'text','block','frequency_plan_name');
  plan_name_delivery_method_stack.appendChild(planNameField);
  //delivery method field
  selling_plan_options = selling_plans_options();
  selectDelievryMethod = select_fields(root,api,selling_plan_options,frequency_plan_type,'Plan Type','delivery_method');
  plan_name_delivery_method_stack.appendChild(selectDelievryMethod);
  //=========== first stack of plan name and delivery method end==============//

//=========== second stack of pay per delivery and prepaid input fields ==============//
  payper_prepaid_delivery_value = createStack(root,api,selling_plan_block_Stack,'');
  //INLINE STACK 3
  inlineStack3 = root.createComponent(InlineStack);
//PREPAID FIELD
  PrepaidFrequencyField = create_text_field(root,api, 'Billing Period',prepaid_billing_value,'text','none','prepaid_billing_value');
//pay per field
 PayperFrequencyField = create_text_field(root,api, 'Delivery Period',per_delivery_order_frequency_value,'text','','per_delivery_order_frequency_value');
//  pay per and prepaid frequency types
 payper_prepaid_frequency_options = delivery_billing_frequency_options();
 //delivery frequency types
 delivery_frequency_type = select_fields(root,api,payper_prepaid_frequency_options,per_delivery_order_frequency_type,'Delivery Type','delivery_billing_type');
 //billing frequency types
 billing_frequency_type = select_fields(root,api,payper_prepaid_frequency_options,per_delivery_order_frequency_type,'Billing Type','delivery_billing_type');
 payper_prepaid_delivery_value.appendChild(delivery_frequency_type);
 payper_prepaid_delivery_value.appendChild(PayperFrequencyField);
//=========== second stack of pay per delivery and prepaid input fields end ==============//
//create cut off days stack
//min and max cycle stack start
min_max_cycle_stack = createStack(root,api,selling_plan_block_Stack,'');
//min cycle field
minCycleField = create_text_field(root,api, 'Mininum number of cycle',minimum_number_cycle,'text','','minimum_number_cycle');
//max cycle field
maxCycleField = create_text_field(root,api, 'Maximum number of cycle',maximum_number_cycle,'text','','maximum_number_cycle');
min_max_cycle_stack.appendChild(minCycleField);
min_max_cycle_stack.appendChild(maxCycleField);

//third stack of offer discount
offer_discount_stack = createStack(root,api,selling_plan_block_Stack,'');
//offer discount check box
let checked_value = '';
if(subscription_discount == 'on'){
   checked_value = 'checked';
}
discountCheckbox = checkBox(root,api,checked_value,'Offer Discount');
offer_discount_stack.appendChild(discountCheckbox);

//fourth stack of discount field and discount type
 discount_field_and_type_stack = createStack(root,api,selling_plan_block_Stack,'');

 //DISCOUNT FIELD
 discount_textField = create_text_field(root,api, 'Discount',subscription_discount_value,'text','','subscription_discount_value');
 DiscountOptions = discount_options();
 selectDiscountMethod = select_fields(root,api,DiscountOptions,subscription_discount_type,'Discount Type','discount_type');

   //recurring discount stack start
   recurring_offer_discount_stack = createStack(root,api,selling_plan_block_Stack,'');
   let recurring_checked_value = '';
   if(subscription_discount_after == 'on'){
     recurring_checked_value = 'checked';
   }
   recurringDiscountCheckbox = checkBox(root,api,recurring_checked_value,'Change Discount After');
  if(subscription_discount == 'on'){
      discount_field_and_type_stack.appendChild(discount_textField);
      discount_field_and_type_stack.appendChild(selectDiscountMethod);
      createSellingPlanObject['subscription_discount'] = 'on';
      recurring_offer_discount_stack.appendChild(recurringDiscountCheckbox);
  }
  if(frequency_plan_type == 'Prepaid'){
    payper_prepaid_delivery_value.appendChild(inlineStack3);
    inlineStack3.appendChild(billing_frequency_type);
    inlineStack3.appendChild(PrepaidFrequencyField);
  }
  //fourth stack of discount field and discount type
  //after cycle field stack
 after_cycle_stack = createStack(root,api,selling_plan_block_Stack,'');
 recurring_discount_field_and_type_stack = createStack(root,api,selling_plan_block_Stack,'');

 change_discount_after_cycle_textfield = create_text_field(root,api, 'After Cycle',change_discount_after_cycle,'text','','change_discount_after_cycle');
 //DISCOUNT FIELD
 discount_textField_after = create_text_field(root,api, 'Chnage Discount After Cycle',discount_value_after,'text','','discount_value_after');
//  DiscountOptions = discount_options();
 recurringSelectDiscountMethod = select_fields(root,api,DiscountOptions,subscription_discount_type_after,'Discount Type','discount_type_after');
  if(subscription_discount_after == 'on'){
    after_cycle_stack.appendChild(change_discount_after_cycle_textfield);
    recurring_discount_field_and_type_stack.appendChild(discount_textField_after);
    recurring_discount_field_and_type_stack.appendChild(recurringSelectDiscountMethod);
    createSellingPlanObject['subscription_discount_after'] = 'on';
  }

}

async function Remove(root, api) {
    const data = api.data;
    const locale = api.locale.initialValue;
    const sessionToken = api.sessionToken;
    const {close, done, setPrimaryAction, setSecondaryAction} = api.container;
    const localizedStrings = translations[locale] || translations.en;
    const token = await sessionToken.getSessionToken();
    validate_token(token).then(response => {
        if (response.ok) {
          response.json().then(json => {
            if(json.status == true){
              if(token == json.decoded_token){
                setPrimaryAction({
                  content: 'Remove from plan',
                  onAction: async () => {
                    const token = await sessionToken.getSessionToken();
                    store_name = json.store_name;
                    fetch(ajax_url+'?action=product_app_extension_remove&store='+store_name,{
                      method: 'POST',
                      headers: {Accept: 'application/json', },
                      body : JSON.stringify(data),
                    }).then(response => {
                      if (response.ok) {
                        response.json().then(json => {
                          if(json.status == true){
                            done();
                          }
                        });
                      }
                    });
                  },
                });
                setSecondaryAction({
                  content: 'Cancel',
                  onAction: () => close(),
                });
                const textElement = root.createComponent(Text);
                textElement.appendChild(
                  root.createText(
                    `This can't be undone . Are you sure you want to Remove this plan from this product?`
                  )
                );
                root.appendChild(textElement);
              }else{
                const textElement = root.createComponent(Text);
                textElement.appendChild(
                  root.createText(
                    `Page not found`
                  )
                );
                root.appendChild(textElement);
              }
            }else{
              const textElement = root.createComponent(Text);
              textElement.appendChild(
                root.createText(
                  `Page not found`
                )
              );
              root.appendChild(textElement);
            }
            root.mount();
          });
        }
  });
}

function titleCase(string){
  return string[0].toUpperCase() + string.slice(1).toLowerCase();
}

// 'Edit' mode should modify an existing selling plan.
// Changes should affect other products that have this plan applied.
// [Shopify admin renders this mode inside an app overlay container]
async function Edit(root, api) {
  const data = api.data;
  const locale = api.locale.initialValue;
  const sessionToken = api.sessionToken;
  const {close, done} = api.container;
  const localizedStrings = translations[locale] || translations.en;
  const token = await sessionToken.getSessionToken();
  page_type = 'on_edit_group';
    validate_token(token).then(response => {
      if (response.ok) {
        response.json().then(json => {
          if(json.status == true){
            if(token == json.decoded_token){
            store_name = json.store_name;
            default_selling_plan_array();
            fetch(ajax_url+'?action=product_app_extension_fetchGroupDetail&store='+store_name,{
              method: 'POST',
              headers: {Accept: 'application/json', },
              body : JSON.stringify(Object.assign({}, data)),
            }).then(response => {
              if (response.ok) {
                response.json().then(json => {
                  if(json.status == true){
                    selling_plan_group_id = (data.sellingPlanGroupId).replace("gid://shopify/SellingPlanGroup/", "");
                    selling_plan_modal_create(root,api,'Update');
                    subscription_plan_group_detail = json.subscription_plan_group_detail;
                    subscription_group['plan_name'] = escapeHtmlToCode(subscription_plan_group_detail[0].plan_name);
                    shop_currency = json.shopCurrency;
                    heading_banner_component(root,api,'Edit Plan');
                      const add_selling_plans = root.createComponent(Button, {
                        title: 'Save',
                        kind: 'primary',
                        onPress: async () => {
                          sd_subscription_edit_case_to_be_added_new_plans_array = [];
                          if(subscription_group['plan_name'] == ''){
                            group_name_textField.updateProps({
                              error : 'Plan Name field is required.',
                            });
                          }else{
                          console.log(allSellingPlanArray);
                          sd_subscription_edit_case_already_existing_plans_array = {};
                          allSellingPlanArray.forEach(value => {
                            if((value.sellingplanid).length > 0){
                              sd_subscription_edit_case_already_existing_plans_array[value.sellingplanid] = value;
                            }else{
                              sd_subscription_edit_case_to_be_added_new_plans_array.push(value);
                            }
                          });
                         if((Object.entries(sd_subscription_edit_case_already_existing_plans_array).length === 0) && (sd_subscription_edit_case_to_be_added_new_plans_array.length === 0)){
                            confirm_delete_selling_plan_Button.updateProps({
                              disabled : true,
                            });
                          }else{
                            add_selling_plans.updateProps({
                              disabled : true,
                            });
                          subscription_group['sd_subscription_edit_case_already_existing_plans_array'] = sd_subscription_edit_case_already_existing_plans_array;
                          subscription_group['sd_subscription_edit_case_to_be_added_new_plans_array'] = sd_subscription_edit_case_to_be_added_new_plans_array,
                          subscription_group['subscription_plan_id'] = (data.sellingPlanGroupId).replace("gid://shopify/SellingPlanGroup/", "");
                          subscription_group['store_data'] = data;
                          const spinner = root.createComponent(Spinner);
                          headingComponent.appendChild(spinner);
                          fetch(ajax_url+'?action=product_app_extension_edit&store='+store_name,{
                            method: 'POST',
                            headers: {Accept: 'application/json', },
                            body : JSON.stringify(Object.assign({}, subscription_group)),
                          }).then(response => {
                            add_selling_plans.updateProps({
                              disabled : false,
                            });
                            headingComponent.removeChild(spinner);
                            if (response.ok) {
                              response.json().then(json => {
                                  if(json.status == true){
                                      done();
                                  }else if(json.status == false){
                                      if((json.message).includes('does not exist')){
                                        if((json.message).includes('does not exist') && (json.message).includes('Selling plan')){
                                          selling_plan_id = json.message.match(/\d+/)[0];
                                          selling_plan_object_index = allSellingPlanArray.findIndex(x => x.sellingplanid === selling_plan_id);
                                          show_message = 'Selling plan "'+allSellingPlanArray[selling_plan_object_index].frequency_plan_name+'" does not exist. Please refresh the page to get the updated data';
                                        }else{
                                          show_message = 'Plan does not exist. Please refresh the page to get the updated data';
                                        }
                                      }else{
                                        show_message = json.message;
                                      }
                                    if(!bannerText){
                                      bannerText = root.createComponent(Banner, {
                                        status: 'critical',
                                        title: show_message,
                                        display : 'block',
                                        onDismiss: () => headingComponent.removeChild(bannerText),
                                      });
                                      headingComponent.appendChild(bannerText);
                                    }else{
                                      bannerText.updateProps({
                                        title : show_message
                                      })
                                      headingComponent.appendChild(bannerText);
                                    }
                                    // }
                                  }
                              });
                            }
                          });
                          }
                        }
                        },
                      });
                      //CANCEL BUTTON
                      const secondaryButton = root.createComponent(Button, {
                        title: 'Cancel',
                        onPress: () => close(),
                      });

                      // create selling plan and cancel button start
                      // create_cancel_button_stack = root.createComponent(InlineStack, {
                      //   inlineAlignment: 'trailing',
                      // });
                      // root.appendChild(create_cancel_button_stack);


                      // const blockStack1 = root.createComponent(BlockStack, {
                      //   inlineAlignment: 'trailing',
                      //   spacing: 'loose',
                      // });
                      // root.appendChild(blockStack1);
                      // let add_space_stack = root.createComponent(InlineStack, {
                      //   inlineAlignment: 'leading',
                      //   fill: true
                      // });
                      // blockStack1.appendChild(create_cancel_button_stack);
                      // blockStack1.appendChild(add_space_stack);
                      let blockStack1 = BlockStack_component(root,api,root,'trailing');
                      create_cancel_button_stack = createStack(root,api,blockStack1,'trailing');
                      let add_space_stack = createStack(root,api,blockStack1,'leading');
                      create_cancel_button_stack.appendChild(secondaryButton);
                      // create_cancel_button_stack.appendChild(add_new_selling_plans);
                      create_cancel_button_stack.appendChild(add_selling_plans);
                    group_name_field(root,api,subscription_plan_group_detail[0].plan_name);
                    selling_plan_form(root,api);
                    const heading = root.createComponent(Heading, {
                      id: 'profile_heading',
                      level: 3,
                    });
                    const headingText = root.createText('Selling Plans');
                    heading.appendChild(headingText);
                    selling_plan_heading_stack.appendChild(heading);
                    all_created_selling_plans = card_component(root,api);
                    root.appendChild(all_created_selling_plans);
                    if(subscription_plan_group_detail.length == 1){
                      disable_delete_button = true;
                    }
                    subscription_plan_group_detail.forEach((plan) => {
                      console.log(plan);
                      prepaid_delivery = '';
                      Object.keys(createSellingPlanObject).forEach(key => {
                        if (key in plan) {
                          delivery_every = titleCase(plan['per_delivery_order_frequency_type']);
                          if(plan['per_delivery_order_frequency_value'] != 1){
                            delivery_every = plan['per_delivery_order_frequency_value']+' '+titleCase(plan['per_delivery_order_frequency_type']);
                          }
                          if(key == 'subscription_discount_type'){
                            if(plan[key] == 'A'){
                              createSellingPlanObject[key] = 'Discount Off';
                            }else if(plan[key] == 'P'){
                              createSellingPlanObject[key] = 'Percent Off(%)';
                            }
                          }else
                          if(key == 'subscription_discount' && plan[key] == '1'){
                            createSellingPlanObject[key] = 'on';
                          }else if(key == 'frequency_plan_type'){
                            if(plan[key] == '1'){
                              createSellingPlanObject[key] = 'Pay Per Delivery';
                            }else if(plan[key] == '2'){
                              createSellingPlanObject[key] = 'Prepaid';
                              prepaid_delivery =  plan['prepaid_billing_value']+' '+titleCase(plan['per_delivery_order_frequency_type'])+'(s) prepaid subscription,';
                            }
                          }else if(key == 'subscription_discount_type_after'){
                          if(plan[key] == 'A'){
                            createSellingPlanObject[key] = 'Discount Off';
                          }else if(plan[key] == 'P'){
                            createSellingPlanObject[key] = 'Percent Off(%)';
                          }
                        }else
                          if(key == 'subscription_discount_after' && plan[key] == '1'){
                            createSellingPlanObject[key] = 'on';
                          }else{
                            createSellingPlanObject[key] = escapeHtmlToCode(plan[key]);
                          }
                        }
                      });
                      selling_plan_name = (prepaid_delivery+' Delivery Every '+delivery_every).trim();
                      createSellingPlanObject['selling_plan_name'] = selling_plan_name;
                      created_selling_plan_form(root,api,'create'); // already added selling plans html
                      createSellingPlanObject = {}; //empty selling plan object to add new values
                      default_selling_plan_array();
                    });
                    root.mount();
                  }else if(json.status == false){
                  }
                });
              }
            });
            }else{
              not_found(root,api);
              root.mount();
            }
          }else{
            not_found(root,api);
            root.mount();
          }
        });
      }

    });
}

// Your extension must render all four modes
extend('Admin::Product::SubscriptionPlan::Add', Add);
extend('Admin::Product::SubscriptionPlan::Create', Create);
extend('Admin::Product::SubscriptionPlan::Remove', Remove);
extend('Admin::Product::SubscriptionPlan::Edit', Edit);
// extend('MyExtensionPoint', checkValidation);
