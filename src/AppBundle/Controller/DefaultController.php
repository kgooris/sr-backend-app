<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        // zoek bestellingen alleen die nog niet zijn gesloten.

        //$drankbestelling = $em->getRepository('AppBundle:DrankStand')->findOneByNaam('HoofdStock');
        //$order = $em->getRepository('AppBundle:OrderType')->findBy(
        //    array('smstype_id' => 10)
        //);
        $query = $em->createQuery('
          SELECT o
          FROM AppBundle:Order o JOIN o.ordertype ot
          WHERE ot.smstype_id = 11 or ot.smstype_id = 12 or ot.smstype_id = 21 or ot.smstype_id = 22 or ot.smstype_id = 26 or ot.smstype_id = 27 or ot.smstype_id = 31 or ot.smstype_id = 32 ORDER BY o.createdAt DESC ');
        $orders = $query->getResult();
        
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
            'orders' => $orders,
        ));
    }
    /**
     * @Route("/lucky/number/{count}")
     */
    public function numberAction($count)
    {
     $numbers = array();
        for ($i = 0; $i < $count; $i++) {
            $numbers[] = rand(0, 100);
        }
        $numbersList = implode(', ', $numbers);

        return $this->render(
        	'default/number.html.twig',
        	array('luckyNumberList' => $numbersList)
    	);
         
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
