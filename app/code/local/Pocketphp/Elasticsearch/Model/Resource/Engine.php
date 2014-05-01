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
 * Elasticsearch engine resource class
 *
 * Provides indexing and engine related operations on elsticsearch cluster
 *
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @subpackage Model
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
class Pocketphp_Elasticsearch_Model_Resource_Engine implements Pocketphp_Elasticsearch_Model_Interface_Engine
{
    /**
     * @var Pocketphp_Elasticsearch_Model_Cluster_Manager
     */
    protected $_clusterManager;

    /**
     * @var Pocketphp_Elasticsearch_Model_Indexer_Entity
     */
    protected $_indexerEntity;

    /**
     * @var Pocketphp_Elasticsearch_Model_Indexer_Entity_Product
     */
    protected $_indexerEntityType;

    /**
     * @var Pocketphp_Elasticsearch_Model_Indexer_Entity_Product
     */
    protected $_indexerEntityProvider;

    /**
     * @var array
     */
    protected $_dateFormats = array();

    /**
     * Advanced index fields prefix
     *
     * @var string
     */
    protected $_advancedIndexFieldsPrefix = '#';

    /**
     * List of static fields for index
     *
     *
     * @var array
     */
    protected $_advancedStaticIndexFields = array('#visibility');

    /**
     * List of obligatory dynamic fields for index
     *
     * @var array
     */
    protected $_advancedDynamicIndexFields = array(
        '#position_category_',
        '#price_'
    );
    /**
     * @var array
     */
    protected $_deleteIndeces = array();

    /**
     * Unlocalized field types.
     *
     * @var array
     */
    protected $_nonLocaleTypes = array(
        'datetime',
        'decimal',
    );
    /**
     * Array of languages codes for indexed stores
     *
     * @var array
     */
    protected $_languageCodes = array();

    /**
     * Current indexed document collected data
     *
     * @var array
     */
    protected $_currentIndexedDocument = array();

    public function __construct()
    {
        $this->_clusterManager = Mage::helper('elasticsearch')
                                     ->getClusterManager();
        $this->_indexerEntity  = Mage::getModel('elasticsearch/indexer_entity', Mage::getModel('elasticsearch/client'));
        $this->_indexerEntity->setType('product');
        $this->_indexerEntityType     = $this->_indexerEntity->getTypeIndexer();
        $this->_indexerEntityProvider = $this->_indexerEntityType->getDataProvider();
    }


    /**
     * Define if engine is active
     *
     * @return bool
     */
    public function test()
    {
        return Mage::helper('elasticsearch')->isActiveEngine();
    }

    /**
     * Define if Layered Navigation is allowed
     *
     * @return bool
     */
    public function isLayeredNavigationAllowed()
    {
        return true;
    }

    /**
     * Alias of isLayeredNavigationAllowed
     *
     * @return bool
     */
    public function isLeyeredNavigationAllowed()
    {
        return true;
    }

    /**
     * Set search resource model
     *
     * @return string
     */
    public function getResourceName()
    {
        return 'elasticsearch/advanced';
    }

    /**
     * Checks if advanced index is allowed for current search engine.
     *
     * @return bool
     */
    public function allowAdvancedIndex()
    {
        return true;
    }

    /**
     * @return \Pocketphp_Elasticsearch_Model_Cluster_Manager
     */
    public function getClusterManager()
    {
        return $this->_clusterManager;
    }

    /**
     * @return \Pocketphp_Elasticsearch_Model_Indexer_Entity
     */
    public function getIndexerEntity()
    {
        return $this->_indexerEntity;
    }

    /**
     * @return \Pocketphp_Elasticsearch_Model_Indexer_Entity_Product
     */
    public function getIndexerEntityType()
    {
        return $this->_indexerEntityType;
    }

    /**
     * @return \Pocketphp_Elasticsearch_Model_Indexer_Entity_Product
     */
    public function getIndexerEntityProvider()
    {
        return $this->_indexerEntityProvider;
    }

