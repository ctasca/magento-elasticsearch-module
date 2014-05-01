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
class Pocketphp_Elasticsearch_Model_Resource_Index extends Mage_CatalogSearch_Model_Resource_Fulltext
{
    /**
     * Adds advanced index data.
     *
     * @param array $index
     * @param int   $storeId
     * @param array $productIds
     * @return mixed
     */
    public function addAdvancedIndex($index, $storeId, $productIds = null)
    {
        if (count($index) == 0)
            return;

        if (is_null($productIds) || !is_array($productIds)) {
            $productIds = array();
            foreach ($index as $productData) {
                $productIds[] = $productData['entity_id'];
            }
        }

        return $this->_addAdvancedIndexData($index, $storeId, $productIds);
    }

    /**
     * @param array $index
     * @param       $storeId
     * @param       $productIds
     * @return array
     */
    protected function _addAdvancedIndexData(array $index, $storeId, $productIds)
    {
        $prefix               = $this->_engine->getFieldsPrefix();
        $prefixedCategoryData = $this->_getCatalogCategoryData($storeId, $productIds);
        $prefixedPriceData    = $this->_getCatalogProductPriceData($productIds);

        $indexIterator = new ArrayIterator($index);
        iterator_apply($indexIterator, array($this, '_advancedIndexCallback'), array(
            $indexIterator, &$index, $prefix, $prefixedCategoryData, $prefixedPriceData
        ));

        return $index;
    }

    /**
     * @param ArrayIterator $iterator
     * @param               $index
     * @param               $prefix
     * @param               $prefixedCategoryData
     * @param               $prefixedPriceData
     */
    protected function _advancedIndexCallback(ArrayIterator $iterator, $index, $prefix, $prefixedCategoryData, $prefixedPriceData)
    {
        while ($iterator->valid()) {
            $productId   = $iterator->key();
            $productData = $iterator->current();
            if (isset($prefixedCategoryData[$productId]) && isset($prefixedPriceData[$productId])) {
                $categoryData = Mage::helper('elasticsearch')
                                    ->pregGrepReplaceArrayKeys('/#/', '', $prefixedCategoryData[$productId]);
                $priceData    = Mage::helper('elasticsearch')
                                    ->pregGrepReplaceArrayKeys('/#/', '', $prefixedPriceData[$productId]);
                $productData += $categoryData;
                $productData += $priceData;
            } else {
                $productData += array(
                    'categories'         => array(),
                    'show_in_categories' => array(),
                    'visibility'         => 0
                );
            }
            $index[$productId] = $productData;
            $iterator->next();
        }
    }

    /**
     * Retrieves category data for advanced index.
     *
     * @param int   $storeId
     * @param array $productIds
     * @param bool  $visibility
     * @return array
     */
    protected function _getCatalogCategoryData($storeId, $productIds, $visibility = true)
    {
        $adapter = $this->_getWriteAdapter();
        $prefix  = $this->_engine->getFieldsPrefix();
        $columns = array(
            'product_id' => 'product_id',
            'parents'    => new Zend_Db_Expr("GROUP_CONCAT(IF(is_parent = 1, category_id, '') SEPARATOR ' ')"),
            'anchors'    => new Zend_Db_Expr("GROUP_CONCAT(IF(is_parent = 0, category_id, '') SEPARATOR ' ')"),
            'positions'  => new Zend_Db_Expr("GROUP_CONCAT(CONCAT(category_id, '_', position) SEPARATOR ' ')"),
        );

        if ($visibility) {
            $columns['visibility'] = 'visibility';
        }

        $select = $adapter->select()
                          ->from(array($this->getTable('catalog/category_product_index')), $columns)
                          ->where('product_id IN (?)', $productIds)
                          ->where('store_id = ?', $storeId)
                          ->group('product_id');

        $result = array();
        foreach ($adapter->fetchAll($select) as $row) {
            $data = array(
                $prefix . 'categories'         => array_values(array_filter(explode(' ', $row['parents']))),
                // array_values to reorder keys
                $prefix . 'show_in_categories' => array_values(array_filter(explode(' ', $row['anchors']))),
                // array_values to reorder keys
            );
            foreach (explode(' ', $row['positions']) as $value) {
                list($categoryId, $position) = explode('_', $value);
                $key        = sprintf('%sposition_category_%d', $prefix, $categoryId);
                $data[$key] = $position;
            }
            if ($visibility) {
                $data[$prefix . 'visibility'] = $row['visibility'];
            }

            $result[$row['product_id']] = $data;
        }

        return $result;
    }

    /**
     * Retrieves product price data for advanced index.
     *
     * @param array $productIds
     * @return array
     */
    protected function _getCatalogProductPriceData($productIds = null)
    {
        $adapter = $this->_getWriteAdapter();
        $prefix  = $this->_engine->getFieldsPrefix();
        $select  = $adapter->select()
                           ->from($this->getTable('catalog/product_index_price'),
                array('entity_id', 'customer_group_id', 'website_id', 'min_price'));

        if ($productIds) {
            $select->where('entity_id IN (?)', $productIds);
        }

        $result = array();
        foreach ($adapter->fetchAll($select) as $row) {
            if (!isset($result[$row['entity_id']])) {
                $result[$row['entity_id']] = array();
            }
            $key                             = sprintf('%sprice_%s_%s', $prefix, $row['customer_group_id'], $row['website_id']);
            $result[$row['entity_id']][$key] = round($row['min_price'], 2);
        }

        return $result;
    }

}
