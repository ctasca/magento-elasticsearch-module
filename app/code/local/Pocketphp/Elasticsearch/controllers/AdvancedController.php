<?php
/**
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

/**
 * Controller for elasticsearch/advanced requests
 *
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @subpackage Block
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
class Pocketphp_Elasticsearch_AdvancedController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalogsearch/session');
        $this->renderLayout();
    }

    public function resultAction()
    {
        $this->loadLayout();
        try {
            Mage::getSingleton('catalogsearch/advanced')->addFilters($this->getRequest()->getQuery());
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('catalogsearch/session')->addError($e->getMessage());
            $this->_redirectError(
                 Mage::getModel('core/url')
                     ->setQueryParams($this->getRequest()->getQuery())
                     ->getUrl('*/*/')
            );
        }
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }
}