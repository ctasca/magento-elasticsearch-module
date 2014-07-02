<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class Pocketphp_Elasticsearch_Model_Auth_Plugin_JettySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Pocketphp_Elasticsearch_Model_Auth_Plugin_Jetty');
    }

    function it_getConnectionParams_returns_array_with_auth_params()
    {
        if (!\Mage::getStoreConfig('elasticsearch/security/auth_enabled'))
            return true;

        $expected = array();

        $expected['auth'] = array(
            "magento",
            "6t5r4e3w2q",
            "Basic",
        );

        $this->init();

        $this->getConnectionParams()->shouldReturn($expected);
    }
}
