<?php

namespace App\Domain\Service;


use App\Domain\Entity\Lesson;
use App\Infrastructure\Repository\CourseRepository;
use App\Infrastructure\Repository\LessonRepository;


class LessonService
{
    public function __construct(private readonly LessonRepository $lessonRepository, private readonly CourseRepository $courseRepository)
    {
    }
    public function create(string $title, int $courseId): Lesson
    {
        $lesson = new Lesson();
        $lesson->setTitle($title);

        $course = $this->courseRepository->find($courseId);
        $lesson->setCourse($course);
        $lesson->setCreatedAt();
        $lesson->setUpdatedAt();
        $this->lessonRepository->create($lesson);

        return $lesson;
    }
    public function removeById(int $lessonId): bool
    {
        $lesson = $this->lessonRepository->find($lessonId);
        if ($lesson instanceof Lesson) {
            $this->lessonRepository->remove($lesson);
            return true;
        }

        return false;
    }
    public function findLessonById(int $id): ?Lesson
    {
        return $this->lessonRepository->find($id);
    }

    /**
     * @return Course[]
     */
    public function findAll(): array
    {
        return $this->lessonRepository->findAll();
    }
    public function updateLessonTitle(int $lessonId, string $title): ?Lesson
    {
        $lesson = $this->lessonRepository->find($lessonId);
        if (!($lesson instanceof Lesson)) {
            return null;
        }
        $this->lessonRepository->updateTitle($lesson, $title);

        return $lesson;
    }
}