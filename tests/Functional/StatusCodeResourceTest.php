<?php

namespace App\Tests\Functional;

use App\DataFixtures\StatusCodeFixture;
use App\DataFixtures\UserFixture;
use App\Entity\StatusCode;
use App\Test\ApiTestCase;

class StatusCodeResourceTest extends ApiTestCase
{
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
            'hydra:totalItems' => 62,
        ]);

        $this->assertCount(30, $response->toArray()['hydra:member']);
    }

    public function testGetStatusCodeDetail(): void
    {
        $this->loadFixtures([UserFixture::class, StatusCodeFixture::class]);
        $client = static::createAuthenticatedClient('admin@admin.com');

        $iri = static::findIriBy(StatusCode::class, ['code' => 200]);

        $client->request('GET', $iri);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/contexts/StatusCode',
            '@id' => $iri,
            '@type' => 'StatusCode',
            'code' => 200,
            'title' => 'OK',
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
            'title' => 'Request Header Too Large',
        ]);
    }

    public function testAlreadyUsedViolation(): void
    {
        $this->loadFixtures([UserFixture::class, StatusCodeFixture::class]);

        $client = static::createAuthenticatedClient('admin@admin.com');
        $client->request('POST', '/status_codes', ['json' => [
            'code' => 200,
            'title' => 'OK',
        ]]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            '@context' => '/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'code',
                    'message' => 'This value is already used.'
                ]
            ]
        ]);
    }
}
