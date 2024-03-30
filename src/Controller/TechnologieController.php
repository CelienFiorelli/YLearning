<?php

namespace App\Controller;

use App\Entity\Technologie;
use App\Repository\TechnologieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class TechnologieController extends AbstractController
{
    #[Route('/api/technologie', name: 'technologie.all', methods: ['GET'])]
    public function getAll(TechnologieRepository $repository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $cacheKey = "get:all:technologie";
        $json = $cache->get($cacheKey, function (ItemInterface $item) use ($serializer, $repository) {
            $item->tag('getAllTechnologieCache');
            $technologies = $repository->findBy(['status' => 'on']);
            return $serializer->serialize($technologies, 'json', ['groups' => 'technologie']);
        });

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/technologie/{id}', name: 'technologie.show', methods: ['GET'])]
    #[ParamConverter("technologie")]
    public function show(?Technologie $technologie, SerializerInterface $serializer): JsonResponse
    {
        if (!$technologie) {
            return new JsonResponse(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }
        $json = $serializer->serialize($technologie, 'json', ['groups' => 'technologie']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/api/technologie', name: 'technologie.create', methods: ['POST'])]
    public function createTechnologie(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, UrlGeneratorInterface $urlGenerator, TagAwareCacheInterface $cache): JsonResponse
    {
        $technologie = $serializer->deserialize($request->getContent(), Technologie::class, 'json');
        $date = new \DateTime();
        $technologie->setStatus('on')->setCreatedAt($date)->setUpdatedAt($date);

        $entityManagerInterface->persist($technologie);
        $entityManagerInterface->flush();
        $location = $urlGenerator->generate('technologie.show', ['id' => $technologie->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $json = $serializer->serialize($technologie, 'json');
        $cache->invalidateTags(['getAllTechnologieCache']);

        return new JsonResponse($json, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('api/technologie/{id}', name: 'technologie.update', methods: ['PUT'])]
    public function updateTechnologie(?Technologie $technologie, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, TagAwareCacheInterface $cache): JsonResponse
    {
        if (!$technologie) {
            return new JsonResponse(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }
        $technologie = $serializer->deserialize($request->getContent(), Technologie::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $technologie]);
        $date = new \DateTime();
        $technologie->setUpdatedAt($date);
        $entityManagerInterface->persist($technologie);
        $entityManagerInterface->flush();
        $cache->invalidateTags(['getAllTechnologieCache']);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT, []);
    }

    #[Route('/api/technologie/{id}', name: 'technologie.delete', methods: ['DELETE'])]
    public function deleteTechnologie(?Technologie $technologie, EntityManagerInterface $entityManagerInterface, TagAwareCacheInterface $cache): JsonResponse
    {
        if (!$technologie) {
            return new JsonResponse(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }
        $technologie->setStatus('off')->setUpdatedAt(new \DateTime());
        $entityManagerInterface->persist($technologie);
        $entityManagerInterface->flush();
        $cache->invalidateTags(['getAllTechnologieCache']);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
