<?php
/* YRC Freight Shipping
 *
 * @category   Webshopapps
 * @package    Webshopapps_Wsafedexfreight
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */

class Webshopapps_Wsafedexfreight_Model_Shipping_Carrier_Wsafedexfreight_Source_Role {
	
public function toOptionArray()
    {
        $wsafedexfreight = Mage::getSingleton('wsafedexfreight/shipping_carrier_wsafedexfreight');
        $arr = array();
        foreach ($wsafedexfreight->getCode('role') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>Mage::helper('usa')->__($v));
        }
        return $arr;
    }
}
