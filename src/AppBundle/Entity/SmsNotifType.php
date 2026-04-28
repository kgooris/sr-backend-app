<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 *
 * @author kristof
 * @ORM\Entity
 * @ORM\Table(name="smsnotiftype")
 */
class SmsNotifType
{
      /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true ,length=50)
     */
    private $naam;

    /**
     * @ORM\Column(type="string", unique=true ,length=50)
     */
    private $omschrijving;
    
    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\OrderType")
     * @ORM\JoinTable(name="smsnotiftype_ordertypes",
     *      joinColumns={@ORM\JoinColumn(name="smsnotif_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="ordertype_id", referencedColumnName="id")}
     * )"
     */
    protected $ordertypes;

    public function __toString()
    {

        return $this->getNaam();
    }
    public function __construct($name = '')
    {
        $this->name = $name;
        $this->ordertypes = new ArrayCollection();
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
     * Set naam
     *
     * @param string $naam
     *
     * @return DrankEenheid
     */
    public function setNaam($naam)
    {
        $this->naam = $naam;

        return $this;
    }

    /**
     * Get naam
     *
     * @return string
     */
    public function getNaam()
    {
        return $this->naam;
    }

    /**
     * @return mixed
     */
    public function getOmschrijving()
    {
        return $this->omschrijving;
    }

    /**
     * @param mixed $omschrijving
     */
    public function setOmschrijving($omschrijving)
    {
        $this->omschrijving = $omschrijving;
    }

    /**
     * @return ArrayCollection
     */
    public function getOrdertypes()
    {
        return $this->ordertypes;
    }

    /**
     * @param ArrayCollection $ordertypes
     */
    public function setOrdertypes($ordertypes)
    {
        $this->ordertypes = $ordertypes;
    }
    /**
     * Add od
     *
     * @param \AppBundle\Entity\OrderType $ordertype
     *
     * @return Order
     */
    public function addOrderType(\AppBundle\Entity\OrderType $ordertype)
    {

        $this->ordertypes[] = $ordertype;
        return $this;
    }

    /**
     * Remove od
     *
     * @param \AppBundle\Entity\OrderType $ordertype
     */
    public function removeOrderType(\AppBundle\Entity\OrderType $ordertype)
    {
        

        $this->ordertypes->removeElement($ordertype);
    }
    
}
