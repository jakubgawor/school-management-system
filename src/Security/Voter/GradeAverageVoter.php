<?php

namespace App\Security\Voter;

use App\Dto\GradeAverageDto;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class GradeAverageVoter extends Voter
{
    public const VIEW_AVERAGE = 'VIEW_AVERAGE';

    public function __construct(
        private Security $security,
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW_AVERAGE])
            && $subject instanceof GradeAverageDto;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_TEACHER')) {
            return true;
        }

        switch ($attribute) {
            case self::VIEW_AVERAGE:
                if ($user->getStudent()->getId() === $subject->studentId) {
                    return true;
                }
        }

        return false;
    }
}
