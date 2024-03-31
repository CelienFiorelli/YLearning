<?php

namespace App\Controller;

use App\Entity\ChallengeReview;
use App\Repository\ChallengeReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Challenge;
use App\Entity\ChallengeComplete;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ChallengeReviewController extends AbstractController
{
    #[Route('/api/review', name: 'review.all', methods: ['GET'])]
    public function getAll(ChallengeReviewRepository $repository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $cacheKey = "get:all:review";
        $json = $cache->get($cacheKey, function (ItemInterface $item) use ($serializer, $repository) {
            $item->tag('getAllReviewCache');
            $challenge = $repository->findAll();
            return $serializer->serialize($challenge, 'json', ['groups' => 'review']);
        });
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/api/review/{id}', name: 'review.show', methods: ['GET'])]
    #[ParamConverter("challenge")]
    public function show(?ChallengeReview $review, SerializerInterface $serializer): JsonResponse
    {
        if (!$review) {
            return new JsonResponse(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }
        $json = $serializer->serialize($review, 'json', ['groups' => 'review']);

        return new JsonResponse($json, 200, [], true);
    }


    #[Route('/api/challenge/{id}/review', name: 'review.challenge', methods: ['GET'])]
    public function reviewByChallenge(int $id, ChallengeReviewRepository $repository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $cacheKey = "get:challenge:review:" . $id;
        $json = $cache->get($cacheKey, function (ItemInterface $item) use ($serializer, $repository, $id) {
            $item->tag(['getReviewCache']);

            $queryBuilder = $repository->createQueryBuilder('cr')
                ->join(ChallengeComplete::class, 'cc', 'WITH', 'cr.challengeComplete = cc.id')
                ->join(Challenge::class, 'c', 'WITH', 'cc.challenge = c.id')
                ->andWhere('c.id = :id')
                ->setParameter('id', $id);

            $challengeReviews = $queryBuilder->getQuery()->getResult();

            return $serializer->serialize($challengeReviews, 'json', ['groups' => 'review']);
        });

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('api/review/{id}', name: 'review.update', methods: ['PUT'])]
    public function updateCourse(?ChallengeReview $review, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, TagAwareCacheInterface $cache): JsonResponse
    {
        if (!$review) {
            return new JsonResponse(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }
        $course = $serializer->deserialize($request->getContent(), ChallengeReview::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $review]);
        $date = new \DateTime();
        $course->setUpdatedAt($date);
        $entityManagerInterface->persist($review);
        $entityManagerInterface->flush();
        $cache->invalidateTags(['getAllReviewCache']);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT, []);
    }

    #[Route('/api/review/{id}', name: 'review.delete', methods: ['DELETE'])]
    public function deleteCourse(?ChallengeReview $review, EntityManagerInterface $entityManagerInterface, TagAwareCacheInterface $cache): JsonResponse
    {
        if (!$review) {
            return new JsonResponse(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }
        $entityManagerInterface->remove($review);
        $entityManagerInterface->flush();
        $cache->invalidateTags(['getAllReviewCache']);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}