<?php

namespace SMS\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;


/**
 * Outbox
 *
 * @ORM\Table(name="outbox", options={"charset"="utf8mb4", "collate"="utf8mb4_unicode_ci"}, indexes={@ORM\Index(name="outbox_processed_ix", columns={"processed"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ExclusionPolicy("none")
 */
class Outbox
{

    // from here source outbox table from gammu-smsd https://github.com/gammu/gammu/blob/master/docs/sql/mysql.sql

    /**
     * @var integer
     * @ORM\Column(name="ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="UpdatedInDB", type="datetime", nullable=false)
     */
    private $updatedindb;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="InsertIntoDB", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $insertintodb;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="SendingDateTime", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $sendingdatetime;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="SendAfter", type="time", nullable=false, options={"default": "00:00:00"})
     * @Exclude
     */
    private $notBefore;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="SendBefore", type="time", nullable=false, options={"default": "23:59:59"})
     * @Exclude
     */
    private $notAfter;

    /**
     * @var string
     *
     * @ORM\Column(name="Text", type="text", length=65535, nullable=true)
     */
    private $textencoded;

    /**
     * @var string
     *
     * @ORM\Column(name="DestinationNumber", type="string", length=20, nullable=false)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="Coding", type="string", nullable=false, options={"default": "Default_No_Compression"})
     */
    private $coding;

    /**
     * @var string
     *
     * @ORM\Column(name="UDH", type="text", length=65535, nullable=true)
     */
    private $udh;

    /**
     * @var integer
     *
     * @ORM\Column(name="Class", type="integer", nullable=true, options={"default": "-1"})
     */
    private $class;

    /**
     * @var string
     *
     * @ORM\Column(name="TextDecoded", type="string", length=160, nullable=true)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="MultiPart", type="string", nullable=true, options={"default": "false"})
     */
    private $multipart;

    /**
     * @var integer
     *
     * @ORM\Column(name="RelativeValidity", type="integer", nullable=true, options={"default": "-1"})
     */
    private $relativevalidity;

    /**
     * @var string
     *
     * @ORM\Column(name="SenderID", type="string", length=255, nullable=true)
     */
    private $senderid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="SendingTimeOut", type="datetime", nullable=true, options={"default": "0000-00-00 00:00:00"})
     */
    private $sendingtimeout;

    /**
     * @var string
     *
     * @ORM\Column(name="DeliveryReport", type="string", nullable=true, options={"default": "default"})
     */
    private $deliveryreport;

    /**
     * @var string
     *
     * @ORM\Column(name="CreatorID", type="text", length=65535, nullable=false)
     */
    private $creatorid;

    /**
     * @var string
     *
     * @ORM\Column(name="Retries", type="integer", length=3, nullable=true, options={"default": 0})
     */
    private $retries;


    // gammu-smsd end





    /**
     * @var boolean
     *
     * @ORM\Column(name="phone", type="smallint", nullable=true)
     * @Exclude
     */
    private $phone;









    /**
     * @var boolean
     *
     * @ORM\Column(name="processed", type="smallint", nullable=false, options={"default": 0})
     */
    private $processed;

    /**
     * @var boolean
     *
     * @ORM\Column(name="error", type="smallint", nullable=false, options={"default": -1})
     * @Exclude
     */
    private $error;

    /**
     * @var boolean
     *
     * @ORM\Column(name="dreport", type="smallint", nullable=false, options={"default": 0})
     * @Exclude
     */
    private $dreport;





    /**
     * @var \DateTime
     *
     * @ORM\Column(name="processed_date", type="datetime", nullable=true, columnDefinition="TIMESTAMP DEFAULT 0")
     */
    private $processedDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="insertdate", type="datetime", nullable=false, columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP")
     */
    private $insertdate;



    /**
     * Set createdAt
     *
     * @ORM\PrePersist

     */


    public function setInsertintodb()
    {
        $this->updatedindb = new \DateTime();
        $this->insertintodb = new \DateTime();

    }



    /**
     * @return string
     */
    public function getRetries()
    {
        return $this->retries;
    }

    /**
     * @param string $retries
     */
    public function setRetries($retries)
    {
        $this->retries = $retries;
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
    public function getMultipart()
    {
        return $this->multipart;
    }

    /**
     * @param string $multipart
     */
    public function setMultipart($multipart)
    {
        $this->multipart = $multipart;
    }

    /**
     * @return string
     */
    public function getSenderid()
    {
        return $this->senderid;
    }

    /**
     * @param string $senderid
     */
    public function setSenderid($senderid)
    {
        $this->senderid = $senderid;
    }

    /**
     * @return \DateTime
     */
    public function getSendingtimeout()
    {
        return $this->sendingtimeout;
    }

    /**
     * @param \DateTime $sendingtimeout
     */
    public function setSendingtimeout($sendingtimeout)
    {
        $this->sendingtimeout = $sendingtimeout;
    }

    /**
     * @return string
     */
    public function getDeliveryreport()
    {
        return $this->deliveryreport;
    }

    /**
     * @param string $deliveryreport
     */
    public function setDeliveryreport($deliveryreport)
    {
        $this->deliveryreport = $deliveryreport;
    }

    /**
     * @return int
     */
    public function getRelativevalidity()
    {
        return $this->relativevalidity;
    }

    /**
     * @param int $relativevalidity
     */
    public function setRelativevalidity($relativevalidity)
    {
        $this->relativevalidity = $relativevalidity;
    }

    /**
     * @return string
     */
    public function getCreatorid()
    {
        return $this->creatorid;
    }

    /**
     * @param string $creatorid
     */
    public function setCreatorid($creatorid)
    {
        $this->creatorid = $creatorid;
    }

    /**
     * @return mixed
     */
    public function getCoding()
    {
        return $this->coding;
    }

    /**
     * @param mixed $coding
     */
    public function setCoding($coding)
    {
        $this->coding = $coding;
    }

    /**
     * @return mixed
     */
    public function getSendbefore()
    {
        return $this->sendbefore;
    }

    /**
     * @param mixed $sendbefore
     */
    public function setSendbefore($sendbefore)
    {
        $this->sendbefore = $sendbefore;
    }

    /**
     * @return mixed
     */
    public function getSendingdatetime()
    {
        return $this->sendingdatetime;
    }

    /**
     * @param mixed $sendingdatetime
     */
    public function setSendingdatetime($sendingdatetime)
    {

        $this->sendingdatetime = $sendingdatetime;
    }

    /**
     * @return mixed
     */
    public function getSendafter()
    {
        return $this->sendafter;
    }

    /**
     * @param mixed $sendafter
     */
    public function setSendafter($sendafter)
    {
        $this->sendafter = $sendafter;
    }

    /**
     * @return mixed
     */
    public function getUpdatedindb()
    {
        return $this->updatedindb;
    }

    /**
     * @param mixed $updatedindb
     * @ORM\PreUpdate
     */
    public function setUpdatedindb($updatedindb)
    {

        $this->updatedindb = new \DateTime();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInsertintodb()
    {
        return $this->insertintodb;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }


    /**
     * @param mixed $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
    	//$this->processedDate = new \DateTime('0000-00-00 00:00:00');
    	$this->processedDate = null;
    	$this->insertdate = new \DateTime('now');
    	$this->processed = false;
    	$this->error = -1;
    	if (!$this->dreport) $this->dreport = 0;
    	if (!$this->notBefore) $this->notBefore = new \DateTime('00:00:00');
    	if (!$this->notAfter) $this->notAfter = new \DateTime('23:59:59');
        if (!$this->sendingdatetime) $this->sendingdatetime = new \DateTime('0000-00-00 00:00:00');
        if (!$this->coding) $this->coding = "Default_No_Compression";
        if (!$this->creatorid) $this->creatorid = "symfony";
        if (!$this->class) $this->class = "-1";
        if (!$this->multipart) $this->multipart = "FALSE";
        if (!$this->relativevalidity) $this->relativevalidity = "-1";
        if (!$this->sendingtimeout) $this->sendingtimeout = new \DateTime('now');
        if (!$this->deliveryreport) $this->deliveryreport = "default";
        if (!$this->retries) $this->relativevalidity = "0";
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
     * @return Outbox
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
     * Set processedDate
     *
     * @param \DateTime $processedDate
     * @return Outbox
     */
    public function setProcessedDate($processedDate)
    {
        $this->processedDate = $processedDate;

        return $this;
    }

    /**
     * Get processedDate
     *
     * @return \DateTime
     */
    public function getProcessedDate()
    {
        return $this->processedDate;
    }

    /**
     * Set insertdate
     *
     * @param \DateTime $insertdate
     * @return Outbox
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
     * @return Outbox
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
     * @return Outbox
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
     * @return Outbox
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
     * Set error
     *
     * @param boolean $error
     * @return Outbox
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get error
     *
     * @return boolean
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set dreport
     *
     * @param boolean $dreport
     * @return Outbox
     */
    public function setDreport($dreport)
    {
        $this->dreport = $dreport;

        return $this;
    }

    /**
     * Get dreport
     *
     * @return boolean
     */
    public function getDreport()
    {
        return $this->dreport;
    }

    /**
     * Set notBefore
     *
     * @param \DateTime $notBefore
     * @return Outbox
     */
    public function setNotBefore($notBefore)
    {
        $this->notBefore = $notBefore;

        return $this;
    }

    /**
     * Get notBefore
     *
     * @return \DateTime
     */
    public function getNotBefore()
    {
        return $this->notBefore;
    }

    /**
     * Set notAfter
     *
     * @param \DateTime $notAfter
     * @return Outbox
     */
    public function setNotAfter($notAfter)
    {
        $this->notAfter = $notAfter;

        return $this;
    }

    /**
     * Get notAfter
     *
     * @return \DateTime
     */
    public function getNotAfter()
    {
        return $this->notAfter;
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

}
