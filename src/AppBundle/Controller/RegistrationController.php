<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RegistrationController
 *
 * @package AppBundle\Controller
 *
 * @author Gilberto López Ambrosino <gilberto.amb@gmail.com>
 */
class RegistrationController extends FOSRestController
{
    /**
     * @param Request $request
     *
     * @return \FOS\UserBundle\Model\UserInterface|\Symfony\Component\Form\FormInterface
     */
    public function registerAction(Request $request)
    {
//        return $this->get('translator')->trans('fos_user.first_name.blank', [], 'validators');
        //rest logic
        $request->request->set('fos_user_registration_form', $request->request->all());
        foreach ($request->request->keys() as $key) {
            if ($key !== 'fos_user_registration_form') {
                $request->request->remove($key);
            }
        }
        //end rest logic

        /** @var $formFactory FactoryInterface */
        $formFactory = $this->get('fos_user.registration.form.factory');
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

//        if (null !== $event->getResponse()) {
//            return $event->getResponse();
//        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $response = new Response();
                }

                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $user;
            }

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

//            if (null !== $response = $event->getResponse()) {
//                return $response;
//            }
        }

        return $form;
    }
}
