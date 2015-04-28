

<?php
/**
 * Created by PhpStorm.
 * User: Ted Sanders Digital Marketing Manager not Developer put smiley face here
 * Date: 3/21/15
 * Time: 10:50 AM
 */

// THIS IS THE OPEN ACTIVE INTEGRATION FILE 

// Open Woocommerce REST api utilizing 'class-wc-api-client.php'
   error_reporting( E_ALL );
   ini_set( 'display_errors', 'On' );
   require_once 'class-wc-api-client.php';

//Consumer key and secret for www.gygicookingclasses.com

    $consumer_key = 'ck_a547bd543c6a880850054c2a4414f50d'; //  Consumer Key here
    $consumer_secret = 'cs_a793b3d1645445b9469f7c465ec4a913'; // Consumer Secret here
    $store_url = 'https://www.gygicookingclasses.com/'; // Base URL for connecting woocommerce store.

    // Initialize the class WC_API_CLIENT
$wc_api = new WC_API_Client( $consumer_key, $consumer_secret, $store_url );

    // Get all orders
    $orders = $wc_api->get_orders();
   // Print all products
   echo "<pre>";
  //print_r( $orders );exit;
   //print_r($orders->orders[0]);
//print_r( $wc_api->get_order( 166 ) );
// Get orders count
//print_r( $wc_api->get_orders_count() );
// Get order notes for a specific order
//print_r( $wc_api->get_order_notes( 166 ) );
// Update order status
//print_r( $wc_api->update_order( 166, $data = array( 'status' => 'failed' ) ) );
// Get all coupons
//print_r( $wc_api->get_coupons() );
// Get coupon by id
//print_r( $wc_api->get_coupon( 173 ) );
// Get coupon by code
//print_r( $wc_api->get_coupon_by_code( 'test coupon' ) );
// Get coupons count
//print_r( $wc_api->get_coupons_count() );
// Get customers
//print_r( $wc_api->get_customers() );
// Get customer by id
//print_r( $wc_api->get_customer( 2 ) );
// Get customer count
//print_r( $wc_api->get_customers_count() );
// Get customer orders
//print_r( $wc_api->get_customer_orders( 2 ) );
// Get all products
//print_r( $wc_api->get_products() );
// Get a single product by id
//print_r( $wc_api->get_product( 167 ) );
// Get products count
//print_r( $wc_api->get_products_count() );
// Get product reviews
//print_r( $wc_api->get_product_reviews( 167 ) );
// Get reports
//print_r( $wc_api->get_reports() );
// Get sales report
//print_r( $wc_api->get_sales_report() );
// Get top sellers report
// print_r( $wc_api->get_top_sellers_report() );

 // Create order in Magento from WooCommerce using a direct database connection  3/20/2015
//echo $orders->orders[0]->currency;ex
$username = "gygi_magento";
$password = "03alpqasZA!@";
$hostname = "localhost"; 

//connection to gygicookingclasses.com database
$dbhandle = mysql_connect($hostname, $username, $password) 
  or die("Unable to connect to MySQL");
echo "Connected to MySQL<br>";
//select a database to work with
$selected = mysql_select_db("gygi_magento",$dbhandle) 
  or die("Could not select examples");

$result=mysql_query("SELECT * FROM `eav_entity_store` WHERE `woo_order_no` = '".$orders->orders[0]->order_number."' ORDER BY `entity_store_id` DESC ");
if(mysql_num_rows($result)>0)
{
  echo 'Invalid Entry';
  header('location:https://www.gygicookingclasses.com/checkout/order-received/'.$_GET['oid'].'?key='.$_GET['orderkey'].'&ip=1');
}
else { 
//mysql_query("INSERT INTO `sales_flat_order`(woo_order_no)VALUES ('".$orders->orders[0]->order_number."')");

require_once 'app/Mage.php';
umask(0);
Mage::app('default');
//$orders->orders[0];
$id=3; // get Customer Id
$customer = Mage::getModel('customer/customer')->load($id);

$transaction = Mage::getModel('core/resource_transaction');
$storeId = $customer->getStoreId();
$reservedOrderId = Mage::getSingleton('eav/config')->getEntityType('order')->fetchNewIncrementId($storeId);
mysql_query("UPDATE `eav_entity_store` SET `woo_order_no` = '".$orders->orders[0]->order_number."' WHERE `increment_last_id` = '".$reservedOrderId."'");
$order = Mage::getModel('sales/order')
->setIncrementId($reservedOrderId)
->setStoreId($storeId)
->setQuoteId(0)
->setGlobal_currency_code($orders->orders[0]->currency)
->setBase_currency_code($orders->orders[0]->currency)
->setStore_currency_code($orders->orders[0]->currency)
->setOrder_currency_code($orders->orders[0]->currency);
//Set your store currency USD or any other

// define customer data from woocommerce (gygicookingclasses.com) to Magento (gygi.com)
$order->setCustomer_email($orders->orders[0]->billing_address->email)
->setCustomerFirstname($orders->orders[0]->billing_address->first_name)
->setCustomerLastname($orders->orders[0]->billing_address->last_name)
->setCustomerGroupId('1')
->setCustomer_is_guest(0)
->setCustomer($customer);

// set Billing Address from woocommerce to magento
$billing = $customer->getDefaultBillingAddress();
$billingAddress = Mage::getModel('sales/order_address')
->setStoreId($storeId)
->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_BILLING)
->setCustomerId($customer->getId())
->setCustomerAddressId($customer->getDefaultBilling())
->setCustomer_address_id($billing->getEntityId())
->setPrefix($billing->getPrefix())
->setFirstname($orders->orders[0]->billing_address->first_name)
->setMiddlename('')
->setLastname($orders->orders[0]->billing_address->last_name)
->setSuffix($billing->getSuffix())
->setCompany($orders->orders[0]->billing_address->company)
->setStreet($orders->orders[0]->billing_address->address_1)
->setCity($orders->orders[0]->billing_address->city)
->setCountry_id($orders->orders[0]->billing_address->country)
->setRegion($orders->orders[0]->billing_address->state)
->setRegion_id($orders->orders[0]->billing_address->state)
->setPostcode($orders->orders[0]->billing_address->postcode)
->setTelephone($orders->orders[0]->billing_address->phone)
->setFax('');
$order->setBillingAddress($billingAddress);

    // set shipping from woocommerce to Magento (default to In Store pickup
