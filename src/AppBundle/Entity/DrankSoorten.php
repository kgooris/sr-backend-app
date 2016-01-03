<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


class DrankSoorten
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
     * @ORM\Column(type="string" )
     */
    private $omschrijving;
    
    /**
     * @ORM\Column(type="string")
     */
    private $foto;
    /**
     * @ORM\Column(type="int")
     */
    private $eenheid_id;
    /**
     * @ORM\Column(type="int")
     */
    private $smspositieid;
    /**
     * @ORM\Column(type="int")
     */
    private $stock;
    

}