<?php
/**
 *
 * Pocketphp_Elasticsearch
 *
 * READ LICENSE AT  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Pocketphp Elasticsearch module to newer versions in the future.
 * If you wish to customize the Pocketphp Elasticsearch module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @copyright  Copyright (C) 2014 Pocketphp ltd (http://pocketphp.co.uk)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
new Pocketphp_Elasticsearch_Autoloader();

/**
 *
 * Provides connectivity to elasticsearch cluster via \Elasticsearch\Client
 * with user-defined configuration values
 *
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @subpackage Model
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
class Pocketphp_Elasticsearch_Model_Client implements Pocketphp_Elasticsearch_Model_Interface_Connectable
{
    protected $_host;
    protected $_port;

    public function __construct()
    {
        $this->_host = Mage::getStoreConfig('elasticsearch/core/host');
        $this->_port = (int) Mage::getStoreConfig('elasticsearch/core/port');
    }

    /**
     *
     * Refer to http://www.elasticsearch.org/guide/en/elasticsearch/client/php-api/current/_configuration.html
     * for available \Elasticsearch\Client Api
     *
     * @return \Elasticsearch\Client
     * @see Pocketphp_Elasticsearch_Model_Interface_Connectable
     */
    public function connect()
    {
        $params            = array();
        $params['hosts'][] = $this->_host . ':' . $this->_port;

        if ($this->_isAuthenticationEnabled()) {
            $plugin = $this->_getAuthPluginModel();
            $params['connectionParams'] = $plugin->getConnectionParams();
        }

        foreach ($this->_getAdditionalHosts() as $host)
            $params['hosts'][] = trim($host);

        return new \Elasticsearch\Client($params);
    }

    /**
     * @return int|bool
     */
    public function isConnected()
    {
        $info = $this->_getClusterManager()->getClusterInfo();

        if (is_array($info) && array_key_exists('status', $info))
            return $info['status'];

        return false;
    }

    /**
     * @return Pocketphp_Elasticsearch_Model_Cluster_Manager
     */
    protected function _getClusterManager()
    {
        return Mage::helper('elasticsearch')->getClusterManager();
    }

    /**
     * @return array
     */
    protected function _getAdditionalHosts()
    {
        $additionalHosts = trim(Mage::getStoreConfig('elasticsearch/core/additional_hosts'));
        if ($additionalHosts)
            return explode(',', $additionalHosts);

        return array();
    }

    /**
     * @return mixed
     */
    protected function _isAuthenticationEnabled()
    {
        return Mage::getStoreConfig('elasticsearch/security/auth_enabled');
    }

    /**
     * @return mixed
     */
    protected function _getAuthPlugin()
    {
        return Mage::getStoreConfig('elasticsearch/security/plugin');
    }

    /**
     * @return false|Pocketphp_Elasticsearch_Model_Interface_Auth_Plugin
     * @see Pocketphp_Elasticsearch_Model_Interface_Auth_Plugin::getConnectionParams
     */
    protected function _getAuthPluginModel()
    {
        return Mage::getModel('elasticsearch/auth_plugin_' . $this->_getAuthPlugin())->init();
    }
}