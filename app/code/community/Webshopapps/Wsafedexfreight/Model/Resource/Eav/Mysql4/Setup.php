<?php
/* Fedex Freight Shipping
 *
 * @category   Webshopapps
 * @package    Webshopapps_Wsafedexfreight
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */
class Webshopapps_Wsafedexfreight_Model_Resource_Eav_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup {
  
    public function getDefaultEntities() {
        return array(
            'catalog_product' => array(
                'entity_model'      => 'catalog/product',
                'attribute_model'   => 'catalog/resource_eav_attribute',
                'table'             => 'catalog/product',
                'additional_attribute_table' => 'catalog/eav_attribute',
                'entity_attribute_collection' => 'catalog/product_attribute_collection',
                'attributes'        => array(
                  'fedex_freight_class' => array(
                    'group'             => 'Shipping',
                    'type'              => 'varchar',
                    'backend'           => '',
                    'frontend'          => '',
                    'label'             => 'Fedex Freight Class',
                    'input'             => 'select',
                    'class'             => '',
                    'source'            => 'wsafedexfreight/shipping_carrier_wsafedexfreight_source_freightclass',
                    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'visible'           => true,
                    'required'          => false,
                    'user_defined'      => true,
                    'searchable'        => false,
                    'filterable'        => false,
                    'comparable'        => false,
                    'visible_on_front'  => false,
                    'unique'            => false,
                    'apply_to'          => 'simple,configurable,bundle,grouped',
                  ),
                  'fedex_item_packaging' => array(
                    'group'             => 'Shipping',
                    'type'              => 'varchar',
                    'backend'           => '',
                    'frontend'          => '',
                    'label'             => 'Item Packaging',
                    'input'             => 'select',
                    'class'             => '',
                    'source'            => 'wsafedexfreight/shipping_carrier_wsafedexfreight_source_itempackaging',
                    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                    'visible'           => true,
                    'required'          => false,
                    'user_defined'      => true,
                    'searchable'        => false,
                    'filterable'        => false,
                    'comparable'        => false,
                    'visible_on_front'  => false,
                    'unique'            => false,
                    'apply_to'          => 'simple,configurable,bundle,grouped',
                  )
               )
           )
      );
    }
  }
?>