<?php
/*
Author
*/

namespace UnitTests\Domain\Service;

use App\Domain\DTO\CourseInputDTO;
use App\Domain\Entity\Course;
use App\Infrastructure\Repository\CourseRepository;
use App\Domain\Service\CourseService;
use App\Infrastructure\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class CourseServiceTest extends TestCase
{
    protected $userRepository;
    //protected $courseService;

    protected function setUp(): void
    {
        // Мокаем репозиторий user
        $this->userRepository = $this->createMock(UserRepository::class);
    }
    public function testCreateCourseSuccessfully()
    {
        $courseData = new CourseInputDTO(
            'Test Course',
            'Test Description',
        );

        // Мокаем репозиторий курса
        $courseRepository = $this->createMock(CourseRepository::class);
        $courseRepository->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(Course::class))
            ->willReturnCallback(function (Course $course) {
                // Имитируем присвоение id при сохранении
                $course->setId(1);
                return 1;
            });

        // Инициализируем сервис с замоканным репозиторием
        $courseService = new CourseService($courseRepository,$this->userRepository);

        // Вызываем метод создания курса
        $course = $courseService->create($courseData);

        // Проверяем, что возвращается корректный объект
        $this->assertInstanceOf(Course::class, $course);
        $this->assertEquals(1, $course->getId());
        $this->assertEquals('Test Course', $course->getTitle());
       // $this->assertEquals('Test Description', $course->getDescription());
    }

    public function testUpdateCourseTitle()
    {
        // Исходный курс
        $existingCourse = new Course();
        $existingCourse->setId(1);
        $existingCourse->setTitle('Old Title');
       // $existingCourse->setDescription('Old Description');

        $courseData = new CourseInputDTO(
            'Updated Title',
            'Updated Description',
        );
        $newTitle  = "Updated Title";
        $courseRepository = $this->createMock(CourseRepository::class);
        // Метод find должен вернуть существующий курс
        $courseRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($existingCourse);

        // Метод update (или save) вызывается для обновлённого курса
        $courseRepository->expects($this->once())
            ->method('updateTitle')
            ->with($this->isInstanceOf(Course::class))
            ->willReturnCallback(function(Course $course) {
               $course->setTitle("Updated Title");

            });

        $courseService = new CourseService($courseRepository, $this->userRepository);
        $updatedCourse = $courseService->updateCourseTitle(1, $newTitle);

        $this->assertInstanceOf(Course::class, $updatedCourse);
        $this->assertEquals('Updated Title', $updatedCourse->getTitle());
      //  $this->assertEquals('Updated Description', $updatedCourse->getDescription());
    }


    public function testRemoveCourse()
    {
        $existingCourse = new Course();
        $existingCourse->setId(1);

        $courseRepository = $this->createMock(CourseRepository::class);
        // Метод find возвращает курс для удаления
        $courseRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($existingCourse);
        // Метод delete вызывается и возвращает true
        $courseRepository->expects($this->once())
            ->method('remove')
            ->with($existingCourse)
            ->willReturn(true);

        $courseService = new CourseService($courseRepository, $this->userRepository);
        $result = $courseService->removeById(1);

        $this->assertTrue($result);
    }

    public function testGetCourses()
    {
        $course1 = new Course();
        $course1->setId(1);
        $course1->setTitle('Course 1');
       // $course1->setDescription('Description 1');

        $course2 = new Course();
        $course2->setId(2);
        $course2->setTitle('Course 2');
        //$course2->setDescription('Description 2');

        $allCourses = [$course1, $course2];

        $courseRepository = $this->createMock(CourseRepository::class);
        $courseRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($allCourses);

        $courseService = new CourseService($courseRepository,$this->userRepository);
        $courses = $courseService->findAll();

        $this->assertCount(2, $courses);
        $this->assertEquals('Course 1', $courses[0]->getTitle());
        $this->assertEquals('Course 2', $courses[1]->getTitle());
    }

/*
    public function testGetCourseByIdFound()
    {
        $existingCourse = new Course();
        $existingCourse->setId(1);
        $existingCourse->setTitle('Test Course');
        $existingCourse->setDescription('Test Description');

        $courseRepository = $this->createMock(CourseRepository::class);
        $courseRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($existingCourse);

        $courseService = new CourseService($courseRepository);
        $course = $courseService->getCourseById(1);

        $this->assertInstanceOf(Course::class, $course);
        $this->assertEquals(1, $course->getId());
    }

    public function testGetCourseByIdNotFound()
    {
        $courseRepository = $this->createMock(CourseRepository::class);
        $courseRepository->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $courseService = new CourseService($courseRepository);
        $course = $courseService->getCourseById(999);

        $this->assertNull($course);
    }



*/
}
