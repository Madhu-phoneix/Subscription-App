<?php

$MYSQL_HOST = "localhost";
$MYSQL_DB = "DATABASE_NAME";
$MYSQL_USER = "DATABASE_USER";
$MYSQL_PASS = "DATABASE_PASSWORD";
$SHOPIFY_APIKEY = "SHOPIFY_API_KEY";
$SHOPIFY_SECRET =  "SHOPIFY_SECRET_KEY";
$API_SECRET_KEY = "SHOPIFY_SECRET_KEY";
$SHOPIFY_API_VERSION = "2024-04";
$SHOPIFY_DOMAIN_URL = "https://your-domain.com";
$SHOPIFY_DIRECTORY_NAME = "";
$SHOPIFY_REDIRECT_URI = "https://your-domain.com/admin/memberPlans.php?billingStatus=unattempted";
$SHOPIFY_SCOPES = "read_translations,read_themes,read_products,write_products,read_customers,write_customers,read_orders,write_orders,read_own_subscription_contracts,write_own_subscription_contracts,read_customer_payment_methods,read_shopify_payments_accounts,read_merchant_managed_fulfillment_orders,write_merchant_managed_fulfillment_orders,read_third_party_fulfillment_orders,write_third_party_fulfillment_orders,read_all_orders";
$image_folder = "https://your-domain.com/application/assets/images/";
$app_extension_id = "app_extension_id";
$theme_block_name = 'subscription_block';
$app_name = 'YOUR_APP_NAME';
try {
	$db = new PDO("mysql:host=$MYSQL_HOST;dbname=$MYSQL_DB", $MYSQL_USER, $MYSQL_PASS, array(PDO::ATTR_PERSISTENT => true));
	// echo "Connected to $MYSQL_DB at $MYSQL_HOST successfully.";
} catch (PDOException $pe) {
    file_put_contents($dirPath . "/application/assets/txt/webhooks/config.txt", "\n\n DB not connected. ". $pe->getMessage(), FILE_APPEND | LOCK_EX);
    // die("Could not connect to the database $MYSQL_DB :" . $pe->getMessage());
    die("Could not connect to the database :");
}

?>