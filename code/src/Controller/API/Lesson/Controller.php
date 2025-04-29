<?php

namespace App\Controller\API\Lesson;


use App\Domain\Entity\Lesson;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use App\Domain\DTO\LessonInputDTO;

#[AsController]
class Controller
{
    public function __construct(
        private readonly Manager $manager,
        private ValidatorInterface $validator,
        private TranslatorInterface $translator,
    ) {
    }

    #[Route(path: 'api/lesson', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $data =  $request->request->all();
        $lessonDto = new LessonInputDTO(
            $data['title'] ?? '',
            $data['description'] ?? '',
            (int)   ($data['courseId'] ?? 0),
            (int) ($data['order'] ?? 0),
            json_decode($data['contents'] ?? '', true)
        );


        $errors = $this->validator->validate($lessonDto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $this->translator->trans($error->getMessage(), [], 'messages');
            }
            return new JsonResponse(['errors' => $errorMessages], 400);
        }

        $lesson = $this->manager->create($lessonDto);
        if ($lesson === null) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($lesson->toArray());

    }

    #[Route(path: 'api/lesson', methods: ['DELETE'])]
    public function delete(Request $request): Response
    {
        $lessonId = (int) $request->request->get('id');
        if (!$lessonId) {
            return new JsonResponse(['errors' => 'ID is required'], Response::HTTP_BAD_REQUEST);
        }


        $result = $this->manager->deleteLessonById($lessonId);
        if ($result) {
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['errors' => 'Invalid ID'], Response::HTTP_NOT_FOUND);
    }

    #[Route(path: 'api/lesson', methods: ['GET'])]
    public function get(Request $request): Response
    {
        $lessonId = (int)$request->query->get('id');
        if ($lessonId === 0) {
            return new JsonResponse(array_map(static fn (Lesson $lesson): array => $lesson->toArray(), $this->manager->getAllLessons()));
        }

        $lesson = $this->manager->getLessonById($lessonId);
        /** @var Lesson|null $lesson */
        if ($lesson instanceof Lesson) {
            return new JsonResponse($lesson->toArray());
        }

        return new JsonResponse(['errors' => 'Invalid ID'], Response::HTTP_NOT_FOUND);
    }


    #[Route(path: 'api/lesson', methods: ['PATCH'])]
    public function update(Request $request): Response
    {

        $data =  $request->request->all();
        $lessonId = (int) ($data['id'] ?? 0);

        if (!$lessonId) {
            return new JsonResponse(['errors' => 'ID is required'], Response::HTTP_BAD_REQUEST);
        }

        $lessonDto = new LessonInputDTO(
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['courseId'] ?? 0,
            $data['order'] ?? 0,
            json_decode($data['contents'] ?? [], true)
        );
        $result = $this->manager->updateLesson($lessonId, $lessonDto);

        if ($result) {
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['errors' => 'Invalid ID'], Response::HTTP_NOT_FOUND);
    }


}