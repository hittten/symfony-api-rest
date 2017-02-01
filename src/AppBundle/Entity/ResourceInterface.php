<?php

namespace AppBundle\Entity;

/**
 * Interface ResourceInterface
 *
 * @package AppBundle\Entity
 * @author Gilberto López Ambrosino <gilberto.amb@gmail.com>
 */
interface ResourceInterface
{
    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();
}
