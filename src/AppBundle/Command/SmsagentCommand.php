<?php


namespace AppBundle\Command;

use Doctrine\ORM\EntityManager;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Templating\Helper\PhoneNumberFormatHelper;
use Proxies\__CG__\AppBundle\Entity\OrderDrank;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use SMS\AdminBundle\Entity\Outbox;
use AppBundle\Entity\Order;
use AppBundle\Entity\User;
use Symfony\Component\Validator\Constraints\Null;

class SmsagentCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('smsagent:monitor')
            ->addOption('watch', null, InputOption::VALUE_REQUIRED, 'Check for changes every n seconds set in option or one by default')
            ->setDescription('Run monitor on inbox changes')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command monitors sms inbox
<info>php %command.full_name%</info>
<info>php %command.full_name% --watch=1 --env=prod</info>
EOF
            );
    }


    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine')->getManager('smsd');
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $em2 = $this->getContainer()->get('doctrine')->getManager();
        $em2->getConnection()->getConfiguration()->setSQLLogger(null);

        $currentDateTime = date("Y-m-d H:i:s") . " ";
        $watch = $input->getOption('watch');
        $once = true;
        $output->writeln($currentDateTime . "Running SMS inbox checker .....");
        while ($once || $watch) {

            $currentDateTime = date("Y-m-d H:i:s");
            $inboxitems = $em->getRepository('SMSAdminBundle:Inbox')->findBy(array('imported' => 0));

            if ($inboxitems) {
                // test to see if doctrine mysql connections is still up, if not, reconnect
                if ($em2->getConnection()->ping() === false) {
                    $em2->getConnection()->close();
                    $em2->getConnection()->connect();
                }
                // all variables used in this script for cleanup reasons

                $inbox = null;

                foreach ($inboxitems as $inbox) {
                    $ord2 = null;
                    $smsMessage = null;
                    $ordertype = null;
                    $alreadyexisting = null;
                    $currentOrder = null;
                    $message = null;
                    $phonenr = null;
                    $txtphone = null;
                    $oldOrder = null;

                    if ((strlen($inbox->getText()) != 106) &&
                        (strlen($inbox->getText()) != 26) &&
                        (strlen($inbox->getText()) != 28)
                    ) {
                        $output->writeln($currentDateTime . "SMS text niet in correct formaat: ");
                        $output->writeln($inbox->getText() . " - " . $inbox->getNumber());
                    } else {


                        $output->writeln($currentDateTime . "GOOD Message: " . $inbox->getText() . " GSM:" . $inbox->getNumber());
                        $this->getContainer()->get('app.smsmessage')->setSmstxt($inbox->getText());
                        $ord2 = $this->getContainer()->get('app.smsmessage')->getOrder();
                        $this->getContainer()->get('app.smsmessage')->cleanup();
                        //$smsMessage->setSmstxt($inbox->getText());
                        //$ord2 = $smsMessage->getOrder();
                        //$smsMessage->__destruct();
                        //$smsMessage = null;


                        //printf($smsMessage->getSmstxt());
                        //\Doctrine\Common\Util\Debug::dump($ord2);
                        //exit();

                        // TODO: implement forwarding
                        // get all users in app
                        // forward all sms txt to them


                        $output->writeln($currentDateTime . "orderinfo: " . $ord2->getDrankstand()->getNaam() . " - " . $ord2->getSmsBestelNr() . " - " . $ord2->getOrdertype()->getBeschrijving());
                        //$output->writeln("Order Type: " . $ord2->getOrdertype()->getBeschrijving());
                        // check if status is new, if new just persist, if update, update existing item with following lookup.

                        if ($ord2->getOrdertype()->getSmstypeId() == 2) { //eindstock!!!
                            $ordertype = $em2->getRepository("AppBundle:OrderType")->findOneBy(array("smstype_id" => '3'));
                            $alreadyexisting = $em2->getRepository("AppBundle:Order")->findOneBy(array("drankstand" => $ord2->getDrankstand(), "festivaldag" => $ord2->getFestivaldag(), "ordertype" => $ordertype));
                            if ($alreadyexisting == null) {
                                // initial save
                                $em2->persist($ord2);
                                $em2->flush();
                                $em2->detach($ord2);
                                $output->writeln($currentDateTime . $ord2->getOrdertype()->getBeschrijving() . " for drankstand " .
                                    $ord2->getDrankstand()->getNaam() . " order ID:" . $ord2->getSmsBestelNr() . " Saved 1 !!");

//                                $currentOrder = $em2->getRepository("AppBundle:Order")->findOneBy(array("smsBestelNr" => $ord2->getSmsBestelNr(), "drankstand" => $ord2->getDrankstand()));
//                                $currentOrder->setOrdertype($em2->getRepository('AppBundle:OrderType')->findOneBy(array('smstype_id' => 3)));
//                                $output->writeln("SMSCONF: " . $currentOrder->getDrankstand()->getNaam() . " - " . $currentOrder->getSmsBestelNr() . " - " . $currentOrder->getOrdertype()->getBeschrijving());
//                                $em2->persist($currentOrder);
//                                $em2->flush();
//                                $em2->detach($currentOrder);



                                //$em2->detach($ord2);
                                //$em2->detach($currentOrder);


//                                $em2->refresh($ord2);
                                //                              $em2->detach($currentOrder);

                            } else {

                                $output->writeln($currentDateTime . "ATTENTION: drankstand " . $ord2->getDrankstand()->getNaam() . " - " . $ord2->getSmsBestelNr() . " - Eindstock alreeds ingegeven!!!");

                            }
                            if ($alreadyexisting != null)
                            {
                                $em2->detach($alreadyexisting);

                            }
                            $em2->detach($ordertype);

                        } else {
                            $managers = $em2->getRepository("AppBundle:User")->findBy(array("manager" => true));
                            /** @var User $manager */
                            foreach ($managers as $manager) {
                                // send out Received confirmation
                                $message = new Outbox();
                                $phonenr = new PhoneNumber();
                                /** @var PhoneNumber $phonenr */
                                $phonenr = $manager->getGsmApp();
                                $txtphone = "+" . $phonenr->getCountryCode() . $phonenr->getNationalNumber();
                                // only copy messages to managers that did not send the request.
                                if ($inbox->getNumber() <> $txtphone) {
                                    $message->setNumber($txtphone);
                                    $this->getContainer()->get('app.smsmessage')->setOrder($ord2);
                                    $message->setText($this->getContainer()->get('app.smsmessage')->getSmstxt());
                                    $this->getContainer()->get('app.smsmessage')->cleanup();
                                    //$smsMessage->__destruct();
                                    //$smsMessage = null;
                                    $output->writeln($currentDateTime . "COPY TO: " . $manager->getUsername() . " met GSMnr: " . $txtphone . " SMSTXT: " . $message->getText());

                                    // persisting disabled to save SMS
                                    $em->persist($message);
                                    $em->flush();

                                }
                                $em->detach($message);
                            }


                            if (($ord2->getOrdertype()->getSmstypeId() == 10) ||
                                ($ord2->getOrdertype()->getSmstypeId() == 20) ||
                                ($ord2->getOrdertype()->getSmstypeId() == 25) ||
                                ($ord2->getOrdertype()->getSmstypeId() == 30)
                            ) {
                                // nieuwe bestellingen 10 = drank 20 = ehbo 25 = wissel 30 = bonkoffer

                                $output->writeln($currentDateTime . $ord2->getOrdertype()->getBeschrijving() . " for drankstand " .
                                    $ord2->getDrankstand()->getNaam() . " order ID:" . $ord2->getSmsBestelNr() . " Saved 2 !!");

                                $em2->persist($ord2);
                                $em2->flush();
                                // get the order added in the database
                                $currentOrder = $em2->getRepository("AppBundle:Order")->findOneBy(array("smsBestelNr" => $ord2->getSmsBestelNr(), "drankstand" => $ord2->getDrankstand()));

                                // send out Received confirmation
                                $message = new Outbox();
                                $phonenr = new PhoneNumber();
                                /** @var PhoneNumber $phonenr */
                                $phonenr = $currentOrder->getDrankstand()->getGsm();
                                $txtphone = "+" . $phonenr->getCountryCode() . $phonenr->getNationalNumber();
                                // double check that order requestor has the same phone than whats configured in the database.
                                if ($inbox->getNumber() == $txtphone) {
                                    $message->setNumber($txtphone); // send message to drankstand.
                                    $ordertype = $em2->getRepository('AppBundle:OrderType')->findOneBy(array('smstype_id' => $currentOrder->getOrdertype()->getSmstypeId() + 1));
                                    $currentOrder->setOrdertype($em2->getRepository('AppBundle:OrderType')->findOneBy(array('smstype_id' => $currentOrder->getOrdertype()->getSmstypeId() + 1)));
                                    $output->writeln($currentDateTime . "SMSCONF: " . $ord2->getDrankstand()->getNaam() . " - " . $ord2->getSmsBestelNr() . " - " . $ord2->getOrdertype()->getBeschrijving());
                                    $em2->persist($currentOrder);
                                    $em2->flush();
                                    $this->getContainer()->get('app.smsmessage')->setOrder($currentOrder);
                                    $output->writeln($currentDateTime . "2 Confirmation: " . $this->getContainer()->get('app.smsmessage')->getSmstxt() . " naar: " . $txtphone);
                                    $message->setText($this->getContainer()->get('app.smsmessage')->getSmstxt());
                                    $this->getContainer()->get('app.smsmessage')->cleanup();
                                    $em->persist($message);
                                    $em->flush();
                                }
                                $em->detach($message);

                                $em2->detach($currentOrder);
                                $em2->detach($ord2);


                            } else if (($ord2->getOrdertype()->getSmstypeId() == 14) ||
                                ($ord2->getOrdertype()->getSmstypeId() == 23) ||
                                ($ord2->getOrdertype()->getSmstypeId() == 28) ||
                                ($ord2->getOrdertype()->getSmstypeId() == 33)
                            ) {
                                // all cancels
                                /** @var PhoneNumber $drankstandGSM */
                                $drankstandGSM = $ord2->getDrankstand()->getGsm();
                                if ($inbox->getNumber() != "+" . $drankstandGSM->getCountryCode() . $drankstandGSM->getNationalNumber()) {
                                    // number of canceler is not the one of the requestor, so we need to pass the cancel to the requestor also.
                                    // todo: implement copy cancelation to requestor if cancel request did not come from the same drankstand
                                    $message = new Outbox();
                                    $phonenr = new PhoneNumber();
                                    /** @var PhoneNumber $phonenr */
                                    $txtphone = "+" . $drankstandGSM->getCountryCode() . $drankstandGSM->getNationalNumber();
                                    $message->setNumber($txtphone);
                                    $this->getContainer()->get('app.smsmessage')->setOrder($ord2);
                                    $message->setText($this->getContainer()->get('app.smsmessage')->getSmstxt());
                                    $this->getContainer()->get('app.smsmessage')->cleanup();
                                    //$smsMessage->__destruct();
                                    //$smsMessage = null;
                                    $output->writeln($currentDateTime . "COPY CANCEL TO DRANKSTAND: " . $ord2->getDrankstand()->getNaam() . " met GSMnr: " . $txtphone . " SMSTXT: " . $message->getText());

                                    // persisting disabled to save SMS
                                    $em->persist($message);
                                    $em->flush();
                                    $em->detach($message);

                                }
//                            // copy to the cancels to other managers
//                            $managers = $em2->getRepository("AppBundle:User")->findBy(array("manager" => true));
//                            /** @var User $manager */
//                            foreach ($managers as $manager)
//                            {
//                                // send out Received confirmation
//                                $message = new Outbox();
//                                $phonenr = new PhoneNumber();
//                                /** @var PhoneNumber $phonenr */
//                                $phonenr = $manager->getGsmApp();
//                                $txtphone = "+" . $phonenr->getCountryCode() . $phonenr->getNationalNumber();
//                                if ($inbox->getNumber() <> $txtphone) {
//                                    $message->setNumber($txtphone);
//                                    $this->getContainer()->get('app.smsmessage')->setOrder($ord2);
//                                    $message->setText($this->getContainer()->get('app.smsmessage')->getSmstxt());
//                                    $this->getContainer()->get('app.smsmessage')->cleanup();
//                                    //$smsMessage->__destruct();
//                                    //$smsMessage = null;
//                                    $output->writeln("COPY CANCEL TO: " . $manager->getUsername() . " met GSMnr: " . $txtphone . " SMSTXT: " . $message->getText());
//
//                                    // persisting disabled to save SMS
//                                    $em->persist($message);
//                                    $em->flush();
//                                }
//                                $em->detach($message);
//                            }


                                //$output->writeln("test-> 1:" . $inbox->getNumber() . "2:+" . $drankstandGSM->getCountryCode().$drankstandGSM->getNationalNumber());


                                $oldOrder = $em2->getRepository("AppBundle:Order")->findOneBy(array("smsBestelNr" => $ord2->getSmsBestelNr(), "drankstand" => $ord2->getDrankstand()));
                                // if item is found in the database, clean it up.

                                if ($oldOrder != null) {
                                    $output->writeln($currentDateTime . "Deleting: " . $oldOrder->getDrankstand()->getNaam() . " - " . $oldOrder->getSmsBestelNr() . " - " . $oldOrder->getOrdertype()->getBeschrijving());
                                    $em2->remove($oldOrder);
                                    $em2->flush();
                                    $em2->detach($oldOrder);
                                    //$em2->refresh($oldOrder);
                                }


                            } else if ($ord2->getOrdertype()->getSmstypeId() == 13) {
                                // drank bestelling geleverd en afgesloten
                                $oldOrder = $em2->getRepository("AppBundle:Order")->findOneBy(array("smsBestelNr" => $ord2->getSmsBestelNr(), "drankstand" => $ord2->getDrankstand()));
                                if ($oldOrder != null) {
                                    $oldOrder->setOrdertype($ord2->getOrdertype());
                                    if ($oldOrder->getOd() != $ord2->getOd()) {
                                        // cleanup old ones
                                        /** @var OrderDrank $item */
                                        foreach ($oldOrder->getOd() as $item) {
                                            $oldOrder->removeOd($item);
                                        }
                                        // add new ones
                                        /** @var OrderDrank $item */
                                        foreach ($ord2->getOd() as $item) {
                                            $oldOrder->addOd($item);
                                        }
                                    }


                                    //$oldOrder->setOd($ord2->getOd());
                                    //$em2->detach($ord2);
                                    $em2->persist($oldOrder);
                                    $em2->flush();
                                    $em2->detach($oldOrder);
                                    //$em2->refresh($oldOrder);
                                    $output->writeln($currentDateTime . "Updating: " . $oldOrder->getDrankstand()->getNaam() . " - " . $oldOrder->getSmsBestelNr() . " to " . $oldOrder->getOrdertype()->getBeschrijving());
                                    //$output->writeln($ord2->getOrdertype()->getBeschrijving() . " for drankstand " .
                                    //    $ord2->getDrankstand()->getNaam() . "Status Updated!!");

                                }
                            } else {
                                // other type of requests follow the default rules
                                $oldOrder = $em2->getRepository("AppBundle:Order")->findOneBy(array("smsBestelNr" => $ord2->getSmsBestelNr(), "drankstand" => $ord2->getDrankstand()));
                                if ($oldOrder != null) {
                                    $oldOrder->setOrdertype($ord2->getOrdertype());
                                    $em2->persist($oldOrder);
                                    $em2->flush();

                                    // send update to drankstand (if not send from)
                                    $message = new Outbox();
                                    $phonenr = new PhoneNumber();
                                    /** @var PhoneNumber $phonenr */
                                    $phonenr = $oldOrder->getDrankstand()->getGsm();
                                    $txtphone = "+" . $phonenr->getCountryCode() . $phonenr->getNationalNumber();
                                    // double check that order requestor has the same phone than whats configured in the database.
                                    if ($inbox->getNumber() <> $txtphone) {
                                        $message->setNumber($txtphone); // send message to drankstand.
                                        $output->writeln($currentDateTime . "OTHERTYPE CONF: " . $oldOrder->getDrankstand()->getNaam() . " - " . $oldOrder->getSmsBestelNr() . " - " . $oldOrder->getOrdertype()->getBeschrijving());
                                        $this->getContainer()->get('app.smsmessage')->setOrder($oldOrder);
                                        $output->writeln($currentDateTime . "3 Confirmation: " . $this->getContainer()->get('app.smsmessage')->getSmstxt() . " naar: " . $txtphone);
                                        $message->setText($this->getContainer()->get('app.smsmessage')->getSmstxt());
                                        $this->getContainer()->get('app.smsmessage')->cleanup();
                                        $em->persist($message);
                                        $em->flush();
                                    }
                                    $em->detach($message);


                                    $em2->detach($oldOrder);
                                    //$em2->refresh($oldOrder);
                                    $output->writeln($currentDateTime . "Updating: " . $oldOrder->getDrankstand()->getNaam() . " - " . $oldOrder->getSmsBestelNr() . " to " . $oldOrder->getOrdertype()->getBeschrijving());
                                    //$output->writeln($ord2->getOrdertype()->getBeschrijving() . " for drankstand " .
                                    //    $ord2->getDrankstand()->getNaam() . "Status Updated!!");
                                } else {
                                    // do nothing with a not found order in the database. Just print out the item.
                                    $output->writeln($currentDateTime . "Error: order not found - " . $ord2->getDrankstand()->getNaam() . " - " . $ord2->getSmsBestelNr() . " to " . $ord2->getOrdertype()->getBeschrijving());
                                }

                            }


                            //$em2->flush();
                            $em2->detach($ord2);

                        }
                    }
                    // update this inbox item and put imported on 1
                    $inbox->setImported(1);
                    $em->persist($inbox);
                    $ord2 = null;
                    $smsMessage = null;
                    $ordertype = null;
                    $alreadyexisting = null;
                    $currentOrder = null;
                    $message = null;
                    $phonenr = null;
                    $txtphone = null;
                    $oldOrder = null;
                    $em2->flush();
                    $em2->clear();
                    gc_collect_cycles();
                    $em->flush();
                    sleep(intval(5));


                    //$em2->flush();
                    //             $em2->clear();
                }

            }
//

            //$em2->flush();
            //$em2->clear();
            //$this->printMemoryUsage($output);
            $inboxitems = null;

            $em->flush();
            $em->clear();
            //$em2->flush();
            //$em2->clear();


            // $status = $adapter->status();

//            foreach($status as $server => $queues)
//            {
//
//                $output->writeln("<info>Status for Server {$server}</info>");
//                $output->writeln("");
//                $table = new Table($output);
//                $table
//                    ->setHeaders(['Queue', 'Jobs', 'Workers working', 'Workers total', 'Errors'])
//                    ->setRows($queues);
//                $table->render($output);
//
//            }
//            $output->writeln("");
//            $output->writeln("");
//
//            $once = false;
            if ($watch) {
                sleep(intval($watch));
            }
        }

    }

    protected
    function printMemoryUsage(OutputInterface $output)
    {
        $output->writeln(sprintf('Memory usage (currently) %dKB/ (max) %dKB', round(memory_get_usage(true) / 1024), memory_get_peak_usage(true) / 1024));
    }
}