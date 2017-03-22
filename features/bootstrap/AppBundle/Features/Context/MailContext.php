<?php

namespace AppBundle\Features\Context;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Exception\ExpectationException;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Link;

/**
 * Class MailContext
 *
 * @package AppBundle\Features\Context
 *
 * @author Gilberto López Ambrosino <gilberto.amb@gmail.com>
 */
class MailContext extends RawMinkContext implements KernelAwareContext
{
    use KernelDictionary;

    /**
     * @var \Swift_Message
     */
    private $currentMessage;

    /**
     * @var Link
     */
    private $currentLink;

    /**
     * @return Client
     *
     * @throws \InvalidArgumentException
     */
    public function getClient()
    {
        $driver = $this->getSession()->getDriver();

        if (!$driver instanceof BrowserKitDriver) {
            throw new \InvalidArgumentException(sprintf('Expected BrowserKitDriver instance, "%s" instance obtained', get_class($driver)));
        }

        return $driver->getClient();
    }

    /**
     * @BeforeScenario
     */
    public function prepare()
    {
        $this->currentMessage = null;
        $this->getClient()->enableProfiler();
    }

    /**
     * @Then /^I should receive (\d+) emails?$/
     *
     * @param int $expected
     */
    public function assertEmailSent($expected)
    {
        $value = $this->getMailCollector()->getMessageCount();

        $this->assertExpected($expected, $value);
    }

    /**
     * @Then /^the subject of email number (\d+) should be (.*)$/
     *
     * @param integer $number
     * @param string  $expected
     */
    public function assertSubject($number, $expected)
    {
        $value = $this->getMessage(--$number)->getSubject();

        $this->assertExpected($expected, $value);
    }

    /**
     * @Given /^I received a "([^"]*)" email to "([^"]*)" from "([^"]*)" with:$/
     *
     * @param string       $subject
     * @param string       $email
     * @param string       $from
     * @param PyStringNode $text
     */
    public function assertEmailMessage($subject, $email, $from, PyStringNode $text)
    {
        $message = $this->getEmailMessage($subject, $email, $from);

        $crawler = new Crawler($message->getBody());
        $body = preg_replace('/\s+/u', ' ', $crawler->text());
        $text = preg_replace('/\s+/u', ' ', $text->getRaw());
        $pattern = '/'.preg_quote($text, '/').'/u';

        $this->assertExpectedMatch($pattern, $body);
    }

    /**
     * @Given /^I received a "([^"]*)" email to "([^"]*)" from "([^"]*)"$/
     *
     * @param string $subject
     * @param string $email
     * @param string $from
     *
     * @return \Swift_Message
     *
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function getEmailMessage($subject, $email, $from)
    {
        $collector = $this->getMailCollector();

        foreach ($collector->getMessages() as $message) {
            if (!$message instanceof \Swift_Message) {
                throw new \InvalidArgumentException(sprintf('Expects Swift_Message instance, "%s" given', get_class($message)));
            }

            if (!$message->getSubject() == $subject) {
                continue;
            }

            $correctRecipient = array_key_exists(
                $email,
                $message->getTo()
            );
            $headers = $message->getHeaders();
            $correctXToHeader = false;
            if ($headers->has('X-Swift-To')) {
                $correctXToHeader = array_key_exists(
                    $email,
                    $headers->get('X-Swift-To')->getFieldBodyModel()
                );
            }

            $correctXFromHeader = false;
            if ($headers->has('X-Swift-From')) {
                $correctXToHeader = array_key_exists(
                    $from,
                    $headers->get('X-Swift-From')->getFieldBodyModel()
                );
            }

            if (!$correctRecipient && !$correctXToHeader && !$correctXFromHeader) {
                continue;
            }

            return $this->currentMessage = $message;
        }

        throw new \Exception(sprintf('No message with "%s" subject sent to "%s"', $subject, $email), $this->getSession()->getDriver());
    }

    /**
     * @Then /^I click link from email body$/
     */
    public function clickLink()
    {
        $this->getSession()->visit($this->currentLink->getUri());
    }

