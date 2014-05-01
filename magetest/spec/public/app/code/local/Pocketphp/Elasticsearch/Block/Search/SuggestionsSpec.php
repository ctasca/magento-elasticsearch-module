<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Block_Search_SuggestionsSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Block_Search_Suggestions');
    }
    /**
     * Moved suggestions block output testing to behat autosuggest.feature
     */
    /*
    function it_returns_suggestions_for_given_search_term_from_elasticsearch_index()
    {
        $expectedJson = '{"_shards":{"total":5,"successful":5,"failed":0},"suggestions":[{"text":"Touch","offset":0,"length":5,"options":[{"text":"HTC Touch Diamond","score":1.0, "payload" : {"id":"166"}},{"text":"Microsoft Natural Ergonomic Keyboard 4000","score":1.0, "payload" : {"id":"159"}},{"text":"Microsoft Wireless Optical Mouse 5000","score":1.0, "payload" : {"id":"162"}},{"text":"Nokia 2610 Phone","score":1.0, "payload" : {"id":"16"}}]}]}';
        $this->getSuggestions("products_1_3","Touch")->shouldReturn($expectedJson);
    }*/
}
