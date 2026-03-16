<?php

namespace SMS\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sentitems
 *
 * @ORM\Table(name="sentitems", indexes={@ORM\Index(name="sentitems_date", columns={"DeliveryDateTime"}), @ORM\Index(name="sentitems_tpmr", columns={"TPMR"}), @ORM\Index(name="sentitems_dest", columns={"DestinationNumber"}), @ORM\Index(name="sentitems_sender", columns={"SenderID"})})
 * @ORM\Entity
 */
class Sentitems
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="UpdatedInDB", type="datetime", nullable=false)
     */
    private $updatedindb = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="InsertIntoDB", type="datetime", nullable=false)
     */
    private $insertintodb = '0000-00-00 00:00:00';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="SendingDateTime", type="datetime", nullable=false)
     */
    private $sendingdatetime = '0000-00-00 00:00:00';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DeliveryDateTime", type="datetime", nullable=true)
     */
    private $deliverydatetime;

    /**
     * @var string
     *
     * @ORM\Column(name="Text", type="text", length=65535, nullable=false)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="DestinationNumber", type="string", length=20, nullable=false)
     */
    private $destinationnumber = '';

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
     * @var integer
     *
     * @ORM\Column(name="Class", type="integer", nullable=false)
     */
    private $class = '-1';

    /**
     * @var string
     *
     * @ORM\Column(name="TextDecoded", type="text", length=65535, nullable=false)
     */
    private $textdecoded;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="SenderID", type="string", length=255, nullable=false)
     */
    private $senderid;

    /**
     * @var integer
     *
     * @ORM\Column(name="SequencePosition", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $sequenceposition = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="Status", type="string", nullable=false)
     */
    private $status = 'SendingOK';

    /**
     * @var integer
     *
     * @ORM\Column(name="StatusError", type="integer", nullable=false)
     */
    private $statuserror = '-1';

    /**
     * @var integer
     *
     * @ORM\Column(name="TPMR", type="integer", nullable=false)
     */
    private $tpmr = '-1';

    /**
     * @var integer
     *
     * @ORM\Column(name="RelativeValidity", type="integer", nullable=false)
     */
    private $relativevalidity = '-1';

    /**
     * @var string
     *
     * @ORM\Column(name="CreatorID", type="text", length=65535, nullable=false)
     */
    private $creatorid;


}

