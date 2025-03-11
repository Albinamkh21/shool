<?php

namespace App\Domain\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use App\Domain\ValueObject\RoleEnum;

#[ORM\Table(name: '`user`')]
#[ORM\Entity]
class User implements EntityInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 32, nullable: false, unique: true)]
    private string $login;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $password;

    #[ORM\Column(type: 'string', length: 256, nullable: false)]
    private string $fullName;

    #[ORM\Column(type: 'integer', nullable: false)]
    private int $age;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private string $isActive;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private string $email;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private string $phone;

    #[ORM\Column(type: 'json', length: 1024, nullable: false)]
    private array $roles = [];


    #[ORM\ManyToMany(targetEntity: Course::class, inversedBy: 'students')]
    #[ORM\JoinTable(name: 'student_course')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'course_id', referencedColumnName: 'id')]
    private Collection $enrolledCourses;

    #[ORM\OneToMany(mappedBy: 'teacher', targetEntity: Course::class)]
    private Collection $teachCourses;



    #[ORM\Column(type: 'string', length: 32, unique: true, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\Column(name: 'deleted_at', type: 'datetime', nullable: true)]
    private DateTime $deletedAt;



    public function __construct()
    {
        $this->enrolledCourses = new ArrayCollection();
        $this->teachCourses = new ArrayCollection();
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     */
    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    /**
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    /**
     * @return string
     */
    public function getIsActive(): string
    {
        return $this->isActive;
    }

    /**
     * @param string $isActive
     */
    public function setIsActive(string $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function enrollInCourse(Course $course): void
    {
        if (!$this->enrolledCourses->contains($course)) {
            $this->enrolledCourses->add($course);
            $course->addStudent($this);
        }
    }

    public function unenrollFromCourse(Course $course): void
    {
        if ($this->enrolledCourses->contains($course)) {
            $this->enrolledCourses->removeElement($course);
            $course->removeStudent($this);
        }
    }

    public function addTeachingCourse(Course $course): void
    {
        if (!$this->teachCourses->contains($course)) {
            $this->teachCourses->add($course);
            $course->setTeacher($this);
        }
    }

    public function removeTeachingCourse(Course $course): void
    {
        if ($this->teachCourses->contains($course)) {
            $this->teachCourses->removeElement($course);
            $course->removeTeacher();
        }
    }




    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = RoleEnum::ROLE_USER->value;

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function setCreatedAt(): void {
        $this->createdAt = new DateTime();
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    public function setUpdatedAt(): void {
        $this->updatedAt = new DateTime();
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

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->login;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'login' => $this->getLogin(),
            'name' => $this->getFullName(),
            'roles' => $this->getRoles(),
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }

}