<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class NotFountController
 *
 * @package AppBundle\Controller
 *
 * @author Gilberto López Ambrosino <gilberto.amb@gmail.com>
 */
class NotFountController
{
    /**
     * @throws NotFoundHttpException
     */
    public function indexAction()
    {
        throw new NotFoundHttpException('Not Found');
    }
}
