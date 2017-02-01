<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\SecurityExtraBundle\Annotation as Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AdminUsersController
 * @Security\PreAuthorize("hasRole('ROLE_SUPER_ADMIN')")
 * @package AppBundle\Controller\Admin
 *
 * @author Gilberto López Ambrosino <gilberto.amb@gmail.com>
 */
class AdminUsersController extends FOSRestController
{
    /**
     * @return User[]|array
     */
    public function getUsersAction()
    {
//        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $manager = $this->getDoctrine()->getManager();

        return $manager->getRepository('AppBundle:User')->findAll();
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function getUserAction($username)
    {
        $manager = $this->getDoctrine()->getManager();
        $user = $manager->getRepository('AppBundle:User')->findOneBy(['username' => $username]);

        if (null === $user) {
            throw new NotFoundHttpException('Resource not found');
        }

        return $user;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function deleteUserAction($username)
    {
        $user = $this->getUserAction($username);
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($user);
        $manager->flush();

        return $user;
    }
}
