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
 * Short description of the class
 *
 * Long description of the class (if any...)
 *
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @subpackage Model
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
class Pocketphp_Elasticsearch_Model_Indexer_Entity_Product implements Pocketphp_Elasticsearch_Model_Interface_Indexer_Type
{
    const INDEX_NAME = 'products';
    const INDEX_TYPE = 'product';
    /**
     * @var array
     */
    protected $_index = array();
    /**
     * @var Pocketphp_Elasticsearch_Model_Interface_Indexer_Type
     */
    protected $_dataProvider = null;

    public function __construct($args = array())
    {
        $this->_index = array();
    }

    /**
     * @return string
     */
    public function getIndexName()
    {
        return static::INDEX_NAME;
    }

    /**
     * @return string
     */
    public function getIndexType()
    {
        return static::INDEX_TYPE;
    }

    /**
     * @return array
     */
    public function getIndex()
    {
        return $this->_index;
    }

    /**
     * @param $key
     * @param $data
     */
    public function addToIndex($key, $data)
    {
        $indexData = array();
        foreach ($data as $attributeCode => $value) {
            if (is_array($value))
                $indexData[$attributeCode] = $value[$key];
            else
                $indexData[$attributeCode] = $value;
        }
        $this->_index[$key] = $indexData;
    }


    /**
     * @return Pocketphp_Elasticsearch_Model_Interface_Indexer_Type
     */
    public function getDataProvider()
    {
        if (!is_null($this->_dataProvider))
            return $this->_dataProvider;
        $this->_dataProvider = Mage::getModel('elasticsearch/indexer_provider_' . $this->getIndexType());

        return $this->_dataProvider;
    }

    /**
     * Creates index for given website and store
     *
     * @param int $storeId
     * @return string
     */
    public function createIndex($storeId)
    {
        return $this->_getClusterManager()
            ->createIndex($this->getIndexName() . "_{$storeId}");
    }

    /**
     * @return Pocketphp_Elasticsearch_Model_Cluster_Manager
     */
    protected function _getClusterManager()
    {
        return Mage::helper('elasticsearch')
            ->getClusterManager();
    }

}
