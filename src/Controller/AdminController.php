<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TodoRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(UserRepository $repository): Response
    {
        $users= $repository->findAll();

        return $this->render('admin/index.html.twig', ['users'=>$users

        ]);
    }
    /**
     * @Route("/admin/delete/user/{id}", name="delete_user")
     */
    public function delete(EntityManagerInterface $manager, User $user){


            $manager->remove($user);
            $manager->flush();

        return $this->redirectToRoute('admin');

    }
    /**
     * @Route ("/admin/show/user/{id}", name ="show_user")
     */
    public function show(User $user, TodoRepository $repository): Response
    {

        $todos= $user->getTodos();

        return $this->render('admin/show.html.twig', [
            'user' => $user, 'todos'=>$todos
        ]);
    }
}
