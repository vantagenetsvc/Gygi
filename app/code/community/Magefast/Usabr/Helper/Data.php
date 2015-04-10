<?php

class Magefast_Usabr_Helper_Data extends Mage_Directory_Helper_Data
{

	public function enableUsabr() {
	
		$enable = Mage::getStoreConfig('general/general/activate_usabr');
			
		if($enable) {
			return true;
		}
		return false;

	}

    /**
     * Retrieve regions data json
     *
     * @return string
     */
    public function getRegionJson()
    {

		
		if($this->enableUsabr()) {
		
			//if enabled
    
			Varien_Profiler::start('TEST: '.__METHOD__);
			if (!$this->_regionJson) {
				$cacheKey = 'DIRECTORY_REGIONS_JSON_STORE'.Mage::app()->getStore()->getId();
				if (Mage::app()->useCache('config')) {
					$json = Mage::app()->loadCache($cacheKey);
				}
				if (empty($json)) {
					$countryIds = array();
					foreach ($this->getCountryCollection() as $country) {
						$countryIds[] = $country->getCountryId();
					}
					$collection = Mage::getModel('directory/region')->getResourceCollection()
						->addCountryFilter($countryIds)
						->load();
					$regions = array();
					foreach ($collection as $region) {
						if (!$region->getRegionId()) {
							continue;
						}
						$regions[$region->getCountryId()][$region->getRegionId()] = array(
							'code'=>$region->getCode(),
							'name'=>$region->getFullName()
						);
					}
					$json = Mage::helper('core')->jsonEncode($regions);

					if (Mage::app()->useCache('config')) {
						Mage::app()->saveCache($json, $cacheKey, array('config'));
					}
				}
				$this->_regionJson = $json;
			}

			Varien_Profiler::stop('TEST: '.__METHOD__);
			return $this->_regionJson;

			
		} else {

	
	        Varien_Profiler::start('TEST: '.__METHOD__);
			if (!$this->_regionJson) {
				$cacheKey = 'DIRECTORY_REGIONS_JSON_STORE'.Mage::app()->getStore()->getId();
				if (Mage::app()->useCache('config')) {
					$json = Mage::app()->loadCache($cacheKey);
				}
				if (empty($json)) {
					$countryIds = array();
					foreach ($this->getCountryCollection() as $country) {
						$countryIds[] = $country->getCountryId();
					}
					$collection = Mage::getModel('directory/region')->getResourceCollection()
						->addCountryFilter($countryIds)
						->load();
					$regions = array();
					foreach ($collection as $region) {
						if (!$region->getRegionId()) {
							continue;
						}
						$regions[$region->getCountryId()][$region->getRegionId()] = array(
							'code' => $region->getCode(),
							'name' => $this->__($region->getName())
						);
					}
					$json = Mage::helper('core')->jsonEncode($regions);

					if (Mage::app()->useCache('config')) {
						Mage::app()->saveCache($json, $cacheKey, array('config'));
					}
				}
				$this->_regionJson = $json;
			}

			Varien_Profiler::stop('TEST: '.__METHOD__);
			return $this->_regionJson;
		
		}
	
	
	}

}
