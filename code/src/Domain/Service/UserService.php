<?php

namespace App\Domain\Service;


use App\Domain\DTO\CreateUserDTO;
use App\Domain\Entity\User;
use App\Infrastructure\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(private readonly UserRepository $userRepository,
                                private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }
    public function create(CreateUserDTO $createUserDTO): User
    {
        $user = new User();
        $user->setLogin($createUserDTO->login);
        $user->setFullName($createUserDTO->fullName);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $createUserDTO->password));
        $user->setPhone($createUserDTO->phone);
        $user->setEmail($createUserDTO->email);
        $user->setAge($createUserDTO->age);
        $user->setIsActive($createUserDTO->isActive);
        $user->setRoles($createUserDTO->roles);
        $user->setCreatedAt();
        $user->setUpdatedAt();

        $this->userRepository->create($user);

        return $user;
    }
    public function update(int $userId, CreateUserDTO $createUserDTO): ?User
    {

        $user = $this->userRepository->find($userId);
        if (!($user instanceof User)) {
            return null;
        }
        $user->setLogin($createUserDTO->login);
        $user->setFullName($createUserDTO->fullName);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $createUserDTO->password));
        $user->setPhone($createUserDTO->phone);
        $user->setEmail($createUserDTO->email);
        $user->setAge($createUserDTO->age);
        $user->setIsActive($createUserDTO->isActive);
        $user->setRoles($createUserDTO->roles);
        $user->setUpdatedAt();

        $this->userRepository->update($user);

        return $user;

    }

    public function removeById(int $userId): bool
    {
        $user = $this->userRepository->find($userId);
        if ($user instanceof User) {
            $this->userRepository->remove($user);

            return true;
        }
        
        return false;
    }
    public function findUserById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    /**
     * @return User[]
     */
    public function findAll(): array
    {
        return $this->userRepository->findAll();
    }
    /**
     * @return User[]
     */

    public function findUsersByLogin(string $login): array
    {
        return $this->userRepository->findUsersByLogin($login);
    }

    public function findUserByLogin(string $login): ?User
    {
        $users = $this->userRepository->findUsersByLogin($login);

        return $users[0] ?? null;
    }
    public function updateUserLogin(int $userId, string $login): ?User
    {
        $user = $this->userRepository->find($userId);
        if (!($user instanceof User)) {
           // return null;
            throw new \Exception('User not found');
        }
        $this->userRepository->updateLogin($user, $login);

        return $user;
    }

    public function updateUserToken(string $login): ?string
    {
        $user = $this->findUserByLogin($login);
        if ($user === null) {
            return null;
        }

        return $this->userRepository->updateUserToken($user);
    }
    public function clearUserToken(string $login): void
    {
        $user = $this->findUserByLogin($login);
        if ($user !== null) {
            $this->userRepository->clearUserToken($user);
        }
    }

}