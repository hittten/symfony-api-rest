<?php

namespace AppBundle\Entity;

use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;

/**
 * Class AuthCode
 *
 * @package AppBundle\Entity
 *
 * @author Gilberto López Ambrosino <gilberto.amb@gmail.com>
 */
class AuthCode extends BaseAuthCode
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var User
     */
    protected $user;
}
