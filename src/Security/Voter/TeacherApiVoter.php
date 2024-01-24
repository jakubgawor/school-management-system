<?php

namespace App\Security\Voter;

use App\ApiResource\TeacherApi;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TeacherApiVoter extends Voter
{
    public const EDIT_TEACHER = 'EDIT_TEACHER';

    public function __construct(
        private Security $security,
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT_TEACHER])
            && $subject instanceof TeacherApi;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case self::EDIT_TEACHER:
                if ($subject->getId() === $user->getTeacher()->getId()) {
                    return true;
                }

        }

        return false;
    }
}
