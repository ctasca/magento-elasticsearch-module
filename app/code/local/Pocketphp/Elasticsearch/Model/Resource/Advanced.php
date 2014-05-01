<?php
/**
 * READ LICENSE AT  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Pocketphp Elasticsearch module to newer versions in the future.
 * If you wish to customize the Pocketphp Elasticsearch module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @copyright  Copyright (C) 2014 Pocketphp ltd (http://pocketphp.co.uk)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Short description of the class
 *
 * Long description of the class (if any...)
 *
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @subpackage Model
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
class Pocketphp_Elasticsearch_Model_Resource_Advanced extends Mage_Core_Model_Resource_Abstract
{
    /**
     * Defines text type fields
     * Integer attributes are saved at metadata as text because in fact they are values for
     * options of select type inputs but their values are presented as text aliases
     *
     * @var array
     */
    protected $_textFieldTypes = array(
        'text',
        'varchar',
        'int'
    );

    /**
     * Empty construct
     */
    protected function _construct()
    {

    }

    /**
     * Add not indexable field to search
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute         $attribute
     * @param string|array                                      $value
     * @param Pocketphp_Elasticsearch_Model_Resource_Collection $collection
     *
     * @return bool
     */
    public function prepareCondition($attribute, $value, $collection)
    {
        return $this->addIndexableAttributeModifiedFilter($collection, $attribute, $value);
    }

    /**
     * Add filter by indexable attribute
     *
     * @param Pocketphp_Elasticsearch_Model_Resource_Collection $collection
     * @param Mage_Catalog_Model_Resource_Eav_Attribute         $attribute
     * @param string|array                                      $value
     *
     * @return bool
     */
    public function addIndexableAttributeModifiedFilter($collection, $attribute, $value)
    {
        $param = $this->_getSearchParam($collection, $attribute, $value);

        if (!empty($param)) {
            $collection->addFqFilter($param);

            return true;
        }

        return false;
    }
    /**
     * Add filter by attribute rated price
     *
     * @param Pocketphp_Elasticsearch_Model_Resource_Collection $collection
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string|array $value
     * @param int $rate
     *
     * @return bool
     */
    public function addRatedPriceFilter($collection, $attribute, $value, $rate = 1)
    {
        $collection->addPriceData();
        $fieldName = $this->_getPriceFieldName();
        $collection->addFqRangeFilter(array($fieldName => $value));

        return true;
    }

    /**
     * Returns price field according to current customer group and website.
     *
     * @return string
     */
    protected function _getPriceFieldName()
    {
        $websiteId       = Mage::app()->getStore()->getWebsiteId();
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $priceField      = 'price_' . $customerGroupId . '_' . $websiteId;

        return $priceField;
    }

    /**
     * Retrieve filter array
     *
     * @param Pocketphp_Elasticsearch_Model_Resource_Collection $collection
     * @param Mage_Catalog_Model_Resource_Eav_Attribute         $attribute
     * @param string|array                                      $value
     * @return array
     */
    protected function _getSearchParam(Pocketphp_Elasticsearch_Model_Resource_Collection $collection,
                                       Mage_Catalog_Model_Resource_Eav_Attribute $attribute, $value)
    {
        if ((!is_string($value) && empty($value))
            || (is_string($value) && strlen(trim($value)) == 0)
            || (is_array($value)
                && isset($value['from'])
                && empty($value['from'])
                && isset($value['to'])
                && empty($value['to']))
        ) {
            return array();
        }

        if (!is_array($value)) {
            $value = array($value);
        }

        $field = Mage::getResourceSingleton('elasticsearch/engine')
                     ->getAttributeFieldName($attribute);

        if ($attribute->getBackendType() == 'datetime') {
            $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            foreach ($value as &$val) {
                if (!is_empty_date($val)) {
                    $date = new Zend_Date($val, $format);
                    $val  = $date->toString(Zend_Date::ISO_8601) . 'Z';
                }
            }
            unset($val);
        }

        if (empty($value)) {
            return array();
        } else {
            return array($field => $value);
        }
    }

    /**
     * Retrieve connection for read data
     *
     * Method implemented for compatibility with existing interface protocol
     * inherited from abstract resource model
     */
    protected function _getReadAdapter()
    {
        return null;
    }

    /**
     * Retrieve connection for write data
     *
     * Method implemented for compatibility with existing interface protocol
     * inherited from abstract resource model
     */
    protected function _getWriteAdapter()
    {
        return null;
    }

}
