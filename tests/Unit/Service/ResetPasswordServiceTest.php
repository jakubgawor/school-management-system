<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Entity\UserVerificationToken;
use App\Service\ResetPasswordService;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordServiceTest extends TestCase
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private EntityManagerInterface $entityManager;
    private TokenService $tokenService;
    private ResetPasswordService $resetPasswordService;

    protected function setUp(): void
    {
        $this->userPasswordHasher = m::mock(UserPasswordHasherInterface::class);
        $this->entityManager = m::mock(EntityManagerInterface::class);
        $this->tokenService = m::mock(TokenService::class);

        $this->resetPasswordService = new ResetPasswordService(
            $this->userPasswordHasher,
            $this->entityManager,
            $this->tokenService
        );
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function resetPassword_resets_user_password()
    {
        $token = 'test-token';
        $plainPassword = 'new-password';
        $hashedPassword = 'hashed-password';

        $userMock = m::mock(User::class);
        $verificationTokenMock = m::mock(UserVerificationToken::class);

        $this->tokenService
            ->shouldReceive('getUserVerificationTokenEntity')
            ->with($token)
            ->andReturn($verificationTokenMock);

        $verificationTokenMock
            ->shouldReceive('getUser')
            ->andReturn($userMock);

        $userMock
            ->shouldReceive('getUserVerificationToken')
            ->andReturn($verificationTokenMock);

        $verificationTokenMock
            ->shouldReceive('getToken')
            ->andReturn($token);

        $this->userPasswordHasher
            ->shouldReceive('hashPassword')
            ->with($userMock, $plainPassword)
            ->andReturn($hashedPassword);

        $userMock
            ->shouldReceive('setPassword')
            ->with($hashedPassword);

        $this->entityManager
            ->shouldReceive('persist')
            ->with($userMock)
            ->once();

        $this->entityManager
            ->shouldReceive('flush')
            ->once();

        $this->tokenService
            ->shouldReceive('removeToken')
            ->with($token)
            ->once();

        $this->resetPasswordService->resetPassword($token, $plainPassword);
        $this->assertTrue(true);
    }
}