<?php
/* YRC Freight Shipping
 *
 * @category   Webshopapps
 * @package    Webshopapps_Yrcfreight
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */


class Webshopapps_Yrcfreight_Model_Carrier_Yrcfreight
    extends Webshopapps_Wsafreightcommon_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'yrcfreight';

   	protected $_modName = 'Webshopapps_Yrcfreight';

    protected $_defaultGatewayUrl = 'https://my.yrc.com/dynamic/national/servlet?CONTROLLER=com.rdwy.ec.rexcommon.proxy.http.controller.ProxyApiController';


   public function setRequest(Mage_Shipping_Model_Rate_Request $request)
    {
    	$r = $this->setBaseRequest($request);

        $r->setUserId($this->getConfigData('userid'));
        $r->setPassword($this->getConfigData('password'));
        $r->setBusinessId($this->getConfigData('business_id'));
        $r->setBusRole($this->getConfigData('business_role'));
        $r->setPaymentTerms($this->getConfigData('payment_terms'));

        $r->setPickupDate($this->getDate());
        $r->setServiceClass('STD'); //TODO - See section 11
        $r->setTypeQuery('QUOTE'); //TODO can be MATRX or QUOTE or TABLE - put in config

        $this->_rawRequest = $r;

        return $this;
    }

	public function isCityRequired()
    {
        return true;
    }


	protected function _getQuotes()
    {
    	return $this->_sendCGIRequest();
    }

 	protected function _sendCGIRequest()
    {
        $r = $this->_rawRequest;
        $origZip = preg_replace('/\s+/', '',$r->getOrigPostal());
        $destZip = preg_replace('/\s+/', '',$r->getDestPostal());

        $params = array(
        	'redir'					=> '/tfq561',
            'LOGIN_USERID' 			=> $r->getUserId(),
            'LOGIN_PASSWORD'      	=> $r->getPassword(),
            'BusId'     			=> $r->getBusinessId(),
            'BusRole'     			=> $r->getBusRole(),
            'PaymentTerms'     		=> $r->getPaymentTerms(),
            'OrigCityName'       	=> $r->getOrigCity(),
        	'OrigStateCode' 		=> $r->getOrigRegionCode(),
            'OrigZipCode'  			=> $origZip,
            'OrigNationCode' 		=> $r->getOrigCountryIso3(),
        	'DestZipCode'  			=> $destZip,
            'DestCityName' 			=> $r->getDestCity(),
            'DestStateCode' 		=> $r->getDestRegionCode(),
            'DestNationCode' 		=> $r->getDestCountryIso3(),
            'ServiceClass' 			=> $r->getServiceClass(),
        	'PickupDate'      		=> $r->getPickupDate(),
            'TypeQuery'  			=> $r->getTypeQuery(),
        );

        $i=0;
    	foreach ($this->getLineItems($r->getIgnoreFreeItems()) as $class=>$weight) {
    		$i++;
	    	$params['LineItemWeight'.$i]=$weight;
	    	$params['LineItemNmfcClass'.$i]=$class;
    	}
    	$params['LineItemCount']=$i;

    	$i=1;
		if (Mage::helper('wsafreightcommon')->getUseLiveAccessories()) {
			$accArray=$r->getAccessories();
			foreach ($accArray as $acc) { // Add accessorials to the XML Request
				switch ($acc) {
                    case 'RES_ORIGIN':
                        $params['AccOption'.$i]='HOMP';
                        $i++;
                        break;
                    case 'LIFT_ORIGIN':
                        $params['AccOption'.$i]='LFTO';
                        $i++;
                        break;
					case 'RES':
						$params['AccOption'.$i]='HOMD';
						$i++;
						break;
					case 'LIFT':
						$params['AccOption'.$i]='LFTD';
						$i++;
						break;
					case 'HAZ':
						$params['AccOption'.$i]='HAZM';
						$i++;
						break;
				}
			}
		}

	    $params['AccOptionCount']=$i-1;

      	if($this->_debug)
        {
       		Mage::helper('wsacommon/log')->postNotice('yrcfreight','YRC Request',$params);
        }


        try {
            $url = $this->getConfigData('gateway_url');
            if (!$url) {
                $url = $this->_defaultGatewayUrl;
            }
            $client = new Zend_Http_Client();
            $client->setUri($url);
            $client->setConfig(array('maxredirects'=>0, 'timeout'=>30));
            $client->setParameterGet($params);
            $response = $client->request();
            $responseBody = $response->getBody();

            if($this->_debug)
            {
            	Mage::helper('wsacommon/log')->postNotice('yrcfreight','URL Request',$client->getLastRequest());
            	Mage::helper('wsacommon/log')->postNotice('yrcfreight','YRC Response',$responseBody);
            }
        } catch (Exception $e) {
        	Mage::helper('wsacommon/log')->postMajor('yrcfreight','YRC Error returned',$e->getMessage());
        	$responseBody = '';
        }

        return $this->_parseXmlResponse($params,$responseBody);
    }



    protected function _parseXmlResponse($params,$response)
    {

    	$priceArr=array();
    	$costArr=array();
    	$quoteId='';

    	if (strlen(trim($response))>0 && Mage::helper('wsacommon')->
                    		checkItems('Y2FycmllcnMveXJjZnJlaWdodC9zaGlwX29uY2U=',
								'eXVtbXlnbGFzcw==','Y2FycmllcnMveXJjZnJlaWdodC9zZXJpYWw=')) {
    		if (strpos(trim($response), '<?xml')===0) {
                if (preg_match('#<\?xml version="1.0"\?>#', $response)) {
                    $response = str_replace('<?xml version="1.0"?>', '<?xml version="1.0" encoding="ISO-8859-1"?>', $response);
                }

                $xml = simplexml_load_string($response);
                if (is_object($xml)) {
                        if (is_object($xml->Response->Errors) && is_object($xml->Response->Errors->Error) && (string)$xml->Response->Errors->Error!='') {
                            $errorTitle = (string)$xml->Response->Errors->Error;
                        } else {
                            $errorTitle = 'Unknown error';
                        }
                		$r = $this->_rawRequest;
                        $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));
                        $shipmentMethods = $this->getShipmentMethods($allowedMethods);
                        $newMethod = false;
						$fee = 0;
                        $residentialFee = 0;

                        if (is_object($xml->BodyMain) && is_object($xml->BodyMain->RateQuote) && is_object($xml->BodyMain->RateQuote->LineItem)) {

                        	foreach ($xml->BodyMain->RateQuote->LineItem as $postage) {
	                   			if (!empty($postage->Code) && in_array((string)$postage->Code, $shipmentMethods)) {
	                               	$fee = ((string)$postage->Charges)/100;
	                            }
	                            else if (!empty($postage->Code) && (string)$postage->Code == 'HOMD') {
	                              	$residentialFee = ((string)$postage->Charges)/100;
	                            }
                    		}
                    		if ($fee!=0) {
	                       		if(strcmp($this->_rawRequest->getShiptoType(), 'Residential') == 0 ||
	                       			$this->_rawRequest->getShiptoType()=='0') {
	                       			if($this->_debug)
						            {
						       			Mage::helper('wsacommon/log')->postNotice('yrcfreight','YRC Response','Residential Found');
						            }
                                    $costArr[$this->_code] = $this->getMethodPrice($fee, $this->_code);
                                    $priceArr[$this->_code] = $this->getMethodPrice($fee, $this->_code);
	                       		}
	                       		else {
                                     $costArr[$this->_code] = $this->getMethodPrice($fee - $residentialFee, $this->_code);
                                     $priceArr[$this->_code] = $this->getMethodPrice($fee - $residentialFee, $this->_code);
	                       		}
                    		}

                        $quoteId=(string)$xml->BodyMain->RateQuote->ReferenceId;;
                    }
                }
            } else {
                $errorTitle = 'Response is in the wrong format';
            }
        }

        return $this->getResultSet($priceArr,$params,$response,$quoteId);

    }



    private function getDate() {
    	$weekendPickup = Mage::getStoreConfig('carriers/yrcfreight/weekend_pickup');

    	$currentDate = date('Ymd',time());

    	$currentDate = date('Ymd',strtotime($currentDate . ' +1 day'));

    	if($weekendPickup) { return $currentDate; }
    	else {
    		if(date('w') == 6){ //it's a Saturday
    			$currentDate = date('Ymd',strtotime($currentDate . ' +2 day'));
    			if ($this->_debug) {
       				Mage::helper('wsacommon/log')->postNotice('yrcfreight','date modified',$currentDate);
				}
			} elseif(date('w') == 0){ //it's a Sunday
    			$currentDate = date('Ymd',strtotime($currentDate . ' +1 day'));
				if ($this->_debug) {
       				Mage::helper('wsacommon/log')->postNotice('yrcfreight','date modified',$currentDate);
				}			}
			return $currentDate;
    	}
    }

 	public function getCode($type, $code='')
    {
        $codes = array(
            'method'=>array(
               $this->_code    		=> Mage::helper('usa')->__('TTL'),
            ),
            'url_type'=>array(
                'Test' 				=> Mage::helper('usa')->__('Test'),
                'Live' 				=> Mage::helper('usa')->__('Live'),
           	),
           	'payment'=>array(
                'Prepaid'    		=> Mage::helper('usa')->__('Prepaid'),
                'Collect'   		=> Mage::helper('usa')->__('Collect'),
            ),
            'role'=>array(
             	'Shipper'    		=> Mage::helper('usa')->__('Shipper'),
             	'Consignee'    		=> Mage::helper('usa')->__('Consignee'),
            	'Third Party'   	=> Mage::helper('usa')->__('Third Party'),
            ),
        );

        if (!isset($codes[$type])) {
//            throw Mage::exception('Mage_Shipping', Mage::helper('usa')->__('Invalid UPS CGI code type: %s', $type));
            return false;
        } elseif (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
//            throw Mage::exception('Mage_Shipping', Mage::helper('usa')->__('Invalid UPS CGI code for type %s: %s', $type, $code));
            return false;
        } else {
            return $codes[$type][$code];
        }
    }


}
