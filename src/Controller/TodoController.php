<?php

namespace App\Controller;

use App\Entity\Check;
use App\Entity\Todo;
use App\Form\TodoType;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 *
 * @Route("{_locale}")
 */

class TodoController extends AbstractController
{
    /**
     * @Route("/todo", name="todo")
     * @Route("/todo/orderBy/{task}", name="orderBy")
     */
    public function todo(Request $request, TodoRepository $repository, UserInterface $user, $task = null, PaginatorInterface $paginator): Response
    {
        if ($task == 'mostRecent') {

            $todos = $repository->findByUserSortedByMostRecent($user);
        } elseif ($task == 'oldest') {
            $todos = $repository->findByUserSortedByOldest($user);
        } elseif ($task == 'urgent') {
            $todos = $repository->findByUserSortedByMostUrgent($user);
        } elseif ($task == 'leastUrgent') {
            $todos = $repository->findByUserSortedByLeastUrgent($user);
        } else {
            $todos = $user->getTodos();
        }

        $todos = $paginator->paginate($todos, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            4 /*limit per page*/
        );

        return $this->render('todo/todo.html.twig', ['todos' => $todos, 'user' => $user]);
    }


    /**
     * @Route("/todo/create", name="todo_create")
     */
    public function create(Todo $todo = null, Request $request, EntityManagerInterface $manager, UserInterface $user)
    {
        $todo = new Todo();
        $todoForm = $this->createForm(TodoType::class, $todo);
        $todoForm->handleRequest($request);

        if ($todoForm->isSubmitted() && $todoForm->isValid()) {
            $todo->setUser($user);
            $todo->setCreatedAt(new \DateTime());
            $manager->persist($todo);
            $manager->flush();
            return $this->redirectToRoute('todo');
        }
        return $this->render('todo/create.html.twig', ['todoForm' => $todoForm->createView()]);

    }

    /**
     * @Route("/todo/delete/{id}", name="delete_todo")
     */
    public function delete(Todo $todo, EntityManagerInterface $manager, UserInterface $user)
    {

        if ($user == $todo->getUser() || in_array('ROLE_ADMIN', $user->getRoles())) {
            $userId = $todo->getUser()->getId();
            $manager->remove($todo);
            $manager->flush();
        }
        if ($user != $todo->getUser() && in_array('ROLE_ADMIN', $user->getRoles())) {

            return $this->redirectToRoute('show_user', ['id' => $userId]);
        }
        return $this->redirectToRoute('todo');
    }

        /**
         * @Route("/todo/check/{id}", name="check")
         */
         public function  check (Todo $todo, EntityManagerInterface $manager)
         {
             if(!$todo->getChecked()) {
                 $check = new Check();
                 $check->setTodo($todo);
                 $check->setUser($todo->getUser());
                 $manager->persist($check);
                 $message = "checked";
             }
             else{
                 $manager->remove($todo->getChecked());
                 $message = "unchecked";
             }
             $manager->flush();

             $data = ['message'=>$message];

               return $this->json($data, 200);
           //return $this->redirectToRoute('todo');
         }



    }