<?php

namespace App\Controller;

use App\Repository\ResponseRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
            $response = $repository->findBy(['is_valid' => 'true']);
            return $serializer->serialize($response, 'json', ['groups' => 'response']);
        });

        return new JsonResponse($json, 200, [], true);
    }
}
