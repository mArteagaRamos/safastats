<?php

namespace App\Controller;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request                     $request,
                             UserPasswordHasherInterface $passwordHasher,
                             EntityManagerInterface      $entityManager): Response
    {
        if ($request->isMethod('POST')) {

            $new_user = new Usuario();
            $new_user->setUsername($request->request->get('username'));
            $new_user->setEmail($request->request->get('email'));
            $password_text = $request->request->get('password');

            $hashedPassword = $passwordHasher->hashPassword(
                $new_user,
                $password_text
            );

            $new_user->setPassword($hashedPassword);

            $entityManager->persist($new_user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');

        }

        return $this->render('register/register.html.twig', [
            'controller_name' => 'RegisterController',
        ]);
    }
}
