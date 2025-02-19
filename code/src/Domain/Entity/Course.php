<?php

namespace App\Domain\Entity;


use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name: 'course')]

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class Course implements EntityInterface
{
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]

    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $title;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: 'Lesson')]
    private Collection $lessons;

    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'teachCourses')]
    #[ORM\JoinColumn(name: 'teacher_id', referencedColumnName: 'id')]
    private ?User $teacher;


    /*
    #[ORM\ManyToMany(targetEntity: 'User', mappedBy: 'studyCourses')]
    private Collection $users;
*/
    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\Column(name: 'deleted_at', type: 'datetime', nullable: true)]
    private DateTime $deletedAt;


    public function __construct()
    {
       $this->lessons = new ArrayCollection();
      // $this->users = new ArrayCollection();
    }
    public function getId(): int
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
    public function getLessons(): Collection
    {
        return $this->lessons;
    }
    public function addLesson(Lesson $lesson): void
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lesson->add($lesson);
            $lesson->setCourse($this);
        }
    }

    public function addUser(User $user): void
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addStudyCourse($this);
        }
    }
    public function deleteUser(User $user): void
    {
        $this->users->removeElement($user);
    }
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @return User
     */
    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    /**
     * @param User $teacher
     */
    public function setTeacher(User $teacher): void
    {
        $this->teacher = $teacher;
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

    /**
     * @param DateTime $deletedAt
     */
    public function setDeletedAt(): void
    {
        $this->deletedAt =  new DateTime();
    }

    /**
     * @return DateTime
     */
    public function getDeletedAt(): DateTime
    {
        return $this->deletedAt;
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
            'title' => $this->getTitle(),
            'showUrl' => '/admin2/course/'.$this->getId(),
           // 'teacher' => isset($this->teacher)? $this->teacher->getFullName() : null,
           // 'lessons' => array_map(static fn(Lesson $lesson) => $lesson->toArray(), $this->lessons->toArray()),
          //  'users' => array_map(static fn(User $user) => $user->toArray(), $this->users->toArray()),
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}