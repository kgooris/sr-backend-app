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
        // replace this example code with whatever you need
        
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
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
