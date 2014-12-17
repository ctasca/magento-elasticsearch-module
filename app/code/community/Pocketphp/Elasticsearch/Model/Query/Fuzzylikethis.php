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
class Pocketphp_Elasticsearch_Model_Query_Fuzzylikethis extends Pocketphp_Elasticsearch_Model_Query_Dsl
{
    /**
     * The text to find documents like it, required.
     * @var string
     */
    protected $_likeText;

    /**
     * Should term frequency be ignored. Defaults to false.
     *
     * @var bool
     */
    protected $_ignoreTf = null;

    /**
     * The maximum number of query terms that will be included in any generated query. Defaults to 25.
     *
     * @var int
     */
    protected $_maxQueryTerms = null;

    /**
     * The minimum similarity of the term variants. Defaults to 0.5
     *
     * When querying numeric, date and IPv4 fields, fuzziness is interpreted as a +/- margin.
     * It behaves like a Range Query where: -fuzziness <= field value <= +fuzziness
     *
     * @see http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/common-options.html#fuzziness
     * @var float
     */
    protected $_fuzziness = null;

    /**
     *
     * Length of required common prefix on variant terms. Defaults to 0.
     *
     * @var int
     */
    protected $_prefixLength = null;

    /**
     * Sets the boost value of the query. Defaults to 1.0.
     *
     * @var float
     */
    protected $_boost = null;

    /**
     * The analyzer that will be used to analyze the text. Defaults to the analyzer associated with the field.
     *
     * @var string
     */
    protected $_analyzer = null;

    /**
     * @param Mage_Core_Model_Store $store
     */
    public function __construct(Mage_Core_Model_Store $store)
    {
        parent::__construct($store);
    }

    /**
     * @param string $analyzer
     */
    public function setAnalyzer($analyzer)
    {
        $this->_analyzer = $analyzer;
    }

    /**
     * @return string
     */
    public function getAnalyzer()
    {
        if (null === $this->_analyzer)
            $this->_analyzer = Mage::getStoreConfig('elasticsearch/search/fuzzylikethis_analyzer');

        return $this->_analyzer;
    }

    /**
     * @param float $boost
     */
    public function setBoost($boost)
    {
        $this->_boost = $boost;
    }

    /**
     * @return float
     */
    public function getBoost()
    {
        if (null === $this->_boost) {
            $this->_boost = Mage::getStoreConfig('elasticsearch/search/fuzzylikethis_boost');
        }

        return $this->_boost;
    }

    /**
     * @param float $fuzziness
     */
    public function setFuzziness($fuzziness)
    {
        $this->_fuzziness = $fuzziness;
    }

    /**
     * @return float
     */
    public function getFuzziness()
    {
        if (!isset($this->_fuzziness)) {
            $this->_fuzziness = Mage::getStoreConfig('elasticsearch/search/fuzzylikethis_fuzziness');
        }

        return $this->_fuzziness;
    }

    /**
     * @param boolean $ignoreTf
     */
    public function setIgnoreTf($ignoreTf)
    {
        $this->_ignoreTf = $ignoreTf;
    }

    /**
     * @return boolean
     */
    public function getIgnoreTf()
    {
        if (null === $this->_ignoreTf) {
            $this->_ignoreTf = Mage::getStoreConfigFlag('elasticsearch/search/fuzzylikethis_tf');
        }

        return $this->_ignoreTf;
    }

    /**
     * @param string $likeText
     */
    public function setLikeText($likeText)
    {
        $this->_likeText = $likeText;
    }

    /**
     * @return string
     */
    public function getLikeText()
    {
        return $this->_likeText;
    }

    /**
     * @param int $prefixLength
     */
    public function setPrefixLength($prefixLength)
    {
        $this->_prefixLength = $prefixLength;
    }

    /**
     * @return int
     */
    public function getPrefixLength()
    {
        if (null === $this->_prefixLength) {
            $this->_prefixLength = Mage::getStoreConfig('elasticsearch/search/fuzzylikethis_pl');
        }

        return $this->_prefixLength;
    }

    /**
     * @param int $maxQueryTerms
     */
    public function setMaxQueryTerms($maxQueryTerms)
    {
        $this->_maxQueryTerms = $maxQueryTerms;
    }

    /**
     * @return int
     */
    public function getMaxQueryTerms()
    {
        if (null === $this->_maxQueryTerms) {
            $this->_maxQueryTerms = Mage::getStoreConfig('elasticsearch/search/fuzzylikethis_mqt');
        }

        return $this->_maxQueryTerms;
    }

    public function get()
    {
        $this->_query = array();

        if ($this->_canAddFields()) {
            $this->_query['fuzzy_like_this']['fields'] = $this->_getFields();
        } else {
            $this->_query['fuzzy_like_this']['fields'] = array('_all');
        }

        if ($this->getLikeText()) {
            $this->_query['fuzzy_like_this']['like_text'] = $this->getLikeText();
        } else {
            $this->_query['fuzzy_like_this']['like_text'] = '';
        }

        $this->_query['fuzzy_like_this']['ignore_tf'] = $this->getIgnoreTf();
        
        if ($this->getMaxQueryTerms()) {
            $this->_query['fuzzy_like_this']['max_query_terms'] = $this->getMaxQueryTerms();
        }
        
        if ($this->getFuzziness()) {
            $this->_query['fuzzy_like_this']['fuzziness'] = $this->getFuzziness();
        }
        
        if ($this->getPrefixLength()) {
            $this->_query['fuzzy_like_this']['prefix_length'] = $this->getPrefixLength();
        }
        
        if ($this->getBoost()) {
            $this->_query['fuzzy_like_this']['boost'] = $this->getBoost();
        }

        if ($this->getAnalyzer()) {
            $this->_query['fuzzy_like_this']['analyzer'] = $this->getAnalyzer();
        }

        return $this->_query;
    }
}
