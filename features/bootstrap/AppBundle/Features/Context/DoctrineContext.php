<?php

namespace AppBundle\Features\Context;

use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class DoctrineContext
 *
 * @package AppBundle\Features\Context
 *
 * @link https://github.com/Behat/Symfony2Extension/blob/master/doc/index.rst
 *
 * @author Gilberto López Ambrosino <gilberto.amb@gmail.com>
 */
class DoctrineContext implements KernelAwareContext
{
    use KernelDictionary;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var bool
     */
    protected static $isClean;

    /**
     * @BeforeFeature
     */
    public static function setupFeature()
    {
        self::$isClean = false;
    }

    /**
     * @BeforeScenario
     */
    public function registerApplication()
    {
        $this->application = new Application($this->getKernel());
        $this->application->setAutoExit(false);
    }

    /**
     * @Given /^the database is clean$/
     */
    public function cleanDatabase()
    {
        $this->dropDatabase();
        $this->createDatabase();
        $this->updateSchema();

        self::$isClean = true;
    }

    /**
     * @Given /^the database is clean once$/
     */
    public function cleanDatabaseOnce()
    {
        if (false === self::$isClean) {
            $this->cleanDatabase();
        }
    }

    /**
     * @Given /^the database does not exists$/
     */
    public function dropDatabase()
    {
        $this->runCommand('doctrine:database:drop', ['--force' => true, '--if-exists' => true]);
    }

    /**
     * @Given /^the database does exists$/
     */
    public function createDatabase()
    {
        $this->runCommand('doctrine:database:create', ['--if-not-exists' => true]);
    }

    /**
     * @Given /^the database schema is update$/
     */
    public function updateSchema()
    {
        $this->runCommand('doctrine:schema:update', ['--force' => true]);
    }

    private function runCommand($name, array $arguments = [])
    {
        $arguments = array_merge([
            'command' => $name,
        ], $arguments);

        $input = new ArrayInput($arguments);

        $output = new BufferedOutput();
        $this->application->run($input, $output);
        $result = $output->fetch();

        if ((bool) preg_match('/exception/', $result)) {
            throw new \Exception($result);
        }
    }
}
