<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_SetupSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith("core_setup");
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Setup');
    }

    function it_creates_elasticsearch_attribute_group_if_does_not_exist()
    {
        $expected = \Zend_Json::encode(array(
           "created" => true
        ));

        $this->createElasticsearchAttributeGroup()->shouldReturn($expected);
    }

    function it_creates_the_name_suggest_input_attribute_if_does_not_exits()
    {
        $expected = \Zend_Json::encode(array(
            "created" => true
        ));

        $this->createNameSuggestInputAttribute()->shouldReturn($expected);
    }

    function it_creates_the_name_suggest_weight_attribute_if_does_not_exits()
    {
        $expected = \Zend_Json::encode(array(
            "created" => true
        ));

        $this->createNameSuggestWeightAttribute()->shouldReturn($expected);
    }

    function it_creates_the_remove_category_names_suggestions_suggest_attribute_if_does_not_exits()
    {
        $expected = \Zend_Json::encode(array(
            "created" => true
        ));

        $this->createRemoveCategoryNamesSuggestionsAttribute()->shouldReturn($expected);
    }

    function it_creates_the_remove_product_name_suggestions_attribute_if_does_not_exits()
    {
        $expected = \Zend_Json::encode(array(
            "created" => true
        ));

        $this->createRemoveProductNameSuggestionsAttribute()->shouldReturn($expected);
    }

    function it_creates_the_es_fields_analyzed_attribute_if_does_not_exits()
    {
        //deprecated method
        $this->createEsFieldsAnalyzedAttribute()->shouldReturnAnInstanceOf('Pocketphp_Elasticsearch_Model_Setup');
    }

    function it_creates_the_add_short_description_to_suggestions_attribute_if_does_not_exits()
    {
        $expected = \Zend_Json::encode(array(
            "created" => true
        ));

        $this->createRemoveShortDescriptionSuggestionsAttribute()->shouldReturn($expected);
    }
}
