<?php

namespace AppBundle\Model\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
//use FOS\UserBundle\Model\UserManager as BaseUserManager;
use FOS\UserBundle\Util\CanonicalFieldsUpdater;
use FOS\UserBundle\Util\PasswordUpdaterInterface;

/**
 * Class UserManager
 *
 * @package AppBundle\Model\Manager
 * @author Gilberto López Ambrosino <gilberto.amb@gmail.com>
 */
class UserManager extends BaseUserManager
{
    /**
     * UserManager constructor.
     *
     * @param PasswordUpdaterInterface $passwordUpdater
     * @param CanonicalFieldsUpdater   $canonicalFieldsUpdater
     * @param ObjectManager            $objectManager
     * @param string                   $class
     */
    public function __construct(PasswordUpdaterInterface $passwordUpdater, CanonicalFieldsUpdater $canonicalFieldsUpdater, ObjectManager $objectManager, $class)
    {
        parent::__construct($passwordUpdater, $canonicalFieldsUpdater, $objectManager, $class);
    }
}
