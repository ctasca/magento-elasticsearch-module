<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_Resource_EngineSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Resource_Engine');
    }

    function it_test_returns_true()
    {
        $this->test()->shouldReturn(true);
    }

    function it_getResultCollection_returns_an_instance_of_Pocketphp_Elasticsearch_Model_Resource_Search_Collection()
    {
        $this->getResultCollection()->shouldReturnAnInstanceOf('Pocketphp_Elasticsearch_Model_Resource_Collection');
    }
}
