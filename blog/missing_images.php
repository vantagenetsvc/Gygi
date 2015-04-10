<?php
/**
 * Created by PhpStorm.
 * User: Ted
 * Date: 12/9/14
 * Time: 3:17 PM
 */



require 'app/Mage.php';
Mage::app();
$_products = Mage::getModel('catalog/product')
    ->getCollection()
    ->addAttributeToSelect('*')
    ->addAttributeToFilter(array(
        array (
            'attribute' => 'image',
            'like' => 'no_selection'
        ),
        array (
            'attribute' => 'image', // null fields
            'null' => true
        ),
        array (
            'attribute' => 'image', // empty, but not null
            'eq' => ''
        ),
        array (
            'attribute' => 'image', // check for information that doesn't conform to Magento's formatting
            'nlike' => '%/%/%'
        ),
    ));

foreach($_products as $_product){

    echo $_product->getSku();

}