<?php

namespace App\Tests\Functional;

use App\Factory\UserFactory;
use App\Factory\UserVerificationTokenFactory;
use App\Tests\Integration\Helper\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;
use Zenstruck\Messenger\Test\Transport\TestTransport;

class UserVerificationTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;
    use InteractsWithMessenger;

    protected function setUp(): void
    {
        TestTransport::resetAll();
    }

    /** @test */
    public function user_email_verification()
    {
        $userRepository = UserFactory::repository();
        $tokenRepository = UserVerificationTokenFactory::repository();

        $this->browser()
            ->post('/api/users', [
                'json' => [
                    'email' => 'email@example.com',
                    'password' => 'password',
                ],
            ])
            ->assertStatus(201);

        $verificationToken = $this->transport('async')->dispatched()->messages()[0]->getContent();

        $user = $userRepository->findOneBy(['email' => 'email@example.com']);

        $this->assertFalse($user->isVerified());
        $this->assertFalse(in_array('ROLE_USER_EMAIL_VERIFIED', $user->getRoles()));
        $this->assertSame($tokenRepository->findOneBy(['token' => $verificationToken])->getToken(), $verificationToken);

        $this->browser()
            ->actingAs($user)
            ->post('/api/account/confirm', [
                'json' => [
                    'token' => $verificationToken,
                ]
            ])
            ->assertStatus(201);

        $this->assertTrue($user->isVerified());
        $this->assertTrue(in_array('ROLE_USER_EMAIL_VERIFIED', $user->getRoles()));
        $this->assertEmpty($tokenRepository->findOneBy(['token' => $verificationToken]));

    }
}