<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\FestivalDag;
use AppBundle\Form\FestivalDagType;

/**
 * FestivalDag controller.
 *
 * @Route("/admin/settings/festivaldag")
 */
class FestivalDagController extends Controller
{
    /**
     * Lists all FestivalDag entities.
     *
     * @Route("/", name="admin_settings_festivaldag_index")
     * @Method("GET")
     * @Security("has_role('ROLE_BASICADMIN')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $festivalDags = $em->getRepository('AppBundle:FestivalDag')->findAll();

        return $this->render('festivaldag/index.html.twig', array(
            'festivalDags' => $festivalDags,
        ));
    }

    /**
     * Creates a new FestivalDag entity.
     *
     * @Route("/new", name="admin_settings_festivaldag_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_BASICADMIN')")
     */
    public function newAction(Request $request)
    {
        $festivalDag = new FestivalDag();
        $festivalDag->setFestActive(0);
        $form = $this->createForm('AppBundle\Form\FestivalDagType', $festivalDag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $em = $this->getDoctrine()->getManager();
            $em->persist($festivalDag);
            $em->flush();

            return $this->redirectToRoute('admin_settings_festivaldag_show', array('id' => $festivalDag->getId()));
        }

        return $this->render('festivaldag/new.html.twig', array(
            'festivalDag' => $festivalDag,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a FestivalDag entity.
     *
     * @Route("/{id}", name="admin_settings_festivaldag_show")
     * @Method("GET")
     * @Security("has_role('ROLE_BASICADMIN')")
     */
    public function showAction(FestivalDag $festivalDag)
    {
        $deleteForm = $this->createDeleteForm($festivalDag);

        return $this->render('festivaldag/show.html.twig', array(
            'festivalDag' => $festivalDag,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing FestivalDag entity.
     *
     * @Route("/{id}/edit", name="admin_settings_festivaldag_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_BASICADMIN')")
     */
    public function editAction(Request $request, FestivalDag $festivalDag)
    {
        $deleteForm = $this->createDeleteForm($festivalDag);
        $editForm = $this->createForm('AppBundle\Form\FestivalDagType', $festivalDag);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($festivalDag);
            $em->flush();

            return $this->redirectToRoute('admin_settings_festivaldag_edit', array('id' => $festivalDag->getId()));
        }

        return $this->render('festivaldag/edit.html.twig', array(
            'festivalDag' => $festivalDag,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a FestivalDag entity.
     *
     * @Route("/{id}", name="admin_settings_festivaldag_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_BASICADMIN')")
     */
    public function deleteAction(Request $request, FestivalDag $festivalDag)
    {
        $form = $this->createDeleteForm($festivalDag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($festivalDag);
            $em->flush();
        }

        return $this->redirectToRoute('admin_settings_festivaldag_index');
    }

    /**
     * Creates a form to delete a FestivalDag entity.
     *
     * @param FestivalDag $festivalDag The FestivalDag entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(FestivalDag $festivalDag)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_settings_festivaldag_delete', array('id' => $festivalDag->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
