<?php

namespace App\Domain\Entity;


use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Payment implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Course::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Course $course;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $totalAmount; // Итоговая сумма с учетом скидки

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $discountApplied = null; // Скидка, если есть

    #[ORM\Column(type: 'boolean')]
    private bool $isInstallment = false; // Флаг рассрочки

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $installmentMonths = null; // Количество месяцев рассрочки

    #[ORM\OneToMany(targetEntity: InstallmentPayment::class, mappedBy: 'payment')]
    private Collection $installmentPayments; // Список платежей при рассрочке

    #[ORM\Column(type: 'string', length: 50)]
    private string $status = 'pending'; // pending, completed, failed

    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool $isFullyPaid = false;
    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\Column(name: 'deleted_at', type: 'datetime', nullable: true)]
    private DateTime $deletedAt;


    public function getId(): int { return $this->id; }
    public function getUser(): User { return $this->user; }
    public function getCourse(): Course { return $this->course; }
    public function getTotalAmount(): float { return $this->totalAmount; }
    public function isInstallment(): bool { return $this->isInstallment; }
    public function getInstallmentMonths(): ?int { return $this->installmentMonths; }
    public function getStatus(): string { return $this->status; }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @param Course $course
     */
    public function setCourse(Course $course): void
    {
        $this->course = $course;
    }

    /**
     * @param float $totalAmount
     */
    public function setTotalAmount(float $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @param float|null $discountApplied
     */
    public function setDiscountApplied(?float $discountApplied): void
    {
        $this->discountApplied = $discountApplied;
    }

    /**
     * @param bool $isInstallment
     */
    public function setIsInstallment(bool $isInstallment): void
    {
        $this->isInstallment = $isInstallment;
    }

    /**
     * @param int|null $installmentMonths
     */
    public function setInstallmentMonths(?int $installmentMonths): void
    {
        $this->installmentMonths = $installmentMonths;
    }

    /**
     * @param Collection $installmentPayments
     */
    public function setInstallmentPayments(Collection $installmentPayments): void
    {
        $this->installmentPayments = $installmentPayments;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function isFullyPaid(): bool
    {
        return $this->isFullyPaid;
    }

    public function markAsFullyPaid(): void
    {
        $this->isFullyPaid = true;
        $this->status = 'completed';
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



}
