<?php
# features/bootstrap/CustomerUserContext.php

use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use MageTest\MagentoExtension\Context\MagentoContext;

class ElasticsearchCustomerContext extends MyMagentoContext
{

    private $searchedTerm = false;

    /**
     * @Given /^I type "([^"]*)" in search input$/
     */
    public function iTypeInSearchInput($text)
    {
        $this->getSession()->getPage()->fillField("search", $text);
    }

    /**
     * @Then /^I should see the following suggestion list:$/
     */
    public function iShouldSeeTheFollowingSuggestionList(TableNode $suggestions)
    {
        $expected = $suggestions->getHash();
        foreach ($expected as $suggestion) {
            $id           = $suggestion['id'];
            $isSuggestion = $this->getSession()->getDriver()
                                 ->evaluateScript("$('elasticsearch-suggestion-$id').innerHTML");
            if ($isSuggestion != $suggestion['suggestion'])
                throw new Exception("Suggestion for '" . $suggestion['suggestion'] . "' not found in suggestions list");
        }
    }

    /**
     * @When /^I switch to "([^"]*)" store$/
     */
    public function iSwitchToStore($store)
    {
        $this->getSession()->getPage()->selectFieldOption('select-language', $store);
    }

    /**
     * @When /^the suggestions list appear$/
     */
    public function theSuggestionsListAppear()
    {
        $visible = $this->getSession()->getDriver()->evaluateScript("$('search_autocomplete').visible()");

        if ($visible == "false")
            throw new Exception("Suggestions list is not visible");
    }

    /**
     * @Given /^I click suggestion "([^"]*)"$/
     */
    public function iClickSuggestion($id)
    {
        $suggestionElement = $this->_findElementWithXpath('//*[@id="elasticsearch-suggestion-' . $id . '"]');
        $suggestionElement[0]->click();
    }

    /**
     * @Given /^I search for "([^"]*)"$/
     */
    public function iSearchFor($text)
    {
        $this->getSession()->getPage()->fillField("search", $text);
        $this->getSession()->getPage()->pressButton('Search');
    }

    /**
     * @Given /^I should see the following layered navigation filters:$/
     */
    public function iShouldSeeTheFollowingLayeredNavigationFilters(TableNode $filters)
    {
        $expected = $filters->getHash();
        foreach ($expected as $key => $filter) {
            //*[@id="narrow-by-list"]/dt[1]
            $iKey              = $key + 1;
            $layeredFilter     = $this->_findElementWithXpath('//*[@id="narrow-by-list"]/dt[' . $iKey . ']');
            $expectedFilerText = trim(strtoupper($filter['filter']));
            $uFText            = trim(strtoupper($layeredFilter[0]->getText()));
            if ($uFText != $expectedFilerText)
                throw new \Exception("{$filter['filter']} filter element not found in layered naviation");
        }
    }

    /**
     * @Given /^I am seeing search results for "([^"]*)"$/
     */
    public function iAmSeeingSearchResultsFor($term)
    {
        $this->searchedTerm = $term;
        $this->iShouldSee("Search results for '$term'");
    }

    /**
     * @When /^I follow layered navigation Electronics filter$/
     */
    public function iFollowLayeredNavigationElectronicsFilter()
    {
        $layeredNavigationFilter = $this->_findElementWithXpath('//*[@id="narrow-by-list"]/dd[1]/ol/li/a');
        $layeredNavigationFilter[0]->click();
    }

    /**
     * @Given /^I should see the following filter "([^"]*)" links:$/
     */
    public function iShouldSeeTheFollowingFilterLinks($ddIndex, TableNode $table)
    {
        $this->_getLayeredNavFiltersByDdIndex($table, $ddIndex);
    }


    /**
     * @param TableNode $filters
     * @param           $ddIndex
     * @throws Exception
     */
    protected function _getLayeredNavFiltersByDdIndex(TableNode $filters, $ddIndex)
    {
        $expected             = $filters->getHash();
        $expectedResultsArray = array_fill(0, count($expected), true);
        $expectedResults      = md5(serialize($expectedResultsArray));
        $testResults          = array();
        $notFound = array();
        foreach ($expected as $i => $filter) {
            $ii        = $i + 1;
            $navFilter = $this->_findElementWithXpath('//*[@id="narrow-by-list"]/dd[' . $ddIndex . ']/ol/li[' . $ii . ']/a');
            if ($filter['filter'] != trim($navFilter[0]->getText())) {
                $notFound[$filter['filter']] = $filter['filter'];
                continue;
            }

            $testResults[] = true;
        }

        $testResultsMd5 = md5(serialize($testResults));

        if ($testResultsMd5 != $expectedResults)
            throw new \Exception("One or more category filters were not found in layered naviation. Not found: " . implode(', ', $notFound));
    }


}