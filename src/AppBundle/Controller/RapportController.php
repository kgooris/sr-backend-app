<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\DrankStand;
use AppBundle\Entity\Order;
use AppBundle\Entity\OrderType;
use AppBundle\Utils\SMSMessage;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Exception;
use Proxies\__CG__\AppBundle\Entity\DrankSoort;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\FestivalDag;
use AppBundle\Entity\OrderDrank;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class RapportController extends Controller
{
    /**
     * @Route("/admin/rapport", name="admin_rapport_homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        
        return $this->render('AdminDefault/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }



    /**
     * @Route("/admin/rapport/intern/print/{van}/{tot}", name="admin_rapport_intern_print")
     * @Security("has_role('ROLE_RAPPORT_INTERN')")
     */
    public function rapportInternPrintAction($van, $tot)
    {
        $em = $this->getDoctrine()->getEntityManager();
        /** @var FestivalDag $selFestivaldag */
        //$selFestivaldag = $em->getRepository("AppBundle:FestivalDag")->find($van);
        /** @var DrankStand $selDrankstand */
        //$selDrankstand = $em->getRepository("AppBundle:DrankStand")->find($van);
        $fromdate = DateTime::createFromFormat("d-m-Y H:i:s",$van . " 00:00:00");
        $todate = DateTime::createFromFormat("d-m-Y H:i:s",$tot . " 23:59:59");
        $pdf_filename = "rapport_interne_drank_". $fromdate->format("Ymd"). "-" .$todate->format("Ymd"). ".pdf";
            $printdata = $this->getRapportArrayInt($fromdate,$todate);

        $pdf = $this->container->get("white_october.tcpdf")->create();


        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Suikerrock');
        $pdf->SetTitle('Rapport Drankstand');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');
        // set header content
        $headertext1 = "<h1>Rapport Interne Drank</h1>";


        $headertext2 = "<table width=\"100%\"><tr><td width=\"50%\"><b>Van: </b>". $fromdate->format("d-m-Y"). "</td><td align=\"right\" width=\"50%\"><b>Tot: </b>".$todate->format("d-m-Y")."</td></tr></table>";


        //$pdf->setOwnHeaderTitle($headertext1);
        //$pdf->setOwnHeaderText($headertext2);
        $pdf->SetHeaderData('uploads/images/logo_suikerrock_30_jaar_front.png', '', $headertext1, $headertext2);


        //$pdf->setPrintFooter(false);



        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(7,37,5,true);
        $pdf->setHeaderMargin(5);
        $pdf->setFooterMargin(50);


        // set auto page breaks
        $pdf->SetAutoPageBreak(true, 10);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

   

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 10, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage('L', "A4");

        // set JPEG quality
        $pdf->setJPEGQuality(75);

        // Image example with resizing
        //$pdf->Image('uploads/images/logo_suikerrock_30_jaar_front.png', 5, 5, 50, 20, 'PNG', '', '', false, 150, '', false, false, 1, false, false, false);

        // set text shadow effect
        // $pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        // Set some content to print


        $items_begin_html = <<<EOD
 
            <table width="1000" align="center" cellspacing="0" cellpadding="3" border="1">
            <tbody>
            
EOD;
        $items_end_html = <<<EOD
        </tbody>
    </table>
 
    
    
    
EOD;

        $items_content_html = "";
        $prevtitle = "";
        $rowcounter = 0;
        $colcounter = 0;

        foreach ($printdata as $line)
        {
            $rowcounter++;
            if ($rowcounter == 1)
            {
                $items_content_html = $items_content_html . "<tr>";
                foreach ($line as $cols)
                {
                    $colcounter++;
                    if ($colcounter == 1)
                    {
                        $items_content_html = $items_content_html . "<td align=\"left\" width=\"300\">".$cols[0]."</td>";

                    }
                    else
                    {
                        $items_content_html = $items_content_html . "<td align=\"center\" valign=\"bottom\" width=\"50\">".$this->verticletext($cols[0])."</td>";
                    }
                }
                $items_content_html = $items_content_html . "</tr>";

            }
            else
            {
                $items_content_html = $items_content_html . "<tr>";
                $colcounter = 0;
                foreach ($line as $cols)
                {
                    $colcounter++;
                    if ($colcounter == 1)
                    {
                        $items_content_html = $items_content_html . "<td align=\"left\" width='300'>".$cols."</td>";

                    }
                    else
                    {
                        $items_content_html = $items_content_html . "<td align=\"center\" width='50'>".$cols."</td>";
                    }
                }
                $items_content_html = $items_content_html . "</tr>";
            }
        }










        $pdf->SetFont('helvetica', '', 10);
        //$totalitems = $order->getOd()->count();
//        if ($totalitems > 19) {
//            $itempositioncounter = 0;
//
//            $pdf->setPrintFooter(false);



        $items_html = $items_begin_html .$items_content_html .$items_end_html;
        $pdf->writeHTML($items_html,true, false, false, false, "");
        //$pdf->writeHTMLCell(283,158,7,35,$items_html,1,0, false, true, "R", false);




//        $footertext1 = "<b>Handtekening Suikerrock</b>";
//        $footertext2 = "<b>Handtekening Vereniging</b>";
//        $pdf->SetFont('helvetica', '', 12);
//        $pdf->writeHTMLCell(65, 23, 7, 175, $footertext1, 1, 1, false, true, "L", true);
//        $pdf->writeHTMLCell(65, 23, 77, 175, $footertext2, 1, 1, false, true, "L", true);


        // ---------------------------------------------------------

        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output($pdf_filename, 'I');




    }

    private function getRapportArrayInt(\DateTime $van, \DateTime $tot)
    {
        $line = array();
        $rapportReturnArray = array();
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        // add to from and start date a time


        $van->setTime(0,0,0);
        $tot->setTime(23,59,0);
//        var_dump($van);
//        var_dump($tot);
//        exit();


        /** @var OrderType $ordertype_dranklevering */
        $ordertype_dranklevering = $em->getRepository('AppBundle:OrderType')->findOneBy(array('smstype_id' => 13));

        /** @var ArrayCollection $drankstand_orders */
        //$drankstand_orders = $em->getRepository('AppBundle:Order')->findBy(array('ordertype' => $ordertype_dranklevering),array('createdAt' => 'ASC'));

        $drankstand_orders = $em->createQuery(
            "SELECT o FROM AppBundle:Order o WHERE o.ordertype = :ordertype and o.createdAt > :datefrom and o.createdAt < :dateto ORDER BY o.createdAt ASC")
            ->setParameter('ordertype',$ordertype_dranklevering)
            ->setParameter('datefrom', $van)
            ->setParameter('dateto', $tot)->getResult();


        $rapportReturnArray = array();
        //var_dump($drankstand_orders);
        //exit();
        if ($drankstand_orders == null)
        {
            //$drankstand_orders = "Nothing Found!";
            $ar_drinks = "Nothing found!";

        }
        else
        {
            // zoek alle dranksoorten voor leveringen
            $arc_drinks = new ArrayCollection();
            /** @var Order $drankstandorder */
            foreach ($drankstand_orders as $drankstandorder) {
                if (strpos($drankstandorder->getDrankstand()->getNaam(), "Intern -") !== false)
                {
                    /** @var OrderDrank $orderdrank_item */
                    foreach ($drankstandorder->getOd() as $orderdrank_item) {
                        $arToAdd = array('id' => $orderdrank_item->getDrank()->getId(), 'naam' => $orderdrank_item->getDrank()->__toString(), 'volgorde' => $orderdrank_item->getDrank()->getRapportvolgorde());
                        if (!$arc_drinks->contains($arToAdd)) {
                            $arc_drinks[] = $arToAdd;

                        }
                    }
                }
            }

            if ($arc_drinks->count() > 0) {

                // sorteer drank volgens rapport_volgorde en dan volgens naam
                $ar_drinks = $arc_drinks->toArray();
                foreach ($ar_drinks as $key => $row) {
                    $drnaam[$key] = $row['naam'];
                    $drvolgorde[$key] = $row['volgorde'];
                }
                array_multisort($drvolgorde, SORT_ASC, $drnaam, SORT_ASC, $ar_drinks);
                // var_dump($arc_drinks);
                //exit();
                // maak eerste lijn
                $line = array();
                // eerste kolom = drank item
                array_push($line, array("Items", ""));
                // eerste festivaldag betekend geen dag -1 gegevens ophalen voor rapport (beginstock =  1ste levering)
                // vanaf dag 2: beginstock is gelijk aan eindstock dag -1


                $drankstanden = $em->getRepository("AppBundle:DrankStand")->findAll();
                foreach ($drankstanden as $drankStand) {
                    // dranknaam start met Intern, enkel dan toevoegen
                    if (strpos($drankStand->getNaam(), "Intern") !== false) {
//

                        $drankstand_orders = $em->createQuery(
                            "SELECT o FROM AppBundle:Order o WHERE o.ordertype = :ordertype and o.drankstand = :drankstand and o.createdAt > :datefrom and o.createdAt < :dateto ORDER BY o.createdAt ASC")
                            ->setParameter('ordertype', $ordertype_dranklevering)
                            ->setParameter('datefrom', $van)
                            ->setParameter('drankstand', $drankStand)
                            ->setParameter('dateto', $tot)->getResult();
                        //$drankstand_orders = $em->getRepository('AppBundle:Order')->findBy(array('ordertype' => $ordertype_dranklevering, 'drankstand' => $drankStand),array('createdAt' => 'ASC'));
                        if (!$drankstand_orders == null) {
                            array_push($line, array($drankStand->getNaam(), ""));
                        }
                    }
                }
                array_push($line, array("Totaal", ""));
//            if (!$drankstand_orders == null) {
//                    /** @var Order $order */
//                    foreach ($drankstand_orders as $order) {
//                        array_push($line, array($order->getDrankstand()->getNaam(), $order->getCreatedAt()->format("H:i")));
//                    }
//                }


                $rapportReturnArray[] = $line;

                // lus door alle dranken, en voeg de lijnen toe
                $linecounter = 0;


                // adding DATA
                foreach ($ar_drinks as $drink) {
                    $linecounter++;
                    $line = array();
                    // eerste kolom = drank
                    array_push($line, $drink['naam']);

                    $drankstanden = $em->getRepository("AppBundle:DrankStand")->findAll();
                    $totaalalledrankstanden = 0;
                    foreach ($drankstanden as $drankStand) {
                        // dranknaam start met Intern, enkel dan toevoegen
                        if (strpos($drankStand->getNaam(), "Intern") !== false) {
                            $drankstand_orders = $em->createQuery(
                                "SELECT o FROM AppBundle:Order o WHERE o.ordertype = :ordertype and o.drankstand = :drankstand and o.createdAt > :datefrom and o.createdAt < :dateto ORDER BY o.createdAt ASC")
                                ->setParameter('ordertype', $ordertype_dranklevering)
                                ->setParameter('datefrom', $van)
                                ->setParameter('drankstand', $drankStand)
                                ->setParameter('dateto', $tot)->getResult();
                            //$drankstand_orders = $em->getRepository('AppBundle:Order')->findBy(array('ordertype' => $ordertype_dranklevering, 'drankstand' => $drankStand),array('createdAt' => 'ASC'));
                            if (!$drankstand_orders == null) {
                                // tel alles samen van 1 bepaalde drank
                                $totaal = 0;
                                $foundDrank = false;
                                foreach ($drankstand_orders as $order) {


                                    /** @var OrderDrank $item */
                                    foreach ($order->getOd() as $item) {

                                        if ($item->getDrank()->getId() == $drink['id']) {
                                            $totaal = $totaal + $item->getHoeveel();
                                            $foundDrank = true;
                                        }
                                    }
                                }

                                if ($foundDrank == false) {
                                    array_push($line, 0);
                                } else {
                                    array_push($line, $totaal);
                                    $totaalalledrankstanden = $totaalalledrankstanden + $totaal;
                                }
                            }
                        }
                    }
                    array_push($line, $totaalalledrankstanden);


//
//                if (!$drankstand_orders == null) {
//                    /** @var Order $order */
//                    foreach ($drankstand_orders as $order) {
//                        $foundDrank = false;
//                        /** @var OrderDrank $item */
//                        foreach ($order->getOd() as $item) {
//                            if ($item->getDrank()->getId() == $drink['id']) {
//                                array_push($line, $item->getHoeveel());
//                                $foundDrank = true;
//                            }
//                        }
//                        if ($foundDrank == false)
//                        {
//                            array_push($line, 0);
//                        }
//
//                    }
//
//                }


                    $rapportReturnArray[] = $line;

//                    $rapportReturnArray[] = $line;
                }
            }
            else
            {
                $ar_drinks = "Nothing found!";
            }


//                $arc_drinks = new ArrayCollection();
//                /** @var Order $drankstandorder */
//                foreach ($drankstand_orders as $drankstandorder)
//                {
//                    /** @var OrderDrank $orderdrank_item */
//                    foreach ($drankstandorder->getOd() as $orderdrank_item)
//                    {
//                        $arToAdd = array('id' => $orderdrank_item->getDrank()->getId(),'naam' => $orderdrank_item->getDrank()->getNaam(),'volgorde' => $orderdrank_item->getDrank()->getRapportvolgorde());
//                        if (!$arc_drinks->contains($arToAdd)) {
//                            $arc_drinks[] = $arToAdd;
//                        }
//
//                    }
//                }




            // get all dranks in all orders

        }
        return $rapportReturnArray;
    }

    private function getRapportArrayHoofdStockLeveringen()
    {
        $line = array();
        $rapportReturnArray = array();
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        // add to from and start date a time

        /** @var OrderType $ordertype_dranklevering */
        $ordertype_dranklevering = $em->getRepository('AppBundle:OrderType')->findOneBy(array('smstype_id' => 4));

        /** @var ArrayCollection $drankstand_orders */
        $drankstand_orders = $em->getRepository('AppBundle:Order')->findBy(array('ordertype' => $ordertype_dranklevering),array('createdAt' => 'ASC'));

        //$drankstand_orders = $em->getRepository('AppBundle:Order')->findAll();
//        $drankstand_orders = $em->createQuery(
//            "SELECT o FROM AppBundle:Order o WHERE o.ordertype = :ordertype and o.createdAt > :datefrom and o.createdAt < :dateto ORDER BY o.createdAt ASC")
//            ->setParameter('ordertype',$ordertype_dranklevering)
//            ->setParameter('datefrom', $van)
//            ->setParameter('dateto', $tot)->getResult();


        $rapportReturnArray = array();
        //var_dump($drankstand_orders);
        //exit();
        if ($drankstand_orders == null)
        {
            //$drankstand_orders = "Nothing Found!";
            $ar_drinks = "Nothing found!";

        }
        else
        {
            // zoek alle dranksoorten voor leveringen
            $arc_drinks = new ArrayCollection();
            /** @var Order $drankstandorder */
            foreach ($drankstand_orders as $drankstandorder) {

                    /** @var OrderDrank $orderdrank_item */
                    foreach ($drankstandorder->getOd() as $orderdrank_item) {
                        $arToAdd = array('id' => $orderdrank_item->getDrank()->getId(), 'naam' => $orderdrank_item->getDrank()->__toString(), 'volgorde' => $orderdrank_item->getDrank()->getRapportvolgorde());
                        if (!$arc_drinks->contains($arToAdd)) {
                            $arc_drinks[] = $arToAdd;
                        }
                    }

            }

            if ($arc_drinks->count() > 0) {

                // sorteer drank volgens rapport_volgorde en dan volgens naam
                $ar_drinks = $arc_drinks->toArray();
                foreach ($ar_drinks as $key => $row) {
                    $drnaam[$key] = $row['naam'];
                    $drvolgorde[$key] = $row['volgorde'];
                }
                array_multisort($drvolgorde, SORT_ASC, $drnaam, SORT_ASC, $ar_drinks);
                // var_dump($arc_drinks);
                //exit();
                // maak eerste lijn
                $line = array();
                // eerste kolom = drank item
                array_push($line, array("Items", ""));
                // eerste festivaldag betekend geen dag -1 gegevens ophalen voor rapport (beginstock =  1ste levering)
                // vanaf dag 2: beginstock is gelijk aan eindstock dag -1


//                $drankstanden = $em->getRepository("AppBundle:DrankStand")->findAll();
//                foreach ($drankstanden as $drankStand) {
//                    // dranknaam start met Intern, enkel dan toevoegen
//                    if (strpos($drankStand->getNaam(), "Intern") !== false) {
////

//                        $drankstand_orders = $em->createQuery(
//                            "SELECT o FROM AppBundle:Order o WHERE o.ordertype = :ordertype and o.drankstand = :drankstand and o.createdAt > :datefrom and o.createdAt < :dateto ORDER BY o.createdAt ASC")
//                            ->setParameter('ordertype', $ordertype_dranklevering)
//                            ->setParameter('datefrom', $van)
//                            ->setParameter('drankstand', $drankStand)
//                            ->setParameter('dateto', $tot)->getResult();
                        //$drankstand_orders = $em->getRepository('AppBundle:Order')->findBy(array('ordertype' => $ordertype_dranklevering, 'drankstand' => $drankStand),array('createdAt' => 'ASC'));
//                        if (!$drankstand_orders == null) {
//                            array_push($line, array($drankStand->getNaam(), ""));
//                        }
//                    }
//                }
//                array_push($line, array("Totaal", ""));
//            if (!$drankstand_orders == null) {
//                    /** @var Order $order */
//                    foreach ($drankstand_orders as $order) {
//                        array_push($line, array($order->getDrankstand()->getNaam(), $order->getCreatedAt()->format("H:i")));
//                    }
//                }
                array_push($line,array("leveringnr",""));
                array_push($line,array("datum",""));
                array_push($line,array("aantal",""));


                $rapportReturnArray[] = $line;

                // lus door alle dranken, en voeg de lijnen toe
                $linecounter = 0;


                // adding DATA
                foreach ($ar_drinks as $drink) {
                    $linecounter++;
                    $line = array();
                    // eerste kolom = drank
                    array_push($line, $drink['naam']);

//                    $drankstanden = $em->getRepository("AppBundle:DrankStand")->findAll();
//                    $totaalalledrankstanden = 0;
//                    foreach ($drankstanden as $drankStand) {
//                        // dranknaam start met Intern, enkel dan toevoegen
//                        if (strpos($drankStand->getNaam(), "Intern") !== false) {
//                            $drankstand_orders = $em->createQuery(
//                                "SELECT o FROM AppBundle:Order o WHERE o.ordertype = :ordertype and o.drankstand = :drankstand and o.createdAt > :datefrom and o.createdAt < :dateto ORDER BY o.createdAt ASC")
//                                ->setParameter('ordertype', $ordertype_dranklevering)
//                                ->setParameter('datefrom', $van)
//                                ->setParameter('drankstand', $drankStand)
//                                ->setParameter('dateto', $tot)->getResult();
                            //$drankstand_orders = $em->getRepository('AppBundle:Order')->findBy(array('ordertype' => $ordertype_dranklevering, 'drankstand' => $drankStand),array('createdAt' => 'ASC'));
                            if (!$drankstand_orders == null) {
                                // tel alles samen van 1 bepaalde drank
                                $totaal = 0;
                                $foundDrank = false;
                                /** @var Order $order */
                                $counter = 0;
                                foreach ($drankstand_orders as $order) {

                                    /** @var OrderDrank $item */
                                    foreach ($order->getOd() as $item) {
                                        if ($item->getDrank()->getId() == $drink['id']) {
                                            $counter++;
                                            if($counter != 1)
                                            {
                                                array_push($line, "");
                                            }
                                            array_push($line, $order->getOrdernotes());
                                            array_push($line, $order->getCreatedAt()->format("d-m-Y"));
                                            array_push($line, $item->getHoeveel());
                                            $rapportReturnArray[] = $line;
                                            $line = array();
                                            //$totaal = $totaal + $item->getHoeveel();
                                            //$foundDrank = true;
                                        }
                                    }

                                }

                                //array_push($line, $totaalalledrankstanden);
//                                if ($foundDrank == false) {
//                                    array_push($line, 0);
//                                } else {
//                                    array_push($line, $totaal);
//                                    $totaalalledrankstanden = $totaalalledrankstanden + $totaal;
//                                }
                            }
                       // }
                  //  }



//
//                if (!$drankstand_orders == null) {
//                    /** @var Order $order */
//                    foreach ($drankstand_orders as $order) {
//                        $foundDrank = false;
//                        /** @var OrderDrank $item */
//                        foreach ($order->getOd() as $item) {
//                            if ($item->getDrank()->getId() == $drink['id']) {
//                                array_push($line, $item->getHoeveel());
//                                $foundDrank = true;
//                            }
//                        }
//                        if ($foundDrank == false)
//                        {
//                            array_push($line, 0);
//                        }
//
//                    }
//
//                }




//                    $rapportReturnArray[] = $line;
                }
            }
            else
            {
                $ar_drinks = "Nothing found!";
            }


//                $arc_drinks = new ArrayCollection();
//                /** @var Order $drankstandorder */
//                foreach ($drankstand_orders as $drankstandorder)
//                {
//                    /** @var OrderDrank $orderdrank_item */
//                    foreach ($drankstandorder->getOd() as $orderdrank_item)
//                    {
//                        $arToAdd = array('id' => $orderdrank_item->getDrank()->getId(),'naam' => $orderdrank_item->getDrank()->getNaam(),'volgorde' => $orderdrank_item->getDrank()->getRapportvolgorde());
//                        if (!$arc_drinks->contains($arToAdd)) {
//                            $arc_drinks[] = $arToAdd;
//                        }
//
//                    }
//                }




            // get all dranks in all orders

        }
        return $rapportReturnArray;
    }

    /**
     * @Route("/admin/rapport/drankstand/print/{drankstandid}/{festivaldagid}/{naamvereniging}", name="admin_rapport_drankstand_print")
     * @Security("has_role('ROLE_RAPPORT_DRANKSTAND')")
     */
    public function rapportDrankstandPrintAction($drankstandid, $festivaldagid, $naamvereniging)
    {
        $em = $this->getDoctrine()->getEntityManager();
        /** @var FestivalDag $selFestivaldag */
        $selFestivaldag = $em->getRepository("AppBundle:FestivalDag")->find($festivaldagid);
        /** @var DrankStand $selDrankstand */
        $selDrankstand = $em->getRepository("AppBundle:DrankStand")->find($drankstandid);

        $printdata = $this->getRepportArray($selDrankstand,$selFestivaldag);

        $pdf = $this->container->get("white_october.tcpdf")->create();


        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Suikerrock');
        $pdf->SetTitle('Rapport Drankstand');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');
        // set header content
        $headertext1 = "<h1>Rapport DrankStand ".$selDrankstand->getNaam()."</h1>";


        $headertext2 = "<table width=\"100%\"><tr><td width=\"50%\"><b>Naam Vereniging: </b>".$naamvereniging."</td><td align=\"right\" width=\"50%\"><b>Festival Dag: </b>".$selFestivaldag->getFestdate()->format("Y-m-d")."</td></tr></table>";


        //$pdf->setOwnHeaderTitle($headertext1);
        //$pdf->setOwnHeaderText($headertext2);
        $pdf->SetHeaderData('uploads/images/logo_suikerrock_30_jaar_front.png', '', $headertext1, $headertext2);


        //$pdf->setPrintFooter(false);



        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(7,37,5,true);
        $pdf->setHeaderMargin(5);
        $pdf->setFooterMargin(50);


        // set auto page breaks
        $pdf->SetAutoPageBreak(true, 10);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);



        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 10, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage('L', "A4");

        // set JPEG quality
        $pdf->setJPEGQuality(75);

        // Image example with resizing
        //$pdf->Image('uploads/images/logo_suikerrock_30_jaar_front.png', 5, 5, 50, 20, 'PNG', '', '', false, 150, '', false, false, 1, false, false, false);

        // set text shadow effect
        // $pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        // Set some content to print


        $items_begin_html = <<<EOD
 
            <table width="1000" align="center" cellspacing="0" cellpadding="3" border="1">
            <tbody>
            
EOD;
        $items_end_html = <<<EOD
        </tbody>
    </table>
 
    
    
    
EOD;

        $items_content_html = "";
        $prevtitle = "";
        $rowcounter = 0;
        $colcounter = 0;

        foreach ($printdata as $line)
        {
            $rowcounter++;
            if ($rowcounter == 1)
            {
                $items_content_html = $items_content_html . "<tr>";
                foreach ($line as $cols)
                {
                    $colcounter++;
                    if ($colcounter == 1)
                    {
                        $items_content_html = $items_content_html . "<td align=\"left\" width=\"300\">".$cols[0]."<br>".$cols[1]."</td>";

                    }
                    else
                    {
                        $items_content_html = $items_content_html . "<td align=\"center\" width=\"70\">".$cols[0]."<br>".$cols[1]."</td>";
                    }
                }
                $items_content_html = $items_content_html . "</tr>";

            }
            else
            {
                $items_content_html = $items_content_html . "<tr>";
                $colcounter = 0;
                foreach ($line as $cols)
                {
                    $colcounter++;
                    if ($colcounter == 1)
                    {
                        $items_content_html = $items_content_html . "<td align=\"left\" width='300'>".$cols."</td>";

                    }
                    else
                    {
                        $items_content_html = $items_content_html . "<td align=\"center\" width='70'>".$cols."</td>";
                    }
                }
                $items_content_html = $items_content_html . "</tr>";
            }
        }










        $pdf->SetFont('helvetica', '', 10);
        //$totalitems = $order->getOd()->count();
//        if ($totalitems > 19) {
//            $itempositioncounter = 0;
//
//            $pdf->setPrintFooter(false);



        $items_html = $items_begin_html .$items_content_html .$items_end_html;
        $pdf->writeHTML($items_html,true, false, false, false, "");
        //$pdf->writeHTMLCell(283,158,7,35,$items_html,1,0, false, true, "R", false);




//        $footertext1 = "<b>Handtekening Suikerrock</b>";
//        $footertext2 = "<b>Handtekening Vereniging</b>";
//        $pdf->SetFont('helvetica', '', 12);
//        $pdf->writeHTMLCell(65, 23, 7, 175, $footertext1, 1, 1, false, true, "L", true);
//        $pdf->writeHTMLCell(65, 23, 77, 175, $footertext2, 1, 1, false, true, "L", true);


        // ---------------------------------------------------------

        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output('example_001.pdf', 'I');




    }

    private function getRepportArray(DrankStand $selDrankstand, FestivalDag $selFestivaldag)
    {
        $line = array();
        $rapportReturnArray = array();

        $em = $this->getDoctrine()->getManager();
        // get all orderID = levering afgesloten voor drankstand + eindstock sorteren op festivaldag nr

        /** @var OrderType $ordertype_dranklevering */
        $ordertype_dranklevering = $em->getRepository('AppBundle:OrderType')->findOneBy(array('smstype_id' => 13));
        /** @var OrderType $ordertype_drankeindstock */
        $ordertype_drankeindstock = $em->getRepository('AppBundle:OrderType')->findOneBy(array('smstype_id' => 2));


        /** @var ArrayCollection $drankstand_orders */
        $drankstand_orders = $em->getRepository('AppBundle:Order')->findBy(array('ordertype' => $ordertype_dranklevering, 'drankstand' => $selDrankstand, 'festivaldag' => $selFestivaldag),array('createdAt' => 'ASC'));
        /** @var ArrayCollection $drankstand_eindstock */
        $drankstand_eindstock = $em->getRepository('AppBundle:Order')->findBy(array('ordertype' => $ordertype_drankeindstock, 'drankstand' => $selDrankstand, 'festivaldag' => $selFestivaldag));

        $festivaldagMinEen = $em->getRepository('AppBundle:FestivalDag')->findOneBy(array('festday' => $selFestivaldag->getFestday()-1));
        /** @var Order $drankstand_eindstock_gisteren */
        $drankstand_eindstock_gisteren = $em->getRepository('AppBundle:Order')->findBy(array('ordertype' => $ordertype_drankeindstock, 'drankstand' => $selDrankstand, 'festivaldag' => $festivaldagMinEen ),array('smsDateTime' => 'ASC'));



        $rapportReturnArray = array();
        if ($drankstand_orders == null)
        {
            //$drankstand_orders = "Nothing Found!";
            $ar_drinks = "Nothing found!";
        } else

        {
            // zoek alle dranksoorten voor leveringen
            $arc_drinks = new ArrayCollection();
            /** @var Order $drankstandorder */
            foreach ($drankstand_orders as $drankstandorder)
            {
                /** @var OrderDrank $orderdrank_item */
                foreach ($drankstandorder->getOd() as $orderdrank_item)
                {
                    $arToAdd = array('id' => $orderdrank_item->getDrank()->getId(),'naam' => $orderdrank_item->getDrank()->__toString(),'volgorde' => $orderdrank_item->getDrank()->getRapportvolgorde());
                    if (!$arc_drinks->contains($arToAdd)) {
                        $arc_drinks[] = $arToAdd;
                    }

                }
            }
            // zoek alle dranksoorten voor eindstock ingave
            if (!$drankstand_eindstock == null) {

                /** @var Order $drankstandorder */
                foreach ($drankstand_eindstock as $drankstandorder) {
                    /** @var OrderDrank $orderdrank_item */
                    foreach ($drankstandorder->getOd() as $orderdrank_item) {
                        $arToAdd = array('id' => $orderdrank_item->getDrank()->getId(), 'naam' => $orderdrank_item->getDrank()->__toString(), 'volgorde' => $orderdrank_item->getDrank()->getRapportvolgorde());
                        if (!$arc_drinks->contains($arToAdd)) {
                            $arc_drinks[] = $arToAdd;
                        }
                    }
                }
            }


            // zoek alle dranksoorten voor eindstock festivaldag -1
            if ($selFestivaldag->getFestday() <> 1) {
                if (!$drankstand_eindstock_gisteren == null) {

                    /** @var Order $drankstandorder */

                    foreach ($drankstand_eindstock_gisteren as $drankstandorder) {
                        /** @var OrderDrank $orderdrank_item */
                        foreach ($drankstandorder->getOd() as $orderdrank_item) {

                            $arToAdd = array('id' => $orderdrank_item->getDrank()->getId(), 'naam' => $orderdrank_item->getDrank()->__toString(), 'volgorde' => $orderdrank_item->getDrank()->getRapportvolgorde());
                            if (!$arc_drinks->contains($arToAdd)) {
                                $arc_drinks[] = $arToAdd;
                            }
                        }
                    }

                }

            }


            // sorteer drank volgens rapport_volgorde en dan volgens naam
            $ar_drinks = $arc_drinks->toArray();
            foreach ($ar_drinks as $key => $row) {
                $drnaam[$key] = $row['naam'];
                $drvolgorde[$key] = $row['volgorde'];
            }
            array_multisort($drvolgorde, SORT_ASC, $drnaam, SORT_ASC, $ar_drinks);
           // var_dump($arc_drinks);
            //exit();
            // maak eerste lijn
            $line = array();
            // eerste kolom = drank item
            array_push($line, array("Items",""));
            // eerste festivaldag betekend geen dag -1 gegevens ophalen voor rapport (beginstock =  1ste levering)
            // vanaf dag 2: beginstock is gelijk aan eindstock dag -1


            if ($selFestivaldag->getFestday() <> 1) {
                // haal beginstock op van dag -1 (check of er gegevens zijn, en maak kolom voor beginstock)
                if (!$drankstand_eindstock_gisteren == null)
                {
                    /** @var Order $order */
                    foreach ($drankstand_eindstock_gisteren as $order) {
                        array_push($line, array('beginstock', $order->getCreatedAt()->format("H:i")));
                    }
                }
                else
                {
                    array_push($line, array('N.B.', ""));
                    //throw new Exception('error! : geen eindstock voor drankstand: '. $selDrankstand->getNaam() . " op festivaldag: ". $festivaldagMinEen->getFestdate()->format("Y-m-d"));
                }

                if (!$drankstand_orders == null) {
                    /** @var Order $order */
                    foreach ($drankstand_orders as $order) {
                        array_push($line, array('levering', $order->getCreatedAt()->format("H:i")));
                    }
                }
                if (!$drankstand_eindstock == null) {
                    //$cols = $drankstand_eindstock->count();
                    /** @var Order $order */
                    foreach ($drankstand_eindstock as $order) {
                        array_push($line, array('eindstock', $order->getCreatedAt()->format("H:i")));
                    }

                    array_push($line, array('verbruik', ""));
                }
            }
            else
            {
                // eerste festivaldag. Beginstock = eerste levering
                if (!$drankstand_orders == null) {
                    $ordercounter = 0;
                    /** @var Order $order */
                    foreach ($drankstand_orders as $order) {
                        $ordercounter++;
                        if ($ordercounter == 1)
                        {
                            array_push($line, array('beginstock', $order->getCreatedAt()->format("H:i")));
                        }
                        else
                        {
                            array_push($line, array('levering', $order->getCreatedAt()->format("H:i")));
                        }

                    }
                }
                if (!$drankstand_eindstock == null) {
                    //$cols = $drankstand_eindstock->count()
                    foreach ($drankstand_eindstock as $order) {
                        array_push($line, array('eindstock', $order->getCreatedAt()->format("H:i")));
                        array_push($line, array('verbruik', ""));
                    }

                }
            }

            $rapportReturnArray[] = $line;

            // lus door alle dranken, en voeg de lijnen toe
            $linecounter = 0;

            // adding DATA
            foreach ($ar_drinks as $drink)
            {
                $linecounter++;
                $line = array();
                // eerste kolom = drank
                array_push($line, $drink['naam']);
                // eerste festivaldag betekend geen dag -1 gegevens ophalen voor rapport (beginstock =  1ste levering)
                // vanaf dag 2: beginstock is gelijk aan eindstock dag -1

                if ($selFestivaldag->getFestday() <> 1) {
                    // haal beginstock op van dag -1 (check of er gegevens zijn, en maak kolom voor beginstock)
                    //$festivaldagMinEen = $em->getRepository('AppBundle:FestivalDag')->findOneBy(array('festday' => $selFestivaldag->getFestday()-1));
                    /** @var Order $drankstand_eindstock_gisteren */
                   // $drankstand_eindstock_gisteren = $em->getRepository('AppBundle:Order')->findOneBy(array('ordertype' => $ordertype_drankeindstock, 'drankstand' => $selDrankstand, 'festivaldag' => $festivaldagMinEen ),array('smsDateTime' => 'ASC'));
                    if (!$drankstand_eindstock_gisteren == null)
                    {
                        // loop true all order product items and find the current in this line product, if not exist throw exception

                        foreach ($drankstand_eindstock_gisteren as $order) {
                            $foundDrank = false;
                            /** @var OrderDrank $item */
                            foreach ($order->getOd() as $item) {

                                if ($item->getDrank()->getId() == $drink['id']) {
                                    array_push($line, $item->getHoeveel());
                                    $foundDrank = true;
                                }

                            }
                            if ($foundDrank == false)
                            {
                                array_push($line, 0);
                            }
                        }

//                            array_push($line, array('beginstock', $drankstand_eindstock_gisteren->getSmsDateTime()->format("H:i:s")));
                    }
                    else
                    {
                        array_push($line, "0"); //geen eindstock dag -1
                        //throw new Exception('error! : geen eindstock voor drankstand: '. $selDrankstand->getNaam() . " op festivaldag: ". $festivaldagMinEen->getFestdate()->format("Y-m-d"));
                    }

                    if (!$drankstand_orders == null) {
                        /** @var Order $order */
                        foreach ($drankstand_orders as $order) {
                            $foundDrank = false;
                            /** @var OrderDrank $item */
                            foreach ($order->getOd() as $item) {
                                if ($item->getDrank()->getId() == $drink['id']) {
                                    array_push($line, $item->getHoeveel());
                                    $foundDrank = true;
                                }
                            }
                            if ($foundDrank == false)
                            {
                                array_push($line, 0);
                            }

                        }

                    }


                    if (!$drankstand_eindstock == null) {
                        /** @var Order $order */
                        foreach ($drankstand_eindstock as $order)
                        {
                            $foundDrank = false;
                            /** @var OrderDrank $item */
                            foreach ($order->getOd() as $item) {
                                if ($item->getDrank()->getId() == $drink['id']) {
                                    $totaalverbruik = 0;
                                    foreach ($line as $aantal)
                                    {
                                        $totaalverbruik = $totaalverbruik + $aantal;
                                    }

                                    array_push($line, $item->getHoeveel());
                                    array_push($line,$totaalverbruik-$item->getHoeveel());
                                    $foundDrank = true;
                                }

                            }
                            if ($foundDrank == false)
                            {
                                array_push($line, 0);
                                $totaalverbruik = 0;
                                foreach ($line as $aantal)
                                {
                                    $totaalverbruik = $totaalverbruik + $aantal;

                                }
                                array_push($line,$totaalverbruik);
                               // array_push($line,"&nbsp;");
                            }
                        }
                    }
                }
                else
                {
                    // eerste festivaldag. Beginstock = eerste levering
                    if (!$drankstand_orders == null) {

                        /** @var Order $order */
                        foreach ($drankstand_orders as $order) {
                            $foundDrank = false;
                            /** @var OrderDrank $item */
                            foreach ($order->getOd() as $item) {
                                if ($item->getDrank()->getId() == $drink['id']) {
                                    array_push($line, $item->getHoeveel());
                                    $foundDrank = true;
                                }
                            }
                            if ($foundDrank == false)
                            {
                                array_push($line, 0);

                            }
                        }
                    }
                    if (!$drankstand_eindstock == null) {
                        /** @var Order $order */
                        foreach ($drankstand_eindstock as $order) {
                            $foundDrank = false;
                            /** @var OrderDrank $item */
                            foreach ($order->getOd() as $item) {
                                if ($item->getDrank()->getId() == $drink['id']) {

                                    $totaalverbruik = 0;
                                    foreach ($line as $aantal)
                                    {
                                        $totaalverbruik = $totaalverbruik + $aantal;
                                    }
                                    array_push($line, $item->getHoeveel());
                                    array_push($line,$totaalverbruik-$item->getHoeveel());
                                    $foundDrank = true;
                                }
                            }
                            if ($foundDrank == false)
                            {
                                array_push($line, 0); // eindstock item niet ingegegeven dus niet meer in stock.
                               //  indien verbruik dient geteld te worden (alles wat ingegeven wordt, uncomment volgende lijnen
                                $totaalverbruik = 0;
                                foreach ($line as $aantal)
                                {
                                    $totaalverbruik = $totaalverbruik + $aantal;

                                }
                                array_push($line,$totaalverbruik);
                                //array_push($line,"&nbsp;");
                            }
                        }

                    }
                }

                $rapportReturnArray[] = $line;

//                    $rapportReturnArray[] = $line;
            }



//                $arc_drinks = new ArrayCollection();
//                /** @var Order $drankstandorder */
//                foreach ($drankstand_orders as $drankstandorder)
//                {
//                    /** @var OrderDrank $orderdrank_item */
//                    foreach ($drankstandorder->getOd() as $orderdrank_item)
//                    {
//                        $arToAdd = array('id' => $orderdrank_item->getDrank()->getId(),'naam' => $orderdrank_item->getDrank()->getNaam(),'volgorde' => $orderdrank_item->getDrank()->getRapportvolgorde());
//                        if (!$arc_drinks->contains($arToAdd)) {
//                            $arc_drinks[] = $arToAdd;
//                        }
//
//                    }
//                }




            // get all dranks in all orders

        }
        return $rapportReturnArray;
    }
    private function getAllDrankRapportArray(FestivalDag $selFestivaldag)
    {
        $line = array();
        $rapportReturnArray = array();

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        // get all orderID = levering afgesloten voor drankstand + eindstock sorteren op festivaldag nr

        ///** @var OrderType $ordertype_dranklevering */
        //$ordertype_dranklevering = $em->getRepository('AppBundle:OrderType')->findOneBy(array('smstype_id' => 13));
        /** @var OrderType $ordertype_drankeindstock */
        $ordertype_drankeindstock = $em->getRepository('AppBundle:OrderType')->findOneBy(array('smstype_id' => 2));

        $alldranks = $em->getRepository("AppBundle:DrankSoort")->findAll();
        $arc_drinks = new ArrayCollection();
        /** @var AppBundle::DrankSoort $item */
        foreach ($alldranks as $item)
        {
            $arc_drinks[] = array('id' => $item->getId(), 'naam' => $item->__toString(), 'volgorde' => $item->getRapportvolgorde());
        }
        // sorteer drank volgens rapport_volgorde en dan volgens naam
        $ar_drinks = $arc_drinks->toArray();
        foreach ($ar_drinks as $key => $row) {
            $drnaam[$key] = $row['naam'];
            $drvolgorde[$key] = $row['volgorde'];
        }
        array_multisort($drvolgorde, SORT_ASC, $drnaam, SORT_ASC, $ar_drinks);
        //var_dump($ar_drinks);
        //exit();
        // maak eerste lijn
        $line = array();
        // eerste kolom = drank item
        array_push($line, array("Items",""));
        array_push($line, array("Hoofdstock",""));
        /** @var ArrayCollection $drankstand_eindstock */
        $drankstand_eindstock = $em->getRepository('AppBundle:Order')->findBy(array('ordertype' => $ordertype_drankeindstock,'festivaldag' => $selFestivaldag));

        if (!$drankstand_eindstock == null) {
            //$cols = $drankstand_eindstock->count()
            /** @var Order $order */
            foreach ($drankstand_eindstock as $order) {
                array_push($line, array("Eindstock voor", $order->getDrankstand()->getNaam()));
            }
            array_push($line, array('Totaal Stock', ""));
        }


        $rapportReturnArray[] = $line;

        // lus door alle dranken, voeg hoofstock toe voor lus 1, voeg dan alle eindstocks toe zoals hier boven, bereken dan het totaal.
        $colcounter = 0;

        // adding DATA
        foreach ($ar_drinks as $drink)
        {
            $colcounter++;
            $totaalinstock = 0;
            $line = array();
            // eerste kolom = drank
            array_push($line, $drink['naam']);

            // voor eerste lus voeg hoofdstock toe voor drank.
//            if ($colcounter == 1)
//            {
                /** @var \AppBundle\Entity\DrankSoort $hoofdstockdrank */
                $hoofdstockdrank = $em->getRepository("AppBundle:DrankSoort")->find($drink['id']);
                array_push($line,$hoofdstockdrank->getStock());

       //     } else {
                // voor de volgende lussen, check deze drank voor de eindstocks, als niet gevonden in een eindstock, zet 0

                if (!$drankstand_eindstock == null) {
                    // loop true all order product items and find the current in this line product, if not exist throw exception

                    foreach ($drankstand_eindstock as $order) {
                        $foundDrank = false;
                        /** @var OrderDrank $item */
                        foreach ($order->getOd() as $item) {

                            if ($item->getDrank()->getId() == $drink['id']) {
                                array_push($line, $item->getHoeveel());
                                $totaalinstock = $totaalinstock + $item->getHoeveel();
                                $foundDrank = true;
                            }
                        }
                        if ($foundDrank == false) {
                            array_push($line, 0);
                        }
                    }
                    array_push($line, $totaalinstock);

//                            array_push($line, array('beginstock', $drankstand_eindstock_gisteren->getSmsDateTime()->format("H:i:s")));
                } else {
                    array_push($line, "0"); //geen eindstock dag -1
                    //throw new Exception('error! : geen eindstock voor drankstand: '. $selDrankstand->getNaam() . " op festivaldag: ". $festivaldagMinEen->getFestdate()->format("Y-m-d"));
                }
          //  }

           $rapportReturnArray[] = $line;

//                    $rapportReturnArray[] = $line;


        }

        return $rapportReturnArray;
    }

    /**
     * @Route("/admin/rapport/hoofdstocklevering", name="admin_rapport_hoofdstocklevering")
     * @Security("has_role('ROLE_RAPPORT_INTERN')")
     */
    public function rapportLeveringHoofdStockAction()
    {
        // replace this example code with whatever you need
        $data = array();
        $em = $this->getDoctrine()->getEntityManager();
        $rapportReturnArray = $this->getRapportArrayHoofdStockLeveringen();
        //var_dump($rapportReturnArray);
        //exit();

        return $this->render('AppBundle:rapport:HoofdStockOrders.html.twig', array(
            'orders' => $rapportReturnArray,
        ));
    }


    /**
     * @Route("/admin/rapport/intern", name="admin_rapport_intern")
     * @Security("has_role('ROLE_RAPPORT_INTERN')")
     */
    public function rapportInternAction(Request $request)
    {
        // replace this example code with whatever you need
        $data = array();
        $em = $this->getDoctrine()->getEntityManager();

        //$activeFestivalDag = $em->getRepository("AppBundle:FestivalDag")->findByfestactive(1);
//        dump($activeFestivalDag);
//        exit();
        $editForm = $this->createFormBuilder($data)
            ->add('van', 'date', array(
                    'label' => "Van",
                    'required' => true,
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'attr' => array(
                        'class' => 'form-control input-inline datepicker',
                        'data-provide' => 'datepicker',
                        'data-date-format' => 'dd/mm/yyyy'
                    )
                )

            )
            ->add('tot', 'date', array(
                    'label' => "Tot",
                    'widget' => 'single_text',
                    'required' => true,
                    'format' => 'dd/MM/yyyy',
                    'attr' => array(
                        'class' => 'form-control input-inline datepicker',
                        'data-provide' => 'datepicker',
                        'data-date-format' => 'dd/mm/yyyy'
                    )
                )

            )
            ->getForm();

        if ('POST' == $request->getMethod())
        {
            $editForm->handleRequest($request);
            //$data = $editForm->getData();

            if ($editForm->isValid()) {
                $formData = $editForm->getData();
                // array 0 contains festivaldag

                /** @var DateTime $selvan */
                $selvan = $formData['van'];
                /** @var DateTime $seltot */
                $seltot = $formData['tot'];


                $datevan = DateTime::createFromFormat('d/m/Y', $formData['van']->format("d/m/Y"));
                //echo $datevan->format('Y-m-d');
                $datetot = DateTime::createFromFormat('d/m/Y', $formData['tot']->format("d/m/Y"));
                //echo $datetot->format('Y-m-d');





//            var_dump($selvan->format('u'));
//
//            var_dump($datevan);
//            var_dump($datetot);
//            exit();

                $rapportReturnArray = $this->getRapportArrayInt($datevan,$datetot);
                return $this->render('AppBundle:rapport:drankstandOrdersInt.html.twig', array(
                    'orders' => $rapportReturnArray,
                    'van' => $datevan,
                    'tot' => $datetot
                ));
            }
            else
            {
                echo "Invalid FORM DATA";
                exit();
            }







//            $nieuwedag->setFestActive(1);

//            // update all active to 0 and then update only 1 to active
//            $qb = $em->createQueryBuilder();
//            $q = $qb->update('AppBundle:FestivalDag', 'f')
//                ->set('f.festactive', '?1')
//                ->setParameter(1,'0')
//                ->getQuery();
//            $q->execute();
//
//
//
//            $em->persist($nieuwedag);
//            $em->flush();



            //return $this->redirectToRoute('admin_rapport_drankstanden');
        }

        return $this->render('@App/rapport/selecteerintern.html.twig', array(
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * @Route("/admin/rapport/drankstanden", name="admin_rapport_drankstanden")
     * @Security("has_role('ROLE_RAPPORT_DRANKSTAND')")
     */
    public function rapportDrankstandenAction(Request $request)
    {
        // replace this example code with whatever you need
        $data = array();
        $em = $this->getDoctrine()->getEntityManager();

        $activeFestivalDag = $em->getRepository("AppBundle:FestivalDag")->findByfestactive(1);
//        dump($activeFestivalDag);
//        exit();
        $editForm = $this->createFormBuilder($data)
            ->add('festivaldag', EntityType::class,
                array(
                    'label' => 'Actieve Festivaldag',
                    'class' => 'AppBundle:FestivalDag',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('f')->orderBy('f.festdate', 'ASC');
                    },
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'data' => $em->getReference("AppBundle:FestivalDag",$activeFestivalDag[0]->getId())
                )

            )
            ->add('drankstand', EntityType::class,
                array('label' => 'Drankstand',
                    'class' => 'AppBundle\Entity\DrankStand',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('ds');
                    },
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true
                )
            )
            ->add('vereniging', TextType::class,
                array('label' => "Naam vereniging:",
                    'required' => true,
                    'attr' => array('size' => '35')
                )
            )
            ->getForm();

        if ('POST' == $request->getMethod())
        {
            $editForm->handleRequest($request);
            $data = $editForm->getData();


            // array 0 contains festivaldag
            /** @var FestivalDag $selFestivaldag */
            $selFestivaldag = $data['festivaldag'];
            /** @var DrankStand $selDrankstand */
            $selDrankstand = $data['drankstand'];
            $txtVereniging = $data['vereniging'];

            $rapportReturnArray = $this->getRepportArray($selDrankstand,$selFestivaldag);

            return $this->render('AppBundle:rapport:drankstandOrders.html.twig', array(
                'orders' => $rapportReturnArray,
                'festivaldag' => $selFestivaldag,
                'drankstand' => $selDrankstand,
                'vereniging' => $txtVereniging
            ));


//            $nieuwedag->setFestActive(1);

//            // update all active to 0 and then update only 1 to active
//            $qb = $em->createQueryBuilder();
//            $q = $qb->update('AppBundle:FestivalDag', 'f')
//                ->set('f.festactive', '?1')
//                ->setParameter(1,'0')
//                ->getQuery();
//            $q->execute();
//
//
//
//            $em->persist($nieuwedag);
//            $em->flush();



            //return $this->redirectToRoute('admin_rapport_drankstanden');
        }

        return $this->render('@App/rapport/selecteer.html.twig', array(
            'edit_form' => $editForm->createView(),
        ));
    }
    /**
     * @Route("/admin/rapport/stockall", name="admin_rapport_stockall")
     * @Security("has_role('ROLE_RAPPORT_DRANKSTAND')")
     */
    public function rapportStockAllAction(Request $request)
    {
        // replace this example code with whatever you need
        $data = array();
        $em = $this->getDoctrine()->getEntityManager();

        $activeFestivalDag = $em->getRepository("AppBundle:FestivalDag")->findByfestactive(1);
//        dump($activeFestivalDag);
//        exit();
        $editForm = $this->createFormBuilder($data)
            ->add('festivaldag', EntityType::class,
                array(
                    'label' => 'Actieve Festivaldag',
                    'class' => 'AppBundle:FestivalDag',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('f')->orderBy('f.festdate', 'ASC');
                    },
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'data' => $em->getReference("AppBundle:FestivalDag",$activeFestivalDag[0]->getId())
                )

            )
            ->getForm();

        if ('POST' == $request->getMethod())
        {
            $editForm->handleRequest($request);
            $data = $editForm->getData();

            // array 0 contains festivaldag
            /** @var FestivalDag $selFestivaldag */
            $selFestivaldag = $data['festivaldag'];

            $rapportReturnArray = $this->getAllDrankRapportArray($selFestivaldag);

            return $this->render('AppBundle:rapport:drankstandStockAll.html.twig', array(
                'orders' => $rapportReturnArray,
                'festivaldag' => $selFestivaldag,
            ));

        }

        return $this->render('@App/rapport/selecteerstockall.html.twig', array(
            'edit_form' => $editForm->createView(),
        ));
    }

    function verticletext($string)
    {
        $vtext ="";
        $tlen = strlen($string);
        for($i=0;$i<$tlen;$i++)
        {
            $vtext .= substr($string,$i,1)."<br />";
        }
        return $vtext;
    }
}
