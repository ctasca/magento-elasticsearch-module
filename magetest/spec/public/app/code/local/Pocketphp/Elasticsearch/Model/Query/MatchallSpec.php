<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_Query_MatchallSpec extends ObjectBehavior
{
    function let()
    {
        $store = \Mage::app()
            ->getStore(2);
        $this->beConstructedWith($store);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Query_Matchall');
    }

    function it_getQuery_returns_match_all_query()
    {
        $expected          = array();
        $expected['match_all'] = array();

        $this->get()
            ->shouldReturn($expected);
    }


    function it_getQuery_returns_dsl_query_match_all_array_with_boost_param()
    {
        $expected                = array();
        $expected['match_all'] = array('boost' => 1.2);

        $this->setBoost(1.2);
        $this->get()
            ->shouldReturn($expected);
    }
}
