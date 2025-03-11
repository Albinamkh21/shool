<?php


namespace App\Controller\API\Lesson;

use App\Domain\Entity\Lesson;
use App\Domain\Entity\LessonContent\LessonContent;
use App\Domain\Service\LessonContentService;
use App\Domain\DTO\LessonContentInputDTO;

class LessonContentManager
{
    public function __construct(private readonly LessonContentService $contentService)
    {
    }

    public function addContentToLesson(int $lessonId, LessonContentInputDTO $contentDTO): ?LessonContent
    {
        return $this->contentService->addContentToLesson($lessonId, $contentDTO);
    }
    public function removeContentFromLesson(int $lessonId, int $contentId): bool
    {
        return $this->contentService->removeContentFromLesson($lessonId, $contentId);
    }
    public function getLessonById(int $courseId): ?Lesson
    {
        return $this->contentService->findLessonById($courseId);
    }

    /**
     * @return Lesson[]
     */
    public function getAllLessons(): array
    {
        return $this->contentService->findAll();
    }

    public function updateLessonTitle(int $lessonId, string $title): bool
    {
        $lesson = $this->contentService->updateLessonTitle($lessonId, $title);

        return $lesson instanceof Lesson;
    }


}