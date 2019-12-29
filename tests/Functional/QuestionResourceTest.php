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

final class QuestionResourceTest extends ApiTestCase
{
    public function testAnswerQuestion(): void
    {
        $email = 'user1@user.com';
        $this->loadFixtures([UserFixture::class, StatusCodeFixture::class, ExamFixture::class]);
        $client = static::createAuthenticatedClient($email);

        $user = $this->getRepository(User::class)->findOneBy(['email' => $email]);
        $exam = $this->getRepository(Exam::class)->findOneBy(['user' => $user]);

        /** @var Question $question */
        $question = $exam->getQuestions()->first();

        $questionUri = $this->findIriBy(Question::class, ['id' => $question->getId()]);

        $client->request('PUT', $questionUri, ['json' => [
            'answer' => 302,
        ]]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/Question',
            '@type' => 'Question',
            'attempts' => 1,
            'correct' => false,
        ]);

        $client->request('PUT', $questionUri, ['json' => [
            'answer' => 301,
        ]]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '@context' => '/contexts/Question',
            '@type' => 'Question',
            'attempts' => 2,
            'correct' => true,
        ]);
    }

    public function testUserCanOnlyAnswerThereOwnQuestions(): void
    {
        $this->loadFixtures([UserFixture::class, StatusCodeFixture::class, ExamFixture::class]);
        $client = static::createAuthenticatedClient('user1@user.com');

        $user = $this->getRepository(User::class)->findOneBy(['email' => 'user2@user.com']);
        $exam = $this->getRepository(Exam::class)->findOneBy(['user' => $user]);

        /** @var Question $question */
        $question = $exam->getQuestions()->first();

        $questionUri = $this->findIriBy(Question::class, ['id' => $question->getId()]);

        $client->request('PUT', $questionUri, ['json' => [
            'answer' => 302,
        ]]);

        $this->assertResponseStatusCodeSame(403);
    }
}
