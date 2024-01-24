<?php

namespace App\Tests\Unit\Validator;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Validator\UniqueEmail;
use App\Validator\UniqueEmailValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class UniqueEmailValidatorTest extends TestCase
{
    private UserRepository $userRepository;
    private UniqueEmailValidator $validator;
    private ExecutionContextInterface $context;

    public function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);

        $this->validator = new UniqueEmailValidator($this->userRepository);
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator->initialize($this->context);
    }

    /** @test */
    public function validate_with_not_unique_email()
    {
        $constraint = new UniqueEmail();

        $this->userRepository->method('findOneBy')->willReturn(new User());

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($this->createMock(ConstraintViolationBuilderInterface::class));

        $this->validator->validate('not-unique@example.com', $constraint);
    }

    /** @test */
    public function validate_with_unique_email()
    {
        $constraint = new UniqueEmail();

        $this->userRepository->method('findOneBy')->willReturn(null);

        $this->context->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate('non-existing@example.com', $constraint);
    }
}