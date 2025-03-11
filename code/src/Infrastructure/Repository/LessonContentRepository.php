<?php

namespace App\Infrastructure\Repository;

/**
 * @extends AbstractRepository<\App\Domain\Entity\LessonContent\LessonContent>
 */

use App\Domain\Entity\LessonContent\LessonContent;


class LessonContentRepository extends AbstractRepository
{
    public function create(LessonContent $lessonContent): LessonContent
    {
        return $this->store($lessonContent);
    }

    public function remove(LessonContent $lessonContent): void
    {
        $this->delete($lessonContent);
    }
    public function find(int $contentId): ?LessonContent
    {
        $repository = $this->entityManager->getRepository(LessonContent::class);
        /** @var LessonContent|null $content */
        $content = $repository->find($contentId);

        return $content;
    }

    /**
     * @return Lesson[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(LessonContent::class)->findAll();
    }



}
