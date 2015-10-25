<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
		$auth_checker = $this->get('security.authorization_checker');
		
		if(!$auth_checker->isGranted('ROLE_USER'))
		{
			return $this->redirectToRoute('login');
		}
		
		$user = $this->getUser();
		
		$users = array();
		
		if($user->hasRole('ROLE_ADMIN'))
		{
			$repositoryUsers = $this->getDoctrine()->getRepository("AppBundle:User");
			$users = $repositoryUsers->findAll();
		}
		
		$repositoryCategories = $this->getDoctrine()->getRepository("AppBundle:Category");
		$qbuilder = $repositoryCategories->createQueryBuilder('c')
					->orderBy('c.name', 'ASC');
		
		$categories = $qbuilder->getQuery()->getResult();
		
		return $this->render('default/index.html.twig', array(
				'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
				'user_FN' => $user->getFirstName(),
				'user_LN' => $user->getLastName(),
				'users' => $users,
				'categories' => $categories
				
		));
    }
}
