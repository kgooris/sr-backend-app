<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\DrankSoort;
use AppBundle\Form\DrankSoortType;
use Uploadable\Fixture\Entity\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\OrderType;
use AppBundle\Entity\Order;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * DrankSoort controller.
 *
 * @Route("/admin/settings/dranksoort")
 */
class DrankSoortController extends Controller
{
    /**
     * Lists all DrankSoort entities.
     *
     * @Route("/", name="admin_settings_dranksoort_index")
     * @Method("GET")
     * @Security("has_role('ROLE_BASICADMIN')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        //$drankSoorts = $em->getRepository('AppBundle:DrankSoort')->findAll();
        //$drankSoorts = $em->getRepository('AppBundle:DrankSoort')->findAllOrderedByName();
        $drankSoorts = $em->getRepository('AppBundle:DrankSoort')->findAllOrderedBySMSPos();
        //$drankSoorts = $em->getRepository('AppBundle:DrankSoort')->findAllWithEenheidAndOrderedByName();
        //exit(\Doctrine\Common\Util\Debug::dump($drankSoorts));
        return $this->render('AppBundle:dranksoort:index.html.twig', array(
            'drankSoorts' => $drankSoorts,
        ));
    }
   
    /**
     * Creates a new DrankSoort entity.
     *
     * @Route("/new", name="admin_settings_dranksoort_new")
     * @Security("has_role('ROLE_BASICADMIN')")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $drankSoort = new DrankSoort();
        $form = $this->createForm('AppBundle\Form\DrankSoortType', $drankSoort);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
			
            //$drankSoort->upload();
            
            $em->persist($drankSoort);
            $em->flush();

            return $this->redirectToRoute('admin_settings_dranksoort_show', array('id' => $drankSoort->getId()));
        }
		//exit(\Doctrine\Common\Util\Debug::dump($drankSoort));
        return $this->render('AppBundle:dranksoort:new.html.twig', array(
            'drankSoort' => $drankSoort,
            'form' => $form->createView(),
        ));
    }
    /**
     * Creates a new DrankSoort entity.
     *
     * @Route("/getorders/{drankid}", name="admin_settings_dranksoort_getorders")
     * @Security("has_role('ROLE_BASICADMIN')")
     * @Method({"GET", "POST"})
     */
    public function getOrders($drankid)
    {
        $em = $this->getDoctrine()->getEntityManager();
        /** @var OrderType $ordertype_dranklevering */
        $ordertype_dranklevering = $em->getRepository('AppBundle:OrderType')->findOneBy(array('smstype_id' => 13));
        /** @var ArrayCollection $drankstand_orders */
        //$drankstand_orders = $em->getRepository('AppBundle:Order')->findBy(array('ordertype' => $ordertype_dranklevering),array('createdAt' => 'ASC'));

        $alleLeveringen =  $em->getRepository("AppBundle:Order")->findBy(array("ordertype" => $ordertype_dranklevering));
        foreach ($alleLeveringen as $levering)
        {

        }


        return $this->render('AppBundle:dranksoort:new.html.twig', array(
            'drankSoort' => $alleLeveringen,
        ));
    }
    /**
     * Finds and displays a DrankSoort entity.
     *
     * @Route("/{id}", name="admin_settings_dranksoort_show")
     * @Security("has_role('ROLE_BASICADMIN')")
     * @Method("GET")
     */
    public function showAction(DrankSoort $drankSoort)
    {
        $deleteForm = $this->createDeleteForm($drankSoort);

        return $this->render('AppBundle:dranksoort:show.html.twig', array(
            'drankSoort' => $drankSoort,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing DrankSoort entity.
     *
     * @Route("/{id}/edit", name="admin_settings_dranksoort_edit")
     * @Security("has_role('ROLE_BASICADMIN')")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, DrankSoort $drankSoort)
    {
        $deleteForm = $this->createDeleteForm($drankSoort);
        //$file = new UploadedFile($drankSoort->getAbsolutePath(),$drankSoort->getFotopath());
        //$drankSoort->setFile($file);

        $editForm = $this->createForm('AppBundle\Form\DrankSoortType', $drankSoort);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($drankSoort);
            $em->flush();

            return $this->redirectToRoute('admin_settings_dranksoort_index');
        }

        return $this->render('AppBundle:dranksoort:edit.html.twig', array(
            'drankSoort' => $drankSoort,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a DrankSoort entity.
     *
     * @Route("/{id}", name="admin_settings_dranksoort_delete")
     * @Security("has_role('ROLE_BASICADMIN')")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, DrankSoort $drankSoort)
    {
        $form = $this->createDeleteForm($drankSoort);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($drankSoort);
            $em->flush();
        }

        return $this->redirectToRoute('admin_settings_dranksoort_index');
    }

    /**
     * Creates a form to delete a DrankSoort entity.
     *
     * @param DrankSoort $drankSoort The DrankSoort entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DrankSoort $drankSoort)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_settings_dranksoort_delete', array('id' => $drankSoort->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
