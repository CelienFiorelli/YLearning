<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Technologie;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class CourseController extends AbstractController
{
    #[Route('/api/course', name: 'course.all', methods: ['GET'])]
    public function getAll(CourseRepository $repository, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $cacheKey = "get:all:course";
        $json = $cache->get($cacheKey, function (ItemInterface $item) use ($serializer, $repository) {
            $item->tag('getAllCourseCache');
            $course = $repository->findBy(['status' => 'on']);
            return $serializer->serialize($course, 'json', ['groups' => 'course']);
        });

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/course/{id}', name: 'course.show', methods: ['GET'])]
    #[ParamConverter("course")]
    public function show(Course $course, SerializerInterface $serializer): JsonResponse
    {
        $json = $serializer->serialize($course, 'json', ['groups' => 'course']);

        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/api/course', name: 'course.create', methods: ['POST'])]
    public function createCourse(Request $request, ValidatorInterface $validator, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, UrlGeneratorInterface $urlGenerator, TagAwareCacheInterface $cache): JsonResponse
    {
        $course = $serializer->deserialize($request->getContent(), Course::class, 'json');
        $errors = $validator->validate($course);
        if ($errors->count()) {
            return new JsonResponse($serializer->serialize($errors, 'json'), Response::HTTP_UNPROCESSABLE_ENTITY, [], true);
        }

        $date = new \DateTime();
        $body = $request->toArray();
        $course->setTitle($body['title'])
            ->setLevel($body['level'])
            ->setStatus('on')
            ->setCreatedAt($date)
            ->setUpdatedAt($date);
        $technoId = $body['techno'];
        $technologie = $entityManagerInterface->getRepository(Technologie::class)->find($technoId);
        if (!$technologie) {
            return new JsonResponse(['error' => 'Technologie not found'], Response::HTTP_BAD_REQUEST);
        }
        $course->setTechnologie($technologie);
        $entityManagerInterface->persist($course);
        $entityManagerInterface->flush();
        $location = $urlGenerator->generate('course.all', ['id' => $course->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $json = $serializer->serialize($course, 'json', ['groups' => 'course']);
        $cache->invalidateTags(['getAllCourseCache']);

        return new JsonResponse($json, Response::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('api/course/{id}', name: 'course.update', methods: ['PUT'])]
    public function updateCourse(Course $course, Request $request, ValidatorInterface $validator, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, TagAwareCacheInterface $cache): JsonResponse
    {
        $course = $serializer->deserialize($request->getContent(), Course::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $course]);
        $errors = $validator->validate($course);
        if ($errors->count()) {
            return new JsonResponse($serializer->serialize($errors, 'json'), Response::HTTP_UNPROCESSABLE_ENTITY, [], true);
        }

        $date = new \DateTime();
        $course->setUpdatedAt($date);
        $entityManagerInterface->persist($course);
        $entityManagerInterface->flush();
        $cache->invalidateTags(['getAllCourseCache']);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT, []);
    }

    #[Route('/api/course/{id}', name: 'course.delete', methods: ['DELETE'])]
    public function deleteCourse(Course $course, EntityManagerInterface $entityManagerInterface, TagAwareCacheInterface $cache): JsonResponse
    {
        $entityManagerInterface->remove($course);
        $entityManagerInterface->flush();
        $cache->invalidateTags(['getAllCourseCache']);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
