<?php

namespace App\Domain\Entity;

use App\Repository\LessonRevisionRepository;

use DateTime;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name: 'lesson_revision')]
#[ORM\Index(columns: ['lesson_id'], name: 'inx__lesson_revision__lesson_id')]
#[ORM\Index(columns: ['title'], name: 'inx__lesson_revision__title')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class LessonRevision
{
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $title;


    #[ORM\ManyToOne(targetEntity: 'Lesson', inversedBy: 'revisions')]
    #[ORM\JoinColumn(name: 'lesson_id', referencedColumnName: 'id')]
    private ?Lesson $lesson;


    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;


    public function __construct()
    {

    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getLesson(): Lesson
    {
        return $this->lesson;
    }

    public function setLesson(Lesson $lesson): void
    {
        $this->lesson = $lesson;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): void {

        $this->createdAt = DateTime::createFromFormat('U', (string)time());
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): void {
        $this->updatedAt = DateTime::createFromFormat('U', (string)time());
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'lesson' => isset( $this->lesson) ? $this->lesson->getTitle() : null ,
            'title' => $this->getTitle(),
            'createdAt' => isset($this->createdAt) ? $this->createdAt->format('Y-m-d H:i:s') : '',

        ];
    }
    public function toArrayShort(): array
    {
        return [
            'title' => $this->getTitle(),
            'lesson' => isset( $this->lesson) ? $this->lesson->getTitle() : null ,

        ];
    }

}