<?php declare(strict_types=1);

namespace App\Tests\Functional;

use App\DataFixtures\UserFixture;
use App\Test\ApiTestCase;

final class ExamResourceTest extends ApiTestCase
{
    public function testStartNewExam(): void
    {
        $this->loadFixtures([UserFixture::class]);
        $client = static::createAuthenticatedClient('user1@user.com');

        $client->request('POST', '/exams', ['json' => []]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/Exam',
            '@type' => 'Exam',
        ]);
    }
}
