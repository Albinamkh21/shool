<?php

namespace App\Controller\API\User\Output;

use App\Controller\DTO\OutputDTOInterface;

class CreatedUserDTO implements OutputDTOInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $login,
        public readonly string $fullName,

        public readonly ?string $avatarLink,
        /** @var string[] $roles */
        public readonly array $roles,
        public readonly string $createdAt,
        public readonly string $updatedAt,
        public readonly ?string $phone,
        public readonly ?string $email,
    ) {
    }
}