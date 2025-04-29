<?php
namespace UnitTests\Domain\Service;

use App\Domain\Entity\User;
use App\Domain\Entity\Course;
use App\Domain\Entity\Payment;
use App\Infrastructure\Repository\CourseRepository;
use App\Domain\Service\CourseService;
use App\Infrastructure\Repository\UserRepository;
use PHPUnit\Framework\TestCase;
use App\Domain\Service\PaymentService;
use App\Infrastructure\Repository\PaymentRepository;
use App\Infrastructure\Repository\InstallmentPaymentRepository;

class PaymentServiceTest extends TestCase
{
    private PaymentService $paymentService;
    private $paymentRepository;
    private $installmentPaymentRepository;

    protected function setUp(): void
    {
        $this->paymentRepository = $this->createMock(PaymentRepository::class);
        $this->installmentPaymentRepository = $this->createMock(InstallmentPaymentRepository::class);
        $this->paymentService = new PaymentService($this->paymentRepository, $this->installmentPaymentRepository);
    }

    public function testSavePaymentSuccess()
    {
        $user = $this->createMock(User::class);
        $course = $this->createMock(Course::class);
        $payment = $this->createMock(Payment::class);

        $this->paymentRepository->expects($this->once())
            ->method('create')
            ->willReturn($payment);

        $result = $this->paymentService->savePayment($user, $course, 100);
        $this->assertTrue($result);
    }

    public function testSavePaymentFailure()
    {
        $user = $this->createMock(User::class);
        $course = $this->createMock(Course::class);


        $this->paymentRepository->expects($this->once())
            ->method('create')
            ->willThrowException(new \Exception("DB error"));

        $result = $this->paymentService->savePayment($user, $course, 100);
        $this->assertFalse($result);
    }

    public function testPayFullPriceSuccess()
    {
        $user = $this->createMock(User::class);
        $course = $this->createMock(Course::class);
        $course->method('getPrice')->willReturn("200");
        $payment = $this->createMock(Payment::class);

        $this->paymentRepository->expects($this->once())
            ->method('create')
            ->willReturn($payment);

        $result = $this->paymentService->payFullPrice($user, $course);
        $this->assertTrue($result);
    }

    public function testPayWithDiscountSuccess()
    {
        $user = $this->createMock(User::class);
        $course = $this->createMock(Course::class);
        $course->method('getPrice')->willReturn("200");
        $payment = $this->createMock(Payment::class);

        $this->paymentRepository->expects($this->once())
            ->method('create')
            ->willReturn($payment);

        $result = $this->paymentService->payWithDiscount($user, $course, 20);
        $this->assertTrue($result);
    }

    public function testPayWithDiscountFailure()
    {
        $user = $this->createMock(User::class);
        $course = $this->createMock(Course::class);
        $course->method('getPrice')->willReturn("200");

        $this->paymentRepository->expects($this->once())
            ->method('create')
            ->willThrowException(new \Exception("DB error"));

        $result = $this->paymentService->payWithDiscount($user, $course, 20);
        $this->assertFalse($result);
    }

    public function testPayInInstallmentsSuccess()
    {
        $user = $this->createMock(User::class);
        $course = $this->createMock(Course::class);
        $payment = $this->createMock(Payment::class);

        $this->paymentRepository->expects($this->once())
            ->method('createPaymentWithInstallments')
            ->willReturn($payment);

        $result = $this->paymentService->payInInstallments($user, $course, 3);
        $this->assertTrue($result);
    }

    public function testPayInInstallmentsFailure()
    {
        $user = $this->createMock(User::class);
        $course = $this->createMock(Course::class);

        $this->paymentRepository->expects($this->once())
            ->method('createPaymentWithInstallments')
            ->willThrowException(new \RuntimeException("DB error"));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('DB error');

        $result = $this->paymentService->payInInstallments($user, $course, 3);
        $this->assertFalse($result);
    }
}
