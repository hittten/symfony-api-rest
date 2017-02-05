@oauth
Feature: Oauth Server
    In order to login with an user
    I need to authenticate with all possibilities

    @security
    Scenario: I can not authenticate with an invalid user
        When I authenticate with the user invalid and password admin on the client 1_client and type token from http://localhost:3000
        Then the response status code should be 200
        And the response should contain "Invalid credentials."
        And the response should not be in JSON

    @security
    Scenario: I can not authenticate with an invalid password
        When I authenticate with the user admin and password invalid on the client 1_client and type token from http://localhost:3000
        Then the response status code should be 200
        And the response should contain "Invalid credentials."
        And the response should not be in JSON

    @security
    Scenario: I can not authenticate with an invalid client
        When I authenticate with the user admin and password admin on the client invalid and type token from http://localhost:3000
        Then the response status code should be 404
        And the response should not be in JSON

    @security
    Scenario: I can not authenticate with an unsupported response type
        When I authenticate with the user admin and password admin on the client 1_client and type invalid from http://localhost:3000
        And I authorize the client
        Then the current url should equal to http://localhost:3000?error=unsupported_response_type

    @security
    Scenario: I can not authenticate with an invalid redirect uri
        When I authenticate with the user admin and password admin on the client 1_client and type token from http://invalid:3000
        And I authorize the client
        Then the response status code should be 400
        And the JSON node "error" should be equal to "redirect_uri_mismatch"

    @authorize
    Scenario: As a user I want to authorize client
        When I authenticate with the user admin and password admin on the client 1_client and type token from http://localhost:3000
        And I authorize the client
        Then the current url should match /^http:\/\/localhost:3000#state=&access_token=[\w]+&expires_in=[\d]+&token_type=[\w]+$/

    @deauthorize
    Scenario: As a user I want to deauthorize client
        When I authenticate with the user admin and password admin on the client 1_client and type token from http://localhost:3000
        And I deauthorize the client
        Then the current url should equal to http://localhost:3000#error=access_denied&error_description=The+user+denied+access+to+your+application

    @login
    Scenario: As a user I want to login with html form
        When I authenticate with the user admin and password admin on the client 1_client and type token from http://localhost:3000
        And I authorize the client
        Then I take the token from current url
        And I visit login page
        Then the response should contain "Logged in as admin"

    @logout
    Scenario: As a user I want to logout
        Given I am authenticated with the user admin and password admin on the client 1_client from http://localhost:3000
        When I visit login page
        Then the response should contain "Logged in as admin"
        Then I click Log out link
        Then the response should contain "Log in"

    @authenticate
    Scenario: As a user I want to authenticated with client id and client secret
        When I try to send a "POST" request to "/oauth/v2/token" with json body:
        """
            {
                "client_id": "1_client",
                "client_secret": "secret",
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

    @authenticate
    Scenario: Authenticated with token
        Given I am authenticated with token admin_token
