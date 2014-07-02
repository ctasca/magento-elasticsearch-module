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
 * @subpackage Model
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
class Pocketphp_Elasticsearch_Model_Adminhtml_System_Config_Backend_Engine extends Mage_Core_Model_Config_Data
{
    /**
     * Invalidate catalog search index if engine was changed and
     * change status for catalog category products and catalog search index process.
     *
     * @return Pocketphp_Elasticsearch_Model_Adminhtml_System_Config_Backend_Engine
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        if ($this->isValueChanged()) {
            $indexer = Mage::getSingleton('index/indexer');
            $indexer->getProcessByCode('catalogsearch_fulltext')
                ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
            $indexer->getProcessByCode('catalog_category_product')
                ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }

        return $this;
    }
}
