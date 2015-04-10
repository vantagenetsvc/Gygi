
<?php
/**
 * User: Ted Sanders Digital Marketing Manager
 * Date: 3/24/2015
 * Time: 10:00 am
 */
//xml feed for gygicookingclasses.com
require_once 'app/Mage.php';
umask(0);
Mage::app('default');
// set Store ID
$store_id = Mage::app()->getStore()->getStoreId();
// set Cat ID Gygi.com/cooking-classes.html
$cat_id = 1431;


$cat = Mage::getModel('catalog/product')->setId(1431);

$products = Mage::getModel('catalog/product')
    ->getCollection()
    ->addCategoryFilter($cat)
    ->addAttributeToSelect('*')
    ->setOrder('name','asc');


$i=1;
foreach($products as $p) {
    //$product    = Mage::getModel('catalog/product');
    echo  $p->getName().'Sku'. $p->getSku().'price'. $p->getPrice().'<br>';
    $i++;
}
echo $i;

$doc = new DOMDocument();
$doc->formatOutput = true;

$r = $doc->createElement( "products" );
$doc->appendChild( $r );

foreach($products as $p) {
    $b = $doc->createElement( "product" );

    $name = $doc->createElement( "name" );
    $name->appendChild(
        $doc->createTextNode( $p->getName() )
    );
    $b->appendChild( $name );

    $sku = $doc->createElement( "sku" );
    $sku->appendChild(
        $doc->createTextNode( $p->getSku() )
    );
    $b->appendChild( $sku );

    $short_description = $doc->createElement( "short_description" );
    $short_description->appendChild(
        $doc->createTextNode( $p->getShortDescription() )
    );
    $b->appendChild( $short_description );

    $description = $doc->createElement( "description" );
    $description->appendChild(
        $doc->createTextNode( $p->getDescription() )
    );
    $b->appendChild( $description );
/// Get Quantity
    $inventory_qty = $doc->createElement( "quantity");
    $inventory_qty->appendChild(
        $doc->createTextNode( $p->getInventoryQuantity () )
    );
    $b->appendChild( $inventory_qty);
/// get news_from_date
    $new_from_date = $doc->createElement( "Product As New From Date");
    $new_from_date->appendChild(
        $doc->createTextNode( $p->getNewsFromDate () )
    );

    $price = $doc->createElement( "price" );
    $price->appendChild(
        $doc->createTextNode( $p->getPrice() )
    );
    $b->appendChild( $price );

    $SpecialPrice = $doc->createElement( "SpecialPrice" );
    $SpecialPrice->appendChild(
        $doc->createTextNode( $p->getSpecialPrice() )
    );
    $b->appendChild( $SpecialPrice );

    $ProductUrl = $doc->createElement( "ProductUrl" );
    $ProductUrl->appendChild(
        $doc->createTextNode( $p->getProductUrl() )
    );
    $b->appendChild( $ProductUrl );

    $ImageUrl = $doc->createElement( "ImageUrl" );
    $ImageUrl->appendChild(
        $doc->createTextNode( $p->getImageUrl() )
    );
    $b->appendChild( $ImageUrl );

    $SmallImageUrl = $doc->createElement( "SmallImageUrl" );
    $SmallImageUrl->appendChild(
        $doc->createTextNode( $p->getSmallImageUrl() )
    );
    $b->appendChild( $SmallImageUrl );

    $ThumbnailUrl = $doc->createElement( "ThumbnailUrl" );
    $ThumbnailUrl->appendChild(
        $doc->createTextNode( $p->getThumbnailUrl() )
    );
    $b->appendChild( $ThumbnailUrl );



    $r->appendChild( $b );
}

echo $doc->saveXML();
$doc->save("magento_inventory_1.xml")
?>