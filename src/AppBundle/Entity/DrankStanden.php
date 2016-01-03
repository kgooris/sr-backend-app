<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


class DrankStanden
{
      /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true, ,length=50)
     */
    private $naam;

    /**
     * @ORM\Column(type="integer", length=8 )
     */
    private $smscode;
    
       

}