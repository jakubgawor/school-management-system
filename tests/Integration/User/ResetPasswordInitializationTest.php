<?php

namespace App\Tests\Integration\User;

use App\Factory\UserFactory;
use App\Factory\UserVerificationTokenFactory;
use App\Message\TokenNotification;
use App\Tests\Integration\Helper\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;
use Zenstruck\Messenger\Test\Transport\TestTransport;

class ResetPasswordInitializationTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;
    use InteractsWithMessenger;

    protected function setUp(): void
    {
        TestTransport::resetAll();
    }


    /** @test */
    public function valid_data_provided()
    {
        $user = UserFactory::createOne();

        $this->browser()
            ->post('/api/account/reset-password', [
                'json' => [
                    'email' => $user->getEmail(),
                ]
            ])->assertStatus(201);
    }

    /** @test */
    public function save_token_in_database()
    {
        $user = UserFactory::createOne();
        $repository = UserVerificationTokenFactory::repository();

        $this->browser()
            ->post('/api/account/reset-password', [
                'json' => [
                    'email' => $user->getEmail(),
                ]
            ])->assertStatus(201);

        $token = $repository->findOneBy(['user' => $user]);

        $this->assertNotNull($token);
        $this->assertSame(
            $user->getUserVerificationToken()->getToken(),
            $token->getToken()
        );
    }

    /** @test */
    public function email_is_sent_for_password_reset()
    {
        $user = UserFactory::createOne();

        $this->browser()
            ->post('/api/account/reset-password', [
                'json' => [
                    'email' => $user->getEmail(),
                ]
            ])->assertStatus(201);

        $this->transport('async')->queue()->assertContains(TokenNotification::class, 1);
        $this->assertSame(
            $user->getUserVerificationToken()->getToken(),
            $this->transport('async')->dispatched()->messages()[0]->getContent()
        );

    }

    /** @test */
    public function blank_email_provided()
    {
        $this->browser()
            ->post('/api/account/reset-password', [
                'json' => [
                    'email' => ''
                ]
            ])->assertStatus(422);
    }

    /** @test */
    public function not_existing_email()
    {
        $this->browser()
            ->post('/api/account/reset-password', [
                'json' => [
                    'email' => 'not-existing@example.com'
                ]
            ])->assertStatus(422);
    }

    /** @test */
    public function not_valid_email()
    {
        $this->browser()
            ->post('/api/account/reset-password', [
                'json' => [
                    'email' => 'not-valid'
                ]
            ])->assertStatus(422);
    }
}