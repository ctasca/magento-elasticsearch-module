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
class Pocketphp_Elasticsearch_Model_Query_Filtered extends Pocketphp_Elasticsearch_Model_Query_Dsl
{
    /**
     * Max size for terms facets
     */
    const FACETS_TERMS_MAX_SIZE = 1000;
    /**
     * @var filter type 'and' or 'or'
     */
    protected $_filterType;

    /**
     * @var Pocketphp_Elasticsearch_Model_Query_Abstract filtered query
     */
    protected $_fQuery;

    /**
     * @param Mage_Core_Model_Store $store
     */
    public function __construct(Mage_Core_Model_Store $store)
    {
        parent::__construct($store);
    }

    /**
     * Returns query DSL array for Searcher
     *
     * @return array
     */
    public function get()
    {
        $this->_query = parent::get();

        if ($this->_getFQuery() instanceof Pocketphp_Elasticsearch_Model_Query_Abstract)
            $this->_query['body']['query']['filtered']['query'] = $this->_getFQuery()->get();

        $this->_setFilters();
        $this->_setFacets();
        $this->_setStatsFields();

        return $this->_query;
    }

    /**
     * @param \filter $filterType
     */
    public function setFilterType($filterType)
    {
        $this->_filterType = $filterType;
    }

    /**
     * @param \Pocketphp_Elasticsearch_Model_Query_Abstract $fQuery
     */
    public function setFQuery($fQuery)
    {
        $this->_fQuery = $fQuery;
    }

    /**
     * @return \Pocketphp_Elasticsearch_Model_Query_Abstract
     */
    protected function _getFQuery()
    {
        return $this->_fQuery;
    }

    /**
     * @return \filter
     */
    protected function _getFilterType()
    {
        return $this->_filterType;
    }

    /**
     * Sets filters for filtered query
     */
    protected function _setFilters()
    {
        if (count($this->_getFilters()) > 0)
            foreach ($this->_getFilters() as $key => $filterValue) {
                if ($key == 'categories') {
                    $orFilter                                                                                  = array();
                    $orFilter['or']['filters'][]['terms'][$key]                                                = array($filterValue);
                    $orFilter['or']['filters'][]['terms']['show_in_categories']                                = array($filterValue);
                    $this->_query['body']['query']['filtered']['filter'][$this->_getFilterType()]['filters'][] = $orFilter;
                } else {
                    $this->_query['body']['query']['filtered']['filter'][$this->_getFilterType()]['filters'][]['term'][$key] = $filterValue;
                }

            }

        if (count($this->_getRangeFilters()) > 0)
            foreach ($this->_getRangeFilters() as $key => $filterValue)
                $this->_query['body']['query']['filtered']['filter'][$this->_getFilterType()]['filters'][]['range'][$key] = $filterValue;
    }

    /**
     * Sets facets for query
     *
     * For categories facet, show_in_categories is added to terms array as well as facet filter
     * for correct layered navigation filtering.
     *
     * When the facet condition has a 'from' or a 'to', facet is of type 'range' and condition
     * is added to ranges array.
     *
     * For other types, a 'terms' facet with corresponding field-value pair is set.
     *
     */
    protected function _setFacets()
    {
        if (count($this->_getFacets()) > 0)
            foreach ($this->_getFacets() as $key => $facet) {
                if (is_array($facet)) {
                    if ($key == 'categories') {
                        $this->_query['body']['facets'][$key]['terms']['size']  = static::FACETS_TERMS_MAX_SIZE;
                        $this->_query['body']['facets'][$key]['terms']['fields'] = array(
                            $key,
                            'show_in_categories'
                        );
                        $this->_query['body']['facets'][$key]['facet_filter']['or']['filters'][]['terms'][$key]                 = $facet;
                        $this->_query['body']['facets'][$key]['facet_filter']['or']['filters'][]['terms']['show_in_categories'] = $facet;
                    } else {
                        foreach ($facet as $condition) {
                            if (isset($condition['from']) || isset($condition['to'])) {
                                if ($condition['from'] == '')
                                    $condition['from'] = 0;
                                if ($condition['to'] == '')
                                    unset($condition['to']);
                                $this->_query['body']['facets'][$key]['range']['field']    = $key;
                                $this->_query['body']['facets'][$key]['range']['ranges'][] = $condition;

                            } else {
                                $this->_query['body']['facets'][$key]['terms']['field'] = $key;
                                break;
                            }
                        }
                    }

                } else {
                    $this->_query['body']['facets'][$key]['terms']['size']  = static::FACETS_TERMS_MAX_SIZE;
                    $this->_query['body']['facets'][$key]['terms']['field'] = $key;
                }

            }

    }

    protected function _setStatsFields()
    {
        foreach ($this->_getStatsFields() as $field) {
            $this->_query['body']['facets'][$field]['statistical']['field'] = $field;
        }
    }
}
