<?php
/**
 *
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
class Pocketphp_Elasticsearch_Model_Catalog_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Attribute
{
    /**
     * Construct attribute filter
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Adds facet condition to product collection.
     *
     * @return Pocketphp_Elasticsearch_Model_Catalog_Layer_Filter_Attribute
     */
    public function addFacetCondition()
    {
        $this->getLayer()
             ->getProductCollection()
             ->addFacetCondition($this->_getAttributeFieldName($this->getAttributeModel()));

        return $this;
    }

    /**
     * Retrieves request parameter and applies it to product collection.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Mage_Core_Block_Abstract         $filterBlock
     * @return Pocketphp_Elasticsearch_Model_Catalog_Layer_Filter_Attribute
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = $request->getParam($this->_requestVar);
        if (is_array($filter) || null === $filter) {
            return $this;
        }

        $text = $this->_getOptionText($filter);
        if ($this->_isValidFilter($filter) && strlen($text)) {
            $this->applyFilterToCollection($this, $filter);
            $this->getLayer()->getState()->addFilter($this->_createItem($text, $filter));
            $this->_items = array();
        }

        return $this;
    }

    /**
     * Applies filter to product collection.
     *
     * @param $filter
     * @param $value
     * @return Pocketphp_Elasticsearch_Model_Catalog_Layer_Filter_Attribute
     */
    public function applyFilterToCollection($filter, $value)
    {
        if (!$this->_isValidFilter($value)) {
            $value = array();
        } else if (!is_array($value)) {
            $value = array($value);
        }

        $attribute = $filter->getAttributeModel();
        $param     = $this->_getSearchParam($attribute, $value);

        $this->getLayer()
             ->getProductCollection()
             ->addFqFilter($param);

        return $this;
    }

    /**
     * Retrieves current items data.
     *
     * @return array
     */
    protected function _getItemsData()
    {
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $attribute         = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        $layer = $this->getLayer();
        $key   = $layer->getStateKey() . '_' . $this->_requestVar;
        $data  = $layer->getAggregator()->getCacheData($key);

        if ($data === null) {
            $facets = $this->_getFacets();
            $data   = array();
            $terms = array();


            if (array_key_exists('terms', $facets) && count($facets['terms']) > 0) {
                if ($attribute->getFrontendInput() != 'text') {
                    $options = $attribute->getFrontend()->getSelectOptions();
                } else {
                    // @todo xdebug when entering this statement.
                    $options = array();
                    foreach ($facets as $label => $count) {
                        $options[] = array(
                            'label' => $label,
                            'value' => $label,
                            'count' => $count,
                        );
                    }
                }

                foreach ($facets['terms'] as $term) {
                    if ($term['count'] > 0)
                        $terms[$term['term']] = $term['count'];
                }

                foreach ($options as $option) {
                    if (is_array($option['value']) || !Mage::helper('core/string')->strlen($option['value'])) {
                        continue;
                    }

                    $optionId = $option['value'];

                    if (isset($terms[$optionId]) && ($this->_getIsFilterableAttribute($attribute) != self::OPTIONS_ONLY_WITH_RESULTS || array_key_exists($optionId, $terms))) {
                        $data[] = array(
                            'label' => $option['label'],
                            'value' => $optionId,
                            'count' => $terms[$optionId]
                        );
                    }
                }
            }

            $tags = array(
                Mage_Eav_Model_Entity_Attribute::CACHE_TAG . ':' . $attribute->getId()
            );

            $tags = $layer->getStateTags($tags);
            $layer->getAggregator()->saveCacheData($data, $key, $tags);
        }

        return $data;
    }


    /**
     * Returns facets data of current attribute.
     *
     * @return array
     */
    protected function _getFacets()
    {
        $productCollection = $this->getLayer()->getProductCollection();
        $fieldName         = $this->_getAttributeFieldName($this->getAttributeModel());
        $facets            = $productCollection->getFacetedData($fieldName);

        return $facets;
    }

    /**
     * Returns option label if attribute uses options.
     *
     * @param int $optionId
     * @return bool|int|string
     */
    protected function _getOptionText($optionId)
    {
        if ($this->getAttributeModel()->getFrontendInput() == 'text') {
            return $optionId; // not an option id
        }

        return parent::_getOptionText($optionId);
    }

    /**
     * Checks if given filter is valid before being applied to product collection.
     *
     * @param string $filter
     * @return bool
     */
    protected function _isValidFilter($filter)
    {
        $attribute = $this->getAttributeModel();
        if ($attribute->getSourceModel() == 'eav/entity_attribute_source_boolean')
            return $filter === '0' || $filter === '1' || false === $filter || true === $filter;


        return !empty($filter);
    }

    /**
     * Returns searched parameter as array.
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param mixed                                     $value
     * @return array
     */
    protected function _getSearchParam($attribute, $value)
    {
        if (empty($value) ||
            (isset($value['from']) && empty($value['from']) &&
                isset($value['to']) && empty($value['to']))
        ) {
            return false;
        }

        $field       = $this->_getAttributeFieldName($attribute);
        $backendType = $attribute->getBackendType();
        if ($backendType == 'datetime') {
            $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            if (is_array($value)) {
                foreach ($value as &$val) {
                    if (!is_empty_date($val)) {
                        $date = new Zend_Date($val, $format);
                        $val  = $date->toString(Zend_Date::ISO_8601) . 'Z';
                    }
                }
                unset($val);
            } else {
                if (!is_empty_date($value)) {
                    $date  = new Zend_Date($value, $format);
                    $value = $date->toString(Zend_Date::ISO_8601) . 'Z';
                }
            }
        }

        if ($attribute->usesSource()) {
            $attribute->setStoreId(Mage::app()->getStore()->getId());
        }

        return array($field => $value);
    }

    /**
     * Returns attribute field name (localized if needed).
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string                                    $localeCode
     * @return string
     */
    protected function _getAttributeFieldName(Mage_Catalog_Model_Resource_Eav_Attribute $attribute, $localeCode = null)
    {
        return Mage::getResourceModel('elasticsearch/engine')->getAttributeFieldName($attribute, $localeCode);
    }

}
