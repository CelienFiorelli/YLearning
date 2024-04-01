<?php

namespace App\Controller;

use App\Entity\Persona;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/api/register', name: 'auth.register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $userPasswordHasher): JsonResponse
    {
        $dateNow = new DateTime();
        $body = $request->toArray();

        $persona = new Persona();
        $persona->setPhone($body['phone']);
        $persona->setEmail($body['email']);
        $persona->setCreatedAt($dateNow)->setUpdatedAt($dateNow);
        $persona->setStatus('on');

        $manager->persist($persona);

        $user = new User();
        $user->setRoles(["USER"]);
        $user->setUsername($body['username']);
        $user->setPassword($userPasswordHasher->hashPassword($user, $body['password']));
        $user->setPersona($persona);
        $user->setCreatedAt($dateNow)->setUpdatedAt($dateNow);
        $manager->persist($user);

        $manager->flush();

        return new JsonResponse(null, Response::HTTP_CREATED, []);
    }
}
