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
class Pocketphp_Elasticsearch_Model_Query_Bool extends Pocketphp_Elasticsearch_Model_Query_Dsl
{
    /**
     * @var array
     */
    protected $_should = array();
    /**
     * @var array
     */
    protected $_must = array();

    /**
     * @param Mage_Core_Model_Store $store
     */
    public function __construct(Mage_Core_Model_Store $store)
    {
        parent::__construct($store);
    }

    /**
     * Returns bool query array for another query (e.g. filtered query)
     *
     * @return array
     */
    public function get()
    {
        $this->_query = array();

        if (count($this->_must) > 0)
            foreach ($this->_must as $must)
                $this->_query['bool']['must'][] = $must->get();

        if (count($this->_should) > 0)
            foreach ($this->_should as $should)
                $this->_query['bool']['should'][] = $should->get();

        return $this->_query;
    }

    /**
     * Adds should query
     *
     * @param Pocketphp_Elasticsearch_Model_Query_Abstract $should
     */
    public function addShould(Pocketphp_Elasticsearch_Model_Query_Abstract $should)
    {
        $this->_should[] = $should;
    }

    /**
     * Add must query
     *
     * @param Pocketphp_Elasticsearch_Model_Query_Abstract $must
     */
    public function addMust(Pocketphp_Elasticsearch_Model_Query_Abstract $must)
    {
        $this->_must[] = $must;
    }
}
