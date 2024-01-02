<?php

namespace App\Tests\Functional\User;

use App\Factory\UserFactory;
use App\Tests\Functional\Helper\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserDeleteTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    public function user_self_deletion()
    {
        $repository = UserFactory::repository();
        $user = UserFactory::createOne();
        $email = $user->getEmail();

        $this->browser()
            ->actingAs($user)
            ->delete('/api/users/' . $user->getId())
            ->assertStatus(204);

        $this->assertEmpty($repository->findOneBy(['email' => $email]));
    }

    /** @test */
    public function unauthorized_user_deletion()
    {
        $repository = UserFactory::repository();

        $user1 = UserFactory::createOne();
        $user2 = UserFactory::createOne();

        $this->browser()
            ->actingAs($user1)
            ->delete('/api/users/' . $user2->getId())
            ->assertStatus(403);

        $this->assertNotEmpty($repository->findOneBy(['email' => $user2->getEmail()]));
    }

    /** @test */
    public function deletion_by_anonymous_user()
    {
        $user = UserFactory::createOne();

        $this->browser()
            ->delete('/api/users/' . $user->getId())
            ->assertStatus(401);
    }
}