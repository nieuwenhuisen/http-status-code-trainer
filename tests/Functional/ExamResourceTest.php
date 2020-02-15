<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\DataFixtures\ExamFixture;
use App\DataFixtures\StatusCodeFixture;
use App\DataFixtures\UserFixture;
use App\Entity\Exam;
use App\Entity\Question;
use App\Entity\User;
use App\Test\ApiTestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class ExamResourceTest extends ApiTestCase
{
    public function testStartNewExam(): void
    {
        $this->loadFixtures([UserFixture::class, StatusCodeFixture::class]);
        $client = static::createAuthenticatedClient('user1@user.com');

        $response = $client->request('POST', '/exams', ['json' => []]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertExamGet($response);
    }

    public function testGetExam(): void
    {
        $email = 'user1@user.com';
        $this->loadFixtures([UserFixture::class, StatusCodeFixture::class, ExamFixture::class]);
        $client = static::createAuthenticatedClient($email);

        $user = $this->getRepository(User::class)->findOneBy(['email' => $email]);

        /** @var Exam $exam */
        $exam = $this->getRepository(Exam::class)->findOneBy(['user' => $user]);

        $examUri = $this->findIriBy(Exam::class, ['id' => $exam->getId()]);
        $this->assertNotNull($examUri, 'Exam uri not found');

        $response = $client->request('GET', $examUri);
        $this->assertExamGet($response);
    }

    public function testFinishExam(): void
    {
        $email = 'user1@user.com';
        $this->loadFixtures([UserFixture::class, StatusCodeFixture::class, ExamFixture::class]);
        $client = static::createAuthenticatedClient($email);

        $user = $this->getRepository(User::class)->findOneBy(['email' => $email]);

        /** @var Exam $exam */
        $exam = $this->getRepository(Exam::class)->findOneBy(['user' => $user]);

        $examUri = $this->findIriBy(Exam::class, ['id' => $exam->getId()]);
        $this->assertNotNull($examUri, 'Exam uri not found');

        $response = $client->request('GET', $examUri);
        $json = json_decode($response->getContent(), true);
        $questions = $json['questions'];

        $answers = [301, 101, 204, 418, 403, 503, 205, 200, 307, 303];

        foreach ($questions as $index => $question) {
            $questionUri = $this->findIriBy(Question::class, ['id' => $question['id']]);

            $this->assertNotNull($questionUri, 'Question uri not found');

            $client->request('PUT', $questionUri, ['json' => [
                'answer' => $answers[$index],
            ]]);

            $this->assertResponseStatusCodeSame(200);
        }

        $response = $client->request('GET', $examUri);
        $this->assertExamGet($response, 'finished');
    }

    private function assertExamGet(ResponseInterface $response, string $status = 'created'): void
    {
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/contexts/Exam',
            '@type' => 'Exam',
            'status' => $status,
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

    public function testGetListOfExams(): void
    {
        $this->loadFixtures([UserFixture::class, StatusCodeFixture::class, ExamFixture::class]);
        $client = static::createAuthenticatedClient('user1@user.com');

        $client->request('GET', 'exams');

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/Exam',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 1,
        ]);
    }
}
