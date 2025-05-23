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

    #[ORM\Column(type: 'text',  nullable: true)]
    private string $description;



    #[ORM\OneToMany(mappedBy: 'course', targetEntity: 'Lesson')]
    private Collection $lessons;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'enrolledCourses')]
    private Collection $students;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'teachCourses')]
    #[ORM\JoinColumn(name: 'teacher_id', referencedColumnName: 'id', nullable: true)]
    private ?User $teacher = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $price;

    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'course')]
    private Collection $payments;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\Column(name: 'deleted_at', type: 'datetime', nullable: true)]
    private DateTime $deletedAt;


    public function __construct()
    {
       $this->lessons = new ArrayCollection();
       $this->students = new ArrayCollection();
       $this->payments = new ArrayCollection();
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


    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
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

    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(User $student): void
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
        }
    }

    public function removeStudent(User $student): void
    {
        if ($this->students->contains($student)) {
            $this->students->removeElement($student);
        }
    }

    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    public function setTeacher(User $teacher): void
    {
        $this->teacher = $teacher;
    }

    public function removeTeacher(): void
    {
        $this->teacher = null;
    }


    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getPayments(): Collection
    {
        return $this->payments;
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

    public function isFree(): bool
    {
        return $this->price == 0;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->getTitle(),
            'showUrl' => '/admin2/course/'.$this->getId(),
            'teacher' => isset($this->teacher)? $this->teacher->getFullName() : null,
            'lessons' => array_map(static fn(Lesson $lesson) => $lesson->toArray(), $this->lessons->toArray()),
            'students' => array_map(static fn(User $user) => $user->toArray(), $this->students->toArray()),
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}