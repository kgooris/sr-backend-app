<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\DrankStand;
use AppBundle\Form\DrankStandType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * DrankStand controller.
 *
 * @Route("/admin/settings/drankstand")
 *
 */
class DrankStandController extends Controller
{
    /**
     * Lists all DrankStand entities.
     *
     * @Route("/", name="admin_settings_drankstand_index")
     * @Security("has_role('ROLE_BASICADMIN')")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $drankStands = $em->getRepository('AppBundle:DrankStand')->findBy(array(),array('naam' => 'ASC'));

        return $this->render('AppBundle:drankstand:index.html.twig', array(
            'drankStands' => $drankStands,
        ));
    }

    /**
     * Creates a new DrankStand entity.
     *
     * @Route("/new", name="admin_settings_drankstand_new")
     * @Security("has_role('ROLE_BASICADMIN')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $drankStand = new DrankStand();
        $form = $this->createForm('AppBundle\Form\DrankStandType', $drankStand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($drankStand);
            $em->flush();

            return $this->redirectToRoute('admin_settings_drankstand_show', array('id' => $drankStand->getId()));
        }
        $em = $this->getDoctrine()->getManager();
        
        $drankStands = $em->getRepository('AppBundle:DrankStand')->findAll();

        return $this->render('AppBundle:drankstand:new.html.twig', array(
            'drankStand' => $drankStand,
            'form' => $form->createView(),
        	'drankStands' => $drankStands,
        ));
    }

    /**
     * Finds and displays a DrankStand entity.
     *
     * @Route("/{id}", name="admin_settings_drankstand_show")
     * @Security("has_role('ROLE_BASICADMIN')")
     * @Method("GET")
     */
    public function showAction(DrankStand $drankStand)
    {
        $deleteForm = $this->createDeleteForm($drankStand);

        return $this->render('AppBundle:drankstand:show.html.twig', array(
            'drankStand' => $drankStand,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing DrankStand entity.
     *
     * @Route("/{id}/edit", name="admin_settings_drankstand_edit")
     * @Security("has_role('ROLE_BASICADMIN')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, DrankStand $drankStand)
    {
        $deleteForm = $this->createDeleteForm($drankStand);
        $editForm = $this->createForm('AppBundle\Form\DrankStandType', $drankStand);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($drankStand);
            $em->flush();

            return $this->redirectToRoute('admin_settings_drankstand_edit', array('id' => $drankStand->getId()));
        }

        return $this->render('AppBundle:drankstand:edit.html.twig', array(
            'drankStand' => $drankStand,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a DrankStand entity.
     *
     * @Route("/{id}", name="admin_settings_drankstand_delete")
     * @Security("has_role('ROLE_BASICADMIN')")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, DrankStand $drankStand)
    {
        $form = $this->createDeleteForm($drankStand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($drankStand);
            $em->flush();
        }

        return $this->redirectToRoute('admin_settings_drankstand_index');
    }

    /**
     * Creates a form to delete a DrankStand entity.
     *
     * @param DrankStand $drankStand The DrankStand entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DrankStand $drankStand)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_settings_drankstand_delete', array('id' => $drankStand->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
