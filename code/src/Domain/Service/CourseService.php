<?php

namespace App\Domain\Service;


use App\Domain\DTO\CourseInputDTO;
use App\Domain\Entity\Course;
use App\Domain\Entity\User;
use App\Infrastructure\Repository\CourseRepository;
use App\Infrastructure\Repository\UserRepository;


class CourseService
{
    public function __construct(
        private readonly CourseRepository $courseRepository,
        private readonly UserRepository $userRepository
    )
    {
    }
    public function create(CourseInputDTO $courseDTO): Course
    {
        $course = new Course();
        $course->setTitle($courseDTO->title);
       // $course->set($courseDTO->title);
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
    public function addStudent(int $courseId, int $studentId): ?Course
    {
        $course = $this->courseRepository->find($courseId);

        print($studentId);
        if (!($course instanceof Course)) {
            return null;
        }
        $user = $this->userRepository->find($studentId);
        if (!($user instanceof User)) {
            return null;
        }

        $this->courseRepository->addStudent($course, $user);

        return $course;

    }

    public function deleteStudent(int $courseId, int $studentId): ?Course
    {
        $course = $this->courseRepository->find($courseId);

        print($studentId);
        if (!($course instanceof Course)) {
            return null;
        }
        $user = $this->userRepository->find($studentId);
        if (!($user instanceof User)) {
            return null;
        }

        $this->courseRepository->deleteStudent($course, $user);

        return $course;

    }

    public function setTeacher(int $courseId, int $userId): ?Course
    {
        $course = $this->courseRepository->find($courseId);

        if (!($course instanceof Course)) {
            return null;
        }
        $user = $this->userRepository->find($userId);
        if (!($user instanceof User)) {
            return null;
        }

        $this->courseRepository->setTeacher($course, $user);

        return $course;

    }
    public function deleteTeacher(int $courseId, int $userId): ?Course
    {
        $course = $this->courseRepository->find($courseId);

        if (!($course instanceof Course)) {
            return null;
        }
        $user = $this->userRepository->find($userId);
        if (!($user instanceof User)) {
            return null;
        }

        $this->courseRepository->deleteTeacher($course, $user);

        return $course;

    }
}