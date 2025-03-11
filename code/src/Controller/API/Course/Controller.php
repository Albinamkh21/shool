<?php

/*
Author
*/

namespace App\Controller\API\Course;

use App\Domain\DTO\CourseInputDTO;
use App\Domain\Entity\Course;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
#[AsController]
class Controller
{
    public function __construct(private readonly Manager $manager,
                                private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route(path: 'api/course', methods: ['POST'])]
    public function create(Request $request): Response
    {

        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $courseDTO = new CourseInputDTO($title, $description);

        $errors = $this->validator->validate($courseDTO);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $this->translator->trans($error->getMessage(), [], 'messages');
            }
            return new JsonResponse(['errors' => $errorMessages], 400);
        }

         $course = $this->manager->create($courseDTO);
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

    #[Route(path: 'api/course/{courseId}/addStudent', methods: ['POST'])]
    public function addStudent(int $courseId, Request $request): Response
    {
        $studentId = $request->request->get('userId');

        $course = $this->manager->addStudent($courseId, (int) $studentId);
        if ($course === null) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($course->toArray());

    }

    #[Route(path: 'api/course/{courseId}/deleteStudent', methods: ['DELETE'])]
    public function deleteStudent(int $courseId, Request $request): Response
    {
        $studentId = $request->query->get('userId');

        $course = $this->manager->deleteStudent($courseId, (int) $studentId);
        if ($course === null) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($course->toArray());

    }


    #[Route(path: 'api/course/{courseId}/setTeacher', methods: ['POST'])]
    public function setTeacher(int $courseId, Request $request): Response
    {
        $userId = $request->request->get('userId');

        $course = $this->manager->setTeacher($courseId, (int)$userId);
        if ($course === null) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($course->toArray());
    }

    #[Route(path: 'api/course/{courseId}/deleteTeacher', methods: ['DELETE'])]
    public function deleteTeacher(int $courseId, Request $request): Response
    {
        $userId = $request->query->get('userId');

        $course = $this->manager->deleteTeacher($courseId, (int)$userId);
        if ($course === null) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($course->toArray());
    }



}