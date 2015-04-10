<?php
/**
 * Created by PhpStorm.
 * User: Ted Sanders (Digital Marketing Manager NOT Developer :)
 * Date: 3/19/15
 * Time: 2:55 PM
 */

    require_once 'http://www.gygicookingclasses.com/class-wc-api-client.php';

    $consumer_key = 'ck_e2e397e851c1734d3c0ba241facb30a3'; // Consumer key for user "restapi"
    $consumer_secret = 'cs_0649904db1ac6cf12339464224a64840'; // Consumer secret for user "restapi"
    $store_url = 'https://www.gygicookingclasses.com/wc-api/v2/'; // Base URL for connecting woocommerce store.

    // Initialize the class
    $wc_api = new WC_API_Client( $consumer_key, $consumer_secret, $store_url );

    // Get all products
    $orders = $wc_api->get_orders();
   // Print all products
   // print_r( $orders );

// Get Index
//print_r( $wc_api->get_index() );
// Get all orders
//print_r( $wc_api->get_orders( array( 'status' => 'completed' ) ) );
// Get a single order by id
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
//Get customer count
print_r( $wc_api->get_customers_count() );
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



 // Create order in Magento from woocommerce 3/20/2015

class Company_Module_Model_HandleOrderCreate extends Mage_Core_Model_Abstract
{

    private $_storeId = '1';
    private $_groupId = '1';
    private $_sendConfirmation = '0';

    private $orderData = array();
    private $_product;

    private $_sourceCustomer;
    private $_sourceOrder;

    public function setOrderInfo(Varien_Object $sourceOrder, Mage_Customer_Model_Customer $sourceCustomer)
    {
        $this->_sourceOrder = $sourceOrder;
        $this->_sourceCustomer = $sourceCustomer;

//You can extract/refactor this if you have more than one product, etc.
        $this->_product = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('sku', 'Some value here...')
            ->addAttributeToSelect('*')
            ->getFirstItem();

//Load full product data to product object
        $this->_product->load($this->_product->getId());

        $this->orderData = array(
            'session'       => array(
                'customer_id'   => $this->_sourceCustomer->getId(),
                'store_id'      => $this->_storeId,
            ),
            'payment'       => array(
                'method'    => 'checkmo',
            ),
            'add_products'  =>array(
                $this->_product->getId() => array('qty' => 1),
            ),
            'order' => array(
                'currency' => 'USD',
                'account' => array(
                    'group_id' => $this->_groupId,
                    'email' => $this->_sourceCustomer->getEmail()
                ),
                'billing_address' => array(
                    'customer_address_id' => $this->_sourceCustomer->getCustomerAddressId(),
                    'prefix' => '',
                    'firstname' => $this->_sourceCustomer->getFirstname(),
                    'middlename' => '',
                    'lastname' => $this->_sourceCustomer->getLastname(),
                    'suffix' => '',
                    'company' => '',
                    'street' => array($this->_sourceCustomer->getStreet(),''),
                    'city' => $this->_sourceCustomer->getCity(),
                    'country_id' => $this->_sourceCustomer->getCountryId(),
                    'region' => '',
                    'region_id' => $this->_sourceCustomer->getRegionId(),
                    'postcode' => $this->_sourceCustomer->getPostcode(),
                    'telephone' => $this->_sourceCustomer->getTelephone(),
                    'fax' => '',
                ),
                'shipping_address' => array(
                    'customer_address_id' => $this->_sourceCustomer->getCustomerAddressId(),
                    'prefix' => '',
                    'firstname' => $this->_sourceCustomer->getFirstname(),
                    'middlename' => '',
                    'lastname' => $this->_sourceCustomer->getLastname(),
                    'suffix' => '',
                    'company' => '',
                    'street' => array($this->_sourceCustomer->getStreet(),''),
                    'city' => $this->_sourceCustomer->getCity(),
                    'country_id' => $this->_sourceCustomer->getCountryId(),
                    'region' => '',
                    'region_id' => $this->_sourceCustomer->getRegionId(),
                    'postcode' => $this->_sourceCustomer->getPostcode(),
                    'telephone' => $this->_sourceCustomer->getTelephone(),
                    'fax' => '',
                ),
                'shipping_method' => 'flatrate_flatrate',
                'comment' => array(
                    'customer_note' => 'This order has been programmatically created via import script.',
                ),
                'send_confirmation' => $this->_sendConfirmation
            ),
        );
    }

    /**
     * Retrieve order create model
     *
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    protected function _getOrderCreateModel()
    {
        return Mage::getSingleton('adminhtml/sales_order_create');
    }

    /**
     * Retrieve session object
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * Initialize order creation session data
     *
     * @param array $data
     * @return Mage_Adminhtml_Sales_Order_CreateController
     */
    protected function _initSession($data)
    {
        /* Get/identify customer */
        if (!empty($data['customer_id'])) {
            $this->_getSession()->setCustomerId((int) $data['customer_id']);
        }

        /* Get/identify store */
        if (!empty($data['store_id'])) {
            $this->_getSession()->setStoreId((int) $data['store_id']);
        }

        return $this;
    }

    /**
     * Creates order
     */
    public function create()
    {
        $orderData = $this->orderData;

        if (!empty($orderData)) {

            $this->_initSession($orderData['session']);

            try {
                $this->_processQuote($orderData);
                if (!empty($orderData['payment'])) {
                    $this->_getOrderCreateModel()->setPaymentData($orderData['payment']);
                    $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($orderData['payment']);
                }

                $item = $this->_getOrderCreateModel()->getQuote()->getItemByProduct($this->_product);

                $item->addOption(new Varien_Object(
                    array(
                        'product' => $this->_product,
                        'code' => 'option_ids',
                        'value' => '5' /* Option id goes here. If more options, then comma separate */
                    )
                ));

                $item->addOption(new Varien_Object(
                    array(
                        'product' => $this->_product,
                        'code' => 'option_5',
                        'value' => 'Some value here'
                    )
                ));

                Mage::app()->getStore()->setConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_ENABLED, "0");

                $_order = $this->_getOrderCreateModel()
                    ->importPostData($orderData['order'])
                    ->createOrder();

                $this->_getSession()->clear();
                Mage::unregister('rule_data');

                return $_order;
            }
            catch (Exception $e){
                Mage::log("Order save error...");
            }
        }

        return null;
    }

    protected function _processQuote($data = array())
    {
        /* Saving order data */
        if (!empty($data['order'])) {
            $this->_getOrderCreateModel()->importPostData($data['order']);
        }

        $this->_getOrderCreateModel()->getBillingAddress();
        $this->_getOrderCreateModel()->setShippingAsBilling(true);

        /* Just like adding products from Magento admin grid */
        if (!empty($data['add_products'])) {
            $this->_getOrderCreateModel()->addProducts($data['add_products']);
        }

        /* Collect shipping rates */
        $this->_getOrderCreateModel()->collectShippingRates();

        /* Add payment data */
        if (!empty($data['payment'])) {
            $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($data['payment']);
        }

        $this->_getOrderCreateModel()
            ->initRuleData()
            ->saveQuote();

        if (!empty($data['payment'])) {
            $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($data['payment']);
        }

        return $this;
    }
}

?>