<?php

/*
Author
*/

namespace App\Controller\API\Course;


use App\Domain\DTO\CourseInputDTO;
use App\Domain\Entity\Course;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use  App\Controller\API\AbstractBaseController;

#[AsController]
class Controller extends AbstractBaseController
{

    public function __construct(private readonly Manager $manager,
                                private readonly ValidatorInterface $validator,
                                private readonly TranslatorInterface $translator,

    ) {
    }

    #[Route(path: 'api/course', methods: ['POST'])]
    public function create(CourseInputDTO $courseDTO): Response
    {
        if(!($courseDTO instanceof CourseInputDTO)) {
            return new JsonResponse(['error' => 'Invalid DTO'], Response::HTTP_BAD_REQUEST);
        }
        $errors = $this->validator->validate($courseDTO);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();  //$this->trans($error->getMessage(), [], 'messages');
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
        $courseId = (int) $request->request->get('id');

        if($courseId === null || $courseId == 0) {
            return new JsonResponse(['error' => 'ID is required'], Response::HTTP_BAD_REQUEST);
        }
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


    #[Route(path: 'api/course/{id}', methods: ['PATCH'])]
    public function update(int $id, CourseInputDTO $courseDTO): Response
    {

        if(!($courseDTO instanceof CourseInputDTO)) {
            return new JsonResponse(['error' => 'Invalid DTO'], Response::HTTP_BAD_REQUEST);
        }

        $errors = $this->validator->validate($courseDTO);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();  //$this->trans($error->getMessage(), [], 'messages');
            }
            return new JsonResponse(['errors' => $errorMessages], 400);
        }

        $result = $this->manager->updateCourse($id, $courseDTO);

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


    #[Route(path: 'api/course/{courseId}/pay', methods: ['POST'])]
    public function payCourse(int $courseId, Request $request): JsonResponse
    {
       $user = $this->getUser(); // Получаем текущего пользователя
       $user->getUserIdentifier();

        /** @var Course $course */
       $course = $this->manager->getCourseById($courseId);

       //todo - check enroll


        if (!$course) {
            return new JsonResponse(['error' => 'Курс не найден'], 404);
        }

        $paymentType = $request->get('payment_type'); // full, discount, installments
        $discountPercent = $request->get('discount', 0);
        $installments = $request->get('installments', 1);

     //   $result =$this->manager->handlePayment( $user->getUserIdentifier(), $course, $paymentType, $discountPercent, $installments);
        $result= null;
        if (!$result) {
            return new JsonResponse(['error' => $this->trans('payment.error.common')], 400);
        }

        return new JsonResponse(['success' => 'Оплата прошла успешно']);
    }




}