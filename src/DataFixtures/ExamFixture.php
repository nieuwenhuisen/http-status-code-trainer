<?php

namespace App\DataFixtures;

use App\Entity\Exam;
use App\Entity\Question;
use App\Entity\StatusCode;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ExamFixture extends Fixture implements DependentFixtureInterface
{
    private const EXAMS = [
        [
            'user_1',
            [
                [301, [302, 303, 301, 304, 307]],
                [101, [100, 101, 102, 200, 103]],
                [204, [200, 202, 205, 201, 204]],
                [418, [417, 404, 418, 403, 401]],
                [403, [417, 404, 418, 403, 401]],
                [503, [501, 503, 504, 502, 505]],
                [205, [210, 206, 200, 205, 207]],
                [200, [210, 206, 200, 205, 207]],
                [307, [302, 303, 301, 304, 307]],
                [303, [302, 303, 301, 304, 307]],
            ],
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::EXAMS as $index => [$userIndex, $questions]) {
            /** @var User $user */
            $user = $this->getReference($userIndex);

            $exam = new Exam();
            $exam->setUser($user);

            foreach ($questions as $questionIndex => [$code, $choices]) {
                /** @var StatusCode $statusCode */
                $statusCode = $this->getReference('status_code_'.$code);

                $question = new Question($exam, $statusCode, $choices, $questionIndex);
                $exam->addQuestion($question);
            }

            $this->addReference('exam_'.$index, $exam);
            $manager->persist($exam);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            StatusCodeFixture::class,
        ];
    }
}
