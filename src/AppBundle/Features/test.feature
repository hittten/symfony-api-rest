Feature: Oauth Server
    In order to login with an user
    I need to authenticate with all possibilities

    Scenario: I can not authenticate with an invalid user
        When I authenticate with the user invalid and password admin on the client 1_randomid_all and type token from http://localhost:3000
        Then the response status code should be 200
        And the response should contain "Invalid credentials."
        And the response should not be in JSON

    Scenario: I can not authenticate with an invalid password
        When I authenticate with the user admin and password invalid on the client 1_randomid_all and type token from http://localhost:3000
        Then the response status code should be 200
        And the response should contain "Invalid credentials."
        And the response should not be in JSON

    Scenario: I can not authenticate with an invalid client
        When I authenticate with the user admin and password admin on the client invalid and type token from http://localhost:3000
        Then the response status code should be 404
        And the response should not be in JSON

    Scenario: I can not authenticate with an unsupported response type
        When I authenticate with the user admin and password admin on the client 1_randomid_all and type invalid from http://localhost:3000
        Then I authorize the client by clicking in Allow button
        Then the current url should equal to http://localhost:3000?error=unsupported_response_type

    Scenario: I can not authenticate with an invalid redirect uri
        When I authenticate with the user admin and password admin on the client 1_randomid_all and type token from http://invalid:3000
        Then I authorize the client by clicking in Allow button
        Then the response status code should be 400
        And the JSON node "error" should be equal to "redirect_uri_mismatch"

    Scenario: As a user I want to login with html form
        When I authenticate with the user admin and password admin on the client 1_randomid_all and type token from http://localhost:3000
        And I authorize the client by clicking in Allow button
        Then I take the token from current url
        When I try to send a "GET" request to "/me"
        And the response status code should be 200

    Scenario: As a user I want to login with html form
        When I authenticate with the user admin and password admin on the client 1_randomid_all and type token from http://localhost:3000
        And I authorize the client by clicking in Allow button
        Then I take the token from current url
        When I try to send a "GET" request to "/me"
        And the response status code should be 200

    Scenario: As a user I want to authenticated with client id and client secret
        When I try to send a "POST" request to "/oauth/v2/token" with json body:
        """
            {
                "client_id": "1_randomid_all",
                "client_secret": "secret_all",
                "grant_type": "password",
                "username": "admin",
                "password": "admin"
            }
        """
        Then the response status code should be 200
        And the JSON node access_token should exist
        And the JSON node expires_in should be equal to the number 3600
        And the JSON node token_type should be equal to bearer
        And the JSON node scope should be null
        And the JSON node refresh_token should exist

    Scenario: Authenticated with token
        Given I am authenticated with token token
        When I try to send a "GET" request to "/users"
        Then print last JSON response
        Then the response status code should be 200

#
#    Scenario: Authenticated with token
#        Given I send a "POST" request to "/users" with json body:
#        """
#            {
#                "username": "user_1"
#            }
#        """
#        Then the response status code should be 200
##        Then print last response
