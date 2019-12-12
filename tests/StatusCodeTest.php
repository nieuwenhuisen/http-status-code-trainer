<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Traits\RecreateDatabaseTrait;

class StatusCodeTest extends ApiTestCase
{
    use RecreateDatabaseTrait;

    public function testCreateStatusCode(): void
    {
        static::createClient()->request('POST', '/status_codes', ['json' => [
            'code' => 100,
            'title' => 'Continue',
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/StatusCode',
            '@type' => 'StatusCode',
            'code' => 100,
            'title' => 'Continue'
        ]);
    }
}
