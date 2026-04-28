<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use libphonenumber\PhoneNumber;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 *
 * @author kristof
 * @ORM\Entity
 * @ORM\Table(name="drankstand")
 * @ORM\HasLifecycleCallbacks()
 */
class DrankStand
{
      /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true,length=50)
     */
    private $naam;

    /**
     * @ORM\Column(type="integer", length=8, unique=true, nullable=false )
     */
    private $smscode;
    /**
     * @ORM\Column(type="phone_number", unique=false)
     * @AssertPhoneNumber(type="mobile")
     *
     */
    protected $gsm;
    /**
     * created Time/Date
     *
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;
    
    /**
     * updated Time/Date
     *
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    protected $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="Order" , mappedBy="drankstand")
     * */
    protected $ord;
    
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
     * @return DrankStand
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
     * Set smscode
     *
     * @ORM\PrePersist
     */
    public function setSmscode( LifecycleEventArgs $event)
    {
        
    	if ($this->smscode == 0)
    	{
	    	$em = $event->getEntityManager();
	    	
	    	while (true)
	    	{
	    		$id = mt_rand(10000000, 99999999);
	    		$item = $em->getRepository('AppBundle:DrankStand')->findBy(array(
	    				'smscode' => $id));
	    		 
	    		//$item = $em->find("AppBundle:DrankStand", $id);
	    		 
	    		if (!$item)
	    		{
	    			
	    			$this->smscode = $id;
	    			break;
	    		}
	    	}
    	}
    	
		
        return $this;
    }

    /**
     * Get smscode
     *
     * @return integer
     */
    public function getSmscode()
    {
        return $this->smscode;
    }

    
    /**
     * Set createdAt
     *
     * @ORM\PrePersist
    
     */
    public function setCreatedAt()
    {
    	$this->createdAt = new \DateTime();
    	$this->updatedAt = new \DateTime();
    
    }
    
    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
    	return $this->createdAt;
    }
    
    /**
     * Set updatedAt
     *
     * @ORM\PreUpdate
     */
    public function setUpdatedAt($updatedAt)
    {
    	$this->updatedAt = new \DateTime();
    
    	return $this;
    }
    
    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
    	return $this->updatedAt;
    }

    /**
     * Set gsm
     *
     * @param phone_number $gsm
     *
     * @return DrankStand
     */
    public function setGsm($gsm)
    {
        $this->gsm = $gsm;

        return $this;
    }

    /**
     * Get gsm
     *
     * @return phone_number
     */
    public function getGsm()
    {
        return $this->gsm;
    }
    
    protected function generatesmsId()
    {
    	
    	$em = $this->getDoctrine()->getManager();
    	while (true)
    	{
    		$id = mt_rand(10000000, 99999999);
    		$item = $em->getRepository('AppBundle:DrankStand')->findBy(array(
    				'smscode' => $id));
    		 
    		//$item = $em->find("AppBundle:DrankStand", $id);
    	
    		if (!$item)
    		{
    			return $id;
    		}
    	}
    	
    	
    	
    	
    	
    	
    	//$entity_name = $em->getClassMetadata(get_class($entity))->getName();
    
    	//while (true)
    	//{
    	//	$id = mt_rand(10000000, 99999999);
    	//	$item = $em->find($entity_name, $id);
    
    	//	if (!$item)
    	//	{
    	//		return $id;
    	//	}
    	//}
    
    	throw new \Exception('RandomIdGenerator worked hard, but could not generate unique ID :(');
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->order = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add order
     *
     * @param \AppBundle\Entity\Order $order
     *
     * @return DrankStand
     */
    public function addOrder(\AppBundle\Entity\Order $order)
    {
        $this->order[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \AppBundle\Entity\Order $order
     */
    public function removeOrder(\AppBundle\Entity\Order $order)
    {
        $this->order->removeElement($order);
    }

    /**
     * Get order
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrder()
    {
        return $this->order;
    }

    function __toString()
    {
        return $this->getNaam();
    }

}
