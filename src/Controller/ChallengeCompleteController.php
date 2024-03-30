<?php

namespace App\Controller;

use App\Entity\Challenge;
use App\Entity\ChallengeComplete;
use App\Entity\Technologie;
use App\Repository\ChallengeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ChallengeCompleteController extends AbstractController
{
    #[Route('/api/challenge/complete', name: 'challenge.complete.all', methods: ['GET'])]
    public function getAll(ChallengeRepository $repository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $cacheKey = "get:all:challenge:complete";
        $json = $cache->get($cacheKey, function (ItemInterface $item) use ($serializer, $repository) {
            $item->tag('getAllChallengeCompleteCache');
            $challengesComplete = $repository->findAll();
            return $serializer->serialize($challengesComplete, 'json', ['groups' => 'challengeComplete']);
        });
        
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/challenge/complete/{id}', name: 'challenge.complete.show', methods: ['GET'])]
    #[ParamConverter("challenge_complete")]
    public function show(ChallengeComplete $challengeComplete, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($challengeComplete, 'json', ['groups' => 'challengeComplete']);

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/challenge/complete', name: 'challenge.complete.create', methods: ['POST'])]
    public function createChallengeComplete(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, UrlGeneratorInterface $urlGenerator, TagAwareCacheInterface $cache): JsonResponse
    {
        $body = $request->toArray();
        $challengeComplete = $serializer->deserialize($request->getContent(), ChallengeComplete::class, 'json');
        $date = new \DateTime();
        $challengeComplete->setCreatedAt($date)->setUpdatedAt($date);

        $technologie = $entityManagerInterface->getRepository(Technologie::class)->find($body['technologie']);
        if (!$technologie) {
            return new JsonResponse(['error' => 'Technologie not found'], Response::HTTP_BAD_REQUEST);
        }
        $challenge = $entityManagerInterface->getRepository(Challenge::class)->find($body['challenge']);
        if (!$challenge) {
            return new JsonResponse(['error' => 'Challenge not found'], Response::HTTP_BAD_REQUEST);
        }
        $user = $this->getUser();
        $challengeComplete->setUser($user);
        $challengeComplete->setChallenge($challenge);
        $challengeComplete->setTechnologie($technologie);

        $entityManagerInterface->persist($challengeComplete);
        $entityManagerInterface->flush();
        $location = $urlGenerator->generate('challenge.complete.show', ['id' => $challengeComplete->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $json = $serializer->serialize($challengeComplete, 'json');
        $cache->invalidateTags(['getAllChallengeCompleteCache']);

        return new JsonResponse($json, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('api/challenge/complete/{id}', name: 'challenge.complete.update', methods: ['PUT'])]
    public function updateChallengeComplete(ChallengeComplete $challengeComplete, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, TagAwareCacheInterface $cache): JsonResponse
    {
        $technologie = $serializer->deserialize($request->getContent(), ChallengeComplete::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $challengeComplete]);
        $date = new \DateTime();
        $technologie->setUpdatedAt($date);
        $entityManagerInterface->persist($technologie);
        $entityManagerInterface->flush();
        $cache->invalidateTags(['getAllChallengeCompleteCache']);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT, []);
    }

    #[Route('api/challenge/complete/{id}/finish', name: 'challenge.complete.finish', methods: ['POST'])]
    public function markAsFinish(ChallengeComplete $challengeComplete, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, TagAwareCacheInterface $cache): JsonResponse
    {
        $date = new \DateTime();
        $challengeComplete->setUpdatedAt($date);

        $timeToFinish = $challengeComplete->getCreatedAt()->diff($date);
        $challengeComplete->setTime($timeToFinish);

        $entityManagerInterface->persist($challengeComplete);
        $entityManagerInterface->flush();
        $cache->invalidateTags(['getAllChallengeCompleteCache']);

        $json = $serializer->serialize($challengeComplete, 'json', ['groups' => 'challengeComplete']);

        return new JsonResponse($json, 200, [], true);
    }
}