<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_PriceSpec extends ObjectBehavior
{

    function let()
    {
        $args = array(
            'product' => \Mage::getModel('catalog/product')->load(54),
            'store' => \Mage::app()->getStore(),
            'core' => \Mage::helper('core'),
            'weee' => \Mage::helper('weee'),
            'tax' => \Mage::helper('tax')
        );

        $this->beConstructedWith($args);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Price');
    }

    function it_getProduct_returns_an_instance_of_Mage_Catalog_Model_Product()
    {
        $this->getProduct()->shouldReturnAnInstanceOf('Mage_Catalog_Model_Product');
    }

    function it_getStore_returns_an_instance_of_Mage_Core_Model_Store()
    {
        $this->getStore()->shouldReturnAnInstanceOf('Mage_Core_Model_Store');
    }

    function it_getCoreHelper_returns_an_instance_of_Mage_Core_Helper_Data()
    {
        $this->getCoreHelper()->shouldReturnAnInstanceOf('Mage_Core_Helper_Data');
    }

    function it_getTaxHelper_returns_an_instance_of_Mage_Tax_Helper_Data()
    {
        $this->getTaxHelper()->shouldReturnAnInstanceOf('Mage_Tax_Helper_Data');
    }

    function it_getWeeeHelper_returns_an_instance_of_()
    {
        $this->getWeeeHelper()->shouldReturnAnInstanceOf('Mage_Weee_Helper_Data');
    }

    function it_getFinalPrice_returns_a_double()
    {
        $this->getFinalPrice()->shouldReturn(699.99);
    }

    function it_getMinimalPrice_returns_a_double()
    {
        $this->getMinimalPrice()->shouldReturn(129.99);
    }

    function it_getMinimalPriceValue_returns_a_double()
    {
        $this->getMinimalPriceValue()->shouldReturn("129.9900");
    }

    function it_getSimplePricesTax_returns_a_boolean()
    {
        $this->getSimplePricesTax()->shouldBeBoolean();
    }

    function it_gets_the_grouped_product_minimal_price()
    {
        $this->get()->shouldReturn("129.9900");
    }

}
