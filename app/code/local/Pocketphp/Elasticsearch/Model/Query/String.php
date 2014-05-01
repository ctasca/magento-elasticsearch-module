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
 * Query String class.
 *
 * To be used as an inner query only, e.g. in bool query shoulds or musts
 *
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @subpackage Model
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
class Pocketphp_Elasticsearch_Model_Query_String extends Pocketphp_Elasticsearch_Model_Query_Dsl
{
    /**
     * @var bool
     */
    protected $_lowercaseExpandedFields;
    /**
     * Should the queries be combined using dis_max (set it to true), or a bool query (set it to false). Defaults to true
     * @var bool
     */
    protected $_useDisMax;

    /**
     * @param Mage_Core_Model_Store $store
     */
    public function __construct(Mage_Core_Model_Store $store)
    {
        parent::__construct($store);
    }

    /**
     * @return array
     */
    public function get()
    {
        $this->_query = array();

        if ($this->_getDefaultField())
            $this->_query['query_string']['default_field'] = $this->_getDefaultField();

        if ($this->_canAddFields())
            $this->_query['query_string']['fields'] = $this->_getFields();

        if ($this->_getQueryString()) {
            $this->_query['query_string']['query'] = $this->_getQueryString();
            $this->_query['query_string']['lowercase_expanded_terms'] = $this->getLowercaseExpandedFields();
            $this->_query['query_string']['use_dis_max'] = $this->getUseDisMax();
        }


        return $this->_query;
    }

    /**
     * Sets lowercase_expended_fields parameter either to true or false
     *
     * Refer to http://stackoverflow.com/questions/19220424/elasticsearch-wildcard-in-query-differences-alex-vs-lex
     *
     * @param boolean $lowercaseExpandedFields
     */
    public function setLowercaseExpandedFields($lowercaseExpandedFields)
    {
        $this->_lowercaseExpandedFields = $lowercaseExpandedFields;
    }

    /**
     * @return boolean
     */
    public function getLowercaseExpandedFields()
    {
        if (!is_bool($this->_lowercaseExpandedFields))
            $this->_lowercaseExpandedFields = Mage::getStoreConfig('elasticsearch/search/query_string_lef') > 0 ? true : false;

        return $this->_lowercaseExpandedFields;
    }

    /**
     * @param $useDisMax
     */
    public function setUseDisMax($useDisMax)
    {
        $this->_useDisMax = $useDisMax;
    }

    /**
     * @return boolean
     */
    public function getUseDisMax()
    {
        if (!is_bool($this->_useDisMax))
            $this->_useDisMax = Mage::getStoreConfig('elasticsearch/search/query_string_udm') > 0 ? true : false;

        return $this->_useDisMax;
    }
}