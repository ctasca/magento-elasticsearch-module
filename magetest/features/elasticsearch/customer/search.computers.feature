Feature: Store search for "computers"
  In order to quickly find the product I am looking for
  As a customer I should be able to search the entire store and view results
  for the search term I have specified


  @mink:sahi
  Scenario: I see results when searching for "computers"
    Given I am on "/"
    And I search for "computers"
    Then I should be on "elasticsearch/search/"
    And I should see "Search results for 'computers'"
    And I should see the following layered navigation filters:
     |filter                                         |
     |Category                                       |
     |Contrast Ratio                                 |
     |Price                                          |
     |Color                                          |
     |Brand                                          |
     |Manufacturer                                   |
     |Megapixels                                     |

  @mink:sahi
  Scenario: I can filter by "Category"
    Given I am seeing search results for "computers"
    When I follow layered navigation Electronics filter
    Then I should be on "elasticsearch/search/index/"
    And I should see the following filter "1" links:
     |filter                                        |
     |Cell Phones                                   |
     |Cameras                                       |
     |Computers                                     |

  @mink:sahi
  Scenario: I can filter by "Contrast Ratio"
    Given I am seeing search results for "computers"
    Then I should see the following filter "2" links:
     |filter                                       |
     |1500:1                                       |
     |3000:1                                       |


  @mink:sahi
  Scenario: I can filter by "Price"
    Given I am seeing search results for "computers"
    Then I should see the following filter "3" links:
     |filter                                       |
     |€0.00 - €999.99                              |
     |€1,000.00 - €1,999.99                        |
     |€2,000.00 - €2,999.99                        |
     |€4,000.00 - €4,999.99                        |


  @mink:sahi
  Scenario: I can filter by "Color"
    Given I am seeing search results for "computers"
    Then I should see the following filter "4" links:
      |filter                                      |
      |Black                                       |
      |Brown                                       |
      |Silver                                      |


  @mink:sahi
  Scenario: I can filter by "Brand"
    Given I am seeing search results for "computers"
    Then I should see the following filter "5" links:
      |filter                                      |
      |Acer                                        |
      |Apple                                       |
      |Toshiba                                     |


  @mink:sahi
  Scenario: I can filter by "Manufacturer"
    Given I am seeing search results for "computers"
    Then I should see the following filter "6" links:
      |filter                                      |
      |Apple                                       |
      |Crucial                                     |
      |HTC                                         |
      |Intel                                       |
      |Logitech                                    |
      |Samsung                                     |


  @mink:sahi
  Scenario: I can filter by "Migapixels"
    Given I am seeing search results for "computers"
    Then I should see the following filter "7" links:
      |filter                                      |
      |5                                           |
