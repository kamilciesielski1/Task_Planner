<?php

namespace TaskPlannerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use TaskPlannerBundle\Entity\Task;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TaskController extends Controller
{
    /**
     * @Route("/newTask", name="newTask")
     */
    public function newTaskAction(Request $request)
    {
        $task = new Task();
        
        $formtask = $this->createFormBuilder($task)
                ->setAction($this->generateUrl('newTask'))
                ->setMethod('POST')
                ->add('category', EntityType::class, array(
                    'class'=>'TaskPlannerBundle:Category',
                    'choice_label'=>'name',
                    'placeholder'=>'Select Category...',
                    'query_builder' => function ($er) {
                    $user1 = $this->get('security.token_storage')->getToken()->getUser();
                    $qb = $er->createQueryBuilder('u');
                    return $qb->select('u')
                    ->where('u.user = :identifier')
                    ->orderBy('u.name', 'ASC')
                    ->setParameter('identifier', $user1);
                    }
                ))
                ->add('name', 'text')
                ->add('description', 'text')
                ->add('deadLine', 'datetime', array('years'=>range(2017,2025)))
                ->add('save', 'submit', array('label'=>'Save Task'))
                ->getForm();
        
        $formtask->handleRequest($request);

        if ($formtask->isSubmitted()) {
            
            $request->getSession()
            ->getFlashBag()
            ->add('success', 'Dodano!');
            
            $task = $formtask->getData();
            
            $user = $this->get('security.token_storage')->getToken()->getUser();
            
            $task->setUser($user);
            
            $task->setStatus(0);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();
            
        }
        return $this->redirectToRoute('main');
    }
    
    /**
     * @Route("/taskList", name="taskList")
     */
    public function taskList()
    {
        $user1 = $this->get('security.token_storage')->getToken()->getUser();
        $id = $user1->getId();
        
        $em = $this->getDoctrine()->getManager();
        
        $tasks = $em->getRepository('TaskPlannerBundle:Task')->findAllByUserId($id);
        
        //$tasks = $user1->getTasks();
        
        return $this->render('TaskPlannerBundle:Task:TaskList.html.twig', array(
            'tasks'=>$tasks
        ));
    }        

    /**
     * @Route("/deleteTask")
     */
    public function deleteTaskAction()
    {
        return $this->render('TaskPlannerBundle:Task:delete_task.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/{id}/showTask", name="showTask")
     */
    public function editTaskAction()
    {
        return $this->render('TaskPlannerBundle:Task:edit_task.html.twig', array(
            // ...
        ));
    }

}
