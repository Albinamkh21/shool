<?php

/*
Author
*/

namespace App\Controller\API\Course;

use App\Domain\Entity\Course;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class Controller
{
    public function __construct(private readonly Manager $manager) {
    }

    #[Route(path: 'api/course', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $title = $request->request->get('title');
        $course = $this->manager->create($title);
        if ($course === null) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($course->toArray());
    }

    #[Route(path: 'api/course', methods: ['DELETE'])]
    public function delete(Request $request): Response
    {
        $courseId = $request->query->get('id');
        $result = $this->manager->deleteCourseById($courseId);
        if ($result) {
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route(path: 'api/course', methods: ['GET'])]
    public function get(Request $request): Response
    {
        $courseId = $request->query->get('id');
        if ($courseId === null) {
            return new JsonResponse(array_map(static fn (Course $course): array => $course->toArray(), $this->manager->getAllCourses()));
        }
        $course = $this->manager->getCourseById($courseId);

        /** @var Course|null $course */
        if ($course instanceof Course) {
            return new JsonResponse($course->toArray());
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route(path: 'api/course', methods: ['PATCH'])]
    public function update(Request $request): Response
    {

        $courseId = $request->query->get('id');
        $title = $request->query->get('title');
        $result = $this->manager->updateCourseTitle($courseId, $title);

        if ($result) {
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


}