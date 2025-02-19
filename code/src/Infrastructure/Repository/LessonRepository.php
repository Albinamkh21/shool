<?php

namespace App\Infrastructure\Repository;

/**
 * @extends AbstractRepository<Lesson>
 */

use App\Domain\Entity\Lesson;


class LessonRepository extends AbstractRepository
{
    public function create(Lesson $lesson): int
    {
        return $this->store($lesson);
    }

    public function remove(Lesson $lesson): void
    {
        $lesson->setDeletedAt();
        $this->flush();
    }
    public function find(int $lessonId): ?Lesson
    {
        $repository = $this->entityManager->getRepository(Lesson::class);
        /** @var Lesson|null $lesson */
        $lesson = $repository->find($lessonId);

        return $lesson;
    }

    /**
     * @return Lesson[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Lesson::class)->findAll();
    }

    public function updateTitle(Lesson $lesson, string $title): void
    {
        $lesson->setTitle($title);
        $this->flush();
    }

    /**
     * @return Lesson[]
     */
    public function getLessonsSortByTitle(int $page, int $perPage, ?int $courseId): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('l')
            ->from($this->getClassName(), 'l')
            ->join('l.currentRevision', 'r');
        if(!is_null($courseId)){
            $queryBuilder->where('l.course = :courseId');
            $queryBuilder->setParameter(':courseId', $courseId);
        }
        $queryBuilder
            ->orderBy('r.title', 'ASC')
            ->setFirstResult($perPage * $page)
            ->setMaxResults($perPage);

        return $queryBuilder->getQuery()->getResult();
    }
    public function getTasksWithRevision(int $lessonId, ?int $userId)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select(' distinct t.id, tr.title, tr.text ')
            ->from($this->getClassName(), 'l')
            ->join('l.tasks', 't')
            ->join('t.revisions', 'tr')
            ->join('tr.userRevisions', 'str');
           // ->leftJoin('t.userRevisions', 'str', Expr\Join::WITH, 'str.user ='.$userId);
        if(!is_null($lessonId)){
            $queryBuilder->andWhere('l.id = :lessonId');
            $queryBuilder->setParameter(':lessonId', $lessonId);
        }
        if(!is_null($userId)){
            $queryBuilder->andWhere('str.user = :userId');
            $queryBuilder->setParameter(':userId', $userId);
        }

        /*$queryBuilder
            ->orderBy('r.title', 'ASC');
*/
       // dd($queryBuilder->getQuery()->getSQL());
       // dd($queryBuilder->getQuery()->getResult());
        return $queryBuilder->getQuery()->getResult();
    }

    public function getLessonsSortByTitle2(int $page, ?int $perPage, ?int $courseId): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('l')
            ->from($this->getClassName(), 'l');
          #  ->join('l.currentRevision', 'r');
        if(!is_null($courseId)){
            $queryBuilder->where('l.course = :courseId');
            $queryBuilder->setParameter(':courseId', $courseId);
        }
        $queryBuilder
            ->orderBy('l.id', 'ASC')
            ->setFirstResult($perPage * $page);
          if(!is_null($perPage)) {
              $queryBuilder->setMaxResults( $perPage );
          }

        return $queryBuilder->getQuery()->getResult();
    }

}
