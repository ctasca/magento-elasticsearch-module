<?php
/**
 *
 * @deprecated not in use since v0.6.0 alpha
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
 * Provides getters for catalog product's prices (inc, excl tax)
 *
 * @deprecated not in use since v0.6.0 alpha
 *
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @subpackage Model
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
class Pocketphp_Elasticsearch_Model_Price
{
    /**
     * @var Mage_Catalog_Model_Product
     */
    private $_product;
    /**
     * @var Mage_Core_Model_Store
     */
    private $_store;
    /**
     * @var Mage_Core_Helper_Data
     */
    private $_coreHelper;
    /**
     * @var Mage_Weee_Helper_Data
     */
    private $_weeeHelper;
    /**
     * @var Mage_Tax_Helper_Data
     */
    private $_taxHelper;
    protected $_simplePricesTax;
    protected $_minimalPriceValue;
    protected $_minimalPrice;
    protected $_weeeTaxAmount;
    protected $_weeeTaxAmountInclTaxes;
    protected $_weeeTaxAttributes;
    protected $_weeeDisplayType;
    protected $_price;
    protected $_priceInclTax;
    protected $_priceExclTax;
    protected $_regularPrice;
    protected $_finalPrice;
    protected $_finalPriceInclTax;

    public function __construct(array $args)
    {
        $this->_product           = $args['product'];
        $this->_store             = $args['store'];
        $this->_coreHelper        = $args['core'];
        $this->_weeeHelper        = $args['weee'];
        $this->_taxHelper         = $args['tax'];
        $this->_product           = Mage::getModel("catalog/product")
            ->getCollection()
            ->addAttributeToSelect(Mage::getSingleton("catalog/config")
            ->getProductAttributes())
            ->addAttributeToFilter("entity_id", $this->_product->getEntityId())
            ->setPage(1, 1)
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->load()
            ->getFirstItem();
        $this->_simplePricesTax   = ($this->_taxHelper->displayPriceIncludingTax() || $this->_taxHelper->displayBothPrices());
        $this->_minimalPriceValue = $this->_product->getMinimalPrice();
        $this->_minimalPrice      = $this->_taxHelper->getPrice($this->_product, $this->_minimalPriceValue, $this->_simplePricesTax);
        $this->_setPrices();
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    /**
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_store;
    }

    /**
     * @return mixed
     */
    public function getCoreHelper()
    {
        return $this->_coreHelper;
    }


    /**
     * @return mixed
     */
    public function getTaxHelper()
    {
        return $this->_taxHelper;
    }

    /**
     * @return mixed
     */
    public function getWeeeHelper()
    {
        return $this->_weeeHelper;
    }

    /**
     * @return double
     */
    public function getMinimalPrice()
    {
        return $this->_minimalPrice;
    }

    /**
     * @return double
     */
    public function getMinimalPriceValue()
    {
        return $this->_minimalPriceValue;
    }

    /**
     * @return mixed
     */
    public function getSimplePricesTax()
    {
        return $this->_simplePricesTax;
    }

    /**
     * @return mixed
     */
    public function getRegularPrice()
    {
        return $this->_regularPrice;
    }

    /**
     * @return mixed
     */
    public function getFinalPrice()
    {
        return $this->_finalPrice;
    }

    /**
     * @return mixed
     */
    public function getFinalPriceInclTax()
    {
        return $this->_finalPriceInclTax;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->_price;
    }

    /**
     * @return mixed
     */
    public function getPriceExclTax()
    {
        return $this->_priceExclTax;
    }

    /**
     * @return mixed
     */
    public function getPriceInclTax()
    {
        return $this->_priceInclTax;
    }

    /**
     * Gets product price depending on its type.
     *
     * If grouped returns the product minimal price
     * For any other product type returns the product final price
     *
     *
     * @return float|mixed
     */
    public function get()
    {
        if ($this->getProduct()->isGrouped())
            return $this->getProduct()
                ->getMinimalPrice();

        return $this->getFinalPrice();

    }

    /**
     * Sets all product prices values depending on type of display
     *  Prices set are the following:
     *  - price
     *  - regular price
     *  - final price
     *  - final price incl tax
     *  - price exl tax
     *  - price include tax
     */
    protected function _setPrices()
    {
        $this->_weeeTaxAmount = $this->getWeeeHelper()
            ->getAmountForDisplay($this->getProduct());

        if ($this->getWeeeHelper()
            ->typeOfDisplay($this->getProduct(), array(
            Mage_Weee_Model_Tax::DISPLAY_INCL_DESCR, Mage_Weee_Model_Tax::DISPLAY_EXCL_DESCR_INCL, 4
        ))
        ) {
            $this->_weeeTaxAmount     = $this->getWeeeHelper()
                ->getAmount($this->getProduct());
            $this->_weeeTaxAttributes = $this->getWeeeHelper()
                ->getProductWeeeAttributesForDisplay($this->getProduct());
        }

        $this->_weeeTaxAmountInclTaxes = $this->_weeeTaxAmount;

        if ($this->getWeeeHelper()
                ->isTaxable() && !$this->getTaxHelper()
                ->priceIncludesTax($this->getStore()
                ->getId())
        ) {
            $this->_weeeTaxAmountInclTaxes = $this->getWeeeHelper()
                ->getAmountInclTaxes(
                $this->getWeeeHelper()
                    ->getProductWeeeAttributesForRenderer($this->getProduct(), null, null, null, true)
            );
        }
        $this->_price             = $this->getTaxHelper()
            ->getPrice($this->getProduct(), $this->getProduct()
            ->getPrice());

        $this->_regularPrice      = $this->getTaxHelper()
            ->getPrice($this->getProduct(), $this->getProduct()
            ->getPrice(), $this->_simplePricesTax);

        $this->_finalPrice        = $this->getTaxHelper()
            ->getPrice($this->getProduct(), $this->getProduct()
            ->getFinalPrice());

        $this->_finalPriceInclTax = $this->getTaxHelper()
            ->getPrice($this->getProduct(), $this->getProduct()
            ->getFinalPrice(), true);

        $this->_weeeDisplayType = $this->getWeeeHelper()
            ->getPriceDisplayType();

        if ($this->_weeeTaxAmount && $this->getWeeeHelper()
                ->typeOfDisplay($this->getProduct(), 0)
        ) {
            $this->_priceExclTax = $this->getCoreHelper()
                ->currency($this->_price + $this->_weeeTaxAmount, true, false);
            $this->_priceInclTax = $this->getCoreHelper()
                ->currency($this->_finalPriceInclTax + $this->_weeeTaxAmountInclTaxes, true, false);
        } elseif ($this->_weeeTaxAmount && $this->getWeeeHelper()
                ->typeOfDisplay($this->getProduct(), 1)
        ) {
            $this->_priceExclTax = $this->getCoreHelper()
                ->currency($this->_price + $this->_weeeTaxAmount, true, false);
            $this->_priceInclTax = $this->getCoreHelper()
                ->currency($this->_finalPriceInclTax + $this->_weeeTaxAmountInclTaxes, true, false);
        } elseif ($this->_weeeTaxAmount && $this->getWeeeHelper()
                ->typeOfDisplay($this->getProduct(), 4)
        ) {
            $this->_priceExclTax = $this->getCoreHelper()
                ->currency($this->_price + $this->_weeeTaxAmount, true, false);
            $this->_priceInclTax = $this->getCoreHelper()
                ->currency($this->_finalPriceInclTax + $this->_weeeTaxAmountInclTaxes, true, false);
        } elseif ($this->_weeeTaxAmount && $this->getWeeeHelper()
                ->typeOfDisplay($this->getProduct(), 2)
        ) {
            $this->_priceExclTax = $this->getCoreHelper()
                ->currency($this->_price, true, false);
            $this->_priceInclTax = $this->getCoreHelper()
                ->currency($this->_finalPriceInclTax + $this->_weeeTaxAmountInclTaxes, true, false);
        } else {
            $this->_priceExclTax = $this->getCoreHelper()
                ->currency($this->_price, true, false);
            $this->_priceInclTax = $this->getCoreHelper()
                ->currency($this->_finalPriceInclTax + $this->_weeeTaxAmountInclTaxes, true, false);
        }
    }
}