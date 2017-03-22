<?php

namespace AppBundle\Features\Context;

use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Nelmio\Alice\Loader\NativeLoader;

/**
 * Class FixtureContext
 *
 * @package AppBundle\Features\Context
 *
 * @link https://github.com/Behat/Symfony2Extension/blob/master/doc/index.rst
 *
 * @author Gilberto López Ambrosino <gilberto.amb@gmail.com>
 */
class FixtureContext implements KernelAwareContext
{
    use KernelDictionary;

    /**
     * @var bool
     */
    protected static $areLoaded;

    /**
     * @BeforeFeature
     */
    public static function setupFeature()
    {
        self::$areLoaded = false;
    }

    /**
     * @Given /^the following (.*) fixtures exists in database$/
     *
     * @param string $file
     */
    public function loadFixtures($file)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $manager = $doctrine->getManager();
        $loader = new NativeLoader();
        $objectsSet = $loader->loadFile(__DIR__.'/../../../../../src/'.$file);

        foreach ($objectsSet->getObjects() as $object) {
            $manager->persist($object);
        }

        $manager->flush();

        self::$areLoaded = true;
    }

    /**
     * @Given /^the following (.*) fixtures exists once in database$/
     *
     * @param string $file
     */
    public function loadOnceFixtures($file)
    {
        if (false === self::$areLoaded) {
            $this->loadFixtures($file);
        }
    }
}
