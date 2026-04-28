<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\OrderType;
use AppBundle\Form\OrderTypeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * OrderType controller.
 *
 * @Route("/admin/settings/ordertype")
 * @Security("has_role('ROLE_ADMIN')")
 */
class OrderTypeController extends Controller
{
    /**
     * Lists all OrderType entities.
     *
     * @Route("/", name="admin_settings_ordertype_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $orderTypes = $em->getRepository('AppBundle:OrderType')->findAll();

        return $this->render('AppBundle:ordertype:index.html.twig', array(
            'orderTypes' => $orderTypes,
        ));
    }

    /**
     * Creates a new OrderType entity.
     *
     * @Route("/new", name="admin_settings_ordertype_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $orderType = new OrderType();
        $form = $this->createForm('AppBundle\Form\OrderTypeType', $orderType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($orderType);
            $em->flush();

            return $this->redirectToRoute('admin_settings_ordertype_show', array('id' => $orderType->getId()));
        }

        return $this->render('AppBundle:ordertype:new.html.twig', array(
            'orderType' => $orderType,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a OrderType entity.
     *
     * @Route("/{id}", name="admin_settings_ordertype_show")
     * @Method("GET")
     */
    public function showAction(OrderType $orderType)
    {
        $deleteForm = $this->createDeleteForm($orderType);

        return $this->render('AppBundle:ordertype:show.html.twig', array(
            'orderType' => $orderType,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing OrderType entity.
     *
     * @Route("/{id}/edit", name="admin_settings_ordertype_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, OrderType $orderType)
    {
        $deleteForm = $this->createDeleteForm($orderType);
        $editForm = $this->createForm('AppBundle\Form\OrderTypeType', $orderType);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($orderType);
            $em->flush();

            return $this->redirectToRoute('admin_settings_ordertype_edit', array('id' => $orderType->getId()));
        }

        return $this->render('AppBundle:ordertype:edit.html.twig', array(
            'orderType' => $orderType,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a OrderType entity.
     *
     * @Route("/{id}", name="admin_settings_ordertype_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, OrderType $orderType)
    {
        $form = $this->createDeleteForm($orderType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($orderType);
            $em->flush();
        }

        return $this->redirectToRoute('admin_settings_ordertype_index');
    }

    /**
     * Creates a form to delete a OrderType entity.
     *
     * @param OrderType $orderType The OrderType entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(OrderType $orderType)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_settings_ordertype_delete', array('id' => $orderType->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
