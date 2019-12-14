<?php

namespace App\Tests\Functional;

use App\DataFixtures\StatusCodeFixture;
use App\DataFixtures\UserFixture;
use App\Test\ApiTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class StatusCodeResourceTest extends ApiTestCase
{
    use FixturesTrait;

    public function testGetStatusCodes(): void
    {
        $this->loadFixtures([UserFixture::class, StatusCodeFixture::class]);

        $client = static::createAuthenticatedClient('admin@admin.com');
        $response = $client->request('GET', '/status_codes');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/StatusCode',
            '@id' => '/status_codes',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 62
        ]);

        $this->assertCount(30, $response->toArray()['hydra:member']);
    }

    public function testGetStatusCodeDetail(): void
    {
        $this->loadFixtures([UserFixture::class, StatusCodeFixture::class]);

        $client = static::createAuthenticatedClient('admin@admin.com');
        $client->request('GET', '/status_codes/200');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/StatusCode',
            '@id' => '/status_codes/200',
            '@type' => 'StatusCode',
            'code' => 200,
            'title' => 'OK'
        ]);
    }

    public function testCreateStatusCode(): void
    {
        $this->loadFixtures([UserFixture::class, StatusCodeFixture::class]);

        $client = static::createAuthenticatedClient('admin@admin.com');
        $client->request('POST', '/status_codes', ['json' => [
            'code' => 494,
            'title' => 'Request Header Too Large',
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/StatusCode',
            '@type' => 'StatusCode',
            'code' => 494,
            'title' => 'Request Header Too Large'
        ]);
    }
}
