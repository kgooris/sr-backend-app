<?php

namespace SMS\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OutboxMultipart
 *
 * @ORM\Table(name="outbox_multipart")
 * @ORM\Entity
 */
class OutboxMultipart
{
    /**
     * @var string
     *
     * @ORM\Column(name="Text", type="text", length=65535, nullable=true)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="Coding", type="string", nullable=false)
     */
    private $coding = 'Default_No_Compression';

    /**
     * @var string
     *
     * @ORM\Column(name="UDH", type="text", length=65535, nullable=true)
     */
    private $udh;

    /**
     * @var integer
     *
     * @ORM\Column(name="Class", type="integer", nullable=true)
     */
    private $class = '-1';

    /**
     * @var string
     *
     * @ORM\Column(name="TextDecoded", type="text", length=65535, nullable=true)
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
     * @var integer
     *
     * @ORM\Column(name="SequencePosition", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $sequenceposition = '1';


}

