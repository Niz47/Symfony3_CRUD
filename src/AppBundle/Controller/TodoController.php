<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Todo;

class TodoController extends Controller
{
    /**
     * @Route("/", name="todo_list")
     */
    public function listAction(Request $request)
    {
        $todos = $this->getDoctrine()
                        ->getRepository("AppBundle:Todo")
                        ->findAll();
        return $this->render('todo/index.html.twig', array('todos' => $todos));
    }

    /**
     * @Route("/todo/create", name="todo_create")
     */
    public function createAction(Request $request)
    {
        $todo = new Todo;
        $form = $this->createFormBuilder($todo)
                        ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                        ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                        ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                        ->add('priority', ChoiceType::class, array('choices' => array('Low'=>'Low', 'Normal'=>'Normal', 'High'=> 'High'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                        ->add('dueDate', DateTimeType::class, array('attr' => array('class' => 'formcontrol', 'style' => 'margin-bottom:15px')))
                        ->add('save', SubmitType::class, array('label'=>'Create Todo', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
                        ->getForm();

        $form -> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Get Data
            /*$name = $form['name']->getData();
            $name = $form['category']->getData();
            $name = $form['description']->getData();
            $name = $form['priority']->getData();
            $name = $form['dueDate']->getData();
            $now = new\DateTime('now');*/

            $todo->setName($form['name']->getData());
            $todo->setCategory($form['category']->getData());
            $todo->setDescription($form['description']->getData());
            $todo->setPriority($form['priority']->getData());
            $todo->setDueDate($form['dueDate']->getData());
            $todo->setCreateDate(new\DateTime('now'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();

            $this->addflash('notice', 'Todo Added!');
            return $this->redirectToRoute('todo_list');
        }
        return $this->render('todo/create.html.twig', array('form'=>$form->createView()));
    }

    /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {
        $todo = $this->getDoctrine()
                        ->getRepository("AppBundle:Todo")
                        ->find($id);
        $now = new\DateTime('now');

        $form = $this->createFormBuilder($todo)
                        ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                        ->add('category', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                        ->add('description', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                        ->add('priority', ChoiceType::class, array('choices' => array('Low'=>'Low', 'Normal'=>'Normal', 'High'=> 'High'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                        ->add('dueDate', DateTimeType::class, array('attr' => array('class' => 'formcontrol', 'style' => 'margin-bottom:15px')))
                        ->add('update', SubmitType::class, array('label'=>'Update Todo', 'attr' => array('class' => 'btn btn-warning', 'style' => 'margin-bottom:15px')))
                        ->getForm();

        $form -> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $todo = $em->getRepository('AppBundle:Todo')->find($id);

            $todo->setName($form['name']->getData());
            $todo->setCategory($form['category']->getData());
            $todo->setDescription($form['description']->getData());
            $todo->setPriority($form['priority']->getData());
            $todo->setDueDate($form['dueDate']->getData());
            $todo->setCreateDate($now);

            $em->flush();

            $this->addflash('notice', 'Todo Updated!');
            return $this->redirectToRoute('todo_list');
        }

        return $this->render('todo/edit.html.twig', array('todo'=>$todo, 'form'=>$form->createView()));
    }

    /**
     * @Route("/todo/detail/{id}", name="todo_detail")
     */
    public function detailAction($id)
    {
        $todo = $this->getDoctrine()
                        ->getRepository("AppBundle:Todo")
                        ->find($id);
        return $this->render('todo/detail.html.twig', array('todo'=>$todo));
    }

    /**
     * @Route("/todo/delete/{id}", name="todo_delete")
     */
    public function deleteAction($id)
    {        
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('AppBundle:Todo')->find($id);
        $em->remove($todo);
        $em->flush();
        $this->addflash('error', 'Todo Removed!');
            return $this->redirectToRoute('todo_list');
    }
}
