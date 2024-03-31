<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Section;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class SectionController extends AbstractController
{
    #[Route('/api/section', name: 'section.all', methods: ['GET'])]
    public function getAll(SectionRepository $repository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $cacheKey = "get:all:section";
        $json = $cache->get($cacheKey, function (ItemInterface $item) use ($serializer, $repository) {
            $item->tag('getAllSectionCache');
            $sections = $repository->findAll();
            return $serializer->serialize($sections, 'json', ['groups' => 'section']);
        });

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/section/{id}', name: 'section.show', methods: ['GET'])]
    #[ParamConverter("section")]
    public function show(?Section $section, SerializerInterface $serializer): JsonResponse
    {
        if (!$section) {
            return new JsonResponse(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }
        $json = $serializer->serialize($section, 'json', ['groups' => 'section']);

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/section', name: 'section.create', methods: ['POST'])]
    public function createSection(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, UrlGeneratorInterface $urlGenerator, TagAwareCacheInterface $cache): JsonResponse
    {
        $section = $serializer->deserialize($request->getContent(), Section::class, 'json');
        $date = new \DateTime();
        $body = $request->toArray();
        $section->setType($body['type'])
            ->setContent($body['content'])
            ->setPosition($body['position'])
            ->setCreatedAt($date)
            ->setUpdatedAt($date);
        $courseID = $body['course'];
        $course = $entityManagerInterface->getRepository(Course::class)->find($courseID);
        if (!$course) {
            return new JsonResponse(['error' => 'Course not found'], Response::HTTP_BAD_REQUEST);
        }
        $section->setCourse($course);
        $entityManagerInterface->persist($section);
        $entityManagerInterface->flush();
        $location = $urlGenerator->generate('section.all', ['id' => $section->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $json = $serializer->serialize($section, 'json', ['groups' => 'section']);
        $cache->invalidateTags(['getAllSectionCache']);

        return new JsonResponse($json, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('api/section/{id}', name: 'section.update', methods: ['PUT'])]
    public function updateSection(?Section $section, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, TagAwareCacheInterface $cache): JsonResponse
    {
        if (!$section) {
            return new JsonResponse(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }
        $section = $serializer->deserialize($request->getContent(), Section::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $section]);
        $date = new \DateTime();
        $section->setUpdatedAt($date);
        $entityManagerInterface->persist($section);
        $entityManagerInterface->flush();
        $cache->invalidateTags(['getAllSectionCache']);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT, []);
    }

    #[Route('/api/section/{id}', name: 'section.delete', methods: ['DELETE'])]
    public function deleteSection(?Section $section, EntityManagerInterface $entityManagerInterface, TagAwareCacheInterface $cache): JsonResponse
    {
        if (!$section) {
            return new JsonResponse(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }
        $entityManagerInterface->remove($section);
        $entityManagerInterface->flush();
        $cache->invalidateTags(['getAllSectionCache']);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
