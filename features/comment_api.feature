@database @fixtures
Feature: A use should be able to send api request to handle comments

    @read
    Scenario: An anonymous user can read all com
        When I send a "GET" request to "/api/comments"
        Then the response status code should be 200
        And the JSON node "items" should have 16 elements
        And the JSON node "totalItems" should be equal to "16"
        And the JSON node "page" should be equal to "1"
        And the JSON node "itemsPerPage" should be equal to "30"
        And the JSON node "items[0].id" should be equal to "com_a1_1"
        And the JSON node "items[0].groupKey" should be equal to "article1"
        And the JSON node "items[0].rate" should be equal to "2.6666666666667"
        And the JSON node "items[0].content" should be equal to "Consequatur quisquam recusandae asperiores."
        And the JSON node "items[0].authorUsername" should be equal to "account1"
        And the JSON node "items[0].children[0].id" should be equal to "com_a1_1_child"
        And the JSON node "items[0].children[0].groupKey" should be equal to "article1"
        And the JSON node "items[0].children[0].rate" should be equal to "2"
        And the JSON node "items[0].children[0].content" should be equal to "Lorem child"
        And the JSON node "items[0].children[0].authorUsername" should be equal to "account5"

        When I send a "GET" request to "/api/comments?itemsPerPage=2"
        Then the response status code should be 200
        And the JSON node "items" should have 2 elements
        And the JSON node "totalItems" should be equal to "16"
        And the JSON node "page" should be equal to "1"
        And the JSON node "itemsPerPage" should be equal to "2"

        When I send a "GET" request to "/api/comments?page=2"
        Then the response status code should be 200
        And the JSON node "items" should have 0 elements
        And the JSON node "totalItems" should be equal to "16"
        And the JSON node "page" should be equal to "2"
        And the JSON node "itemsPerPage" should be equal to "30"

        When I send a "GET" request to "/api/comments?page=2&itemsPerPage=10"
        Then the response status code should be 200
        And the JSON node "items" should have 6 elements
        And the JSON node "totalItems" should be equal to "16"
        And the JSON node "page" should be equal to "2"
        And the JSON node "itemsPerPage" should be equal to "10"

        When I send a "GET" request to "/api/comments?exists[parent]"
        Then the response status code should be 200
        And the JSON node "items" should have 1 elements
        And the JSON node "totalItems" should be equal to "1"
        And the JSON node "page" should be equal to "1"
        And the JSON node "itemsPerPage" should be equal to "30"
        And the JSON node "items[0].id" should be equal to "com_a1_1_child"
        And the JSON node "items[0].groupKey" should be equal to "article1"
        And the JSON node "items[0].rate" should be equal to "2"
        And the JSON node "items[0].content" should be equal to "Lorem child"
        And the JSON node "items[0].authorUsername" should be equal to "account5"

        When I send a "GET" request to "/api/comments?groupKey=article1&&exists[parent]=false"
        Then the response status code should be 200
        And the JSON node "items" should have 10 elements
        And the JSON node "totalItems" should be equal to "10"
        And the JSON node "page" should be equal to "1"
        And the JSON node "itemsPerPage" should be equal to "30"
        And the JSON node "items[0].id" should be equal to "com_a1_1"
        And the JSON node "items[0].groupKey" should be equal to "article1"
        And the JSON node "items[0].rate" should be equal to "2.6666666666667"
        And the JSON node "items[0].content" should be equal to "Consequatur quisquam recusandae asperiores."
        And the JSON node "items[0].authorUsername" should be equal to "account1"

    @read
    Scenario: An anonymous user can't post new com
        When I send a "POST" request to "/api/comments" with body:
        """
        {
            "content": "Test",
            "groupKey": "super_group"
        }
        """
        Then the response status code should be 401

    @read
    Scenario: An anonymous user can't put existing com
        When I send a "PATCH" request to "/api/comments/com_a1_1" with body:
        """
        {
            "rate": 4.5
        }
        """
        Then the response status code should be 401
