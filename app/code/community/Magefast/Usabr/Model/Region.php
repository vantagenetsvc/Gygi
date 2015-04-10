<?php

class Magefast_Usabr_Model_Region extends Mage_Directory_Model_Region {


    public function getName()
    {
	
		if(Mage::helper('usabr')->enableUsabr()) {

			$name = $this->getData('name');

			if (!is_null($name)) {

				/*
				* Get country ID, if US will change name to code
				*/

				$regionCountry = $this->getData('country_id');

				if($regionCountry && $regionCountry=='US') {

					$name = $this->getData('code');

				}

				unset($regionCountry);

			}

			/*
			* If $name is null, will get default region name
			*/

			if (is_null($name)) {
				$name = $this->getData('default_name');
			}
			return $name;
		
		} else {
		
			$name = $this->getData('name');
			if (is_null($name)) {
				$name = $this->getData('default_name');
			}
			return $name;
		
		}
		
    }


	/*
	* Here fix for dropdown list for frontend
	*/

    public function getFullName()
    {

		$name = $this->getData('name');

        if (is_null($name)) {
            $name = $this->getData('default_name');
        }
        return $name;
    }

}