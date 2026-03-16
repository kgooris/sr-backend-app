<?php

namespace AppBundle\Entity;

use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\Common\PropertyChangedListener;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Order;


/**
 *
 * @author kristof
 * @ORM\Entity
 * @ORM\Table(name="orderdrank")
 */
class OrderDrank
{

    /**
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\Column(type="integer")
	 */
	private $id;
	
		
	/**
	 * @ORM\ManyToOne(targetEntity="DrankSoort", inversedBy="od")
	 * @ORM\JoinColumn(name="d_id", referencedColumnName="id")
	 * */
	private $drank;
	
	
	
	/**
	 *
	 * @ORM\Column(type="integer")
	 */
	private $hoeveel;
	
	
	  /**
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="od")
     * @ORM\JoinColumn(name="o_id", referencedColumnName="id")
     *
       * @var Order
       */
    private $ord;

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
     * Set hoeveel
     *
     * @param integer $hoeveel
     *
     * @return OrderDrank
     */
    public function setHoeveel($hoeveel)
    {
        if ($hoeveel != $this->hoeveel)
        {
            // Order = Mainstock changes

            if (is_object($this->getOrd()) && $this->getOrd()->getOrdertype()->getSmstypeId() == 4)
            {
                // changes quantity is & update the stock
                $chValue = $hoeveel - $this->hoeveel;
                $this->getDrank()->addToStock($chValue);
                
            }
            $this->hoeveel = $hoeveel;
        }


        return $this;
    }

    /**
     * Get hoeveel
     *
     * @return integer
     */
    public function getHoeveel()
    {
        return $this->hoeveel;
    }

    /**
     * Set drank
     *
     * @param \AppBundle\Entity\DrankSoort $drank
     *
     * @return OrderDrank
     */
    public function setDrank(\AppBundle\Entity\DrankSoort $drank = null)
    {

        if ($drank != $this->drank)
        {
            // drank is not the same, update both the loosing drank stock and the winning drank stock

            if (is_object($this->getOrd()) && $this->getOrd()->getOrdertype()->getSmstypeId() == 4)
            {
                // remove stock for removed drank
                $this->getDrank()->removeFromStock($this->getHoeveel());
                // add stock on winning drank
                $drank->addToStock($this->getHoeveel());
            }
        }
        
        
        $this->drank = $drank;

        return $this;
    }

    /**
     * Get drank
     *
     * @return \AppBundle\Entity\DrankSoort
     */
    public function getDrank()
    {
        return $this->drank;
    }

    /**
     * Set ord
     *
     * @param Order $ord
     *
     * @return OrderDrank
     */
    //public function setOrd(\AppBundle\Entity\Order $ord = null)
    public function setOrd(Order $ord)
    {
        $this->ord = $ord;

        return $this;
    }

    /**
     * Get ord
     *
     * @return Order
     */
    public function getOrd()
    {
        return $this->ord;
    }
}
