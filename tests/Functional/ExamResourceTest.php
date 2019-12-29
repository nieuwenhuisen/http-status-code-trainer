<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\DataFixtures\StatusCodeFixture;
use App\DataFixtures\UserFixture;
use App\Test\ApiTestCase;

final class ExamResourceTest extends ApiTestCase
{
    public function testStartNewExam(): void
    {
        $this->loadFixtures([UserFixture::class, StatusCodeFixture::class]);
        $client = static::createAuthenticatedClient('user1@user.com');

        $response = $client->request('POST', '/exams', ['json' => []]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/Exam',
            '@type' => 'Exam',
            'status' => 'created',
        ]);

        $json = json_decode($response->getContent(), true);
        $questions = $json['questions'];

        $this->assertCount(10, $questions);

        $question = $questions[0];

        $this->assertArrayHasKey('id', $question);
        $this->assertArrayHasKey('choices', $question);
        $this->assertArrayHasKey('question', $question);
        $this->assertCount(5, $question['choices']);
    }
}
