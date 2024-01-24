<?php

namespace App\Tests\Unit\Validator;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Validator\ResetPasswordEmail;
use App\Validator\ResetPasswordEmailValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class ResetPasswordEmailValidatorTest extends TestCase
{
    private UserRepository $userRepository;
    private ResetPasswordEmailValidator $validator;
    private ExecutionContextInterface $context;

    public function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);

        $this->validator = new ResetPasswordEmailValidator($this->userRepository);
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator->initialize($this->context);
    }

    /** @test */
    public function validate_with_existing_user()
    {
        $constraint = new ResetPasswordEmail();

        $this->userRepository->method('findOneBy')->willReturn(new User());

        $this->context->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate('existing@example.com', $constraint);
    }

    /** @test */
    public function validate_not_existing_user()
    {
        $constraint = new ResetPasswordEmail();

        $this->userRepository->method('findOneBy')->willReturn(null);

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->notExistingUser)
            ->willReturn($this->createMock(ConstraintViolationBuilderInterface::class));

        $this->validator->validate('non-existing@example.com', $constraint);
    }

}