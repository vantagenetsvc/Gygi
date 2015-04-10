<?php
/*
 * @category   Webshopapps
 * @package    Webshopapps_UsaShipping
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt
 * @author     Karen Baker <sales@webshopapps.com>
*/
class Webshopapps_Boxmenu_Model_Boxmenu extends Mage_Core_Model_Abstract
{

	static protected $_boxmenuGroups;

    public function _construct()
    {
        parent::_construct();

        $this->_init('boxmenu/boxmenu');
        $this->setIdFieldName('boxmenu_id');
    }


     /**
     * Retrieve option array excluding USPS flat boxes
     *
     * @return array
     */
    static public function getOptionArray()
    {
        $options = array();
        foreach(self::getBoxmenuGroups() as $boxmenuId=>$boxmenuGroup) {
            $boxType = $boxmenuGroup->getBoxType();
            if($boxType == 4 || $boxType == 0) { //include not defined to cater for upgrades where sql may not run.
                $options[$boxmenuId] = $boxmenuGroup['title'];
            }
        }
        return $options;
    }



	public function toOptionArray()
    {
        $arr = array();
        foreach(self::getBoxmenuGroups() as $boxmenuId=>$boxmenuGroup) {
        	$arr[] = array('value'=>$boxmenuId, 'label'=>$boxmenuGroup['title']);
        }
        return $arr;
    }

    static public function getBoxmenuGroups()
    {
        if (is_null(self::$_boxmenuGroups)) {
            self::$_boxmenuGroups = Mage::getModel('boxmenu/boxmenu')->getCollection();
        }

        return self::$_boxmenuGroups;
    }


    /**
     * Retrieve all standard options (not USPS)
     *
     * @internal param bool $uspsBoxes
     * @return   array
     * @bug      parameter always set to true for packing box attribute by Mage_Adminhtml_Block_Widget_Form::_setFieldset
     */
    static public function getAllOptions()
    {
        $res = array();
        $res[] = array('value'=>'', 'label'=> Mage::helper('catalog')->__('-- Custom --'));
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }

    /**
     * Get the USPS boxes only
     *
     * @return array
     */
    static public function getAllUSPSOptions()
    {
        $options = array();

        foreach(self::getBoxmenuGroups() as $boxmenuId=>$boxmenuGroup) {
            $boxType = $boxmenuGroup->getBoxType();

            if ($boxType!=4 && $boxType!=0) {
                $options[$boxmenuId] = $boxmenuGroup['title'];
            }
        }

        return $options;
    }

    /**
     * Retrieve option text
     *
     * @param int $optionId
     * @return string
     */
    static public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }


     /**
     * Get Column(s) names for flat data building
     *
     * @return array
     */
    public function getFlatColums()
    {
        $columns = array();
        $columns[$this->getAttribute()->getAttributeCode()] = array(
            'type'      => 'int',
            'unsigned'  => false,
            'is_null'   => true,
            'default'   => null,
            'extra'     => null
        );
        return $columns;
   }

    /**
     * Retrieve Select for update Attribute value in flat table
     *
     * @param   int $store
     * @return  Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute_option')
            ->getFlatUpdateSelect($this->getAttribute(), $store, false);
    }
}