<?php

namespace App\Tests\Functional;

use App\DataFixtures\UserFixture;
use App\Test\ApiTestCase;

class UserResourceTest extends ApiTestCase
{
    public function testCreateUser(): void
    {
        $this->loadFixtures([UserFixture::class]);

        $client = static::createAuthenticatedClient('admin@admin.com');

        $response = $client->request('POST', '/users', ['json' => [
            'email' => 'john@gmail.com',
            'password' => 'Test123',
        ]]);

        $this->assertResponseStatusCodeSame(201);

        $this->assertJsonEquals([
            '@context' => '/contexts/User',
            '@id' => '/users/5',
            '@type' => 'User',
            'email' => 'john@gmail.com',
            'id' => 5,
        ]);
    }
}
