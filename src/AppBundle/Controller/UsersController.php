<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class UsersController
 *
 * @package AppBundle\Controller
 *
 * @author Gilberto López Ambrosino <gilberto.amb@gmail.com>
 */
class UsersController extends FOSRestController
{
    /**
     * @return User[]|array
     */
    public function getUsersAction()
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
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
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $manager = $this->getDoctrine()->getManager();
        $user = $manager->getRepository('AppBundle:User')->findOneBy(['username' => $username]);

        if (null === $user || !$user->isEnabled()) {
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
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUserAction($username);
        $user->setEnabled(false);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->flush();

        return $user;
    }

    /**
     * @return User
     */
    public function getMeAction()
    {
        return $this->getUser();
    }

    /**
     * @param Request $request
     *
     * @return Request
     */
    public function postUsersAction(Request $request)
    {
        return $request;
    }
}
