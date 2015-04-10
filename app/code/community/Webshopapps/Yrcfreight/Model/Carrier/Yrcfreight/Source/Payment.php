<?php
/* YRC Freight Shipping
 *
 * @category   Webshopapps
 * @package    Webshopapps_Yrcfreight
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */

class Webshopapps_Yrcfreight_Model_Carrier_Yrcfreight_Source_Payment {
	
public function toOptionArray()
    {
        $yrcfreight = Mage::getSingleton('yrcfreight/carrier_yrcfreight');
        $arr = array();
        foreach ($yrcfreight->getCode('payment') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>Mage::helper('usa')->__($v));
        }
        return $arr;
    }
}
