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
 *
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @subpackage Model
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
class Pocketphp_Elasticsearch_Model_Indexer_Provider_Product implements Pocketphp_Elasticsearch_Model_Cluster_Api, Pocketphp_Elasticsearch_Model_Interface_Indexer_Provider
{
    const VISIBILITY_NOT_VISIBLE = 1;
    const VISIBILITY_IN_CATALOG  = 2;
    const VISIBILITY_IN_SEARCH   = 3;
    const VISIBILITY_BOTH        = 4;

    /**
     * @var string
     */
    private $_indexName;
    /**
     * @var int
     */
    private $_store;

    /**
     * @var Varien_Data_Collection_Db
     */
    protected $_attributesCollection;

    /**
     * @var Varien_Data_Collection_Db
     */
    protected $_indexableAttributes;

    public function __construct()
    {
    }

    /**
     * @param int $store
     */
    public function setStore($store)
    {
        $this->_store = $store;
    }

    /**
     * @return int
     */
    public function getStore()
    {
        return $this->_store;
    }

    /**
     * @param string $indexName
     */
    public function setIndexName($indexName)
    {
        $this->_indexName = $indexName;
    }

    /**
     * @return string
     */
    public function getIndexName()
    {
        return $this->_indexName;
    }

    /**
     * Sets indexer metadata for product entities
     *
     * @param Mage_Core_Model_Abstract $product
     * @return array
     */
    public function getData(Mage_Core_Model_Abstract $product)
    {
        $data                        = array();
        $data['entity_id']           = $product->getEntityId();
        $data['type_id']             = $product->getTypeId();
        $data['store_id']            = $product->getStoreId();
        $data['store_ids']           = $product->getStoreIds();
        $data['name_suggest']        = $this->_prepareNameSuggestField($product);
        $data['name_suggest_static'] = $product->getNameSuggestInputIndexValue();

        return $data;
    }

    /**
     * @return array
     */
    public function getMapping()
    {
        return array(
            "product" => array(
                "properties" => array(
                    "entity_id"           => array(
                        "type" => Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_INT
                    ),
                    "type_id"             => array(
                        "type"  => Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_STRING,
                        "index" => Pocketphp_Elasticsearch_Model_Cluster_Api::FIELD_NOT_ANALYZED
                    ),
                    "store_id"            => array(
                        "type" => Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_INT
                    ),
                    "store_ids"           => array(
                        "type"  => Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_STRING,
                        "index" => Pocketphp_Elasticsearch_Model_Cluster_Api::FIELD_NOT_ANALYZED
                    ),
                    "status"              => array(
                        "type" => Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_INT
                    ),
                    "visibility"          => array(
                        "type" => Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_INT
                    ),
                    "in_stock"            => array(
                        "type" => Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_BOOLEAN
                    ),
                    "#categories"         => array(
                        "type"  => Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_STRING,
                        "index" => Pocketphp_Elasticsearch_Model_Cluster_Api::FIELD_NOT_ANALYZED
                    ),
                    "#show_in_categories" => array(
                        "type"  => Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_STRING,
                        "index" => Pocketphp_Elasticsearch_Model_Cluster_Api::FIELD_NOT_ANALYZED
                    ),
                    "categories"          => array(
                        "type"  => Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_STRING,
                        "index" => Pocketphp_Elasticsearch_Model_Cluster_Api::FIELD_NOT_ANALYZED
                    ),
                    "show_in_categories"  => array(
                        "type"  => Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_STRING,
                        "index" => Pocketphp_Elasticsearch_Model_Cluster_Api::FIELD_NOT_ANALYZED
                    ),
                    "price"               => array(
                        "type" => Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_DOUBLE
                    ),
                    "name_suggest"        => array(
                        "type"             => Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_COMPLETION,
                        "payloads"         => true,
                        "analyzer"         => 'standard',
                        "max_input_length" => 255
                    ),
                    "_options"            => array(
                        "type"     => Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_STRING,
                        "analyzer" => 'standard'
                    ),
                )
            )
        );
    }

