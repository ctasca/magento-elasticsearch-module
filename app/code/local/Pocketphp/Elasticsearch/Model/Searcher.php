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
class Pocketphp_Elasticsearch_Model_Searcher implements Pocketphp_Elasticsearch_Model_Interface_Searcher
{
    /**
     * @param Pocketphp_Elasticsearch_Model_Query_Abstract $query
     * @return array
     */
    public function search(Pocketphp_Elasticsearch_Model_Query_Abstract $query)
    {
        try{
            Varien_Profiler::start('ELASTICSEARCH_SEARCH');
            $client = Mage::getModel('elasticsearch/client')
                          ->connect();

            $result = $client->search($query->get());
            Varien_Profiler::stop('ELASTICSEARCH_SEARCH');
        }catch(Exception $e) {
            $result = array();
        }

        return $result;
    }
}
