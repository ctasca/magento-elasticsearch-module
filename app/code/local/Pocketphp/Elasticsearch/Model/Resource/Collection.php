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
 *
 * Resource collection for catalog and catalogsearch layered navigation and search results
 *
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @subpackage Model
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
class Pocketphp_Elasticsearch_Model_Resource_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{

    /**
     * Store search query text
     *
     * @var string
     */
    protected $_searchQueryText = '';

    /**
     * Store search query params
     *
     * @var array
     */
    protected $_searchQueryParams = array();

    /**
     * Store search query filters
     *
     * @var array
     */
    protected $_searchQueryFilters = array();

    /**
     * Store search query range filters
     *
     * @var array
     */
    protected $_searchQueryRangeFilters = array();

    /**
     * Store found entities ids
     *
     * @var array
     */
    protected $_searchedEntityIds = array();

    /**
     * Store engine instance
     *
     * @var Pocketphp_Elasticsearch_Model_Resource_Engine
     */
    protected $_engine = null;

    /**
     * Store sort orders
     *
     * @var array
     */
    protected $_sortBy = array();

    /**
     * General default query *:* to disable query limitation
     *
     * @var array
     */
    protected $_generalDefaultQuery = array('*' => '*');

    /**
     * General default query
     *
     * @var array
     */
    protected $_elasticsearchQuery = array();

    /**
     * Flag that defines if faceted data needs to be loaded
     *
     * @var bool
     */
    protected $_facetedDataIsLoaded = false;

    /**
     * Faceted search result data
     *
     * @var array
     */
    protected $_facetedData = array();

    /**
     * Conditions for faceted search
     *
     * @var array
     */
    protected $_facetedConditions = array();

    /**
     * Stores original page size, because _pageSize will be unset at _beforeLoad()
     * to disable limitation for collection at load with parent method
     *
     * @var int|bool
     */
    protected $_storedPageSize = false;

    /**
     * Initialize factory
     *
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param array                             $args
     */
    public function __construct($resource = null, array $args = array())
    {
        parent::__construct($resource);
    }

    /**
     * @return Pocketphp_Elasticsearch_Model_Resource_Engine
     */
    public function getEngine()
    {
        return $this->_engine;
    }

    /**
     * Stores query text filter.
     *
     * @param $query
     * @return Pocketphp_Elasticsearch_Model_Resource_Collection
     */
    public function addSearchFilter($query)
    {
        $this->_searchQueryText = $query;

        return $this;
    }

    /**
     * Aggregates search query filters.
     *
     * @return array
     */
    public function getExtendedSearchParams()
    {
        $result               = $this->_searchQueryFilters;
        $result['query_text'] = $this->_searchQueryText;

        return $result;
    }

    /**
     * @param Pocketphp_Elasticsearch_Model_Resource_Engine $engine
     * @return Pocketphp_Elasticsearch_Model_Resource_Collection
     */
    public function setEngine(Pocketphp_Elasticsearch_Model_Resource_Engine $engine)
    {
        $this->_engine = $engine;

        return $this;
    }

    /**
     * Specify category filter for product collection
     *
     * @param   Mage_Catalog_Model_Category $category
     * @return  Pocketphp_Elasticsearch_Model_Resource_Search_Collection
     */
    public function addCategoryFilter(Mage_Catalog_Model_Category $category)
    {
        $this->addFqFilter(array('categories' => $category->getId()));
        parent::addCategoryFilter($category);

        return $this;
    }

    /**
     * Add some fields to filter.
     *
     * @param $fields
     * @return Pocketphp_Elasticsearch_Model_Resource_Search_Collection
     */
    public function addFieldsToFilter($fields)
    {
        return $this;
    }


    /**
     * Add search query filter (fq)
     *
     * @param   array $filter
     * @return  Pocketphp_Elasticsearch_Model_Resource_Search_Collection
     */
    public function addFqFilter(array $filter)
    {
        if (is_array($filter)) {
            foreach ($filter as $field => $value)
                $this->_searchQueryFilters[$field] = $value;

        }

        return $this;
    }

    /**
     * Stores range filter query.
     *
     * @param array $params
     * @return Pocketphp_Elasticsearch_Model_Resource_Search_Collection
     */
    public function addFqRangeFilter($params)
    {
        if (is_array($params)) {
            foreach ($params as $field => $value) {
                $this->_searchQueryRangeFilters[$field] = $value;
            }
        }

        return $this;
    }

    /**
     * Retrieves current collection stats.
     * Used for max price.
     *
     * @param $fields
     * @return mixed
     */
    public function getStats($fields)
    {
        $query  = $this->_getQuery();
        $fQuery = $this->_getFQuery($query);

        $fQuery->setStatsFields($fields);

        $result              = $this->_engine->getStats($fQuery);
        $this->_totalRecords = $this->_getResultHitTotals($result);

        return $result;
    }

    /**
     * Set query *:* to disable query limitation
     *
     * @return Pocketphp_Elasticsearch_Model_Resource_Search_Collection
     */
    public function setGeneralDefaultQuery()
    {
        $this->_searchQueryParams = $this->_generalDefaultQuery;

        return $this;
    }

