<?php

namespace App\Security\Voter;

use App\ApiResource\UserApi;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserApiVoter extends Voter
{
    public const PATCH = 'PATCH';
    public const DELETE = 'DELETE';

    public function __construct(
        private Security $security,
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::PATCH, self::DELETE])
            && $subject instanceof UserApi;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        assert($subject instanceof UserApi);

        switch ($attribute) {
            case self::DELETE:
            case self::PATCH:
                if ($subject->id === $user->getId()) {
                    return true;
                }
        }

        return false;
    }
}
