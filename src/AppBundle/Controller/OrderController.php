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
 * @Route("/admin/order")
 * @Security("has_role('ROLE_ADMIN')")
 */
class OrderController extends Controller
{
    /**
     * Lists all Order entities.
     *
     * @Route("/", name="admin_order_index")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        //$sql = "SELECT i FROM AppBundle:Order i";
        //$em->createQuery($sql);
        $orders = $em->getRepository('AppBundle:Order')->findAll();
        //$paginator = $this->get("knp_paginator");
        //$orders = $paginator->paginate(
        //    $sql,
        //    $this->get('request')->query->get('page', 1)/*page number*/,10/*limit per page*/
        //);


        return $this->render('AppBundle:order:index.html.twig', array(
            'orders' => $orders,
        ));
    }



    /**
     * Creates a new Order entity.
     *
     * @Route("/new", name="admin_order_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction(Request $request)
    {
        $order = new Order();
        $em = $this->getDoctrine()->getManager();
        $activeFestivalDag = $em->getRepository("AppBundle:FestivalDag")->findByfestactive(1);
        $order->setFestivaldag($activeFestivalDag[0]);
        
        $form = $this->createForm('AppBundle\Form\OrderType', $order);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //exit(\Doctrine\Common\Util\Debug::dump($order));
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $order->setOwner($user);

            $em->persist($order);
            $em->flush();

            return $this->redirectToRoute('admin_order_show', array('id' => $order->getId()));
        }

        return $this->render('AppBundle:order:new.html.twig', array(
            'order' => $order,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Order entity.
     *
     * @Route("/{id}", name="admin_order_show")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAction(Order $order)
    {
        $deleteForm = $this->createDeleteForm($order);

        return $this->render('AppBundle:order:show.html.twig', array(
            'order' => $order,
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing Order entity.
     *
     * @Route("/{id}/edit", name="admin_order_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
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

            return $this->redirectToRoute('admin_order_edit', array('id' => $order->getId()));
        }

        return $this->render('AppBundle:order:edit.html.twig', array(
            'order' => $order,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        ));
    }


    /**
     * Deletes a Order entity.
     *
     * @Route("/{id}", name="admin_order_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
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
            return $this->redirectToRoute('admin_order_mainstock');
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
            return $this->createFormBuilder()
                ->setAction($this->generateUrl('admin_order_delete', array('id' => $order->getId())))
                ->setMethod('DELETE')
                ->getForm();


    }

}
