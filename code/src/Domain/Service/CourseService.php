<?php

namespace App\Domain\Service;


use App\Domain\Entity\Course;
use App\Infrastructure\Repository\CourseRepository;


class CourseService
{
    public function __construct(private readonly CourseRepository $courseRepository)
    {
    }
    public function create(string $title): Course
    {
        $course = new Course();
        $course->setTitle($title);
        $course->setCreatedAt();
        $course->setUpdatedAt();
        $this->courseRepository->create($course);

        return $course;
    }
    public function removeById(int $courseId): bool
    {
        $course = $this->courseRepository->find($courseId);
        if ($course instanceof Course) {
            $this->courseRepository->remove($course);

            return true;
        }

        return false;
    }
    public function findCourseById(int $id): ?Course
    {
        return $this->courseRepository->find($id);
    }

    /**
     * @return Course[]
     */
    public function findAll(): array
    {
        return $this->courseRepository->findAll();
    }
    public function updateCourseTitle(int $courseId, string $title): ?Course
    {
        $course = $this->courseRepository->find($courseId);
        if (!($course instanceof Course)) {
            return null;
        }
        $this->courseRepository->updateTitle($course, $title);

        return $course;
    }
}