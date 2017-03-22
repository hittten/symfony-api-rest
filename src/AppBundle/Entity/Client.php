<?php

namespace AppBundle\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;

/**
 * Class Client
 *
 * @package AppBundle\Entity
 *
 * @author Gilberto López Ambrosino <gilberto.amb@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * @var int
     */
    protected $id;
}
