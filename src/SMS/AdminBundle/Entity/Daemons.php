<?php

namespace SMS\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Daemons
 *
 * @ORM\Table(name="daemons")
 * @ORM\Entity
 */
class Daemons
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
     * @var string
     *
     * @ORM\Column(name="Start", type="text", length=65535, nullable=false)
     */
    private $start;

    /**
     * @var string
     *
     * @ORM\Column(name="Info", type="text", length=65535, nullable=false)
     */
    private $info;


}

