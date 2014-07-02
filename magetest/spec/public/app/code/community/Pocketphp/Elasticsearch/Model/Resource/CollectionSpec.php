<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_Resource_CollectionSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Resource_Collection');
    }

    function it_setEngine_returns_an_instance_of_Pocketphp_Elasticsearch_Model_Resource_Search_Collection()
    {
        $this->setEngine(\Mage::getModel('elasticsearch/engine'))->shouldReturnAnInstanceOf('Pocketphp_Elasticsearch_Model_Resource_Search_Collection');
    }

    function it_setGeneralDefaultQuery_returns_an_instance_of_Pocketphp_Elasticsearch_Model_Resource_Search_Collection()
    {
        $this->setGeneralDefaultQuery()->shouldReturnAnInstanceOf('Pocketphp_Elasticsearch_Model_Resource_Search_Collection');
    }

    function it_setStoreId_returns_an_instance_of_Pocketphp_Elasticsearch_Model_Resource_Search_Collection()
    {
        $this->setStoreId()->shouldReturnAnInstanceOf('Pocketphp_Elasticsearch_Model_Resource_Search_Collection');
    }
}
