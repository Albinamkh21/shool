<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;


/**
 * @template T
 */
abstract class AbstractRepository
{
    public function __construct(protected readonly EntityManagerInterface $entityManager)
    {
    }

    protected function flush(): void
    {
        $this->entityManager->flush();
    }

    /**
     * @param T $entity
     */
    protected function store(EntityInterface $entity): EntityInterface
    {
        $this->entityManager->persist($entity);
        $this->flush();

        return $entity;
    }
    protected function delete(EntityInterface $entity): bool
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return true;
    }
    public function update(EntityInterface $entity): EntityInterface
    {

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }
}