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
class Pocketphp_Elasticsearch_Model_Catalog_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Category
{
    /**
     * Retrieve layer object
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        $layer = $this->_getData('layer');
        if (is_null($layer)) {
            $layer = Mage::getSingleton('elasticsearch/catalog_layer');
            $this->setData('layer', $layer);
        }

        return $layer;
    }

    /**
     * Adds category filter to product collection.
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Pocketphp_Elasticsearch_Model_Catalog_Layer_Filter_Category
     */
    public function addCategoryFilter($category)
    {
        $value = array(
            'categories' => $category->getId()
        );
        $this->getLayer()
             ->getProductCollection()
             ->addFqFilter($value);

        return $this;
    }

    /**
     * Add params to faceted search
     *
     * @return Pocketphp_Elasticsearch_Model_Catalog_Layer_Filter_Category
     */
    public function addFacetCondition()
    {
        $category           = $this->getCategory();
        $childrenCategories = $category->getChildrenCategories();

        $useFlat    = (bool) Mage::getStoreConfig('catalog/frontend/flat_catalog_category');
        $categories = ($useFlat)
            ? array_keys($childrenCategories)
            : array_keys($childrenCategories->toArray());

        $this->getLayer()
             ->getProductCollection()
             ->addFacetCondition('categories', $categories);

        return $this;
    }

    /**
     * Retrieves request parameter and applies it to product collection.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Mage_Core_Block_Abstract         $filterBlock
     * @return Pocketphp_Elasticsearch_Model_Catalog_Layer_Filter_Category
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        if (!Mage::helper('elasticsearch')->isActiveEngine())
            return parent::apply($request, $filterBlock);

        $filter = (int) $request->getParam($this->getRequestVar());

        if ($filter) {
            $this->_categoryId = $filter;
        }

        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getCategory();
        if (!Mage::registry('current_category_filter')) {
            Mage::register('current_category_filter', $category);
        }

        if (!$filter) {
            $this->addCategoryFilter($category, null);

            return $this;
        }

        $this->_appliedCategory = Mage::getModel('catalog/category')
                                      ->setStoreId(Mage::app()
                                                       ->getStore()
                                                       ->getId())
                                      ->load($filter);

        if ($this->_isValidCategory($this->_appliedCategory)) {
            $this->getLayer()
                 ->getProductCollection()
                 ->addCategoryFilter($this->_appliedCategory);
            $this->addCategoryFilter($this->_appliedCategory);
            $this->getLayer()->getState()->addFilter(
                 $this->_createItem($this->_appliedCategory->getName(), $filter));
        }

        return $this;
    }

    /**
     * Retrieves current items data.
     *
     * @return array
     */
    protected function _getItemsData()
    {
        if (!Mage::helper('elasticsearch')->isActiveEngine())
            return parent::_getItemsData();

        $key  = $this->getLayer()->getStateKey() . '_SUBCATEGORIES';
        $data = $this->getLayer()->getCacheData($key);

        if ($data === null) {
            /** @var $category Mage_Catalog_Model_Categeory */
            $category   = $this->getCategory();
            $categories = $category->getChildrenCategories();

            $productCollection = $this->getLayer()->getProductCollection();
            $facets            = $productCollection->getFacetedData('categories');
            $facetsTerms       = array_key_exists('terms', $facets) ? $facets['terms'] : array();
            $data              = array();
            foreach ($categories as $category) {
                $categoryId = $category->getId();

                foreach ($facetsTerms as $facetTerm) {
                    $term  = $facetTerm['term'];
                    $count = $facetTerm['count'];

                    if ($term == $categoryId) {
                        $category->setProductCount($count);
                    } else {
                        $category->setProductCount(0);
                    }
                    if ($category->getIsActive() && $category->getProductCount()) {
                        $data[] = array(
                            'label' => Mage::helper('core')->escapeHtml($category->getName()),
                            'value' => $categoryId,
                            'count' => $category->getProductCount(),
                        );
                    }
                }

            }
            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }

        return $data;
    }
}
