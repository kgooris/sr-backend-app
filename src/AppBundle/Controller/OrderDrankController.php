<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\OrderDrank;
use AppBundle\Form\OrderDrankType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * OrderDrank controller.
 *
 * @Route("/admin/orderdrank")
 * @Security("has_role('ROLE_ADMIN')")
 */
class OrderDrankController extends Controller
{
    /**
     * Lists all OrderDrank entities.
     *
     * @Route("/", name="admin_orderdrank_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $orderDranks = $em->getRepository('AppBundle:OrderDrank')->findAll();

        return $this->render('orderdrank/index.html.twig', array(
            'orderDranks' => $orderDranks,
        ));
    }

    /**
     * Creates a new OrderDrank entity.
     *
     * @Route("/new", name="admin_orderdrank_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $orderDrank = new OrderDrank();
        $form = $this->createForm('AppBundle\Form\OrderDrankType', $orderDrank);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($orderDrank);
            $em->flush();

            return $this->redirectToRoute('admin_orderdrank_show', array('id' => $orderdrank->getId()));
        }

        return $this->render('orderdrank/new.html.twig', array(
            'orderDrank' => $orderDrank,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a OrderDrank entity.
     *
     * @Route("/{id}", name="admin_orderdrank_show")
     * @Method("GET")
     */
    public function showAction(OrderDrank $orderDrank)
    {
        $deleteForm = $this->createDeleteForm($orderDrank);

        return $this->render('orderdrank/show.html.twig', array(
            'orderDrank' => $orderDrank,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing OrderDrank entity.
     *
     * @Route("/{id}/edit", name="admin_orderdrank_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, OrderDrank $orderDrank)
    {
        $deleteForm = $this->createDeleteForm($orderDrank);
        $editForm = $this->createForm('AppBundle\Form\OrderDrankType', $orderDrank);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($orderDrank);
            $em->flush();

            return $this->redirectToRoute('admin_orderdrank_edit', array('id' => $orderDrank->getId()));
        }

        return $this->render('orderdrank/edit.html.twig', array(
            'orderDrank' => $orderDrank,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a OrderDrank entity.
     *
     * @Route("/{id}", name="admin_orderdrank_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, OrderDrank $orderDrank)
    {
        $form = $this->createDeleteForm($orderDrank);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($orderDrank);
            $em->flush();
        }

        return $this->redirectToRoute('admin_orderdrank_index');
    }

    /**
     * Creates a form to delete a OrderDrank entity.
     *
     * @param OrderDrank $orderDrank The OrderDrank entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(OrderDrank $orderDrank)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_orderdrank_delete', array('id' => $orderDrank->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
