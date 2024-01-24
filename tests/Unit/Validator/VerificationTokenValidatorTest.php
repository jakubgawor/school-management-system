<?php

namespace App\Tests\Unit\Validator;

use App\Entity\UserVerificationToken;
use App\Repository\UserVerificationTokenRepository;
use App\Validator\VerificationToken;
use App\Validator\VerificationTokenValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class VerificationTokenValidatorTest extends TestCase
{
    private UserVerificationTokenRepository $userVerificationTokenRepository;
    private VerificationTokenValidator $validator;
    private ExecutionContextInterface $context;

    public function setUp(): void
    {
        $this->userVerificationTokenRepository = $this->createMock(UserVerificationTokenRepository::class);
        $this->validator = new VerificationTokenValidator($this->userVerificationTokenRepository);

        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator->initialize($this->context);
    }

    /** @test */
    public function not_existing_token()
    {
        $constraint = new VerificationToken();

        $this->userVerificationTokenRepository->method('findOneBy')->willReturn(null);

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->tokenDoesNotExists)
            ->willReturn($this->createMock(ConstraintViolationBuilderInterface::class));

        $this->validator->validate('not-existing-token', $constraint);
    }

    /** @test */
    public function expired_token()
    {
        $constraint = new VerificationToken();
        $expiredToken = $this->createMock(UserVerificationToken::class);
        $expiredToken->method('getExpiresAt')->willReturn(new \DateTimeImmutable('-1 day'));

        $this->userVerificationTokenRepository->method('findOneBy')->willReturn($expiredToken);

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->expiredMessage)
            ->willReturn($this->createMock(ConstraintViolationBuilderInterface::class));

        $this->validator->validate('expired-token', $constraint);

    }

    /** @test */
    public function valid_token()
    {
        $constraint = new VerificationToken();
        $validToken = $this->createMock(UserVerificationToken::class);
        $validToken->method('getExpiresAt')->willReturn(new \DateTimeImmutable('+1 day'));

        $this->userVerificationTokenRepository->method('findOneBy')->willReturn($validToken);

        $this->context->expects($this->never())
            ->method('buildViolation');

        $this->validator->validate('valid-token', $constraint);
    }
}