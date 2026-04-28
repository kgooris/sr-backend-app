<?php
/** Class explaining the way SMS is going to be send. This class always returns a string.*/

namespace AppBundle\Model;


use AppBundle\AppBundle;
use AppBundle\Entity\DrankSoort;
use AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;


class SMSProductItem
{

    /** @var DrankSoort  */
    private $dranksoort;

    /** @var int */
    private $aantal;

    /**
     * @return DrankSoort
     */
    public function getDranksoort()
    {
        return $this->dranksoort;
    }

    /**
     * @param DrankSoort $dranksoort
     */
    public function setDranksoort($dranksoort)
    {
        $this->dranksoort = $dranksoort;
    }

    /**
     * @return int
     */
    public function getAantal()
    {
        return $this->aantal;
    }

    /**
     * @param int $aantal
     */
    public function setAantal($aantal)
    {
        $this->aantal = $aantal;
    }

    





}