<?php
   include("header.php");
   echo $mainobj->store;

  // $get_data =  $mainobj->customQuery("select variant_id from `subscriptionPlanGroupsProducts` C WHERE `subscription_plan_group_id` LIKE '76450890069'");
  // echo '<pre>';
  // print_r($get_data);
//   die;

// check if anchors exists
  $get_group_details = '{
    subscriptionContract(id :"gid://shopify/SubscriptionContract/17935040794") {
        originOrder{
            name
            tags
            customerLocale
            shippingLine{
                code
                originalPriceSet{
                    shopMoney{
                        amount
                        currencyCode
                    }
                }
            }
            shippingAddress{
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
        }
        customer{
            displayName
            email
            state
            tags
        }
        customerPaymentMethod{
            id
            instrument{
                __typename
                ... on CustomerCreditCard {
                    brand
                    expiresSoon
                    expiryMonth
                    expiryYear
                    firstDigits
                    lastDigits
                    name
                }
                ... on CustomerShopPayAgreement {
                    expiresSoon
                    expiryMonth
                    expiryYear
                    lastDigits
                    name
                }
                ... on CustomerPaypalBillingAgreement{
                    paypalAccountEmail
                }
            }
        }
        nextBillingDate
        billingPolicy{
            anchors{
                cutoffDay
                day
                month
                type
            }
        }
        lines(first:50){
            edges{
                node{
                    id
                    quantity
                    sellingPlanId
                    productId
                    variantId
                    variantTitle
                    title
                    quantity
                    variantImage{
                        src
                    }
                    lineDiscountedPrice{
                        amount
                    }
                    discountAllocations{
                        amount{
                          amount
                        }
                        discount{
                          __typename
                         ... on SubscriptionManualDiscount{
                            title
                        }
                      }
                    }
                    pricingPolicy {
                        basePrice{
                            amount
                        }
                        cycleDiscounts {
                            adjustmentType
                            afterCycle
                            computedPrice{
                                amount
                            }
                            adjustmentValue{
                                ... on MoneyV2{
                                    amount
                                }
                                ... on SellingPlanPricingPolicyPercentageValue{
                                    percentage
                                }
                            }
                        }

                    }
                    currentPrice{
                        amount
                        currencyCode
                    }
                }
            }
        }
    }
}';
  $graphQL_group_details = $mainobj->graphqlQuery($get_group_details,null,null,null);
  echo '<pre>';
  print_r($graphQL_group_details);
  die;


// $draft_id = $mainobj->getContractDraftId('16095478042');
// // echo $draft_id;
// // // die;


//   $update_delivery_policy = '
//   mutation {
//     subscriptionDraftUpdate(
//       draftId: "'.$draft_id.'"
//       input: {
//       deliveryPolicy: {
//         interval : MONTH
//         intervalCount : 1
//         anchors: [],
//       }
//     }
//     ) {
//       draft {
//       id
//       }
//       userErrors {
//       field
//       message
//       }
//     }
//     }
//   ';

// $graphQL_update_delivery_policy = $mainobj->graphqlQuery($update_delivery_policy,null,null,null);
// echo '<pre>';
// print_r($graphQL_update_delivery_policy);
// // // // die;

// $mainobj->commitContractDraft($draft_id);

// $all_subscription_array = array('16118743322','15108309274','14938079514','15119810842','15134949658','15236137242','15500181786','15707144474','15939469594','15981150490','15996256538','16022307098','16034890010',
// '16065003802','16066740506','16164618522','16206594330','16237658394','16288219418','15409512730','16337600794');
// foreach($all_subscription_array as $key=>$value){
//   try{
//     $get_group_details = '{
//       sellingPlanGroup(id:"gid://shopify/SellingPlanGroup/212926508"){
//          id
//         sellingPlans(first:10){
//           edges{
//             node{
//               name
//               billingPolicy{
//                 __typename
//                 ... on SellingPlanRecurringBillingPolicy{
//                       anchors{
//                         day
//                         month
//                         type
//                       }
//                     }
//               }
//                deliveryPolicy{
//                 __typename
//               ... on SellingPlanRecurringDeliveryPolicy{
//                 anchors{
//                   day
//                   month
//                   type
//                 }
//               }
//               }
//             }
//           }
//         }
//       }
//     }';
//     $graphQL_group_details = $mainobj->graphqlQuery($get_group_details,null,null,null);
//     echo '<pre>';
//     print_r($graphQL_group_details);
//  }catch(Exception $e) {
//     echo 'Message: ' .$e->getMessage();
//   }
  // try{

  //   echo "==========================<br>";
    // $get_group_details = '{
    //   subscriptionContract(id:"gid://shopify/SubscriptionContract/16095478042"){
    //     id
    //     deliveryPolicy{
    //       interval
    //       intervalCount
    //       anchors{
    //         day
    //         cutoffDay
    //       }
    //     }
    //     billingPolicy{
    //       interval
    //       intervalCount
    //     }
    //   }
    // }';
    // $graphQL_group_details = $mainobj->graphqlQuery($get_group_details,null,null,null);
    // echo '<pre>';
    // print_r($graphQL_group_details);
  // }catch(Exception $e) {
  //   echo 'Message: ' .$e->getMessage();
  // }
// }

?>
<?php include("footer.php"); ?>