Feature: Store search auto-suggestions
  In order to quickly find the product I am looking for
  As a customer I should be able to see a list of suggestions
  when typing text in the search input

  @mink:sahi
  Scenario: Find 3 suggestions for English store
    Given I am on "/"
    And I type "Touch" in search input
    And I wait for "1" seconds
    And I should see the following suggestion list:
      |id     |suggestion                                 |
      |159    |Microsoft Natural Ergonomic Keyboard 4000  |
      |162    |Microsoft Wireless Optical Mouse 5000      |
      |16     |Nokia 2610 Phone                           |


  @mink:sahi
  Scenario: Find 4 suggestions for French store
    Given I switch to "French" store
    When I type "Touch" in search input
    And I wait for "1" seconds
    And I should see the following suggestion list:
      |id     |suggestion                                 |
      |166    |HTC Touch Diamond                          |
      |159    |Microsoft Natural Ergonomic Keyboard 4000  |
      |162    |Microsoft Wireless Optical Mouse 5000      |
      |16     |Nokia 2610 Phone                           |


  @mink:sahi
  Scenario: Find 4 suggestions for German store
    Given I switch to "German" store
    When I type "Touch" in search input
    And I wait for "1" seconds
    And I should see the following suggestion list:
      |id     |suggestion                                 |
      |166    |HTC Touch Diamond                          |
      |159    |Microsoft Natural Ergonomic Keyboard 4000  |
      |162    |Microsoft Wireless Optical Mouse 5000      |
      |16     |Nokia 2610 Phone                           |

  @mink:sahi
  Scenario: I am redirected to Nokia 2610 Phone product page
    Given I type "touch" in search input
    And I wait for "3" seconds
    When the suggestions list appear
    And I click suggestion "16"
    Then I should be on "nokia-2610-phone.html"

  @mink:sahi
  Scenario: I am redirected to Microsoft Natural Ergonomic Keyboard 4000 product page
    Given I type "touch" in search input
    And I wait for "3" seconds
    When the suggestions list appear
    And I click suggestion "159"
    Then I should be on "microsoft-natural-ergonomic-keyboard-4000.html"