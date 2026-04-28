<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
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

/**
 * Order controller.
 *
 * @Route("/admin/mainstock")
 */
class MainStockController extends Controller
{

    /**
     * Lists all Order entities.
     *
     * @Route("/", name="admin_mainstock_index")
     * @Method("GET")
     * @Security("has_role('ROLE_MAINSTOCK_LIST')")
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        // get mainstock

        $mainstock = $em->getRepository('AppBundle:DrankStand')->findOneByNaam('HoofdStock');

        $orders = $em->getRepository('AppBundle:Order')->findByDrankstand($mainstock->getId());
        return $this->render('AppBundle:order:/mainstock/index.html.twig', array(
            'orders' => $orders,
        ));
    }

    /**
     * Creates a new Order entity.
     *
     * @Route("/new", name="admin_mainstock_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_MAINSTOCK_NEW')")
     */
    public function newAction(Request $request)
    {
        $order = new Order();
        $order->setOrdertype($this->getDoctrine()->getRepository('AppBundle:OrderType')->findOneBy(array('smstype_id' => 4)));
        $order->setDrankstand($this->getDoctrine()->getRepository('AppBundle:DrankStand')->findOneBySmscode(11111111));
        $order->setSmsBestelNr(999);
        $order->setSmsDateTime(new \DateTime());

        $form = $this->createForm('AppBundle\Form\OrderType_mainstock', $order);
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


            $this->addFlash(
                'notice',
                array(
                    'alert' => 'success',
                    'title' => 'Success!',
                    'message' => 'Your changes where saved.'
                )
            );

            $em->persist($order);
            $em->flush();

            return $this->redirectToRoute('admin_mainstock_show', array('id' => $order->getId()));
        }

        return $this->render('AppBundle:order:/mainstock/new.html.twig', array(
            'order' => $order,
            'form' => $form->createView(),
        ));
    }


    /**
     * Finds and displays a Order entity.
     *
     * @Route("/{id}", name="admin_mainstock_show")
     * @Method("GET")
     * @Security("has_role('ROLE_MAINSTOCK_LIST')")
     */
    public function showAction(Order $order)
    {
        $deleteForm = $this->createDeleteForm($order);

        return $this->render('AppBundle:order:/mainstock/show.html.twig', array(
            'order' => $order,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Order entity.
     *
     * @Route("/{id}/edit", name="admin_mainstock_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_MAINSTOCK_EDIT')")
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

            return $this->redirectToRoute('admin_mainstock_show', array('id' => $order->getId()));
        }


        return $this->render('AppBundle:order:/mainstock/edit.html.twig', array(
            'order' => $order,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Deletes a Order entity.
     *
     * @Route("/{id}", name="admin_mainstock_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_MAINSTOCK_DELETE')")
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
        if ($order->getOrdertype()->getSmstypeId() == 4) {
            return $this->redirectToRoute('admin_mainstock_index');
        } else {
            return $this->redirectToRoute('admin_order_index');
        }
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
        if ($order->getOrdertype()->getSmstypeId() == 4) {
            return $this->createFormBuilder()
                ->setAction($this->generateUrl('admin_mainstock_delete', array('id' => $order->getId())))
                ->setMethod('DELETE')
                ->getForm();
        } else {
            return $this->createFormBuilder()
                ->setAction($this->generateUrl('admin_order_delete', array('id' => $order->getId())))
                ->setMethod('DELETE')
                ->getForm();

        }
    }

}
