<?php
namespace App\Domain\Entity\LessonContent;

use App\Domain\Entity\EntityInterface;
use App\Domain\Entity\Lesson;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
#[ORM\Table(name: "lesson_content")]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "type", type: "string")]
#[ORM\DiscriminatorMap([
    "text" => TextLessonContent::class,
    "video" => VideoLessonContent::class
])]
abstract class LessonContent implements LessonContentInterface, EntityInterface
{
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Lesson::class, inversedBy: "contents")]
    protected Lesson $lesson;

    abstract public function getType(): string;
    abstract public function getContent(): ?string;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Lesson
     */
    public function getLesson(): Lesson
    {
        return $this->lesson;
    }

    /**
     * @param Lesson $lesson
     */
    public function setLesson(Lesson $lesson): void
    {
        $this->lesson = $lesson;
    }
    public function toArray(): array
    {
        return [
            'type'  => $this->getType(), //$this->getCurrentRevision()->getTitle(),
            'content' => $this->getContent(),

        ];
    }



}
