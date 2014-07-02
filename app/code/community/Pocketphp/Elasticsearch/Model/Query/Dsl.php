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
class Pocketphp_Elasticsearch_Model_Query_Dsl extends Pocketphp_Elasticsearch_Model_Query_Abstract
{

    /**
     * @var string
     */
    protected $_defaultField;
    /**
     * @var array
     */
    protected $_fields;
    /**
     * @var array
     */
    protected $_sortableAttributes;

    /**
     * Creates a new instance of Pocketphp_Elasticsearch_Model_Query_Dsl
     * with given store.
     *
     * Store should be an instance of Mage_Core_Model_Store
     *
     * @param Mage_Core_Model_Store $store
     */
    public function __construct(Mage_Core_Model_Store $store)
    {
        $this->_store   = $store;
        $this->_storeId = $store->getId();
    }

    /**
     * Returns query DSL array for Searcher
     *
     * @return array
     */
    public function get()
    {
        $this->_query = array();
        //$this->_query['body']['locale'] = $this->_getLocaleCode();
        $this->_setIndexAndType();
        $this->_addSearchTypeIfSet();
        $this->_addQParamIfSet();
        $this->_setFrom();
        $this->_setSize();
        $this->_setSortBy();
        $this->_setBody();

        return $this->_query;
    }

    /**
     * @param mixed $defaultField
     */
    public function setDefaultField($defaultField)
    {
        $this->_defaultField = $defaultField;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        $this->_fields = $fields;
    }

    /**
     * @return array
     */
    protected function _getFields()
    {
        return $this->_fields;
    }

    /**
     * @return mixed
     */
    protected function _getDefaultField()
    {
        return $this->_defaultField;
    }

    /**
     * @return bool
     */
    protected function _canAddFields()
    {
        return $this->_getFields() && is_array($this->_getFields()) && count($this->_getFields()) > 0;
    }


    protected function _setIndexAndType()
    {
        $this->_query['index'] = $this->_getIndexName() . '_' . $this->_getStoreId();
        $this->_query['type']  = $this->_getIndexType();
    }

    protected function _addSearchTypeIfSet()
    {
        if ($this->_getType())
            $this->_query['search_type'] = $this->_getType();
    }

    protected function _addQParamIfSet()
    {
        if ($this->_getQ())
            $this->_query['q'] = $this->_getQ();
    }

    protected function _setBody()
    {
        $this->_query['body'] = $this->_getBody();
    }

    protected function _setFilters()
    {
        if (count($this->_getFilters()) > 0)
            $this->_query['body']['query']['filters'] = $this->_getFilters();
    }

    protected function _setFacets()
    {
        if (count($this->_getFacets()) > 0)
            $this->_query['body']['facets'] = $this->_getFacets();
    }

    protected function _setStatsFields()
    {
        if (count($this->_getStatsFields()) > 0)
            $this->_query['body']['query']['stats']['fields'] = $this->_getStatsFields();
    }

    /**
     * Sets from paramater
     */
    protected function _setFrom()
    {
        if ($this->_getFrom() && is_numeric($this->_getFrom()))
            $this->_query['from'] = $this->_getFrom();
    }

    /**
     * Sets size paramter
     */
    protected function _setSize()
    {
        if ($this->_getSize() && is_numeric($this->_getSize()))
            $this->_query['size'] = $this->_getSize();
    }

    /**
     * Sets sort by parameter
     */
    protected function _setSortBy()
    {
        if ($this->_getSortBy()) {
            $sortFields = $this->_prepareSortFields($this->_getSortBy());
            $sortArray = array();
            foreach($sortFields as $sortField) {
                foreach ($sortField as $field => $order) {
                    $sortArray[] = "{$field}:{$order}";
                }
            }
            $this->_query['sort'] = implode(',', $sortArray);
        }
    }

    /**
     * Returns sortable attribute field name.
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return string
     */
    protected function _getSortableAttributeFieldName($attribute)
    {
        if (is_string($attribute)) {
            $this->_setSortableAttributesIfNotSet();

            if (!isset($this->_sortableAttributes[$attribute]))
                return $attribute;

            $attribute = $this->_sortableAttributes[$attribute];
        }

        $attributeCode = $attribute->getAttributeCode();

        return 'sort_' . $attributeCode;
    }

    /**
     * Retrieves all sortable product attributes.
     *
     * @return array
     */
    protected function _setSortableAttributesIfNotSet()
    {
        if (null === $this->_sortableAttributes) {
            $this->_sortableAttributes = Mage::getSingleton('catalog/config')->getAttributesUsedForSortBy();
            if (array_key_exists('price', $this->_sortableAttributes)) {
                //handled with searchable attribute.
                unset($this->_sortableAttributes['price']);
            }
        }
    }

    /**
     * Prepares sort fields.
     *
     * @param array $sortBy
     * @return array
     */
    protected function _prepareSortFields($sortBy)
    {
        $result = array();
        foreach ($sortBy as $sort) {
            $_sort     = each($sort);
            $sortField = $_sort['key'];
            $sortType  = $_sort['value'];
            if ($sortField == 'relevance') {
                $sortField = '_score';
            } elseif ($sortField == 'position') {
                $sortField = 'position_category_' . Mage::registry('current_category')->getId();
            } elseif ($sortField == 'price') {
                $websiteId       = Mage::app()->getStore()->getWebsiteId();
                $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
                $sortField       = 'price_' . $customerGroupId . '_' . $websiteId;
            } else {
                $sortField = $this->_getSortableAttributeFieldName($sortField);
            }
            $result[] = array($sortField => trim(strtolower($sortType)));
        }

        return $result;
    }

}
