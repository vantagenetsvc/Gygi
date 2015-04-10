<?php

 /**
 * WebShopApps Shipping Module
 *
 * @category    WebShopApps
 * @package     WebShopApps_Freightcommon
 * User         Joshua Stewart
 * Date         03/03/2014
 * Time         15:10
 * @copyright   Copyright (c) 2014 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2014, Zowta, LLC - US license
 * @license     http://www.WebShopApps.com/license/license.txt - Commercial license
 *
 */

require_once 'IWD/OnepageCheckout/controllers/IndexController.php';
//This overload isnt working on this controller

class Webshopapps_Wsafreightcommon_IwdonepagecheckoutController extends IWD_OnepageCheckout_IndexController
{
    public function getFreightAction()
    {
        if ($this->getRequest()->isGet()) {
            $liftgateRequired = $this->getRequest()->getParam('liftgate_required') == "true" ? true : false;
            $notifyRequired   = $this->getRequest()->getParam('notify_required') == "true" ? true : false;
            $insideRequired   = $this->getRequest()->getParam('inside_required') == "true" ? true : false;
            $shiptoType       = $this->getRequest()->getParam('shipto_type');
        } else {
            $liftgateRequired = false;
            $notifyRequired = false;
            $insideRequired = false;
            $shiptoType = 0;
        }

        $this->getOnepagecheckout()->getQuote()->getBillingAddress()->setLiftgateRequired($liftgateRequired);
        $this->getOnepagecheckout()->getQuote()->getBillingAddress()->setNotifyRequired($notifyRequired);
        $this->getOnepagecheckout()->getQuote()->getBillingAddress()->setInsideDelivery($insideRequired);
        $this->getOnepagecheckout()->getQuote()->getBillingAddress()->setShiptoType($shiptoType);

        $this->getOnepagecheckout()->getQuote()->getShippingAddress()->setLiftgateRequired($liftgateRequired);
        $this->getOnepagecheckout()->getQuote()->getShippingAddress()->setNotifyRequired($notifyRequired);
        $this->getOnepagecheckout()->getQuote()->getShippingAddress()->setInsideDelivery($insideRequired);
        $this->getOnepagecheckout()->getQuote()->getShippingAddress()->setShiptoType($shiptoType);

        $this->getOnepagecheckout()->getQuote()->getShippingAddress()->setCollectShippingRates(true);

        $this->getOnepagecheckout()->getQuote()->save();

        $this->getOnepagecheckout()->getQuote()->getShippingAddress()->collectShippingRates()->save();

        $result = $this->_getShippingMethodsHtml();

        $this->getResponse()->setBody($result);
    }
}
