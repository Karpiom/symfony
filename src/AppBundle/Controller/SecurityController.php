<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
    
    /**
     * @Route("/recacc", name="recovery_account")
     */
    public function recoveryAccountGET()
    {
        return $this->render('default/recacc.html.twig');
    }
    
    /**
     * @Route("/recacc_post", name="recovery_account_post")
     * @Method({"POST"})
     */
    public function recoveryAccountPOST(Request $request)
    {
        $email = $request->get("user_mail");
        if(!$email || $email === "") {
            return new Response("Nie podano adresu email.");
        }
        
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->findOneBy(array("email" => $email));
        
        if(!$user) {
            return new Response("Taki adres email nie istnieje w naszej bazie.");
        }
        
        $arr = "QWERTYUIOPLKJHGFDSAZXCVBNM1234567890!@#$%^&*()_-+=/?.>,<";
        $arrLen = strlen($arr);
        $newPassword = "";
        
        for($i = 0; $i < 8; $i++) {
            $newPassword .= $arr[rand(0, $arrLen)];
        }
        
        // $encoder = $this->container->get('security.password_encoder');
        // $new_pass_encoded = $encoder->encodePassword($user, $newPassword);
        // $user->setPassword($new_pass_encoded);
        
        $em->flush();
        
        $message = \Swift_Message::newInstance()
            ->setSubject('Twoje nowe hasło')    // trzeba wymyslic nazwe serwisu
            ->setFrom('send@example.com')   // trzeba podac adres nadawcy
            ->setTo($user->getEmail())
            ->setBody(
                'Witaj '.$user->getFirstName().' '.$user->getLastName().'!<br />Twoje nowe hasło to: '.$newPassword,
                'text/html'
            );
        
        $this->get('mailer')->send($message);

        return new Response("OK");
    }
}