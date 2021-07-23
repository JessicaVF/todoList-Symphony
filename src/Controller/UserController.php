<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RegisterType;

/**
 *
 * @Route("{_locale}")
 */


class UserController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(): Response
    {
        return $this->render('user/index.html.twig');
    }
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $user= new User();
        $formRegister = $this->createForm(RegisterType::class, $user);
        $formRegister->handleRequest($request);
        if($formRegister->isSubmitted() && $formRegister->isValid()){

            $hashedPassword = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('login');
        }

        return $this->render('user/register.html.twig', ['formRegister'=>$formRegister->createView()]);

    }


    /**
     * @Route("/logout", name="logout")
     */
    public function logout(){

    }
}
