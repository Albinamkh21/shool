<?php

namespace App\Controller\API\User;

use App\Controller\API\User\Input\CreateUserDTO;
use App\Domain\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class UserController
{
    public function __construct(private readonly UserManager $manager) {
    }

    #[Route(path: 'api/user', methods: ['POST'])]
    public function create(Request $request): Response
    {


        $login = $request->request->get('login');
        $phone = $request->request->get('phone');
        $email = $request->request->get('email');
        $fullName = $request->request->get('fullName');
        $password = $request->request->get('password');
        $age = $request->request->get('age');
        $isActive = $request->request->get('isActive');
        $roles = $request->request->get('roles');
        $roles = json_decode($roles, true, 512, JSON_THROW_ON_ERROR);
        $createUserDTO = new CreateUserDTO($login, $fullName, $email, $phone, $password, $age, $isActive, $roles);
        $user = $this->manager->create($createUserDTO);
        if ($user === null) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($user);
    }

    #[Route(path: 'api/user', methods: ['DELETE'])]
    public function delete(Request $request): Response
    {
        $userId = $request->query->get('id');
        $result = $this->manager->deleteUserById($userId);
        if ($result) {
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route(path: 'api/user', methods: ['GET'])]
    public function get(Request $request): Response
    {
        $userId = $request->query->get('id');
        if ($userId === null) {
            return new JsonResponse(array_map(static fn (User $user): array => $user->toArray(), $this->manager->getAllUsers()));
        }
        $user = $this->manager->getUserById($userId);

        /** @var User|null $user */
        if ($user instanceof User) {
            return new JsonResponse($user->toArray());
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route(path: 'api/user', methods: ['PATCH'])]
    public function update(Request $request): Response
    {

        $userId = $request->query->get('id');
        $login = $request->query->get('login');
        $result = $this->manager->updateUserLogin($userId, $login);

        if ($result) {
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


}