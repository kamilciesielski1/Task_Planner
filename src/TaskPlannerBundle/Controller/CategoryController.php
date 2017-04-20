<?php

namespace TaskPlannerBundle\Controller;

use TaskPlannerBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Category controller.
 *
 * 
 */
class CategoryController extends Controller
{
    /**
     * Creates a new category entity.
     *
     * @Route("/newCategory", name="category_new")
     * @Method({"POST"})
     */
    public function newAction(Request $request)
    {
        $category = new Category();
        
        $form = $this->createFormBuilder($category)
                ->setAction($this->generateUrl('category_new'))
                ->setMethod('POST')
                ->add('name', 'text')
                ->add('save', 'submit', array('label'=>'Save Category'))
                ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            
            $request->getSession()
            ->getFlashBag()
            ->add('success', 'Dodano!');
            
            $category = $form->getData();
            
            $user = $this->get('security.token_storage')->getToken()->getUser();
            
            $category->setUser($user);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            
            
        }
        return $this->redirectToRoute('main');
    }

    
}
            
            
            
            
