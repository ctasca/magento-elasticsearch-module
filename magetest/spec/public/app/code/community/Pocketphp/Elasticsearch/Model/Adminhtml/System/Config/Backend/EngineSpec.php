<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_Adminhtml_System_Config_Backend_EngineSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Adminhtml_System_Config_Backend_Engine');
    }
}
