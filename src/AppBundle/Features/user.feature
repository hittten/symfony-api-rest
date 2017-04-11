@user
Feature: User Resource
    In order to manage users
    I need to create, read, update, and delete a user

    Background:
        Given the database is clean once
        Given the following AppBundle/DataFixtures/ORM/fixtures.yml fixtures exists once in database

    @validation @blank
    Scenario: I can not register without required data
        Given I am authenticated with token token_client_web
        When I try to send a "POST" request to "/users"
        Then the response status code should be 400
        And the JSON node "errors[0].context" should be equal to "fos_user_registration_form.email"
        And the JSON node "errors[0].code" should be equal to "fos_user.email.blank"
        And the JSON node "errors[1].context" should be equal to "fos_user_registration_form.username"
        And the JSON node "errors[1].code" should be equal to "fos_user.username.blank"
        And the JSON node "errors[2].context" should be equal to "fos_user_registration_form.plainPassword.first"
        And the JSON node "errors[2].code" should be equal to "fos_user.password.blank"
        And the JSON node "errors[3].context" should be equal to "fos_user_registration_form.firstName"
        And the JSON node "errors[3].code" should be equal to "fos_user.first_name.blank"
        And the JSON node "errors[4].context" should be equal to "fos_user_registration_form.lastName"
        And the JSON node "errors[4].code" should be equal to "fos_user.last_name.blank"

    @validation @extra_fields
    Scenario: I can not register with extra data
        Given I am authenticated with token token_client_web
        When I try to send a "POST" request to "/users" with json body:
        """
        {
            "invalid": "value"
        }
        """
        Then the response status code should be 400
        And the JSON node "errors[0].code" should be equal to "fos_user.form.extra_fields"

    @validation @short
    Scenario: I can not register with short data
        Given I am authenticated with token token_client_web
        When I try to send a "POST" request to "/users" with json body:
        """
        {
            "firstName": "1",
            "lastName": "1",
            "email": "u@d.com",
            "username": "1",
            "plainPassword": {
                "first": "1",
                "second": "1"
            }
        }
        """
        Then the response status code should be 400
        And the JSON node "errors[0].context" should be equal to "fos_user_registration_form.username"
        And the JSON node "errors[0].code" should be equal to "fos_user.username.short"
        And the JSON node "errors[1].context" should be equal to "fos_user_registration_form.plainPassword.first"
        And the JSON node "errors[1].code" should be equal to "fos_user.password.short"
        And the JSON node "errors[2].context" should be equal to "fos_user_registration_form.firstName"
        And the JSON node "errors[2].code" should be equal to "fos_user.first_name.short"
        And the JSON node "errors[3].context" should be equal to "fos_user_registration_form.lastName"
        And the JSON node "errors[3].code" should be equal to "fos_user.last_name.short"

    @validation @long
    Scenario: I can not register with long data
        Given I am authenticated with token token_client_web
        When I try to send a "POST" request to "/users" with json body:
        """
        {
            "firstName": "_abcdef_10_abcdef_20_abcdef_30_abcdef_40_abcdef_50_abcdef_60_abcdef_70",
            "lastName": "_abcdef_10_abcdef_20_abcdef_30_abcdef_40_abcdef_50_abcdef_60_abcdef_70",
            "email": "u_abcdef_10_abcdef_20_abcdef_30_abcdef_40_abcdef_50_abcdef_60_abcdef_70_abcdef_80_abcdef_90_abcde_100_abcde_110_abcde_120_abcde_130_abcde_140_abcde_150_abcde_160_abcde_170_abcde_180_abcde_190@d.com",
            "username": "_abcdef_10_abcdef_20_abcdef_30_abcdef_40_abcdef_50_abcdef_60_abcdef_70_abcdef_80_abcdef_90_abcde_100_abcde_110_abcde_120_abcde_130_abcde_140_abcde_150_abcde_160_abcde_170_abcde_180_abcde_190",
            "plainPassword": {
                "first": "1234",
                "second": "1234"
            }
        }
        """
        Then the response status code should be 400
        And the JSON node "errors[0].context" should be equal to "fos_user_registration_form.email"
        And the JSON node "errors[0].code" should be equal to "fos_user.email.long"
        And the JSON node "errors[1].context" should be equal to "fos_user_registration_form.username"
        And the JSON node "errors[1].code" should be equal to "fos_user.username.long"
        And the JSON node "errors[2].context" should be equal to "fos_user_registration_form.firstName"
        And the JSON node "errors[2].code" should be equal to "fos_user.first_name.long"
        And the JSON node "errors[3].context" should be equal to "fos_user_registration_form.lastName"
        And the JSON node "errors[3].code" should be equal to "fos_user.last_name.long"

    @validation @invalid
    Scenario: I can not register with invalid data
        Given I am authenticated with token token_client_web
        When I try to send a "POST" request to "/users" with json body:
        """
        {
            "firstName": "first name",
            "lastName": "last name",
            "username": "username",
            "email": "invalid",
            "plainPassword": {
                "first": "1234",
                "second": "1234"
            }
        }
        """
        Then the response status code should be 400
        And the JSON node "errors[0].context" should be equal to "fos_user_registration_form.email"
        And the JSON node "errors[0].code" should be equal to "fos_user.email.invalid"

    @validation @already_used
    Scenario: I can not register with already used data
        Given I am authenticated with token token_client_web
        When I try to send a "POST" request to "/users" with json body:
        """
        {
            "firstName": "first name",
            "lastName": "last name",
            "username": "registered",
            "email": "registered@example.com",
            "plainPassword": {
                "first": "1234",
                "second": "1234"
            }
        }
        """
        Then the response status code should be 400
        And the JSON node "errors[0].context" should be equal to "fos_user_registration_form.email"
        And the JSON node "errors[0].code" should be equal to "fos_user.email.already_used"
        And the JSON node "errors[1].context" should be equal to "fos_user_registration_form.username"
        And the JSON node "errors[1].code" should be equal to "fos_user.username.already_used"

    @validation @mismatch
    Scenario: I can not register with mismatch data
        Given I am authenticated with token token_client_web
        When I try to send a "POST" request to "/users" with json body:
        """
        {
            "firstName": "first name",
            "lastName": "last name",
            "username": "user",
            "email": "user@example.com",
            "plainPassword": {
                "first": "1234",
                "second": "5678"
            }
        }
        """
        Then the response status code should be 400
        And the JSON node "errors[0].context" should be equal to "fos_user_registration_form.plainPassword.first"
        And the JSON node "errors[0].code" should be equal to "fos_user.password.mismatch"

