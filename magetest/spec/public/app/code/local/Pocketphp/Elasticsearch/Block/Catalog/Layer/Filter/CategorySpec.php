<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Block_Catalog_Layer_Filter_CategorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Block_Catalog_Layer_Filter_Category');
    }

    function it_addFacetCondition_return_instance_of_Pocketphp_Elasticsearch_Block_Catalog_Layer_Filter_Category()
    {
        $this->init();
        $this->addFacetCondition()->shouldReturnAnInstanceOf('Pocketphp_Elasticsearch_Block_Catalog_Layer_Filter_Category');
    }
}
