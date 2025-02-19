<?php

namespace App\Controller\API\Token;


use App\Application\Security\AuthService;
use App\Controller\Exception\AccessDeniedException;
use App\Controller\Exception\UnauthorizedException;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Service\UserService;

class Manager
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly UserService $userService,)
    {
    }

    /**
     * @throws AccessDeniedException
     * @throws UnauthorizedException
     */
    public function getToken(Request $request): string
    {
        $user = $request->getUser();
        $password = $request->getPassword();

        if (!$user || !$password) {
            throw new UnauthorizedException();
        }
        if (!$this->authService->isCredentialsValid($user, $password)) {
            throw new AccessDeniedException();
        }

        return $this->authService->getToken($user);
    }
    public function refreshToken(UserInterface $user): string
    {

        //  $this->userService->clearUserToken($user->getUserIdentifier());

        //return $this->authService->getToken($user->getUserIdentifier());

        return 'token';
    }
}
