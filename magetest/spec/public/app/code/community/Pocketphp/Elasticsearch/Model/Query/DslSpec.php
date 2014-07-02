<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_Query_DslSpec extends ObjectBehavior
{
    function let()
    {
        $store = \Mage::app()->getStore(1);
        $this->beConstructedWith($store);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Query_Dsl');
    }

    function it_getQuery_returns_query_dsl_array_with_index_and_type()
    {
        $expected = array();
        $expected['index'] = "products_1";
        $expected['type'] = "product";
        $expected['body'] = array();

        $this->setIndexName('products');
        $this->setIndexType('product');

        $this->get()->shouldReturn($expected);
    }

    function it_getQuery_returns_query_dsl_array_with_index_type_and_search_type()
    {
        $expected = array();
        $expected['index'] = "products_1";
        $expected['type'] = "product";
        $expected['search_type'] = "query_and_fetch";
        $expected['body'] = array();

        $this->setIndexName('products');
        $this->setIndexType('product');
        $this->setType("query_and_fetch");
        $expected['body'] = array();

        $this->get()->shouldReturn($expected);
    }

    function it_getQuery_returns_query_dsl_array_with_index_type_search_type_and_q()
    {
        $expected = array();
        $expected['index'] = "products_1";
        $expected['type'] = "product";
        $expected['search_type'] = "query_and_fetch";
        $expected['q'] = "searched text";

        $this->setIndexName('products');
        $this->setIndexType('product');
        $this->setType("query_and_fetch");
        $this->setQ("searched text");
        $expected['body'] = array();

        $this->get()->shouldReturn($expected);
    }
}
