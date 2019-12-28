<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Exam;
use App\Service\ExamService;
use PHPUnit\Util\Exception;
use Symfony\Component\Security\Core\Security;

final class CreateNewExamController
{
    private $security;
    private $examService;

    public function __construct(Security $security, ExamService $examService)
    {
        $this->security = $security;
        $this->examService = $examService;
    }

    public function __invoke(): Exam
    {
        $user = $this->security->getUser();

        if (!$user) {
            throw new Exception('No user provided');
        }

        return $this->examService->createExamForUser($user);
    }
}
