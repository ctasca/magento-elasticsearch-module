<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Elasticsearch\Common\Exceptions\Missing404Exception;
class Pocketphp_Elasticsearch_Model_Cluster_ManagerSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Cluster_Manager');
    }

    function it_gets_the_cluster_info_as_an_array()
    {
        $this->getClusterInfo()->shouldBeArray();
    }

    function it_successfully_delete_an_index_from_cluster()
    {
        $expected = array("acknowledged" =>true);
        try{
            $this->deleteIndex('products')->shouldReturn($expected);
        }catch(Missing404Exception $e) {

        }
    }

    function it_successfully_make_put_mapping_request()
    {
        $mappings = \Mage::getModel('elasticsearch/indexer_provider_product')->getMapping();
        $expected = array("acknowledged" =>true);
        $this->createIndex('products');
        $this->putMapping($mappings, 'products', 'product')->shouldReturn($expected);
    }

    function it_checks_if_a_document_already_exists(\Pocketphp_Elasticsearch_Model_Interface_Indexer_Type $indexer)
    {
        $indexer->getIndexName()->willReturn('products');
        $indexer->getIndexType()->willReturn('product');
        $this->documentExists($indexer, 1, 27)->shouldReturn(true);
    }

}
