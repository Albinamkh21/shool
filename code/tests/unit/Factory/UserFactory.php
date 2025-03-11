<?php
/*
Author
*/

namespace UnitTests\Factory;

use App\Domain\DTO\CreateUserDTO;
use App\Domain\Entity\User;
use Faker\Factory as FakerFactory;
class UserFactory
{
    /**
     * Создаёт сущность User на основе объекта CreateUserDTO.
     *
     * @param CreateUserDTO $dto
     * @return User
     */
    public static function createFromDTO(CreateUserDTO $dto): User
    {
        $user = new User();
        $user->setLogin($dto->login);
        $user->setFullName($dto->fullName);
        $user->setEmail($dto->email);
        $user->setPhone($dto->phone);
        $user->setPassword($dto->password);
        $user->setAge($dto->age);
        $user->setIsActive($dto->isActive);
        $user->setRoles($dto->roles);

        return $user;
    }

    /**
     * Создаёт сущность User, заполняя данные с помощью Faker и позволяя переопределить отдельные поля.
     *
     * @param array $overrides Массив переопределения значений полей.
     * @return User
     */
    public static function create(array $overrides = []): User
    {
        $faker = FakerFactory::create();

        $dto = new CreateUserDTO(
            $overrides['login']    ?? $faker->userName,
            $overrides['fullName'] ?? $faker->name,
            $overrides['email']    ?? $faker->email,
            $overrides['phone']    ?? $faker->numerify('##########'),
            $overrides['password'] ?? $faker->password,
            $overrides['age']      ?? $faker->numberBetween(18, 80),
            $overrides['isActive'] ?? true,
            $overrides['roles']    ?? ['ROLE_USER']
        );

            return self::createFromDTO($dto);
    }
}