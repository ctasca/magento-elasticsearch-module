<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Block_Search_FormSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Block_Search_Form');
    }

    function it_returns_the_elasticsearch_search_url()
    {
        $expected = \Mage::getBaseUrl() . "elasticsearch/search/";
        $this->getSearchUrl()->shouldReturn($expected);
    }

    function it_returns_the_elasticsearch_suggest_url()
    {
        $expected = \Mage::getBaseUrl() . "elasticsearch/suggest/";
        $this->getSuggestUrl()->shouldReturn($expected);
    }
}
