<?php

namespace App\Controller;

use App\Entity\Ability;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/api/user/{id}/ability', name: 'user.ability.all', methods: ['GET'])]
    #[ParamConverter("user")]
    public function getUserAvailabilies(User $user, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface): JsonResponse
    {
        $abilities = $user->getAbilities();
        $json = $serializer->serialize($abilities, 'json', ['groups' => 'userAbility']);
        
        return new JsonResponse($json, 200, [], true);
    }
}