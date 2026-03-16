<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\SmsNotifType;
use AppBundle\Form\SmsNotifTypeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * SmsNotifType controller.
 *
 * @Route("/admin/settings/smsnotiftype")
 * @Security("has_role('ROLE_ADMIN')")
 */
class SmsNotifTypeController extends Controller
{
    /**
     * Lists all SmsNotifType entities.
     *
     * @Route("/", name="admin_settings_smsnotiftype_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $smsNotifTypes = $em->getRepository('AppBundle:SmsNotifType')->findAll();

        return $this->render('smsnotiftype/index.html.twig', array(
            'smsNotifTypes' => $smsNotifTypes,
        ));
    }

    /**
     * Creates a new SmsNotifType entity.
     *
     * @Route("/new", name="admin_settings_smsnotiftype_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $smsNotifType = new SmsNotifType();
        $form = $this->createForm('AppBundle\Form\SmsNotifTypeType', $smsNotifType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($smsNotifType);
            $em->flush();

            return $this->redirectToRoute('admin_settings_smsnotiftype_show', array('id' => $smsNotifType->getId()));
        }

        return $this->render('smsnotiftype/new.html.twig', array(
            'smsNotifType' => $smsNotifType,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a SmsNotifType entity.
     *
     * @Route("/{id}", name="admin_settings_smsnotiftype_show")
     * @Method("GET")
     */
    public function showAction(SmsNotifType $smsNotifType)
    {
        $deleteForm = $this->createDeleteForm($smsNotifType);

        return $this->render('smsnotiftype/show.html.twig', array(
            'smsNotifType' => $smsNotifType,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing SmsNotifType entity.
     *
     * @Route("/{id}/edit", name="admin_settings_smsnotiftype_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, SmsNotifType $smsNotifType)
    {
        $deleteForm = $this->createDeleteForm($smsNotifType);
        $editForm = $this->createForm('AppBundle\Form\SmsNotifTypeType', $smsNotifType);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($smsNotifType);
            $em->flush();

            return $this->redirectToRoute('admin_settings_smsnotiftype_edit', array('id' => $smsNotifType->getId()));
        }

        return $this->render('smsnotiftype/edit.html.twig', array(
            'smsNotifType' => $smsNotifType,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a SmsNotifType entity.
     *
     * @Route("/{id}", name="admin_settings_smsnotiftype_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, SmsNotifType $smsNotifType)
    {
        $form = $this->createDeleteForm($smsNotifType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($smsNotifType);
            $em->flush();
        }

        return $this->redirectToRoute('admin_settings_smsnotiftype_index');
    }

    /**
     * Creates a form to delete a SmsNotifType entity.
     *
     * @param SmsNotifType $smsNotifType The SmsNotifType entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(SmsNotifType $smsNotifType)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_settings_smsnotiftype_delete', array('id' => $smsNotifType->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
