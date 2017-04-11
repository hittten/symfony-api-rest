<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class TaskController
 *
 * @package AppBundle\Controller
 *
 * @author Gilberto López Ambrosino <gilberto.amb@gmail.com>
 */
class TaskController extends FOSRestController
{
    /**
     * @return Task[]|array
     */
    public function getTasksAction()
    {
        $manager = $this->getDoctrine()->getManager();

        return $manager->getRepository('AppBundle:Task')->findAll();
    }

    /**
     * @param string $name
     *
     * @return Task
     */
    public function getTaskAction($name)
    {
        $manager = $this->getDoctrine()->getManager();
        $task = $manager->getRepository('AppBundle:Task')->findOneBy(['name' => $name]);

        if (null === $task || !$task->isEnabled()) {
            throw new NotFoundHttpException('Resource not found');
        }

        return $task;
    }

    /**
     * @param Request $request
     * @return Task|Form
     */
    public function postTasksAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm('AppBundle\Form\TaskType', $task);

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $form;
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($task);
        $manager->flush();

        return $task;
    }

    /**
     * @param string $name
     *
     * @return Task
     */
    public function deleteTaskAction($name)
    {
        $task = $this->getTaskAction($name);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($task);
        $manager->flush();

        return $task;
    }
}
