@database @fixtures @read
Feature: Try to see some project in home page

    Scenario: check if the homepage is status 200
        Given I am on "/"
        Then the response status code should be 200
        And I should see "HomePage"
