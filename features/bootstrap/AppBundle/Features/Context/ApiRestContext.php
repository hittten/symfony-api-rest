<?php

namespace AppBundle\Features\Context;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Element\DocumentElement;
use Behat\Mink\Exception\ExpectationException;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * Class ApiRestContext
 *
 * @package AppBundle\Features\Context
 *
 * @link https://github.com/Behat/Symfony2Extension/blob/master/doc/index.rst
 *
 * @author Gilberto López Ambrosino <gilberto.amb@gmail.com>
 */
class ApiRestContext extends RawMinkContext implements KernelAwareContext
{
    use KernelDictionary;

    /**
     * @var string
     */
    protected $oauthTokenUrl;

    /**
     * @var string
     */
    protected $oauthAuthUrl;

    /**
     * @var string
     */
    protected $oauthAuthLoginUrl;

    /**
     * ApiRestContext constructor.
     *
     * @param string $oauthTokenUrl
     * @param string $oauthAuthUrl
     * @param string $oauthAuthLoginUrl
     */
    public function __construct($oauthTokenUrl, $oauthAuthUrl, $oauthAuthLoginUrl)
    {
        $this->oauthTokenUrl = $oauthTokenUrl;
        $this->oauthAuthUrl = $oauthAuthUrl;
        $this->oauthAuthLoginUrl = $oauthAuthLoginUrl;
    }

    /**
     * @return Client
     *
     * @throws \Exception
     */
    public function getClient()
    {
        $driver = $this->getSession()->getDriver();

        if (!$driver instanceof BrowserKitDriver) {
            throw new \Exception(sprintf('Expects BrowserKitDriver instance, %s given', get_class($driver)));
        }

        return $driver->getClient();
    }

    /**
     * @When /^I authenticate with the user (.*) and password (.*) on the client (.*) and type (.*) from (.*)$/
     *
     * @param string $username
     * @param string $password
     * @param string $clientId
     * @param string $type
     * @param string $redirectUri
     *
     * @return DocumentElement
     */
    public function authenticateInOauthForm($username, $password, $clientId, $type, $redirectUri)
    {
        $browser = $this->getSession();

        $url = $this->locatePath($this->oauthAuthUrl).'?'.http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => $type,
        ]);

        $browser->visit($url);
        $browser->getPage()->fillField('username', $username);
        $browser->getPage()->fillField('password', $password);
        $browser->getPage()->pressButton('_submit');

        return $browser->getPage();
    }

    /**
     * @Given /^I am authenticated with the user (.*) and password (.*) on the client (.*) from (.*)$/
     *
     * @param string $username
     * @param string $password
     * @param string $clientId
     * @param string $redirectUri
     */
    public function authenticateInOauth($username, $password, $clientId, $redirectUri)
    {
        $this->authenticateInOauthForm($username, $password, $clientId, 'token', $redirectUri);
        $this->authorizeClient();
        $this->setOauthTokenFromCurrentUrl();
    }

    /**
     * @When /^I visit login page$/
     *
     * @return DocumentElement
     */
    public function loginPage()
    {
        $browser = $this->getSession();
        $browser->visit($this->locatePath($this->oauthAuthLoginUrl));

        return $browser->getPage();
    }

    /**
     * @Then /^I take the token from current url$/
     *
     * @throws \Exception
     */
    public function setOauthTokenFromCurrentUrl()
    {
        $url = $this->getSession()->getCurrentUrl();
        parse_str(parse_url($url, PHP_URL_FRAGMENT), $queryParams);
        if (null == $queryParams) {
            parse_str(parse_url($url, PHP_URL_QUERY), $queryParams);
        }
        if (isset($queryParams['error'])) {
            throw new \Exception("Error: {$queryParams['error']}");
        }
        if (!isset($queryParams['access_token'])) {
            throw new \Exception("Can not take token from $url");
        }

        $this->setOauthToken($queryParams['access_token']);
    }

    /**
     * @Given /^I am authenticated with token (.*)$/
     *
     * @param string $token
     */
    public function setOauthToken($token)
    {
        $this->getClient()->setServerParameter('HTTP_AUTHORIZATION', "Bearer $token");
    }

    /**
     * @When /^I try to send a "([^"]*)" request to "([^"]*)"$/
     *
     * @param string $method
     * @param string $url
     *
     * @return DocumentElement
     */
    public function sendRequest($method, $url)
    {
        return $this->sendRequestWithJsonBody($method, $url);
    }

    /**
     * @When /^I try to send a "([^"]*)" request to "([^"]*)" with json body:$/
     *
     * @param string       $method
     * @param string       $url
     * @param PyStringNode $body
     *
     * @return DocumentElement
     */
    public function sendRequestWithJsonBody($method, $url, PyStringNode $body = null)
    {
        $client = $this->getClient();
        $client->request(
            $method,
            $this->locatePath($url),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $body !== null ? $body->getRaw() : null
        );

        $client->followRedirects();

        return $this->getSession()->getPage();
    }

    /**
     * @Then /^I authorize the client$/
     *
     * @throws \Exception
     */
    public function authorizeClient()
    {
        $this->getSession()->getPage()->pressButton("Allow");
    }

    /**
     * @Then /^I deauthorize the client$/
     *
     * @throws \Exception
     */
    public function deauthorizeClient()
    {
        $this->getSession()->getPage()->pressButton("Deny");
    }

    /**
     * @Then /^I click (.*) link$/
     *
     * @param string $link
     *
     * @throws \Exception
     */
    public function clickLink($link)
    {
        $this->getSession()->getPage()->clickLink($link);
    }

    /**
     * @Then /^the page should contain "([^"]*)"$/
     *
     * @param string $text
     */
    public function thePageContains($text)
    {
        $this->assertSession()->responseContains($text);
    }

    /**
     * @Then /^the current url should match (.*)$/
     *
     * @param string $pattern
     */
    public function theCurrentUrlShouldMatch($pattern)
    {
        $currentUrl = $this->getSession()->getCurrentUrl();
        $message = sprintf('Current URL "%s" does not match the regex "%s".', $currentUrl, $pattern);
        $this->assert(preg_match($pattern, $currentUrl), $message);
    }

    /**
     * @Then /^the current url should equal to (.*)$/
     *
     * @param string $url
     */
    public function theCurrentUrlShouldEqualTo($url)
    {
        $currentUrl = $this->getSession()->getCurrentUrl();
        $message = sprintf('Current URL is "%s", but "%s" expected.', $currentUrl, $url);
        $this->assert(($currentUrl == $url), $message);
    }

    private function assert($condition, $message)
    {
        if ($condition) {
            return;
        }

        throw new ExpectationException($message, $this->getSession()->getDriver());
    }
}
