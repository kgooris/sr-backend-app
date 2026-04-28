<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\DrankEenheid;
use AppBundle\Form\DrankEenheidType;

/**
 * DrankEenheid controller.
 *
 * @Route("/admin/settings/drankeenheden")
 */
class DrankEenheidController extends Controller
{
    /**
     * Lists all DrankEenheid entities.
     *
     * @Route("/", name="admin_settings_drankeenheden_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $drankEenheids = $em->getRepository('AppBundle:DrankEenheid')->findAll();

        return $this->render('drankeenheid/index.html.twig', array(
            'drankEenheids' => $drankEenheids,
        ));
    }

    /**
     * Creates a new DrankEenheid entity.
     *
     * @Route("/new", name="admin_settings_drankeenheden_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $drankEenheid = new DrankEenheid();
        $form = $this->createForm('AppBundle\Form\DrankEenheidType', $drankEenheid);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($drankEenheid);
            $em->flush();

            return $this->redirectToRoute('admin_settings_drankeenheden_show', array('id' => $drankEenheid->getId()));
        }

        return $this->render('drankeenheid/new.html.twig', array(
            'drankEenheid' => $drankEenheid,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a DrankEenheid entity.
     *
     * @Route("/{id}", name="admin_settings_drankeenheden_show")
     * @Method("GET")
     */
    public function showAction(DrankEenheid $drankEenheid)
    {
        $deleteForm = $this->createDeleteForm($drankEenheid);

        return $this->render('drankeenheid/show.html.twig', array(
            'drankEenheid' => $drankEenheid,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing DrankEenheid entity.
     *
     * @Route("/{id}/edit", name="admin_settings_drankeenheden_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, DrankEenheid $drankEenheid)
    {
        $deleteForm = $this->createDeleteForm($drankEenheid);
        $editForm = $this->createForm('AppBundle\Form\DrankEenheidType', $drankEenheid);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($drankEenheid);
            $em->flush();

            return $this->redirectToRoute('admin_settings_drankeenheden_edit', array('id' => $drankEenheid->getId()));
        }

        return $this->render('drankeenheid/edit.html.twig', array(
            'drankEenheid' => $drankEenheid,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a DrankEenheid entity.
     *
     * @Route("/{id}", name="admin_settings_drankeenheden_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, DrankEenheid $drankEenheid)
    {
        $form = $this->createDeleteForm($drankEenheid);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($drankEenheid);
            $em->flush();
        }

        return $this->redirectToRoute('admin_settings_drankeenheden_index');
    }

    /**
     * Creates a form to delete a DrankEenheid entity.
     *
     * @param DrankEenheid $drankEenheid The DrankEenheid entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DrankEenheid $drankEenheid)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_settings_drankeenheden_delete', array('id' => $drankEenheid->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
