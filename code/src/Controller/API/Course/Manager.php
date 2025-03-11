<?php


namespace App\Controller\API\Course;

use App\Domain\DTO\CourseInputDTO;
use App\Domain\Entity\Course;
use App\Domain\Service\CourseService;

class Manager
{
    public function __construct(private readonly CourseService $courseService)
    {
    }

    public function create(CourseInputDTO $courseDTO): ?Course
    {
        return $this->courseService->create($courseDTO);
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

    public function addStudent(int $courseId, int $studentId): ?Course
    {
        $course = $this->courseService->addStudent($courseId, $studentId);

        return $course ;
    }

    public function deleteStudent(int $courseId, int $studentId): ?Course
    {
        $course = $this->courseService->deleteStudent($courseId, $studentId);

        return $course ;
    }

    public function setTeacher(int $courseId, int $userId): ?Course
    {
        $course = $this->courseService->setTeacher($courseId, $userId);

        return $course ;
    }


    public function deleteTeacher(int $courseId, int $userId): ?Course
    {
        $course = $this->courseService->deleteTeacher($courseId, $userId);

        return $course ;
    }


}