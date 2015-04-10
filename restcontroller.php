<?php
/**
 * Created by PhpStorm.
 * User: Ted Sanders
 * Date: 3/20/15
 * Time: 2:02 PM
 */
 

class RestConnect_TestController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {

        //Basic parameters that need to be provided for oAuth authentication
        //on Magento
        $params = array(
            'siteUrl' => 'http://www.gygi.com/oauth',
            'requestTokenUrl' => 'http://www.gygi.com/oauth/initiate',
            'accessTokenUrl' => 'http://www.gygi.com/oauth/token',
            'authorizeUrl' => 'http://www.gygi.com/admin/oAuth_authorize',//This URL is used only if we authenticate as Admin user type
            'consumerKey' => 'fe770afdb19332e4f42297349d6cb6b4',//Consumer key registered in server administration
            'consumerSecret' => '6b5dc30339d937caf0191ee05b3ad32c',//Consumer secret registered in server administration
            'callbackUrl' => 'http://www.gygi.com/restconnect/test/callback',//Url of callback action below
        );

        // Initiate oAuth consumer with above parameters
        $consumer = new Zend_Oauth_Consumer($params);
        // Get request token
        $requestToken = $consumer->getRequestToken();
        // Get session
        $session = Mage::getSingleton('core/session');
        // Save serialized request token object in session for later use
        $session->setRequestToken(serialize($requestToken));
        // Redirect to authorize URL
        $consumer->redirect();

        return;
    }

    public function callbackAction() {

        //oAuth parameters
        $params = array(
            'siteUrl' => 'http://www.gygi.com/oauth',
            'requestTokenUrl' => 'http://www.gygi.com/oauth/initiate',
            'accessTokenUrl' => 'http://www.gygi.com/oauth/token',
            'consumerKey' => 'fe770afdb19332e4f42297349d6cb6b4',
            'consumerSecret' => '6b5dc30339d937caf0191ee05b3ad32c'
        );

        // Get session
        $session = Mage::getSingleton('core/session');
        // Read and unserialize request token from session
        $requestToken = unserialize($session->getRequestToken());
        // Initiate oAuth consumer
        $consumer = new Zend_Oauth_Consumer($params);
        // Using oAuth parameters and request Token we got, get access token
        $acessToken = $consumer->getAccessToken($_GET, $requestToken);
        // Get HTTP client from access token object
        $restClient = $acessToken->getHttpClient($params);
        // Set REST resource URL
        $restClient->setUri('http://www.gygi.com/api/rest/products');
        // In Magento it is neccesary to set json or xml headers in order to work
        $restClient->setHeaders('Accept', 'application/json');
        // Get method
        $restClient->setMethod(Zend_Http_Client::GET);
        //Make REST request
        $response = $restClient->request();
        // Here we can see that response body contains json list of products
        Zend_Debug::dump($response);

        return;
    }

}