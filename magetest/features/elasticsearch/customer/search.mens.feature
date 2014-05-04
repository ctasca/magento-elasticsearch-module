Feature: Store search for "mens"
  In order to quickly find the product I am looking for
  As a customer I should be able to search the entire store and view results
  for the search term I have specified


  @mink:sahi
  Scenario: I see results when searching for "mens"
    Given I am on "/"
    And I search for "mens"
    Then I should be on "elasticsearch/search/"
    And I should see "Search results for 'mens'"
    And I should see the following layered navigation filters:
      |filter                                         |
      |Category                                       |
      |Price                                          |
      |Color                                          |
      |Brand                                          |
      |Megapixels                                     |
      |Shoe Type                                      |

  @mink:sahi
  Scenario: I can filter by "Category"
    Given I am seeing search results for "mens"
    Then I should see the following filter "1" links:
      |filter                                         |
      |Electronics                                    |
      |Apparel                                        |


  @mink:sahi
  Scenario: I can filter by "Price"
    Given I am seeing search results for "mens"
    Then I should see the following filter "2" links:
     |filter                                       |
     |€0.00 - €999.99                              |
     |€2,000.00 - €2,999.99                        |


  @mink:sahi
  Scenario: I can filter by "Color"
    Given I am seeing search results for "mens"
    Then I should see the following filter "3" links:
      |filter                                      |
      |Black                                       |
      |Blue                                        |
      |Gray                                        |
      |Green                                       |
      |Red                                         |
      |Silver                                      |
      |White                                       |


  @mink:sahi
  Scenario: I can filter by "Brand"
    Given I am seeing search results for "mens"
    Then I should see the following filter "4" links:
      |filter                                      |
      |Apple                                       |


  @mink:sahi
  Scenario: I can filter by "Migapixels"
    Given I am seeing search results for "mens"
    Then I should see the following filter "5" links:
      |filter                                      |
      |7                                           |
      |8                                           |

  @mink:sahi
  Scenario: I can filter by "Shoe Type"
    Given I am seeing search results for "mens"
    Then I should see the following filter "6" links:
      |filter                                       |
      |Dress                                        |
      |Sandal                                       |
      |Tennis                                       |