<?php
/*
Author
*/

namespace UnitTests\Domain\Service;

use App\Domain\DTO\CreateUserDTO;
use App\Domain\Service\UserService;
use App\Infrastructure\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use PHPUnit\Framework\TestCase;
use UnitTests\Factory\UserFactory;
use App\Domain\Entity\User;

class UserServiceTest extends TestCase
{
    protected $userRepository;
    protected $userService;
    protected  UserPasswordHasherInterface $userPasswordHasher;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->userPasswordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $this->userService = new UserService($this->userRepository, $this->userPasswordHasher);
    }

    // Положительный кейс: успешное создание пользователя
    public function testAddUserSuccess()
    {
        $userData =  new CreateUserDTO(
            'john_doe',
            'John Doe',
            'john@example.com',
            '1234567890',
            'secret',
            25,
            true,
            ['ROLE_USER'],
        );

        // Ожидаем, что при сохранении объект будет получен и ему установят ID
        $this->userRepository->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(User::class))
            ->willReturnCallback(function (User $user) {
                $user->setId(1);
                return $user;
            });

        $user = $this->userService->create($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(1, $user->getId());
        $this->assertEquals('john_doe', $user->getLogin());
        $this->assertEquals('John Doe', $user->getFullName());
        $this->assertEquals('john@example.com', $user->getEmail());
        $this->assertEquals('1234567890', $user->getPhone());
        $this->assertEquals($this->userPasswordHasher->hashPassword($user, 'secret'), $user->getPassword());
        $this->assertEquals(25, $user->getAge());
       // $this->assertTrue($user->getIsActive());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }


    // Положительный кейс: успешное обновление пользователя
    public function testUpdateUserSuccess()
    {
        // Создаем исходного пользователя через фабрику
        $existingUser = UserFactory::create([
            'login'    => 'john_doe',
            'fullName' => 'John Doe',
            'email'    => 'john@example.com',
            'phone'    => '1234567890',
            'password' => 'secret',
            'age'      => 25,
            'isActive' => true,
            'roles'    => ['ROLE_USER'],
        ]);
        $existingUser->setId(1);

        $updateUserDTO = new CreateUserDTO(
            'jane_doe', 'Jane Doe',
            'jane@example.com',
            '0987654321', 'newsecret',
            30, false,['ROLE_ADMIN'],
        );

        $updateUser = UserFactory::createFromDTO($updateUserDTO);


        // Метод find возвращает исходного пользователя
        $this->userRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($existingUser);

        // Метод update вызывается и возвращает обновленный объект
        $this->userRepository->expects($this->once())
            ->method('update')
            ->with($this->isInstanceOf(User::class))
            ->willReturn($updateUser);

        $user = $this->userService->update(1, $updateUserDTO);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('jane_doe', $user->getLogin());
        $this->assertEquals('Jane Doe', $user->getFullName());
        $this->assertEquals('jane@example.com', $user->getEmail());
        $this->assertEquals('0987654321', $user->getPhone());
        $this->assertEquals($this->userPasswordHasher->hashPassword($user, 'newsecret'), $user->getPassword());
        $this->assertEquals(30, $user->getAge());
      //  $this->assertFalse($user->getIsActive());
        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());

    }

    public function testUpdateLoginUserSuccess()
    {
        // Создаем исходного пользователя через фабрику
        $existingUser = UserFactory::create([
            'login'    => 'john_doe',
            'fullName' => 'John Doe',
            'email'    => 'john@example.com',
            'phone'    => '1234567890',
            'password' => 'secret',
            'age'      => 25,
            'isActive' => true,
            'roles'    => ['ROLE_USER'],
        ]);
        $existingUser->setId(1);

        $updateData = [
            'login'    => 'jane_doe',
            'fullName' => 'Jane Doe',
            'email'    => 'jane@example.com',
            'phone'    => '0987654321',
            'password' => 'newsecret',
            'age'      => 30,
            'isActive' => false,
            'roles'    => ['ROLE_ADMIN'],
        ];

        // Метод find возвращает исходного пользователя
        $this->userRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($existingUser);

        // Метод update вызывается и возвращает обновленный объект
        $this->userRepository->expects($this->once())
            ->method('updateLogin')
            ->with($this->isInstanceOf(User::class),  $updateData['login'] )
            ->willReturnCallback(function (User $user) {
                $user->setLogin('jane_doe');
                return $user;
            });

        $user = $this->userService->updateUserLogin(1, $updateData['login']);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('jane_doe', $user->getLogin());
        /*  $this->assertEquals('Jane Doe', $user->getFullName());
          $this->assertEquals('jane@example.com', $user->getEmail());
          $this->assertEquals('0987654321', $user->getPhone());
          $this->assertEquals('newsecret', $user->getPassword());
          $this->assertEquals(30, $user->getAge());
          $this->assertFalse($user->getIsActive());
          $this->assertEquals(['ROLE_ADMIN'], $user->getRoles());
        */
    }
    // Негативный кейс: попытка обновить несуществующего пользователя
    public function testEditUserNotFound()
    {

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not found');

        $this->userService->updateUserLogin(999, 'login');
    }


    // Положительный кейс: успешное удаление пользователя
    public function testRemoveUserSuccess()
    {
        $existingUser = UserFactory::create();
        $existingUser->setId(1);

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($existingUser);
        $this->userRepository->expects($this->once())
            ->method('remove')
            ->with($existingUser);
         //   ->willReturn(true);

        $result = $this->userService->removeById(1);
        $this->assertTrue($result);
    }

    // Негативный кейс: попытка удалить несуществующего пользователя
    public function testRemoveUserNotFound()
    {
        $this->userRepository->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $result = $this->userService->removeById(999);
        $this->assertFalse($result);
    }

    // Положительный кейс: получение пользователя по ID
    public function testGetUserByIdFound()
    {
        $existingUser = UserFactory::create([
            'login'    => 'john_doe',
            'fullName' => 'John Doe',
            'email'    => 'john@example.com',
            'phone'    => '1234567890',
            'password' => 'secret',
            'age'      => 25,
            'isActive' => true,
            'roles'    => ['ROLE_USER'],
        ]);
        $existingUser->setId(1);

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($existingUser);

        $user = $this->userService->findUserById(1);
      //  $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(1, $user->getId());
    }

    // Негативный кейс: пользователь не найден по ID
    public function testGetUserByIdNotFound()
    {
        $this->userRepository->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $user = $this->userService->findUserById(999);
        $this->assertNull($user);
    }

    // Положительный кейс: получение списка всех пользователей
    public function testGetUsers()
    {
        $user1 = UserFactory::create([
            'login'    => 'john_doe',
            'fullName' => 'John Doe',
            'email'    => 'john@example.com',
        ]);
        $user1->setId(1);

        $user2 = UserFactory::create([
            'login'    => 'jane_doe',
            'fullName' => 'Jane Doe',
            'email'    => 'jane@example.com',
        ]);
        $user2->setId(2);

        $allUsers = [$user1, $user2];

        $this->userRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($allUsers);

        $users = $this->userService->findAll();
        $this->assertCount(2, $users);
        $this->assertEquals('john_doe', $users[0]->getLogin());
        $this->assertEquals('jane_doe', $users[1]->getLogin());
    }

}
