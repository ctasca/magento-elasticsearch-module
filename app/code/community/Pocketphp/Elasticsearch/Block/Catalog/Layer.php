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
 * @subpackage Block
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
class Pocketphp_Elasticsearch_Block_Catalog_Layer extends Mage_Catalog_Block_Layer_View
{

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        Mage::register('current_layer', $this->getLayer(), true);
    }

    /**
     * Get layer object
     *
     * @return Pocketphp_Elasticsearch_Model_Layer
     */
    public function getLayer()
    {
        return Mage::getSingleton('elasticsearch/catalog_layer');
    }

    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock()
    {
        $helper = Mage::helper('elasticsearch');
        if ($helper->isThirdPartSearchEngine() && $helper->isActiveEngine()) {
            return ($this->canShowOptions() || count($this->getLayer()
                                                          ->getState()
                                                          ->getFilters()));
        }

        return parent::canShowBlock();
    }

    /**
     * Initialize blocks names
     */
    protected function _initBlocks()
    {
        parent::_initBlocks();

        if (Mage::helper('elasticsearch')->isActiveEngine()) {
            $this->_categoryBlockName        = 'elasticsearch/catalog_layer_filter_category';
            $this->_attributeFilterBlockName = 'elasticsearch/catalog_layer_filter_attribute';
            $this->_priceFilterBlockName     = 'elasticsearch/catalog_layer_filter_price';
            $this->_decimalFilterBlockName   = 'elasticsearch/catalog_layer_filter_decimal';
        }
    }

    /**
     * Prepare child blocks
     *
     * @return Mage_Catalog_Block_Layer_View
     */
    protected function _prepareLayout()
    {
        if (!$this->helper('elasticsearch')->isActiveEngine())
            return parent::_prepareLayout();

        $stateBlock = $this->getLayout()
                           ->createBlock($this->_stateBlockName)
                           ->setLayer($this->getLayer());

        $categoryBlock = $this->getLayout()
                              ->createBlock($this->_categoryBlockName)
                              ->setLayer($this->getLayer())
                              ->init();


        $filterableAttributes = $this->_getFilterableAttributes();
        $filters              = $this->_getFiltersArray($filterableAttributes);

        $this->setChild('layer_state', $stateBlock);
        $this->setChild('category_filter', $categoryBlock->addFacetCondition());

        $this->_addFacetConditionToFilters($filters);

        $this->getLayer()
             ->apply();

        return $this;
    }

    /**
     * @param $filterableAttributes
     * @return array
     */
    protected function _getFiltersArray($filterableAttributes)
    {
        $filters = array();
        foreach ($filterableAttributes as $attribute) {
            if ($attribute->getAttributeCode() == 'price') {
                $filterBlockName = $this->_priceFilterBlockName;
            } elseif ($attribute->getBackendType() == 'decimal') {
                $filterBlockName = $this->_decimalFilterBlockName;
            } else {
                $filterBlockName = $this->_attributeFilterBlockName;
            }

            $filters[$attribute->getAttributeCode() . '_filter'] = $this->getLayout()
                                                                        ->createBlock($filterBlockName)
                                                                        ->setLayer($this->getLayer())
                                                                        ->setAttributeModel($attribute)
                                                                        ->init();
        }

        return $filters;
    }

    /**
     * @param $filters
     */
    protected function _addFacetConditionToFilters($filters)
    {
        foreach ($filters as $filterName => $block) {
            $this->setChild($filterName, $block->addFacetCondition());
        }
    }
}
