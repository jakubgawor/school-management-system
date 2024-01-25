<?php

namespace App\Tests\Integration\User;

use App\Factory\UserFactory;
use App\Factory\UserVerificationTokenFactory;
use App\Tests\Integration\Helper\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ResetPasswordConfirmationTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    public function token_removing_from_database_after_resetting_password()
    {
        $repository = UserVerificationTokenFactory::repository();
        $tokenFactory = UserVerificationTokenFactory::createOne([
            'user' => UserFactory::createOne([
                'roles' => ['ROLE_USER_EMAIL_VERIFIED'],
                'isVerified' => true,
            ])
        ]);

        $token = $tokenFactory->getToken();

        $this->browser()
            ->post('/api/account/reset-password/confirm', [
                'json' => [
                    'token' => $token,
                    'password' => 'new_password'
                ]
            ])->assertStatus(204);

        $this->assertNull($repository->findOneBy(['token' => $token]));

    }

    /** @test */
    public function login_check_after_resetting_password()
    {
        $user = UserFactory::createOne();
        $token = UserVerificationTokenFactory::createOne(['user' => $user]);

        $this->browser()
            ->post('/api/account/reset-password/confirm', [
                'json' => [
                    'token' => $token->getToken(),
                    'password' => 'new_password'
                ]
            ])->assertStatus(204)
            ->post('/api/login_check', [
                'json' => [
                    'email' => $user->getEmail(),
                    'password' => 'new_password'
                ]
            ])->assertStatus(200);

    }

    /** @test */
    public function expired_token_provided()
    {
        $token = UserVerificationTokenFactory::createOne([
            'expiresAt' => new \DateTimeImmutable('-3 minutes'),
        ]);

        $this->browser()
            ->post('/api/account/reset-password/confirm', [
                'json' => [
                    'token' => $token->getToken(),
                    'password' => 'new_password'
                ]
            ])->assertStatus(422);
    }

    /** @test */
    public function not_existing_token()
    {
        $this->browser()
            ->post('/api/account/reset-password/confirm', [
                'json' => [
                    'token' => bin2hex(random_bytes(32)),
                    'password' => 'new_password'
                ]
            ])->assertStatus(422);
    }

    /** @test */
    public function null_provided()
    {
        $this->browser()
            ->post('/api/account/reset-password/confirm', [
                'json' => [
                    'token' => '',
                    'password' => ''
                ]
            ])->assertStatus(422);
    }

    /** @test */
    public function longer_token_provided()
    {
        $this->browser()
            ->post('/api/account/reset-password/confirm', [
                'json' => [
                    'token' => bin2hex(random_bytes(34)),
                    'password' => 'new_password'
                ]
            ])->assertStatus(422);
    }


}