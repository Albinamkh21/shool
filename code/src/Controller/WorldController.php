<?php

namespace App\Controller;

use App\Domain\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WorldController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
    )
    {
    }

    #[Route(path: '/world/hello', name: 'app_world_hello')]
    public function hello(): Response
    {
        //$user = $this->userService->create('My user');

        return $this->json("Hello world");
    }
}