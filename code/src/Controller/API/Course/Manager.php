<?php


namespace App\Controller\API\Course;

use App\Domain\Entity\Course;
use App\Domain\Service\CourseService;

class Manager
{
    public function __construct(private readonly CourseService $courseService)
    {
    }

    public function create(string $title): ?Course
    {
        return $this->courseService->create($title);
    }
    public function deleteCourseById(int $courseId): bool
    {
        return $this->courseService->removeById($courseId);
    }
    public function getCourseById(int $courseId): ?Course
    {
        return $this->courseService->findCourseById($courseId);
    }

    /**
     * @return Course[]
     */
    public function getAllCourses(): array
    {
        return $this->courseService->findAll();
    }

    public function updateCourseTitle(int $courseId, string $title): bool
    {
        $course = $this->courseService->updateCourseTitle($courseId, $title);

        return $course instanceof Course;
    }


}