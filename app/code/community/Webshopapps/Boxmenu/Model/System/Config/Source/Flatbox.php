<?php

/*
 * @category   Webshopapps
 * @package    Webshopapps_Boxmenu
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt
 * @author     Karen Baker <sales@webshopapps.com>
*/
class Webshopapps_Boxmenu_Model_System_Config_Source_Flatbox
{

    public function getCode($type, $code='')
    {
        $codes = array(

            'usps_box'=>array(
                '4' => Mage::helper('shipping')->__('Custom Box'),
                '1' => Mage::helper('shipping')->__('USPS Small Flat Rate Box'),
                '2' => Mage::helper('shipping')->__('USPS Medium Flat Rate Box'),
                '3' => Mage::helper('shipping')->__('USPS Large Flat Rate Box'),
            ),
            'usps_coded_box'=>array(
              //  '4' => Mage::helper('shipping')->__('Custom Box'),
                '1' => Mage::helper('shipping')->__('SM FLAT RATE BOX'),
                '2' => Mage::helper('shipping')->__('MD FLAT RATE BOX'),
                '3' => Mage::helper('shipping')->__('LG FLAT RATE BOX'),
            ),

        );

        if (!isset($codes[$type])) {
            Mage::helper('wsalogger/log')->postCritical('boxmenu','Invalid Flat Box Code',$code);
        }

        if (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            Mage::helper('wsalogger/log')->postCritical('boxmenu','Invalid Flat Box Code',$code);
        }

        return $codes[$type][$code];
    }
}
