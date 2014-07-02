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
interface Pocketphp_Elasticsearch_Model_Interface_Indexer_Provider
{
    public function getData(Mage_Core_Model_Abstract $model);

    public function getMapping();

    public function getStore();

    public function setStore($storeId);

    public function getIndexName();

    public function setIndexName($name);

    public function putStoreMappings();

    public function getIndexableAttributes();

    public function getSupportedLanguages();

    public function isIndexableAttribute(Mage_Catalog_Model_Resource_Eav_Attribute $attribute);

    public function isAttributeWithOptions(Mage_Catalog_Model_Resource_Eav_Attribute $attribute);
}
