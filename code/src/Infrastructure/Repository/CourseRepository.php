<?php

namespace App\Infrastructure\Repository;

/**
 * @extends AbstractRepository<Course>
 */
use App\Domain\Entity\Course;

class CourseRepository  extends AbstractRepository
{


    public function create(Course $course): int
    {
        return $this->store($course);
    }

    public function remove(Course $course): void
    {
        $course->setDeletedAt();
        $this->flush();
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
