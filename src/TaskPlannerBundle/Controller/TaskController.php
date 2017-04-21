<?php

namespace TaskPlannerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use TaskPlannerBundle\Entity\Task;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * @Security("has_role('ROLE_USER')")
 */
class TaskController extends Controller
{
    /**
     * @Route("/newTask", name="newTask")
     * @Method({"POST"})
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
                ->add('deadLine', DateType::class, array(
                'widget' => 'single_text',
                ))
                ->add('save', 'submit', array('label'=>'Save Task'))
                ->getForm();
        
        $formtask->handleRequest($request);

        if ($formtask->isSubmitted()) {
            
            $request->getSession()
            ->getFlashBag()
            ->add('success', 'Task added!');
            
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
        
        $tasks = $em->getRepository('TaskPlannerBundle:Task')->findAllByOutOfDate($id);
        
        $tasksByDate = $em->getRepository('TaskPlannerBundle:Task')->findAllbyDate($id);
        
        $tasksByStatus = $em->getRepository('TaskPlannerBundle:Task')->findAllbyStatus($id);
        
        return $this->render('TaskPlannerBundle:Task:TaskList.html.twig', array(
            'tasks'=>$tasks, 'taskDate'=>$tasksByDate, 'taskStatus'=>$tasksByStatus
        ));
    }        

    /**
     * @Route("/{id}/deleteTask", name="deleteTask")
     */
    public function deleteTaskAction($id)
    {
        $user1 = $this->get('security.token_storage')->getToken()->getUser();
        $id2 = $user1->getId();
        
        $em = $this->getDoctrine()->getManager();
        
        $qb = $em->createQueryBuilder();
        $query = $qb->delete('TaskPlannerBundle:Task', 'task')
            ->where('task.id = :id', 'task.user = :id2')
            ->setParameter('id', $id)
            ->setParameter('id2', $id2)
            ->getQuery();
        $query->execute();
        
        return $this->redirectToRoute('taskList');
    }

    /**
     * @Route("/{id}/showTask", name="showTask")
     * @Method({"GET", "POST"})
     */
    public function editTaskAction(Request $request, $id)
    {
        $task = $this->getDoctrine()->getRepository('TaskPlannerBundle:Task')->find($id);
        
        
        $form = $this->createFormBuilder($task)
                ->setAction($this->generateUrl('showTask', ['id'=>$id]))
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
                ->add('deadLine', DateType::class, array(
                'widget' => 'single_text',
                ))
                ->add('save', 'submit', array('label'=>'Commit changes!'))
                ->getForm();
                            
        if ($request->isMethod('GET'))
        {
                return $this->render('TaskPlannerBundle:Task:edit_task.html.twig', array(
                'form'=>$form->createView()
                ));
        } 
        else if ($request->isMethod('POST'))
        {
            $form->handleRequest($request);
            
            
            if ($form->isSubmitted()) 
            {
                $this->getDoctrine()->getManager()->flush();
                
                $request->getSession()
                ->getFlashBag()
                ->add('success', 'Changes commited!');
            }
            return $this->redirectToRoute('taskList');
        }
             
    }
    /**
     * @Route("/{id}/changeStatus", name="changeStatus")
     */
    public function changeStatusAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $repository = $this->getDoctrine()->getRepository('TaskPlannerBundle:Task');
        
        $task = $repository->find($id);
        
        if ($task)
        {
            if ($task->getStatus() == 0)
            {
                $task->setStatus(1);
                
            } 
            
            $em->flush();
        }
        
        return $this->redirectToRoute('taskList');
    }
                

}
