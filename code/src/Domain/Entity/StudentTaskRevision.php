<?php

namespace App\Domain\Entity;

use App\Repository\StudentTaskRevisionRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name: 'student_task_revision')]
#[ORM\Index(columns: ['user_id'], name: 'inx__student_task_revision__user_id')]
#[ORM\Index(columns: ['task_id'], name: 'inx__student_task_revision__task_revision_id')]
#[ORM\Index(columns: ['task_id', 'revision_id'], name: 'inx__student_task_revision__task_id__revision_id')]

#[ORM\Entity(repositoryClass: StudentTaskRevisionRepository::class)]
#[ORM\HasLifecycleCallbacks]

class StudentTaskRevision
{
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;



    #[ORM\ManyToOne(targetEntity: 'User')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: 'Task')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id')]
    private Task $task;

    #[ORM\ManyToOne(targetEntity: 'TaskRevision')]
    #[ORM\JoinColumn(name: 'revision_id', referencedColumnName: 'id')]
    private TaskRevision $taskRevision;



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



    public function getTask(): Task
    {
        return $this->task;
    }

    public function setTask(Task $task): void
    {
        $this->task = $task;
    }

    public function getTaskRevision(): TaskRevision
    {
        return $this->taskRevision;
    }

    public function setTaskRevision(TaskRevision $taskRevision): void
    {
        $this->taskRevision = $taskRevision;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): void {
        $this->createdAt = new DateTime();
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): void {
        $this->updatedAt = new DateTime();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'student' =>$this->student->getName(),
            'task' =>$this->task->getTitle(),
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}