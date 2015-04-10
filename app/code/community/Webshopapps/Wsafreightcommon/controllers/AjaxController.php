<?php

 /**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_$(PROJECT_NAME)
 * User         joshstewart
 * Date         08/07/2013
 * Time         12:00
 * @copyright   Copyright (c) $(YEAR) Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, $(YEAR), Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

// this is needed so can re-render the shipping results html
require_once 'Mage/Checkout/controllers/OnepageController.php';
class Webshopapps_Wsafreightcommon_AjaxController extends Mage_Checkout_OnepageController
{
    private $_rates;
    protected $_address;
    protected $_billingAddress;

    /**
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = null;

    protected $_checkoutSession;


    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')
            ->setHeader('Login-Required', 'true')
            ->sendResponse();
        return $this;
    }

    /**
     * Validate ajax request and redirect on failure
     *
     * @return bool
     */
    protected function _expireAjax()
    {
        if (!$this->_getQuote()->hasItems()
            || $this->_getQuote()->getHasError()
        ) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        $action = $this->getRequest()->getActionName();
        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
            && !in_array($action, array('index', 'progress'))) {
            $this->_ajaxRedirectResponse();
            return true;
        }

        return false;
    }

    /**
     * Cutdown version of getFreightAction.
     * This just sets the values on the shipping address, no collect rates or update of html required
     */
    public function setAddressAttributesAction(){
        if ($this->_expireAjax()) {
            return;
        }

        $this->_setAccessorials(false);
    }

    /**
     * Retrieves new freight rates with appropriate accessory charges added.
     */
    public function getFreightAction() {
        if ($this->_expireAjax()) {
            return;
        }

        $this->_setAccessorials();

        $result = $this->_getShippingMethodsHtml();

        $this->getResponse()->setBody($result);
    }

    protected function _setAccessorials() {
        if ($this->getRequest()->isGet()) {
            $liftgateRequired = $this->getRequest()->getParam('liftgate_required') == "true" ? true : false;
            $notifyRequired = $this->getRequest()->getParam('notify_required') == "true" ? true : false;
            $insideRequired = $this->getRequest()->getParam('inside_required') == "true" ? true : false;
            $shiptoType = $this->getRequest()->getParam('shipto_type');
        } else {
            $liftgateRequired = false;
            $notifyRequired = false;
            $insideRequired = false;
            $shiptoType = 0;
        }

        if(Mage::getStoreConfig('shipping/wsafreightcommon/default_address')) {
            $destType = Mage::getSingleton('wsafreightcommon/freightCommon')->getCode('dest_type_reverse', $shiptoType);
        } else {
            $destType = Mage::getSingleton('wsafreightcommon/freightCommon')->getCode('dest_type', $shiptoType);
        }

        if(Mage::helper('wsacommon')->isModuleEnabled
            ('Idev_OneStepCheckout', 'onestepcheckout/general/rewrite_checkout_links')) {

            $billingAddress = $this->getBillingAddress();
            $billingAddress->setLiftgateRequired($liftgateRequired)
                ->setNotifyRequired($notifyRequired)
                ->setInsideDelivery($insideRequired)
                ->setShiptoType($shiptoType)
                ->setDestType($destType)
                ->save();
        }

        $address = $this->_getShippingAddress();
        $address->setLiftgateRequired($liftgateRequired)
            ->setNotifyRequired($notifyRequired)
            ->setInsideDelivery($insideRequired)
            ->setDestType($destType)
            ->setShiptoType($shiptoType);

        $address->setCollectShippingRates(true);
        $this->_getQuote()->collectTotals();

        $address->save();
        $this->_getQuote()->save();

    }

    protected function _getShippingAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->_getQuote()->getShippingAddress();
        }
        return $this->_address;
    }

    protected function getBillingAddress()
    {
        if (empty($this->_billingAddress)) {
            $this->_billingAddress = $this->_getQuote()->getBillingAddress();
        }
        return $this->_billingAddress;
    }

    /**
     * Get frontend checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        if ($this->_quote === null) {
            $this->_checkoutSession = Mage::getSingleton('checkout/session');
        }
        return $this->_checkoutSession;
    }

    protected function _getQuote()
    {
        if ($this->_quote === null) {
            return $this->_getCheckout()->getQuote();
        }
        return $this->_quote;
    }

    /**
     * Added for OSC compatibility.
     *
     * @return string
     */
    protected function _getShippingMethodsHtml()
    {
        if(!Mage::helper('wsacommon')->isModuleEnabled('Idev_OneStepCheckout',
                    'onestepcheckout/general/rewrite_checkout_links')) {
            return parent::_getShippingMethodsHtml();
        }

        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('onestepcheckout_ajax_save_billing');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();

        return $output;
    }

}