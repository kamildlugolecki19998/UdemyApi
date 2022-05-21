<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    #[Route(
        '/api/users',
        name: 'register_user',
        methods: ['POST']
    )]
    public function registerUser(
        UserPasswordHasherInterface $passwordHasher,
        Request                     $request,
        SerializerInterface         $serializer,
        ManagerRegistry             $doctrine,
        ValidatorInterface          $validator
    ): Response
    {
        /** @var User $user */
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->render('validators/user_template.html.twig', ['errors' => $errors]);
        }

        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

        $manager = $doctrine->getManager();
        $manager->persist($user);
        $manager->flush();

        return $this->json($user);
    }
}
