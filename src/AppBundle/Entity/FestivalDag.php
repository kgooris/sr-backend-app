<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @author kristof
 * @ORM\Entity
 * @ORM\Table(name="festivaldag")
 */
class FestivalDag
{
      /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", unique=true)
     */
    private $festdate;

    /**
     * @ORM\Column(type="smallint", length=1, unique=true)
     */
    private $festday;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $festinfo;

    /**
     * @ORM\OneToMany(targetEntity="Order" , mappedBy="festivaldag")
     * */
    protected $ord;

    /**
     * @ORM\Column(type="boolean", options={"default":0}, nullable=false)
     */
    private $festactive;





    /**
     * @return mixed
     */
    public function getFestActive()
    {
        return $this->festactive;
    }

    /**
     * @param mixed $festactive
     */
    public function setFestActive($festactive)
    {
        $this->festactive = $festactive;
    }


    public function __toString()
    {
        $returnvalue = $this->getFestday()." - ".$this->getFestdate()->format("d-M-y");

        return $returnvalue;
    }
    
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set festdate
     *
     * @param \DateTime $festdate
     *
     * @return FestivalDagen
     */
    public function setFestdate($festdate)
    {
        $this->festdate = $festdate;

        return $this;
    }

    /**
     * Get festdate
     *
     * @return \DateTime
     */
    public function getFestdate()
    {
        return $this->festdate;
    }

    /**
     * Set festday
     *
     * @param integer $festday
     *
     * @return FestivalDagen
     */
    public function setFestday($festday)
    {
        $this->festday = $festday;

        return $this;
    }

    /**
     * Get festday
     *
     * @return integer
     */
    public function getFestday()
    {
        return $this->festday;
    }

    /**
     * Set festinfo
     *
     * @param string $festinfo
     *
     * @return FestivalDagen
     */
    public function setFestinfo($festinfo)
    {
        $this->festinfo = $festinfo;

        return $this;
    }

    /**
     * Get festinfo
     *
     * @return string
     */
    public function getFestinfo()
    {
        return $this->festinfo;
    }
}
