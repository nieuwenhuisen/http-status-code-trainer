<?php declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Question;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AnswerQuestionVoter extends Voter
{
    protected function supports($attribute, $subject): bool
    {
        return 'ANSWER_QUESTION' === $attribute
            && $subject instanceof Question;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Question $subject */
        $examUser = $subject->getExam()->getUser();

        return $examUser->isEqualTo($user);
    }
}
