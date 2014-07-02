<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_Term_SuggesterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith("name_suggest");
    }
    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Term_Suggester');
    }
    /**
     * Moved suggester testing to behat autosuggest.feature
     */
    /*function it_returns_suggestions_for_given_term()
    {
        $expected = array(
            "_shards"     => array(
                "total"      => 5,
                "successful" => 5,
                "failed"     => 0
            ),
            "suggestions" => array(
                "text"    => "Touch",
                "offset"  => 0,
                "length"  => 5,
                "options" => array(
                    array(
                        "text"    => "Nokia 2610 Phone",
                        "score"   => (double) "1.0",
                        "payload" => array("id" => '16')
                    ),
                    array(
                        "text"    => "HTC Touch Diamond",
                        "score"   => "1.0",
                        "payload" => array("id" => '166')
                    ),
                    array(
                        "text"    => "Microsoft Natural Ergonomic Keyboard 4000",
                        "score"   => "1.0",
                        "payload" => array("id" => '159')
                    )
                )
            )
        );

        $expectedJson = '{"_shards":{"total":5,"successful":5,"failed":0},"suggestions":[{"text":"Touch","offset":0,"length":5,"options":[{"text":"HTC Touch Diamond","score":1.0, "payload" : {"id":"166"}},{"text":"Microsoft Natural Ergonomic Keyboard 4000","score":1.0, "payload" : {"id":"159"}},{"text":"Microsoft Wireless Optical Mouse 5000","score":1.0, "payload" : {"id":"162"}},{"text":"Nokia 2610 Phone","score":1.0, "payload" : {"id":"16"}}]}]}';

        $this->suggest("products_1_3", "Touch", 10)->shouldReturn($expectedJson);
    }

    function it_returns_3_suggestions_only_for_the_english_store()
    {
        $expectedJson = '{"_shards":{"total":5,"successful":5,"failed":0},"suggestions":[{"text":"Touch","offset":0,"length":5,"options":[{"text":"Microsoft Natural Ergonomic Keyboard 4000","score":1.0, "payload" : {"id":"159"}},{"text":"Microsoft Wireless Optical Mouse 5000","score":1.0, "payload" : {"id":"162"}},{"text":"Nokia 2610 Phone","score":1.0, "payload" : {"id":"16"}}]}]}';

        $this->suggest("products_1_1", "Touch", 10)->shouldReturn($expectedJson);
    }

    function it_returns_4_suggestions_for_the_french_store()
    {
        $expectedJson = '{"_shards":{"total":5,"successful":5,"failed":0},"suggestions":[{"text":"Touch","offset":0,"length":5,"options":[{"text":"HTC Touch Diamond","score":1.0, "payload" : {"id":"166"}},{"text":"Microsoft Natural Ergonomic Keyboard 4000","score":1.0, "payload" : {"id":"159"}},{"text":"Microsoft Wireless Optical Mouse 5000","score":1.0, "payload" : {"id":"162"}},{"text":"Nokia 2610 Phone","score":1.0, "payload" : {"id":"16"}}]}]}';

        $this->suggest("products_1_2", "Touch", 10)->shouldReturn($expectedJson);
    }

    function it_returns_4_suggestions_for_the_german_store()
    {
        $expectedJson = '{"_shards":{"total":5,"successful":5,"failed":0},"suggestions":[{"text":"Touch","offset":0,"length":5,"options":[{"text":"HTC Touch Diamond","score":1.0, "payload" : {"id":"166"}},{"text":"Microsoft Natural Ergonomic Keyboard 4000","score":1.0, "payload" : {"id":"159"}},{"text":"Microsoft Wireless Optical Mouse 5000","score":1.0, "payload" : {"id":"162"}},{"text":"Nokia 2610 Phone","score":1.0, "payload" : {"id":"16"}}]}]}';

        $this->suggest("products_1_3", "Touch", 10)->shouldReturn($expectedJson);
    }

    */
}
