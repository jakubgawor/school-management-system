<?php

namespace App\Tests\Functional\User;

use App\Factory\UserVerificationTokenFactory;
use App\Tests\Functional\Helper\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserVerificationTokenTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    public function email_verification_via_token()
    {
        $token = UserVerificationTokenFactory::createOne();

        $this->browser()
            ->post('/api/account/confirm', [
                'json' => [
                    'token' => $token->getToken(),
                ]
            ])
            ->assertStatus(201);
    }

    /** @test */
    public function not_existing_token()
    {
        $this->browser()
            ->post('/api/account/confirm', [
                'json' => [
                    'token' => 'token',
                ]
            ])
            ->assertStatus(422);
    }

    /** @test */
    public function token_expired()
    {
        $token = UserVerificationTokenFactory::createOne([
            'expiresAt' => new \DateTimeImmutable('-3 minutes'),
        ]);

        $this->browser()
            ->post('/api/account/confirm', [
                'json' => [
                    'token' => $token->getToken(),
                ]
            ])
            ->assertStatus(422);
    }

}