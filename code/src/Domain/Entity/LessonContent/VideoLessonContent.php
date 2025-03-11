<?php
namespace App\Domain\Entity\LessonContent;

use App\Domain\Entity\Lesson;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "video_lesson_content")]
class VideoLessonContent extends LessonContent
{
    #[ORM\Column(type: "string")]
    private string $videoUrl;

    public function __construct(Lesson $lesson, string $videoUrl)
    {
        $this->lesson = $lesson;
        $this->videoUrl = $videoUrl;
    }

    public function getType(): string
    {
        return self::TYPE_VIDEO;
    }

    public function getContent(): ?string
    {
        return $this->videoUrl;
    }
}
