<?php

namespace App\Domain\Service;


use App\Domain\DTO\LessonContentInputDTO;
use App\Domain\Entity\Lesson;
use App\Domain\Entity\LessonContent\LessonContent;
use App\Domain\Entity\LessonContent\TextLessonContent;
use App\Domain\Entity\LessonContent\VideoLessonContent;
use App\Infrastructure\Repository\LessonContentRepository;
use App\Infrastructure\Repository\LessonRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;


class LessonContentService
{
    public function __construct(
        private readonly LessonRepository $lessonRepository,
  //      private readonly CourseRepository $courseRepository,
        private readonly LessonContentRepository $contentRepository,
        private  readonly  ValidatorInterface $validator
    )
    {
    }
    //todo
    //add content to existing lesson, remove content
    //factory to create different contents
    public function addContentToLesson(int $lessonId, LessonContentInputDTO $contentDTO): LessonContent
    {

        // 🛑 Валидация входных данных
        $errors = $this->validator->validate($contentDTO);
        if (count($errors) > 0) {
            throw new ValidationFailedException($contentDTO, $errors);
        }

        $lesson = $this->lessonRepository->find($lessonId);
        if($contentDTO->type == 'text') {
            $lessonContent = new TextLessonContent($lesson, $contentDTO->content);

        }
        if($contentDTO->type == 'video') {
            $lessonContent = new VideoLessonContent($lesson, $contentDTO->content);

        }
        $this->contentRepository->create($lessonContent);

        return $lessonContent;
        //return new LessonOutputDTO($lesson->getId(), $lesson->getTitle(), $lesson->getDescription());
    }
    public function removeContentFromLesson(int $lessonId, int $contentId): bool
    {
        $lesson = $this->lessonRepository->find($lessonId);
        $content = $this->contentRepository->find($contentId);
        if ($content instanceof LessonContent) {
            $this->contentRepository->remove($content);
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
}