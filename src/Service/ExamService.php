<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Exam;
use App\Entity\Question;
use App\Entity\User;
use App\Repository\ExamRepository;
use App\Repository\StatusCodeRepository;

final class ExamService
{
    private $examRepository;
    private $statusCodeRepository;

    public function __construct(ExamRepository $examRepository, StatusCodeRepository $statusCodeRepository)
    {
        $this->examRepository = $examRepository;
        $this->statusCodeRepository = $statusCodeRepository;
    }

    public function createExamForUser(User $user): Exam
    {
        $exam = new Exam();
        $exam->setUser($user);

        $statusCodes = $this->statusCodeRepository->getForUser($user);

        for ($i = 0; $i < 20; ++$i) {
            $question = Question::fromExamAndStatusCodeAndPosition($exam, $statusCodes[$i], $i);
            $exam->addQuestion($question);
        }

        return $exam;
    }
}