    /**
     * Returns store locale code.
     *
     * @param null $store
     * @return string
     */
    public function getLocaleCode($store = null)
    {
        return Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $store);
    }

    /**
     * Returns advanced index fields prefix
     *
     * @return string
     */
    public function getFieldsPrefix()
    {
        return $this->_advancedIndexFieldsPrefix;
    }

    /**
     * Prepares index array
     *
     * @param        $index
     * @param string $separator
     * @return array
     */
    public function prepareEntityIndex($index, $separator = null)
    {
        return $index;
    }

    /**
     * Retrieves stats for specified query.
     *
     * @param $query
     * @return array
     */
    public function getStats($query)
    {
        return $this->search($query);
    }

    /**
     * Retrieve allowed visibility values for current engine
     *
     * @see
     *
     * @return array
     */
    public function getAllowedVisibility()
    {
        return Mage::getSingleton('catalog/product_visibility')
                   ->getVisibleInSiteIds();
    }

    /**
     * Returns advanced search results.
     *
     * @return Pocketphp_Elasticsearch_Model_Resource_Collection
     */
    public function getAdvancedResultCollection()
    {
        return $this->getResultCollection();
    }

    /**
     * @return Pocketphp_Elasticsearch_Model_Resource_Collection
     */
    public function getResultCollection()
    {
        return Mage::getResourceModel('elasticsearch/collection')
                   ->setEngine($this);
    }

    /**
     * Prepare advanced index for products
     *
     * @param array $index
     * @param int   $storeId
     * @param array $productIds
     * @return array
     */
    public function addAdvancedIndex($index, $storeId, $productIds = null)
    {
        return Mage::getResourceSingleton('elasticsearch/index')
                   ->addAdvancedIndex($index, $storeId, $productIds);
    }

    /**
     *
     * Cleans cache when index exists, otherwise does nothing else.
     *
     * For re-creating index and mapping see saveEntityIndexes method
     *
     * @param null $storeId
     */
    public function cleanIndex($storeId = null)
    {
        if (!$storeId && !is_numeric($storeId))
            return;

        if ($this->getClusterManager()->indexExists($this->getIndexerEntityType(), $storeId))
            $this->getClusterManager()->clearCache($this->getIndexerEntityType(), $storeId);

    }

    /**
     * @param       $storeId
     * @param array $indexes
     * @return Pocketphp_Elasticsearch_Model_Resource_Engine
     */
    public function saveEntityIndexes($storeId, array $indexes)
    {
        if (count($indexes) == 0)
            return $this;

        $provider = $this->getIndexerEntityProvider();
        $provider->setIndexName($this->getIndexerEntityType()
                                     ->getIndexName());
        $provider->setStore($storeId);
        $indexes = $this->addAdvancedIndex($indexes, $storeId, array_keys($indexes));

        if (!$this->getClusterManager()->indexExists($this->getIndexerEntityType(), $storeId)) {
            $this->_makeCreateIndexRequest($storeId);
            $provider->putStoreMappings();
        }
        $indexesIterator = new ArrayIterator($indexes);
        iterator_apply($indexesIterator, array($this, '_prepareIndexesCallback'), array($indexesIterator));

        return $this;
    }

    /**
     * @param array|string $query
     * @return array
     */
    public function search($query)
    {
        $searcher = Mage::getModel('elasticsearch/searcher');

        return $searcher->search($query);
    }

    /**
     * Returns attribute field name.
     *
     * For locale field name use getLocaleAttributeFieldName instead.
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return string
     */
    public function getAttributeFieldName(Mage_Catalog_Model_Resource_Eav_Attribute $attribute)
    {
        if (!($attribute instanceof Mage_Catalog_Model_Resource_Eav_Attribute))
            return '';

        return $attribute->getAttributeCode();
    }

    /**
     * Returns sortable attribute field name.
     *
     * For locale field name use getLocaleAttributeFieldName instead.
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return string
     */
    public function getSortableAttributeFieldName(Mage_Catalog_Model_Resource_Eav_Attribute $attribute)
    {
        return 'sort_' . $this->getAttributeFieldName($attribute);
    }

    /**
     * Returns attribute field name (localized if needed).
     *
     * @depreated not in use
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param string                                    $locale
     * @return string
     */
    public function getLocaleAttributeFieldName(Mage_Catalog_Model_Resource_Eav_Attribute $attribute, $locale = null)
    {
        if (!($attribute instanceof Mage_Catalog_Model_Resource_Eav_Attribute))
            return '';

        $attributeCode = $attribute->getAttributeCode();

        if ($attributeCode != 'score' && !in_array($attribute->getBackendType(), $this->_nonLocaleTypes)) {
            if (null === $locale) {
                $locale = $this->getLocaleCode();
            }
            $languageCode   = $this->getLanguageCodeByLocaleCode($locale);
            $languageSuffix = $languageCode ? '_' . $languageCode : '';
            $attributeCode .= $languageSuffix;
        }

        return $attributeCode;
    }


    /**
     * Returns language code of specified locale code.
     *
     * @deprecated not in use
     *
     * @param string $localeCode
     * @return bool
     */
    public function getLanguageCodeByLocaleCode($localeCode)
    {
        $localeCode = (string) $localeCode;
        if (!$localeCode)
            return false;


        if (!isset($this->_languageCodes[$localeCode])) {
            $languages                         = $this->getIndexerEntityProvider()
                                                      ->getSupportedLanguages();
            $this->_languageCodes[$localeCode] = false;
            foreach ($languages as $code => $locales) {
                if (is_array($locales)) {
                    if (in_array($localeCode, $locales)) {
                        $this->_languageCodes[$localeCode] = $code;
                    }
                } elseif ($localeCode == $locales) {
                    $this->_languageCodes[$localeCode] = $code;
                }
            }
        }

        return $this->_languageCodes[$localeCode];
    }

    /**
     * @param ArrayIterator $iterator
     */
    protected function _prepareIndexesCallback(ArrayIterator $iterator)
    {
        while ($iterator->valid()) {
            $index         = $iterator->current();
            $indexIterator = new ArrayIterator($index);
            iterator_apply($indexIterator, array($this, '_prepareIndexDocumentCallback'), array($indexIterator));
            $indexedEntity = $this->_addAdditonalIndexParameters($index['sku']);
            $this->getClusterManager()->indexDocument($this->getIndexerEntityType(), $this->getIndexerEntityProvider()
                                                                                          ->getStore(), $indexedEntity->getId(), $this->_currentIndexedDocument);
            $iterator->next();
        }

    }

    /**
     * @param ArrayIterator $iterator
     */
    protected function _prepareIndexDocumentCallback(ArrayIterator $iterator)
    {
        $this->_currentIndexedDocument = $iterator->getArrayCopy();
        $indexableAttributes           = $this->getIndexerEntityProvider()
                                              ->getIndexableAttributes();
        $storeId                       = $this->getIndexerEntityProvider()->getStore();

        while ($iterator->valid()) {
            $attributeCode = $iterator->key();
            $value         = $iterator->current();

            if (is_array($value)) {
                $value = array_values(array_filter(array_unique($value)));
            }
            if (array_key_exists($attributeCode, $indexableAttributes['searchable'])) {
                $attributeInstance = $indexableAttributes['searchable'][$attributeCode];
                if ($attributeInstance->usesSource() && !empty($value)) {
                    if ($attributeInstance->getFrontendInput() == 'multiselect') {
                        $value = explode(',', is_array($value) ? $value[0] : $value);
                    }
                    if ($this->getIndexerEntityProvider()
                             ->isAttributeWithOptions($attributeInstance)
                    ) {
                        $val = is_array($value) ? $value[0] : $value;
                        if (!isset($this->_currentIndexedDocument['_options'])) {
                            $this->_currentIndexedDocument['_options'] = array();
                        }
                        $option                                      = $attributeInstance->setStoreId($storeId)
                                                                                         ->getFrontend()
                                                                                         ->getOption($val);
                        $this->_currentIndexedDocument['_options'][] = $option;
                    }
                }
                if ($attributeInstance->getBackendType() == 'datetime') {
                    $value = $this->_addStoreDateFormat($storeId, $value[0]);
                }
                $this->_currentIndexedDocument[$attributeCode] = $this->_castAttributeValueType($attributeInstance, $value);
            } elseif (array_key_exists($attributeCode, $indexableAttributes['sortable'])) {
                $sortableVal       = is_array($value) ? $value[0] : $value;
                $attributeInstance = $indexableAttributes['sortable'][$attributeCode];
                $sortable          = $this->getSortableAttributeFieldName($attributeInstance);

                $attributeInstance->setStoreId($storeId);

                if ($attributeInstance->usesSource()) {
                    $sortableVal = $attributeInstance->getFrontend()
                                                     ->getOption($sortableVal);
                } elseif ($attributeInstance->getBackendType() == 'decimal') {
                    $sortableVal = (double) $sortableVal;
                }
                $this->_currentIndexedDocument[$sortable]      = $sortableVal;
                $this->_currentIndexedDocument[$attributeCode] = $this->_castAttributeValueType($attributeInstance, $value);
            } else {
                $this->_currentIndexedDocument[$attributeCode] = $value;
            }

            $iterator->next();
        }
    }

    /**
     * Transforms specified date to basic YYYY-MM-dd format.
     *
     * @param int    $storeId
     * @param string $date
     * @return null|string
     */
    protected function _addStoreDateFormat($storeId, $date = null)
    {
        if (!isset($this->_dateFormats[$storeId])) {
            $timezone = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE, $storeId);
            $locale   = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeId);
            $locale   = new Zend_Locale($locale);

            $dateObj = new Zend_Date(null, null, $locale);
            $dateObj->setTimezone($timezone);
            $this->_dateFormats[$storeId] = array($dateObj, $locale->getTranslation(null, 'date', $locale));
        }

        if (is_empty_date($date)) {
            return null;
        }

        list($dateObj, $localeDateFormat) = $this->_dateFormats[$storeId];
        $dateObj->setDate($date, $localeDateFormat);

        return $dateObj->toString('YYYY-MM-dd');
    }

    /**
     *
     * @param string $sku
     * @return bool|Mage_Catalog_Model_Product
     */
    protected function _loadProductBySku($sku)
    {
        return Mage::getModel('catalog/product')
                   ->loadByAttribute('sku', $sku);
    }

    /**
     * @param $storeId
     */
    protected function _makeCreateIndexRequest($storeId)
    {
        try {
            $this->getIndexerEntityType()
                 ->createIndex($storeId);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param                                           $productAttributeValue
     * @return float|int|string
     */
    protected function _castAttributeValueType(Mage_Catalog_Model_Resource_Eav_Attribute $attribute, $productAttributeValue)
    {
        $productAttributeValue = is_array($productAttributeValue) && array_key_exists(0, $productAttributeValue) ? $productAttributeValue[0] : $productAttributeValue;
        if ($attribute->getBackendType() == 'decimal') {
            return (double) $productAttributeValue;
        } elseif ($attribute->getBackendType() == 'int') {
            return (int) $productAttributeValue;
        }

        return $productAttributeValue;
    }

    /**
     * @param $sku
     * @return bool|Mage_Catalog_Model_Product
     */
    protected function _addAdditonalIndexParameters($sku)
    {
        $indexedEntity = $this->_loadProductBySku($sku);
        $indexedEntity->setStoreId($this->getIndexerEntityProvider()->getStore());
        $additionalIndexerData = $this->getIndexerEntityProvider()
                                      ->getData($indexedEntity);

        foreach ($additionalIndexerData as $code => $additionalData) {
            $this->_currentIndexedDocument[$code] = $additionalData;
        }

        return $indexedEntity;
    }

    /**
     * @param $storeId
     * @return mixed
     */
    protected function _getStoreLocaleCode($storeId)
    {
        $store     = Mage::app()
                         ->getStore($storeId);
        $localCode = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $store);

        return $localCode;
    }
}