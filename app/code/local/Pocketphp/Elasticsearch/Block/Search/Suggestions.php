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
 * @subpackage Block
 * @author     Carlo Tasca <carlo.tasca.mail@gmail.com>
 */
class Pocketphp_Elasticsearch_Block_Search_Suggestions extends Mage_Core_Block_Template
{
    /**
     * @param $index
     * @param $term
     * @return string
     */
    public function getSuggestions($index, $term)
    {
        $suggester = Mage::getModel('elasticsearch/term_suggester', "name_suggest");
        return $suggester->suggest($index, $term);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $html = '';

        if (!$this->_beforeToHtml())
            return $html;

        $options = $this->_getSuggestionsOptions();
        $html    = $this->_getSuggestionsHtml($options);

        return $html;
    }

    /**
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * @return Mage_Core_Model_Website
     */
    protected function _getWebsite()
    {
        return Mage::app()->getWebsite();
    }

    /**
     * Gets suggestions options for products_x_x index
     *
     * @return array
     */
    protected function _getSuggestionsOptions()
    {
        $indexName   = "products_{$this->_getStore()->getId()}";
        $suggestions = $this->getSuggestions($indexName, $this->helper('catalogsearch')
            ->getQueryText());
        if ($this->_canGiveSuggestions($suggestions))
            return $suggestions['suggestions'][0]['options'];

        return array();
    }


    /**
     * @param $suggestions
     * @return bool
     */
    protected function _canGiveSuggestions($suggestions)
    {
        return array_key_exists('suggestions', $suggestions) && array_key_exists(0, $suggestions['suggestions']) && array_key_exists('options', $suggestions['suggestions'][0]);
    }

    /**
     * @param $options
     * @return string
     */
    protected function _getSuggestionsHtml(array $options)
    {
        $count = $this->_getOptionsCount($options);
        $html  = '<ul><li style="display:none"></li>';

        foreach ($options as $index => $option) {
            $option['row_class'] = '';
            if ($index == 0) {
                /*$html .= '<li style="background:#ffffff;" title="' . $this->escapeHtml($this->helper('catalogsearch')
                        ->getQueryText()) . '" class="first">' . '<span class="amount">found: <span>' . $this->_getOptionsArrayCount($options) . '</span></span>' . "<strong>Term: </strong>" . $this->escapeHtml($this->helper('catalogsearch')
                        ->getQueryText()) . '</li>';*/
                $html .= '<li style="text-align:center;background:#fff;font-weight:bold" onclick="return false;"><span class="informal">' . $this->__("Suggestions for '%s'", $this->escapeHtml($this->helper('catalogsearch')
                        ->getQueryText())) . '</span></li>';
            }

            if ($index == $count) {
                $option['row_class'] .= ' last';
            }
            $html .= '<li id="elasticsearch-suggestion-' . $option['payload']['id'] . '" onclick="elasticsearch.gotoSuggestionPath(\'' . $this->escapeHtml($option['payload']['path']) . '\')" title="' . $this->escapeHtml($option['text']) . '" class="' . $option['row_class'] . '">' . $this->escapeHtml($option['text']) . '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    /**
     * @param array $options
     * @return int
     */
    protected function _getOptionsCount(array $options)
    {
        return count($options) - 1;
    }

    /**
     * @param array $options
     * @return int
     */
    protected function _getOptionsArrayCount(array $options)
    {
        return count($options);
    }
}