$shipping = $customer->getDefaultShippingAddress();
$shippingAddress = Mage::getModel('sales/order_address')
->setStoreId($storeId)
->setAddressType(Mage_Sales_Model_Quote_Address::TYPE_SHIPPING)
->setCustomerId($customer->getId())
->setCustomerAddressId($customer->getDefaultShipping())
->setCustomer_address_id($shipping->getEntityId())
->setPrefix($shipping->getPrefix())
->setFirstname($orders->orders[0]->shipping_address->first_name)
->setMiddlename('')
->setLastname($orders->orders[0]->shipping_address->last_name)
->setSuffix($shipping->getSuffix())
->setCompany($orders->orders[0]->shipping_address->company)
->setStreet($orders->orders[0]->shipping_address->address_1)
->setCity($orders->orders[0]->shipping_address->city)
->setCountry_id($orders->orders[0]->shipping_address->country)
->setRegion($orders->orders[0]->shipping_address->state)
->setRegion_id($orders->orders[0]->shipping_address->state)
->setPostcode($orders->orders[0]->shipping_address->postcode)
->setTelephone($orders->orders[0]->billing->phone)
->setFax($shipping->getFax());

$order->setShippingAddress($shippingAddress)
->setShipping_method('flatrate_flatrate');
//->setShippingDescription($this->getCarrierName('flatrate'));
//some error i am getting here need to solve further
//you can set your payment method name here as per your need
//TS - Payment options will include PayPal, Authorize.net, and Amazon Payments
$orderPayment = Mage::getModel('sales/order_payment')
->setStoreId($storeId)
->setCustomerPaymentId(0)
->setMethod('purchaseorder')
->setPo_number(' – ');
$order->setPayment($orderPayment);

// let say, we have 2 products
//check that your products exists
//need to add code for configurable products if any **No configurable products are needed here.  This will push just simple products.
// no configurable products will be added (for now) FYI
$subTotal = 0;
$products = array();
$i=1;
foreach($orders->orders[0]->line_items as $item)
{
$products[$i]=array('qty'=> $item->quantity ,'price'=> $item->price ,'name'=>$item->name , 'sku'=>$item->sku  , 'subtotal'=>$item->subtotal , 'total'=>$item->total );	
$i++;
}
//print_r($products);exit;

foreach ($products as $productId=>$product) {
$_product = Mage::getModel('catalog/product')->load($productId);
$rowTotal = $product['price'] * $product['qty'];
$orderItem = Mage::getModel('sales/order_item')
->setStoreId($storeId)
->setQuoteItemId(0)
->setQuoteParentItemId(NULL)
->setProductId($productId)
->setProductType($_product->getTypeId())
->setQtyBackordered(NULL)
->setTotalQtyOrdered($product['qty'])
->setQtyOrdered($product['qty'])
->setName($product['name'])
->setSku($product['sku'])
->setPrice($product['price'])
->setBasePrice($product['price'])
->setOriginalPrice($product['price'])
->setRowTotal($rowTotal)
->setBaseRowTotal($rowTotal);

$subTotal += $rowTotal;
$order->addItem($orderItem);
}

$order->setSubtotal($subTotal)
->setBaseSubtotal($subTotal)
->setGrandTotal($subTotal)
->setBaseGrandTotal($subTotal);

$transaction->addObject($order);
$transaction->addCommitCallback(array($order, 'place'));
$transaction->addCommitCallback(array($order, 'save'));
$transaction->save();

header('location:https://www.gygicookingclasses.com/checkout/order-received/'.$_GET['oid'].'?key='.$_GET['orderkey'].'&ip=1');
}


?>
