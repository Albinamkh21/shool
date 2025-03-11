<?php

namespace App\Controller\API\Lesson;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Domain\DTO\LessonContentInputDTO;

#[Route('api/lesson/{lessonId}/content')]
class LessonContentController extends AbstractController
{

    public function __construct(
        private  LessonContentManager $manager,
        private ValidatorInterface $validator,
    )
    {

    }

    #[Route('/add', methods: ['POST'])]
    public function addContent(int $lessonId, Request $request): JsonResponse
    {
        $data =  $request->request->all();
        $lessonContentDTO = new LessonContentInputDTO($data['type'] ?? '', $data['content'] ?? '');

        $errors = $this->validator->validate($lessonContentDTO);

        if (count($errors) > 0) {
            return $this->json(['errors' => $this->formatValidationErrors($errors)], 400);
        }

        $lessonContent = $this->manager->addContentToLesson($lessonId, $lessonContentDTO);

        return $this->json(['contentId' => $lessonContent->getId()]);
    }

    #[Route('/{contentId}/update', methods: ['PUT'])]
    public function updateContent(int $lessonId, int $contentId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $lessonContentDTO = new LessonContentInputDTO($data['type'], $data['content']);

        $updatedContent = $this->lessonContentService->updateLessonContent($lessonId, $contentId, $lessonContentDTO);

        return $this->json(['contentId' => $updatedContent->getId()]);
    }

    #[Route('/{contentId}/delete', methods: ['DELETE'])]
    public function deleteContent(int $lessonId, int $contentId): JsonResponse
    {
        $this->manager->removeContentFromLesson($lessonId, $contentId);

        return $this->json(['message' => 'Content deleted']);
    }
}
