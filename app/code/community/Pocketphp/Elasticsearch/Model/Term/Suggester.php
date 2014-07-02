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
class Pocketphp_Elasticsearch_Model_Term_Suggester implements Pocketphp_Elasticsearch_Model_Interface_Suggester
{
    protected $_field;

    public function __construct($field)
    {
        $this->_field = $field;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->_field;
    }

    /**
     * @param $index
     * @param $term
     * @return string
     */
    public function suggest($index, $term)
    {
        $client = Mage::getModel('elasticsearch/client')
            ->connect();
        $escaper = Mage::getModel('elasticsearch/text_escaper');
        $escaper->setText($term);
        $params                                               = array();
        $params['index']                                      = $index;
        $params['body']['suggestions']['text']                = $escaper->escape();
        $params['body']['suggestions']['completion']['field'] = $this->getField();
        $params['body']['suggestions']['completion']['size']  = Mage::getStoreConfig('elasticsearch/suggest/size');

        if (Mage::getStoreConfig('elasticsearch/suggest/is_fuzzy_query') && !is_numeric(Mage::getStoreConfig('elasticsearch/suggest/fuzziness')))
            $params['body']['suggestions']['completion']['fuzzy'] = true;

        if (Mage::getStoreConfig('elasticsearch/suggest/is_fuzzy_query') && is_numeric(Mage::getStoreConfig('elasticsearch/suggest/fuzziness')))
            $params['body']['suggestions']['completion']['fuzzy']['fuzziness'] = Mage::getStoreConfig('elasticsearch/suggest/fuzziness');

        return $client->suggest($params);
    }

}
