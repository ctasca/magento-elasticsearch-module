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
class Pocketphp_Elasticsearch_Model_Catalog_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price
{
    const CACHE_TAG = 'MAXPRICE';

    /**
     * Retrieves max price for ranges definition.
     *
     * @return float
     */
    public function getMaxPriceInt()
    {
        $searchParams = $this->getLayer()->getProductCollection()->getExtendedSearchParams();
        $uniquePart = strtoupper(md5(serialize($searchParams)));
        $cacheKey = 'MAXPRICE_' . $this->getLayer()->getStateKey() . '_' . $uniquePart;

        $cachedData = Mage::app()->loadCache($cacheKey);
        if (!$cachedData) {
            $stats = $this->getLayer()->getProductCollection()->getStats($this->_getFilterField());

            $max = $this->_isFacetMaxPriceSet($stats) ? $stats['facets'][$this->_getFilterField()]['max'] : null;

            if (!is_numeric($max)) {
                $max = parent::getMaxPriceInt();
            }

            $cachedData = (float) $max;
            $tags = $this->getLayer()->getStateTags();
            $tags[] = self::CACHE_TAG;
            Mage::app()->saveCache($cachedData, $cacheKey, $tags);
        }

        return $cachedData;
    }

    /**
     * Adds facet condition to product collection.
     *
     * @return Pocketphp_Elasticsearch_Model_Catalog_Layer_Filter_Price
     */
    public function addFacetCondition()
    {
        $range    = $this->getPriceRange();
        $maxPrice = $this->getMaxPriceInt();
        if ($maxPrice > 0) {
            $priceFacets = array();
            $facetCount  = (int) ceil($maxPrice / $range);

            for ($i = 0; $i < $facetCount + 1; $i++) {
                $from          = ($i === 0) ? '' : ($i * $range);
                $to            = ($i === $facetCount) ? '' : (($i + 1) * $range);
                $priceFacets[] = array(
                    'from'          => $from,
                    'to'            => $to,
                    'include_upper' => !($i < $facetCount)
                );
            }

            $this->getLayer()->getProductCollection()->addFacetCondition($this->_getFilterField(), $priceFacets);
        }

        return $this;
    }

    /**
     * Returns cache tag.
     *
     * @return string
     */
    public function getCacheTag()
    {
        return self::CACHE_TAG;
    }

    /**
     * Apply price range filter to product collection.
     *
     * @return Pocketphp_Elasticsearch_Model_Catalog_Layer_Filter_Price
     */
    protected function _applyPriceRange()
    {
        $interval = $this->getInterval();
        if (!$interval) {
            return $this;
        }

        list($from, $to) = $interval;
        if ($from === '' && $to === '') {
            return $this;
        }

        if ($to !== '') {
            $to = (float) $to;
            if ($from == $to) {
                $to += .01;
            }
        }

        $field = $this->_getFilterField();
        $value = array(
            $field => array(
                'include_upper' => !($to < $this->getMaxPriceInt())
            )
        );

        if (!empty($from)) {
            $value[$field]['from'] = $from;
        }
        if (!empty($to)) {
            $value[$field]['to'] = $to;
        }

        $this->getLayer()->getProductCollection()->addFqRangeFilter($value);

        return $this;
    }

    /**
     * Returns price field according to current customer group and website.
     *
     * @return string
     */
    protected function _getFilterField()
    {
        // @todo cache filter field
        $websiteId       = Mage::app()->getStore()->getWebsiteId();
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        $priceField      = 'price_' . $customerGroupId . '_' . $websiteId;

        return $priceField;
    }

    /**
     * Retrieves current items data.
     *
     * @return array
     */
    protected function _getItemsData()
    {
        if (Mage::app()->getStore()->getConfig(self::XML_PATH_RANGE_CALCULATION) == self::RANGE_CALCULATION_IMPROVED) {
            return $this->_getCalculatedItemsData();
        } elseif ($this->getInterval()) {
            return array();
        }

        $facets = $this->getLayer()->getProductCollection()->getFacetedData($this->_getFilterField());

        $data = array();

        if (!empty($facets)) {
            foreach ($facets['ranges'] as $i => $range) {
                if ($range['count'] < 1) {
                    unset($facets['ranges'][$i]);
                }
            }
            foreach ($facets['ranges'] as $range) {
                $from = @$range['from'];
                $to = @$range['to'];
                $count = @$range['count'];

                $data[] = array(
                    'label' => $this->_renderRangeLabel($from, $to),
                    'value' => $from . '-' . $to,
                    'count' => $count,
                );

            }
        }

        return $data;
    }

    /**
     * @param $stats
     * @return bool
     */
    protected function _isFacetMaxPriceSet($stats)
    {
        return array_key_exists('facets', $stats) && array_key_exists($this->_getFilterField(), $stats['facets']) && array_key_exists('max', $stats['facets'][$this->_getFilterField()]);
    }
}
