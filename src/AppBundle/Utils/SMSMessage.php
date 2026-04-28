<?php
/** Class explaining the way SMS is going to be send. This class always returns a string.*/

namespace AppBundle\Utils;


use AppBundle\AppBundle;
use AppBundle\Entity\DrankSoort;
use AppBundle\Entity\DrankStand;
use AppBundle\Entity\OrderType;
use AppBundle\Entity\OrderDrank;
use AppBundle\Entity\Order;
use AppBundle\Entity;
use DateTime;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Monolog\Logger;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Log;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Tests\Controller;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class SMSMessage

{

    /** @var  Order */
    private $order;
    /** @var  string */
    private $smstxt;
    
    private $logger;

    /** @var EntityManager  */
    private $em;




    public function __construct(Logger $logger, EntityManager $em)
    {


        $this->logger = $logger;
        $this->em = $em;
        if ($this->order)
        {
            $this->em->detach($this->order);
        }



    }
    
//    function __destruct()
//    {
//        if (!$this->em == null) {
//            $this->em->detach($this->order);
//        }
//        //$this->em->clear();
//        //$this->em->close();
//       // unset($this->logger,$this->smstxt,$this->order);
//        //unset($this);
//        //gc_collect_cycles();
//
//    }
    
    public function cleanup()
    {
        if (!$this->em == null) {
            if (!$this->order == null) {
                $this->em->detach($this->order);
               // unset($this->order);
            }
        }
        if(!$this->smstxt == null) {
            //unset($this->smstxt);
        }
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        // convert SMSTXT to OrderObject
        // this is only to load data insight of symfony database.
        // as this converts the smstxt (unauthenticated towards the symfony object authenticated
        // we need to auhtentication. We will use smsuser and smspassword defined in FOSUserBundle for this.
        // Datetime

        // first check the validity of the sms txt format
        $this->order = new Order();
        if ((strlen($this->smstxt) != 26) && (strlen($this->smstxt) != 28) && (strlen($this->smstxt) != 106))
        {
            throw new Exception("Invalid smstxt to be able to convert it to Order object");
        }
        $day = substr($this->smstxt,0,2);
        $month = substr($this->smstxt,2,2);
        $year = substr($this->smstxt,4,2);
        $hour = substr($this->smstxt,6,2);
        $min = substr($this->smstxt,8,4);
        $datetime = new DateTime();
        $datetime->createFromFormat("Y-m-d H:i",$year . "-" .$month."-".$day." ".$hour.":".$min);
        $this->order->setSmsDateTime($datetime);

      
        // toestelnr
        // access database and find unique ID, fill it with all info related drankstand ids
        $toestel = substr($this->smstxt,12,8);
        $this->order->setDrankstand($this->em->getRepository('AppBundle:DrankStand')->findOneBy(array('smscode' => $toestel)));
        // typeaanvraag
        // access database and find smstype in ordertype table and get all related info
        $strOrderType = substr($this->smstxt,20,2);
        $this->order->setOrdertype($this->em->getRepository('AppBundle:OrderType')->findOneBy(array('smstype_id' => $strOrderType)));

        // bestelnr
        $strBestelnr = substr($this->smstxt,22,4);
        $this->order->setSmsBestelNr(intval($strBestelnr));

        // owner
        $this->order->setOwner($this->em->getRepository('AppBundle:User')->findOneBy(array('username' => "smsuser")));

        // festivaldag
        $this->order->setFestivaldag($this->em->getRepository('AppBundle:FestivalDag')->findOneBy(array('festactive' => true)));


        // producten
        // loop over all possitions if the ordertypes are 10,13,2
        // get amount, if amount is higher thene 0 -> get related info from product table add quantity to OrderDrank
        if (($this->order->getOrdertype()->getSmstypeId() == 10) ||
            ($this->order->getOrdertype()->getSmstypeId() == 13) ||
            ($this->order->getOrdertype()->getSmstypeId() == 2))
        {
            $smsdrinks = str_split(substr($this->smstxt, 26, 80), 2);
            //\Doctrine\Common\Util\Debug::dump($smsdrinks);
            $pos = 0;
            $od = null;
            foreach ($smsdrinks as $item) {

                $pos++;
                if (intval($item) > 0) {

                    $od = new OrderDrank();

                    $drink = $this->em->getRepository('AppBundle:DrankSoort')->findOneBy(array('smspositieid' => $pos));
                    if ($drink == NULL)
                    {
                        throw new Exception("DRINK NOT FOUND!! position id: ".$pos);
                    }
                    $od->setDrank($drink);
                    $od->setHoeveel(intval($item));
                   // $od->setOrd(null);

                    $this->order->addOd($od);
                    //$this->em->detach($od);
                    //$this->em->detach($drink);


                    //\Doctrine\Common\Util\Debug::dump($od);
                }

            }
            if (!$od == null) {
                $this->em->detach($od);
            }


        }
        // special lenght for Fight & EHBO
        if (strlen($this->smstxt) == 28) 
        {
            $hulptype = substr($this->smstxt, 26,2);
            $this->order->setOrdernotes($hulptype);
//            if ($hulptype == "01")
//            {
//                $this->order->setOrdernotes("NOOD: Vechtpartij aan de gang");
//            }
//            else
//            {
//                $this->order->setOrdernotes("NOOD: EHBO nodig");
//            }
        }
       
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getSmstxt()
    {
        // convert Order object to SMS txt
        $newout_drinks = "";
        // defining variables to use in output
        $hour = $this->order->getSmsDateTime()->format("H");
        $min = $this->order->getSmsDateTime()->format("i");
        $day = $this->order->getSmsDateTime()->format("d");
        $month = $this->order->getSmsDateTime()->format("m");
        $year = $this->order->getSmsDateTime()->format("Y");


        $out = $day . $month . $year . $hour . $min .
            str_pad($this->order->getDrankstand()->getSmscode(),8,'0',STR_PAD_LEFT) .
            str_pad($this->order->getOrdertype()->getSmstypeId(),2,'0',STR_PAD_LEFT) .
            str_pad($this->order->getSmsBestelNr(),4,'0',STR_PAD_LEFT);
        // validating text length:
        if (strlen($out) != 26)
        {
            printf("Exception. non valid SmsText Length: ". $out);
            $this->logger->info("class: SMSMessage - Exception. non valid SmsText Length: ". $out);
            exit();

        }

        //total sms length with products should contain 106 charters
        // if order contains products, add them
        if (($this->order->getOrdertype()->getSmstypeId() == 10) ||
            ($this->order->getOrdertype()->getSmstypeId() == 2) ||
            ($this->order->getOrdertype()->getSmstypeId() == 13))
        {
            
            $newout_drinks = "00000000000000000000000000000000000000000000000000000000000000000000000000000000"; //40 product 80 characters
            
            /** @var OrderDrank $product */
            foreach ($this->order->getOd() as $product)
            {
                $posid = $product->getDrank()->getSmspositieid();
                $newout_drinks = substr_replace($newout_drinks,str_pad($product->getHoeveel(),2,'0',STR_PAD_LEFT),($posid*2)-2,2);



            }
            $this->em->detach($product);

            //$this->logger->info("hello");
            //check total lenght should be 106
            if (strlen($out. $newout_drinks) != 106)
            {
                printf("Exception. non valid SmsText Length: ". $out . $newout_drinks);
                $this->logger->info("class: SMSMessage - Exception. non valid SmsText Length: ". $out . $newout_drinks);

                exit();

            }
            return $out . $newout_drinks;
        }
        elseif (($this->order->getOrdertype()->getSmstypeId() == 20))
        {
            return $out . $this->order->getOrdernotes();
        }
        else
        {
            return $out;
        }


    }

    /**
     * @param string $smstxt
     */
    public function setSmstxt($smstxt)
    {
        $this->smstxt = $smstxt;
    }

}