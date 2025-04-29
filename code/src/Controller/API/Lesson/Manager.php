<?php


namespace App\Controller\API\Lesson;

use App\Domain\DTO\LessonInputDTO;
use App\Domain\Entity\Course;
use App\Domain\Entity\Lesson;
use App\Domain\Service\LessonService;

class Manager
{
    public function __construct(private readonly LessonService $lessonService)
    {
    }

    public function create(LessonInputDTO $lessonDTO): ?Lesson
    {
        return $this->lessonService->create($lessonDTO);
    }
    public function deleteLessonById(int $lessonId): bool
    {
        return $this->lessonService->removeById($lessonId);
    }
    public function getLessonById(int $courseId): ?Lesson
    {
        return $this->lessonService->findLessonById($courseId);
    }

    /**
     * @return Lesson[]
     */
    public function getAllLessons(): array
    {
        print('getAllLessons');
        return $this->lessonService->findAll();
    }

    public function updateLessonTitle(int $lessonId, string $title): bool
    {
        $lesson = $this->lessonService->updateLessonTitle($lessonId, $title);

        return $lesson instanceof Lesson;
    }
    public function updateLesson(int $lessonId, LessonInputDTO $lessonInputDTO): bool
    {
        $lesson = $this->lessonService->updateLesson($lessonId, $lessonInputDTO);

        return $lesson instanceof Lesson;
    }


}