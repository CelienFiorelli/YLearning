<?php

namespace App\Controller;

use App\Entity\Challenge;
use App\Repository\ChallengeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ChallengeController extends AbstractController
{
    #[Route('/api/challenge', name: 'challenge.all', methods: ['GET'])]
    public function getAll(ChallengeRepository $repository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $cacheKey = "get:all:challenge";
        $json = $cache->get($cacheKey, function(ItemInterface $item) use ($serializer, $repository){
            $item->tag('getAllChallengeCache');
            $challenge = $repository->findBy(['status' => 'on']);
            return $serializer->serialize($challenge, 'json', ['group' => 'challenge']);
        });
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/challenge/{id}', name: 'challenge.show', methods: ['GET'])]
    #[ParamConverter("challenge")]
    public function show(Challenge $challenge, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($challenge, 'json', ['groups' => 'challenge']);

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/challenge', name: 'challenge.create', methods: ['POST'])]
    public function createCourse(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, UrlGeneratorInterface $urlGenerator, TagAwareCacheInterface $cache): JsonResponse
    {
        $challenge = $serializer->deserialize($request->getContent(), Challenge::class, 'json');
        $date = new \DateTime();
        $body = $request->toArray();
        $challenge->setDescription($body['description'])
            ->setLevel($body['level'])
            ->setStatus('on')
            ->setCreatedAt($date)
            ->setUpdatedAt($date);
        $entityManagerInterface->persist($challenge);
        $entityManagerInterface->flush();
        $location = $urlGenerator->generate('challenge.all', ['id' => $challenge->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $json = $serializer->serialize($challenge, 'json', ['groups' => 'course']);
        $cache->invalidateTags(['getAllChallengeCache']);

        return new JsonResponse($json, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('api/challenge/{id}', name: 'challenge.update', methods: ['PUT'])]
    public function updateSection(Challenge $challenge, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, TagAwareCacheInterface $cache): JsonResponse
    {
        $challenge = $serializer->deserialize($request->getContent(), Challenge::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $challenge]);
        $date = new \DateTime();
        $challenge->setUpdatedAt($date);
        $entityManagerInterface->persist($challenge);
        $entityManagerInterface->flush();
        $cache->invalidateTags(['getAllChallengeCache']);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT, []);
    }

    #[Route('/api/challenge/{id}', name: 'challenge.delete', methods: ['DELETE'])]
    public function deleteSection(Challenge $challenge, EntityManagerInterface $entityManagerInterface, TagAwareCacheInterface $cache): JsonResponse
    {
        $entityManagerInterface->remove($challenge);
        $entityManagerInterface->flush();
        $cache->invalidateTags(['getAllChallengeCache']);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
