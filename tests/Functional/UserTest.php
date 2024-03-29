<?php

namespace App\Tests\Functional;

use App\Factory\UserFactory;
use App\Message\TokenNotification;
use App\Tests\Integration\Helper\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;
use Zenstruck\Messenger\Test\Transport\TestTransport;

class UserTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;
    use InteractsWithMessenger;

    protected function setUp(): void
    {
        TestTransport::resetAll();
    }

    /** @test */
    public function user_creation_integrations()
    {
        $repository = UserFactory::repository();

        $this->browser()
            ->post('/api/users', [
                'json' => [
                    'email' => 'email@example.com',
                    'password' => 'password',
                ],
            ])
            ->assertStatus(201);

        $this->assertNotEmpty($repository->findOneBy(['email' => 'email@example.com']));

        $token = $repository->findOneBy(['email' => 'email@example.com'])->getUserVerificationToken()->getToken();

        $this->transport('async')->queue()->assertContains(TokenNotification::class, 1);
        $this->assertSame(
            $token,
            $this->transport('async')->dispatched()->messages()[0]->getContent()
        );
        $this->assertSame(
            'email@example.com',
            $this->transport('async')->dispatched()->messages()[0]->getSendto()
        );
    }
}