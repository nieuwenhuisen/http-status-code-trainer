<?php

namespace App\Tests\Functional;

use App\DataFixtures\StatusCodeFixture;
use App\DataFixtures\UserFixture;
use App\Entity\User;
use App\Test\ApiTestCase;

class UserResourceTest extends ApiTestCase
{
    public function testGetUsers(): void
    {
        $this->loadFixtures([UserFixture::class]);

        $client = static::createAuthenticatedClient('admin@admin.com');
        $response = $client->request('GET', '/users');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => '/users',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 4,
        ]);

        $this->assertCount(4, $response->toArray()['hydra:member']);
    }

    public function testCreateUser(): void
    {
        $this->loadFixtures([UserFixture::class]);
        $client = static::createAuthenticatedClient('admin@admin.com');

        $client->request('POST', '/users', ['json' => [
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

    public function testGetUserDetail(): void
    {
        $this->loadFixtures([UserFixture::class, StatusCodeFixture::class]);
        $client = static::createAuthenticatedClient('admin@admin.com');

        $iri = static::findIriBy(User::class, ['email' => 'user1@user.com']);

        $client->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@id' => $iri,
            '@type' => 'User',
            'email' => 'user1@user.com',
        ]);
    }

    public function testUpdateUser(): void
    {
        $this->loadFixtures([UserFixture::class]);
        $client = static::createAuthenticatedClient('admin@admin.com');

        $iri = static::findIriBy(User::class, ['email' => 'user1@user.com']);

        $client->request('PUT', $iri, ['json' => [
            'email' => 'user1@example.com',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'email' => 'user1@example.com',
        ]);
    }

    public function testDeleteUser(): void
    {
        $this->loadFixtures([UserFixture::class]);
        $client = static::createAuthenticatedClient('admin@admin.com');

        $iri = static::findIriBy(User::class, ['email' => 'user1@user.com']);

        $client->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(204);

        $this->assertNull(
            $this->getRepository(User::class)->findOneBy(['email' => 'user1@user.com'])
        );
    }
}
