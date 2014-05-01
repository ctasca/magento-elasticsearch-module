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
 * Short description of the class
 *
 * Long description of the class (if any...)
 *
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @subpackage Model
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */

$versionHelper = Mage::helper('elasticsearch/version');

if ($versionHelper->isMageEnterprise() && class_exists("Enterprise_Search_Model_Adminhtml_System_Config_Source_Engine")) {
    class Pocketphp_Elasticsearch_Model_Adminhtml_System_Config_Source_Engine extends Enterprise_Search_Model_Adminhtml_System_Config_Source_Engine
    {
        /**
         * @return array
         */
        public function toOptionArray()
        {
            $options = parent::toOptionArray();
            $engines = array(
                'elasticsearch/engine' => Mage::helper('elasticsearch')
                        ->__('Elasticsearch')
            );
            $options = array_merge($options, $engines);

            foreach ($engines as $k => $v) {
                $options[] = array(
                    'value' => $k,
                    'label' => $v
                );
            }

            return $options;
        }
    }
}

if ($versionHelper->isMageCommunity()) {
    class Pocketphp_Elasticsearch_Model_Adminhtml_System_Config_Source_Engine
    {
        /**
         * @return array
         */
        public function toOptionArray()
        {
            $engines = array(
                'catalogsearch/fulltext_engine' => Mage::helper('catalogsearch')
                        ->__('MySql Fulltext'),
                'elasticsearch/engine'          => Mage::helper('elasticsearch')
                        ->__('Elasticsearch')
            );
            $options = array();
            foreach ($engines as $k => $v) {
                $options[] = array(
                    'value' => $k,
                    'label' => $v
                );
            }

            return $options;
        }
    }
}

