<?php
/**
 *
 * READ LICENSE AT http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

/**
 * Short description of the class
 *
 * Long description of the class (if any...)
 *
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @subpackage Model
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
class Pocketphp_Elasticsearch_Model_Auth_Plugin_Jetty implements Pocketphp_Elasticsearch_Model_Interface_Auth_Plugin
{
    /**
     * @var string
     */
    protected $_user;
    /**
     * @var string
     */
    protected $_password;
    /**
     * @var string
     */
    protected $_authType;

    /**
     * @return Pocketphp_Elasticsearch_Model_Auth_Plugin_Jetty
     */
    public function init ()
    {
        $this->_user = Mage::getStoreConfig('elasticsearch/security/jetty_user');
        $this->_password = Mage::getStoreConfig('elasticsearch/security/jetty_password');
        $this->_authType = Mage::getStoreConfig('elasticsearch/security/jetty_auth_type');
        return $this;
    }


    /**
     * Returns connectionParams array for Elasticsearch\Client
     *
     * @return array
     */
    public function getConnectionParams()
    {
        $params = array();

        $params['auth'] = array(
            $this->getUser(),
            $this->getPassword(),
            $this->getAuthType()
        );

        return $params;
    }


    /**
     * @return string
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * @return string
     */
    public function getAuthType()
    {
        return $this->_authType;
    }

}
