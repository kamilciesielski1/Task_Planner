<?php

namespace TaskPlannerBundle\Controller;

use TaskPlannerBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Comment controller.
 *@Security("has_role('ROLE_USER')")
 *
 */
class CommentController extends Controller
{
    /**
     * Creates a new comment entity.
     *
     * @Route("/{id}/addComm", name="addComm")
     *@Method({"GET","POST"})
     */
    public function newAction(Request $request, $id)
    {
        $task = $this->getDoctrine()->getRepository('TaskPlannerBundle:Task')->find($id);
        
        $comment = new Comment();
                        
        $form = $this->createFormBuilder($comment)
                ->setAction($this->generateUrl('addComm', ['id'=>$id]))
                ->setMethod('POST')
                ->add('text', 'textarea', array(
                    'attr'=>array('cols'=>20, 'rows'=>5)))
                ->add('save', 'submit')
                ->getForm();
                
        $comments = $task->getComments();
           
        if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            
                $em = $this->getDoctrine()->getManager();
            
                $comment = $form->getData();
                $comment->setTask($task);
            
                $em->persist($comment);
                $em->flush();
            
            
        }
        return $this->render('TaskPlannerBundle:Comment:comment.html.twig', array(
                'form'=>$form->createView(), 'comments'=>$comments
                ));
     
    }
    
}
