<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
}
