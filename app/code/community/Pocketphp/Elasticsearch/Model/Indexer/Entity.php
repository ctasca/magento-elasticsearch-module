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
class Pocketphp_Elasticsearch_Model_Indexer_Entity implements Pocketphp_Elasticsearch_Model_Interface_Indexer
{
    protected $_client;
    protected $_type;

    /**
     * @param Pocketphp_Elasticsearch_Model_Interface_Connectable $client
     */
    public function __construct(Pocketphp_Elasticsearch_Model_Interface_Connectable $client)
    {
        $this->_client = $client;
    }

    /**
     * @return Pocketphp_Elasticsearch_Model_Interface_Connectable
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @return Pocketphp_Elasticsearch_Model_Interface_Indexer_Type
     */
    public function getTypeIndexer()
    {
        return Mage::getSingleton('elasticsearch/indexer_entity_' . $this->getType());
    }

    /**
     * @return array
     */
    public function index()
    {
        $index = $this->getTypeIndexer()->getIndex();
        $this->indexDocuments($this->getTypeIndexer()->getDataProvider()->getStore(), $index);
    }

    /**
     * @return Pocketphp_Elasticsearch_Model_Cluster_Manager
     */
    protected function _getClusterManager()
    {
        return Mage::helper('elasticsearch')->getClusterManager();
    }

    /**
     * @return Mage_Core_Model_Resource_Website_Collection
     */
    protected function _getWebsites()
    {
        return Mage::getModel('core/website')->getCollection();
    }

    /**
     * @param       $storeId
     * @param array $documents
     * @return array
     */
    /*public function indexDocuments($storeId, array $documents)
    {
        $responses = array();
        foreach ($documents as $id => $document)
            $responses[] = $this->_getClusterManager()
                ->indexDocument($this->getTypeIndexer(), $storeId, $id, $document);

        return $responses;
    }*/
}
