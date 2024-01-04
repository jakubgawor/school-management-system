<?php

namespace App\Tests\Functional\User;

use App\Factory\UserFactory;
use App\Tests\Functional\Helper\ApiTestCase;
use Zenstruck\Browser\Json;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserRegistrationTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    public function user_creation_with_post_request()
    {
        $this->browser()
            ->post('/api/users', [
                'json' => [
                    'email' => 'email@example.com',
                    'password' => 'password',
                ],
            ])
            ->use(function (Json $json) {
                $json->assertMissing('id');
                $json->assertMissing('password');
            })
            ->assertStatus(201)
            ->post('/api/login_check', [
                'json' => [
                    'email' => 'email@example.com',
                    'password' => 'password'
                ]
            ])
            ->assertStatus(200)
            ->assertContains('token');
    }

    /** @test */
    public function user_creation_when_email_is_already_in_use()
    {
        $user = UserFactory::createOne();

        $this->browser()
            ->post('/api/users', [
                'json' => [
                    'email' => $user->getEmail(),
                    'password' => 'password',
                ],
            ])
            ->assertStatus(422);
    }
}