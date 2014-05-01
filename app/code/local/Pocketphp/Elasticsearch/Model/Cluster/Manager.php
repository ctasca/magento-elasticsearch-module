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
 * Elasticsearch Cluster Manager
 *
 * Provides access to cluster operations such as mapping, indexing, indexing documents, deleting
 * Class protocol provides wrappers for \Elasticsearch\Client PHP API
 *
 * Refer to http://www.elasticsearch.org/guide/en/elasticsearch/client/php-api/current/index.html
 * for more details
 *
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @subpackage Model
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
class Pocketphp_Elasticsearch_Model_Cluster_Manager implements Pocketphp_Elasticsearch_Model_Cluster_Api
{
    /**
     * @return \Elasticsearch\Client
     */
    public function getClient()
    {
        return Mage::getModel('elasticsearch/client')
                   ->connect();
    }

    /**
     * @return array
     */
    public function getClusterInfo()
    {
        $client   = $this->getClient();
        $response = $client->info();

        if (is_array($response))
            return $response;

        return array();
    }

    /**
     * @param Pocketphp_Elasticsearch_Model_Interface_Indexer_Type $indexer
     * @param                                                      $store
     * @return bool
     */
    public function indexExists(Pocketphp_Elasticsearch_Model_Interface_Indexer_Type $indexer, $store)
    {
        $params          = array();
        $params['index'] = $indexer->getIndexName() . "_{$store}";

        return $this->getClient()->indices()->exists($params);
    }

    /**
     * @param Pocketphp_Elasticsearch_Model_Interface_Indexer_Type $indexer
     * @param                                                      $store
     * @param                                                      $id
     * @return array
     */
    public function documentExists(Pocketphp_Elasticsearch_Model_Interface_Indexer_Type $indexer, $store, $id)
    {
        $params          = array();
        $params['index'] = $indexer->getIndexName() . "_{$store}";
        $params['type']  = $indexer->getIndexType();
        $params['id']    = $id;

        return $this->getClient()->exists($params);
    }

    /**
     * @param Pocketphp_Elasticsearch_Model_Interface_Indexer_Type $indexer
     * @param                                                      $store
     * @return array
     */
    public function clearCache(Pocketphp_Elasticsearch_Model_Interface_Indexer_Type $indexer, $store)
    {
        $indexList                = $indexer->getIndexName() . '_' . $store;

        $ccParams                 = array();
        $ccParams['index']        = $indexList;
        $ccParams['filter']       = true;
        $ccParams['filter_cache'] = true;
        $ccParams['filter_keys']  = true;
        $ccParams['id']           = true;
        $ccParams['id_cache']     = true;
        $ccParams['recycler']     = true;

        return $this->getClient()->indices()->clearCache($ccParams);
    }

    /**
     * @param Pocketphp_Elasticsearch_Model_Interface_Indexer_Type $indexer
     * @param                                                      $store
     * @param                                                      $id
     * @param array                                                $data
     * @return array
     */
    public function indexDocument(Pocketphp_Elasticsearch_Model_Interface_Indexer_Type $indexer, $store, $id, array $data)
    {
        $params = array();

        $params['index'] = $indexer->getIndexName() . "_{$store}";
        $params['type']  = $indexer->getIndexType();
        $params['id']    = $id;
        $params['body']  = $data;

        if ($this->documentExists($indexer, $store, $id)) {
            $params['body']        = array();
            $params['body']['doc'] = $data;

            return $this->getClient()->update($params);
        }

        return $this->getClient()->index($params);
    }

