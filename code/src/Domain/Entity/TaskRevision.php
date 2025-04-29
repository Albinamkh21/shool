<?php

namespace App\Domain\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;



#[ORM\Table(name: 'task_revision')]
#[ORM\Index(columns: ['task_id'], name: 'inx__task_revision__task_id')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class TaskRevision
{
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $title;

    #[ORM\Column(type: 'text', length: 512, nullable: false)]
    private string $text;

    #[ORM\ManyToOne(targetEntity: 'Task', inversedBy: 'revisions')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id')]
    private ?Task $task;

    #[ORM\ManyToMany(targetEntity: 'StudentTaskRevision', mappedBy: 'taskRevision')]
    private Collection $userRevisions;

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

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function setTask(Task $task): void
    {
        $this->task = $task;
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
            'task' => isset( $this->task) ? $this->task->getId() : null ,
            'title' => $this->getTitle(),
            'text' => $this->getText(),
            'createdAt' => isset($this->createdAt) ? $this->createdAt->format('Y-m-d H:i:s') : '',

        ];
    }
    public function toArrayShort(): array
    {
        return [
            'title' => $this->getTitle(),
            'text' => $this->getText(),
            'task' => isset( $this->task) ? $this->task->getId() : null ,

        ];
    }

}