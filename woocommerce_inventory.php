<?php
/**
 * Created by PhpStorm.
 * User: Ted Sanders Digital Marketing Manager not Developer put smiley face here
 * Date: 3/23/15
 * Time: 10:50 AM
 */

// REST api script to push product inventory from https:www.gygi.com/cooking-classes.html to https:www.gygicookingclasses.com
// Attributes included are quantity ("inventory_qty") and sku ("sku")
// Category is Cooking Classes (ID: 1431) (add all product inventory from www.gygi.com to www.gygicookingclasses.com
// accompanying CRON job to run q 15 minutes to update order inventory status.
// 2nd file to be created is Woo Commerce_products.php to update product attributes with CRON job q 1 day.
// Accompanying example is found http://www.magentocommerce.com/api/rest/introduction.html under Create a simple product as an Admin user with OAuth authentication


// Open session in Magento to access REST api in Magento
$callbackUrl -= "http://www.gygi.com/oauth_admin.php";
$temporaryCredentialsRequestUrl = "http://www.gygi.com/oauth/initiate?oauth_callback=" . urlencode ($callbackUrl);
$adminAuthorizationUrl='http://www.gygi.com/admin/oauth_authorize';
$accessTokenRequestUrl='http://www.gygi.com/oauth/token';
$apiUrl = 'http:www.gygi.com/api/rest';
$consumerKey = "XXXXXXXXXXXXXX";//need to include REST api consumer key
$consumerSecret = "XXXXXXXXXXXX"; // need to include REST api consumer secret

// Start session in REST api using oauth_token
session_start();
if (!isset$_GET ('oauth_token']) && isset ($_SESSION['state']) && $_SESSION['state'] == 1) {
    if $_SESSION ['state'] = 0;
}
//try condition if oauth_token state isn't working
    try {
   $authType = ($_SESSION['state'] == 2) ? OAUTH_AUTH_TYPE_AUTHORIZATION : OAUTH_AUTH_TYPE_URI;
   $oauthClient = new OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, $authType);
        $oauthClient->enableDebug();

    if (!isset($_GET['oauth_token']) && !$_SESSION['state']) {
        $requestToken = $oauthClient->getRequestToken($temporaryCredentialsRequestUrl);
        $_SESSION['secret'] = $requestToken['oauth_token_secret'];
        $_SESSION['state'] = 1;
        header('Location: ' . $adminAuthorizationUrl . '?oauth_token=' . $requestToken['oauth_token']);
        exit;
    } else if ($_SESSION['state'] == 1) {
        $oauthClient->setToken($_GET['oauth_token'], $_SESSION['secret']);
        $accessToken = $oauthClient->getAccessToken($accessTokenRequestUrl);
        $_SESSION['state'] = 2;
        $_SESSION['token'] = $accessToken['oauth_token'];
        $_SESSION['secret'] = $accessToken['oauth_token_secret'];
        header('Location: ' . $callbackUrl);
        exit;
    } else {
        $oauthClient->setToken($_SESSION['token'], $_SESSION['secret']);
        $resourceUrl = "$apiUrl/products";
// This needs to be a variable to call existing products in attribute_set_id='36' for www.gygi.com/cooking-classes.html
        $productData = json_encode(array(
            'type_id'           => 'simple',
            'attribute_set_id'  => 36,
            'sku'               => 'simple' . uniqid(),
            'weight'            => 1,
            'status'            => 1,
            'visibility'        => 4,
            'name'              => 'Simple Product',
            'description'       => 'Simple Description',
            'short_description' => 'Simple Short Description',
            'price'             => 99.95,
            'tax_class_id'      => 0,
        ));
        $headers = array('Content-Type' => 'application/json');
        $oauthClient->fetch($resourceUrl, $productData, OAUTH_HTTP_METHOD_POST, $headers);
        print_r($oauthClient->getLastResponseInfo());
    }
} catch (OAuthException $e) {
    print_r($e);

}
//Need to open session in

