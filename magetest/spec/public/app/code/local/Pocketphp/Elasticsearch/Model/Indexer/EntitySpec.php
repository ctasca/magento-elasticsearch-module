<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_Indexer_EntitySpec extends ObjectBehavior
{
    function let()
    {
        $client = \Mage::getModel('elasticsearch/client');
        $this->beConstructedWith($client);
        $this->setType('product');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Indexer_Entity');
    }

    function its_elasticsearch_client_should_be_of_type_connectable()
    {
        $this->getClient()->shouldHaveType("Pocketphp_Elasticsearch_Model_Interface_Connectable");
    }

    function its_type_is_a_string()
    {
        $this->getType()->shouldBeString();
    }

    function it_instanciates_entity_indexer_by_its_type()
    {
        $this->getTypeIndexer()->shouldHaveType("Pocketphp_Elasticsearch_Model_Interface_Indexer_Type");
    }
    /*
    function it_indexes_data_to_elasticsearch_cluster_and_returns_responses_array()
    {
        $this->index()->shouldBeArray();
    }*/
}
