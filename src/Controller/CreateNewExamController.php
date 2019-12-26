<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Exam;
use Symfony\Component\Security\Core\Security;

final class CreateNewExamController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(): Exam
    {
        $exam = new Exam;
        $exam->setUser($this->security->getUser());

        return $exam;
    }
}