    /**
     * @Then /^the email body should contain (.*) link (.*)$/
     *
     * @param string $pattern
     * @param string $by
     *
     * @return void
     * @throws \Exception
     */
    public function assertLinkBy($pattern, $by)
    {
        if (null === $this->currentMessage) {
            throw new \Exception('You must use "Given I received a subject email to email@example.com from from@example" first');
        }

        if ($by !== 'url' && $by !== 'pattern') {
            throw new \InvalidArgumentException('Invalid by argument. [url|pattern]');
        }

        $body = $this->currentMessage->getBody();
        $crawler = new Crawler($body, $this->getMinkParameter('base_url'));

        if ($by === 'url') {
            $pattern = '/'.preg_quote($pattern, '/').'/';
        }

        $nodeLinks = $crawler->filter('a')->reduce(function (Crawler $node) use ($pattern) {
            return ((boolean) preg_match($pattern, $node->link()->getUri()));
        });

        if ($nodeLinks->count() === 0) {
            throw new \Exception(sprintf('No links with "%s" pattern', $pattern));
        }

        $this->currentLink = $nodeLinks->links()[0];

        return;
    }

    /**
     * @Then /^print mail messages in (\w+) format$/
     *
     * @param string $bodyFormat
     *
     * @throws \InvalidArgumentException
     */
    public function printMail($bodyFormat)
    {
        if ($bodyFormat !== 'html' && $bodyFormat !== 'text' && $bodyFormat !== 'lynx') {
            throw new \InvalidArgumentException('Invalid format argument. [html|text|lynx]');
        }

        $collector = $this->getMailCollector();

        foreach ($collector->getMessages() as $message) {
            if (!$message instanceof \Swift_Message) {
                throw new \InvalidArgumentException(sprintf('Expects Swift_Message instance, "%s" instance obtained', get_class($message)));
            }

            foreach ((array) $message->getTo() as $email => $name) {
                echo "To: $name <$email>\n";
            }
            foreach ((array) $message->getFrom() as $email => $name) {
                echo "From: $name <$email>\n";
            }
            foreach ((array) $message->getCc() as $email => $name) {
                echo "CC: $name <$email>\n";
            }
            foreach ((array) $message->getBcc() as $email => $name) {
                echo "BCC: $name <$email>\n";
            }
            echo "Subject: {$message->getSubject()}\n\n";

            if ('html' === $bodyFormat) {
                echo "Body [HTML]: \n\n".print_r($message->getBody(), true);
            }

            if ('text' === $bodyFormat) {
                $crawler = new Crawler($message->getBody(), $this->getMinkParameter('base_url'));

                echo "Body [TEXT]: \n\n{$crawler->filter('body')->text()}";
            }

            if ('lynx' === $bodyFormat) { //TODO: make condition if lynx if not installed use text format.
                echo "Body [LYNX]: \n\n".shell_exec("echo '{$message->getBody()}' | lynx --dump --stdin");
            }
        }
    }

    private function getProfile()
    {
        return $this->getClient()->getProfile();
    }

    /**
     * @return MessageDataCollector
     *
     * @throws \InvalidArgumentException
     */
    private function getMailCollector()
    {
        $mailCollector = $this->getProfile()->getCollector('swiftmailer');

        if (!$mailCollector instanceof MessageDataCollector) {
            throw new \InvalidArgumentException(sprintf('Expects MessageDataCollector instance, "%s" instance obtained', get_class($mailCollector)));
        }

        return $mailCollector;
    }

    /**
     * @param $number
     *
     * @return \Swift_Message
     *
     * @throws \InvalidArgumentException
     */
    private function getMessage($number)
    {
        $messages = $this->getMailCollector()->getMessages();
        $message = $messages[$number];

        if (!$message instanceof \Swift_Message) {
            throw new \InvalidArgumentException(sprintf('Expects Swift_Message instance, "%s" instance obtained', get_class($message)));
        }

        return $message;
    }

    private function assertExpectedMatch($pattern, $value)
    {
        $this->assertMatch($pattern, $value, sprintf('Value "%s" do not match with "%s"', $value, $pattern));
    }

    private function assertExpected($expected, $value)
    {
        $this->assert(($expected === $value), sprintf('Expected "%s", but value is "%s"', $expected, $value));
    }

    private function assertMatch($pattern, $subject, $message)
    {
        $this->assert((bool) preg_match($pattern, $subject), $message);
    }

    private function assert($condition, $message)
    {
        if ($condition) {
            return;
        }

        throw new ExpectationException($message, $this->getSession()->getDriver());
    }
}
