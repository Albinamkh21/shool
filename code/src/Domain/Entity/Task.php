<?php

namespace App\Domain\Entity;

use App\Repository\TaskRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;


#[ORM\Table(name: 'task')]
#[ORM\Index(columns: ['lesson_id'], name: 'inx__task__lesson_id')]
#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\HasLifecycleCallbacks]


class Task
{
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;


    #[ORM\ManyToOne(targetEntity: 'Lesson', inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: 'lesson_id', referencedColumnName: 'id')]
    private ?Lesson $lesson;

    #[ORM\OneToOne(targetEntity: 'TaskRevision')]
    #[ORM\JoinColumn(name: 'current_revision_id', referencedColumnName: 'id')]
    private TaskRevision $currentRevision;

/*
    #[ORM\OneToOne(targetEntity: 'StudentTaskRevision')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id')]

//    #[ORM\ManyToMany(targetEntity: 'StudentTaskRevision', mappedBy: 'task')]
    private StudentTaskRevision $userRevision;
*/

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: 'TaskRevision')]
    private Collection $revisions;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;


    public function __construct()
    {
        $this->revisions = new ArrayCollection();
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
        return $this->currentRevision->getTitle();
    }

    public function setTitle(string $title): void
    {
        $this->currentRevision->setTitle($title);
    }

    public function getText(): string
    {
        return $this->currentRevision->getText();
    }

    public function setText(string $text): void
    {
        $this->currentRevision->setText($text);
    }

    public function getLesson(): Lesson
    {
        return $this->lesson;
    }

    public function setLesson(Lesson $lesson): void
    {
        $this->lesson = $lesson;
    }

    public function getRevisions(): Collection
    {
        return $this->revisions;
    }

    public function addRevisions(TaskRevision $revision): void
    {
        if (!$this->revisions->contains($revision)) {
            $this->revisions->add($revision);
            $revision->setTask($this);
        }
    }

    /**
     * @return TaskRevision
     */
    public function getCurrentRevision(): TaskRevision
    {
        return $this->currentRevision;
    }

    /**
     * @param TaskRevision $currentRevision
     */
    public function setCurrentRevision(TaskRevision $currentRevision): void
    {
        $this->currentRevision = $currentRevision;
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
            'createdAt' => isset($this->createdAt) ? $this->createdAt->format('Y-m-d H:i:s') : ''

        ];
    }
    public function toArrayShort(): array
    {
        return [
            'title' => $this->getTitle(),
            'text' => $this->getText(),
            'lesson' => isset( $this->lesson) ? $this->lesson->getTitle() : null

        ];
    }

}