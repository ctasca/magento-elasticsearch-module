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
 * @subpackage Helper
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
class Pocketphp_Elasticsearch_Helper_Data extends Mage_Core_Helper_Data
{

    /**
     * Retrieve advanced search URL
     *
     * @return string
     */
    public function getAdvancedSearchUrl()
    {
        return $this->_getUrl('elasticsearch/advanced');
    }

    /**
     * @return Pocketphp_Elasticsearch_Model_Cluster_Manager
     */
    public function getClusterManager()
    {
        return Mage::getModel("elasticsearch/cluster_manager");
    }

    /**
     * @return Pocketphp_Elasticsearch_Model_Resource_Engine
     */
    public function getEngine()
    {
        return Mage::getResourceSingleton('elasticsearch/engine');
    }

    /**
     * Notifies system a third party search engine is in use
     *
     * @return bool
     */
    public function isThirdPartSearchEngine()
    {
        $client = Mage::getModel('elasticsearch/client')->connect();

        return $client->ping();
    }

    /**
     * Notifies the system third party search engine is active
     *
     * @return bool
     */
    public function isActiveEngine()
    {
        $client = Mage::getModel('elasticsearch/client')->connect();

        return $client->ping();
    }

    /**
     *
     * Travers array and pull elements with keys that match the pattern and preg replace
     * keys.
     *
     * @param string $pattern
     * @param string $replace
     * @param array  $input
     * @param int    $flags
     * @return array
     */
    public function pregGrepReplaceArrayKeys($pattern, $replace, $input, $flags = 0)
    {
        $keys = preg_grep($pattern, array_keys($input), $flags);
        $vals = array();
        foreach ($keys as $key) {
            $keyReplace        = preg_replace($pattern, $replace, $key);
            $vals[$keyReplace] = $input[$key];
        }

        return $vals;
    }
}