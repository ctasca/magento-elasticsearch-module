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
class Pocketphp_Elasticsearch_Model_Query_Matchall extends Pocketphp_Elasticsearch_Model_Query_Dsl
{
    protected $_boost;

    public function __construct(Mage_Core_Model_Store $store)
    {
        parent::__construct($store);

    }

    /**
     * @param mixed $boost
     */
    public function setBoost($boost)
    {
        $this->_boost = $boost;
    }

    /**
     * Returns query DSL array for another query (e.g. filtered query)
     *
     * @return array
     */
    public function get()
    {
        $this->_query = array();
        $this->_query['match_all'] = array();

        if ($this->_getBoost() && is_numeric($this->_getBoost()))
            $this->_query['match_all']['boost'] = $this->_getBoost();

        return $this->_query;
    }

    /**
     * @return mixed
     */
    protected function _getBoost()
    {
        return $this->_boost;
    }
}
