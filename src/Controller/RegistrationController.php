<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/api/register/{username}/{password}')]
    public function registerUser(UserPasswordHasherInterface $passwordHash, Request $request,
                                 ManagerRegistry             $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $user = new User();
        $plaintextPassword = $request->attributes->get('password');

        $hashedPassword = $passwordHash->hashPassword(
            $user,
            $plaintextPassword
        );

        $user->setPassword($hashedPassword);
        $user->setEmail($request->attributes->get('username'));

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response(null, 200);
    }
}