    /**
     * Stores sort order.
     *
     * @param string $attribute
     * @param string $dir
     * @return Pocketphp_Elasticsearch_Model_Resource_Search_Collection
     */
    public function setOrder($attribute, $dir = self::SORT_ORDER_DESC)
    {
        $this->_sortBy[] = array($attribute => $dir);

        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        if (is_null($this->_totalRecords)) {
            $query  = $this->_getQuery();
            $fQuery = $this->_getFQuery($query);
            $result = $this->_engine->search($fQuery);

            if (isset($result['facets'])) {
                $this->_facetedData         = $result['facets'];
                $this->_facetedDataIsLoaded = true;
            }

            $this->_totalRecords = $this->_getResultHitTotals($result);
        }

        return $this->_totalRecords;
    }

    /**
     * Return field faceted data from faceted search result
     *
     * @param string $field
     *
     * @return array
     */
    public function getFacetedData($field)
    {
        if (!$this->_facetedDataIsLoaded) {
            $this->loadFacetedData();
        }

        if (isset($this->_facetedData[$field])) {
            return $this->_facetedData[$field];
        }

        return array();
    }

    /**
     * Load faceted data if not loaded
     *
     * @return Pocketphp_Elasticsearch_Model_Resource_Search_Collection
     */
    public function loadFacetedData()
    {
        $this->_resetFacetedData();

        if (empty($this->_facetedConditions))
            return $this;

        $query  = $this->_getQuery();
        $fQuery = $this->_getFQuery($query);
        $result = $this->_engine->search($fQuery);

        if (isset($result['facets'])) {
            $this->_facetedData         = $result['facets'];
            $this->_facetedDataIsLoaded = true;
        }

        return $this;
    }

    /**
     * Allow to set faceted search conditions to retrieve result by single query
     *
     * @param string         $field
     * @param string | array $condition
     *
     * @return Pocketphp_Elasticsearch_Model_Resource_Search_Collection
     */
    public function addFacetCondition($field, $condition = null)
    {
        if (array_key_exists($field, $this->_facetedConditions)) {
            if (!empty($this->_facetedConditions[$field])) {
                $this->_facetedConditions[$field] = array($this->_facetedConditions[$field]);
            }
            $this->_facetedConditions[$field][] = $condition;
        } else {
            $this->_facetedConditions[$field] = $condition;
        }

        $this->_facetedDataIsLoaded = false;

        return $this;
    }

    /**
     * @return Mage_Catalog_Model_Resource_Product_Collection|void
     */
    protected function _beforeLoad()
    {
        $query  = $this->_getQuery();
        $fQuery = $this->_getFQuery($query);
        $result = $this->_engine->search($fQuery);

        $ids = $this->_getResultIds($result);

        $this->getSelect()
             ->where('e.entity_id IN (?)', $ids);

        $this->_storedPageSize = $this->_pageSize;
        $this->_pageSize       = false;

        return parent::_beforeLoad();

    }

    /**
     * Sort collection items by sort order of found ids
     *
     * @return Enterprise_Search_Model_Resource_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        $this->_items = $this->_sortResultItems();
        /**
         * Revert page size for proper paginator ranges
         */
        $this->_pageSize = $this->_storedPageSize;

