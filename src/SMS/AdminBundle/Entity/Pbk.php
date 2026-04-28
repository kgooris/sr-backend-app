<?php

namespace SMS\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pbk
 *
 * @ORM\Table(name="pbk")
 * @ORM\Entity
 */
class Pbk
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="GroupID", type="integer", nullable=false)
     */
    private $groupid = '-1';

    /**
     * @var string
     *
     * @ORM\Column(name="Name", type="text", length=65535, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="Number", type="text", length=65535, nullable=false)
     */
    private $number;


}

