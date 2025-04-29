<?php

namespace App\Domain\Entity;


use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class InstallmentPayment implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Payment::class, inversedBy: 'installmentPayments')]
    #[ORM\JoinColumn(nullable: false)]
    private Payment $payment;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $amount; // Сумма платежа

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dueDate; // Дата, когда должен быть оплачен

    #[ORM\Column(type: 'boolean')]
    private bool $paid = false; // Оплачен или нет

    public function getId(): int { return $this->id; }
    public function getPayment(): Payment { return $this->payment; }
    public function getAmount(): float { return $this->amount; }
    public function getDueDate(): \DateTimeInterface { return $this->dueDate; }
    public function isPaid(): bool { return $this->paid; }

    /**
     * @param Payment $payment
     */
    public function setPayment(Payment $payment): void
    {
        $this->payment = $payment;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @param \DateTimeInterface $dueDate
     */
    public function setDueDate(\DateTimeInterface $dueDate): void
    {
        $this->dueDate = $dueDate;
    }

    /**
     * @param bool $paid
     */
    public function setPaid(bool $paid): void
    {
        $this->paid = $paid;
    }


}
