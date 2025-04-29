<?php

namespace App\Infrastructure\Symfony\RequestResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class DtoResolver implements ValueResolverInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $dtoClass = $argument->getType();

        if (!class_exists($dtoClass)) {
            return [];
        }

        // Десериализуем JSON в нужный DTO-класс
        $dto = $this->serializer->denormalize(
            $request->request->all(),
            $dtoClass,

        );

/*
        // Валидация DTO через Symfony Validator
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            throw new ValidationFailedException($dto, $errors);
        }
*/
        yield $dto;
    }
}
