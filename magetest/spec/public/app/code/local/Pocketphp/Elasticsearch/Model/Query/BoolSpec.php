<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_Query_BoolSpec extends ObjectBehavior
{

    function let()
    {
        $store = \Mage::app()->getStore(1);
        $this->beConstructedWith($store);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Query_Bool');
    }

    function it_get_returns_query_bool_array_with_default_field_and_query()
    {
        $expected = array();
        $expected['bool']['should'][0]['query_string']['default_field'] = "_all";
        $expected['bool']['should'][0]['query_string']['query'] = "microsoft";
        $expected['bool']['should'][0]['query_string']['lowercase_expanded_terms'] = false;
        $expected['bool']['should'][0]['query_string']['use_dis_max'] = true;

        $queryString = \Mage::getModel('elasticsearch/query_string', \Mage::app()->getStore(1));
        $queryString->setDefaultField('_all');
        $queryString->setQueryString('microsoft');

        $this->addShould($queryString);

        $this->get()->shouldReturn($expected);
    }
}
