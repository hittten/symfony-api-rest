<?php

namespace AppBundle\Entity;

use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;

/**
 * Class AccessToken
 *
 * @package AppBundle\Entity
 *
 * @author Gilberto López Ambrosino <gilberto.amb@gmail.com>
 */
class AccessToken extends BaseAccessToken
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
