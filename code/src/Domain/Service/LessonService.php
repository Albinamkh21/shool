<?php

namespace App\Domain\Service;


use App\Domain\DTO\LessonInputDTO;
use App\Domain\DTO\LessonOutputDTO;
use App\Domain\Entity\Course;
use App\Domain\Entity\Lesson;
use App\Domain\Entity\LessonContent\TextLessonContent;
use App\Domain\Entity\LessonContent\VideoLessonContent;
use App\Infrastructure\Repository\CourseRepository;
use App\Infrastructure\Repository\LessonContentRepository;
use App\Infrastructure\Repository\LessonRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;


class LessonService
{
    public function __construct(
        private readonly LessonRepository $lessonRepository,
        private readonly CourseRepository $courseRepository,
        private readonly LessonContentRepository $contentRepository,
        private  readonly ValidatorInterface $validator
    )
    {
    }
    //todo
    //add content to existing lesson, remove content
    //factory to create different contents
    public function create(LessonInputDTO $lessonDTO): Lesson
    {

        // 🛑 Валидация входных данных
        $errors = $this->validator->validate($lessonDTO);
        if (count($errors) > 0) {
            throw new ValidationFailedException($lessonDTO, $errors);
        }
        $lesson = new Lesson();
        $lesson->setTitle($lessonDTO->title);
        $lesson->setOrder($lessonDTO->order);

        $course = $this->courseRepository->find($lessonDTO->courseId);
        if (!($course instanceof Course)) {
            throw new \Exception("Course not found");
        }
        $lesson->setCourse($course);
        $lesson->setCreatedAt();
        $lesson->setUpdatedAt();
        $this->lessonRepository->create($lesson);


        foreach ($lessonDTO->contents as $key => $content) {


            if($key == 'text') {
                $lessonContent = new TextLessonContent($lesson, $content);

            }
            if($key == 'video') {
                $lessonContent = new VideoLessonContent($lesson, $content);

            }

            $lesson->addContent($lessonContent);
            $this->contentRepository->create($lessonContent);
        }
        return $lesson;
        //return new LessonOutputDTO($lesson->getId(), $lesson->getTitle(), $lesson->getDescription());
    }
    public function removeById(int $lessonId): bool
    {
        $lesson = $this->lessonRepository->find($lessonId);
        if ($lesson instanceof Lesson) {
            $this->lessonRepository->remove($lesson);
            return true;
        }

        return false;
    }
    public function findLessonById(int $id): ?Lesson
    {
        return $this->lessonRepository->find($id);
    }

    /**
     * @return Course[]
     */
    public function findAll(): array
    {
        return $this->lessonRepository->findAll();
    }

    public function updateLessonTitle(int $lessonId, string $title): ?Lesson
    {
        $lesson = $this->lessonRepository->find($lessonId);
        if (!($lesson instanceof Lesson)) {
            return null;
        }
        $this->lessonRepository->updateTitle($lesson, $title);

        return $lesson;
    }
    public function updateLesson(int $lessonId, LessonInputDTO $lessonDTO): ?Lesson
    {

        $errors = $this->validator->validate($lessonDTO);
        if (count($errors) > 0) {
            throw new ValidationFailedException($lessonDTO, $errors);
        }
        $lesson = $this->lessonRepository->find($lessonId);
        if (!($lesson instanceof Lesson)) {
          //  throw new \Exception("Lesson not found");
            return  null;
        }

        $course = $this->courseRepository->find($lessonDTO->courseId);
        if (!($course instanceof Course)) {
           // throw new \Exception("Course not found");
            return  null;
        }
        $lesson->setTitle($lessonDTO->title);
        $lesson->setOrder($lessonDTO->order);
        $lesson->setCourse($course);
        $lesson->setCreatedAt();
        $lesson->setUpdatedAt();
        $this->lessonRepository->update($lesson);

        return $lesson;
    }
}