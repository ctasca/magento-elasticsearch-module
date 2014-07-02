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
 * Events observer class
 *
 * Listens for following Magento dispatched events:
 *
 *  - catalogsearch_index_process_start
 *  - catalog_product_delete_after_done
 *
 *
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @subpackage Model
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
class Pocketphp_Elasticsearch_Model_Event_Observer
{

    /**
     * Deletes store index before indexing process starts
     *
     * Listening to event catalogsearch_index_process_start
     *
     * @param Varien_Event_Observer $observer
     */
    public function clearAllIndexes(Varien_Event_Observer $observer)
    {
        $storeId    = $observer->getEvent()->getStoreId();
        $productIds = $observer->getEvent()->getProductIds();
        if (null === $storeId && null === $productIds) {
            $elasticHelper = Mage::helper('elasticsearch');
            $engine        = Mage::helper('catalogsearch')->getEngine();
            if ($engine instanceof Pocketphp_Elasticsearch_Model_Resource_Engine && $elasticHelper->isActiveEngine()) {
                try {
                    $manager     = Mage::getModel('elasticsearch/cluster_manager');
                    $indexerType = Mage::getModel('elasticsearch/indexer_entity_product');
                    $manager->clearAllCache($indexerType);
                    $manager->deleteAllIndexes($indexerType);
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
    }

    /**
     * Sets reindex status to 'required' when an indexable attribute has changed data
     *
     * @param Varien_Event_Observer $observer
     */
    public function setProductDeletedRequiredReindexStatus(Varien_Event_Observer $observer)
    {
        if (Mage::helper('elasticsearch')->isActiveEngine()) {
            Mage::getSingleton('index/indexer')->getProcessByCode('catalogsearch_fulltext')
                ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }
    }
}
