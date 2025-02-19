<?php

/*
Author
*/

namespace App\Controller\API\User;

use App\Domain\Entity\User;
use App\Domain\Service\UserService;
use App\Controller\API\User\Input\CreateUserDTO;
use App\Controller\API\User\Output\CreatedUserDTO;

class UserManager
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function create(CreateUserDTO $createUserDTO): CreatedUserDTO
    {


        $user = $this->userService->create($createUserDTO);

        return new CreatedUserDTO(
            $user->getId(),
            $user->getLogin(),
            $user->getFullName(),
           '',
           // $user->getAvatarLink(),
            $user->getRoles(),
            $user->getCreatedAt()->format('Y-m-d H:i:s'),
            $user->getUpdatedAt()->format('Y-m-d H:i:s'),
            $user->getPhone(),
            $user->getEmail()

        );
    }
    public function deleteUserById(int $userId): bool
    {
        return $this->userService->removeById($userId);
    }
    public function getUserById(int $userId): ?User
    {
        return $this->userService->findUserById($userId);
    }

    /**
     * @return User[]
     */
    public function getAllUsers(): array
    {
        return $this->userService->findAll();
    }

    public function updateUserLogin(int $userId, string $login): bool
    {
        $user = $this->userService->updateUserLogin($userId, $login);

        return $user instanceof User;
    }


}