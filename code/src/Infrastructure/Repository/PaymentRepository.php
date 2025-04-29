<?php

namespace App\Infrastructure\Repository;

/**
 * @extends AbstractRepository<Payment>
 */
use App\Domain\Entity\Payment;
use App\Domain\Entity\User;
use App\Domain\Entity\Course;

class PaymentRepository  extends AbstractRepository
{


    public function create(Payment $payment): Payment
    {
        return $this->store($payment);
    }
    public function createPaymentWithInstallments(
        User $user,
        Course $course,
        int $months,
        InstallmentPaymentRepository $installmentPaymentRepository
    ): Payment
    {
        $this->entityManager->beginTransaction(); // Начало транзакции

        try {

            $payment = new Payment();
            $payment->setUser($user);
            $payment->setCourse($course);
            $payment->setTotalAmount($course->getPrice());
            $payment->setIsInstallment(true);
            $payment->setInstallmentMonths($months);
            $payment->setStatus('pending');

            $this->entityManager->persist($payment);
            $this->entityManager->flush();

            //  Создаем  плат оплаты при  рассрочке
            $startDate = new \DateTime();
            $installmentAmount = round($course->getPrice() / $months, 2);
            for ($i = 1; $i <= $months; $i++) {
                $paymentDate = (clone $startDate)->modify("+{$i} month");
                $installmentPaymentRepository->createInstallment($payment, $installmentAmount, $paymentDate);
            }

            $this->entityManager->commit(); // Фиксируем транзакцию

            return $payment;
        } catch (Exception $e) {
            $this->entityManager->rollback(); // Откат в случае ошибки
            throw new \RuntimeException('Ошибка при создании рассрочки: ' . $e->getMessage());
        }
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

}
