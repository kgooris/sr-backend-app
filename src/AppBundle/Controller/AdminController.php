<?php

namespace AppBundle\Controller;

use AppBundle\Utils\SMSMessage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\FestivalDag;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin_homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $drankSoorts = $em->getRepository('AppBundle:DrankSoort')->findAllOrderedBySMSPos();
        
        return $this->render('AdminDefault/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
            'drankSoorts' => $drankSoorts,
        ));
    }
    /**
     * @Route("/admin/settings/reset/orders", name="admin_settings_reset_orders")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function resetOrdersQuestion()
    {
        return $this->render('@App/settings/resetorders.html.twig');
    }
    /**
     * @Route("/admin/settings/reset/orders/ok", name="admin_settings_reset_orders_ok")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function resetOrdersAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $query1 = $em->createQuery('DELETE FROM AppBundle:OrderDrank od');
        $query1->execute();
        $query2 = $em->createQuery('DELETE FROM AppBundle:Order o');
        $query2->execute();

        $dranken = $em->getRepository("AppBundle:DrankSoort")->findAll();
        foreach ($dranken as $drank)
        {
            $drank->setStock(0);
            $em->persist($drank);
            $em->flush();

        }
        return $this->render('AdminDefault/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));


    }

    /**
     * @Route("/admin/settings/general/edit", name="admin_settings_general_edit")
     * @Security("has_role('ROLE_ADMIN')")
     */
        public function editSettingAction(Request $request)
    {
        // replace this example code with whatever you need
        $data = array();
        $em = $this->getDoctrine()->getEntityManager();

        $activeFestivalDag = $em->getRepository("AppBundle:FestivalDag")->findByfestactive(1);
//        dump($activeFestivalDag);
//        exit();
        $editForm = $this->createFormBuilder($data)
            ->add('festivaldag', EntityType::class,
                array(
                    'label' => 'Actieve Festivaldag',
                    'class' => 'AppBundle:FestivalDag',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('f')->orderBy('f.festdate', 'ASC');
                    },
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                    'data' => $em->getReference("AppBundle:FestivalDag",$activeFestivalDag[0]->getId())
                )

            )
            ->getForm();

        if ('POST' == $request->getMethod())
        {
            $editForm->handleRequest($request);
            $data = $editForm->getData();
            // array 0 contains festivaldag
            /** @var FestivalDag $nieuwedag */
            $nieuwedag = $data['festivaldag'];
            $nieuwedag->setFestActive(1);

            // update all active to 0 and then update only 1 to active
            $qb = $em->createQueryBuilder();
            $q = $qb->update('AppBundle:FestivalDag', 'f')
                ->set('f.festactive', '?1')
                ->setParameter(1,'0')
                ->getQuery();
            $q->execute();



            $em->persist($nieuwedag);
            $em->flush();



            return $this->redirectToRoute('admin_settings_festivaldag_index');
        }

        return $this->render('@App/settings/edit.html.twig', array(
            'edit_form' => $editForm->createView(),
        ));
    }

   
    /**
     * @Route("/lucky/number/{count}")
     */
    public function numberAction($count)
    {
//     $numbers = array();
//        for ($i = 0; $i < $count; $i++) {
//            $numbers[] = rand(0, 100);
//        }
//        $numbersList = implode(', ', $numbers);
//
//        return $this->render(
//        	'default/number.html.twig',
//        	array('luckyNumberList' => $numbersList)
//    	);
       //$smsmessage = new SMSMessage();
        //$smsmessage->setSmstxt("2205201622457014831010099905000000000000000000000000000000000000000000000000000000000000000000000000000000");
        //$order = $smsmessage->getOrder();
        
        
         
    }

    /**
     * @Route("/api/lucky/number")
     */
    public function apiNumberAction()
    {
    	$data = array(
    			'lucky_number' => rand(0, 100),
    	);
    
    	return new JsonResponse($data);
    }
}
