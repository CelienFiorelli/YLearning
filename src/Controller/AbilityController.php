<?php

namespace App\Controller;

use App\Entity\Ability;
use App\Entity\Technologie;
use App\Repository\AbilityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class AbilityController extends AbstractController
{
    #[Route('/api/ability', name: 'ability.all', methods: ['GET'])]
    public function getAll(AbilityRepository $repository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $cacheKey = "get:all:ability";
        $json = $cache->get($cacheKey, function (ItemInterface $item) use ($serializer, $repository) {
            $item->tag('getAllAbilityCache');
            $abilities = $repository->findAll();
            return $serializer->serialize($abilities, 'json', ['groups' => 'userAbility']);
        });

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/ability', name: 'ability.createOrUpdate', methods: ['POST'])]
    public function createAbility(Request $request, AbilityRepository $repository, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, TagAwareCacheInterface $cache): JsonResponse
    {
        $body = $request->toArray();
        $technologie = $entityManagerInterface->getRepository(Technologie::class)->find($body['technologie']);
        if (!$technologie) {
            return new JsonResponse(['error' => 'Technologie not found'], Response::HTTP_BAD_REQUEST);
        }

        $date = new \DateTime();
        $ability = $repository->findOneBy(['technologie' => $technologie]);
        if ($ability) {
            $ability->setLevel($body['level']);
        } else {
            $ability = $serializer->deserialize($request->getContent(), Ability::class, 'json');

            $user = $this->getUser();
            $ability->setUser($user);
            $ability->setTechnologie($technologie);
            $ability->setCreatedAt($date);
        }

        $ability->setUpdatedAt($date);

        $entityManagerInterface->persist($ability);
        $entityManagerInterface->flush();
        $cache->invalidateTags(['getAllAbilityCache']);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT, []);
    }

    #[Route('/api/ability/{id}', name: 'ability.delete', methods: ['DELETE'])]
    public function deleteAbility(?Ability $ability, EntityManagerInterface $entityManagerInterface, TagAwareCacheInterface $cache): JsonResponse
    {
        if (!$ability) {
            return new JsonResponse(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }
        $entityManagerInterface->remove($ability);
        $entityManagerInterface->flush();
        $cache->invalidateTags(['getAllAbilityCache']);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
