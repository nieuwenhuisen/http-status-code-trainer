<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\ExamService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ExamFixture extends Fixture implements DependentFixtureInterface
{
    private $examService;

    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    public function load(ObjectManager $manager): void
    {
        foreach (UserFixture::USERS as $userIndex => [$email, $password, $roles]) {
            if (!in_array('ROLE_USER', $roles, true)) {
                continue;
            }

            /** @var User $user */
            $user = $this->getReference($userIndex);
            $exam = $this->examService->createExamForUser($user);

            $this->addReference('exam_'.$userIndex, $exam);
            $manager->persist($exam);
        }

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies(): array
    {
        return [
            UserFixture::class
        ];
    }
}
