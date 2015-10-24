<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Form\RegisterForm;
use AppBundle\Entity\User;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login_route")
     */
    public function loginAction(Request $request)
    {
		$authenticationUtils = $this->get('security.authentication_utils');
		
		$error = $authenticationUtils->getLastAuthenticationError();
		$lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(new RegisterForm(), new User(), array(
			'action' => $this->generateUrl('user_registration').'#one',
			'method' => 'POST',
		));
		
		return $this->render(
			'default/notlogged.html.twig',
			array(
				'error' => $error,
				'last_username' => $lastUsername,
				'regform' => $form->createView()
			)
		);
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
    }
	
    /**
     * @Route("/loggedout", name="logout_complete")
     */
	public function logoutComplete()
	{
        $form = $this->createForm(new RegisterForm(), new User(), array(
			'action' => $this->generateUrl('user_registration').'#one',
			'method' => 'POST',
		));

		return $this->render(
			'default/loggedout.html.twig',
			array(
				'last_username' => $this->get('security.authentication_utils')->getLastUsername(),
				'regform' => $form->createView()
			)
		);
	}
}