#    @serializer
#    Scenario: I can not see all my data
#        Given I am authenticated with token token_web_user_registered
#        When I try to send a "GET" request to "/me"
#        Then the JSON node first_name should be equal to Registered
#        Then the JSON node last_name should be equal to User
#        Then the JSON node email should be equal to "registered@domain.com"

    @security
    Scenario: I can not register an user without client token
        When I try to send a "POST" request to "/users"
        Then the response status code should be 401
        Then the JSON node error should be equal to access_denied
        Then the JSON node error_description should be equal to "OAuth2 authentication required"

    @create
    Scenario: As a user I want to register
        Given I am authenticated with token token_client_web
        When I try to send a "POST" request to "/users" with json body:
        """
        {
            "firstName": "First name",
            "lastName": "Last name",
            "email": "new_user@domain.com",
            "username": "new_user",
            "plainPassword": {
                "first": "new_user",
                "second": "new_user"
            }
        }
        """
        Then the response status code should be 200
        And the JSON node first_name should be equal to the string "First name"
        And the JSON node last_name should be equal to the string "Last name"
        And the JSON node email should be equal to the string "new_user@domain.com"
        And the JSON node enabled should be false
        Then print mail messages in lynx format
        Given I received a "Welcome new_user!" email to "new_user@domain.com" from "webmaster@example.com"
        Then the email body should contain /\/oauth\/v2\/user\/register\/confirm\/[\w-]+/ link pattern

    @activate
    Scenario: As a user I want to activated my account
        When I am on "https://api.symfony.com/oauth/v2/user/register/confirm/confirmation_token_user_unconfirmed"
        Then the response should not be in JSON
        And the response status code should be 200
        And the page should contain "Congrats unconfirmed, your account is now activated."
        Given I am authenticated with token token_user_web_unconfirmed
        And I try to send a "GET" request to "/me"
        And the JSON node enabled should be true

    @read @serializer
    Scenario: As a user I want to get my data
        Given I am authenticated with token token_user_web_registered
        When I try to send a "GET" request to "/me"
        Then the JSON node id should be equal to 2
        And the JSON node first_name should be equal to Registered
        And the JSON node last_name should be equal to User
        And the JSON node username should be equal to "registered"
        And the JSON node email should be equal to "registered@example.com"
        And the JSON node "roles[0]" should be equal to ROLE_USER
        And the JSON node created_at should exist
        And the JSON node updated_at should exist

    @update
    Scenario: As a user I want to update my data
        Given I am authenticated with token token_user_web_update
        When I try to send a "PATCH" request to "/me" with json body:
        """
        {
            "firstName": "updated",
            "lastName": "updated",
            "username": "updated"
        }
        """
        Then print last JSON response
        Then the response status code should be 200
        And the JSON node first_name should be equal to updated
        And the JSON node last_name should be equal to updated
        And the JSON node username should be equal to updated
        And the JSON node email should be equal to "registered@example.com"


    Scenario: As a user I want to update my password

    Scenario: As a user I want to recovery my password

    Scenario: As a user I want to unregister
