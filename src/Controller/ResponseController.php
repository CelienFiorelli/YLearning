<?php

namespace App\Controller;

use App\Entity\Response as EntityResponse;
use App\Entity\Section;
use App\Repository\ResponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ResponseController extends AbstractController
{
    #[Route('/api/response', name: 'response.all', methods: ['GET'])]
    public function getAll(ResponseRepository $repository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $cacheKey = "get:all:response";
        $json = $cache->get($cacheKey, function (ItemInterface $item) use ($serializer, $repository) {
            $item->tag('getAllResponseCache');
            $response = $repository->findBy(['isValid' => 'true']);
            return $serializer->serialize($response, 'json', ['groups' => 'response', 'section']);
        });

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/response', name: 'response.create', methods: ['POST'])]
    public function createResponse(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, UrlGeneratorInterface $urlGenerator, TagAwareCacheInterface $cache): JsonResponse
    {
        $response = $serializer->deserialize($request->getContent(), EntityResponse::class, 'json');
        $date = new \DateTime();
        $body = $request->toArray();
        $response->setIsValid(true)
            ->setCreatedAt($date)
            ->setUpdatedAt($date);
        $sectionId = $body['section'];
        $section = $entityManagerInterface->getRepository(Section::class)->find($sectionId);
        if (!$section) {
            return new JsonResponse(['error' => 'Section not found'], Response::HTTP_NOT_FOUND);
        }
        $response->setSection($section);
        $entityManagerInterface->persist($response);
        $entityManagerInterface->flush();
        $location = $urlGenerator->generate('response.all', ['id' => $response->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $json = $serializer->serialize($response, 'json', ['groups' => 'response']);
        $cache->invalidateTags(['getAllResponseCache']);

        return new JsonResponse($json, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('api/response/{id}', name: 'response.update', methods: ['PUT'])]
    public function updateResponse(EntityResponse $response, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, TagAwareCacheInterface $cache): JsonResponse
    {
        $response = $serializer->deserialize($request->getContent(), EntityResponse::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $response]);
        $date = new \DateTime();
        $response->setUpdatedAt($date);
        $entityManagerInterface->persist($response);
        $entityManagerInterface->flush();
        $cache->invalidateTags(['getAllResponseCache']);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT, []);
    }

    #[Route('/api/response/{id}', name: 'response.delete', methods: ['DELETE'])]
    public function deleteResponse(EntityResponse $response, EntityManagerInterface $entityManagerInterface, TagAwareCacheInterface $cache): JsonResponse
    {
        $entityManagerInterface->remove($response);
        $entityManagerInterface->flush();
        $cache->invalidateTags(['getAllResponseCache']);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
