<?php
# features/bootstrap/CustomerUserContext.php

use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use MageTest\MagentoExtension\Context\MagentoContext;

class ElasticsearchCustomerContext extends MyMagentoContext
{

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
            $id = $suggestion['id'];
            $isSuggestion = $this->getSession()->getDriver()->evaluateScript("$('elasticsearch-suggestion-$id').innerHTML");
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
}