<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\User;

class AdminController extends Controller
{
    /**
     * @Route("/getuser/{userid}", name="admin_getuser")
     */
    public function getUserById($userid)
    {
        $user = $this->getDoctrine()->getManager()->getRepository("AppBundle:User")->find($userid);
        
        return new JsonResponse(array(
            'id' => $user->getId(),
            'first' => $user->getFirstName(),
            'last' => $user->getLastName(),
            'email' => $user->getEmail(),
            'isactive' => $user->getIsActive(),
            'isadmin' => $user->getAccess()
        ));
    }
    
    /**
     * @Route("/setuser", name="admin_setuser")
     */
    public function setUserById(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $id = (int)$request->get("id");
        $firstName = $request->get("first");
        $lastName = $request->get("last");
        $email = $request->get("email");
        $active = (int)$request->get("active") == 1;
        $access = (int)$request->get("access") == 1;
        $pass = $request->get("pass");
        
        $user = $em->getRepository("AppBundle:User")->find($id);
        
        if(!$user) {
            return new Response("Nie znaleziono użytkownika o id ".$id);
        }
        
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setEmail($email);
        $user->setAccess($access);
        $user->setIsActive($active);
        
        if($pass != "") {
            $encoder = $this->container->get('security.password_encoder');
            $new_pass_encoded = $encoder->encodePassword($user, $pass);
            $user->setPassword($new_pass_encoded);
        }
        
        $user->setPlainPassword("123456789");   // uciszenie validator'a, brak pomysłu jak to obejść i aby nie wysypało rejestracji
        
        $validator = $this->get('validator');
        $errors = $validator->validate($user);
        
        if(count($errors) > 0) {
            return new Response("Nie wszystkie pola zostały wypełnione poprawnie.\nJeśli nie chcesz zmieniać użytkownikowi hasła, pozostaw to pole puste.");
        }
        
        $em->flush();
        
        return new Response("OK");
    }
    
    /**
     * @Route("/remtask/{id}", name="remove_task", requirements={"id": "\d+"})
     * @Method({"POST"})
     */
    public function removeTask($id)
    {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository("AppBundle:Task")->find($id);
        if(!$task) {
            return new Response("NOT_FOUND");
        }
        
        $em->remove($task);
        $em->flush();
        
        return new Response("OK");
    }
}