        return $this;
    }

    /**
     * @param array $result
     * @return array
     */
    protected function _getResultIds(array $result)
    {
        if (isset($result['hits']['hits']) && count($result['hits']['hits']) > 0) {
            foreach ($result['hits']['hits'] as $hit) {
                $this->_searchedEntityIds[] = $hit['_id'];
            }
        }

        return array('ids' => $this->_searchedEntityIds);
    }

    /**
     * @param array $result
     * @return array
     */
    protected function _getResultHitTotals(array $result)
    {
        if (isset($result['hits']) && isset($result['hits']['total']))
            return (int) $result['hits']['total'];

        return 0;
    }

    /**
     * @return Pocketphp_Elasticsearch_Model_Query_Bool|Pocketphp_Elasticsearch_Model_Query_Matchall
     */
    protected function _getQuery()
    {
        if (!strlen($this->_searchQueryText))
            return $this->_getMatchallQuery();

        return $this->_getBoolQuery();
    }

    /**
     * @return Pocketphp_Elasticsearch_Model_Query_Bool
     */
    protected function _getBoolQuery()
    {
        $store     = Mage::app()->getStore($this->getStoreId());
        $boolQuery = Mage::getModel('elasticsearch/query_bool', $store);
        $query     = null;

        if (Mage::getStoreConfig('elasticsearch/search/search_query') == 'query_string')
            $query = $this->_getQueryString($store);

        if (Mage::getStoreConfig('elasticsearch/search/search_query') == 'fuzzylikethis')
            $query = $this->_getFuzzylikethisQuery($store);

        $boolQuery->addShould($query);

        return $boolQuery;
    }

    /**
     * @param Mage_Core_Model_Store $store
     * @return false|Pocketphp_Elasticsearch_Model_Query_String
     */
    protected function _getQueryString(Mage_Core_Model_Store $store)
    {
        $queryString = \Mage::getModel('elasticsearch/query_string', $store);

        $this->_setQueryFields($queryString);
        $queryString->setQueryString($this->_searchQueryText);

        return $queryString;
    }

    /**
     * @param Mage_Core_Model_Store $store
     * @return false|Pocketphp_Elasticsearch_Model_Query_Fuzzylikethis
     */
    protected function _getFuzzylikethisQuery(Mage_Core_Model_Store $store)
    {
        $flt = \Mage::getModel('elasticsearch/query_fuzzylikethis', $store);

        $this->_setQueryFields($flt);
        $flt->setLikeText($this->_searchQueryText);

        return $flt;
    }

    /**
     * Returns true if field is boosted i.e. contains ^ character.
     *
     * Example name^100
     *
     * @param $field
     * @return int
     */
    protected function _isBoostedField($field)
    {
        return preg_match('/^/', $field);
    }

    /**
     * @return Pocketphp_Elasticsearch_Model_Query_Matchall
     */
    protected function _getMatchallQuery()
    {
        $store         = Mage::app()->getStore($this->getStoreId());
        $matchallQuery = Mage::getModel('elasticsearch/query_matchall', $store);

        return $matchallQuery;
    }

    /**
     * @param Pocketphp_Elasticsearch_Model_Query_Abstract $query
     * @return Pocketphp_Elasticsearch_Model_Query_Filtered
     */
    protected function _getFQuery(Pocketphp_Elasticsearch_Model_Query_Abstract $query)
    {
        $fQuery = $this->_initFQuery();
        $fQuery->setFQuery($query);
        $this->_setFromAndSize($fQuery);
        $this->_setSortBy($fQuery);
        $this->_setQueryFacetConfitions($fQuery);

        return $fQuery;
    }

    /**
     * @return Pocketphp_Elasticsearch_Model_Query_Filtered
     */
    protected function _initFQuery()
    {
        $store  = Mage::app()->getStore($this->getStoreId());
        $fQuery = Mage::getModel('elasticsearch/query_filtered', $store);
        $fQuery->setIndexName('products');
        $fQuery->setIndexType('product');
        $fQuery->setFilterType('and');
        $fQuery->setFilters($this->_searchQueryFilters);
        $fQuery->setRangeFilters($this->_searchQueryRangeFilters);

        return $fQuery;
    }

    /**
     * Sets query from and size
     *
     * @param Pocketphp_Elasticsearch_Model_Query_Abstract $dsl
     */
    protected function _setFromAndSize(Pocketphp_Elasticsearch_Model_Query_Abstract $dsl)
    {
        if ($this->_pageSize !== false) {
            $page     = ($this->_curPage > 0) ? (int) $this->_curPage : 1;
            $rowCount = ($this->_pageSize > 0) ? (int) $this->_pageSize : 1;
            $offset   = $rowCount * ($page - 1);
            $dsl->setFrom($offset);
            $dsl->setSize($rowCount);
        }
    }

    /**
     * Resets faceted data to empty array
     *
     * @return array
     */
    protected function _resetFacetedData()
    {
        return $this->_facetedData = array();
    }

    /**
     * Sorts searched entity ids and returns sorted items array
     *
     * @return mixed
     */
    protected function _sortResultItems()
    {
        $sortedItems = array();

        foreach ($this->_searchedEntityIds as $id) {
            if (isset($this->_items[$id])) {
                $sortedItems[$id] = $this->_items[$id];
            }
        }

        return $sortedItems;
    }

    /**
     * @param $fQuery
     */
    protected function _setSortBy(Pocketphp_Elasticsearch_Model_Query_Abstract $fQuery)
    {
        if ($this->_sortBy)
            $fQuery->setSortBy($this->_sortBy);
    }

    /**
     * @param $fQuery
     */
    protected function _setQueryFacetConfitions($fQuery)
    {
        if (!empty($this->_facetedConditions))
            $fQuery->setFacets($this->_facetedConditions);
    }


    /**
     * Returns search fields array.
     *
     * Array built from configuration value for "Searched Fields CSV".
     * Remember to trim values when using array values in loops.
     *
     * @return array
     */
    protected function _getSearchFields()
    {
        $fields      = explode(',', Mage::getStoreConfig('elasticsearch/search/searched_fields'));
        $cleanFields = array();

        foreach ($fields as $field) {
            $cleanField = trim($field);
            if (strlen($cleanField) > 0)
                $cleanFields[] = $cleanField;
        }

        return $cleanFields;
    }

    /**
     * @param Pocketphp_Elasticsearch_Model_Query_Abstract $query
     */
    protected function _setQueryFields(Pocketphp_Elasticsearch_Model_Query_Abstract $query)
    {
        $searchedFields = $this->_getSearchFields();

        if (count($searchedFields) > 0)
            $query->setFields($searchedFields);
        else
            $query->setDefaultField('_all');
    }
}