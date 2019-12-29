<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Exam;
use App\Entity\Question;
use App\Entity\User;
use App\Repository\StatusCodeRepository;

final class ExamService
{
    private $statusCodeRepository;

    public function __construct(StatusCodeRepository $statusCodeRepository)
    {
        $this->statusCodeRepository = $statusCodeRepository;
    }

    private function getRandomChoices(array $groups, int $currentCode, int $amount): array
    {
        $choices = [$currentCode];

        $groupCode = (int) mb_substr((string) $currentCode, 0, 1) * 100;

        // Get group and exclude the current code
        $group = array_filter($groups[$groupCode], static function ($code) use ($currentCode) {
            return $code !== $currentCode;
        });

        unset($groups[$groupCode]);

        do {
            if (0 === \count($group)) {
                $groupCode = array_rand($groups);
                $group = $groups[$groupCode];
                unset($groups[$groupCode]);
            }

            shuffle($group);
            $choices[] = array_shift($group);
        } while (\count($choices) < $amount);

        shuffle($choices);

        return $choices;
    }

    public function createExamForUser(User $user, int $optionsPerQuestion = 5): Exam
    {
        $exam = new Exam();
        $exam->setUser($user);

        $codes = $this->statusCodeRepository->getCodesGroupByType();
        $statusCodes = $this->statusCodeRepository->getForUser($user);

        for ($i = 0; $i < 10; ++$i) {
            $statusCode = $statusCodes[$i];
            $choices = $this->getRandomChoices($codes, $statusCode->getId(), $optionsPerQuestion);

            $question = new Question($exam, $statusCode, $choices, $i);
            $exam->addQuestion($question);
        }

        return $exam;
    }
}
