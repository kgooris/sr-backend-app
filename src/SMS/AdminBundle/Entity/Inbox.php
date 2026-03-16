<?php

namespace SMS\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;

/**
 * Inbox
 *
 * @ORM\Table(name="inbox", options={"charset"="utf8mb4", "collate"="utf8mb4_unicode_ci"})
 * @ORM\Entity
 * @ExclusionPolicy("none")
 */
class Inbox
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="SenderNumber", type="string", length=20, nullable=false)
     */
    private $number;

     /**
     * @var \DateTime
     *
     * @ORM\Column(name="UpdatedInDB", type="datetime", nullable=false, columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP")
     */
    private $insertdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ReceivingDateTime", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $smsdate;

    /**
     * @var string
     *
     * @ORM\Column(name="TextDecoded", type="text", length=65535, nullable=true)
     */
    private $text;
    
    /**
     * @var string
     *
     * @ORM\Column(name="Text", type="text", length=65535, nullable=false)
     */
    private $textencoded;
    
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="phone", type="smallint", nullable=true, options={"default": 0})
     * @Exclude
     */
    private $phone;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Processed", type="smallint", nullable=false, options={"default": 0})
     */
    private $processed;

    /**
     * @var string
     *
     * @ORM\Column(name="Coding", type="string", nullable=false)
     */
    private $coding = 'Default_No_Compression';

    /**
     * @var string
     *
     * @ORM\Column(name="UDH", type="text", length=65535, nullable=false)
     */
    private $udh;

    /**
     * @var string
     *
     * @ORM\Column(name="SMSCNumber", type="string", length=20, nullable=false)
     */
    private $smscnumber = '';

    /**
     * @var string
     *
     * @ORM\Column(name="RecipientID", type="text", length=65535, nullable=false)
     */
    private $recipientid;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="Class", type="integer", nullable=false)
     */
    private $class = '-1';

    /**
     * @var integer
     *
     * @ORM\Column(name="imported", type="integer", nullable=true, options={"default": 0})
     */
    private $imported;

    /**
     * @return int
     */
    public function getImported()
    {
        return $this->imported;
    }

    /**
     * @param int $imported
     */
    public function setImported($imported)
    {
        $this->imported = $imported;
    }





    /**
     * @return \DateTime
     */
    public function getReceivingdatetime()
    {
        return $this->receivingdatetime;
    }

    /**
     * @param \DateTime $receivingdatetime
     */
    public function setReceivingdatetime($receivingdatetime)
    {
        $this->receivingdatetime = $receivingdatetime;
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
     * Set number
     *
     * @param string $number
     * @return Inbox
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set smsdate
     *
     * @param \DateTime $smsdate
     * @return Inbox
     */
    public function setSmsdate($smsdate)
    {
        $this->smsdate = $smsdate;

        return $this;
    }

    /**
     * Get smsdate
     *
     * @return \DateTime
     */
    public function getSmsdate()
    {
        return $this->smsdate;
    }

    /**
     * Set insertdate
     *
     * @param \DateTime $insertdate
     * @return Inbox
     */
    public function setInsertdate($insertdate)
    {
        $this->insertdate = $insertdate;

        return $this;
    }

    /**
     * Get insertdate
     *
     * @return \DateTime
     */
    public function getInsertdate()
    {
        return $this->insertdate;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Inbox
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set phone
     *
     * @param boolean $phone
     * @return Inbox
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return boolean
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set processed
     *
     * @param boolean $processed
     * @return Inbox
     */
    public function setProcessed($processed)
    {
        $this->processed = $processed;

        return $this;
    }

    /**
     * Get processed
     *
     * @return boolean
     */
    public function getProcessed()
    {
        return $this->processed;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedindb()
    {
        return $this->updatedindb;
    }

    /**
     * @param \DateTime $updatedindb
     */
    public function setUpdatedindb($updatedindb)
    {
        $this->updatedindb = $updatedindb;
    }

    /**
     * @return string
     */
    public function getTextencoded()
    {
        return $this->textencoded;
    }

    /**
     * @param string $textencoded
     */
    public function setTextencoded($textencoded)
    {
        $this->textencoded = $textencoded;
    }

    /**
     * @return string
     */
    public function getCoding()
    {
        return $this->coding;
    }

    /**
     * @param string $coding
     */
    public function setCoding($coding)
    {
        $this->coding = $coding;
    }

    /**
     * @return string
     */
    public function getUdh()
    {
        return $this->udh;
    }

    /**
     * @param string $udh
     */
    public function setUdh($udh)
    {
        $this->udh = $udh;
    }

    /**
     * @return string
     */
    public function getSmscnumber()
    {
        return $this->smscnumber;
    }

    /**
     * @param string $smscnumber
     */
    public function setSmscnumber($smscnumber)
    {
        $this->smscnumber = $smscnumber;
    }

    /**
     * @return string
     */
    public function getRecipientid()
    {
        return $this->recipientid;
    }

    /**
     * @param string $recipientid
     */
    public function setRecipientid($recipientid)
    {
        $this->recipientid = $recipientid;
    }

    /**
     * @return int
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param int $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }
    
}
