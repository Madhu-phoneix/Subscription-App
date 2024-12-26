<?php
/*
This file contains all global variables & db connection & API Library Connection
*/
/* ==============  Debug Mode ==============*/
ob_start();
set_time_limit(0);
// ini_set("display_errors", 1);
// error_reporting(E_ALL);

/* ==============  Include GraphQL and Shopify Curl support file to run API's ==============*/
$dirPath =dirname(dirname(__DIR__));
require_once($dirPath."/application/library/shopify.php");
require_once($dirPath."/graphLoad/autoload.php");

if(isset($_GET['shop'])){
 $store = $_GET['shop'];
}

class Keys {
	/* ==============  App Standardize Variables Declaration ==============*/
	public $MYSQL_HOST,$MYSQL_DB,$MYSQL_USER,$MYSQL_PASS,$SHOPIFY_APIKEY,$SHOPIFY_SECRET,$SHOPIFY_SCOPES,$SHOPIFY_DIRECTORY_NAME,$SHOPIFY_API_VERSION,$SHOPIFY_DOMAIN_URL,$SHOPIFY_REDIRECT_URI,$db;

	public function __construct(){
		$this->TESTING_STORES = array('demo-subscription-app-testing.myshopify.com','dev-subscription.myshopify.com','dev-nisha-subscription.myshopify.com','testing-neha-subscription.myshopify.com','testing-advanced-wholesale-pro.myshopify.com');
		$this->MYSQL_HOST = "localhost";
		$this->MYSQL_DB = "DATABASE_NAME";
		$this->MYSQL_USER = "MYSQL_USER";
		$this->MYSQL_PASS = "MYSQL_PASSWORD";
		$this->SHOPIFY_APIKEY = "shopify_api_key";
		$this->SHOPIFY_SECRET = "shopify_secret_key";
		// $this->SHOPIFY_SCOPES = "read_content,write_content,read_themes,write_themes,read_products,write_products,read_customers,write_customers,read_orders,write_orders,read_own_subscription_contracts,write_own_subscription_contracts,read_customer_payment_methods,read_shopify_payments_accounts,read_translations,write_translations,read_merchant_managed_fulfillment_orders,write_merchant_managed_fulfillment_orders,write_third_party_fulfillment_orders";
		$this->SHOPIFY_SCOPES = "read_translations,read_themes,read_products,write_products,read_customers,write_customers,read_orders,write_orders,read_own_subscription_contracts,write_own_subscription_contracts,read_customer_payment_methods,read_shopify_payments_accounts,read_merchant_managed_fulfillment_orders,write_merchant_managed_fulfillment_orders,read_third_party_fulfillment_orders,write_third_party_fulfillment_orders";
		$this->SHOPIFY_API_VERSION = "2023-07";
		$this->SHOPIFY_DIRECTORY_NAME = "";
		$this->SHOPIFY_DOMAIN_URL = "https://your-domain.com";
		$this->SHOPIFY_REDIRECT_URI = "https://your-domain.com/admin/memberPlans.php?billingStatus=unattempted";
		$this->image_folder = "https://your-domain.com/application/assets/images/";
		$this->db = $this->connection();
		$this->created_at = date('Y-m-d H:i:s');
		/* ==============  App Error Variables Declaration  ==============*/
		$this->installtion_error =	"Something went wrong in Installation Process ,Please try installing again or contact Us at <a href = 'mailto: your-email.com'>your-email.com</a>";
		$this->app_extension_id = "app_extension_id";
		$this->theme_block_name = 'subscription_block';
        $this->app_name = 'your_app_name';
		$this->theme_assets_fields = array("store_id","asset_name","asset_id");
		$this->subscritionOrderContractProductDetails_fields = array("store_id","contract_id","product_id","variant_id","product_name","product_handle","variant_name","variant_image",'recurring_computed_price',"quantity","subscription_price","contract_line_item_id","created_at");
        $this->contract_sale_fields = array("store_id","contract_id","total_sale","created_at");
		$this->subscriptionOrderContract_fields = array("store_id","contract_id","contract_products","order_id","order_no","shopify_customer_id","billing_policy_value","delivery_policy_value","delivery_billing_type",'min_cycle','max_cycle','anchor_day','cut_off_days','after_cycle',"selling_plan_id","discount_type","discount_value",'recurring_discount_type','recurring_discount_value',"next_billing_date","firstRenewal_dateTime","created_at","updated_at");
		$this->customers_fields = array("store_id","shopify_customer_id","email","name","created_at");
		$this->storeInstallOffers_fields = array("","","");
		$this->theme_assets_values = array();
		$this->subscritionOrderContractProductDetails_values = array();
		$this->subscriptionOrderContract_values = array();
		$this->contract_sale_values = array();
		$this->customers_values = array();
		$this->input_max_character = 50;
	}

	/* ==============  Database Connection ==============*/
	public function connection(){
		try {
			$result= new PDO("mysql:host=$this->MYSQL_HOST;dbname=$this->MYSQL_DB", $this->MYSQL_USER, $this->MYSQL_PASS);
		} catch (PDOException $pe) {
			die("Could not connect to the database $MYSQL_DB :" . $pe->getMessage());
		}
		return $result;
	}
	public function customQuery($query){
		$result = $this->db->query($query);
		$row_data = $result->fetchAll(PDO::FETCH_ASSOC);
		// $this->db=null; //close db connection
		return $row_data;	// return associative array
	}
	public function __destruct()
    {
        $this->db = null;
    }

}//Class End


