<?php

namespace App\Infrastructure\Repository;

/**
 * @extends AbstractRepository<User>
 */
use App\Domain\Entity\User;
class UserRepository extends AbstractRepository
{
    public function create(User $user): int
    {
        return $this->store($user);
    }
    public function remove(User $user): void
    {
        $user->setDeletedAt();
        $this->flush();
    }
    public function find(int $userId): ?User
    {
        $repository = $this->entityManager->getRepository(User::class);
        /** @var User|null $user */
        $user = $repository->find($userId);

        return $user;
    }
    /**
     * @return User[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(User::class)->findAll();
    }

    public function findUsersByLogin(string $name): array
    {
        return $this->entityManager->getRepository(User::class)->findBy(['login' => $name]);
    }
    public function updateLogin(User $user, string $login): void
    {
        $user->setLogin($login);
        $this->flush();
    }
    public function updateUserToken(User $user): string
    {
        $token = base64_encode(random_bytes(20));
        $user->setToken($token);
        $this->flush();

        return $token;
    }
    public function clearUserToken(User $user): void
    {
        $user->setToken(null);
        $this->flush();
    }

}