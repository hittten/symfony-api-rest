<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Loader\NativeLoader;

/**
 * Class LoadFixtures
 * @package AppBundle\DataFixtures\ORM
 *
 * @link https://github.com/nelmio/alice#table-of-contents
 *
 * @author Gilberto López Ambrosino <gilberto.amb@gmail.com>
 */
class LoadFixtures implements FixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $loader = new NativeLoader();
        $objectsSet = $loader->loadFile(__DIR__.'/fixtures.yml');

        foreach ($objectsSet->getObjects() as $object) {
            $manager->persist($object);
        }

        $manager->flush();
    }
}
