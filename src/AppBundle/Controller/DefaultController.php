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
			$authenticationUtils = $this->get('security.authentication_utils');
			$error = $authenticationUtils->getLastAuthenticationError();
			$lastUsername = $authenticationUtils->getLastUsername();
			
			return $this->render('default/notlogged.html.twig', array(
				'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
				'error' => $error,
				'last_username' => $lastUsername
			));
		}
		
		return $this->render('default/index.html.twig', array(
				'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..')
		));
    }
}
