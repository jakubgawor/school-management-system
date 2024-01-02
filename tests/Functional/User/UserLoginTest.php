<?php

namespace App\Tests\Functional\User;

use App\Factory\UserFactory;
use App\Tests\Functional\Helper\ApiTestCase;

class UserLoginTest extends ApiTestCase
{
    /** @test */
    public function jwt_token_generation()
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
            ->assertAuthenticated();
    }
}