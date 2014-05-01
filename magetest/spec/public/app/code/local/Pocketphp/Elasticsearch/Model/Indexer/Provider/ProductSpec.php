<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_Indexer_Provider_ProductSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Indexer_Provider_Product');
    }
    /*
    function it_getData_returns_array_with_elasticsearch_indexable_product_data()
    {
        $product = \Mage::getModel('catalog/product')->load(166);
        $this->setIndexName('products');
        $this->setWebsite(1);
        $this->setStore(1);
        $this->getData($product)->shouldBeArray();
    }

    function it_getMapping_returns_the_expected_array()
    {
        $expected = array(
            "product" => array(
                "properties" => array(
                    "entity_id"    => array(
                        "type" => \Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_LONG,
                    ),
                    "type_id"      => array(
                        "type" => \Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_STRING,
                    ),
                    "category_ids" => array(
                        "type" => \Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_STRING,
                    ),
                    "store_ids"    => array(
                        "type" => \Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_STRING,
                    ),
                    "name_suggest" => array(
                        "type"     => \Pocketphp_Elasticsearch_Model_Cluster_Api::MAPPING_TYPE_COMPLETION,
                        "payloads" => true
                    )
                )
            )
        );

        $this->getMapping()->shouldReturn($expected);
    }

    function it_prepareProductCollection_returns_a_filtered_product_collection()
    {
        $products = \Mage::getModel('catalog/product')->setData("store_id", 1)->getResourceCollection();
        $this->prepareCollection($products)->shouldReturnAnInstanceOf("Mage_Catalog_Model_Resource_Product_Collection");
    }*/
}
