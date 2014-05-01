<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_Text_FilterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(" J'étudie le français ");
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Text_Filter');
    }

    function it_filters_text_by_removing_any_latin_character_from_given_text()
    {
        $expected = "jetudie-le-francais";
        $this->filter()->shouldReturn($expected);
    }
}