    /**
     * @return array|Varien_Data_Collection_Db
     */
    public function getIndexableAttributes()
    {
        if (null === $this->_indexableAttributes) {
            $this->_indexableAttributes = array();
            $entityType                 = Mage::getSingleton('eav/config')
                                              ->getEntityType('catalog_product');
            $entity                     = $entityType->getEntity();

            /* @var $productAttributeCollection Mage_Catalog_Model_Resource_Product_Attribute_Collection */
            $attributes = $this->_getAttributeCollection();

            foreach ($attributes as $attribute) {
                /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
                $attribute->setEntity($entity);
                $this->_indexableAttributes['searchable'][$attribute->getAttributeCode()] = $attribute;
            }

            $this->_indexableAttributes['sortable'] = Mage::getSingleton('catalog/config')
                                                          ->getAttributesUsedForSortBy();

            if (array_key_exists('price', $this->_indexableAttributes['sortable'])) {
                unset($this->_indexableAttributes['sortable']['price']);
            }
        }

        return $this->_indexableAttributes;
    }

    /**
     * Put fields mapping for attributes collection
     *
     */
    public function putStoreMappings()
    {
        $analyzedFields  = $this->_getAnalyzedFieldsArray();
        $attributesArray = $this->getIndexableAttributes();

        Mage::helper('elasticsearch')->getClusterManager()
            ->putMapping($this->getMapping(), $this->getIndexName() . "_{$this->getStore()}", 'product');

        foreach ($attributesArray as $attribute) {
            foreach ($attribute as $code => $instance) {
                if (array_key_exists($code, $analyzedFields)) {
                    if (isset($attributesArray['sortable'][$code])) {
                        // multi-field type
                        $mapping = $this->_mapSortableAnalyzedAttribute($instance, $analyzedFields[$code]);
                    } else {
                        $mapping = $this->_mapAnalyzedAttribute($instance, $analyzedFields[$code]);
                    }
                    $this->_putFieldMapping($mapping);
                } else {
                    $mapping = $this->_mapIndexableAttribute($instance, static::FIELD_NOT_ANALYZED);
                    $this->_putFieldMapping($mapping);
                }
            }
        }
    }


    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return bool
     */
    public function isIndexableAttribute(Mage_Catalog_Model_Resource_Eav_Attribute $attribute)
    {
        if ($attribute->getBackendType() == 'varchar' && !$attribute->getBackendModel()) {
            return true;
        }

        if ($attribute->getBackendType() == 'int'
            && $attribute->getSourceModel() != 'eav/entity_attribute_source_boolean'
            && ($attribute->getIsSearchable() || $attribute->getIsFilterable() || $attribute->getIsFilterableInSearch())
        ) {
            return true;
        }

        if ($attribute->getIsSearchable() || $attribute->getIsFilterable() || $attribute->getIsFilterableInSearch()) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if given attribute has options
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return bool
     */
    public function isAttributeWithOptions(Mage_Catalog_Model_Resource_Eav_Attribute $attribute)
    {
        $model = Mage::getModel($attribute->getSourceModel());

        return $attribute->usesSource() && $attribute->getBackendType() == 'int' && $model instanceof Mage_Eav_Model_Entity_Attribute_Source_Table;
    }

    /**
     * Defines provider's supported languages.
     *
     * @return array
     */
    public function getSupportedLanguages()
    {
        return array(
            /**
             * SnowBall filter based
             */
            // Danish
            'da' => 'da_DK',
            // Dutch
            'nl' => 'nl_NL',
            // English
            'en' => array('en_AU', 'en_CA', 'en_NZ', 'en_GB', 'en_US'),
            // Finnish
            'fi' => 'fi_FI',
            // French
            'fr' => array('fr_CA', 'fr_FR'),
            // German
            'de' => array('de_DE', 'de_DE', 'de_AT'),
            // Hungarian
            'hu' => 'hu_HU',
            // Italian
            'it' => array('it_IT', 'it_CH'),
            // Norwegian
            'nb' => array('nb_NO', 'nn_NO'),
            // Portuguese
            'pt' => array('pt_BR', 'pt_PT'),
            // Romanian
            'ro' => 'ro_RO',
            // Russian
            'ru' => 'ru_RU',
            // Spanish
            'es' => array('es_AR', 'es_CL', 'es_CO', 'es_CR', 'es_ES', 'es_MX', 'es_PA', 'es_PE', 'es_VE'),
            // Swedish
            'sv' => 'sv_SE',
            // Turkish
            'tr' => 'tr_TR',

            /**
             * Lucene class based
             */
            // Czech
            'cs' => 'cs_CZ',
            // Greek
            'el' => 'el_GR',
            // Thai
            'th' => 'th_TH',
            // Chinese
            'zh' => array('zh_CN', 'zh_HK', 'zh_TW'),
            // Japanese
            'ja' => 'ja_JP',
            // Korean
            'ko' => 'ko_KR'
        );
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param                                           $productAttributeValue
     * @return float|int|string
     */
    protected function _castAttributeValueType(Mage_Catalog_Model_Resource_Eav_Attribute $attribute, $productAttributeValue)
    {
        if ($attribute->getBackendType() == 'decimal') {
            return (double) $productAttributeValue;
        } elseif ($attribute->getBackendType() == 'int') {
            return (int) $productAttributeValue;
        }

        return $productAttributeValue;
    }

    /**
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @return float|int|string
     */
    protected function _mapAttributeType(Mage_Catalog_Model_Resource_Eav_Attribute $attribute)
    {
        if ($attribute->getBackendType() == 'int')
            return self::MAPPING_TYPE_INT;

        if ($attribute->getBackendType() == 'decimal')
            return self::MAPPING_TYPE_DOUBLE;

        if ($attribute->getBackendType() == 'varchar' || $attribute->getBackendType() == 'text' || $attribute->getBackendType() == 'static')
            return self::MAPPING_TYPE_STRING;

        if ($attribute->getBackendType() == 'datetime')
            return self::MAPPING_TYPE_DATE;

        return self::MAPPING_TYPE_UNKNOWN;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _prepareNameSuggestField(Mage_Catalog_Model_Product $product)
    {
        $filter = Mage::getModel('elasticsearch/text_filter', $product->getName());
        $weight = is_numeric($product->getNameSuggestWeight()) ? $product->getNameSuggestWeight() : 1;
        $input  = array();

        if ($product->getNameSuggestInput())
            $input = explode(',', trim($product->getNameSuggestInput()));

        if (!$product->getRemoveCatnamesSuggestions())
            $input = array_merge($input, $this->_getProductCategoryNames($product));

        if (!$product->getRemoveNameSuggestions()) {
            $input[] = $product->getName();
            $input   = array_merge($input, explode("-", $filter->filter()));
        }

        if (!$product->getRemoveSdSuggestions() && strlen($product->getShortDescription()))
            $input[] = $product->getShortDescription();

        $product->setNameSuggestInputIndexValue($input);

        return $this->_getNameSuggestFieldArray($product, $input, $weight);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param array                      $input
     * @param                            $weight
     * @return array
     */
    protected function _getNameSuggestFieldArray(Mage_Catalog_Model_Product $product, array $input, $weight)
    {
        return array(
            "input"   => $input,
            "output"  => $product->getName(),
            "weight"  => (int) $weight,
            "payload" => array(
                "id"   => $product->getEntityId(),
                "path" => $this->_getProductUrlPath($product)
            )
        );
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    protected function _getProductCategoryNames(Mage_Catalog_Model_Product $product)
    {
        $names       = array();
        $categoryIds = $product->getCategoryIds();

        foreach ($categoryIds as $categoryId) {
            $category     = Mage::getModel('catalog/category')
                                ->load($categoryId);
            $filter       = Mage::getModel('elasticsearch/text_filter', $category->getName());
            $filteredName = explode('-', $filter->filter());
            foreach ($filteredName as $namePart) {
                $names[] = $namePart;
            }
        }

        return $names;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    protected function _getProductUrlPath(Mage_Catalog_Model_Product $product)
    {
        return $product->getUrlPath();
    }

    /**
     * @param Mage_Core_Model_Abstract $product
     * @return array
     */
    protected function _getCategoryIdsArray(Mage_Core_Model_Abstract $product)
    {
        $data = array();
        foreach ($product->getCategoryIds() as $categoryId) {
            $category = Mage::getModel('catalog/category')
                            ->load((int) $categoryId);
            $path     = $category->getPath();
            $idsa     = explode('/', $path);
            foreach ($idsa as $id) {
                $data[] = $id;
            }
        }

        return $data;
    }

    /**
     * Retrieve collection of attributes with visible and add-to-index filter
     *
     * @return array
     */
    protected function _getAttributeCollection()
    {
        $entityType                 = Mage::getSingleton('eav/config')
                                          ->getEntityType('catalog_product');
        $productAttributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
                                          ->setEntityTypeFilter($entityType->getEntityTypeId())
                                          ->addVisibleFilter()
                                          ->addToIndexFilter(true);

        return $productAttributeCollection->getItems();
    }


    /**
     * Maps indexable entity's attribute type and index
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param                                           $index
     * @return array
     */
    protected function _mapIndexableAttribute(Mage_Catalog_Model_Resource_Eav_Attribute $attribute, $index)
    {
        $type = $this->_mapAttributeType($attribute);

        return array(
            "product" => array(
                "properties" => array(
                    $attribute->getAttributeCode() => array(
                        "type"  => $type,
                        "index" => $index,
                    )
                )
            )
        );
    }

    /**
     * Maps indexable entity's attribute type and index
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param                                           $analyzed
     * @return array
     */
    protected function _mapAnalyzedAttribute(Mage_Catalog_Model_Resource_Eav_Attribute $attribute, $analyzed)
    {
        $type = $this->_mapAttributeType($attribute);

        return array(
            "product" => array(
                "properties" => array(
                    $attribute->getAttributeCode() => array(
                        "type"     => $type,
                        "analyzer" => $analyzed
                    )
                )
            )
        );
    }

    /**
     * Maps indexable entity's attribute type and index
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param                                           $analyzed
     * @return array
     */
    protected function _mapSortableAnalyzedAttribute(Mage_Catalog_Model_Resource_Eav_Attribute $attribute, $analyzed)
    {
        $type = $this->_mapAttributeType($attribute);

        return array(
            "product" => array(
                "properties" => array(
                    $attribute->getAttributeCode() => array(
                        "type"   => "multi_field",
                        "fields" => array(
                            $attribute->getAttributeCode()           => array(
                                "type"     => $type,
                                "analyzer" => $analyzed
                            ),
                            'sort_' . $attribute->getAttributeCode() => array(
                                "type"  => $type,
                                "index" => static::FIELD_NOT_ANALYZED
                            )
                        )
                    )
                )
            )
        );
    }

    /**
     *
     * Puts field mapping to elasticsearch index
     *
     * @param array $mapping
     */
    protected function _putFieldMapping(array $mapping)
    {
        Mage::helper('elasticsearch')
            ->getClusterManager()
            ->putMapping($mapping, $this->getIndexName() . "_{$this->getStore()}", "product");
    }

    /**
     * Returns array built from value set in admin panel for Analyzed Fields CSV
     *
     * @return array
     */
    protected function _getAnalyzedFieldsArray()
    {
        $csv            = Mage::getStoreConfig('elasticsearch/mapping/analyzed_fields', $this->getStore());
        $fields         = explode(',', $csv);
        $analyzedFields = array();
        foreach ($fields as $field) {
            $explodedField = explode(':', $field);
            // count for possible duplicates
            $analyzedFields[$explodedField[0]] = $explodedField[1];
        }

        return $analyzedFields;
    }
}
