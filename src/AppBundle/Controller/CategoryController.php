<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
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
			return new JsonResponse($ret);	// error
		
		$ret["pages"] = (int)($count / 20 + 1);
		
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
		$qb->setMaxResults(20);
		$qb->setFirstResult(((int)$page - 1) * 20);
		
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
}
