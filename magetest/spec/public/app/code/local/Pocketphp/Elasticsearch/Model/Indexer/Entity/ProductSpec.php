<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_Indexer_Entity_ProductSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Indexer_Entity_Product');
    }
    /*
    function it_indexes_data_and_return_array_with_json_documents_as_values()
    {
        try{
            $this->index(1, 1)->shouldBeArray();
        }catch(\Exception $e) {

        }

    }*/

    function it_returns_the_products_index_name()
    {
        $this->getIndexName()->shouldBe("products");
    }

    function it_returns_the_product_index_type()
    {
        $this->getIndexType()->shouldBe("product");
    }
}
