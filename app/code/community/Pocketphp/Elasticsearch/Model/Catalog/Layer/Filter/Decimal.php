<?php
/**
 *
 * READ LICENSE AT http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
class Pocketphp_Elasticsearch_Model_Catalog_Layer_Filter_Decimal extends Mage_Catalog_Model_Layer_Filter_Decimal
{
    const CACHE_TAG = 'MAXVALUE';

    /**
     * @return Pocketphp_Elasticsearch_Model_Catalog_Layer_Filter_Decimal
     */
    public function addFacetCondition()
    {
        $range    = $this->getRange();
        $maxValue = $this->getMaxValue();
        if ($maxValue > 0) {
            $facets     = array();
            $facetCount = (int) ceil($maxValue / $range);

            for ($i = 0; $i < $facetCount + 1; $i++) {
                $facets[] = array(
                    'from'          => $i * $range,
                    'to'            => ($i + 1) * $range,
                    'include_upper' => !($i < $facetCount)
                );
            }

            $fieldName = $this->_getFilterField();
            $this->getLayer()->getProductCollection()->addFacetCondition($fieldName, $facets);
        }

        return $this;
    }

    /**
     * @return float|mixed
     */
    public function getMaxValue()
    {
        $searchParams = $this->getLayer()->getProductCollection()->getExtendedSearchParams();
        $uniquePart = strtoupper(md5(serialize($searchParams)));
        $cacheKey = 'MAXVALUE_' . $this->getLayer()->getStateKey() . '_' . $uniquePart;

        $cachedData = Mage::app()->loadCache($cacheKey);
        if (!$cachedData) {
            $stats = $this->getLayer()->getProductCollection()->getStats($this->_getFilterField());

            $max = $this->_isFacetMaxValueSet($stats) ? $stats['facets'][$this->_getFilterField()]['max'] : null;

            if (!is_numeric($max)) {
                $max = parent::getMaxValue();
            }

            $cachedData = (float) $max;
            $tags = $this->getLayer()->getStateTags();
            $tags[] = self::CACHE_TAG;
            Mage::app()->saveCache($cachedData, $cacheKey, $tags);
        }

        return $cachedData;
    }

    /**
     * @param $filter
     * @param $range
     * @param $index
     * @return Pocketphp_Elasticsearch_Model_Catalog_Layer_Filter_Decimal
     */
    public function applyFilterToCollection($filter, $range, $index)
    {
        $value = array(
            $this->_getFilterField() => array(
                'from' => ($range * ($index - 1)),
                'to'   => $range * $index,
            )
        );
        $filter->getLayer()->getProductCollection()->addFqRangeFilter($value);

        return $this;
    }

    /**
     * @param Zend_Controller_Request_Abstract        $request
     * @param Mage_Catalog_Block_Layer_Filter_Decimal $filterBlock
     * @return Pocketphp_Elasticsearch_Model_Catalog_Layer_Filter_Decimal|Mage_Catalog_Model_Layer_Filter_Decimal
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }

        $filter = explode(',', $filter);
        if (count($filter) != 2) {
            return $this;
        }

        list($index, $range) = $filter;

        if ((int) $index && (int) $range) {
            $this->setRange((int) $range);

            $this->applyFilterToCollection($this, $range, $index);
            $this->getLayer()->getState()->addFilter(
                 $this->_createItem($this->_renderItemLabel($range, $index), $filter)
            );

            $this->_items = array();
        }

        return $this;
    }

    /**
     * Retrieves current items data.
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $theRange = $this->getRange();
        $fieldName = $this->_getFilterField();
        $facets = $this->getLayer()->getProductCollection()->getFacetedData($fieldName);
        $data = array();

        if (!empty($facets) && array_key_exists('ranges', $facets)) {
            foreach ($facets['ranges'] as $i => $range) {
                if ($range['count'] < 1) {
                    unset($facets['ranges'][$i]);
                }
            }
            foreach ($facets['ranges'] as $range) {
                $from = $range['from'];
                $to = $range['to'];
                $count = $range['count'];

                $rangeKey = $to / $theRange;

                $data[] = array(
                    'label' => $this->_renderItemLabel($from, $rangeKey),
                    'value' => $from . '-' . $to,
                    'count' => $count,
                );

            }
        }

        return $data;
    }

    /**
     * Renders decimal ranges.
     *
     * @param int $range
     * @param float $value
     * @return string
     */
    protected function _renderItemLabel($range, $value)
    {
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $attribute = $this->getAttributeModel();

        if ($attribute->getFrontendInput() == 'price') {
            return parent::_renderItemLabel($range, $value);
        }

        $from = ($value - 1) * $range;
        $to = $value * $range;

        if ($from != $to) {
            $to -= 0.01;
        }

        $to = Zend_Locale_Format::toFloat($to, array('locale' => Mage::getResourceModel('elasticsearch/engine')->getLocaleCode()));

        return Mage::helper('catalog')->__('%s - %s', $from, $to);
    }


    /**
     * Returns decimal field name.
     *
     * @return string
     */
    protected function _getFilterField()
    {
        $fieldName = Mage::getResourceModel('elasticsearch/engine')->getAttributeFieldName($this->getAttributeModel());

        return $fieldName;
    }

    /**
     * @param $stats
     * @return bool
     */
    protected function _isFacetMaxValueSet($stats)
    {
        return array_key_exists('facets', $stats) && array_key_exists($this->_getFilterField(), $stats['facets']) && array_key_exists('max', $stats['facets'][$this->_getFilterField()]);
    }

}
