<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_Query_FilteredSpec extends ObjectBehavior
{
    function let()
    {
        $store = \Mage::app()->getStore(1);
        $this->beConstructedWith($store);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Query_Filtered');
    }

    function it_get_returns_query_filtered_array_with_bool_query_and_filters_and_facets()
    {
        $queryString = \Mage::getModel('elasticsearch/query_string', \Mage::app()->getStore(1));
        $queryString->setDefaultField('_all');
        $queryString->setQueryString('microsoft');

        $boolQuery = \Mage::getModel('elasticsearch/query_bool', \Mage::app()->getStore(1));
        $boolQuery->addShould($queryString);

        $facets = array();
        $facets['#categories']['terms']['field'] = "#categories";
        $facets['color']['terms']['field'] = "color";
        //$facets['#price_0_1']['statistical']['field'] = "#price_0_1";

        $expected = array();
        $expected['index'] = "products_1";
        $expected['type'] = "product";
        $expected['body']['query']['filtered']['query'] = $boolQuery->get();
        //$expected['body']['query']['filtered']['filter']['and']['filters'][0]['range']['#price_0_1']['from'] = 10;
        //$expected['body']['query']['filtered']['filter']['and']['filters'][0]['range']['#price_0_1']['to'] = 2000000;
        $expected['body']['facets']['#categories']['terms']['field'] = "#categories";
        $expected['body']['facets']['color']['terms']['field'] = "color";

        $this->setIndexName('products');
        $this->setIndexType('product');
        $this->setFilterType('and');
        $this->setFacets($facets);

        $this->setFQuery($boolQuery);
        $this->get()->shouldReturn($expected);
    }
}
