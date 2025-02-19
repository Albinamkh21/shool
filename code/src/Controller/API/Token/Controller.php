<?php

namespace App\Controller\API\Token;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class Controller
{
    public function __construct(private readonly Manager $manager) {
    }

    #[Route(path: 'api/token', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        return new JsonResponse(['token' => $this->manager->getToken($request)]);

    }
    #[Route(path: 'api/refresh-token', methods: ['POST'])]
    public function refresh(Request $request): Response
    {
        return new JsonResponse(['token' => $this->manager->refreshToken($this->getUser())]);
    }
}