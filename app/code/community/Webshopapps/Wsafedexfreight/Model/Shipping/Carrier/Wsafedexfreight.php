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
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Fedex shipping implementation
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author      Magento Core Team <core@magentocommerce.com>
 */
/* Fedex Freight Shipping
 *
 * @category   Webshopapps
 * @package    Webshopapps_Wsafedexfreight
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Wsafedexfreight_Model_Shipping_Carrier_Wsafedexfreight
    extends Webshopapps_Wsafreightcommon_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
	private $_residential = false;

    /**
     * Code of the carrier
     *
     * @var string
     */
    const CODE = 'wsafedexfreight';

   protected $_modName = 'Webshopapps_Wsafedexfreight';


    /**
     * Code of the carrier
     *
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Path to wsdl file of rate service
     *
     * @var string
     */
    protected $_rateServiceWsdl;

    /**
     * Path to wsdl file of ship service
     *
     * @var string
     */
    protected $_shipServiceWsdl = null;

    /**
     * Path to wsdl file of track service
     *
     * @var string
     */
    protected $_trackServiceWsdl = null;

    /**
     * Container types that could be customized for FedEx carrier
     *
     * @var array
     */
    protected $_customizableContainerTypes = array('YOUR_PACKAGING');

    public function __construct()
    {
        parent::__construct();
        $wsdlBasePath = Mage::getModuleDir('etc', 'Webshopapps_Wsafedexfreight')  . DS . 'wsdl' . DS . 'Fedex' . DS;
        $this->_shipServiceWsdl = $wsdlBasePath . 'ShipService_v9.wsdl';
        $this->_trackServiceWsdl = $wsdlBasePath . 'TrackService_v5.wsdl';

        $wsdlWsaPath = Mage::getModuleDir('etc', 'Webshopapps_Wsafedexfreight')  . DS . 'wsdl' . DS . 'Fedex' . DS;
        $this->_rateServiceWsdl = $wsdlWsaPath . 'RateService_v10.wsdl';

    }

    /**
     * Create soap client with selected wsdl
     *
     * @param string $wsdl
     * @param bool|int $trace
     * @return SoapClient
     */
    protected function _createSoapClient($wsdl, $trace = false)
    {
        $client = new SoapClient($wsdl, array('trace' => $trace));
        $client->__setLocation($this->getConfigFlag('sandbox_mode')
            ? 'https://wsbeta.fedex.com:443/web-services/rate'
            : 'https://ws.fedex.com:443/web-services/rate'
        );

        return $client;
    }

    /**
     * Create rate soap client
     *
     * @return SoapClient
     */
    protected function _createRateSoapClient()
    {
        return $this->_createSoapClient($this->_rateServiceWsdl);
    }

    /**
     * Create ship soap client
     *
     * @return SoapClient
     */
    protected function _createShipSoapClient()
    {
        return $this->_createSoapClient($this->_shipServiceWsdl, 1);
    }

    /**
     * Create track soap client
     *
     * @return SoapClient
     */
    protected function _createTrackSoapClient()
    {
        return $this->_createSoapClient($this->_trackServiceWsdl, 1);
    }


    /**
     * Check if city option required
     *
     * @return boolean
     */
    public function isCityRequired()
    {
        return true;
    }

     /**
     * Check if city option required
     *
     * @return boolean
     */
    public function isStateProvinceRequired()
    {
        return true;
    }

    /**
     * Prepare and set request to this instance
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Usa_Model_Shipping_Carrier_Fedex
     */
    public function setRequest(Mage_Shipping_Model_Rate_Request $request)
    {

    	$r = $this->setBaseRequest($request);

    	$this->_request = $request;

        if ($request->getFedexAccount()) {
            $account = $request->getFedexAccount();
        } else {
            $account = $this->getConfigData('account');
        }
        $r->setAccount($account);

        if ($request->getFedexFreightAccount()) {
            $freightAccount = $request->getFedexFreightAccount();
        } else {
            $freightAccount = $this->getConfigData('freight_account');
        }

        if ($request->getFedexfreightAccountId()){
            $r->setFreightAccount($request->getFedexfreightAccountId());
        }else{
        $r->setFreightAccount($freightAccount);
        }
        if ($request->getFedexDropoff()) {
            $dropoff = $request->getFedexDropoff();
        } else {
            $dropoff = $this->getConfigData('dropoff');
        }
        $r->setDropoffType($dropoff);

        if ($request->getFedexPackaging()) {
            $packaging = $request->getFedexPackaging();
        } else {
            $packaging = $this->getConfigData('packaging');
        }
        $r->setPackaging($packaging);

        $weight = $this->getTotalNumOfBoxes($request->getPackageWeight());
        $r->setWeight($weight);
        if ($request->getFreeMethodWeight()!= $request->getPackageWeight()) {
            $r->setFreeMethodWeight($request->getFreeMethodWeight());
        }

        if($request->getFedexfreightMeterNumber()){
            $r->setMeterNumber($request->getFedexfreightMeterNumber());
        } else {
            $r->setMeterNumber($this->getConfigData('meter_number'));
        }

        if($request->getFedexfreightKey()){
            $r->setKey($request->getFedexfreightKey());
        } else {
            $r->setKey($this->getConfigData('key'));
        }

        if($request->getFedexfreightPassword()){
            $r->setPassword($request->getFedexfreightPassword());
        } else {
            $r->setPassword($this->getConfigData('password'));
        }

        $r->setIsReturn($request->getIsReturn());

        $r->setPayorName($this->getConfigData('payor_name'));

      	if ($request->getFedexfreightCountry()) {
            $payorCountry = $request->getFedexfreightCountry();
        } else {
            $payorCountry = $this->getConfigData('payor_country_id');
        }

        $r->setPayorCountry(Mage::getModel('directory/country')->load($payorCountry)->getIso2Code());

        if ($request->getFedexfreightState()) {
        	$payorRegionCode = $request->getFedexfreightState();
        } else {
        	$payorRegionCode = $this->getConfigData('payor_region_id');
        	if (is_numeric($payorRegionCode)) {
        		$payorRegionCode = Mage::getModel('directory/region')->load($payorRegionCode)->getCode();
        	}
        }
        $r->setPayorRegionCode($payorRegionCode);

        if ($request->getFedexfreightZipcode()) {
            $r->setPayorPostal($request->getFedexfreightZipcode());
        } else {
            $r->setPayorPostal($this->getConfigData('payor_postcode'));
        }

        if ($request->getFedexfreightCity()) {
            $r->setPayorCity($request->getFedexfreightCity());
        } else {
            $r->setPayorCity($this->getConfigData('payor_city'));
        }

        if ($request->getFedexfreightStreet()) {
            $r->setPayorStreetAddress($request->getFedexfreightStreet());
        } else {
            $r->setPayorStreetAddress($this->getConfigData('payor_street_address'));
        }

        if ($request->getFedexfreightFreightRole()){
            $r->setPayorRole($request->getFedexfreightFreightRole());
        }else{
            $r->setPayorRole($this->getConfigData('role'));
        }

        if ($request->getFedexfreightPaymentType()){
            $r->setPaymentType($request->getFedexfreightPaymentType());
        }else{
            $r->setPaymentType($this->getConfigData('payment_type'));
        }

        $this->_rawRequest = $r;

        return $this;
    }

    /**
     * Get result of request
     *
     * @return mixed
     */
    public function getResult()
    {
       return $this->_result;
    }

    /**
     * Get version of rates request
     *
     * @return array
     */
    public function getVersionInfo()
    {
        return array(
            'ServiceId'    => 'crs',
            'Major'        => '10',
            'Intermediate' => '0',
            'Minor'        => '0'
        );
    }

    /**
     * Do remote request for  and handle errors
     *
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _getQuotes()
    {
        $r = $this->_rawRequest;

        if (!Mage::getStoreConfig('shipping/wsafreightcommon/use_accessories')) {
            $r->setShiptoType(0);
        }

        //FedEx uses a boolean to define if residential. Residential is 0 by default
        if($r->getShiptoType()){
            $shiptoType = 0;
        } else {
            $shiptoType = 1;
        }

        $ratesRequest = array(
            'WebAuthenticationDetail' => array(
                'UserCredential' => array(
                    'Key'      => $r->getKey(),
                    'Password' => $r->getPassword()
                )
            ),
            'ClientDetail' => array(
                'AccountNumber' => $r->getAccount(),
                'MeterNumber'   => $r->getMeterNumber()
            ),
            'Version' => $this->getVersionInfo(),
            'RequestedShipment' => array(
                'DropoffType'   => $r->getDropoffType(),
                'ShipTimestamp' => date('c'),
                'PackagingType' => $r->getPackaging(),
                'TotalInsuredValue' => array(
                    'Ammount'  => $r->getValue(),
                    'Currency' => $this->getCurrencyCode()
                ),
                'Shipper' => array(
                    'Address' => array(
                        'PostalCode'  			=> $r->getOrigPostal(),
                		'City'					=> $r->getOrigCity(),
	            		'StateOrProvinceCode' 	=> $r->getOrigRegionCode(),
                        'CountryCode' 			=> $r->getOrigCountry()
                    )
                ),
                'Recipient' => array(
                    'Address' => array(
                        'PostalCode'            => $r->getDestPostal(),
                        'CountryCode'           => $r->getDestCountry(),
                		'StateOrProvinceCode' 	=> $r->getDestRegionCode(),
                		'City'					=> 'NA',
                        'Residential'           => $shiptoType
                    )
                ),
                'ShippingChargesPayment' => array(
                    'PaymentType' => 'SENDER',
                    'Payor' => array(
                        'AccountNumber' => $r->getAccount(),
                        'CountryCode'   => $r->getOrigCountry()
                    )
                ),
                'RateRequestTypes' => $this->getConfigData('request_type'),
                'PackageCount'     => '1',
              //  'PackageDetail'    => 'INDIVIDUAL_PACKAGES',
                'FreightShipmentDetail' => array(
                	'FedExFreightAccountNumber' => $r->getFreightAccount(),
					'FedExFreightBillingContactAndAddress' => array (
	            		'Address' => array (
	            			'StreetLines' => $r->getPayorStreetAddress(),
	            			'City'	=> $r->getPayorCity(),
	            			'StateOrProvinceCode' => $r->getPayorRegionCode(),
	            			'PostalCode' => $r->getPayorPostal(),
	                        'CountryCode' => $r->getPayorCountry(),
	                        'Residential' => $r->getShiptoType(),
	            		),
	            	),
	            	'Role'			=> $r->getPayorRole(),
	            	'PaymentType'	=> $r->getPaymentType(),
                )
            )
        );

        if (Mage::helper('wsafreightcommon')->getUseLiveAccessories()) {
            $accArray=$r->getAccessories();
            if ($r->getOriginLiftgateReqd()) {
                array_push($accArray, 'ORIGLIFT');
            }
            foreach ($accArray as $acc) { // Add accessorials to the XML Request
                switch ($acc) {
                    case 'LIFT':
                        $ratesRequest['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'LIFTGATE_DELIVERY';
                        break;
                    case 'ORIGLIFT':
                        $ratesRequest['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'LIFTGATE_PICKUP';
                        break;
                }
            }
        }

        $this->addLineItems($ratesRequest, $r->getIgnoreFreeItems());

    	if (!Mage::helper('wsacommon')->checkItems('Y2FycmllcnMvd3NhZmVkZXhmcmVpZ2h0L3NoaXBfb25jZQ==',
     		'd2Fyd29ybGQ=','Y2FycmllcnMvd3NhZmVkZXhmcmVpZ2h0L3NlcmlhbA==')) {
				return null;
     	}

        $requestString = serialize($ratesRequest);
        $response = $this->_getCachedQuotes($requestString);
        $debugData = array('request' => $ratesRequest);
        if ($response === null) {
            try {
                $client = $this->_createRateSoapClient();
                $response = $client->getRates($ratesRequest);
                $this->_setCachedQuotes($requestString, serialize($response));
                $debugData['result'] = $response;
            } catch (Exception $e) {
                $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
                Mage::logException($e);
            }
        } else {
            $response = unserialize($response);
            $debugData['result'] = $response;
        }
    	if($this->_debug)
        {
       		Mage::helper('wsalogger/log')->postInfo('wsafedexfreight','Fedex Freight Request/Response',$debugData);
        }
            return $this->_prepareRateResponse($response, $ratesRequest);
    }

    /**
     * Prepare shipping rate result based on response
     *
     * @param mixed $response
     * @param $ratesRequest
     * @return Mage_Shipping_Model_Rate_Result
     */
    protected function _prepareRateResponse($response, $ratesRequest)
    {
        $costArr = array();
        $priceArr = array();
        $errorTitle = 'Unable to retrieve tracking';
        $requestType = $this->getConfigData('request_type');

        if (is_object($response)) {
            if ($response->HighestSeverity == 'FAILURE' || $response->HighestSeverity == 'ERROR') {
                $errorTitle = (string)$response->Notifications->Message;
            } elseif (isset($response->RateReplyDetails)) {
                $allowedMethods = explode(",", $this->getConfigData('allowed_methods'));

                if (is_array($response->RateReplyDetails)) {
                    foreach ($response->RateReplyDetails as $rate) {
                        $this->_extractRatesFromXml($priceArr,$costArr,$rate,$requestType,$allowedMethods);
                    }
                    asort($priceArr);
                } else {
                    $rate = $response->RateReplyDetails;

                    $this->_extractRatesFromXml($priceArr,$costArr,$rate,$requestType,$allowedMethods);
                }
            }
        }

        return $this->getResultSet($priceArr,$ratesRequest,$response,'');
    }


    protected function _extractRatesFromXml(&$priceArr,&$costArr,$rate,$requestType,$allowedMethods) {
        $serviceName = (string)$rate->ServiceType;
        $foundShipmentDetails = null;

        if (in_array($serviceName, $allowedMethods)) {
            if (is_array($rate->RatedShipmentDetails)) {
                foreach ($rate->RatedShipmentDetails as $shipDetail) {
                    if (strpos((string)$shipDetail->ShipmentRateDetail->RateType,$requestType) !== false) {
                        $foundShipmentDetails = $shipDetail;
                        break;
                    }
                }
            } else {
                if (strpos((string)$rate->RatedShipmentDetails->ShipmentRateDetail->RateType,$requestType) !== false) {
                    $foundShipmentDetails = $rate->RatedShipmentDetails;
                }
            }
        }

        if (is_null($foundShipmentDetails)) {
            return;
        }

        $amount = (string)$foundShipmentDetails->ShipmentRateDetail->TotalNetCharge->Amount;

        $costArr[$serviceName]  = $amount;
        $priceArr[$serviceName] = $this->getMethodPrice($amount, $serviceName);
    }

    /**
     * Get configuration data of carrier
     *
     * @param string $type
     * @param string $code
     * @return array|bool
     */
    public function getCode($type, $code='')
    {
        $codes = array(
            'method' => array(
                'FEDEX_1_DAY_FREIGHT'                 => Mage::helper('usa')->__('1 Day Freight'),
                'FEDEX_2_DAY_FREIGHT'                 => Mage::helper('usa')->__('2 Day Freight'),
                'FEDEX_3_DAY_FREIGHT'                 => Mage::helper('usa')->__('3 Day Freight'),
                'FEDEX_FREIGHT_ECONOMY'               => Mage::helper('usa')->__('Economy Freight'),
                'FEDEX_FREIGHT_PRIORITY'              => Mage::helper('usa')->__('Priority Freight'),
                'INTERNATIONAL_ECONOMY_FREIGHT'       => Mage::helper('usa')->__('International Economy Freight'),
                'INTERNATIONAL_PRIORITY_FREIGHT'      => Mage::helper('usa')->__('International Priority Freight'),
                'FEDEX_FIRST_FREIGHT'                 => Mage::helper('usa')->__('First Freight'),
                'FEDEX_NATIONAL_FREIGHT'              => Mage::helper('usa')->__('National Freight'),
            ),
            'dropoff' => array(
                'REGULAR_PICKUP'          => Mage::helper('usa')->__('Regular Pickup'),
                'REQUEST_COURIER'         => Mage::helper('usa')->__('Request Courier'),
                'DROP_BOX'                => Mage::helper('usa')->__('Drop Box'),
                'BUSINESS_SERVICE_CENTER' => Mage::helper('usa')->__('Business Service Center'),
                'STATION'                 => Mage::helper('usa')->__('Station')
            ),
            'packaging' => array(
                'FEDEX_PAK'      => Mage::helper('usa')->__('FedEx Pak'),
                'FEDEX_BOX'      => Mage::helper('usa')->__('FedEx Box'),
                'FEDEX_TUBE'     => Mage::helper('usa')->__('FedEx Tube'),
                'FEDEX_10KG_BOX' => Mage::helper('usa')->__('FedEx 10kg Box'),
                'FEDEX_25KG_BOX' => Mage::helper('usa')->__('FedEx 25kg Box'),
                'YOUR_PACKAGING' => Mage::helper('usa')->__('Your Packaging')
            ),
            'unitofmeasure' => array (
            	'KG'			=> 'KG',
            	'LB'			=> 'LB'
            ),
            'item_packaging' => array(
                'BAG' 				=> Mage::helper('usa')->__('BAG'),
                'BARREL'     		=> Mage::helper('usa')->__('BARREL'),
             	'BASKET'      		=> Mage::helper('usa')->__('BASKET'),
				 'BOX'      		=> Mage::helper('usa')->__('BOX' ),
				 'BUCKET'      		=> Mage::helper('usa')->__('BUCKET' ),
				 'BUNDLE'      		=> Mage::helper('usa')->__('BUNDLE'),
				 'CARTON'      		=> Mage::helper('usa')->__('CARTON' ),
				 'CASE'      		=> Mage::helper('usa')->__('CASE' ),
				 'CONTAINER'      	=> Mage::helper('usa')->__('CONTAINER'),
				 'CRATE'      		=> Mage::helper('usa')->__('CRATE'),
				 'CYLINDER'      	=> Mage::helper('usa')->__('CYLINDER'),
				 'DRUM'      		=> Mage::helper('usa')->__('DRUM' ),
				 'ENVELOPE'      	=> Mage::helper('usa')->__('ENVELOPE' ),
				 'HAMPER'      		=> Mage::helper('usa')->__('HAMPER'),
				 'OTHER'      		=> Mage::helper('usa')->__('OTHER'  ),
				 'PAIL'      		=> Mage::helper('usa')->__('PAIL' ),
				 'PALLET'      		=> Mage::helper('usa')->__('PALLET' ),
				 'PIECE'      		=> Mage::helper('usa')->__('PIECE'),
				 'REEL'      		=> Mage::helper('usa')->__( 'REEL'),
				 'ROLL'      		=> Mage::helper('usa')->__('ROLL'),
				 'SKID'      		=> Mage::helper('usa')->__('SKID' ),
				 'TANK'      		=> Mage::helper('usa')->__('TANK'  ),
				 'TUBE'      		=> Mage::helper('usa')->__('TUBE' ),
            ),
            'role'			=> array (
            	'CONSIGNEE'		=> 'Consignee',
            	'SHIPPER'		=> 'Shipper',
                'THIRD_PARTY'	=> 'Third Party',
            ),
            'request_type'	=> array (
             	'ACCOUNT'		=> 'Account',
            	'LIST'			=> 'List',
            ),
            'freight_class' => array(
                'CLASS_050' 	=> 'CLASS_050',
                'CLASS_055' 	=> 'CLASS_055',
                'CLASS_060' 	=> 'CLASS_060',
                'CLASS_065' 	=> 'CLASS_065',
                'CLASS_070' 	=> 'CLASS_070',
                'CLASS_077_5' 	=> 'CLASS_077_5',
                'CLASS_085' 	=> 'CLASS_085',
                'CLASS_092_5' 	=> 'CLASS_092_5',
                'CLASS_100' 	=> 'CLASS_100',
                'CLASS_110' 	=> 'CLASS_110',
                'CLASS_125' 	=> 'CLASS_125',
                'CLASS_150' 	=> 'CLASS_150',
              	'CLASS_175' 	=> 'CLASS_175',
                'CLASS_200' 	=> 'CLASS_200',
                'CLASS_250' 	=> 'CLASS_250',
                'CLASS_300' 	=> 'CLASS_300',
                'CLASS_400' 	=> 'CLASS_400',
                'CLASS_500' 	=> 'CLASS_500',
            ),
            'payment' 			=> array (
            	'COLLECT'		=> 'Collect',
            	'PREPAID'		=> 'Prepaid',
            ),
            'containers_filter' => array(
                array(
                    'containers' => array('FEDEX_PAK'),
                    'filters'    => array(
                        'within_us' => array(
                            'method' => array(
                                'FEDEX_EXPRESS_SAVER',
                                'FEDEX_2_DAY',
                                'STANDARD_OVERNIGHT',
                                'PRIORITY_OVERNIGHT',
                                'FIRST_OVERNIGHT',
                            )
                        ),
                        'from_us' => array(
                            'method' => array(
                                'INTERNATIONAL_FIRST',
                                'INTERNATIONAL_ECONOMY',
                                'INTERNATIONAL_PRIORITY',
                            )
                        )
                    )
                ),
                array(
                    'containers' => array('FEDEX_BOX', 'FEDEX_TUBE'),
                    'filters'    => array(
                        'within_us' => array(
                            'method' => array(
                                'FEDEX_2_DAY',
                                'STANDARD_OVERNIGHT',
                                'PRIORITY_OVERNIGHT',
                                'FIRST_OVERNIGHT',
                            )
                        ),
                        'from_us' => array(
                            'method' => array(
                                'INTERNATIONAL_FIRST',
                                'INTERNATIONAL_ECONOMY',
                                'INTERNATIONAL_PRIORITY',
                            )
                        )
                    )
                ),
                array(
                    'containers' => array('FEDEX_10KG_BOX', 'FEDEX_25KG_BOX'),
                    'filters'    => array(
                        'within_us' => array(),
                        'from_us' => array('method' => array('INTERNATIONAL_PRIORITY'))
                    )
                ),
                array(
                    'containers' => array('YOUR_PACKAGING'),
                    'filters'    => array(
                        'within_us' => array(
                            'method' =>array(
                                'FEDEX_GROUND',
                                'GROUND_HOME_DELIVERY',
                                'SMART_POST',
                                'FEDEX_EXPRESS_SAVER',
                                'FEDEX_2_DAY',
                                'STANDARD_OVERNIGHT',
                                'PRIORITY_OVERNIGHT',
                                'FIRST_OVERNIGHT',
                                'FEDEX_FREIGHT',
                                'FEDEX_1_DAY_FREIGHT',
                                'FEDEX_2_DAY_FREIGHT',
                                'FEDEX_3_DAY_FREIGHT',
                                'FEDEX_NATIONAL_FREIGHT',
                				'FEDEX_FREIGHT_ECONOMY',
                				'FEDEX_FREIGHT_PRIORITY',

                            )
                        ),
                        'from_us' => array(
                            'method' =>array(
                                'INTERNATIONAL_FIRST',
                                'INTERNATIONAL_ECONOMY',
                                'INTERNATIONAL_PRIORITY',
                                'INTERNATIONAL_GROUND',
                                'FEDEX_FREIGHT',
                                'FEDEX_1_DAY_FREIGHT',
                                'FEDEX_2_DAY_FREIGHT',
                                'FEDEX_3_DAY_FREIGHT',
                                'FEDEX_NATIONAL_FREIGHT',
                                'INTERNATIONAL_ECONOMY_FREIGHT',
                                'INTERNATIONAL_PRIORITY_FREIGHT',
                            )
                        )
                    )
                )
            ),

            'delivery_confirmation_types' => array(
                'NO_SIGNATURE_REQUIRED' => Mage::helper('usa')->__('Not Required'),
                'ADULT'                 => Mage::helper('usa')->__('Adult'),
                'DIRECT'                => Mage::helper('usa')->__('Direct'),
                'INDIRECT'              => Mage::helper('usa')->__('Indirect'),
            ),
        );

        if (!isset($codes[$type])) {
            return false;
        } elseif (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }

    /**
     *  Return FeDex currency ISO code by Magento Base Currency Code
     *
     *  @return string 3-digit currency code
     */
    public function getCurrencyCode ()
    {
        $codes = array(
            'DOP' => 'RDD', // Dominican Peso
            'XCD' => 'ECD', // Caribbean Dollars
            'ARS' => 'ARN', // Argentina Peso
            'SGD' => 'SID', // Singapore Dollars
            'KRW' => 'WON', // South Korea Won
            'JMD' => 'JAD', // Jamaican Dollars
            'CHF' => 'SFR', // Swiss Francs
            'JPY' => 'JYE', // Japanese Yen
            'KWD' => 'KUD', // Kuwaiti Dinars
            'GBP' => 'UKL', // British Pounds
            'AED' => 'DHS', // UAE Dirhams
            'MXN' => 'NMP', // Mexican Pesos
            'UYU' => 'UYP', // Uruguay New Pesos
            'CLP' => 'CHP', // Chilean Pesos
            'TWD' => 'NTD', // New Taiwan Dollars
        );
        $currencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
        return isset($codes[$currencyCode]) ? $codes[$currencyCode] : $currencyCode;
    }

    /**
     * Get tracking
     *
     * @param mixed $trackings
     * @return mixed
     */
    public function getTracking($trackings)
    {
        $this->setTrackingReqeust();

        if (!is_array($trackings)) {
            $trackings=array($trackings);
        }

        foreach($trackings as $tracking){
            $this->_getXMLTracking($tracking);
        }

        return $this->_result;
    }

    /**
     * Set tracking request
     *
     * @return void
     */
    protected function setTrackingReqeust()
    {
        $r = new Varien_Object();

        $account = $this->getConfigData('account');
        $r->setAccount($account);

        $this->_rawTrackingRequest = $r;
    }

    /**
     * Send request for tracking
     *
     * @param array $tracking
     * @return void
     */
    protected function _getXMLTracking($tracking)
    {
        $trackRequest = array(
            'WebAuthenticationDetail' => array(
                'UserCredential' => array(
                    'Key'      => $this->getConfigData('key'),
                    'Password' => $this->getConfigData('password')
                )
            ),
            'ClientDetail' => array(
                'AccountNumber' => $this->getConfigData('account'),
                'MeterNumber'   => $this->getConfigData('meter_number')
            ),
            'Version' => array(
                'ServiceId'    => 'trck',
                'Major'        => '5',
                'Intermediate' => '0',
                'Minor'        => '0'
            ),
            'PackageIdentifier' => array(
                'Type'  => 'TRACKING_NUMBER_OR_DOORTAG',
                'Value' => $tracking,
            ),
            /*
             * 0 = summary data, one signle scan structure with the most recent scan
             * 1 = multiple sacn activity for each package
             */
            'IncludeDetailedScans' => 1,
        );
        $requestString = serialize($trackRequest);
        $response = $this->_getCachedQuotes($requestString);
        $debugData = array('request' => $trackRequest);
        if ($response === null) {
            try {
                $client = $this->_createTrackSoapClient();
                $response = $client->track($trackRequest);
                $this->_setCachedQuotes($requestString, serialize($response));
                $debugData['result'] = $response;
            } catch (Exception $e) {
                $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
                Mage::logException($e);
            }
        } else {
            $response = unserialize($response);
            $debugData['result'] = $response;
        }
        $this->_debug($debugData);

        $this->_parseTrackingResponse($tracking, $response);
    }

    /**
     * Parse tracking response
     *
     * @param array $trackingValue
     * @param stdClass $response
     */
    protected function _parseTrackingResponse($trackingValue, $response)
    {
        $errorTitle = 'Unable to retrieve tracking';

        if (is_object($response)) {
            if ($response->HighestSeverity == 'FAILURE' || $response->HighestSeverity == 'ERROR') {
                $errorTitle = (string)$response->Notifications->Message;
            } elseif (isset($response->TrackDetails)) {
                $trackInfo = $response->TrackDetails;
                $resultArray['status'] = (string)$trackInfo->StatusDescription;
                $resultArray['service'] = (string)$trackInfo->ServiceInfo;
                $timestamp = isset($trackInfo->EstimatedDeliveryTimestamp) ?
                    $trackInfo->EstimatedDeliveryTimestamp : $trackInfo->ActualDeliveryTimestamp;
                $timestamp = strtotime((string)$timestamp);
                if ($timestamp) {
                    $resultArray['deliverydate'] = date('Y-m-d', $timestamp);
                    $resultArray['deliverytime'] = date('H:i:s', $timestamp);
                }

                $deliveryLocation = isset($trackInfo->EstimatedDeliveryAddress) ?
                    $trackInfo->EstimatedDeliveryAddress : $trackInfo->ActualDeliveryAddress;
                $deliveryLocationArray = array();
                if (isset($deliveryLocation->City)) {
                    $deliveryLocationArray[] = (string)$deliveryLocation->City;
                }
                if (isset($deliveryLocation->StateOrProvinceCode)) {
                    $deliveryLocationArray[] = (string)$deliveryLocation->StateOrProvinceCode;
                }
                if (isset($deliveryLocation->CountryCode)) {
                    $deliveryLocationArray[] = (string)$deliveryLocation->CountryCode;
                }
                if ($deliveryLocationArray) {
                    $resultArray['deliverylocation'] = implode(', ', $deliveryLocationArray);
                }

                $resultArray['signedby'] = (string)$trackInfo->DeliverySignatureName;
                $resultArray['shippeddate'] = date('Y-m-d', (int)$trackInfo->ShipTimestamp);
                if (isset($trackInfo->PackageWeight) && isset($trackInfo->Units)) {
                    $weight = (string)$trackInfo->PackageWeight;
                    $unit = (string)$trackInfo->Units;
                    $resultArray['weight'] = "{$weight} {$unit}";
                }

                $packageProgress = array();
                if (isset($trackInfo->Events)) {
                    $events = $trackInfo->Events;
                    if (isset($events->Address)) {
                        $events = array($events);
                    }
                    foreach ($events as $event) {
                        $tempArray = array();
                        $tempArray['activity'] = (string)$event->EventDescription;
                        $timestamp = strtotime((string)$event->Timestamp);
                        if ($timestamp) {
                            $tempArray['deliverydate'] = date('Y-m-d', $timestamp);
                            $tempArray['deliverytime'] = date('H:i:s', $timestamp);
                        }
                        if (isset($event->Address)) {
                            $addressArray = array();
                            $address = $event->Address;
                            if (isset($address->City)) {
                                $addressArray[] = (string)$address->City;
                            }
                            if (isset($address->StateOrProvinceCode)) {
                                $addressArray[] = (string)$address->StateOrProvinceCode;
                            }
                            if (isset($address->CountryCode)) {
                                $addressArray[] = (string)$address->CountryCode;
                            }
                            if ($addressArray) {
                                $tempArray['deliverylocation'] = implode(', ', $addressArray);
                            }
                        }
                        $packageProgress[] = $tempArray;
                    }
                }

                $resultArray['progressdetail'] = $packageProgress;
            }
        }

        if(!$this->_result){
            $this->_result = Mage::getModel('shipping/tracking_result');
        }

        if(isset($resultArray)) {
            $tracking = Mage::getModel('shipping/tracking_result_status');
            $tracking->setCarrier('fedex');
            $tracking->setCarrierTitle($this->getConfigData('title'));
            $tracking->setTracking($trackingValue);
            $tracking->addData($resultArray);
            $this->_result->append($tracking);
        } else {
           $error = Mage::getModel('shipping/tracking_result_error');
           $error->setCarrier('fedex');
           $error->setCarrierTitle($this->getConfigData('title'));
           $error->setTracking($trackingValue);
           $error->setErrorMessage($errorTitle ? $errorTitle : Mage::helper('usa')->__('Unable to retrieve tracking'));
           $this->_result->append($error);
        }
    }


    /**
     * Get tracking response
     *
     * @return string
     */
    public function getResponse()
    {
        $statuses = '';
        if ($this->_result instanceof Mage_Shipping_Model_Tracking_Result){
            if ($trackings = $this->_result->getAllTrackings()) {
                foreach ($trackings as $tracking){
                    if($data = $tracking->getAllData()){
                        if (!empty($data['status'])) {
                            $statuses .= Mage::helper('usa')->__($data['status'])."\n<br/>";
                        } else {
                            $statuses .= Mage::helper('usa')->__('Empty response')."\n<br/>";
                        }
                    }
                }
            }
        }
        if (empty($statuses)) {
            $statuses = Mage::helper('usa')->__('Empty response');
        }
        return $statuses;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        $allowed = explode(',', $this->getConfigData('allowed_methods'));
        $arr = array();
        foreach ($allowed as $k) {
            $arr[$k] = $this->getCode('method', $k);
        }
        return $arr;
    }

    /**
     * Return array of authenticated information
     *
     * @return array
     */
    protected function _getAuthDetails()
    {
        return array(
            'WebAuthenticationDetail' => array(
                'UserCredential' => array(
                    'Key'      => $this->getConfigData('key'),
                    'Password' => $this->getConfigData('password')
                )
            ),
            'ClientDetail' => array(
                'AccountNumber' => $this->getConfigData('account'),
                'MeterNumber'   => $this->getConfigData('meter_number')
            ),
            'TransactionDetail' => array(
                'CustomerTransactionId' => '*** Express Domestic Shipping Request v9 using PHP ***'
            ),
            'Version' => array(
                'ServiceId'     => 'ship',
                'Major'         => '9',
                'Intermediate'  => '0',
                'Minor'         => '0'
            )
        );
    }

    /**
     * Form array with appropriate structure for shipment request
     *
     * @param Varien_Object $request
     * @return array
     */
    protected function _formShipmentRequest(Varien_Object $request)
    {
        if ($request->getReferenceData()) {
            $referenceData = $request->getReferenceData() . $request->getPackageId();
        } else {
            $referenceData = 'Order #'
                             . $request->getOrderShipment()->getOrder()->getIncrementId()
                             . ' P'
                             . $request->getPackageId();
        }
        $packageParams = $request->getPackageParams();
        $customsValue = $packageParams->getCustomsValue();
        $height = $packageParams->getHeight();
        $width = $packageParams->getWidth();
        $length = $packageParams->getLength();
        $weightUnits = $packageParams->getWeightUnits() == Zend_Measure_Weight::POUND ? 'LB' : 'KG';
        $dimensionsUnits = $packageParams->getDimensionUnits() == Zend_Measure_Length::INCH ? 'IN' : 'CM';
        $unitPrice = 0;
        $itemsQty = 0;
        $itemsDesc = array();
        $countriesOfManufacture = array();
        $productIds = array();
        $packageItems = $request->getPackageItems();
        foreach ($packageItems as $itemShipment) {
                $item = new Varien_Object();
                $item->setData($itemShipment);

                $unitPrice  += $item->getPrice();
                $itemsQty   += $item->getQty();

                $itemsDesc[]    = $item->getName();
                $productIds[]   = $item->getProductId();
        }

        // get countries of manufacture
        $productCollection = Mage::getResourceModel('catalog/product_collection')
            ->addStoreFilter($request->getStoreId())
            ->addFieldToFilter('entity_id', array('in' => $productIds))
            ->addAttributeToSelect('country_of_manufacture');
        foreach ($productCollection as $product) {
            $countriesOfManufacture[] = $product->getCountryOfManufacture();
        }

        $paymentType = $request->getIsReturn() ? 'RECIPIENT' : 'SENDER';
        $requestClient = array(
            'RequestedShipment' => array(
                'ShipTimestamp' => time(),
                'DropoffType'   => $this->getConfigData('dropoff'),
                'PackagingType' => $request->getPackagingType(),
                'ServiceType' => $request->getShippingMethod(),
                'Shipper' => array(
                    'Contact' => array(
                        'PersonName' => $request->getShipperContactPersonName(),
                        'CompanyName' => $request->getShipperContactCompanyName(),
                        'PhoneNumber' => $request->getShipperContactPhoneNumber()
                    ),
                    'Address' => array(
                        'StreetLines' => array($request->getShipperAddressStreet()),
                        'City' => $request->getShipperAddressCity(),
                        'StateOrProvinceCode' => $request->getShipperAddressStateOrProvinceCode(),
                        'PostalCode' => $request->getShipperAddressPostalCode(),
                        'CountryCode' => $request->getShipperAddressCountryCode()
                    )
                ),
                'Recipient' => array(
                    'Contact' => array(
                        'PersonName' => $request->getRecipientContactPersonName(),
                        'CompanyName' => $request->getRecipientContactCompanyName(),
                        'PhoneNumber' => $request->getRecipientContactPhoneNumber()
                    ),
                    'Address' => array(
                        'StreetLines' => array($request->getRecipientAddressStreet()),
                        'City' => $request->getRecipientAddressCity(),
                        'StateOrProvinceCode' => $request->getRecipientAddressStateOrProvinceCode(),
                        'PostalCode' => $request->getRecipientAddressPostalCode(),
                        'CountryCode' => $request->getRecipientAddressCountryCode(),
                        'Residential' => (bool)$this->getConfigData('residence_delivery')
                    ),
                ),
                'ShippingChargesPayment' => array(
                    'PaymentType' => $paymentType,
                    'Payor' => array(
                        'AccountNumber' => $this->getConfigData('account'),
                        'CountryCode'   => Mage::getStoreConfig(
                            self::XML_PATH_STORE_COUNTRY_ID,
                            $request->getStoreId()
                        )
                    )
                ),
                'LabelSpecification' =>array(
                    'LabelFormatType' => 'COMMON2D',
                    'ImageType' => 'PNG',
                    'LabelStockType' => 'PAPER_8.5X11_TOP_HALF_LABEL',
                ),
                'RateRequestTypes'  => array('ACCOUNT'),
                'PackageCount'      => 1,
                'RequestedPackageLineItems' => array(
                    'SequenceNumber' => '1',
                    'Weight' => array(
                        'Units' => $weightUnits,
                        'Value' =>  $request->getPackageWeight()
                    ),
                    'CustomerReferences' => array(
                        'CustomerReferenceType' => 'CUSTOMER_REFERENCE',
                        'Value' => $referenceData
                    ),
                    'SpecialServicesRequested' => array(
                        'SpecialServiceTypes' => 'SIGNATURE_OPTION',
                        'SignatureOptionDetail' => array('OptionType' => $packageParams->getDeliveryConfirmation())
                    ),
                )
            )
        );

        // for international shipping
        if ($request->getShipperAddressCountryCode() != $request->getRecipientAddressCountryCode()) {
            $requestClient['RequestedShipment']['CustomsClearanceDetail'] =
                array(
                    'CustomsValue' =>
                    array(
                        'Currency' => $request->getBaseCurrencyCode(),
                        'Amount' => $customsValue,
                    ),
                    'DutiesPayment' => array(
                        'PaymentType' => $paymentType,
                        'Payor' => array(
                            'AccountNumber' => $this->getConfigData('account'),
                            'CountryCode'   => Mage::getStoreConfig(
                                self::XML_PATH_STORE_COUNTRY_ID,
                                $request->getStoreId()
                            )
                        )
                    ),
                    'Commodities' => array(
                        'Weight' => array(
                            'Units' => $weightUnits,
                            'Value' =>  $request->getPackageWeight()
                        ),
                        'NumberOfPieces' => 1,
                        'CountryOfManufacture' => implode(',', array_unique($countriesOfManufacture)),
                        'Description' => implode(', ', $itemsDesc),
                        'Quantity' => ceil($itemsQty),
                        'QuantityUnits' => 'pcs',
                        'UnitPrice' => array(
                            'Currency' => $request->getBaseCurrencyCode(),
                            'Amount' =>  $unitPrice
                        ),
                        'CustomsValue' => array(
                            'Currency' => $request->getBaseCurrencyCode(),
                            'Amount' =>  $customsValue
                        ),
                    )
                );
        }

        if ($request->getMasterTrackingId()) {
            $requestClient['RequestedShipment']['MasterTrackingId'] = $request->getMasterTrackingId();
        }

        // set dimensions
        if ($length || $width || $height) {
            $requestClient['RequestedShipment']['RequestedPackageLineItems']['Dimensions'] = array();
            $dimenssions = &$requestClient['RequestedShipment']['RequestedPackageLineItems']['Dimensions'];
            $dimenssions['Length'] = $length;
            $dimenssions['Width']  = $width;
            $dimenssions['Height'] = $height;
            $dimenssions['Units'] = $dimensionsUnits;
        }

        return $this->_getAuthDetails() + $requestClient;
    }

    /**
     * Do shipment request to carrier web service, obtain Print Shipping Labels and process errors in response
     *
     * @param Varien_Object $request
     * @return Varien_Object
     */
    protected function _doShipmentRequest(Varien_Object $request)
    {
        $this->_prepareShipmentRequest($request);
        $result = new Varien_Object();
        $client = $this->_createShipSoapClient();
        $requestClient = $this->_formShipmentRequest($request);
        $response = $client->processShipment($requestClient);

        if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
            $shippingLabelContent = $response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image;
            $trackingNumber = $response->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds->TrackingNumber;
            $result->setShippingLabelContent($shippingLabelContent);
            $result->setTrackingNumber($trackingNumber);
            $debugData = array('request' => $client->__getLastRequest(), 'result' => $client->__getLastResponse());
            $this->_debug($debugData);
        } else {
            $debugData = array(
                'request' => $client->__getLastRequest(),
                'result' => array(
                    'error' => '',
                    'code' => '',
                    'xml' => $client->__getLastResponse()
                )
            );
            if (is_array($response->Notifications)) {
                foreach ($response->Notifications as $notification) {
                    $debugData['result']['code'] .= $notification->Code . '; ';
                    $debugData['result']['error'] .= $notification->Message . '; ';
                }
            } else {
                $debugData['result']['code'] = $response->Notifications->Code . ' ';
                $debugData['result']['error'] = $response->Notifications->Message . ' ';
            }
            $this->_debug($debugData);
            $result->setErrors($debugData['result']['error']);
        }
        $result->setGatewayResponse($client->__getLastResponse());

        return $result;
    }

    /**
     * For multi package shipments. Delete requested shipments if the current shipment
     * request is failed
     *
     * @param array $data
     * @return bool
     */
    public function rollBack($data)
    {
        $requestData = $this->_getAuthDetails();
        $requestData['DeletionControl'] = 'DELETE_ONE_PACKAGE';
        foreach ($data as &$item) {
            $requestData['TrackingId'] = $item['tracking_number'];
            $client = $this->_createShipSoapClient();
            $client->deleteShipment($requestData);
        }
        return true;
    }

    /**
     * Return container types of carrier
     *
     * @param Varien_Object|null $params
     * @return array|bool
     */
    public function getContainerTypes(Varien_Object $params = null)
    {
        if ($params == null) {
            return $this->_getAllowedContainers($params);
        }
        $method             = $params->getMethod();
        $countryShipper     = $params->getCountryShipper();
        $countryRecipient   = $params->getCountryRecipient();

        if (($countryShipper == self::USA_COUNTRY_ID && $countryRecipient == self::CANADA_COUNTRY_ID
            || $countryShipper == self::CANADA_COUNTRY_ID && $countryRecipient == self::USA_COUNTRY_ID)
            && $method == 'FEDEX_GROUND'
        ) {
            return array('YOUR_PACKAGING' => Mage::helper('usa')->__('Your Packaging'));
        } else if ($method == 'INTERNATIONAL_ECONOMY' || $method == 'INTERNATIONAL_FIRST') {
            $allTypes = $this->getContainerTypesAll();
            $exclude = array('FEDEX_10KG_BOX' => '', 'FEDEX_25KG_BOX' => '');
            return array_diff_key($allTypes, $exclude);
        } else if ($method == 'EUROPE_FIRST_INTERNATIONAL_PRIORITY') {
            $allTypes = $this->getContainerTypesAll();
            $exclude = array('FEDEX_BOX' => '', 'FEDEX_TUBE' => '');
            return array_diff_key($allTypes, $exclude);
        } else if ($countryShipper == self::CANADA_COUNTRY_ID && $countryRecipient == self::CANADA_COUNTRY_ID) {
            // hack for Canada domestic. Apply the same filter rules as for US domestic
            $params->setCountryShipper(self::USA_COUNTRY_ID);
            $params->setCountryRecipient(self::USA_COUNTRY_ID);
        }

        return $this->_getAllowedContainers($params);
    }

    /**
     * Return all container types of carrier
     *
     * @return array|bool
     */
    public function getContainerTypesAll()
    {
        return $this->getCode('packaging');
    }

    /**
     * Return structured data of containers witch related with shipping methods
     *
     * @return array|bool
     */
    public function getContainerTypesFilter()
    {
        return $this->getCode('containers_filter');
    }

    /**
     * Return delivery confirmation types of carrier
     *
     * @param Varien_Object|null $params
     * @return array
     */
    public function getDeliveryConfirmationTypes(Varien_Object $params = null)
    {
        return $this->getCode('delivery_confirmation_types');
    }


     protected function addLineItems(&$ratesRequest, $ignoreFreeItems) {
       	$lineItemArray=array();
   		$defaultFreightClass = $this->getConfigData('default_freight_class');
   		$defaultPackagingType = $this->getConfigData('item_packaging');
   		$useParent=true;
   		$unitOfMeasure = $this->getConfigData('unit_of_measure');

       	 foreach ($this->_request->getAllItems() as $item) {

       	 	$weight=0;
   			$qty=0;
   			$price=0;

   			if (!Mage::helper('wsacommon/shipping')->getItemTotals($item, $weight,$qty,$price,$useParent,$ignoreFreeItems)) {
   				continue;
   			}

       	 	if ($item->getParentItem()!=null &&  $useParent ) {
   				// must be a bundle
       	 		$product = $item->getParentItem()->getProduct();;
   			} else if ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE && !$useParent ) {
   				if ($item->getHasChildren()) {
                   	foreach ($item->getChildren() as $child) {
                   		$product=$child->getProduct();
   						break;
                   	}
   				}
   			} else {
   				$product = $item->getProduct();
   			}

   			$price=$price/$qty;
       		$freightClass=$product->getData('fedex_freight_class');
       		$packagingType=$product->getData('fedex_item_packaging');

       		if (empty($freightClass) || $freightClass=='') {
       			$freightClass=$defaultFreightClass; // use default
       		}

       	 	if (empty($packagingType) || $packagingType=='') {
       			$packagingType=$defaultPackagingType; // use default
       		}

       		$uniquePackage = $freightClass.$packagingType;


   			if (empty($lineItemArray) || !array_key_exists($uniquePackage,$lineItemArray)) {
   				$lineItemArray[$uniquePackage]= array (
   						'Class'			=> $freightClass,
   						'PackagingType'	=> $packagingType,
   						'Weight'		=> $weight,
   				);
   			} else {

   				$lineItemArray[$uniquePackage]['Weight']= $lineItemArray[$uniquePackage]['Weight']+ ($weight);
   			}
       	 }
       	 foreach ($lineItemArray as $lineItem) {

       	 	$ratesRequest['RequestedShipment']['FreightShipmentDetail']['LineItems'][]= array (
       	 		'FreightClass'	=> $lineItem['Class'],
       	 		'Packaging'		=> $lineItem['PackagingType'],
       	 		'Weight'		=> array(
                            'Value' => (float)ceil($lineItem['Weight']),
                            'Units' => $unitOfMeasure,
                ),
       	 	);
       	 }
	}

}

