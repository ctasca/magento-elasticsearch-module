<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_Query_StringSpec extends ObjectBehavior
{
    function let()
    {
        $store = \Mage::app()->getStore(1);
        $this->beConstructedWith($store);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Query_String');
    }

    function it_get_returns_query_bool_array_with_default_field_and_query()
    {
        $expected = array();
        $expected['query_string']['default_field'] = "_all";
        $expected['query_string']['query'] = "microsoft";
        $expected['query_string']['lowercase_expanded_terms'] = false;
        $expected['query_string']['use_dis_max'] = true;

        $this->setDefaultField('_all');
        $this->setQueryString('microsoft');

        $this->get()->shouldReturn($expected);
    }

    function it_get_returns_query_string_with_lowercase_expanded_terms_and_use_dis_max_params()
    {
        $expected = array();
        $expected['query_string']['fields'] = array(
            "name",
            "description"
        );
        $expected['query_string']['query'] = "microsoft";
        $expected['query_string']['lowercase_expanded_terms'] = false;
        $expected['query_string']['use_dis_max'] = true;

        $this->setFields(array('name', 'description'));
        $this->setQueryString('microsoft');

        $this->get()->shouldReturn($expected);
    }
}
