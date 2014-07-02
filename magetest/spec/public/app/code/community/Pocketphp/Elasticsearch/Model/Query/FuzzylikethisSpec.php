<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_Query_FuzzylikethisSpec extends ObjectBehavior
{
    function let()
    {
        $store = \Mage::app()->getStore(1);
        $this->beConstructedWith($store);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Query_Fuzzylikethis');
    }

    function it_get_returns_fuzzylikethis_query_array_with_fields_set_to_all_without_analyzer()
    {
        $expected = array();
        $expected['fuzzy_like_this']['fields'] = array('_all');
        $expected['fuzzy_like_this']['like_text'] = 'text like this one';
        $expected['fuzzy_like_this']['ignore_tf'] = false;
        $expected['fuzzy_like_this']['max_query_terms'] = 25;
        $expected['fuzzy_like_this']['fuzziness'] = 0.5;
        $expected['fuzzy_like_this']['prefix_length'] = 0;
        $expected['fuzzy_like_this']['boost'] = 1.0;

        $this->setLikeText('text like this one');

        $this->get()->shouldReturn($expected);
    }

    function it_get_returns_fuzzylikethis_query_array_with_fields_set_to_all_with_analyzer()
    {
        $expected = array();
        $expected['fuzzy_like_this']['fields'] = array('_all');
        $expected['fuzzy_like_this']['like_text'] = 'text like this one';
        $expected['fuzzy_like_this']['ignore_tf'] = false;
        $expected['fuzzy_like_this']['max_query_terms'] = 25;
        $expected['fuzzy_like_this']['fuzziness'] = 0.5;
        $expected['fuzzy_like_this']['prefix_length'] = 0;
        $expected['fuzzy_like_this']['boost'] = 1.0;
        $expected['fuzzy_like_this']['analyzer'] = 'standard';

        $this->setLikeText('text like this one');
        $this->setAnalyzer('standard');

        $this->get()->shouldReturn($expected);
    }

    function it_get_returns_fuzzylikethis_query_array_with_specified_fields_set_to_all_without_analyzer()
    {
        $fields = array('name', 'description');

        $expected = array();
        $expected['fuzzy_like_this']['fields'] = $fields;
        $expected['fuzzy_like_this']['like_text'] = 'text like this one';
        $expected['fuzzy_like_this']['ignore_tf'] = false;
        $expected['fuzzy_like_this']['max_query_terms'] = 25;
        $expected['fuzzy_like_this']['fuzziness'] = 0.5;
        $expected['fuzzy_like_this']['prefix_length'] = 0;
        $expected['fuzzy_like_this']['boost'] = 1.0;

        $this->setLikeText('text like this one');
        $this->setFields(array('name', 'description'));

        $this->get()->shouldReturn($expected);
    }

    function it_get_returns_fuzzylikethis_query_array_with_specified_fields_set_to_all_with_analyzer()
    {
        $fields = array('name', 'description');

        $expected = array();
        $expected['fuzzy_like_this']['fields'] = $fields;
        $expected['fuzzy_like_this']['like_text'] = 'text like this one';
        $expected['fuzzy_like_this']['ignore_tf'] = false;
        $expected['fuzzy_like_this']['max_query_terms'] = 25;
        $expected['fuzzy_like_this']['fuzziness'] = 0.5;
        $expected['fuzzy_like_this']['prefix_length'] = 0;
        $expected['fuzzy_like_this']['boost'] = 1.0;
        $expected['fuzzy_like_this']['analyzer'] = 'standard';

        $this->setLikeText('text like this one');
        $this->setAnalyzer('standard');
        $this->setFields(array('name', 'description'));

        $this->get()->shouldReturn($expected);
    }
}
