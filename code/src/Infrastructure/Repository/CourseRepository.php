<?php

namespace App\Infrastructure\Repository;

/**
 * @extends AbstractRepository<Course>
 */
use App\Domain\Entity\Course;
use App\Domain\Entity\User;

class CourseRepository  extends AbstractRepository
{


    public function create(Course $course): Course
    {
        return $this->store($course);
    }

    public function remove(Course $course): bool

    {
       return  $this->delete($course);
        //$course->setDeletedAt();
        //$this->flush();
    }
    public function find(int $courseId): ?Course
    {
        $repository = $this->entityManager->getRepository(Course::class);
        /** @var Course|null $course */
        $course = $repository->find($courseId);

        return $course;
    }

    /**
     * @return Course[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Course::class)->findAll();
    }


    public function updateTitle(Course $course, string $title): void
    {
        $course->setTitle($title);
        $this->flush();
    }
    public function addStudent(Course $course, User $user): void
    {
        $course->addStudent($user);
        $user->enrollInCourse($course);
        $this->flush();
    }

    public function deleteStudent(Course $course, User $user): void
    {
        $course->removeStudent($user);
        $user->unenrollFromCourse($course);
        $this->flush();
    }


    public function setTeacher(Course $course, User $user): void
    {
        $course->setTeacher($user);
        $user->addTeachingCourse($course);
        $this->flush();
    }
    public function deleteTeacher(Course $course, User $user): void
    {
        $course->removeTeacher($user);
        $user->removeTeachingCourse($course);
        $this->flush();
    }



    /**
     * @return Course[]
     */
    public function getCoursesSortByTitle(int $page, int $perPage): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('c.*, u.*')
            ->from($this->getClassName(), 'c')
            ->leftJoin('c.teacher', 't')
            ->leftJoin('t.user', 'u')
            ->orderBy('c.title', 'ASC')
            ->setFirstResult($perPage * $page)
            ->setMaxResults($perPage);

        return $queryBuilder->getQuery()->getResult();
    }
}
