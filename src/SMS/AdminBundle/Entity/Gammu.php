<?php

namespace SMS\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gammu
 *
 * @ORM\Table(name="gammu")
 * @ORM\Entity
 */
class Gammu
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="Version", type="integer", nullable=false)
     */
    private $version = '0';


}

