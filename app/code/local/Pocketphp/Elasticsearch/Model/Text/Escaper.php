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
 * Apache Lucene Query Parser
 *
 *
 * @see        http://lucene.apache.org/core/3_6_0/queryparsersyntax.html
 * @category   Pocketphp
 * @package    Pocketphp_Elasticsearch
 * @subpackage Model
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
class Pocketphp_Elasticsearch_Model_Text_Escaper
{
    /**
     * @var string the text to be escaped
     */
    private $_text;

    /**
     * Construct with text to be escaped
     *
     * @param $text
     */
    public function __construct($text = '')
    {
        $this->_text = $text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->_text = $text;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * Escapes a special characters in text.
     *
     * Lucene supports escaping special characters that are part of the query syntax.
     * The current list special characters are:
     *
     * + - && || ! ( ) { } [ ] ^ " ~ * ? : \
     *
     * @see Apache Lucene - Query Parser Syntax
     * @return mixed
     */
    public function escape()
    {
        if ($this->_isPhrase())
            return $this->_escapeTerms();

        return $this->_escapeTerm();

    }

    /**
     * Returns true if given value contains more than one word
     *
     * @return bool
     */
    protected function _isPhrase()
    {
        $words = array();

        if (strlen(trim($this->getText())))
            $words = explode(' ', trim($this->getText()));

        return count($words) > 1;
    }

    /**.
     *
     * @return string
     */
    protected function _escapeTerm()
    {
        $pattern = '/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\\\)/';
        $replace = '\\\$1';

        return preg_replace($pattern, $replace, $this->getText());
    }

    /**
     *
     * @return string
     */
    protected function _escapeTerms()
    {
        $pattern = '/("|\\\)/';
        $replace = '\\\$1';

        return preg_replace($pattern, $replace, $this->getText());
    }

}
