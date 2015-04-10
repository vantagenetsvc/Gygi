<?php

/**
 * Magento Webshopapps Module
 *
 * @category   Webshopapps
 * @package    Webshopapps Wsacommon
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    www.webshopapps.com/license/license.txt
 * @author     Karen Baker <sales@webshopapps.com>
*/

class Webshopapps_Wsafedexfreight_Model_Observer extends Mage_Core_Model_Abstract
{
	public function postError($observer) {
		if (!Mage::helper('wsacommon')->checkItems('Y2FycmllcnMvd3NhZmVkZXhmcmVpZ2h0L3NoaXBfb25jZQ==',
     		'd2Fyd29ybGQ=','Y2FycmllcnMvd3NhZmVkZXhmcmVpZ2h0L3NlcmlhbA==')) {
				$session = Mage::getSingleton('adminhtml/session');
				$session->addError(Mage::helper('adminhtml')->__(base64_decode('U2VyaWFsIEtleSBJcyBOT1QgVmFsaWQgZm9yIFdlYlNob3BBcHBzIEZlZGV4IEZyZWlnaHQ=')))  ;
     	}
	}	
}
