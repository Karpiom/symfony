<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Task;
use AppBundle\Entity\Category;

class CategoryController extends Controller
{
    /**
     * @Route("/addcategory", name="user_addcategory")
     * @Method({"POST"})
     */
    public function onAddCategory(Request $request)
    {
        $name = $request->get("cat_name");
        if(empty($name))
            return new Response("NAME_NOT_SET");
        
        $em = $this->getDoctrine()->getManager();
        
        $cat = $em->getRepository("AppBundle:Category")->findOneBy(array("name" => $name));
        if($cat)
            return new Response("ALREADY_EXISTS");
        
        $cat = new Category();
        $cat->setName($name);
        
        $em->persist($cat);
        $em->flush();
        
        return new Response($cat->getId());
    }
    
    /**
     * @Route("/remcategory", name="user_remcategory")
     * @Method({"POST"})
     * Admin only
     */
    public function onRemoveCategory(Request $request)
    {
        $id = (int)$request->get("id");
        $em = $this->getDoctrine()->getManager();
        $cat = $em->getRepository("AppBundle:Category")->find($id);
        
        if(!$cat)
            return new Response("NOT_FOUND");
        
        $em->remove($cat);
        $em->flush();
        
        return new Response($id);
    }
    
    /**
     * @Route("/gettasks/{id}/{page}/{orderBy}/{AscDesc}", name="user_gettasks")
     */
    public function onGetTasks($id, $page, $orderBy, $AscDesc)
    {        
        $em = $this->getDoctrine()->getManager();
        $ret = array(
            "pages" => -1,
            "data" => array()
        );
        
        $qb = $em->createQueryBuilder();
        $qb->select('count(t.id)');
        $qb->from('AppBundle:Task','t');
        $qb->where('t.category = :id')->setParameter('id', (int)$id);

        $count = $qb->getQuery()->getSingleScalarResult();
        
        if(!$count)
            return new JsonResponse($ret);    // error
        
        $ret["pages"] = (int)($count / 10 + 1);
        
        if($AscDesc != "DESC")
            $AscDesc = "ASC";
        
        switch($orderBy)
        {
            case "id":
                $orderBy = "t.id";
                break;
            case "end":
                $orderBy = "t.endTime";
                break;
            case "ended":
                $orderBy = "t.ended";
                break;
            case "priority":
                $orderBy = "t.priority";
                break;
            default:
                $orderBy = "t.name";
                break;
        }
        
        $repTasks = $em->getRepository("AppBundle:Task");
        $qb = $repTasks->createQueryBuilder("t");
        $qb->where("t.category = :id")->setParameter("id", (int)$id);
        $qb->orderBy($orderBy, $AscDesc);
        $qb->setMaxResults(10);
        $qb->setFirstResult(((int)$page - 1) * 10);
        
        $tasks = $qb->getQuery()->getResult();
        if(!$tasks || !count($tasks))
        {
            $ret["pages"] = 0;
            return new JsonResponse($ret);
        }
        
        foreach($tasks as $task)
        {
            $ret["data"][] = array(
                "id" => $task->getId(),
                "name" => $task->getName(),
                "description" => $task->getDescription(),
                "endtime" => $task->getEndTime(),
                "ended" => $task->getEnded(),
                "priority" => $task->getPriority()
            );
        }
        
        return new JsonResponse($ret);
    }
    
    /**
     * @Route("/addtask", name="user_addtask")
     * @Method({"POST"})
     */
    public function onAddTask(Request $request)
    {
        try
        {
            $categoryId = $request->get("categoryId");
            $name = $request->get("name");
            $description = $request->get("description");
            $endTime = $request->get("endtime");
            $priority = (int)$request->get("priority");
            $priority = max(1, min($priority, 4));
            
            $em = $this->getDoctrine()->getManager();
            $category = $em->getRepository("AppBundle:Category")->find($categoryId);
            
            if(!$category)
                return new Response("FAILED");
            
            $task = new Task();
            $task->setName($name);
            $task->setDescription($description);
            $task->setEndTime(new \DateTime($endTime));
            $task->setPriority($priority);
            $task->setCategory($category);
            $task->setEnded(false);
            
            $validator = $this->get('validator');
            $errors = $validator->validate($task);
            
            if(count($errors) > 0) {
                return new Response("Nie wszystkie pola zostały wypełnione poprawnie.");
            }
            
            $em->persist($task);
            $em->flush();
            
            return new Response("OK");
        }
        catch(\Exception $e) {
            return new Response($e->getMessage());
        }
    }
    
    /**
     * @Route("/endtask/{id}", name="user_endtask", requirements={"id": "\d+"})
     * @Method({"POST"})
     */
    public function onEndTask($id)
    {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository("AppBundle:Task")->find($id);
        if(!$task) {
            return new Response("NOT_FOUND");
        }
        
        $task->setEnded(true);
        $em->flush();
        
        return new Response("OK");
    }
}
