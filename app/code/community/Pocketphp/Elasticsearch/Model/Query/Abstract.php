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
abstract class Pocketphp_Elasticsearch_Model_Query_Abstract
{
    /**
     * @var array elasticsearch query
     */
    protected $_query = array();
    /**
     * @var string Lucene query string
     */
    protected $_q;
    /**
     * @var string query string
     */
    protected $_queryString;
    /**
     * @var string search type e.g. query_and_fetch
     */
    protected $_type;
    /**
     * @var string index name
     */
    protected $_indexName;
    /**
     * @var string index type
     */
    protected $_indexType;
    /**
     * @var int
     */
    protected $_from;
    /**
     * @var int
     */
    protected $_size;
    /**
     * @var array query body parameters array
     */
    protected $_body = array();
    /**
     * @var array search query filters array
     */
    protected $_filters = array();
    /**
     * @var array search query facets array
     */
    protected $_facets = array();
    /**
     * @var array search query range filters array
     */
    protected $_rangeFilters = array();
    /**
     * @var array search query range filters array
     */
    protected $_statsFields = array();
    /**
     * @var Mage_Core_Model_Store
     */
    protected $_store;
    /**
     * @var int
     */
    protected $_storeId;
    /**
     * @var string
     */
    protected $_localeCode;
    /**
     * @var string
     */
    protected $_sortBy;
    /**
     * @return array
     */
    abstract function get();

    /**
     * @param string $indexName
     */
    public function setIndexName($indexName)
    {
        $this->_indexName = $indexName;
    }

    /**
     * @param string $indexType
     */
    public function setIndexType($indexType)
    {
        $this->_indexType = $indexType;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * @param string $q
     */
    public function setQ($q)
    {
        $this->_q = $q;
    }

    /**
     * @param string $queryString
     */
    public function setQueryString($queryString)
    {
        $this->_queryString = $queryString;
    }

    /**
     * @param array $body
     */
    public function setBody(array $body)
    {
        $this->_body = $body;
    }

    /**
     * @param array $filters
     */
    public function setFilters(array $filters)
    {
        $this->_filters = $filters;
    }

    /**
     * @param array $facets
     */
    public function setFacets($facets)
    {
        $this->_facets = $facets;
    }


    /**
     * @param array $rangeFilters
     */
    public function setRangeFilters(array $rangeFilters)
    {
        $this->_rangeFilters = $rangeFilters;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->_size = $size;
    }

    /**
     * @param int $from
     */
    public function setFrom($from)
    {
        $this->_from = $from;
    }

    /**
     * @param string $sortBy
     */
    public function setSortBy($sortBy)
    {
        $this->_sortBy = $sortBy;
    }

    /**
     * @param array $statsFields
     */
    public function setStatsFields($statsFields)
    {
        if (!is_array($statsFields)) {
            $statsFields = array($statsFields);
        }
        $this->_statsFields = $statsFields;
    }

    /**
     * @return string
     */
    protected function _getType()
    {
        return $this->_type;
    }

    /**
     * @return int
     */
    protected function _getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        return $this->_store;
    }


    /**
     * @return string
     */
    protected function _getQ()
    {
        $escaper = Mage::getModel('elasticsearch/text_escaper');
        $escaper->setText($this->_q);
        return $escaper->escape();
    }


    /**
     * @return string
     */
    protected function _getQueryString()
    {
        $escaper = Mage::getModel('elasticsearch/text_escaper');
        $escaper->setText($this->_queryString);
        return $escaper->escape();
    }


    /**
     * @return string
     */
    protected function _getIndexName()
    {
        return $this->_indexName;
    }


    /**
     * @return string
     */
    protected function _getIndexType()
    {
        return $this->_indexType;
    }
    /**
     * @return string
     */
    protected function _getSortBy()
    {
        return $this->_sortBy;
    }

    /**
     * @return int
     */
    protected function _getSize()
    {
        return $this->_size;
    }

    /**
     * @return int
     */
    protected function _getFrom()
    {
        return $this->_from;
    }

    /**
     * @return array
     */
    protected function _getBody()
    {
        return $this->_body;
    }

    /**
     * @return array
     */
    protected function _getFilters()
    {
        return $this->_filters;
    }

    /**
     * @return array
     */
    protected function _getFacets()
    {
        return $this->_facets;
    }


    /**
     * @return array
     */
    protected function _getRangeFilters()
    {
        return $this->_rangeFilters;
    }

    /**
     * @return string
     */
    protected function _getLocaleCode()
    {
        if (!$this->_localeCode)
            $this->_localeCode = $this->_getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE);
        return $this->_localeCode;
    }

    /**
     * @return array
     */
    protected function _getStatsFields()
    {
        return $this->_statsFields;
    }
}
