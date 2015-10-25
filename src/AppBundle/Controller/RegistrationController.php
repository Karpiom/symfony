<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\RegisterForm;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(new RegisterForm(), $user, array(
			'action' => $this->generateUrl('user_registration').'#one',
			'method' => 'POST',
		));

        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('registration_complete');
        }

        return $this->render(
            'default/register.html.twig',
            array('regform' => $form->createView())
        );
    }
	
	/**
     * @Route("/regcomplete", name="registration_complete")
     */
    public function registerComplete(Request $request)
    {
		return $this->render('default/regcomplete.html.twig');
	}
}