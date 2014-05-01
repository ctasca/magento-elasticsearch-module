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
interface Pocketphp_Elasticsearch_Model_Cluster_Api
{
    const MAPPING                 = '_mapping';
    const SUGGEST                 = '_suggest';
    const SEARCH                  = '_search';
    const FSEARCH                 = '_search?q=%s';
    const WARMER                  = '_warmer';
    const ALIAS                   = '_alias';
    const SETTINGS                = '_settings';
    const MAPPING_TYPE_STRING     = 'string';
    const MAPPING_TYPE_INT        = 'integer';
    const MAPPING_TYPE_LONG       = 'long';
    const MAPPING_TYPE_FLOAT      = 'float';
    const MAPPING_TYPE_DOUBLE     = 'double';
    const MAPPING_TYPE_BOOLEAN    = 'boolean';
    const MAPPING_TYPE_DATE       = 'date';
    const MAPPING_TYPE_COMPLETION = 'completion';
    const MAPPING_TYPE_UNKNOWN    = 'null';
    const FIELD_NOT_ANALYZED      = 'not_analyzed';
}
