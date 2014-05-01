<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_ClientSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Client');
    }

    function it_connects_to_elasticsearch_host_and_returns_http_response_object()
    {
        $this->connect()->shouldReturnAnInstanceOf('Elasticsearch\Client');
    }

    function it_verifies_connection_to_cluster()
    {
        $this->isConnected()->shouldReturn(200);
    }
}
