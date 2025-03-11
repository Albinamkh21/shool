<?php
/*
Author
*/

namespace unit\Domain\Service;

use App\Domain\DTO\LessonInputDTO;
use App\Domain\Entity\Course;
use App\Domain\Entity\Lesson;
use App\Domain\Entity\LessonContent\TextLessonContent;
use App\Domain\Service\LessonService;
use App\Domain\Service\LessonContentService;
use App\Domain\Entity\LessonContent\LessonContent;
use App\Infrastructure\Repository\CourseRepository;
use App\Infrastructure\Repository\LessonContentRepository;
use App\Infrastructure\Repository\LessonRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use PHPUnit\Framework\TestCase;

class LessonServiceTest extends TestCase
{
    protected LessonRepository $lessonRepository;
    protected LessonService $lessonService;
    private  CourseRepository $courseRepository;
    private  LessonContentRepository $contentRepository;
    private   ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->lessonRepository = $this->createMock(LessonRepository::class);
        $this->contentRepository = $this->createMock(LessonContentRepository::class);
        $this->courseRepository = $this->createMock(CourseRepository::class);
        $this->validator = $this->createMock(ValidatorInterface::class);

        $this->lessonService = new LessonService($this->lessonRepository, $this->courseRepository, $this->contentRepository, $this->validator);

    }

    // Положительный кейс: успешное создание урока
    public function testCreateLessonSuccess(): void
    {
        $course = new Course();
        $course->setTitle("test");
        $course->setId(1);

        //string $title, string $description,  int $courseId, int $order, array $content
        $content = ['text' => 'test content for lesson 1'];
        $lessonDTO = new LessonInputDTO('Lesson test', 'Description', 1, 1, $content);
        $this->lessonRepository->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(Lesson::class))
            ->willReturnCallback(function (Lesson $lesson) {
                // Симулируем присвоение ID при сохранении
                $lesson->setId(1);
                return $lesson;
            });

        $this->courseRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($course);
           /* ->willReturnCallback(function (Course $course) {
                // Симулируем присвоение ID при сохранении
                $course->setId(1);
                return $course;
            });
           */

        $lesson = $this->lessonService->create($lessonDTO);

        $this->assertInstanceOf(Lesson::class, $lesson);
        $this->assertEquals(1, $lesson->getId());
        $this->assertEquals('Lesson test', $lesson->getTitle());
        //$this->assertInstanceOf(LessonContent::class, $lesson->getContent());
       // $this->assertEquals('This is the lesson content', $lesson->getContent()->getContent());
    }

    // Негативный кейс: при создании урока отсутствует title
    public function testCreateLessonMissingTitle(): void
    {
        $this->expectException(\TypeError::class);
      //  $this->expectExceptionMessage("%null given%");

        $lessonDTO = new LessonInputDTO(null, 'Description', 999, 1, []);
        $this->lessonService->create($lessonDTO);
    }

    public function testCreateLessonNotExistingCourse(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Course not found");

        $lessonDTO = new LessonInputDTO('Test', 'Description', 999, 1, []);
        $this->lessonService->create($lessonDTO);
    }



    // Положительный кейс: успешное обновление урока
    public function testUpdateLessonSuccessfully(): void
    {
        $lessonDTO = new LessonInputDTO('Lesson test', 'Description', 1, 1, []);
        $lessonId = 1;
        $lesson = $this->createMock(Lesson::class);
        $course = $this->createMock(Course::class);
/*
        $this->validator->expects($this->once())
            ->method('validate')
            ->with($lessonDTO)
            ->willReturn(new ConstraintViolationList());
*/
        $this->lessonRepository->expects($this->once())
            ->method('find')
            ->with($lessonId)
            ->willReturn($lesson);

        $this->courseRepository->expects($this->once())
            ->method('find')
            ->with($lessonDTO->courseId)
            ->willReturn($course);

        $lesson->expects($this->once())->method('setTitle')->with($lessonDTO->title);
        $lesson->expects($this->once())->method('setOrder')->with($lessonDTO->order);
        $lesson->expects($this->once())->method('setCourse')->with($course);
        $lesson->expects($this->once())->method('setUpdatedAt');

        $this->lessonRepository->expects($this->once())->method('update')->with($lesson);

        $result = $this->lessonService->updateLesson($lessonId, $lessonDTO);
        $this->assertInstanceOf(Lesson::class, $result);
    }

    public function testUpdateLessonNotFound(): void
    {
        $lessonId = 1;
        $lessonDTO = new LessonInputDTO('Lesson test', 'Description', 1, 1, []);

    //  $this->validator->method('validate')->willReturn(new ConstraintViolationList());
        $this->lessonRepository->method('find')->with($lessonId)->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Lesson not found");

        $this->lessonService->updateLesson($lessonId, $lessonDTO);
    }
    public function testUpdateLessonCourseNotFound(): void
    {
        $lessonId = 1;
        $lessonDTO = new LessonInputDTO('Lesson test', 'Description', 10, 1, []);
        $lessonDTO->courseId = 10;

        $lesson = $this->createMock(Lesson::class);

        //$this->validator->method('validate')->willReturn(new ConstraintViolationList());
        $this->lessonRepository->method('find')->with($lessonId)->willReturn($lesson);
        $this->courseRepository->method('find')->with($lessonDTO->courseId)->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Course not found");

        $this->lessonService->updateLesson($lessonId, $lessonDTO);
    }


    // Положительный кейс: успешное удаление урока
    public function testRemoveLessonSuccess(): void
    {
        $existingLesson = new Lesson();
        $existingLesson->setId(1);

        $this->lessonRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($existingLesson);

        $this->lessonRepository->expects($this->once())
            ->method('remove')
            ->with($existingLesson);
            //->willReturn(true);

        $result = $this->lessonService->removeById(1);
        $this->assertTrue($result);
    }

    // Негативный кейс: попытка удалить несуществующий урок
    public function testRemoveLessonNotFound(): void
    {
        $this->lessonRepository->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $result = $this->lessonService->removeById(999);
        $this->assertFalse($result);
    }

    // Положительный кейс: получение урока по ID
    public function testGetLessonByIdFound(): void
    {
        $existingLesson = new Lesson();
        $existingLesson->setId(1);
        $existingLesson->setTitle('Test Lesson');


        $this->lessonRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($existingLesson);

        $lesson = $this->lessonService->findLessonById(1);
        $this->assertInstanceOf(Lesson::class, $lesson);
        $this->assertEquals(1, $lesson->getId());
    }

    // Негативный кейс: урок не найден по ID
    public function testGetLessonByIdNotFound(): void
    {
        $this->lessonRepository->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $lesson = $this->lessonService->findLessonById(999);
        $this->assertNull($lesson);
    }

    // Положительный кейс: получение списка всех уроков
    public function testGetLessons(): void
    {
        $lesson1 = new Lesson();
        $lesson1->setId(1);
        $lesson1->setTitle('Lesson 1');

        $lesson2 = new Lesson();
        $lesson2->setId(2);
        $lesson2->setTitle('Lesson 2');


        $allLessons = [$lesson1, $lesson2];

        $this->lessonRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($allLessons);

        $lessons = $this->lessonService->findAll();
        $this->assertCount(2, $lessons);
        $this->assertEquals('Lesson 1', $lessons[0]->getTitle());
        $this->assertEquals('Lesson 2', $lessons[1]->getTitle());
    }

}