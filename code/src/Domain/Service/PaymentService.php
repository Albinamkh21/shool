<?php

namespace App\Domain\Service;



use App\Domain\Entity\Course;
use App\Domain\Entity\Payment;
use App\Domain\Entity\User;
use App\Domain\Entity\InstallmentPayment;

use App\Infrastructure\Repository\InstallmentPaymentRepository;
use App\Infrastructure\Repository\PaymentRepository;



class PaymentService
{
    public function __construct(

        private readonly PaymentRepository $paymentRepository,
        private readonly InstallmentPaymentRepository $installmentPaymentRepository
    )
    {
    }


    public function savePayment(User $user, Course $course, $amount): bool
    {

        try {
            $payment = new Payment();
            $payment->setUser($user);
            $payment->setCourse($course);
            $payment->setTotalAmount($amount);

            $this->paymentRepository->create($payment);

            return true;
        } catch (\Exception $e) {
            // Логирование ошибки можно добавить здесь
            return false;
        }

    }

    public function payFullPrice(User $user, Course $course): bool
    {

        return $this->savePayment($user, $course, $course->getPrice());
    }

    public function payWithDiscount(User $user, Course $course, float $discountPercent): bool
    {
        $discountedPrice = $course->getPrice() * (1 - $discountPercent / 100);
        return $this->savePayment($user, $course, $discountedPrice);
    }

    public function payInInstallments(User $user, Course $course, int $installments): bool
    {

        $this->paymentRepository->createPaymentWithInstallments($user, $course, $installments, $this->installmentPaymentRepository);

        return true;

    }


}