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
 *
 *
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @subpackage Model
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
interface Pocketphp_Elasticsearch_Model_Interface_Engine
{
    public function test();

    public function getFieldsPrefix();

    public function getResultCollection();

    public function isLayeredNavigationAllowed();

    public function allowAdvancedIndex();

    public function getAllowedVisibility();

    public function getAdvancedResultCollection();

    public function getClusterManager();

    public function getIndexerEntity();

    public function getIndexerEntityType();

    public function getIndexerEntityProvider();

    public function getLanguageCodeByLocaleCode($localeCode);

    public function getSortableAttributeFieldName(Mage_Catalog_Model_Resource_Eav_Attribute $attribute);

    public function getLocaleCode();

    public function cleanIndex();

    public function getResourceName();

    public function getStats($query);

    public function saveEntityIndexes($storeId, array $indexes);

    public function addAdvancedIndex($index, $storeId);

    public function prepareEntityIndex($index);

    public function search($query);
}
