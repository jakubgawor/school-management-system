<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Entity\UserVerificationToken;
use App\Repository\UserVerificationTokenRepository;
use App\Service\TokenService;
use App\Service\VerificationUserMailService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class VerificationUserMailServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private UserVerificationTokenRepository $userVerificationTokenRepository;
    private TokenService $tokenService;
    private VerificationUserMailService $verificationUserMailService;

    protected function setUp(): void
    {
        $this->entityManager = m::mock(EntityManagerInterface::class);
        $this->userVerificationTokenRepository = m::mock(UserVerificationTokenRepository::class);
        $this->tokenService = m::mock(TokenService::class);

        $this->verificationUserMailService = new VerificationUserMailService(
            $this->entityManager,
            $this->userVerificationTokenRepository,
            $this->tokenService
        );
    }

    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function verifyUser_works_correctly()
    {
        $verificationToken = m::mock(UserVerificationToken::class);
        $user = m::mock(User::class);

        $this->userVerificationTokenRepository
            ->shouldReceive('findOneByToken')
            ->with('token')
            ->andReturn($verificationToken);

        $verificationToken
            ->shouldReceive('getUser')
            ->andReturn($user);

        $user->shouldReceive('setIsVerified')->with(true)->once();
        $user->shouldReceive('setRoles')->with(['ROLE_USER_EMAIL_VERIFIED'])->once();

        $this->entityManager->shouldReceive('persist')->with($user)->once();
        $this->entityManager->shouldReceive('flush')->once();

        $this->tokenService->shouldReceive('removeToken')->with('token')->once();

        $this->verificationUserMailService->verifyUser('token');
        $this->assertTrue(true);
    }

}