@database @fixtures @javascript
Feature: A use should be able to read comments and rates and respond to them

    @read
    Scenario: An anonymous user can read all com
        Given I am on "/"
        And I should see "HomePage"
        And I should see "Article 1"
        And I should see "Article 2"
        And I should see "Login"
        And I should not see "Logout"
        When I follow "Article 1"
        Then I should not see "Article 2 title"
        And I should see "Article 1 title"
        And I should see "Espace commentaire"
        And I should see "Pour poster un commentaire, veuillez vous connecter avec l'un des provider suivant:"
        And I should see "account1 @"
        And I should see "Consequatur quisquam recusandae asperiores."
        And I should see "(2.7)"
        And I should see "account5 @"
        And I should see "Lorem child"
        And I should not see "répondre"
