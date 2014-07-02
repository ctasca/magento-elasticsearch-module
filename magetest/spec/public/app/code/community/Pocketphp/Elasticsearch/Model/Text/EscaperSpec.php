<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_Text_EscaperSpec extends ObjectBehavior
{
    function let () {
        $this->beConstructedWith("this%^%^");
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Text_Escaper');
    }

    function it_escapes_text_following_apache_lucene_query_parser_syntax()
    {
        $this->escape()->shouldReturn('this%\^%\^');
    }

    function it_escapes_text_following_apache_lucene_query_parser_syntax_via_setText()
    {
        $this->setText('this!@@£$%^&*(*&^');
        $this->escape()->shouldReturn('this\!@@£$%\^&\*\(\*&\^');
    }
}
