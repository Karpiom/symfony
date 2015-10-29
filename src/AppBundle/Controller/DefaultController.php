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
        
        if(!$auth_checker->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('login');
        }
        
        $user = $this->getUser();
        
        $users = array();
        
        if($user->hasRole('ROLE_ADMIN')) {
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
    
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $nextWeek = new \DateTime("+7 days");
        $today = new \DateTime("today");
        
        $repTasks = $em->getRepository("AppBundle:Task");
        $qb = $repTasks->createQueryBuilder("t");
        $qb->where("t.endTime < :dt AND t.ended = 0")->setParameter("dt", $nextWeek);
        $qb->orderBy("t.endTime", "ASC");
        
        $priorityTasks = $qb->getQuery()->getResult();
        
        $repTasks = $em->getRepository("AppBundle:Task");
        $qb = $repTasks->createQueryBuilder("t");
        $qb->where("t.endTime < :dt AND t.ended = 0")->setParameter("dt", $today);
        $qb->orderBy("t.endTime", "DESC");
        
        $lastTasks = $qb->getQuery()->getResult();
        
        return $this->render('default/dashboard.html.twig',
            array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
                'user_FN' => $user->getFirstName(),
                'user_LN' => $user->getLastName(),
                'priorityTasks' => $priorityTasks,
                'lastTasks' => $lastTasks
            )
        );
    }
}
