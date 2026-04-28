<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use libphonenumber\PhoneNumber;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks() 
 * @ORM\Table(name="users")
 * Defines the properties of the UserOld entity to represent the application users.
 * See http://symfony.com/doc/current/book/doctrine.html#creating-an-entity-class
 *
 * Tip: if you have an existing database, you can generate these entity class automatically.
 * See http://symfony.com/doc/current/cookbook/doctrine/reverse_engineering.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */

class UserOld implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\Email(
     * 		message = "Het email adres {{ value }} is niet juist.",
     * 		checkMX = true
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="phone_number", unique=true)
     * @AssertPhoneNumber(type="mobile")
     * 
     */
    private $gsm_app;
    
    /**
     * @ORM\Column(type="phone_number", unique=true)
     * @AssertPhoneNumber(type="mobile")
     */
    private $gsm_perso;
    
    /**
     * @ORM\Column(type="string")
     
     */
    private $password;
    
    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = array();
    
   
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
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    public function eraseCredentials()
    {
    }
    
    public function getUsername()
    {
    	return $this->username;
    }
    public function getPassword()
    {
    	return $this->password;
    }
    public function getRoles()
    {
    	//$roles = $this->roles;
    
    	// guarantees that a user always has at least one role for security
    	//if (empty($roles)) {
    	#	$roles[] = 'ROLE_USER';
    	//}
    
    	#return array_unique($roles);
    }
    
    public function setRoles(array $roles)
    {
    	#$this->roles = $roles;
    }
    
    
    public function getSalt()
    {
    	// you *may* need a real salt depending on your encoder
    	// see section on salt below
    	return null;
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
     * Set username
     *
     * @param string $username
     *
     * @return UserOld
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return UserOld
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set gsmApp
     *
     * @param phone_number $gsmApp
     *
     * @return UserOld
     */
    public function setGsmApp($gsmApp)
    {
        
    	$this->gsm_app = $gsmApp;

        return $this;
    }

    /**
     * Get gsmApp
     *
     * @return phone_number
     */
    public function getGsmApp()
    {
        return $this->gsm_app;
    }

    /**
     * Set gsmPerso
     *
     * @param phone_number $gsmPerso
     *
     * @return UserOld
     */
    public function setGsmPerso($gsmPerso)
    {
        $this->gsm_perso = $gsmPerso;

        return $this;
    }

    /**
     * Get gsmPerso
     *
     * @return phone_number
     */
    public function getGsmPerso()
    {
        return $this->gsm_perso;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return UserOld
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return UserOld
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}
