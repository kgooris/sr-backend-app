<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
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
use libphonenumber\PhoneNumber;

/**
 * Order controller.
 *
 * @Route("/admin/nood")
 */
class NoodController extends Controller
{

    /**
     * Lists all Order entities.
     *
     * @Route("/", name="admin_nood_index")
     * @Method("GET")
     * @Security("has_role('ROLE_NOOD_LIST')")
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
          WHERE ot.smstype_id = 21 ');
        $orders = $query->getResult();

        //$orders = $em->getRepository('AppBundle:Order')->findByDrankstand($drankbestelling->getId());
        return $this->render('AppBundle:order:/noodoproepen/index.html.twig', array(
            'orders' => $orders,
        ));
    }


    /**
     * Creates a new Order entity.
     *
     * @Route("/new", name="admin_nood_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_NOOD_NEW')")
     */
    public function newAction(Request $request)
    {
        $order = new Order();
        $em = $this->getDoctrine()->getManager();
        $activeFestivalDag = $em->getRepository("AppBundle:FestivalDag")->findByfestactive(1);
        $order->setFestivaldag($activeFestivalDag[0]);
        
        $order->setOrdertype($this->getDoctrine()->getRepository('AppBundle:OrderType')->findOneBy(array('smstype_id' => 21)));
        $order->setSmsBestelNr(999);
        $order->setSmsDateTime(new \DateTime());

        $form = $this->createForm('AppBundle\Form\OrderType_nood', $order);
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

            return $this->redirectToRoute('admin_nood_show', array('id' => $order->getId()));
        }

        return $this->render('AppBundle:order:/noodoproepen/new.html.twig', array(
            'order' => $order,
            'form' => $form->createView(),
        ));
    }


    
    /**
     * Finds and displays a Order entity.
     *
     * @Route("/{id}", name="admin_nood_show")
     * @Method("GET")
     * @Security("has_role('ROLE_NOOD_LIST')")

     */
    public function showAction(Order $order)
    {
        $deleteForm = $this->createDeleteForm($order);

        return $this->render('AppBundle:order:/noodoproepen/show.html.twig', array(
            'order' => $order,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays a Order entity.
     *
     * @Route("/{id}/bevestig", name="admin_nood_bevestig")
     * @Method("GET")
     * @Security("has_role('ROLE_NOOD_BEVESTIG')")
     */
    public function confirmAction(Order $order)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $order->setOrdertype($this->getDoctrine()->getRepository('AppBundle:OrderType')->findOneBy(array('smstype_id' => 22)));
        $em->persist($order);
        $em->flush();

        // send message to drankstand confirmation
        $sms = new Outbox();
        $em2 = $this->getDoctrine()->getEntityManager('smsd');
        /** @var PhoneNumber $phonenr */
        $phonenr = $order->getDrankstand()->getGsm();

        $sms->setNumber("+" . $phonenr->getCountryCode() . $phonenr->getNationalNumber());
        $sms->setText("Hulp onderweg!");
        $em2->persist($sms);
        $em2->flush();



        $this->addFlash(
            'notice',
            array(
                'alert' => 'success',
                'title' => 'Success!',
                'message' => 'Bevestiging Opgeslagen en doorgestuurd naar de aanvrager'
            )
        );

        return $this->redirectToRoute('admin_nood_index');
    }

    /**
     * Displays a form to edit an existing Order entity.
     *
     * @Route("/{id}/edit", name="admin_nood_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_NOOD_EDIT')")
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

            return $this->redirectToRoute('admin_nood_show', array('id' => $order->getId()));
        }


        return $this->render('@App/order/noodoproepen/edit.html.twig', array(
            'order' => $order,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Deletes a Order entity.
     *
     * @Route("/{id}", name="admin_nood_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_NOOD_DELETE')")
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

        return $this->redirectToRoute('admin_nood_index');

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
            ->setAction($this->generateUrl('admin_nood_delete', array('id' => $order->getId())))
            ->setMethod('DELETE')
            ->getForm();


    }

}
