<?php

namespace App\Controller;

use App\Entity\Technologie;
use App\Repository\TechnologieRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class TechnologieController extends AbstractController
{
    // #[Route('/technologie', name: 'app_technologie')]
    // public function index(): JsonResponse
    // {
    //     return $this->json([
    //         'message' => 'Welcome to your new controller!',
    //         'path' => 'src/Controller/TechnologieController.php',
    //     ]);
    // }
    #[Route('/api/technologie', name: 'technologie.all', methods: ['GET'])]
    public function getAll (TechnologieRepository $repository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $cacheKey = "get:all:technologie";
        $jsonTests = $cache->get($cacheKey, function (ItemInterface $item) use ($serializer, $repository) {
            $item->tag('getAllTestCache');
            $tests = $repository->findAll();
            return $serializer->serialize($tests, 'json');
        });
        
        return new JsonResponse($jsonTests, 200, [], true);
    }

    #[Route('/api/technologie/{id}', name: 'technologie.show', methods: ['GET'])]
    #[ParamConverter("technologie")]
    public function show (Technologie $technologie, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($technologie, 'json');
        return new JsonResponse($json, 200, [], true);
    }
}
