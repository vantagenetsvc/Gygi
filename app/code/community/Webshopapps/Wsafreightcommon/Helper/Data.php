<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog data helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Webshopapps_Wsafreightcommon_Helper_Data extends Mage_Core_Helper_Abstract
{

    protected static $_fixLiftgateFee;
    protected static $_fixDeliveryType;
    protected static $_residentialFee;
    protected static $_liftgateFee;
    protected static $_insideDeliveryFee;
    protected static $_liveAccessories;
    protected static $_hazardous;
    protected static $_defaultFreightClass;
    protected static $_minFreightWeight;
    protected static $_fixedNotifyRequired;
    protected static $_active;
    protected static $_freeFreightResidentialFee;
    protected static $_freeFreightLiftgateFee;
    protected static $_notifyFee;
    protected static $_hideAccessorials;
    protected static $_hideAddressType;
    protected static $_hideLiftgate;
    protected static $_hideNotify;
    protected static $_hideInsideDelivery;
    protected static $_debug;
    protected static $_minDimWeight;
    protected static $_shipRules;
    protected static $_displayRules;


    protected static $_possibleFreightCarriers = array(
        'Webshopapps_Wsafreightcommon' => 'freefreight',
        'Webshopapps_Cerasisfreight' => 'cerasisfreight',
        'Webshopapps_Ctsfreight' => 'ctsfreight',
        'Webshopapps_Dmtrans' => 'dmtrans',
        'Webshopapps_Newgistics' => 'newgistics',
        'Webshopapps_Abffreight' => 'abffreight',
        'Webshopapps_Wsafedexfreight' => 'wsafedexfreight',
        'Webshopapps_Conwayfreight' => 'conwayfreight',
        'Webshopapps_Estesfreight' => 'estesfreight',
        'Webshopapps_Echofreight' => 'echofreight',
        'Webshopapps_Rlfreight' => 'rlfreight',
        'Webshopapps_Wsaupsfreight' => 'wsaupsfreight',
        'Webshopapps_Yrcfreight' => 'yrcfreight',
        'Webshopapps_Wsaolddominion' => 'wsaolddominion',
        'Webshopapps_Prostar' => 'prostar',
    );

    public static function isDebug()
    {
        if (self::$_debug == NULL) {
            self::$_debug = Mage::helper('wsalogger')->isDebug('Webshopapps_Wsafreightcommon');
        }
        return self::$_debug;
    }

    public static function getDefaultFreightClass()
    {
        if (self::$_defaultFreightClass == NULL) {
            self::$_defaultFreightClass = Mage::getStoreConfig('shipping/wsafreightcommon/default_freight_class');
        }
        return self::$_defaultFreightClass;
    }

    public static function isActive()
    {
        if (self::$_active == NULL) {
            self::$_active = count(self::getAllFreightCarriers()) > 0;
        }
        return self::$_active;
    }

    /**
     * This is used in DropShip to remove elements from the post array in the controller
     *
     * @return array
     */
    public function getAllAccessoryCodes()
    {
        return array('liftgate_required','notify_required','inside_required','shipto_type','allow_notify','inside_delivery');
    }

    public static function isFixedLiftgateFee()
    {
        if (self::$_fixLiftgateFee == NULL) {
            self::$_fixLiftgateFee = Mage::getStoreConfig('shipping/wsafreightcommon/apply_live_liftgate');
        }
        return self::$_fixLiftgateFee;
    }

    public static function isFixedDeliveryType()
    {
        if (self::$_fixDeliveryType == NULL) {
            self::$_fixDeliveryType = Mage::getStoreConfig('shipping/wsafreightcommon/apply_live_business');
        }
        return self::$_fixDeliveryType;
    }

    public static function isNotifyRequired()
    {
        if (self::$_fixedNotifyRequired == NULL) {
            self::$_fixedNotifyRequired = Mage::getStoreConfig('shipping/wsafreightcommon/apply_live_notify');
        }
        return self::$_fixedNotifyRequired;
    }

    public static function getResidentialFee()
    {
        if (self::$_residentialFee == NULL) {
            self::$_residentialFee = Mage::getStoreConfig('shipping/wsafreightcommon/residential_fee');
        }
        return self::$_residentialFee;
    }

    public static function getLiftgateFee()
    {
        if (self::$_liftgateFee == NULL) {
            self::$_liftgateFee = Mage::getStoreConfig('shipping/wsafreightcommon/liftgate_fee');
        }
        return self::$_liftgateFee;
    }

    public static function getInsideDeliveryFee()
    {
        if (self::$_insideDeliveryFee == NULL) {
            self::$_insideDeliveryFee = Mage::getStoreConfig('shipping/wsafreightcommon/inside_delivery_fee');
        }
        return self::$_insideDeliveryFee;
    }

    public static function getNotifyFee()
    {
        if (self::$_notifyFee == NULL) {
            self::$_notifyFee = Mage::getStoreConfig('shipping/wsafreightcommon/notify_fee');
        }
        return self::$_notifyFee;
    }

    public static function getUseLiveAccessories()
    {
        if (self::$_liveAccessories == NULL) {
            self::$_liveAccessories = Mage::getStoreConfig('shipping/wsafreightcommon/use_accessories');
        }
        return self::$_liveAccessories;
    }

    public static function isHazardous()
    {
        if (self::$_hazardous == NULL) {
            self::$_hazardous = Mage::getStoreConfig('shipping/wsafreightcommon/hazardous');
        }
        return self::$_hazardous;
    }

    public static function getMinFreightWeight()
    {
        if (self::$_minFreightWeight == NULL) {
            self::$_minFreightWeight = Mage::getStoreConfig('shipping/wsafreightcommon/min_weight');
        }
        return self::$_minFreightWeight;
    }

    public static function getMinDimWeight()
    {
        if (self::$_minDimWeight == NULL) {
            self::$_minDimWeight = Mage::getStoreConfig('shipping/wsafreightcommon/minimum_dimensions');
        }
        return self::$_minDimWeight;
    }

    public static function getFreeFreightResidentialFee()
    {
        if (self::$_freeFreightResidentialFee == NULL) {
            self::$_freeFreightResidentialFee = Mage::getStoreConfig('carriers/freefreight/residential_fee');
        }
        return self::$_freeFreightResidentialFee;
    }

    public static function getFreeFreightLiftgateFee()
    {
        if (self::$_freeFreightLiftgateFee == NULL) {
            self::$_freeFreightLiftgateFee = Mage::getStoreConfig('carriers/freefreight/liftgate_fee');
        }
        return self::$_freeFreightLiftgateFee;
    }


    protected static function _getAccessorials()
    {
        if (self::$_hideAccessorials == NULL) {
            self::$_hideAccessorials = explode(',', Mage::getStoreConfig('shipping/wsafreightcommon/hide_accessorials'));
        }
        return self::$_hideAccessorials;
    }



    public static function hideAddressType()
    {
        if (self::$_hideAddressType == NULL) {
            self::$_hideAddressType = in_array('address_type', self::_getAccessorials());
        }
        return self::$_hideAddressType;
    }

    public static function hideLiftgate()
    {
        if (self::$_hideLiftgate == NULL) {
            self::$_hideLiftgate = in_array('liftgate', self::_getAccessorials());
        }
        return self::$_hideLiftgate;
    }

    public static function hideNotify()
    {
        if (self::$_hideNotify == NULL) {
            self::$_hideNotify = in_array('notify', self::_getAccessorials());
        }
        return self::$_hideNotify;
    }

    public static function hideInsideDelivery()
    {
        if (self::$_hideInsideDelivery == NULL) {
            self::$_hideInsideDelivery = in_array('inside_delivery', self::_getAccessorials());
        }
        return self::$_hideInsideDelivery;
    }

    protected static function _getDisplayRules()
    {
        if (self::$_displayRules == NULL) {
            self::$_displayRules = explode(',', Mage::getStoreConfig('shipping/wsafreightcommon/display_freight_rules'));
        }
        return self::$_displayRules;
    }

    public static function displayProductShipsFreight()
    {
        if (self::$_hideInsideDelivery == NULL) {
            self::$_hideInsideDelivery = in_array('inside_delivery', self::_getAccessorials());
        }
        return self::$_hideInsideDelivery;
    }


    protected static function _getShipRules()
    {
        if (self::$_shipRules == NULL) {
            self::$_shipRules = explode(',', Mage::getStoreConfig('shipping/wsafreightcommon/ship_freight_rules'));
        }
        return self::$_shipRules;
    }



    public function getCustomDescription()
    {
        $description = trim(nl2br(Mage::getStoreConfig('shipping/wsafreightcommon/custom_description', Mage::app()->getStore()), true));

        return $description != '' ? $description : false;
    }

    public function isResSelectorEnabled()
    {
        if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Desttype', 'shipping/desttype/active') ||
            Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Wsavalidation', 'shipping/wsavalidation/active')
        ) {
            return true;
        }
        return false;
    }

    /**
     * Retrieves enabled freight carriers.
     */
    public static function getAllFreightCarriers()
    {
        $enabledCarriers = array();

        foreach (self::$_possibleFreightCarriers as $freightModuleName => $freightShortName) {
            if (Mage::helper('wsacommon')->isModuleEnabled($freightModuleName,
                'carriers/' . $freightShortName . '/active')
            ) {
                $enabledCarriers[] = $freightShortName;
            }

        }

        return $enabledCarriers;
    }

    public function displayAccessorialsAtCheckout()
    {
        $display = true;
        if (Mage::getStoreConfig('shipping/wsafreightcommon/apply_live_liftgate') &&
            Mage::getStoreConfig('shipping/wsafreightcommon/apply_live_business') &&
            (!$this->isNotifyOptionEnabled() ||
                ($this->isNotifyOptionEnabled() &&
                    Mage::getStoreConfig('shipping/wsafreightcommon/apply_live_notify')))
        ) {
            $display = false;
        }
        return $display;
    }

    private function getWeight($items)
    {
        $addressWeight = 0;
        foreach ($items as $item) {
            /**
             * Skip if this item is virtual
             */

            if ($item->getProduct()->isVirtual()) {
                continue;
            }
            /**
             * Children weight we calculate for parent
             */
            if ($item->getParentItem()) {
                continue;
            }

            if ($item->getHasChildren() && $item->isShipSeparately()) {
                foreach ($item->getChildren() as $child) {
                    if ($child->getProduct()->isVirtual()) {
                        continue;
                    }

                    if (!$item->getProduct()->getWeightType()) {
                        $itemWeight = $child->getWeight();
                        $itemQty = $item->getQty() * $child->getQty();
                        $rowWeight = $itemWeight * $itemQty;
                        $addressWeight += $rowWeight;

                    }
                }
                if ($item->getProduct()->getWeightType()) {
                    $itemWeight = $item->getWeight();
                    $rowWeight = $itemWeight * $item->getQty();
                    $addressWeight += $rowWeight;

                }
            } else {

                $itemWeight = $item->getWeight();
                $rowWeight = $itemWeight * $item->getQty();
                $addressWeight += $rowWeight;

            }
        }
        return $addressWeight;
    }

    public function getOptions()
    {
        $enabledFreightCarriers = $this->getAllFreightCarriers();
        $needles = array('echofreight','prostar');

        if(count(array_intersect($needles, $enabledFreightCarriers)) > 0) {
            if (Mage::getStoreConfig('shipping/wsafreightcommon/default_address', Mage::app()->getStore())) {
                $options = array(
                    Mage::helper('shipping')->__('Business'),
                    Mage::helper('shipping')->__('Residential'),
                    Mage::helper('shipping')->__('Construction Site'),
                    Mage::helper('shipping')->__('Trade Show')
                );
            } else {
                $options = array(
                    Mage::helper('shipping')->__('Residential'),
                    Mage::helper('shipping')->__('Business'),
                    Mage::helper('shipping')->__('Construction Site'),
                    Mage::helper('shipping')->__('Trade Show')
                );
            }
            return $options;
        } else {
            if (Mage::getStoreConfig('shipping/wsafreightcommon/default_address', Mage::app()->getStore())) {
                $options = array(
                    Mage::helper('shipping')->__('Business'),
                    Mage::helper('shipping')->__('Residential'),
                );
            } else {
                $options = array(
                    Mage::helper('shipping')->__('Residential'),
                    Mage::helper('shipping')->__('Business')
                );
            }
            return $options;
        }
    }

    /**
     * Checks to see if passed in items have and freight only items within.
     * Also gets the max dimensions. Havent changed method name as need it to be backwards compatible
     *
     * @param  array   $items
     * @param  int     $maxItemDimensions
     * @return bool
     */
    public function hasFreightItems($items, &$maxItemDimensions = 0)
    {
        $dimensionalActive = Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Shipusa', 'shipping/shipusa/active');

        $useParent = Mage::getStoreConfig('shipping/wsafreightcommon/use_parent');
        $hasFreightItems = false;


        foreach ($items as $item) {

            $product = Mage::helper('wsacommon/shipping')->getProduct($item, $useParent);

            $freightClass = $product->getData('freight_class');
            $fedexClass = $product->getData('fedex_freight_class');
            $freightClassSelect = $product->getData('freight_class_select');
            $mustShipFreightSet = $product->getData('must_ship_freight');

            if ($dimensionalActive) {
                $length = $product->getData('ship_length');
                $height = $product->getData('ship_height');
                $width = $product->getData('ship_width');
                $circumference = $length + 2 * ($height + $width);
                $maxItemDimensions = !(is_null($circumference)) && $maxItemDimensions < $circumference ? $circumference : $maxItemDimensions;
            }


            $freightClassSet = ($freightClass != "" || $fedexClass != "" || $freightClassSelect != "") ? true : false;

            if ($this->_productShipsFreight($mustShipFreightSet,$freightClassSet)) {
                $hasFreightItems = true;
            }

        }
        return $hasFreightItems;
    }

    /**
     * Ships via freight depending on shipping settings specified
     * @param $mustShipFreightSet
     * @param $freightClassSet
     * @return bool
     */
    protected function _productShipsFreight($mustShipFreightSet,$freightClassSet)
    {
        $shipRules = self::_getShipRules();

        if ($mustShipFreightSet && in_array('product_must',$shipRules )) {
            return true;
        }

        if ($freightClassSet) {
            if (in_array('product_freight',$shipRules)) {
                return true;
            }

            if ($mustShipFreightSet && in_array('product_freight_and_must',$shipRules)) {
                return true;
            }
        }

        return false;
    }

    public function isAddressTypeOptionEnabled($showFreight)
    {

        if (!$showFreight) {
            if (self::isResSelectorEnabled()) {
                return true;
            } else {
                return false;
            }
        }

        // else want to show freight, so can ignore res selector extn


        if (self::hideAddressType() ||
            $this->isFixedDeliveryType()
        ) {
            return false;
        }
        return true;
    }

    /**
     * Liftgate is enabled if not selected to hide and isnt set to a fixed liftgate fee
     * @return bool
     */
    public function isLiftgateEnabled()
    {
        if (self::hideLiftgate() || self::isFixedLiftgateFee()) {
            return false;
        }
        return true;
    }

    public function isNotifyOptionEnabled()
    {
        if (self::hideNotify()) {
            return false;
        }
        $enabledCarriers = $this->getAllFreightCarriers();

        $applicableCarriers = array('cerasisfreight', 'estesfreight', 'echofreight', 'prostar');

        foreach ($applicableCarriers as $carrier) {

            if (in_array($carrier, $enabledCarriers)) {
                return true;
            } else {
                continue;
            }
        }
        return false;
    }

    public function isInsideDeliveryEnabled()
    {
        if (self::hideInsideDelivery()) {
            return false;
        }

        $enabledCarriers = $this->getAllFreightCarriers();

        $applicableCarriers = array('cerasisfreight', 'estesfreight', 'prostar');

        foreach ($applicableCarriers as $carrier) {
            if (in_array($carrier, $enabledCarriers)) {
                return true;
            } else {
                continue;
            }
        }

        return false;
    }

    public function setDateOffset($todaysDate, $carrier)
    {

        $blackoutDeliveryDates = Mage::getStoreConfig('carriers/' . $carrier . '/delivery_dates');

        if (!empty($blackoutDeliveryDates)) {
            $blackoutDates = explode(",", $blackoutDeliveryDates);

            foreach ($blackoutDates as $dates) {

                $dates = str_replace("/", "", $dates);

                $year = substr($dates, -4);
                $month = substr($dates, 0, -6);
                $day = substr($dates, 2, -4);

                $changeDate = $year . $month . $day;

                if ($changeDate == $todaysDate) {
                    $todaysDate = date('Ymd', time() + 259200);
                    break;
                }
            }
        }
        return $todaysDate;
    }

    /**
     * If true will only show Freight carriers
     *
     * @param $cartWeight
     * @param $hasFreightItems
     * @param $maxItemDimensions
     * @return bool
     */
    public function showOnlyCommonFreight($cartWeight,$hasFreightItems,$maxItemDimensions)
    {
        $displayRules = self::_getDisplayRules();

        if (self::isDebug()) {
            Mage::helper('wsalogger/log')->postDebug('WSAFreightCommon', 'Display Rules', $displayRules);
            Mage::helper('wsalogger/log')->postDebug('WSAFreightCommon', 'Has Freight Items', $hasFreightItems);
            Mage::helper('wsalogger/log')->postDebug('WSAFreightCommon', 'Maximum Item Dimensions', $maxItemDimensions);
            Mage::helper('wsalogger/log')->postDebug('WSAFreightCommon', 'Min Dimension Threshold', self::getMinDimWeight());
            Mage::helper('wsalogger/log')->postDebug('WSAFreightCommon', 'Cart Weight', $cartWeight);
        }

        if ($hasFreightItems && in_array('product_ships_freight',$displayRules)) {
            return true;
        }

        if ($cartWeight >= self::getMinFreightWeight() && in_array('weight',$displayRules) ) {
            return true;
        }

        if (self::getMinDimWeight() > 0 &&
                $maxItemDimensions >= self::getMinDimWeight() && in_array('dimensions',$displayRules) ) { // FREIGHT-115
            return true;
        }

        if ($cartWeight >= self::getMinFreightWeight() && self::getMinDimWeight() > 0 &&  // FREIGHT-127
                $maxItemDimensions >= self::getMinDimWeight() &&  in_array('weight_dims',$displayRules) ) {
            return true;
        }

        return false;
    }

    protected function _freightCarrierInCart($items) {

        if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Dropcommon','carriers/dropship/active') ||
            Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Dropship','carriers/dropship/active') ) {
            $carriers = Mage::helper('dropcommon/shipcalculate')->getCarriersForItems($items);
            $freightCarrierNameArr = $this->getAllFreightCarriers();
            foreach ($carriers as $carrier) {
                if (in_array($carrier,$freightCarrierNameArr)) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }

    /**
     * Decide based on shipRules whether to show Freight Carrier or not
     * Looks at weight threshold, dimension threshold, whether freight items in the cart
     * If true will not show Freight Carriers
     *
     * @param           $items
     * @param  int      $cartWeight
     * @param  bool     $hasFreightItems
     * @param  int      $maxItemDimensions
     * @return bool
     */
    public function dontShowCommonFreight($items, $cartWeight = null,$hasFreightItems = null,$maxItemDimensions=null)
    {
        // if dropship installed might not be freight in this cart, as may be diff warehouse
        if (!$this->_freightCarrierInCart($items)) {
            return true;
        }

        if (is_null($cartWeight)) {
            $cartWeight = $this->getWeight($items);
        }

        if (is_null($maxItemDimensions) || is_null($hasFreightItems)) {
            $maxItemDimensions = 0;
            $hasFreightItems = $this->hasFreightItems($items, $maxItemDimensions);
        }

        if ($hasFreightItems) {
            return false; // show freight
        }

        $shipRules = self::_getShipRules();

        if ($cartWeight >= self::getMinFreightWeight() && in_array('weight',$shipRules) ) {
            return false;
        }

        if ($maxItemDimensions >= self::getMinDimWeight() && self::getMinDimWeight() > 0 && in_array('dimensions',$shipRules) ) {
            return false;
        }

        if ($cartWeight >= self::getMinFreightWeight() &&
            $maxItemDimensions >= self::getMinDimWeight() && self::getMinDimWeight() > 0 && in_array('weight_dims',$shipRules) ) {
            return false;
        }

        return true;
    }

    /**
     * Called from Shipping_Model_Shipping
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @param string $limitCarrier current carriers limited to
     * @return array
     */
    public function limitCarriersBasedOnFreightRules(Mage_Shipping_Model_Rate_Request $request, $limitCarrier)
    {
        if (count($this->getAllFreightCarriers()) < 1) {
            return $limitCarrier; // no currently active freight carriers
        }
        $items = $request->getAllItems();

        $alwaysShowCarriersArr = explode(',', Mage::getStoreConfig('shipping/wsafreightcommon/show_carriers'));
        $alwaysShowCarriersArr = $alwaysShowCarriersArr[0] == '' ? array() : $alwaysShowCarriersArr;

        $allFreightCarriers = $this->getAllFreightCarriers();

        $maxItemDimensions = 0;

        $hasFreightItems = $this->hasFreightItems($items, $maxItemDimensions);

        if ($this->showOnlyCommonFreight($request->getPackageWeight(),$hasFreightItems,$maxItemDimensions)) {

            if (!$limitCarrier) {
                $limitCarrier = array();

            } else {
                if (!is_array($limitCarrier)) {
                    $limitCarrier = array($limitCarrier);
                }
            }

            if (count($alwaysShowCarriersArr)) {
                foreach ($alwaysShowCarriersArr as $showCarrierCode) {
                    $limitCarrier[] = $showCarrierCode;
                }
            }

            //Always add in admin shipping. Reduces required config and potential support.
            if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Adminshipping')) {
                $limitCarrier[] = 'adminshipping';
                $limitCarrier = array_unique($limitCarrier);
            }

            foreach ($allFreightCarriers as $limit) {
                $limitCarrier[] = $limit;
            }

        } elseif ($this->dontShowCommonFreight($items, $request->getPackageWeight(),$hasFreightItems,$maxItemDimensions)) {
            // remove freight from showing
            if (!$limitCarrier) {
                $carriers = Mage::getStoreConfig('carriers', $request->getStoreId());
                foreach ($carriers as $carrierCode => $carrierConfig) {
                    if (in_array($carrierCode, $allFreightCarriers)) {
                        continue;
                    }
                    $limitCarrier[] = $carrierCode;
                }

            } else {
                if (!is_array($limitCarrier)) {
                    $limitCarrier = array($limitCarrier);
                }
                foreach ($limitCarrier as $carrierCode => $carrierConfig) {
                    if (in_array($carrierCode, $allFreightCarriers)) {
                        continue;
                    }
                    $limitCarrier[] = $carrierCode;
                }
            }

        }
        return $limitCarrier;
    }

    /**
     * Works out if a zero freight charge is allowed.
     * UPS Freight sometimes returns price/cost as $0 if error encountered.
     * This is done before free shipping is called and should be based on the raw charge from the carrier.
     *
     * @param string - extension code name
     * @return bool - free freight allowed
     */
    public function allowFreeFreight($extension)
    {
        if (Mage::getStoreConfig('carriers/' . $extension . '/apply_zero_fee') || $extension == 'freefreight') {
            return true;
        } else return false;
    }


    public function buildOrderViewHtml($order)
    {
        $htmlOutput = '';
        $innerHtmlOutput = $this->getFreightShippingInfo($order);

        if (!empty($innerHtmlOutput)) {

            $htmlOutput = '<div class="box-right"><div class="clear"></div><div class="entry-edit">';
            $htmlOutput .= '<div class="entry-edit-head">';
            $htmlOutput .= '<h4 class="icon-head head-shipping-method">';
            $htmlOutput .= Mage::helper("sales")->__("Freight Shipping Information");
            $htmlOutput .= '</h4>';
            $htmlOutput .= '</div><fieldset>';
            $htmlOutput .= $innerHtmlOutput;
            $htmlOutput .= '</fieldset> <div class="clear"/></div></div>';
        }

        return "'" . $htmlOutput . "'";
    }

    public function getFreightShippingInfo($order)
    {
        $innerHtmlOutput = '';
        if ($order->getFreightQuoteId()) {
            $innerHtmlOutput .= Mage::helper('sales')->__('Freight Reference Id - %s', $order->getFreightQuoteId());
            $innerHtmlOutput .= '<br />';
        }
        if ($order->getLiftgateRequired()) {
            $innerHtmlOutput .= Mage::helper('sales')->__('Liftgate Required');
            $innerHtmlOutput .= '<br />';
        }
        if ($order->getNotifyRequired()) {
            $innerHtmlOutput .= Mage::helper('sales')->__('Scheduled Appointment Required');
            $innerHtmlOutput .= '<br />';
        }
        if ($order->getInsideDelivery()) {
            $innerHtmlOutput .= Mage::helper('sales')->__('Inside Delivery Required');
            $innerHtmlOutput .= '<br />';
        }
        if (($order->getShiptoType() != '')) {
            switch($order->getShiptoType())
            {
                case 0: $innerHtmlOutput .= Mage::helper('sales')->__('Address Type - Residential');
                    break;
                case 1: $innerHtmlOutput .= Mage::helper('sales')->__('Address Type - Business');
                    break;
                case 2: $innerHtmlOutput .= Mage::helper('sales')->__('Address Type - Construction Site');
                    break;
                case 3: $innerHtmlOutput .= Mage::helper('sales')->__('Address Type - Trade Show');
                    break;
                default: $innerHtmlOutput .= Mage::helper('sales')->__('Address Type - Business');
            }

            $innerHtmlOutput .= '<br />';
        }

        return $innerHtmlOutput;
    }

    public function getDefaultShiptoType()
    {
        return Mage::getStoreConfig('shipping/wsafreightcommon/default_address', Mage::app()->getStore());
    }

    public function getDefaultLiftgate()
    {
        return Mage::getStoreConfig('shipping/wsafreightcommon/default_liftgate', Mage::app()->getStore());
    }


}
