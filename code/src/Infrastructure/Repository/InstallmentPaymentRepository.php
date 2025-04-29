<?php

namespace App\Infrastructure\Repository;

/**
 * @extends AbstractRepository<InstallmentPayment>
 */
use App\Domain\Entity\InstallmentPayment;
use App\Domain\Entity\Payment;


class InstallmentPaymentRepository  extends AbstractRepository
{

    public function createInstallment(Payment $payment, float $amount, \DateTime $paymentDate)
    {
        $installment = new InstallmentPayment();
        $installment->setPayment($payment);
        $installment->setAmount($amount);
        $installment->setDueDate($paymentDate);

        $this->entityManager->persist($installment);
        $this->entityManager->flush();
    }

    public function create(InstallmentPayment $installmentPayment): InstallmentPayment
    {
        return $this->store($installmentPayment);
    }
/*
    public function create(Payment $payment): Payment
    {
        return $this->store($payment);
    }

    public function remove(Payment $payment): bool

    {
       return  $this->delete($payment);

    }
    public function find(int $paymentId): ?Payment
    {
        $repository = $this->entityManager->getRepository(Payment::class);

        $payment = $repository->find($paymentId);

        return $payment;
    }


    public function findAll(): array
    {
        return $this->entityManager->getRepository(Payment::class)->findAll();
    }
*/

    public function findUnpaidInstallments(): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.isPaid = false')
            ->getQuery()
            ->getResult();
    }

}
