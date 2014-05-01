<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Helper_DataSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Helper_Data');
    }

    function it_getClusterManager_returns_an_instance_of_Pocketphp_Elasticsearch_Model_Cluster_Manager()
    {
        $this->getClusterManager()->shouldBeAnInstanceOf("Pocketphp_Elasticsearch_Model_Cluster_Manager");
    }

    function it_getEngine_returns_an_instance_of_Pocketphp_Elasticsearch_Model_Resource_Engine()
    {
        $this->getEngine()->shouldBeAnInstanceOf("Pocketphp_Elasticsearch_Model_Resource_Engine");
    }

    function it_isThirdPartSearchEngine_returns_true()
    {
        $this->isThirdPartSearchEngine()->shouldReturn(true);
    }

    function it_isActiveEngine_returns_true()
    {
        $this->isActiveEngine()->shouldReturn(true);
    }
}
