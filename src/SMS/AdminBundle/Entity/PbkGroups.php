<?php

namespace SMS\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PbkGroups
 *
 * @ORM\Table(name="pbk_groups")
 * @ORM\Entity
 */
class PbkGroups
{
    /**
     * @var string
     *
     * @ORM\Column(name="Name", type="text", length=65535, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


}

