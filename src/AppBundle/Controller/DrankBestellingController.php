<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Model\SMSMessage;
use SMS\AdminBundle\Entity\Outbox;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Order;
use AppBundle\Form\OrderType;
use AppBundle\Entity\DrankStand;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\OrderDrank;


/**
 * Order controller.
 *
 * @Route("/admin/drankbestelling")
 */
class DrankBestellingController extends Controller
{

    /**
     * Lists all Order entities.
     *
     * @Route("/", name="admin_drankbestelling_index")
     * @Method("GET")
     * @Security("has_role('ROLE_BESTELLING_LIST')")
     */
    public function indexAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        // zoek bestellingen alleen die nog niet zijn gesloten.

        //$drankbestelling = $em->getRepository('AppBundle:DrankStand')->findOneByNaam('HoofdStock');
        //$order = $em->getRepository('AppBundle:OrderType')->findBy(
        //    array('smstype_id' => 10)
        //);
        $query = $em->createQuery('
          SELECT o
          FROM AppBundle:Order o JOIN o.ordertype ot
          WHERE ot.smstype_id = 11 or ot.smstype_id = 12');
        $orders = $query->getResult();

        //$orders = $em->getRepository('AppBundle:Order')->findByDrankstand($drankbestelling->getId());
        return $this->render('AppBundle:order:/drankbestelling/index.html.twig', array(
            'orders' => $orders,
        ));
    }


    /**
     * Creates a new Order entity.
     *
     * @Route("/new", name="admin_drankbestelling_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_BESTELLING_NEW')")
     */
    public function newAction(Request $request)
    {
        $order = new Order();
        $em = $this->getDoctrine()->getManager();
        $activeFestivalDag = $em->getRepository("AppBundle:FestivalDag")->findByfestactive(1);
        $order->setFestivaldag($activeFestivalDag[0]);
        
        $order->setOrdertype($this->getDoctrine()->getRepository('AppBundle:OrderType')->findOneBy(array('smstype_id' => 11)));
        $order->setSmsBestelNr(99999);
        $order->setSmsDateTime(new \DateTime());

        $form = $this->createForm('AppBundle\Form\OrderType_drankbestelling', $order);
        $form->handleRequest($request);

        // still to do :
        // add drankstand info
        // add smsdatetime
        // add smsordernr
        // add ordertype


        if ($form->isSubmitted() && $form->isValid()) {
            // TODO: doublecheck making sure all non editable preset values are still set correctly.

            $em = $this->getDoctrine()->getManager();
            //exit(\Doctrine\Common\Util\Debug::dump($order));
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $order->setOwner($user);

            $em->persist($order);
            $em->flush();

            return $this->redirectToRoute('admin_drankbestelling_show', array('id' => $order->getId()));
        }

        return $this->render('AppBundle:order:/drankbestelling/new.html.twig', array(
            'order' => $order,
            'form' => $form->createView(),
        ));
    }


