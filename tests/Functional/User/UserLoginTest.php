<?php

namespace App\Tests\Functional\User;

use App\Factory\UserFactory;
use App\Tests\Functional\Helper\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserLoginTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    public function jwt_token_generation_with_valid_credentials()
    {
        $user = UserFactory::createOne();

        $this->browser()
            ->post('/api/login_check', [
                'json' => [
                    'email' => $user->getEmail(),
                    'password' => 'password',
                ],
            ])
            ->assertStatus(200)
            ->assertContains('token');
    }

    /** @test */
    public function jwt_token_generation_with_invalid_credentials()
    {
        $this->browser()
            ->post('/api/login_check', [
                'json' => [
                    'email' => 'invalid_email@email.com',
                    'password' => 'pass',
                ],
            ])
            ->assertStatus(401);
    }

    /** @test */
    public function user_login_with_jwt_token()
    {
        $user = UserFactory::createOne();

        $response = $this->browser()
            ->post('/api/login_check', [
                'json' => [
                    'email' => $user->getEmail(),
                    'password' => 'password',
                ]
            ]);

        $jwtToken = json_decode($response->json(), true)['token'];

        $this->browser()
            ->get('/api/users', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $jwtToken,
                ],
            ])
            ->assertAuthenticated($user);
    }
}