<?php

namespace SMS\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Phones
 *
 * @ORM\Table(name="phones")
 * @ORM\Entity
 */
class Phones
{
    /**
     * @var string
     *
     * @ORM\Column(name="ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="UpdatedInDB", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="update")
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
     * @ORM\Column(name="TimeOut", type="datetime", nullable=false, options={"default": "0000-00-00 00:00:00"})
     */
    private $timeout;

    /**
     * @var string
     *
     * @ORM\Column(name="Send", type="string", nullable=false, options={"default": "no"})
     */
    private $send;

    /**
     * @var string
     *
     * @ORM\Column(name="Receive", type="string", nullable=false, options={"default": "no"})
     */
    private $receive;

    /**
     * @var string
     *
     * @ORM\Column(name="IMEI", type="string", length=35, nullable=false)
     */
    private $imei;

    /**
     * @var string
     *
     * @ORM\Column(name="Client", type="text", length=65535, nullable=false)
     */
    private $client;

    /**
     * @var integer
     *
     * @ORM\Column(name="Battery", type="integer", nullable=false, options={"default": -1})
     */
    private $battery;

    /**
     * @var integer
     *
     * @ORM\Column(name="Signal", type="integer", nullable=false, options={"default": -1})
     */
    private $signal;

    /**
     * @var integer
     *
     * @ORM\Column(name="Sent", type="integer", nullable=false, options={"default": 0})
     */
    private $sent;

    /**
     * @var integer
     *
     * @ORM\Column(name="Received", type="integer", nullable=false, options={"default": 0})
     */
    private $received;


}

