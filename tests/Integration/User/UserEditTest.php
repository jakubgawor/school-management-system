<?php

namespace App\Tests\Integration\User;

use App\Factory\UserFactory;
use App\Tests\Integration\Helper\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserEditTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    public function self_user_update_via_patch_with_unique_email()
    {
        $repository = UserFactory::repository();
        $user = UserFactory::createOne();

        $this->browser()
            ->actingAs($user)
            ->patch('/api/users/' . $user->getId(), [
                'json' => [
                    'email' => 'new_email@example.com'
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json'
                ]
            ])
            ->assertStatus(200);
        $this->assertNotEmpty($repository->findOneBy(['email' => 'new_email@example.com']));
    }

    /** @test */
    public function self_user_update_via_patch_with_email_already_in_use()
    {
        $user = UserFactory::createOne();

        $this->browser()
            ->actingAs($user)
            ->patch('/api/users/' . $user->getId(), [
                'json' => [
                    'email' => UserFactory::createOne()->getEmail(),
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json'
                ]
            ])
            ->assertStatus(422);
    }

    /** @test */
    public function unauthorized_patch_on_another_user()
    {
        $user1 = UserFactory::createOne();
        $user2 = UserFactory::createOne();

        $this->browser()
            ->actingAs($user1)
            ->patch('/api/users/' . $user2->getId(), [
                'json' => [
                    'email' => 'new_email@example.com'
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json'
                ]
            ])
            ->assertStatus(403);
    }

    /** @test */
    public function patch_by_anonymous_user()
    {
        $user = UserFactory::createOne();

        $this->browser()
            ->patch('/api/users/' . $user->getId(), [
                'json' => [
                    'email' => 'new_email@example.com'
                ],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json'
                ]
            ])
            ->assertStatus(401);
    }
}