    /**
     * Finds and displays a Order entity.
     *
     * @Route("/print/{id}/{finalize}", name="admin_drankbestelling_print")
     * @Method("GET")
     * @Security("has_role('ROLE_BESTELLING_PRINT') or has_role('ROLE_BESTELLING_PRINTFIN')")
     */
    public function printAction(Order $order, $finalize)
    {

        if ($finalize == 1)
        {

            $this->denyAccessUnlessGranted('ROLE_BESTELLING_PRINTFIN', null, 'No access!!');
            $em = $this->getDoctrine()->getEntityManager();
            $order->setOrdertype($this->getDoctrine()->getRepository('AppBundle:OrderType')->findOneBy(array('smstype_id' => 12)));
            $em->persist($order);
            $em->flush();
            $sms = new Outbox();
            $em2 = $this->getDoctrine()->getEntityManager('smsd');

            $sms->setNumber("+32473251937");
            $sms->setText("PRINT GEDAAN!!!");
            $em2->persist($sms);
            $em2->flush();


        }
        else
        {
            $this->denyAccessUnlessGranted('ROLE_BESTELLING_PRINT', null, 'No access!!');
//            $em2 = $this->getDoctrine()->getEntityManager('smsd');
//            $sms = new Outbox();
//            $sms->setNumber("+32473251937");
//            $sms->setText("PRINT GEDAAN!!!");
//            $em2->persist($sms);
//            $em2->flush();
        }


        $pdf = $this->container->get("white_october.tcpdf")->create();


        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Kristof Gooris');
        $pdf->SetTitle('Bestelling Drankstand');
        $pdf->SetSubject('');
        $pdf->SetKeywords('');
        // set header content
        $headertext1 = "<h1>Drank Bestelling</h1>";
        $headertext2 = "<b>Drankstand: </b>".$order->getDrankstand()->getNaam();
        $headertext2 = $headertext2 . "<br/><b>BestelDatum: </b>".$order->getCreatedAt()->format("d/m/Y");
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
        $pdf->SetFont('dejavusans', '', 14, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage('P', "A5");

        // set JPEG quality
        $pdf->setJPEGQuality(75);

        // Image example with resizing
        //$pdf->Image('uploads/images/logo_suikerrock_30_jaar_front.png', 5, 5, 50, 20, 'PNG', '', '', false, 150, '', false, false, 1, false, false, false);

        // set text shadow effect
        // $pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        // Set some content to print
        $items_begin_html = <<<EOD
            
            <table cellspacing="0" cellpadding="3" border="1">
            <thead>
            <tr>
                <th width="300"><b>Soort</b></th>
                <th width="80" align="center"><b>Aantal</b></th>
                <th width="87" align="center"><b>Correctie</b></th>
            </tr>
            </thead>
            <tbody>
EOD;
        $items_end_html = <<<EOD
        </tbody>
    </table>
    
EOD;
        /** @var OrderDrank $item */
        $items_content_html = "";
        $pdf->SetFont('helvetica', '', 13);
        //$totalitems = $order->getOd()->count();
//        if ($totalitems > 19) {
//            $itempositioncounter = 0;
//
//            $pdf->setPrintFooter(false);
            foreach ($order->getOd() as $item) {
               // $itempositioncounter++;
                $items_content_html = $items_content_html . "<tr><td width=\"300\">" . $item->getDrank()->getEenheid()->getNaam() . " " . $item->getDrank()->getNaam() . "</td>";
                $items_content_html = $items_content_html . "<td width=\"80\" align=\"center\">" . $item->getHoeveel() . "</td><td width=\"87\"></td></tr>";
//                if ($itempositioncounter == 19)
//                {
//                    $items_html = $items_begin_html . $items_content_html .$items_end_html;
//
//                    if ($totalitems == $itempositioncounter)
//                    {
//                        $pdf->setPrintFooter(true);
//                    }
//                    else
//                    {
//                        $pdf->setPrintFooter(false);
//                    }
//
//                    $pdf->AddPage('P', "A5");
//                    $pdf->writeHTML($items_html,true, false, false, false, "");
//                    $items_content_html = "";
//                    $itempositioncounter = 0;
//
//
//                }
           }

//            $pdf->setPrintFooter(true);
  //         $pdf->AddPage('P', "A5");
          // $items_html = $items_begin_html . $items_content_html .$items_end_html;
    //       $pdf->writeHTML($items_html,true, false, false, false, "");


//        }
//        else
//        {
//            foreach ($order->getOd() as $item) {
//                $items_content_html = $items_content_html . "<tr><td width=\"300\">" . $item->getDrank()->getEenheid()->getNaam() . " " . $item->getDrank()->getNaam() . "</td>";
//                $items_content_html = $items_content_html . "<td width=\"80\" align=\"center\">" . $item->getHoeveel() . "</td><td width=\"87\"></td></tr>";
//            }
//        }

//        $totaal = 18;
//            $beginlus=0;
//            // zorgen voor nieuwe pagina vanaf lus 16
//            $itemstodo = $totaal;
//        for ($x = 1; $x<= $totaal; $x++)
//            {
//                $beginlus++;
//
//                //15 met footer
//                //19 zonder footer
//
//                // is totaal groter dan 15 -> huidige tabel vullen tot max 19 (lus)
//
//                // als totaal > 19
//               // if ($totaal > 16)
//                // als itemtodo > 16
//                if ($itemstodo > 0 && $beginlus == 17)
//                {
//
//                    $items_html = $items_begin_html . $items_content_html .$items_end_html;
//                    $pdf->writeHTML($items_html,true, false, false, false, "");
//                    $pdf->AddPage('P', "A5");
//                    $items_content_html = "<tr><td width=\"300\" >item</td><td width=\"80\" align=\"center\">5</td><td width=\"87\"></td></tr>";
//                    $beginlus = 0;
//                }
//                else
//                {
//                    $items_content_html = $items_content_html . "<tr><td width=\"300\" >item</td><td width=\"80\" align=\"center\">5</td><td width=\"87\"></td></tr>";
//                }
//                $itemstodo--;
//            }
            $items_html = $items_begin_html . $items_content_html .$items_end_html;
            $pdf->writeHTML($items_html,true, false, false, false, "");




        // als totaal kleiner is of gelijk aan is 19 en totaal groter is dan 16dan zorg voor niewe pagina na 16 stuks


        //$pdf->writeHTML($items_html,true, false, false, false, "");

        // Print text using writeHTMLCell()
        //$pdf->writeHTMLCell(80, 10, 60, 5, $headertext1, 0, 0, false, true, "L", true);
        //$pdf->SetFont('helvetica', '', 12);
        //$pdf->writeHTMLCell(80, 10, 61, 15, $headertext2, 0, 0, false, true, "L", true);
        //$pdf->writeHTMLCell(0, 1, 5, 25, "", "B", 1, false, true, "C", true);
        //$pdf->writeHTMLCell(0, 1, 5, 30, "", 0, 1, false, true, "C", true);
        //$pdf->setPageMark();

        //$pdf->writeHTMLCell(0,1,7,35,$items_html,0,1,false,true,"L",true);



        $footertext1 = "<b>Handtekening Suikerrock</b>";
        $footertext2 = "<b>Handtekening Vereniging</b>";
        $pdf->SetFont('helvetica', '', 12);

        ///$footertext3 = "<table>";
        // Set some content to print
        $footertext3 = <<<EOD
            <table cellspacing="0" cellpadding="0" border="1">
            <tr>
                <td height="100" width="200"><b>Handtekening Suikerrock</b></td>
                <td width="60"></td>
                <td width="200"><b>Handtekening Vereniging</b></td>
            </tr>
            </table>
EOD;
        $pdf->writeHTML($footertext3,true, false, false, false, "C");
        //$pdf->writeHTMLCell(65, 23, 7, 175, $footertext1, 1, 1, false, true, "L", true);
        //$pdf->writeHTMLCell(65, 23, 77, 175, $footertext2, 1, 1, false, true, "L", true);




         //$pdf->writeHTMLCell(0, 0, '', '', $items_html, 0, 1, 0, true, '', true);

        // ---------------------------------------------------------

        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output('example_001.pdf', 'I');


//        return $this->render('AppBundle:order:/drankbestelling/show.html.twig', array(
//            'order' => $order,
//            'delete_form' => $deleteForm->createView(),
//        ));
    }


    /**
     * Finds and displays a Order entity.
     *
     * @Route("/{id}", name="admin_drankbestelling_show")
     * @Method("GET")
     * @Security("has_role('ROLE_BESTELLING_LIST')")

     */
    public function showAction(Order $order)
    {
        $deleteForm = $this->createDeleteForm($order);

        
        //$smsMessage = $this->get('app.smsmessage');
        //$smsMessage->setSmstxt("2205201622457014831010099905000000000000000000000000000000000000000000000000000000000000000000000000000000");
        //$ord2 = $smsMessage->getOrder();
        //printf($smsMessage->getSmstxt());
        //\Doctrine\Common\Util\Debug::dump($ord2);
        //exit();


        return $this->render('AppBundle:order:/drankbestelling/show.html.twig', array(
            'order' => $order,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays a Order entity.
     *
     * @Route("/{id}/levershow", name="admin_drankbestelling_levershow")
     * @Method("GET")
     * @Security("has_role('ROLE_BESTELLING_MAAKLEVERING')")
     */
    public function leverShowAction(Order $order)
    {
        return $this->render('AppBundle:order:/drankbestelling/maaklevering.html.twig', array(
            'order' => $order,
        ));
    }
    /**
     * maak een bestelling automatisch een levering zonder aanpassingen.
     *
     * @Route("/{id}/leverok", name="admin_drankbestelling_leverok")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_BESTELLING_MAAKLEVERING')")
     */
    public function leverokAction(Request $request, Order $order)
    {
        $em = $this->getDoctrine()->getManager();
        $ordertype_levering = $em->getRepository("AppBundle:OrderType")->findOneBy(array('smstype_id' => 13));
     //   $ordertype_bestdone = $em->getRepository("AppBundle:OrderType")->findOneBy(array('smstype_id' => 12));
        //dump($ordertype_bestdone);
        //exit();
        // maak nieuwe Order, met informatie over $order
//        $levering = new Order();
//        $levering->setDrankstand($order->getDrankstand());
//        $levering->setOrdertype($ordertype_levering);
//        $levering->setSmsBestelNr(99999);
//        $levering->setSmsDateTime(new \DateTime("now"));
//        $levering->setFestivaldag($order->getFestivaldag());
//        foreach ($order->getOd() as $leveritem)
//        {
//            $levering->addOd($leveritem);
//        }
//        $user = $this->container->get('security.token_storage')->getToken()->getUser();
//        $levering->setOwner($user);
//

        $order->setOrdertype($ordertype_levering);

        $em->persist($order);
//        $em->persist($levering);

        $em->flush();

            return $this->redirectToRoute('admin_drankbestelling_index');
    }



    /**
     * Displays a form to edit an existing Order entity.
     *
     * @Route("/{id}/edit", name="admin_drankbestelling_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_BESTELLING_EDIT')")
     */
    public function editAction(Request $request, Order $order)
    {


        $deleteForm = $this->createDeleteForm($order);
        $editForm = $this->createForm('AppBundle\Form\OrderType', $order);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();

            return $this->redirectToRoute('admin_drankbestelling_show', array('id' => $order->getId()));
        }


        return $this->render('AppBundle:order:/drankbestelling/edit.html.twig', array(
            'order' => $order,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Deletes a Order entity.
     *
     * @Route("/{id}", name="admin_drankbestelling_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_BESTELLING_DELETE')")
     */
    public function deleteAction(Request $request, Order $order)
    {
        $form = $this->createDeleteForm($order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($order->getOrdertype()->getSmstypeId() == 4) {
                $originalOrder = $em->getRepository('AppBundle:Order')->find($order->getId());
                if (!$originalOrder) {
                    throw $this->createNotFoundException('No Order found for id ' . $order->getId());
                }
                /* @var \AppBundle\Entity\OrderDrank $od */
                foreach ($originalOrder->getod() as $od) {
                    //update the stock
                    $dranksoort = $od->getDrank();
                    $currentstock = $dranksoort->getStock();
                    $newstock = $currentstock - $od->getHoeveel();
                    $dranksoort->setStock($newstock);

                    //$order->removeOd($od);
                }
            }

            $em->remove($order);
            $em->flush();
        }

        return $this->redirectToRoute('admin_drankbestelling_index');

    }


    /**
     * Creates a form to delete a Order entity.
     *
     * @param Order $order The Order entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Order $order)
    {

        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_drankbestelling_delete', array('id' => $order->getId())))
            ->setMethod('DELETE')
            ->getForm();


    }

}
