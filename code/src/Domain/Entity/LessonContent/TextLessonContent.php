<?php
namespace App\Domain\Entity\LessonContent;

use App\Domain\Entity\Lesson;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "text_lesson_content")]
class TextLessonContent extends LessonContent
{
    #[ORM\Column(type: "text")]
    private string $text;

    public function __construct(Lesson $lesson, string $text)
    {
        $this->lesson = $lesson;
        $this->text = $text;
    }

    public function getType(): string
    {
        return self::TYPE_TEXT;
    }

    public function getContent(): ?string
    {
        return $this->text;
    }
}