    /**
     *
     * Creates specified index with 50 shards and 0 replica
     * @todo put values in config
     *
     * @param $index
     * @return string
     */
    public function createIndex($index)
    {
        $indexParams['index']                                  = $index;
        $indexParams['body']['settings']['number_of_shards']   = Mage::getStoreConfig('elasticsearch/index/num_shards');
        $indexParams['body']['settings']['number_of_replicas'] = Mage::getStoreConfig('elasticsearch/index/num_replicas');

        $indexParams['body']['settings']['analysis']['analyzer'] = array(
            'whitespace'                => array(
                'tokenizer' => 'standard',
                'filter'    => array('lowercase'),
            ),
            'edge_ngram_front'          => array(
                'tokenizer' => 'standard',
                'filter'    => array('length', 'edge_ngram_front', 'lowercase'),
            ),
            'edge_ngram_back'           => array(
                'tokenizer' => 'standard',
                'filter'    => array('length', 'edge_ngram_back', 'lowercase'),
            ),
            'shingle'                   => array(
                'tokenizer' => 'standard',
                'filter'    => array('shingle', 'length', 'lowercase'),
            ),
            'shingle_strip_ws'          => array(
                'tokenizer' => 'standard',
                'filter'    => array('shingle', 'strip_whitespaces', 'length', 'lowercase'),
            ),
            'shingle_strip_apos_and_ws' => array(
                'tokenizer' => 'standard',
                'filter'    => array('shingle', 'strip_apostrophes', 'strip_whitespaces', 'length', 'lowercase'),
            ),
        );

        $indexParams['body']['settings']['analysis']['filter'] = array(
            'shingle'           => array(
                'type'             => 'shingle',
                'max_shingle_size' => 20,
                'output_unigrams'  => true,
            ),
            'strip_whitespaces' => array(
                'type'        => 'pattern_replace',
                'pattern'     => '\s',
                'replacement' => '',
            ),
            'strip_apostrophes' => array(
                'type'        => 'pattern_replace',
                'pattern'     => "'",
                'replacement' => '',
            ),
            'edge_ngram_front'  => array(
                'type'     => 'edgeNGram',
                'min_gram' => 3,
                'max_gram' => 10,
                'side'     => 'front',
            ),
            'edge_ngram_back'   => array(
                'type'     => 'edgeNGram',
                'min_gram' => 3,
                'max_gram' => 10,
                'side'     => 'back',
            ),
            'length'            => array(
                'type' => 'length',
                'min'  => 2,
            ),
        );

        return $this->getClient()
                    ->indices()
                    ->create($indexParams);
    }

    /**
     * Takes a mapping array
     * and makes PUT request to elasticsearch cluster.
     *
     * Returns response array
     *
     * @param array $mappings
     * @param       $index
     * @param       $type
     * @return array
     */
    public function putMapping(array $mappings, $index, $type)
    {
        $params['index'] = $index;
        $params['type']  = $type;
        $params['body']  = $mappings;

        return $this->getClient()
                    ->indices()
                    ->putMapping($params);
    }

    /**
     * Deletes index from cluster
     *
     * @param  $index
     * @return array
     */
    public function deleteIndex($index)
    {
        $client                = $this->getClient();
        $deleteParams['index'] = $index;

        return $client->indices()
                      ->delete($deleteParams);
    }

    /**
     * Performs delete operation for all stores indexes
     *
     * @param Pocketphp_Elasticsearch_Model_Interface_Indexer_Type $indexerType
     */
    public function deleteAllIndexes(Pocketphp_Elasticsearch_Model_Interface_Indexer_Type $indexerType)
    {
        $websites = Mage::getModel('core/website')->getCollection();

        foreach ($websites as $website) {
            $stores = $website->getStores();
            foreach ($stores as $store) {
                if ($this->indexExists($indexerType, $store->getId()))
                    $this->deleteIndex($indexerType->getIndexName() . '_' . $store->getId());
            }
        }
    }

    /**
     * Performs clear cache operation in all store indexes for given indexer type
     *
     * @param Pocketphp_Elasticsearch_Model_Interface_Indexer_Type $indexerType
     */
    public function clearAllCache(Pocketphp_Elasticsearch_Model_Interface_Indexer_Type $indexerType)
    {
        $websites = Mage::getModel('core/website')->getCollection();

        foreach ($websites as $website) {
            $stores = $website->getStores();
            foreach ($stores as $store) {
                if ($this->indexExists($indexerType, $store->getId()))
                    $this->clearCache($indexerType, $store->getId());
            }
        }
    }

}