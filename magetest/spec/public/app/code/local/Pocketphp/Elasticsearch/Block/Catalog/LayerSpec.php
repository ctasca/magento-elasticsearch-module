<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Block_LayerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Block_Catalog_Layer');
    }

    function it_getLayer_returns_an_instance_of_Pocketphp_Elasticsearch_Model_Catalog_Layer()
    {
        $this->getLayer()->shouldReturnAnInstanceOf("Pocketphp_Elasticsearch_Model_Catalog_Layer");
    }

    /**
    function it_canShowOptions_returns_a_boolean()
    {
        $this->canShowOptions()->shouldReturnBool();
    }*/
}
