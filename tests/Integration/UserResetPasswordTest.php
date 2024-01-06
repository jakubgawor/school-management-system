<?php

namespace App\Tests\Integration;

use App\Factory\UserFactory;
use App\Message\TokenNotification;
use App\Tests\Functional\Helper\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;
use Zenstruck\Messenger\Test\Transport\TestTransport;

class UserResetPasswordTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;
    use InteractsWithMessenger;

    protected function setUp(): void
    {
        TestTransport::resetAll();
    }

    /** @test */
    public function user_reset_password()
    {
        $user = UserFactory::createOne([
            'password' => 'old_password'
        ]);

        $this->browser()
            ->post('/api/account/reset-password', [
                'json' => [
                    'email' => $user->getEmail(),
                ]
            ])->assertStatus(201);

        $token = $user->getUserVerificationToken()->getToken();

        $this->transport('async')->queue()->assertContains(TokenNotification::class, 1);
        $this->assertSame(
            $token,
            $this->transport('async')->dispatched()->messages()[0]->getContent()
        );

        $this->browser()
            ->post('/api/account/reset-password/confirm', [
                'json' => [
                    'token' => $token,
                    'password' => 'new_password',
                ]
            ])->assertStatus(204)
            ->post('/api/login_check', [
                'json' => [
                    'email' => $user->getEmail(),
                    'password' => 'new_password',
                ]
            ])->assertStatus(200);

    }

}