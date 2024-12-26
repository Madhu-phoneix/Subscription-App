<?php
include("header.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<div class="Polaris-Layout">
<?php
include("navigation.php");
?>

<link href="<?php echo $SHOPIFY_DOMAIN_URL ;?>/application/assets/css/docstyle.css" rel="stylesheet">

<script src="<?php echo $SHOPIFY_DOMAIN_URL ;?>/application/assets/js/bootstrap.bundle.min.js"></script>

    <!-- Document Wrapper =============================== -->
    <div id="main-wrapper">

      <!-- Content  ============================ -->
      <div id="content" role="main">
        <div class="docmain-outer sd_app_documentation">
          <!-- Sidebar Navigation	============================ -->
          <div class="idocs-navigation bg-light">
            <ul class="nav flex-column">
              <li class="nav-item">
                <a class="nav-link active" href="#idocs_start">Getting Started</a>
                <ul class="nav flex-column">
                  <li class="nav-item">
                    <a class="nav-link" href="#idocs_dashboard">Dashboard</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#idocs_installation">Create Plans</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#idocs_html_structure">Subscriptions Listing</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#idocs_sass">Subscriptions Update</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#idocs_color_schemes" >Settings</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#idocs_analytics" >Analytics</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#idocs_theme_integrate" >Theme Integrate</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#idocs_contactUs" >Contact Us</a>
                  </li>

                </ul>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#idocs_faq">FAQ</a>
              </li>
            </ul>
          </div>

          <!-- Docs Content	============================ -->
          <div class="idocs-content">
            <div class="container">
              <!-- Getting Started	============================ -->
              <section id="idocs_dashboard">
                <h1>Dashboard</h1>
                <p>
                 On the dashboard page merchant will get the idea why subscription widget not displaying on the product page
                </p>
                <p>A contact email is also there to contact with the support team</p>
                <figure>
                    <img src="<?php echo $image_folder; ?>sd_dashboard.png" alt="img" class="img-fluid" />
                </figure>
              </section>

              <hr class="divider" />
              <section id="idocs_start">
                <h1>Documentation Subscription</h1>
                <hr />
                <p>
                  <strong>Listing: -</strong> List view gives you many essential features in one go like you can search, delete, and get a brief view of every Plan.
                </p>
                <p>
                  <strong>Search:</strong> You can search the Plan with their own names.
                </p>
                <p>
                  <strong>Brief View:</strong> In this, you can get a brief or short view of each Plan like how many total products & selling plans are there in the respective plan. You can simply rename the Plan name by clicking on the “pencil” icon.
                </p>

              </section>

              <hr class="divider" />

              <!-- Installation	============================ -->
              <section id="idocs_installation">
                <h2>Create: -</h2>
                <p class="lead">
                  My plan can be created in 3 easy to go steps.
                </p>
                <p>
                  <strong>Step1: </strong> Enter the plan name for recognition, this name is just for the app's internal process. To assign the products to the plan click “Select Products”. Here we have given the flexibility to the merchants to either assign the plan to the whole product or to any of the desired variants. Also, the picker has multiple product /variants select options.
                  <figure>
                    <img src="<?php echo $image_folder; ?>create_subs.png" alt="img" class="img-fluid" />
                  </figure>
                </p>
                <ol>
                  <li>
                    <strong>Step2: </strong> To add the selling plans in a plan,
                    there are two types: -
                    <ul>
                      <li>
                        <strong> Per Delivery:</strong> In this type, a new order will be created for every delivery by deducting the amount from the user payment gateway automatically.
                        <figure>
                          <img src="<?php echo $image_folder; ?>pay_per_delivery.png" alt="img" class="img-fluid" />
                        </figure>
                      </li>
                      <li>
                        <strong> Prepaid:</strong> In this type, the User will be charged for future deliveries at single order & then fulfillments need to be processed at the delivery end. For example: - If the billing period is of 30 days & delivery is of every 3 days then at the first order total amount of 10 fulfillments will be deducted and after 10th delivery, the new order will be created automatically for the next 10 deliveries and the cycle will go on until the customer subscription is ended.
                        <figure>
                          <img src="<?php echo $image_folder; ?>prepaid_plan.png" alt="img" class="img-fluid" />
                        </figure>
                      </li>
                    </ul>
                  </li>
                  <li>Selling plans name
                    <ul>
                      <li>
                        Enter the selling plan name for recognition, this name is just for the app's internal process.
                      </li>
                    </ul>
                  </li>
                  <li>Merchant will see the many advance features while creating the selling plans :-
                    <ul>
                        <li>
                          Delivery Period :- The time gap between two order deliveries within the subscription plan. The number should be a minimum of 1, and the timeline can be days/weeks/months/years. For instance, if we have a subscription plan setup for 1 week and the first order is placed today, the next order will be automatically placed exactly after a week. The cycle will continue every week.
                        </li>
                        <li>
                          Billing Period :- Billing Period will exist in the prepaid orders. This is the number of deliveries that the customers can pay in advance for. For example, if you set the delivery frequency as 1 week, and if the billing timeframe is 12 weeks, and if , your customers will be charged for 12 weeks, but receive the deliveries once a week, for 12 weeks.
                        </li>
                        <li>
                          Minimum number of Cycle :- Minimum number of billing iteration you want to bind your customers with, before they can cancel their subscription. Default value is one (the very first billing iteration).
                        </li>
                        <li>
                          Maximum number of cycle :- Maximum number of billing iteration that will be fulfilled as a part of the subscription plan, after which it will automatically pause. Merchant or customer(If customer has permission by the merchant) can activate or delete the subscription whenever they want.
                        </li>
                        <li>
                          Set Order date :- Merchant can set the specific order date, on which the order will be placed. This will show only when the delivery period is in weeks/months/years.
                        </li>
                        <li>
                          Cut off Day :- The cutoff indicates how many days in advance the order would need to be placed in order to qualify for the upcoming order cycle.
                        </li>
                        <li>
                          Offer Discount :- There are two types of discount Fixed and Percentage.
                          <p>If merchant select the Fixed discount then that fixed amount will be discounted from the product price.</p>
                          <p>If  merchant select the Percentage discount then that percent of the discount will be applied on the product on the product page.</p>
                          <p>Discount will not get disabled or changed if not any recurring discount applied.</p>
                        </li>
                        <li>
                          Recurring Discount :- If recurring discount is enabled then the discount on the subscription product will be changed after the mentioned after cycle. Once the after cycle discount is applied then there is no way to disabled or change the discount. In that case merchant or customer can delete the currenct subscription and can create the new one.
                        </li>
                    </ul>
                  </li>
                  <li>
                    <p><strong> Key Points: -</strong></p>
                    <ol>
                      <li>
                        At least 1 product is mandatory in a plan.
                      </li>
                      <li>
                        A maximum of 20 selling plans can be created in a plan.
                      </li>
                      <li>
                        You can Offer the discount by enabling the toggle button & set the discount value on the product price in percentage or amount.
                      </li>
                      <!-- <li>
                        Make sure to give relatable value in the “Selling Plan Name” field as these names will show on the customer subscription & order page as well. For example – a) Delivery every day with $5 off on single quantity. B) Prepay for 30 days & get delivery every day with 10% off.
                      </li> -->
                    </ol>
                  </li>
                </ol>
                <p>
                  <strong>Step3: </strong> All selling plans will be listed here. You can add more by clicking the “add selling plan” button. If you need to edit the already added selling plan then simply click on “edit” button.
                  <figure>
                    <img src="<?php echo $image_folder; ?>selling_plan_list.png" alt="img" class="img-fluid" />
                  </figure>
                </p>
                <p>
                  <strong>Preview: </strong> You can get the idea of the display on the product page from the preview area. Note: - Image & Prices are dummy data. On the website, Prices will show the relevant data as per the product price & the selling plan discount.
                  <p>
                    You can get the idea of the display on the product page from the preview area. Note: - Image & Prices are dummy data. On the website, Prices will show the relevant data as per the product price & the selling plan discount.
                  </p>
                </p>
                <p>
                  <strong>Edit: </strong> Once you click the edit button, you will see the Plan complete view area where you can edit the whole plan.
                  <figure>
                    <img src="<?php echo $image_folder; ?>edit_button_click.png" alt="img" class="img-fluid" />
                  </figure>
                </p>
                <p>
                  <strong>Selling Plan: </strong> You can add, update, delete selling plans in a Plan from here.
                  <figure>
                    <img src="<?php echo $image_folder; ?>edit_group_screen.png" alt="img" class="img-fluid" />
                  </figure>
                </p>
                <p>
                  <strong>Products: </strong> You can manage products in a Plan from here.
                  <figure>
                    <img src="<?php echo $image_folder; ?>edit_product_tab.png" alt="img" class="img-fluid" />
                  </figure>
                </p>

                <p>
                  <strong>Delete Plan: </strong> Simply click on the red dustbin icon to completely remove the Plan from the product. Once this action is performed, the app purchase option widget will stop displaying on the site product page.
                </p>

                <p>
                  <strong>Plan Name:</strong> To change the plan name, click on the edit link just below the name in the left panel of edit screen.
                </p>

              </section>

              <hr class="divider" />

              <!-- HTML Structure	============================ -->
              <section id="idocs_html_structure">
                <h2>Subscription Listing</h2>
                <img src="<?php echo $image_folder; ?>contract_list.png" alt="img" class="img-fluid" />

                <p>Table List view gives all your Subscriptions. From the Search box, you can get specifics from the table by entering the search keyword as a reference to any of the column names.</p>

                <p>
                  <strong>Edit & View:</strong> To view the complete subscription or to update it, click on the desired table row.
                </p>

                <p>
                  <strong>Subscription Settings:</strong> These settings play an important role in subscription renewals, so make sure you read all the settings carefully & select options in each setting as per your requirements.
                </p>
              </section>

              <hr class="divider" />

              <!-- Sass	============================ -->
              <section id="idocs_sass">
                <h2>Subscriptions Update</h2>
                <img src="<?php echo $image_folder; ?>update_contract.png" alt="img" class="img-fluid" />

                <p>
                  <strong>The highlighted portion:</strong> will give you the details of subscription parameters like the Subscription Id, Subscription status, Customer details, Subscription Total Amount, Delivery & Billing cycle, and the Order Number from where the subscription originated. The Delete Button will permanently delete the subscription & its related data.
                </p>
                <p>
                  <strong>Products:</strong> here you can manage subscription products. You can increase the quantity of the existing products or delete them as well or add new products to the subscription itself.  While adding product to the subscription, discount will be automatically applied on that product based on the selling plan applied on that contract. The currency of the product will be the same the currency of that contract.
                </p>

                <p>
                  <strong>Shipping & Billing Address:</strong> you can view your billing address & update the shipping address for prepaid & pay per delivery fulfillment if required.
                </p>


                <p>
                  <strong>Payment Details:</strong> Please check your email if you update the “payment details” attached to the subscription.
                </p>

                <p>
                  <strong>Past Orders:</strong> All the previous orders to the date will be listed here.
                </p>

                <p>
                  <strong>Upcoming Orders:</strong> This option will be visible in “pay per delivery” subscriptions only. All the upcoming orders to the date will be listed here. For future dates, you have the feature to either skip the order & attempt earlier billing for a particular date.
                </p>

                <p>
                  <strong>Pending Orders:</strong> All the previous pending orders to the date will be listed here
                </p>

                <p>
                  <strong>Failure Orders:</strong> All the past failure orders will be listed here. Orders may have failed due to unattended 3-D Secure billing attempts or payment cards expirations.
                </p>

                <p>
                  <strong>Skip Orders:</strong> This option will be visible in “pay per delivery” subscriptions only. Orders that you gave skipped in upcoming orders will be listed here.
                </p>

                <p>
                  <strong>Upcoming Fulfillments:</strong> This option will be visible in “prepaid” subscriptions only. All fulfillments of the current prepaid cycle will be listed here with skip feature. Skipped fulfillments will append to the last of the current prepaid cycle.
                </p>

                <p>
                  <strong>Rescheduled Fulfillments of current cycle:</strong> This option will be visible in “prepaid” subscriptions only. Skipped fulfillments history i.e., from what to When will be displayed here.
                </p>

              </section>

              <hr class="divider" />

              <!-- Color Schemes	============================ -->
              <section id="idocs_color_schemes">
                <h2>Settings</h2>
                <p>
                  <strong>Product Page:</strong> This setting is to customize the my plans widget displayed on the product page in terms of desired headings, labels, descriptions & color schemes as per one’s requirements.
                  <figure>
                    <img src="<?php echo $image_folder; ?>product_page_setting.png" alt="img" class="img-fluid w-100">
                  </figure>
                </p>

                <p>
                  <strong>Customer Account Page:</strong> This setting gives merchants control to their customer to which features they can access in the subscriptions.
                  <figure>
                    <img src="<?php echo $image_folder; ?>customer_account_page.png" alt="img" class="img-fluid w-100">
                  </figure>
                </p>

                <p>
                  <strong>Email Configuration:</strong>
                  <ol>
                    <li>
                    From here you can set email configuration to send mail from your server:
                      <ul>
                        <li>
                        Email SMTP Configuration - Add email configuration settings to send mail from your server.
                        </li>
                        <li>
                         For using Gmail SMTP please enable these 2 options for the gmail ID you want to send mail from - Unlock Captcha & Less Secure Apps
                        </li>
                        <li>
                         Email Subject Configuration - Add email subject to send subject in email template.
                        </li>
                        <li>
                         Test SMTP Mail - Please test the SMTP settings by sending a test mail.
                        </li>
                      </ul>
                    </li>
                  </ol>
                    <figure>
                    <img src="<?php echo $image_folder; ?>email_configuration.png" alt="img" class="img-fluid w-100">
                  </figure>
                </p>

                <p>
                  <strong>Email Setting:</strong>
                  <ol>
                    <li>
                      From here you can set email template setting:
                      <ul>
                        <li>
                        Email Footer Text - Merchant can add custom text here which will be visible at the footer of the email template.
                        </li>
                        <li>
                         Email Logo - Merchant can add logo url here after uploading file in the shopify admin, the logo will be visible in the email template.
                        </li>
                        <li>
                         Social Links - Merchant can add or remove social links from the email template.
                        </li>
                      </ul>
                    </li>
                  </ol>
                    <figure>
                    <img src="<?php echo $image_folder; ?>email_setting.png" alt="img" class="img-fluid w-100">
                  </figure>
                </p>
                <p>
                  <strong>Email Notification:</strong> This setting gives merchants control to send all or some specific emails to the customer when any change occur in the subscription.
                  <figure>
                    <img src="<?php echo $image_folder; ?>email_notification.png" alt="img" class="img-fluid w-100">
                  </figure>
                </p>
              </section>
              <hr class="divider" />
              <section id="idocs_analytics">
                <p>
                  <strong>Analytics:</strong>
                  <ol>
                    <li>
                      Here merchant can analyze the subscription orders and graphical representation of their orders :-
                      <ul>
                        <li>
                          Merchant will see the total sale of their subscription orders, this will include the first order's subscription products price total and the recurring order total.
                        </li>
                        <li>
                          Count of the total Subscription plan group existing in the app.
                        </li>
                        <li>
                          Count of the total active subscription.
                        </li>
                        <li>Count of the Total Pause subscription</li>
                        <li>Count of the Total Recurring Subscriptions Orders</li>
                        <li>Graphical representation of the recurring subscription order with order total on the daily basis, merchant can see the data of the particular interval of the dates.</li>
                        <li>Merchant can see the most purchased selling plans.</li>
                      </ul>
                    </li>
                  </ol>
                  <figure>
                    <img src="<?php echo $image_folder; ?>sd_analytics.png" alt="img" class="img-fluid w-100">
                  </figure>
                </p>
              </section>
              <hr class="divider" />
              <section id="idocs_theme_integrate">
                <p>
                  <strong>Theme Integration :</strong>
                  <ol>
                    <li>
                      To add or remove subscription from the different themes merchant need to integrate the theme.
                      <ul>
                        <li>
                          Select the theme from which you want to add or remove the subsctiption.
                        </li>
                        <li>
                          Then click on the "Enable/Disable App", this will redirect you in the theme customizer where you will save the setting.
                        </li>
                      </ul>
                    </li>
                  </ol>
                  <figure>
                    <img src="<?php echo $image_folder; ?>theme_integrate.png" alt="img" class="img-fluid w-100">
                  </figure>
                </p>
               </section>
              <section id="idocs_contactUs">
                <p>
                  <strong>Contact Us:</strong>Merchant can ask any query about the app and also can share the suggestion about the app so that we can enhance the feature of the app according to the merchant need.
                  <figure>
                    <img src="<?php echo $image_folder; ?>sd_contactUs.png" alt="img" class="img-fluid w-100">
                  </figure>
                </p>
              </section>
              <hr class="divider" />
              <!-- FAQ	============================ -->
              <section id="idocs_faq">
                <h2>FAQ</h2>
                <h4>Why are My Plans not showing on the product page?</h4>
                <ul>
                  <li>Make Sure you have enabled the app from Theme Customizer. Follow the Steps in the Shopify Admin Panel: -
                    <i>Online Store->Current Theme->Customize->Theme Settings->App embeds-> Advanced Subscription-> Enable the toggle button -> Save</i>
                    Refresh the product page to see the “Purchase Options” Widget.
                  </li>
                  <li>Most Importantly, as per Shopify default standards, have to have Shopify Payments Setup for the options (in Settings -> Payments) to appear on a product page. </li>
                  <li>Product Inventory needs to be more than zero. </li>
                </ul>
                <p>If still facing issues, Kindly contact us at <a href="mailto:your-email.com">your-email.com</a> </p>
                <p class="text-4">
                  A FAQ is a list of frequently asked questions (FAQs) and answers
                  on a particular topic.
                </p>

                <h4>Why are my customers not able to see their subscription dashboard? </h4>
                <p>Kindly make sure you have enabled customer accounts. Follow these steps in the Shopify Admin Panel for the same: - </p>
                <p><i>Settings -> Checkout -> Customer accounts,</i> here you can either select
                  “Accounts are optional” or “Accounts are required”</p>

                  <p><strong>Accounts are required: </strong>If this is selected, the User while making checkout will be first prompted to signup/login to the site.</p>

                  <p><strong>Accounts are optional: </strong>If this is selected, the User will not be prompted to signup/Login while they make a checkout but once checkout is completed & order is created, automatically customer account will be created with the email id used in the order & customer will receive an email to set the account password.</p>
              </section>

            </div>
          </div>
        </div>
      </div>
      <!-- Content end -->
    </div>
    <!-- Document Wrapper end -->
<?php include("footer.php"); ?>

