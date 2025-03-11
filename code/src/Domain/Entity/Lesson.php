<?php

namespace App\Domain\Entity;

use App\Domain\Entity\LessonContent\LessonContent;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Table(name: 'lesson')]
#[ORM\Index(columns: ['course_id', 'id'], name: 'inx__lesson__course_id__id')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]

class Lesson implements EntityInterface
{
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;


    #[ORM\ManyToOne(targetEntity: 'Course', inversedBy: 'lessons')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id')]
    private Course $course;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $title;

    #[ORM\Column(name: 'orderNum', type: 'integer')]
    private int $order;




    #[ORM\OneToMany(mappedBy: "lesson", targetEntity: LessonContent::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    private Collection $contents;

    /*
    #[ORM\OneToMany(mappedBy: 'lesson', targetEntity: 'Task')]
    private Collection $tasks;


    #[ORM\OneToMany(mappedBy: 'lesson', targetEntity: 'LessonRevision')]
    private Collection $revisions;

    #[ORM\OneToOne(targetEntity: 'LessonRevision')]
    #[ORM\JoinColumn(name: 'current_revision_id', referencedColumnName: 'id')]
    private ?LessonRevision $currentRevision;

*/


    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\Column(name: 'deleted_at', type: 'datetime', nullable: true)]
    private DateTime $deletedAt;

    public function __construct()
    {

        $this->contents = new ArrayCollection();
        /*
        $this->tasks = new ArrayCollection();
        $this->revisions = new ArrayCollection();
        $this->currentRevision = null;
        */
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
       // return is_null($this->currentRevision) ? '' : $this->currentRevision->getTitle();
    }

    public function setTitle(string $title): void
    {

        $this->title = $title;
        /*
        if(!is_null($this->currentRevision))
            $this->currentRevision->setTitle($title);
       */
    }

    public function getCourse(): Course
    {
        return $this->course;
    }

    public function setCourse(Course $course): void
    {
        $this->course = $course;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder(int $order): void
    {
        $this->order = $order;
    }

    public function getContents(): Collection
    {
        return $this->contents;
    }

    public function addContent(LessonContent $content): void
    {
        if (!$this->contents->contains($content)) {
            $this->contents->add($content);
        }
    }

    public function removeContent(LessonContent $content): void
    {
        $this->contents->removeElement($content);
    }
/*
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): void
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setLesson($this);
        }
    }

    public function getRevisions(): Collection
    {
        return $this->revisions;
    }

    public function addRevisions(LessonRevision $revision): void
    {
        if (!$this->revisions->contains($revision)) {
            $this->revisions->add($revision);
            $revision->setLesson($this);
        }
    }
*/

    /*
    public function getCurrentRevision(): LessonRevision
    {
        return $this->currentRevision;
    }


    public function setCurrentRevision(LessonRevision $currentRevision): void
    {
        $this->currentRevision = $currentRevision;
    }
*/
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
            'course' => $this->course->getTitle(),
            'title'  => $this->getTitle(),
            'order'  => $this->getOrder(),
            'contents' =>  array_map(static fn(LessonContent $content) => $content->toArray(), $this->getContents()->toArray()),
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s')
        ];
    }
    public function toArrayShort(): array
    {
        return [
            'title'  => $this->getTitle(), //$this->getCurrentRevision()->getTitle(),
            'course' => $this->course->getTitle(),

        ];
    }
}